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

        @if(!$virtualRooms->isEmpty())
        <div class="vt-search-box" style="max-width: 500px; margin: 0 auto 2.5rem; position: relative;">
            <svg style="position: absolute; left: 1.25rem; top: 50%; transform: translateY(-50%); width: 1.25rem; height: 1.25rem; color: #6b7280; pointer-events: none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input type="text" id="tourSearchInput" placeholder="{{ app()->getLocale() === 'en' ? 'Search room title or description...' : 'Cari judul atau deskripsi ruangan...' }}" onkeyup="filterTourCards()" style="width: 100%; padding: 0.85rem 1.25rem 0.85rem 3.25rem; border-radius: 2rem; border: 1px solid #e5e7eb; box-shadow: 0 4px 10px -1px rgba(0, 0, 0, 0.08); font-size: 1rem; outline: none; transition: all 0.2s; background: white;">
        </div>
        @endif

        @if($virtualRooms->isEmpty())
            <div style="text-align:center;padding:4rem;color:#9ca3af;">
                <svg style="width:64px;height:64px;margin:0 auto 1rem;display:block;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                <p>{{ __('home.virtual_tour.no_rooms') }}</p>
            </div>
        @else
            <div class="vt-rooms-grid" id="tourCardsWrapper">
                @foreach($virtualRooms as $room)
                <div class="vt-room-card"
                     data-search="{{ mb_strtolower($room->translated_name . ' ' . $room->translated_description) }}"
                     data-room-id="{{ $room->id }}"
                     data-room-name="{{ addslashes($room->translated_name) }}"
                     data-room-image="{{ $room->image_360_path ? asset('storage/'.$room->image_360_path) : '' }}"
                     onclick="openTour(this.dataset.roomId, this.dataset.roomName, this.dataset.roomImage)">
                    <div class="vt-room-thumb">
                        @if($room->thumbnail_path)
                            <img src="{{ asset('storage/'.$room->thumbnail_path) }}" alt="{{ $room->translated_name }}" loading="lazy">
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
                        <h3 class="vt-room-name">{{ $room->translated_name }}</h3>
                        @if($room->description)
                            <p class="vt-room-desc">{{ $room->translated_description }}</p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            <div id="noTourResultsMsg" style="display:none; text-align:center; padding:3rem; color:#6b7280;">
                <p style="font-size:1.1rem;">{{ app()->getLocale() === 'en' ? 'No rooms match your search.' : 'Tidak ada ruangan yang sesuai dengan pencarian Anda.' }}</p>
            </div>
        @endif
    </div>
</section>

{{-- Pannellum Fullscreen Modal --}}
<div class="vt-modal-overlay" id="vtModal">
    <div class="vt-modal-inner">
        <div class="vt-modal-header">
            <span class="vt-modal-title" id="vtModalTitle">{{ __('home.virtual_tour.room_title') }}</span>
            <div class="vt-modal-actions">
                <button class="vt-modal-eye" id="vtListBtn" title="Daftar Ruangan">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </button>
                <button class="vt-modal-close" onclick="closeTour()">&#x2715;</button>
            </div>
        </div>
        <div id="vt-panorama"></div>

        {{-- Panorama List Popup --}}
        <div class="vt-list-popup" id="vtListPopup">
            <div class="vt-list-content">
                <div class="vt-list-header">
                    <div class="vt-list-title-wrap">
                        <div class="vt-list-decoration"></div>
                        <h2 class="vt-list-title">Panorama list:</h2>
                    </div>
                    <button class="vt-list-close" id="vtListClose">
                        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="vt-list-grid" id="vtListGrid">
                    {{-- Rooms will be injected by JS --}}
                </div>
            </div>
        </div>
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
            'name'     => $room->translated_name,
            'imageUrl'     => $room->image_360_path ? asset('storage/'.$room->image_360_path) : '',
            'thumbnailUrl' => $room->thumbnail_path ? asset('storage/'.$room->thumbnail_path) : '',
            'hotspots'     => $room->hotspots->map(function($h) {
                return [
                    'type'           => $h->type,
                    'pitch'          => (float) $h->pitch,
                    'yaw'            => (float) $h->yaw,
                    'text_tooltip'   => $h->translated_text_tooltip,
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
<script>
function filterTourCards() {
    var input = document.getElementById('tourSearchInput');
    if (!input) return;
    var filter = input.value.toLowerCase().trim();
    var cards = document.querySelectorAll('#tourCardsWrapper .vt-room-card');
    var visibleCount = 0;

    cards.forEach(function(card) {
        var searchStr = card.getAttribute('data-search') || '';
        if (searchStr.indexOf(filter) > -1) {
            card.style.display = '';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });

    var noMsg = document.getElementById('noTourResultsMsg');
    if (noMsg) {
        noMsg.style.display = (visibleCount === 0 && cards.length > 0) ? 'block' : 'none';
    }
}
</script>
@endpush
