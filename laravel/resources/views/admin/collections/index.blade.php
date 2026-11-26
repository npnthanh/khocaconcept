@extends('layouts.admin')
@section('heading', 'Bộ sưu tập')
@section('content')
    <div class="admin-card table-wrap">
        <div class="admin-actions">
            <p class="help">Tạo các landing page tuyển chọn riêng, có thể tối ưu SEO và đưa lên trang chủ.</p>
            <a class="admin-button" href="{{ route('admin.collections.create') }}">+ Tạo bộ sưu tập</a>
        </div>
        <table class="data-table">
            <thead><tr><th>Tên bộ sưu tập</th><th>Số sách</th><th>Thứ tự</th><th>Trạng thái</th><th></th></tr></thead>
            <tbody>
                @forelse ($collections as $collection)
                    <tr>
                        <td><a class="admin-link" href="{{ route('admin.collections.edit', $collection) }}"><strong>{{ $collection->title }}</strong></a><br><span class="help">/bo-suu-tap/{{ $collection->slug }}</span></td>
                        <td>{{ $collection->books_count }}</td>
                        <td>{{ $collection->sort_order }}</td>
                        <td><span @class(['status', 'off' => !$collection->is_published])>{{ $collection->is_published ? 'Hiển thị' : 'Ẩn' }}</span> @if($collection->is_featured)<span class="featured-mark">Trang chủ</span>@endif</td>
                        <td>
                            <div class="action-buttons">
                                <a class="btn-action btn-edit" href="{{ route('admin.collections.edit', $collection) }}">Sửa</a>
                                <form class="inline-form" action="{{ route('admin.collections.destroy', $collection) }}" method="post">
                                    @csrf @method('DELETE')
                                    <button class="btn-action btn-delete" type="button" onclick="confirmDelete(this, 'Xóa bộ sưu tập này?')">Xóa</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5">Chưa có bộ sưu tập.</td></tr>
                @endforelse
            </tbody>
        </table>
        <x-pagination :paginator="$collections" />
    </div>
@endsection
