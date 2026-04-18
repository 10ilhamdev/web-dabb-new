@extends('layouts.guest')

@section('title', ($locale === 'en' && $selectedPage->title_en ? $selectedPage->title_en : $selectedPage->title) . ' — ' . $feature->name . ' — ' . config('app.name'))

@push('styles')
<link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
<link rel="stylesheet" href="{{ asset('css/feature-page.css') }}">
<link rel="stylesheet" href="{{ asset('css/virtual_slideshow.css') }}">
<style>
    /* Back button for slideshow view */
    .vss-back-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: white;
        background: rgba(255,255,255,0.15);
        padding: 0.5rem 1rem;
        border-radius: 2rem;
        text-decoration: none;
        font-size: 0.875rem;
        margin-bottom: 1rem;
        transition: background 0.2s;
    }
    .vss-back-btn:hover {
        background: rgba(255,255,255,0.25);
        color: white;
    }
</style>
@endpush

@section('content')

@php
    $locale = app()->getLocale();

    $heroSlide = $slides->firstWhere('slide_type', 'hero');
    $contentSlides = $slides->where('slide_type', '!=', 'hero')->values();

    function vssYouTubeEmbed($url) {
        if (!$url) return null;
        $patterns = [
            '/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/',
            '/youtu\.be\/([a-zA-Z0-9_-]+)/',
            '/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/',
        ];
        foreach ($patterns as $p) {
            if (preg_match($p, $url, $m)) {
                return 'https://www.youtube.com/embed/' . $m[1] . '?rel=0&modestbranding=1';
            }
        }
        return $url; // direct MP4 or other
    }

    /**
     * Detect video URL type: 'youtube', 'google_drive', 'direct_video', or 'generic_url'
     */
    function vssVideoUrlType($url) {
        if (!$url) return null;
        if (str_contains($url, 'youtube.com') || str_contains($url, 'youtu.be') || str_contains($url, 'youtube.com/embed')) {
            return 'youtube';
        }
        if (str_contains($url, 'drive.google.com')) {
            return 'google_drive';
        }
        $ext = strtolower(pathinfo(parse_url($url, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));
        if (in_array($ext, ['mp4', 'webm', 'ogg'])) {
            return 'direct_video';
        }
        return 'generic_url';
    }

    /**
     * Get Google Drive embed URL from a share/view link
     */
    function vssGoogleDriveEmbed($url) {
        if (!$url) return null;
        $patterns = [
            '/\/file\/d\/([a-zA-Z0-9_-]+)/',
            '/id=([a-zA-Z0-9_-]+)/',
        ];
        foreach ($patterns as $p) {
            if (preg_match($p, $url, $m)) {
                return 'https://drive.google.com/file/d/' . $m[1] . '/preview';
            }
        }
        return $url;
    }

    function vssGoogleDriveStreamUrl($url) {
        if (!$url) return $url;
        $patterns = [
            '/\/file\/d\/([a-zA-Z0-9_-]+)/',
            '/id=([a-zA-Z0-9_-]+)/',
        ];
        foreach ($patterns as $p) {
            if (preg_match($p, $url, $m)) {
                return url('/gdrive-stream/' . $m[1]);
            }
        }
        return $url;
    }

    function vssPopupData($captionData) {
        if (is_array($captionData) && ($captionData['type'] ?? '') === 'multi') {
            return json_encode($captionData);
        }
        return (string)($captionData ?? '');
    }

    function vssProcessImageUrl($url) {
        if (empty($url)) return null;

        // Check if it's a Google Drive URL
        if (strpos($url, 'drive.google.com') !== false) {
            $patterns = [
                '/\/file\/d\/([a-zA-Z0-9_-]+)/',
                '/id=([a-zA-Z0-9_-]+)/',
                '/\/open\?id=([a-zA-Z0-9_-]+)/'
            ];
            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $url, $matches)) {
                    return 'https://lh3.googleusercontent.com/d/' . $matches[1];
                }
            }
        }

        // Wikimedia Commons: /wiki/File:NAME → Special:FilePath/NAME
        if (preg_match('/commons\.wikimedia\.org\/wiki\/File:(.+)/', $url, $matches)) {
            return 'https://commons.wikimedia.org/wiki/Special:FilePath/' . $matches[1];
        }

        return $url;
    }
@endphp

{{-- Scroll Progress Bar --}}
<div class="vsshow-progress-bar" id="vss-progress"></div>

