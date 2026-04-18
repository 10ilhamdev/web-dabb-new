@extends('layouts.guest')

@section('title', $feature->name . ' — ' . config('app.name'))

@push('styles')
<link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
<link rel="stylesheet" href="{{ asset('css/feature-page.css') }}">
<link rel="stylesheet" href="{{ asset('css/virtual_tour.css') }}">
<style>
    .vb-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }

    .vb-book-section {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1rem;
        margin-bottom: 3rem;
        width: 100%;
    }

    .vb-book-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }

    .flip-book {
        box-shadow: 0 0 20px 0 rgba(0, 0, 0, 0.5);
        background-size: cover;
        margin: 0 auto;
        transition: transform 0.6s cubic-bezier(0.645, 0.045, 0.355, 1);
    }

    .flip-book.is-front-cover {
        transform: translateX(-25%);
    }

    .flip-book.is-back-cover {
        transform: translateX(25%);
    }

    /* Ensure enough width for 2-page spread on desktop */
    @media (min-width: 768px) {
        .vb-container {
            max-width: 1200px;
        }
        .flip-book-wrapper {
            width: 100%;
            display: flex;
            justify-content: center;
            overflow: hidden;
            padding: 20px 0;
            min-height: 50vh;
        }
    }

    .page {
        padding: 20px;
        background-color: hsl(35, 55%, 98%);
        color: hsl(35, 35%, 35%);
        border: solid 1px hsl(35, 20%, 70%);
        overflow: hidden;
    }

    .page .page-content {
        width: 100%;
        height: 100%;
        position: relative;
    }

    .page .page-content .page-header {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 30px;
        font-size: 100%;
        text-transform: uppercase;
        text-align: center;
        z-index: 2;
    }

    .page .page-content .page-inner {
        position: absolute;
        left: 15px;
        right: 0;
        top: 30px;
        bottom: 30px;
    }

    .page .page-content .page-image {
        position: absolute;
        background-size: contain;
        background-position: center center;
        background-repeat: no-repeat;
    }

    .page .page-content .page-text {
        position: absolute;
        font-size: 80%;
        text-align: justify;
        padding: 8px;
        box-sizing: border-box;
        overflow: auto;
    }

    .page .page-content .page-footer {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 30px;
        border-top: solid 1px hsl(35, 55%, 90%);
        font-size: 80%;
        color: hsl(35, 20%, 50%);
        z-index: 2;
    }

    .page.--left {
        border-right: 0;
        box-shadow: inset -7px 0 30px -7px rgba(0, 0, 0, 0.4);
    }

    .page.--right {
        border-left: 0;
        box-shadow: inset 7px 0 30px -7px rgba(0, 0, 0, 0.4);
    }

    .page.--right .page-footer {
        text-align: right;
    }

    .page.hard {
        background-color: hsl(35, 50%, 90%);
        border: solid 1px hsl(35, 20%, 50%);
    }

    .page.page-cover {
        background-color: transparent;
        color: hsl(35, 35%, 35%);
        border: solid 1px hsl(35, 20%, 50%);
        overflow: hidden;
        padding: 0;
    }

    .page.page-cover .page-cover-inner {
        position: absolute;
        inset: 0;
        background: linear-gradient(to bottom, #b45309, #78350f);
        overflow: hidden;
    }

    .page.page-cover .cover-spine {
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 8px;
        background: linear-gradient(to right, #78350f, #b45309);
    }

    .page.page-cover .cover-image-container {
        position: absolute;
        top: 12px;
        left: 18px;
        right: 12px;
        bottom: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255,255,255,0.1);
        overflow: hidden;
    }

    .page.page-cover .cover-image-container img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        pointer-events: none;
    }

    .page.page-cover .cover-title {
        position: absolute;
        top: 16px;
        left: 0;
        right: 0;
        text-align: center;
        padding: 0 16px;
        color: white;
        font-weight: 600;
        font-size: 1.1rem;
        text-shadow: 0 2px 4px rgba(0,0,0,0.5);
        line-height: 1.3;
        z-index: 1;
    }

    .page.page-cover .cover-extra-texts {
        position: absolute;
        bottom: 16px;
        left: 0;
        right: 0;
        text-align: center;
        padding: 0 16px;
        z-index: 1;
    }

    .page.page-cover .cover-extra-texts span {
        display: block;
        color: rgba(255,255,255,0.8);
        font-size: 0.7rem;
        text-shadow: 0 1px 3px rgba(0,0,0,0.5);
        margin-top: 4px;
    }

    .page.page-cover h2 {
        text-align: center;
        padding-top: 50%;
        font-size: 210%;
    }

    .page.page-cover-top {
        box-shadow: inset 0px 0 30px 0px rgba(36, 10, 3, 0.5), -2px 0 5px 2px rgba(0, 0, 0, 0.4);
    }

    .page.page-cover-bottom {
        box-shadow: inset 0px 0 30px 0px rgba(36, 10, 3, 0.5), 10px 0 8px 0px rgba(0, 0, 0, 0.4);
    }

    .vb-controls {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .vb-controls .btn-prev,
    .vb-controls .btn-next {
        padding: 0.5rem 1.25rem;
        background: #0d9488;
        color: white;
        border: none;
        border-radius: 0.375rem;
        cursor: pointer;
        font-weight: 500;
        font-size: 0.9rem;
        transition: background 0.2s;
    }

    .vb-controls .btn-prev:hover,
    .vb-controls .btn-next:hover {
        background: #0f766e;
    }

    .vb-controls .page-info {
        font-size: 0.9rem;
        color: #374151;
    }

    .vb-controls .page-info span {
        font-weight: 600;
    }

    .vb-state-info {
        text-align: center;
        font-size: 0.85rem;
        color: #6b7280;
    }

    .vb-state-info i {
        font-style: italic;
    }
</style>
@endpush

@section('content')

<script>
window.onerror = function(msg, url, line, col, error) {
   alert("Error: " + msg + "\nLine: " + line + "\nCol: " + col);
};
</script>

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

{{-- Blue gradient hero --}}
<div class="vt-hero">
    <div class="container">
        @if($feature->parent)
            <p style="font-size:0.8rem;opacity:0.6;margin-bottom:0.5rem;text-transform:uppercase;letter-spacing:0.08em;">
                {{ app()->getLocale() === 'en' && $feature->parent->name_en ? $feature->parent->name_en : $feature->parent->name }}
            </p>
        @endif
        <h1>{{ app()->getLocale() === 'en' && $feature->name_en ? $feature->name_en : $feature->name }}</h1>
        <p>{{ app()->getLocale() === 'en' ? 'Virtual Book Exhibition - Flip through pages like a real book' : 'Pameran Arsip Virtual Buku - Balik halaman layaknya buku nyata' }}</p>
    </div>
</div>

{{-- Room Grid --}}
<section class="vt-rooms-section">
    <div class="container">
        <h2 class="vt-section-title">{{ app()->getLocale() === 'en' ? 'Select Book' : 'Pilih Buku' }}</h2>
        <p class="vt-section-sub">{{ app()->getLocale() === 'en' ? 'Click one of the books below to start reading' : 'Klik salah satu buku di bawah untuk mulai membaca' }}</p>

        @if($books->isEmpty())
            <div style="text-align:center;padding:4rem;color:#9ca3af;">
                <svg style="width:64px;height:64px;margin:0 auto 1rem;display:block;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/></svg>
                <p>{{ app()->getLocale() === 'en' ? 'No books available yet' : 'Belum ada buku' }}</p>
            </div>
        @else
            <div class="vt-rooms-grid">
                @foreach($books as $book)
                <a href="?read={{ $book->id }}" class="vt-room-card" style="text-decoration:none; color:inherit;">
                    <div class="vt-room-thumb">
                        @if($book->thumbnail)
                            <img src="{{ asset('storage/'.$book->thumbnail) }}" alt="{{ $book->title }}" loading="lazy">
                        @elseif($book->cover_image)
                            <img src="{{ asset('storage/'.$book->cover_image) }}" alt="{{ $book->title }}" loading="lazy">
                        @else
                            <div class="vt-room-thumb-placeholder" style="background:#f3f4f6; display:flex; align-items:center; justify-content:center; height:100%; font-size:3rem;">📚</div>
                        @endif
                        <div class="vt-enter-btn"><span>{{ app()->getLocale() === 'en' ? 'Read Book' : 'Baca Buku' }}</span></div>
                        <div class="vt-room-badge">
                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332-.477-4.5-1.253"></path></svg>
                            {{ $book->pages()->count() }} {{ app()->getLocale() === 'en' ? 'Pages' : 'Halaman' }}
                        </div>
                    </div>
                    <div class="vt-room-info">
                        <h3 class="vt-room-name">{{ app()->getLocale() === 'en' && $book->title_en ? $book->title_en : $book->title }}</h3>
                    </div>
                </a>
                @endforeach
            </div>
        @endif
    </div>
</section>

{{-- Login Modal (if required) --}}
@if(isset($requiresLoginModal) && $requiresLoginModal)
    @include('partials.login_modal', [
        'previewImage' => $loginModalPreview ?? null,
        'roomName'     => $loginModalRoomName ?? null
    ])
@endif

@endsection
