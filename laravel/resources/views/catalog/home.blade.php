@extends('layouts.app')

@section('title', \App\Models\Setting::get('site_name', 'KHO - CA').' · Books & Arts')
@section('meta_description', 'Khám phá thế giới sách và truyện tranh nghệ thuật được tuyển chọn theo chuẩn mực triển lãm quốc tế.')

@section('content')
    @php
        $heroBooks = $books->take(4);
        $trendingBooks = $books->take(12);
        $heroImages = [
            'images/taschen/hero-star-wars.jpg',
            'images/taschen/hero-45th-edition.jpg',
            'images/taschen/hero-gourmand.jpg',
            'images/taschen/hero-printed-fabrics.jpg',
            'images/taschen/hero-national-geographic.jpg',
        ];
    @endphp

    <!-- HERO TEASER FLEX (FULLSCREEN EDITORIAL SLIDER) -->
    <section class="taschen-hero-slider" id="hero-slider" aria-label="Tuyển tập nổi bật">
        <div class="taschen-slider-track">
            @if ($banners->isNotEmpty())
                @foreach ($banners as $index => $banner)
                    <div class="taschen-slide taschen-slide--{{ $banner->text_position ?? 'left' }} {{ $loop->first ? 'is-active' : '' }}" data-slide="{{ $index }}">
                        <div class="taschen-slide-bg">
                            <picture>
                                @if ($banner->mobile_image_url)
                                    <source media="(max-width: 768px)" srcset="{{ $banner->mobile_image_url }}">
                                @endif
                                @if ($banner->image_url)
                                    <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" loading="{{ $loop->first ? 'eager' : 'lazy' }}">
                                @else
                                    @php($fallbackImg = $heroImages[$index % count($heroImages)])
                                    <img src="{{ asset($fallbackImg) }}" alt="{{ $banner->title }}" loading="{{ $loop->first ? 'eager' : 'lazy' }}">
                                @endif
                            </picture>
                        </div>

                        <div class="container" style="position: relative; z-index: 3;">
                            <div class="taschen-slide-content">
                                <span class="taschen-slide-badge">{{ $banner->eyebrow ?: 'ẤN BẢN NỔI BẬT' }}</span>
                                
                                <h1 class="taschen-slide-title">{{ $banner->title }}</h1>
                                
                                @if ($banner->description)
                                    <div class="taschen-slide-quote">
                                        “{{ $banner->description }}”
                                    </div>
                                @endif

                                @if ($banner->link_url)
                                    <a class="taschen-btn" href="{{ $banner->link_url }}">
                                        <span>{{ $banner->link_label ?: 'Khám phá ngay' }}</span>
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                                    </a>
                                @else
                                    <a class="taschen-btn" href="{{ route('books.index') }}">
                                        <span>{{ $banner->link_label ?: 'Khám phá ấn bản' }}</span>
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                @forelse ($heroBooks as $index => $hero)
                    @php($heroImage = $heroImages[$index % count($heroImages)])
                    <div class="taschen-slide {{ $loop->first ? 'is-active' : '' }}" data-slide="{{ $index }}">
                        <div class="taschen-slide-bg">
                            <img src="{{ asset($heroImage) }}" alt="{{ $hero->title }}" loading="{{ $loop->first ? 'eager' : 'lazy' }}">
                        </div>

                        <div class="container" style="position: relative; z-index: 3;">
                            <div class="taschen-slide-content">
                                <span class="taschen-slide-badge">{{ $hero->categories->first()?->name ?? 'ẤN BẢN NGHỆ THUẬT' }}</span>
                                
                                <h1 class="taschen-slide-title">{{ $hero->title }}</h1>
                                
                                <div class="taschen-slide-quote">
                                    @if ($hero->summary)
                                        “{{ \Illuminate\Support\Str::limit($hero->summary, 130) }}”
                                    @else
                                        “Một ấn phẩm nghệ thuật đỉnh cao, mang lại trải nghiệm thị giác và giá trị tri thức trọn vẹn.”
                                    @endif
                                    <span>— {{ $hero->author ?: 'EDITORIAL REVIEW' }}</span>
                                </div>

                                <a class="taschen-btn" href="{{ route('books.show', $hero) }}">
                                    <span>Khám phá ấn bản</span>
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="taschen-slide is-active">
                        <div class="taschen-slide-bg">
                            <img src="{{ asset($heroImages[0]) }}" alt="" loading="eager">
                        </div>
                        <div class="container">
                            <div class="taschen-slide-content">
                                <span class="taschen-slide-badge">BỘ SƯU TẬP ĐẶC BIỆT</span>
                                <h1 class="taschen-slide-title">Không gian Trưng Bày Sách Nghệ Thuật</h1>
                                <div class="taschen-slide-quote">
                                    “Những cuốn sách lưu giữ giá trị của thời gian và vẻ đẹp của nghệ thuật đỉnh cao.”
                                    <span>— BAN BIÊN TẬP</span>
                                </div>
                                <a class="taschen-btn" href="{{ route('books.index') }}">Khám phá thư viện</a>
                            </div>
                        </div>
                    </div>
                @endforelse
            @endif
        </div>

        @php($slideCount = $banners->isNotEmpty() ? $banners->count() : $heroBooks->count())
        @if ($slideCount > 1)
            <!-- Slider Dots -->
            <div class="taschen-slider-dots">
                @for ($index = 0; $index < $slideCount; $index++)
                    <button class="taschen-dot {{ $index === 0 ? 'is-active' : '' }}" type="button" aria-label="Chuyển đến slide {{ $index + 1 }}" data-dot="{{ $index }}"></button>
                @endfor
            </div>

            <!-- Slider Arrows -->
            <button class="taschen-slider-arrow taschen-slider-arrow--prev" type="button" aria-label="Slide trước" data-slider-prev>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
            </button>
            <button class="taschen-slider-arrow taschen-slider-arrow--next" type="button" aria-label="Slide kế tiếp" data-slider-next>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
            </button>
        @endif
    </section>

    <!-- PRODUCT PLACEMENT SECTION: NEW & TRENDING -->
    <section class="taschen-section" id="limited">
        <div class="container">
            <div class="taschen-section-header">
                <div class="taschen-section-header-text">
                    <h2 class="taschen-section-title">NEW &amp; TRENDING</h2>
                </div>
            </div>

            <div class="taschen-grid-4">
                @forelse ($trendingBooks as $book)
                    <x-book-card :book="$book" :is-new="$loop->iteration <= 4" />
                @empty
                    <div style="grid-column: 1 / -1; text-align: center; padding: 60px 0; color: var(--text-secondary);">
                        <p style="font-family: var(--font-serif); font-size: 24px; margin-bottom: 12px;">Hiện chưa có ấn phẩm nào được trưng bày</p>
                        <p>Vui lòng quay lại sau hoặc truy cập trang quản trị để cập nhật thêm sách.</p>
                    </div>
                @endforelse
            </div>

            @if ($trendingBooks->isNotEmpty())
                <div class="taschen-section-footer">
                    <a class="taschen-btn-outline-center" href="{{ route('books.index') }}">
                        Xem tất cả
                    </a>
                </div>
            @endif
        </div>
    </section>

    <!-- GIFTS HORIZONTAL CAROUSEL SECTION -->
    @if ($gifts->isNotEmpty())
        <section class="taschen-section gifts-section" id="gifts-showcase">
            <div class="container">
                <div class="gifts-section-header">
                    <h2 class="gifts-section-title">GIFTS</h2>
                    <div class="gifts-header-actions">
                        <a class="gifts-see-more-btn" href="{{ route('gifts.index') }}">
                            <span>Xem tất cả</span>
                        </a>
                        <div class="gifts-nav-arrows">
                            <button class="gifts-nav-arrow gifts-nav-prev" type="button" aria-label="Cuộn sang trái" data-gifts-prev>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                            </button>
                            <button class="gifts-nav-arrow gifts-nav-next" type="button" aria-label="Cuộn sang phải" data-gifts-next>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="gifts-carousel-wrapper">
                    <div class="gifts-carousel-track" id="gifts-carousel-track">
                        @foreach ($gifts as $gift)
                            <article class="gifts-card">
                                <a class="gifts-card-media" href="{{ route('gifts.show', $gift) }}" aria-label="{{ $gift->title }}">
                                    @if ($gift->cover_url)
                                        <div class="gifts-card-img-wrap">
                                            <img src="{{ $gift->cover_url }}" alt="{{ $gift->title }}" loading="lazy">
                                        </div>
                                    @else
                                        <div class="gifts-card-fallback">
                                            <span>GIFTS</span>
                                            <strong>{{ \Illuminate\Support\Str::limit($gift->title, 35) }}</strong>
                                        </div>
                                    @endif
                                </a>
                                <div class="gifts-card-info">
                                    <h3 class="gifts-card-title">
                                        <a href="{{ route('gifts.show', $gift) }}">{{ $gift->title }}</a>
                                    </h3>
                                    @if ($gift->brand)
                                        <p class="gifts-card-subtitle">{{ $gift->brand }}</p>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif

    @if ($featuredBooks->isNotEmpty())
        <section class="taschen-section admin-featured-section" id="featured-books">
            <div class="container">
                <div class="taschen-section-header">
                    <div class="taschen-section-header-text">
                        <h2 class="taschen-section-title">EDITOR'S SELECTION</h2>
                    </div>
                </div>
                <div class="taschen-grid-4">
                    @foreach ($featuredBooks as $book)
                        <x-book-card :book="$book" :is-new="false" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($collections->isNotEmpty())
        <section class="taschen-section" id="collections" style="padding-top: 20px;">
            <div class="container">
                <div class="taschen-section-header"><div class="taschen-section-header-text"><p class="taschen-section-kicker">CURATED COLLECTIONS</p><h2 class="taschen-section-title">Bộ Sưu Tập</h2></div></div>
                <div class="taschen-collections-grid">
                    @foreach ($collections as $collection)
                        <a class="taschen-collection-card" href="{{ route('collections.show', $collection) }}">
                            @if($collection->cover_url)<img src="{{ $collection->cover_url }}" alt="{{ $collection->title }}" loading="lazy">@endif
                            <div><p>{{ $collection->subtitle ?: 'Bộ sưu tập tuyển chọn' }}</p><h3>{{ $collection->title }}</h3><span>{{ $collection->books_count }} ấn phẩm →</span></div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- CATEGORY SHOWCASE TILES -->
    <?php if ($categories->isNotEmpty() || $exploreTiles->isNotEmpty()): ?>
        <section class="taschen-section" id="category-showcase" style="padding-top: 20px;">
            <div class="container">
                <div class="taschen-section-header">
                    <div class="taschen-section-header-text">
                        <h2 class="taschen-section-title">MORE TO EXPLORE</h2>
                    </div>
                </div>

                <div class="taschen-categories-grid">
                    <?php if ($exploreTiles->isNotEmpty()): ?>
                        <?php foreach ($exploreTiles as $tileIndex => $tile):
                            $tileImage = $tile->image_url ?: asset($heroImages[$tileIndex % count($heroImages)]);
                            $tileOpacity = min(0.95, max(0.35, (float) ($tile->overlay_opacity ?? 0.78)));
                            $tileSoftOpacity = min(0.75, $tileOpacity * 0.58);
                        ?>
                            <a class="taschen-category-card" href="{{ $tile->resolved_url }}">
                                <div class="taschen-category-bg" style="background-image: linear-gradient(135deg, rgba(20,20,20,{{ number_format($tileSoftOpacity, 2, '.', '') }}) 0%, rgba(5,5,5,{{ number_format($tileOpacity, 2, '.', '') }}) 100%), url('{{ $tileImage }}');"></div>
                                <div class="taschen-category-content">
                                    <h3 class="taschen-category-name">{{ $tile->title }}</h3>
                                    <span class="taschen-category-count">{{ $tile->subtitle ?: 'Khám phá bộ sưu tập →' }}</span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <?php foreach ($categories->take(4) as $catIndex => $cat): ?>
                            <a class="taschen-category-card" href="{{ route('books.index', ['category' => $cat->slug]) }}">
                                <div class="taschen-category-bg" style="background-image: linear-gradient(135deg, rgba(20,20,20,0.45) 0%, rgba(5,5,5,0.88) 100%), url('{{ asset($heroImages[$catIndex % count($heroImages)]) }}');"></div>
                                <div class="taschen-category-content">
                                    <h3 class="taschen-category-name">{{ $cat->name }}</h3>
                                    <span class="taschen-category-count">Khám phá bộ sưu tập →</span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

