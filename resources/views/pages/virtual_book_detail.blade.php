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
        flex-direction: column;
        gap: 3.5rem;
        background: white;
        padding: 3rem;
        border-radius: 1.5rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
    }
    .book-main-section {
        display: flex;
        gap: 3.5rem;
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
    .book-cover-placeholder {
        background: #f3f4f6;
        aspect-ratio: 3/4;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.75rem;
        font-size: 5rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
    .book-info-side {
        flex: 1;
        min-width: 0;
    }
    .book-title {
        font-size: 2.25rem;
        font-weight: 800;
        color: #111827;
        margin-bottom: 1.75rem;
        line-height: 1.2;
    }
    .book-description-box {
        background: #f8fafc;
        border-left: 4px solid #057ece;
        padding: 1.75rem;
        border-radius: 0.75rem;
        margin-bottom: 2rem;
        box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.02);
    }
    .description-heading {
        font-size: 1.125rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 1rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .book-description-content {
        color: #334155;
        line-height: 1.7;
        font-size: 1.05rem;
    }
    .book-description-content p {
        margin-bottom: 1rem;
    }
    .book-description-content p:last-child {
        margin-bottom: 0;
    }
    .book-metadata-section {
        border-top: 2px solid #f1f5f9;
        padding-top: 2.5rem;
    }
    .metadata-section-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 1.75rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .metadata-table {
        width: 100%;
        border-collapse: collapse;
    }
    .metadata-table td {
        padding: 0.85rem 0;
        vertical-align: top;
        font-size: 1.025rem;
        border-bottom: 1px solid #f8fafc;
    }
    .metadata-table tr:last-child td {
        border-bottom: none;
    }
    .metadata-label {
        font-weight: 700;
        color: #334155;
    }
    .metadata-separator {
        width: 25px;
        color: #94a3b8;
        text-align: center;
    }
    .metadata-value {
        color: #475569;
        line-height: 1.6;
    }
    .read-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.85rem 2rem;
        background-color: #057ece;
        color: white;
        font-weight: 600;
        font-size: 1.125rem;
        border-radius: 0.75rem;
        text-decoration: none;
        transition: all 0.2s;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        width: 100%;
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
        .book-main-section {
            flex-direction: column;
            align-items: center;
        }
        .book-cover-side {
            flex: 0 0 auto;
            width: 280px;
            margin-bottom: 2rem;
        }
        .book-info-side {
            width: 100%;
        }
        .metadata-table tr {
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            align-items: baseline;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 0.75rem;
            gap: 0;
        }
        .metadata-table td {
            display: block;
            padding: 0;
        }
        .metadata-label {
            width: auto;
            padding-top: 0;
            flex-shrink: 0;
        }
        .metadata-separator {
            display: inline;
            padding: 0 4px;
            color: #94a3b8;
            flex-shrink: 0;
        }
        .metadata-value {
            flex: 1;
            min-width: 0;
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
            <!-- Bagian Atas: Cover + Tombol Baca (Kiri) & Judul + Deskripsi (Kanan) -->
            <div class="book-main-section">
                <div class="book-cover-side">
                    @if($book->thumbnail)
                        <img src="{{ asset('storage/'.$book->thumbnail) }}" alt="{{ $book->translated_title }}">
                    @elseif($book->cover_image)
                        <img src="{{ asset('storage/'.$book->cover_image) }}" alt="{{ $book->translated_title }}">
                    @else
                        <div class="book-cover-placeholder">📚</div>
                    @endif
                    
                    <div class="mt-6 text-center">
                        <a href="?read={{ $book->id }}" class="read-button">
                            <svg style="width:1.35rem; height:1.35rem; margin-right:0.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            {{ app()->getLocale() === 'en' ? 'Read Book' : 'Baca Buku' }}
                        </a>
                    </div>
                </div>

                <div class="book-info-side">
                    <h2 class="book-title">{{ $book->translated_title }}</h2>
                    
                    <div class="book-description-box">
                        <h3 class="description-heading">
                            <svg class="w-5 h-5 text-[#057ece]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            {{ app()->getLocale() === 'en' ? 'Description' : 'Deskripsi' }}
                        </h3>
                        <div class="book-description-content rte-guest-content">
                            @if($book->translated_description)
                                {!! $book->translated_description !!}
                            @else
                                <p class="text-gray-500 italic">{{ app()->getLocale() === 'en' ? 'No description available for this book.' : 'Belum ada deskripsi untuk buku ini.' }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bagian Bawah: Informasi Buku (Penulis sampai Sinopsis) -->
            <div class="book-metadata-section">
                <h3 class="metadata-section-title">
                    <svg class="w-6 h-6 text-[#057ece]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    {{ app()->getLocale() === 'en' ? 'Book Information' : 'Informasi Buku' }}
                </h3>

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
            </div>
        </div>
    </div>
</div>

@endsection
