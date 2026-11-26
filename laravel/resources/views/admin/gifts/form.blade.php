@extends('layouts.admin')
@section('heading', $gift->exists ? 'Chỉnh sửa sản phẩm Gift: '.$gift->title : 'Thêm sản phẩm Gift mới')
@section('content')
    <div class="admin-card form-card">
        <div class="form-header-bar">
            <div>
                <h2>{{ $gift->exists ? 'Chỉnh sửa sản phẩm Gift' : 'Thêm sản phẩm Gift mới' }}</h2>
                <p class="form-subtitle">Điền thông tin và tải lên hình ảnh cho sản phẩm quà tặng / phụ kiện nghệ thuật.</p>
            </div>
            <a class="btn-back" href="{{ route('admin.gifts.index') }}">← Quay lại danh sách Gift</a>
        </div>

        <form method="post" action="{{ $gift->exists ? route('admin.gifts.update', $gift) : route('admin.gifts.store') }}" enctype="multipart/form-data">
            @csrf
            @if ($gift->exists)
                @method('PUT')
            @endif

            <div class="form-sections">
                <!-- Section 1: Thông tin cơ bản -->
                <div class="form-section">
                    <div class="form-section-head">
                        <span class="section-badge">1</span>
                        <h3>Thông tin sản phẩm Gift</h3>
                    </div>
                    <div class="form-grid">
                        <div class="form-group full">
                            <label class="form-label">Tên sản phẩm Quà tặng <span class="req">*</span></label>
                            <input class="form-control" name="title" value="{{ old('title', $gift->title) }}" placeholder="Ví dụ: Bộ cọ vẽ, bánh kẹo nghệ thuật..." required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Thương hiệu</label>
                            <input class="form-control" name="brand" value="{{ old('brand', $gift->brand) }}" placeholder="Ví dụ: Cavallini & Co.">
                            <span class="sub-label">Không phải tác giả. Đây là nhãn hàng / nhà sản xuất.</span>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Loại sản phẩm</label>
                            <input class="form-control" name="product_type" value="{{ old('product_type', $gift->product_type) }}" placeholder="Ví dụ: Cọ vẽ, Bánh kẹo, Phụ kiện">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Mã SKU / Barcode</label>
                            <input class="form-control" name="sku" value="{{ old('sku', $gift->sku) }}" placeholder="Ví dụ: GIFT-2026-001">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Kích thước / Quy cách đóng gói</label>
                            <input class="form-control" name="dimensions" value="{{ old('dimensions', $gift->dimensions) }}" placeholder="Ví dụ: 30 x 40 x 10 cm, Hộp cứng nhung cao cấp">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Tình trạng hàng</label>
                            <select class="form-control" name="availability_status">
                                <option value="available" @selected(old('availability_status', $gift->availability_status ?? 'available') === 'available')>Đang có sẵn</option>
                                <option value="reserved" @selected(old('availability_status', $gift->availability_status) === 'reserved')>Tạm giữ</option>
                                <option value="sold" @selected(old('availability_status', $gift->availability_status) === 'sold')>Đã bán / Hết hàng</option>
                                <option value="coming_soon" @selected(old('availability_status', $gift->availability_status) === 'coming_soon')>Sắp về hàng</option>
                            </select>
                        </div>

                        <div class="form-group full">
                            <div class="status-toggle-card">
                                <input type="hidden" name="is_published" value="0">
                                <label class="toggle-switch">
                                    <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $gift->exists ? $gift->is_published : true))>
                                    <span class="toggle-slider"></span>
                                </label>
                                <div class="toggle-info">
                                    <strong>Hiển thị sản phẩm công khai</strong>
                                    <span>Hiển thị sản phẩm trên trang GIFTS và cho phép người xem tra cứu, gửi yêu cầu quan tâm.</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Section 2: Hình ảnh & Gallery -->
                <div class="form-section">
                    <div class="form-section-head">
                        <span class="section-badge">2</span>
                        <h3>Hình ảnh &amp; Thư viện ảnh (Gallery)</h3>
                    </div>
                    <div class="form-grid">
                        <!-- Cover Image -->
                        <div class="form-group full">
                            <label class="form-label">Ảnh đại diện chính (Cover Image) <span class="sub-label">(Kích thước khuyến nghị: 1200x1200px hoặc tỷ lệ 1:1; PNG, JPG, WEBP tối đa 10MB)</span></label>
                            <div class="file-upload-box">
                                <input type="file" name="cover" id="coverInput" data-max-mb="10" accept="image/jpeg,image/png,image/webp" onchange="previewUploadImage(this, 'coverPreviewImg', 'coverFileNameText')">
                                <label for="coverInput" class="file-upload-label">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                    <span id="coverFileNameText">Bấm để chọn hoặc kéo thả ảnh đại diện mới vào đây</span>
                                </label>
                            </div>
                            @if ($gift->cover_url)
                                <div class="current-cover-wrap" style="margin-top: 12px; display: flex; align-items: center; gap: 14px;">
                                    <div style="width: 100px; height: 100px; border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0; background: #f8fafc;">
                                        <img id="coverPreviewImg" src="{{ $gift->cover_url }}" alt="Ảnh sản phẩm hiện tại" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                    <div>
                                        <span class="cover-label" style="font-weight: 600; color: #334155;">Ảnh đại diện hiện tại</span>
                                        <p class="help">Chọn ảnh mới bên trên nếu muốn thay thế ảnh này.</p>
                                    </div>
                                </div>
                            @else
                                <div id="coverPreviewWrap" style="display: none; margin-top: 12px;">
                                    <div style="width: 100px; height: 100px; border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0; background: #f8fafc;">
                                        <img id="coverPreviewImg" src="" alt="Xem trước ảnh bìa" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Gallery Images -->
                        <div class="form-group full" style="margin-top: 10px;">
                            <label class="form-label">Thư viện ảnh chi tiết (Gallery) <span class="sub-label">(Kích thước khuyến nghị: cạnh dài 1600–2400px, giữ tỷ lệ theo ảnh gốc; PNG, JPG, WEBP tối đa 10MB/ảnh, tối đa 16 ảnh)</span></label>
                            <div class="file-upload-box">
                                <input type="file" name="gallery[]" id="galleryInput" data-max-mb="10" accept="image/jpeg,image/png,image/webp" multiple onchange="handleGallerySelect(this)">
                                <label for="galleryInput" class="file-upload-label">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                    <span id="galleryFileNameText">+ Thêm ảnh vào gallery (Có thể chọn nhiều lần hoặc chọn nhiều ảnh cùng lúc)</span>
                                </label>
                            </div>

                            <!-- Live preview for newly selected gallery images -->
                            <div id="galleryBatchSection" style="display: none; margin-top: 14px;">
                                <div class="admin-gallery-batch-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                    <span><strong id="galleryBatchCount">0</strong> ảnh mới sẵn sàng lưu:</span>
                                    <button type="button" class="btn-action btn-delete" style="font-size: 11px; padding: 4px 8px;" onclick="clearAllNewGallery()">Xóa tất cả ảnh mới</button>
                                </div>
                                <div class="admin-preview-grid" id="galleryNewPreviewGrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(110px, 1fr)); gap: 12px;"></div>
                            </div>

                            @if ($gift->exists && $gift->galleryImages->isNotEmpty())
                                <div style="margin-top: 20px;">
                                    <span class="cover-label" style="display: block; margin-bottom: 10px; font-weight: 600; color: #334155;">Các ảnh đã lưu trong Gallery ({{ $gift->galleryImages->count() }} ảnh):</span>
                                    <div class="admin-gallery-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 12px;">
                                        @foreach ($gift->galleryImages as $image)
                                            <div class="admin-gallery-item" style="border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; background: #fff;">
                                                <div style="width: 100%; height: 110px; background: #f1f5f9;">
                                                    <img src="{{ $image->url }}" alt="{{ $image->alt_text ?: 'Ảnh gallery '.$gift->title }}" style="width: 100%; height: 100%; object-fit: cover;">
                                                </div>
                                                <div class="admin-gallery-item-footer" style="padding: 6px 8px; display: flex; justify-content: space-between; align-items: center; background: #f8fafc; font-size: 12px;">
                                                    <span style="color: #64748b;">Ảnh {{ $loop->iteration }}</span>
                                                    <button class="btn-action btn-delete" type="button" style="font-size: 11px; padding: 2px 6px;" onclick="deleteGalleryImage('{{ route('admin.gifts.gallery.destroy', [$gift, $image]) }}')">Xóa</button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Section 3: Mô tả & Thông tin chi tiết -->
                <div class="form-section">
                    <div class="form-section-head">
                        <span class="section-badge">3</span>
                        <h3>Mô tả &amp; Chi tiết sản phẩm</h3>
                    </div>
                    <div class="form-grid">
                        <div class="form-group full">
                            <label class="form-label">Tóm tắt ngắn (Summary)</label>
                            <textarea class="form-control" name="summary" rows="3" placeholder="Đoạn văn ngắn giới thiệu điểm đặc sắc của món quà...">{{ old('summary', $gift->summary) }}</textarea>
                        </div>

                        <div class="form-group full">
                            <label class="form-label">Thông tin giới thiệu &amp; Thông số chi tiết (HTML / Đoạn văn)</label>
                            <textarea class="form-control" name="additional_information" id="additionalInfoTextarea" rows="8" placeholder="Nhập mô tả chi tiết, chất liệu, phụ kiện kèm theo...">{{ old('additional_information', $gift->additional_information) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Section 4: SEO -->
                <div class="form-section">
                    <div class="form-section-head">
                        <span class="section-badge">4</span>
                        <h3>Tối ưu SEO</h3>
                    </div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">SEO Title</label>
                            <input class="form-control" name="meta_title" value="{{ old('meta_title', $gift->meta_title) }}" placeholder="Tiêu đề hiển thị trên Google search...">
                        </div>
                        <div class="form-group full">
                            <label class="form-label">SEO Description</label>
                            <textarea class="form-control" name="meta_description" rows="2" placeholder="Đoạn mô tả ngắn gọn khi chia sẻ link trên mạng xã hội...">{{ old('meta_description', $gift->meta_description) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-footer-actions" style="margin-top: 32px; display: flex; justify-content: flex-end; gap: 12px;">
                <a class="btn-cancel" href="{{ route('admin.gifts.index') }}">Hủy bỏ</a>
                <button class="admin-button" type="submit" style="min-width: 180px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    {{ $gift->exists ? 'Lưu thay đổi' : 'Thêm sản phẩm Gift' }}
                </button>
            </div>
        </form>
    </div>

    <!-- Hidden form for deleting individual gallery images -->
    <form id="deleteGalleryForm" method="post" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <script>
    function previewUploadImage(input, previewId, textSpanId) {
        const textSpan = document.getElementById(textSpanId);
        const previewImg = document.getElementById(previewId);
        const previewWrap = document.getElementById('coverPreviewWrap');
        if (input.files && input.files[0]) {
            const file = input.files[0];
            if (textSpan) {
                textSpan.textContent = 'Đã chọn file: ' + file.name;
                textSpan.style.color = '#1B4332';
                textSpan.style.fontWeight = '600';
            }
            if (previewImg) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    if (previewWrap) previewWrap.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        }
    }

    let galleryDataTransfer = new DataTransfer();
    const maxTotal = 16;

    function handleGallerySelect(input) {
        const newFiles = input.files;
        if (!newFiles || newFiles.length === 0) return;

        for (let i = 0; i < newFiles.length; i++) {
            const file = newFiles[i];
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
                item.style.cssText = 'position: relative; border-radius: 6px; overflow: hidden; border: 1px solid #e2e8f0; background: #fff; padding: 4px;';

                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.alt = file.name;
                img.style.cssText = 'width: 100%; height: 80px; object-fit: cover; border-radius: 4px;';

                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.innerHTML = '✕';
                removeBtn.title = 'Xóa ảnh này';
                removeBtn.style.cssText = 'position: absolute; top: 6px; right: 6px; background: rgba(0,0,0,0.6); color: #fff; border: none; border-radius: 50%; width: 20px; height: 20px; font-size: 11px; cursor: pointer; display: flex; align-items: center; justify-content: center;';
                removeBtn.onclick = (function(idx) {
                    return function() { removeGalleryFile(idx); };
                })(i);

                item.appendChild(img);
                item.appendChild(removeBtn);
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
        if (confirm('Bạn có chắc chắn muốn xóa ảnh này khỏi gallery?')) {
            const form = document.getElementById('deleteGalleryForm');
            form.action = actionUrl;
            form.submit();
        }
    }
    </script>
@endsection
