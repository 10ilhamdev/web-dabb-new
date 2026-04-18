@extends('layouts.guest')

@section('title', $feature->name . ' — ' . config('app.name'))

@push('styles')
<link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
<link rel="stylesheet" href="{{ asset('css/feature-page.css') }}">
<link rel="stylesheet" href="{{ asset('css/virtual_slideshow.css') }}">
<style>
    /* Landing Page Grid Styles */
    .vss-landing-hero {
        background: linear-gradient(135deg, #0f172a 0%, #174E93 50%, #3b82f6 100%);
        padding: 4rem 1rem;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    .vss-landing-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }
    .vss-landing-hero .container {
        position: relative;
        z-index: 1;
        max-width: 1200px;
        margin: 0 auto;
    }
    .vss-landing-hero h1 {
        color: white;
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
    }
    .vss-landing-hero p {
        color: rgba(255,255,255,0.8);
        font-size: 1.1rem;
        max-width: 600px;
        margin: 0 auto;
    }
    .vss-landing-hero .hero-badge {
        display: inline-block;
        background: rgba(255,255,255,0.15);
        padding: 0.5rem 1rem;
        border-radius: 2rem;
        font-size: 0.85rem;
        color: rgba(255,255,255,0.9);
        margin-bottom: 1.5rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .vss-rooms-section {
        padding: 3rem 1rem;
        max-width: 1200px;
        margin: 0 auto;
    }
    .vss-section-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.5rem;
        text-align: center;
    }
    .vss-section-sub {
        color: #6b7280;
        text-align: center;
        margin-bottom: 2rem;
    }
    .vss-rooms-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.5rem;
    }
    .vss-room-card {
        background: white;
        border-radius: 1rem;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        transition: transform 0.2s, box-shadow 0.2s;
        cursor: pointer;
        text-decoration: none;
        color: inherit;
        display: block;
    }
    .vss-room-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }
    .vss-room-thumb {
        position: relative;
        height: 180px;
        background: #f3f4f6;
        overflow: hidden;
    }
    .vss-room-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .vss-room-thumb-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
    }
    .vss-enter-btn {
        position: absolute;
        inset: 0;
        background: rgba(23, 78, 147, 0.85);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.2s;
    }
    .vss-room-card:hover .vss-enter-btn {
        opacity: 1;
    }
    .vss-enter-btn span {
        color: white;
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        border: 2px solid white;
        border-radius: 2rem;
    }
    .vss-room-badge {
        position: absolute;
        bottom: 0.75rem;
        right: 0.75rem;
        background: rgba(0,0,0,0.7);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 1rem;
        font-size: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    .vss-room-info {
        padding: 1rem;
    }
    .vss-room-name {
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.25rem;
    }
    .vss-room-desc {
        font-size: 0.875rem;
        color: #6b7280;
    }
    .vss-room-meta {
        font-size: 0.75rem;
        color: #9ca3af;
        margin-top: 0.5rem;
    }
</style>
@endpush

@section('content')

@php
    $locale = app()->getLocale();
@endphp

{{-- Breadcrumb --}}
<div class="feature-breadcrumb">
    <div class="container">
        @if($feature->parent)
            <a href="{{ url($feature->parent->path ?? '#') }}">
                {{ app()->getLocale() === 'en' && $feature->parent->name_en ? $feature->parent->name_en : $feature->parent->name }}
            </a>
            <span class="sep">/</span>
        @endif
        <span class="current">{{ app()->getLocale() === 'en' && $feature->name_en ? $feature->name_en : $feature->name }}</span>
    </div>
</div>

{{-- Hero --}}
<div class="vss-landing-hero">
    <div class="container">
        <div class="hero-badge">
            {{ app()->getLocale() === 'en' ? 'Virtual Archive Exhibition' : 'Pameran Arsip Virtual' }}
        </div>
        <h1>{{ app()->getLocale() === 'en' && $feature->name_en ? $feature->name_en : $feature->name }}</h1>
        <p>{{ __('home.virtual_slideshow.hero_desc') }}</p>
    </div>
</div>

{{-- Rooms Grid --}}
<section class="vss-rooms-section">
    <h2 class="vss-section-title">{{ __('home.virtual_slideshow.select_exhibition') }}</h2>
    <p class="vss-section-sub">{{ __('home.virtual_slideshow.select_exhibition_desc') }}</p>

    @if($pages->isEmpty())
        <div style="text-align:center;padding:4rem;color:#9ca3af;">
            <svg style="width:64px;height:64px;margin:0 auto 1rem;display:block;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            <p>{{ __('home.virtual_slideshow.no_exhibitions') }}</p>
        </div>
    @else
        <div class="vss-rooms-grid">
            @foreach($pages as $page)
            <a href="{{ url($feature->path) }}?page={{ $page->order }}" class="vss-room-card">
                <div class="vss-room-thumb">
                    @php
                        $pageSlides = $page->slideshowSlides ?? collect();
                        $firstSlide = $pageSlides->sortBy('order')->first();

                        $thumbUrl = null;
                        if ($page->thumbnail_path) {
                            $thumbUrl = asset('storage/'.$page->thumbnail_path);
                        } elseif ($firstSlide) {
                            // Check uploaded images first
                            if ($firstSlide->images && count($firstSlide->images) > 0) {
                                $thumbUrl = asset('storage/'.$firstSlide->images[0]);
                            }
                            // Then check URL images
                            elseif ($firstSlide->image_urls && count($firstSlide->image_urls) > 0) {
                                $firstUrl = $firstSlide->image_urls[0];
                                // Convert Google Drive URL if needed
                                if (strpos($firstUrl, 'drive.google.com') !== false) {
                                    // Try various patterns
                                    $patterns = [
                                        '/\/file\/d\/([a-zA-Z0-9_-]+)/',
                                        '/id=([a-zA-Z0-9_-]+)/',
                                        '/\/open\?id=([a-zA-Z0-9_-]+)/'
                                    ];
                                    $converted = false;
                                    foreach ($patterns as $pattern) {
                                        if (preg_match($pattern, $firstUrl, $m)) {
                                            // Use lh3.googleusercontent.com format which supports CORS
                                            $thumbUrl = 'https://lh3.googleusercontent.com/d/' . $m[1];
                                            $converted = true;
                                            break;
                                        }
                                    }
                                    if (!$converted) {
                                        $thumbUrl = $firstUrl;
                                    }
                                } elseif (preg_match('/commons\.wikimedia\.org\/wiki\/File:(.+)/', $firstUrl, $m)) {
                                    $thumbUrl = 'https://commons.wikimedia.org/wiki/Special:FilePath/' . $m[1];
                                } else {
                                    $thumbUrl = $firstUrl;
                                }
                            }
                        }
                    @endphp
                    @if($thumbUrl)
                        <img src="{{ $thumbUrl }}" alt="{{ $page->title }}" loading="lazy" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="vss-room-thumb-placeholder" style="display:none;">🎞</div>
                    @else
                        <div class="vss-room-thumb-placeholder">🎞</div>
                    @endif
                    <div class="vss-enter-btn"><span>{{ app()->getLocale() === 'en' ? 'View' : 'Lihat' }}</span></div>
                    <div class="vss-room-badge">
                        <svg style="width:12px;height:12px;" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                        </svg>
                        {{ $pageSlides->count() }} {{ app()->getLocale() === 'en' ? 'slides' : 'slide' }}
                    </div>
                </div>
                <div class="vss-room-info">
                    <h3 class="vss-room-name">{{ $locale === 'en' && $page->title_en ? $page->title_en : $page->title }}</h3>
                    @if($page->description)
                        <p class="vss-room-desc">{{ Str::limit(strip_tags($page->description), 100) }}</p>
                    @endif
                </div>
            </a>
            @endforeach
        </div>
    @endif
</section>

{{-- Login Modal (if guest) --}}
@if(isset($requiresLoginModal) && $requiresLoginModal)
    @include('partials.login_modal', [
        'previewImage' => $loginModalPreview ?? null,
        'roomName'     => $loginModalRoomName ?? null
    ])
@endif

@endsection
