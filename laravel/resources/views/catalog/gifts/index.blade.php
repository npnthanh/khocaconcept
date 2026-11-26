@extends('layouts.app')

@section('title', 'GIFTS · '.\App\Models\Setting::get('site_name', 'KHO - CA'))
@section('meta_description', 'Khám phá các sản phẩm quà tặng, dụng cụ sáng tạo và phụ kiện nghệ thuật được tuyển chọn.')

@section('content')
    <div class="container" style="padding-top: 0; padding-bottom: 80px;">
        <nav class="taschen-breadcrumb" aria-label="Breadcrumb">
            <div class="taschen-breadcrumb-inner">
                <a href="{{ route('home') }}">Trang chủ</a>
                <span class="sep">|</span>
                <span class="current">GIFTS <span class="count">({{ $gifts->total() }} sản phẩm)</span></span>
            </div>
        </nav>

        <section class="product-list">
            <div class="product-list__actions">
                <div>
                    <p style="font-family: var(--font-sans, 'Inter'); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 2px; color: var(--text-secondary, #666); margin-bottom: 6px;">QUÀ TẶNG · PHỤ KIỆN · SẢN PHẨM SÁNG TẠO</p>
                    <h1 class="product-list__wrapper--title">Tất cả sản phẩm</h1>
                    @if ($term)
                        <p style="font-size: 14px; color: var(--text-secondary, #666); margin-top: 4px;">Kết quả tìm kiếm cho: “<strong>{{ $term }}</strong>”</p>
                    @endif
                </div>
                <form method="get" action="{{ route('gifts.index') }}" style="display: inline-flex; align-items: center; gap: 8px;">
                    @if ($term)<input type="hidden" name="q" value="{{ $term }}">@endif
                    <label for="giftSortSelect" style="font-size: 13px; color: var(--text-secondary, #666); font-weight: 500;">Sắp xếp:</label>
                    <select id="giftSortSelect" name="sort" onchange="this.form.submit()" style="padding: 7px 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px; background: #fff; cursor: pointer; color: #111;">
                        <option value="" @selected(!$sort)>Tên sản phẩm A - Z</option>
                        <option value="newest" @selected($sort === 'newest')>Mới thêm</option>
                    </select>
                </form>
            </div>

            @if ($gifts->isNotEmpty())
                <div class="taschen-grid-4">
                    @foreach ($gifts as $gift)
                        <x-gift-card :gift="$gift" />
                    @endforeach
                </div>
                <x-pagination :paginator="$gifts" />
            @else
                <div class="taschen-no-results" style="padding: 80px 0; text-align: center;">
                    <p style="font-family: var(--font-serif, serif); font-size: 32px; font-style: italic; margin-bottom: 12px; color: #111;">Chưa có sản phẩm Gift phù hợp</p>
                    <p style="color: #666;">Hãy thử từ khóa khác hoặc quay lại sau.</p>
                </div>
            @endif
        </section>
    </div>
@endsection
