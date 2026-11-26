<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gift;
use App\Models\GiftImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class GiftController extends Controller
{
    public function index(Request $request)
    {
        $term = $request->string('q')->trim()->toString();
        $gifts = Gift::query()
            ->withCount('galleryImages')
            ->search($term)
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.gifts.index', compact('gifts', 'term'));
    }

    public function bulk(Request $request)
    {
        $data = $request->validate([
            'gift_ids' => ['required', 'array', 'min:1'],
            'gift_ids.*' => ['integer', 'exists:gifts,id'],
            'action' => ['required', Rule::in(['publish', 'unpublish', 'delete'])],
        ]);

        if ($data['action'] === 'publish') {
            Gift::whereKey($data['gift_ids'])->update(['is_published' => true]);
            $message = 'Đã hiển thị '.count($data['gift_ids']).' sản phẩm quà tặng.';
        } elseif ($data['action'] === 'unpublish') {
            Gift::whereKey($data['gift_ids'])->update(['is_published' => false]);
            $message = 'Đã ẩn '.count($data['gift_ids']).' sản phẩm quà tặng.';
        } else {
            $gifts = Gift::query()->whereIn('id', $data['gift_ids'])->with('galleryImages')->get();
            foreach ($gifts as $gift) {
                if ($gift->cover_image) {
                    Storage::disk('public')->delete($gift->cover_image);
                }
                if ($gift->og_image) {
                    Storage::disk('public')->delete($gift->og_image);
                }
                foreach ($gift->galleryImages as $image) {
                    Storage::disk('public')->delete($image->path);
                }
                $gift->delete();
            }
            $message = 'Đã xóa '.count($data['gift_ids']).' sản phẩm quà tặng.';
        }

        return back()->with('success', $message);
    }

    public function create()
    {
        return view('admin.gifts.form', ['gift' => new Gift()]);
    }

    public function store(Request $request)
    {
        $gift = Gift::create($this->validated($request));
        $this->storeGalleryImages($gift, $request);

        return redirect()->route('admin.gifts.index')->with('success', 'Đã thêm sản phẩm Gift mới thành công.');
    }

    public function edit(Gift $gift)
    {
        return view('admin.gifts.form', ['gift' => $gift->load('galleryImages')]);
    }

    public function update(Request $request, Gift $gift)
    {
        $gift->update($this->validated($request, $gift));
        $this->storeGalleryImages($gift, $request);

        return redirect()->route('admin.gifts.index')->with('success', 'Đã cập nhật thông tin sản phẩm Gift thành công.');
    }

    public function destroy(Gift $gift)
    {
        if ($gift->cover_image) {
            Storage::disk('public')->delete($gift->cover_image);
        }
        if ($gift->og_image) {
            Storage::disk('public')->delete($gift->og_image);
        }
        foreach ($gift->galleryImages as $image) {
            Storage::disk('public')->delete($image->path);
        }
        $gift->delete();

        return back()->with('success', 'Đã xóa sản phẩm Gift.');
    }

    public function destroyImage(Gift $gift, GiftImage $image)
    {
        abort_unless((int) $image->gift_id === (int) $gift->id, 404);

        Storage::disk('public')->delete($image->path);
        $image->delete();

        return back()->with('success', 'Đã xóa ảnh khỏi gallery Gift.');
    }

    private function validated(Request $request, ?Gift $gift = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:255'],
            'product_type' => ['nullable', 'string', 'max:120'],
            'sku' => ['nullable', 'string', 'max:64', Rule::unique('gifts', 'sku')->ignore($gift)],
            'dimensions' => ['nullable', 'string', 'max:255'],
            'availability_status' => ['nullable', Rule::in(['available', 'reserved', 'sold', 'coming_soon'])],
            'summary' => ['nullable', 'string'],
            'additional_information' => ['nullable', 'string'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'cover' => ['nullable', 'image', 'max:10240'],
            'gallery' => ['nullable', 'array', 'max:16'],
            'gallery.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'og_image_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'is_published' => ['nullable', 'boolean'],
        ], [
            'cover.image' => 'Ảnh đại diện phải là tệp hình ảnh hợp lệ.',
            'cover.max' => 'Ảnh đại diện quá lớn. Vui lòng nén ảnh xuống tối đa 10MB.',
            'gallery.array' => 'Danh sách ảnh gallery không hợp lệ.',
            'gallery.max' => 'Gallery Gift chỉ được tải lên tối đa 16 ảnh.',
            'gallery.*.image' => 'Mỗi ảnh gallery Gift phải là tệp hình ảnh hợp lệ.',
            'gallery.*.mimes' => 'Ảnh gallery Gift chỉ hỗ trợ JPG, PNG hoặc WEBP.',
            'gallery.*.max' => 'Có ảnh gallery Gift vượt quá 10MB. Vui lòng nén ảnh rồi thử lại.',
            'og_image_file.image' => 'Ảnh SEO phải là tệp hình ảnh hợp lệ.',
            'og_image_file.mimes' => 'Ảnh SEO chỉ hỗ trợ JPG, PNG hoặc WEBP.',
            'og_image_file.max' => 'Ảnh SEO quá lớn. Vui lòng nén ảnh xuống tối đa 5MB.',
        ]);

        if ($request->hasFile('cover')) {
            if ($gift?->cover_image) {
                Storage::disk('public')->delete($gift->cover_image);
            }
            $data['cover_image'] = $request->file('cover')->store('gift-covers', 'public');
            $this->syncPublicStorage($request->file('cover')->getRealPath(), $data['cover_image']);
        }

        unset($data['cover'], $data['gallery']);

        if ($request->hasFile('og_image_file')) {
            if ($gift?->og_image) {
                Storage::disk('public')->delete($gift->og_image);
            }
            $data['og_image'] = $request->file('og_image_file')->store('gifts/og', 'public');
            $this->syncPublicStorage($request->file('og_image_file')->getRealPath(), $data['og_image']);
        }
        unset($data['og_image_file']);

        $data['availability_status'] = $data['availability_status'] ?? 'available';
        $data['is_published'] = $request->boolean('is_published');
        $data['slug'] = $gift?->slug ?: Str::slug($data['title']).'-'.Str::lower(Str::random(6));

        return $data;
    }

    private function storeGalleryImages(Gift $gift, Request $request): void
    {
        if (! $request->hasFile('gallery')) {
            return;
        }

        $sortOrder = (int) $gift->galleryImages()->max('sort_order');

        foreach ($request->file('gallery') as $file) {
            $sortOrder++;
            $path = $file->store('gift-gallery', 'public');
            $this->syncPublicStorage($file->getRealPath(), $path);

            $gift->galleryImages()->create([
                'path' => $path,
                'alt_text' => 'Hình ảnh quà tặng '.$gift->title,
                'sort_order' => $sortOrder,
            ]);
        }
    }

    private function syncPublicStorage(string $source, string $path): void
    {
        $destination = base_path('../public_html/storage/'.$path);
        if (! file_exists(dirname($destination))) {
            @mkdir(dirname($destination), 0755, true);
        }
        @copy($source, $destination);
    }
}
