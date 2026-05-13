@extends('layouts.guest')

@section('title', $book->translated_title . ' — ' . config('app.name'))

@push('styles')
<link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
<link rel="stylesheet" href="{{ asset('css/feature-page.css') }}">
<link rel="stylesheet" href="{{ asset('css/virtual_tour.css') }}">
<style>
    .book-detail-container {
        padding: 4rem 0;
        background: #f9fafb;
        min-height: calc(100vh - 300px);
    }
    .book-detail-wrapper {
        display: flex;
        gap: 3rem;
        background: white;
        padding: 2.5rem;
        border-radius: 1.5rem;
        shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
    }
    .book-cover-side {
        flex: 0 0 350px;
    }
    .book-cover-side img {
        width: 100%;
        height: auto;
        border-radius: 0.75rem;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    .book-info-side {
        flex: 1;
    }
    .book-title {
        font-size: 2.25rem;
        font-weight: 800;
        color: #111827;
        margin-bottom: 2rem;
        line-height: 1.2;
    }
    .metadata-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 2.5rem;
    }
    .metadata-table td {
        padding: 0.75rem 0;
        vertical-align: top;
        font-size: 1rem;
    }
    .metadata-label {
        width: 180px;
        font-weight: 700;
        color: #374151;
    }
    .metadata-separator {
        width: 20px;
        color: #9ca3af;
    }
    .metadata-value {
        color: #4b5563;
    }
    .read-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.6rem 1.7rem;
        background-color: #057ece;
        color: white;
        font-weight: 600;
        font-size: 1.125rem;
        border-radius: 0.5rem;
        text-decoration: none;
        transition: all 0.2s;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    .read-button:hover {
        background-color: #055bab;
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        color: white;
    }
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: #6b7280;
        text-decoration: none;
        font-size: 0.875rem;
        margin-bottom: 1.5rem;
        transition: color 0.2s;
    }
    .back-link:hover {
        color: #111827;
    }

    @media (max-width: 991px) {
        .book-detail-wrapper {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        .book-cover-side {
            flex: 0 0 auto;
            width: 280px;
        }
        .metadata-table td {
            display: block;
            text-align: left;
        }
        .metadata-label, .metadata-separator {
            display: inline-block;
        }
        .metadata-table tr {
            display: flex;
            flex-wrap: wrap;
            padding: 0.5rem 0;
            border-bottom: 1px solid #f3f4f6;
        }
    }
</style>
@endpush

@section('content')
{{-- Hero section for consistency --}}
<div class="vt-hero" style="padding: 3rem 0;">
    <div class="container">
        @if($feature->parent)
            <p style="font-size:0.8rem;opacity:0.6;margin-bottom:0.5rem;text-transform:uppercase;letter-spacing:0.08em;">
                {{ app()->getLocale() === 'en' && $feature->parent->name_en ? $feature->parent->name_en : $feature->parent->name }}
            </p>
        @endif
        <h1>{{ $book->translated_title }}</h1>
    </div>
</div>

<div class="book-detail-container">
    <div class="container">
        <a href="{{ url($feature->path) }}" class="back-link">
            <svg style="width:1.25rem;height:1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            {{ app()->getLocale() === 'en' ? 'Back to Selection' : 'Kembali ke Pemilihan' }}
        </a>

        <div class="book-detail-wrapper">
            <div class="book-cover-side">
                @if($book->thumbnail)
                    <img src="{{ asset('storage/'.$book->thumbnail) }}" alt="{{ $book->translated_title }}">
                @elseif($book->cover_image)
                    <img src="{{ asset('storage/'.$book->cover_image) }}" alt="{{ $book->translated_title }}">
                @else
                    <div style="background:#f3f4f6; aspect-ratio:3/4; display:flex; align-items:center; justify-content:center; border-radius:0.75rem; font-size:5rem;">📚</div>
                @endif
            </div>

            <div class="book-info-side">
                <h2 class="book-title">{{ $book->translated_title }}</h2>

                <table class="metadata-table">
                    <tr>
                        <td class="metadata-label">{{ app()->getLocale() === 'en' ? 'Author' : 'Penulis' }}</td>
                        <td class="metadata-separator">:</td>
                        <td class="metadata-value">{{ $book->author ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td class="metadata-label">{{ app()->getLocale() === 'en' ? 'Dimensions' : 'Dimensi' }}</td>
                        <td class="metadata-separator">:</td>
                        <td class="metadata-value">{{ $book->dimensions ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td class="metadata-label">{{ app()->getLocale() === 'en' ? 'Total Pages' : 'Jumlah Halaman' }}</td>
                        <td class="metadata-separator">:</td>
                        <td class="metadata-value">
                            @php
                                $detailPages = (!empty($book->pdf_path) && !empty($book->total_pages)) 
                                    ? $book->total_pages 
                                    : $book->pages()->count();
                                $detailPages = preg_replace('/(\s*halaman|\s*pages)/i', '', $detailPages);
                            @endphp
                            {{ $detailPages }} {{ app()->getLocale() === 'en' ? 'Pages' : 'Halaman' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="metadata-label">{{ app()->getLocale() === 'en' ? 'Weight' : 'Berat' }}</td>
                        <td class="metadata-separator">:</td>
                        <td class="metadata-value">{{ $book->weight ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td class="metadata-label">{{ app()->getLocale() === 'en' ? 'Language' : 'Bahasa' }}</td>
                        <td class="metadata-separator">:</td>
                        <td class="metadata-value">{{ $book->language ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td class="metadata-label">{{ app()->getLocale() === 'en' ? 'Publisher' : 'Penerbit' }}</td>
                        <td class="metadata-separator">:</td>
                        <td class="metadata-value">{{ $book->publisher ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td class="metadata-label">{{ app()->getLocale() === 'en' ? 'Publication Year' : 'Tahun Terbit' }}</td>
                        <td class="metadata-separator">:</td>
                        <td class="metadata-value">{{ $book->publication_year ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td class="metadata-label">ISBN</td>
                        <td class="metadata-separator">:</td>
                        <td class="metadata-value">{{ $book->isbn ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td class="metadata-label">{{ app()->getLocale() === 'en' ? 'Synopsis' : 'Sinopsis' }}</td>
                        <td class="metadata-separator">:</td>
                        <td class="metadata-value">{{ $book->synopsis ?: '-' }}</td>
                    </tr>
                </table>

                <a href="?read={{ $book->id }}" class="read-button">
                    {{ app()->getLocale() === 'en' ? 'Read' : 'Baca' }}
                </a>
            </div>
        </div>
    </div>
</div>

@endsection
