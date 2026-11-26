<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Collection as BookCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CollectionController extends Controller
{
    public function index()
    {
        return view('admin.collections.index', [
            'collections' => BookCollection::withCount('books')->orderBy('sort_order')->latest('id')->paginate(20),
        ]);
    }

    public function create()
    {
        return view('admin.collections.form', [
            'collection' => new BookCollection(),
            'books' => Book::query()->bookOnly()->orderBy('title')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $collection = BookCollection::create($this->validated($request));
        $this->syncBooks($collection, $request);

        return redirect()->route('admin.collections.index')->with('success', 'Đã tạo bộ sưu tập.');
    }

    public function edit(BookCollection $collection)
    {
        return view('admin.collections.form', [
            'collection' => $collection->load('books'),
            'books' => Book::query()->bookOnly()->orderBy('title')->get(),
        ]);
    }

    public function update(Request $request, BookCollection $collection)
    {
        $collection->update($this->validated($request, $collection));
        $this->syncBooks($collection, $request);

        return redirect()->route('admin.collections.index')->with('success', 'Đã cập nhật bộ sưu tập.');
    }

    public function destroy(BookCollection $collection)
    {
        foreach ([$collection->cover_image, $collection->og_image] as $path) {
            if ($path) {
                Storage::disk('public')->delete($path);
            }
        }
        $collection->delete();

        return back()->with('success', 'Đã xóa bộ sưu tập.');
    }

    private function validated(Request $request, ?BookCollection $collection = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'og_image_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'is_featured' => ['nullable', 'boolean'],
            'is_published' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'book_ids' => ['nullable', 'array'],
            'book_ids.*' => ['integer', 'exists:books,id'],
        ]);

        $data['slug'] = $collection?->slug ?: Str::slug($data['title']).'-'.Str::lower(Str::random(6));
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_published'] = $request->boolean('is_published');

        if ($request->hasFile('cover')) {
            if ($collection?->cover_image) {
                Storage::disk('public')->delete($collection->cover_image);
            }
            $data['cover_image'] = $request->file('cover')->store('collections', 'public');
        }

        if ($request->hasFile('og_image_file')) {
            if ($collection?->og_image) {
                Storage::disk('public')->delete($collection->og_image);
            }
            $data['og_image'] = $request->file('og_image_file')->store('collections/og', 'public');
        }

        unset($data['cover'], $data['og_image_file'], $data['book_ids']);

        return $data;
    }

    private function syncBooks(BookCollection $collection, Request $request): void
    {
        $bookIds = array_values(array_filter($request->input('book_ids', [])));
        $sync = [];
        foreach ($bookIds as $index => $bookId) {
            $sync[(int) $bookId] = ['sort_order' => $index];
        }
        $collection->books()->sync($sync);
    }
}
