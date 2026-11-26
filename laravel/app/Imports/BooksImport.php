<?php

namespace App\Imports;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BooksImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows): void
    {
        $this->importRows($rows);
    }

    public function importRows(Collection $rows): void
    {
        foreach ($rows as $row) {
            $row = $row instanceof Collection ? $row : collect($row);
            $title = trim((string) ($row['title'] ?? ''));
            if ($title === '') {
                continue;
            }

            $isbn = trim((string) ($row['isbn'] ?? ''));
            $slug = Str::slug($title);
            $book = $isbn !== ''
                ? Book::firstOrNew(['isbn' => $isbn])
                : Book::firstOrNew(['slug' => $slug]);

            $book->fill([
                'title' => $title,
                'slug' => $book->slug ?: $slug.'-'.Str::lower(Str::random(6)),
                'author' => $this->string($row, 'author'),
                'publisher' => $this->string($row, 'publisher'),
                'language' => $this->string($row, 'language'),
                'book_condition' => $this->string($row, 'book_condition') ?: 'unknown',
                'availability_status' => $this->string($row, 'availability_status') ?: 'available',
                'published_year' => $this->integer($row, 'published_year'),
                'volume_count' => $this->integer($row, 'volume_count'),
                'isbn' => $isbn ?: null,
                'page_count' => $this->integer($row, 'page_count'),
                'dimensions' => $this->string($row, 'dimensions'),
                'summary' => $this->string($row, 'summary'),
                'additional_information' => $this->string($row, 'additional_information'),
                'is_published' => filter_var($row['is_published'] ?? true, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true,
            ])->save();

            $categoryIds = collect(preg_split('/[,;|]/', (string) ($row['categories'] ?? '')))
                ->map(fn (string $name) => trim($name))
                ->filter()
                ->map(fn (string $name) => Category::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name])->id);

            if ($categoryIds->isNotEmpty()) {
                $book->categories()->sync($categoryIds);
            }
        }
    }

    private function string(Collection $row, string $key): ?string
    {
        $value = trim((string) ($row[$key] ?? ''));
        return $value !== '' ? $value : null;
    }

    private function integer(Collection $row, string $key): ?int
    {
        return is_numeric($row[$key] ?? null) ? (int) $row[$key] : null;
    }
}
