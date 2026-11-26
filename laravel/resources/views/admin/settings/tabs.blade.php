@php
    $settingsTab = match (true) {
        request()->routeIs('admin.explore.*') => 'explore',
        request()->routeIs('admin.banners.*') => 'banners',
        request()->routeIs('admin.about.*') => 'about',
        default => 'general',
    };
@endphp

<nav class="admin-settings-tabs" aria-label="Các nhóm cấu hình website">
    <a href="{{ route('admin.settings.edit') }}" @class(['active' => $settingsTab === 'general'])>Cấu hình chung</a>
    <a href="{{ route('admin.explore.index') }}" @class(['active' => $settingsTab === 'explore'])>More to Explore</a>
    <a href="{{ route('admin.banners.index') }}" @class(['active' => $settingsTab === 'banners'])>Slider &amp; Banner</a>
    <a href="{{ route('admin.about.edit') }}" @class(['active' => $settingsTab === 'about'])>Trang giới thiệu</a>
</nav>
