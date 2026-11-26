@extends('layouts.app')

@section('title', ($book->meta_title ?: $book->title).' · '.\App\Models\Setting::get('site_name', 'KHO - CA'))
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($book->meta_description ?: $book->summary ?: $book->title), 160))
@section('meta_og_image', $book->og_image_url)

@section('content')
    @php
        $contactPhone = \App\Models\Setting::get('contact_phone');
        $contactEmail = \App\Models\Setting::get('contact_email');
        $contactZalo = \App\Models\Setting::get('contact_zalo');
        $contactFacebook = \App\Models\Setting::get('contact_facebook') ?: \App\Models\Setting::get('contact_messenger');
        $phoneHref = $contactPhone ? preg_replace('/[^0-9+]/', '', $contactPhone) : null;
        $zaloHref = $contactZalo
            ? (\Illuminate\Support\Str::startsWith($contactZalo, ['http://', 'https://']) ? $contactZalo : 'https://zalo.me/'.preg_replace('/\D+/', '', $contactZalo))
            : null;
        $fbHref = $contactFacebook
            ? (\Illuminate\Support\Str::startsWith($contactFacebook, ['http://', 'https://']) ? $contactFacebook : 'https://facebook.com/'.$contactFacebook)
            : null;
        $galleryItems = collect();
        if ($book->cover_url) {
            $galleryItems->push([
                'url' => $book->cover_url,
                'alt' => 'Bìa ấn bản '.$book->title,
                'label' => 'Bìa trước',
            ]);
        }
        foreach ($book->galleryImages as $galleryImage) {
            $galleryItems->push([
                'url' => $galleryImage->url,
                'alt' => $galleryImage->alt_text ?: 'Hình ảnh '.$book->title,
                'label' => 'Ảnh gallery',
            ]);
        }
        $themes = [
            ['bg' => 'linear-gradient(145deg, #0b1528 0%, #1e293b 100%)', 'accent' => '#e2c275', 'border' => 'rgba(226, 194, 117, 0.45)'],
            ['bg' => 'linear-gradient(145deg, #062419 0%, #0f3d2b 100%)', 'accent' => '#e6caa1', 'border' => 'rgba(230, 202, 161, 0.45)'],
            ['bg' => 'linear-gradient(145deg, #270d14 0%, #431622 100%)', 'accent' => '#f3d9a2', 'border' => 'rgba(243, 217, 162, 0.45)'],
            ['bg' => 'linear-gradient(145deg, #18181b 0%, #2c2c34 100%)', 'accent' => '#d4af37', 'border' => 'rgba(212, 175, 55, 0.45)'],
            ['bg' => 'linear-gradient(145deg, #1b1308 0%, #3a2810 100%)', 'accent' => '#ffd27d', 'border' => 'rgba(255, 210, 125, 0.45)'],
        ];
        $theme = $themes[($book->id ?? 0) % count($themes)];
    @endphp
    <div class="container taschen-detail">
        <!-- BREADCRUMBS -->
        @php
            $primaryCategory = $book->categories->first();
        @endphp
        <nav class="taschen-breadcrumb" aria-label="Breadcrumb" style="margin-bottom: 24px !important; padding-top: 0 !important;">
            <div class="taschen-breadcrumb-inner">
                <a href="{{ route('home') }}">Trang chủ</a>
                <span class="sep">|</span>
                <a href="{{ route('books.index') }}">Sách</a>
                @if ($primaryCategory)
                    <span class="sep">|</span>
                    <a href="{{ route('books.index', ['category' => $primaryCategory->slug]) }}">{{ $primaryCategory->name }}</a>
                @endif
                <span class="sep">|</span>
                <span class="current">{{ $book->title }}</span>
            </div>
        </nav>

        <!-- 2-Column Detail Layout -->
        <div class="taschen-detail-grid">
            <!-- Left: Large Book Presentation -->
            <div class="taschen-detail-media taschen-gallery-media">
                @if ($galleryItems->isNotEmpty())
                    <div class="taschen-gallery" data-gallery>
                        <figure class="taschen-gallery-main">
                            @if ($galleryItems->count() > 1)
                                <button class="taschen-stage-arrow taschen-stage-prev" type="button" aria-label="Ảnh trước" data-gallery-stage-prev>
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                                </button>
                                <button class="taschen-stage-arrow taschen-stage-next" type="button" aria-label="Ảnh tiếp theo" data-gallery-stage-next>
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                                </button>
                            @endif

                            <div class="taschen-detail-stage">
                                <div class="taschen-detail-book-3d" id="detailBookWrapper">
                                    <img class="taschen-detail-img" src="{{ $galleryItems->first()['url'] }}" alt="{{ $galleryItems->first()['alt'] }}" data-gallery-main>
                                </div>
                            </div>

                            <button class="taschen-gallery-expand" type="button" aria-label="Phóng to hình ảnh" data-gallery-open="0">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 3 21 3 21 9"></polyline><polyline points="9 21 3 21 3 15"></polyline><line x1="21" y1="3" x2="14" y2="10"></line><line x1="3" y1="21" x2="10" y2="14"></line></svg>
                            </button>

                            <figcaption data-gallery-caption style="display: none;">{{ $galleryItems->first()['label'] }}</figcaption>

                            @if ($galleryItems->count() > 1)
                                <div class="taschen-stage-counter">
                                    <span id="galleryCurrentIndex">1</span> / {{ $galleryItems->count() }}
                                </div>
                            @endif
                        </figure>
                    </div>
                @else
                    <div class="taschen-detail-stage">
                        <div class="taschen-detail-book-3d taschen-card-fallback taschen-detail-fallback" style="width: 320px; height: 460px; background: {{ $theme['bg'] }}; border: 1px solid {{ $theme['border'] }};">
                            <div class="taschen-fallback-inner" style="border: 1px solid {{ $theme['border'] }}; padding: 28px 22px;">
                                <div class="taschen-fallback-top">
                                    <span class="taschen-fallback-cat" style="color: {{ $theme['accent'] }}; font-size: 11px;">
                                        {{ strtoupper($book->categories->first()?->name ?? 'TASCHEN') }}
                                    </span>
                                    <div class="taschen-fallback-ornament" style="color: {{ $theme['accent'] }}; margin-top: 4px;">
                                        <svg width="24" height="14" viewBox="0 0 24 12" fill="currentColor"><circle cx="12" cy="6" r="3"/><line x1="0" y1="6" x2="7" y2="6" stroke="currentColor" stroke-width="1.5"/><line x1="17" y1="6" x2="24" y2="6" stroke="currentColor" stroke-width="1.5"/></svg>
                                    </div>
                                </div>
                                <div class="taschen-fallback-center">
                                    <strong class="taschen-fallback-title" style="font-size: 20px; line-height: 1.4;">{{ $book->title }}</strong>
                                </div>
                                <div class="taschen-fallback-bottom">
                                    @if ($book->author)
                                        <small class="taschen-fallback-author" style="font-size: 13px;">{{ $book->author }}</small>
                                    @else
                                        <small class="taschen-fallback-author" style="font-size: 13px;">Ấn bản tuyển chọn đặc biệt</small>
                                    @endif
                                    <span class="taschen-fallback-brand" style="color: {{ $theme['accent'] }}; font-size: 11px;">KHO - CA · ART &amp; EXHIBITION</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right: Product Info & Specs -->
            <div class="taschen-detail-info">
                <div class="taschen-detail-series">
                    @foreach ($book->categories as $category)
                        <a href="{{ route('books.index', ['category' => $category->slug]) }}" style="border-bottom: 1px solid var(--t-black);">{{ $category->name }}</a>
                        @if (!$loop->last) · @endif
                    @endforeach
                </div>

                <h1 class="taschen-detail-title">{{ $book->title }}</h1>

                <div class="taschen-detail-author">
                    Tác giả: <strong>{{ $book->author ?: 'Đang cập nhật tác giả' }}</strong>
                </div>

                @if ($book->summary)
                    <div class="taschen-detail-summary">
                        {!! nl2br(e($book->summary)) !!}
                    </div>
                @endif

                <!-- Specification Sheet (Chỉ hiển thị Số trang và Kích thước) -->
                @if ($book->page_count || $book->dimensions)
                    <div class="taschen-specs-table">
                        @if ($book->page_count)
                            <div class="taschen-specs-row">
                                <span class="taschen-specs-label">Số trang</span>
                                <span class="taschen-specs-value">{{ $book->page_count }} trang</span>
                            </div>
                        @endif

                        @if ($book->dimensions)
                            <div class="taschen-specs-row">
                                <span class="taschen-specs-label">Kích thước</span>
                                <span class="taschen-specs-value">{{ $book->dimensions }}</span>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Nút duy nhất: Liên hệ trực tuyến -->
                <div class="taschen-contact-action-wrap">
                    <button type="button" class="taschen-btn-online-contact" onclick="toggleContactModal(true)">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
                        <span>Liên hệ trực tuyến</span>
                    </button>
                </div>

                <!-- Modal chọn kênh: Facebook / Zalo / Email -->
                <div class="taschen-contact-modal" id="contactOnlineModal" style="display: none;" onclick="if(event.target === this) toggleContactModal(false)">
                    <div class="taschen-contact-modal-dialog">
                        <button type="button" class="taschen-contact-modal-close" onclick="toggleContactModal(false)" aria-label="Đóng">&times;</button>
                        <div class="taschen-contact-modal-header">
                            <h3 class="taschen-contact-modal-title">Liên hệ trực tuyến</h3>
                            <p class="taschen-contact-modal-book">Chọn phương thức bạn muốn kết nối trao đổi về ấn bản: <strong>{{ $book->title }}</strong></p>
                        </div>

                        <div class="taschen-modal-channels-grid">
                            @if ($fbHref)
                                <a class="taschen-modal-channel-card taschen-mc--fb" href="{{ route('books.contact.click', [$book, 'facebook']) }}" target="_blank" rel="noopener">
                                    <div class="taschen-mc-icon">
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                    </div>
                                    <div class="taschen-mc-info">
                                        <strong>Facebook</strong>
                                        <span>Nhắn tin trao đổi qua Facebook</span>
                                    </div>
                                    <span class="taschen-mc-arrow">→</span>
                                </a>
                            @endif

                            @if ($zaloHref)
                                <a class="taschen-modal-channel-card taschen-mc--zalo" href="{{ route('books.contact.click', [$book, 'zalo']) }}" target="_blank" rel="noopener">
                                    <div class="taschen-mc-icon">
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12c0 1.82.49 3.53 1.35 5L2 22l5.22-1.31C8.63 21.49 10.26 22 12 22c5.52 0 10-4.48 10-10S17.52 2 12 2zm0 18c-1.5 0-2.91-.41-4.13-1.12l-.3-.17-3.09.78.82-2.99-.19-.32C4.37 14.94 4 13.51 4 12c0-4.41 3.59-8 8-8s8 3.59 8 8-3.59 8-8 8z"/></svg>
                                    </div>
                                    <div class="taschen-mc-info">
                                        <strong>Zalo</strong>
                                        <span>Chat trực tiếp qua Zalo</span>
                                    </div>
                                    <span class="taschen-mc-arrow">→</span>
                                </a>
                            @endif

                            @if ($contactEmail)
                                <a class="taschen-modal-channel-card taschen-mc--email" href="{{ route('books.contact.click', [$book, 'email']) }}">
                                    <div class="taschen-mc-icon">
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                                    </div>
                                    <div class="taschen-mc-info">
                                        <strong>Email</strong>
                                        <span>Gửi thư qua Email</span>
                                    </div>
                                    <span class="taschen-mc-arrow">→</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>

        @if ($book->additional_information)
            <!-- Expandable Read More Section (TASCHEN READ MORE / LIRE DAVANTAGE) -->
            <div class="taschen-readmore-wrapper">
                <button type="button" class="taschen-readmore-toggle" id="readMoreToggleBtn" onclick="toggleReadMore()" aria-expanded="false">
                    <span id="readMoreLabel">Đọc thêm</span>
                    <span id="readMoreSymbol">+</span>
                </button>

                <div class="taschen-readmore-content" id="readMoreContent" style="display: none;">
                    <div class="taschen-readmore-inner">
                        <div class="taschen-readmore-grid">
                            @if (str_contains($book->additional_information, '<!-- split -->') || str_contains($book->additional_information, '---'))
                                @php
                                    $parts = preg_split('/<!--\s*split\s*-->|(?<=\n)---(?=\n)/', $book->additional_information, 2);
                                    $leftCol = trim($parts[0] ?? '');
                                    $rightCol = trim($parts[1] ?? '');
                                @endphp
                                <div class="taschen-readmore-col-main">
                                    {!! \Illuminate\Support\Str::contains($leftCol, ['<p>', '<h1', '<h2', '<h3', '<div', '<ul']) ? $leftCol : nl2br(e($leftCol)) !!}
                                </div>
                                <div class="taschen-readmore-col-side">
                                    {!! \Illuminate\Support\Str::contains($rightCol, ['<p>', '<h1', '<h2', '<h3', '<div', '<ul']) ? $rightCol : nl2br(e($rightCol)) !!}
                                </div>
                            @else
                                <div class="taschen-readmore-col-main">
                                    {!! \Illuminate\Support\Str::contains($book->additional_information, ['<p>', '<h1', '<h2', '<h3', '<div', '<ul']) ? $book->additional_information : nl2br(e($book->additional_information)) !!}
                                </div>
                                <div class="taschen-readmore-col-side">
                                    @if ($book->publisher)
                                        <h3>Giới thiệu về nhà xuất bản</h3>
                                        <p><strong>{{ $book->publisher }}</strong> là đơn vị xuất bản uy tín, đồng hành cùng các tác phẩm văn học nghệ thuật giá trị.</p>
                                    @endif

                                    <div class="taschen-readmore-quick-specs">
                                        <h3>{{ $book->title }}</h3>
                                        @if($book->author)<p>Tác giả: <strong>{{ $book->author }}</strong></p>@endif
                                        @if($book->dimensions)<p>Kích thước: {{ $book->dimensions }}</p>@endif
                                        @if($book->page_count)<p>Số trang: {{ $book->page_count }} trang</p>@endif
                                        @if($book->isbn)<p>ISBN: {{ $book->isbn }}</p>@endif
                                        @if($book->language)<p>Ngôn ngữ: {{ $book->language }}</p>@endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if ($galleryItems->isNotEmpty())
            <div class="taschen-lightbox" data-gallery-lightbox aria-hidden="true">
                <div class="taschen-lightbox-backdrop" data-lightbox-close></div>
                <div class="taschen-lightbox-content" role="dialog" aria-modal="true" aria-label="Phóng to hình ảnh ấn bản">
                    <button class="taschen-lightbox-close" type="button" aria-label="Đóng hình ảnh" data-lightbox-close>&times;</button>
                    @if ($galleryItems->count() > 1)
                        <button class="taschen-lightbox-arrow taschen-lightbox-arrow--prev" type="button" aria-label="Ảnh trước" data-lightbox-prev>‹</button>
                    @endif
                    <img class="taschen-lightbox-image" src="{{ $galleryItems->first()['url'] }}" alt="{{ $galleryItems->first()['alt'] }}" data-lightbox-image>
                    @if ($galleryItems->count() > 1)
                        <button class="taschen-lightbox-arrow taschen-lightbox-arrow--next" type="button" aria-label="Ảnh kế tiếp" data-lightbox-next>›</button>
                    @endif
                    <p class="taschen-lightbox-caption" data-lightbox-caption>{{ $galleryItems->first()['label'] }}</p>
                </div>
            </div>
        @endif

        <!-- Related / Suggested Books Section: YOU MAY BE INTERESTED IN -->
        @if ($relatedBooks->isNotEmpty())
            <section class="taschen-related" id="you-may-be-interested-in">
                <div class="taschen-section-header">
                    <div class="taschen-section-header-text">
                        <h2 class="taschen-section-title">YOU MAY BE INTERESTED IN</h2>
                    </div>
                </div>

                <div class="taschen-grid-4">
                    @foreach ($relatedBooks->take(4) as $relatedBook)
                        <x-book-card :book="$relatedBook" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const gallery = document.querySelector('[data-gallery]');
            const lightbox = document.querySelector('[data-gallery-lightbox]');
            if (!gallery || !lightbox) return;

            const galleryItems = @json($galleryItems);
            const mainImage = gallery.querySelector('[data-gallery-main]');
            const mainOpenButton = gallery.querySelector('[data-gallery-open]');
            const caption = gallery.querySelector('[data-gallery-caption]');
            const stagePrev = gallery.querySelector('[data-gallery-stage-prev]');
            const stageNext = gallery.querySelector('[data-gallery-stage-next]');
            const counterIndex = document.getElementById('galleryCurrentIndex');
            const lightboxImage = lightbox.querySelector('[data-lightbox-image]');
            const lightboxCaption = lightbox.querySelector('[data-lightbox-caption]');
            let activeIndex = 0;

            const setActiveImage = (index) => {
                if (!galleryItems.length) return;
                activeIndex = (index + galleryItems.length) % galleryItems.length;
                const item = galleryItems[activeIndex];
                if (item) {
                    mainImage.src = item.url;
                    mainImage.alt = item.alt;
                    if (caption) caption.textContent = item.label;
                    if (counterIndex) counterIndex.textContent = activeIndex + 1;
                    if (mainOpenButton) mainOpenButton.dataset.galleryOpen = activeIndex;
                }
            };

            stagePrev?.addEventListener('click', (e) => {
                e.stopPropagation();
                setActiveImage(activeIndex - 1);
            });

            stageNext?.addEventListener('click', (e) => {
                e.stopPropagation();
                setActiveImage(activeIndex + 1);
            });

            const openLightbox = (index) => {
                setActiveImage(Number(index) || 0);
                lightboxImage.src = mainImage.src;
                lightboxImage.alt = mainImage.alt;
                if (lightboxCaption && caption) lightboxCaption.textContent = caption.textContent;
                lightbox.classList.add('is-open');
                lightbox.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
            };

            const closeLightbox = () => {
                lightbox.classList.remove('is-open');
                lightbox.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
            };

            const moveLightbox = (direction) => {
                setActiveImage(activeIndex + direction);
                lightboxImage.src = mainImage.src;
                lightboxImage.alt = mainImage.alt;
                if (lightboxCaption && caption) lightboxCaption.textContent = caption.textContent;
            };

            mainOpenButton.addEventListener('click', () => openLightbox(mainOpenButton.dataset.galleryOpen));
            mainImage.addEventListener('click', () => openLightbox(mainOpenButton.dataset.galleryOpen));
            lightbox.querySelectorAll('[data-lightbox-close]').forEach((element) => element.addEventListener('click', closeLightbox));
            lightbox.querySelector('[data-lightbox-prev]')?.addEventListener('click', () => moveLightbox(-1));
            lightbox.querySelector('[data-lightbox-next]')?.addEventListener('click', () => moveLightbox(1));

            document.addEventListener('keydown', (event) => {
                if (!lightbox.classList.contains('is-open')) return;
                if (event.key === 'Escape') closeLightbox();
                if (event.key === 'ArrowLeft') moveLightbox(-1);
                if (event.key === 'ArrowRight') moveLightbox(1);
            });
        });

        function toggleReadMore() {
            const content = document.getElementById('readMoreContent');
            const symbol = document.getElementById('readMoreSymbol');
            const btn = document.getElementById('readMoreToggleBtn');
            if (!content || !symbol) return;

            const isHidden = content.style.display === 'none';
            if (isHidden) {
                content.style.display = 'block';
                symbol.textContent = '−';
                if (btn) {
                    btn.setAttribute('aria-expanded', 'true');
                    btn.classList.add('is-expanded');
                }
            } else {
                content.style.display = 'none';
                symbol.textContent = '+';
                if (btn) {
                    btn.setAttribute('aria-expanded', 'false');
                    btn.classList.remove('is-expanded');
                }
            }
        }

        function toggleContactModal(show) {
            const modal = document.getElementById('contactOnlineModal');
            if (!modal) return;
            if (show) {
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            } else {
                modal.style.display = 'none';
                document.body.style.overflow = '';
            }
        }

        @if ($errors->any())
            document.addEventListener('DOMContentLoaded', function() {
                toggleContactModal(true);
            });
        @endif
    </script>
@endpush
