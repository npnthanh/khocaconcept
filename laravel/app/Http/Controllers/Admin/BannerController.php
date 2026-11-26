<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index()
    {
        return view('admin.banners.index', ['banners' => Banner::orderBy('placement')->orderBy('sort_order')->latest('id')->paginate(20)]);
    }

    public function create()
    {
        return view('admin.banners.form', ['banner' => new Banner()]);
    }

    public function store(Request $request)
    {
        Banner::create($this->validated($request));

        return redirect()->route('admin.banners.index')->with('success', 'Đã tạo banner.');
    }

    public function edit(Banner $banner)
    {
        return view('admin.banners.form', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $banner->update($this->validated($request, $banner));

        return redirect()->route('admin.banners.index')->with('success', 'Đã cập nhật banner.');
    }

    public function destroy(Banner $banner)
    {
        if ($banner->image_path) {
            Storage::disk('public')->delete($banner->image_path);
        }
        if ($banner->mobile_image_path) {
            Storage::disk('public')->delete($banner->mobile_image_path);
        }
        $banner->delete();

        return back()->with('success', 'Đã xóa banner.');
    }

    private function validated(Request $request, ?Banner $banner = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'eyebrow' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'text_position' => ['nullable', 'string', 'in:left,right,center'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'mobile_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'link_url' => ['nullable', 'string', 'max:500'],
            'link_label' => ['nullable', 'string', 'max:100'],
            'placement' => ['required', 'in:home'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        
        // Handle desktop image
        if ($request->hasFile('image')) {
            if ($banner?->image_path) {
                Storage::disk('public')->delete($banner->image_path);
            }
            $data['image_path'] = $request->file('image')->store('banners', 'public');
        }
        unset($data['image']);

        // Handle mobile image
        if ($request->hasFile('mobile_image')) {
            if ($banner?->mobile_image_path) {
                Storage::disk('public')->delete($banner->mobile_image_path);
            }
            $data['mobile_image_path'] = $request->file('mobile_image')->store('banners/mobile', 'public');
        } elseif ($request->boolean('delete_mobile_image')) {
            if ($banner?->mobile_image_path) {
                Storage::disk('public')->delete($banner->mobile_image_path);
            }
            $data['mobile_image_path'] = null;
        }
        unset($data['mobile_image']);

        return $data;
    }
}
