@extends('layouts.app')

@section('title', ($collection->meta_title ?: $collection->title).' · '.\App\Models\Setting::get('site_name', 'KHO - CA'))
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($collection->meta_description ?: $collection->description ?: $collection->title), 160))
@section('meta_og_image', $collection->og_image_url)

@section('content')
    <div class="container collection-page">
        <nav class="taschen-breadcrumb" aria-label="Breadcrumb"><div class="taschen-breadcrumb-inner"><a href="{{ route('home') }}">Trang chủ</a><span class="sep">|</span><span class="current">{{ $collection->title }}</span></div></nav>
        <header class="collection-hero">
            @if ($collection->cover_url)<img src="{{ $collection->cover_url }}" alt="{{ $collection->title }}">@endif
            <div class="collection-hero__content"><p class="taschen-section-kicker">BỘ SƯU TẬP TUYỂN CHỌN</p><h1>{{ $collection->title }}</h1>@if($collection->subtitle)<p class="collection-hero__subtitle">{{ $collection->subtitle }}</p>@endif @if($collection->description)<div class="collection-hero__description">{!! nl2br(e($collection->description)) !!}</div>@endif</div>
        </header>
        <section class="taschen-section collection-books"><div class="taschen-grid-4">@forelse($collection->books as $book)<x-book-card :book="$book" :is-new="false" />@empty<div class="collection-empty">Bộ sưu tập này chưa có sách.</div>@endforelse</div></section>
    </div>
@endsection
