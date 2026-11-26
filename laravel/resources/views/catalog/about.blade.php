@extends('layouts.app')

@section('title', 'Về Chúng Tôi · Không Gian Trưng Bày Sách Nghệ Thuật · '.\App\Models\Setting::get('site_name', 'KHO - CA'))
@section('meta_description', 'Khám phá câu chuyện, tôn chỉ và sứ mệnh tôn vinh nghệ thuật xuất bản, các ấn bản sách và truyện tranh đặc sắc tại '.\App\Models\Setting::get('site_name', 'KHO - CA').'.')

@section('content')
    <!-- ABOUT HERO BANNER (BANNER 1: ĐẦU TRANG) -->
    <section class="taschen-about-hero" aria-label="Giới thiệu không gian trưng bày">
        <div class="taschen-about-hero-bg">
            <picture>
                @if (!empty($aboutData['about_hero_image_mobile']))
                    <source media="(max-width: 768px)" srcset="{{ $aboutData['about_hero_image_mobile'] }}">
                @endif
                <img src="{{ $aboutData['about_hero_image'] }}" alt="{{ $aboutData['about_hero_title'] }}" loading="eager">
            </picture>
        </div>
        <div class="container" style="position: relative; z-index: 3;">
            <div class="taschen-about-hero-content">
                @if (!empty($aboutData['about_hero_badge']))
                    <span class="taschen-about-hero-badge">{{ $aboutData['about_hero_badge'] }}</span>
                @endif
                <h1 class="taschen-about-hero-title">{{ $aboutData['about_hero_title'] }}</h1>
                @if (!empty($aboutData['about_hero_quote']))
                    <p class="taschen-about-hero-quote">
                        {{ $aboutData['about_hero_quote'] }}
                    </p>
                @endif
            </div>
        </div>
    </section>

    <!-- EDITORIAL NARRATIVE & MANIFESTO -->
    <section class="taschen-section taschen-about-story">
        <div class="container">
            <div class="taschen-about-grid">
                <div class="taschen-about-col-left">
                    @if (!empty($aboutData['about_story_kicker']))
                        <p class="taschen-section-kicker">{{ $aboutData['about_story_kicker'] }}</p>
                    @endif
                    @if (!empty($aboutData['about_story_heading']))
                        <h2 class="taschen-about-heading">{{ $aboutData['about_story_heading'] }}</h2>
                    @endif
                    
                    @if (!empty($aboutData['about_story_quote']))
                        <div class="taschen-about-quote-box">
                            <p>{{ $aboutData['about_story_quote'] }}</p>
                            @if (!empty($aboutData['about_story_quote_author']))
                                <span>{{ $aboutData['about_story_quote_author'] }}</span>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="taschen-about-col-right">
                    @if (!empty($aboutData['about_story_lead']))
                        <p class="taschen-about-lead">
                            {!! nl2br(e($aboutData['about_story_lead'])) !!}
                        </p>
                    @endif
                    @if (!empty($aboutData['about_story_p1']))
                        <p class="taschen-about-text">
                            {!! nl2br(e($aboutData['about_story_p1'])) !!}
                        </p>
                    @endif
                    @if (!empty($aboutData['about_story_p2']))
                        <p class="taschen-about-text">
                            {!! nl2br(e($aboutData['about_story_p2'])) !!}
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- SPLIT 50/50 VISUAL SHOWCASE (BANNER 2: CUỐI TRANG / SƯU TẦM & THƯỞNG LÃM) -->
    <section class="taschen-split-section" style="margin: 40px 0 60px 0;">
        <div class="taschen-split-image">
            <picture>
                @if (!empty($aboutData['about_split_image_mobile']))
                    <source media="(max-width: 768px)" srcset="{{ $aboutData['about_split_image_mobile'] }}">
                @endif
                <img src="{{ $aboutData['about_split_image'] }}" alt="{{ $aboutData['about_split_title'] }}" loading="lazy">
            </picture>
        </div>
        <div class="taschen-split-content">
            @if (!empty($aboutData['about_split_kicker']))
                <p class="taschen-split-kicker">{{ $aboutData['about_split_kicker'] }}</p>
            @endif
            <h2 class="taschen-split-title">{{ $aboutData['about_split_title'] }}</h2>
            @if (!empty($aboutData['about_split_quote']))
                <blockquote class="taschen-split-quote">
                    {{ $aboutData['about_split_quote'] }}
                </blockquote>
            @endif
            @if (!empty($aboutData['about_split_text']))
                <p style="color: rgba(255, 255, 255, 0.8); font-size: 14.5px; line-height: 1.7; margin-bottom: 28px;">
                    {!! nl2br(e($aboutData['about_split_text'])) !!}
                </p>
            @endif
            @if (!empty($aboutData['about_split_btn_text']))
                <a class="taschen-btn" href="{{ $aboutData['about_split_btn_link'] ?: route('books.index') }}">
                    <span>{{ $aboutData['about_split_btn_text'] }}</span>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                </a>
            @endif
        </div>
    </section>

    <!-- EXHIBITION STATS COUNTER -->
    <section class="taschen-about-stats">
        <div class="container">
            <div class="taschen-stats-grid">
                <div class="taschen-stat-item">
                    <span class="taschen-stat-value">{{ $totalBooks }}+</span>
                    <span class="taschen-stat-label">Ấn Phẩm Tuyển Chọn</span>
                </div>
                <div class="taschen-stat-item">
                    <span class="taschen-stat-value">{{ $totalCategories }}</span>
                    <span class="taschen-stat-label">Chủ Đề Trưng Bày</span>
                </div>
                <div class="taschen-stat-item">
                    <span class="taschen-stat-value">2019</span>
                    <span class="taschen-stat-label">Năm Thành Lập</span>
                </div>
                <div class="taschen-stat-item">
                    <span class="taschen-stat-value">24/7</span>
                    <span class="taschen-stat-label">Không Gian Mở Trực Tuyến</span>
                </div>
            </div>
        </div>
    </section>

    <!-- FEATURED CURATED SELECTION -->
    @if ($featuredBooks->isNotEmpty())
        <section class="taschen-section" style="padding-top: 60px;">
            <div class="container">
                <div class="taschen-section-header">
                    <div class="taschen-section-header-text">
                        <h2 class="taschen-section-title">CURATED HIGHLIGHTS</h2>
                    </div>
                </div>

                <div class="taschen-grid-4">
                    @foreach ($featuredBooks as $book)
                        <x-book-card :book="$book" />
                    @endforeach
                </div>

                <div class="taschen-section-footer">
                    <a class="taschen-btn-outline-center" href="{{ route('books.index') }}">
                        Xem tất cả ấn bản
                    </a>
                </div>
            </div>
        </section>
    @endif

@endsection
