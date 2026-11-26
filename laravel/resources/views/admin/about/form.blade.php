@extends('layouts.admin')
@section('heading', 'Cấu hình website')
@section('content')
    @include('admin.settings.tabs')
    <div class="policy-admin-container">
        <!-- Main Edit Card -->
        <div class="admin-card form-card">
            <div class="form-header-bar policy-header-bar" style="flex-wrap: wrap; gap: 16px;">
                <div>
                    <h2>Quản lý Nội dung &amp; 2 Banner Trang About Us</h2>
                    <p class="form-subtitle">
                        Tùy chỉnh hình ảnh 2 banner (đầu trang &amp; cuối trang) cùng toàn bộ các đoạn văn bản, trích dẫn hiển thị trên trang Giới thiệu.
                    </p>
                </div>
                <div class="policy-header-actions" style="display: flex; gap: 10px; align-items: center;">
                    <a class="btn-preview-link" href="{{ route('about') }}" target="_blank" title="Xem trang ngoài website">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                        <span>Xem trang About Us ↗</span>
                    </a>

                    <form method="post" action="{{ route('admin.about.reset') }}" onsubmit="return confirm('Bạn có chắc chắn muốn khôi phục toàn bộ nội dung &amp; hình ảnh trang About Us về mặc định ban đầu không?');" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn-reset-default" title="Khôi phục nội dung gốc ban đầu" style="background: #f8fafc; border: 1px solid #cbd5e1; color: #475569; padding: 7px 14px; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
                            <span>Khôi phục mặc định</span>
                        </button>
                    </form>
                </div>
            </div>

            <form method="post" action="{{ route('admin.about.update') }}" enctype="multipart/form-data" id="aboutPageForm">
                @csrf

                <div class="form-sections">
                    <!-- SECTION 1: BANNER ĐẦU TRANG (HERO BANNER) -->
                    <div class="form-section">
                        <div class="form-section-head">
                            <span class="section-badge">1</span>
                            <h3>Banner 1: Hero Banner Đầu Trang</h3>
                        </div>
                        <p class="help" style="margin: -4px 0 18px;">Khung banner toàn cảnh xuất hiện đầu tiên khi người đọc truy cập vào trang About Us.</p>

                        <div class="form-grid">
                            <div class="form-group full">
                                <label class="form-label">Ảnh nền Banner đầu trang (Bản Desktop / Mặc định) <span class="sub-label">(Kích thước khuyến nghị: 1920x800px hoặc tỷ lệ 16:9; JPG, PNG, WEBP, GIF tối đa 10MB)</span></label>
                                <div class="file-upload-box">
                                    <input type="file" name="about_hero_image" id="heroImageInput" data-max-mb="10" accept="image/jpeg,image/png,image/webp,image/gif" onchange="previewUploadImage(this, 'heroPreviewImg', 'heroFileNameText')">
                                    <label for="heroImageInput" class="file-upload-label">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                        <span id="heroFileNameText">Bấm để chọn hoặc kéo thả ảnh banner desktop mới vào đây</span>
                                    </label>
                                </div>

                                <div class="current-cover-wrap" style="margin-top: 14px; display: flex; align-items: flex-start; gap: 16px;">
                                    <div style="position: relative; border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0; width: 260px; height: 130px; background: #111;">
                                        <img id="heroPreviewImg" src="{{ $aboutData['about_hero_image'] }}" alt="Ảnh Banner 1 hiện tại" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                    <div style="display: flex; flex-direction: column; gap: 8px; justify-content: center;">
                                        <span class="cover-label" style="font-weight: 600; color: #334155;">
                                            {{ $aboutData['has_custom_hero_image'] ? 'Ảnh banner Desktop đang sử dụng (Đã tải lên)' : 'Ảnh banner Desktop mặc định hệ thống' }}
                                        </span>
                                        @if($aboutData['has_custom_hero_image'])
                                            <label style="display: inline-flex; align-items: center; gap: 6px; font-size: 13px; color: #b91c1c; cursor: pointer;">
                                                <input type="checkbox" name="remove_about_hero_image" value="1">
                                                <span>Xóa ảnh desktop đã tải lên để quay lại ảnh mặc định</span>
                                            </label>
                                        @endif
                                        <small style="color: #64748b;">Kích thước khuyến nghị Desktop: 1920 x 800px hoặc tỷ lệ 16:9.</small>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group full" style="border-top: 1px dashed #e2e8f0; padding-top: 18px; margin-top: 6px;">
                                <label class="form-label">
                                    Ảnh Banner đầu trang dành riêng cho Mobile <span class="sub-label">(Tùy chọn - Khuyến nghị tỷ lệ dọc 4:5 hoặc 9:16 như 1080x1350px hoặc 1080x1920px; JPG, PNG, WEBP, GIF tối đa 10MB)</span>
                                </label>
                                <div class="file-upload-box">
                                    <input type="file" name="about_hero_image_mobile" id="heroMobileImageInput" data-max-mb="10" accept="image/jpeg,image/png,image/webp,image/gif" onchange="previewUploadImage(this, 'heroMobilePreviewImg', 'heroMobileFileNameText')">
                                    <label for="heroMobileImageInput" class="file-upload-label">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                        <span id="heroMobileFileNameText">Bấm để chọn ảnh banner dành riêng cho Mobile (hoặc để trống để dùng ảnh Desktop)</span>
                                    </label>
                                </div>

                                <div class="current-cover-wrap" style="margin-top: 14px; display: flex; align-items: flex-start; gap: 16px;">
                                    <div style="position: relative; border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0; width: 140px; height: 160px; background: #111; display: flex; align-items: center; justify-content: center;">
                                        @if(!empty($aboutData['about_hero_image_mobile']))
                                            <img id="heroMobilePreviewImg" src="{{ $aboutData['about_hero_image_mobile'] }}" alt="Ảnh Banner Mobile hiện tại" style="width: 100%; height: 100%; object-fit: cover;">
                                        @else
                                            <img id="heroMobilePreviewImg" src="{{ $aboutData['about_hero_image'] }}" alt="Chưa có banner mobile" style="width: 100%; height: 100%; object-fit: cover; opacity: 0.6;">
                                        @endif
                                    </div>
                                    <div style="display: flex; flex-direction: column; gap: 8px; justify-content: center;">
                                        <span class="cover-label" style="font-weight: 600; color: #334155;">
                                            {{ $aboutData['has_custom_hero_image_mobile'] ? 'Đang dùng Banner riêng cho Mobile' : 'Chưa tải ảnh riêng (Đang dùng ảnh Desktop tự động co dãn)' }}
                                        </span>
                                        @if($aboutData['has_custom_hero_image_mobile'])
                                            <label style="display: inline-flex; align-items: center; gap: 6px; font-size: 13px; color: #b91c1c; cursor: pointer;">
                                                <input type="checkbox" name="remove_about_hero_image_mobile" value="1">
                                                <span>Xóa banner Mobile (Sử dụng lại ảnh Desktop)</span>
                                            </label>
                                        @endif
                                        <small style="color: #64748b;">Kích thước khuyến nghị Mobile: 1080 x 1350px hoặc 1080 x 1920px (Tỷ lệ 4:5 hoặc 9:16 dọc).</small>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Nhãn nhỏ đầu trang (Badge / Kicker)</label>
                                <input class="form-control" name="about_hero_badge" value="{{ old('about_hero_badge', $aboutData['about_hero_badge']) }}" placeholder="Ví dụ: THE TASCHEN SPIRIT & MANIFESTO">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Tiêu đề chính Banner (H1) <span class="req">*</span></label>
                                <input class="form-control" name="about_hero_title" value="{{ old('about_hero_title', $aboutData['about_hero_title']) }}" placeholder="Ví dụ: Nơi Tri Thức Hòa Quyện Cùng Mỹ Thuật" required>
                            </div>

                            <div class="form-group full">
                                <label class="form-label">Đoạn trích dẫn / Slogan dưới tiêu đề</label>
                                <textarea class="form-control" name="about_hero_quote" rows="2" placeholder="Ví dụ: “Một cuốn sách nghệ thuật đích thực không chỉ để đọc, mà là một tác phẩm trưng bày lưu giữ linh hồn...”">{{ old('about_hero_quote', $aboutData['about_hero_quote']) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 2: CÂU CHUYỆN & TÔN CHỈ (EDITORIAL NARRATIVE) -->
                    <div class="form-section">
                        <div class="form-section-head">
                            <span class="section-badge">2</span>
                            <h3>Nội dung: Câu chuyện &amp; Tôn chỉ (Giữa trang)</h3>
                        </div>
                        <p class="help" style="margin: -4px 0 18px;">Phần bài viết chia sẻ về sứ mệnh và cảm hứng nghệ thuật xuất bản.</p>

                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">Nhãn chuyên mục (Kicker)</label>
                                <input class="form-control" name="about_story_kicker" value="{{ old('about_story_kicker', $aboutData['about_story_kicker']) }}" placeholder="Ví dụ: CÂU CHUYỆN & TÔN CHỈ">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Tiêu đề phần câu chuyện</label>
                                <input class="form-control" name="about_story_heading" value="{{ old('about_story_heading', $aboutData['about_story_heading']) }}" placeholder="Ví dụ: Hành Trình Tôn Vinh Ấn Bản Sách Độc Bản">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Trích dẫn nổi bật trong khung đen</label>
                                <textarea class="form-control" name="about_story_quote" rows="2" placeholder="Ví dụ: “Chúng tôi tin rằng mỗi trang sách là một phòng triển lãm thu nhỏ...”">{{ old('about_story_quote', $aboutData['about_story_quote']) }}</textarea>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Người phát ngôn trích dẫn</label>
                                <input class="form-control" name="about_story_quote_author" value="{{ old('about_story_quote_author', $aboutData['about_story_quote_author']) }}" placeholder="Ví dụ: — BAN GIÁM TUYỂN & BIÊN TẬP">
                            </div>

                            <div class="form-group full">
                                <label class="form-label">Đoạn văn mở đầu (Lead paragraph)</label>
                                <textarea class="form-control" name="about_story_lead" rows="3" placeholder="Đoạn văn giới thiệu nổi bật...">{{ old('about_story_lead', $aboutData['about_story_lead']) }}</textarea>
                            </div>

                            <div class="form-group full">
                                <label class="form-label">Đoạn văn chi tiết 1 (Về mỹ thuật &amp; in ấn)</label>
                                <textarea class="form-control" name="about_story_p1" rows="3" placeholder="Đoạn văn chi tiết thứ nhất...">{{ old('about_story_p1', $aboutData['about_story_p1']) }}</textarea>
                            </div>

                            <div class="form-group full">
                                <label class="form-label">Đoạn văn chi tiết 2 (Về trải nghiệm độc giả)</label>
                                <textarea class="form-control" name="about_story_p2" rows="3" placeholder="Đoạn văn chi tiết thứ hai...">{{ old('about_story_p2', $aboutData['about_story_p2']) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 3: BANNER CUỐI TRANG / SPLIT BANNER (SƯU TẦM & THƯỞNG LÃM) -->
                    <div class="form-section">
                        <div class="form-section-head">
                            <span class="section-badge">3</span>
                            <h3>Banner 2: Khối Triển Lãm &amp; Sưu Tầm Sách (Cuối trang)</h3>
                        </div>
                        <p class="help" style="margin: -4px 0 18px;">Khối banner 50/50 gồm ảnh minh họa nghệ thuật bên trái và nội dung ấn bản kèm nút bấm bên phải.</p>

                        <div class="form-grid">
                            <div class="form-group full">
                                <label class="form-label">Ảnh minh họa Banner 2 (Bản Desktop / Mặc định) <span class="sub-label">(Kích thước khuyến nghị: 1200x900px hoặc tỷ lệ 4:3; JPG, PNG, WEBP, GIF tối đa 10MB)</span></label>
                                <div class="file-upload-box">
                                    <input type="file" name="about_split_image" id="splitImageInput" data-max-mb="10" accept="image/jpeg,image/png,image/webp,image/gif" onchange="previewUploadImage(this, 'splitPreviewImg', 'splitFileNameText')">
                                    <label for="splitImageInput" class="file-upload-label">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                        <span id="splitFileNameText">Bấm để chọn hoặc kéo thả ảnh minh họa desktop mới vào đây</span>
                                    </label>
                                </div>

                                <div class="current-cover-wrap" style="margin-top: 14px; display: flex; align-items: flex-start; gap: 16px;">
                                    <div style="position: relative; border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0; width: 220px; height: 140px; background: #27104e;">
                                        <img id="splitPreviewImg" src="{{ $aboutData['about_split_image'] }}" alt="Ảnh Banner 2 hiện tại" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                    <div style="display: flex; flex-direction: column; gap: 8px; justify-content: center;">
                                        <span class="cover-label" style="font-weight: 600; color: #334155;">
                                            {{ $aboutData['has_custom_split_image'] ? 'Ảnh banner Desktop đang sử dụng (Đã tải lên)' : 'Ảnh banner Desktop mặc định hệ thống' }}
                                        </span>
                                        @if($aboutData['has_custom_split_image'])
                                            <label style="display: inline-flex; align-items: center; gap: 6px; font-size: 13px; color: #b91c1c; cursor: pointer;">
                                                <input type="checkbox" name="remove_about_split_image" value="1">
                                                <span>Xóa ảnh desktop đã tải lên để quay lại ảnh mặc định</span>
                                            </label>
                                        @endif
                                        <small style="color: #64748b;">Kích thước khuyến nghị Desktop: 1200 x 900px (Tỷ lệ 4:3).</small>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group full" style="border-top: 1px dashed #e2e8f0; padding-top: 18px; margin-top: 6px;">
                                <label class="form-label">
                                    Ảnh Banner 2 dành riêng cho Mobile <span class="sub-label">(Tùy chọn - Khuyến nghị tỷ lệ dọc 4:5 hoặc 9:16 như 1080x1350px hoặc 1080x1920px; JPG, PNG, WEBP, GIF tối đa 10MB)</span>
                                </label>
                                <div class="file-upload-box">
                                    <input type="file" name="about_split_image_mobile" id="splitMobileImageInput" data-max-mb="10" accept="image/jpeg,image/png,image/webp,image/gif" onchange="previewUploadImage(this, 'splitMobilePreviewImg', 'splitMobileFileNameText')">
                                    <label for="splitMobileImageInput" class="file-upload-label">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                        <span id="splitMobileFileNameText">Bấm để chọn ảnh banner 2 riêng cho Mobile (hoặc để trống để dùng ảnh Desktop)</span>
                                    </label>
                                </div>

                                <div class="current-cover-wrap" style="margin-top: 14px; display: flex; align-items: flex-start; gap: 16px;">
                                    <div style="position: relative; border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0; width: 140px; height: 160px; background: #27104e; display: flex; align-items: center; justify-content: center;">
                                        @if(!empty($aboutData['about_split_image_mobile']))
                                            <img id="splitMobilePreviewImg" src="{{ $aboutData['about_split_image_mobile'] }}" alt="Ảnh Banner 2 Mobile hiện tại" style="width: 100%; height: 100%; object-fit: cover;">
                                        @else
                                            <img id="splitMobilePreviewImg" src="{{ $aboutData['about_split_image'] }}" alt="Chưa có banner mobile" style="width: 100%; height: 100%; object-fit: cover; opacity: 0.6;">
                                        @endif
                                    </div>
                                    <div style="display: flex; flex-direction: column; gap: 8px; justify-content: center;">
                                        <span class="cover-label" style="font-weight: 600; color: #334155;">
                                            {{ $aboutData['has_custom_split_image_mobile'] ? 'Đang dùng Banner 2 riêng cho Mobile' : 'Chưa tải ảnh riêng (Đang dùng ảnh Desktop tự động co dãn)' }}
                                        </span>
                                        @if($aboutData['has_custom_split_image_mobile'])
                                            <label style="display: inline-flex; align-items: center; gap: 6px; font-size: 13px; color: #b91c1c; cursor: pointer;">
                                                <input type="checkbox" name="remove_about_split_image_mobile" value="1">
                                                <span>Xóa banner 2 Mobile (Sử dụng lại ảnh Desktop)</span>
                                            </label>
                                        @endif
                                        <small style="color: #64748b;">Kích thước khuyến nghị Mobile: 1080 x 1350px hoặc 1080 x 1920px (Tỷ lệ 4:5 hoặc 9:16 dọc).</small>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Nhãn nhỏ (Kicker)</label>
                                <input class="form-control" name="about_split_kicker" value="{{ old('about_split_kicker', $aboutData['about_split_kicker']) }}" placeholder="Ví dụ: EXHIBITION & COLLECTING">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Tiêu đề Banner 2 <span class="req">*</span></label>
                                <input class="form-control" name="about_split_title" value="{{ old('about_split_title', $aboutData['about_split_title']) }}" placeholder="Ví dụ: Nghệ Thuật Sưu Tầm & Thưởng Lãm Sách" required>
                            </div>

                            <div class="form-group full">
                                <label class="form-label">Câu trích dẫn ngắn (Quote)</label>
                                <textarea class="form-control" name="about_split_quote" rows="2" placeholder="Ví dụ: “Mỗi ấn phẩm là cánh cửa bước vào vũ trụ sáng tạo...”">{{ old('about_split_quote', $aboutData['about_split_quote']) }}</textarea>
                            </div>

                            <div class="form-group full">
                                <label class="form-label">Đoạn văn mô tả chi tiết</label>
                                <textarea class="form-control" name="about_split_text" rows="3" placeholder="Đoạn văn giới thiệu bộ sưu tập...">{{ old('about_split_text', $aboutData['about_split_text']) }}</textarea>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Tên nút bấm (Button Text)</label>
                                <input class="form-control" name="about_split_btn_text" value="{{ old('about_split_btn_text', $aboutData['about_split_btn_text']) }}" placeholder="Ví dụ: Duyệt bộ sưu tập ấn bản">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Link chuyển hướng khi bấm nút</label>
                                <input class="form-control" name="about_split_btn_link" value="{{ old('about_split_btn_link', $aboutData['about_split_btn_link']) }}" placeholder="Để trống sẽ mặc định trỏ về trang /sach">
                            </div>
                    </div>
                </div>

                <div class="form-footer-actions" style="margin-top: 32px; display: flex; justify-content: flex-end; gap: 12px;">
                    <button class="admin-button" type="submit" style="min-width: 180px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                        Lưu thay đổi About Us
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function previewUploadImage(input, previewId, textSpanId) {
        const textSpan = document.getElementById(textSpanId);
        const previewImg = document.getElementById(previewId);
        if (input.files && input.files[0]) {
            const file = input.files[0];
            if (textSpan) {
                textSpan.textContent = 'Đã chọn file: ' + file.name;
                textSpan.style.color = '#1B4332';
                textSpan.style.fontWeight = '600';
            }
            if (previewImg) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }
    }
    </script>
@endsection
