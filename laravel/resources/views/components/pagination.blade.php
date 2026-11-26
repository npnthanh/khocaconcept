@props(['paginator'])
@if ($paginator->hasPages())
    @php
        $current = $paginator->currentPage();
        $last = $paginator->lastPage();
        $start = max(1, $current - 2);
        $end = min($last, $current + 2);
    @endphp
    <nav class="pagination" aria-label="Phân trang">
        @if ($paginator->onFirstPage()) <span class="disabled">‹</span>
        @else <a href="{{ $paginator->previousPageUrl() }}" aria-label="Trang trước">‹</a> @endif
        @if ($start > 1)<a href="{{ $paginator->url(1) }}">1</a>@if ($start > 2)<span>…</span>@endif @endif
        @for ($page = $start; $page <= $end; $page++)
            <a href="{{ $paginator->url($page) }}" @class(['current' => $page === $current])>{{ $page }}</a>
        @endfor
        @if ($end < $last)@if ($end < $last - 1)<span>…</span>@endif<a href="{{ $paginator->url($last) }}">{{ $last }}</a>@endif
        @if ($paginator->hasMorePages()) <a href="{{ $paginator->nextPageUrl() }}" aria-label="Trang sau">›</a>
        @else <span class="disabled">›</span> @endif
    </nav>
@endif
