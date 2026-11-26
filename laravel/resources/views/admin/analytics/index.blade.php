@extends('layouts.admin')
@section('heading', 'Thống kê website')
@section('content')
    <div class="admin-card analytics-filter"><form method="get"><label>Khoảng thời gian</label><select name="range" onchange="this.form.submit()"><option value="7" @selected($range === 7)>7 ngày</option><option value="30" @selected($range === 30)>30 ngày</option><option value="90" @selected($range === 90)>90 ngày</option><option value="365" @selected($range === 365)>365 ngày</option></select></form></div>
    <div class="stats analytics-stats">
        <div class="stat-card"><span>Lượt xem sản phẩm</span><strong>{{ number_format($bookViews) }}</strong><small>Sách và Gift · {{ $range }} ngày gần nhất</small></div>
        <div class="stat-card"><span>Lượt tìm kiếm</span><strong>{{ number_format($searches) }}</strong><small>Tổng từ khóa đã ghi nhận</small></div>
        <div class="stat-card"><span>Bấm Zalo</span><strong>{{ number_format($zaloClicks) }}</strong><small>{{ $range }} ngày gần nhất</small></div>
        <div class="stat-card"><span>Bấm Email</span><strong>{{ number_format($emailClicks) }}</strong><small>{{ $range }} ngày gần nhất</small></div>
    </div>

    <div class="analytics-columns">
        <div class="admin-card table-wrap"><div class="admin-actions"><div><h2>Sách được xem nhiều</h2><p class="help">Dựa trên lượt mở trang chi tiết.</p></div></div><table class="data-table"><thead><tr><th>Sách</th><th>Lượt xem</th></tr></thead><tbody>@forelse($topBooks as $item)<tr><td>{{ $item->book?->title ?: 'Sách đã xóa' }}</td><td><strong>{{ number_format($item->views) }}</strong></td></tr>@empty<tr><td colspan="2">Chưa có dữ liệu.</td></tr>@endforelse</tbody></table></div>
        <div class="admin-card table-wrap"><div class="admin-actions"><div><h2>Gift được xem nhiều</h2><p class="help">Theo dõi riêng, không trộn với bảng Sách.</p></div></div><table class="data-table"><thead><tr><th>Sản phẩm</th><th>Lượt xem</th></tr></thead><tbody>@forelse($topGifts as $item)<tr><td>{{ $item->gift?->title ?: 'Gift đã xóa' }}</td><td><strong>{{ number_format($item->views) }}</strong></td></tr>@empty<tr><td colspan="2">Chưa có dữ liệu.</td></tr>@endforelse</tbody></table></div>
        <div class="admin-card table-wrap"><div class="admin-actions"><div><h2>Từ khóa không có kết quả</h2><p class="help">Giúp bổ sung nội dung mà người xem đang tìm.</p></div></div><table class="data-table"><thead><tr><th>Từ khóa</th><th>Lần không có kết quả</th></tr></thead><tbody>@forelse($noResultSearches as $query)<tr><td>{{ $query->term }}</td><td><strong>{{ number_format($query->no_result_hits) }}</strong></td></tr>@empty<tr><td colspan="2">Chưa có từ khóa nào.</td></tr>@endforelse</tbody></table></div>
    </div>

    <div class="admin-card table-wrap"><div class="admin-actions"><div><h2>Lượt xem theo ngày</h2><p class="help">Tổng lượt mở trang chi tiết trong {{ $range }} ngày gần nhất.</p></div></div><table class="data-table"><thead><tr><th>Ngày</th><th>Lượt xem</th></tr></thead><tbody>@forelse($dailyViews as $day)<tr><td>{{ \Carbon\Carbon::parse($day->day)->format('d/m/Y') }}</td><td>{{ number_format($day->views) }}</td></tr>@empty<tr><td colspan="2">Chưa có dữ liệu.</td></tr>@endforelse</tbody></table></div>
@endsection
