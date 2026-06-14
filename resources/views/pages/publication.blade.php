@extends('layouts.guest')

@section('title', $feature->name . ' — ' . config('app.name'))

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
        .pub-section-bg {
            background: #f8f9fa;
        }
        
        /* Table Style (Pengumuman) */
        .pub-table-container {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border: 1px solid #edf2f7;
        }
        .pub-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        .pub-table td {
            padding: 1.25rem 1rem;
            border-bottom: 1px solid #edf2f7;
            border-right: 1px solid #edf2f7;
            font-size: 0.95rem;
            color: #4a5568;
            vertical-align: middle;
        }
        .pub-table th {
            background: #174E93;
            color: white;
            padding: 1.25rem 1rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            border-right: 1px solid rgba(255,255,255,0.1);
            border-bottom: 2px solid rgba(0,0,0,0.05);
        }
        .pub-table td:last-child, .pub-table th:last-child { border-right: none; }
        .pub-table tr:last-child td { border-bottom: none; }
        
        .pub-table thead tr:first-child th:first-child { border-top-left-radius: 1rem; }
        .pub-table thead tr:first-child th:last-child { border-top-right-radius: 1rem; }
        .pub-table tbody tr:last-child td:first-child { border-bottom-left-radius: 1rem; }
        .pub-table tbody tr:last-child td:last-child { border-bottom-right-radius: 1rem; }
        .pub-table tr:hover td { background: #f7fafc; }
        
        .pdf-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            background: #718096;
            color: white;
            border-radius: 0.5rem;
            transition: all 0.2s;
        }
        .pdf-btn:hover {
            background: #4a5568;
            transform: scale(1.05);
        }

        /* News Layout (Berita) */
        .news-card {
            display: flex;
            gap: 1.5rem;
            background: white;
            border-radius: 1rem;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
            border: 1px solid #edf2f7;
            box-shadow: 0 2px 10px rgba(0,0,0,0.02);
            transition: all 0.3s ease;
        }
        .news-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            border-color: #174E93;
        }
        .news-img-wrap {
            flex: 0 0 240px;
            height: 160px;
            border-radius: 0.75rem;
            overflow: hidden;
        }
        .news-img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .news-content {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .news-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 0.75rem;
            line-height: 1.4;
        }
        .news-desc {
            color: #718096;
            line-height: 1.6;
            font-size: 0.9rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin-bottom: auto;
        }

        /* Sidebar & Popular News */
        .pub-container {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 2.5rem;
            align-items: start;
        }
        .pub-container.no-sidebar {
            grid-template-columns: 1fr;
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
        }
        .popular-news-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .popular-news-info {
            flex: 1;
        }
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
        .popular-news-date {
            font-size: 0.75rem;
            color: #a0aec0;
        }
        .meta-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            font-size: 0.85rem;
            color: #718096;
            margin-bottom: 0.5rem;
        }
        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }
        .meta-item svg {
            width: 14px;
            height: 14px;
        }

        @media (max-width: 1024px) { .pub-container { grid-template-columns: 1fr 240px; } }
        @media (max-width: 768px) { .pub-container { grid-template-columns: 1fr 180px; } }
        @media (max-width: 640px) { .pub-container { grid-template-columns: 1fr 140px; } }
        @media (max-width: 480px) { .pub-container { grid-template-columns: 1fr 110px; } }

        /* Gallery Grid (Galeri) */
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
        }
        .gallery-item {
            position: relative;
            aspect-ratio: 4/3;
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            cursor: pointer;
        }
        .gallery-item img, .gallery-item video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }
        .gallery-item:hover img {
            transform: scale(1.1);
        }
        .gallery-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(transparent, rgba(0,0,0,0.7));
            opacity: 0;
            transition: opacity 0.3s;
            display: flex;
            align-items: flex-end;
            padding: 1.25rem;
        }
        .gallery-item:hover .gallery-overlay {
            opacity: 1;
        }
        .gallery-title {
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
        }

        /* Pagination */
        .page-nav {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 3rem;
        }
        .page-nav-btn {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.75rem;
            background: white;
            border: 1px solid #edf2f7;
            color: #4a5568;
            font-weight: 600;
            transition: all 0.2s;
        }
        .page-nav-btn:hover, .page-nav-btn.active {
            background: #174E93;
            color: white;
            border-color: #174E93;
        }

        @media (max-width: 768px) {
            .news-card { flex-direction: column; gap: 1rem; }
            .news-img-wrap { flex: 0 0 200px; width: 100%; }
            .pub-table { min-width: 600px !important; }
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
            <span class="current">{{ $locale === 'id' ? $feature->name : $feature->name_en ?? $feature->name }}</span>
        </div>
    </div>

    {{-- Hero --}}
    <div class="pub-hero">
        <div class="container">
            <h1>{{ strtoupper($locale === 'id' ? $feature->name : $feature->name_en ?? $feature->name) }}</h1>
        </div>
    </div>

    @if ($currentPage)
    <section class="pub-section">
        <div class="container">
            
            {{-- Pengumuman Layout --}}
            @if($currentPage->type === 'pengumuman')
                <div>
                    {{-- Search Field --}}
                    <div class="flex justify-end mb-4 items-center gap-2">
                        <form action="{{ request()->url() }}" method="GET" class="flex items-center gap-2">
                            <label class="text-sm font-medium text-gray-600">{{ __('cms.datatable.search_placeholder') === 'Search...' ? 'Search:' : 'Cari:' }}</label>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('cms.datatable.search_placeholder') }}"
                                class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-64">
                        </form>
                    </div>

                    <div class="pub-table-container">
                    <table class="pub-table">
                        <thead>
                            <tr>
                                <th width="60">{{ __('home.publication.col_no') }}</th>
                                <th>{{ __('home.publication.col_title') }}</th>
                                <th width="180">{{ __('home.publication.col_date') }}</th>
                                <th width="100" style="text-align: center;">{{ __('home.publication.col_link') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                // In a real scenario, you might have multiple rows for one "page" if it's an announcement list.
                                // But here we follow the "one record = one content" pattern.
                                // If the user wants a list, they add multiple pages.
                            @endphp
                            @forelse($allPages as $idx => $p)
                                @if($p->type === 'pengumuman')
                                @php
                                    $pDate = ($p->published_at ?? $p->created_at)->translatedFormat('d F Y');
                                    $pTitle = $locale === 'en' ? $p->title_en ?? $p->title : $p->title;
                                @endphp
                                <tr x-transition>
                                    <td>{{ ($allPages->currentPage() - 1) * $allPages->perPage() + $idx + 1 }}</td>
                                    <td class="font-semibold">{{ $pTitle }}</td>
                                    <td>{{ $pDate }}</td>
                                    <td style="text-align: center;">
                                        @if(isset($p->extra_data['file']))
                                            <a href="{{ asset('storage/' . $p->extra_data['file']) }}" target="_blank" class="pdf-btn" title="{{ __('home.publication.view_pdf') }}">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                        @else
                                            <span class="text-gray-300">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-10 text-gray-500">
                                        {{ __('cms.publication.empty') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                </div>
            @endif

            {{-- Berita Layout --}}
            @if($currentPage->type === 'berita')
                @php
                    $hasSidebar = ($popularNews && $popularNews->isNotEmpty());
                @endphp
                <div class="pub-container{{ !$hasSidebar ? ' no-sidebar' : '' }}">
                    {{-- Main Content: News List --}}
                    <div class="news-main">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-xl font-bold text-gray-800">{{ $locale === 'en' ? 'Latest News' : 'Berita Terkini' }}</h2>
                            {{-- Search Field --}}
                            <form action="{{ request()->url() }}" method="GET" class="flex items-center gap-2">
                                <div class="relative">
                                    <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('cms.datatable.search_placeholder') }}"
                                        class="pl-10 pr-4 py-2 border border-gray-300 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-64">
                                    <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="bg-[#F8F9FA] rounded-xl p-6 mb-8 border border-gray-100">
                            <h3 class="text-lg font-bold text-gray-800 mb-2">{{ $locale === 'en' ? 'Welcome to News Portal,' : 'Selamat datang di portal Berita,' }}</h3>
                            <p class="text-sm text-gray-600 leading-relaxed">
                                {{ $locale === 'en' ? 'Get the latest information by following our official social media channels. Find various interesting insights about sustainable archive management, archive literacy, activity agendas, and our latest public services.' : 'Dapatkan informasi terkini dengan mengikuti kanal media sosial resmi Depot Arsip Berkelanjutan Bandung (DABB). Temukan berbagai wawasan menarik mengenai pengelolaan arsip berkelanjutan, edukasi kearsipan, agenda kegiatan, serta layanan publik terbaru dari kami.' }}
                            </p>
                        </div>

                        <div class="news-list">
                            @forelse($allPages as $p)
                                @php
                                    $pDate = ($p->published_at ?? $p->created_at)->translatedFormat('d F Y');
                                    $pTitle = $locale === 'en' ? $p->title_en ?? $p->title : $p->title;
                                    $pImg = $p->images ? $p->images[0] : null;
                                @endphp
                                <div class="news-card">
                                    <div class="news-img-wrap">
                                        @if($pImg)
                                            <img src="{{ asset('storage/' . $pImg) }}" alt="{{ $pTitle }}">
                                        @else
                                            <div class="w-full h-full bg-gray-100 flex items-center justify-center">
                                                <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="news-content">
                                        <div class="meta-info !mb-3">
                                            <div class="meta-item">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="text-gray-400"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                                <span>{{ $pDate }}</span>
                                            </div>
                                            <div class="meta-item">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="text-gray-400"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                                <span>Tim DABB</span>
                                            </div>
                                        </div>
                                        <h3 class="news-title">
                                            <a href="{{ route('publication.detail', ['path' => ltrim($feature->path, '/'), 'id' => $p->id]) }}" class="hover:text-[#174E93] transition-colors">
                                                {{ $pTitle }}
                                            </a>
                                        </h3>
                                        <div class="news-desc">
                                            {{ $locale === 'en' ? ($p->subtitle_en ?? $p->subtitle) : $p->subtitle }}
                                        </div>
                                        <div class="flex items-center justify-between mt-4">
                                            <a href="{{ route('publication.detail', ['path' => ltrim($feature->path, '/'), 'id' => $p->id]) }}" class="text-[#174E93] font-bold text-sm hover:underline">
                                                {{ $locale === 'en' ? 'Read More' : 'Selengkapnya' }} →
                                            </a>
                                            <div class="meta-info !mb-0">
                                                <div class="meta-item">
                                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="text-gray-400"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                                    <span>{{ $p->views }}</span>
                                                </div>
                                                <div class="meta-item">
                                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="text-gray-400"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" /></svg>
                                                    <span>{{ $p->shares }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-center py-10 text-gray-500">{{ __('cms.publication.empty') }}</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- Sidebar --}}
                    <aside class="news-sidebar">
                        @if($popularNews && $popularNews->isNotEmpty())
                        <div class="sidebar-block mb-10">
                            <h2 class="sidebar-title">{{ $locale === 'en' ? 'Popular News' : 'Berita Populer' }}</h2>
                            <div class="popular-news-list">
                                @foreach($popularNews as $pn)
                                    @php
                                        $pnTitle = $locale === 'en' ? $pn->title_en ?? $pn->title : $pn->title;
                                        $pnImg = $pn->images ? $pn->images[0] : null;
                                    @endphp
                                    <a href="{{ route('publication.detail', ['path' => ltrim($feature->path, '/'), 'id' => $pn->id]) }}" class="popular-news-item">
                                        <div class="popular-news-img">
                                            @if($pnImg)
                                                <img src="{{ asset('storage/' . $pnImg) }}" alt="{{ $pnTitle }}">
                                            @else
                                                <div class="w-full h-full bg-gray-100 flex items-center justify-center">
                                                    <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="popular-news-info">
                                            <h4 class="popular-news-title">{{ $pnTitle }}</h4>
                                            <div class="flex items-center gap-3">
                                                <span class="popular-news-date">{{ ($pn->published_at ?? $pn->created_at)->translatedFormat('d M Y') }}</span>
                                                <div class="flex items-center gap-2 text-[10px] text-gray-400">
                                                    <div class="flex items-center gap-1">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                                        <span>{{ $pn->views }}</span>
                                                    </div>
                                                    <div class="flex items-center gap-1">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" /></svg>
                                                        <span>{{ $pn->shares }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </aside>
                </div>
            @endif

            {{-- Galeri Layout --}}
            @if($currentPage->type === 'galeri')
                <div x-data="{ lightbox: false, lightboxUrl: '', lightboxType: 'image' }">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold text-gray-800">{{ $locale === 'en' ? 'Gallery List' : 'Daftar Galeri' }}</h2>
                    </div>

                    <div class="bg-[#F8F9FA] rounded-xl p-6 mb-8 border border-gray-100">
                        <h3 class="text-lg font-bold text-gray-800 mb-2">{{ $locale === 'en' ? 'Welcome to Gallery Portal,' : 'Selamat datang di portal Galeri,' }}</h3>
                        <p class="text-sm text-gray-600 leading-relaxed">
                            {{ $locale === 'en' ? 'Through this lens, we invite you to trace the steps of the Bandung Sustainable Archives Depot (DABB). This page presents a visual recording of archive preservation technical activities to the public services that we dedicate to caring for the collective memory of the nation.' : 'Melalui lensa kamera, kami mengajak Anda menelusuri jejak langkah Depot Arsip Berkelanjutan Bandung (DABB). Halaman ini menyajikan rekaman visual dari aktivitas teknis pelestarian arsip hingga layanan publik yang kami dedikasikan untuk merawat memori kolektif bangsa.' }}
                        </p>
                    </div>

                    <div class="gallery-grid">
                        @if(isset($allGalleryMedia) && count($allGalleryMedia) > 0)
                            @foreach($allGalleryMedia as $img)
                            @php
                                $isVid = Str::endsWith($img, ['.mp4', '.webm', '.ogg']);
                                // Handle full URLs vs storage paths
                                $fullUrl = (Str::startsWith($img, ['http://', 'https://'])) ? $img : asset('storage/' . str_replace('storage/', '', $img));
                            @endphp
                            <div class="gallery-item" @click="lightbox = true; lightboxUrl = '{{ $fullUrl }}'; lightboxType = '{{ $isVid ? 'video' : 'image' }}'">
                                @if($isVid)
                                    <video src="{{ $fullUrl }}"></video>
                                    <div class="absolute inset-0 flex items-center justify-center z-10 pointer-events-none">
                                        <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center border border-white/30 shadow-lg">
                                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.333-5.89a1.5 1.5 0 000-2.538L6.3 2.841z" /></svg>
                                        </div>
                                    </div>
                                @else
                                    <img src="{{ $fullUrl }}" alt="Gallery Image">
                                @endif
                                <div class="gallery-overlay">
                                    <span class="gallery-title">{{ $locale === 'en' ? $currentPage->title_en ?? $currentPage->title : $currentPage->title }}</span>
                                </div>
                            </div>
                            @endforeach
                        @endif
                    </div>

                    {{-- Lightbox Modal --}}
                    <div x-show="lightbox" 
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="fixed inset-0 z-[999] flex items-center justify-center bg-black/90 p-4"
                        @keydown.escape.window="lightbox = false"
                        x-cloak>
                        
                        <button @click="lightbox = false" class="absolute top-6 right-6 text-white hover:text-gray-300 transition-colors z-[1000] p-2 bg-black/50 rounded-full">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>

                        <div class="max-w-6xl w-full h-full flex items-center justify-center" @click.away="lightbox = false">
                            <template x-if="lightboxType === 'image'">
                                <img :src="lightboxUrl" class="max-w-full max-h-full object-contain shadow-2xl rounded-lg">
                            </template>
                            <template x-if="lightboxType === 'video'">
                                <video :src="lightboxUrl" controls autoplay class="max-w-full max-h-full shadow-2xl rounded-lg"></video>
                            </template>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Pagination if multiple pages exist --}}
            @if($allPages->lastPage() > 1)
                <div class="page-nav">
                    {{-- Previous Page Link --}}
                    @if ($allPages->onFirstPage())
                        <span class="page-nav-btn opacity-50 cursor-not-allowed text-gray-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                        </span>
                    @else
                        <a href="{{ $allPages->previousPageUrl() }}" class="page-nav-btn">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                        </a>
                    @endif

                    @for($i = 1; $i <= $allPages->lastPage(); $i++)
                        <a href="{{ $allPages->url($i) }}" class="page-nav-btn {{ $allPages->currentPage() === $i ? 'active' : '' }}">
                            {{ $i }}
                        </a>
                    @endfor

                    {{-- Next Page Link --}}
                    @if ($allPages->hasMorePages())
                        <a href="{{ $allPages->nextPageUrl() }}" class="page-nav-btn">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                        </a>
                    @else
                        <span class="page-nav-btn opacity-50 cursor-not-allowed text-gray-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                        </span>
                    @endif
                </div>
            @endif

        </div>
    </section>
    @else
    <section class="pub-section">
        <div class="container text-center py-20">
            <p class="text-gray-400">{{ __('home.publication.empty') }}</p>
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
