<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\BooksImport;
use App\Imports\BooksPreviewImport;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class BookImportController extends Controller
{
    public function create()
    {
        return view('admin.books.import');
    }

    public function store(Request $request)
    {
        return $this->preview($request);
    }

    public function preview(Request $request)
    {
        $request->validate(['file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240']]);

        $import = new BooksPreviewImport();
        Excel::import($import, $request->file('file'));

        $rows = [];
        $seenIsbns = [];
        foreach ($import->rows->values() as $index => $row) {
            $data = collect($row)->map(function ($value) {
                return is_string($value) ? trim($value) : $value;
            })->all();
            $data['is_published'] = $data['is_published'] ?? true;

            $errors = Validator::make($data, [
                'title' => ['required', 'string', 'max:255'],
                'author' => ['nullable', 'string', 'max:255'],
                'publisher' => ['nullable', 'string', 'max:255'],
                'language' => ['nullable', 'string', 'max:100'],
                'book_condition' => ['nullable', 'in:unknown,new,used,limited,signed'],
                'availability_status' => ['nullable', 'in:available,reserved,sold,coming_soon'],
                'published_year' => ['nullable', 'integer', 'min:1000', 'max:'.(now()->year + 1)],
                'volume_count' => ['nullable', 'integer', 'min:1', 'max:999'],
                'isbn' => ['nullable', 'string', 'max:32'],
                'page_count' => ['nullable', 'integer', 'min:1'],
                'is_published' => ['nullable'],
            ])->errors()->all();

            $isbn = trim((string) ($data['isbn'] ?? ''));
            if ($isbn !== '' && isset($seenIsbns[$isbn])) {
                $errors[] = 'ISBN bị trùng với dòng '.$seenIsbns[$isbn].'.';
            } elseif ($isbn !== '') {
                $seenIsbns[$isbn] = $index + 2;
            }

            $published = strtolower(trim((string) ($data['is_published'] ?? '')));
            if ($published !== '' && ! in_array($published, ['1', '0', 'true', 'false', 'yes', 'no', 'co', 'khong'], true)) {
                $errors[] = 'is_published chỉ nhận 1/0, true/false, yes/no.';
            }

            $rows[] = [
                'row' => $index + 2,
                'data' => $data,
                'errors' => array_values(array_unique($errors)),
                'valid' => count($errors) === 0,
            ];
        }

        $token = (string) Str::uuid();
        Cache::put('book-import-preview:'.$token, [
            'rows' => collect($rows)->where('valid', true)->pluck('data')->values()->all(),
            'total' => count($rows),
            'valid' => collect($rows)->where('valid', true)->count(),
        ], now()->addMinutes(30));

        return view('admin.books.import-preview', compact('rows', 'token'));
    }

    public function confirm(Request $request)
    {
        $data = $request->validate(['token' => ['required', 'string', 'uuid']]);
        $preview = Cache::get('book-import-preview:'.$data['token']);
        abort_unless(is_array($preview), 419, 'Bản xem trước đã hết hạn.');

        (new BooksImport())->importRows(collect($preview['rows'] ?? []));
        Cache::forget('book-import-preview:'.$data['token']);

        return redirect()->route('admin.books.index')->with('success', 'Đã import dữ liệu sách.');
    }
}