@endsection

@push('scripts')
<script>
    // Hero Slider Carousel Script
    document.addEventListener('DOMContentLoaded', function() {
        const slides = document.querySelectorAll('.taschen-slide');
        const dots = document.querySelectorAll('[data-dot]');
        const prevBtn = document.querySelector('[data-slider-prev]');
        const nextBtn = document.querySelector('[data-slider-next]');

        if (slides.length <= 1) return;

        let currentIndex = 0;
        let slideInterval = null;

        function goToSlide(index) {
            slides[currentIndex].classList.remove('is-active');
            if (dots[currentIndex]) dots[currentIndex].classList.remove('is-active');

            currentIndex = (index + slides.length) % slides.length;

            slides[currentIndex].classList.add('is-active');
            if (dots[currentIndex]) dots[currentIndex].classList.add('is-active');
        }

        function startAutoPlay() {
            stopAutoPlay();
            slideInterval = setInterval(() => {
                goToSlide(currentIndex + 1);
            }, 6000);
        }

        function stopAutoPlay() {
            if (slideInterval) clearInterval(slideInterval);
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                goToSlide(currentIndex - 1);
                startAutoPlay();
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                goToSlide(currentIndex + 1);
                startAutoPlay();
            });
        }

        dots.forEach(dot => {
            dot.addEventListener('click', function() {
                const targetIndex = parseInt(this.getAttribute('data-dot'), 10);
                goToSlide(targetIndex);
                startAutoPlay();
            });
        });

        // Touch Swipe support for mobile
        const sliderContainer = document.querySelector('.taschen-hero-slider');
        if (sliderContainer) {
            let touchStartX = 0;
            let touchEndX = 0;

            sliderContainer.addEventListener('touchstart', (e) => {
                touchStartX = e.changedTouches[0].screenX;
            }, { passive: true });

            sliderContainer.addEventListener('touchend', (e) => {
                touchEndX = e.changedTouches[0].screenX;
                const diff = touchStartX - touchEndX;
                if (Math.abs(diff) > 45) {
                    if (diff > 0) {
                        goToSlide(currentIndex + 1);
                    } else {
                        goToSlide(currentIndex - 1);
                    }
                    startAutoPlay();
                }
            }, { passive: true });
        }

        startAutoPlay();

        // GIFTS Horizontal Carousel Scrolling
        const giftsTrack = document.getElementById('gifts-carousel-track');
        const giftsPrevBtn = document.querySelector('[data-gifts-prev]');
        const giftsNextBtn = document.querySelector('[data-gifts-next]');

        if (giftsTrack) {
            const scrollStep = 320;
            if (giftsPrevBtn) {
                giftsPrevBtn.addEventListener('click', () => {
                    giftsTrack.scrollBy({ left: -scrollStep, behavior: 'smooth' });
                });
            }
            if (giftsNextBtn) {
                giftsNextBtn.addEventListener('click', () => {
                    giftsTrack.scrollBy({ left: scrollStep, behavior: 'smooth' });
                });
            }
        }
    });
</script>
@endpush
