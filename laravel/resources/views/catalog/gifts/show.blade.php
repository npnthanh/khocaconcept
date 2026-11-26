@extends('layouts.app')

@section('title', ($gift->meta_title ?: $gift->title).' · '.\App\Models\Setting::get('site_name', 'KHO - CA'))
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($gift->meta_description ?: $gift->summary ?: $gift->title), 160))
@section('meta_og_image', $gift->og_image_url)

@section('content')
    @php
        $contactEmail = \App\Models\Setting::get('contact_email');
        $contactZalo = \App\Models\Setting::get('contact_zalo');
        $contactFacebook = \App\Models\Setting::get('contact_facebook') ?: \App\Models\Setting::get('contact_messenger');
        $zaloHref = $contactZalo
            ? (\Illuminate\Support\Str::startsWith($contactZalo, ['http://', 'https://']) ? $contactZalo : 'https://zalo.me/'.preg_replace('/\D+/', '', $contactZalo))
            : null;
        $fbHref = $contactFacebook
            ? (\Illuminate\Support\Str::startsWith($contactFacebook, ['http://', 'https://']) ? $contactFacebook : 'https://facebook.com/'.$contactFacebook)
            : null;
        $galleryItems = collect();
        if ($gift->cover_url) {
            $galleryItems->push(['url' => $gift->cover_url, 'alt' => 'Hình ảnh '.$gift->title, 'label' => 'Ảnh sản phẩm']);
        }
        foreach ($gift->galleryImages as $galleryImage) {
            $galleryItems->push(['url' => $galleryImage->url, 'alt' => $galleryImage->alt_text ?: 'Hình ảnh '.$gift->title, 'label' => 'Ảnh gallery']);
        }
        $themes = [
            ['bg' => 'linear-gradient(145deg, #301329 0%, #7c2d5d 100%)', 'accent' => '#ffd4e8', 'border' => 'rgba(255, 212, 232, 0.45)'],
            ['bg' => 'linear-gradient(145deg, #162b24 0%, #27765b 100%)', 'accent' => '#d8f6e8', 'border' => 'rgba(216, 246, 232, 0.45)'],
            ['bg' => 'linear-gradient(145deg, #3a2510 0%, #9b6b27 100%)', 'accent' => '#ffe6af', 'border' => 'rgba(255, 230, 175, 0.45)'],
        ];
        $theme = $themes[($gift->id ?? 0) % count($themes)];
    @endphp

    <div class="container taschen-detail">
        <nav class="taschen-breadcrumb" aria-label="Breadcrumb" style="margin-bottom: 24px !important; padding-top: 0 !important;">
            <div class="taschen-breadcrumb-inner">
                <a href="{{ route('home') }}">Trang chủ</a>
                <span class="sep">|</span>
                <a href="{{ route('gifts.index') }}">GIFTS</a>
                <span class="sep">|</span>
                <span class="current">{{ $gift->title }}</span>
            </div>
        </nav>

        <div class="taschen-detail-grid">
            <div class="taschen-detail-media taschen-gallery-media">
                @if ($galleryItems->isNotEmpty())
                    <div class="taschen-gallery" data-gallery>
                        <figure class="taschen-gallery-main">
                            @if ($galleryItems->count() > 1)
                                <button class="taschen-stage-arrow taschen-stage-prev" type="button" aria-label="Ảnh trước" data-gallery-stage-prev>‹</button>
                                <button class="taschen-stage-arrow taschen-stage-next" type="button" aria-label="Ảnh tiếp theo" data-gallery-stage-next>›</button>
                            @endif
                            <div class="taschen-detail-stage">
                                <div class="taschen-detail-book-3d" id="detailGiftWrapper">
                                    <img class="taschen-detail-img" src="{{ $galleryItems->first()['url'] }}" alt="{{ $galleryItems->first()['alt'] }}" data-gallery-main>
                                </div>
                            </div>
                            <button class="taschen-gallery-expand" type="button" aria-label="Phóng to hình ảnh" data-gallery-open="0">↗</button>
                            <figcaption data-gallery-caption style="display: none;">{{ $galleryItems->first()['label'] }}</figcaption>
                            @if ($galleryItems->count() > 1)
                                <div class="taschen-stage-counter"><span id="galleryCurrentIndex">1</span> / {{ $galleryItems->count() }}</div>
                            @endif
                        </figure>
                    </div>
                @else
                    <div class="taschen-detail-stage">
                        <div class="taschen-detail-book-3d taschen-card-fallback taschen-detail-fallback" style="width: 320px; height: 460px; background: {{ $theme['bg'] }}; border: 1px solid {{ $theme['border'] }};">
                            <div class="taschen-fallback-inner" style="border: 1px solid {{ $theme['border'] }}; padding: 28px 22px;">
                                <div class="taschen-fallback-top"><span class="taschen-fallback-cat" style="color: {{ $theme['accent'] }}; font-size: 11px;">GIFTS</span></div>
                                <div class="taschen-fallback-center"><strong class="taschen-fallback-title" style="font-size: 20px; line-height: 1.4;">{{ $gift->title }}</strong></div>
                                <div class="taschen-fallback-bottom">
                                    <small class="taschen-fallback-author" style="font-size: 13px;">{{ $gift->brand ?: 'Sản phẩm tuyển chọn' }}</small>
                                    <span class="taschen-fallback-brand" style="color: {{ $theme['accent'] }}; font-size: 11px;">KHO - CA · GIFTS</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="taschen-detail-info">
                <div class="taschen-detail-series"><a href="{{ route('gifts.index') }}" style="border-bottom: 1px solid var(--t-black);">GIFTS</a></div>
                <h1 class="taschen-detail-title">{{ $gift->title }}</h1>
                <div class="taschen-detail-author">Thương hiệu: <strong>{{ $gift->brand ?: 'Đang cập nhật thương hiệu' }}</strong></div>

                @if ($gift->product_type)
                    <div class="taschen-detail-author">Loại sản phẩm: <strong>{{ $gift->product_type }}</strong></div>
                @endif

                @if ($gift->summary)
                    <div class="taschen-detail-summary">{!! nl2br(e($gift->summary)) !!}</div>
                @endif

                @if ($gift->dimensions || $gift->sku)
                    <div class="taschen-specs-table">
                        @if ($gift->dimensions)<div class="taschen-specs-row"><span class="taschen-specs-label">Quy cách / Kích thước</span><span class="taschen-specs-value">{{ $gift->dimensions }}</span></div>@endif
                        @if ($gift->sku)<div class="taschen-specs-row"><span class="taschen-specs-label">Mã sản phẩm</span><span class="taschen-specs-value">{{ $gift->sku }}</span></div>@endif
                    </div>
                @endif

                <div class="taschen-contact-action-wrap">
                    <button type="button" class="taschen-btn-online-contact" onclick="toggleContactModal(true)">
                        <span>Liên hệ mua sản phẩm</span>
                    </button>
                </div>

                <div class="taschen-contact-modal" id="contactOnlineModal" style="display: none;" onclick="if(event.target === this) toggleContactModal(false)">
                    <div class="taschen-contact-modal-dialog">
                        <button type="button" class="taschen-contact-modal-close" onclick="toggleContactModal(false)" aria-label="Đóng">&times;</button>
                        <div class="taschen-contact-modal-header">
                            <h3 class="taschen-contact-modal-title">Liên hệ mua sản phẩm</h3>
                            <p class="taschen-contact-modal-book">Chọn phương thức trao đổi về: <strong>{{ $gift->title }}</strong></p>
                        </div>
                        <div class="taschen-modal-channels-grid">
                            @if ($fbHref)<a class="taschen-modal-channel-card taschen-mc--fb" href="{{ route('gifts.contact.click', [$gift, 'facebook']) }}" target="_blank" rel="noopener"><div class="taschen-mc-info"><strong>Facebook</strong><span>Nhắn tin trao đổi qua Facebook</span></div><span class="taschen-mc-arrow">→</span></a>@endif
                            @if ($zaloHref)<a class="taschen-modal-channel-card taschen-mc--zalo" href="{{ route('gifts.contact.click', [$gift, 'zalo']) }}" target="_blank" rel="noopener"><div class="taschen-mc-info"><strong>Zalo</strong><span>Chat trực tiếp qua Zalo</span></div><span class="taschen-mc-arrow">→</span></a>@endif
                            @if ($contactEmail)<a class="taschen-modal-channel-card taschen-mc--email" href="{{ route('gifts.contact.click', [$gift, 'email']) }}"><div class="taschen-mc-info"><strong>Email</strong><span>Gửi thư qua Email</span></div><span class="taschen-mc-arrow">→</span></a>@endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($gift->additional_information)
            <div class="taschen-readmore-wrapper">
                <button type="button" class="taschen-readmore-toggle" id="readMoreToggleBtn" onclick="toggleReadMore()" aria-expanded="false"><span id="readMoreLabel">Đọc thêm</span><span id="readMoreSymbol">+</span></button>
                <div class="taschen-readmore-content" id="readMoreContent" style="display: none;"><div class="taschen-readmore-inner"><div class="taschen-readmore-grid"><div class="taschen-readmore-col-main">{!! \Illuminate\Support\Str::contains($gift->additional_information, ['<p>', '<h1', '<h2', '<h3', '<div', '<ul']) ? $gift->additional_information : nl2br(e($gift->additional_information)) !!}</div><div class="taschen-readmore-col-side"><h3>Thông tin sản phẩm</h3><div class="taschen-readmore-quick-specs"><h3>{{ $gift->title }}</h3>@if($gift->brand)<p>Thương hiệu: <strong>{{ $gift->brand }}</strong></p>@endif @if($gift->product_type)<p>Loại sản phẩm: {{ $gift->product_type }}</p>@endif @if($gift->dimensions)<p>Quy cách: {{ $gift->dimensions }}</p>@endif @if($gift->sku)<p>Mã sản phẩm: {{ $gift->sku }}</p>@endif</div></div></div></div></div>
            </div>
        @endif

        @if ($galleryItems->isNotEmpty())
            <div class="taschen-lightbox" data-gallery-lightbox aria-hidden="true"><div class="taschen-lightbox-backdrop" data-lightbox-close></div><div class="taschen-lightbox-content" role="dialog" aria-modal="true" aria-label="Phóng to hình ảnh sản phẩm"><button class="taschen-lightbox-close" type="button" aria-label="Đóng hình ảnh" data-lightbox-close>&times;</button>@if ($galleryItems->count() > 1)<button class="taschen-lightbox-arrow taschen-lightbox-arrow--prev" type="button" aria-label="Ảnh trước" data-lightbox-prev>‹</button>@endif<img class="taschen-lightbox-image" src="{{ $galleryItems->first()['url'] }}" alt="{{ $galleryItems->first()['alt'] }}" data-lightbox-image>@if ($galleryItems->count() > 1)<button class="taschen-lightbox-arrow taschen-lightbox-arrow--next" type="button" aria-label="Ảnh kế tiếp" data-lightbox-next>›</button>@endif<p class="taschen-lightbox-caption" data-lightbox-caption>{{ $galleryItems->first()['label'] }}</p></div></div>
        @endif

        @if ($relatedGifts->isNotEmpty())
            <section class="taschen-related" id="related-gifts"><div class="taschen-section-header"><div class="taschen-section-header-text"><h2 class="taschen-section-title">CÓ THỂ BẠN CŨNG QUAN TÂM</h2></div></div><div class="taschen-grid-4">@foreach ($relatedGifts as $relatedGift)<x-gift-card :gift="$relatedGift" />@endforeach</div></section>
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
            const openButton = gallery.querySelector('[data-gallery-open]');
            const caption = gallery.querySelector('[data-gallery-caption]');
            const counter = document.getElementById('galleryCurrentIndex');
            const lightboxImage = lightbox.querySelector('[data-lightbox-image]');
            const lightboxCaption = lightbox.querySelector('[data-lightbox-caption]');
            let activeIndex = 0;
            const setImage = (index) => {
                activeIndex = (index + galleryItems.length) % galleryItems.length;
                const item = galleryItems[activeIndex];
                mainImage.src = item.url; mainImage.alt = item.alt;
                if (caption) caption.textContent = item.label;
                if (counter) counter.textContent = activeIndex + 1;
                if (openButton) openButton.dataset.galleryOpen = activeIndex;
            };
            gallery.querySelector('[data-gallery-stage-prev]')?.addEventListener('click', () => setImage(activeIndex - 1));
            gallery.querySelector('[data-gallery-stage-next]')?.addEventListener('click', () => setImage(activeIndex + 1));
            const open = () => { lightboxImage.src = mainImage.src; lightboxImage.alt = mainImage.alt; lightboxCaption.textContent = caption?.textContent || ''; lightbox.classList.add('is-open'); lightbox.setAttribute('aria-hidden', 'false'); document.body.style.overflow = 'hidden'; };
            const close = () => { lightbox.classList.remove('is-open'); lightbox.setAttribute('aria-hidden', 'true'); document.body.style.overflow = ''; };
            openButton?.addEventListener('click', open); mainImage.addEventListener('click', open);
            lightbox.querySelectorAll('[data-lightbox-close]').forEach((el) => el.addEventListener('click', close));
            lightbox.querySelector('[data-lightbox-prev]')?.addEventListener('click', () => { setImage(activeIndex - 1); open(); });
            lightbox.querySelector('[data-lightbox-next]')?.addEventListener('click', () => { setImage(activeIndex + 1); open(); });
            document.addEventListener('keydown', (event) => { if (!lightbox.classList.contains('is-open')) return; if (event.key === 'Escape') close(); if (event.key === 'ArrowLeft') setImage(activeIndex - 1); if (event.key === 'ArrowRight') setImage(activeIndex + 1); });
        });
        function toggleReadMore() { const content = document.getElementById('readMoreContent'), symbol = document.getElementById('readMoreSymbol'), btn = document.getElementById('readMoreToggleBtn'); if (!content) return; const hidden = content.style.display === 'none'; content.style.display = hidden ? 'block' : 'none'; symbol.textContent = hidden ? '−' : '+'; btn?.setAttribute('aria-expanded', hidden ? 'true' : 'false'); }
        function toggleContactModal(show) { const modal = document.getElementById('contactOnlineModal'); if (!modal) return; modal.style.display = show ? 'flex' : 'none'; document.body.style.overflow = show ? 'hidden' : ''; }
    </script>
@endpush
