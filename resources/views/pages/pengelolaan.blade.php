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

        /* Flowchart & Cards */
        .flowchart-box {
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            border-radius: 1rem;
            padding: 2.5rem 2rem;
            color: white;
            margin-bottom: 2rem;
            box-shadow: 0 15px 30px rgba(30,58,138,0.2);
            position: relative;
            overflow: hidden;
        }
        .flowchart-title { font-size: 1.5rem; font-weight: 800; margin-bottom: 1.5rem; text-transform: uppercase; letter-spacing: 0.05em; }
        .flowchart-steps { display: flex; flex-direction: column; gap: 1rem; position: relative; z-index: 1; }
        .flow-step {
            background: rgba(255,255,255,0.95);
            color: #1e293b;
            padding: 1.25rem 1.5rem;
            border-radius: 0.75rem;
            border-left: 6px solid #f59e0b;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .flow-step h4 { font-size: 1.1rem; font-weight: 700; margin: 0 0 0.35rem; color: #0f172a; }
        .flow-step p { font-size: 0.95rem; margin: 0; color: #475569; line-height: 1.6; }

        .btn-download-pdf {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            background: #174E93;
            color: white;
            padding: 0.85rem 1.75rem;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 0.95rem;
            text-decoration: none;
            transition: all 0.2s;
            margin-top: 1rem;
            margin-bottom: 2.5rem;
            box-shadow: 0 4px 12px rgba(23, 78, 147, 0.2);
        }
        .btn-download-pdf:hover { background: #123b70; color: white; transform: translateY(-1px); }

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

        /* Checkmark List */
        .check-list {
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
        }
        .check-item {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            font-size: 0.98rem;
            color: #334155;
            line-height: 1.5;
            padding: 0.75rem 1rem;
            background: white;
            border-radius: 0.5rem;
            border: 1px solid #f1f5f9;
            box-shadow: 0 2px 4px rgba(0,0,0,0.01);
        }
        .check-icon {
            color: #10b981;
            flex: 0 0 20px;
            margin-top: 2px;
        }

        /* Quote Box */
        .quote-box {
            position: relative;
            background: linear-gradient(to right, #f8fafc, #f1f5f9);
            border-left: 6px solid #174E93;
            padding: 2rem 2.5rem;
            border-radius: 0 1rem 1rem 0;
            margin: 2.5rem 0;
            font-style: italic;
            color: #334155;
            font-size: 1.15rem;
            line-height: 1.7;
        }
        .quote-icon {
            position: absolute;
            top: -15px;
            left: 20px;
            background: #174E93;
            color: white;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            font-style: normal;
        }

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
            .quote-box { padding: 1.25rem 1.5rem; }
        }
    </style>
@endpush

@section('content')
    @php
        $title = $currentPage ? ($locale === 'en' ? ($currentPage->title_en ?? $currentPage->title) : $currentPage->title) : ($locale === 'en' ? ($feature->name_en ?? $feature->name) : $feature->name);
        $content = $currentPage ? ($locale === 'en' ? ($currentPage->description_en ?? $currentPage->description) : $currentPage->description) : ($locale === 'en' ? ($feature->content_en ?? $feature->content) : $feature->content);
        $date = ($currentPage->created_at ?? $feature->updated_at ?? now())->translatedFormat('d F Y');
        $type = $currentPage ? $currentPage->type : 'penyusutan';
        $views = $currentPage ? $currentPage->views : 0;
        $shares = $currentPage ? $currentPage->shares : 0;
        $publishedAt = ($currentPage->published_at ?? $currentPage->created_at ?? $feature->updated_at ?? now())->translatedFormat('d F Y');
        $updatedAt = ($currentPage->updated_at ?? $feature->updated_at ?? now())->translatedFormat('d F Y');

        // Fallback default rich text content if DB is empty
        if (!$currentPage && empty(trim(strip_tags($content)))) {
            // $content = '<p>' . ($locale === 'en' ? 'Archive management at the Bandung Sustainable Archive Depot (DABB) includes a series of systematic processes to ensure the preservation, security, and availability of archives as the collective memory of the nation and an authentic and reliable source of information.' : 'Pengelolaan arsip di Depot Arsip Berkelanjutan Bandung (DABB) mencakup serangkaian proses sistematis guna menjamin pelestarian, keamanan, dan ketersediaan arsip sebagai memori kolektif bangsa serta sumber informasi yang autentik dan terpercaya.') . '</p>';
        }

        // Icons array for variety in cards
        $availableIcons = [
            'clipboard' => '<svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>',
            'archive' => '<svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>',
            'book' => '<svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>',
            'calendar' => '<svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>',
            'map' => '<svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>',
            'document' => '<svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>',
            'lock' => '<svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>',
            'database' => '<svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/></svg>',
            'users' => '<svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>',
            'globe' => '<svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h.095c.5 0 .905-.405.905-.905 0-.714.211-1.412.608-2.006L17 11h-1a2 2 0 01-2-2V7a2 2 0 00-2-2H8M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
            'tag' => '<svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>',
            'folder' => '<svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>',
            'check' => '<svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>',
            'star' => '<svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>'
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
            <div class="pub-container">
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

                    {{-- DYNAMIC SUB-TYPE SECTIONS --}}
                    @if($currentPage && is_array($currentPage->extra_data))

                        {{-- 2. PENYIMPANAN --}}
                        @if($type === 'penyimpanan')
                            @if(!empty($currentPage->extra_data['prinsip_penyimpanan']) || !empty($currentPage->extra_data['prinsip_list']) || !empty($currentPage->extra_data['prinsip_title']) || !empty($currentPage->extra_data['prinsip_desc']))
                                @php
                                    $pTitle = $locale === 'en' && !empty($currentPage->extra_data['prinsip_title_en'])
                                        ? $currentPage->extra_data['prinsip_title_en']
                                        : ($currentPage->extra_data['prinsip_title'] ?? ($locale === 'en' ? 'Storage Principles' : 'Prinsip Penyimpanan Arsip Statis'));
                                    $pDesc = $locale === 'en' && !empty($currentPage->extra_data['prinsip_desc_en'])
                                        ? $currentPage->extra_data['prinsip_desc_en']
                                        : ($currentPage->extra_data['prinsip_desc'] ?? ($currentPage->extra_data['prinsip_penyimpanan'] ?? ''));
                                @endphp
                                <h3 class="service-subtitle">
                                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $pTitle }}
                                </h3>
                                <div class="service-box border-l-4 border-l-[#f59e0b] bg-amber-50/30">
                                    @if(!empty($pDesc))
                                        <p class="text-gray-700 leading-relaxed font-medium mb-3">{!! nl2br(e($pDesc)) !!}</p>
                                    @endif

                                    @if(!empty($currentPage->extra_data['prinsip_list']) && is_array($currentPage->extra_data['prinsip_list']))
                                        <ul class="space-y-3 {{ !empty($pDesc) ? 'border-t border-amber-200/60 pt-3' : '' }}">
                                            @foreach($currentPage->extra_data['prinsip_list'] as $pItem)
                                                @php
                                                    $itemTitle = $locale === 'en' && !empty($pItem['title_en']) ? $pItem['title_en'] : ($pItem['title'] ?? '');
                                                    $itemDesc = $locale === 'en' && !empty($pItem['desc_en']) ? $pItem['desc_en'] : ($pItem['desc'] ?? ($pItem['text'] ?? ''));
                                                @endphp
                                                @if(!empty($itemTitle) || !empty($itemDesc))
                                                    <li class="flex items-start gap-2.5 text-gray-700">
                                                        <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                        <div>
                                                            @if(!empty($itemTitle))
                                                                <h4 class="font-bold text-gray-800">{{ $itemTitle }}</h4>
                                                            @endif
                                                            @if(!empty($itemDesc))
                                                                <p class="text-gray-600 text-sm leading-relaxed mt-0.5">{!! nl2br(e($itemDesc)) !!}</p>
                                                            @endif
                                                        </div>
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            @endif

                            @if(!empty($currentPage->extra_data['sistem_penyimpanan']) && is_array($currentPage->extra_data['sistem_penyimpanan']))
                                @php
                                    $sTitle = $locale === 'en' && !empty($currentPage->extra_data['sistem_title_en'])
                                        ? $currentPage->extra_data['sistem_title_en']
                                        : ($currentPage->extra_data['sistem_title'] ?? ($locale === 'en' ? 'Storage Systems' : 'Sistem Penyimpanan'));
                                @endphp
                                <h3 class="service-subtitle">
                                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                                    {{ $sTitle }}
                                </h3>
                                <div class="cards-grid">
                                    @foreach($currentPage->extra_data['sistem_penyimpanan'] as $index => $item)
                                        @php
                                            $itemTitle = $locale === 'en' && !empty($item['title_en']) ? $item['title_en'] : ($item['title'] ?? '');
                                            $itemDesc = $locale === 'en' && !empty($item['desc_en']) ? $item['desc_en'] : ($item['desc'] ?? '');
                                            $icon = !empty($item['icon']) && isset($availableIcons[$item['icon']]) ? $availableIcons[$item['icon']] : $cardIcons[$index % count($cardIcons)];
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

                            @if(!empty($currentPage->extra_data['fasilitas_list']) || !empty($currentPage->extra_data['ruang_list']))
                                @php
                                    $fTitle = $locale === 'en' && !empty($currentPage->extra_data['fasilitas_title_en'])
                                        ? $currentPage->extra_data['fasilitas_title_en']
                                        : ($currentPage->extra_data['fasilitas_title'] ?? ($locale === 'en' ? 'Storage Facilities' : 'Fasilitas Penyimpanan'));
                                    $rTitle = $locale === 'en' && !empty($currentPage->extra_data['ruang_title_en'])
                                        ? $currentPage->extra_data['ruang_title_en']
                                        : ($currentPage->extra_data['ruang_title'] ?? ($locale === 'en' ? 'Storage Rooms' : 'Ruang Penyimpanan'));
                                @endphp
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 my-8">
                                    @if(!empty($currentPage->extra_data['fasilitas_list']) && is_array($currentPage->extra_data['fasilitas_list']))
                                        <div class="service-box m-0">
                                            <h4 class="text-lg font-bold text-[#174E93] mb-4 flex items-center gap-2 border-b border-gray-200 pb-3">
                                                <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                                {{ $fTitle }}
                                            </h4>
                                            <div class="check-list">
                                                @foreach($currentPage->extra_data['fasilitas_list'] as $item)
                                                    @php $text = $locale === 'en' && !empty($item['text_en']) ? $item['text_en'] : ($item['text'] ?? ''); @endphp
                                                    <div class="check-item">
                                                        <svg class="check-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                        <span>{{ $text }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                            @php
                                                $fImages = !empty($currentPage->extra_data['fasilitas_images']) && is_array($currentPage->extra_data['fasilitas_images'])
                                                    ? $currentPage->extra_data['fasilitas_images']
                                                    : (!empty($currentPage->extra_data['fasilitas_image']) ? [$currentPage->extra_data['fasilitas_image']] : []);
                                            @endphp
                                            @if(count($fImages) > 0)
                                                <div class="mt-6 pt-4 border-t border-gray-100">
                                                    <div class="grid grid-cols-1 {{ count($fImages) > 1 ? 'sm:grid-cols-2' : '' }} gap-4">
                                                        @foreach($fImages as $img)
                                                            <div class="rounded-lg overflow-hidden shadow-sm border border-gray-100 bg-gray-50 aspect-video">
                                                                <img src="{{ asset('storage/' . $img) }}" alt="{{ $fTitle }}" class="w-full h-full object-cover">
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                    @if(!empty($currentPage->extra_data['ruang_list']) && is_array($currentPage->extra_data['ruang_list']))
                                        <div class="service-box m-0">
                                            <h4 class="text-lg font-bold text-[#174E93] mb-4 flex items-center gap-2 border-b border-gray-200 pb-3">
                                                <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                                {{ $rTitle }}
                                            </h4>
                                            <div class="check-list">
                                                @foreach($currentPage->extra_data['ruang_list'] as $item)
                                                    @php $text = $locale === 'en' && !empty($item['text_en']) ? $item['text_en'] : ($item['text'] ?? ''); @endphp
                                                    <div class="check-item">
                                                        <svg class="check-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                        <span>{{ $text }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                            @php
                                                $rImages = !empty($currentPage->extra_data['ruang_images']) && is_array($currentPage->extra_data['ruang_images'])
                                                    ? $currentPage->extra_data['ruang_images']
                                                    : (!empty($currentPage->extra_data['ruang_image']) ? [$currentPage->extra_data['ruang_image']] : []);
                                            @endphp
                                            @if(count($rImages) > 0)
                                                <div class="mt-6 pt-4 border-t border-gray-100">
                                                    <div class="grid grid-cols-1 {{ count($rImages) > 1 ? 'sm:grid-cols-2' : '' }} gap-4">
                                                        @foreach($rImages as $img)
                                                            <div class="rounded-lg overflow-hidden shadow-sm border border-gray-100 bg-gray-50 aspect-video">
                                                                <img src="{{ asset('storage/' . $img) }}" alt="{{ $rTitle }}" class="w-full h-full object-cover">
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endif

                        {{-- 3. PRESERVASI --}}
                        @elseif($type === 'preservasi')
                            @php
                                $pTitle = $locale === 'en' && !empty($currentPage->extra_data['preservasi_title_en'])
                                    ? $currentPage->extra_data['preservasi_title_en']
                                    : ($currentPage->extra_data['preservasi_title'] ?? ($locale === 'en' ? 'Preservation Activities' : 'Kegiatan Preservasi'));
                                $resTitle = $locale === 'en' && !empty($currentPage->extra_data['restorasi_title_en'])
                                    ? $currentPage->extra_data['restorasi_title_en']
                                    : ($currentPage->extra_data['restorasi_title'] ?? ($locale === 'en' ? 'Archive Restoration' : 'Restorasi Arsip'));
                            @endphp
                            @if(!empty($currentPage->extra_data['preservasi_list']) && is_array($currentPage->extra_data['preservasi_list']))
                                <h3 class="service-subtitle">
                                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                    {{ $pTitle }}
                               </h3>
                                <div class="service-box">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        @foreach($currentPage->extra_data['preservasi_list'] as $item)
                                            @php $text = $locale === 'en' && !empty($item['text_en']) ? $item['text_en'] : ($item['text'] ?? ''); @endphp
                                            <div class="check-item m-0 shadow-sm border border-gray-200">
                                                <svg class="check-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                <span>{{ $text }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if(!empty($currentPage->extra_data['restorasi_desc']) || !empty($currentPage->extra_data['restorasi_list']))
                                <div class="flowchart-box my-8" style="background: linear-gradient(135deg, #174E93, #2563eb);">
                                    <div class="flowchart-title flex items-center gap-2">
                                        <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                                        {{ $resTitle }}
                                    </div>
                                    @if(!empty($currentPage->extra_data['restorasi_desc']))
                                        @php
                                            $rDesc = $locale === 'en' && !empty($currentPage->extra_data['restorasi_desc_en'])
                                                ? $currentPage->extra_data['restorasi_desc_en']
                                                : $currentPage->extra_data['restorasi_desc'];
                                        @endphp
                                        <p class="text-white/90 text-lg mb-6 leading-relaxed font-medium">{!! nl2br(e($rDesc)) !!}</p>
                                    @endif

                                    @if(!empty($currentPage->extra_data['restorasi_list']) && is_array($currentPage->extra_data['restorasi_list']))
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                            @foreach($currentPage->extra_data['restorasi_list'] as $item)
                                                @php $text = $locale === 'en' && !empty($item['text_en']) ? $item['text_en'] : ($item['text'] ?? ''); @endphp
                                                <div class="flow-step py-3 px-4 flex items-center gap-3 border-l-4 border-l-[#f59e0b]">
                                                    <svg class="w-5 h-5 text-[#f59e0b] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    <span class="text-gray-800 font-semibold text-sm">{{ $text }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endif

                        {{-- 4. PENGOLAHAN --}}
                        @elseif($type === 'pengolahan')
                            @if(!empty($currentPage->extra_data['pengolahan_list']) && is_array($currentPage->extra_data['pengolahan_list']))
                                @php
                                    $pengolahanTitle = $locale === 'en'
                                        ? (array_key_exists('pengolahan_title_en', $currentPage->extra_data) ? $currentPage->extra_data['pengolahan_title_en'] : 'Processing Stages')
                                        : (array_key_exists('pengolahan_title', $currentPage->extra_data) ? $currentPage->extra_data['pengolahan_title'] : 'Tahapan Pengolahan');
                                @endphp
                                @if(!empty($pengolahanTitle))
                                    <h3 class="service-subtitle">
                                        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        {{ $pengolahanTitle }}
                                    </h3>
                                @endif
                                <div class="flowchart-box">
                                    <div class="flowchart-steps">
                                        @foreach($currentPage->extra_data['pengolahan_list'] as $index => $item)
                                            @php $text = $locale === 'en' && !empty($item['text_en']) ? $item['text_en'] : ($item['text'] ?? ''); @endphp
                                            <div class="flow-step flex items-center gap-4">
                                                <div class="w-10 h-10 rounded-full bg-[#174E93] text-white font-bold flex items-center justify-center shrink-0 text-lg shadow-md">{{ $index + 1 }}</div>
                                                <p class="text-gray-800 font-semibold text-base m-0">{{ $text }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                        {{-- 5. PEMANFAATAN --}}
                        @elseif($type === 'pemanfaatan')
                            @if(!empty($currentPage->extra_data['mekanisme_title']) || !empty($currentPage->extra_data['mekanisme_desc']))
                                @php
                                    $mTitle = $locale === 'en' && !empty($currentPage->extra_data['mekanisme_title_en']) ? $currentPage->extra_data['mekanisme_title_en'] : ($currentPage->extra_data['mekanisme_title'] ?? '');
                                    $mDesc = $locale === 'en' && !empty($currentPage->extra_data['mekanisme_desc_en']) ? $currentPage->extra_data['mekanisme_desc_en'] : ($currentPage->extra_data['mekanisme_desc'] ?? '');
                                @endphp
                                <div class="service-box border-t-4 border-t-[#174E93] shadow-md">
                                    @if(!empty($mTitle))
                                        <h4 class="text-xl font-bold text-gray-800 mb-2">{{ $mTitle }}</h4>
                                    @endif
                                    @if(!empty($mDesc))
                                        <p class="text-gray-600 leading-relaxed">{!! nl2br(e($mDesc)) !!}</p>
                                    @endif
                                </div>
                            @endif

                            @if(!empty($currentPage->extra_data['pemanfaatan_quote']))
                                @php
                                    $quote = $locale === 'en' && !empty($currentPage->extra_data['pemanfaatan_quote_en']) ? $currentPage->extra_data['pemanfaatan_quote_en'] : $currentPage->extra_data['pemanfaatan_quote'];
                                @endphp
                                <div class="quote-box shadow-md">
                                    <div class="quote-icon">“</div>
                                    <p class="m-0 font-medium">{!! nl2br(e($quote)) !!}</p>
                                </div>
                            @endif

                            @if(!empty($currentPage->extra_data['akses_list']) && is_array($currentPage->extra_data['akses_list']))
                                @php
                                    $aksesTitle = $locale === 'en'
                                        ? (array_key_exists('akses_title_en', $currentPage->extra_data) ? $currentPage->extra_data['akses_title_en'] : 'Access & Utilization Services')
                                        : (array_key_exists('akses_title', $currentPage->extra_data) ? $currentPage->extra_data['akses_title'] : 'Layanan Akses & Pemanfaatan');
                                @endphp
                                @if(!empty($aksesTitle))
                                    <h3 class="service-subtitle">
                                        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/></svg>
                                        {{ $aksesTitle }}
                                    </h3>
                                @endif
                                <div class="cards-grid">
                                    @foreach($currentPage->extra_data['akses_list'] as $index => $item)
                                        @php
                                            $itemTitle = $locale === 'en' && !empty($item['title_en']) ? $item['title_en'] : ($item['title'] ?? '');
                                            $itemDesc = $locale === 'en' && !empty($item['desc_en']) ? $item['desc_en'] : ($item['desc'] ?? '');
                                            $icon = !empty($item['icon']) && isset($availableIcons[$item['icon']]) ? $availableIcons[$item['icon']] : $cardIcons[$index % count($cardIcons)];
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

                        {{-- 6. PENJANGKAUAN --}}
                        @elseif($type === 'penjangkauan')
                            @if(!empty($currentPage->extra_data['kegiatan_list']) && is_array($currentPage->extra_data['kegiatan_list']))
                                @php
                                    $kegiatanTitle = $locale === 'en'
                                        ? (!empty($currentPage->extra_data['kegiatan_title_en']) ? $currentPage->extra_data['kegiatan_title_en'] : ($currentPage->extra_data['kegiatan_title'] ?? 'Outreach Programs & Activities'))
                                        : (!empty($currentPage->extra_data['kegiatan_title']) ? $currentPage->extra_data['kegiatan_title'] : 'Program & Kegiatan Penjangkauan');
                                @endphp
                                <h3 class="service-subtitle">
                                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    {{ $kegiatanTitle }}
                                </h3>
                                <div class="cards-grid">
                                    @foreach($currentPage->extra_data['kegiatan_list'] as $index => $item)
                                        @php
                                            $itemTitle = $locale === 'en' && !empty($item['title_en']) ? $item['title_en'] : ($item['title'] ?? '');
                                            $itemDesc = $locale === 'en' && !empty($item['desc_en']) ? $item['desc_en'] : ($item['desc'] ?? '');
                                            $icon = !empty($item['icon']) && isset($availableIcons[$item['icon']]) ? $availableIcons[$item['icon']] : $cardIcons[$index % count($cardIcons)];
                                            $buttonLabel = $locale === 'en' && !empty($item['button_label_en']) ? $item['button_label_en'] : ($item['button_label'] ?? 'Kunjungi');
                                            $buttonUrl = $item['button_url'] ?? '';
                                            $hasButton = !empty($buttonUrl) && $buttonUrl !== '#';
                                        @endphp
                                        <div class="service-card">
                                            <div class="card-icon">{!! $icon !!}</div>
                                            <div class="card-info">
                                                <h4 class="{{ $hasButton ? 'pr-20 sm:pr-24' : '' }}">{{ $itemTitle }}</h4>
                                                <p>{!! nl2br(e($itemDesc)) !!}</p>
                                            </div>
                                            @if($hasButton)
                                                <a href="{{ $buttonUrl }}" target="_blank" class="absolute top-6 right-6 inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-[#174E93] hover:bg-blue-800 text-white text-xs font-semibold rounded-lg transition-colors shadow-sm">
                                                    <span>{{ $buttonLabel }}</span>
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                </a>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                        {{-- 7. AKUISISI --}}
                        @elseif($type === 'akuisisi')
                            @if(!empty($currentPage->extra_data['tahapan_list']) && is_array($currentPage->extra_data['tahapan_list']))
                                @php
                                    $tahapanTitle = $locale === 'en'
                                        ? (!empty($currentPage->extra_data['tahapan_title_en']) ? $currentPage->extra_data['tahapan_title_en'] : ($currentPage->extra_data['tahapan_title'] ?? 'Acquisition Stages & Procedures'))
                                        : (!empty($currentPage->extra_data['tahapan_title']) ? $currentPage->extra_data['tahapan_title'] : 'Tahapan & Prosedur Akuisisi');
                                @endphp
                                <h3 class="service-subtitle">
                                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                                    {{ $tahapanTitle }}
                                </h3>
                                <div class="flowchart-box">
                                    <div class="flowchart-steps">
                                        @foreach($currentPage->extra_data['tahapan_list'] as $index => $item)
                                            @php
                                                $itemTitle = $locale === 'en' && !empty($item['title_en']) ? $item['title_en'] : ($item['title'] ?? '');
                                                $itemDesc = $locale === 'en' && !empty($item['desc_en']) ? $item['desc_en'] : ($item['desc'] ?? '');
                                            @endphp
                                            <div class="flow-step">
                                                <div class="flex items-center gap-3 mb-2">
                                                    <span class="w-8 h-8 rounded-full bg-[#174E93] text-white font-bold flex items-center justify-center text-sm shadow-sm">{{ $index + 1 }}</span>
                                                    <h4>{{ $itemTitle }}</h4>
                                                </div>
                                                <p class="pl-11">{!! nl2br(e($itemDesc)) !!}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                        @endif

                    @endif

                    {{-- GALLERY IMAGES --}}
                    @if($currentPage && !empty($currentPage->images) && is_array($currentPage->images))
                        <h3 class="service-subtitle">
                            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            {{ $locale === 'en' ? 'Documentation & Gallery' : 'Dokumentasi & Galeri' }}
                        </h3>
                        <div class="gallery-grid">
                            @foreach($currentPage->images as $img)
                                <div class="gallery-item" @click="openLightbox = true; activeImage = '{{ asset('storage/' . $img) }}'">
                                    <img src="{{ asset('storage/' . $img) }}" alt="Galeri Pengelolaan">
                                    <div class="gallery-overlay">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- PDF FILE DOWNLOAD --}}
                    @if($currentPage && is_array($currentPage->extra_data) && !empty($currentPage->extra_data['file']))
                        @php
                            $filePath = $currentPage->extra_data['file'];
                            $fullPath = public_path('storage/' . $filePath);
                            $storagePath = storage_path('app/public/' . $filePath);
                            $fileSizeBytes = Storage::disk('public')->exists($filePath) ? Storage::disk('public')->size($filePath) : (file_exists($fullPath) ? filesize($fullPath) : (file_exists($storagePath) ? filesize($storagePath) : 0));
                            if ($fileSizeBytes >= 1048576) {
                                $fileSizeStr = round($fileSizeBytes / 1048576, 1) . ' MB';
                            } elseif ($fileSizeBytes > 0) {
                                $fileSizeStr = round($fileSizeBytes / 1024, 0) . ' KB';
                            } else {
                                $fileSizeStr = '0 KB';
                            }
                            $customFileName = !empty($currentPage->extra_data['file_name']) ? $currentPage->extra_data['file_name'] : basename($filePath);
                        @endphp
                        <div class="pt-4 border-t border-gray-100 mt-6">
                            <a href="{{ asset('storage/' . $filePath) }}" target="_blank" class="btn-download-pdf">
                                <svg width="22" height="22" fill="currentColor" viewBox="0 0 24 24"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg>
                                <span>{{ $customFileName }} ({{ $fileSizeStr }})</span>
                            </a>
                        </div>
                    @endif
                    @else
                        <div class="p-8 text-center bg-gray-50 rounded-2xl border border-gray-200 my-8">
                            <p class="text-gray-500 text-lg font-medium">{{ app()->getLocale() == 'en' ? 'No archive management content available yet.' : 'Belum ada konten pengelolaan yang tersedia.' }}</p>
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
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            </a>
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->fullUrl()) }}" target="_blank" class="share-btn btn-li" title="Share on LinkedIn" onclick="trackShare()">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                            </a>
                            <a href="https://wa.me/?text={{ urlencode($title . ' ' . request()->fullUrl()) }}" target="_blank" class="share-btn btn-wa" title="Share on WhatsApp" onclick="trackShare()">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.98 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.263-1.643a11.822 11.822 0 005.783 1.513h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
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
                        <a href="/publikasi/berita" class="btn-sidebar-more">{{ __('home.layanan_publik.see_more_news') }}</a>
                    </div>

                    {{-- Pameran Arsip --}}
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
                        <a href="/pameran/tetap" class="btn-sidebar-more">{{ __('home.layanan_publik.see_more_exhibitions') }}</a>
                    </div>

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
                title: '{{ $locale === 'en' ? 'Link Copied!' : 'Tautan Disalin!' }}',
                text: '{{ $locale === 'en' ? 'The link has been copied to your clipboard.' : 'Tautan telah berhasil disalin.' }}',
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
            alert('Tautan disalin: ' + url);
            trackShare();
        });
    }

    function trackShare() {
        @if($currentPage)
        fetch('{{ route('pengelolaan.share.increment', $currentPage->id) }}', {
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
