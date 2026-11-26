@extends('layouts.admin')
@section('heading', 'Import sách từ Excel')
@section('content')
    <form class="admin-card form-card" method="post" enctype="multipart/form-data" action="{{ route('admin.books.import.store') }}">
        @csrf
        <div class="form-header-bar">
            <div>
                <h2>Chọn tệp dữ liệu Excel / CSV</h2>
                <p class="form-subtitle">Hỗ trợ định dạng <strong>.xlsx, .xls, .csv</strong>, dung lượng tối đa 10 MB. File sẽ được xem trước và kiểm tra từng dòng trước khi ghi vào hệ thống. <a class="admin-link" href="{{ asset('templates/import-sach-mau.csv') }}" download>Tải tệp CSV mẫu ↗</a></p>
            </div>
            <a class="btn-back" href="{{ route('admin.books.index') }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                Quay lại
            </a>
        </div>

        <div class="form-sections">
            <div class="form-section">
                <div class="form-group full">
                    <label class="form-label">Chọn tệp dữ liệu sách <span class="req">*</span></label>
                    <div class="file-upload-box">
                        <input type="file" name="file" id="importFile" accept=".xlsx,.xls,.csv" required onchange="updateImportFileName(this)">
                        <label for="importFile" class="file-upload-label">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                            <span id="importFileNameText">Bấm chọn hoặc kéo file Excel/CSV vào đây</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-head">
                    <span class="section-badge">i</span>
                    <h3>Các cột được hỗ trợ trong file Excel</h3>
                </div>
                <p class="help" style="margin-bottom: 10px;">
                    <code>title</code> (bắt buộc), <code>author</code>, <code>categories</code>, <code>publisher</code>, <code>language</code>, <code>book_condition</code>, <code>availability_status</code>, <code>published_year</code>, <code>volume_count</code>, <code>isbn</code>, <code>page_count</code>, <code>dimensions</code>, <code>summary</code>, <code>additional_information</code>, <code>is_published</code>.
                </p>
                <p class="help">
                    Nếu có nhiều thể loại, điền trong một ô và ngăn cách bởi dấu phẩy. Bản ghi có cùng mã sách (ISBN) sẽ được tự động cập nhật.
                </p>
                <div class="danger-note" style="margin-top: 14px; margin-bottom: 0;">
                    Ảnh bìa có thể tải lên sau trong phần chỉnh sửa sách. Tiến trình import sẽ không làm mất ảnh bìa hiện có.
                </div>
            </div>
        </div>

        <div class="form-footer-actions">
            <a class="admin-button secondary" href="{{ route('admin.books.index') }}">Hủy bỏ</a>
            <button class="admin-button" type="submit">Bắt đầu import</button>
        </div>
    </form>

    <script>
    function updateImportFileName(input) {
        const textSpan = document.getElementById('importFileNameText');
        if (input.files && input.files[0]) {
            textSpan.textContent = 'Đã chọn tệp: ' + input.files[0].name;
            textSpan.style.color = '#1B4332';
            textSpan.style.fontWeight = '600';
        }
    }
    </script>
@endsection
