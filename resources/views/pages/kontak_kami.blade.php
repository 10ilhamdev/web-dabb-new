@extends('layouts.guest')

@section('title', ($locale === 'en' ? ($currentPage->title_en ?? $currentPage->title ?? $feature->name_en ?? $feature->name) : ($currentPage->title ?? $feature->name)) . ' — ' . config('app.name'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
    <link rel="stylesheet" href="{{ asset('css/feature-page.css') }}">
    <link rel="stylesheet" href="{{ asset('cms_rte/runtime/guest_richtexteditor_content.css?v=' . (file_exists(public_path('cms_rte/runtime/guest_richtexteditor_content.css')) ? filemtime(public_path('cms_rte/runtime/guest_richtexteditor_content.css')) : time())) }}">
    <style>
        .pub-hero {
            position: relative;
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),
                url('/image/background.png') center 35%/cover no-repeat;
            color: #fff;
            padding: 48px 0;
            min-height: 160px;
            display: flex;
            align-items: center;
        }
        .pub-hero h1 {
            font-family: 'Poppins', sans-serif;
            font-size: 32px;
            font-weight: 800;
            margin: 0;
            letter-spacing: 1px;
            color: #fff;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }
        .pub-section {
            padding: 48px 0;
            background: #fff;
        }

        .pub-container {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 2.5rem;
            align-items: start;
        }
        .pub-container.no-sidebar {
            grid-template-columns: 1fr;
        }

        .detail-main {
            grid-column: 1;
            min-width: 0;
        }

        .news-sidebar {
            grid-column: 2;
            position: sticky;
            top: 140px;
            max-height: calc(100vh - 160px);
            overflow-y: auto;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .news-sidebar::-webkit-scrollbar {
            display: none;
        }

        .detail-title {
            font-size: 2.25rem;
            font-weight: 800;
            color: #1a202c;
            line-height: 1.2;
            margin-bottom: 0.5rem;
        }

        .detail-subtitle {
            font-size: 1.25rem;
            font-weight: 600;
            color: #4a5568;
            margin-bottom: 1.5rem;
        }

        .meta-info {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            font-size: 0.9rem;
            color: #718096;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid #edf2f7;
            flex-wrap: wrap;
        }
        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .meta-item svg {
            width: 18px;
            height: 18px;
        }

        .detail-content {
            font-size: 1.05rem;
            color: #2d3748;
            line-height: 1.8;
            margin-bottom: 3rem;
        }
        .detail-content img {
            border-radius: 1rem;
            margin: 2rem 0;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        /* Service Specific Styles */
        .service-subtitle {
            font-size: 1.35rem;
            font-weight: 700;
            color: #1a202c;
            margin: 2.5rem 0 1.25rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #edf2f7;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .service-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 1.75rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
        }

        /* Cards Grid */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
            margin: 2rem 0;
        }
        @media(max-width: 768px) { .cards-grid { grid-template-columns: 1fr; } }
        .service-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            padding: 1.75rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
            display: flex;
            gap: 1.25rem;
            align-items: flex-start;
            transition: all 0.2s;
            position: relative;
        }
        .service-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.06); border-color: #cbd5e1; }
        .card-icon {
            width: 52px;
            height: 52px;
            border-radius: 0.75rem;
            background: #eff6ff;
            color: #174E93;
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 52px;
        }
        .card-info {
            flex: 1;
            min-width: 0;
        }
        .card-info h4 { font-size: 1.1rem; font-weight: 700; color: #1e293b; margin: 0 0 0.5rem; }
        .card-info p { font-size: 0.92rem; color: #64748b; margin: 0; line-height: 1.5; }

        /* Gallery Grid */
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin: 2rem 0;
        }
        @media(max-width: 640px) { .gallery-grid { grid-template-columns: repeat(2, 1fr); } }
        .gallery-item {
            aspect-ratio: 4/3;
            border-radius: 0.75rem;
            overflow: hidden;
            background: #edf2f7;
            cursor: pointer;
            position: relative;
            group: group;
        }
        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        .gallery-item:hover img { transform: scale(1.08); }
        .gallery-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0,0,0,0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.2s;
            color: white;
        }
        .gallery-item:hover .gallery-overlay { opacity: 1; }

        /* Share Section */
        .share-section {
            padding-top: 2rem;
            border-top: 1px solid #edf2f7;
            margin-top: 3rem;
        }
        .share-label {
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 1rem;
            display: block;
        }
        .share-buttons {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
        }
        .share-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            transition: transform 0.2s;
        }
        .share-btn:hover { transform: scale(1.1); }
        .btn-copy { background: #718096; }
        .btn-x { background: #000000; }
        .btn-fb { background: #1877F2; }
        .btn-li { background: #0077B5; }
        .btn-wa { background: #25D366; }

        .footer-meta {
            margin-top: 2rem;
            font-size: 0.85rem;
            color: #a0aec0;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        /* Sidebar */
        .sidebar-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #174E93;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #174E93;
            text-transform: uppercase;
        }
        .popular-news-item {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.25rem;
            padding-bottom: 1.25rem;
            border-bottom: 1px solid #edf2f7;
        }
        .popular-news-img {
            flex: 0 0 80px;
            height: 60px;
            border-radius: 0.5rem;
            overflow: hidden;
            background: #edf2f7;
        }
        .popular-news-img img { width: 100%; height: 100%; object-fit: cover; }
        .popular-news-info { flex: 1; }
        .popular-news-title {
            font-size: 0.85rem;
            font-weight: 600;
            line-height: 1.4;
            color: #2d3748;
            margin-bottom: 0.25rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .popular-news-date { font-size: 0.75rem; color: #a0aec0; }
        .btn-sidebar-more {
            display: block;
            text-align: center;
            background: #edf2f7;
            color: #4a5568;
            padding: 0.75rem;
            border-radius: 0.5rem;
            font-weight: 600;
            font-size: 0.85rem;
            text-decoration: none;
            transition: all 0.2s;
            margin-top: 1rem;
        }
        .btn-sidebar-more:hover { background: #e2e8f0; color: #1a202c; }

        @media (max-width: 1024px) {
            .pub-container { grid-template-columns: 1fr 240px; }
        }
        @media (max-width: 991px) {
            .pub-container { grid-template-columns: 1fr; gap: 2rem; }
            .detail-main { grid-column: 1 / -1; }
            .news-sidebar {
                grid-column: 1 / -1;
                position: static !important;
                max-height: none !important;
                overflow: visible !important;
                min-width: 0 !important;
            }
        }
        @media (max-width: 480px) {
            .gallery-grid { grid-template-columns: 1fr; }
        }
    </style>
@endpush

@section('content')
    @php
        $title = $currentPage ? ($locale === 'en' ? ($currentPage->title_en ?? $currentPage->title) : $currentPage->title) : ($locale === 'en' ? ($feature->name_en ?? $feature->name) : $feature->name);
        $content = $currentPage ? ($locale === 'en' ? ($currentPage->description_en ?? $currentPage->description) : $currentPage->description) : ($locale === 'en' ? ($feature->content_en ?? $feature->content) : $feature->content);
        $date = ($currentPage->created_at ?? $feature->updated_at ?? now())->translatedFormat('d F Y');
        $type = $currentPage ? $currentPage->type : 'kontak';
        $views = $currentPage ? $currentPage->views : 0;
        $shares = $currentPage ? $currentPage->shares : 0;
        $publishedAt = ($currentPage->published_at ?? $currentPage->created_at ?? $feature->updated_at ?? now())->translatedFormat('d F Y');
        $updatedAt = ($currentPage->updated_at ?? $feature->updated_at ?? now())->translatedFormat('d F Y');

        // Available Icons
        $availableIcons = [
            'phone' => '<svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>',
            'message' => '<svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>',
            'mail' => '<svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>',
            'map' => '<svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.243-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>',
            'building' => '<svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>',
            'clock' => '<svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
            'globe' => '<svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>',
        ];
        $cardIcons = array_values($availableIcons);
    @endphp

    {{-- Breadcrumb --}}
    <div class="feature-breadcrumb">
        <div class="container">
            @if ($feature->parent)
                <a href="{{ url($feature->parent->path ?? '#') }}">
                    {{ $locale === 'en' ? ($feature->parent->name_en ?? $feature->parent->name) : $feature->parent->name }}
                </a>
                <span class="sep">/</span>
            @endif
            <span class="current">
                {{ $locale === 'id' ? $feature->name : ($feature->name_en ?? $feature->name) }}
            </span>
        </div>
    </div>

    {{-- Hero --}}
    <div class="pub-hero">
        <div class="container">
            <h1>{{ strtoupper($title) }}</h1>
        </div>
    </div>

    {{-- Sub-Pages Navigation Pill Bar --}}
    @if($pages->count() > 1)
    <div class="bg-white border-b border-gray-200 sticky top-[80px] z-30 shadow-sm">
        <div class="container">
            <div class="flex items-center gap-2 overflow-x-auto py-3.5 scrollbar-none">
                @foreach($pages as $index => $p)
                    @php
                        $pTitle = $locale === 'en' ? ($p->title_en ?? $p->title) : $p->title;
                        $isActive = ($currentPage && $currentPage->id === $p->id);
                    @endphp
                    <a href="{{ route('feature.page', ['feature' => $feature->id, 'pageNum' => $index + 1]) }}"
                       class="px-5 py-2.5 rounded-full text-sm font-semibold whitespace-nowrap transition-all {{ $isActive ? 'bg-[#174E93] text-white shadow-md' : 'bg-gray-100 hover:bg-gray-200 text-gray-700' }}">
                        {{ $pTitle }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <section class="pub-section" x-data="{ openLightbox: false, activeImage: '' }">
        <div class="container">
            @php
                $hasSidebar = ($popularNews && $popularNews->isNotEmpty()) || ($pameranArsip && $pameranArsip->isNotEmpty());
            @endphp
            <div class="pub-container{{ !$hasSidebar ? ' no-sidebar' : '' }}">
                {{-- Main Detail --}}
                <div class="detail-main">
                    <h1 class="detail-title">{{ $title }}</h1>

                    <div class="meta-info">
                        <div class="meta-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                            <span>{{ __('home.layanan_publik.tim') }}</span>
                        </div>
                        <div class="meta-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            <span>{{ $updatedAt }}</span>
                        </div>
                        <div class="meta-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            <span>{{ $views }} {{ __('home.layanan_publik.views') }}</span>
                        </div>
                        <div class="meta-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" /></svg>
                            <span id="share-count-header">{{ $shares }} {{ __('home.layanan_publik.shares') }}</span>
                        </div>
                    </div>

                    @if($currentPage)
                        {{-- Description Content --}}
                        <div class="detail-content richtext-guest-view">
                            {!! $content !!}
                        </div>

                        {{-- DYNAMIC EXTRA DATA SECTIONS --}}
                        @if(is_array($currentPage->extra_data))

                            {{-- 0. Top Cards (Kartu Highlight Utama) --}}
                            @if(!empty($currentPage->extra_data['top_cards']) && is_array($currentPage->extra_data['top_cards']))
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                                    @foreach($currentPage->extra_data['top_cards'] as $index => $item)
                                        @php
                                            $itemTitle = $locale === 'en' && !empty($item['title_en']) ? $item['title_en'] : ($item['title'] ?? '');
                                            $itemDesc = $locale === 'en' && !empty($item['subtitle_en']) ? $item['subtitle_en'] : ($item['subtitle'] ?? '');
                                            $iconKey = $item['icon'] ?? 'map';
                                            $icon = isset($availableIcons[$iconKey]) ? $availableIcons[$iconKey] : $availableIcons['map'];
                                        @endphp
                                        <div class="bg-[#0d3b84] text-white rounded-2xl p-6 shadow-xl flex flex-col items-center text-center transition-transform hover:-translate-y-1 border border-blue-800">
                                            <div class="text-blue-200 mb-3 flex items-center justify-center w-14 h-14 bg-white/10 rounded-full">
                                                {!! str_replace('width="26" height="26"', 'width="32" height="32"', $icon) !!}
                                            </div>
                                            <span class="text-sm font-medium text-blue-100 mb-1">{!! nl2br(e($itemDesc)) !!}</span>
                                            <h4 class="text-2xl font-bold text-white m-0">{!! nl2br(e($itemTitle)) !!}</h4>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            {{-- 1. Informasi Kontak & Alamat --}}
                            @php
                                $alamatTitleEn = !empty($currentPage->extra_data['alamat_section_title_en']) ? $currentPage->extra_data['alamat_section_title_en'] : (!empty($currentPage->extra_data['alamat_section_title']) ? $currentPage->extra_data['alamat_section_title'] : 'Contact Information & Address');
                                $alamatTitleId = !empty($currentPage->extra_data['alamat_section_title']) ? $currentPage->extra_data['alamat_section_title'] : 'Informasi Kontak & Alamat';
                                $alamatTitle = $locale === 'en' ? $alamatTitleEn : $alamatTitleId;

                                $alamat = $locale === 'en' && !empty($currentPage->extra_data['alamat_lengkap_en']) ? $currentPage->extra_data['alamat_lengkap_en'] : ($currentPage->extra_data['alamat_lengkap'] ?? '');
                                $telepon = $currentPage->extra_data['telepon'] ?? '';
                                $whatsapp = $currentPage->extra_data['whatsapp'] ?? '';
                                $email = $currentPage->extra_data['email'] ?? '';
                                $instagram = $currentPage->extra_data['instagram'] ?? '';
                                $twitter = $currentPage->extra_data['twitter'] ?? '';
                                $facebook = $currentPage->extra_data['facebook'] ?? '';
                                $youtube = $currentPage->extra_data['youtube'] ?? '';
                                $hasKontakInfo = !empty($alamat) || !empty($telepon) || !empty($whatsapp) || !empty($email) || !empty($instagram) || !empty($twitter) || !empty($facebook) || !empty($youtube);
                            @endphp

                            @if($hasKontakInfo)
                                <h3 class="service-subtitle">
                                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    {{ $alamatTitle }}
                                </h3>
                                <div class="service-box border-l-4 border-l-[#174E93] shadow-sm space-y-6 bg-blue-50/20">
                                    @if(!empty($alamat))
                                        <div class="flex items-start gap-3 p-4 bg-white rounded-xl border border-gray-100 shadow-sm">
                                            <div class="p-2.5 bg-blue-50 text-[#174E93] rounded-lg shrink-0 mt-0.5">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.243-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            </div>
                                            <div>
                                                <h4 class="font-bold text-gray-800 text-base mb-1">{{ __('home.kontak_kami.address') }}</h4>
                                                <p class="text-gray-600 leading-relaxed m-0 text-sm">{!! nl2br(e($alamat)) !!}</p>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        @if(!empty($telepon))
                                            <div class="flex items-center gap-3 p-3.5 bg-white rounded-xl border border-gray-100 shadow-sm hover:border-blue-200 transition-all">
                                                <div class="p-2 bg-blue-50 text-blue-600 rounded-lg shrink-0">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                                </div>
                                                <div>
                                                    <span class="block text-[11px] font-bold text-gray-400 uppercase">{{ __('home.kontak_kami.phone') }}</span>
                                                    <a href="tel:{{ $telepon }}" class="font-semibold text-gray-800 hover:text-[#174E93] text-sm">{{ $telepon }}</a>
                                                </div>
                                            </div>
                                        @endif

                                        @if(!empty($whatsapp))
                                            <div class="flex items-center gap-3 p-3.5 bg-white rounded-xl border border-gray-100 shadow-sm hover:border-emerald-200 transition-all">
                                                <div class="p-2 bg-emerald-50 text-emerald-600 rounded-lg shrink-0">
                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347"/></svg>
                                                </div>
                                                <div>
                                                    <span class="block text-[11px] font-bold text-gray-400 uppercase">WhatsApp</span>
                                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $whatsapp) }}" target="_blank" class="font-semibold text-gray-800 hover:text-emerald-600 text-sm">{{ $whatsapp }}</a>
                                                </div>
                                            </div>
                                        @endif

                                        @if(!empty($email))
                                            <div class="flex items-center gap-3 p-3.5 bg-white rounded-xl border border-gray-100 shadow-sm hover:border-amber-200 transition-all">
                                                <div class="p-2 bg-amber-50 text-amber-600 rounded-lg shrink-0">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                                </div>
                                                <div>
                                                    <span class="block text-[11px] font-bold text-gray-400 uppercase">Email</span>
                                                    <a href="mailto:{{ $email }}" class="font-semibold text-gray-800 hover:text-amber-600 text-sm">{{ $email }}</a>
                                                </div>
                                            </div>
                                        @endif

                                        @if(!empty($instagram))
                                            <div class="flex items-center gap-3 p-3.5 bg-white rounded-xl border border-gray-100 shadow-sm hover:border-pink-200 transition-all">
                                                <div class="p-2 bg-pink-50 text-pink-600 rounded-lg shrink-0">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3V2z"/></svg>
                                                </div>
                                                <div>
                                                    <span class="block text-[11px] font-bold text-gray-400 uppercase">Instagram</span>
                                                    <a href="{{ $instagram }}" target="_blank" class="font-semibold text-gray-800 hover:text-pink-600 text-sm truncate block max-w-[200px]">{{ str_replace(['https://instagram.com/', 'https://www.instagram.com/'], '@', $instagram) }}</a>
                                                </div>
                                            </div>
                                        @endif

                                        @if(!empty($twitter))
                                            <div class="flex items-center gap-3 p-3.5 bg-white rounded-xl border border-gray-100 shadow-sm hover:border-gray-300 transition-all">
                                                <div class="p-2 bg-gray-100 text-gray-800 rounded-lg shrink-0">
                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                                </div>
                                                <div>
                                                    <span class="block text-[11px] font-bold text-gray-400 uppercase">X / Twitter</span>
                                                    <a href="{{ $twitter }}" target="_blank" class="font-semibold text-gray-800 hover:text-gray-900 text-sm truncate block max-w-[200px]">{{ str_replace(['https://twitter.com/', 'https://x.com/'], '@', $twitter) }}</a>
                                                </div>
                                            </div>
                                        @endif

                                        @if(!empty($facebook))
                                            <div class="flex items-center gap-3 p-3.5 bg-white rounded-xl border border-gray-100 shadow-sm hover:border-blue-200 transition-all">
                                                <div class="p-2 bg-blue-50 text-blue-600 rounded-lg shrink-0">
                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                                </div>
                                                <div>
                                                    <span class="block text-[11px] font-bold text-gray-400 uppercase">Facebook</span>
                                                    <a href="{{ $facebook }}" target="_blank" class="font-semibold text-gray-800 hover:text-blue-600 text-sm truncate block max-w-[200px]">{{ str_replace(['https://facebook.com/', 'https://www.facebook.com/'], '', $facebook) }}</a>
                                                </div>
                                            </div>
                                        @endif

                                        @if(!empty($youtube))
                                            <div class="flex items-center gap-3 p-3.5 bg-white rounded-xl border border-gray-100 shadow-sm hover:border-red-200 transition-all md:col-span-2">
                                                <div class="p-2 bg-red-50 text-red-600 rounded-lg shrink-0">
                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.5 12 3.5 12 3.5s-7.505 0-9.377.55a3.016 3.016 0 0 0-2.122 2.136C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.55 9.376.55 9.376.55s7.505 0 9.377-.55a3.016 3.016 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                                                </div>
                                                <div>
                                                    <span class="block text-[11px] font-bold text-gray-400 uppercase">YouTube</span>
                                                    <a href="{{ $youtube }}" target="_blank" class="font-semibold text-gray-800 hover:text-red-600 text-sm truncate block max-w-[400px]">{{ str_replace(['https://youtube.com/', 'https://www.youtube.com/'], '', $youtube) }}</a>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            {{-- 2. Jadwal / Jam Operasional --}}
                            @php
                                $jamTitleEn = !empty($currentPage->extra_data['jam_section_title_en']) ? $currentPage->extra_data['jam_section_title_en'] : (!empty($currentPage->extra_data['jam_section_title']) ? $currentPage->extra_data['jam_section_title'] : 'Operating & Service Hours');
                                $jamTitleId = !empty($currentPage->extra_data['jam_section_title']) ? $currentPage->extra_data['jam_section_title'] : 'Jadwal & Jam Operasional';
                                $jamTitle = $locale === 'en' ? $jamTitleEn : $jamTitleId;

                                $jamDesc = $locale === 'en' && !empty($currentPage->extra_data['jam_operasional_desc_en']) ? $currentPage->extra_data['jam_operasional_desc_en'] : ($currentPage->extra_data['jam_operasional_desc'] ?? '');
                                $jamList = $currentPage->extra_data['jam_operasional_list'] ?? [];
                            @endphp

                            @if(!empty($jamDesc) || !empty($jamList))
                                <h3 class="service-subtitle">
                                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $jamTitle }}
                                </h3>
                                <div class="service-box border-l-4 border-l-[#f59e0b] shadow-sm space-y-4 bg-amber-50/20">
                                    @if(!empty($jamDesc))
                                        <p class="text-gray-700 font-medium leading-relaxed m-0">{!! nl2br(e($jamDesc)) !!}</p>
                                    @endif

                                    @if(!empty($jamList) && is_array($jamList))
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                                            @foreach($jamList as $item)
                                                @php
                                                    $hari = $locale === 'en' && !empty($item['hari_en']) ? $item['hari_en'] : ($item['hari'] ?? '');
                                                    $jam = $item['jam'] ?? '';
                                                @endphp
                                                @if(!empty($hari) || !empty($jam))
                                                    <div class="p-3.5 bg-white rounded-xl border border-gray-100 shadow-sm flex items-center justify-between gap-4">
                                                        <div class="flex items-center gap-2.5">
                                                            <div class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></div>
                                                            <span class="font-bold text-gray-800 text-sm">{{ $hari }}</span>
                                                        </div>
                                                        <span class="px-3 py-1 bg-amber-50 text-amber-800 font-semibold text-xs rounded-lg border border-amber-100 whitespace-nowrap">{{ $jam }}</span>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endif

                            {{-- 3. Kartu Informasi / Layanan (cards) --}}
                            @if(!empty($currentPage->extra_data['cards']) && is_array($currentPage->extra_data['cards']))
                                @php
                                    $cardsTitleEn = !empty($currentPage->extra_data['cards_section_title_en']) ? $currentPage->extra_data['cards_section_title_en'] : (!empty($currentPage->extra_data['cards_section_title']) ? $currentPage->extra_data['cards_section_title'] : 'Service Channels & Inquiries');
                                    $cardsTitleId = !empty($currentPage->extra_data['cards_section_title']) ? $currentPage->extra_data['cards_section_title'] : 'Saluran Layanan & Pengaduan';
                                    $cardsTitle = $locale === 'en' ? $cardsTitleEn : $cardsTitleId;
                                @endphp
                                <h3 class="service-subtitle">
                                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                    {{ $cardsTitle }}
                                </h3>
                                <div class="cards-grid">
                                    @foreach($currentPage->extra_data['cards'] as $index => $item)
                                        @php
                                            $itemTitle = $locale === 'en' && !empty($item['title_en']) ? $item['title_en'] : ($item['title'] ?? '');
                                            $itemDesc = $locale === 'en' && !empty($item['subtitle_en']) ? $item['subtitle_en'] : ($item['subtitle'] ?? '');
                                            $iconKey = $item['icon'] ?? 'phone';
                                            $icon = isset($availableIcons[$iconKey]) ? $availableIcons[$iconKey] : $cardIcons[$index % count($cardIcons)];
                                        @endphp
                                        <div class="service-card">
                                            <div class="card-icon">{!! $icon !!}</div>
                                            <div class="card-info">
                                                <h4>{{ $itemTitle }}</h4>
                                                <p>{!! nl2br(e($itemDesc)) !!}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                        @endif

                        {{-- GALLERY IMAGES --}}
                        @if(!empty($currentPage->images) && is_array($currentPage->images))
                            <h3 class="service-subtitle">
                                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                {{ __('home.kontak_kami.gallery') }}
                            </h3>
                            <div class="gallery-grid">
                                @foreach($currentPage->images as $img)
                                    <div class="gallery-item" @click="openLightbox = true; activeImage = '{{ asset('storage/' . $img) }}'">
                                        <img src="{{ asset('storage/' . $img) }}" alt="Galeri Kontak Kami">
                                        <div class="gallery-overlay">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                    @else
                        <div class="p-8 text-center bg-gray-50 rounded-2xl border border-gray-200 my-8">
                            <p class="text-gray-500 text-lg font-medium">{{ __('home.kontak_kami.empty') }}</p>
                        </div>
                    @endif

                    {{-- Bagikan / Share Widget --}}
                    <div class="share-section">
                        <span class="share-label">{{ __('home.layanan_publik.share') }}</span>
                        <div class="share-buttons">
                            <button class="share-btn btn-copy" onclick="copyToClipboard()" title="Copy Link">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" /></svg>
                            </button>
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode($title) }}" target="_blank" class="share-btn btn-x" title="Share on X" onclick="trackShare()">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                            </a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}" target="_blank" class="share-btn btn-fb" title="Share on Facebook" onclick="trackShare()">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.6-5.373-12-12-12s-12 5.4-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            </a>
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->fullUrl()) }}" target="_blank" class="share-btn btn-li" title="Share on LinkedIn" onclick="trackShare()">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                            </a>
                            <a href="https://wa.me/?text={{ urlencode($title . ' ' . request()->fullUrl()) }}" target="_blank" class="share-btn btn-wa" title="Share on WhatsApp" onclick="trackShare()">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347"/></svg>
                            </a>
                        </div>

                        <div class="footer-meta">
                            <div class="text-[13px] text-gray-600 font-medium">{{ __('home.layanan_publik.seen') }} : {{ $views }}</div>
                            <div class="text-[13px] text-gray-600 font-medium">{{ __('home.layanan_publik.published_at') }} : {{ $publishedAt }}</div>
                            <div class="text-[13px] text-gray-600 font-medium">{{ __('home.layanan_publik.updated_at') }} : {{ $updatedAt }}</div>
                        </div>
                    </div>
                </div>

                {{-- Sidebar --}}
                <aside class="news-sidebar">

                    {{-- Popular News --}}
                    @if($popularNews && $popularNews->isNotEmpty())
                    @php
                        $newsFeature = \App\Models\Feature::where('page_type', 'publication')->where('path', 'like', '%berita%')->first();
                        $newsLink = $newsFeature ? url($newsFeature->path) : '/dabb/berita';
                    @endphp
                    <div class="sidebar-block mb-10">
                        <h2 class="sidebar-title">{{ __('home.layanan_publik.popular_news') }}</h2>
                        <div class="popular-news-list">
                            @foreach($popularNews as $pn)
                                @php
                                    $pnTitle = $locale === 'en' ? ($pn->title_en ?? $pn->title) : $pn->title;
                                    $pnImg = $pn->images ? $pn->images[0] : null;
                                @endphp
                                <a href="{{ route('publication.detail', ['path' => ltrim($feature->path, '/'), 'id' => $pn->id]) }}" class="popular-news-item">
                                    @if($pnImg)
                                    <div class="popular-news-img">
                                        <img src="{{ asset('storage/' . $pnImg) }}" alt="{{ $pnTitle }}">
                                    </div>
                                    @endif
                                    <div class="popular-news-info">
                                        <h4 class="popular-news-title">{{ $pnTitle }}</h4>
                                        <div class="flex items-center gap-3">
                                            <span class="popular-news-date">{{ ($pn->published_at ?? $pn->created_at)->translatedFormat('d M Y') }}</span>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                        <a href="{{ $newsLink }}" class="btn-sidebar-more">{{ __('home.layanan_publik.see_more_news') }}</a>
                    </div>
                    @endif

                    {{-- Pameran Arsip --}}
                    @if($pameranArsip && $pameranArsip->isNotEmpty())
                    @php
                        $firstExhibition = \App\Models\Feature::where('parent_id', 3)->where('is_active', true)->orderBy('order')->first();
                        $exhibitionLink = $firstExhibition ? url($firstExhibition->path) : '#';
                    @endphp
                    <div class="sidebar-block mb-10">
                        <h2 class="sidebar-title">{{ __('home.layanan_publik.archive_exhibition') }}</h2>
                        <div class="popular-news-list">
                            @foreach($pameranArsip as $pa)
                                <a href="{{ $pa->link }}" class="popular-news-item">
                                    @if($pa->image)
                                    <div class="popular-news-img">
                                        <img src="{{ $pa->image }}" alt="{{ $pa->title }}">
                                    </div>
                                    @endif
                                    <div class="popular-news-info">
                                        <h4 class="popular-news-title">{{ $pa->title }}</h4>
                                        <div class="flex items-center gap-3">
                                            <span class="popular-news-date">{{ $pa->date->translatedFormat('d M Y') }}</span>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                        <a href="{{ $exhibitionLink }}" class="btn-sidebar-more">{{ __('home.layanan_publik.see_more_exhibitions') }}</a>
                    </div>
                    @endif

                </aside>
            </div>
        </div>

        {{-- Lightbox Modal --}}
        <div x-cloak x-show="openLightbox" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4" @keydown.escape.window="openLightbox = false">
            <div class="relative max-w-4xl max-h-[90vh] overflow-hidden rounded-xl bg-black shadow-2xl" @click.away="openLightbox = false">
                <button @click="openLightbox = false" class="absolute top-4 right-4 z-10 flex h-10 w-10 items-center justify-center rounded-full bg-black/60 text-white hover:bg-black/80 focus:outline-none">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
                <img :src="activeImage" class="max-h-[85vh] max-w-full object-contain">
            </div>
        </div>
    </section>

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
<script>
    function copyToClipboard() {
        var url = window.location.href;
        navigator.clipboard.writeText(url).then(function() {
            Swal.fire({
                icon: 'success',
                title: '{{ __('home.kontak_kami.link_copied') }}',
                text: '{{ __('home.kontak_kami.link_copied_desc') }}',
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end',
                timerProgressBar: true
            });
            trackShare();
        }).catch(function(err) {
            const el = document.createElement('textarea');
            el.value = url;
            document.body.appendChild(el);
            el.select();
            document.execCommand('copy');
            document.body.removeChild(el);
            alert('{{ __('home.kontak_kami.link_copied') }} ' + url);
            trackShare();
        });
    }

    function trackShare() {
        @if($currentPage)
        fetch('{{ route('kontak_kami.share.increment', $currentPage->id) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const counterHeader = document.getElementById('share-count-header');
                if (counterHeader) {
                    counterHeader.innerText = data.shares + ' {{ __('home.layanan_publik.shares') }}';
                }
            }
        })
        .catch(error => console.error('Error tracking share:', error));
        @endif
    }
</script>
@endpush
