<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\HomeExploreTile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class HomeExploreController extends Controller
{
    public function index()
    {
        return view('admin.explore.index', [
            'tiles' => HomeExploreTile::with('category')
                ->orderBy('sort_order')
                ->orderBy('id')
                ->paginate(20),
        ]);
    }

    public function create()
    {
        return view('admin.explore.form', [
            'tile' => new HomeExploreTile([
                'subtitle' => 'Khám phá bộ sưu tập →',
                'overlay_opacity' => 0.78,
                'is_active' => true,
            ]),
            'categories' => $this->categories(),
        ]);
    }

    public function store(Request $request)
    {
        HomeExploreTile::create($this->validated($request));

        return redirect()->route('admin.explore.index')->with('success', 'Đã thêm ô More to Explore.');
    }

    public function edit(HomeExploreTile $tile)
    {
        return view('admin.explore.form', [
            'tile' => $tile,
            'categories' => $this->categories(),
        ]);
    }

    public function update(Request $request, HomeExploreTile $tile)
    {
        $tile->update($this->validated($request, $tile));

        return redirect()->route('admin.explore.index')->with('success', 'Đã cập nhật ô More to Explore.');
    }

    public function destroy(HomeExploreTile $tile)
    {
        if ($tile->image_path && !str_starts_with($tile->image_path, 'images/')) {
            Storage::disk('public')->delete($tile->image_path);
        }

        $tile->delete();

        return back()->with('success', 'Đã xóa ô More to Explore.');
    }

    private function categories()
    {
        return Category::query()
            ->where('is_active', true)
            ->whereNotIn('slug', ['gifts', 'qua-tang'])
            ->where('slug', 'not like', '%gift%')
            ->where('name', 'not like', '%gift%')
            ->where('name', 'not like', '%quà tặng%')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    private function validated(Request $request, ?HomeExploreTile $tile = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'link_type' => ['required', Rule::in(['category', 'url'])],
            'category_id' => [
                'nullable',
                'integer',
                Rule::exists('categories', 'id')->where(function ($query): void {
                    $query->where('is_active', true)
                        ->whereNotIn('slug', ['gifts', 'qua-tang'])
                        ->where('slug', 'not like', '%gift%')
                        ->where('name', 'not like', '%gift%')
                        ->where('name', 'not like', '%quà tặng%');
                }),
                Rule::requiredIf(fn (): bool => $request->input('link_type') === 'category'),
            ],
            'link_url' => [
                'nullable', 'string', 'max:500',
                Rule::requiredIf(fn (): bool => $request->input('link_type') === 'url'),
            ],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'overlay_opacity' => ['required', 'numeric', 'min:0.35', 'max:0.95'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'category_id.required' => 'Vui lòng chọn thể loại đích.',
            'link_url.required' => 'Vui lòng nhập liên kết đích.',
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['category_id'] = $data['link_type'] === 'category' ? ($data['category_id'] ?? null) : null;
        $data['link_url'] = $data['link_type'] === 'url' ? trim($data['link_url'] ?? '') : null;

        if ($request->hasFile('image')) {
            if ($tile?->image_path && !str_starts_with($tile->image_path, 'images/')) {
                Storage::disk('public')->delete($tile->image_path);
            }

            $data['image_path'] = $request->file('image')->store('home-explore', 'public');
        }

        unset($data['image']);

        return $data;
    }
}
