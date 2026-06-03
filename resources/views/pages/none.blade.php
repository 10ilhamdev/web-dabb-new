@extends('layouts.guest')

@section('title', $feature->name . ' — ' . config('app.name'))

@push('styles')
<link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
<link rel="stylesheet" href="{{ asset('css/feature-page.css') }}">
<link rel="stylesheet" href="{{ asset('cms_rte/runtime/guest_richtexteditor_content.css?v=' . (file_exists(public_path('cms_rte/runtime/guest_richtexteditor_content.css')) ? filemtime(public_path('cms_rte/runtime/guest_richtexteditor_content.css')) : time())) }}">
@endpush

@section('content')

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

{{-- Hero --}}
<div class="feature-hero">
    <div class="container">
        @if($feature->parent)
            <p style="font-size:0.8rem;opacity:0.7;margin-bottom:0.4rem;text-transform:uppercase;letter-spacing:0.08em;color:#fff;">
                {{ app()->getLocale() === 'en' && $feature->parent->name_en ? $feature->parent->name_en : $feature->parent->name }}
            </p>
        @endif
        <h1>{{ app()->getLocale() === 'en' && $feature->name_en ? $feature->name_en : $feature->name }}</h1>
    </div>
</div>

{{-- CMS Content Sections --}}
<div class="feature-content" style="padding-bottom: 5rem;">
    <div class="container">
        @if($feature->content)
        <div class="feature-simple-content">
            <div class="rte-content rte-content-body">
                {!! app()->getLocale() === 'en' && $feature->content_en ? $feature->content_en : $feature->content !!}
            </div>
        </div>
        @else
        <div style="text-align:center;padding:4rem;color:#9ca3af;">
            <p>{{ __('home.virtual_3d_tour.no_rooms') }}</p>
        </div>
        @endif
    </div>
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
</div>

@endsection

@push('scripts')
<script src="{{ asset('js/pages/feature.js') }}"></script>
@endpush
