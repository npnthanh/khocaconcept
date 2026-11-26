<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookImage;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $term = $request->string('q')->trim()->toString();

        return view('admin.books.index', [
            'books' => Book::bookOnly()->with('categories')->withCount('galleryImages')->search($term)->latest()->paginate(20)->withQueryString(),
            'term' => $term,
            'categories' => Category::whereNotIn('slug', ['gifts', 'qua-tang'])->where('name', 'not like', '%gift%')->where('name', 'not like', '%quà tặng%')->orderBy('name')->get(),
        ]);
    }

    public function bulk(Request $request)
    {
        $data = $request->validate([
            'book_ids' => ['required', 'array', 'min:1'],
            'book_ids.*' => ['integer', 'exists:books,id'],
            'action' => ['required', Rule::in(['publish', 'unpublish', 'assign_category', 'delete_images'])],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
        ]);

        $books = Book::query()->whereIn('id', $data['book_ids'])->with('galleryImages')->get();

        if ($data['action'] === 'publish') {
            Book::whereKey($data['book_ids'])->update(['is_published' => true]);
            $message = 'Đã hiển thị '.count($data['book_ids']).' sách.';
        } elseif ($data['action'] === 'unpublish') {
            Book::whereKey($data['book_ids'])->update(['is_published' => false]);
            $message = 'Đã ẩn '.count($data['book_ids']).' sách.';
        } elseif ($data['action'] === 'assign_category') {
            if (! $request->filled('category_id')) {
                return back()->withErrors(['category_id' => 'Vui lòng chọn thể loại cần gán.']);
            }
            foreach ($books as $book) {
                $book->categories()->syncWithoutDetaching([$data['category_id']]);
            }
            $message = 'Đã gán thể loại cho '.count($data['book_ids']).' sách.';
        } else {
            foreach ($books as $book) {
                foreach ($book->galleryImages as $image) {
                    Storage::disk('public')->delete($image->path);
                    $image->delete();
                }
            }
            $message = 'Đã xóa toàn bộ ảnh gallery của '.count($data['book_ids']).' sách.';
        }

        return back()->with('success', $message);
    }

    public function create()
    {
        return view('admin.books.form', ['book' => new Book(), 'categories' => Category::whereNotIn('slug', ['gifts', 'qua-tang'])->where('name', 'not like', '%gift%')->where('name', 'not like', '%quà tặng%')->orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        $book = Book::create($this->validated($request));
        $book->categories()->sync($request->input('category_ids', []));
        $this->storeGalleryImages($book, $request);

        return redirect()->route('admin.books.index')->with('success', 'Đã thêm sách.');
    }

    public function edit(Book $book)
    {
        return view('admin.books.form', ['book' => $book->load(['categories', 'galleryImages']), 'categories' => Category::whereNotIn('slug', ['gifts', 'qua-tang'])->where('name', 'not like', '%gift%')->where('name', 'not like', '%quà tặng%')->orderBy('name')->get()]);
    }

    public function update(Request $request, Book $book)
    {
        $book->update($this->validated($request, $book));
        $book->categories()->sync($request->input('category_ids', []));
        $this->storeGalleryImages($book, $request);

        return redirect()->route('admin.books.index')->with('success', 'Đã cập nhật sách.');
    }

    public function destroy(Book $book)
    {
        if ($book->cover_image) {
            Storage::disk('public')->delete($book->cover_image);
        }
        foreach ($book->galleryImages as $image) {
            Storage::disk('public')->delete($image->path);
        }
        $book->delete();

        return back()->with('success', 'Đã xóa sách.');
    }

    public function destroyImage(Book $book, BookImage $image)
    {
        abort_unless((int) $image->book_id === (int) $book->id, 404);

        Storage::disk('public')->delete($image->path);
        $image->delete();

        return back()->with('success', 'Đã xóa ảnh khỏi gallery.');
    }

    private function validated(Request $request, ?Book $book = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'author' => ['nullable', 'string', 'max:255'],
            'publisher' => ['nullable', 'string', 'max:255'],
            'language' => ['nullable', 'string', 'max:100'],
            'book_condition' => ['nullable', Rule::in(['unknown', 'new', 'used', 'limited', 'signed'])],
            'availability_status' => ['nullable', Rule::in(['available', 'reserved', 'sold', 'coming_soon'])],
            'featured_sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'published_year' => ['nullable', 'integer', 'min:1000', 'max:'.(now()->year + 1)],
            'volume_count' => ['nullable', 'integer', 'min:1', 'max:999'],
            'isbn' => ['nullable', 'string', 'max:32', Rule::unique('books', 'isbn')->ignore($book)],
            'page_count' => ['nullable', 'integer', 'min:1'],
            'dimensions' => ['nullable', 'string', 'max:255'],
            'summary' => ['nullable', 'string'],
            'additional_information' => ['nullable', 'string'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'cover' => ['nullable', 'image', 'max:5120'],
            'gallery' => ['nullable', 'array', 'max:12'],
            'gallery.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'og_image_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'category_ids' => ['required', 'array', 'min:1'],
            'category_ids.*' => ['integer', 'exists:categories,id'],
            'is_published' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
        ], [
            'cover.image' => 'Ảnh bìa phải là tệp hình ảnh hợp lệ.',
            'cover.max' => 'Ảnh bìa quá lớn. Vui lòng nén ảnh xuống tối đa 5MB.',
            'gallery.array' => 'Danh sách ảnh gallery không hợp lệ.',
            'gallery.max' => 'Gallery chỉ được tải lên tối đa 12 ảnh.',
            'gallery.*.image' => 'Mỗi ảnh gallery phải là tệp hình ảnh hợp lệ.',
            'gallery.*.mimes' => 'Ảnh gallery chỉ hỗ trợ JPG, PNG hoặc WEBP.',
            'gallery.*.max' => 'Có ảnh gallery vượt quá 10MB. Vui lòng nén ảnh rồi thử lại.',
            'og_image_file.image' => 'Ảnh SEO phải là tệp hình ảnh hợp lệ.',
            'og_image_file.mimes' => 'Ảnh SEO chỉ hỗ trợ JPG, PNG hoặc WEBP.',
            'og_image_file.max' => 'Ảnh SEO quá lớn. Vui lòng nén ảnh xuống tối đa 5MB.',
            'category_ids.required' => 'Vui lòng chọn ít nhất một thể loại cho sách.',
        ]);

        if ($request->hasFile('cover')) {
            if ($book?->cover_image) {
                Storage::disk('public')->delete($book->cover_image);
            }
            $data['cover_image'] = $request->file('cover')->store('covers', 'public');
        }

        unset($data['cover'], $data['category_ids']);
        unset($data['gallery']);
        $data['is_featured'] = $request->boolean('is_featured');
        if ($request->hasFile('og_image_file')) {
            if ($book?->og_image) {
                Storage::disk('public')->delete($book->og_image);
            }
            $data['og_image'] = $request->file('og_image_file')->store('books/og', 'public');
        }
        unset($data['og_image_file']);
        $data['book_condition'] = $data['book_condition'] ?? 'unknown';
        $data['availability_status'] = $data['availability_status'] ?? 'available';
        $data['is_published'] = $request->boolean('is_published');
        $data['slug'] = $book?->slug ?: Str::slug($data['title']).'-'.Str::lower(Str::random(6));

        return $data;
    }

    private function storeGalleryImages(Book $book, Request $request): void
    {
        if (! $request->hasFile('gallery')) {
            return;
        }

        $sortOrder = (int) $book->galleryImages()->max('sort_order');

        foreach ($request->file('gallery') as $file) {
            $sortOrder++;
            $book->galleryImages()->create([
                'path' => $file->store('book-gallery', 'public'),
                'alt_text' => 'Hình ảnh '.$book->title,
                'sort_order' => $sortOrder,
            ]);
        }
    }
}
