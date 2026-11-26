@extends('layouts.admin')
@section('heading', $book->exists ? 'Sửa thông tin sách' : 'Thêm sách mới')
@section('content')
    <form class="admin-card form-card" method="post" enctype="multipart/form-data" action="{{ $book->exists ? route('admin.books.update', $book) : route('admin.books.store') }}">
        @csrf 
        @if($book->exists) @method('PUT') @endif

        <div class="form-header-bar">
            <div>
                <h2>{{ $book->exists ? 'Chỉnh sửa: '.$book->title : 'Tạo cuốn sách mới' }}</h2>
                <p class="form-subtitle">Điền thông tin chi tiết dưới đây để hiển thị lên thư viện.</p>
            </div>
            <a class="btn-back" href="{{ route('admin.books.index') }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                Quay lại
            </a>
        </div>

        <div class="form-sections">
            <!-- Section 1: Thông tin cơ bản -->
            <div class="form-section">
                <div class="form-section-head">
                    <span class="section-badge">1</span>
                    <h3>Thông tin cơ bản</h3>
                </div>
                <div class="form-grid">
                    <div class="form-group full">
                        <label class="form-label">Tựa đề sách <span class="req">*</span></label>
                        <input class="form-control" name="title" value="{{ old('title', $book->title) }}" placeholder="Nhập tên sách..." required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tác giả</label>
                        <input class="form-control" name="author" value="{{ old('author', $book->author) }}" placeholder="Tên tác giả">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Số trang</label>
                        <input class="form-control" type="number" min="1" name="page_count" value="{{ old('page_count', $book->page_count) }}" placeholder="Ví dụ: 280">
                    </div>

                    <div class="form-group full">
                        <label class="form-label">Kích thước</label>
                        <input class="form-control" name="dimensions" value="{{ old('dimensions', $book->dimensions) }}" placeholder="Ví dụ: 13 x 20.5 cm hoặc 25 x 33.4 cm">
                    </div>
                </div>
            </div>

            <!-- Section 2: Ảnh bìa & Thư viện ảnh -->
            <!-- Section 2: Ảnh bìa & Gallery -->
            <div class="form-section">
                <div class="form-section-head">
                    <span class="section-badge">2</span>
                    <h3>Ảnh bìa & Gallery</h3>
                </div>
                <div class="form-grid">
                    <!-- Ảnh bìa chính -->
                    <div class="form-group full">
                        <label class="form-label">Ảnh bìa chính <span class="sub-label">(Kích thước khuyến nghị: 1200x1800px hoặc tỷ lệ 2:3; JPG, PNG, WEBP tối đa 5MB)</span></label>
                        <div class="file-upload-box">
                            <input type="file" name="cover" id="coverInput" data-max-mb="5" accept="image/jpeg,image/png,image/webp" onchange="handleCoverChange(this)">
                            <label for="coverInput" class="file-upload-label">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                <span id="fileNameText">Chọn hoặc kéo tệp ảnh bìa vào đây</span>
                            </label>
                        </div>

                        <!-- Live preview for selected cover -->
                        <div id="coverNewPreview" style="display: none;" class="admin-upload-preview-card">
                            <img id="coverPreviewImg" src="" alt="Xem trước ảnh bìa mới">
                            <div class="admin-upload-preview-info">
                                <strong>Ảnh bìa mới đã chọn</strong>
                                <span id="coverPreviewMeta"></span>
                            </div>
                            <button type="button" class="btn-action btn-delete" style="padding: 6px 12px;" onclick="clearCoverSelection()">Hủy chọn</button>
                        </div>

                        @if($book->cover_url)
                            <div class="current-cover-wrap" id="currentCoverWrap">
                                <span class="cover-label">Ảnh bìa hiện tại:</span>
                                <img class="cover-preview" src="{{ $book->cover_url }}" alt="Ảnh bìa hiện tại">
                            </div>
                        @endif
                    </div>

                    <!-- Ảnh gallery chi tiết -->
                    <div class="form-group full">
                        <label class="form-label">Ảnh gallery chi tiết <span class="sub-label">(Kích thước khuyến nghị: cạnh dài 1600–2400px, giữ tỷ lệ theo ảnh gốc; JPG, PNG, WEBP tối đa 10MB/ảnh, tối đa 12 ảnh)</span></label>
                        <div class="file-upload-box">
                            <input type="file" name="gallery[]" id="galleryInput" data-max-mb="10" accept="image/jpeg,image/png,image/webp" multiple onchange="handleGalleryFiles(this.files)">
                            <label for="galleryInput" class="file-upload-label">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                <span id="galleryFileNameText">+ Thêm ảnh vào gallery (Có thể chọn nhiều lần hoặc chọn nhiều ảnh cùng lúc)</span>
                            </label>
                        </div>

                        <p class="sub-label" style="margin-top: 6px; color: #64748b;">
                            💡 <em>Mẹo: Bạn có thể nhấn vào ô nhiều lần để thêm từng ảnh, hoặc giữ phím <strong>Ctrl</strong> (hoặc <strong>Shift</strong>) để chọn nhiều ảnh cùng lúc trong cửa sổ tệp.</em>
                        </p>

                        <!-- Live preview for newly selected gallery images -->
                        <div id="galleryBatchSection" style="display: none;">
                            <div class="admin-gallery-batch-header">
                                <span><strong id="galleryBatchCount">0</strong> ảnh mới sẵn sàng lưu:</span>
                                <button type="button" class="btn-action btn-delete" style="font-size: 11px; padding: 4px 8px;" onclick="clearAllNewGallery()">Xóa tất cả ảnh mới</button>
                            </div>
                            <div class="admin-preview-grid" id="galleryNewPreviewGrid"></div>
                        </div>

                        @if ($book->exists && $book->galleryImages->isNotEmpty())
                            <div style="margin-top: 20px;">
                                <span class="cover-label" style="display: block; margin-bottom: 8px;">Các ảnh đã lưu trong Gallery:</span>
                                <div class="admin-gallery-grid">
                                    @foreach ($book->galleryImages as $image)
                                        <div class="admin-gallery-item">
                                            <img src="{{ $image->url }}" alt="{{ $image->alt_text ?: 'Ảnh gallery '.$book->title }}">
                                            <div class="admin-gallery-item-footer">
                                                <span>Ảnh {{ $loop->iteration }}</span>
                                                <button class="btn-action btn-delete" type="button" onclick="deleteGalleryImage('{{ route('admin.books.gallery.destroy', [$book, $image]) }}')">Xóa</button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Section 3: Thể loại & Hiển thị -->
            <div class="form-section">
                <div class="form-section-head">
                    <span class="section-badge">3</span>
                    <h3>Thể loại & Trạng thái</h3>
                </div>
                
                <div class="form-group full">
                    <label class="form-label">Chọn thể loại <span class="req">*</span></label>
                    <div class="cat-chips">
                        @foreach($categories as $category)
                            <label class="cat-chip">
                                <input type="checkbox" name="category_ids[]" value="{{ $category->id }}" @checked(in_array($category->id, old('category_ids', $book->categories->modelKeys())))>
                                <span class="chip-body">
                                    <svg class="check-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                    {{ $category->name }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="form-group full" style="margin-top: 12px;">
                    <div class="status-toggle-card">
                        <input type="hidden" name="is_published" value="0">
                        <label class="toggle-switch">
                            <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $book->exists ? $book->is_published : true))>
                            <span class="toggle-slider"></span>
                        </label>
                        <div class="toggle-info">
                            <strong>Hiển thị sách công khai</strong>
                            <span>Hiển thị sách trên website và trong kết quả tra cứu của bạn đọc.</span>
                        </div>
                    </div>
                </div>

                <div class="form-grid" style="margin-top: 14px;">
                    <div class="form-group">
                        <label class="form-label">Thứ tự nổi bật</label>
                        <input class="form-control" type="number" min="0" name="featured_sort_order" value="{{ old('featured_sort_order', $book->featured_sort_order ?? 0) }}" placeholder="0">
                        <span class="sub-label">Số nhỏ hơn sẽ được ưu tiên hiển thị trước.</span>
                    </div>
                    <div class="form-group">
                        <div class="status-toggle-card">
                            <input type="hidden" name="is_featured" value="0">
                            <label class="toggle-switch">
                                <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $book->exists ? $book->is_featured : false))>
                                <span class="toggle-slider"></span>
                            </label>
                            <div class="toggle-info">
                                <strong>Đưa vào sách nổi bật</strong>
                                <span>Hiển thị trong khu vực tuyển chọn trên trang chủ.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 4: Mô tả & Tóm tắt -->
            <div class="form-section">
                <div class="form-section-head">
                    <span class="section-badge">4</span>
                    <h3>Nội dung & Ghi chú</h3>
                </div>
                <div class="form-grid">
                    <div class="form-group full">
                        <label class="form-label">Tóm tắt nội dung</label>
                        <textarea class="form-control" name="summary" rows="5" placeholder="Viết giới thiệu hoặc tóm tắt ngắn về cuốn sách...">{{ old('summary', $book->summary) }}</textarea>
                    </div>

                    <div class="form-group full">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                            <label class="form-label" style="margin-bottom: 0;">Thông tin khác / Ghi chú (Hiển thị trong mục "Đọc thêm +")</label>
                            <button type="button" class="btn-action" style="background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; font-size: 12px; padding: 5px 12px; border-radius: 4px; cursor: pointer; font-weight: 600;" onclick="insertTwoColumnTemplate()">
                                + Chèn mẫu 2 cột
                            </button>
                        </div>
                        <textarea class="form-control" id="additionalInfoTextarea" name="additional_information" rows="10" placeholder="Nhập bài viết chi tiết hoặc bấm '+ Chèn mẫu 2 cột'...">{{ old('additional_information', $book->additional_information) }}</textarea>
                        <p class="sub-label" style="margin-top: 6px; font-size: 12px; color: #666;">
                            💡 <strong>Mẹo:</strong> Dùng dấu <code>---</code> ở giữa để chia làm 2 cột (Cột trái: Bài viết & Giới thiệu chi tiết; Cột phải: Về tác giả / NXB). Hỗ trợ các thẻ HTML như <code>&lt;h2&gt;</code>, <code>&lt;h3&gt;</code>, <code>&lt;strong&gt;</code>, <code>&lt;a&gt;</code>, <code>&lt;p&gt;</code>.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Section 5: SEO -->
            <div class="form-section">
                <div class="form-section-head">
                    <span class="section-badge">5</span>
                    <h3>SEO riêng cho sách</h3>
                </div>
                <div class="form-grid">
                    <div class="form-group full">
                        <label class="form-label">SEO title</label>
                        <input class="form-control" name="meta_title" value="{{ old('meta_title', $book->meta_title) }}" maxlength="255" placeholder="Mặc định dùng tựa sách">
                    </div>
                    <div class="form-group full">
                        <label class="form-label">SEO description</label>
                        <textarea class="form-control" name="meta_description" rows="3" maxlength="500" placeholder="Mô tả ngắn riêng cho công cụ tìm kiếm…">{{ old('meta_description', $book->meta_description) }}</textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Ảnh chia sẻ mạng xã hội <span class="sub-label">(OG image - kích thước khuyến nghị: 1200x630px hoặc tỷ lệ 1.91:1; JPG, PNG, WEBP tối đa 5MB)</span></label>
                        <input class="form-control" type="file" name="og_image_file" data-max-mb="5" accept="image/jpeg,image/png,image/webp">
                    </div>
                    @if ($book->og_image_url)
                        <div class="form-group">
                            <label class="form-label">Ảnh SEO hiện tại</label>
                            <img class="seo-image-preview" src="{{ $book->og_image_url }}" alt="Ảnh SEO hiện tại">
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="form-footer-actions">
            <a class="admin-button secondary" href="{{ route('admin.books.index') }}">Hủy bỏ</a>
            <button class="admin-button" type="submit">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                {{ $book->exists ? 'Cập nhật sách' : 'Lưu sách mới' }}
            </button>
        </div>
    </form>

    <form id="deleteGalleryForm" method="post" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <script>
    // Cover Preview
    function handleCoverChange(input) {
        const previewWrap = document.getElementById('coverNewPreview');
        const previewImg = document.getElementById('coverPreviewImg');
        const previewMeta = document.getElementById('coverPreviewMeta');
        const textSpan = document.getElementById('fileNameText');

        if (input.files && input.files[0]) {
            const file = input.files[0];
            textSpan.textContent = 'Đã chọn: ' + file.name;
            textSpan.style.color = '#1B4332';
            textSpan.style.fontWeight = '600';

            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewMeta.textContent = file.name + ' (' + (file.size / 1024 / 1024).toFixed(2) + ' MB)';
                previewWrap.style.display = 'flex';
            };
            reader.readAsDataURL(file);
        }
    }

    function clearCoverSelection() {
        const input = document.getElementById('coverInput');
        const previewWrap = document.getElementById('coverNewPreview');
        const textSpan = document.getElementById('fileNameText');
        input.value = '';
        previewWrap.style.display = 'none';
        textSpan.textContent = 'Chọn hoặc kéo tệp ảnh bìa vào đây';
        textSpan.style.color = '';
        textSpan.style.fontWeight = '';
    }

    // Accumulative Gallery Selection & Live Previews
    let galleryDataTransfer = new DataTransfer();

    function handleGalleryFiles(newFiles) {
        if (!newFiles || !newFiles.length) return;

        const maxTotal = 12;
        for (let i = 0; i < newFiles.length; i++) {
            const file = newFiles[i];
            // Prevent duplicate file by name and size
            let isDuplicate = false;
            for (let j = 0; j < galleryDataTransfer.files.length; j++) {
                if (galleryDataTransfer.files[j].name === file.name && galleryDataTransfer.files[j].size === file.size) {
                    isDuplicate = true;
                    break;
                }
            }
            if (!isDuplicate) {
                if (galleryDataTransfer.files.length >= maxTotal) {
                    alert('Chỉ được chọn tối đa ' + maxTotal + ' ảnh gallery!');
                    break;
                }
                galleryDataTransfer.items.add(file);
            }
        }

        syncGalleryInputAndRender();
    }

    function removeGalleryFile(index) {
        const newDt = new DataTransfer();
        for (let i = 0; i < galleryDataTransfer.files.length; i++) {
            if (i !== index) {
                newDt.items.add(galleryDataTransfer.files[i]);
            }
        }
        galleryDataTransfer = newDt;
        syncGalleryInputAndRender();
    }

    function clearAllNewGallery() {
        galleryDataTransfer = new DataTransfer();
        syncGalleryInputAndRender();
    }

    function syncGalleryInputAndRender() {
        const input = document.getElementById('galleryInput');
        input.files = galleryDataTransfer.files;

        const textSpan = document.getElementById('galleryFileNameText');
        const batchSection = document.getElementById('galleryBatchSection');
        const countSpan = document.getElementById('galleryBatchCount');
        const grid = document.getElementById('galleryNewPreviewGrid');

        const total = galleryDataTransfer.files.length;
        if (total > 0) {
            textSpan.textContent = 'Đã chọn ' + total + ' ảnh (Bấm để thêm ảnh khác)';
            textSpan.style.color = '#1B4332';
            textSpan.style.fontWeight = '600';
            countSpan.textContent = total;
            batchSection.style.display = 'block';

            grid.innerHTML = '';
            for (let i = 0; i < total; i++) {
                const file = galleryDataTransfer.files[i];
                const item = document.createElement('div');
                item.className = 'admin-preview-item';

                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.alt = file.name;

                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'admin-preview-remove-btn';
                removeBtn.innerHTML = '✕';
                removeBtn.title = 'Xóa ảnh này';
                removeBtn.onclick = (function(idx) {
                    return function() { removeGalleryFile(idx); };
                })(i);

                const info = document.createElement('div');
                info.className = 'admin-preview-item-info';
                info.innerHTML = `<span>${file.name}</span><small>${(file.size / 1024 / 1024).toFixed(2)} MB</small>`;

                item.appendChild(img);
                item.appendChild(removeBtn);
                item.appendChild(info);
                grid.appendChild(item);
            }
        } else {
            textSpan.textContent = '+ Thêm ảnh vào gallery (Có thể chọn nhiều lần hoặc chọn nhiều ảnh cùng lúc)';
            textSpan.style.color = '';
            textSpan.style.fontWeight = '';
            batchSection.style.display = 'none';
            grid.innerHTML = '';
        }
    }

    function deleteGalleryImage(actionUrl) {
        if (confirm('Xóa ảnh này khỏi gallery?')) {
            const form = document.getElementById('deleteGalleryForm');
            form.action = actionUrl;
            form.submit();
        }
    }

    function insertTwoColumnTemplate() {
        const textarea = document.getElementById('additionalInfoTextarea');
        if (!textarea) return;
        const sample = `<h2>Tác phẩm giả tưởng không gian hay nhất mọi thời đại!</h2>
<p><strong>Khám phá vũ trụ ấn bản lần đầu tiên theo phong cách truyện tranh nghệ thuật.</strong></p>

<p>Tháng 1 năm 1976, tác phẩm được chuẩn bị công phu với chất lượng in ấn cao cấp, phục chế kỹ thuật số từ các bản vẽ gốc.</p>

<p>Những trang sách được tái hiện sống động với đường nét minh họa chi tiết, đưa độc giả bước vào hành trình phiêu lưu kỳ thú.</p>

<p>23 số đầu tiên đã được phục chế kỹ thuật số để khắc phục những khiếm khuyết của bản in gốc giá rẻ, đồng thời vẫn giữ được tinh thần và chất liệu của truyện tranh thời đó.</p>

<p><strong>Ấn bản dành cho nhà sưu tập với số lượng 1.000 bản được đánh số.</strong></p>

---

<h3>Giới thiệu về nhà xuất bản</h3>
<p><strong>Nhà xuất bản</strong> là đơn vị xuất bản uy tín, đồng hành cùng các tác phẩm văn học nghệ thuật giá trị.</p>

<h3>Thư viện ấn bản đặc biệt</h3>
<p>Phiên bản giới hạn 1.000 bản.<br>Sách bìa cứng in hình nghệ thuật cao cấp, hộp bảo vệ sang trọng.</p>

<p>ISBN 978-3-7544-0580-2<br>Phiên bản: Tiếng Việt / Tiếng Anh</p>`;

        if (!textarea.value.trim() || confirm('Chèn mẫu 2 cột vào ô này? (Nội dung cũ sẽ được thay thế)')) {
            textarea.value = sample;
        }
    }
    </script>
@endsection
