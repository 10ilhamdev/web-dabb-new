@extends('layouts.guest')

@section('title', $feature->name . ' — ' . config('app.name'))

@push('styles')
<link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
<link rel="stylesheet" href="{{ asset('css/feature-page.css') }}">
<link rel="stylesheet" href="{{ asset('css/virtual_3d_tour.css') }}">
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


{{-- Hero: blue gradient only for virtual archive pages; photo-hero for all others --}}
@php
    $isVirtualArchive = str_contains(request()->path(), 'pameran/virtual') ||
                        str_contains(request()->path(), 'pameran-arsip-virtual') ||
                        str_contains(request()->path(), 'pameran-virtual');
@endphp

@if($isVirtualArchive)
{{-- Blue gradient hero (Pameran Arsip Virtual) --}}
<div class="vt-hero">
    <div class="container">
        @if($feature->parent)
            <p style="font-size:0.8rem;opacity:0.6;margin-bottom:0.5rem;text-transform:uppercase;letter-spacing:0.08em;">
                {{ app()->getLocale() === 'en' && $feature->parent->name_en ? $feature->parent->name_en : $feature->parent->name }}
            </p>
        @endif
        <h1>{{ app()->getLocale() === 'en' && $feature->name_en ? $feature->name_en : $feature->name }}</h1>
        <p>{{ __('home.virtual_3d_tour.hero_desc') }}</p>
    </div>
</div>
@else
{{-- Photo hero (semua halaman lain) — sama seperti login --}}
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
@endif


