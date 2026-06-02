@extends('layouts.guest')

@section('title', $feature->name . ' — ' . config('app.name'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
    <link rel="stylesheet" href="{{ asset('css/feature-page.css') }}">
    <link rel="stylesheet" href="{{ asset('cms_rte/runtime/guest_richtexteditor_content.css?v=' . (file_exists(public_path('cms_rte/runtime/guest_richtexteditor_content.css')) ? filemtime(public_path('cms_rte/runtime/guest_richtexteditor_content.css')) : time())) }}">
    <style>
        .profile-hero {
            position: relative;
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),
                url('/image/background.png') center 35%/cover no-repeat;
            color: #fff;
            padding: 48px 0;
            min-height: 160px;
            display: flex;
            align-items: center;
        }

        .profile-hero h1 {
            font-family: 'Poppins', 'Montserrat', sans-serif;
            font-size: 32px;
            font-weight: 800;
            margin: 0;
            letter-spacing: 1px;
            color: #fff;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .profile-section {
            padding: 36px 0;
            background: transparent !important;
        }

        /* Match CMS Preview container width for layout parity */
        .profile-section .container {
            max-width: 1170px !important;
        }

        .profile-section-bg {
            background: #f8f9fa;
        }

        .profile-section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        .profile-section-subtitle {
            font-size: 1.1rem;
            color: #174E93;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        .profile-section-desc table { border-collapse: collapse !important; margin: 1rem 0 !important; max-width: 100% !important; }
        .profile-section-desc table th { font-weight: 700 !important; text-align: center !important; padding: 6px 8px !important; border: 1px solid #000000 !important; }
        .profile-section-desc table td { padding: 6px 8px !important; border: 1px solid #000000 !important; vertical-align: top !important; }
        .profile-section-desc img { max-width: 100%; height: auto !important; border-radius: 2px; }
        
        /* Figure & Figcaption Parity */
        .profile-section-desc figure { display: inline-table; margin: 0.5em 4px; vertical-align: top; max-width: 100%; }
        .profile-section-desc figcaption { text-align: center; font-size: 0.85em; color: #555; padding: 4px 0; display: table-caption; caption-side: bottom; word-break: break-word; }

        .profile-section-desc [style*="text-align:center"],
        .profile-section-desc [style*="text-align: center"],
        .profile-section-desc [style*="text-align : center"],
        .profile-section-desc [align="center"] { 
            text-align: center !important; 
        }

        .profile-section-desc [style*="text-align:left"],
        .profile-section-desc [style*="text-align: left"],
        .profile-section-desc [style*="text-align : left"],
        .profile-section-desc [align="left"] { 
            text-align: left !important; 
        }

        .profile-section-desc [style*="text-align:right"],
        .profile-section-desc [style*="text-align: right"],
        .profile-section-desc [style*="text-align : right"],
        .profile-section-desc [align="right"] { 
            text-align: right !important; 
        }

        .profile-section-desc [style*="text-align:center"] img,
        .profile-section-desc [style*="text-align: center"] img,
        .profile-section-desc [align="center"] img { 
            width: auto !important; 
            margin-left: auto !important; 
            margin-right: auto !important; 
            display: block !important;
        }

        .profile-section-desc [style*="text-align:left"] img,
        .profile-section-desc [align="left"] img { 
            margin-left: 0 !important; 
            margin-right: auto !important;
            display: inline-block !important;
        }

        .profile-section-desc [style*="text-align:right"] img,
        .profile-section-desc [align="right"] img { 
            margin-left: auto !important; 
            margin-right: 0 !important; 
            display: inline-block !important;
        }

        .page-image {
            width: 100%;
            border-radius: 0.75rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            object-fit: cover;
        }

        @media (max-width: 768px) {
            .profile-section {
                width: 100% !important;
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch !important;
            }
            .profile-section .container {
                width: 1170px !important;
                max-width: none !important;
                padding-left: 20px !important;
                padding-right: 20px !important;
                box-sizing: border-box !important;
            }
        }

.page-link-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 1.5rem;
            padding: 0.75rem 1.5rem;
            background: #174E93;
            color: white;
            border-radius: 0.5rem;
            font-weight: 600;
            text-decoration: none;
            transition: background 0.2s;
            font-size: 0.9rem;
        }

        .page-link-btn:hover {
            background: #1e40af;
        }

        .struktur-layout {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
        }

        @media (min-width: 768px) {
            .struktur-layout {
                grid-template-columns: 1fr auto;
            align-items: flex-start;
            }
        }

        .struktur-right {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
        }

        .struktur-logo {
            max-width: 120px;
            height: auto;
            object-fit: contain;
        }

        .struktur-image {
            width: 100%;
            max-width: 500px;
            border-radius: 0.75rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            object-fit: cover;
        }

        .sdm-layout {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
        }

        @media (min-width: 768px) {
            .sdm-layout {
                grid-template-columns: 1fr;
            }
        }

        .chart-card {
            background: white;
            border-bottom: 1px solid #e2e8f0;
            border-radius: 0;
            padding: 3rem 2rem;
            transition: background 0.2s ease;
            max-width: 800px;
            margin: 0 auto;
            width: 100%;
        }
        
        .chart-card:last-child {
            border-bottom: none;
        }

        .chart-card h4 {
            font-size: 1.25rem;
            font-weight: 500;
            color: #1e293b;
            margin-bottom: 2.5rem;
            text-align: center;
        }

        .chart-card canvas {
            max-height: 400px;
        }

        .sdm-container-box {
            border: 1px solid #e2e8f0;
            background: white;
            border-radius: 0.5rem;
            overflow: hidden;
            margin-top: 2rem;
        }

        .profile-divider {
            border: 0;
            height: 1px;
            background: #e8ecef;
            margin: 0;
        }

        .section-block {
            margin-bottom: 1.5rem;
        }

        .section-block:last-child {
            margin-bottom: 0;
        }

        .section-block h3 {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        .section-block p {
            color: #475569;
            line-height: 1.75;
            margin-bottom: 0.75rem;
        }

        .section-images {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            padding-top: 0.5rem;
            width: 100%;
            align-items: center;
            margin-top: 0.75rem;
        }

        .section-images img {
            max-width: 300px;
            width: 100%;
            border-radius: 0.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            object-fit: cover;
        }

        .page-nav {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 1.5rem;
        }

        .page-nav-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 2.5rem;
            height: 2.5rem;
            padding: 0 0.75rem;
            border-radius: 0.5rem;
            font-weight: 600;
            font-size: 0.875rem;
            text-decoration: none;
            transition: all 0.2s;
            border: 2px solid #e5e7eb;
            background: white;
            color: #6b7280;
        }

        .page-nav-btn:hover {
            border-color: #174E93;
            color: #174E93;
        }

        .page-nav-btn.active {
            background: #174E93;
            border-color: #174E93;
            color: white;
        }

        .page-image-container {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            padding-top: 0.5rem;
            width: 100%;
            align-items: center;
            background: transparent;
            border: none;
            box-shadow: none;
        }

        .page-image-container > div {
            background: transparent;
            box-shadow: none;
            border: none;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
        }

        .page-image-container > div > img {
            background: transparent;
            box-shadow: none;
            border: none;
            max-width: 100%;
            height: auto;
            display: block;
        }
    </style>
@endpush

@section('content')

    {{-- Breadcrumb --}}
    <div class="feature-breadcrumb">
        <div class="container">
            @if ($feature->parent)
                <a href="{{ url($feature->parent->path ?? '#') }}">
                    {{ $locale === 'en' ? $feature->parent->name_en ?? $feature->parent->name : $feature->parent->name }}
                </a>
                <span class="sep">/</span>
            @endif
            <span class="current">
                {{ $locale === 'id' ? $feature->name : $feature->name_en ?? $feature->name }}
            </span>
        </div>
    </div>

    {{-- Hero --}}
    <div class="profile-hero">
        <div class="container">
            @if ($feature->parent)
                <p
                    style="font-size:0.8rem;opacity:0.6;margin-bottom:0.5rem;text-transform:uppercase;letter-spacing:0.08em;">
                    {{ $locale === 'en' ? $feature->parent->name_en ?? $feature->parent->name : $feature->parent->name }}
                </p>
            @endif
            <h1>{{ strtoupper($locale === 'id' ? $feature->name : $feature->name_en ?? $feature->name) }}</h1>
        </div>
    </div>

    {{-- Profile Pages --}}
    @if ($currentPage)
        @php
            $page = $currentPage;
            $pageTitle = $locale === 'en' ? $page->title_en ?? $page->title : $page->title;
            $pageDesc =
                $locale === 'en' ? $page->description_en ?? ($page->description ?? '') : $page->description ?? '';
            $chartData = $page->chart_data;
        @endphp

        {{-- ===== DYNAMIC GUEST LAYOUT (100% FOLLOWS CMS PREVIEW LOGIC) ===== --}}
        @if (in_array($page->type, ['default', 'tugas_fungsi']))
            <section class="profile-section{{ !$isEven ? ' profile-section-bg' : '' }}">
                <div class="container">
                    @php
                        $hasDesc = !empty(trim(strip_tags($pageDesc))) || !empty(trim($pageDesc));
                        $hasImages = $page->images && count($page->images) > 0;
                        $hasSections = $page->sections && $page->sections->count() > 0;
                        $hasTitle = in_array($page->type, ['default', 'tugas_fungsi']) && !empty(trim($pageTitle));
                        $hasLink = !empty(trim($page->link_text)) && !empty(trim($page->link_url));
                    @endphp

                    @if ($hasImages)
                        <div style="display:grid;grid-template-columns:1fr auto;gap:32px;align-items:start;width:100%;" class="guest-dynamic-grid">
                            <div class="preview-text-col" style="min-width:0;overflow:hidden;">
                                <div style="width: 100%; word-break: break-word; overflow-wrap: break-word; min-width: 0;">
                                    @if ($hasTitle)
                                        <h2 class="profile-section-title">{{ $pageTitle }}</h2>
                                    @endif
                                    @if ($hasDesc)
                                        <div class="profile-section-desc" style="margin-bottom: 1.5rem;">{!! $pageDesc !!}</div>
                                    @endif
                                    @if ($hasSections)
                                        @foreach ($page->sections as $section)
                                            <div class="section-block" style="margin-bottom: 1.5rem;">
                                                @if ($section->title)
                                                    <h2 style="font-size: 1.5rem; font-weight: 700; color: #1e293b; margin-bottom: 0.75rem; text-decoration: underline;">
                                                        {{ $locale === 'en' ? $section->title_en ?? $section->title : $section->title }}
                                                    </h2>
                                                @endif
                                                @if ($section->description)
                                                    <div class="profile-section-desc" style="color: #475569; line-height: 1.75; font-size: 1rem;">{!! $locale === 'en' ? $section->description_en ?? $section->description : $section->description !!}</div>
                                                @endif
                                            </div>
                                        @endforeach
                                    @endif
                                    @if ($hasLink)
                                        <a href="{{ $page->link_url }}" class="page-link-btn" target="_blank" style="display:inline-flex;align-items:center;gap:0.5rem;margin-top:1.5rem;padding:0.75rem 1.5rem;background:#174E93;color:white;border-radius:0.5rem;font-weight:500;text-decoration:none;">
                                            {{ $page->link_text }}
                                            <svg style="width: 1rem; height: 1rem; margin-left: 0.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                        </a>
                                    @endif
                                </div>
                            </div>
                            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:32px;min-width:220px;" class="guest-dynamic-img-col">
                                @foreach ($page->images as $idx => $img)
                                    @php
                                        $posData = $page->image_positions[$idx] ?? null;
                                        $w = 200;
                                        $h = 150;
                                        $oX = 0;
                                        $oY = 0;
                                        $focalX = 50;
                                        $focalY = 50;
                                        if (is_array($posData)) {
                                            $w = floatval($posData['width'] ?? 200);
                                            $h = floatval($posData['height'] ?? 150);
                                            $oX = floatval($posData['offsetX'] ?? 0);
                                            $oY = floatval($posData['offsetY'] ?? 0);
                                            if (isset($posData['position'])) {
                                                $parts = explode(' ', $posData['position']);
                                                $focalX = floatval($parts[0] ?? 50);
                                                $focalY = floatval($parts[1] ?? 50);
                                            }
                                        }
                                        $mr = -$oX;
                                        $mt = $oY;
                                        $styleStr = "position: relative; border-radius: 0.75rem; overflow: visible !important; width: {$w}px; height: {$h}px; margin: {$mt}px {$mr}px 0 0; z-index: 10;";
                                    @endphp
                                    <div class="guest-img-wrapper" style="{!! $styleStr !!}">
                                        <img src="{{ asset('storage/' . $img) }}" alt="{{ $pageTitle }}" style="width: 100%; height: 100%; object-fit: cover; object-position: {{ $focalX }}% {{ $focalY }}%; display: block; border-radius: 0.75rem;">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @elseif ($hasDesc || $hasSections || $hasTitle || $hasLink)
                        <div style="width: 100%; word-break: break-word; overflow-wrap: break-word; min-width: 0;">
                            @if ($hasTitle)
                                <h2 class="profile-section-title">{{ $pageTitle }}</h2>
                            @endif
                            @if ($hasDesc)
                                <div class="profile-section-desc" style="margin-bottom: 1.5rem;">{!! $pageDesc !!}</div>
                            @endif
                            @if ($hasSections)
                                @foreach ($page->sections as $section)
                                    <div class="section-block" style="margin-bottom: 1.5rem;">
                                        @if ($section->title)
                                            <h2 style="font-size: 1.5rem; font-weight: 700; color: #1e293b; margin-bottom: 0.75rem; text-decoration: underline;">
                                                {{ $locale === 'en' ? $section->title_en ?? $section->title : $section->title }}
                                            </h2>
                                        @endif
                                        @if ($section->description)
                                            <div class="profile-section-desc" style="color: #475569; line-height: 1.75; font-size: 1rem;">{!! $locale === 'en' ? $section->description_en ?? $section->description : $section->description !!}</div>
                                        @endif
                                    </div>
                                @endforeach
                            @endif
                            @if ($hasLink)
                                <a href="{{ $page->link_url }}" class="page-link-btn" target="_blank" style="display:inline-flex;align-items:center;gap:0.5rem;margin-top:1.5rem;padding:0.75rem 1.5rem;background:#174E93;color:white;border-radius:0.5rem;font-weight:500;text-decoration:none;">
                                    {{ $page->link_text }}
                                    <svg style="width: 1rem; height: 1rem; margin-left: 0.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </section>
            <hr class="profile-divider">
        @endif

        {{-- ===== STRUKTUR IMAGE ===== --}}
        @if ($page->type === 'struktur_image')
            <section class="profile-section{{ !$isEven ? ' profile-section-bg' : '' }}">
                <div class="container">
                    @php
                        $hasDesc = !empty(trim(strip_tags($pageDesc))) || !empty(trim($pageDesc));
                        $hasImages = $page->images && count($page->images) > 0;
                        $hasSections = $page->sections && $page->sections->count() > 0;
                        $hasTitle = !empty(trim($pageTitle));
                    @endphp

                    {{-- Grid layout: text/sections left, images right — exact same structure as tugas_fungsi --}}
                    @if ($hasImages)
                        <div style="display:grid;grid-template-columns:1fr auto;gap:32px;align-items:start;width:100%;" class="guest-dynamic-grid">
                            <div class="preview-text-col" style="min-width:0;overflow:hidden;">
                                <div style="width: 100%; word-break: break-word; overflow-wrap: break-word; min-width: 0;">
                                    @if ($hasTitle)
                                        <h2 class="profile-section-title">{{ $pageTitle }}</h2>
                                    @endif
                                    @if ($hasDesc)
                                        <div class="profile-section-desc" style="margin-bottom: 1.5rem;">{!! $pageDesc !!}</div>
                                    @endif
                                    @if ($hasSections)
                                        @foreach ($page->sections as $section)
                                            <div class="section-block" style="margin-bottom: 1.5rem;">
                                                @if ($section->title)
                                                    <h2 style="font-size: 1.5rem; font-weight: 700; color: #1e293b; margin-bottom: 0.75rem; text-decoration: underline;">
                                                        {{ $locale === 'en' ? $section->title_en ?? $section->title : $section->title }}
                                                    </h2>
                                                @endif
                                                @if ($section->description)
                                                    <div class="profile-section-desc" style="color: #475569; line-height: 1.75; font-size: 1rem;">{!! $locale === 'en' ? $section->description_en ?? $section->description : $section->description !!}</div>
                                                @endif
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:32px;min-width:220px;" class="guest-dynamic-img-col">
                                @foreach ($page->images as $idx => $img)
                                    @php
                                        $posData = $page->image_positions[$idx] ?? null;
                                        $w = 200;
                                        $h = 150;
                                        $oX = 0;
                                        $oY = 0;
                                        $focalX = 50;
                                        $focalY = 50;
                                        if (is_array($posData)) {
                                            $w = floatval($posData['width'] ?? 200);
                                            $h = floatval($posData['height'] ?? 150);
                                            $oX = floatval($posData['offsetX'] ?? 0);
                                            $oY = floatval($posData['offsetY'] ?? 0);
                                            if (isset($posData['position'])) {
                                                $parts = explode(' ', $posData['position']);
                                                $focalX = floatval($parts[0] ?? 50);
                                                $focalY = floatval($parts[1] ?? 50);
                                            }
                                        }
                                        $mr = -$oX;
                                        $mt = $oY;
                                        $styleStr = "position: relative; border-radius: 0.75rem; overflow: visible !important; width: {$w}px; height: {$h}px; margin: {$mt}px {$mr}px 0 0; z-index: 10;";
                                    @endphp
                                    <div class="guest-img-wrapper" style="{!! $styleStr !!}">
                                        <img src="{{ asset('storage/' . $img) }}" alt="{{ $pageTitle }}" style="width: 100%; height: 100%; object-fit: cover; object-position: {{ $focalX }}% {{ $focalY }}%; display: block; border-radius: 0.75rem;">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @elseif ($hasDesc || $hasSections || $hasTitle)
                        <div style="width: 100%; word-break: break-word; overflow-wrap: break-word; min-width: 0;">
                            @if ($hasTitle)
                                <h2 class="profile-section-title">{{ $pageTitle }}</h2>
                            @endif
                            @if ($hasDesc)
                                <div class="profile-section-desc" style="margin-bottom: 1.5rem;">{!! $pageDesc !!}</div>
                            @endif
                            @if ($hasSections)
                                @foreach ($page->sections as $section)
                                    <div class="section-block" style="margin-bottom: 1.5rem;">
                                        @if ($section->title)
                                            <h2 style="font-size: 1.5rem; font-weight: 700; color: #1e293b; margin-bottom: 0.75rem; text-decoration: underline;">
                                                {{ $locale === 'en' ? $section->title_en ?? $section->title : $section->title }}
                                            </h2>
                                        @endif
                                        @if ($section->description)
                                            <div class="profile-section-desc" style="color: #475569; line-height: 1.75; font-size: 1rem;">{!! $locale === 'en' ? $section->description_en ?? $section->description : $section->description !!}</div>
                                        @endif
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    @endif

                    {{-- Struktur bottom: logo + section images stacked vertically --}}
                    @if ($page->logo_path || ($page->sections && $page->sections->count()))
                        <div style="display: flex; flex-direction: column; align-items: center; gap: 1rem; margin-top: 1.5rem;">
                            @if ($page->logo_path)
                                <img src="{{ asset('storage/' . $page->logo_path) }}" alt="Logo" style="max-width: 120px; height: auto; object-fit: contain;">
                            @endif
                            @if ($page->sections && $page->sections->count())
                                @foreach ($page->sections as $section)
                                    @if ($section->images && count($section->images))
                                        @foreach ($section->images as $img)
                                            <img src="{{ asset('storage/' . $img) }}" alt="Struktur"
                                                style="width: 100%; max-width: 500px; border-radius: 0.75rem; box-shadow: 0 4px 12px rgba(0,0,0,0.1); object-fit: contain;">
                                        @endforeach
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    @endif
                </div>
            </section>
            <hr class="profile-divider">
        @endif

        {{-- ===== SDM CHART ===== --}}
        @if ($page->type === 'sdm_chart')
            <section class="profile-section{{ !$isEven ? ' profile-section-bg' : '' }}">
                <div class="container">
                    @php
                        $hasDesc = !empty(trim(strip_tags($pageDesc))) || !empty(trim($pageDesc));
                        $hasImages = $page->images && count($page->images) > 0;
                        $hasSections = $page->sections && $page->sections->count() > 0;
                        $hasCharts = $chartData && is_array($chartData) && count($chartData) > 0;
                        $hasTitle = !empty(trim($pageTitle));
                    @endphp

                    {{-- Description + Images Grid (same as tugas_fungsi) --}}
                    @if ($hasImages)
                        <div style="display:grid;grid-template-columns:1fr auto;gap:32px;align-items:start;width:100%;margin-bottom:2rem;" class="guest-dynamic-grid">
                            <div class="preview-text-col" style="min-width:0;overflow:hidden;">
                                <div style="width: 100%; word-break: break-word; overflow-wrap: break-word; min-width: 0;">
                                    @if ($hasTitle)
                                        <h2 class="profile-section-title">{{ $pageTitle }}</h2>
                                    @endif
                                    @if ($hasDesc)
                                        <div class="profile-section-desc" style="margin-bottom: 1.5rem;">{!! $pageDesc !!}</div>
                                    @endif
                                    @if ($hasSections)
                                        @foreach ($page->sections as $section)
                                            <div class="section-block" style="margin-bottom: 1.5rem;">
                                                @if ($section->title)
                                                    <h2 style="font-size: 1.5rem; font-weight: 700; color: #1e293b; margin-bottom: 0.75rem; text-decoration: underline;">
                                                        {{ $locale === 'en' ? $section->title_en ?? $section->title : $section->title }}
                                                    </h2>
                                                @endif
                                                @if ($section->description)
                                                    <div class="profile-section-desc" style="color: #475569; line-height: 1.75; font-size: 1rem;">{!! $locale === 'en' ? $section->description_en ?? $section->description : $section->description !!}</div>
                                                @endif
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:32px;min-width:220px;" class="guest-dynamic-img-col">
                                @foreach ($page->images as $idx => $img)
                                    @php
                                        $posData = $page->image_positions[$idx] ?? null;
                                        $w = 200;
                                        $h = 150;
                                        $oX = 0;
                                        $oY = 0;
                                        $focalX = 50;
                                        $focalY = 50;
                                        if (is_array($posData)) {
                                            $w = floatval($posData['width'] ?? 200);
                                            $h = floatval($posData['height'] ?? 150);
                                            $oX = floatval($posData['offsetX'] ?? 0);
                                            $oY = floatval($posData['offsetY'] ?? 0);
                                            if (isset($posData['position'])) {
                                                $parts = explode(' ', $posData['position']);
                                                $focalX = floatval($parts[0] ?? 50);
                                                $focalY = floatval($parts[1] ?? 50);
                                            }
                                        }
                                        $mr = -$oX;
                                        $mt = $oY;
                                        $styleStr = "position: relative; border-radius: 0.75rem; overflow: visible !important; width: {$w}px; height: {$h}px; margin: {$mt}px {$mr}px 0 0; z-index: 10;";
                                    @endphp
                                    <div class="guest-img-wrapper" style="{!! $styleStr !!}">
                                        <img src="{{ asset('storage/' . $img) }}" alt="{{ $pageTitle }}" style="width: 100%; height: 100%; object-fit: cover; object-position: {{ $focalX }}% {{ $focalY }}%; display: block; border-radius: 0.75rem;">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @elseif ($hasDesc || $hasSections || $hasTitle)
                        <div style="width: 100%; word-break: break-word; overflow-wrap: break-word; min-width: 0; margin-bottom: 2rem;">
                            @if ($hasTitle)
                                <h2 class="profile-section-title">{{ $pageTitle }}</h2>
                            @endif
                            @if ($hasDesc)
                                <div class="profile-section-desc" style="margin-bottom: 1.5rem;">{!! $pageDesc !!}</div>
                            @endif
                            @if ($hasSections)
                                @foreach ($page->sections as $section)
                                    <div class="section-block" style="margin-bottom: 1.5rem;">
                                        @if ($section->title)
                                            <h2 style="font-size: 1.5rem; font-weight: 700; color: #1e293b; margin-bottom: 0.75rem; text-decoration: underline;">
                                                {{ $locale === 'en' ? $section->title_en ?? $section->title : $section->title }}
                                            </h2>
                                        @endif
                                        @if ($section->description)
                                            <div class="profile-section-desc" style="color: #475569; line-height: 1.75; font-size: 1rem;">{!! $locale === 'en' ? $section->description_en ?? $section->description : $section->description !!}</div>
                                        @endif
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    @endif

                    {{-- Charts Section (below description/images) --}}
                    @if ($hasCharts)
                        <div class="sdm-container-box" style="max-width:64rem;margin:2rem auto;">
                            <div style="padding: 1.5rem 2rem; border-bottom: 2px solid #000;">
                                <h3 style="font-size: 1.75rem; font-weight: 800; color: #000; margin: 0; text-transform: uppercase; letter-spacing: -0.025em;">
                                    {{ $locale === 'en' ? $page->subtitle_en ?? $page->subtitle : $page->subtitle ?? 'SUMBER DAYA MANUSIA' }}
                                </h3>
                            </div>
                            
                            <div class="sdm-layout" id="sdm-charts-container">
                                @php
                                    $chartIndex = 0;
                                @endphp
                                @foreach ($chartData as $chartKey => $chart)
                                    @if (isset($chart['labels']) && is_array($chart['labels']) && count($chart['labels']) > 0)
                                        <div class="chart-card" data-chart-key="{{ $chartKey }}"
                                            data-chart-type="{{ $chart['type'] ?? 'bar' }}">
                                            <h4>Grafik {{ $chart['title'] ?? ucwords(str_replace('_', ' ', $chart['field'] ?? $chartKey)) }}
                                            </h4>
                                            <div style="height:350px;position:relative">
                                                <canvas id="chart-{{ $page->id }}-{{ $chartIndex }}"></canvas>
                                            </div>
                                        </div>
                                        @php
                                            $chartIndex++;
                                        @endphp
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </section>
        @endif

        {{-- Bottom Page Navigation --}}
        @if ($totalPages > 1)
            <section class="profile-section" style="padding-top: 0;">
                <div class="container">
                    <div class="page-nav">
                        @for ($i = 1; $i <= $totalPages; $i++)
                            <a href="{{ request()->url() }}?page={{ $i }}"
                                class="page-nav-btn{{ $currentPageIndex + 1 === $i ? ' active' : '' }}">
                                {{ $i }}
                            </a>
                        @endfor
                    </div>
                </div>
            </section>
        @endif
    @else
        <section class="profile-section">
            <div class="container text-center py-16">
                <p class="text-gray-400">{{ __('cms.profile.public_empty') }}</p>
            </div>
        </section>
    @endif

    {{-- Login Modal (if guest) --}}
    @if(isset($requiresLoginModal) && $requiresLoginModal)
        @include('partials.login_modal', [
            'loginModalPreviews'  => $loginModalPreviews ?? [],
            'loginModalPreview'   => $loginModalPreview ?? null,
            'loginModalRoomNames' => $loginModalRoomNames ?? [],
            'loginModalRoomName'  => $loginModalRoomName ?? null,
            'loginModalPrompt'    => $loginModalPrompt ?? null
        ])
    @endif

@endsection

@push('scripts')
@endpush

@push('scripts')
    @if ($currentPage && $currentPage->type === 'sdm_chart' && $currentPage->chart_data)
        @php
            $chartData = $currentPage->chart_data;
            $chartColors = [
                '#36c5f0',
                '#0a0b1e',
                '#85d7ff',
                '#2eb67d',
                '#174e93',
                '#10b981',
                '#06b6d4',
                '#6366f1',
            ];
            $chartDataJson = json_encode($chartData);
        @endphp
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const chartData = {!! $chartDataJson !!};
                const chartColors = ['#36c5f0', '#0a0b1e', '#85d7ff', '#2eb67d', '#174e93', '#10b981', '#06b6d4', '#6366f1'];
                const pageId = '{{ $currentPage->id }}';

                // Plugin to draw total in center of doughnut
                const centerTextPlugin = {
                    id: 'centerText',
                    beforeDraw: function(chart) {
                        if (chart.config.type !== 'doughnut') return;
                        const width = chart.width,
                            height = chart.height,
                            ctx = chart.ctx;
                        ctx.restore();
                        const fontSize = (height / 160).toFixed(2);
                        ctx.font = "bold " + fontSize + "em sans-serif";
                        ctx.textBaseline = "middle";
                        const text = chart.data.datasets[0].data.reduce((a, b) => a + b, 0).toString();
                        const textX = Math.round((width - ctx.measureText(text).width) / 2);
                        const textY = height / 2;
                        ctx.fillStyle = '#1e293b';
                        ctx.fillText(text, textX, textY);
                        ctx.save();
                    }
                };
                Chart.register(centerTextPlugin);

                let chartIndex = 0;
                Object.keys(chartData).forEach(function(key) {
                    const chart = chartData[key];
                    if (!chart.labels || chart.labels.length === 0) return;

                    const canvasId = 'chart-' + pageId + '-' + chartIndex;
                    const canvas = document.getElementById(canvasId);
                    if (!canvas) return;

                    const isDoughnut = chart.type === 'pie' || chart.type === 'doughnut';
                    const colors = chartColors;

                    if (isDoughnut) {
                        new Chart(canvas.getContext('2d'), {
                            type: 'doughnut',
                            data: {
                                labels: chart.labels,
                                datasets: [{
                                    data: chart.data,
                                    backgroundColor: colors,
                                    borderWidth: 4,
                                    borderColor: '#ffffff',
                                    hoverOffset: 10
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                cutout: '70%',
                                plugins: {
                                    legend: {
                                        position: 'bottom',
                                        labels: {
                                            usePointStyle: true,
                                            padding: 20,
                                            font: { size: 12, weight: '500' },
                                            color: '#64748b'
                                        }
                                    },
                                    tooltip: {
                                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                                        padding: 12,
                                        cornerRadius: 8,
                                        displayColors: true
                                    }
                                }
                            }
                        });
                    } else {
                        new Chart(canvas.getContext('2d'), {
                            type: 'bar',
                            data: {
                                labels: chart.labels,
                                datasets: [{
                                    label: 'Jumlah',
                                    data: chart.data,
                                    backgroundColor: '#36c5f0',
                                    borderRadius: 8,
                                    borderSkipped: false,
                                    barThickness: 40
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { display: false },
                                    tooltip: {
                                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                                        padding: 12,
                                        cornerRadius: 8
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: { stepSize: 1, color: '#94a3b8', font: { size: 10 } },
                                        grid: { color: '#e2e8f0', borderDash: [5, 5], drawBorder: false }
                                    },
                                    x: {
                                        ticks: { color: '#94a3b8', font: { size: 10 } },
                                        grid: { display: false, drawBorder: false }
                                    }
                                }
                            }
                        });
                    }
                    chartIndex++;
                });
            });
        </script>
    @endif
@endpush
