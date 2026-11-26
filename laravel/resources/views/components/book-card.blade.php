@props(['book', 'isNew' => false, 'badge' => null])

@php
    $cardBadge = $badge ?? ($isNew ? 'MỚI' : null);
    $primaryCategory = $book->categories->first();
    $themes = [
        ['bg' => 'linear-gradient(145deg, #0b1528 0%, #1e293b 100%)', 'accent' => '#e2c275', 'border' => 'rgba(226, 194, 117, 0.45)'],
        ['bg' => 'linear-gradient(145deg, #062419 0%, #0f3d2b 100%)', 'accent' => '#e6caa1', 'border' => 'rgba(230, 202, 161, 0.45)'],
        ['bg' => 'linear-gradient(145deg, #270d14 0%, #431622 100%)', 'accent' => '#f3d9a2', 'border' => 'rgba(243, 217, 162, 0.45)'],
        ['bg' => 'linear-gradient(145deg, #18181b 0%, #2c2c34 100%)', 'accent' => '#d4af37', 'border' => 'rgba(212, 175, 55, 0.45)'],
        ['bg' => 'linear-gradient(145deg, #1b1308 0%, #3a2810 100%)', 'accent' => '#ffd27d', 'border' => 'rgba(255, 210, 125, 0.45)'],
    ];
    $theme = $themes[($book->id ?? 0) % count($themes)];
@endphp

<article class="product-tile taschen-card" data-book-id="{{ $book->id }}" data-testid="product-tile">
    <a class="product-tile__image-wrapper taschen-card-media" href="{{ route('books.show', $book) }}" aria-label="Xem chi tiết {{ $book->title }}">
        @if ($cardBadge)
            <div class="product-tile__label--container">
                <div class="label label__{{ $cardBadge === 'MỚI' || $cardBadge === 'NEW' ? 'New' : 'ComingSoon' }} taschen-card-badge {{ $cardBadge === 'MỚI' || $cardBadge === 'NEW' ? 'taschen-card-badge--red' : '' }}">
                    {{ $cardBadge }}
                </div>
            </div>
        @endif

        <div class="taschen-card-img-wrapper">
            @if ($book->cover_url)
                <div class="taschen-book-3d">
                    <img class="taschen-card-img" src="{{ $book->cover_url }}" alt="Bìa sách {{ $book->title }}" loading="lazy">
                </div>
            @else
                <div class="taschen-book-3d taschen-card-fallback" style="background: {{ $theme['bg'] }}; border: 1px solid {{ $theme['border'] }};">
                    <div class="taschen-fallback-inner" style="border: 1px solid {{ $theme['border'] }};">
                        <div class="taschen-fallback-top">
                            <span class="taschen-fallback-cat" style="color: {{ $theme['accent'] }};">
                                {{ strtoupper(\Illuminate\Support\Str::limit($primaryCategory?->name ?? 'TASCHEN', 16)) }}
                            </span>
                            <div class="taschen-fallback-ornament" style="color: {{ $theme['accent'] }};">
                                <svg width="20" height="12" viewBox="0 0 24 12" fill="currentColor"><circle cx="12" cy="6" r="3"/><line x1="0" y1="6" x2="7" y2="6" stroke="currentColor" stroke-width="1.5"/><line x1="17" y1="6" x2="24" y2="6" stroke="currentColor" stroke-width="1.5"/></svg>
                            </div>
                        </div>
                        <div class="taschen-fallback-center">
                            <strong class="taschen-fallback-title">{{ \Illuminate\Support\Str::limit($book->title, 48) }}</strong>
                        </div>
                        <div class="taschen-fallback-bottom">
                            @if ($book->author)
                                <small class="taschen-fallback-author">{{ \Illuminate\Support\Str::limit($book->author, 26) }}</small>
                            @else
                                <small class="taschen-fallback-author">Ấn bản tuyển chọn</small>
                            @endif
                            <span class="taschen-fallback-brand" style="color: {{ $theme['accent'] }};">KHO - CA</span>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </a>

    <div class="product-tile__info-wrapper taschen-card-body">
        <h3 class="product-tile__heading taschen-card-title" role="heading" aria-level="2">
            <a href="{{ route('books.show', $book) }}">
                <strong>{{ $book->title }}</strong>
                @if ($book->author)
                    <span class="taschen-card-author-name">{{ $book->author }}</span>
                @endif
            </a>
        </h3>

        <div class="product-tile__price-row taschen-card-footer">
            <a class="taschen-card-action" href="{{ route('books.show', $book) }}" aria-label="Xem chi tiết {{ $book->title }}">
                Xem chi tiết
            </a>
        </div>
    </div>
</article>
