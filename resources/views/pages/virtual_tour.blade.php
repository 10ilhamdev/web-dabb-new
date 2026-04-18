@extends('layouts.guest')

@section('title', $feature->name . ' — ' . config('app.name'))

@push('styles')
<link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
<link rel="stylesheet" href="{{ asset('css/feature-page.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.css"> 
<link rel="stylesheet" href="{{ asset('css/virtual_tour.css') }}">
@endpush

@section('content')

{{-- Breadcrumb --}}
<div class="feature-breadcrumb">
    <div class="container">
        @if($feature->parent)
            <a href="{{ url($feature->parent->path ?? '#') }}">{{ app()->getLocale() === 'en' && $feature->parent->name_en ? $feature->parent->name_en : $feature->parent->name }}</a>
            <span class="sep">/</span>
        @endif
        <span class="current">{{ app()->getLocale() === 'en' && $feature->name_en ? $feature->name_en : $feature->name }}</span>
    </div>
</div>

{{-- Hero --}}
<div class="vt-hero">
    <div class="container">
        @if($feature->parent)
            <p style="font-size:0.8rem;opacity:0.6;margin-bottom:0.5rem;text-transform:uppercase;letter-spacing:0.08em;">
                {{ app()->getLocale() === 'en' && $feature->parent->name_en ? $feature->parent->name_en : $feature->parent->name }}
            </p>
        @endif
        <h1>{{ app()->getLocale() === 'en' && $feature->name_en ? $feature->name_en : $feature->name }}</h1>
        <p>{{ __('home.virtual_tour.hero_desc') }}</p>
    </div>
</div>


{{-- Room Grid --}}
<section class="vt-rooms-section">
    <div class="container">
        <h2 class="vt-section-title">{{ __('home.virtual_tour.select_room') }}</h2>
        <p class="vt-section-sub">{{ __('home.virtual_tour.select_room_desc') }}</p>

        @if($virtualRooms->isEmpty())
            <div style="text-align:center;padding:4rem;color:#9ca3af;">
                <svg style="width:64px;height:64px;margin:0 auto 1rem;display:block;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                <p>{{ __('home.virtual_tour.no_rooms') }}</p>
            </div>
        @else
            <div class="vt-rooms-grid">
                @foreach($virtualRooms as $room)
                <div class="vt-room-card"
                     data-room-id="{{ $room->id }}"
                     data-room-name="{{ addslashes($room->name) }}"
                     data-room-image="{{ $room->image_360_path ? asset('storage/'.$room->image_360_path) : '' }}"
                     onclick="openTour(this.dataset.roomId, this.dataset.roomName, this.dataset.roomImage)">
                    <div class="vt-room-thumb">
                        @if($room->thumbnail_path)
                            <img src="{{ asset('storage/'.$room->thumbnail_path) }}" alt="{{ $room->name }}" loading="lazy">
                        @else
                            <div class="vt-room-thumb-placeholder">🏛️</div>
                        @endif
                        <div class="vt-enter-btn"><span>{{ __('home.virtual_tour.enter_room') }}</span></div>
                        <div class="vt-room-badge">
                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                            {{ __('home.virtual_tour.hotspot_count', ['count' => $room->hotspots_count]) }}
                        </div>
                    </div>
                    <div class="vt-room-info">
                        <h3 class="vt-room-name">{{ $room->name }}</h3>
                        @if($room->description)
                            <p class="vt-room-desc">{{ $room->description }}</p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

{{-- Pannellum Fullscreen Modal --}}
<div class="vt-modal-overlay" id="vtModal">
    <div class="vt-modal-inner">
        <div class="vt-modal-header">
            <span class="vt-modal-title" id="vtModalTitle">{{ __('home.virtual_tour.room_title') }}</span>
            <button class="vt-modal-close" onclick="closeTour()">&#x2715;</button>
        </div>
        <div id="vt-panorama"></div>
    </div>
</div>

{{-- Login Modal (if guest) --}}
@if(isset($requiresLoginModal) && $requiresLoginModal)
    @include('partials.login_modal', [
        'loginModalPreviews'  => $loginModalPreviews ?? [],
        'loginModalPreview'   => $loginModalPreview ?? null,
        'loginModalRoomNames' => $loginModalRoomNames ?? [],
        'loginModalRoomName'  => $loginModalRoomName ?? null
    ])
@endif

@endsection

@push('scripts')
{{-- Pass server data to JS (only data, no logic) --}}
<script type="application/json" id="vtRoomDataJson">{!! json_encode(
    $virtualRooms->keyBy('id')->map(function($room) {
        return [
            'id'       => (string) $room->id,
            'name'     => $room->name,
            'imageUrl' => $room->image_360_path ? asset('storage/'.$room->image_360_path) : '',
            'hotspots' => $room->hotspots->map(function($h) {
                return [
                    'pitch'          => (float) $h->pitch,
                    'yaw'            => (float) $h->yaw,
                    'text_tooltip'   => $h->text_tooltip,
                    'target_room_id' => (string) $h->target_room_id,
                ];
            })->values(),
        ];
    })
) !!}</script>
<script>
    window.vtRoomData = JSON.parse(document.getElementById('vtRoomDataJson').textContent);
</script>
<script src="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js"></script>
<script src="{{ asset('js/virtual_tour.js') }}"></script>
@endpush
