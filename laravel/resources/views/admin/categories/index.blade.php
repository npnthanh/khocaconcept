@extends('layouts.admin')
@section('heading', 'Thể loại')
@section('content')
    <div class="admin-card table-wrap"><div class="admin-actions"><p class="help">Quản lý các nhóm sách trên website.</p><a class="admin-button" href="{{ route('admin.categories.create') }}">+ Thêm thể loại</a></div>
    <table class="data-table"><thead><tr><th>Tên thể loại</th><th>Mô tả</th><th>Số sách</th><th>Trạng thái</th><th></th></tr></thead><tbody>@forelse($categories as $category)<tr><td><strong>{{ $category->name }}</strong></td><td>{{ \Illuminate\Support\Str::limit($category->description, 70) }}</td><td>{{ $category->books_count }}</td><td><span @class(['status','off' => !$category->is_active])>{{ $category->is_active ? 'Hiển thị' : 'Ẩn' }}</span></td><td>
    <div class="action-buttons">
        <a class="btn-action btn-edit" href="{{ route('admin.categories.edit', $category) }}">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            <span>Sửa</span>
        </a>
        <form class="inline-form" action="{{ route('admin.categories.destroy', $category) }}" method="post">
            @csrf @method('DELETE')
            <button class="btn-action btn-delete" type="button" onclick="confirmDelete(this, 'Bạn có chắc chắn muốn xóa thể loại này?')">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                <span>Xóa</span>
            </button>
        </form>
    </div>
</td></tr>@empty<tr><td colspan="5">Chưa có thể loại.</td></tr>@endforelse</tbody></table><x-pagination :paginator="$categories" /></div>
@endsection
