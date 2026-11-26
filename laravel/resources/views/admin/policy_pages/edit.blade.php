@extends('layouts.admin')
@section('heading', 'Cấu hình Trang chính sách & Điều khoản')
@section('content')
    <div class="policy-admin-container">
        <!-- Tab Navigation Bar -->
        <div class="policy-tabs-nav">
            @foreach($supported as $key => $info)
                <a href="{{ route('admin.policy-pages.edit', ['tab' => $key]) }}" 
                   class="policy-tab-item {{ $currentTab === $key ? 'active' : '' }}">
                    @if($key === 'copyright')
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                    @elseif($key === 'terms')
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    @else
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    @endif
                    <span class="tab-label">{{ $info['name'] }}</span>
                </a>
            @endforeach
        </div>

        @php
            $activePageInfo = $supported[$currentTab];
            $currentData = $pagesData[$currentTab];
        @endphp

        <!-- Main Edit Card -->
        <div class="admin-card form-card">
            <div class="form-header-bar policy-header-bar">
                <div>
                    <div class="policy-page-badge">
                        <span class="badge-dot"></span>
                        Đang cấu hình: <strong>{{ $activePageInfo['name'] }}</strong>
                    </div>
                    <p class="form-subtitle" style="margin-top: 4px;">
                        Tùy chỉnh tiêu đề, thông tin SEO và nội dung chi tiết hiển thị cho bạn đọc trên website.
                    </p>
                </div>
                <div class="policy-header-actions">
                    <a class="btn-preview-link" href="{{ route($activePageInfo['route']) }}" target="_blank" title="Xem trang ngoài website">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                        <span>Xem trang ngoài web ↗</span>
                    </a>
                </div>
            </div>

            <form method="post" action="{{ route('admin.policy-pages.update') }}" id="policyPageForm">
                @csrf
                <input type="hidden" name="page" value="{{ $currentTab }}">

                <div class="form-sections">
                    <!-- Section 1: Thông tin đầu trang & SEO -->
                    <div class="form-section">
                        <div class="form-section-head">
                            <span class="section-badge">1</span>
                            <h3>Tiêu đề & Thông tin SEO</h3>
                        </div>
                        <div class="form-grid">
                            <div class="form-group full">
                                <label class="form-label">Tiêu đề chính trang (H1) <span class="req">*</span></label>
                                <input class="form-control" name="title" value="{{ old('title', $currentData['title']) }}" placeholder="Nhập tiêu đề trang..." required>
                            </div>

                            <div class="form-group full">
                                <label class="form-label">Kicker / Phụ đề tiếng Anh nhỏ phía trên tiêu đề</label>
                                <input class="form-control" name="kicker" value="{{ old('kicker', $currentData['kicker']) }}" placeholder="Ví dụ: ARCHIVAL & INTELLECTUAL PROPERTY POLICY">
                            </div>

                            <div class="form-group full">
                                <label class="form-label">Thẻ mô tả tóm tắt cho công cụ tìm kiếm (Meta Description)</label>
                                <textarea class="form-control" name="meta" rows="2" placeholder="Tóm tắt 1-2 câu về nội dung trang để hiển thị trên Google search...">{{ old('meta', $currentData['meta']) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Nội dung chi tiết -->
                    <div class="form-section">
                        <div class="form-section-head" style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <span class="section-badge">2</span>
                                <h3>Nội dung bài viết / Điều khoản</h3>
                            </div>
                            <div class="editor-mode-toggle">
                                <button type="button" class="btn-mode-switch active" id="btnModeVisual" onclick="switchEditorMode('visual')">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    Soạn thảo trực quan
                                </button>
                                <button type="button" class="btn-mode-switch" id="btnModeHtml" onclick="switchEditorMode('html')">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
                                    Mã HTML
                                </button>
                            </div>
                        </div>

                        <!-- Editor Toolbar -->
                        <div class="policy-editor-toolbar" id="editorToolbar">
                            <div class="toolbar-group">
                                <button type="button" class="tb-btn" title="Tiêu đề mục lớn (H2)" onclick="formatDoc('formatBlock', 'h2')"><b>H2</b></button>
                                <button type="button" class="tb-btn" title="Tiêu đề mục nhỏ (H3)" onclick="formatDoc('formatBlock', 'h3')"><b>H3</b></button>
                                <button type="button" class="tb-btn" title="Đoạn văn bình thường (P)" onclick="formatDoc('formatBlock', 'p')">P</button>
                            </div>
                            <div class="toolbar-divider"></div>
                            <div class="toolbar-group">
                                <button type="button" class="tb-btn" title="In đậm (Bold)" onclick="formatDoc('bold')"><b>B</b></button>
                                <button type="button" class="tb-btn" title="In nghiêng (Italic)" onclick="formatDoc('italic')"><i>I</i></button>
                                <button type="button" class="tb-btn" title="Gạch chân (Underline)" onclick="formatDoc('underline')"><u>U</u></button>
                            </div>
                            <div class="toolbar-divider"></div>
                            <div class="toolbar-group">
                                <button type="button" class="tb-btn" title="Danh sách gạch đầu dòng" onclick="formatDoc('insertUnorderedList')">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                                </button>
                                <button type="button" class="tb-btn" title="Danh sách đánh số" onclick="formatDoc('insertOrderedList')">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="10" y1="6" x2="21" y2="6"/><line x1="10" y1="12" x2="21" y2="12"/><line x1="10" y1="18" x2="21" y2="18"/><path d="M4 6h1v4"/><path d="M4 10h2"/><path d="M6 18H4c0-1 2-2 2-3s-1-1.5-2-1"/></svg>
                                </button>
                                <button type="button" class="tb-btn" title="Trích dẫn (Blockquote)" onclick="formatDoc('formatBlock', 'blockquote')">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21c3 0 7-1 7-8V5c0-1.25-.756-2.017-2-2H4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2 1 0 1 0 1 1v1c0 1-1 2-2 2s-1 .008-1 1.031V20c0 1 0 1 1 1z"/><path d="M15 21c3 0 7-1 7-8V5c0-1.25-.757-2.017-2-2h-4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2 1 0 1 0 1 1v1c0 1-1 2-2 2s-1 .008-1 1.031V20c0 1 0 1 1 1z"/></svg>
                                </button>
                            </div>
                            <div class="toolbar-divider"></div>
                            <div class="toolbar-group">
                                <button type="button" class="tb-btn" title="Chèn liên kết (Link)" onclick="insertCustomLink()">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                                </button>
                                <button type="button" class="tb-btn" title="Gỡ liên kết" onclick="formatDoc('unlink')">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18.84 12.25l1.72-1.71a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M5.16 11.75l-1.72 1.71a5 5 0 0 0 7.07 7.07l1.72-1.71"/><line x1="2" y1="2" x2="22" y2="22"/></svg>
                                </button>
                                <button type="button" class="tb-btn" title="Đường kẻ ngang phân cách" onclick="formatDoc('insertHorizontalRule')">―</button>
                                <button type="button" class="tb-btn" title="Xóa định dạng đang chọn" onclick="formatDoc('removeFormat')">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Visual Editor Area -->
                        <div class="policy-visual-editor-container" id="visualContainer">
                            <div class="taschen-legal-body policy-content-editable" id="visualEditor" contenteditable="true" spellcheck="false">{!! old('content', $currentData['content']) !!}</div>
                        </div>

                        <!-- Raw HTML Textarea Area -->
                        <div class="policy-html-editor-container" id="htmlContainer" style="display: none;">
                            <textarea class="form-control policy-raw-html" id="rawHtmlTextarea" name="content" rows="22" spellcheck="false">{{ old('content', $currentData['content']) }}</textarea>
                            <p class="editor-help-text">Mẹo: Bạn có thể nhập trực tiếp các thẻ HTML như <code>&lt;section class="taschen-legal-section"&gt;</code>, <code>&lt;h2 class="taschen-legal-h2"&gt;</code>, <code>&lt;p&gt;</code>, <code>&lt;ul class="taschen-legal-list"&gt;</code> để cấu trúc bài viết.</p>
                        </div>
                    </div>
                </div>

                <!-- Form Action Buttons -->
                <div class="form-footer-actions policy-footer-actions">
                    <button type="button" class="btn-action-outline-danger" onclick="openResetModal()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                        Khôi phục nội dung mẫu
                    </button>

                    <button class="admin-button" type="submit" onclick="syncEditorBeforeSubmit()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                        Lưu thay đổi trang
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Xác Nhận Khôi Phục Mặc Định -->
    <div class="delete-modal-backdrop" id="resetModal" aria-hidden="true" style="display: none;">
        <div class="delete-modal-card">
            <div class="delete-modal-icon" style="background: rgba(220, 53, 69, 0.1); color: #dc3545;">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/>
                </svg>
            </div>
            <h3 class="delete-modal-title">Khôi phục nội dung mặc định?</h3>
            <p class="delete-modal-text">
                Toàn bộ nội dung tùy chỉnh hiện tại của trang <strong>{{ $activePageInfo['name'] }}</strong> sẽ được đặt lại về bản mẫu chuẩn ban đầu. Hành động này không thể hoàn tác!
            </p>
            <div class="delete-modal-actions">
                <button type="button" class="btn-modal-cancel" onclick="closeResetModal()">Hủy bỏ</button>
                <form method="post" action="{{ route('admin.policy-pages.reset') }}" style="display: inline;">
                    @csrf
                    <input type="hidden" name="page" value="{{ $currentTab }}">
                    <button type="submit" class="btn-modal-confirm" style="background: #dc3545;">Khôi phục ngay</button>
                </form>
            </div>
        </div>
    </div>

    <style>
        .policy-admin-container {
            max-width: 1100px;
            margin: 0 auto;
        }
        .policy-tabs-nav {
            display: flex;
            gap: 10px;
            margin-bottom: 22px;
            flex-wrap: wrap;
        }
        .policy-tab-item {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 20px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            color: #4a5568;
            text-decoration: none;
            font-size: 0.92rem;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        .policy-tab-item:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            color: #1a202c;
            transform: translateY(-1px);
        }
        .policy-tab-item.active {
            background: #1B4332;
            border-color: #1B4332;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(27, 67, 50, 0.2);
        }
        .policy-header-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }
        .policy-page-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 1.05rem;
            color: #1B4332;
            font-weight: 500;
        }
        .badge-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: #2d6a4f;
            display: inline-block;
        }
        .btn-preview-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            color: #334155;
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .btn-preview-link:hover {
            background: #e2e8f0;
            color: #0f172a;
        }
        .editor-mode-toggle {
            display: flex;
            background: #f1f5f9;
            padding: 3px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }
        .btn-mode-switch {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border: none;
            background: transparent;
            border-radius: 6px;
            font-size: 0.82rem;
            font-weight: 600;
            color: #64748b;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .btn-mode-switch.active {
            background: #ffffff;
            color: #1B4332;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .policy-editor-toolbar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 4px;
            padding: 8px 12px;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-bottom: none;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }
        .toolbar-group {
            display: flex;
            align-items: center;
            gap: 3px;
        }
        .toolbar-divider {
            width: 1px;
            height: 20px;
            background: #cbd5e1;
            margin: 0 6px;
        }
        .tb-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 32px;
            height: 30px;
            padding: 0 8px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 5px;
            color: #334155;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .tb-btn:hover {
            background: #e2e8f0;
            color: #0f172a;
            border-color: #cbd5e1;
        }
        .policy-visual-editor-container {
            border: 1px solid #cbd5e1;
            border-bottom-left-radius: 8px;
            border-bottom-right-radius: 8px;
            background: #ffffff;
            padding: 24px 30px;
            min-height: 480px;
            max-height: 700px;
            overflow-y: auto;
        }
        .policy-content-editable {
            min-height: 400px;
            outline: none;
            font-family: inherit;
            line-height: 1.8;
            color: #1a1a1a;
        }
        .policy-content-editable h2 {
            font-size: 1.35rem;
            font-weight: 700;
            color: #1B4332;
            margin-top: 24px;
            margin-bottom: 12px;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 6px;
        }
        .policy-content-editable h3 {
            font-size: 1.15rem;
            font-weight: 600;
            color: #2d3748;
            margin-top: 18px;
            margin-bottom: 8px;
        }
        .policy-content-editable p {
            margin-bottom: 14px;
        }
        .policy-content-editable ul, .policy-content-editable ol {
            padding-left: 24px;
            margin-bottom: 16px;
        }
        .policy-content-editable li {
            margin-bottom: 8px;
        }
        .policy-content-editable blockquote {
            border-left: 4px solid #1B4332;
            padding: 10px 18px;
            background: #f8fafc;
            margin: 16px 0;
            color: #475569;
            font-style: italic;
        }
        .policy-raw-html {
            font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
            font-size: 0.9rem;
            line-height: 1.5;
            background: #0f172a;
            color: #f8fafc;
            border: 1px solid #1e293b;
            border-radius: 8px;
            padding: 16px;
            width: 100%;
        }
        .editor-help-text {
            font-size: 0.82rem;
            color: #64748b;
            margin-top: 8px;
        }
        .editor-help-text code {
            background: #e2e8f0;
            color: #0f172a;
            padding: 2px 5px;
            border-radius: 4px;
            font-size: 0.8rem;
        }
        .policy-footer-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 24px;
            padding-top: 18px;
            border-top: 1px solid #e2e8f0;
        }
        .btn-action-outline-danger {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            background: #fff;
            border: 1px solid #fca5a5;
            border-radius: 6px;
            color: #dc2626;
            font-weight: 600;
            font-size: 0.88rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .btn-action-outline-danger:hover {
            background: #fef2f2;
            border-color: #ef4444;
        }
    </style>

    <script>
        let currentEditorMode = 'visual';

        function switchEditorMode(mode) {
            const visualContainer = document.getElementById('visualContainer');
            const htmlContainer = document.getElementById('htmlContainer');
            const visualEditor = document.getElementById('visualEditor');
            const rawHtmlTextarea = document.getElementById('rawHtmlTextarea');
            const editorToolbar = document.getElementById('editorToolbar');
            const btnVisual = document.getElementById('btnModeVisual');
            const btnHtml = document.getElementById('btnModeHtml');

            if (mode === 'html') {
                rawHtmlTextarea.value = visualEditor.innerHTML;
                visualContainer.style.display = 'none';
                editorToolbar.style.opacity = '0.5';
                editorToolbar.style.pointerEvents = 'none';
                htmlContainer.style.display = 'block';
                btnVisual.classList.remove('active');
                btnHtml.classList.add('active');
                currentEditorMode = 'html';
            } else {
                visualEditor.innerHTML = rawHtmlTextarea.value;
                htmlContainer.style.display = 'none';
                visualContainer.style.display = 'block';
                editorToolbar.style.opacity = '1';
                editorToolbar.style.pointerEvents = 'auto';
                btnHtml.classList.remove('active');
                btnVisual.classList.add('active');
                currentEditorMode = 'visual';
            }
        }

        function formatDoc(cmd, value = null) {
            document.execCommand(cmd, false, value);
            document.getElementById('visualEditor').focus();
        }

        function insertCustomLink() {
            const url = prompt('Nhập đường dẫn liên kết (URL):', 'https://');
            if (url && url !== 'https://') {
                formatDoc('createLink', url);
            }
        }

        function syncEditorBeforeSubmit() {
            const visualEditor = document.getElementById('visualEditor');
            const rawHtmlTextarea = document.getElementById('rawHtmlTextarea');
            if (currentEditorMode === 'visual') {
                rawHtmlTextarea.value = visualEditor.innerHTML;
            }
        }

        function openResetModal() {
            document.getElementById('resetModal').style.display = 'flex';
        }

        function closeResetModal() {
            document.getElementById('resetModal').style.display = 'none';
        }
    </script>
@endsection
