@extends('layouts.admin')
@section('heading', 'Cấu hình website')
@section('content')
    @include('admin.settings.tabs')
    <form class="admin-card form-card" method="post" enctype="multipart/form-data" action="{{ route('admin.settings.update') }}">
        @csrf

        <div class="form-header-bar">
            <div>
                <h2>Cài đặt chung Website</h2>
                <p class="form-subtitle">Tùy chỉnh tên thương hiệu, Logo đại diện và nội dung chân trang (Footer).</p>
            </div>
        </div>

        <div class="form-sections">
            <!-- Section 1: Nhận diện thương hiệu -->
            <div class="form-section">
                <div class="form-section-head">
                    <span class="section-badge">1</span>
                    <h3>Thương hiệu & Logo</h3>
                </div>
                <div class="form-grid">
                    <div class="form-group full">
                        <label class="form-label">Tên website <span class="req">*</span></label>
                        <input class="form-control" name="site_name" value="{{ old('site_name', $settings['site_name']) }}" placeholder="Ví dụ: KHO - CA" required>
                    </div>

                    <div class="form-group full">
                        <label class="form-label">Khẩu hiệu / Thẻ mô tả ngắn (Tagline)</label>
                        <input class="form-control" name="site_tagline" value="{{ old('site_tagline', $settings['site_tagline']) }}" placeholder="Ví dụ: Không gian trưng bày, khám phá và tôn vinh sách nghệ thuật cao cấp">
                    </div>

                    <div class="form-group full">
                        <label class="form-label">Logo / Avatar Website <span class="sub-label">(Kích thước khuyến nghị: 600x180px hoặc tỷ lệ 10:3; PNG, JPG, WEBP tối đa 5MB)</span></label>
                        <div class="file-upload-box">
                            <input type="file" name="logo" id="logoInput" data-max-mb="5" accept="image/jpeg,image/png,image/webp" onchange="updateLogoName(this)">
                            <label for="logoInput" class="file-upload-label">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                <span id="logoFileNameText">Bấm chọn hoặc kéo file logo mới vào đây</span>
                            </label>
                        </div>
                        <div class="current-cover-wrap" style="margin-top: 14px;">
                            <span class="cover-label">Logo hiện tại:</span>
                            <img class="brand-badge-img" src="{{ $settings['site_logo'] ?: asset('images/logo.png') }}" alt="Logo hiện tại" style="width: 48px; height: 48px;">
                        </div>
                    </div>

                    <div class="form-group full">
                        <label class="form-label">Favicon trình duyệt <span class="sub-label">(Kích thước khuyến nghị: 32x32px, 48x48px hoặc 64x64px; ICO, PNG, JPG, WEBP tối đa 1MB)</span></label>
                        <div class="file-upload-box">
                            <input type="file" name="favicon" id="faviconInput" data-max-mb="1" accept=".ico,image/x-icon,image/png,image/jpeg,image/webp" onchange="updateFaviconName(this)">
                            <label for="faviconInput" class="file-upload-label">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                <span id="faviconFileNameText">Bấm chọn favicon mới</span>
                            </label>
                        </div>
                        <div class="current-cover-wrap" style="display: flex; align-items: center; gap: 14px; margin-top: 14px;">
                            <div style="display: grid; place-items: center; width: 48px; height: 48px; overflow: hidden; border: 1px solid #e2e8f0; border-radius: 10px; background: #f8fafc;">
                                <img src="{{ $settings['favicon'] ?: ($settings['site_logo'] ?: asset('images/logo.png')) }}" alt="Favicon hiện tại" style="width: 32px; height: 32px; object-fit: contain;">
                            </div>
                            <div>
                                <span class="cover-label">Favicon hiện tại</span>
                                <p class="help" style="margin: 4px 0 0;">Hiển thị trên tab trình duyệt và khi khách lưu website.</p>
                                @if ($settings['favicon'])
                                    <label style="display: inline-flex; align-items: center; gap: 7px; margin-top: 8px; color: #64748b; font-size: 12px;">
                                        <input type="checkbox" name="remove_favicon" value="1">
                                        Xóa favicon tùy chỉnh và dùng ảnh mặc định
                                    </label>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Chân trang (Footer) -->
            <div class="form-section">
                <div class="form-section-head">
                    <span class="section-badge">2</span>
                    <h3>Thông tin Chân trang (Footer)</h3>
                </div>
                <div class="form-grid">
                    <div class="form-group full">
                        <label class="form-label">Mô tả chân trang</label>
                        <textarea class="form-control" name="footer_description" rows="3" placeholder="Đoạn văn ngắn giới thiệu ở chân trang...">{{ old('footer_description', $settings['footer_description']) }}</textarea>
                    </div>

                    <div class="form-group full">
                        <label class="form-label">Dòng bản quyền (Copyright)</label>
                        <input class="form-control" name="footer_copyright" value="{{ old('footer_copyright', $settings['footer_copyright']) }}" placeholder="Ví dụ: © 2026 Trưng Bày Sách. Tất cả các quyền được bảo lưu.">
                    </div>
                </div>
            </div>

            <!-- Section 3: Kênh liên hệ -->
            <div class="form-section">
                <div class="form-section-head">
                    <span class="section-badge">3</span>
                    <h3>Kênh liên hệ trực tuyến (Facebook / Zalo / Email)</h3>
                </div>
                <p class="help" style="margin: -4px 0 18px;">Các liên kết này sẽ xuất hiện trong nút <strong>"Liên hệ trực tuyến"</strong> trên trang chi tiết sách để khách hàng trao đổi trực tiếp.</p>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Facebook / Fanpage <span class="sub-label">(Link trang Facebook hoặc Messenger)</span></label>
                        <input class="form-control" name="contact_facebook" value="{{ old('contact_facebook', $settings['contact_facebook'] ?? $settings['contact_messenger']) }}" placeholder="Ví dụ: https://facebook.com/ten-trang hoặc https://m.me/ten-trang">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Zalo <span class="sub-label">(Số điện thoại hoặc link Zalo)</span></label>
                        <input class="form-control" name="contact_zalo" value="{{ old('contact_zalo', $settings['contact_zalo']) }}" placeholder="Ví dụ: 0901234567 hoặc https://zalo.me/0901234567">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email nhận liên hệ</label>
                        <input class="form-control" type="email" name="contact_email" value="{{ old('contact_email', $settings['contact_email']) }}" placeholder="Ví dụ: contact@example.com">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Số điện thoại hotline</label>
                        <input class="form-control" name="contact_phone" value="{{ old('contact_phone', $settings['contact_phone']) }}" placeholder="Ví dụ: 0901 234 567">
                    </div>
                </div>
            </div>
        </div>

        <div class="form-footer-actions">
            <button class="admin-button" type="submit">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                Lưu cấu hình
            </button>
        </div>
    </form>

    <script>
    function updateLogoName(input) {
        const textSpan = document.getElementById('logoFileNameText');
        if (input.files && input.files[0]) {
            textSpan.textContent = 'Đã chọn: ' + input.files[0].name;
            textSpan.style.color = '#1B4332';
            textSpan.style.fontWeight = '600';
        }
    }

    function updateFaviconName(input) {
        const textSpan = document.getElementById('faviconFileNameText');
        if (input.files && input.files[0]) {
            textSpan.textContent = 'Đã chọn: ' + input.files[0].name;
            textSpan.style.color = '#1B4332';
            textSpan.style.fontWeight = '600';
        }
    }
    </script>
@endsection
