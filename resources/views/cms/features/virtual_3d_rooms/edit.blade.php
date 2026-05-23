@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/cms/virtual_3d_rooms.css') }}">
    <link rel="stylesheet" href="{{ asset('css/cms/virtual_3d_rooms_form.css') }}">
    <style>
        /* Force RTE toolbar into a single scrollable row for captions */
        .rte-caption-container div[class*='rte-commandbar'] {
            white-space: nowrap !important;
            overflow-x: auto !important;
            display: none !important; /* Hide by default */
            flex-wrap: nowrap !important;
            padding: 0 !important;
            background: #f8fafc !important;
            border-bottom: 1px solid #e2e8f0 !important;
            height: 34px !important;
            min-height: 34px !important;
        }
        .rte-caption-container:focus-within div[class*='rte-commandbar'] {
            display: flex !important;
        }
        .rte-caption-container div[class*='rte-commandbar'] > div {
            display: flex !important;
            flex-wrap: nowrap !important;
            align-items: center !important;
            height: 100% !important;
        }
        .rte-caption-container div[class*='rte-toolbar-item'] {
            transform: scale(0.7) !important;
            margin: -4px !important;
            width: 24px !important;
            height: 24px !important;
        }
        .rte-caption-container .rte-modern-editor {
            height: 80px !important;
            min-height: 80px !important;
        }
        .caption-widget-mode select {
            width: 100%;
            padding: 4px 8px;
            font-size: 0.75rem;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            margin-bottom: 8px;
            background-color: #f8fafc;
        }
        .caption-qa-pair {
            position: relative;
            padding: 10px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        .caption-qa-remove {
            position: absolute;
            top: 5px;
            right: 5px;
            width: 20px;
            height: 20px;
            background: #fee2e2;
            color: #ef4444;
            border: none;
            border-radius: 50%;
            font-size: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .caption-qa-pair input {
            width: 100%;
            padding: 6px 10px;
            font-size: 0.8rem;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            margin-bottom: 5px;
        }
        .caption-qa-add {
            width: 100%;
            padding: 6px;
            font-size: 0.75rem;
            color: #2563eb;
            background: #eff6ff;
            border: 1px dashed #bfdbfe;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
        }
        .rte-compact-container {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            overflow: hidden;
        }
        @media (max-width: 480px) {
            #propertiesPanel .grid-cols-4 {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            }
        }
    </style>
@endpush

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
    <a href="{{ route('cms.features.virtual_3d_rooms.index', $feature) }}"
        class="text-gray-400 hover:text-gray-600 transition-colors">{{ __('cms.virtual_3d_rooms.breadcrumb_parent') }}</a>
@endsection
@section('breadcrumb_active', __('cms.virtual_3d_rooms.breadcrumb_edit', ['name' => $room->name]))

@section('content')

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            {{ __('cms.virtual_3d_rooms.form_title_edit', ['name' => $room->name]) }}</h1>
        <p class="text-sm text-gray-500 mt-1">{{ __('cms.virtual_3d_rooms.form_desc_edit') }}</p>
    </div>

    <form action="{{ route('cms.features.virtual_3d_rooms.update', [$feature, $room]) }}" method="POST"
        enctype="multipart/form-data" id="virtual3d-room-form">
        @csrf
        @method('PUT')
        <input type="hidden" name="auto_thumbnail" id="autoThumbnailInput">
        <input type="hidden" name="remove_thumbnail" id="removeThumbnailInput" value="0">

        <div class="flex flex-col lg:flex-row gap-6 items-start">

            <!-- Left Column: Form, Colors, Media, Hotspot -->
            <div class="space-y-6 w-full lg:w-[38%] lg:shrink-0">

                <!-- Basic Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <h3 class="text-sm font-semibold text-gray-800 mb-4">{{ __('cms.virtual_3d_rooms.info_title') }}</h3>
                    <div class="space-y-4">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1">{{ __('cms.virtual_3d_rooms.label_name') }}
                                <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $room->name) }}" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1">{{ __('cms.virtual_3d_rooms.label_desc') }}</label>
                            <textarea name="description" rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description', $room->description) }}</textarea>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1">{{ __('cms.virtual_3d_rooms.label_thumbnail') }}</label>
                            @if ($room->thumbnail_path)
                                <div class="relative mt-1 mb-2" id="thumbnailPreviewWrap">
                                    <img src="{{ asset('storage/' . $room->thumbnail_path) }}" id="thumbnailPreviewImg"
                                        class="w-full h-24 object-cover rounded-lg border border-gray-200">
                                    <button type="button" id="removeThumbnailBtn" onclick="removeThumbnail()"
                                        title="{{ __('cms.virtual_3d_rooms.media_delete') }}"
                                        class="absolute top-1.5 right-1.5 w-6 h-6 flex items-center justify-center rounded-full text-white text-xs font-bold shadow-md transition-transform hover:scale-110"
                                        style="background-color:#ef4444;">
                                        &times;
                                    </button>
                                </div>
                            @endif
                            <input type="file" name="thumbnail" id="thumbnailFileInput"
                                accept="image/jpeg,image/png,image/webp"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white file:mr-4 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                            <p class="text-xs text-gray-500 mt-1.5">{{ __('cms.virtual_3d_rooms.thumbnail_keep') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Room Colors -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <h3 class="text-sm font-semibold text-gray-800 mb-4">{{ __('cms.virtual_3d_rooms.colors_title') }}</h3>
                    <div class="space-y-4">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1">{{ __('cms.virtual_3d_rooms.label_wall_color') }}</label>
                            <div class="flex items-center gap-3">
                                <input type="color" name="wall_color" id="wallColorInput"
                                    value="{{ old('wall_color', $room->wall_color) }}"
                                    class="w-10 h-10 p-1 border border-gray-300 rounded-lg cursor-pointer"
                                    onchange="updatePreviewColors()">
                                <input type="text" id="wallColorText" value="{{ old('wall_color', $room->wall_color) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" readonly>
                            </div>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1">{{ __('cms.virtual_3d_rooms.label_floor_color') }}</label>
                            <div class="flex items-center gap-3">
                                <input type="color" name="floor_color" id="floorColorInput"
                                    value="{{ old('floor_color', $room->floor_color) }}"
                                    class="w-10 h-10 p-1 border border-gray-300 rounded-lg cursor-pointer"
                                    onchange="updatePreviewColors()">
                                <input type="text" id="floorColorText"
                                    value="{{ old('floor_color', $room->floor_color) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" readonly>
                            </div>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1">{{ __('cms.virtual_3d_rooms.label_ceiling_color') }}</label>
                            <div class="flex items-center gap-3">
                                <input type="color" name="ceiling_color" id="ceilingColorInput"
                                    value="{{ old('ceiling_color', $room->ceiling_color) }}"
                                    class="w-10 h-10 p-1 border border-gray-300 rounded-lg cursor-pointer"
                                    onchange="updatePreviewColors()">
                                <input type="text" id="ceilingColorText"
                                    value="{{ old('ceiling_color', $room->ceiling_color) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Door / Hotspot -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5" x-data="{
                    currentWall: 'front',
                    doors: {{ json_encode(
                        $room->doors ?? [
                            'front' => ['link_type' => 'none', 'target' => null, 'label' => null],
                            'back' => ['link_type' => 'none', 'target' => null, 'label' => null],
                            'left' => ['link_type' => 'none', 'target' => null, 'label' => null],
                            'right' => ['link_type' => 'none', 'target' => null, 'label' => null],
                        ],
                    ) }},
                    syncDoors() {
                        // Keep global JS state in sync for the wall editor preview
                        window.doorsData = JSON.parse(JSON.stringify(this.doors));
                        if (window.updateWallEditorDoors) window.updateWallEditorDoors();
                    },
                    init() {
                        // Listen for wall change events from the JS wall editor
                        window.addEventListener('wall-changed', (e) => {
                            this.currentWall = e.detail.wall;
                        });
                        this.syncDoors();
                    }
                }">
                    <h3 class="text-sm font-semibold text-gray-800 mb-2">{{ __('cms.virtual_3d_rooms.door_title') }}</h3>
                    <p class="text-xs text-gray-500 mb-4">{{ __('cms.virtual_3d_rooms.editor_door_settings_for') }} <span class="font-bold text-blue-600"
                            x-text="currentWall === 'front' ? '{{ __('cms.virtual_3d_rooms.editor_wall_title_front') }}' : (currentWall === 'back' ? '{{ __('cms.virtual_3d_rooms.editor_wall_title_back') }}' : (currentWall === 'left' ? '{{ __('cms.virtual_3d_rooms.editor_wall_title_left') }}' : '{{ __('cms.virtual_3d_rooms.editor_wall_title_right') }}'))"></span>
                    </p>

                    <div class="space-y-4">
                        {{-- Loop through walls to create hidden inputs for ALL walls --}}
                        <template x-for="(wall) in ['front', 'back', 'left', 'right']">
                            <div>
                                <input type="hidden" :name="'doors[' + wall + '][link_type]'"
                                    :value="doors[wall].link_type">
                                <input type="hidden" :name="'doors[' + wall + '][target]'" :value="doors[wall].target">
                                <input type="hidden" :name="'doors[' + wall + '][label]'" :value="doors[wall].label">
                            </div>
                        </template>

                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1">{{ __('cms.virtual_3d_rooms.label_door_type') }}</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                                x-model="doors[currentWall].link_type" @change="syncDoors()">
                                <option value="none">{{ __('cms.virtual_3d_rooms.door_type_none') }}</option>
                                <option value="room">{{ __('cms.virtual_3d_rooms.door_type_room') }}</option>
                                <option value="url">{{ __('cms.virtual_3d_rooms.door_type_url') }}</option>
                            </select>
                        </div>

                        <div x-show="doors[currentWall].link_type === 'room'">
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1">{{ __('cms.virtual_3d_rooms.label_target_room') }}</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                                x-model="doors[currentWall].target">
                                <option value="">{{ __('cms.virtual_3d_rooms.target_room_placeholder') }}</option>
                                @foreach ($allRooms as $r)
                                    <option value="{{ $r->id }}">{{ $r->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div x-show="doors[currentWall].link_type === 'url'">
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1">{{ __('cms.virtual_3d_rooms.label_target_url') }}</label>
                            <input type="text" x-model="doors[currentWall].target"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                                placeholder="https://...">
                        </div>

                        <div x-show="doors[currentWall].link_type !== 'none'">
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1">{{ __('cms.virtual_3d_rooms.label_door_label') }}</label>
                            <input type="text" x-model="doors[currentWall].label" @input="syncDoors()"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                                placeholder="{{ __('cms.virtual_3d_rooms.door_label_placeholder') }}">
                        </div>
                    </div>
                </div>

                <!-- {{ __('cms.virtual_3d_rooms.media_title') }} -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold text-gray-800">{{ __('cms.virtual_3d_rooms.media_title') }}</h3>
                        <span id="mediaCountBadge"
                            class="text-xs font-medium text-gray-500 bg-gray-100 px-2 py-1 rounded">0 item</span>
                    </div>

                    <div id="uploadMediaSection">
                        <div class="space-y-3 mb-4">
                            {{-- Wall indicator synced from wall editor tabs --}}
                            <input type="hidden" id="uploadWall" value="front">
                            <div>
                                <label
                                    class="block text-xs font-medium text-gray-600 mb-1">{{ __('cms.virtual_3d_rooms.media_selected_wall') }}</label>
                                <div id="uploadWallBadge"
                                    class="inline-flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-semibold text-white"
                                    style="background-color: #1e40af;">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 6h16M4 12h16M4 18h16" />
                                    </svg>
                                    <span id="uploadWallLabel">{{ __('cms.virtual_3d_rooms.media_wall_front') }}</span>
                                </div>
                                <p class="text-xs text-gray-400 mt-1">{!! __('cms.virtual_3d_rooms.media_wall_hint') !!}</p>
                            </div>

                            <div>
                                <label
                                    class="block text-xs font-medium text-gray-600 mb-1">{{ __('cms.virtual_3d_rooms.media_type_label') }}</label>
                                <select id="uploadType"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                    <option value="image">{{ __('cms.virtual_3d_rooms.media_type_image') }}</option>
                                    <option value="video">{{ __('cms.virtual_3d_rooms.media_type_video') }}</option>
                                </select>
                            </div>

                            <div>
                                <label
                                    class="block text-xs font-medium text-gray-600 mb-1">{{ __('cms.virtual_3d_rooms.media_file_label') }}</label>
                                <input id="uploadFile" type="file"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white file:mr-4 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer"
                                    accept="image/*,video/mp4,video/webm">
                            </div>

                        </div>

                        <button type="button" onclick="uploadNewMedia()"
                            class="w-full text-sm font-semibold text-white px-4 py-2.5 rounded-lg flex items-center justify-center gap-2 transition-colors"
                            style="background-color: #1e3a5f;" onmouseover="this.style.backgroundColor='#162d4a'"
                            onmouseout="this.style.backgroundColor='#1e3a5f'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            {!! __('cms.virtual_3d_rooms.media_upload_btn') !!}
                        </button>
                    </div>

                    <!-- List of uploaded media (filtered per wall) -->
                    <div id="mediaList" class="mt-4 space-y-2">
                        @forelse($room->media as $media)
                            <div class="flex items-center gap-3 p-2 bg-gray-50 rounded-lg border border-gray-100 media-list-item"
                                data-id="{{ $media->id }}" data-wall="{{ $media->wall }}">
                                <div class="w-12 h-10 flex-shrink-0 rounded overflow-hidden bg-gray-200">
                                    @if ($media->type === 'image')
                                        <img src="{{ asset('storage/' . $media->file_path) }}"
                                            class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-medium text-gray-800 truncate">{{ ucfirst($media->type) }}
                                        #{{ $media->id }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ __('cms.virtual_3d_rooms.media_wall_label', ['wall' => ucfirst($media->wall)]) }}
                                    </p>
                                </div>
                                <button type="button" data-media-id="{{ $media->id }}"
                                    onclick="deleteMediaItem(+this.dataset.mediaId, this)"
                                    class="p-1.5 text-red-500 hover:bg-red-50 rounded transition-colors"
                                    title="{{ __('cms.virtual_3d_rooms.media_delete') }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        @empty
                            <div id="noMediaMsg"
                                class="text-center py-4 text-sm text-gray-400 border-2 border-dashed border-gray-100 rounded-lg">
                                {{ __('cms.virtual_3d_rooms.media_empty') }}
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>

            <!-- Right Column: 3D Preview + Wall Editor -->
            <div class="w-full space-y-6 lg:w-[62%]">

                <!-- 3D Preview -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <h3 class="text-sm font-semibold text-gray-800 mb-1">{{ __('cms.virtual_3d_rooms.preview_title') }}
                    </h3>
                    <p class="text-xs text-gray-500 mb-4">{{ __('cms.virtual_3d_rooms.preview_desc_edit') }}</p>

                    <div class="room3d-preview-wrap" id="preview3dContainer">
                        <div class="room3d-scene" id="preview3dScene">
                            <div class="room3d-face front" id="pv-wall-front">
                                {{ __('cms.virtual_3d_rooms.preview_front') }}</div>
                            <div class="room3d-face back" id="pv-wall-back">
                                <span>{{ __('cms.virtual_3d_rooms.preview_back') }}</span>
                                <div class="room3d-door" id="pv-door">
                                    <span>{{ __('cms.virtual_3d_rooms.preview_door') }}</span>
                                    <div class="room3d-door-knob"></div>
                                </div>
                            </div>
                            <div class="room3d-face left" id="pv-wall-left">{{ __('cms.virtual_3d_rooms.preview_left') }}
                            </div>
                            <div class="room3d-face right" id="pv-wall-right">
                                {{ __('cms.virtual_3d_rooms.preview_right') }}</div>
                            <div class="room3d-face floor" id="pv-floor">{{ __('cms.virtual_3d_rooms.preview_floor') }}
                            </div>
                            <div class="room3d-face ceiling" id="pv-ceiling"><span
                                    style="display:inline-block; transform:scaleY(-1);">{{ __('cms.virtual_3d_rooms.preview_ceiling') }}</span>
                            </div>
                        </div>

                        <div class="preview-rot-container">
                            <button type="button" class="preview-rot-btn active"
                                onclick="rotatePreview('default', this)">{{ __('cms.virtual_3d_rooms.preview_btn_default') }}</button>
                            <button type="button" class="preview-rot-btn"
                                onclick="rotatePreview('front', this)">{{ __('cms.virtual_3d_rooms.preview_btn_front') }}</button>
                            <button type="button" class="preview-rot-btn"
                                onclick="rotatePreview('left', this)">{{ __('cms.virtual_3d_rooms.preview_btn_left') }}</button>
                            <button type="button" class="preview-rot-btn"
                                onclick="rotatePreview('right', this)">{{ __('cms.virtual_3d_rooms.preview_btn_right') }}</button>
                            <button type="button" class="preview-rot-btn"
                                onclick="rotatePreview('back', this)">{{ __('cms.virtual_3d_rooms.preview_btn_back') }}</button>
                            <button type="button" class="preview-rot-btn"
                                onclick="rotatePreview('top', this)">{{ __('cms.virtual_3d_rooms.preview_btn_top') }}</button>
                        </div>
                    </div>
                </div>

                <!-- Wall Editor (Drag & Reposition) -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-800">{{ __('cms.virtual_3d_rooms.editor_title') }}
                            </h3>
                            <p class="text-xs text-gray-500 mt-0.5">{{ __('cms.virtual_3d_rooms.editor_desc') }}</p>
                        </div>
                    </div>
                    <div class="mb-4 flex flex-wrap gap-2">
                        <button type="button" onclick="switchWallView('front')" data-wall="front"
                            class="wall-tab-btn active">{{ __('cms.virtual_3d_rooms.editor_wall_front') }}</button>
                        <button type="button" onclick="switchWallView('left')" data-wall="left"
                            class="wall-tab-btn">{{ __('cms.virtual_3d_rooms.editor_wall_left') }}</button>
                        <button type="button" onclick="switchWallView('right')" data-wall="right"
                            class="wall-tab-btn">{{ __('cms.virtual_3d_rooms.editor_wall_right') }}</button>
                        <button type="button" onclick="switchWallView('back')" data-wall="back"
                            class="wall-tab-btn">{{ __('cms.virtual_3d_rooms.editor_wall_back') }}</button>
                    </div>

                    <div id="wallEditor" class="wall-panel" data-wall-color="{{ $room->wall_color }}">
                        <div class="wall-panel-title" id="wallTitleLabel">
                            {{ __('cms.virtual_3d_rooms.editor_wall_title_front') }}</div>
                        <div id="doorRender" class="door-rendered" style="display: none;"
                            data-active="{{ $room->door_link_type !== 'none' ? '1' : '0' }}"
                            data-door-wall="{{ $room->door_wall ?? 'back' }}">
                            <div class="text-center">{{ __('cms.virtual_3d_rooms.preview_door') }}<br><span
                                    class="text-xs opacity-70">{{ $room->door_label ?: __('cms.virtual_3d_rooms.door_label_placeholder') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Properties Panel -->
                    <div class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200" id="propertiesPanel"
                        style="display: none;">
                        <div class="flex justify-between items-center mb-3">
                            <p class="text-xs font-semibold text-gray-700">
                                {{ __('cms.virtual_3d_rooms.editor_props_title') }}</p>
                            <button type="button" onclick="deleteActiveMedia()"
                                class="text-xs text-red-600 font-semibold hover:text-red-700">{{ __('cms.virtual_3d_rooms.editor_props_delete') }}</button>
                        </div>
                        <div class="grid grid-cols-4 gap-3">
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">X (%)</label>
                                <input type="number" id="propX" step="any"
                                    class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs"
                                    onchange="updatePropertiesFromInput()">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Y (%)</label>
                                <input type="number" id="propY" step="any"
                                    class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs"
                                    onchange="updatePropertiesFromInput()">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">W (%)</label>
                                <input type="number" id="propW" step="any"
                                    class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs"
                                    onchange="updatePropertiesFromInput()">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">H (%)</label>
                                <input type="number" id="propH" step="any"
                                    class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs"
                                    onchange="updatePropertiesFromInput()">
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="block text-xs text-gray-500 mb-1">{{ __('cms.virtual_3d_rooms.media_desc_label') }}</label>
                            <div id="propCaptionWidget" class="caption-widget-container"></div>
                            <input type="hidden" id="propDescription">
                        </div>
                        <button type="button" onclick="saveV3DActiveMedia()"
                            class="mt-3 w-full text-sm font-semibold text-white px-3 py-2 rounded-lg transition-colors"
                            style="background-color:#1d4ed8;">{{ __('cms.virtual_3d_rooms.editor_props_save') }}</button>
                    </div>
                </div>
            </div>

        </div>

        <!-- Sticky Footer Actions -->
        <div class="mt-8 flex justify-end gap-3 pb-8">
            <a href="{{ route('cms.features.virtual_3d_rooms.index', $feature) }}"
                class="px-5 py-2.5 bg-gray-100 border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 active:bg-gray-300 transition-colors shadow-sm">
                {{ __('cms.virtual_3d_rooms.btn_cancel') }}
            </a>
            <button type="submit" id="saveRoomBtn"
                class="px-5 py-2.5 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm flex items-center gap-2"
                style="background-color:#1d4ed8;" onmouseover="this.style.backgroundColor='#1e40af'"
                onmouseout="this.style.backgroundColor='#1d4ed8'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                </svg>
                {{ __('cms.virtual_3d_rooms.btn_save_edit') }}
            </button>
        </div>
    </form>

    {{-- Room media data for JS wall editor --}}
    <script type="application/json" id="roomMediaData">@json(['media' => $room->media, 'doors' => $room->doors])</script>
@endsection

@push('scripts')
    {{-- Blade data passed as JSON (not linted as JS by IDE) --}}
    <script type="application/json" id="v3dConfig">{!! json_encode([
        'csrf' => csrf_token(),
        'routes' => [
            'upload' => route('cms.features.virtual_3d_rooms.media.store', [$feature, $room]),
            'updateMedia' => route('cms.features.virtual_3d_rooms.media.update', [$feature, $room, '__MEDIA_ID__']),
            'deleteMedia' => route('cms.features.virtual_3d_rooms.media.destroy', [$feature, $room, '__MEDIA_ID__']),
        ],
        'translations' => [
            'uploadChoose' => __('cms.virtual_3d_rooms.media_upload_choose'),
            'uploadSuccess' => __('cms.virtual_3d_rooms.media_upload_success'),
        ],
        'wallColor' => $room->wall_color,
        'labels' => [
            'wall' => [
                'front' => ['big' => __('cms.virtual_3d_rooms.editor_wall_title_front'), 'small' => __('cms.virtual_3d_rooms.editor_wall_front'), 'preview' => __('cms.virtual_3d_rooms.editor_wall_front')],
                'left'  => ['big' => __('cms.virtual_3d_rooms.editor_wall_title_left'),  'small' => __('cms.virtual_3d_rooms.editor_wall_left'),  'preview' => __('cms.virtual_3d_rooms.editor_wall_left')],
                'right' => ['big' => __('cms.virtual_3d_rooms.editor_wall_title_right'), 'small' => __('cms.virtual_3d_rooms.editor_wall_right'), 'preview' => __('cms.virtual_3d_rooms.editor_wall_right')],
                'back'  => ['big' => __('cms.virtual_3d_rooms.editor_wall_title_back'),  'small' => __('cms.virtual_3d_rooms.editor_wall_back'),  'preview' => __('cms.virtual_3d_rooms.editor_wall_back')],
            ]
        ],
        'messages' => [
            'mediaEmpty' => __('cms.virtual_3d_rooms.media_empty'),
            'selectFile' => __('cms.virtual_3d_rooms.media_upload_choose'),
            'uploadSuccess' => __('cms.virtual_3d_rooms.media_upload_success'),
            'uploadFailed' => __('cms.virtual_3d_rooms.media_upload_failed'),
            'saveSuccess' => __('cms.virtual_3d_rooms.media_save_success'),
            'saveFailed' => __('cms.virtual_3d_rooms.media_save_failed'),
            'deleteConfirm' => __('cms.virtual_3d_rooms.media_delete_confirm'),
            'deleteSuccess' => __('cms.virtual_3d_rooms.media_delete_success'),
            'deleteFailed' => __('cms.virtual_3d_rooms.media_delete_failed'),
            'doorLabel' => __('cms.virtual_3d_rooms.door_label_placeholder'),
        ],
        'badgeSuffix' => __('cms.virtual_3d_rooms.media_count'),
        'deleteBtn' => __('cms.virtual_3d_rooms.media_delete')
    ]) !!}</script>
    {{-- Load external JS first so functions are available --}}
    <script src="{{ asset('js/cms/virtual_3d_rooms.js') }}"></script>
    <script src="{{ asset('js/cms/virtual_3d_rooms_edit.js') }}"></script>
    <script>
        var __t = {
            caption_single: '{{ __('cms.virtual_3d_rooms.caption_single') }}',
            caption_multi_qa: '{{ __('cms.virtual_3d_rooms.caption_multi_qa') }}',
            question: '{{ __('cms.virtual_3d_rooms.question') }}',
            answer: '{{ __('cms.virtual_3d_rooms.answer') }}',
            add_qa: '{{ __('cms.virtual_3d_rooms.add_qa') }}',
        };

        window.allRteInstances = [];

        function createCaptionWidget(containerEl, namePrefix, captionIndex, existingData, options) {
            options = options || {};
            var singlePlaceholder = options.singlePlaceholder || '{{ __('cms.virtual_3d_rooms.media_desc_placeholder') }}';
            
            var existingMode = 'single';
            var existingSingle = '';
            var existingQa = [];
            
            if (existingData) {
                if (typeof existingData === 'object' && existingData.type === 'multi') {
                    existingMode = 'multi';
                    existingQa = existingData.items || [];
                } else if (typeof existingData === 'string' && (existingData.startsWith('{') || existingData.startsWith('['))) {
                    try {
                        var parsed = JSON.parse(existingData);
                        if (parsed.type === 'multi') {
                            existingMode = 'multi';
                            existingQa = parsed.items || [];
                        } else if (Array.isArray(parsed)) {
                            existingMode = 'multi';
                            existingQa = parsed;
                        } else {
                            existingSingle = existingData;
                        }
                    } catch(e) {
                        existingSingle = existingData;
                    }
                } else if (typeof existingData === 'string') {
                    existingSingle = existingData;
                } else if (Array.isArray(existingData)) {
                    existingMode = 'multi';
                    existingQa = existingData;
                }
            }

            // Cleanup old editors in this container before clearing
            containerEl.querySelectorAll('.rte-caption-editor').forEach(el => {
                if (el.__rte_instance) {
                    try { el.__rte_instance.destroy(); } catch(e) {}
                    if (window.allRteInstances) {
                        window.allRteInstances = window.allRteInstances.filter(ed => ed !== el.__rte_instance);
                    }
                    delete el.__rte_instance;
                }
            });

            containerEl.innerHTML = '';

            var modeDiv = document.createElement('div');
            modeDiv.className = 'caption-widget-mode';
            var modeSelect = document.createElement('select');
            modeSelect.innerHTML = '<option value="single"' + (existingMode === 'single' ? ' selected' : '') + '>' + __t.caption_single + '</option>' +
                                  '<option value="multi"' + (existingMode === 'multi' ? ' selected' : '') + '>' + __t.caption_multi_qa + '</option>';
            modeDiv.appendChild(modeSelect);
            containerEl.appendChild(modeDiv);

            var singleDiv = document.createElement('div');
            singleDiv.className = 'caption-single-section';
            singleDiv.style.display = existingMode === 'single' ? 'block' : 'none';
            var singleInput = document.createElement('textarea');
            singleInput.className = 'form-input rte-caption-editor';
            singleInput.placeholder = singlePlaceholder;
            singleInput.rows = 3;
            singleInput.value = existingSingle;
            singleDiv.appendChild(singleInput);
            containerEl.appendChild(singleDiv);

            function attachRTE(el, initialVal) {
                if (typeof RichTextEditor === 'undefined') {
                    setTimeout(function() { attachRTE(el, initialVal); }, 200);
                    return;
                }
                
                var editor = new RichTextEditor(el, {
                    base_url: '/cms_rte',
                    editorBodyCssClass: 'rte-content-body',
                    height: 80,
                    showStatusBar: false,
                    toolbar: "bold,italic,underline,|,forecolor,backcolor,|,justifyleft,justifycenter,|,insertorderedlist,insertunorderedlist,|,link,|,undo,redo,codeview"
                });
                
                if (initialVal) {
                    try { editor.setHTMLCode(initialVal); } catch(e) {}
                }
                
                // Store instance on the element for easy retrieval
                el.__rte_instance = editor;
                
                if (!window.allRteInstances) window.allRteInstances = [];
                window.allRteInstances.push(editor);
                return editor;
            }

            var singleEditor = null;
            if (existingMode === 'single') {
                singleEditor = attachRTE(singleInput, existingSingle);
            }

            var multiDiv = document.createElement('div');
            multiDiv.className = 'caption-multi-section';
            multiDiv.style.display = existingMode === 'multi' ? 'block' : 'none';

            var qaList = document.createElement('div');
            qaList.className = 'caption-qa-list';
            multiDiv.appendChild(qaList);

            var qaCounter = { value: 0 };
            function addQaPair(q, a) {
                var idx = qaCounter.value++;
                var pair = document.createElement('div');
                pair.className = 'caption-qa-pair';
                pair.innerHTML = '<button type="button" class="caption-qa-remove" onclick="this.parentElement.remove()">✕</button>' +
                                '<label style="font-size:0.65rem;color:#6b7280;margin-bottom:2px;display:block;">' + __t.question + '</label>' +
                                '<input type="text" class="qa-q" placeholder="' + __t.question + '..." value="' + (q || '').replace(/"/g, '&quot;') + '">' +
                                '<label style="font-size:0.65rem;color:#6b7280;margin:4px 0 2px;display:block;">' + __t.answer + '</label>' +
                                '<textarea class="qa-a rte-caption-editor">' + (a || '').replace(/</g, '&lt;') + '</textarea>';
                qaList.appendChild(pair);
                var aTextarea = pair.querySelector('.qa-a');
                if (aTextarea) attachRTE(aTextarea, a);
            }

            if (existingQa.length > 0) {
                existingQa.forEach(function(item) { addQaPair(item.question || '', item.answer || ''); });
            } else if (existingMode === 'multi') {
                addQaPair('', '');
            }

            var addBtn = document.createElement('button');
            addBtn.type = 'button';
            addBtn.className = 'caption-qa-add';
            addBtn.innerHTML = '+ ' + __t.add_qa;
            addBtn.addEventListener('click', function() { addQaPair('', ''); });
            multiDiv.appendChild(addBtn);
            containerEl.appendChild(multiDiv);

            modeSelect.addEventListener('change', function() {
                if (this.value === 'multi') {
                    singleDiv.style.display = 'none';
                    multiDiv.style.display = 'block';
                    if (qaList.children.length === 0) addQaPair('', '');
                } else {
                    singleDiv.style.display = 'block';
                    multiDiv.style.display = 'none';
                    if (!singleEditor) singleEditor = attachRTE(singleInput, existingSingle);
                }
            });

            return {
                getData: function() {
                    try {
                        if (modeSelect.value === 'single') {
                            let content = '';
                            const sInput = containerEl.querySelector('.rte-caption-editor:not(.qa-a)');
                            if (sInput && sInput.__rte_instance) {
                                try {
                                    content = sInput.__rte_instance.getHTMLCode() || sInput.__rte_instance.getHTML() || (sInput.__rte_instance.content ? sInput.__rte_instance.content.innerHTML : '');
                                    sInput.value = content; // Sync
                                } catch(e) {
                                    content = sInput.value;
                                }
                            } else if (sInput) {
                                content = sInput.value;
                            }
                            return content || '';
                        } else {
                            var items = [];
                            const pairs = qaList.querySelectorAll('.caption-qa-pair');
                            pairs.forEach(function(pair, i) {
                                var qInput = pair.querySelector('.qa-q');
                                var q = qInput ? qInput.value : '';
                                var a = '';
                                var aTextarea = pair.querySelector('.qa-a');
                                if (aTextarea && aTextarea.__rte_instance) {
                                    try { 
                                        const inst = aTextarea.__rte_instance;
                                        a = inst.getHTMLCode() || inst.getHTML() || (inst.content ? inst.content.innerHTML : '');
                                        aTextarea.value = a; // Sync
                                    } catch(e) { 
                                        a = aTextarea.value; 
                                    }
                                } else if (aTextarea) {
                                    a = aTextarea.value;
                                }
                                items.push({ question: q || '', answer: a || '' });
                            });
                            return { type: 'multi', items: items };
                        }
                    } catch (err) {
                        console.error('Error in getData:', err);
                        return modeSelect.value === 'single' ? '' : { type: 'multi', items: [] };
                    }
                }
            };
        }

        var uploadWidget = null;
        var propWidget = null;

        (function() {
            var cfg = JSON.parse(document.getElementById('v3dConfig').textContent);
            window.v3dCsrf = cfg.csrf;
            window.v3dRoutes = cfg.routes;
            window.v3dConfig = cfg;

            var wallEl = document.getElementById('wallEditor');
            if (wallEl) wallEl.style.backgroundColor = cfg.wallColor;


            // Override selectItem and saveActiveMedia
            var originalSelectItem = window.selectItem;
            window.selectItem = function(id) {
                originalSelectItem(id);
                var item = mediaItems.find(m => m.id == id);
                if (item) {
                    var propContainer = document.getElementById('propCaptionWidget');
                    if (propContainer) {
                        propWidget = createCaptionWidget(propContainer, 'prop_caption', id, item.description || '', {});
                    }
                }
            };

            window.saveV3DActiveMedia = async function() {
                if (!activeMediaId || !activeItem) return;
                
                try {
                    const descriptionData = propWidget ? propWidget.getData() : '';
                    const finalDescription = typeof descriptionData === 'object' ? JSON.stringify(descriptionData) : descriptionData;
                    console.log('Final description to save:', finalDescription);

                    const url = window.v3dRoutes.updateMedia.replace('__MEDIA_ID__', activeItem.id);

                    const response = await fetch(url, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': window.v3dCsrf
                        },
                        body: JSON.stringify({
                            position_x: activeItem.position_x,
                            position_y: activeItem.position_y,
                            width: activeItem.width,
                            height: activeItem.height,
                            description: finalDescription
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        activeItem.description = data.media.description;
                        
                        // Robust toast message retrieval
                        let successMsg = 'Saved!';
                        try {
                            if (window.v3dConfig && window.v3dConfig.translations && window.v3dConfig.translations.messages) {
                                successMsg = window.v3dConfig.translations.messages.saveSuccess || successMsg;
                            }
                        } catch(e) {}
                        showToast(successMsg);
                    } else {
                        alert('Save failed: ' + (data.message || 'Unknown error from server'));
                    }
                } catch (error) {
                    console.error('Save error details:', error);
                    alert('Error while saving: ' + error.message);
                }
            };

            window.uploadNewMedia = async function() {
                try {
                    const fileInput = document.getElementById('uploadFile');
                    if (!fileInput || !fileInput.files.length) {
                        alert(cfg.translations.uploadChoose);
                        return;
                    }
                    const formData = new FormData();
                    formData.append('file', fileInput.files[0]);
                    formData.append('wall', document.getElementById('uploadWall').value);
                    formData.append('type', document.getElementById('uploadType').value);
                    formData.append('position_x', 50);
                    formData.append('position_y', 50);
                    formData.append('width', 30);
                    formData.append('height', 40);
                    
                    formData.append('description', '');

                    const response = await fetch(window.v3dRoutes.upload, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': window.v3dCsrf },
                        body: formData
                    });
                    const data = await response.json();
                    if (data.success) {
                        mediaItems.push(data.media);
                        renderWallItems();
                        selectItem(data.media.id);
                        fileInput.value = '';
                        
                        
                        addMediaToList(data.media);
                        showToast(cfg.translations.uploadSuccess);
                    } else {
                        alert('Upload failed: ' + (data.message || 'Error'));
                    }
                } catch (error) {
                    console.error('Upload error:', error);
                    alert('Error: ' + error.message);
                }
            };
        })();
    </script>
@endpush
