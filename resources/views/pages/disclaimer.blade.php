@extends('layouts.guest')

@section('title', $title . ' — ' . config('app.name'))

@push('styles')
<link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
<link rel="stylesheet" href="{{ asset('css/feature-page.css') }}">
@endpush

@section('content')

{{-- Breadcrumb --}}
<div class="feature-breadcrumb">
    <div class="container">
        <span class="current">{{ $title }}</span>
    </div>
</div>

{{-- Hero --}}
<div class="feature-hero" style="min-height: 180px;">
    <div class="container">
        <h1 style="text-transform: uppercase;">{{ $title }}</h1>
    </div>
</div>

{{-- Content Section --}}
<div class="feature-content" style="padding-bottom: 5rem;">
    <div class="container">

        <div style="background: #fff; border-radius: 12px; display: flex; gap: 10px; width: 100%;">

            <div style="flex-shrink: 0; padding-top: 5px;">
                <svg fill="none" stroke="#3598db" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" width="32" height="32">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
            </div>

            <div style="flex: 1; min-width: 0;">
                <h2 style="font-size: 1.5rem; font-weight: 700; color: #222; margin: 5px 0 1rem;">{{ $title }}</h2>

                <div style="font-size: 0.95rem; color: #555; line-height: 1.8;">
                    <style>
                        /* Penyesuaian agar align (rata kiri/tengah/kanan) dari editor terbaca di website */
                        .rte-content img {
                            display: inline-block;
                            max-width: 100%;
                            height: auto;
                        }
                        .rte-content p {
                            margin-bottom: 1rem;
                        }
                        .rte-content a {
                            color: #3598db;
                            text-decoration: underline;
                        }
                    </style>
                    <div class="rte-content" style="border-left: 5px solid #eaeaea; padding-left: 1.5rem; overflow-x: hidden; word-wrap: break-word;">
                        {!! $content ?? '' !!}
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection
