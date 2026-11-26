@props(['gift'])

@php
    $themes = [
        ['bg' => 'linear-gradient(145deg, #301329 0%, #7c2d5d 100%)', 'accent' => '#ffd4e8', 'border' => 'rgba(255, 212, 232, 0.45)'],
        ['bg' => 'linear-gradient(145deg, #162b24 0%, #27765b 100%)', 'accent' => '#d8f6e8', 'border' => 'rgba(216, 246, 232, 0.45)'],
        ['bg' => 'linear-gradient(145deg, #3a2510 0%, #9b6b27 100%)', 'accent' => '#ffe6af', 'border' => 'rgba(255, 230, 175, 0.45)'],
    ];
    $theme = $themes[($gift->id ?? 0) % count($themes)];
@endphp

<article class="product-tile taschen-card" data-gift-id="{{ $gift->id }}" data-testid="gift-tile">
    <a class="product-tile__image-wrapper taschen-card-media" href="{{ route('gifts.show', $gift) }}" aria-label="Xem chi tiết {{ $gift->title }}">
        <div class="taschen-card-img-wrapper">
            @if ($gift->cover_url)
                <div class="taschen-book-3d">
                    <img class="taschen-card-img" src="{{ $gift->cover_url }}" alt="{{ $gift->title }}" loading="lazy">
                </div>
            @else
                <div class="taschen-book-3d taschen-card-fallback" style="background: {{ $theme['bg'] }}; border: 1px solid {{ $theme['border'] }};">
                    <div class="taschen-fallback-inner" style="border: 1px solid {{ $theme['border'] }};">
                        <div class="taschen-fallback-top">
                            <span class="taschen-fallback-cat" style="color: {{ $theme['accent'] }};">GIFTS</span>
                        </div>
                        <div class="taschen-fallback-center">
                            <strong class="taschen-fallback-title">{{ \Illuminate\Support\Str::limit($gift->title, 48) }}</strong>
                        </div>
                        <div class="taschen-fallback-bottom">
                            <small class="taschen-fallback-author">{{ $gift->brand ?: 'Sản phẩm tuyển chọn' }}</small>
                            <span class="taschen-fallback-brand" style="color: {{ $theme['accent'] }};">KHO - CA</span>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </a>

    <div class="product-tile__info-wrapper taschen-card-body">
        <h3 class="product-tile__heading taschen-card-title" role="heading" aria-level="2">
            <a href="{{ route('gifts.show', $gift) }}">
                <strong>{{ $gift->title }}</strong>
                @if ($gift->brand)
                    <span class="taschen-card-author-name">{{ $gift->brand }}</span>
                @endif
            </a>
        </h3>
        <div class="product-tile__price-row taschen-card-footer">
            <a class="taschen-card-action" href="{{ route('gifts.show', $gift) }}" aria-label="Xem chi tiết {{ $gift->title }}">
                Xem chi tiết
            </a>
        </div>
    </div>
</article>
