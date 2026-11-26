@extends('layouts.admin')
@section('heading', 'Cấu hình website')
@section('content')
    @include('admin.settings.tabs')
    <div class="admin-card table-wrap">
        <div class="admin-actions">
            <p class="help">Quản lý hình ảnh và nội dung trình chiếu (Hero Slider) trên đầu trang chủ. Bạn có thể thêm nhiều slide, tải ảnh bìa chất lượng cao, đặt tiêu đề và liên kết nút bấm.</p>
            <a class="admin-button" href="{{ route('admin.banners.create') }}">+ Thêm Slide / Banner mới</a>
        </div>
        <table class="data-table">
            <thead><tr><th>Banner</th><th>Vị trí</th><th>Lịch hiển thị</th><th>Thứ tự</th><th>Trạng thái</th><th></th></tr></thead>
            <tbody>
                @forelse ($banners as $banner)
                    <tr>
                        <td>@if($banner->image_url)<img class="admin-table-thumb" src="{{ $banner->image_url }}" alt="">@endif<strong>{{ $banner->title }}</strong><br><span class="help">{{ $banner->eyebrow ?: '—' }} • Vị trí chữ: {{ ($banner->text_position ?? 'left') === 'right' ? 'Bên phải' : (($banner->text_position ?? 'left') === 'center' ? 'Chính giữa' : 'Bên trái') }}</span></td>
                        <td>{{ $banner->placement }}</td>
                        <td><span class="help">{{ $banner->starts_at?->format('d/m/Y H:i') ?: 'Ngay' }} → {{ $banner->ends_at?->format('d/m/Y H:i') ?: 'Không hạn' }}</span></td>
                        <td>{{ $banner->sort_order }}</td>
                        <td><span @class(['status', 'off' => !$banner->is_active])>{{ $banner->is_active ? 'Đang bật' : 'Đang tắt' }}</span></td>
                        <td><div class="action-buttons"><a class="btn-action btn-edit" href="{{ route('admin.banners.edit', $banner) }}">Sửa</a><form class="inline-form" action="{{ route('admin.banners.destroy', $banner) }}" method="post">@csrf @method('DELETE')<button class="btn-action btn-delete" type="button" onclick="confirmDelete(this, 'Xóa banner này?')">Xóa</button></form></div></td>
                    </tr>
                @empty
                    <tr><td colspan="6">Chưa có banner.</td></tr>
                @endforelse
            </tbody>
        </table>
        <x-pagination :paginator="$banners" />
    </div>
@endsection
