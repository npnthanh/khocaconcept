@extends('layouts.admin')
@section('heading', $category->exists ? 'Sửa thể loại' : 'Thêm thể loại mới')
@section('content')
    <form class="admin-card form-card" method="post" action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}">
        @csrf 
        @if($category->exists) @method('PUT') @endif

        <div class="form-header-bar">
            <div>
                <h2>{{ $category->exists ? 'Chỉnh sửa thể loại: '.$category->name : 'Tạo thể loại mới' }}</h2>
                <p class="form-subtitle">Điền thông tin nhóm sách để phân loại trên hệ thống.</p>
            </div>
            <a class="btn-back" href="{{ route('admin.categories.index') }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                Quay lại
            </a>
        </div>

        <div class="form-sections">
            <div class="form-section">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Tên thể loại <span class="req">*</span></label>
                        <input class="form-control" name="name" value="{{ old('name', $category->name) }}" placeholder="Ví dụ: Văn học, Kinh tế..." required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Thứ tự hiển thị</label>
                        <input class="form-control" type="number" min="0" name="sort_order" value="{{ old('sort_order', $category->sort_order ?? 0) }}" placeholder="0">
                    </div>

                    <div class="form-group full">
                        <label class="form-label">Mô tả thể loại</label>
                        <textarea class="form-control" name="description" rows="4" placeholder="Mô tả ngắn gọn về nhóm sách này...">{{ old('description', $category->description) }}</textarea>
                    </div>

                    <div class="form-group full">
                        <div class="status-toggle-card">
                            <input type="hidden" name="is_active" value="0">
                            <label class="toggle-switch">
                                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->exists ? $category->is_active : true))>
                                <span class="toggle-slider"></span>
                            </label>
                            <div class="toggle-info">
                                <strong>Hiển thị thể loại này</strong>
                                <span>Hiển thị thể loại này trên menu điều hướng và bộ lọc tìm kiếm.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-footer-actions">
            <a class="admin-button secondary" href="{{ route('admin.categories.index') }}">Hủy bỏ</a>
            <button class="admin-button" type="submit">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                {{ $category->exists ? 'Cập nhật thể loại' : 'Lưu thể loại mới' }}
            </button>
        </div>
    </form>
@endsection
