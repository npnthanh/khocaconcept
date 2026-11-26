@extends('layouts.admin')
@section('heading', 'Tổng quan')
@section('content')
    <div class="stats"><div class="stat-card"><span>Tổng số sách</span><strong>{{ $bookCount }}</strong></div><div class="stat-card"><span>Gift</span><strong>{{ $giftCount }}</strong></div><div class="stat-card"><span>Đang hiển thị</span><strong>{{ $publishedBookCount }}</strong></div><div class="stat-card"><span>Thể loại sách</span><strong>{{ $categoryCount }}</strong></div></div>
    <div class="admin-card"><div class="admin-actions"><div><h2>Sách vừa cập nhật</h2><p class="help">Các tựa sách mới nhất trong hệ thống.</p></div><a class="admin-button" href="{{ route('admin.books.create') }}">+ Thêm sách</a></div>
        <div class="table-wrap"><table class="data-table"><thead><tr><th>Tựa sách</th><th>Tác giả</th><th>Thể loại</th><th>Trạng thái</th></tr></thead><tbody>@forelse($latestBooks as $book)<tr><td><a class="admin-link" href="{{ route('admin.books.edit', $book) }}">{{ $book->title }}</a></td><td>{{ $book->author ?: '—' }}</td><td>{{ $book->categories->pluck('name')->join(', ') ?: '—' }}</td><td><span @class(['status','off' => !$book->is_published])>{{ $book->is_published ? 'Hiển thị' : 'Ẩn' }}</span></td></tr>@empty<tr><td colspan="4">Chưa có sách. Hãy thêm cuốn đầu tiên.</td></tr>@endforelse</tbody></table></div>
    </div>
@endsection