{{-- Breadcrumb with back button --}}
<div class="feature-breadcrumb">
    <div class="container">
        @if($feature->parent)
            <a href="{{ url($feature->parent->path ?? '#') }}">
                {{ app()->getLocale() === 'en' && $feature->parent->name_en ? $feature->parent->name_en : $feature->parent->name }}
            </a>
            <span class="sep">/</span>
        @endif
        <a href="{{ url($feature->path) }}">{{ app()->getLocale() === 'en' && $feature->name_en ? $feature->name_en : $feature->name }}</a>
        <span class="sep">/</span>
        <span class="current">{{ $locale === 'en' && $selectedPage->title_en ? $selectedPage->title_en : $selectedPage->title }}</span>
    </div>
</div>

{{-- ======== HERO SECTION ======== --}}
@if($heroSlide)
<section class="vsshow-hero" style="{{ $heroSlide->bg_color && $heroSlide->bg_color !== '#ffffff' ? 'background: linear-gradient(135deg, '.e($heroSlide->bg_color).' 0%, #174E93 60%, #2563EB 100%);' : '' }}">
    <div id="vss-particles" class="vsshow-hero-particles"></div>

    @php
        $heroImages = $heroSlide->images ?? [];
        $heroImageUrls = $heroSlide->image_urls ?? [];
        $heroAllImages = array_merge($heroImages, $heroImageUrls);
        $heroUploadedCount = count($heroImages);
    @endphp
    @if(count($heroAllImages) > 0)
    <div style="position:absolute;inset:0;z-index:0;">
        @php
            $heroImg = $heroAllImages[0];
            $isHeroUploaded = $heroUploadedCount > 0;
            $heroImgSrc = $isHeroUploaded ? asset('storage/'.$heroImg) : vssProcessImageUrl($heroImg);
        @endphp
        <img src="{{ $heroImgSrc }}"
            alt="{{ $heroSlide->title }}"
            style="width:100%;height:100%;object-fit:cover;opacity:0.25;"
            onerror="this.style.display='none';">
    </div>
    @endif

    <div class="vsshow-hero-content vsshow-hero-anim">
        <div class="vsshow-hero-badge vsshow-enter" data-enter-delay="0">
            {{ $locale === 'en' && $selectedPage->title_en ? $selectedPage->title_en : $selectedPage->title }}
        </div>
        @if($heroSlide->title)
        <h1 class="vsshow-hero-title vsshow-enter" data-enter-delay="1">
            {{ $locale === 'en' && $heroSlide->title_en ? $heroSlide->title_en : $heroSlide->title }}
        </h1>
        @else
        <h1 class="vsshow-hero-title vsshow-enter" data-enter-delay="1">
            {{ $locale === 'en' && $selectedPage->title_en ? $selectedPage->title_en : $selectedPage->title }}
        </h1>
        @endif
        @if($heroSlide->subtitle)
        <p class="vsshow-hero-subtitle vsshow-enter" data-enter-delay="2">
            {{ $locale === 'en' && $heroSlide->subtitle_en ? $heroSlide->subtitle_en : $heroSlide->subtitle }}
        </p>
        @endif
        @if($heroSlide->description)
        <p class="vsshow-hero-subtitle vsshow-enter" data-enter-delay="3" style="font-size:1rem;opacity:0.7;">
            {!! $locale === 'en' && $heroSlide->description_en ? $heroSlide->description_en : $heroSlide->description !!}
        </p>
        @endif
    </div>

    <div class="vsshow-hero-scroll-hint vsshow-enter" data-enter-delay="5">
        <div class="vsshow-hero-scroll-line"></div>
        Scroll
    </div>
</section>
@else
{{-- Default Hero when no hero slide --}}
<section class="vsshow-hero">
    <div id="vss-particles" class="vsshow-hero-particles"></div>
    <div class="vsshow-hero-content vsshow-hero-anim">
        <div class="vsshow-hero-badge vsshow-enter" data-enter-delay="0">{{ $locale === 'en' && $selectedPage->title_en ? $selectedPage->title_en : $selectedPage->title }}</div>
        <h1 class="vsshow-hero-title vsshow-enter" data-enter-delay="1">
            {{ $locale === 'en' && $selectedPage->title_en ? $selectedPage->title_en : $selectedPage->title }}
        </h1>
        @if($selectedPage->description)
        <p class="vsshow-hero-subtitle vsshow-enter" data-enter-delay="2">{!! Str::limit(strip_tags($locale === 'en' && $selectedPage->description_en ? $selectedPage->description_en : $selectedPage->description), 200) !!}</p>
        @endif
    </div>
    <div class="vsshow-hero-scroll-hint vsshow-enter" data-enter-delay="5">
        <div class="vsshow-hero-scroll-line"></div>
        Scroll
    </div>
</section>
@endif

