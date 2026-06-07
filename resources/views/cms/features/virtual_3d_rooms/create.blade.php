@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/cms/virtual_3d_rooms.css') }}">
    <link rel="stylesheet" href="{{ asset('css/cms/virtual_3d_rooms_form.css') }}">
@endpush

@php $isEdit = false; @endphp

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
        class="text-gray-400 hover:text-gray-600 transition-colors">{{ $feature->name }}</a>
@endsection
@section('breadcrumb_active', __('cms.virtual_3d_rooms.breadcrumb_create'))

@section('content')
    <div class="mb-4">
        <a href="{{ route('cms.features.virtual_3d_rooms.index', $feature) }}"
            class="inline-flex items-center gap-2 px-3 py-2 rounded-lg text-white text-sm font-medium transition-colors shadow-sm"
            style="background-color: #818284;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            {{ __('cms.virtual_3d_rooms.back_to_list') }}
        </a>
    </div>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">{{ __('cms.virtual_3d_rooms.form_title_create') }}</h1>
        <p class="text-sm text-gray-500 mt-1">{{ __('cms.virtual_3d_rooms.form_desc_create') }}</p>
    </div>

    <form action="{{ route('cms.features.virtual_3d_rooms.store', $feature) }}" method="POST"
        enctype="multipart/form-data" id="virtual3d-room-form">
        @csrf
        <input type="hidden" name="auto_thumbnail" id="autoThumbnailInput">

        <div class="flex flex-col lg:flex-row gap-6 items-start">

            <!-- Left Column: Form & Hotspots -->
            <div class="space-y-6 w-full lg:w-[38%] lg:shrink-0">

                <!-- Basic Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <h3 class="text-sm font-semibold text-gray-800 mb-4">{{ __('cms.virtual_3d_rooms.info_title') }}</h3>

                    <div class="space-y-4">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1">{{ __('cms.virtual_3d_rooms.label_name') }}
                                <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1">{{ __('cms.virtual_3d_rooms.label_desc') }}
                                <span class="text-red-500">*</span></label>
                            <textarea name="description" rows="3" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description') }}</textarea>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1">{{ __('cms.virtual_3d_rooms.label_thumbnail') }}</label>
                            <input type="file" name="thumbnail" accept="image/jpeg,image/png,image/webp"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white file:mr-4 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                            <p class="text-xs text-gray-500 mt-1.5">{{ __('cms.virtual_3d_rooms.thumbnail_help') }}</p>
                        </div>

                        <div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        {{ __('cms.virtual_3d_rooms.label_diameter_front') }} <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" name="diameter_front" value="{{ old('diameter_front', 1000) }}" required min="1"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        {{ __('cms.virtual_3d_rooms.label_diameter_back') }} <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" name="diameter_back" value="{{ old('diameter_back', 1000) }}" required min="1"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        {{ __('cms.virtual_3d_rooms.label_diameter_left') }} <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" name="diameter_left" value="{{ old('diameter_left', 1000) }}" required min="1"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        {{ __('cms.virtual_3d_rooms.label_diameter_right') }} <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" name="diameter_right" value="{{ old('diameter_right', 1000) }}" required min="1"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1.5">{{ __('cms.virtual_3d_rooms.diameter_help') }}</p>
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
                                    value="{{ old('wall_color', '#e5e7eb') }}"
                                    class="w-10 h-10 p-1 border border-gray-300 rounded-lg cursor-pointer"
                                    onchange="updatePreviewColors()">
                                <input type="text" id="wallColorText" value="{{ old('wall_color', '#e5e7eb') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" readonly>
                            </div>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1">{{ __('cms.virtual_3d_rooms.label_floor_color') }}</label>
                            <div class="flex items-center gap-3">
                                <input type="color" name="floor_color" id="floorColorInput"
                                    value="{{ old('floor_color', '#8B7355') }}"
                                    class="w-10 h-10 p-1 border border-gray-300 rounded-lg cursor-pointer"
                                    onchange="updatePreviewColors()">
                                <input type="text" id="floorColorText" value="{{ old('floor_color', '#8B7355') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" readonly>
                            </div>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1">{{ __('cms.virtual_3d_rooms.label_ceiling_color') }}</label>
                            <div class="flex items-center gap-3">
                                <input type="color" name="ceiling_color" id="ceilingColorInput"
                                    value="{{ old('ceiling_color', '#f5f5f5') }}"
                                    class="w-10 h-10 p-1 border border-gray-300 rounded-lg cursor-pointer"
                                    onchange="updatePreviewColors()">
                                <input type="text" id="ceilingColorText" value="{{ old('ceiling_color', '#f5f5f5') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Door / Hotspot Settings -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5" x-data="{
                    currentWall: 'front',
                    doors: {
                        'front': { 'link_type': 'none', 'target': null, 'label': null },
                        'back': { 'link_type': 'none', 'target': null, 'label': null },
                        'left': { 'link_type': 'none', 'target': null, 'label': null },
                        'right': { 'link_type': 'none', 'target': null, 'label': null },
                    },
                    syncDoors() {
                        const pvDoor = document.getElementById('pv-door');
                        if (pvDoor) {
                            const backDoor = this.doors['back'] || { link_type: 'none' };
                            if (backDoor.link_type === 'room' || backDoor.link_type === 'url') {
                                pvDoor.style.display = 'flex';
                            } else {
                                pvDoor.style.display = 'none';
                            }
                        }
                    },
                    init() {
                        this.$watch('doors', () => { this.syncDoors(); }, { deep: true });
                        this.syncDoors();
                    }
                }">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold text-gray-800">{{ __('cms.virtual_3d_rooms.door_title') }}</h3>
                        <div class="flex gap-1 p-1 bg-gray-100 rounded-lg">
                            <template x-for="wall in ['front', 'left', 'right', 'back']">
                                <button type="button" @click="currentWall = wall"
                                    :class="currentWall === wall ? 'bg-white text-blue-600 shadow-sm' :
                                        'text-gray-500 hover:text-gray-700'"
                                    class="px-2 py-1 text-[10px] font-bold rounded"
                                    x-text="{ 'front': '{{ __('cms.virtual_3d_rooms.preview_btn_front') }}', 'left': '{{ __('cms.virtual_3d_rooms.preview_btn_left') }}', 'right': '{{ __('cms.virtual_3d_rooms.preview_btn_right') }}', 'back': '{{ __('cms.virtual_3d_rooms.preview_btn_back') }}' }[wall]">
                                </button>
                            </template>
                        </div>
                    </div>

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
                            <select
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                x-model="doors[currentWall].link_type">
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
                            <p class="text-xs text-gray-500 mt-1">
                                {{ __('cms.virtual_3d_rooms.rooms_available', ['count' => $allRooms->count()]) }}</p>
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
                            <input type="text" x-model="doors[currentWall].label"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                                placeholder="{{ __('cms.virtual_3d_rooms.door_label_placeholder') }}">
                        </div>
                    </div>
                </div>

                {{-- {{ __('cms.virtual_3d_rooms.media_title') }} (disabled on create – available after save) --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 opacity-60">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold text-gray-800">{{ __('cms.virtual_3d_rooms.media_title') }}</h3>
                    </div>
                    <div class="text-center py-6 border-2 border-dashed border-gray-200 rounded-lg bg-gray-50">
                        <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p class="text-sm font-medium text-gray-500">{{ __('cms.virtual_3d_rooms.media_save_first') }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ __('cms.virtual_3d_rooms.media_save_first_sub') }}</p>
                    </div>
                </div>

            </div>

            <!-- Right Column: 3D Preview -->
            <div class="w-full lg:w-[62%]">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <h3 class="text-sm font-semibold text-gray-800 mb-1">{{ __('cms.virtual_3d_rooms.preview_title') }}
                    </h3>
                    <p class="text-xs text-gray-500 mb-4">{{ __('cms.virtual_3d_rooms.preview_desc') }}</p>

                    <div class="room3d-preview-wrap" id="preview3dContainer">
                        <div class="room3d-scene" id="preview3dScene">
                            <div class="room3d-face front" id="pv-wall-front">
                                {{ __('cms.virtual_3d_rooms.preview_front') }}</div>
                            <div class="room3d-face back" id="pv-wall-back">
                                <span>{{ __('cms.virtual_3d_rooms.preview_back') }}</span>
                                <div class="room3d-door" id="pv-door" style="display: none;">
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

                        <!-- Rotation controls overlay -->
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
                {{ __('cms.virtual_3d_rooms.btn_save_create') }}
            </button>
        </div>
    </form>
@endsection

@push('scripts')
    <script src="{{ asset('js/cms/virtual_3d_rooms_create.js') }}"></script>
@endpush
