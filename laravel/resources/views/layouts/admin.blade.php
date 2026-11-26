<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Quản trị') · {{ \App\Models\Setting::get('site_name', 'KHO - CA') }}</title>
    @php
        $favicon = \App\Models\Setting::get('favicon')
            ?: \App\Models\Setting::get('site_logo', asset('images/logo.png'));
    @endphp
    <link rel="icon" href="{{ $favicon }}">
    <link rel="shortcut icon" href="{{ $favicon }}">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/catalog.css') }}?v={{ file_exists(base_path('../public_html/css/catalog.css')) ? filemtime(base_path('../public_html/css/catalog.css')) : (file_exists(public_path('css/catalog.css')) ? filemtime(public_path('css/catalog.css')) : time()) }}">
    <style>
        .admin-client-error {
            position: fixed;
            z-index: 1000;
            top: 24px;
            right: 24px;
            width: min(420px, calc(100vw - 48px));
            padding: 14px 16px;
            border: 1px solid #fecaca;
            border-radius: 12px;
            color: #991b1b;
            background: #fff1f2;
            box-shadow: 0 14px 32px rgba(15, 23, 42, .14);
            font-size: 14px;
            line-height: 1.5;
        }
    </style>
</head>
<body class="admin-body">
<div class="admin-shell">
    <div class="admin-sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
    <aside class="admin-sidebar" id="adminSidebar">
        <a class="admin-brand" href="{{ route('admin.dashboard') }}" aria-label="{{ \App\Models\Setting::get('site_name', 'KHO - CA') }}">
            <span class="admin-brand-badge">
                @if ($siteLogo = \App\Models\Setting::get('site_logo'))
                    <img src="{{ $siteLogo }}" class="brand-badge-img" alt="{{ \App\Models\Setting::get('site_name', 'KHO - CA') }}">
                @else
                    <span class="brand-blue">KHO</span>
                    <span class="brand-dash">-</span>
                    <span class="brand-blue">CA</span>
                @endif
            </span>
        </a>
        <nav class="admin-menu">
            <a href="{{ route('admin.dashboard') }}" @class(['active' => request()->routeIs('admin.dashboard')])>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                <span>Tổng quan</span>
            </a>
            <a href="{{ route('admin.books.index') }}" @class(['active' => request()->routeIs('admin.books.*')])>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                <span>Quản lý sách</span>
            </a>
            <a href="{{ route('admin.gifts.index') }}" @class(['active' => request()->routeIs('admin.gifts.*')])>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 12 20 22 4 22 4 12"/><rect x="2" y="7" width="20" height="5"/><line x1="12" y1="22" x2="12" y2="7"/><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/></svg>
                <span>Quản lý Gift</span>
            </a>
            <a href="{{ route('admin.collections.index') }}" @class(['active' => request()->routeIs('admin.collections.*')])>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                <span>Bộ sưu tập</span>
            </a>
            <a href="{{ route('admin.analytics.index') }}" @class(['active' => request()->routeIs('admin.analytics.*')])>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" y1="19" x2="4" y2="10"/><line x1="10" y1="19" x2="10" y2="4"/><line x1="16" y1="19" x2="16" y2="13"/><line x1="22" y1="19" x2="22" y2="7"/></svg>
                <span>Thống kê</span>
            </a>
            <a href="{{ route('admin.categories.index') }}" @class(['active' => request()->routeIs('admin.categories.*')])>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
                <span>Thể loại</span>
            </a>
            <a href="{{ route('admin.profile.edit') }}" @class(['active' => request()->routeIs('admin.profile.*')])>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                <span>Tài khoản</span>
            </a>
            <a href="{{ route('admin.settings.edit') }}" @class(['active' => request()->routeIs('admin.settings.*', 'admin.explore.*', 'admin.banners.*', 'admin.about.*')])>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                <span>Cấu hình website</span>
            </a>
            <a href="{{ route('home') }}" target="_blank">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                <span>Xem website ↗</span>
            </a>
            <form action="{{ route('admin.logout') }}" method="post">
                @csrf
                <button type="submit">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    <span>Đăng xuất</span>
                </button>
            </form>
        </nav>
    </aside>
    <main class="admin-content">
        <div class="admin-top">
            <button type="button" class="admin-mobile-toggle" aria-label="Toggle menu" onclick="toggleSidebar()">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>
            <h1>@yield('heading', 'Quản trị')</h1>
        </div>
        @if(session('success'))<div class="flash success">{{ session('success') }}</div>@endif
        @if(session('error'))<div class="flash form-error">{{ session('error') }}</div>@endif
        @if(isset($errors) && $errors->any())<div class="flash form-error"><strong>Vui lòng kiểm tra lại:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        @yield('content')
    </main>
