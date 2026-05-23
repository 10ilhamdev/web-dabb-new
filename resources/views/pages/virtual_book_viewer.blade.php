<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $book->translated_title }} — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body, html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            background-color: #f8fafc;
            font-family: 'Montserrat', sans-serif;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .viewer-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #ffffff;
            padding: 0.75rem 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            z-index: 10;
        }

        .viewer-back {
            text-decoration: none;
            color: #4b5563;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex: 1;
        }

        .viewer-back:hover {
            color: #111827;
        }

        .viewer-title {
            font-weight: 600;
            color: #111827;
            text-align: center;
            flex: 2;
        }

        .viewer-spacer {
            flex: 1;
        }

        .viewer-content {
            flex: 1;
            padding: 1rem;
            position: relative;
            overflow: hidden; /* Prevent scrolling */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center; /* Center everything vertically */
            background: transparent;
        }

        /* Single-view: allow vertical scrolling so a zoomed single page can be
           taller than the viewport without being cropped. */
        body.single-view-active .viewer-content {
            overflow-y: auto;
            justify-content: flex-start;
            padding-top: 1.5rem;
        }

        /* Books Styles */
        .flip-book {
            margin: 0 auto;
            transition: transform 0.6s cubic-bezier(0.645, 0.045, 0.355, 1);
        }

        .flip-book.is-front-cover {
            transform: translateX(-25%);
        }
        .flip-book.is-back-cover {
            transform: translateX(25%);
        }

        .flip-book-wrapper {
            width: 100%;
            /* 550x733 ratio per page -> 1100x733 total = ~1.5 ratio */
            /* Scale width dynamically based on available height to ensure it fits without scrolling */
            max-width: min(1200px, calc((100vh - 180px) * 1.5), 100%);
            margin: 0 auto;
            display: block;
            position: relative;
            padding: 10px 0;
            box-sizing: border-box;
            transition: max-width 0.3s ease;
        }

        @media (max-width: 640px) {
            .viewer-header {
                flex-direction: column;
                gap: 0.5rem;
                padding: 0.75rem 1rem;
                align-items: center;
            }
            .viewer-title {
                font-size: 0.95rem;
                order: -1;
                flex: none;
            }
            .viewer-back {
                font-size: 0.85rem;
                flex: none;
            }
            .vb-controls button {
                padding: 0.5rem 1rem;
                font-size: 0.8rem;
            }
            .vb-search input {
                width: 50px;
                font-size: 0.8rem;
            }
            .vb-search button {
                padding: 0.25rem 0.5rem;
                font-size: 0.75rem;
            }
        }

        /* Single page (portrait) mode: wrapper is ~90vw so one page is rendered
           much larger than a single page in double view — genuine zoom. Height grows
           automatically (ratio 1/0.75); page is allowed to exceed viewport height and
           the viewer-content becomes scrollable (see body.single-view-active rule).
           Still below 2 * BASE_WIDTH (1100) so PageFlip remains in portrait. */
        .flip-book-wrapper.single-view {
            max-width: min(90vw, 1000px);
        }

        .page {
            padding: 20px;
            background-color: hsl(35, 55%, 98%);
            color: hsl(35, 35%, 35%);
            border: solid 1px hsl(35, 20%, 70%);
            overflow: hidden;
            box-shadow: 0 0 5px rgba(0,0,0,0.2);
        }

        .page .page-content { width: 100%; height: 100%; position: relative; }
        .page .page-content .page-header { position: absolute; top: 0; left: 0; right: 0; height: 30px; font-size: 100%; text-transform: uppercase; text-align: center; z-index: 1; }
        /* Contained mode (default): inner is bounded by header & footer */
        .page .page-content .page-inner { position: absolute; left: 15px; right: 0; top: 30px; bottom: 30px; }
        /* Fullbleed mode: inner spans the entire page; header/footer still rendered but image overlaps when 100% */
        .page.fullbleed-page { padding: 0; }
        .page .page-content.fullbleed .page-inner { left: 0; right: 0; top: 0; bottom: 0; z-index: 2; }
        .page .page-content.fullbleed .page-header { left: 20px; right: 20px; top: 8px; background: transparent; border: 0; }
        .page .page-content.fullbleed .page-footer { left: 20px; right: 20px; bottom: 8px; background: transparent; border: 0; }
        .page .page-content .page-image { position: absolute; background-size: contain; background-position: center center; background-repeat: no-repeat; }
        .page .page-content.fullbleed .page-image { background-size: 100% 100%; }
        .page .page-content .page-text { position: absolute; font-size: 80%; text-align: justify; padding: 8px; box-sizing: border-box; overflow: auto; z-index: 3; }
        .page .page-content .page-footer { position: absolute; bottom: 0; left: 0; right: 0; height: 30px; border-top: solid 1px hsl(35, 55%, 90%); font-size: 80%; color: hsl(35, 20%, 50%); z-index: 1; }
        .page.--left { border-right: 0; box-shadow: inset -7px 0 30px -7px rgba(0, 0, 0, 0.4); }
        .page.--right { border-left: 0; box-shadow: inset 7px 0 30px -7px rgba(0, 0, 0, 0.4); text-align: right; }
        .page.hard { background-color: hsl(35, 50%, 90%); border: solid 1px hsl(35, 20%, 50%); }
        .page.page-cover { background-color: transparent; color: hsl(35, 35%, 35%); border: none; padding: 0; }
        .page.page-cover .page-cover-inner { position: absolute; inset: 0; background: linear-gradient(to bottom, #b45309, #78350f); overflow: hidden; }
        .page.page-cover .cover-spine { position: absolute; left: 0; top: 0; bottom: 0; width: 6.25%; background: linear-gradient(to right, #78350f, #b45309); z-index: 1; pointer-events: none; }
        .page.page-cover .cover-image-container { position: absolute; width: 81.25%; height: 90.625%; top: 50%; left: 53.125%; transform: translate(-50%, -50%); display: flex; align-items: center; justify-content: center; background: rgba(255, 255, 255, 0.1); overflow: visible; z-index: 5; }
        .page.page-cover .cover-image-container img { max-width: 100%; max-height: 100%; object-fit: contain; pointer-events: none; }
        .page.page-cover .cover-title { position: absolute; top: 6.25%; left: 0; right: 0; text-align: center; padding: 0 16px; color: white; font-weight: 600; font-size: 1.2rem; text-shadow: 0 2px 4px rgba(0,0,0,0.8); line-height: 1.3; z-index: 20; }
        .page.page-cover .cover-extra-texts { position: absolute; bottom: 6.25%; left: 0; right: 0; text-align: center; padding: 0 16px; z-index: 20; }
        .page.page-cover .cover-extra-texts span { display: block; color: rgba(255,255,255,0.9); font-size: 0.75rem; text-shadow: 0 1px 3px rgba(0,0,0,0.8); margin-top: 4px; }
        .page.page-cover h2 { text-align: center; padding-top: 50%; font-size: 210%; }
        .page.page-cover-top { box-shadow: 0 0 15px rgba(0,0,0,0.3); }
        .page.page-cover-bottom { box-shadow: 0 0 15px rgba(0,0,0,0.3); }

        /* Gutter / Spine Shadow Effect */
        .page.--left::after {
            content: "";
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            width: 40px;
            background: linear-gradient(to left, rgba(0,0,0,0.2), transparent);
            pointer-events: none;
            z-index: 10;
        }
        .page.--right::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: 40px;
            background: linear-gradient(to right, rgba(0,0,0,0.2), transparent);
            pointer-events: none;
            z-index: 10;
        }
        /* Hide gutter in single view */
        body.single-view-active .page.--left::after,
        body.single-view-active .page.--right::after {
            display: none;
        }

        .vb-controls { display: flex; align-items: center; justify-content: center; gap: 1rem; flex-wrap: wrap; margin-top: 1rem; padding-bottom: 1rem; }
        .vb-controls button { padding: 0.6rem 1.25rem; background: #0d9488; color: white; border: none; border-radius: 0.5rem; cursor: pointer; font-weight: 600; font-size: 0.9rem; transition: all 0.2s; box-shadow: 0 2px 4px rgba(13, 148, 136, 0.2); }
        .vb-controls button:hover { background: #0f766e; transform: translateY(-1px); box-shadow: 0 4px 6px rgba(13, 148, 136, 0.3); }
        .vb-controls button:active { transform: translateY(0); }

        .vb-search { display: flex; align-items: center; gap: 0.5rem; background: #fff; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.25rem 0.5rem; margin-left: 0.5rem; }
        .vb-search input { width: 60px; border: none; outline: none; font-size: 0.9rem; text-align: center; padding: 0.2rem; font-weight: 600; color: #111827; }
        .vb-search button { padding: 0.3rem 0.75rem; font-size: 0.8rem; background: #f3f4f6; color: #4b5563; box-shadow: none; }
        .vb-search button:hover { background: #e5e7eb; color: #111827; transform: none; box-shadow: none; }
        .vb-state-info { display: none; }

        /* View mode toggle (double / single page) */
        .vb-view-toggle { display: inline-flex; align-items: center; border: 1px solid #d1d5db; border-radius: 0.5rem; overflow: hidden; background: #fff; }
        .vb-view-toggle button { background: transparent; color: #4b5563; border: none; padding: 0.45rem 0.9rem; cursor: pointer; font-size: 0.85rem; font-weight: 500; display: inline-flex; align-items: center; gap: 0.4rem; transition: background 0.2s, color 0.2s; }
        .vb-view-toggle button:hover { background: #f3f4f6; color: #111827; }
        .vb-view-toggle button.active { background: #0d9488; color: #fff; }
        .vb-view-toggle button.active:hover { background: #0f766e; color: #fff; }
        .vb-view-toggle svg { width: 16px; height: 16px; }

        /* PDF loading overlay */
        #pdfLoadingOverlay {
            position: absolute;
            inset: 0;
            background: rgba(255,255,255,0.8);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 100;
            backdrop-filter: blur(4px);
        }
        .pdf-spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f4f6;
            border-top: 4px solid #0d9488;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 1rem;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
    @if($book->pdf_path)
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
        <script>
            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
        </script>
    @endif
</head>
<body>

    <div class="viewer-header">
        <a href="?detail={{ $book->id }}" class="viewer-back">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            {{ app()->getLocale() === 'en' ? 'Back to Exhibition' : 'Kembali ke Pameran' }}
        </a>
        <div class="viewer-title">
            {{ $book->translated_title }}
        </div>
        <div class="viewer-spacer" style="display:flex; justify-content:flex-end;">
            <div class="vb-view-toggle" role="group" aria-label="Zoom mode">
                <button type="button" id="vbViewDouble" class="active" title="{{ app()->getLocale() === 'en' ? 'Zoom out (default)' : 'Perkecil (bawaan)' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"></circle><line x1="21" y1="21" x2="16.5" y2="16.5"></line><line x1="7.5" y1="11" x2="14.5" y2="11"></line></svg>
                    {{ app()->getLocale() === 'en' ? 'Zoom out' : 'Perkecil' }}
                </button>
                <button type="button" id="vbViewSingle" title="{{ app()->getLocale() === 'en' ? 'Zoom in (single page)' : 'Perbesar (satu halaman)' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"></circle><line x1="21" y1="21" x2="16.5" y2="16.5"></line><line x1="11" y1="7.5" x2="11" y2="14.5"></line><line x1="7.5" y1="11" x2="14.5" y2="11"></line></svg>
                    {{ app()->getLocale() === 'en' ? 'Zoom in' : 'Perbesar' }}
                </button>
            </div>
        </div>
    </div>

    <div class="viewer-content">
        @php
            $bookPages = $book->pages()->orderBy('order')->get();
            $bookId = 'flipBook_' . $book->id;
            $coverScaleFactor = 550 / 192;
        @endphp

        <div class="flip-book-wrapper">
            @if($book->pdf_path)
                <div id="pdfLoadingOverlay">
                    <div class="pdf-spinner"></div>
                    <p class="text-sm text-gray-600 font-medium">Memuat PDF...</p>
                    <p id="pdfProgress" class="text-xs text-gray-400 mt-1">0%</p>
                </div>
            @endif
            <div class="flip-book" id="{{ $bookId }}">
                {{-- Front Cover --}}
                @if($book->cover_image)
                    @php
                        $titlePos = $book->title_position ?? [];
                        $titleTx = ($titlePos['x'] ?? 0) * $coverScaleFactor;
                        $titleTy = ($titlePos['y'] ?? 0) * $coverScaleFactor;
                        $coverTexts = $book->cover_texts ?? [];
                    @endphp
                    <div class="page page-cover page-cover-top" data-density="hard">
                        <div class="page-content">
                            <div class="page-cover-inner">
                                <div class="cover-spine"></div>
                                <div class="cover-image-container">
                                    <img src="{{ asset('storage/' . $book->cover_image) }}" style="transform: translate({{ ($book->cover_position['x'] ?? 0) * $coverScaleFactor }}px, {{ ($book->cover_position['y'] ?? 0) * $coverScaleFactor }}px) scale({{ $book->cover_scale ?? 1 }});">
                                </div>
                                <div class="cover-title" style="transform: translate({{ $titleTx }}px, {{ $titleTy }}px);">
                                    {{ $book->translated_title }}
                                </div>
                                @if(count($coverTexts) > 0)
                                <div class="cover-extra-texts">
                                    @foreach($coverTexts as $ct)
                                        @php
                                            $ctPos = $ct['position'] ?? [];
                                            $ctTx = ($ctPos['x'] ?? 0) * $coverScaleFactor;
                                            $ctTy = ($ctPos['y'] ?? 0) * $coverScaleFactor;
                                        @endphp
                                        <span style="transform: translate({{ $ctTx }}px, {{ $ctTy }}px);">{{ $ct['text'] ?? '' }}</span>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                @if(!$book->pdf_path)
                {{-- Pages --}}
                @foreach($bookPages as $pageIndex => $page)
                    @php
                        $images = $page->page_images ?? [];
                        $imagePositions = $page->image_positions ?? [];
                        $imageHeight = $page->image_height ?? 40;
                        $imgSize = max(20, (int) $imageHeight);
                        $fitMode = $page->image_fit_mode ?? 'contained';
                        $imgHeightPct = $fitMode === 'fullbleed' ? $imgSize : $imgSize * 0.75;
                        $textPos = $page->text_position ?? ['x' => 0, 'y' => 0, 'width' => 45, 'height' => 30];
                    @endphp
                    <div class="page {{ $fitMode === 'fullbleed' ? 'fullbleed-page' : '' }}">
                        <div class="page-content {{ $fitMode === 'fullbleed' ? 'fullbleed' : '' }}">
                            @if($page->title)
                            <h2 class="page-header">{{ $page->translated_title }}</h2>
                            @endif
                            <div class="page-inner">
                                @if(count($images) > 0)
                                    @foreach($images as $imgIndex => $image)
                                    @php $pos = $imagePositions[$imgIndex] ?? ['x' => 0, 'y' => 0]; @endphp
                                    <div class="page-image" style="background-image: url('{{ asset('storage/' . $image) }}'); left: {{ $pos['x'] ?? 0 }}%; top: {{ $pos['y'] ?? 0 }}%; width: {{ $imgSize }}%; height: {{ $imgHeightPct }}%;"></div>
                                    @endforeach
                                @endif
                                @if($page->content)
                                <div class="page-text" style="left: {{ $textPos['x'] ?? 0 }}%; top: {{ $textPos['y'] ?? 0 }}%; width: {{ $textPos['width'] ?? 45 }}%; height: {{ $textPos['height'] ?? 30 }}%;">
                                    {!! nl2br(e($page->translated_content)) !!}
                                </div>
                                @endif
                            </div>
                            <div class="page-footer">{{ $pageIndex + 1 }}</div>
                        </div>
                    </div>
                @endforeach

                @if($bookPages->count() % 2 !== 0)
                    <div class="page"><div class="page-content"></div></div>
                @endif
                @endif

                {{-- Back Cover --}}
                @php
                    $backTitlePos = $book->back_title_position ?? [];
                    $backTitleTx = ($backTitlePos['x'] ?? 0) * $coverScaleFactor;
                    $backTitleTy = ($backTitlePos['y'] ?? 0) * $coverScaleFactor;
                    $backCoverTexts = $book->back_cover_texts ?? [];
                @endphp
                <div class="page page-cover page-cover-bottom" data-density="hard">
                    <div class="page-content">
                        <div class="page-cover-inner">
                            <div class="cover-spine"></div>
                            @if($book->back_cover_image)
                            <div class="cover-image-container">
                                <img src="{{ asset('storage/' . $book->back_cover_image) }}" style="transform: translate({{ ($book->back_cover_position['x'] ?? 0) * $coverScaleFactor }}px, {{ ($book->back_cover_position['y'] ?? 0) * $coverScaleFactor }}px) scale({{ $book->back_cover_scale ?? 1 }});">
                            </div>
                            @endif
                            @if($book->back_title)
                            <div class="cover-title" style="transform: translate({{ $backTitleTx }}px, {{ $backTitleTy }}px);">
                                {{ $book->back_title }}
                            </div>
                            @endif
                            @if(count($backCoverTexts) > 0)
                            <div class="cover-extra-texts">
                                @foreach($backCoverTexts as $bct)
                                    @php
                                        $bctPos = $bct['position'] ?? [];
                                        $bctTx = ($bctPos['x'] ?? 0) * $coverScaleFactor;
                                        $bctTy = ($bctPos['y'] ?? 0) * $coverScaleFactor;
                                    @endphp
                                    <span style="transform: translate({{ $bctTx }}px, {{ $bctTy }}px);">{{ $bct['text'] ?? '' }}</span>
                                @endforeach
                            </div>
                            @endif
                            @if(!$book->back_cover_image && !$book->back_title)
                            <h2 style="color: white;">THE END</h2>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="vb-controls" data-book="{{ $bookId }}">
            <button type="button" class="btn-prev">{{ app()->getLocale() === 'en' ? 'Previous page' : 'Halaman sebelumnya' }}</button>
            <span class="page-info" style="color:#4b5563; font-weight: 500; font-size: 0.95rem;">
                <span class="page-current">1</span> {{ app()->getLocale() === 'en' ? 'of' : 'dari' }} <span class="page-total">-</span>
            </span>
            <button type="button" class="btn-next">{{ app()->getLocale() === 'en' ? 'Next page' : 'Halaman selanjutnya' }}</button>

            <div class="vb-search">
                <input type="number" id="pageSearchInput" min="1" placeholder="No.">
                <button type="button" id="btnSearchPage">{{ app()->getLocale() === 'en' ? 'Go' : 'Cari' }}</button>
            </div>
        </div>
        <div class="vb-state-info" data-book="{{ $bookId }}">
            State: <i class="page-state">read</i>, orientation: <i class="page-orientation">landscape</i>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var appLocale = '{{ app()->getLocale() }}';
            var bookId = '{{ $bookId }}';
            var bookEl = document.getElementById(bookId);
            var wrapperEl = bookEl.parentElement; // .flip-book-wrapper

            var pageFlip = null;
            var currentPageIndex = 0;
            var viewMode = 'double'; // 'double' | 'single'

            // Higher internal resolution so the rendered pages look sharp when zoomed.
            // size: stretch will scale to the wrapper; larger base = more pixels for backgrounds.
            var BASE_WIDTH = 1100;
            var BASE_HEIGHT = 1466;

            function updateCoverMode() {
                if (!pageFlip) return;
                var totalPages = pageFlip.getPageCount();
                var isFrontCover = (currentPageIndex === 0);
                var isBackCover = (currentPageIndex >= totalPages - 1);
                var orientation = pageFlip.getOrientation ? pageFlip.getOrientation() : 'landscape';

                bookEl.classList.remove('is-front-cover', 'is-back-cover');
                if (orientation === 'landscape') {
                    if (isFrontCover) bookEl.classList.add('is-front-cover');
                    else if (isBackCover) bookEl.classList.add('is-back-cover');
                }
            }

            function initBook() {
                if (typeof window.PageFlip === 'undefined') {
                    setTimeout(initBook, 100);
                    return;
                }

                var pages = bookEl.querySelectorAll('.page');
                if (pages.length === 0) return;

                pageFlip = new window.PageFlip(bookEl, {
                    width: BASE_WIDTH,
                    height: BASE_HEIGHT,
                    size: "stretch",
                    minWidth: 200,
                    maxWidth: 2000,
                    minHeight: 300,
                    maxHeight: 2700,
                    maxShadowOpacity: 0.5,
                    showCover: true,
                    mobileScrollSupport: false
                });

                pageFlip.loadFromHTML(pages);

                updateCoverMode();

                var controls = document.querySelector('.vb-controls');
                var stateInfo = document.querySelector('.vb-state-info');

                if (controls) {
                    var totalPages = pageFlip.getPageCount();
                    controls.querySelector('.page-total').innerText = totalPages;
                    controls.querySelector('.page-current').innerText = 1;

                    var searchInput = document.getElementById('pageSearchInput');
                    if (searchInput) searchInput.setAttribute('max', totalPages);

                    controls.querySelector('.btn-prev').addEventListener('click', function() { pageFlip.flipPrev(); });
                    controls.querySelector('.btn-next').addEventListener('click', function() { pageFlip.flipNext(); });
                    pageFlip.on('flip', function(e) {
                        currentPageIndex = e.data;
                        controls.querySelector('.page-current').innerText = e.data + 1;
                        updateCoverMode();
                    });
                }

                // Page Search Functionality
                var searchInput = document.getElementById('pageSearchInput');
                var searchBtn = document.getElementById('btnSearchPage');

                function jumpToPage() {
                    var pageNum = parseInt(searchInput.value);
                    if (isNaN(pageNum)) return;

                    var total = pageFlip.getPageCount();
                    if (pageNum >= 1 && pageNum <= total) {
                        pageFlip.flip(pageNum - 1);
                    } else {
                        alert(appLocale === 'en' ? 'Page not found' : 'Halaman tidak ditemukan');
                    }
                }

                if (searchBtn) searchBtn.addEventListener('click', jumpToPage);
                if (searchInput) {
                    searchInput.addEventListener('keypress', function(e) {
                        if (e.key === 'Enter') jumpToPage();
                    });
                }

                if (stateInfo) {
                    stateInfo.querySelector('.page-orientation').innerText = pageFlip.getOrientation();
                    pageFlip.on('changeState', function(e) { stateInfo.querySelector('.page-state').innerText = e.data; });
                    pageFlip.on('changeOrientation', function(e) {
                        stateInfo.querySelector('.page-orientation').innerText = e.data;
                        updateCoverMode();
                    });
                }
            }

            // View mode toggle — only changes wrapper width; StPageFlip auto-detects
            // portrait vs landscape based on available width, so no destroy/re-init needed.
            var btnDouble = document.getElementById('vbViewDouble');
            var btnSingle = document.getElementById('vbViewSingle');
            function setViewMode(mode) {
                if (mode === viewMode) return;
                viewMode = mode;

                if (mode === 'single') {
                    wrapperEl.classList.add('single-view');
                    document.body.classList.add('single-view-active');
                    btnSingle.classList.add('active');
                    btnDouble.classList.remove('active');
                } else {
                    wrapperEl.classList.remove('single-view');
                    document.body.classList.remove('single-view-active');
                    btnDouble.classList.add('active');
                    btnSingle.classList.remove('active');
                }

                // Nudge the PageFlip instance to re-measure the container.
                // StPageFlip listens to window resize, so dispatch one after the
                // CSS transition settles.
                setTimeout(function() {
                    window.dispatchEvent(new Event('resize'));
                }, 50);
                setTimeout(function() {
                    window.dispatchEvent(new Event('resize'));
                }, 350);
            }
            if (btnDouble) btnDouble.addEventListener('click', function() { setViewMode('double'); });
            if (btnSingle) btnSingle.addEventListener('click', function() { setViewMode('single'); });

            // PDF Integration
            @if($book->pdf_path)
            var pdfUrl = '{{ asset("storage/" . $book->pdf_path) }}';
            var pdfDoc = null;
            
            async function loadPdf() {
                var loadingOverlay = document.getElementById('pdfLoadingOverlay');
                var progressText = document.getElementById('pdfProgress');
                
                try {
                    const loadingTask = pdfjsLib.getDocument(pdfUrl);
                    loadingTask.onProgress = function(progress) {
                        if (progress.total > 0) {
                            var pct = Math.round((progress.loaded / progress.total) * 100);
                            progressText.innerText = `Mengunduh PDF: ${pct}%`;
                        }
                    };
                    
                    pdfDoc = await loadingTask.promise;
                    const hasCmsFrontCover = bookEl.querySelector('.page-cover-top') !== null;
                    const hasCmsBackCover = bookEl.querySelector('.page-cover-bottom') !== null;
                    const startPage = hasCmsFrontCover ? 2 : 1;
                    
                    // 1. Create blank placeholders for ALL pages immediately
                    const backCover = bookEl.querySelector('.page-cover-bottom');
                    for (let i = startPage; i <= pdfDoc.numPages; i++) {
                        const pageDiv = document.createElement('div');
                        pageDiv.className = 'page fullbleed-page pdf-page-placeholder';
                        pageDiv.id = `pdf-page-${i}`;
                        
                        if (!hasCmsFrontCover && i === 1) pageDiv.setAttribute('data-density', 'hard');
                        if (!hasCmsBackCover && i === pdfDoc.numPages) pageDiv.setAttribute('data-density', 'hard');
                        
                        pageDiv.innerHTML = `
                            <div class="page-content fullbleed">
                                <div class="page-inner">
                                    <div class="page-image" style="background-color: #f3f4f6; display: flex; align-items: center; justify-content: center; width: 100%; height: 100%;">
                                        <div class="pdf-spinner" style="width: 20px; height: 20px; border-width: 2px;"></div>
                                    </div>
                                </div>
                                <div class="page-footer">${hasCmsFrontCover ? i - 1 : i}</div>
                            </div>
                        `;
                        
                        if (backCover) {
                            bookEl.insertBefore(pageDiv, backCover);
                        } else {
                            bookEl.appendChild(pageDiv);
                        }
                    }

                    // 2. Add empty page if needed for even count
                    const totalPages = bookEl.querySelectorAll('.page').length;
                    if (totalPages % 2 !== 0) {
                        const emptyPage = document.createElement('div');
                        emptyPage.className = 'page';
                        emptyPage.innerHTML = '<div class="page-content"></div>';
                        if (backCover) bookEl.insertBefore(emptyPage, backCover);
                        else bookEl.appendChild(emptyPage);
                    }
                    
                    // 3. Open the book immediately!
                    loadingOverlay.style.display = 'none';
                    initBook();
                    
                    // 4. Start rendering images incrementally in the background
                    renderPagesIncremental(startPage);
                    
                } catch (error) {
                    console.error('Error loading PDF:', error);
                    progressText.innerText = 'Gagal memuat PDF.';
                    progressText.classList.add('text-red-500');
                }
            }

            async function renderPagesIncremental(startPage) {
                const hasCmsFrontCover = bookEl.querySelector('.page-cover-top') !== null;
                
                for (let i = startPage; i <= pdfDoc.numPages; i++) {
                    try {
                        const page = await pdfDoc.getPage(i);
                        const viewport = page.getViewport({ scale: 2 });
                        
                        let canvas, context;
                        if (typeof OffscreenCanvas !== 'undefined') {
                            canvas = new OffscreenCanvas(viewport.width, viewport.height);
                            context = canvas.getContext('2d');
                        } else {
                            canvas = document.createElement('canvas');
                            canvas.height = viewport.height;
                            canvas.width = viewport.width;
                            context = canvas.getContext('2d');
                        }
                        
                        await page.render({ canvasContext: context, viewport: viewport }).promise;
                        
                        let dataUrl;
                        if (typeof OffscreenCanvas !== 'undefined' && canvas.convertToBlob) {
                            const blob = await canvas.convertToBlob({ type: 'image/jpeg', quality: 0.8 });
                            dataUrl = await new Promise(resolve => {
                                const reader = new FileReader();
                                reader.onloadend = () => resolve(reader.result);
                                reader.readAsDataURL(blob);
                            });
                        } else {
                            dataUrl = canvas.toDataURL('image/jpeg', 0.8);
                        }
                        
                        // Update the placeholder
                        const pageDiv = document.getElementById(`pdf-page-${i}`);
                        if (pageDiv) {
                            const imgDiv = pageDiv.querySelector('.page-image');
                            imgDiv.style.backgroundImage = `url(${dataUrl})`;
                            imgDiv.style.backgroundColor = 'transparent';
                            imgDiv.style.backgroundSize = '100% 100%';
                            imgDiv.innerHTML = ''; // Remove spinner
                        }
                        
                        // Small yield to keep UI smooth
                        await new Promise(resolve => setTimeout(resolve, 50));
                    } catch (e) {
                        console.warn(`Failed to render page ${i}:`, e);
                    }
                }
            }

            async function renderPdfPage(num, isOffset = false) {
                const page = await pdfDoc.getPage(num);
                const viewport = page.getViewport({ scale: 2 });
                
                let canvas, context;
                if (typeof OffscreenCanvas !== 'undefined') {
                    canvas = new OffscreenCanvas(viewport.width, viewport.height);
                    context = canvas.getContext('2d');
                } else {
                    canvas = document.createElement('canvas');
                    canvas.height = viewport.height;
                    canvas.width = viewport.width;
                    context = canvas.getContext('2d');
                }
                
                await page.render({ canvasContext: context, viewport: viewport }).promise;
                
                let dataUrl;
                if (typeof OffscreenCanvas !== 'undefined' && canvas.convertToBlob) {
                    const blob = await canvas.convertToBlob({ type: 'image/jpeg', quality: 0.8 });
                    dataUrl = await new Promise(resolve => {
                        const reader = new FileReader();
                        reader.onloadend = () => resolve(reader.result);
                        reader.readAsDataURL(blob);
                    });
                } else {
                    dataUrl = canvas.toDataURL('image/jpeg', 0.8);
                }
                
                // Create the flipbook page element
                const pageDiv = document.createElement('div');
                pageDiv.className = 'page fullbleed-page';
                
                // If it's a PDF-only book (no CMS covers), make first/last hard
                const hasCmsFrontCover = bookEl.querySelector('.page-cover-top') !== null;
                const hasCmsBackCover = bookEl.querySelector('.page-cover-bottom') !== null;
                
                if (!hasCmsFrontCover && num === 1) pageDiv.setAttribute('data-density', 'hard');
                if (!hasCmsBackCover && num === pdfDoc.numPages) pageDiv.setAttribute('data-density', 'hard');
                
                const contentDiv = document.createElement('div');
                contentDiv.className = 'page-content fullbleed';
                
                const innerDiv = document.createElement('div');
                innerDiv.className = 'page-inner';
                
                const imgDiv = document.createElement('div');
                imgDiv.className = 'page-image';
                imgDiv.style.backgroundImage = `url(${dataUrl})`;
                imgDiv.style.left = '0';
                imgDiv.style.top = '0';
                imgDiv.style.width = '100%';
                imgDiv.style.height = '100%';
                imgDiv.style.backgroundSize = '100% 100%';
                
                const footerDiv = document.createElement('div');
                footerDiv.className = 'page-footer';
                // Adjust page number display if skipping first page
                footerDiv.innerText = isOffset ? num - 1 : num;
                
                innerDiv.appendChild(imgDiv);
                contentDiv.appendChild(innerDiv);
                contentDiv.appendChild(footerDiv);
                pageDiv.appendChild(contentDiv);
                
                return pageDiv;
            }

            loadPdf();
            @else
            initBook();
            @endif
        });
    </script>
    {{-- Login required modal --}}
    @if(isset($requiresLoginModal) && $requiresLoginModal)
        @include('partials.login_modal', [
            'previewImage' => $loginModalPreview ?? null,
            'roomName' => $loginModalRoomName ?? null
        ])
    @endif
</body>
</html>
