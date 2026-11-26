@extends('layouts.admin')
@section('heading', 'Quản lý Sản phẩm Quà tặng (Gifts)')
@section('content')
    <div class="admin-card table-wrap">
        <div class="table-tools">
            <form class="admin-search" method="get">
                <input name="q" value="{{ $term }}" placeholder="Tìm theo tên sản phẩm quà tặng, mã SKU…">
                <button type="submit">Tìm</button>
            </form>
            <div style="display: flex; gap: 8px; align-items: center;">
                <a class="admin-button secondary" href="{{ route('gifts.index') }}" target="_blank" title="Xem trang Gift ngoài web">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                    <span>Xem trang Gift ngoài web ↗</span>
                </a>
                <a class="admin-button" href="{{ route('admin.gifts.create') }}">+ Thêm sản phẩm Gift</a>
            </div>
        </div>

        <form id="bulkGiftForm" class="bulk-toolbar" method="post" action="{{ route('admin.gifts.bulk') }}">
            @csrf
            <strong>Tác vụ hàng loạt</strong>
            <select name="action" id="bulkAction" required>
                <option value="">Chọn tác vụ…</option>
                <option value="publish">Hiển thị sản phẩm</option>
                <option value="unpublish">Ẩn sản phẩm</option>
                <option value="delete">Xóa sản phẩm đã chọn</option>
            </select>
            <button class="admin-button" type="submit">Áp dụng</button>
            <span class="help" data-selected-count>Chưa chọn sản phẩm</span>
        </form>

        <table class="data-table">
            <thead>
                <tr>
                    <th class="check-cell"><input type="checkbox" data-select-all aria-label="Chọn tất cả"></th>
                    <th style="width: 70px;">Ảnh</th>
                    <th>Sản phẩm Gift</th>
                    <th>Quy cách / Kích thước</th>
                    <th>Gallery</th>
                    <th>Tình trạng hàng</th>
                    <th>Trạng thái</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($gifts as $gift)
                    <tr>
                        <td class="check-cell"><input type="checkbox" name="gift_ids[]" value="{{ $gift->id }}" form="bulkGiftForm" data-gift-check></td>
                        <td>
                            <div style="width: 52px; height: 52px; border-radius: 6px; overflow: hidden; background: #f1f5f9; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center;">
                                @if($gift->cover_url)
                                    <img src="{{ $gift->cover_url }}" alt="{{ $gift->title }}" style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5"><rect x="3" y="8" width="18" height="13" rx="2"/><path d="M12 8v13"/><path d="M19 12v7a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-7"/><path d="M7.5 8a2.5 2.5 0 0 1 0-5A4.8 8 0 0 1 12 8a4.8 8 0 0 1 4.5-5 2.5 2.5 0 0 1 0 5"/></svg>
                                @endif
                            </div>
                        </td>
                        <td>
                            <a class="admin-link" href="{{ route('admin.gifts.edit', $gift) }}"><strong>{{ $gift->title }}</strong></a>
                            @if ($gift->brand)<br><span class="help">Thương hiệu: {{ $gift->brand }}</span>@endif
                            <br><span class="help">{{ $gift->sku ? 'Mã SKU: '.$gift->sku : 'Chưa có mã SKU' }}</span>
                        </td>
                        <td>{{ $gift->dimensions ?: '—' }}</td>
                        <td>
                            <a class="admin-link" href="{{ route('admin.gifts.edit', $gift) }}">{{ $gift->gallery_images_count }} ảnh</a>
                        </td>
                        <td>
                            @php
                                $statusLabels = [
                                    'available' => 'Đang có sẵn',
                                    'reserved' => 'Tạm giữ',
                                    'sold' => 'Hết hàng',
                                    'coming_soon' => 'Sắp về',
                                ];
                            @endphp
                            <span class="badge-status {{ $gift->availability_status }}">
                                {{ $statusLabels[$gift->availability_status] ?? 'Đang có sẵn' }}
                            </span>
                        </td>
                        <td><span @class(['status','off' => !$gift->is_published])>{{ $gift->is_published ? 'Hiển thị' : 'Ẩn' }}</span></td>
                        <td>
                            <div class="action-buttons">
                                <a class="btn-action btn-edit" href="{{ route('admin.gifts.edit', $gift) }}">Sửa</a>
                                <form class="inline-form" action="{{ route('admin.gifts.destroy', $gift) }}" method="post">
                                    @csrf @method('DELETE')
                                    <button class="btn-action btn-delete" type="button" onclick="confirmDelete(this, 'Bạn có chắc chắn muốn xóa sản phẩm Gift này và toàn bộ ảnh liên quan?')">Xóa</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 40px 20px; color: #64748b;">
                            <p style="font-size: 16px; margin-bottom: 8px;">Chưa có sản phẩm quà tặng nào trong hệ thống.</p>
                            <a class="admin-button" href="{{ route('admin.gifts.create') }}">+ Thêm sản phẩm Gift đầu tiên</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <x-pagination :paginator="$gifts" />
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const all = document.querySelector('[data-select-all]');
            const checks = () => Array.from(document.querySelectorAll('[data-gift-check]'));
            const count = document.querySelector('[data-selected-count]');
            const updateCount = () => {
                const selected = checks().filter((input) => input.checked).length;
                if (count) count.textContent = selected ? `Đã chọn ${selected} sản phẩm` : 'Chưa chọn sản phẩm';
            };
            all?.addEventListener('change', () => {
                checks().forEach((input) => input.checked = all.checked);
                updateCount();
            });
            checks().forEach((input) => input.addEventListener('change', updateCount));
        });
    </script>
@endsection
