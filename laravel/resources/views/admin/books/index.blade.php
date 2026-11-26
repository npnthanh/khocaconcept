@extends('layouts.admin')
@section('heading', 'Quản lý sách')
@section('content')
    <div class="admin-card table-wrap">
        <div class="table-tools">
            <form class="admin-search" method="get">
                <input name="q" value="{{ $term }}" placeholder="Tìm theo tựa sách, tác giả, mã ISBN…">
                <button type="submit">Tìm</button>
            </form>
            <div>
                <a class="admin-button secondary" href="{{ route('admin.books.import.create') }}">Import Excel</a>
                <a class="admin-button secondary" href="{{ route('admin.collections.index') }}">Bộ sưu tập</a>
                <a class="admin-button" href="{{ route('admin.books.create') }}">+ Thêm sách</a>
            </div>
        </div>

        <form id="bulkBookForm" class="bulk-toolbar" method="post" action="{{ route('admin.books.bulk') }}">
            @csrf
            <strong>Tác vụ hàng loạt</strong>
            <select name="action" id="bulkAction" required>
                <option value="">Chọn tác vụ…</option>
                <option value="publish">Hiển thị sách</option>
                <option value="unpublish">Ẩn sách</option>
                <option value="assign_category">Gán thêm thể loại</option>
                <option value="delete_images">Xóa toàn bộ ảnh gallery</option>
            </select>
            <select name="category_id" id="bulkCategory" hidden>
                <option value="">Chọn thể loại</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
            <button class="admin-button" type="submit">Áp dụng</button>
            <span class="help" data-selected-count>Chưa chọn sách</span>
        </form>

        <table class="data-table">
            <thead>
                <tr>
                    <th class="check-cell"><input type="checkbox" data-select-all aria-label="Chọn tất cả"></th>
                    <th>Sách</th><th>Tác giả</th><th>Thể loại</th><th>Gallery</th><th>Năm XB</th><th>Trạng thái</th><th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($books as $book)
                    <tr>
                        <td class="check-cell"><input type="checkbox" name="book_ids[]" value="{{ $book->id }}" form="bulkBookForm" data-book-check></td>
                        <td>
                            <a class="admin-link" href="{{ route('admin.books.edit', $book) }}"><strong>{{ $book->title }}</strong></a>
                            @if ($book->is_featured)<span class="featured-mark">Nổi bật</span>@endif
                            <br><span class="help">{{ $book->isbn ?: 'Chưa có mã ISBN' }}</span>
                        </td>
                        <td>{{ $book->author ?: '—' }}</td>
                        <td>{{ $book->categories->pluck('name')->join(', ') ?: '—' }}</td>
                        <td><a class="admin-link" href="{{ route('admin.books.edit', $book) }}">{{ $book->gallery_images_count }} ảnh</a></td>
                        <td>{{ $book->published_year ?: '—' }}</td>
                        <td><span @class(['status','off' => !$book->is_published])>{{ $book->is_published ? 'Hiển thị' : 'Ẩn' }}</span></td>
                        <td>
                            <div class="action-buttons">
                                <a class="btn-action btn-edit" href="{{ route('admin.books.edit', $book) }}">Sửa</a>
                                <form class="inline-form" action="{{ route('admin.books.destroy', $book) }}" method="post">
                                    @csrf @method('DELETE')
                                    <button class="btn-action btn-delete" type="button" onclick="confirmDelete(this, 'Bạn có chắc chắn muốn xóa cuốn sách này và toàn bộ ảnh liên quan?')">Xóa</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8">Chưa có sách phù hợp.</td></tr>
                @endforelse
            </tbody>
        </table>
        <x-pagination :paginator="$books" />
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const all = document.querySelector('[data-select-all]');
            const checks = () => Array.from(document.querySelectorAll('[data-book-check]'));
            const count = document.querySelector('[data-selected-count]');
            const updateCount = () => {
                const selected = checks().filter((input) => input.checked).length;
                if (count) count.textContent = selected ? `Đã chọn ${selected} sách` : 'Chưa chọn sách';
            };
            all?.addEventListener('change', () => {
                checks().forEach((input) => input.checked = all.checked);
                updateCount();
            });
            checks().forEach((input) => input.addEventListener('change', updateCount));

            const action = document.getElementById('bulkAction');
            const category = document.getElementById('bulkCategory');
            action?.addEventListener('change', () => {
                category.hidden = action.value !== 'assign_category';
                category.required = action.value === 'assign_category';
            });
        });
    </script>
@endsection
