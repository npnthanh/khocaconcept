<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function edit()
    {
        $settings = [
            'site_name' => Setting::get('site_name', 'KHO - CA'),
            'site_logo' => Setting::get('site_logo'),
            'favicon' => Setting::get('favicon'),
            'footer_description' => Setting::get('footer_description', 'Nhà trưng bày và tôn vinh những ấn bản sách, truyện tranh nghệ thuật đặc sắc. Nơi tri thức hòa quyện cùng mỹ thuật thị giác.'),
            'footer_copyright' => Setting::get('footer_copyright', '© '.date('Y').' KHO - CA. Mọi quyền được bảo lưu.'),
            'site_tagline' => Setting::get('site_tagline', 'Không gian trưng bày, khám phá và tôn vinh sách nghệ thuật cao cấp'),
            'contact_phone' => Setting::get('contact_phone'),
            'contact_email' => Setting::get('contact_email'),
            'contact_zalo' => Setting::get('contact_zalo'),
            'contact_facebook' => Setting::get('contact_facebook') ?: Setting::get('contact_messenger'),
            'contact_messenger' => Setting::get('contact_messenger'),
        ];

        return view('admin.settings.form', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'footer_description' => ['nullable', 'string'],
            'footer_copyright' => ['nullable', 'string', 'max:255'],
            'site_tagline' => ['nullable', 'string', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:32'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_zalo' => ['nullable', 'string', 'max:255'],
            'contact_facebook' => ['nullable', 'string', 'max:500'],
            'contact_messenger' => ['nullable', 'string', 'max:500'],
            'logo' => ['nullable', 'image', 'max:5120'],
            'favicon' => ['nullable', 'file', 'mimes:ico,png,jpg,jpeg,webp', 'max:1024'],
            'remove_favicon' => ['nullable', 'boolean'],
        ], [
            'logo.image' => 'Logo phải là tệp hình ảnh hợp lệ.',
            'logo.max' => 'Logo quá lớn. Vui lòng chọn ảnh tối đa 5MB.',
            'favicon.file' => 'Favicon không hợp lệ.',
            'favicon.mimes' => 'Favicon chỉ hỗ trợ ICO, PNG, JPG hoặc WEBP.',
            'favicon.max' => 'Favicon quá lớn. Vui lòng chọn tệp tối đa 1MB.',
        ]);

        Setting::set('site_name', $data['site_name']);
        Setting::set('footer_description', $data['footer_description'] ?? '');
        Setting::set('footer_copyright', $data['footer_copyright'] ?? '');
        Setting::set('site_tagline', $data['site_tagline'] ?? '');
        Setting::set('contact_phone', $data['contact_phone'] ?? '');
        Setting::set('contact_email', $data['contact_email'] ?? '');
        Setting::set('contact_zalo', $data['contact_zalo'] ?? '');
        Setting::set('contact_facebook', $data['contact_facebook'] ?? ($data['contact_messenger'] ?? ''));
        Setting::set('contact_messenger', $data['contact_facebook'] ?? ($data['contact_messenger'] ?? ''));

        if ($request->boolean('remove_favicon') && ! $request->hasFile('favicon')) {
            $oldFaviconPath = Setting::get('favicon_path');
            if ($oldFaviconPath && Storage::disk('public')->exists($oldFaviconPath)) {
                Storage::disk('public')->delete($oldFaviconPath);
            }

            Setting::set('favicon_path', null);
            Setting::set('favicon', null);
        }

        if ($request->hasFile('logo')) {
            $oldLogoPath = Setting::get('site_logo_path');
            if ($oldLogoPath && Storage::disk('public')->exists($oldLogoPath)) {
                Storage::disk('public')->delete($oldLogoPath);
            }

            $path = $request->file('logo')->store('settings', 'public');
            $url = Storage::url($path);
            Setting::set('site_logo_path', $path);
            Setting::set('site_logo', $url);

            // Copy to public/images/logo.png for instant static fallback
            @copy($request->file('logo')->getRealPath(), public_path('images/logo.png'));
            if (file_exists(base_path('../public_html/images/logo.png'))) {
                @copy($request->file('logo')->getRealPath(), base_path('../public_html/images/logo.png'));
            }
        }

        if ($request->hasFile('favicon')) {
            $oldFaviconPath = Setting::get('favicon_path');
            if ($oldFaviconPath && Storage::disk('public')->exists($oldFaviconPath)) {
                Storage::disk('public')->delete($oldFaviconPath);
            }

            $path = $request->file('favicon')->store('settings', 'public');
            Setting::set('favicon_path', $path);
            Setting::set('favicon', Storage::disk('public')->url($path));
        }

        return back()->with('success', 'Đã cập nhật cấu hình website thành công.');
    }
}
