<!doctype html>
@php
    $isGiftRoute = request()->routeIs('gifts.*');
    $favicon = \App\Models\Setting::get('favicon')
        ?: \App\Models\Setting::get('site_logo', asset('images/logo.png'));
@endphp
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ \Illuminate\Support\Str::limit(strip_tags($__env->yieldContent('meta_description', \App\Models\Setting::get('site_tagline', 'Không gian trưng bày, khám phá và tôn vinh sách nghệ thuật cao cấp'))), 160) }}">
    <title>@yield('title', \App\Models\Setting::get('site_name', 'KHO - CA').' · Books & Arts')</title>
    <meta property="og:title" content="{{ $__env->yieldContent('title', \App\Models\Setting::get('site_name', 'KHO - CA')) }}">
    <meta property="og:description" content="{{ \Illuminate\Support\Str::limit(strip_tags($__env->yieldContent('meta_description', \App\Models\Setting::get('site_tagline', ''))), 160) }}">
    @if ($ogImage = $__env->yieldContent('meta_og_image'))
        <meta property="og:image" content="{{ $ogImage }}">
    @endif
    <link rel="icon" href="{{ $favicon }}">
    <link rel="shortcut icon" href="{{ $favicon }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="{{ asset('css/catalog.css') }}?v={{ file_exists(base_path('../public_html/css/catalog.css')) ? filemtime(base_path('../public_html/css/catalog.css')) : (file_exists(public_path('css/catalog.css')) ? filemtime(public_path('css/catalog.css')) : time()) }}">
    @stack('styles')
