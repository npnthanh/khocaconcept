@extends('layouts.app')

@section('title', ($pageData['title'] ?? 'Chính Sách Lưu Trữ & Bản Quyền') . ' · ' . \App\Models\Setting::get('site_name', 'KHO - CA'))
@section('meta_description', $pageData['meta'] ?? 'Chính sách lưu trữ, giám tuyển và bảo vệ quyền tác giả, sở hữu trí tuệ đối với các ấn phẩm sách và truyện nghệ thuật.')

@section('content')
    <div class="container" style="padding-top: 24px; padding-bottom: 80px;">
        <!-- BREADCRUMBS -->
        <nav class="breadcrumb" aria-label="Breadcrumb" style="margin-bottom: 30px;">
            <ol vocab="https://schema.org" typeof="BreadcrumbList" role="list" class="list">
                <li property="itemListElement" typeof="ListItem" class="item">
                    <a property="item" typeof="WebPage" title="Trang chủ" href="{{ route('home') }}">
                        <span property="name">Trang chủ</span>
                    </a>
                    <meta property="position" content="1">
                </li>
                <li property="itemListElement" typeof="ListItem" class="item">
                    <span property="name">{{ $pageData['title'] ?? 'Chính sách lưu trữ & Bản quyền' }}</span>
                    <meta property="position" content="2">
                </li>
            </ol>
        </nav>

        <!-- MAIN LEGAL CONTAINER -->
        <div class="taschen-legal-wrapper">
            <!-- Header Bar -->
            <header class="taschen-legal-header">
                @if(!empty($pageData['kicker']))
                    <p class="taschen-section-kicker">{{ $pageData['kicker'] }}</p>
                @endif
                <h1 class="taschen-legal-title">{{ $pageData['title'] ?? 'Chính Sách Lưu Trữ & Bản Quyền Tác Phẩm' }}</h1>
                <div class="taschen-legal-meta">
                    <span>Áp dụng cho toàn bộ không gian trưng bày</span> &middot; 
                    <span>Cập nhật: {{ date('m/Y') }}</span>
                </div>
            </header>

            <div class="taschen-legal-divider"></div>

            <!-- Content Sections -->
            <div class="taschen-legal-body">
                {!! $pageData['content'] ?? '' !!}
            </div>
        </div>
    </div>
@endsection
