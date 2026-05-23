@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.css" />
    <link rel="stylesheet" href="{{ asset('css/cms/virtual_rooms.css') }}">
@endpush

@php
    $isEdit = isset($room);
    $actionUrl = $isEdit
        ? route('cms.features.virtual_rooms.update', [$feature, $room])
        : route('cms.features.virtual_rooms.store', $feature);
@endphp

@section('breadcrumb_items')
    <span class="text-gray-400">CMS</span>
    <span class="text-gray-300">/</span>
    <a href="{{ route('cms.features.index') }}"
        class="text-gray-400 hover:text-gray-600 transition-colors">{{ __('cms.features.title') }}</a>
    @if ($feature->parent)
        @php
            $grandparent = $feature->parent->parent;
        @endphp

        @if ($grandparent && $grandparent->id !== $feature->parent->id)
            <span class="text-gray-300">/</span>
            <a href="{{ url('/cms/features/' . $grandparent->id . '/') }}"
                class="text-gray-400 hover:text-gray-600 transition-colors">{{ $grandparent->name }}</a>
        @endif

        <span class="text-gray-300">/</span>
        <a href="{{ url('/cms/features/' . $feature->parent->id . '/') }}"
            class="text-gray-400 hover:text-gray-600 transition-colors">{{ $feature->parent->name }}</a>
    @endif
    <span class="text-gray-300">/</span>
    <a href="{{ route('cms.features.show', $feature) }}"
        class="text-gray-400 hover:text-gray-600 transition-colors">{{ $feature->name }}</a>
@endsection
@section('breadcrumb_active', $isEdit ? __('cms.virtual_rooms.breadcrumb_edit') :
    __('cms.virtual_rooms.breadcrumb_create'))