</head>
<body>
    <a class="sr-only" href="#main-content">Chuyển đến nội dung chính</a>

    <!-- TASCHEN-INSPIRED EDITORIAL HEADER -->
    <header class="taschen-header">
        <div class="taschen-header-inner">
            <!-- Left Navigation -->
            <div class="taschen-header-left">
                <button class="taschen-mobile-toggle" type="button" aria-label="Mở menu điều hướng" aria-expanded="false" data-menu-toggle>
                    <span></span>
                    <span></span>
                    <span></span>
                </button>

                <nav class="taschen-nav" aria-label="Menu chính">
                    <div class="taschen-nav-item has-megamenu">
                        <a class="taschen-nav-link {{ request()->routeIs('books.index') && !request('category') && !request('q') && !request('sort') ? 'active' : '' }}" href="javascript:void(0)" role="button" aria-haspopup="true" aria-expanded="false" style="cursor: pointer;">BOOKS</a>
                        
                        <!-- TASCHEN FULL-WIDTH DYNAMIC MEGA MENU -->
                        <div class="taschen-megamenu" id="sach-megamenu" aria-label="Danh mục chi tiết Sách">
                            <div class="taschen-megamenu-inner">
                                <div class="taschen-megamenu-grid">
                                    <!-- Cột 1: Phân loại & Tuyển tập chính -->
                                    <div class="taschen-megamenu-col">
                                        <ul class="taschen-megamenu-list">
                                            <li><a class="taschen-megamenu-link {{ !request('category') && !request('q') && !request('sort') && request()->routeIs('books.index') ? 'is-current' : '' }}" href="{{ route('books.index') }}">Tất cả ấn phẩm</a></li>
                                            <li><a class="taschen-megamenu-link {{ request('sort') === 'newest' ? 'is-current' : '' }}" href="{{ route('books.index', ['sort' => 'newest']) }}">Ấn phẩm mới phát hành</a></li>
                                            <li><a class="taschen-megamenu-link {{ request('sort') === 'bestseller' ? 'is-current' : '' }}" href="{{ route('books.index', ['sort' => 'bestseller']) }}">Ấn bản tiêu biểu</a></li>
                                        </ul>
                                    </div>

                                    <!-- Các cột danh mục thực tế từ Database -->
                                    @php
                                        $categoriesList = $navCategories ?? collect();
                                        $chunkSize = ceil($categoriesList->count() > 0 ? $categoriesList->count() / 3 : 1);
                                        $categoryChunks = $categoriesList->chunk(max(1, (int)$chunkSize));
                                    @endphp

                                    @foreach ($categoryChunks as $chunk)
                                        <div class="taschen-megamenu-col">
                                            <ul class="taschen-megamenu-list">
                                                @foreach ($chunk as $cat)
                                                    <li>
                                                        <a class="taschen-megamenu-link {{ request('category') === $cat->slug ? 'is-current' : '' }}" href="{{ route('books.index', ['category' => $cat->slug]) }}">
                                                             {{ $cat->name }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <a class="taschen-nav-link {{ request()->routeIs('gifts.*') ? 'active' : '' }}" href="{{ route('gifts.index') }}">GIFTS</a>

                    <a class="taschen-nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">ABOUT US</a>
                </nav>
            </div>

            <!-- Center Brand Logo / Wordmark -->
            <div class="taschen-brand">
                <a href="{{ route('home') }}" aria-label="Trang chủ">
                    @if ($siteLogo = \App\Models\Setting::get('site_logo'))
                        <img src="{{ $siteLogo }}" alt="{{ \App\Models\Setting::get('site_name', 'KHO - CA') }}" class="taschen-brand-img">
                    @else
                        <img src="{{ asset('images/logo.png') }}" alt="{{ \App\Models\Setting::get('site_name', 'KHO - CA') }}" class="taschen-brand-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-flex';">
                        <span class="taschen-brand-logo" style="display: none;">
                            <span class="brand-blue">KHO</span>
                            <span class="brand-dash">-</span>
                            <span class="brand-blue">CA</span>
                        </span>
                    @endif
                </a>
            </div>

            <!-- Right Tools (Search Only) -->
            <div class="taschen-header-right">
                <form class="taschen-search-form" action="{{ $isGiftRoute ? route('gifts.index') : route('books.index') }}" method="get" role="search">
                    <div class="taschen-search-field-wrap">
                        <input class="taschen-search-input" name="q" value="{{ request('q') }}" placeholder="{{ $isGiftRoute ? 'Tìm kiếm sản phẩm, thương hiệu...' : 'Tìm kiếm sách, tác giả, thể loại...' }}" autocomplete="off" aria-label="{{ $isGiftRoute ? 'Tìm kiếm sản phẩm Gift' : 'Tìm kiếm sách' }}" @unless($isGiftRoute) data-search-input aria-controls="search-autocomplete" @endunless>
                        @unless($isGiftRoute)<div class="taschen-search-autocomplete" id="search-autocomplete" data-search-autocomplete hidden></div>@endunless
                    </div>
                    <button class="taschen-search-btn" type="submit" aria-label="Tìm kiếm">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    </button>
                </form>
            </div>
        </div>

        <!-- Mega Menu Dim Backdrop Overlay -->
        <div class="taschen-megamenu-backdrop" data-megamenu-backdrop></div>

        <!-- Mobile Drawer Navigation (Taschen Style Vertical Menu) -->
        <div class="taschen-mobile-nav" data-mobile-nav>
            <div class="taschen-mobile-header">
                <button type="button" class="taschen-mobile-close-btn" data-mobile-close aria-label="Đóng menu">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
                <form class="taschen-mobile-search-form" action="{{ $isGiftRoute ? route('gifts.index') : route('books.index') }}" method="get" role="search">
                    <button class="taschen-mobile-search-btn" type="submit" aria-label="Tìm kiếm">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    </button>
                    <input class="taschen-mobile-search-input" name="q" value="{{ request('q') }}" placeholder="{{ $isGiftRoute ? 'Tìm kiếm Gift...' : 'Tìm kiếm sách...' }}" autocomplete="off" aria-label="Tìm kiếm">
                </form>
            </div>

            <div class="taschen-mobile-menu-body">
                <a class="taschen-mobile-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">HOME</a>

                <div class="taschen-mobile-accordion is-open" id="mobileBooksAccordion">
                    <button type="button" class="taschen-mobile-accordion-btn" onclick="this.closest('.taschen-mobile-accordion').classList.toggle('is-open')" aria-expanded="true">
                        <span>BOOKS</span>
                        <span class="taschen-mobile-acc-icon"></span>
                    </button>
                    <div class="taschen-mobile-accordion-content">
                        <div class="taschen-mobile-sublist">
                            <a class="taschen-mobile-sublink" href="{{ route('books.index') }}">Tất cả ấn phẩm</a>
                            <a class="taschen-mobile-sublink" href="{{ route('books.index', ['sort' => 'newest']) }}">Ấn phẩm mới phát hành</a>
                            <a class="taschen-mobile-sublink" href="{{ route('books.index', ['sort' => 'bestseller']) }}">Ấn bản tiêu biểu</a>
                        </div>
                        <div class="taschen-mobile-sublist-divider"></div>
                        <div class="taschen-mobile-sublist">
                            @foreach ($categoriesList as $cat)
                                <a class="taschen-mobile-sublink {{ request('category') === $cat->slug ? 'active' : '' }}" href="{{ route('books.index', ['category' => $cat->slug]) }}">{{ $cat->name }}</a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <a class="taschen-mobile-link {{ request()->routeIs('gifts.*') ? 'active' : '' }}" href="{{ route('gifts.index') }}">GIFTS</a>
                <a class="taschen-mobile-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">ABOUT US</a>
            </div>
        </div>
    </header>

    <!-- MAIN CONTENT -->
    <main id="main-content">
        @yield('content')
    </main>

    <!-- TASCHEN 4-COLUMN FOOTER -->
    <footer class="taschen-footer" id="footer-section">
        <div class="container">
            <div class="taschen-footer-grid">
                <!-- Col 1: Brand & Philosophy -->
                <div class="taschen-footer-col">
                    <div class="taschen-footer-brand">
                        <span class="brand-blue-footer">KHO</span> <span class="brand-dash-footer">-</span> <span class="brand-blue-footer">CA</span>
                    </div>
                    <p class="taschen-footer-text">
                        {{ \App\Models\Setting::get('footer_description', 'Nhà trưng bày và tôn vinh những ấn bản sách, truyện tranh nghệ thuật đặc sắc. Nơi tri thức hòa quyện cùng mỹ thuật thị giác.') }}
                    </p>
                    <div style="display: flex; gap: 16px; color: var(--t-gray-400); font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">
                        <span>Art</span> · <span>Design</span> · <span>Comics</span> · <span>Culture</span>
                    </div>
                </div>

                <!-- Col 2: Connections -->
                <div class="taschen-footer-col">
                    <h3>Khám phá</h3>
                    <div class="taschen-footer-links">
                        <a href="{{ route('books.index') }}">Tất cả ấn bản</a>
                        <a href="{{ route('home') }}#collections">Bộ sưu tập theo chủ đề</a>
                        <a href="{{ route('books.index', ['sort' => 'newest']) }}">Ấn phẩm mới phát hành</a>
                        <a href="{{ route('books.index') }}">Tra cứu danh mục</a>
                        <a href="{{ route('about') }}">Về chúng tôi &amp; Tôn chỉ</a>
                    </div>
                </div>



                <!-- Col 4: Newsletter Subscription -->
                <div class="taschen-footer-col">
                    <h3>Đăng ký nhận tin nghệ thuật</h3>
                    <p class="taschen-footer-text" style="margin-bottom: 8px;">
                        Đăng ký nhận thông tin về các bộ sưu tập mới, ấn bản giới hạn và các sự kiện triển lãm đặc biệt.
                    </p>
                    <form class="taschen-newsletter-form" action="#" method="post" onsubmit="alert('Cảm ơn bạn đã đăng ký nhận tin nghệ thuật!'); return false;">
                        <input type="email" placeholder="Nhập địa chỉ email của bạn..." required aria-label="Địa chỉ email">
                        <button type="submit" aria-label="Đăng ký nhận tin">→</button>
                    </form>
                </div>
            </div>

            <!-- Footer Bottom -->
            <div class="taschen-footer-bottom">
                <span>{{ \App\Models\Setting::get('footer_copyright', '© '.date('Y').' Trưng Bày Sách & Truyện. Mọi quyền được bảo lưu.') }}</span>
                <span>Không gian trưng bày nghệ thuật xuất bản cao cấp</span>
            </div>
        </div>
    </footer>

    <!-- Global Interactions JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile Menu Toggle & Close
            const toggle = document.querySelector('[data-menu-toggle]');
            const mobileNav = document.querySelector('[data-mobile-nav]');
            const mobileCloseBtn = document.querySelector('[data-mobile-close]');
            if (toggle && mobileNav) {
                toggle.addEventListener('click', function() {
                    const isOpen = mobileNav.classList.toggle('is-open');
                    toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                    document.body.style.overflow = isOpen ? 'hidden' : '';
                });
                if (mobileCloseBtn) {
                    mobileCloseBtn.addEventListener('click', function() {
                        mobileNav.classList.remove('is-open');
                        toggle.setAttribute('aria-expanded', 'false');
                        document.body.style.overflow = '';
                    });
                }
            }

            // Mega Menu Robust Hover, Click & Backdrop Handling
            const megaNavItem = document.querySelector('.has-megamenu');
            const megaDropdown = document.querySelector('.taschen-megamenu');
            const backdrop = document.querySelector('[data-megamenu-backdrop]');
            if (megaNavItem && megaDropdown) {
                let closeTimer = null;

                const openMenu = () => {
                    if (closeTimer) clearTimeout(closeTimer);
                    megaNavItem.classList.add('is-open');
                    megaDropdown.classList.add('is-open');
                    if (backdrop) backdrop.classList.add('is-active');
                };

                const closeMenu = () => {
                    if (closeTimer) clearTimeout(closeTimer);
                    megaNavItem.classList.remove('is-open');
                    megaDropdown.classList.remove('is-open');
                    if (backdrop) backdrop.classList.remove('is-active');
                };

                const scheduleClose = () => {
                    if (closeTimer) clearTimeout(closeTimer);
                    closeTimer = setTimeout(closeMenu, 240);
                };

                megaNavItem.addEventListener('mouseenter', openMenu);
                megaNavItem.addEventListener('mouseleave', scheduleClose);
                megaDropdown.addEventListener('mouseenter', openMenu);
                megaDropdown.addEventListener('mouseleave', scheduleClose);
                megaNavItem.addEventListener('focusin', openMenu);
                megaNavItem.addEventListener('focusout', scheduleClose);

                // Click toggle on Sách button
                const megaNavTrigger = megaNavItem.querySelector('.taschen-nav-link');
                if (megaNavTrigger) {
                    megaNavTrigger.addEventListener('click', function(e) {
                        e.preventDefault();
                        if (megaNavItem.classList.contains('is-open')) {
                            closeMenu();
                        } else {
                            openMenu();
                        }
                    });
                }

                if (backdrop) {
                    backdrop.addEventListener('click', closeMenu);
                }
            }

            // Smart catalogue autocomplete and popular search suggestions
            const searchInput = document.querySelector('[data-search-input]');
            const searchPanel = document.querySelector('[data-search-autocomplete]');
            const suggestionUrl = @json(route('books.suggestions'));
            let searchTimer = null;
            let searchRequest = null;

            if (searchInput && searchPanel) {
                const closeSearchPanel = () => {
                    searchPanel.hidden = true;
                    searchPanel.classList.remove('is-open');
                };

                const appendText = (parent, tag, text, className = '') => {
                    const element = document.createElement(tag);
                    element.textContent = text;
                    if (className) element.className = className;
                    parent.appendChild(element);
                    return element;
                };

                const renderSearchPanel = (payload, term) => {
                    searchPanel.replaceChildren();
                    const hasBooks = Array.isArray(payload.books) && payload.books.length > 0;
                    const hasPopular = Array.isArray(payload.popular) && payload.popular.length > 0;

                    if (hasBooks) {
                        appendText(searchPanel, 'div', 'Ấn phẩm phù hợp', 'taschen-search-autocomplete__heading');
                        payload.books.forEach((book) => {
                            const link = document.createElement('a');
                            link.href = book.url;
                            link.className = 'taschen-search-autocomplete__book';
                            appendText(link, 'strong', book.title);
                            const meta = [book.author, book.category].filter(Boolean).join(' · ');
                            if (meta) appendText(link, 'span', meta);
                            searchPanel.appendChild(link);
                        });
                    }

                    if (hasPopular) {
                        appendText(searchPanel, 'div', term ? 'Từ khóa phổ biến khác' : 'Từ khóa phổ biến', 'taschen-search-autocomplete__heading');
                        const popularWrap = document.createElement('div');
                        popularWrap.className = 'taschen-search-autocomplete__popular';
                        payload.popular.forEach((item) => {
                            const link = document.createElement('a');
                            link.href = '{{ route('books.index') }}?q=' + encodeURIComponent(item.term);
                            link.textContent = item.term;
                            popularWrap.appendChild(link);
                        });
                        searchPanel.appendChild(popularWrap);
                    }

                    if (!hasBooks && !hasPopular && term) {
                        appendText(searchPanel, 'div', 'Nhấn Enter để xem toàn bộ kết quả', 'taschen-search-autocomplete__empty');
                    }

                    if (searchPanel.children.length) {
                        searchPanel.hidden = false;
                        searchPanel.classList.add('is-open');
                    } else {
                        closeSearchPanel();
                    }
                };

                const loadSearchSuggestions = (term = '') => {
                    if (searchRequest) searchRequest.abort();
                    searchRequest = new AbortController();
                    fetch(suggestionUrl + '?q=' + encodeURIComponent(term), {
                        headers: { 'Accept': 'application/json' },
                        signal: searchRequest.signal,
                    })
                        .then((response) => response.ok ? response.json() : Promise.reject())
                        .then((payload) => renderSearchPanel(payload, term))
                        .catch((error) => {
                            if (error.name !== 'AbortError') closeSearchPanel();
                        });
                };

                searchInput.addEventListener('focus', () => loadSearchSuggestions(searchInput.value.trim()));
                searchInput.addEventListener('input', () => {
                    clearTimeout(searchTimer);
                    const term = searchInput.value.trim();
                    searchTimer = setTimeout(() => loadSearchSuggestions(term), term ? 180 : 0);
                });

                document.addEventListener('click', (event) => {
                    if (!event.target.closest('.taschen-search-form')) closeSearchPanel();
                });
            }
        });
    </script>
    <script src="{{ asset('js/custom-select.js') }}?v={{ file_exists(public_path('js/custom-select.js')) ? filemtime(public_path('js/custom-select.js')) : time() }}"></script>
    @stack('scripts')
</body>
</html>
