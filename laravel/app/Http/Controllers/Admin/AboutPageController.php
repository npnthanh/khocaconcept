<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AboutPageController extends Controller
{
    public static function getDefaults(): array
    {
        return [
            // Banner 1: Đầu trang (Hero Banner)
            'about_hero_badge' => 'THE TASCHEN SPIRIT & MANIFESTO',
            'about_hero_title' => 'Nơi Tri Thức Hòa Quyện Cùng Mỹ Thuật',
            'about_hero_quote' => '“Một cuốn sách nghệ thuật đích thực không chỉ để đọc, mà là một tác phẩm trưng bày lưu giữ linh hồn của sáng tạo và vẻ đẹp vượt thời gian.”',
            'about_hero_image' => asset('images/taschen/hero-printed-fabrics.jpg'),
            'about_hero_image_mobile' => '',

            // Section 2: Câu chuyện & Tôn chỉ
            'about_story_kicker' => 'CÂU CHUYỆN & TÔN CHỈ',
            'about_story_heading' => 'Hành Trình Tôn Vinh Ấn Bản Sách Độc Bản',
            'about_story_quote' => '“Chúng tôi tin rằng mỗi trang sách là một phòng triển lãm thu nhỏ, nơi độc giả chạm vào tinh hoa văn hóa và thị giác.”',
            'about_story_quote_author' => '— BAN GIÁM TUYỂN & BIÊN TẬP',
            'about_story_lead' => 'Được khởi nguồn từ niềm đam mê sâu sắc với nghệ thuật xuất bản và văn hóa thưởng lãm sách, :site_name được định vị như một không gian triển lãm số cao cấp — nơi hội tụ những ấn phẩm tinh hoa nhất của văn học, truyện tranh kinh điển, nghệ thuật thị giác và các chuyên khảo quý hiếm.',
            'about_story_p1' => 'Lấy cảm hứng từ ngôn ngữ thiết kế và tiêu chuẩn mỹ thuật của nhà xuất bản Taschen huyền thoại, chúng tôi tiếp cận từng cuốn sách không chỉ như một nguồn tri thức, mà là một hiện vật thẩm mỹ trọn vẹn: từ chất liệu bìa, kỹ nghệ đóng sách, nghệ thuật sắp chữ (typography) cho đến trải nghiệm thị giác sống động trên từng trang in.',
            'about_story_p2' => 'Tại đây, độc giả có thể khám phá những bộ sưu tập được giám tuyển cẩn trọng theo từng chủ đề độc đáo, tra cứu thông số kỹ thuật chi tiết của từng ấn bản và tìm kiếm nguồn cảm hứng bất tận từ những trang sách vượt thời gian.',

            // Banner 2: Khối triển lãm / Sưu tầm (Split Banner)
            'about_split_kicker' => 'EXHIBITION & COLLECTING',
            'about_split_title' => 'Nghệ Thuật Sưu Tầm & Thưởng Lãm Sách',
            'about_split_quote' => '“Mỗi ấn phẩm là cánh cửa bước vào vũ trụ sáng tạo của các tác giả, họa sĩ và nhà làm sách hàng đầu.”',
            'about_split_text' => '',
            'about_split_btn_text' => 'Duyệt bộ sưu tập ấn bản',
            'about_split_btn_link' => '',
            'about_split_image' => asset('images/taschen/hero-star-wars.jpg'),
            'about_split_image_mobile' => '',
        ];
    }

    public function edit()
    {
        $defaults = self::getDefaults();
        $siteName = Setting::get('site_name', 'KHO - CA');
        $defaults['about_story_lead'] = str_replace(':site_name', $siteName, $defaults['about_story_lead']);

        $aboutData = [];
        foreach ($defaults as $key => $defaultVal) {
            $val = Setting::get($key, $defaultVal);
            if ($key === 'about_split_text' && is_string($val) && str_contains($val, 'Từ những cuốn truyện tranh kinh điển')) {
                $val = '';
            }
            $aboutData[$key] = $val;
        }

        // Add flags for whether custom images are used
        $aboutData['has_custom_hero_image'] = (bool) Setting::get('about_hero_image');
        $aboutData['has_custom_hero_image_mobile'] = (bool) Setting::get('about_hero_image_mobile');
        $aboutData['has_custom_split_image'] = (bool) Setting::get('about_split_image');
        $aboutData['has_custom_split_image_mobile'] = (bool) Setting::get('about_split_image_mobile');

        return view('admin.about.form', compact('aboutData'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            // Banner 1: Đầu trang
            'about_hero_badge' => ['nullable', 'string', 'max:255'],
            'about_hero_title' => ['required', 'string', 'max:255'],
            'about_hero_quote' => ['nullable', 'string', 'max:1000'],
            'about_hero_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:10240'],
            'remove_about_hero_image' => ['nullable', 'boolean'],
            'about_hero_image_mobile' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:10240'],
            'remove_about_hero_image_mobile' => ['nullable', 'boolean'],

            // Câu chuyện & Tôn chỉ
            'about_story_kicker' => ['nullable', 'string', 'max:255'],
            'about_story_heading' => ['nullable', 'string', 'max:255'],
            'about_story_quote' => ['nullable', 'string', 'max:1000'],
            'about_story_quote_author' => ['nullable', 'string', 'max:255'],
            'about_story_lead' => ['nullable', 'string'],
            'about_story_p1' => ['nullable', 'string'],
            'about_story_p2' => ['nullable', 'string'],

            // Banner 2: Khối triển lãm / Sưu tầm
            'about_split_kicker' => ['nullable', 'string', 'max:255'],
            'about_split_title' => ['required', 'string', 'max:255'],
            'about_split_quote' => ['nullable', 'string', 'max:1000'],
            'about_split_text' => ['nullable', 'string'],
            'about_split_btn_text' => ['nullable', 'string', 'max:100'],
            'about_split_btn_link' => ['nullable', 'string', 'max:500'],
            'about_split_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:10240'],
            'remove_about_split_image' => ['nullable', 'boolean'],
            'about_split_image_mobile' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:10240'],
            'remove_about_split_image_mobile' => ['nullable', 'boolean'],
        ]);

        // Handle Hero Image Upload & Removal
        if ($request->boolean('remove_about_hero_image')) {
            $oldPath = Setting::get('about_hero_image_path');
            if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
            Setting::set('about_hero_image_path', null);
            Setting::set('about_hero_image', null);
        } elseif ($request->hasFile('about_hero_image')) {
            $oldPath = Setting::get('about_hero_image_path');
            if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('about_hero_image')->store('about', 'public');
            $url = Storage::disk('public')->url($path);
            Setting::set('about_hero_image_path', $path);
            Setting::set('about_hero_image', $url);

            // Copy to public_html/storage fallback if exists
            $dest = base_path('../public_html/storage/'.$path);
            if (!file_exists(dirname($dest))) {
                @mkdir(dirname($dest), 0755, true);
            }
            @copy($request->file('about_hero_image')->getRealPath(), $dest);
        }

        // Handle Hero Mobile Image Upload & Removal
        if ($request->boolean('remove_about_hero_image_mobile')) {
            $oldPath = Setting::get('about_hero_image_mobile_path');
            if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
            Setting::set('about_hero_image_mobile_path', null);
            Setting::set('about_hero_image_mobile', null);
        } elseif ($request->hasFile('about_hero_image_mobile')) {
            $oldPath = Setting::get('about_hero_image_mobile_path');
            if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('about_hero_image_mobile')->store('about/mobile', 'public');
            $url = Storage::disk('public')->url($path);
            Setting::set('about_hero_image_mobile_path', $path);
            Setting::set('about_hero_image_mobile', $url);

            // Copy to public_html/storage fallback if exists
            $dest = base_path('../public_html/storage/'.$path);
            if (!file_exists(dirname($dest))) {
                @mkdir(dirname($dest), 0755, true);
            }
            @copy($request->file('about_hero_image_mobile')->getRealPath(), $dest);
        }

        // Handle Split Image Upload & Removal
        if ($request->boolean('remove_about_split_image')) {
            $oldPath = Setting::get('about_split_image_path');
            if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
            Setting::set('about_split_image_path', null);
            Setting::set('about_split_image', null);
        } elseif ($request->hasFile('about_split_image')) {
            $oldPath = Setting::get('about_split_image_path');
            if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('about_split_image')->store('about', 'public');
            $url = Storage::disk('public')->url($path);
            Setting::set('about_split_image_path', $path);
            Setting::set('about_split_image', $url);

            // Copy to public_html/storage fallback if exists
            $dest = base_path('../public_html/storage/'.$path);
            if (!file_exists(dirname($dest))) {
                @mkdir(dirname($dest), 0755, true);
            }
            @copy($request->file('about_split_image')->getRealPath(), $dest);
        }

        // Handle Split Mobile Image Upload & Removal
        if ($request->boolean('remove_about_split_image_mobile')) {
            $oldPath = Setting::get('about_split_image_mobile_path');
            if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
            Setting::set('about_split_image_mobile_path', null);
            Setting::set('about_split_image_mobile', null);
        } elseif ($request->hasFile('about_split_image_mobile')) {
            $oldPath = Setting::get('about_split_image_mobile_path');
            if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('about_split_image_mobile')->store('about/mobile', 'public');
            $url = Storage::disk('public')->url($path);
            Setting::set('about_split_image_mobile_path', $path);
            Setting::set('about_split_image_mobile', $url);

            // Copy to public_html/storage fallback if exists
            $dest = base_path('../public_html/storage/'.$path);
            if (!file_exists(dirname($dest))) {
                @mkdir(dirname($dest), 0755, true);
            }
            @copy($request->file('about_split_image_mobile')->getRealPath(), $dest);
        }

        // Save Text Fields
        $textFields = [
            'about_hero_badge',
            'about_hero_title',
            'about_hero_quote',
            'about_story_kicker',
            'about_story_heading',
            'about_story_quote',
            'about_story_quote_author',
            'about_story_lead',
            'about_story_p1',
            'about_story_p2',
            'about_split_kicker',
            'about_split_title',
            'about_split_quote',
            'about_split_text',
            'about_split_btn_text',
            'about_split_btn_link',
        ];

        foreach ($textFields as $field) {
            Setting::set($field, $data[$field] ?? '');
        }

        return back()->with('success', 'Đã cập nhật nội dung và hình ảnh trang Giới thiệu (About Us) thành công.');
    }

    public function resetDefault()
    {
        $keys = array_keys(self::getDefaults());
        $keys[] = 'about_hero_image_path';
        $keys[] = 'about_hero_image_mobile_path';
        $keys[] = 'about_split_image_path';
        $keys[] = 'about_split_image_mobile_path';

        // Delete uploaded files
        foreach (['about_hero_image_path', 'about_hero_image_mobile_path', 'about_split_image_path', 'about_split_image_mobile_path'] as $pathKey) {
            $path = Setting::get($pathKey);
            if ($path && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }

        foreach ($keys as $key) {
            Setting::set($key, null);
        }

        return back()->with('success', 'Đã khôi phục toàn bộ nội dung trang Giới thiệu về mặc định ban đầu.');
    }
}
