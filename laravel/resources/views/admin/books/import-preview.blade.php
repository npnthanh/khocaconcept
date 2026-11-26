@extends('layouts.admin')
@section('heading', 'Xem trước dữ liệu import')
@section('content')
    @php
        $validCount = collect($rows)->where('valid', true)->count();
        $errorCount = count($rows) - $validCount;
    @endphp

    <div class="stats import-preview-stats">
        <div class="stat-card"><span>Tổng số dòng</span><strong>{{ count($rows) }}</strong></div>
        <div class="stat-card"><span>Sẽ import</span><strong>{{ $validCount }}</strong></div>
        <div class="stat-card"><span>Có lỗi</span><strong>{{ $errorCount }}</strong></div>
    </div>

    <div class="admin-card table-wrap">
        <div class="admin-actions import-preview-header">
            <div>
                <h2>Kiểm tra trước khi ghi dữ liệu</h2>
                <p class="help">Các dòng hợp lệ sẽ được import khi bạn xác nhận. Dòng lỗi sẽ được bỏ qua và không làm dừng các dòng đúng.</p>
            </div>
            <a class="admin-button secondary" href="{{ route('admin.books.import.create') }}">Chọn file khác</a>
        </div>

        <table class="data-table import-preview-table">
            <thead>
                <tr><th>Dòng</th><th>Tựa sách</th><th>ISBN</th><th>Tác giả</th><th>Thể loại</th><th>Kiểm tra</th></tr>
            </thead>
            <tbody>
                @forelse ($rows as $item)
                    <tr class="{{ $item['valid'] ? '' : 'import-row-error' }}">
                        <td><strong>#{{ $item['row'] }}</strong></td>
                        <td>{{ $item['data']['title'] ?? '—' }}</td>
                        <td>{{ $item['data']['isbn'] ?? '—' }}</td>
                        <td>{{ $item['data']['author'] ?? '—' }}</td>
                        <td>{{ $item['data']['categories'] ?? '—' }}</td>
                        <td>
                            @if ($item['valid'])
                                <span class="status">Hợp lệ</span>
                            @else
                                <div class="import-errors">
                                    @foreach ($item['errors'] as $error)
                                        <span>{{ $error }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6">File không có dòng dữ liệu.</td></tr>
                @endforelse
            </tbody>
        </table>

        @if ($validCount > 0)
            <div class="form-footer-actions import-preview-actions">
                <a class="admin-button secondary" href="{{ route('admin.books.import.create') }}">Hủy</a>
                <form method="post" action="{{ route('admin.books.import.confirm') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <button class="admin-button" type="submit">Import {{ $validCount }} dòng hợp lệ</button>
                </form>
            </div>
        @endif
    </div>
@endsection