@section('content')
    <div class="mb-4">
        <a href="{{ route('cms.features.virtual_rooms.index', $feature) }}"
            class="inline-flex items-center gap-2 px-3 py-2 rounded-lg text-white text-sm font-medium transition-colors shadow-sm"
            style="background-color: #818284;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            {{ __('cms.virtual_rooms.back_to_list') }}
        </a>
    </div>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            {{ $isEdit ? __('cms.virtual_rooms.form_title_edit') : __('cms.virtual_rooms.form_title_create') }}</h1>
        <p class="text-sm text-gray-500 mt-1">{{ __('cms.virtual_rooms.form_desc') }}</p>
    </div>

    <form action="{{ $actionUrl }}" method="POST" enctype="multipart/form-data" id="virtual-room-form">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        <div class="flex flex-col lg:flex-row gap-6 items-start">

            <!-- Left Column: Form & Hotspots -->
            <div class="space-y-6 w-full lg:w-[38%] lg:shrink-0">

                <!-- Basic Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <h3 class="text-sm font-semibold text-gray-800 mb-4">{{ __('cms.virtual_rooms.info_title') }}</h3>

                    <div class="space-y-4">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1">{{ __('cms.virtual_rooms.label_name') }}
                                <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $room->name ?? '') }}" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1">{{ __('cms.virtual_rooms.label_desc') }}
                                <span class="text-red-500">*</span></label>
                            <textarea name="description" rows="3" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description', $room->description ?? '') }}</textarea>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1">{{ __('cms.virtual_rooms.label_thumbnail') }}
                                {!! !$isEdit ? '<span class="text-red-500">*</span>' : '' !!}</label>
                            <input type="file" name="thumbnail" accept="image/jpeg,image/png,image/webp"
                                {{ !$isEdit ? 'required' : '' }}
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white file:mr-4 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                            <p class="text-xs text-gray-500 mt-1.5">{{ __('cms.virtual_rooms.thumbnail_help') }}</p>
                            @if ($isEdit && $room->thumbnail_path)
                                <img src="{{ asset('storage/' . $room->thumbnail_path) }}"
                                    class="mt-2 w-full h-24 object-cover rounded-lg border border-gray-200">
                            @endif
                        </div>

                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1">{{ __('cms.virtual_rooms.label_image_360') }}
                                {!! !$isEdit ? '<span class="text-red-500">*</span>' : '' !!}</label>
                            <input type="file" name="image_360" id="image_360_input"
                                accept="image/jpeg,image/png,image/webp" {{ !$isEdit ? 'required' : '' }}
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white file:mr-4 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                            <p class="text-xs text-gray-500 mt-1.5">{{ __('cms.virtual_rooms.image_360_help') }}</p>
                            <input type="hidden" id="existing_panorama_url"
                                value="{{ $isEdit && $room->image_360_path ? asset('storage/' . $room->image_360_path) : '' }}">
                        </div>
                    </div>
                </div>

                <!-- Hotspots Management -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold text-gray-800">{{ __('cms.virtual_rooms.hotspot_title') }}</h3>
                        <button type="button" id="add-hotspot-btn"
                            class="text-sm font-semibold text-white px-3 py-1.5 rounded-md flex items-center gap-1 transition-colors"
                            style="background-color: #1e3a5f;" onmouseover="this.style.backgroundColor='#162d4a'"
                            onmouseout="this.style.backgroundColor='#1e3a5f'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            {{ __('cms.virtual_rooms.hotspot_add') }}
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mb-4">
                        {{ __('cms.virtual_rooms.hotspot_rooms_available', ['count' => $allRooms->count()]) }}</p>

                    <div id="hotspot-container" class="space-y-4">
                        <!-- Hotspots will be rendered here by JS -->
                    </div>

                    <div id="no-hotspot-msg"
                        class="text-center py-4 text-sm text-gray-400 border-2 border-dashed border-gray-100 rounded-lg">
                        {{ __('cms.virtual_rooms.hotspot_empty') }}
                    </div>
                </div>

            </div>

            <!-- Right Column: 360 Preview -->
            <div class="w-full lg:w-[62%]">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 lg:sticky lg:top-6">
                    <h3 class="text-sm font-semibold text-gray-800 mb-1">{{ __('cms.virtual_rooms.preview_title') }}</h3>
                    <p class="text-xs text-gray-500 mb-4">{{ __('cms.virtual_rooms.preview_desc') }}</p>

                    <div class="w-full bg-gray-900 rounded-xl overflow-hidden relative h-[300px] sm:h-[450px] lg:h-[550px]" id="panorama-container">
                        <!-- Pannellum viewer mounts here -->
                        <div id="panorama" class="w-full h-full"></div>

                        <div id="panorama-placeholder"
                            class="absolute inset-0 flex flex-col items-center justify-center text-gray-500 bg-gray-100 z-10 transition-opacity duration-300">
                            <svg class="w-12 h-12 mb-2 text-gray-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="text-sm font-medium">{{ __('cms.virtual_rooms.preview_placeholder') }}</span>
                            <span class="text-xs mt-1">{{ __('cms.virtual_rooms.preview_placeholder_sub') }}</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Sticky Footer Actions -->
        <div class="mt-8 flex justify-end gap-3 pb-8">
            <a href="{{ route('cms.features.virtual_rooms.index', $feature) }}"
                class="px-5 py-2.5 bg-gray-100 border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 active:bg-gray-300 transition-colors shadow-sm">
                {{ __('cms.virtual_rooms.btn_cancel') }}
            </a>
            <button type="submit"
                class="px-5 py-2.5 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm flex items-center gap-2"
                style="background-color:#1d4ed8;" onmouseover="this.style.backgroundColor='#1e40af'"
                onmouseout="this.style.backgroundColor='#1d4ed8'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                </svg>
                {{ __('cms.virtual_rooms.btn_save') }}
            </button>
        </div>
    </form>

    <!-- Data for JS (with translations) -->
    <script type="application/json" id="allRoomsJson">{!! json_encode($allRooms->map(fn($r) => ['id' => $r->id, 'name' => $r->name])) !!}</script>
    <script type="application/json" id="existingHotspotsJson">{!! json_encode($isEdit ? $room->hotspots : []) !!}</script>
    <script type="application/json" id="hotspotLabelsJson">{
        "select_placeholder": "{{ __('cms.virtual_rooms.label_target_room') }}",
        "hotspot_index": "{{ __('cms.virtual_rooms.hotspot_label_index') }}",
        "tooltip_label": "{{ __('cms.virtual_rooms.label_tooltip') }}",
        "target_label": "{{ __('cms.virtual_rooms.label_target_room') }}",
        "delete_confirm": "{{ __('cms.virtual_rooms.label_delete_confirm') }}"
    }</script>
    <script>
        window.allRoomsData = JSON.parse(document.getElementById('allRoomsJson').textContent);
        window.existingHotspots = JSON.parse(document.getElementById('existingHotspotsJson').textContent);
        window.hotspotLabels = JSON.parse(document.getElementById('hotspotLabelsJson').textContent);
    </script>
@endsection

@push('scripts')
    <!-- Pannellum JS -->
    <script src="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js"></script>
    <script src="{{ asset('js/cms/virtual_rooms.js') }}"></script>
@endpush
