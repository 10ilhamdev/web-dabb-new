@extends('layouts.guest')

@section('title', $feature->name . ' — ' . config('app.name'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
    <link rel="stylesheet" href="{{ asset('css/feature-page.css') }}">
    <link rel="stylesheet" href="{{ asset('cms_rte/runtime/guest_richtexteditor_content.css') }}">
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
            overflow: hidden;
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
            gap: 2rem;
            background: white;
            border-radius: 1.25rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border: 1px solid #edf2f7;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            transition: transform 0.3s;
        }
        .news-card:hover {
            transform: translateY(-5px);
        }
        .news-img-wrap {
            flex: 0 0 300px;
            height: 200px;
            border-radius: 1rem;
            overflow: hidden;
        }
        .news-img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .news-content {
            flex: 1;
        }
        .news-date {
            font-size: 0.8rem;
            color: #a0aec0;
            margin-bottom: 0.5rem;
            display: block;
        }
        .news-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 1rem;
            line-height: 1.3;
        }
        .news-desc {
            color: #4a5568;
            line-height: 1.6;
            font-size: 0.95rem;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

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
                <div class="news-list">
                    <div class="news-card">
                        @php
                            $newsImg = $currentPage->images ? $currentPage->images[0] : null;
                        @endphp
                        @if($newsImg)
                        <div class="news-img-wrap">
                            <img src="{{ asset('storage/' . $newsImg) }}" alt="{{ $locale === 'en' ? $currentPage->title_en ?? $currentPage->title : $currentPage->title }}">
                        </div>
                        @endif
                        <div class="news-content">
                            <span class="news-date">{{ ($currentPage->published_at ?? $currentPage->created_at)->translatedFormat('d F Y') }}</span>
                            <h2 class="news-title">{{ $locale === 'en' ? $currentPage->title_en ?? $currentPage->title : $currentPage->title }}</h2>
                            <div class="news-desc richtext-guest-view">
                                {!! $locale === 'en' ? $currentPage->description_en ?? $currentPage->description : $currentPage->description !!}
                            </div>
                            @if($currentPage->link_url)
                                <a href="{{ $currentPage->link_url }}" class="page-link-btn" style="margin-top: 1rem;">
                                    {{ $currentPage->link_text ?? __('home.publication.read_more') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            {{-- Galeri Layout --}}
            @if($currentPage->type === 'galeri')
                <div class="gallery-grid">
                    @if($currentPage->images)
                        @foreach($currentPage->images as $img)
                        <div class="gallery-item">
                            @if(Str::endsWith($img, ['.mp4', '.webm', '.ogg']))
                                <video src="{{ asset('storage/' . $img) }}" controls></video>
                            @else
                                <img src="{{ asset('storage/' . $img) }}" alt="Gallery Image">
                            @endif
                            <div class="gallery-overlay">
                                <span class="gallery-title">{{ $locale === 'en' ? $currentPage->title_en ?? $currentPage->title : $currentPage->title }}</span>
                            </div>
                        </div>
                        @endforeach
                    @endif
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

@endsection
