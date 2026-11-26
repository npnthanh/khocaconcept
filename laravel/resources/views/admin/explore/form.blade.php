@extends('layouts.admin')
@section('heading', 'Cấu hình website')
@section('content')
    @include('admin.settings.tabs')
    <form class="admin-card form-card" method="post" enctype="multipart/form-data" action="{{ $tile->exists ? route('admin.explore.update', $tile) : route('admin.explore.store') }}">
        @csrf
        @if($tile->exists) @method('PUT') @endif

        <div class="form-header-bar">
            <div>
                <h2>{{ $tile->exists ? 'Chỉnh sửa: '.$tile->title : 'Ô khám phá mới' }}</h2>
                <p class="form-subtitle">Ảnh sẽ được phủ lớp gradient tối để giữ đúng hiệu ứng ảnh chìm của giao diện hiện tại.</p>
            </div>
            <a class="btn-back" href="{{ route('admin.explore.index') }}">Quay lại</a>
        </div>

        <div class="form-sections">
            <div class="form-section">
                <div class="form-section-head"><span class="section-badge">1</span><h3>Nội dung hiển thị</h3></div>
                <div class="form-grid">
                    <div class="form-group full">
                        <label class="form-label">Tiêu đề <span class="req">*</span></label>
                        <input class="form-control" name="title" value="{{ old('title', $tile->title) }}" placeholder="Ví dụ: Artbook & Illustration" required>
                    </div>
                    <div class="form-group full">
                        <label class="form-label">Dòng phụ / nút kêu gọi</label>
                        <input class="form-control" name="subtitle" value="{{ old('subtitle', $tile->subtitle ?: 'Khám phá bộ sưu tập →') }}" placeholder="Ví dụ: Khám phá bộ sưu tập →">
                    </div>
                    <div class="form-group full">
                        <label class="form-label">Ảnh nền <span class="sub-label">Kích thước khuyến nghị: 1200x800px hoặc tỷ lệ 3:2; JPG, PNG, WEBP tối đa 5MB. Nếu bỏ trống sẽ dùng ảnh mặc định.</span></label>
                        <input class="form-control" type="file" name="image" data-max-mb="5" accept="image/jpeg,image/png,image/webp">
                        @if($tile->image_url)
                            <div class="explore-current-image">
                                <img src="{{ $tile->image_url }}" alt="Ảnh hiện tại">
                                <span>Ảnh hiện tại</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-head"><span class="section-badge">2</span><h3>Liên kết khi khách bấm</h3></div>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label" for="explore-link-type">Loại liên kết <span class="req">*</span></label>
                        <select class="form-control" name="link_type" id="explore-link-type" required onchange="toggleExploreLinkFields()">
                            <option value="category" @selected(old('link_type', $tile->link_type ?: 'category') === 'category')>Trang thể loại sách</option>
                            <option value="url" @selected(old('link_type', $tile->link_type) === 'url')>Đường dẫn tùy chỉnh</option>
                        </select>
                    </div>
                    <div class="form-group" id="explore-category-field">
                        <label class="form-label">Thể loại sách <span class="req">*</span></label>
                        <select class="form-control" name="category_id">
                            <option value="">-- Chọn thể loại --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected((string) old('category_id', $tile->category_id) === (string) $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group full" id="explore-url-field">
                        <label class="form-label">Đường dẫn tùy chỉnh <span class="req">*</span></label>
                        <input class="form-control" name="link_url" value="{{ old('link_url', $tile->link_url) }}" placeholder="https://... hoặc /sach?category=...">
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-head"><span class="section-badge">3</span><h3>Hiển thị & hiệu ứng ảnh</h3></div>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Thứ tự hiển thị</label>
                        <input class="form-control" type="number" name="sort_order" min="0" max="9999" value="{{ old('sort_order', $tile->sort_order ?? 0) }}">
                        <span class="form-help">Số nhỏ hơn sẽ đứng trước.</span>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Mức độ ảnh chìm <span id="explore-opacity-value">{{ (int) round((float) old('overlay_opacity', $tile->overlay_opacity ?? 0.78) * 100) }}%</span></label>
                        <input class="form-control" type="range" name="overlay_opacity" min="0.35" max="0.95" step="0.01" value="{{ old('overlay_opacity', $tile->overlay_opacity ?? 0.78) }}" oninput="document.getElementById('explore-opacity-value').textContent = Math.round(this.value * 100) + '%'">
                        <span class="form-help">Giữ trong khoảng này để chữ trắng luôn dễ đọc.</span>
                    </div>
                    <div class="form-group full">
                        <div class="status-toggle-card">
                            <input type="hidden" name="is_active" value="0">
                            <label class="toggle-switch"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $tile->exists ? $tile->is_active : true))><span class="toggle-slider"></span></label>
                            <div class="toggle-info"><strong>Hiển thị ô này trên trang chủ</strong><span>Có thể tạm ẩn mà không cần xóa cấu hình.</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-footer-actions">
            <a class="admin-button secondary" href="{{ route('admin.explore.index') }}">Hủy</a>
            <button class="admin-button" type="submit">{{ $tile->exists ? 'Cập nhật' : 'Thêm ô khám phá' }}</button>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    function toggleExploreLinkFields() {
        const type = document.getElementById('explore-link-type')?.value;
        const categoryField = document.getElementById('explore-category-field');
        const urlField = document.getElementById('explore-url-field');
        if (!categoryField || !urlField) return;
        categoryField.style.display = type === 'category' ? '' : 'none';
        urlField.style.display = type === 'url' ? '' : 'none';
    }

    document.addEventListener('DOMContentLoaded', toggleExploreLinkFields);
</script>
@endpush