{{-- ======== CONTENT SLIDES ======== --}}
@foreach($contentSlides as $slideIndex => $slide)
@php
    $title = $locale === 'en' && $slide->title_en ? $slide->title_en : $slide->title;
    $subtitle = $locale === 'en' && $slide->subtitle_en ? $slide->subtitle_en : $slide->subtitle;
    $desc = $locale === 'en' && $slide->description_en ? $slide->description_en : $slide->description;
    $images = $slide->images ?? [];
    $imageUrls = $slide->image_urls ?? [];
    $allImages = array_merge($images, $imageUrls);
    $popup = $slide->info_popup ?? [];
    $bgStyle = ($slide->bg_color && $slide->bg_color !== '#ffffff') ? "background-color: {$slide->bg_color};" : '';
    $embedUrl = vssYouTubeEmbed($slide->video_url);
    $isYoutube = $slide->video_url && strpos($slide->video_url, 'youtube') !== false || strpos($slide->video_url, 'youtu.be') !== false;
@endphp

<section class="vsshow-section" style="{{ $bgStyle }}">
    <div class="vsshow-container">

        {{-- TEXT only --}}
        @if($slide->slide_type === 'text')
        <div class="vsshow-text-section">
            @if($title)
                <div class="vsshow-section-tag vsshow-enter" data-swipe="{{ $slideIndex % 2 === 0 ? 'left' : 'right' }}" data-enter-delay="0">{{ $locale === 'en' && $selectedPage->title_en ? $selectedPage->title_en : $selectedPage->title }}</div>
                <h2 class="vsshow-section-title vsshow-enter" data-swipe="{{ $slideIndex % 2 === 0 ? 'left' : 'right' }}" data-enter-delay="1">{{ $title }}</h2>
                <div class="vsshow-divider vsshow-enter" data-swipe="{{ $slideIndex % 2 === 0 ? 'left' : 'right' }}" data-enter-delay="2"></div>
            @endif
            @if($subtitle)
                <p class="vsshow-section-subtitle vsshow-enter" data-swipe="{{ $slideIndex % 2 === 0 ? 'left' : 'right' }}" data-enter-delay="3">{{ $subtitle }}</p>
            @endif
            @if($desc)
                <div class="vsshow-section-desc vsshow-enter" data-swipe="{{ $slideIndex % 2 === 0 ? 'left' : 'right' }}" data-enter-delay="4">{!! $desc !!}</div>
            @endif
        </div>

        {{-- CAROUSEL only --}}
        @elseif($slide->slide_type === 'carousel')
        <div>
            @if($title)
            <div class="vsshow-text-section" style="margin-bottom:2.5rem;">
                <div class="vsshow-section-tag vsshow-enter" data-swipe="{{ $slideIndex % 2 === 0 ? 'left' : 'right' }}" data-enter-delay="0">{{ $locale === 'en' && $selectedPage->title_en ? $selectedPage->title_en : $selectedPage->title }}</div>
                <h2 class="vsshow-section-title vsshow-enter" data-swipe="{{ $slideIndex % 2 === 0 ? 'left' : 'right' }}" data-enter-delay="1">{{ $title }}</h2>
                <div class="vsshow-divider vsshow-enter" data-swipe="{{ $slideIndex % 2 === 0 ? 'left' : 'right' }}" data-enter-delay="2"></div>
            </div>
            @if($subtitle)<p class="vsshow-section-subtitle vsshow-enter" data-swipe="{{ $slideIndex % 2 === 0 ? 'left' : 'right' }}" data-enter-delay="3" style="text-align:center;">{{ $subtitle }}</p>@endif
            @endif

            @if(count($allImages) > 0)
            <div class="vsshow-enter" data-swipe="{{ $slideIndex % 2 === 0 ? 'left' : 'right' }}" data-enter-delay="4">
            <div class="vsshow-carousel">
                <div class="vsshow-carousel-track">
                    @php
                        $unifiedImageOrder = $popup['unified_image_order'] ?? null;
                        $carouselRenderIdx = 0;
                    @endphp
                    @if($unifiedImageOrder && is_array($unifiedImageOrder))
                        @foreach($unifiedImageOrder as $orderItem)
                            @php
                                $itemType = $orderItem['type'] ?? null;
                                $imgSrc = null;
                                $itemCaption = '';

                                if ($itemType === 'upload') {
                                    $idx = $orderItem['uploadIndex'] ?? 0;
                                    $imgPath = $images[$idx] ?? null;
                                    if ($imgPath) {
                                        $imgSrc = asset('storage/'.$imgPath);
                                        $itemCaption = $popup[(string)$carouselRenderIdx] ?? '';
                                    }
                                } elseif ($itemType === 'url') {
                                    $idx = $orderItem['urlIndex'] ?? 0;
                                    $imgPath = $imageUrls[$idx] ?? null;
                                    if ($imgPath) {
                                        $imgSrc = vssProcessImageUrl($imgPath);
                                        $itemCaption = $popup[(string)$carouselRenderIdx] ?? '';
                                    }
                                }
                            @endphp
                            @if($imgSrc)
                            <div class="vsshow-carousel-slide">
                                <img src="{{ $imgSrc }}" alt="{{ $title }} — gambar {{ $carouselRenderIdx+1 }}" loading="lazy" style="width:100%;height:100%;object-fit:contain;">
                                @if(!empty($itemCaption))
                                <button class="vsshow-info-btn"
                                    data-popup="{{ vssPopupData($itemCaption) }}"
                                    data-img-src="{{ $imgSrc }}"
                                    title="Info">?</button>
                                @endif
                            </div>
                            @php $carouselRenderIdx++; @endphp
                            @endif
                        @endforeach
                    @else
                        @php
                            $uploadedCount = count($images);
                        @endphp
                        @foreach($allImages as $imgIdx => $imgPath)
                        <div class="vsshow-carousel-slide">
                            @php
                                $isUploadedImage = $imgIdx < $uploadedCount;
                                $imgSrc = $isUploadedImage ? asset('storage/'.$imgPath) : vssProcessImageUrl($imgPath);
                            @endphp
                            <img src="{{ $imgSrc }}" alt="{{ $title }} — gambar {{ $imgIdx+1 }}" loading="lazy" style="width:100%;height:100%;object-fit:contain;">
                            @if(!empty($popup[$imgIdx]) || !empty($popup[(string)$imgIdx]))
                            <button class="vsshow-info-btn"
                                data-popup="{{ vssPopupData($popup[$imgIdx] ?? $popup[(string)$imgIdx] ?? '') }}"
                                data-img-src="{{ $imgSrc }}"
                                title="Info">?</button>
                            @endif
                        </div>
                        @endforeach
                    @endif
                </div>

                @if(count($allImages) > 1)
                <button class="vsshow-carousel-btn prev" aria-label="Previous">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button class="vsshow-carousel-btn next" aria-label="Next">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
                <button class="vsshow-carousel-btn pause-play" id="carousel-pause-btn" aria-label="Pause">
                    <svg class="pause-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <svg class="play-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </button>
                @endif

                <div class="vsshow-carousel-dots">
                    @foreach($allImages as $imgIdx => $_)
                    <span class="vsshow-dot {{ $imgIdx === 0 ? 'active' : '' }}" data-idx="{{ $imgIdx }}"></span>
                    @endforeach
                </div>
            </div>
            </div>
            @endif

            @if($desc)
            <div class="vsshow-section-desc vsshow-enter" data-swipe="{{ $slideIndex % 2 === 0 ? 'left' : 'right' }}" data-enter-delay="4" style="text-align:center;margin:2rem auto 0;max-width:680px;display:block;">{!! $desc !!}</div>
            @endif
        </div>

        {{-- VIDEO --}}
        @elseif($slide->slide_type === 'video')
        <div>
            @if($title)
            <div class="vsshow-text-section" style="margin-bottom:2.5rem;">
                <div class="vsshow-section-tag vsshow-enter" data-swipe="{{ $slideIndex % 2 === 0 ? 'left' : 'right' }}" data-enter-delay="0">{{ $locale === 'en' && $selectedPage->title_en ? $selectedPage->title_en : $selectedPage->title }}</div>
                <h2 class="vsshow-section-title vsshow-enter" data-swipe="{{ $slideIndex % 2 === 0 ? 'left' : 'right' }}" data-enter-delay="1">{{ $title }}</h2>
                <div class="vsshow-divider vsshow-enter" data-swipe="{{ $slideIndex % 2 === 0 ? 'left' : 'right' }}" data-enter-delay="2"></div>
                @if($subtitle)<p class="vsshow-section-subtitle vsshow-enter" data-swipe="{{ $slideIndex % 2 === 0 ? 'left' : 'right' }}" data-enter-delay="3">{{ $subtitle }}</p>@endif
                @if($desc)<div class="vsshow-section-desc vsshow-enter" data-swipe="{{ $slideIndex % 2 === 0 ? 'left' : 'right' }}" data-enter-delay="4">{!! $desc !!}</div>@endif
            </div>
            @endif

            @if($slide->video_url || $slide->video_file)
            <div class="vsshow-video-wrap vsshow-enter" data-swipe="{{ $slideIndex % 2 === 0 ? 'left' : 'right' }}" data-enter-delay="5">
                @if(!empty($popup['video']) || (!empty($popup['video_url']) && $slide->video_url))
                <button class="vsshow-info-btn vsshow-video-info-btn"
                    data-popup="{{ vssPopupData($popup['video'] ?? $popup['video_url'] ?? '') }}"
                    title="Info Video">?</button>
                @endif

                @if($slide->video_file)
                {{-- Video dari upload file --}}
                <video controls style="width:100%;max-height:480px;display:block;background:#000;">
                    <source src="{{ asset('storage/' . $slide->video_file) }}" type="video/mp4">
                    Browser Anda tidak mendukung video.
                </video>
                @elseif($slide->video_url)
                    @php $vidType = vssVideoUrlType($slide->video_url); @endphp
                    @if($vidType === 'youtube')
                    <div class="vsshow-video-iframe-wrap" data-src="{{ $embedUrl }}">
                        <iframe data-src="{{ $embedUrl }}" allowfullscreen allow="autoplay; encrypted-media"
                            title="{{ $title ?? 'Video' }}"></iframe>
                    </div>
                    @elseif($vidType === 'google_drive')
                    <video controls style="width:100%;max-height:480px;display:block;background:#000;"
                        onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                        <source src="{{ vssGoogleDriveStreamUrl($slide->video_url) }}" type="video/mp4">
                    </video>
                    <div style="display:none;flex-direction:column;align-items:center;justify-content:center;min-height:200px;background:#000;color:#fff;border-radius:12px;">
                        <svg style="width:48px;height:48px;margin-bottom:8px;opacity:0.5;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        <p style="margin:0;">Video tidak dapat diputar langsung.</p>
                        <a href="{{ $slide->video_url }}" target="_blank" rel="noopener" style="color:#60a5fa;margin-top:8px;text-decoration:underline;">Buka di Google Drive</a>
                    </div>
                    @elseif($vidType === 'direct_video')
                    <video controls style="width:100%;max-height:480px;display:block;background:#000;">
                        <source src="{{ $slide->video_url }}" type="video/mp4">
                        Browser Anda tidak mendukung video.
                    </video>
                    @else
                    {{-- Generic URL (Vimeo, Dailymotion, dll) - embed via iframe --}}
                    <div class="vsshow-video-iframe-wrap" data-src="{{ $slide->video_url }}">
                        <iframe data-src="{{ $slide->video_url }}" allowfullscreen allow="autoplay; encrypted-media"
                            title="{{ $title ?? 'Video' }}" style="width:100%;height:100%;border:0;"></iframe>
                    </div>
                    @endif
                @endif
            </div>
            @endif
        </div>

        {{-- TEXT + CAROUSEL --}}
        @elseif($slide->slide_type === 'text_carousel')
        @php
            // Get carousel videos (from video_file as array or carousel_video_urls)
            $carouselVideoFiles = [];
            if ($slide->video_file) {
                $vf = $slide->video_file;
                if (is_array($vf)) {
                    $carouselVideoFiles = $vf;
                } elseif (is_string($vf) && str_starts_with($vf, '[')) {
                    $decoded = json_decode($vf, true);
                    $carouselVideoFiles = is_array($decoded) ? $decoded : [];
                }
            }
            $carouselVideoUrls = $slide->carousel_video_urls ?? [];
            $hasCarouselVideos = !empty($carouselVideoFiles) || !empty($carouselVideoUrls);
        @endphp
        <div class="vsshow-split {{ $slide->layout === 'right' ? 'vsshow-split-right' : '' }}{{ $slide->layout === 'center' ? ' vsshow-split-center' : '' }}">
            {{-- Text --}}
            <div class="vsshow-split-text">
                @if($title)
                <div class="vsshow-section-tag vsshow-enter" data-swipe="{{ $slideIndex % 2 === 0 ? 'left' : 'right' }}" data-enter-delay="0">{{ $locale === 'en' && $selectedPage->title_en ? $selectedPage->title_en : $selectedPage->title }}</div>
                <h2 class="vsshow-section-title vsshow-enter" data-swipe="{{ $slideIndex % 2 === 0 ? 'left' : 'right' }}" data-enter-delay="1" style="text-align:left;">{{ $title }}</h2>
                <div class="vsshow-divider vsshow-enter" data-swipe="{{ $slideIndex % 2 === 0 ? 'left' : 'right' }}" data-enter-delay="2"></div>
                @endif
                @if($subtitle)
                <p class="vsshow-section-subtitle vsshow-enter" data-swipe="{{ $slideIndex % 2 === 0 ? 'left' : 'right' }}" data-enter-delay="3" style="text-align:left;">{{ $subtitle }}</p>
                @endif
                @if($desc)
                <div class="vsshow-section-desc vsshow-enter" data-swipe="{{ $slideIndex % 2 === 0 ? 'left' : 'right' }}" data-enter-delay="4" style="text-align:left;">{!! $desc !!}</div>
                @endif
            </div>

            {{-- Carousel (Images or Videos) --}}
            <div class="vsshow-enter" data-swipe="{{ $slideIndex % 2 === 0 ? 'left' : 'right' }}" data-enter-delay="5">
                @if(count($allImages) > 0)
                {{-- Image Carousel --}}
                <div class="vsshow-carousel">
                    <div class="vsshow-carousel-track">
                        @php
                            $unifiedImageOrder = $popup['unified_image_order'] ?? null;
                            $carouselRenderIdx = 0;
                        @endphp
                        @if($unifiedImageOrder && is_array($unifiedImageOrder))
                            @foreach($unifiedImageOrder as $orderItem)
                                @php
                                    $itemType = $orderItem['type'] ?? null;
                                    $imgSrc = null;
                                    $itemCaption = '';

                                    if ($itemType === 'upload') {
                                        $idx = $orderItem['uploadIndex'] ?? 0;
                                        $imgPath = $images[$idx] ?? null;
                                        if ($imgPath) {
                                            $imgSrc = asset('storage/'.$imgPath);
                                            $itemCaption = $popup[(string)$carouselRenderIdx] ?? '';
                                        }
                                    } elseif ($itemType === 'url') {
                                        $idx = $orderItem['urlIndex'] ?? 0;
                                        $imgPath = $imageUrls[$idx] ?? null;
                                        if ($imgPath) {
                                            $imgSrc = vssProcessImageUrl($imgPath);
                                            $itemCaption = $popup[(string)$carouselRenderIdx] ?? '';
                                        }
                                    }
                                @endphp
                                @if($imgSrc)
                                <div class="vsshow-carousel-slide">
                                    <img src="{{ $imgSrc }}"
                                        alt="{{ $title }} — gambar {{ $carouselRenderIdx+1 }}"
                                        loading="lazy"
                                        style="width:100%;height:100%;object-fit:contain;"
                                    >
                                    @if(!empty($itemCaption))
                                    <button class="vsshow-info-btn"
                                        data-popup="{{ vssPopupData($itemCaption) }}"
                                        data-img-src="{{ $imgSrc }}"
                                        title="Info">?</button>
                                    @endif
                                </div>
                                @php $carouselRenderIdx++; @endphp
                                @endif
                            @endforeach
                        @else
                            @php
                                $uploadedCount = count($images);
                            @endphp
                            @foreach($allImages as $imgIdx => $imgPath)
                            <div class="vsshow-carousel-slide">
                                @php
                                    $isUploadedImage = $imgIdx < $uploadedCount;
                                    $imgSrc = $isUploadedImage ? asset('storage/'.$imgPath) : vssProcessImageUrl($imgPath);
                                @endphp
                                <img src="{{ $imgSrc }}"
                                    alt="{{ $title }} — gambar {{ $imgIdx+1 }}"
                                    loading="lazy"
                                    style="width:100%;height:100%;object-fit:contain;"
                                >
                                @if(!empty($popup[$imgIdx]) || !empty($popup[(string)$imgIdx]))
                                <button class="vsshow-info-btn"
                                    data-popup="{{ vssPopupData($popup[$imgIdx] ?? $popup[(string)$imgIdx] ?? '') }}"
                                    data-img-src="{{ $imgSrc }}"
                                    title="Info">?</button>
                                @endif
                            </div>
                            @endforeach
                        @endif
                    </div>

                    @if(count($allImages) > 1)
                    <button class="vsshow-carousel-btn prev" aria-label="Previous">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <button class="vsshow-carousel-btn next" aria-label="Next">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                    <button class="vsshow-carousel-btn pause-play" aria-label="Pause">
                        <svg class="pause-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <svg class="play-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </button>
                    @endif

                    <div class="vsshow-carousel-dots">
                        @foreach($allImages as $imgIdx => $_)
                        <span class="vsshow-dot {{ $imgIdx === 0 ? 'active' : '' }}" data-idx="{{ $imgIdx }}"></span>
                        @endforeach
                    </div>
                </div>
                @elseif($hasCarouselVideos)
                {{-- Video Carousel - Use carousel_video_order for consistent ordering and correct caption keys --}}
                <div class="vsshow-carousel">
                    <div class="vsshow-carousel-track">
                        @php
                            $carouselVideoOrder = $popup['carousel_video_order'] ?? null;
                            $carouselVideoCaptions = $popup['carousel_videos'] ?? [];
                            $carouselRenderIdx = 0;
                        @endphp
                        @if($carouselVideoOrder && is_array($carouselVideoOrder))
                            {{-- Use carousel_video_order: URLs and uploads are mixed per the saved order --}}
                            @foreach($carouselVideoOrder as $orderItem)
                                @php
                                    $itemType = $orderItem['type'] ?? null;
                                    $itemCaption = '';
                                @endphp
                                @if($itemType === 'url')
                                    @php
                                        $urlIdx = $orderItem['urlIndex'] ?? 0;
                                        $vidUrl = $carouselVideoUrls[$urlIdx] ?? null;
                                        $itemCaption = $carouselVideoCaptions['url_' . $urlIdx] ?? '';
                                    @endphp
                                    @if($vidUrl)
                                        <div class="vsshow-carousel-slide">
                                            @php
                                                $vidEmbedUrl = vssYouTubeEmbed($vidUrl);
                                                $vidUrlType = vssVideoUrlType($vidUrl);
                                            @endphp
                                            @if($vidUrlType === 'youtube')
                                            <div class="vsshow-video-iframe-wrap">
                                                <iframe src="{{ $vidEmbedUrl }}" allowfullscreen allow="autoplay; encrypted-media"
                                                    title="{{ $title ?? 'Video ' . ($carouselRenderIdx + 1) }}" style="border:0;"></iframe>
                                            </div>
                                            @elseif($vidUrlType === 'google_drive')
                                            <video controls style="width:100%;max-height:420px;display:block;background:#000;"
                                                onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                                                <source src="{{ vssGoogleDriveStreamUrl($vidUrl) }}" type="video/mp4">
                                            </video>
                                            <div style="display:none;flex-direction:column;align-items:center;justify-content:center;min-height:200px;background:#000;color:#fff;border-radius:12px;">
                                                <svg style="width:48px;height:48px;margin-bottom:8px;opacity:0.5;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                                <p style="margin:0;">Video tidak dapat diputar langsung.</p>
                                                <a href="{{ $vidUrl }}" target="_blank" rel="noopener" style="color:#60a5fa;margin-top:8px;text-decoration:underline;">Buka di Google Drive</a>
                                            </div>
                                            @elseif($vidUrlType === 'direct_video')
                                            <video controls style="width:100%;max-height:420px;display:block;background:#000;">
                                                <source src="{{ $vidUrl }}" type="video/mp4">
                                                Browser Anda tidak mendukung video.
                                            </video>
                                            @else
                                            {{-- Generic URL - embed via iframe --}}
                                            <div class="vsshow-video-iframe-wrap">
                                                <iframe src="{{ $vidUrl }}" allowfullscreen allow="autoplay; encrypted-media"
                                                    title="{{ $title ?? 'Video ' . ($carouselRenderIdx + 1) }}" style="border:0;"></iframe>
                                            </div>
                                            @endif
                                            @if(!empty($itemCaption))
                                            <button class="vsshow-info-btn"
                                                data-popup="{{ vssPopupData($itemCaption) }}"
                                                title="Info">?</button>
                                            @endif
                                        </div>
                                        @php $carouselRenderIdx++; @endphp
                                    @endif
                                @elseif($itemType === 'upload' || $itemType === 'newUpload')
                                    @php
                                        $uploadIndex = $orderItem['uploadIndex'] ?? $orderItem['newUploadIndex'] ?? 0;
                                        $vidFile = $carouselVideoFiles[$uploadIndex] ?? null;
                                        $itemCaption = $carouselVideoCaptions['upload_' . $uploadIndex] ?? ($carouselVideoCaptions['newUpload_' . $uploadIndex] ?? '');
                                    @endphp
                                    @if($vidFile)
                                        <div class="vsshow-carousel-slide">
                                            <video controls style="width:100%;max-height:300px;display:block;background:#000;">
                                                <source src="{{ asset('storage/' . $vidFile) }}" type="video/mp4">
                                                Browser Anda tidak mendukung video.
                                            </video>
                                            @if(!empty($itemCaption))
                                            <button class="vsshow-info-btn"
                                                data-popup="{{ vssPopupData($itemCaption) }}"
                                                title="Info">?</button>
                                            @endif
                                        </div>
                                        @php $carouselRenderIdx++; @endphp
                                    @endif
                                @endif
                            @endforeach
                        @else
                            {{-- Fallback: render URLs first, then uploads (legacy behavior) --}}
                            @foreach($carouselVideoUrls as $vidIdx => $vidUrl)
                            <div class="vsshow-carousel-slide">
                                @php
                                    $vidEmbedUrl = vssYouTubeEmbed($vidUrl);
                                    $vidUrlType = vssVideoUrlType($vidUrl);
                                @endphp
                                @if($vidUrlType === 'youtube')
                                <div class="vsshow-video-iframe-wrap">
                                    <iframe src="{{ $vidEmbedUrl }}" allowfullscreen allow="autoplay; encrypted-media"
                                        title="{{ $title ?? 'Video ' . ($vidIdx + 1) }}" style="border:0;"></iframe>
                                </div>
                                @elseif($vidUrlType === 'google_drive')
                                <video controls style="width:100%;max-height:420px;display:block;background:#000;"
                                    onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                                    <source src="{{ vssGoogleDriveStreamUrl($vidUrl) }}" type="video/mp4">
                                </video>
                                <div style="display:none;flex-direction:column;align-items:center;justify-content:center;min-height:200px;background:#000;color:#fff;border-radius:12px;">
                                    <svg style="width:48px;height:48px;margin-bottom:8px;opacity:0.5;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                    <p style="margin:0;">Video tidak dapat diputar langsung.</p>
                                    <a href="{{ $vidUrl }}" target="_blank" rel="noopener" style="color:#60a5fa;margin-top:8px;text-decoration:underline;">Buka di Google Drive</a>
                                </div>
                                @elseif($vidUrlType === 'direct_video')
                                <video controls style="width:100%;max-height:420px;display:block;background:#000;">
                                    <source src="{{ $vidUrl }}" type="video/mp4">
                                    Browser Anda tidak mendukung video.
                                </video>
                                @else
                                <div class="vsshow-video-iframe-wrap">
                                    <iframe src="{{ $vidUrl }}" allowfullscreen allow="autoplay; encrypted-media"
                                        title="{{ $title ?? 'Video ' . ($vidIdx + 1) }}" style="border:0;"></iframe>
                                </div>
                                @endif
                                @if(!empty($carouselVideoCaptions['url_' . $vidIdx]))
                                <button class="vsshow-info-btn"
                                    data-popup="{{ vssPopupData($carouselVideoCaptions['url_' . $vidIdx]) }}"
                                    title="Info">?</button>
                                @endif
                            </div>
                            @endforeach
                            @foreach($carouselVideoFiles as $vidIdx => $vidFile)
                            <div class="vsshow-carousel-slide">
                                <video controls style="width:100%;max-height:300px;display:block;background:#000;">
                                    <source src="{{ asset('storage/' . $vidFile) }}" type="video/mp4">
                                    Browser Anda tidak mendukung video.
                                </video>
                                @if(!empty($carouselVideoCaptions['upload_' . $vidIdx]))
                                <button class="vsshow-info-btn"
                                    data-popup="{{ vssPopupData($carouselVideoCaptions['upload_' . $vidIdx]) }}"
                                    title="Info">?</button>
                                @endif
                            </div>
                            @endforeach
                        @endif
                    </div>

                    @php
                        $totalCarouselVideos = $carouselVideoOrder && is_array($carouselVideoOrder)
                            ? $carouselRenderIdx
                            : (count($carouselVideoUrls) + count($carouselVideoFiles));
                    @endphp

                    @if($totalCarouselVideos > 1)
                    <button class="vsshow-carousel-btn prev" aria-label="Previous">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <button class="vsshow-carousel-btn next" aria-label="Next">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                    <button class="vsshow-carousel-btn pause-play" aria-label="Pause">
                        <svg class="pause-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <svg class="play-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </button>
                    @endif

                    <div class="vsshow-carousel-dots">
                        @for($di = 0; $di < $totalCarouselVideos; $di++)
                        <span class="vsshow-dot {{ $di === 0 ? 'active' : '' }}" data-idx="{{ $di }}"></span>
                        @endfor
                    </div>
                </div>
                @endif
            </div>
        </div>{{-- end split --}}
        @endif

    </div>{{-- end container --}}
