@extends('layouts.admin')
@section('heading', 'Cấu hình website')
@section('content')
    @include('admin.settings.tabs')
    <div class="admin-card table-wrap">
        <div class="admin-actions">
            <div>
                <h2>Các ô khám phá trên trang chủ</h2>
                <p class="help">Chỉnh ảnh, nội dung, liên kết và thứ tự. Hiệu ứng ảnh chìm vẫn được giữ trên website.</p>
            </div>
            <a class="admin-button" href="{{ route('admin.explore.create') }}">+ Thêm ô khám phá</a>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Ảnh</th>
                    <th>Nội dung</th>
                    <th>Liên kết</th>
                    <th>Thứ tự</th>
                    <th>Trạng thái</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tiles as $tile)
                    <tr>
                        <td>
                            @if ($tile->image_url)
                                <img class="admin-table-thumb explore-table-thumb" src="{{ $tile->image_url }}" alt="">
                            @else
                                <span class="help">Ảnh mặc định</span>
                            @endif
                        </td>
                        <td>
                            <a class="admin-link" href="{{ route('admin.explore.edit', $tile) }}"><strong>{{ $tile->title }}</strong></a><br>
                            <span class="help">{{ $tile->subtitle ?: 'Khám phá bộ sưu tập →' }}</span>
                        </td>
                        <td>
                            @if ($tile->link_type === 'category')
                                {{ $tile->category?->name ?: 'Chưa chọn thể loại' }}
                            @else
                                <span class="help">{{ IlluminateSupportStr::limit($tile->link_url, 42) ?: 'Chưa có liên kết' }}</span>
                            @endif
                        </td>
                        <td>{{ $tile->sort_order }}</td>
                        <td><span @class(['status', 'off' => !$tile->is_active])>{{ $tile->is_active ? 'Hiển thị' : 'Đang ẩn' }}</span></td>
                        <td>
                            <div class="action-buttons">
                                <a class="btn-action btn-edit" href="{{ route('admin.explore.edit', $tile) }}">Sửa</a>
                                <form class="inline-form" action="{{ route('admin.explore.destroy', $tile) }}" method="post">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn-action btn-delete" type="button" onclick="confirmDelete(this, 'Xóa ô khám phá này?')">Xóa</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6">Chưa có cấu hình nào. Hãy thêm ô đầu tiên để thay thế các ô mặc định trên trang chủ.</td></tr>
                @endforelse
            </tbody>
        </table>

        <x-pagination :paginator="$tiles" />
    </div>
@endsection
