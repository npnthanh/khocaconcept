@extends('layouts.admin')
@section('heading', 'Cấu hình website')
@section('content')
    @include('admin.settings.tabs')
    <form class="admin-card form-card" method="post" enctype="multipart/form-data" action="{{ $banner->exists ? route('admin.banners.update', $banner) : route('admin.banners.store') }}">
        @csrf @if($banner->exists) @method('PUT') @endif
        <div class="form-header-bar"><div><h2>{{ $banner->exists ? 'Chỉnh sửa: '.$banner->title : 'Slide / Banner mới' }}</h2><p class="form-subtitle">Slide sẽ xuất hiện trực tiếp tại khu vực trình chiếu đầu trang chủ (Hero Slider).</p></div><a class="btn-back" href="{{ route('admin.banners.index') }}">Quay lại</a></div>
        <div class="form-sections">
            <div class="form-section">
                <div class="form-section-head"><span class="section-badge">1</span><h3>Nội dung & Hình ảnh Slider</h3></div>
                <div class="form-grid">
                    <div class="form-group full"><label class="form-label">Tiêu đề slide <span class="req">*</span></label><input class="form-control" name="title" value="{{ old('title', $banner->title) }}" placeholder="Ví dụ: Đắc nhân tâm hoặc Không gian Trưng Bày Sách Nghệ Thuật" required></div>
                    <div class="form-group"><label class="form-label">Kicker / Nhãn nhỏ (Eyebrow)</label><input class="form-control" name="eyebrow" value="{{ old('eyebrow', $banner->eyebrow) }}" placeholder="Ví dụ: KỸ NĂNG SỐNG / ẤN BẢN NGHỆ THUẬT"></div>
                    <div class="form-group"><label class="form-label">Vị trí hiển thị chữ</label><select class="form-control" name="text_position"><option value="left" @selected(old('text_position', $banner->text_position ?? 'left') === 'left')>Bên trái (Mặc định - Phù hợp ảnh có vật thể bên phải)</option><option value="right" @selected(old('text_position', $banner->text_position ?? 'left') === 'right')>Bên phải (Phù hợp ảnh có sách / vật thể bên trái)</option><option value="center" @selected(old('text_position', $banner->text_position ?? 'left') === 'center')>Chính giữa (Căn giữa nội dung)</option></select></div>
                    <div class="form-group"><label class="form-label">Chữ trên nút bấm</label><input class="form-control" name="link_label" value="{{ old('link_label', $banner->link_label) }}" placeholder="Khám phá ấn bản"></div>
                    <div class="form-group"><label class="form-label">Đường dẫn khi bấm nút</label><input class="form-control" type="text" name="link_url" value="{{ old('link_url', $banner->link_url) }}" placeholder="Ví dụ: /books/dac-nhan-tam hoặc https://…"></div>
                    <div class="form-group full">
                        <label class="form-label">Ảnh nền Slide cho Máy tính / PC <span class="sub-label">(Kích thước khuyến nghị: 1920x800px hoặc tỷ lệ 16:9; JPG, PNG, WEBP tối đa 10MB)</span></label>
                        <input class="form-control" type="file" name="image" data-max-mb="10" accept="image/jpeg,image/png,image/webp">
                        @if($banner->image_url)
                            <div style="margin-top: 10px;">
                                <img class="banner-preview" src="{{ $banner->image_url }}" alt="Banner PC hiện tại" style="max-height: 140px; border-radius: 6px; object-fit: cover; border: 1px solid #e2e8f0;">
                            </div>
                        @endif
                    </div>

                    <div class="form-group full">
                        <label class="form-label">Ảnh nền Slide riêng cho Điện thoại / Mobile <span class="sub-label">(Tùy chọn - Khuyến nghị tỷ lệ dọc 4:5 hoặc 9:16 như 1080x1350px hoặc 1080x1920px; JPG, PNG, WEBP tối đa 10MB)</span></label>
                        <input class="form-control" type="file" name="mobile_image" data-max-mb="10" accept="image/jpeg,image/png,image/webp">
                        @if($banner->mobile_image_url)
                            <div style="margin-top: 10px; display: flex; align-items: center; gap: 16px; background: #f8fafc; padding: 12px; border-radius: 8px; border: 1px solid #e2e8f0;">
                                <img class="banner-preview" src="{{ $banner->mobile_image_url }}" alt="Banner Mobile hiện tại" style="max-height: 150px; max-width: 120px; border-radius: 6px; object-fit: cover; border: 1px solid #cbd5e1;">
                                <div>
                                    <span style="font-size: 13px; font-weight: 600; color: #0f172a; display: block; margin-bottom: 6px;">Đang sử dụng ảnh Mobile riêng biệt</span>
                                    <label style="display: inline-flex; align-items: center; gap: 6px; font-size: 12.5px; color: #dc2626; cursor: pointer; font-weight: 500;">
                                        <input type="checkbox" name="delete_mobile_image" value="1"> ✕ Xóa ảnh Mobile (quay lại dùng chung ảnh PC)
                                    </label>
                                </div>
                            </div>
                        @else
                            <p class="sub-label" style="margin-top: 6px; color: #64748b;">
                                💡 <em>Nếu để trống, hệ thống sẽ tự động sử dụng ảnh PC với bố cục tối ưu riêng cho điện thoại.</em>
                            </p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="form-section">
                <div class="form-section-head"><span class="section-badge">2</span><h3>Lịch hiển thị</h3></div>
                <div class="form-grid">
                    <div class="form-group"><label class="form-label">Vị trí</label><select class="form-control" name="placement"><option value="home">Trang chủ</option></select></div>
                    <div class="form-group"><label class="form-label">Thứ tự</label><input class="form-control" type="number" min="0" name="sort_order" value="{{ old('sort_order', $banner->sort_order ?? 0) }}"></div>
                    <div class="form-group"><label class="form-label">Bắt đầu</label><input class="form-control" type="datetime-local" name="starts_at" value="{{ old('starts_at', $banner->starts_at?->format('Y-m-d\TH:i')) }}"></div>
                    <div class="form-group"><label class="form-label">Kết thúc</label><input class="form-control" type="datetime-local" name="ends_at" value="{{ old('ends_at', $banner->ends_at?->format('Y-m-d\TH:i')) }}"></div>
                    <div class="form-group full"><div class="status-toggle-card"><input type="hidden" name="is_active" value="0"><label class="toggle-switch"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $banner->exists ? $banner->is_active : true))><span class="toggle-slider"></span></label><div class="toggle-info"><strong>Bật banner</strong><span>Banner chỉ hiện khi đang bật và nằm trong khoảng thời gian đã đặt.</span></div></div></div>
                </div>
            </div>
        </div>
        <div class="form-footer-actions"><a class="admin-button secondary" href="{{ route('admin.banners.index') }}">Hủy</a><button class="admin-button" type="submit">{{ $banner->exists ? 'Cập nhật' : 'Tạo banner' }}</button></div>
    </form>
@endsection
