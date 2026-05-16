@extends('layouts.guest')

@section('title', ($locale === 'en' ? ($currentPage->title_en ?? $currentPage->title ?? $feature->name_en ?? $feature->name) : ($currentPage->title ?? $feature->name)) . ' — ' . config('app.name'))

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
            margin: 2rem 0 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #edf2f7;
        }
        .service-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .service-box p { margin: 0 0 0.75rem; }
        .service-box p:last-child { margin: 0; }

        /* Form Styles */
        .service-form {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            margin-bottom: 3rem;
        }
        .form-group {
            display: grid;
            grid-template-columns: 200px 1fr;
            align-items: center;
            gap: 1rem;
        }
        @media(max-width: 768px) {
            .form-group { grid-template-columns: 1fr; align-items: start; gap: 0.5rem; }
        }
        .form-label {
            font-weight: 600;
            color: #2d3748;
            font-size: 0.95rem;
        }
        .form-label .required { color: #e53e3e; }
        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #cbd5e0;
            border-radius: 0.5rem;
            font-size: 0.95rem;
            color: #2d3748;
            background: #fff;
            transition: all 0.2s;
        }
        .form-input:focus, .form-select:focus, .form-textarea:focus {
            border-color: #3182ce;
            box-shadow: 0 0 0 3px rgba(49,130,206,0.15);
            outline: none;
        }
        .form-textarea { min-height: 120px; resize: vertical; }
        .form-file-wrap {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .btn-choose-file {
            background: #edf2f7;
            border: 1px solid #cbd5e0;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
        }
        .file-hint { font-size: 0.8rem; color: #718096; margin-top: 0.25rem; font-weight: 600; }
        .captcha-box {
            display: inline-flex;
            align-items: center;
            justify-content: space-between;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            padding: 0.75rem 1.25rem;
            border-radius: 0.5rem;
            width: 300px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .captcha-left { display: flex; align-items: center; gap: 0.75rem; font-weight: 500; color: #374151; font-size: 0.9rem; }
        .captcha-checkbox { width: 22px; height: 22px; border: 2px solid #cbd5e1; border-radius: 0.25rem; cursor: pointer; }
        .captcha-logo { display: flex; flex-direction: column; align-items: center; font-size: 0.65rem; color: #94a3b8; }
        .captcha-logo img { width: 28px; height: 28px; margin-bottom: 2px; }

        .btn-submit {
            background: #0284c7;
            color: #fff;
            font-weight: 600;
            padding: 0.75rem 2.5rem;
            border-radius: 0.5rem;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            transition: background 0.2s;
            align-self: flex-start;
        }
        .btn-submit:hover { background: #0369a1; }

        /* Calendar Widget */
        .calendar-widget {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            margin-bottom: 2.5rem;
        }
        .calendar-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }
        .calendar-title { font-size: 1.25rem; font-weight: 700; color: #1e293b; }
        .calendar-nav { display: flex; gap: 0.5rem; }
        .calendar-nav button {
            background: #f1f5f9;
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #64748b;
        }
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 0.5rem;
            text-align: center;
        }
        .calendar-day-name { font-weight: 600; font-size: 0.8rem; color: #94a3b8; padding-bottom: 0.5rem; text-transform: uppercase; }
        .calendar-date {
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.95rem;
            color: #334155;
            font-weight: 500;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.2s;
        }
        .calendar-date:hover { background: #f1f5f9; }
        .calendar-date.booked { background: #0f172a; color: #fff; }
        .calendar-date.selected { background: #0284c7; color: #fff; }

        /* Flowchart & Circles */
        .circles-wrapper {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
            margin: 2.5rem 0;
            flex-wrap: wrap;
        }
        .circle-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            flex: 1;
            min-width: 120px;
        }
        .circle-num {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: #2b3a8cb3;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
            box-shadow: 0 4px 10px rgba(43,58,140,0.2);
            border: 3px solid #2b3a8c;
        }
        .circle-text { font-size: 0.85rem; font-weight: 600; color: #334155; line-height: 1.3; }
        .circle-arrow { color: #94a3b8; font-weight: bold; font-size: 1.25rem; align-self: flex-start; margin-top: 18px; }

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
        .flow-step h4 { font-size: 1.05rem; font-weight: 700; margin: 0 0 0.25rem; color: #0f172a; }
        .flow-step p { font-size: 0.9rem; margin: 0; color: #475569; }

        .btn-download-pdf {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            background: #64748b;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            font-size: 0.95rem;
            text-decoration: none;
            transition: background 0.2s;
            margin-bottom: 2.5rem;
        }
        .btn-download-pdf:hover { background: #475569; color: white; }

        /* Cards Grid */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            margin: 2.5rem 0;
        }
        @media(max-width: 768px) { .cards-grid { grid-template-columns: 1fr; } }
        .service-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            display: flex;
            gap: 1rem;
            align-items: flex-start;
        }
        .card-icon {
            width: 48px;
            height: 48px;
            border-radius: 0.5rem;
            background: #eff6ff;
            color: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 48px;
        }
        .card-info h4 { font-size: 1.05rem; font-weight: 700; color: #1e293b; margin: 0 0 0.5rem; }
        .card-info p { font-size: 0.85rem; color: #64748b; margin: 0; line-height: 1.4; }

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

        @media (max-width: 1024px) { .pub-container { grid-template-columns: 1fr; } }
    </style>
@endpush

@section('content')
    @php
        $title = $currentPage ? ($locale === 'en' ? ($currentPage->title_en ?? $currentPage->title) : $currentPage->title) : ($locale === 'en' ? ($feature->name_en ?? $feature->name) : $feature->name);
        $content = $currentPage ? ($locale === 'en' ? ($currentPage->description_en ?? $currentPage->description) : $currentPage->description) : ($locale === 'en' ? ($feature->content_en ?? $feature->content) : $feature->content);
        $date = ($currentPage->created_at ?? $feature->updated_at ?? now())->translatedFormat('d F Y');
        $matchTitle = strtolower($currentPage ? $currentPage->title : $feature->name);

        $views = $currentPage ? $currentPage->views : 0;
        $shares = $currentPage ? $currentPage->shares : 0;
        $publishedAt = ($currentPage && !empty($currentPage->extra_data['auto_today_date'])) ? now()->translatedFormat('d F Y') : ($currentPage->published_at ?? $currentPage->created_at ?? $feature->updated_at ?? now())->translatedFormat('d F Y');
        $updatedAt = ($currentPage && !empty($currentPage->extra_data['auto_today_date'])) ? now()->translatedFormat('d F Y') : ($currentPage->updated_at ?? $feature->updated_at ?? now())->translatedFormat('d F Y');

        // Fallback default rich text content to match reference images perfectly if DB is empty
        if (!$currentPage && empty(trim(strip_tags($content)))) {
            if (str_contains($matchTitle, 'kunjungan') || str_contains($matchTitle, 'penelitian')) {
                $content = '<p>Arsip Nasional Republik Indonesia (ANRI) melalui Depot Arsip Berkelanjutan Bandung memberikan kesempatan kepada masyarakat untuk belajar dan mengenal kearsipan secara langsung. Melalui kegiatan kunjungan edukatif ini, diharapkan masyarakat dapat meningkatkan pemahaman dan kesadaran akan pentingnya arsip sebagai sumber informasi dan sejarah. Kunjungan ini juga menjadi sarana pembelajaran mengenai pengelolaan arsip, pemanfaatan arsip statis, serta penelusuran sumber sejarah yang tersimpan di Depot Arsip ANRI Bandung.</p>';
            } elseif (str_contains($matchTitle, 'laraska') || str_contains($matchTitle, 'restorasi')) {
                $content = '<p>Posisi geografis Indonesia yang berada di garis khatulistiwa serta ancaman ring of fire menjadikan Indonesia sebagai wilayah dengan tingkat kerawanan bencana yang tinggi. Untuk itu, baik bencana hidrometeorologi maupun bencana geologi seperti tektonik dan vulkanik. Bencana hidrometeorologi meliputi banjir, tanah longsor, dan angin puting beliung, sedangkan bencana geologi antara lain gempa bumi dan letusan gunung berapi. Berbagai peristiwa tersebut tidak hanya menimbulkan korban jiwa, tetapi juga berdampak pada kesehatan fisik dan psikologis masyarakat, serta menyebabkan kerusakan infrastruktur, fasilitas umum, kantor pemerintahan, dan pusat layanan publik.<br><br>Dampak bencana paling dirasakan oleh masyarakat dan keluarga sebagai unit terkecil dalam kehidupan berbangsa dan bernegara. Selain gangguan kesehatan dan trauma psikologis, bencana juga berpotensi merusak atau memusnahkan dokumen penting milik pribadi dan keluarga yang memiliki nilai hukum dan administratif.<br><br>Sebagai bentuk kehadiran negara dalam melindungi hak keperdataan masyarakat serta meminimalkan dampak psikologis akibat kehilangan dokumen penting, ANRI melalui program LARASKA (Layanan Restorasi Arsip Keluarga) menyediakan layanan perlindungan dan penyelamatan arsip masyarakat, terdampak bencana. Pelaksanaan layanan ini mengacu pada Peraturan ANRI Nomor 9 Tahun 2019 tentang Standar Pelayanan LARASKA di Lingkungan ANRI. Melalui program ini, masyarakat dapat melakukan perbaikan arsip keluarga secara gratis dengan datang langsung ke kantor DABB di Jl. Raya Derwati, Mekar Jaya, Kec. Rancasari, Kota Bandung.</p>';
            } elseif (str_contains($matchTitle, 'statis') || str_contains($matchTitle, 'arsip')) {
                $content = '<p>Layanan arsip statis adalah penyediaan arsip statis kepada pengguna arsip statis untuk kepentingan pemerintahan, pembangunan, penelitian, dan ilmu pengetahuan untuk kesejahteraan rakyat, sesuai kaidah-kaidah kearsipan demi kemaslahatan bangsa.</p>';
            } elseif (str_contains($matchTitle, 'konsultasi')) {
                $content = '<p>Konsultasi Kearsipan Depot Arsip Berkelanjutan Bandung merupakan layanan pendampingan bagi masyarakat untuk memperoleh informasi dan bimbingan terkait pengelolaan arsip. Melalui layanan ini, pengunjung dapat berkonsultasi mengenai pengelolaan, pemeliharaan, dan pemanfaatan arsip statis, serta penelusuran sumber sejarah yang tersimpan di Depot Arsip ANRI Bandung. Layanan ini bertujuan untuk meningkatkan pemahaman dan kesadaran masyarakat terhadap pentingnya arsip sebagai sumber informasi dan memori kolektif bangsa.</p>';
            } else {
                $content = '<p>Layanan Perpustakaan DABB menyediakan bahan perpustakaan dan referensi untuk mendukung kegiatan pengarsipan dan penelitian. Layanan ini memberikan akses kepada pengguna untuk membaca, meminjam, dan memanfaatkan koleksi yang tersedia sesuai dengan peraturan yang berlaku.</p>';
            }
        }
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
            <a href="{{ url(ltrim($feature->path, '/')) }}">
                {{ $locale === 'id' ? $feature->name : ($feature->name_en ?? $feature->name) }}
            </a>
            @if($currentPage && $currentPage->title !== $feature->name)
                <span class="sep">/</span>
                <span class="current">{{ $title }}</span>
            @endif
        </div>
    </div>

    {{-- Hero --}}
    <div class="pub-hero">
        <div class="container">
            <h1>{{ strtoupper($title) }}</h1>
        </div>
    </div>

    <section class="pub-section">
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
                            <span>{{ $publishedAt }}</span>
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
                        <div class="detail-content richtext-guest-view">
                            {!! $content !!}
                        </div>

                        {{-- Dynamic Service Layouts --}}
                        @if (str_contains($matchTitle, 'kunjungan') || str_contains($matchTitle, 'penelitian'))
                            {{-- Layout 1: Pendaftaran Kunjungan --}}
                            @if(!isset($currentPage->extra_data['show_jadwal']) || $currentPage->extra_data['show_jadwal'] == 1)
                                @php
                                    $titleJadwal = $locale === 'en' && !empty($currentPage->extra_data['title_jadwal_en'])
                                        ? $currentPage->extra_data['title_jadwal_en']
                                        : (!empty($currentPage->extra_data['title_jadwal']) ? $currentPage->extra_data['title_jadwal'] : __('home.layanan_publik.visiting_hours'));
                                @endphp
                                <h3 class="service-subtitle">{{ $titleJadwal }}</h3>
                                <div class="service-box">
                                    @if(!empty($currentPage->extra_data['jadwal_kunjungan']))
                                        @php
                                            $jadwalText = $locale === 'en' && !empty($currentPage->extra_data['jadwal_kunjungan_en'])
                                                ? $currentPage->extra_data['jadwal_kunjungan_en']
                                                : $currentPage->extra_data['jadwal_kunjungan'];
                                        @endphp
                                        {!! nl2br(e($jadwalText)) !!}
                                    @else
                                        {!! __('home.layanan_publik.jadwal_kunjungan_default') !!}
                                    @endif
                                </div>
                            @endif

                            @if(!isset($currentPage->extra_data['show_pengajuan']) || $currentPage->extra_data['show_pengajuan'] == 1)
                                @php
                                    $titlePengajuan = $locale === 'en' && !empty($currentPage->extra_data['title_pengajuan_en'])
                                        ? $currentPage->extra_data['title_pengajuan_en']
                                        : (!empty($currentPage->extra_data['title_pengajuan']) ? $currentPage->extra_data['title_pengajuan'] : __('home.layanan_publik.visiting_app'));
                                @endphp
                                <h3 class="service-subtitle">{{ $titlePengajuan }}</h3>
                                <div class="service-box">
                                    @if(!empty($currentPage->extra_data['pengajuan_kunjungan']))
                                        @php
                                            $pengajuanText = $locale === 'en' && !empty($currentPage->extra_data['pengajuan_kunjungan_en'])
                                                ? $currentPage->extra_data['pengajuan_kunjungan_en']
                                                : $currentPage->extra_data['pengajuan_kunjungan'];
                                        @endphp
                                        {!! nl2br(e($pengajuanText)) !!}
                                    @else
                                        <p>{{ __('home.layanan_publik.pengajuan_kunjungan_default') }}</p>
                                    @endif
                                </div>
                            @endif

                            {{-- Calendar Widget --}}
                            @if(!isset($currentPage->extra_data['show_kalender']) || $currentPage->extra_data['show_kalender'] == 1)
                                @php
                                    $currentYear = now()->year;
                                    $currentMonth = now()->month;

                                    $googleHolidays = array_merge(
                                        \App\Services\GoogleCalendarHolidayService::getHolidays($currentYear - 1),
                                        \App\Services\GoogleCalendarHolidayService::getHolidays($currentYear),
                                        \App\Services\GoogleCalendarHolidayService::getHolidays($currentYear + 1)
                                    );
                                    $customLibur = !empty($currentPage->extra_data['libur_dates']) ? collect($currentPage->extra_data['libur_dates'])->pluck('reason', 'date')->toArray() : [];
                                    $liburDates = array_merge($googleHolidays, $customLibur);

                                    $tutupSlots = !empty($currentPage->extra_data['tutup_slots']) ? collect($currentPage->extra_data['tutup_slots'])->groupBy('date')->toArray() : [];
                                    $kuotaHarian = $currentPage->extra_data['kuota_harian'] ?? (($currentPage->extra_data['kuota_pagi'] ?? 2) + ($currentPage->extra_data['kuota_siang'] ?? 2));
                                @endphp
                                <div class="calendar-widget" id="calendar-widget-anchor">
                                    <div class="calendar-header">
                                        <div class="calendar-title" id="calendar-title-display">{{ now()->translatedFormat('F Y') }}</div>
                                        <div class="calendar-nav">
                                            <button type="button" onclick="changeCalendarMonth(-1)"><svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></button>
                                            <button type="button" onclick="changeCalendarMonth(1)"><svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></button>
                                        </div>
                                    </div>
                                    <div class="calendar-grid" id="calendar-grid-display">
                                        <!-- Rendered by JS -->
                                    </div>
                                    <div class="flex items-center gap-4 mt-4 text-xs text-gray-500 justify-center">
                                        <div class="flex items-center gap-1.5"><span class="w-3 h-3 bg-red-100 rounded-full inline-block border border-red-300"></span> Libur / Tutup</div>
                                        <div class="flex items-center gap-1.5"><span class="w-3 h-3 bg-gray-200 rounded-full inline-block"></span> Penuh (Full)</div>
                                        <div class="flex items-center gap-1.5"><span class="w-3 h-3 bg-yellow-100 rounded-full inline-block border border-yellow-300"></span> Tutup Sebagian</div>
                                        <div class="flex items-center gap-1.5"><span class="w-3 h-3 bg-[#0284c7] rounded-full inline-block"></span> Dipilih</div>
                                    </div>
                                </div>

                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        let currentYear = {{ $currentYear }};
                                        let currentMonth = {{ $currentMonth }}; // 1-12
                                        const liburDates = {!! json_encode($liburDates) !!};
                                        const tutupSlots = {!! json_encode($tutupSlots) !!};
                                        const kuotaHarian = {{ $kuotaHarian }};
                                        const monthNamesEn = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
                                        const monthNamesId = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
                                        const isEn = '{{ $locale }}' === 'en';
                                        const monthNames = isEn ? monthNamesEn : monthNamesId;

                                        function renderCalendar() {
                                            const titleDisplay = document.getElementById('calendar-title-display');
                                            const gridDisplay = document.getElementById('calendar-grid-display');
                                            if (!titleDisplay || !gridDisplay) return;

                                            titleDisplay.innerText = monthNames[currentMonth - 1] + ' ' + currentYear;

                                            const firstDay = new Date(currentYear, currentMonth - 1, 1).getDay(); // 0 for Sunday
                                            const daysInMonth = new Date(currentYear, currentMonth, 0).getDate();

                                            let html = `
                                                <div class="calendar-day-name">Min</div>
                                                <div class="calendar-day-name">Sen</div>
                                                <div class="calendar-day-name">Sel</div>
                                                <div class="calendar-day-name">Rab</div>
                                                <div class="calendar-day-name">Kam</div>
                                                <div class="calendar-day-name">Jum</div>
                                                <div class="calendar-day-name">Sab</div>
                                            `;

                                            for (let i = 0; i < firstDay; i++) {
                                                html += `<div></div>`;
                                            }

                                            for (let d = 1; d <= daysInMonth; d++) {
                                                const monthStr = String(currentMonth).padStart(2, '0');
                                                const dayStr = String(d).padStart(2, '0');
                                                const dateStr = `${currentYear}-${monthStr}-${dayStr}`;

                                                const isLibur = !!liburDates[dateStr];
                                                const liburReason = isLibur ? liburDates[dateStr] : '';
                                                const hasTutup = !!tutupSlots[dateStr];
                                                const tutupInfo = hasTutup ? tutupSlots[dateStr] : [];

                                                let className = 'calendar-date';
                                                let title = '';

                                                if (isLibur) {
                                                    className += ' holiday bg-red-100 text-red-600 font-bold cursor-not-allowed';
                                                    title = (isEn ? 'Holiday: ' : 'Libur: ') + liburReason;
                                                } else if (hasTutup) {
                                                    const tutupSlotTypes = tutupInfo.map(ti => ti.slot);
                                                    const fullSlot = tutupInfo.find(ti => ti.slot === 'full');
                                                    const pagiSlot = tutupInfo.find(ti => ti.slot === 'pagi');
                                                    const siangSlot = tutupInfo.find(ti => ti.slot === 'siang');

                                                    const isFullClosed = (fullSlot && fullSlot.max_quota == 0) ||
                                                                        (tutupSlotTypes.includes('full') && (!fullSlot || fullSlot.max_quota == 0)) ||
                                                                        (tutupSlotTypes.includes('pagi') && (!pagiSlot || pagiSlot.max_quota == 0) && tutupSlotTypes.includes('siang') && (!siangSlot || siangSlot.max_quota == 0));

                                                    if (isFullClosed) {
                                                        className += ' booked bg-gray-200 text-gray-500 line-through cursor-not-allowed';
                                                        title = isEn ? 'Registration Fully Closed' : 'Pendaftaran Ditutup Penuh';
                                                    } else {
                                                        className += ' partial-booked bg-yellow-100 text-yellow-800 cursor-pointer';
                                                        let details = [];
                                                        tutupInfo.forEach(ti => {
                                                            let slotName = ti.slot.charAt(0).toUpperCase() + ti.slot.slice(1);
                                                            let quotaStr = (ti.max_quota && ti.max_quota > 0) ? ` (Kuota Maks: ${ti.max_quota})` : " (Ditutup)";
                                                            details.push(`${slotName}${quotaStr}`);
                                                        });
                                                        title = (isEn ? 'Quota/Time Settings: ' : 'Pengaturan Kuota/Waktu: ') + details.join(', ');
                                                    }
                                                } else {
                                                    className += ' hover:bg-blue-50 cursor-pointer';
                                                    title = (isEn ? 'Available - Daily Max Quota: ' : 'Tersedia - Kuota Maksimal Harian: ') + kuotaHarian + (isEn ? ' Visits' : ' Kunjungan');
                                                }

                                                html += `<div class="${className}" title="${title}" data-date="${dateStr}">${d}</div>`;
                                            }

                                            gridDisplay.innerHTML = html;

                                            gridDisplay.querySelectorAll('.calendar-date').forEach(el => {
                                                el.addEventListener('click', function() {
                                                    if (!this.classList.contains('cursor-not-allowed')) {
                                                        gridDisplay.querySelectorAll('.calendar-date').forEach(item => item.classList.remove('selected'));
                                                        this.classList.add('selected');
                                                        let dtInput = document.getElementById('form_visit_date');
                                                        if (dtInput) {
                                                            dtInput.value = this.getAttribute('data-date');
                                                        }
                                                    } else {
                                                        Swal.fire({
                                                            title: isEn ? 'Information' : 'Informasi',
                                                            text: this.getAttribute('title') || (isEn ? 'Date unavailable' : 'Tanggal tidak tersedia'),
                                                            icon: 'info',
                                                            confirmButtonColor: '#174E93'
                                                        });
                                                    }
                                                });
                                            });
                                        }

                                        window.changeCalendarMonth = function(offset) {
                                            currentMonth += offset;
                                            if (currentMonth > 12) {
                                                currentMonth = 1;
                                                currentYear++;
                                            } else if (currentMonth < 1) {
                                                currentMonth = 12;
                                                currentYear--;
                                            }
                                            renderCalendar();
                                        };

                                        renderCalendar();
                                    });
                                </script>
                            @endif

                            {{-- Form --}}
                            @if(!isset($currentPage->extra_data['show_form']) || $currentPage->extra_data['show_form'] == 1)
                                <form action="#" method="POST" enctype="multipart/form-data" class="service-form" onsubmit="event.preventDefault(); if(typeof grecaptcha !== 'undefined' && !grecaptcha.getResponse()) { Swal.fire({ title: 'Oops!', text: '{{ __('home.layanan_publik.captcha_warning') }}', icon: 'warning', confirmButtonColor: '#174E93' }); return false; } Swal.fire({ title: 'Berhasil!', text: 'Formulir berhasil dikirim!', icon: 'success', confirmButtonColor: '#174E93' });">
                                    @if(!empty($currentPage->extra_data['form_fields']) && is_array($currentPage->extra_data['form_fields']))
                                        @foreach($currentPage->extra_data['form_fields'] as $field)
                                            @php
                                                $fieldId = 'form_' . ($field['id'] ?? uniqid());
                                                $fieldLabel = $locale === 'en' && !empty($field['label_en']) ? $field['label_en'] : ($field['label'] ?? '');
                                                $fieldType = $field['type'] ?? 'text';
                                                $isRequired = !empty($field['required']) && $field['required'] !== 'false' && $field['required'] !== false && $field['required'] != 0;
                                            @endphp
                                            <div class="form-group">
                                                <label class="form-label" for="{{ $fieldId }}">{{ $fieldLabel }} @if($isRequired)<span class="required">*</span>@endif</label>
                                                @if($fieldType === 'textarea')
                                                    <textarea id="{{ $fieldId }}" name="{{ $field['id'] ?? '' }}" class="form-input" rows="3" @if($isRequired) required @endif></textarea>
                                                @elseif($fieldType === 'select')
                                                    @php
                                                        $optionsStr = $locale === 'en' && !empty($field['options_en']) ? $field['options_en'] : ($field['options'] ?? '');
                                                        $optionsArr = array_filter(array_map('trim', explode(',', $optionsStr)));
                                                    @endphp
                                                    <select id="{{ $fieldId }}" name="{{ $field['id'] ?? '' }}" class="form-select" @if($isRequired) required @endif>
                                                        <option value="">{{ __('home.layanan_publik.form_select') }}</option>
                                                        @foreach($optionsArr as $opt)
                                                            <option value="{{ $opt }}">{{ $opt }}</option>
                                                        @endforeach
                                                    </select>
                                                @elseif($fieldType === 'file')
                                                    <div>
                                                        <div class="form-file-wrap">
                                                            <button type="button" class="btn-choose-file" onclick="document.getElementById('{{ $fieldId }}').click()">{{ __('home.layanan_publik.choose_file') }}</button>
                                                            <span id="{{ $fieldId }}_text">{{ __('home.layanan_publik.no_file') }}</span>
                                                            <input type="file" id="{{ $fieldId }}" name="{{ $field['id'] ?? '' }}" class="hidden" onchange="document.getElementById('{{ $fieldId }}_text').innerText = this.files[0] ? this.files[0].name : '{{ __('home.layanan_publik.no_file') }}'" @if($isRequired) required @endif>
                                                        </div>
                                                        @if(!empty($field['options']))
                                                            <div class="file-hint">{{ $locale === 'en' && !empty($field['options_en']) ? $field['options_en'] : $field['options'] }}</div>
                                                        @endif
                                                    </div>
                                                @else
                                                    <input type="{{ $fieldType }}" id="{{ $fieldId }}" name="{{ $field['id'] ?? '' }}" class="form-input" @if($isRequired) required @endif @if($fieldType === 'number') min="1" @endif>
                                                @endif
                                            </div>
                                        @endforeach
                                    @else
                                        {{-- Fallback to default static form if no dynamic fields configured --}}
                                        <div class="form-group">
                                            <label class="form-label" for="form_name">{{ __('home.layanan_publik.form_name') }} <span class="required">*</span></label>
                                            <input type="text" id="form_name" class="form-input" required>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label" for="form_email">{{ __('home.layanan_publik.form_email') }} <span class="required">*</span></label>
                                            <input type="email" id="form_email" class="form-input" required>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label" for="form_phone_office">{{ __('home.layanan_publik.form_phone_office') }}</label>
                                            <input type="text" id="form_phone_office" class="form-input">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label" for="form_phone">{{ __('home.layanan_publik.form_phone') }} <span class="required">*</span></label>
                                            <input type="text" id="form_phone" class="form-input" required>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label" for="form_institution">{{ __('home.layanan_publik.form_institution') }} <span class="required">*</span></label>
                                            <input type="text" id="form_institution" class="form-input" required>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label" for="form_position">{{ __('home.layanan_publik.form_position') }} <span class="required">*</span></label>
                                            <input type="text" id="form_position" class="form-input" required>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label" for="form_visit_date">{{ __('home.layanan_publik.form_visit_date') }} <span class="required">*</span></label>
                                            <input type="date" id="form_visit_date" class="form-input" required>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label" for="form_visit_time">{{ __('home.layanan_publik.form_visit_time') }} <span class="required">*</span></label>
                                            <select id="form_visit_time" class="form-select" required>
                                                <option value="">{{ __('home.layanan_publik.form_select') }}</option>
                                                <option value="pagi">{{ __('home.layanan_publik.form_time_pagi') }}</option>
                                                <option value="siang">{{ __('home.layanan_publik.form_time_siang') }}</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label" for="form_visitor_count">{{ __('home.layanan_publik.form_visitor_count') }} <span class="required">*</span></label>
                                            <input type="number" id="form_visitor_count" class="form-input" required min="1">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label" for="form_visit_purpose">{{ __('home.layanan_publik.form_visit_purpose') }} <span class="required">*</span></label>
                                            <select id="form_visit_purpose" class="form-select" required>
                                                <option value="">{{ __('home.layanan_publik.form_select') }}</option>
                                                <option value="edukasi">{{ __('home.layanan_publik.form_purpose_edukasi') }}</option>
                                                <option value="penelitian">{{ __('home.layanan_publik.form_purpose_penelitian') }}</option>
                                                <option value="kunker">{{ __('home.layanan_publik.form_purpose_kunker') }}</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">{{ __('home.layanan_publik.form_req_letter') }}</label>
                                            <div>
                                                <div class="form-file-wrap">
                                                    <button type="button" class="btn-choose-file" onclick="document.getElementById('surat_file').click()">{{ __('home.layanan_publik.choose_file') }}</button>
                                                    <span id="file_chosen_text">{{ __('home.layanan_publik.no_file') }}</span>
                                                    <input type="file" id="surat_file" class="hidden" onchange="document.getElementById('file_chosen_text').innerText = this.files[0] ? this.files[0].name : '{{ __('home.layanan_publik.no_file') }}'">
                                                </div>
                                                <div class="file-hint">{{ __('home.layanan_publik.file_hint') }}</div>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="form-group">
                                        <label class="form-label">{{ __('home.layanan_publik.form_captcha') }} <span class="required">*</span></label>
                                        <div class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_SITE_KEY', '6LfD3PIbAAAAAJs_eEHvoOl75_83eXSqpPSRFJ_u') }}"></div>
                                    </div>
                                    <button type="submit" class="btn-submit">{{ __('home.layanan_publik.form_submit') }}</button>
                                </form>
                            @endif

                        @elseif (str_contains($matchTitle, 'laraska') || str_contains($matchTitle, 'restorasi'))
                            {{-- Layout 2: LARASKA --}}
                            <h3 class="service-subtitle">{{ __('home.layanan_publik.service_hours') }}</h3>
                            <div class="service-box">
                                @if(is_array($currentPage->extra_data) && array_key_exists('laraska_hours', $currentPage->extra_data))
                                    {!! nl2br(e($currentPage->extra_data['laraska_hours'] ?? '')) !!}
                                @else
                                    {!! __('home.layanan_publik.laraska_hours') !!}
                                @endif
                            </div>

                            {{-- Maklumat Box --}}
                            <div class="p-8 bg-[#1e3a8a] text-white rounded-2xl mb-8 text-center shadow-xl relative overflow-hidden border-4 border-yellow-500">
                                <h3 class="text-2xl font-bold mb-4 tracking-wider text-yellow-400">{{ (is_array($currentPage->extra_data) && array_key_exists('maklumat_title', $currentPage->extra_data)) ? ($currentPage->extra_data['maklumat_title'] ?? '') : __('home.layanan_publik.maklumat_title') }}</h3>
                                <p class="text-lg leading-relaxed mb-6 font-medium">{!! (is_array($currentPage->extra_data) && array_key_exists('maklumat_content', $currentPage->extra_data)) ? nl2br(e($currentPage->extra_data['maklumat_content'] ?? '')) : __('home.layanan_publik.maklumat_content') !!}</p>
                                <div class="text-right text-sm opacity-90 pr-4 font-semibold">
                                    <p>{{ (is_array($currentPage->extra_data) && array_key_exists('maklumat_date', $currentPage->extra_data)) ? ($currentPage->extra_data['maklumat_date'] ?? '') : __('home.layanan_publik.maklumat_date') }}</p>
                                    <p>{{ (is_array($currentPage->extra_data) && array_key_exists('maklumat_director', $currentPage->extra_data)) ? ($currentPage->extra_data['maklumat_director'] ?? '') : __('home.layanan_publik.maklumat_director') }}</p>
                                </div>
                            </div>

                            {{-- Mekanisme Flowchart --}}
                            <h3 class="service-subtitle">{{ __('home.layanan_publik.mechanism') }}</h3>
                            <div class="flowchart-box">
                                <div class="flowchart-title">{{ (is_array($currentPage->extra_data) && array_key_exists('laraska_mech_title', $currentPage->extra_data)) ? ($currentPage->extra_data['laraska_mech_title'] ?? '') : __('home.layanan_publik.laraska_mech_title') }}</div>
                                <div class="flowchart-steps">
                                    @if(isset($currentPage->extra_data['laraska_steps']) && is_array($currentPage->extra_data['laraska_steps']))
                                        @foreach($currentPage->extra_data['laraska_steps'] as $step)
                                            <div class="flow-step">
                                                <h4>{{ app()->getLocale() == 'en' ? ($step['title_en'] ?? $step['title'] ?? '') : ($step['title'] ?? '') }}</h4>
                                                <p>{{ app()->getLocale() == 'en' ? ($step['desc_en'] ?? $step['desc'] ?? '') : ($step['desc'] ?? '') }}</p>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="flow-step">
                                            <h4>{{ (is_array($currentPage->extra_data) && array_key_exists('laraska_step1_title', $currentPage->extra_data)) ? ($currentPage->extra_data['laraska_step1_title'] ?? '') : __('home.layanan_publik.laraska_step1_title') }}</h4>
                                            <p>{{ (is_array($currentPage->extra_data) && array_key_exists('laraska_step1_desc', $currentPage->extra_data)) ? ($currentPage->extra_data['laraska_step1_desc'] ?? '') : __('home.layanan_publik.laraska_step1_desc') }}</p>
                                        </div>
                                        <div class="flow-step">
                                            <h4>{{ (is_array($currentPage->extra_data) && array_key_exists('laraska_step2_title', $currentPage->extra_data)) ? ($currentPage->extra_data['laraska_step2_title'] ?? '') : __('home.layanan_publik.laraska_step2_title') }}</h4>
                                            <p>{{ (is_array($currentPage->extra_data) && array_key_exists('laraska_step2_desc', $currentPage->extra_data)) ? ($currentPage->extra_data['laraska_step2_desc'] ?? '') : __('home.layanan_publik.laraska_step2_desc') }}</p>
                                        </div>
                                        <div class="flow-step">
                                            <h4>{{ (is_array($currentPage->extra_data) && array_key_exists('laraska_step3_title', $currentPage->extra_data)) ? ($currentPage->extra_data['laraska_step3_title'] ?? '') : __('home.layanan_publik.laraska_step3_title') }}</h4>
                                            <p>{{ (is_array($currentPage->extra_data) && array_key_exists('laraska_step3_desc', $currentPage->extra_data)) ? ($currentPage->extra_data['laraska_step3_desc'] ?? '') : __('home.layanan_publik.laraska_step3_desc') }}</p>
                                        </div>
                                        <div class="flow-step">
                                            <h4>{{ (is_array($currentPage->extra_data) && array_key_exists('laraska_step4_title', $currentPage->extra_data)) ? ($currentPage->extra_data['laraska_step4_title'] ?? '') : __('home.layanan_publik.laraska_step4_title') }}</h4>
                                            <p>{{ (is_array($currentPage->extra_data) && array_key_exists('laraska_step4_desc', $currentPage->extra_data)) ? ($currentPage->extra_data['laraska_step4_desc'] ?? '') : __('home.layanan_publik.laraska_step4_desc') }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            @if(!empty($currentPage->extra_data['file']))
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
                                <a href="{{ asset('storage/' . $filePath) }}" target="_blank" class="btn-download-pdf">
                                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg>
                                    <span>{{ $customFileName }} ({{ $fileSizeStr }})</span>
                                </a>
                            @endif

                        @elseif (str_contains($matchTitle, 'statis') || (str_contains($matchTitle, 'arsip') && !str_contains($matchTitle, 'konsultasi')))
                            {{-- Layout 3: Layanan Arsip Statis --}}
                            <h3 class="service-subtitle">{{ __('home.layanan_publik.service_hours') }}</h3>
                            <div class="service-box">
                                @if($currentPage && is_array($currentPage->extra_data) && (array_key_exists('statis_hours', $currentPage->extra_data) || array_key_exists('statis_hours_en', $currentPage->extra_data)))
                                    {!! nl2br(e(app()->getLocale() == 'en' ? ($currentPage->extra_data['statis_hours_en'] ?? $currentPage->extra_data['statis_hours'] ?? '') : ($currentPage->extra_data['statis_hours'] ?? ''))) !!}
                                @else
                                    {!! __('home.layanan_publik.statis_hours') !!}
                                @endif
                            </div>

                            <h3 class="service-subtitle">{{ __('home.layanan_publik.archive_order') }}</h3>
                            <div class="service-box">
                                @if($currentPage && is_array($currentPage->extra_data) && (array_key_exists('statis_order_hours', $currentPage->extra_data) || array_key_exists('statis_order_hours_en', $currentPage->extra_data)))
                                    {!! nl2br(e(app()->getLocale() == 'en' ? ($currentPage->extra_data['statis_order_hours_en'] ?? $currentPage->extra_data['statis_order_hours'] ?? '') : ($currentPage->extra_data['statis_order_hours'] ?? ''))) !!}
                                @else
                                    {!! __('home.layanan_publik.statis_order_hours') !!}
                                @endif
                            </div>

                            @if(($currentPage && isset($currentPage->extra_data['statis_stages']) && is_array($currentPage->extra_data['statis_stages']) && count($currentPage->extra_data['statis_stages']) > 0) || ($currentPage && !isset($currentPage->extra_data['statis_stages'])))
                                <h3 class="service-subtitle">{{ __('home.layanan_publik.stages') }}</h3>
                                <div class="circles-wrapper">
                                    @if(isset($currentPage->extra_data['statis_stages']) && is_array($currentPage->extra_data['statis_stages']))
                                        @foreach($currentPage->extra_data['statis_stages'] as $index => $stage)
                                            <div class="circle-step">
                                                <div class="circle-num">{{ $index + 1 }}</div>
                                                <div class="circle-text">{{ app()->getLocale() == 'en' ? ($stage['title_en'] ?? $stage['title'] ?? '') : ($stage['title'] ?? '') }}</div>
                                            </div>
                                            @if(!$loop->last)
                                                <div class="circle-arrow">➔</div>
                                            @endif
                                        @endforeach
                                    @else
                                        <div class="circle-step">
                                            <div class="circle-num">1</div>
                                            <div class="circle-text">{{ app()->getLocale() == 'en' ? ($currentPage->extra_data['statis_stage1_en'] ?? $currentPage->extra_data['statis_stage1'] ?? __('home.layanan_publik.statis_stage1')) : ($currentPage->extra_data['statis_stage1'] ?? __('home.layanan_publik.statis_stage1')) }}</div>
                                        </div>
                                        <div class="circle-arrow">➔</div>
                                        <div class="circle-step">
                                            <div class="circle-num">2</div>
                                            <div class="circle-text">{{ app()->getLocale() == 'en' ? ($currentPage->extra_data['statis_stage2_en'] ?? $currentPage->extra_data['statis_stage2'] ?? __('home.layanan_publik.statis_stage2')) : ($currentPage->extra_data['statis_stage2'] ?? __('home.layanan_publik.statis_stage2')) }}</div>
                                        </div>
                                        <div class="circle-arrow">➔</div>
                                        <div class="circle-step">
                                            <div class="circle-num">3</div>
                                            <div class="circle-text">{{ app()->getLocale() == 'en' ? ($currentPage->extra_data['statis_stage3_en'] ?? $currentPage->extra_data['statis_stage3'] ?? __('home.layanan_publik.statis_stage3')) : ($currentPage->extra_data['statis_stage3'] ?? __('home.layanan_publik.statis_stage3')) }}</div>
                                        </div>
                                        <div class="circle-arrow">➔</div>
                                        <div class="circle-step">
                                            <div class="circle-num">4</div>
                                            <div class="circle-text">{{ app()->getLocale() == 'en' ? ($currentPage->extra_data['statis_stage4_en'] ?? $currentPage->extra_data['statis_stage4'] ?? __('home.layanan_publik.statis_stage4')) : ($currentPage->extra_data['statis_stage4'] ?? __('home.layanan_publik.statis_stage4')) }}</div>
                                        </div>
                                        <div class="circle-arrow">➔</div>
                                        <div class="circle-step">
                                            <div class="circle-num">5</div>
                                            <div class="circle-text">{{ app()->getLocale() == 'en' ? ($currentPage->extra_data['statis_stage5_en'] ?? $currentPage->extra_data['statis_stage5'] ?? __('home.layanan_publik.statis_stage5')) : ($currentPage->extra_data['statis_stage5'] ?? __('home.layanan_publik.statis_stage5')) }}</div>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            @php
                                $hasMech1 = ($currentPage && isset($currentPage->extra_data['statis_mech1_steps']) && is_array($currentPage->extra_data['statis_mech1_steps']) && count($currentPage->extra_data['statis_mech1_steps']) > 0) || ($currentPage && !isset($currentPage->extra_data['statis_mech1_steps']));
                                $hasMech2 = ($currentPage && isset($currentPage->extra_data['statis_mech2_steps']) && is_array($currentPage->extra_data['statis_mech2_steps']) && count($currentPage->extra_data['statis_mech2_steps']) > 0) || ($currentPage && !isset($currentPage->extra_data['statis_mech2_steps']));
                            @endphp

                            @if($hasMech1 || $hasMech2)
                                <h3 class="service-subtitle">{{ __('home.layanan_publik.mechanism') }}</h3>

                                @if($hasMech1)
                                    {{-- Langsung --}}
                                    <div class="flowchart-box mb-4">
                                        <div class="flowchart-title">{{ app()->getLocale() == 'en' ? ($currentPage->extra_data['statis_mech1_title_en'] ?? $currentPage->extra_data['statis_mech1_title'] ?? __('home.layanan_publik.statis_mech1_title')) : ($currentPage->extra_data['statis_mech1_title'] ?? __('home.layanan_publik.statis_mech1_title')) }}</div>
                                        <div class="flowchart-steps">
                                            @if(isset($currentPage->extra_data['statis_mech1_steps']) && is_array($currentPage->extra_data['statis_mech1_steps']))
                                                @foreach($currentPage->extra_data['statis_mech1_steps'] as $step)
                                                    <div class="flow-step">
                                                        <h4>{{ app()->getLocale() == 'en' ? ($step['title_en'] ?? $step['title'] ?? '') : ($step['title'] ?? '') }}</h4>
                                                        <p>{{ app()->getLocale() == 'en' ? ($step['desc_en'] ?? $step['desc'] ?? '') : ($step['desc'] ?? '') }}</p>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="flow-step">
                                                    <h4>{{ app()->getLocale() == 'en' ? ($currentPage->extra_data['statis_mech1_req_title_en'] ?? $currentPage->extra_data['statis_mech1_req_title'] ?? __('home.layanan_publik.statis_mech1_req_title')) : ($currentPage->extra_data['statis_mech1_req_title'] ?? __('home.layanan_publik.statis_mech1_req_title')) }}</h4>
                                                    <p>{{ app()->getLocale() == 'en' ? ($currentPage->extra_data['statis_mech1_req_desc_en'] ?? $currentPage->extra_data['statis_mech1_req_desc'] ?? __('home.layanan_publik.statis_mech1_req_desc')) : ($currentPage->extra_data['statis_mech1_req_desc'] ?? __('home.layanan_publik.statis_mech1_req_desc')) }}</p>
                                                </div>
                                                <div class="flow-step">
                                                    <h4>{{ app()->getLocale() == 'en' ? ($currentPage->extra_data['statis_mech1_stage_title_en'] ?? $currentPage->extra_data['statis_mech1_stage_title'] ?? __('home.layanan_publik.statis_mech1_stage_title')) : ($currentPage->extra_data['statis_mech1_stage_title'] ?? __('home.layanan_publik.statis_mech1_stage_title')) }}</h4>
                                                    <p>{{ app()->getLocale() == 'en' ? ($currentPage->extra_data['statis_mech1_stage_desc_en'] ?? $currentPage->extra_data['statis_mech1_stage_desc'] ?? __('home.layanan_publik.statis_mech1_stage_desc')) : ($currentPage->extra_data['statis_mech1_stage_desc'] ?? __('home.layanan_publik.statis_mech1_stage_desc')) }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    @if(!empty($currentPage->extra_data['statis_direct_pdf']))
                                        @php
                                            $filePath1 = $currentPage->extra_data['statis_direct_pdf'];
                                            $fullPath1 = public_path('storage/' . $filePath1);
                                            $storagePath1 = storage_path('app/public/' . $filePath1);
                                            $fileSizeBytes1 = Storage::disk('public')->exists($filePath1) ? Storage::disk('public')->size($filePath1) : (file_exists($fullPath1) ? filesize($fullPath1) : (file_exists($storagePath1) ? filesize($storagePath1) : 0));
                                            if ($fileSizeBytes1 >= 1048576) {
                                                $fileSizeStr1 = round($fileSizeBytes1 / 1048576, 1) . ' MB';
                                            } elseif ($fileSizeBytes1 > 0) {
                                                $fileSizeStr1 = round($fileSizeBytes1 / 1024, 0) . ' KB';
                                            } else {
                                                $fileSizeStr1 = '0 KB';
                                            }
                                            $customFileName1 = !empty($currentPage->extra_data['statis_direct_pdf_name']) ? $currentPage->extra_data['statis_direct_pdf_name'] : basename($filePath1);
                                        @endphp
                                        <a href="{{ asset('storage/' . $filePath1) }}" target="_blank" class="btn-download-pdf">
                                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg>
                                            <span>{{ $customFileName1 }} ({{ $fileSizeStr1 }})</span>
                                        </a>
                                    @endif
                                @endif

                                @if($hasMech2)
                                    {{-- Tidak Langsung --}}
                                    <div class="flowchart-box mb-4" style="background: linear-gradient(135deg, #0f172a, #334155);">
                                        <div class="flowchart-title">{{ app()->getLocale() == 'en' ? ($currentPage->extra_data['statis_mech2_title_en'] ?? $currentPage->extra_data['statis_mech2_title'] ?? __('home.layanan_publik.statis_mech2_title')) : ($currentPage->extra_data['statis_mech2_title'] ?? __('home.layanan_publik.statis_mech2_title')) }}</div>
                                        <div class="flowchart-steps">
                                            @if(isset($currentPage->extra_data['statis_mech2_steps']) && is_array($currentPage->extra_data['statis_mech2_steps']))
                                                @foreach($currentPage->extra_data['statis_mech2_steps'] as $step)
                                                    <div class="flow-step">
                                                        <h4>{{ app()->getLocale() == 'en' ? ($step['title_en'] ?? $step['title'] ?? '') : ($step['title'] ?? '') }}</h4>
                                                        <p>{{ app()->getLocale() == 'en' ? ($step['desc_en'] ?? $step['desc'] ?? '') : ($step['desc'] ?? '') }}</p>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="flow-step">
                                                    <h4>{{ app()->getLocale() == 'en' ? ($currentPage->extra_data['statis_mech2_online_title_en'] ?? $currentPage->extra_data['statis_mech2_online_title'] ?? __('home.layanan_publik.statis_mech2_online_title')) : ($currentPage->extra_data['statis_mech2_online_title'] ?? __('home.layanan_publik.statis_mech2_online_title')) }}</h4>
                                                    <p>{{ app()->getLocale() == 'en' ? ($currentPage->extra_data['statis_mech2_online_desc_en'] ?? $currentPage->extra_data['statis_mech2_online_desc'] ?? __('home.layanan_publik.statis_mech2_online_desc')) : ($currentPage->extra_data['statis_mech2_online_desc'] ?? __('home.layanan_publik.statis_mech2_online_desc')) }}</p>
                                                </div>
                                                <div class="flow-step">
                                                    <h4>{{ app()->getLocale() == 'en' ? ($currentPage->extra_data['statis_mech2_send_title_en'] ?? $currentPage->extra_data['statis_mech2_send_title'] ?? __('home.layanan_publik.statis_mech2_send_title')) : ($currentPage->extra_data['statis_mech2_send_title'] ?? __('home.layanan_publik.statis_mech2_send_title')) }}</h4>
                                                    <p>{{ app()->getLocale() == 'en' ? ($currentPage->extra_data['statis_mech2_send_desc_en'] ?? $currentPage->extra_data['statis_mech2_send_desc'] ?? __('home.layanan_publik.statis_mech2_send_desc')) : ($currentPage->extra_data['statis_mech2_send_desc'] ?? __('home.layanan_publik.statis_mech2_send_desc')) }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @endif

                            @if(!empty($currentPage->extra_data['statis_indirect_pdf']))
                                @php
                                    $filePath2 = $currentPage->extra_data['statis_indirect_pdf'];
                                    $fullPath2 = public_path('storage/' . $filePath2);
                                    $storagePath2 = storage_path('app/public/' . $filePath2);
                                    $fileSizeBytes2 = Storage::disk('public')->exists($filePath2) ? Storage::disk('public')->size($filePath2) : (file_exists($fullPath2) ? filesize($fullPath2) : (file_exists($storagePath2) ? filesize($storagePath2) : 0));
                                    if ($fileSizeBytes2 >= 1048576) {
                                        $fileSizeStr2 = round($fileSizeBytes2 / 1048576, 1) . ' MB';
                                    } elseif ($fileSizeBytes2 > 0) {
                                        $fileSizeStr2 = round($fileSizeBytes2 / 1024, 0) . ' KB';
                                    } else {
                                        $fileSizeStr2 = '0 KB';
                                    }
                                    $customFileName2 = !empty($currentPage->extra_data['statis_indirect_pdf_name']) ? $currentPage->extra_data['statis_indirect_pdf_name'] : basename($filePath2);
                                @endphp
                                <a href="{{ asset('storage/' . $filePath2) }}" target="_blank" class="btn-download-pdf">
                                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg>
                                    <span>{{ $customFileName2 }} ({{ $fileSizeStr2 }})</span>
                                </a>
                            @endif

                        @elseif (str_contains($matchTitle, 'konsultasi'))
                            {{-- Layout 4: Konsultasi Kearsipan --}}
                            <h3 class="service-subtitle">{{ __('home.layanan_publik.consultation_types') }}</h3>
                            <div class="service-box">
                                @if($currentPage && is_array($currentPage->extra_data) && (array_key_exists('consultation_desc', $currentPage->extra_data) || array_key_exists('consultation_desc_en', $currentPage->extra_data)))
                                    {!! nl2br(e(app()->getLocale() == 'en' ? ($currentPage->extra_data['consultation_desc_en'] ?? $currentPage->extra_data['consultation_desc'] ?? '') : ($currentPage->extra_data['consultation_desc'] ?? ''))) !!}
                                @else
                                    <p>{{ __('home.layanan_publik.consultation_desc') }}</p>
                                @endif
                            </div>

                            @if(!isset($currentPage->extra_data['show_consultation_form']) || $currentPage->extra_data['show_consultation_form'] == 1)
                                @php
                                    $formTitle = $currentPage && is_array($currentPage->extra_data) && !empty($currentPage->extra_data['consultation_form_title'])
                                        ? (app()->getLocale() == 'en' ? ($currentPage->extra_data['consultation_form_title_en'] ?? $currentPage->extra_data['consultation_form_title']) : $currentPage->extra_data['consultation_form_title'])
                                        : __('home.layanan_publik.consultation_form');

                                    $formSend = $currentPage && is_array($currentPage->extra_data) && !empty($currentPage->extra_data['consultation_form_send'])
                                        ? (app()->getLocale() == 'en' ? ($currentPage->extra_data['consultation_form_send_en'] ?? $currentPage->extra_data['consultation_form_send']) : $currentPage->extra_data['consultation_form_send'])
                                        : __('home.layanan_publik.consultation_form_send');

                                    $formSuccess = $currentPage && is_array($currentPage->extra_data) && !empty($currentPage->extra_data['consultation_success'])
                                        ? (app()->getLocale() == 'en' ? ($currentPage->extra_data['consultation_success_en'] ?? $currentPage->extra_data['consultation_success']) : $currentPage->extra_data['consultation_success'])
                                        : __('home.layanan_publik.consultation_success');
                                @endphp

                                <h3 class="service-subtitle">{{ $formTitle }}</h3>
                                <form action="#" method="POST" enctype="multipart/form-data" class="service-form" onsubmit="event.preventDefault(); Swal.fire({ title: '{{ app()->getLocale() == 'en' ? 'Success!' : 'Berhasil!' }}', text: '{{ addslashes($formSuccess) }}', icon: 'success', confirmButtonColor: '#174E93' });">
                                    @if(!empty($currentPage->extra_data['consultation_form_fields']) && is_array($currentPage->extra_data['consultation_form_fields']))
                                        @foreach($currentPage->extra_data['consultation_form_fields'] as $field)
                                            @php
                                                $fieldId = 'consult_' . ($field['id'] ?? uniqid());
                                                $fieldLabel = app()->getLocale() === 'en' && !empty($field['label_en']) ? $field['label_en'] : ($field['label'] ?? '');
                                                $fieldType = $field['type'] ?? 'text';
                                                $isRequired = !empty($field['required']) && $field['required'] !== 'false' && $field['required'] !== false && $field['required'] != 0;
                                                $placeholder = app()->getLocale() === 'en' && !empty($field['placeholder_en']) ? $field['placeholder_en'] : ($field['placeholder'] ?? '');
                                            @endphp
                                            <div class="form-group" @if($fieldType === 'textarea') style="align-items: start;" @endif>
                                                <label class="form-label" for="{{ $fieldId }}">{{ $fieldLabel }} @if($isRequired)<span class="required">*</span>@endif</label>
                                                @if($fieldType === 'textarea')
                                                    <textarea id="{{ $fieldId }}" name="{{ $field['id'] ?? '' }}" class="form-textarea" rows="3" @if($isRequired) required @endif placeholder="{{ $placeholder }}"></textarea>
                                                @elseif($fieldType === 'select')
                                                    @php
                                                        $optionsStr = app()->getLocale() === 'en' && !empty($field['options_en']) ? $field['options_en'] : ($field['options'] ?? '');
                                                        $optionsArr = array_filter(array_map('trim', explode(',', $optionsStr)));
                                                    @endphp
                                                    <select id="{{ $fieldId }}" name="{{ $field['id'] ?? '' }}" class="form-select" @if($isRequired) required @endif>
                                                        <option value="">{{ __('home.layanan_publik.form_select') }}</option>
                                                        @foreach($optionsArr as $opt)
                                                            <option value="{{ $opt }}">{{ $opt }}</option>
                                                        @endforeach
                                                    </select>
                                                @elseif($fieldType === 'file')
                                                    <div>
                                                        <div class="form-file-wrap">
                                                            <button type="button" class="btn-choose-file" onclick="document.getElementById('{{ $fieldId }}').click()">{{ __('home.layanan_publik.choose_file') }}</button>
                                                            <span id="{{ $fieldId }}_text">{{ __('home.layanan_publik.no_file') }}</span>
                                                            <input type="file" id="{{ $fieldId }}" name="{{ $field['id'] ?? '' }}" class="hidden" onchange="document.getElementById('{{ $fieldId }}_text').innerText = this.files[0] ? this.files[0].name : '{{ __('home.layanan_publik.no_file') }}'" @if($isRequired) required @endif>
                                                        </div>
                                                        @if(!empty($field['options']))
                                                            <div class="file-hint">{{ app()->getLocale() === 'en' && !empty($field['options_en']) ? $field['options_en'] : $field['options'] }}</div>
                                                        @endif
                                                    </div>
                                                @else
                                                    <input type="{{ $fieldType }}" id="{{ $fieldId }}" name="{{ $field['id'] ?? '' }}" class="form-input" @if($isRequired) required @endif @if($fieldType === 'number') min="1" @endif placeholder="{{ $placeholder }}">
                                                @endif
                                            </div>
                                        @endforeach
                                    @else
                                        {{-- Fallback to default static form if no dynamic fields configured --}}
                                        <div class="form-group">
                                            <label class="form-label">{{ __('home.layanan_publik.form_name') }} <span class="required">*</span></label>
                                            <input type="text" class="form-input" required>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">{{ __('home.layanan_publik.form_institution') }} <span class="required">*</span></label>
                                            <input type="text" class="form-input" required>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">{{ __('home.layanan_publik.form_email') }} <span class="required">*</span></label>
                                            <input type="email" class="form-input" required>
                                        </div>
                                        <div class="form-group" style="align-items: start;">
                                            <label class="form-label">{{ __('home.layanan_publik.consultation_form_detail') }} <span class="required">*</span></label>
                                            <textarea class="form-textarea" required placeholder="{{ __('home.layanan_publik.consultation_form_placeholder') }}"></textarea>
                                        </div>
                                    @endif
                                    <button type="submit" class="btn-submit">{{ $formSend }}</button>
                                </form>
                            @endif

                        @else
                            {{-- Layout 5: Perpustakaan --}}
                            @php
                                $hasLibData = $currentPage && is_array($currentPage->extra_data) && (array_key_exists('lib_obj1', $currentPage->extra_data) || array_key_exists('lib_objs', $currentPage->extra_data));
                                $libObjs = [];
                                if ($currentPage && is_array($currentPage->extra_data)) {
                                    if (!empty($currentPage->extra_data['lib_objs']) && is_array($currentPage->extra_data['lib_objs'])) {
                                        foreach ($currentPage->extra_data['lib_objs'] as $obj) {
                                            $text = app()->getLocale() == 'en' ? ($obj['text_en'] ?? $obj['text'] ?? '') : ($obj['text'] ?? '');
                                            if (!empty($text)) {
                                                $libObjs[] = $text;
                                            }
                                        }
                                    } else {
                                        $o1 = app()->getLocale() == 'en' ? ($currentPage->extra_data['lib_obj1_en'] ?? $currentPage->extra_data['lib_obj1'] ?? '') : ($currentPage->extra_data['lib_obj1'] ?? '');
                                        $o2 = app()->getLocale() == 'en' ? ($currentPage->extra_data['lib_obj2_en'] ?? $currentPage->extra_data['lib_obj2'] ?? '') : ($currentPage->extra_data['lib_obj2'] ?? '');
                                        $o3 = app()->getLocale() == 'en' ? ($currentPage->extra_data['lib_obj3_en'] ?? $currentPage->extra_data['lib_obj3'] ?? '') : ($currentPage->extra_data['lib_obj3'] ?? '');
                                        if ($o1) $libObjs[] = $o1;
                                        if ($o2) $libObjs[] = $o2;
                                        if ($o3) $libObjs[] = $o3;
                                    }
                                }
                                if (empty($libObjs) && !$hasLibData) {
                                    $libObjs = [__('home.layanan_publik.lib_obj1'), __('home.layanan_publik.lib_obj2'), __('home.layanan_publik.lib_obj3')];
                                }
                            @endphp
                            @if(!empty($libObjs))
                                <h3 class="service-subtitle">{{ __('home.layanan_publik.objectives') }}</h3>
                                <div class="service-box">
                                    @foreach($libObjs as $objText)
                                        <p>{{ $objText }}</p>
                                    @endforeach
                                </div>
                            @endif

                            <div class="mb-8">
                                @php
                                    $libBtn = $hasLibData ? (app()->getLocale() == 'en' ? ($currentPage->extra_data['lib_visit_btn_en'] ?? $currentPage->extra_data['lib_visit_btn'] ?? '') : ($currentPage->extra_data['lib_visit_btn'] ?? '')) : __('home.layanan_publik.lib_visit_btn');
                                    $libUrl = $currentPage && is_array($currentPage->extra_data) && !empty($currentPage->extra_data['lib_redirect_url']) ? $currentPage->extra_data['lib_redirect_url'] : '';
                                @endphp
                                @if($libBtn !== '')
                                    @if(!empty($libUrl))
                                        <a href="{{ $libUrl }}" target="_blank" class="inline-block bg-[#0284c7] hover:bg-[#0369a1] text-white font-bold px-8 py-3 rounded-full shadow-lg transition-all text-sm uppercase tracking-wider">{{ $libBtn }}</a>
                                    @else
                                        <a href="#" class="inline-block bg-[#0284c7] hover:bg-[#0369a1] text-white font-bold px-8 py-3 rounded-full shadow-lg transition-all text-sm uppercase tracking-wider" onclick="event.preventDefault(); Swal.fire({ title: 'Informasi', text: '{{ __('home.layanan_publik.lib_redirect') }}', icon: 'info', confirmButtonColor: '#174E93' });">{{ $libBtn }}</a>
                                    @endif
                                @endif
                            </div>

                            @php
                                $libCards = [];
                                if ($currentPage && is_array($currentPage->extra_data)) {
                                    if (!empty($currentPage->extra_data['lib_cards']) && is_array($currentPage->extra_data['lib_cards'])) {
                                        foreach ($currentPage->extra_data['lib_cards'] as $card) {
                                            $cTitle = app()->getLocale() == 'en' ? ($card['title_en'] ?? $card['title'] ?? '') : ($card['title'] ?? '');
                                            $cDesc = app()->getLocale() == 'en' ? ($card['desc_en'] ?? $card['desc'] ?? '') : ($card['desc'] ?? '');
                                            if (!empty($cTitle) || !empty($cDesc)) {
                                                $libCards[] = ['title' => $cTitle, 'desc' => $cDesc];
                                            }
                                        }
                                    } else {
                                        $c1Title = app()->getLocale() == 'en' ? ($currentPage->extra_data['lib_card1_title_en'] ?? $currentPage->extra_data['lib_card1_title'] ?? '') : ($currentPage->extra_data['lib_card1_title'] ?? '');
                                        $c1Desc = app()->getLocale() == 'en' ? ($currentPage->extra_data['lib_card1_desc_en'] ?? $currentPage->extra_data['lib_card1_desc'] ?? '') : ($currentPage->extra_data['lib_card1_desc'] ?? '');
                                        $c2Title = app()->getLocale() == 'en' ? ($currentPage->extra_data['lib_card2_title_en'] ?? $currentPage->extra_data['lib_card2_title'] ?? '') : ($currentPage->extra_data['lib_card2_title'] ?? '');
                                        $c2Desc = app()->getLocale() == 'en' ? ($currentPage->extra_data['lib_card2_desc_en'] ?? $currentPage->extra_data['lib_card2_desc'] ?? '') : ($currentPage->extra_data['lib_card2_desc'] ?? '');
                                        $c3Title = app()->getLocale() == 'en' ? ($currentPage->extra_data['lib_card3_title_en'] ?? $currentPage->extra_data['lib_card3_title'] ?? '') : ($currentPage->extra_data['lib_card3_title'] ?? '');
                                        $c3Desc = app()->getLocale() == 'en' ? ($currentPage->extra_data['lib_card3_desc_en'] ?? $currentPage->extra_data['lib_card3_desc'] ?? '') : ($currentPage->extra_data['lib_card3_desc'] ?? '');
                                        if ($c1Title || $c1Desc) $libCards[] = ['title' => $c1Title, 'desc' => $c1Desc];
                                        if ($c2Title || $c2Desc) $libCards[] = ['title' => $c2Title, 'desc' => $c2Desc];
                                        if ($c3Title || $c3Desc) $libCards[] = ['title' => $c3Title, 'desc' => $c3Desc];
                                    }
                                }
                                if (empty($libCards) && !$hasLibData) {
                                    $libCards = [
                                        ['title' => __('home.layanan_publik.lib_card1_title'), 'desc' => __('home.layanan_publik.lib_card1_desc')],
                                        ['title' => __('home.layanan_publik.lib_card2_title'), 'desc' => __('home.layanan_publik.lib_card2_desc')],
                                        ['title' => __('home.layanan_publik.lib_card3_title'), 'desc' => __('home.layanan_publik.lib_card3_desc')],
                                    ];
                                }
                                // Icons array for variety
                                $cardIcons = [
                                    '<svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>',
                                    '<svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13.5h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>',
                                    '<svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>'
                                ];
                            @endphp
                            {{-- Cards Grid --}}
                            @if(!empty($libCards))
                                <div class="cards-grid">
                                    @foreach($libCards as $index => $card)
                                        @php $icon = $cardIcons[$index % count($cardIcons)]; @endphp
                                        <div class="service-card">
                                            <div class="card-icon">{!! $icon !!}</div>
                                            <div class="card-info">
                                                <h4>{{ $card['title'] }}</h4>
                                                <p>{{ $card['desc'] }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @php
                                $libHours = $hasLibData ? (app()->getLocale() == 'en' ? ($currentPage->extra_data['lib_hours_en'] ?? $currentPage->extra_data['lib_hours'] ?? '') : ($currentPage->extra_data['lib_hours'] ?? '')) : __('home.layanan_publik.statis_hours');
                            @endphp
                            @if($libHours !== '')
                                <h3 class="service-subtitle">{{ __('home.layanan_publik.service_hours') }}</h3>
                                <div class="service-box">
                                    {!! nl2br(e($libHours)) !!}
                                </div>
                            @endif

                            @php
                                $libRules = [];
                                if ($currentPage && is_array($currentPage->extra_data)) {
                                    if (!empty($currentPage->extra_data['lib_rules']) && is_array($currentPage->extra_data['lib_rules'])) {
                                        foreach ($currentPage->extra_data['lib_rules'] as $rule) {
                                            $text = app()->getLocale() == 'en' ? ($rule['text_en'] ?? $rule['text'] ?? '') : ($rule['text'] ?? '');
                                            if (!empty($text)) {
                                                $libRules[] = $text;
                                            }
                                        }
                                    } else {
                                        $r1 = app()->getLocale() == 'en' ? ($currentPage->extra_data['lib_rule1_en'] ?? $currentPage->extra_data['lib_rule1'] ?? '') : ($currentPage->extra_data['lib_rule1'] ?? '');
                                        $r2 = app()->getLocale() == 'en' ? ($currentPage->extra_data['lib_rule2_en'] ?? $currentPage->extra_data['lib_rule2'] ?? '') : ($currentPage->extra_data['lib_rule2'] ?? '');
                                        $r3 = app()->getLocale() == 'en' ? ($currentPage->extra_data['lib_rule3_en'] ?? $currentPage->extra_data['lib_rule3'] ?? '') : ($currentPage->extra_data['lib_rule3'] ?? '');
                                        if ($r1) $libRules[] = $r1;
                                        if ($r2) $libRules[] = $r2;
                                        if ($r3) $libRules[] = $r3;
                                    }
                                }
                                if (empty($libRules) && !$hasLibData) {
                                    $libRules = [__('home.layanan_publik.lib_rule1'), __('home.layanan_publik.lib_rule2'), __('home.layanan_publik.lib_rule3')];
                                }
                            @endphp
                            @if(!empty($libRules))
                                <h3 class="service-subtitle">{{ __('home.layanan_publik.rules') }}</h3>
                                <div class="service-box">
                                    @foreach($libRules as $ruleText)
                                        <p>{{ $ruleText }}</p>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Photos Grid --}}
                            @php
                                $displayLibPhotos = [];
                                if ($currentPage && is_array($currentPage->extra_data)) {
                                    if (!empty($currentPage->extra_data['lib_photos']) && is_array($currentPage->extra_data['lib_photos'])) {
                                        $displayLibPhotos = $currentPage->extra_data['lib_photos'];
                                    } else {
                                        if (!empty($currentPage->extra_data['lib_photo1'])) {
                                            $displayLibPhotos[] = $currentPage->extra_data['lib_photo1'];
                                        }
                                        if (!empty($currentPage->extra_data['lib_photo2'])) {
                                            $displayLibPhotos[] = $currentPage->extra_data['lib_photo2'];
                                        }
                                    }
                                }
                            @endphp

                            @if(!$hasLibData || !empty($displayLibPhotos))
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 my-8">
                                    @if(!empty($displayLibPhotos))
                                        @php $totalPhotos = count($displayLibPhotos); @endphp
                                        @foreach($displayLibPhotos as $photoPath)
                                            <div class="rounded-xl overflow-hidden shadow-md h-64 bg-gray-100 group relative {{ $totalPhotos == 1 ? 'md:col-span-2' : '' }}">
                                                <img src="{{ asset('storage/' . $photoPath) }}" alt="Fasilitas Perpustakaan" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="rounded-xl overflow-hidden shadow-md h-64 bg-gray-100">
                                            <img src="/image/background.png" alt="Fasilitas Perpustakaan" class="w-full h-full object-cover">
                                        </div>
                                        <div class="rounded-xl overflow-hidden shadow-md h-64 bg-gray-100">
                                            <img src="/image/background.png" alt="Fasilitas Perpustakaan" class="w-full h-full object-cover">
                                        </div>
                                    @endif
                                </div>
                            @endif

                            @php
                                $libProcTitle = $hasLibData ? (app()->getLocale() == 'en' ? ($currentPage->extra_data['lib_proc_title_en'] ?? $currentPage->extra_data['lib_proc_title'] ?? '') : ($currentPage->extra_data['lib_proc_title'] ?? '')) : __('home.layanan_publik.lib_proc_title');
                                $libProcs = [];
                                if ($currentPage && is_array($currentPage->extra_data)) {
                                    if (!empty($currentPage->extra_data['lib_procs']) && is_array($currentPage->extra_data['lib_procs'])) {
                                        foreach ($currentPage->extra_data['lib_procs'] as $proc) {
                                            $pTitle = app()->getLocale() == 'en' ? ($proc['title_en'] ?? $proc['title'] ?? '') : ($proc['title'] ?? '');
                                            $pDesc = app()->getLocale() == 'en' ? ($proc['desc_en'] ?? $proc['desc'] ?? '') : ($proc['desc'] ?? '');
                                            if (!empty($pTitle) || !empty($pDesc)) {
                                                $libProcs[] = ['title' => $pTitle, 'desc' => $pDesc];
                                            }
                                        }
                                    } else {
                                        $p1Title = app()->getLocale() == 'en' ? ($currentPage->extra_data['lib_proc1_title_en'] ?? $currentPage->extra_data['lib_proc1_title'] ?? '') : ($currentPage->extra_data['lib_proc1_title'] ?? '');
                                        $p1Desc = app()->getLocale() == 'en' ? ($currentPage->extra_data['lib_proc1_desc_en'] ?? $currentPage->extra_data['lib_proc1_desc'] ?? '') : ($currentPage->extra_data['lib_proc1_desc'] ?? '');
                                        $p2Title = app()->getLocale() == 'en' ? ($currentPage->extra_data['lib_proc2_title_en'] ?? $currentPage->extra_data['lib_proc2_title'] ?? '') : ($currentPage->extra_data['lib_proc2_title'] ?? '');
                                        $p2Desc = app()->getLocale() == 'en' ? ($currentPage->extra_data['lib_proc2_desc_en'] ?? $currentPage->extra_data['lib_proc2_desc'] ?? '') : ($currentPage->extra_data['lib_proc2_desc'] ?? '');
                                        $p3Title = app()->getLocale() == 'en' ? ($currentPage->extra_data['lib_proc3_title_en'] ?? $currentPage->extra_data['lib_proc3_title'] ?? '') : ($currentPage->extra_data['lib_proc3_title'] ?? '');
                                        $p3Desc = app()->getLocale() == 'en' ? ($currentPage->extra_data['lib_proc3_desc_en'] ?? $currentPage->extra_data['lib_proc3_desc'] ?? '') : ($currentPage->extra_data['lib_proc3_desc'] ?? '');
                                        $p4Title = app()->getLocale() == 'en' ? ($currentPage->extra_data['lib_proc4_title_en'] ?? $currentPage->extra_data['lib_proc4_title'] ?? '') : ($currentPage->extra_data['lib_proc4_title'] ?? '');
                                        $p4Desc = app()->getLocale() == 'en' ? ($currentPage->extra_data['lib_proc4_desc_en'] ?? $currentPage->extra_data['lib_proc4_desc'] ?? '') : ($currentPage->extra_data['lib_proc4_desc'] ?? '');
                                        if ($p1Title || $p1Desc) $libProcs[] = ['title' => $p1Title, 'desc' => $p1Desc];
                                        if ($p2Title || $p2Desc) $libProcs[] = ['title' => $p2Title, 'desc' => $p2Desc];
                                        if ($p3Title || $p3Desc) $libProcs[] = ['title' => $p3Title, 'desc' => $p3Desc];
                                        if ($p4Title || $p4Desc) $libProcs[] = ['title' => $p4Title, 'desc' => $p4Desc];
                                    }
                                }
                                if (empty($libProcs) && !$hasLibData) {
                                    $libProcs = [
                                        ['title' => __('home.layanan_publik.lib_proc1_title'), 'desc' => __('home.layanan_publik.lib_proc1_desc')],
                                        ['title' => __('home.layanan_publik.lib_proc2_title'), 'desc' => __('home.layanan_publik.lib_proc2_desc')],
                                        ['title' => __('home.layanan_publik.lib_proc3_title'), 'desc' => __('home.layanan_publik.lib_proc3_desc')],
                                        ['title' => __('home.layanan_publik.lib_proc4_title'), 'desc' => __('home.layanan_publik.lib_proc4_desc')],
                                    ];
                                }
                            @endphp
                            @if($libProcTitle !== '' || !empty($libProcs))
                                {{-- Prosedur Infographic --}}
                                @if($libProcTitle !== '')
                                    <h3 class="service-subtitle">{{ $libProcTitle }}</h3>
                                @endif
                                <div class="flowchart-box mb-4">
                                    @if($libProcTitle !== '')
                                        <div class="flowchart-title">{{ $libProcTitle }}</div>
                                    @endif
                                    <div class="flowchart-steps">
                                        @foreach($libProcs as $proc)
                                            <div class="flow-step">
                                                <h4>{{ $proc['title'] }}</h4>
                                                <p>{{ $proc['desc'] }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if($currentPage && is_array($currentPage->extra_data) && !empty($currentPage->extra_data['lib_pdf']))
                                @php
                                    $libPdfPath = $currentPage->extra_data['lib_pdf'];
                                    $libPdfSizeBytes = Storage::disk('public')->exists($libPdfPath) ? Storage::disk('public')->size($libPdfPath) : 0;
                                    $libPdfSizeStr = $libPdfSizeBytes > 1048576 ? round($libPdfSizeBytes / 1048576, 2) . ' MB' : ($libPdfSizeBytes > 0 ? round($libPdfSizeBytes / 1024, 0) . ' KB' : '0 KB');
                                    $libPdfName = !empty($currentPage->extra_data['lib_pdf_name']) ? $currentPage->extra_data['lib_pdf_name'] : basename($libPdfPath);
                                @endphp
                                <a href="{{ asset('storage/' . $libPdfPath) }}" target="_blank" class="btn-download-pdf">
                                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg>
                                    <span>{{ $libPdfName }} ({{ $libPdfSizeStr }})</span>
                                </a>
                            @elseif(!$hasLibData)
                                <a href="#" class="btn-download-pdf" onclick="event.preventDefault(); Swal.fire({ title: '{{ __('home.layanan_publik.downloading_pdf') }}', text: '{{ __('home.layanan_publik.lib_pdf') }} {{ __('home.layanan_publik.downloading_pdf_desc') }}', icon: 'info', confirmButtonColor: '#174E93' });">
                                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg>
                                    <span>{{ __('home.layanan_publik.lib_pdf') }}</span>
                                </a>
                            @endif

                        @endif
                    @else
                        <div class="p-8 text-center bg-gray-50 rounded-2xl border border-gray-200 my-8">
                            <p class="text-gray-500 text-lg font-medium">{{ app()->getLocale() == 'en' ? 'No public service content available yet.' : 'Belum ada konten layanan publik yang tersedia.' }}</p>
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
    </section>
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
        fetch('{{ route('layanan_publik.share.increment', $currentPage->id) }}', {
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
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endpush