{{-- Virtual 3D Rooms Section (if virtual path) --}}
@if(str_contains(request()->path(), 'virtual'))
<section class="vt-rooms-section">
    <div class="container">
        <h2 class="vt-section-title">{{ __('home.virtual_3d_tour.select_room') }}</h2>
        <p class="vt-section-sub">{{ __('home.virtual_3d_tour.select_room_desc') }}</p>

        @if($virtual3dRooms->isEmpty())
            <div style="text-align:center;padding:4rem;color:#9ca3af;">
                <svg style="width:64px;height:64px;margin:0 auto 1rem;display:block;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <p>{{ __('home.virtual_3d_tour.no_rooms') }}</p>
            </div>
        @else
            <div class="vt-rooms-grid">
                @foreach($virtual3dRooms as $room)
                <div class="vt-room-card" data-room-id="{{ $room->id }}" onclick="openRoom3D(+this.dataset.roomId)">
                    <div class="vt-room-thumb">
                        @if($room->thumbnail_path)
                            <img src="{{ asset('storage/'.$room->thumbnail_path) }}" alt="{{ $room->name }}" loading="lazy">
                        @else
                            <div class="vt-room-thumb-placeholder vt3d-placeholder">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:48px;height:48px;opacity:0.4;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            </div>
                        @endif
                        <div class="vt-enter-btn"><span>{{ __('home.virtual_3d_tour.enter_room') }}</span></div>
                        <div class="vt-room-badge">
                            <svg style="width:12px;height:12px;" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                            </svg>
                            3D Virtual
                        </div>
                    </div>
                    <div class="vt-room-info">
                        <h3 class="vt-room-name">{{ $room->name }}</h3>
                        @if($room->description)
                            <p class="vt-room-desc">{{ Str::limit($room->description, 80) }}</p>
                        @endif
                        <div class="vt-room-meta">
                            <span>{{ __('home.virtual_3d_tour.media_on_wall', ['count' => count($room->media) ?? 0]) }}</span>
                            @if($room->door_link_type !== 'none')
                                <span>• {{ __('home.virtual_3d_tour.door_label', ['label' => $room->door_label ?: __('home.virtual_3d_tour.door_active')]) }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
@endif

{{-- CMS Content Sections --}}
<div class="feature-content" style="padding-bottom: 5rem;">
    <div class="container">
        @if(isset($pages) && $pages->count() > 0)
            @if($currentPage->description)
            <div class="feature-welcome-box">
                <h3>{{ __('cms.feature_pages.welcome', ['name' => (app()->getLocale() === 'en' && $feature->name_en ? $feature->name_en : $feature->name)]) }}</h3>
                <p>{{ app()->getLocale() === 'en' && $currentPage->description_en ? $currentPage->description_en : $currentPage->description }}</p>
            </div>
            @endif

            <div class="feature-list-header">
                <h2 class="feature-list-title">{{ __('cms.feature_pages.list_title', ['name' => (app()->getLocale() === 'en' && $feature->name_en ? $feature->name_en : $feature->name)]) }}</h2>
                <div class="feature-search">
                    <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" placeholder="{{ __('cms.feature_pages.search_placeholder') }}" id="sectionSearch" onkeyup="filterSections()">
                    <button type="button" class="search-btn" onclick="filterSections()">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="feature-sections" id="featureSections">
                @foreach($currentPage->sections as $section)
                <div class="feature-section" data-title="{{ mb_strtolower($section->title) }}">
                    <div class="section-title-box">
                        <h3>{{ app()->getLocale() === 'en' && $section->title_en ? $section->title_en : $section->title }}</h3>
                    </div>

                    @if($section->images && count($section->images))
                    <div class="section-images section-images-{{ min(count($section->images), 4) }}cols">
                        @foreach($section->images as $imgIndex => $img)
                        <div class="section-img-wrap" data-img="{{ asset('storage/' . $img) }}" onclick="openImageModal(this.dataset.img)">
                            <img src="{{ asset('storage/' . $img) }}" alt="{{ $section->title }}" loading="lazy"
                                data-position="{{ isset($section->image_positions[$imgIndex]) ? $section->image_positions[$imgIndex] : 'center' }}">
                            <div class="hover-overlay">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="zoom-icon">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                                </svg>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    @if($section->description)
                    <div class="section-description">
                        <p>{!! nl2br(e(app()->getLocale() === 'en' && $section->description_en ? $section->description_en : $section->description)) !!}</p>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>

            @if($totalPages > 1)
            <div class="feature-pagination">
                @if($currentPageNum > 1)
                    <a href="{{ route('feature.page', [$feature, $currentPageNum - 1]) }}" class="page-btn page-prev">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                @endif
                @for($i = 1; $i <= $totalPages; $i++)
                    <a href="{{ route('feature.page', [$feature, $i]) }}" class="page-num {{ $i === $currentPageNum ? 'active' : '' }}">{{ $i }}</a>
                @endfor
                @if($currentPageNum < $totalPages)
                    <a href="{{ route('feature.page', [$feature, $currentPageNum + 1]) }}" class="page-btn page-next">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                @endif
            </div>
            @endif
        @else
            @if($feature->content)
            <div class="feature-simple-content">
                <div class="prose max-w-none">
                    {!! app()->getLocale() === 'en' && $feature->content_en ? $feature->content_en : $feature->content !!}
                </div>
            </div>
            @endif
        @endif
    </div>
</div>

{{-- 3D Viewer Overlay (unchanged) --}}
<div id="room3d-viewer" style="display:none; position:fixed; inset:0; z-index:9999; background:#111;">
    <div id="vt3d-topbar">
        <div id="vt3d-room-title">{{ __('home.virtual_3d_tour.viewer_title') }}</div>
        <div style="display:flex;align-items:center;gap:12px;">
            <div id="vt3d-view-btns" style="display:flex;gap:6px;">
                @foreach(['front'=>__('home.virtual_3d_tour.view_front'),'left'=>__('home.virtual_3d_tour.view_left'),'right'=>__('home.virtual_3d_tour.view_right'),'back'=>__('home.virtual_3d_tour.view_back')] as $v=>$l)
                <button class="vt3d-view-btn" data-view="{{ $v }}">{{ $l }}</button>
                @endforeach
            </div>
            <button id="vt3d-close-btn" onclick="closeRoom3D()" title="{{ __('home.virtual_3d_tour.close') }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:20px;height:20px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>
    <div id="vt3d-scene-wrapper">
        <div id="vt3d-scene">
            <div id="vt3d-floor"   class="vt3d-surface"></div>
            <div id="vt3d-ceiling" class="vt3d-surface"></div>
            <div id="vt3d-wall-front" data-wall="front" class="vt3d-wall">
                <div class="vt3d-media-layer" data-wall="front"></div>
                <div class="vt3d-door-slot" data-wall="front" style="display:none; cursor:pointer;" onclick="handleDoorClick(event, this)">
                    <img class="vt3d-door-portal" src="" style="display:none;" />
                    <div class="vt3d-door-frame"><div class="vt3d-door-panel"><div class="vt3d-door-knob"></div></div></div>
                    <div class="vt3d-door-label"></div>
                </div>
            </div>
            <div id="vt3d-wall-back" data-wall="back" class="vt3d-wall">
                <div class="vt3d-media-layer" data-wall="back"></div>
                <div class="vt3d-door-slot" data-wall="back" style="display:none; cursor:pointer;" onclick="handleDoorClick(event, this)">
                    <img class="vt3d-door-portal" src="" style="display:none;" />
                    <div class="vt3d-door-frame"><div class="vt3d-door-panel"><div class="vt3d-door-knob"></div></div></div>
                    <div class="vt3d-door-label"></div>
                </div>
            </div>
            <div id="vt3d-wall-left" data-wall="left" class="vt3d-wall">
                <div class="vt3d-media-layer" data-wall="left"></div>
                <div class="vt3d-door-slot" data-wall="left" style="display:none; cursor:pointer;" onclick="handleDoorClick(event, this)">
                    <img class="vt3d-door-portal" src="" style="display:none;" />
                    <div class="vt3d-door-frame"><div class="vt3d-door-panel"><div class="vt3d-door-knob"></div></div></div>
                    <div class="vt3d-door-label"></div>
                </div>
            </div>
            <div id="vt3d-wall-right" data-wall="right" class="vt3d-wall">
                <div class="vt3d-media-layer" data-wall="right"></div>
                <div class="vt3d-door-slot" data-wall="right" style="display:none; cursor:pointer;" onclick="handleDoorClick(event, this)">
                    <img class="vt3d-door-portal" src="" style="display:none;" />
                    <div class="vt3d-door-frame"><div class="vt3d-door-panel"><div class="vt3d-door-knob"></div></div></div>
                    <div class="vt3d-door-label"></div>
                </div>
            </div>
        </div>
    </div>
    <div id="vt3d-hint">{{ __('home.virtual_3d_tour.hint') }}</div>
</div>

<div id="imageModal" class="feature-image-modal" onclick="closeImageModal()">
    <span class="close-modal">&times;</span>
    <img class="modal-content" id="modalImg">
</div>

<script type="application/json" id="virtualRooms3DData">{!! json_encode($virtual3dRooms->map(function($r) {
        return [
            'id'             => $r->id,
            'name'           => $r->name,
            'wall_color'     => $r->wall_color,
            'floor_color'    => $r->floor_color,
            'ceiling_color'  => $r->ceiling_color,
            'doors'          => $r->doors,
            'thumbnail_url'  => $r->thumbnail_path ? asset('storage/' . $r->thumbnail_path) : null,
            'media'          => $r->media->map(function($m) {
                return [
                    'id'         => $m->id,
                    'wall'       => $m->wall,
                    'type'       => $m->type,
                    'file_path'  => asset('storage/'.$m->file_path),
                    'position_x' => (float) $m->position_x,
                    'position_y' => (float) $m->position_y,
                    'width'      => (float) $m->width,
                    'height'     => (float) $m->height
                ];
            })->values()
        ];
    })->values()) !!}</script>
<script>
    window.virtualRooms3D = JSON.parse(document.getElementById('virtualRooms3DData').textContent);
    document.querySelectorAll('[data-position]').forEach(function(el) { el.style.objectPosition = el.dataset.position; });
</script>

{{-- Login Modal (if guest) --}}
@if(isset($requiresLoginModal) && $requiresLoginModal)
    @include('partials.login_modal', [
        'previewImage'    => $loginModalPreview ?? null,
        'roomName'       => $loginModalRoomName ?? null,
        'loginModalPreviews'  => $loginModalPreviews ?? [],
        'loginModalPreview'   => $loginModalPreview ?? null,
        'loginModalRoomNames' => $loginModalRoomNames ?? [],
        'loginModalRoomName'  => $loginModalRoomName ?? null
    ])
@endif

@endsection

@push('scripts')
<script src="{{ asset('js/pages/feature.js') }}"></script>
<script src="{{ asset('js/virtual_3d_tour.js') }}"></script>
@endpush