</div>
<div id="adminClientError" class="admin-client-error" role="alert" hidden></div>

<!-- Modal Xác Nhận Xóa -->
<div class="delete-modal-backdrop" id="deleteModal" aria-hidden="true">
    <div class="delete-modal-card">
        <div class="delete-modal-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                <line x1="12" y1="9" x2="12" y2="13"/>
                <line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
        </div>
        <h3 class="delete-modal-title">Xác nhận xóa</h3>
        <p class="delete-modal-text" id="deleteModalText">Bạn có chắc chắn muốn xóa mục này? Hành động này không thể hoàn tác.</p>
        <div class="delete-modal-actions">
            <button type="button" class="btn-modal-cancel" onclick="closeDeleteModal()">Hủy bỏ</button>
            <button type="button" class="btn-modal-confirm" id="confirmDeleteBtn">Xóa ngay</button>
        </div>
    </div>
</div>

@stack('scripts')
<script>
let pendingFormToSubmit = null;
function toggleSidebar() {
    const sidebar = document.getElementById('adminSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    if (sidebar && overlay) {
        sidebar.classList.toggle('open');
        overlay.classList.toggle('open');
    }
}
function confirmDelete(element, message = 'Bạn có chắc chắn muốn xóa mục này?') {
    pendingFormToSubmit = element.closest('form');
    document.getElementById('deleteModalText').textContent = message;
    const modal = document.getElementById('deleteModal');
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}
function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    modal.classList.remove('active');
    document.body.style.overflow = '';
    pendingFormToSubmit = null;
}
document.addEventListener('DOMContentLoaded', () => {
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    if (confirmBtn) {
        confirmBtn.addEventListener('click', () => {
            if (pendingFormToSubmit) {
                pendingFormToSubmit.submit();
            }
        });
    }
    const modal = document.getElementById('deleteModal');
    if (modal) {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeDeleteModal();
            }
        });
    }
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal && modal.classList.contains('active')) {
            closeDeleteModal();
        }
    });

    const clientError = document.getElementById('adminClientError');
    let clientErrorTimer = null;
    window.showAdminClientError = function (message) {
        if (!clientError) {
            window.alert(message);
            return;
        }
        clientError.textContent = message;
        clientError.hidden = false;
        if (clientErrorTimer) {
            window.clearTimeout(clientErrorTimer);
        }
        clientErrorTimer = window.setTimeout(() => {
            clientError.hidden = true;
        }, 8000);
    };

    document.querySelectorAll('form[enctype="multipart/form-data"]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            const oversizedFiles = [];

            form.querySelectorAll('input[type="file"][data-max-mb]').forEach((input) => {
                const maxBytes = Number(input.dataset.maxMb) * 1024 * 1024;
                Array.from(input.files || []).forEach((file) => {
                    if (file.size > maxBytes) {
                        const fieldName = input.name.startsWith('gallery')
                            ? 'Ảnh gallery'
                            : (input.name === 'cover' ? 'Ảnh chính'
                                : (input.name === 'logo' ? 'Logo'
                                    : (input.name === 'favicon' ? 'Favicon'
                                        : (input.name.includes('mobile') ? 'Ảnh Mobile'
                                            : (input.name.includes('hero') || input.name === 'image' ? 'Ảnh banner'
                                                : (input.name.includes('split') ? 'Ảnh minh họa' : 'Ảnh tải lên'))))));
                        oversizedFiles.push(fieldName + ': ' + file.name + ' vượt quá ' + input.dataset.maxMb + 'MB.');
                    }
                });
            });

            if (oversizedFiles.length > 0) {
                event.preventDefault();
                window.showAdminClientError(
                    'Không thể gửi biểu mẫu. ' + oversizedFiles.slice(0, 3).join(' ')
                    + (oversizedFiles.length > 3 ? ' Vui lòng kiểm tra lại các ảnh đã chọn.' : '')
                );
            }
        });
    });
});
</script>
<script src="{{ asset('js/custom-select.js') }}?v={{ file_exists(public_path('js/custom-select.js')) ? filemtime(public_path('js/custom-select.js')) : time() }}"></script>
</body></html>