</section>
@endforeach

{{-- Empty state --}}
@if($slides->isEmpty())
<section class="vsshow-section" style="min-height:60vh;display:flex;align-items:center;justify-content:center;">
    <div class="vsshow-text-section">
        <div style="font-size:4rem;margin-bottom:1rem;">🎞</div>
        <h2 class="vsshow-section-title" style="color:#94a3b8;">Konten sedang disiapkan</h2>
        <p class="vsshow-section-desc">Halaman ini belum memiliki slide. Silakan kembali lagi nanti.</p>
    </div>
</section>
@endif

{{-- ======== INFO POPUP MODAL ======== --}}
<div id="vss-popup-overlay" class="vsshow-popup-overlay"></div>
<div id="vss-popup-card" class="vsshow-popup-card" role="dialog" aria-modal="true" aria-labelledby="vss-popup-title">
    <div class="vsshow-popup-header">
        <div class="vsshow-popup-icon">?</div>
        <button id="vss-popup-close" class="vsshow-popup-close" aria-label="Tutup">✕</button>
    </div>
    <img id="vss-popup-img" class="vsshow-popup-img" src="" alt="" style="display:none;">
    <div id="vss-popup-body" class="vsshow-popup-body"></div>
</div>

@endsection

{{-- Login Modal (if guest) --}}
@if(isset($requiresLoginModal) && $requiresLoginModal)
    @include('partials.login_modal', [
        'previewImage' => $loginModalPreview ?? null,
        'roomName'     => $loginModalRoomName ?? null
    ])
@endif

@push('scripts')
<script src="{{ asset('js/pages/virtual_slideshow.js') }}"></script>
@endpush
