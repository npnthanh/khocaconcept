@extends('layouts.admin')
@section('heading', $collection->exists ? 'Sửa bộ sưu tập' : 'Tạo bộ sưu tập')
@section('content')
    @php($selectedBooks = old('book_ids', $collection->exists ? $collection->books->modelKeys() : []))
    <form class="admin-card form-card" method="post" enctype="multipart/form-data" action="{{ $collection->exists ? route('admin.collections.update', $collection) : route('admin.collections.store') }}">
        @csrf
        @if($collection->exists) @method('PUT') @endif
        <div class="form-header-bar">
            <div><h2>{{ $collection->exists ? 'Chỉnh sửa: '.$collection->title : 'Bộ sưu tập mới' }}</h2><p class="form-subtitle">Một landing page riêng cho nhóm sách được tuyển chọn.</p></div>
            <a class="btn-back" href="{{ route('admin.collections.index') }}">Quay lại</a>
        </div>

        <div class="form-sections">
            <div class="form-section">
                <div class="form-section-head"><span class="section-badge">1</span><h3>Nội dung & hình ảnh</h3></div>
                <div class="form-grid">
                    <div class="form-group full"><label class="form-label">Tên bộ sưu tập <span class="req">*</span></label><input class="form-control" name="title" value="{{ old('title', $collection->title) }}" required></div>
                    <div class="form-group full"><label class="form-label">Tiêu đề phụ</label><input class="form-control" name="subtitle" value="{{ old('subtitle', $collection->subtitle) }}"></div>
                    <div class="form-group full"><label class="form-label">Mô tả</label><textarea class="form-control" name="description" rows="5">{{ old('description', $collection->description) }}</textarea></div>
                    <div class="form-group"><label class="form-label">Ảnh đại diện <span class="sub-label">(Kích thước khuyến nghị: 1600x1000px hoặc tỷ lệ 8:5; JPG, PNG, WEBP tối đa 5MB)</span></label><input class="form-control" type="file" name="cover" data-max-mb="5" accept="image/jpeg,image/png,image/webp">@if($collection->cover_url)<img class="seo-image-preview" src="{{ $collection->cover_url }}" alt="Ảnh bộ sưu tập">@endif</div>
                    <div class="form-group"><label class="form-label">Thứ tự hiển thị</label><input class="form-control" type="number" name="sort_order" min="0" value="{{ old('sort_order', $collection->sort_order ?? 0) }}"></div>
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-head"><span class="section-badge">2</span><h3>Chọn sách trong bộ sưu tập</h3></div>
                <p class="help">Có thể chọn nhiều sách. Thứ tự trong danh sách sẽ là thứ tự hiển thị ngoài website.</p>
                <div class="collection-book-picker">
                    @foreach ($books as $book)
                        <label><input type="checkbox" name="book_ids[]" value="{{ $book->id }}" @checked(in_array($book->id, $selectedBooks))><span><strong>{{ $book->title }}</strong><small>{{ $book->author ?: 'Chưa có tác giả' }}</small></span></label>
                    @endforeach
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-head"><span class="section-badge">3</span><h3>Hiển thị & SEO</h3></div>
                <div class="form-grid">
                    <div class="form-group"><label class="form-label">SEO title</label><input class="form-control" name="meta_title" value="{{ old('meta_title', $collection->meta_title) }}"></div>
                    <div class="form-group"><label class="form-label">SEO description</label><textarea class="form-control" name="meta_description" rows="3">{{ old('meta_description', $collection->meta_description) }}</textarea></div>
                    <div class="form-group"><label class="form-label">Ảnh chia sẻ (OG image) <span class="sub-label">(Kích thước khuyến nghị: 1200x630px hoặc tỷ lệ 1.91:1; JPG, PNG, WEBP tối đa 5MB)</span></label><input class="form-control" type="file" name="og_image_file" data-max-mb="5" accept="image/jpeg,image/png,image/webp"></div>
                    <div class="form-group full"><div class="status-toggle-card"><input type="hidden" name="is_published" value="0"><label class="toggle-switch"><input type="checkbox" name="is_published" value="1" @checked(old('is_published', $collection->exists ? $collection->is_published : true))><span class="toggle-slider"></span></label><div class="toggle-info"><strong>Hiển thị công khai</strong><span>Cho phép người xem truy cập landing page bộ sưu tập.</span></div></div></div>
                    <div class="form-group full"><div class="status-toggle-card"><input type="hidden" name="is_featured" value="0"><label class="toggle-switch"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $collection->exists ? $collection->is_featured : false))><span class="toggle-slider"></span></label><div class="toggle-info"><strong>Đưa lên trang chủ</strong><span>Hiển thị bộ sưu tập trong khu vực tuyển chọn.</span></div></div></div>
                </div>
            </div>
        </div>
        <div class="form-footer-actions"><a class="admin-button secondary" href="{{ route('admin.collections.index') }}">Hủy</a><button class="admin-button" type="submit">{{ $collection->exists ? 'Cập nhật' : 'Tạo bộ sưu tập' }}</button></div>
    </form>
@endsection
