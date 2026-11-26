@php
    use App\Models\Setting;
    $selectedCatObj = $selectedCatObj ?? ($categories->firstWhere('slug', $selectedCategory) ?? null);
    // Gift có trang và component riêng, không dùng giao diện danh mục Sách.
    $isGiftPage = false;
    $activeFilterCount = ($selectedCategory ? 1 : 0) + ($term ? 1 : 0) + ($sort ? 1 : 0) + ($year ? 1 : 0) + ($author ? 1 : 0) + ($publisher ? 1 : 0) + ($language ? 1 : 0) + ($condition ? 1 : 0) + ($availability ? 1 : 0);
@endphp

@extends('layouts.app')

@section('title', ($isGiftPage ? 'Tất cả sản phẩm · ' : ($selectedCatObj ? $selectedCatObj->name.' · ' : '')).'Danh Mục Sách & Ấn Bản · '.Setting::get('site_name', 'KHO - CA'))
@section('meta_description', 'Khám phá bộ sưu tập sách nghệ thuật, văn học, lịch sử và các ấn bản sưu tầm đặc sắc.')

@section('content')
    <div class="container" style="padding-top: 0; padding-bottom: 80px;">
        <!-- BREADCRUMBS -->
        <nav class="taschen-breadcrumb" aria-label="Breadcrumb">
            <div class="taschen-breadcrumb-inner">
                <a href="{{ route('home') }}">Trang chủ</a>
                <span class="sep">|</span>
                <a href="{{ route('books.index') }}">Sách</a>
                <span class="sep">|</span>
                <span class="current">{{ $isGiftPage ? 'Tất cả sản phẩm' : ($selectedCatObj ? $selectedCatObj->name : 'Tất cả ấn phẩm') }} <span class="count">({{ $books->total() }} tác phẩm)</span></span>
            </div>
        </nav>

        @if ($popularSearches->isNotEmpty() && !$term)
            <div class="taschen-popular-searches" aria-label="Từ khóa phổ biến">
                <span class="taschen-popular-searches__label">Từ khóa phổ biến</span>
                @foreach ($popularSearches as $popularSearch)
                    <a href="{{ route('books.index', ['q' => $popularSearch->term]) }}">{{ $popularSearch->term }}</a>
                @endforeach
            </div>
        @endif

        <!-- PRODUCT LIST SECTION (TASCHEN LUXURY CATEGORY INTERFACE) -->
        <section class="product-list">
            <!-- HEADER & ACTIONS -->
            <div class="product-list__actions">
                <div>
                    @unless ($isGiftPage)
                        <p style="font-family: var(--font-sans, 'Inter'); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 2px; color: var(--text-secondary, #666); margin-bottom: 6px;">
                            DANH MỤC &middot; BỘ SƯU TẬP &middot; ẤN BẢN
                        </p>
                    @endunless
                    <h1 class="product-list__wrapper--title" role="heading" aria-level="1">
                        {{ $isGiftPage ? 'Tất cả sản phẩm' : ($selectedCatObj ? $selectedCatObj->name : 'Tất Cả Sách & Ấn Bản') }}
                    </h1>
                    @if ($term)
                        <p style="font-size: 14px; color: var(--text-secondary, #666); margin-top: 4px;">
                            Kết quả tìm kiếm cho: “<strong>{{ $term }}</strong>”
                        </p>
                    @endif
                </div>

                <div class="product-list__actions-filter-wrapper">
                    <!-- Quick Active Filter Tags -->
                    <div class="product-list__actions-active-filters">
                        @if ($term)
                            <a href="{{ route('books.index', array_filter(['category' => $selectedCategory, 'sort' => $sort])) }}" class="button button--secondary button--small" style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; font-size: 12px; border: 1px solid #ddd; border-radius: 4px;">
                                <span>Từ khóa: {{ $term }}</span>
                                <span style="font-weight: 700;">&times;</span>
                            </a>
                        @endif
                        @unless ($isGiftPage)
                            @if ($selectedCatObj)
                                <a href="{{ route('books.index', ['q' => $term, 'sort' => $sort]) }}" class="button button--secondary button--small" style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; font-size: 12px; border: 1px solid #ddd; border-radius: 4px;">
                                    <span>{{ $selectedCatObj->name }}</span>
                                    <span style="font-weight: 700;">&times;</span>
                                </a>
                            @endif
                            @if ($year || $author || $publisher || $language || $condition || $availability)
                                <span class="taschen-filter-summary">Đang lọc nâng cao</span>
                            @endif
                        @endunless
                    </div>

                    @if ($isGiftPage)
                        <!-- Sorter for Gifts page only -->
                        <form method="get" action="{{ route('books.index') }}" style="display: inline-flex; align-items: center; gap: 8px;">
                            <input type="hidden" name="category" value="{{ $selectedCategory ?: 'gifts' }}">
                            @if ($term)
                                <input type="hidden" name="q" value="{{ $term }}">
                            @endif
                            <label for="giftSortSelect" style="font-size: 13px; color: var(--text-secondary, #666); font-weight: 500;">Sắp xếp:</label>
                            <select id="giftSortSelect" name="sort" onchange="this.form.submit()" style="padding: 7px 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px; background: #fff; cursor: pointer; color: #111;">
                                <option value="" {{ !$sort ? 'selected' : '' }}>Mặc định (A - Z)</option>
                                <option value="newest" {{ $sort === 'newest' ? 'selected' : '' }}>Mới nhất</option>
                                <option value="bestseller" {{ $sort === 'bestseller' ? 'selected' : '' }}>Nổi bật nhất</option>
                            </select>
                        </form>
                    @else
                        <!-- Open Slide-out Drawer Button for Books -->
                        <button class="product-list__filter-button button button--primary button--icon button--icon-left" type="button" data-filter-toggle aria-expanded="false">
                            <span class="button__text">Bộ lọc &amp; Sắp xếp</span>
                            @if ($activeFilterCount > 0)
                                <span class="taschen-badge" style="background: var(--t-red, #d92b2b); color: #fff; border-radius: 10px; padding: 1px 7px; font-size: 11px; margin-left: 6px;">{{ $activeFilterCount }}</span>
                            @endif
                            <span class="icon icon--sliders" style="margin-left: 6px;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="4" y1="21" x2="4" y2="14"></line>
                                    <line x1="4" y1="10" x2="4" y2="3"></line>
                                    <line x1="12" y1="21" x2="12" y2="12"></line>
                                    <line x1="12" y1="8" x2="12" y2="3"></line>
                                    <line x1="20" y1="21" x2="20" y2="16"></line>
                                    <line x1="20" y1="12" x2="20" y2="3"></line>
                                    <line x1="1" y1="14" x2="7" y2="14"></line>
                                    <line x1="9" y1="8" x2="15" y2="8"></line>
                                    <line x1="17" y1="16" x2="23" y2="16"></line>
                                </svg>
                            </span>
                        </button>
                    @endif
                </div>
            </div>

            <!-- MAIN GRID & FLYOUT DRAWER WRAPPER -->
            <div class="product-list__wrapper">
                @unless ($isGiftPage)
                    <!-- SLIDE-OUT FILTER FLYOUT DRAWER (OFF-CANVAS) -->
                    <section class="flyout flyout__closed" id="filter-flyout" tabindex="-1" aria-hidden="true">
                        <div class="flyout__overlay" data-filter-close></div>
                        
                        <div class="flyout__content">
                            <!-- Drawer Header with Close Button -->
                            <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 20px; border-bottom: 1px solid #e8e8e5; margin-bottom: 24px;">
                                <h2 style="font-family: var(--font-brand, 'Founders Grotesk Condensed', sans-serif); font-size: 24px; text-transform: uppercase; letter-spacing: 1px; margin: 0;">
                                    Bộ Lọc &amp; Sắp Xếp
                                </h2>
                                <button class="flyout__close-btn" type="button" data-filter-close aria-label="Đóng bảng bộ lọc" style="display: flex; align-items: center; justify-content: center; width: 36px; height: 36px; min-width: 36px; min-height: 36px; border-radius: 50%; background: #f4f4f2; border: 1px solid #e0e0dc; cursor: pointer; font-size: 16px; font-weight: 700; color: #000; z-index: 10;">&times;</button>
                            </div>

                            <!-- Filter Form -->
                            <form id="filter-form" action="{{ route('books.index') }}" method="get" class="product-list__filter-wrapper">
                                @if ($term)
                                    <input type="hidden" name="q" value="{{ $term }}">
                                @endif

                                <!-- Sorter Section -->
                                <div class="product-list__sorter" style="margin-bottom: 30px;">
                                    <label class="text text--bacchus text--regular" style="font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 10px;">
                                        Sắp xếp theo
                                    </label>
                                    <select name="sort" class="button button--secondary" style="width: 100%; padding: 12px 16px; border: 1px solid #ccc; font-size: 14px; background: #fff; cursor: pointer;" onchange="this.form.submit()">
                                        <option value="" {{ !$sort ? 'selected' : '' }}>Mặc định (Tựa sách A - Z)</option>
                                        <option value="newest" {{ $sort === 'newest' ? 'selected' : '' }}>Mới phát hành (Gần đây nhất)</option>
                                        <option value="bestseller" {{ $sort === 'bestseller' ? 'selected' : '' }}>Ấn bản tiêu biểu (Bán chạy nhất)</option>
                                    </select>
                                </div>

                                <div class="smart-filter-block">
                                    <h3>Khám phá nâng cao</h3>
                                    <div class="smart-filter-grid">
                                        <label>
                                            <span>Năm xuất bản</span>
                                            <select name="year">
                                                <option value="">Tất cả các năm</option>
                                                @foreach ($years as $filterYear)
                                                    <option value="{{ $filterYear }}" @selected($year === (string) $filterYear)>{{ $filterYear }}</option>
                                                @endforeach
                                            </select>
                                        </label>
                                        <label>
                                            <span>Tác giả</span>
                                            <select name="author">
                                                <option value="">Tất cả tác giả</option>
                                                @foreach ($authors as $filterAuthor)
                                                    <option value="{{ $filterAuthor }}" @selected($author === $filterAuthor)>{{ $filterAuthor }}</option>
                                                @endforeach
                                            </select>
                                        </label>
                                        <label>
                                            <span>Nhà xuất bản</span>
                                            <select name="publisher">
                                                <option value="">Tất cả NXB</option>
                                                @foreach ($publishers as $filterPublisher)
                                                    <option value="{{ $filterPublisher }}" @selected($publisher === $filterPublisher)>{{ $filterPublisher }}</option>
                                                @endforeach
                                            </select>
                                        </label>
                                        <label>
                                            <span>Ngôn ngữ</span>
                                            <select name="language">
                                                <option value="">Tất cả ngôn ngữ</option>
                                                @foreach ($languages as $filterLanguage)
                                                    <option value="{{ $filterLanguage }}" @selected($language === $filterLanguage)>{{ $filterLanguage }}</option>
                                                @endforeach
                                            </select>
                                        </label>
                                        <label>
                                            <span>Tình trạng sách</span>
                                            <select name="condition">
                                                <option value="">Tất cả tình trạng</option>
                                                @foreach ($bookConditions as $condKey => $condLabel)
                                                    <option value="{{ $condKey }}" @selected($condition === $condKey)>{{ $condLabel }}</option>
                                                @endforeach
                                            </select>
                                        </label>
                                        <label>
                                            <span>Trạng thái</span>
                                            <select name="availability">
                                                <option value="">Tất cả trạng thái</option>
                                                @foreach ($availabilityStatuses as $availabilityKey => $availabilityLabel)
                                                    <option value="{{ $availabilityKey }}" @selected($availability === $availabilityKey)>{{ $availabilityLabel }}</option>
                                                @endforeach
                                            </select>
                                        </label>
                                    </div>
                                </div>

                                <!-- Category Filter List Accordion -->
                                <div style="margin-bottom: 30px;">
                                    <h3 style="font-size: 14px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 14px; padding-bottom: 8px; border-bottom: 1px solid #f0f0f0;">
                                        Danh mục thể loại
                                    </h3>
                                    <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 10px;">
                                        <li>
                                            <label style="display: flex; align-items: center; justify-content: space-between; cursor: pointer; font-size: 14px;">
                                                <span style="display: flex; align-items: center; gap: 10px;">
                                                    <input type="radio" name="category" value="" {{ !$selectedCategory ? 'checked' : '' }} onchange="this.form.submit()">
                                                    <span>Tất cả thể loại</span>
                                                </span>
                                            </label>
                                        </li>
                                        @foreach ($categories as $category)
                                            <li>
                                                <label style="display: flex; align-items: center; justify-content: space-between; cursor: pointer; font-size: 14px;">
                                                    <span style="display: flex; align-items: center; gap: 10px;">
                                                        <input type="radio" name="category" value="{{ $category->slug }}" {{ $selectedCategory === $category->slug ? 'checked' : '' }} onchange="this.form.submit()">
                                                        <span style="{{ $selectedCategory === $category->slug ? 'font-weight: 700;' : '' }}">{{ $category->name }}</span>
                                                    </span>
                                                    <span style="font-size: 12px; color: #888;">({{ $category->books_count }})</span>
                                                </label>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>

                                <!-- Bottom Action Buttons -->
                                <div style="display: flex; gap: 12px; padding-top: 20px; border-top: 1px solid #e8e8e5; margin-top: auto;">
                                    <a href="{{ route('books.index') }}" class="button button--secondary" style="flex: 1; text-align: center; padding: 12px 16px; font-size: 13px; text-transform: uppercase; font-weight: 700; letter-spacing: 1px; border: 1px solid #ccc;">
                                        Đặt lại bộ lọc
                                    </a>
                                    <button type="submit" class="button button--primary" style="flex: 1; text-align: center; padding: 12px 16px; font-size: 13px; text-transform: uppercase; font-weight: 700; letter-spacing: 1px; background: #000; color: #fff;">
                                        Áp dụng
                                    </button>
                                </div>
                            </form>
                        </div>
                    </section>
                @endunless

                <!-- 4-COLUMN PRODUCT GRID (TASCHEN STANDARD) -->
                <div class="product-list__grid taschen-grid-4" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 48px 48px; width: 100%;">
                    @forelse ($books as $book)
                        <x-book-card :book="$book" :is-new="false" :badge="null" />
                    @empty
                        <div class="taschen-no-results">
                            <p style="font-family: var(--font-serif, 'Sabon Next LT Pro', serif); font-size: 32px; font-style: italic; margin-bottom: 12px; color: #111;">
                                {{ $isGiftPage ? 'Chưa có sản phẩm quà tặng phù hợp' : 'Không tìm thấy ấn phẩm phù hợp' }}
                            </p>
                            <p style="color: #666; margin-bottom: 24px; font-size: 15px;">
                                {{ $isGiftPage ? 'Hãy thử tìm kiếm với từ khóa khác hoặc quay lại xem toàn bộ thư viện sách.' : 'Hãy thử tìm kiếm với từ khóa khác, bỏ bớt bộ lọc hoặc duyệt theo thể loại tuyển chọn.' }}
                            </p>
                            @if ($nearbyBooks->isNotEmpty())
                                <div class="taschen-similar-results">
                                    <p class="taschen-similar-results__title">Có thể bạn đang tìm:</p>
                                    <div class="taschen-similar-results__list">
                                        @foreach ($nearbyBooks as $nearbyBook)
                                            <a href="{{ route('books.show', $nearbyBook) }}">
                                                <strong>{{ $nearbyBook->title }}</strong>
                                                @if ($nearbyBook->author)<span>{{ $nearbyBook->author }}</span>@endif
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            <a class="button button--primary" href="{{ route('books.index') }}" style="display: inline-block; padding: 12px 28px; background: #000; color: #fff; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; font-size: 13px;">
                                Xem tất cả sách
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- PAGINATION -->
            <div style="margin-top: 60px; display: flex; justify-content: center;">
                <x-pagination :paginator="$books" />
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.querySelector('[data-filter-toggle]');
            const flyout = document.getElementById('filter-flyout');
            const closeBtns = document.querySelectorAll('[data-filter-close]');

            if (toggleBtn && flyout) {
                const openDrawer = () => {
                    flyout.classList.remove('flyout__closed');
                    flyout.classList.add('flyout__open');
                    flyout.setAttribute('aria-hidden', 'false');
                    toggleBtn.setAttribute('aria-expanded', 'true');
                    document.body.style.overflow = 'hidden';
                };

                const closeDrawer = () => {
                    flyout.classList.remove('flyout__open');
                    flyout.classList.add('flyout__closed');
                    flyout.setAttribute('aria-hidden', 'true');
                    toggleBtn.setAttribute('aria-expanded', 'false');
                    document.body.style.overflow = '';
                };

                toggleBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (flyout.classList.contains('flyout__open')) {
                        closeDrawer();
                    } else {
                        openDrawer();
                    }
                });

                closeBtns.forEach(btn => {
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        closeDrawer();
                    });
                });

                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && flyout.classList.contains('flyout__open')) {
                        closeDrawer();
                    }
                });
            }
        });
    </script>
@endpush
