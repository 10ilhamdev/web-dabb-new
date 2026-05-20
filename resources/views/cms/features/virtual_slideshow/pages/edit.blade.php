@extends('layouts.app')

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
    @if(isset($page))
        <span class="text-gray-300">/</span>
        <a href="{{ route('cms.features.slideshow.pages.slides.index', [$feature, $page]) }}"
            class="text-gray-400 hover:text-gray-600 transition-colors">{{ $page->title }}</a>
    @endif
@endsection
@section('breadcrumb_active', __('cms.virtual_slideshow.edit_slide_title'))

@push('styles')
    {{-- RTE CSS loaded globally via layouts/app.blade.php --}}
    <link rel="stylesheet" href="{{ asset('css/cms/virtual_slideshow/pages/edit.css') }}">
    <style>
        /* Force RTE toolbar into a single scrollable row for captions */
        .rte-compact-container .rte-toolbar,
        .rte-compact-container .rte-tb-top,
        .rte-compact-container .rte-tb-bottom {
            white-space: nowrap !important;
            overflow-x: auto !important;
            display: flex !important;
            flex-wrap: nowrap !important;
            padding: 0 !important;
            background: #f8fafc !important;
            height: 34px !important;
            min-height: 34px !important;
        }
        .rte-compact-container .rte-tb-top {
            border-bottom: none !important;
            margin-bottom: 0 !important;
        }
        .rte-compact-container .rte-tb-btn {
            transform: scale(0.7) !important;
            margin: 0 !important;
            width: 24px !important;
            height: 24px !important;
            flex-shrink: 0 !important;
        }
        .rte-compact-container .richtexteditor {
            height: 125px;
            min-height: 125px !important;
            border-radius: 4px !important;
            overflow: hidden !important;
            border: 1px solid #e2e8f0 !important;
            display: flex !important;
            flex-direction: column !important;
        }
        .rte-compact-container .rte-content-wrap {
            flex: 1 1 auto !important;
            height: auto;
            min-height: 40px !important;
            overflow-y: auto !important;
        }
        .rte-compact-container .rte-statusbar {
            height: 24px !important;
            min-height: 24px !important;
            padding: 0 8px !important;
            font-size: 10px !important;
            background: #f8fafc !important;
            border-top: 1px solid #e2e8f0 !important;
            display: flex !important;
        }
        .rte-compact-container .rte-statusbar * {
            font-size: 10px !important;
        }
    </style>
@endpush

@section('content')
    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex items-center gap-3">
            <a href="{{ isset($page) ? route('cms.features.slideshow.pages.slides.index', [$feature, $page]) : route('cms.features.slideshow.index', $feature) }}"
                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-white transition-colors shadow-sm"
                style="background-color: #818284;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">{{ __('cms.virtual_slideshow.edit_slide_title') }}</h1>
                <p class="text-sm text-gray-500 mt-0.5">{{ $feature->name }}</p>
                @if (isset($page))
                    <p class="text-sm text-blue-600 mt-0.5">
                        {{ __('cms.virtual_slideshow.page_label', ['title' => $page->title]) }}</p>
                @endif
            </div>
        </div>

        <form
            action="{{ isset($page) ? route('cms.features.slideshow.pages.slides.update', [$feature, $page, $slide]) : route('cms.features.slideshow.update', [$feature, $slide]) }}"
            method="POST" enctype="multipart/form-data" id="slideForm"
            data-redirect="{{ isset($page) ? route('cms.features.slideshow.pages.slides.index', [$feature, $page]) : route('cms.features.slideshow.index', $feature) }}">
            @csrf @method('PUT')

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4">
                    <div class="font-semibold">{{ __('cms.virtual_slideshow.errors_found') }}</div>
                    <ul class="list-disc list-inside mt-1 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (isset($page))
                <input type="hidden" name="feature_page_id" value="{{ $page->id }}">
            @endif

            {{-- Tipe Slide --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                <h2 class="text-base font-semibold text-gray-800">{{ __('cms.virtual_slideshow.step1_type') }}</h2>
                <input type="hidden" name="slide_type" id="slide_type_input" value="{{ $slide->slide_type }}">
                <input type="hidden" name="unified_video_order" id="unifiedVideoOrder">
                <input type="hidden" name="existing_carousel_videos" id="existingCarouselVideosInput">
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3">
                    @foreach (['text', 'hero', 'carousel', 'video', 'text_carousel'] as $type)
                        <div class="slide-type-card {{ $slide->slide_type === $type ? 'active' : '' }}"
                            data-type="{{ $type }}"
                            onclick="{{ $type === 'hero' ? 'trySelectHero()' : "selectType('$type')" }}">
                            <div class="icon">
                                {{ ['text' => '📝', 'hero' => '🌟', 'carousel' => '🖼️', 'video' => '🎬', 'text_carousel' => '📋'][$type] }}
                            </div>
                            <div class="label">{{ __('cms.virtual_slideshow.type_' . $type) }}</div>
                            <div class="desc">{{ __('cms.virtual_slideshow.type_' . $type . '_desc') }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Konten --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4 mt-4">
                <h2 class="text-base font-semibold text-gray-800">{{ __('cms.virtual_slideshow.step2_content') }}</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">{{ __('cms.virtual_slideshow.slide_title_label') }} <span
                                class="text-gray-400 text-xs">({{ __('cms.virtual_slideshow.optional') }})</span></label>
                        <input type="text" name="title" class="form-input" value="{{ old('title', $slide->title) }}">
                    </div>
                    <div>
                        <label class="form-label">{{ __('cms.virtual_slideshow.slide_subtitle_label') }} <span
                                class="text-gray-400 text-xs">({{ __('cms.virtual_slideshow.optional') }})</span></label>
                        <input type="text" name="subtitle" class="form-input"
                            value="{{ old('subtitle', $slide->subtitle) }}">
                    </div>
                </div>
                <div class="rte-wrapper">
                    <label class="form-label">{{ __('cms.virtual_slideshow.slide_desc_label') }} <span
                            class="text-gray-400 text-xs">({{ __('cms.virtual_slideshow.optional') }} -
                            {{ __('cms.virtual_slideshow.desc_toolbar_hint') }})</span></label>
                    <div id="div_editor1" style="min-width:100%; min-height: 500px;">{!! old('description', $slide->description) !!}</div>
                    <input type="hidden" name="description" id="hiddenDescription">
                </div>

                <div class="panel-layout" style="display:none;">
                    <label class="form-label">{{ __('cms.virtual_slideshow.layout_label') }}</label>
                    <div class="flex gap-3">
                        @foreach (['left' => 'layout_left', 'center' => 'layout_center', 'right' => 'layout_right'] as $val => $key)
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="layout" value="{{ $val }}"
                                    {{ old('layout', $slide->layout) === $val ? 'checked' : '' }}>
                                <span class="text-sm text-gray-700">{{ __('cms.virtual_slideshow.' . $key) }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div class="panel-layout-center">
                    <input type="hidden" name="layout" value="center" id="layout_center_hidden">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="form-label">{{ __('cms.virtual_slideshow.bg_color_label') }}</label>
                        <div class="flex items-center gap-2">
                            <input type="color" name="bg_color"
                                value="{{ old('bg_color', $slide->bg_color ?? '#ffffff') }}"
                                class="w-10 h-10 rounded border border-gray-200 cursor-pointer">
                            <input type="text" id="bg_color_text"
                                value="{{ old('bg_color', $slide->bg_color ?? '#ffffff') }}" class="form-input"
                                style="width:140px;">
                        </div>
                    </div>
                    <div>
                        <label class="form-label">{{ __('cms.virtual_slideshow.order_label') }}</label>
                        <input type="number" name="order" min="0" value="{{ old('order', $slide->order) }}"
                            class="form-input" required>
                    </div>
                </div>
            </div>

            {{-- Gambar --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4 mt-4" id="panel-images">
                <h2 class="text-base font-semibold text-gray-800">{{ __('cms.virtual_slideshow.step3_media') }}</h2>

                {{-- Toggle untuk text_carousel: Gambar atau Video --}}
                <div id="carouselMediaToggle" class="flex gap-4 mb-3 hidden">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="carousel_media_type" value="images" checked
                            onchange="toggleCarouselMediaType('images')">
                        <span class="text-sm text-gray-700">{{ __('cms.virtual_slideshow.media_type_images') }}</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="carousel_media_type" value="videos"
                            onchange="toggleCarouselMediaType('videos')">
                        <span class="text-sm text-gray-700">{{ __('cms.virtual_slideshow.media_type_videos') }}</span>
                    </label>
                </div>

                {{-- Image Sections --}}
                <div id="imageSections">
                    <input type="hidden" name="unified_image_order" id="unifiedImageOrderInput" value="">

                    {{-- === DATA TERSIMPAN: Gambar Upload + Gambar URL disatukan di atas === --}}
                    @php
                        $hasExistingUploads = $slide->images && count($slide->images) > 0;
                        $hasExistingUrls = $slide->image_urls && count($slide->image_urls) > 0;
                        $existingUploadCount = $hasExistingUploads ? count($slide->images) : 0;
                        $unifiedImageOrder = $slide->info_popup['unified_image_order'] ?? null;

                        // Build ordered list of existing items based on unified_image_order
                        $orderedExistingItems = [];
                        if (
                            $unifiedImageOrder &&
                            is_array($unifiedImageOrder) &&
                            ($hasExistingUploads || $hasExistingUrls)
                        ) {
                            $orderPos = 0;
                            foreach ($unifiedImageOrder as $orderItem) {
                                $itemType = $orderItem['type'] ?? null;
                                if ($itemType === 'existing' || $itemType === 'upload') {
                                    $idx = $orderItem['existingIndex'] ?? ($orderItem['uploadIndex'] ?? null);
                                    if ($idx !== null && isset($slide->images[$idx])) {
                                        $orderedExistingItems[] = [
                                            'type' => 'upload',
                                            'idx' => (int) $idx,
                                            'path' => $slide->images[$idx],
                                            'orderPos' => $orderPos,
                                        ];
                                    }
                                } elseif ($itemType === 'existingUrl' || $itemType === 'url') {
                                    $idx = $orderItem['existingUrlIndex'] ?? ($orderItem['urlIndex'] ?? null);
                                    if ($idx !== null && isset($slide->image_urls[$idx])) {
                                        $orderedExistingItems[] = [
                                            'type' => 'url',
                                            'idx' => (int) $idx,
                                            'url' => $slide->image_urls[$idx],
                                            'orderPos' => $orderPos,
                                        ];
                                    }
                                }
                                $orderPos++;
                            }
                        }

                        // Fallback: if no unified order or incomplete, use default order (uploads then URLs)
                        if (empty($orderedExistingItems)) {
                            $orderPos = 0;
                            if ($hasExistingUploads) {
                                foreach ($slide->images as $idx => $imgPath) {
                                    $orderedExistingItems[] = [
                                        'type' => 'upload',
                                        'idx' => (int) $idx,
                                        'path' => $imgPath,
                                        'orderPos' => $orderPos,
                                    ];
                                    $orderPos++;
                                }
                            }
                            if ($hasExistingUrls) {
                                foreach ($slide->image_urls as $idx => $imgUrl) {
                                    $orderedExistingItems[] = [
                                        'type' => 'url',
                                        'idx' => (int) $idx,
                                        'url' => $imgUrl,
                                        'orderPos' => $orderPos,
                                    ];
                                    $orderPos++;
                                }
                            }
                        }
                    @endphp

                    @if ($hasExistingUploads || $hasExistingUrls)
                        <div class="mb-4">
                            <label class="form-label">{{ __('cms.virtual_slideshow.existing_images') }}</label>

                            {{-- Preview bersama: gambar upload + gambar URL yang sudah tersimpan (mengikuti unified_image_order) --}}
                            <div id="existingImagesArea" class="flex flex-wrap gap-2 mb-3">
                                @foreach ($orderedExistingItems as $orderPos => $item)
                                    @if ($item['type'] === 'upload')
                                        @php
                                            $idx = $item['idx'];
                                            $imgPath = $item['path'];
                                        @endphp
                                        <div class="existing-img-wrap" id="existing-wrap-{{ $idx }}"
                                            data-original-index="{{ $idx }}">
                                            <img src="{{ asset('storage/' . $imgPath) }}" alt="">
                                            <input type="hidden" name="existing_images[{{ $idx }}]"
                                                value="{{ $imgPath }}" id="existing-input-{{ $idx }}">
                                            <input type="hidden" name="deleted_images[]"
                                                id="deleted-input-{{ $idx }}" value="">
                                            <button type="button" class="remove-existing"
                                                onclick="removeExisting({{ $idx }})">✕</button>
                                        </div>
                                    @elseif ($item['type'] === 'url')
                                        @php
                                            $idx = $item['idx'];
                                            $imgUrl = $item['url'];
                                        @endphp
                                        <div class="existing-url-img-wrap" id="existing-url-wrap-{{ $idx }}"
                                            data-url-original-index="{{ $idx }}">
                                            @php
                                                $displayUrl = $imgUrl;
                                                if (preg_match('/\/file\/d\/([a-zA-Z0-9_-]+)/', $imgUrl, $m)) {
                                                    $displayUrl = 'https://lh3.googleusercontent.com/d/' . $m[1];
                                                } elseif (preg_match('/[?&]id=([a-zA-Z0-9_-]+)/', $imgUrl, $m)) {
                                                    $displayUrl = 'https://lh3.googleusercontent.com/d/' . $m[1];
                                                } elseif (
                                                    preg_match(
                                                        '/commons\.wikimedia\.org\/wiki\/File:(.+)/',
                                                        $imgUrl,
                                                        $m,
                                                    )
                                                ) {
                                                    $displayUrl =
                                                        'https://commons.wikimedia.org/wiki/Special:FilePath/' . $m[1];
                                                }
                                            @endphp
                                            <img src="{{ $displayUrl }}" alt=""
                                                style="height:60px;width:60px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb;"
                                                onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                                            <div class="flex flex-col items-center justify-center"
                                                style="height:60px;width:60px;background:#f3f4f6;border-radius:8px;border:1px solid #e5e7eb;display:none;">
                                                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                <a href="{{ $imgUrl }}" target="_blank"
                                                    class="text-xs text-blue-500 hover:text-blue-700 mt-1">{{ __('cms.virtual_slideshow.view') }}</a>
                                            </div>
                                            <button type="button" class="remove-existing"
                                                onclick="removeExistingUrl({{ $idx }})">✕</button>
                                        </div>
                                    @endif
                                @endforeach
                            </div>

                            {{-- Caption: gambar upload + gambar URL yang sudah tersimpan (mengikuti unified_image_order) --}}
                            <div id="existingInfoPopupRowsWrapper"
                                class="{{ ($slide->slide_type ?? $slide->type) === 'hero' ? 'hidden' : '' }}">
                                <div id="existingInfoPopupRows" class="space-y-2">
                                    <label
                                        class="form-label">{{ __('cms.virtual_slideshow.popup_existing_images') }}</label>
                                    @php $captionDisplayNum = 1; @endphp
                                    @foreach ($orderedExistingItems as $item)
                                        @if ($item['type'] === 'upload')
                                            @php $idx = $item['idx']; @endphp
                                            <div class="info-popup-row" id="existing-popup-row-{{ $idx }}">
                                                <label class="form-label"
                                                    style="margin-bottom:4px;">{{ __('cms.virtual_slideshow.image_number', ['number' => $captionDisplayNum]) }}</label>
                                                <div class="existing-caption-widget"
                                                    data-caption-index="{{ $idx }}"
                                                    data-caption-data="{{ json_encode($slide->info_popup[$item['orderPos']] ?? ($slide->info_popup[(string) $item['orderPos']] ?? '')) }}">
                                                </div>
                                            </div>
                                        @elseif ($item['type'] === 'url')
                                            @php $idx = $item['idx']; @endphp
                                            <div class="info-popup-row" id="existing-url-popup-row-{{ $idx }}">
                                                <label class="form-label" style="margin-bottom:4px;">Info Popup Caption
                                                    (gambar URL)
                                                    {{ $captionDisplayNum }}</label>
                                                <div class="existing-url-caption-widget"
                                                    data-url-caption-index="{{ $idx }}"
                                                    data-url-caption-data="{{ json_encode($slide->info_popup[$item['orderPos']] ?? ($slide->info_popup[(string) $item['orderPos']] ?? '')) }}">
                                                </div>
                                            </div>
                                        @endif
                                        @php $captionDisplayNum++; @endphp
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Radio untuk MENAMBAH gambar baru --}}
                    <div class="flex gap-4 mb-3">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="image_method" value="upload" checked
                                onchange="toggleImageMethod('upload')">
                            <span class="text-sm text-gray-700">{{ __('cms.virtual_slideshow.method_upload') }}</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="image_method" value="url"
                                onchange="toggleImageMethod('url')">
                            <span class="text-sm text-gray-700">{{ __('cms.virtual_slideshow.method_url') }}</span>
                        </label>
                    </div>

                    <div id="image-upload-section">
                        <label
                            class="flex items-center gap-3 px-4 py-3 border-2 border-dashed border-gray-200 rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition-colors">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="text-sm text-gray-500"
                                id="uploadHintText">{{ __('cms.virtual_slideshow.add_new_images') }}</span>
                            <input type="file" name="images[]" accept="image/*" multiple class="hidden"
                                id="imageInput" onchange="previewNewImages(this)">
                        </label>
                    </div>

                    {{-- Bagian URL: existing URL tetap di sini (agar JS berjalan), ditambah slot untuk URL baru --}}
                    <div id="image-url-section" class="hidden">
                        <div id="image-url-list" class="space-y-2 mb-3">
                            @if ($slide->image_urls && count($slide->image_urls) > 0)
                                @foreach ($slide->image_urls as $idx => $imgUrl)
                                    <div class="image-url-entry flex gap-2 items-start" data-index="{{ $idx }}"
                                        data-is-existing="1">
                                        <a href="{{ $imgUrl }}" target="_blank"
                                            class="url-link-btn px-2 py-2 text-blue-600 hover:bg-blue-50 rounded-lg flex-shrink-0"
                                            title="{{ __('cms.virtual_slideshow.open_link') }}">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                        </a>
                                        <input type="text" name="existing_image_urls[{{ $idx }}]"
                                            class="form-input flex-1"
                                            placeholder="{{ __('cms.virtual_slideshow.image_url_placeholder') }}"
                                            value="{{ $imgUrl }}" data-index="{{ $idx }}"
                                            oninput="updateUrlLink(this)">
                                        <button type="button" onclick="removeExistingUrl({{ $idx }})"
                                            class="px-2 py-2 text-red-500 hover:bg-red-50 rounded-lg flex-shrink-0"
                                            title="{{ __('cms.virtual_slideshow.delete') }}">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                @endforeach
                            @else
                                <div class="image-url-entry flex gap-2 items-start" data-index="0">
                                    <a href="#" target="_blank"
                                        class="url-link-btn px-2 py-2 text-blue-600 hover:bg-blue-50 rounded-lg flex-shrink-0 opacity-30 cursor-not-allowed"
                                        title="{{ __('cms.virtual_slideshow.open_link') }}" onclick="return false;">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                    </a>
                                    <input type="text" name="new_image_urls[0]" class="form-input flex-1"
                                        placeholder="{{ __('cms.virtual_slideshow.image_url_placeholder') }}"
                                        data-index="0" oninput="updateUrlLink(this)">
                                    <button type="button" onclick="removeImageUrlEntry(this)"
                                        class="px-2 py-2 text-red-500 hover:bg-red-50 rounded-lg flex-shrink-0"
                                        title="{{ __('cms.virtual_slideshow.delete') }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            @endif
                        </div>
                        <button type="button" onclick="addImageUrlEntry()" id="addImageUrlBtn"
                            class="flex items-center gap-2 px-4 py-2 text-sm text-blue-600 border border-blue-300 rounded-lg hover:bg-blue-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            {{ __('cms.virtual_slideshow.add_image_url') }}
                        </button>
                    </div>

                    <div id="newUrlPreviewArea" class="flex flex-wrap gap-3 mt-3 hidden"></div>

                    <div id="infoPopupUploadImageArea" class="mt-4 hidden" style="display: none;">
                        <label class="form-label">{{ __('cms.virtual_slideshow.popup_existing_images') }}</label>
                        <div id="infoPopupUploadRows" class="space-y-2"></div>
                    </div>

                    <div id="infoPopupUrlImageArea" class="mt-4 hidden" style="display: none;">
                        <label class="form-label">{{ __('cms.virtual_slideshow.popup_url_images') }}</label>
                        <div id="infoPopupUrlRows" class="space-y-2"></div>
                    </div>

                    <div id="heroImageLimitWarning"
                        class="hidden mt-2 px-3 py-2 bg-amber-50 border border-amber-200 rounded-lg text-xs text-amber-700">
                        {{ __('cms.virtual_slideshow.hero_limit_warning') }}
                    </div>
                    {{-- ./end imageSections --}}
                </div>

                {{-- Preview area for new uploaded images (outside imageSections) --}}
                <div id="newImagePreviewArea" class="flex flex-wrap gap-3 mt-3 hidden"></div>


                <script>
                    // Initialize on page load
                    (function initImageMethod() {
                        var uploadRadio = document.querySelector('input[name="image_method"][value="upload"]');
                        var urlRadio = document.querySelector('input[name="image_method"][value="url"]');
                        var uploadSection = document.getElementById('image-upload-section');
                        var urlSection = document.getElementById('image-url-section');
                        var uploadCaption = document.getElementById('infoPopupUploadImageArea');
                        var urlCaption = document.getElementById('infoPopupUrlImageArea');
                        var uploadPreviewArea = document.getElementById('newImagePreviewArea');
                        var urlPreviewArea = document.getElementById('newUrlPreviewArea');
                        var typeInput = document.getElementById('slide_type_input');
                        var isHeroType = typeInput && typeInput.value === 'hero';

                        if (urlRadio && urlRadio.checked) {
                            // URL method selected
                            uploadSection.classList.add('hidden');
                            uploadSection.style.display = 'none';
                            urlSection.classList.remove('hidden');
                            urlSection.style.display = '';
                            updateUrlImagePreviews();
                        } else {
                            // Upload method selected (default)
                            uploadSection.classList.remove('hidden');
                            uploadSection.style.display = '';
                            urlSection.classList.add('hidden');
                            urlSection.style.display = 'none';
                            updateUrlImagePreviews();
                        }

                        // Initialize hero caption/limit state based on current type
                        var typeInput = document.getElementById('slide_type_input');
                        if (typeInput && typeof window.selectType === 'function') {
                            window.selectType(typeInput.value);
                        }
                    })();
                </script>

                {{-- Video Sections (for text_carousel) --}}
                <div id="videoSections" class="hidden">
                    <div class="flex gap-4 mb-3">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="carousel_video_method" value="url" checked
                                onchange="toggleCarouselVideoMethod('url')">
                            <span class="text-sm text-gray-700">{{ __('cms.virtual_slideshow.method_url') }}</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="carousel_video_method" value="upload"
                                onchange="toggleCarouselVideoMethod('upload')">
                            <span class="text-sm text-gray-700">{{ __('cms.virtual_slideshow.method_upload') }}</span>
                        </label>
                    </div>

                    <div id="carousel-video-url-section">
                        <div id="carousel-video-url-list" class="space-y-2 mb-3">
                            @if (!empty($slide->carousel_video_urls))
                                @foreach ($slide->carousel_video_urls as $index => $videoUrl)
                                    <div class="carousel-video-url-entry flex gap-2 items-start"
                                        data-index="{{ $index }}">
                                        <input type="text" name="carousel_video_urls[]" class="form-input flex-1"
                                            placeholder="{{ __('cms.virtual_slideshow.carousel_video_url_placeholder') }}"
                                            data-index="{{ $index }}"
                                            data-caption="{{ is_array($slide->info_popup['carousel_videos']['url_' . $index] ?? '') ? json_encode($slide->info_popup['carousel_videos']['url_' . $index]) : $slide->info_popup['carousel_videos']['url_' . $index] ?? '' }}"
                                            value="{{ $videoUrl }}" oninput="updateCarouselUrlCaption(this)">
                                        <button type="button" onclick="removeCarouselVideoUrlEntry(this)"
                                            class="px-2 py-2 text-red-500 hover:bg-red-50 rounded-lg flex-shrink-0"
                                            title="{{ __('cms.virtual_slideshow.delete') }}">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                @endforeach
                            @else
                                <div class="carousel-video-url-entry flex gap-2 items-start" data-index="0">
                                    <input type="text" name="carousel_video_urls[]" class="form-input flex-1"
                                        placeholder="{{ __('cms.virtual_slideshow.carousel_video_url_placeholder') }}"
                                        data-index="0" data-caption="" oninput="updateCarouselUrlCaption(this)">
                                    <button type="button" onclick="removeCarouselVideoUrlEntry(this)"
                                        class="px-2 py-2 text-red-500 hover:bg-red-50 rounded-lg flex-shrink-0"
                                        title="{{ __('cms.virtual_slideshow.delete') }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            @endif
                        </div>
                        <button type="button" onclick="addCarouselVideoUrlEntry()"
                            class="flex items-center gap-2 px-4 py-2 text-sm text-blue-600 border border-blue-300 rounded-lg hover:bg-blue-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            {{ __('cms.virtual_slideshow.add_video_url') }}
                        </button>
                    </div>

                    <div id="carouselVideoUrlPreviewArea" class="flex flex-wrap gap-3 mb-3"></div>

                    <div id="carousel-video-upload-section" class="hidden">
                        <label
                            class="flex items-center gap-3 px-4 py-3 border-2 border-dashed border-gray-200 rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition-colors">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            <span
                                class="text-sm text-gray-500">{{ __('cms.virtual_slideshow.carousel_video_upload_hint') }}</span>
                            <input type="file" name="carousel_videos[]" multiple accept="video/*" class="hidden"
                                id="carouselVideoInput" onchange="previewCarouselVideos(this)">
                        </label>
                        <div id="carouselVideoPreviewArea" class="flex flex-wrap gap-3 mt-3"></div>
                    </div>

                    <div id="infoPopupCarouselVideoArea">
                        <label class="form-label mt-2">{{ __('cms.virtual_slideshow.popup_caption_videos') }} <span
                                class="text-gray-400 text-xs">({{ __('cms.virtual_slideshow.popup_caption_hint') }})</span></label>
                        <div id="carouselVideoInfoPopupRows" class="space-y-2">
                            <p class="text-xs text-gray-400 italic" id="noCarouselVideosHint">
                                {{ __('cms.virtual_slideshow.add_videos_first') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Video --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4 mt-4" id="panel-video">
                <h2 class="text-base font-semibold text-gray-800">{{ __('cms.virtual_slideshow.step4_video') }}</h2>

                <div class="flex gap-4 mb-3">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="video_method" value="url"
                            {{ !$slide->video_file ? 'checked' : '' }} onchange="toggleVideoMethod('url')">
                        <span class="text-sm text-gray-700">{{ __('cms.virtual_slideshow.method_url') }}</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="video_method" value="upload"
                            {{ $slide->video_file ? 'checked' : '' }} onchange="toggleVideoMethod('upload')">
                        <span class="text-sm text-gray-700">{{ __('cms.virtual_slideshow.method_upload') }}</span>
                    </label>
                </div>

                <input type="hidden" name="delete_existing_video" id="deleteExistingVideo" value="0">
                <input type="hidden" name="clear_existing_url" id="clearExistingUrl" value="0">

                <div id="video-url-section" class="{{ $slide->video_file ? 'hidden' : '' }}">
                    @if ($slide->video_url)
                        <div class="mb-3">
                            <label class="form-label">{{ __('cms.virtual_slideshow.existing_video_url') }}</label>
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                                <span class="text-sm text-gray-600 flex-1 truncate">{{ $slide->video_url }}</span>
                                <a href="{{ $slide->video_url }}" target="_blank"
                                    class="text-blue-600 hover:underline text-sm">{{ __('cms.virtual_slideshow.view') }}</a>
                                <button type="button" onclick="openDeleteUrlModal()"
                                    class="px-3 py-1 text-sm text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-colors">
                                    {{ __('cms.virtual_slideshow.delete') }}
                                </button>
                            </div>
                        </div>
                    @endif

                    <div class="flex gap-2 items-start">
                        <input type="text" name="video_url" class="form-input flex-1"
                            placeholder="{{ __('cms.virtual_slideshow.single_video_url_placeholder') }}"
                            value="{{ old('video_url', $slide->video_url) }}" oninput="previewVideoUrl(this)">
                        <div
                            class="url-preview-placeholder w-24 h-16 rounded-lg border border-gray-200 bg-gray-50 flex items-center justify-center overflow-hidden flex-shrink-0">
                            <span class="text-xs text-gray-400">{{ __('cms.virtual_slideshow.preview') }}</span>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="form-label">{{ __('cms.virtual_slideshow.popup_video_url') }}</label>
                        <div id="videoCaptionWidgetUrl"
                            data-caption-data="{{ json_encode($slide->info_popup['video_url'] ?? '') }}"></div>
                    </div>
                </div>

                <div id="video-upload-section" class="{{ !$slide->video_file ? 'hidden' : '' }}">
                    @if ($slide->video_file)
                        <div id="existingVideoInfo" class="mb-3">
                            <label class="form-label">{{ __('cms.virtual_slideshow.existing_video_upload') }}</label>
                            <div class="flex flex-col gap-3 p-3 bg-gray-50 rounded-lg">
                                <video class="w-full max-w-md rounded" controls>
                                    <source src="{{ asset('storage/' . $slide->video_file) }}" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                                <div class="flex items-center justify-between">
                                    <p class="text-sm text-gray-600 truncate">{{ basename($slide->video_file) }}</p>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ asset('storage/' . $slide->video_file) }}" target="_blank"
                                            class="text-blue-600 hover:underline text-sm">{{ __('cms.virtual_slideshow.open') }}</a>
                                        <button type="button" onclick="openDeleteVideoModal()"
                                            class="px-3 py-1 text-sm text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-colors">
                                            {{ __('cms.virtual_slideshow.delete') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    <label
                        class="flex items-center gap-3 px-4 py-3 border-2 border-dashed border-gray-200 rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition-colors">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        <span class="text-sm text-gray-500">{{ __('cms.virtual_slideshow.video_upload_hint') }}</span>
                        <input type="file" name="video_file" accept="video/*" class="hidden" id="videoInput"
                            onchange="previewVideoFile(this)">
                    </label>
                    <div id="videoFilePreview" class="mt-3 hidden">
                        <video id="video-preview-player" controls class="w-full max-w-md rounded-lg"></video>
                        <p id="video-file-name" class="text-sm text-gray-500 mt-2"></p>
                    </div>

                    <div class="mt-4">
                        <label class="form-label">{{ __('cms.virtual_slideshow.popup_video_upload') }}</label>
                        <div id="videoCaptionWidget"
                            data-caption-data="{{ json_encode($slide->info_popup['video'] ?? '') }}"></div>
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <div class="flex items-center justify-end gap-3 mt-4">
                <a href="{{ isset($page) ? route('cms.features.slideshow.pages.slides.index', [$feature, $page]) : route('cms.features.slideshow.index', $feature) }}"
                    class="px-5 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                    {{ __('cms.virtual_slideshow.cancel') }}
                </a>
                <button type="submit"
                    class="px-6 py-2.5 text-sm font-semibold text-white bg-[#174E93] hover:bg-blue-800 rounded-lg transition-colors shadow-sm">
                    {{ __('cms.virtual_slideshow.update_slide') }}
                </button>
            </div>

            {{-- Delete Video Modal --}}
            <div id="deleteVideoModal" class="fixed inset-0 items-center justify-center p-4 hidden"
                style="z-index: 9999;">
                <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeDeleteVideoModal()"
                    style="position: fixed; top: 0; right: 0; bottom: 0; left: 0;"></div>
                <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm z-[9999] p-6">
                    <div class="flex flex-col items-center text-center gap-4">
                        <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center">
                            <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-semibold text-gray-800">
                                {{ __('cms.virtual_slideshow.delete_video_upload_title') }}</h3>
                            <p class="text-sm text-gray-500 mt-1">
                                {{ __('cms.virtual_slideshow.delete_video_upload_confirm') }}
                            </p>
                        </div>
                        <div class="flex items-center gap-3 w-full">
                            <button type="button" onclick="closeDeleteVideoModal()"
                                class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                                {{ __('cms.virtual_slideshow.cancel') }}
                            </button>
                            <button type="button" onclick="submitDeleteVideoModal()"
                                class="flex-1 px-4 py-2.5 text-sm font-semibold text-white bg-red-500 hover:bg-red-600 rounded-lg transition-colors">
                                {{ __('cms.virtual_slideshow.delete') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Clear URL Modal --}}
            <div id="deleteUrlModal" class="fixed inset-0 items-center justify-center p-4 hidden" style="z-index: 9999;">
                <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeDeleteUrlModal()"
                    style="position: fixed; top: 0; right: 0; bottom: 0; left: 0;"></div>
                <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm z-[9999] p-6">
                    <div class="flex flex-col items-center text-center gap-4">
                        <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center">
                            <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-semibold text-gray-800">
                                {{ __('cms.virtual_slideshow.delete_video_url_title') }}</h3>
                            <p class="text-sm text-gray-500 mt-1">
                                {{ __('cms.virtual_slideshow.delete_video_url_confirm') }}
                            </p>
                        </div>
                        <div class="flex items-center gap-3 w-full">
                            <button type="button" onclick="closeDeleteUrlModal()"
                                class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                                {{ __('cms.virtual_slideshow.cancel') }}
                            </button>
                            <button type="button" onclick="submitDeleteUrlModal()"
                                class="flex-1 px-4 py-2.5 text-sm font-semibold text-white bg-red-500 hover:bg-red-600 rounded-lg transition-colors">
                                {{ __('cms.virtual_slideshow.delete') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                function openDeleteVideoModal() {
                    document.getElementById('deleteVideoModal').classList.remove('hidden');
                    document.getElementById('deleteVideoModal').classList.add('flex');
                }

                function closeDeleteVideoModal() {
                    document.getElementById('deleteVideoModal').classList.add('hidden');
                    document.getElementById('deleteVideoModal').classList.remove('flex');
                }

                function submitDeleteVideoModal() {
                    closeDeleteVideoModal();
                    document.getElementById('deleteExistingVideo').value = '1';
                    var existingVidInfo = document.getElementById('existingVideoInfo');
                    if (existingVidInfo) existingVidInfo.style.display = 'none';
                    var vidInput = document.getElementById('infoPopupVideoInput');
                    if (vidInput) vidInput.value = '';
                }

                function openDeleteUrlModal() {
                    document.getElementById('deleteUrlModal').classList.remove('hidden');
                    document.getElementById('deleteUrlModal').classList.add('flex');
                }

                function closeDeleteUrlModal() {
                    document.getElementById('deleteUrlModal').classList.add('hidden');
                    document.getElementById('deleteUrlModal').classList.remove('flex');
                }

                function submitDeleteUrlModal() {
                    closeDeleteUrlModal();
                    document.getElementById('clearExistingUrl').value = '1';
                    var urlSection = document.getElementById('video-url-section');
                    var urlDiv = urlSection.querySelector('.mb-3');
                    if (urlDiv) urlDiv.remove();
                    var vidInput = document.getElementById('infoPopupVideoInput');
                    if (vidInput) vidInput.value = '';
                }
            </script>
        </form>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript" src="{{ asset('cms_rte/rte.js?v=' . (file_exists(public_path('cms_rte/rte.js')) ? filemtime(public_path('cms_rte/rte.js')) : time())) }}"></script>
    <script type="text/javascript" src="{{ asset('cms_rte/all_plugins.js?v=' . (file_exists(public_path('cms_rte/all_plugins.js')) ? filemtime(public_path('cms_rte/all_plugins.js')) : time())) }}"></script>
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
    </style>
    <script>
        window.allRteInstances = [];
        var __t = {
            upload_images_first: '{{ __('cms.virtual_slideshow.upload_images_first') }}',
            add_videos_first: '{{ __('cms.virtual_slideshow.add_videos_first') }}',
            preview: '{{ __('cms.virtual_slideshow.preview') }}',
            view: '{{ __('cms.virtual_slideshow.view') }}',
            google_drive: '{{ __('cms.virtual_slideshow.method_url') }}',
            video_url: '{{ __('cms.virtual_slideshow.method_url') }}',
            image_url_placeholder: '{{ __('cms.virtual_slideshow.image_url_placeholder') }}',
            carousel_video_url_placeholder: '{{ __('cms.virtual_slideshow.carousel_video_url_placeholder') }}',
            caption_single: '{{ __('cms.virtual_slideshow.caption_single') }}',
            caption_multi_qa: '{{ __('cms.virtual_slideshow.caption_multi_qa') }}',
            question: '{{ __('cms.virtual_slideshow.question') }}',
            answer: '{{ __('cms.virtual_slideshow.answer') }}',
            add_qa: '{{ __('cms.virtual_slideshow.add_qa') }}',
            popup_existing_images: '{{ __('cms.virtual_slideshow.popup_existing_images') }}',
            popup_url_images: '{{ __('cms.virtual_slideshow.popup_url_images') }}',
            image_number: '{{ __('cms.virtual_slideshow.image_number') }}',
            single_video_caption: '{{ __('cms.virtual_slideshow.popup_video_upload') }}',
            url_video_caption: '{{ __('cms.virtual_slideshow.popup_video_url') }}',
            delete: '{{ __('cms.virtual_slideshow.delete') }}',
        };
        /**
         * Reusable Caption Widget: supports Single caption or Multi Q&A mode
         */
        function createCaptionWidget(containerEl, namePrefix, captionIndex, existingData, options) {
            options = options || {};
            var singlePlaceholder = options.singlePlaceholder || '{{ __('cms.virtual_slideshow.caption_single') }}...';
            var isArray = options.isArray !== false;

            var existingMode = 'single';
            var existingSingle = '';
            var existingQa = [];
            if (existingData && typeof existingData === 'object' && existingData.type === 'multi') {
                existingMode = 'multi';
                existingQa = existingData.items || [];
            } else if (existingData && typeof existingData === 'string') {
                existingSingle = existingData;
            }

            var modeNamePrefix = namePrefix.replace('info_popup_', 'info_popup_mode_');
            var qaNamePrefix = namePrefix.replace('info_popup_', 'info_popup_qa_');
            var modeName = isArray ? modeNamePrefix + '[' + captionIndex + ']' : modeNamePrefix;
            var singleName = namePrefix + (isArray ? '[' + captionIndex + ']' : '');

            containerEl.innerHTML = '';

            var modeDiv = document.createElement('div');
            modeDiv.className = 'caption-widget-mode';
            var modeSelect = document.createElement('select');
            modeSelect.name = modeName;
            modeSelect.innerHTML = '<option value="single"' + (existingMode === 'single' ? ' selected' : '') + '>' + __t
                .caption_single + '</option>' +
                '<option value="multi"' + (existingMode === 'multi' ? ' selected' : '') + '>' + __t.caption_multi_qa +
                '</option>';
            modeDiv.appendChild(modeSelect);
            containerEl.appendChild(modeDiv);

            var singleDiv = document.createElement('div');
            singleDiv.className = 'caption-single-section';
            singleDiv.style.display = existingMode === 'single' ? 'block' : 'none';
            var singleInput = document.createElement('textarea');
            singleInput.name = singleName;
            singleInput.className = 'form-input rte-caption-editor';
            singleInput.placeholder = singlePlaceholder;
            singleInput.rows = 3;
            singleInput.value = existingSingle;
            singleDiv.appendChild(singleInput);
            containerEl.appendChild(singleDiv);

            // Function to safely initialize RTE on an element
            function attachRTE(el) {
                if (typeof RichTextEditor === 'undefined') {
                    setTimeout(function() { attachRTE(el); }, 200);
                    return;
                }
                
                // Wrap in a compact container to force size via CSS
                var wrap = document.createElement('div');
                wrap.className = 'rte-compact-container';
                el.parentNode.insertBefore(wrap, el);
                wrap.appendChild(el);
                
                var editor = new RichTextEditor(el, {
                    base_url: '/cms_rte',
                    editorBodyCssClass: 'rte-content-body',
                    height: 100,
                    showStatusBar: false,
                    toolbar: "bold,italic,underline,|,forecolor,backcolor,|,justifyleft,justifycenter,|,insertorderedlist,insertunorderedlist,|,link,insertimage,|,undo,redo,codeview"
                });
                if (!window.allRteInstances) window.allRteInstances = [];
                window.allRteInstances.push(editor);
                return editor;
            }

            // Initialize RTE for single input if visible
            var singleEditor = null;
            if (existingMode === 'single') {
                singleEditor = attachRTE(singleInput);
            }

            var multiDiv = document.createElement('div');
            multiDiv.className = 'caption-multi-section';
            multiDiv.style.display = existingMode === 'multi' ? 'block' : 'none';

            var qaList = document.createElement('div');
            qaList.className = 'caption-qa-list';
            multiDiv.appendChild(qaList);

            var qaCounter = {
                value: 0
            };
            function addQaPair(q, a) {
                var idx = qaCounter.value++;
                var pair = document.createElement('div');
                pair.className = 'caption-qa-pair';
                var qaBaseName = isArray ? qaNamePrefix + '[' + captionIndex + '][' + idx + ']' : qaNamePrefix + '[' + idx +
                    ']';
                pair.innerHTML =
                    '<button type="button" class="caption-qa-remove" onclick="this.parentElement.remove()">✕</button>' +
                    '<label style="font-size:0.75rem;color:#6b7280;margin-bottom:2px;display:block;">' + __t.question +
                    '</label>' +
                    '<input type="text" name="' + qaBaseName + '[question]" placeholder="' + __t.question + '..." value="' +
                    (q || '').replace(/"/g, '&quot;') + '">' +
                    '<label style="font-size:0.75rem;color:#6b7280;margin:6px 0 2px;display:block;">' + __t.answer +
                    '</label>' +
                    '<textarea name="' + qaBaseName + '[answer]" placeholder="' + __t.answer + '..." class="rte-caption-editor">' + (a || '').replace(
                        /</g, '&lt;') + '</textarea>';
                qaList.appendChild(pair);

                // Attach RTE to answer textarea
                var aTextarea = pair.querySelector('textarea[name$="[answer]"]');
                if (aTextarea) attachRTE(aTextarea);
            }

            if (existingQa.length > 0) {
                existingQa.forEach(function(item) {
                    addQaPair(item.question || '', item.answer || '');
                });
            } else if (existingMode === 'multi') {
                addQaPair('', '');
            }

            var addBtn = document.createElement('button');
            addBtn.type = 'button';
            addBtn.className = 'caption-qa-add';
            addBtn.innerHTML = __t.add_qa;
            addBtn.addEventListener('click', function() {
                addQaPair('', '');
            });
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
                    if (!singleEditor) {
                        singleEditor = attachRTE(singleInput);
                    }
                }
            });

            return {
                addQaPair: addQaPair,
                modeSelect: modeSelect,
                singleInput: singleInput
            };
        }

        (function() {

            // Existing carousel video files (stored in video_file as JSON array)
            @php
                $existingCarouselVideos = [];
                $carouselVideoOrder = [];
                if ($slide->video_file && $slide->slide_type === 'text_carousel') {
                    $vf = $slide->video_file;
                    if (is_array($vf)) {
                        $existingCarouselVideos = $vf;
                    } elseif (is_string($vf) && str_starts_with($vf, '[')) {
                        $decoded = json_decode($vf, true);
                        $existingCarouselVideos = is_array($decoded) ? $decoded : [];
                    }
                }
                if (!empty($slide->info_popup['carousel_video_order'])) {
                    $carouselVideoOrder = $slide->info_popup['carousel_video_order'];
                }
            @endphp
            var existingCarouselVideos = {!! json_encode($existingCarouselVideos) !!};
            var carouselVideoOrder = {!! json_encode($carouselVideoOrder) !!};

            var typeConfig = {
                hero: {
                    showImages: true,
                    showVideo: false,
                    showCarouselVideo: false,
                    showLayout: false,
                    showImageCaption: false
                },
                text: {
                    showImages: false,
                    showVideo: false,
                    showCarouselVideo: false,
                    showLayout: false
                },
                carousel: {
                    showImages: true,
                    showVideo: false,
                    showCarouselVideo: false,
                    showLayout: false
                },
                video: {
                    showImages: false,
                    showVideo: true,
                    showCarouselVideo: false,
                    showLayout: false
                },
                text_carousel: {
                    showImages: true,
                    showVideo: false,
                    showCarouselVideo: false,
                    showLayout: true
                },
            };

            window.trySelectHero = function() {
                var currentType = document.getElementById('slide_type_input').value;
                var hasHeroSlide =
                    @isset($hasHeroSlide)
                        {{ $hasHeroSlide ? 'true' : 'false' }}
                    @else
                        false
                    @endisset ;
                if (hasHeroSlide && currentType !== 'hero') {
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __('cms.virtual_slideshow.hero_exists_title') }}',
                        text: '{{ __('cms.virtual_slideshow.hero_exists_error') }}',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#ef4444',
                    });
                    return;
                }
                selectType('hero');
            };

            window.selectType = function(type) {
                document.getElementById('slide_type_input').value = type;
                document.querySelectorAll('.slide-type-card').forEach(function(c) {
                    c.classList.remove('active');
                });
                var card = document.querySelector('.slide-type-card[data-type="' + type + '"]');
                if (card) card.classList.add('active');

                var cfg = typeConfig[type];
                document.getElementById('panel-images').style.display = cfg.showImages ? 'block' : 'none';
                document.getElementById('panel-video').style.display = cfg.showVideo ? 'block' : 'none';
                document.querySelectorAll('.panel-layout').forEach(function(el) {
                    el.style.display = cfg.showLayout ? 'block' : 'none';
                });
                var hiddenLayout = document.getElementById('layout_center_hidden');
                if (hiddenLayout) hiddenLayout.disabled = cfg.showLayout;

                // Show/hide image/video sections based on type
                var imageSections = document.getElementById('imageSections');
                var videoSections = document.getElementById('videoSections');
                var carouselToggle = document.getElementById('carouselMediaToggle');

                if (type === 'text_carousel') {
                    // Show toggle and check for existing video URLs to decide default
                    var hasExistingVideoUrls = document.querySelectorAll(
                            '#carousel-video-url-list input[name="carousel_video_urls[]"]').length > 0 &&
                        Array.from(document.querySelectorAll(
                            '#carousel-video-url-list input[name="carousel_video_urls[]"]')).some(function(input) {
                            return input.value && input.value.trim() !== '';
                        });

                    // Also check for existing uploaded videos
                    var hasExistingUploadedVideos = (typeof existingCarouselVideos !== 'undefined' &&
                        existingCarouselVideos.length > 0);

                    if (carouselToggle) carouselToggle.classList.remove('hidden');

                    if (hasExistingVideoUrls || hasExistingUploadedVideos) {
                        // Use videos mode if there are existing video URLs or uploads
                        imageSections.classList.add('hidden');
                        videoSections.classList.remove('hidden');
                        var vidRadio = carouselToggle ? carouselToggle.querySelector('input[value="videos"]') :
                            null;
                        if (vidRadio) vidRadio.checked = true;
                        toggleCarouselMediaType('videos');

                        // If there are uploaded videos but no URLs, default to upload method
                        if (hasExistingUploadedVideos && !hasExistingVideoUrls) {
                            var uploadRadio = document.querySelector(
                                'input[name="carousel_video_method"][value="upload"]');
                            if (uploadRadio) {
                                uploadRadio.checked = true;
                                toggleCarouselVideoMethod('upload');
                            }
                        }
                    } else {
                        // Default to images
                        if (imageSections) imageSections.classList.remove('hidden');
                        if (videoSections) videoSections.classList.add('hidden');
                        var imgRadio = carouselToggle ? carouselToggle.querySelector('input[value="images"]') :
                            null;
                        if (imgRadio) imgRadio.checked = true;
                        toggleCarouselMediaType('images');
                    }
                } else {
                    // Hide toggle, show images by default
                    if (carouselToggle) carouselToggle.classList.add('hidden');
                    if (imageSections) imageSections.classList.remove('hidden');
                    if (videoSections) videoSections.classList.add('hidden');
                }

                // Hero: enforce 1-gambar limit, sembunyikan URL section saat sudah ada gambar
                var isHero = (type === 'hero');
                if (isHero) {
                    var existingCount = 0;
                    var existingInputs = document.querySelectorAll(
                        '#existingImagesArea input[name^="existing_images["]:not([disabled])');
                    existingInputs.forEach(function(inp) {
                        existingCount++;
                    });
                    var hasExistingImage = existingCount > 0;

                    var addUrlBtn = document.getElementById('addImageUrlBtn');
                    if (addUrlBtn) {
                        addUrlBtn.style.display = 'none';
                    }

                    var heroLimitWarning = document.getElementById('heroImageLimitWarning');
                    if (heroLimitWarning) {
                        heroLimitWarning.style.display = hasExistingImage ? 'block' : 'none';
                    }

                    // Hide existing image caption widget for hero type
                    var existingCaptionWrapper = document.getElementById('existingInfoPopupRowsWrapper');
                    if (existingCaptionWrapper) {
                        existingCaptionWrapper.classList.add('hidden');
                        existingCaptionWrapper.style.display = 'none';
                    }
                } else {
                    // non-hero: restore "Tambah URL Gambar" button
                    var addUrlBtn = document.getElementById('addImageUrlBtn');
                    if (addUrlBtn) {
                        addUrlBtn.style.display = 'inline-flex';
                    }

                    // Show existing image caption widget for non-hero types
                    var existingCaptionWrapper = document.getElementById('existingInfoPopupRowsWrapper');
                    if (existingCaptionWrapper) {
                        existingCaptionWrapper.classList.remove('hidden');
                        existingCaptionWrapper.style.display = '';
                    }
                }

                // Clear video data when switching to non-video type
                if (!cfg.showVideo) {
                    document.getElementById('deleteExistingVideo').value = '1';
                    document.getElementById('clearExistingUrl').value = '1';
                    var existingVideoInfo = document.getElementById('existingVideoInfo');
                    if (existingVideoInfo) existingVideoInfo.style.display = 'none';
                } else {
                    document.getElementById('deleteExistingVideo').value = '0';
                    document.getElementById('clearExistingUrl').value = '0';
                    // Restore visibility of existing video info if it was hidden
                    var existingVideoInfo = document.getElementById('existingVideoInfo');
                    if (existingVideoInfo) {
                        existingVideoInfo.removeAttribute('hidden');
                        existingVideoInfo.style.display = '';
                    }
                    // Restore visibility of video sections and toggle based on current method
                    var videoUrlSection = document.getElementById('video-url-section');
                    var videoUploadSection = document.getElementById('video-upload-section');
                    if (videoUrlSection) {
                        videoUrlSection.classList.remove('hidden');
                        videoUrlSection.style.display = '';
                    }
                    if (videoUploadSection) {
                        videoUploadSection.classList.remove('hidden');
                        videoUploadSection.style.display = '';
                    }
                    // Sync visibility with checked radio
                    var videoMethodRadio = document.querySelector('input[name="video_method"]:checked');
                    if (videoMethodRadio) {
                        if (videoMethodRadio.value === 'url') {
                            if (videoUploadSection) {
                                videoUploadSection.classList.add('hidden');
                                videoUploadSection.style.display = 'none';
                            }
                        } else {
                            if (videoUrlSection) {
                                videoUrlSection.classList.add('hidden');
                                videoUrlSection.style.display = 'none';
                            }
                        }
                    }
                }
            };

            window.toggleCarouselMediaType = function(type) {
                var isImages = type === 'images';
                var imageSections = document.getElementById('imageSections');
                var videoSections = document.getElementById('videoSections');

                // Update radio button checked state
                var radioImages = document.querySelector('input[name="carousel_media_type"][value="images"]');
                var radioVideos = document.querySelector('input[name="carousel_media_type"][value="videos"]');
                if (radioImages) radioImages.checked = isImages;
                if (radioVideos) radioVideos.checked = !isImages;

                if (isImages) {
                    imageSections.classList.remove('hidden');
                    videoSections.classList.add('hidden');
                } else {
                    imageSections.classList.add('hidden');
                    videoSections.classList.remove('hidden');
                }
            };

            window.toggleImageMethod = function(method) {
                var uploadSection = document.getElementById('image-upload-section');
                var urlSection = document.getElementById('image-url-section');
                var uploadCaptionArea = document.getElementById('infoPopupUploadImageArea');
                var urlCaptionArea = document.getElementById('infoPopupUrlImageArea');
                var uploadPreviewArea = document.getElementById('newImagePreviewArea');
                var urlPreviewArea = document.getElementById('newUrlPreviewArea');
                var typeInput = document.getElementById('slide_type_input');
                var isHeroType = typeInput && typeInput.value === 'hero';
                if (method === 'url') {
                    // HERO: jika sudah ada gambar upload, tolak switch ke URL
                    if (isHeroType) {
                        var hasNewUpload = typeof selectedNewImageFiles !== 'undefined' && selectedNewImageFiles
                            .length > 0;
                        var existingInputs = document.querySelectorAll(
                            '#existingImagesArea input[name^="existing_images["]:not([disabled])');
                        var hasExistingUpload = existingInputs.length > 0;

                        if (hasNewUpload || hasExistingUpload) {
                            Swal.fire({
                                icon: 'warning',
                                title: '{{ __('cms.virtual_slideshow.hero_url_restriction') }}',
                                text: '{{ __('cms.virtual_slideshow.hero_upload_restriction') }}',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#d97706',
                            });
                            var uploadRadio = document.querySelector('input[name="image_method"][value="upload"]');
                            if (uploadRadio) uploadRadio.checked = true;
                            uploadSection.classList.remove('hidden');
                            uploadSection.style.display = '';
                            urlSection.classList.add('hidden');
                            urlSection.style.display = 'none';
                            return;
                        }
                    }
                    uploadSection.classList.add('hidden');
                    uploadSection.style.display = 'none';
                    urlSection.classList.remove('hidden');
                    urlSection.style.display = '';
                    var existingCaptionWrapper = document.getElementById('existingInfoPopupRowsWrapper');
                    if (existingCaptionWrapper) {
                        existingCaptionWrapper.classList.add('hidden');
                        existingCaptionWrapper.style.display = 'none';
                    }
                    updateUrlImagePreviews();
                } else {
                    // HERO: jika sudah ada URL yang diisi, tolak switch ke Upload
                    if (isHeroType) {
                        var urlEntries = document.querySelectorAll('#image-url-list .image-url-entry');
                        var hasFilledUrl = false;
                        urlEntries.forEach(function(entry) {
                            var input = entry.querySelector('input[name^="new_image_urls"]');
                            if (input && input.value.trim() !== '') hasFilledUrl = true;
                        });

                        if (hasFilledUrl) {
                            Swal.fire({
                                icon: 'warning',
                                title: '{{ __('cms.virtual_slideshow.hero_upload_restriction') }}',
                                text: '{{ __('cms.virtual_slideshow.hero_url_restriction') }}',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#d97706',
                            });
                            var urlRadio = document.querySelector('input[name="image_method"][value="url"]');
                            if (urlRadio) urlRadio.checked = true;
                            uploadSection.classList.add('hidden');
                            uploadSection.style.display = 'none';
                            urlSection.classList.remove('hidden');
                            urlSection.style.display = '';
                            return;
                        }
                    }
                    uploadSection.classList.remove('hidden');
                    uploadSection.style.display = '';
                    urlSection.classList.add('hidden');
                    urlSection.style.display = 'none';
                    var existingCaptionWrapper = document.getElementById('existingInfoPopupRowsWrapper');
                    if (existingCaptionWrapper) {
                        if (isHeroType) {
                            existingCaptionWrapper.classList.add('hidden');
                            existingCaptionWrapper.style.display = 'none';
                        } else {
                            existingCaptionWrapper.classList.remove('hidden');
                            existingCaptionWrapper.style.display = '';
                        }
                    }
                    updateUrlImagePreviews();
                }
            };

            window.toggleVideoMethod = function(method) {
                var uploadSection = document.getElementById('video-upload-section');
                var urlSection = document.getElementById('video-url-section');
                var existingVideoInfo = document.getElementById('existingVideoInfo');
                if (method === 'url') {
                    uploadSection.classList.add('hidden');
                    uploadSection.style.display = 'none';
                    urlSection.classList.remove('hidden');
                    urlSection.style.display = '';
                    // Clear upload data when switching to URL method
                    var videoInput = document.getElementById('videoInput');
                    if (videoInput) videoInput.value = '';
                    var videoFilePreview = document.getElementById('videoFilePreview');
                    if (videoFilePreview) videoFilePreview.classList.add('hidden');
                } else {
                    uploadSection.classList.remove('hidden');
                    uploadSection.style.display = '';
                    urlSection.classList.add('hidden');
                    urlSection.style.display = 'none';
                    // Clear URL data when switching to upload method
                    var videoUrlInput = document.querySelector('input[name="video_url"]');
                    if (videoUrlInput) videoUrlInput.value = '';
                    var urlPreviewPlaceholder = urlSection.querySelector('.url-preview-placeholder');
                    if (urlPreviewPlaceholder) urlPreviewPlaceholder.innerHTML =
                        '<span class="text-xs text-gray-400">' + __t.preview + '</span>';
                    // Show existing video info when switching to upload method
                    if (existingVideoInfo) {
                        existingVideoInfo.removeAttribute('hidden');
                        existingVideoInfo.style.display = '';
                    }
                }
            };

            window.previewVideoUrl = function(input) {
                var preview = input.closest('#video-url-section').querySelector('.url-preview-placeholder');
                var url = input.value.trim();

                if (!url) {
                    preview.innerHTML = '<span class="text-xs text-gray-400">' + __t.preview + '</span>';
                    return;
                }

                var youtubeId = getYouTubeId(url);
                if (youtubeId) {
                    preview.innerHTML = '<img src="https://img.youtube.com/vi/' + youtubeId +
                        '/1.jpg" class="w-full h-full object-cover rounded-lg">';
                } else if (url.endsWith('.mp4') || url.endsWith('.webm') || url.endsWith('.ogg')) {
                    preview.innerHTML = '<video src="' + url +
                        '" class="w-full h-full object-cover rounded-lg"></video>';
                } else if (url.includes('drive.google.com')) {
                    var gdThumb = convertGoogleDriveUrl(url);
                    if (gdThumb) {
                        preview.innerHTML = '<img src="' + gdThumb +
                            '" class="w-full h-full object-cover rounded-lg" onerror="this.parentElement.innerHTML=\'<div class=\\\'flex flex-col items-center justify-center w-full h-full\\\'><svg class=\\\'w-5 h-5 text-blue-500\\\' fill=\\\'none\\\' stroke=\\\'currentColor\\\' viewBox=\\\'0 0 24 24\\\'><path stroke-linecap=\\\'round\\\' stroke-linejoin=\\\'round\\\' stroke-width=\\\'2\\\' d=\\\'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z\\\'/></svg><span class=\\\'text-xs text-blue-500 mt-1\\\'>' +
                            __t.google_drive + '</span></div>\';">';
                    } else {
                        preview.innerHTML =
                            '<div class="flex flex-col items-center justify-center w-full h-full">' +
                            '<svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>' +
                            '<span class="text-xs text-blue-500 mt-1">' + __t.google_drive + '</span></div>';
                    }
                } else if (url.startsWith('http://') || url.startsWith('https://')) {
                    preview.innerHTML = '<div class="flex flex-col items-center justify-center w-full h-full">' +
                        '<svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>' +
                        '<span class="text-xs text-gray-500 mt-1">' + __t.video_url + '</span></div>';
                } else {
                    preview.innerHTML = '<span class="text-xs text-gray-400">' + __t.preview + '</span>';
                }
            };

            // Restore video URL preview on page load
            document.addEventListener('DOMContentLoaded', function() {
                var videoUrlInput = document.querySelector('input[name="video_url"]');
                if (videoUrlInput && videoUrlInput.value.trim()) {
                    previewVideoUrl(videoUrlInput);
                }
            });

            window.previewVideoFile = function(input) {
                var previewArea = document.getElementById('videoFilePreview');
                var player = document.getElementById('video-preview-player');
                var fileName = document.getElementById('video-file-name');
                var file = input.files[0];

                if (file) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        player.src = e.target.result;
                        fileName.textContent = file.name;
                        previewArea.classList.remove('hidden');
                    };
                    reader.readAsDataURL(file);
                }
            };

            window.previewImageUrl = function(input) {
                updateUrlImagePreviews();
            };

            window.removeImageUrlEntry = function(btn) {
                var entry = btn.closest('.image-url-entry');
                if (!entry) return;

                // Only count NEW image URL entries (not existing ones)
                var newEntries = document.querySelectorAll(
                    '#image-url-list .image-url-entry:not([data-is-existing="1"])');
                // Determine the domIdx of the entry being removed
                var inpOfRemoved = entry.querySelector('input[name^="new_image_urls"]');
                var removedDomIdx = inpOfRemoved ? parseInt(inpOfRemoved.getAttribute('data-index')) : -1;

                if (newEntries.length > 1) {
                    entry.remove();
                    // Reindex data-index on remaining NEW entries only
                    var remaining = document.querySelectorAll(
                        '#image-url-list .image-url-entry:not([data-is-existing="1"])');
                    remaining.forEach(function(ent, i) {
                        var inp = ent.querySelector('input[name^="new_image_urls"]');
                        if (inp) inp.setAttribute('data-index', i);
                    });
                    // Remove and reindex caption rows for this URL
                    var uploadCaptionRows = document.getElementById('infoPopupUploadRows');
                    if (uploadCaptionRows) {
                        var removedCaption = uploadCaptionRows.querySelector('[data-url-slot-index="' +
                            removedDomIdx + '"]');
                        if (removedCaption) removedCaption.remove();
                        // Reindex remaining caption rows
                        uploadCaptionRows.querySelectorAll('[data-url-slot-index]').forEach(function(container) {
                            var currentSlot = parseInt(container.getAttribute('data-url-slot-index'));
                            if (currentSlot > removedDomIdx) {
                                container.setAttribute('data-url-slot-index', currentSlot - 1);
                            }
                        });
                    }
                    // Delete caption tracker for this URL
                    if (removedDomIdx >= 0) {
                        delete urlImageCaptionTracker[removedDomIdx];
                    }
                } else {
                    var input = entry.querySelector('input[name^="new_image_urls"]');
                    if (input) {
                        input.value = '';
                        input.setAttribute('data-caption', '');
                    }
                    // Remove caption row if only one entry
                    var uploadCaptionRows = document.getElementById('infoPopupUploadRows');
                    if (uploadCaptionRows && removedDomIdx >= 0) {
                        var captionRow = uploadCaptionRows.querySelector('[data-url-slot-index="' + removedDomIdx +
                            '"]');
                        if (captionRow) captionRow.remove();
                        delete urlImageCaptionTracker[removedDomIdx];
                    }
                }
                updateUrlImagePreviews();
            };

            // Hero: hanya boleh 1 gambar total (upload + URL)
            window.isHeroSingleImageMode = function() {
                var typeInput = document.getElementById('slide_type_input');
                if (!typeInput || typeInput.value !== 'hero') return false;

                // Count existing (saved) images
                var existingCount = 0;
                var existingInputs = document.querySelectorAll(
                    '#existingImagesArea input[name^="existing_images["]:not([disabled])');
                existingInputs.forEach(function(inp) {
                    existingCount++;
                });

                // Count newly uploaded files
                var newUploadCount = (typeof selectedNewImageFiles !== 'undefined') ? selectedNewImageFiles.length :
                    0;

                // Count filled URL entries (new ones)
                var urlEntries = document.querySelectorAll('#image-url-list .image-url-entry');
                var urlCount = 0;
                urlEntries.forEach(function(entry) {
                    var input = entry.querySelector('input[name^="new_image_urls"]');
                    if (input && input.value.trim() !== '') urlCount++;
                });

                return (existingCount + newUploadCount + urlCount) >= 1;
            };

            window.addImageUrlEntry = function() {
                // Hero: hanya 1 gambar
                if (typeof isHeroSingleImageMode === 'function' && isHeroSingleImageMode()) {
                    var warning = document.getElementById('heroImageLimitWarning');
                    if (warning) {
                        warning.textContent = '{{ __('cms.virtual_slideshow.hero_limit_warning') }}';
                        warning.style.display = 'block';
                    }
                    return;
                }
                var list = document.getElementById('image-url-list');
                var newEntries = list.querySelectorAll('.image-url-entry:not([data-is-existing="1"])');
                var newIndex = newEntries.length;

                var entry = document.createElement('div');
                entry.className = 'image-url-entry flex gap-2 items-start';
                entry.setAttribute('data-index', newIndex);
                entry.innerHTML =
                    '<a href="#" target="_blank" class="url-link-btn px-2 py-2 text-blue-600 hover:bg-blue-50 rounded-lg flex-shrink-0 opacity-30 cursor-not-allowed" title="{{ __('cms.virtual_slideshow.open_link') }}" onclick="return false;">' +
                    '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg></a>' +
                    '<input type="text" name="new_image_urls[' + newIndex + ']" class="form-input flex-1" ' +
                    'placeholder="' + __t.image_url_placeholder + '" data-index="' + newIndex +
                    '" oninput="updateUrlLink(this)">' +
                    '<button type="button" onclick="removeImageUrlEntry(this)" class="px-2 py-2 text-red-500 hover:bg-red-50 rounded-lg flex-shrink-0" title="' +
                    __t.delete + '">' +
                    '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>';
                list.appendChild(entry);

                // Also add the corresponding caption field
                updateUrlImagePreviews();
            };

            window.updateUrlLink = function(input) {
                var entry = input.closest('.image-url-entry');
                var linkBtn = entry.querySelector('.url-link-btn');
                var url = input.value.trim();

                if (url && (url.startsWith('http://') || url.startsWith('https://'))) {
                    linkBtn.href = url;
                    linkBtn.classList.remove('opacity-30', 'cursor-not-allowed');
                    linkBtn.onclick = null;
                } else {
                    linkBtn.href = '#';
                    linkBtn.classList.add('opacity-30', 'cursor-not-allowed');
                    linkBtn.onclick = function() {
                        return false;
                    };
                }

                // If editing an existing URL image, sync the "existing images" preview in real-time.
                syncExistingUrlPreview(input);
                updateUrlImagePreviews();
            };

            function syncExistingUrlPreview(input) {
                if (!input || !input.name || input.name.indexOf('existing_image_urls[') !== 0) return;

                var idx = parseInt(input.getAttribute('data-index'), 10);
                if (isNaN(idx)) {
                    var m = input.name.match(/existing_image_urls\[(\d+)\]/);
                    if (m) idx = parseInt(m[1], 10);
                }
                if (isNaN(idx)) return;

                var wrap = document.getElementById('existing-url-wrap-' + idx);
                if (!wrap) return;

                var url = input.value.trim();
                var previewImg = wrap.querySelector('img');
                var fallbackBox = previewImg ? previewImg.nextElementSibling : null;
                var viewLink = wrap.querySelector('a[target="_blank"]');

                if (viewLink) {
                    viewLink.href = url || '#';
                }

                if (!previewImg) return;

                if (!url) {
                    previewImg.removeAttribute('src');
                    previewImg.style.display = 'none';
                    if (fallbackBox) fallbackBox.style.display = 'flex';
                    return;
                }

                previewImg.src = convertImageUrl(url);
                previewImg.style.display = '';
                if (fallbackBox) fallbackBox.style.display = 'none';
            }

            // Caption tracker for new uploaded images to persist values across re-renders
            var newUploadImageCaptionTracker = {};
            var urlImageCaptionTracker = {};

            function extractWidgetState(widgetContainer) {
                if (!widgetContainer) return '';
                var modeSelect = widgetContainer.querySelector('select[name^="info_popup_mode_"]');
                if (!modeSelect) return '';
                if (modeSelect.value === 'single') {
                    var singleInput = widgetContainer.querySelector('.caption-single-section textarea');
                    return singleInput ? singleInput.value : '';
                } else {
                    var items = [];
                    var pairs = widgetContainer.querySelectorAll('.caption-qa-pair');
                    pairs.forEach(function(pair) {
                        var qInput = pair.querySelector('input[type="text"]');
                        var aTextarea = pair.querySelector('textarea');
                        items.push({
                            question: qInput ? qInput.value : '',
                            answer: aTextarea ? aTextarea.value : ''
                        });
                    });
                    if (items.length === 0) items.push({
                        question: '',
                        answer: ''
                    });
                    return {
                        type: 'multi',
                        items: items
                    };
                }
            }

            function convertImageUrl(url) {
                // Google Drive
                var match = url.match(/\/file\/d\/([a-zA-Z0-9_-]+)/);
                if (match) return 'https://lh3.googleusercontent.com/d/' + match[1];
                match = url.match(/[?&]id=([a-zA-Z0-9_-]+)/);
                if (match) return 'https://lh3.googleusercontent.com/d/' + match[1];
                // Wikimedia Commons: /wiki/File:NAME → Special:FilePath/NAME
                match = url.match(/commons\.wikimedia\.org\/wiki\/File:(.+)/);
                if (match) return 'https://commons.wikimedia.org/wiki/Special:FilePath/' + match[1];
                return url;
            }

            // Unified preview function for URL images and uploads
            window.updateUrlImagePreviews = function() {
                var uploadPreviewArea = document.getElementById('newImagePreviewArea');
                var urlPreviewArea = document.getElementById('newUrlPreviewArea');
                var uploadCaptionArea = document.getElementById('infoPopupUploadImageArea');
                var uploadCaptionRows = document.getElementById('infoPopupUploadRows');
                var urlCaptionArea = document.getElementById('infoPopupUrlImageArea');

                // Hide the old url areas completely
                if (urlPreviewArea) {
                    urlPreviewArea.classList.add('hidden');
                    urlPreviewArea.style.display = 'none';
                }
                if (urlCaptionArea) {
                    urlCaptionArea.classList.add('hidden');
                    urlCaptionArea.style.display = 'none';
                }

                // Save URL image captions before clearing
                uploadCaptionRows.querySelectorAll('[data-url-slot-index]').forEach(function(container) {
                    var slotIdx = parseInt(container.getAttribute('data-url-slot-index'));
                    if (!isNaN(slotIdx)) {
                        var backendIdx = parseInt(container.getAttribute('data-backend-idx'));
                        var state = extractWidgetState(container);
                        var stateStr = typeof state === 'object' ? JSON.stringify(state) : state;
                        urlImageCaptionTracker[slotIdx] = state;
                        // Find the URL input whose data-index matches slotIdx
                        var allUrlInputs = document.querySelectorAll(
                            '#image-url-list input[name^="new_image_urls"]');
                        for (var i = 0; i < allUrlInputs.length; i++) {
                            if (parseInt(allUrlInputs[i].getAttribute('data-index')) === slotIdx) {
                                allUrlInputs[i].setAttribute('data-caption', stateStr);
                                break;
                            }
                        }
                    }
                });

                // Save new upload caption values before clearing
                uploadCaptionRows.querySelectorAll('[data-upload-slot-index]').forEach(function(container) {
                    var slotIdx = parseInt(container.getAttribute('data-upload-slot-index'));
                    if (!isNaN(slotIdx)) {
                        newUploadImageCaptionTracker[slotIdx] = extractWidgetState(container);
                    }
                });

                window.mediaTimeline = window.mediaTimeline || [];
                var activeItems = [];
                var numValidUrls = 0;

                // Get all URL inputs
                var urlInputs = document.querySelectorAll('#image-url-list input[name^="new_image_urls"]');
                var existingUploadedCount = getRemainingExistingCount();
                var uploadedCount = selectedNewImageFiles.length;

                urlInputs.forEach(function(input, idx) {
                    var url = input.value.trim();
                    var entry = input.closest('.image-url-entry');
                    var linkBtn = entry ? entry.querySelector('.url-link-btn') : null;
                    var caption = input.getAttribute('data-caption') || '';

                    var uid = input.getAttribute('data-uid');
                    if (!uid && url) { // Only timestamp when user actually enters a URL
                        uid = 'url_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
                        input.setAttribute('data-uid', uid);
                        window.mediaTimeline.push({
                            type: 'url',
                            uid: uid,
                            timestamp: Date.now()
                        });
                    }

                    if (url && (url.startsWith('http://') || url.startsWith('https://'))) {
                        if (linkBtn) {
                            linkBtn.href = url;
                            linkBtn.classList.remove('opacity-30', 'cursor-not-allowed');
                            linkBtn.onclick = null;
                        }
                        var timelineEntry = window.mediaTimeline.find(function(t) {
                            return t.uid === uid;
                        });
                        activeItems.push({
                            type: 'url',
                            url: convertImageUrl(url),
                            originalUrl: url,
                            uid: uid,
                            domIdx: idx, // M index
                            backendIdx: existingUploadedCount + uploadedCount +
                                numValidUrls, // E + N + M
                            caption: caption,
                            timestamp: timelineEntry ? timelineEntry.timestamp : Date.now()
                        });
                        numValidUrls++;
                    } else {
                        if (linkBtn) {
                            linkBtn.href = '#';
                            linkBtn.classList.add('opacity-30', 'cursor-not-allowed');
                            linkBtn.onclick = function() {
                                return false;
                            };
                        }
                    }
                });

                selectedNewImageFiles.forEach(function(file, idx) {
                    var uid = file._uid;
                    if (!uid) { // default if missing
                        uid = 'upload_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
                        file._uid = uid;
                        window.mediaTimeline.push({
                            type: 'upload',
                            uid: uid,
                            timestamp: Date.now()
                        });
                    }
                    var timelineEntry = window.mediaTimeline.find(function(t) {
                        return t.uid === uid;
                    });
                    activeItems.push({
                        type: 'upload',
                        file: file,
                        uid: uid,
                        domIdx: idx, // N index
                        backendIdx: idx, // Prefix covers the shift
                        timestamp: timelineEntry ? timelineEntry.timestamp : Date.now()
                    });
                });

                // Clear unified areas
                uploadPreviewArea.innerHTML = '';
                uploadCaptionRows.innerHTML = '';

                var totalImages = activeItems.length;

                if (totalImages > 0) {
                    uploadPreviewArea.classList.remove('hidden');
                    uploadPreviewArea.style.display = 'flex';
                } else {
                    uploadPreviewArea.classList.add('hidden');
                    uploadPreviewArea.style.display = 'none';
                }

                var typeInput = document.getElementById('slide_type_input');
                var isHeroType = typeInput && typeInput.value === 'hero';
                if (totalImages > 0 && !isHeroType) {
                    uploadCaptionArea.classList.remove('hidden');
                    uploadCaptionArea.style.display = 'block';
                } else {
                    uploadCaptionArea.classList.add('hidden');
                    uploadCaptionArea.style.display = 'none';
                }

                // Sort purely chronologically by timestamp (ascending) so visual order matches input order
                activeItems.sort(function(a, b) {
                    return a.timestamp - b.timestamp;
                });

                var renderIdx = 0;
                activeItems.forEach(function(item) {
                    var displayPosition = renderIdx + 1;
                    renderIdx++;

                    if (item.type === 'upload') {
                        // Render upload preview synchronously to preserve order
                        var wrap = document.createElement('div');
                        wrap.className = 'img-preview-wrap';
                        wrap.innerHTML =
                            '<div style="height:60px;width:60px;background:#f3f4f6;border-radius:8px;border:1px solid #e5e7eb;display:flex;align-items:center;justify-content:center;"><span class="text-xs text-gray-400">...</span></div>';
                        uploadPreviewArea.appendChild(wrap);

                        var reader = new FileReader();
                        (function(idx, fileReader, container) {
                            fileReader.onload = function(e) {
                                container.innerHTML = '<img src="' + e.target.result +
                                    '" alt="" style="height:60px;width:60px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb;">' +
                                    '<button type="button" class="remove-img" onclick="removePreviewImage(' +
                                    idx + ')">✕</button>';
                            };
                        })(item.domIdx, reader, wrap);
                        reader.readAsDataURL(item.file);

                        // Render upload caption — reuse existing widget if already in DOM to preserve user input
                        var existingUploadContainer = uploadCaptionRows.querySelector(
                            '[data-upload-slot-index="' + item.domIdx + '"]');
                        var savedCaption = newUploadImageCaptionTracker[item.domIdx] || '';
                        var row = document.createElement('div');
                        row.className = 'info-popup-row';
                        var labelEl = document.createElement('label');
                        labelEl.className = 'form-label';
                        labelEl.style.marginBottom = '4px';
                        labelEl.textContent = __t.popup_existing_images + ' ' + displayPosition;
                        row.appendChild(labelEl);

                        if (existingUploadContainer && existingUploadContainer.querySelector(
                                '.caption-widget-mode')) {
                            // Widget already exists — only recreate if caption state differs
                            var currentState = extractWidgetState(existingUploadContainer);
                            var currentStr = typeof currentState === 'object' ? JSON.stringify(
                                currentState) : currentState;
                            var newStr = typeof savedCaption === 'object' ? JSON.stringify(savedCaption) : (
                                savedCaption || '');
                            if (currentStr === newStr) {
                                row.appendChild(existingUploadContainer);
                                uploadCaptionRows.appendChild(row);
                            } else {
                                var widgetContainer = document.createElement('div');
                                widgetContainer.setAttribute('data-upload-slot-index', item.domIdx);
                                createCaptionWidget(widgetContainer, 'info_popup_new_images', item
                                    .backendIdx, savedCaption, {
                                        singlePlaceholder: __t.popup_existing_images + ' ' +
                                            displayPosition + '...',
                                        isArray: true
                                    });
                                row.appendChild(widgetContainer);
                                uploadCaptionRows.appendChild(row);
                            }
                        } else {
                            var widgetContainer = document.createElement('div');
                            widgetContainer.setAttribute('data-upload-slot-index', item.domIdx);
                            createCaptionWidget(widgetContainer, 'info_popup_new_images', item.backendIdx,
                                savedCaption, {
                                    singlePlaceholder: __t.popup_existing_images + ' ' +
                                        displayPosition + '...',
                                    isArray: true
                                });
                            row.appendChild(widgetContainer);
                            uploadCaptionRows.appendChild(row);
                        }

                    } else if (item.type === 'url') {
                        // Render URL preview
                        var wrap = document.createElement('div');
                        wrap.className = 'img-preview-wrap';
                        var isGoogleDrive = item.originalUrl.includes('drive.google.com');

                        wrap.innerHTML = '<img src="' + item.url +
                            '" alt="" style="height:60px;width:60px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb;" onerror="this.style.display=\'none\'; this.nextElementSibling.style.display=\'flex\';">' +
                            '<div class="flex flex-col items-center justify-center" style="height:60px;width:60px;background:#f3f4f6;border-radius:8px;border:1px solid #e5e7eb;display:none;">' +
                            '<svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>' +
                            '<a href="' + item.originalUrl +
                            '" target="_blank" class="text-xs text-blue-500 hover:text-blue-700 mt-1">' +
                            __t.view + '</a></div>' +
                            '<button type="button" class="remove-img" onclick="removeUrlImage(' + item
                            .domIdx + ')">✕</button>';
                        uploadPreviewArea.appendChild(wrap);

                        // Render URL caption — reuse existing widget if already in DOM to preserve user input
                        var existingContainer = uploadCaptionRows.querySelector('[data-url-slot-index="' +
                            item.domIdx + '"]');
                        var savedCaption = urlImageCaptionTracker[item.domIdx] || '';
                        var captionValue = item.caption || savedCaption || '';
                        var captionData = captionValue;
                        if (typeof captionValue === 'string' && captionValue.charAt(0) === '{') {
                            try {
                                captionData = JSON.parse(captionValue);
                            } catch (e) {}
                        }

                        var row = document.createElement('div');
                        row.className = 'info-popup-row';
                        var labelEl = document.createElement('label');
                        labelEl.className = 'form-label';
                        labelEl.style.marginBottom = '4px';
                        labelEl.textContent = __t.popup_url_images + ' ' + displayPosition;
                        row.appendChild(labelEl);

                        if (existingContainer && existingContainer.querySelector('.caption-widget-mode')) {
                            // Widget already exists — only recreate if caption state differs
                            var currentState = extractWidgetState(existingContainer);
                            var currentStr = typeof currentState === 'object' ? JSON.stringify(
                                currentState) : currentState;
                            var newStr = typeof captionData === 'object' ? JSON.stringify(captionData) : (
                                captionData || '');
                            if (currentStr === newStr) {
                                // State unchanged — preserve existing DOM (preserves user input)
                                row.appendChild(existingContainer);
                                uploadCaptionRows.appendChild(row);
                            } else {
                                // State changed — recreate with new data
                                var widgetContainer = document.createElement('div');
                                widgetContainer.setAttribute('data-url-slot-index', item.domIdx);
                                widgetContainer.setAttribute('data-backend-idx', item.backendIdx);
                                createCaptionWidget(widgetContainer, 'info_popup_images', item.backendIdx,
                                    captionData, {
                                        singlePlaceholder: __t.popup_url_images + ' ' +
                                            displayPosition + '...',
                                        isArray: true
                                    });
                                row.appendChild(widgetContainer);
                                uploadCaptionRows.appendChild(row);
                            }
                        } else {
                            // No existing widget — create new one
                            var widgetContainer = document.createElement('div');
                            widgetContainer.setAttribute('data-url-slot-index', item.domIdx);
                            widgetContainer.setAttribute('data-backend-idx', item.backendIdx);
                            createCaptionWidget(widgetContainer, 'info_popup_images', item.backendIdx,
                                captionData, {
                                    singlePlaceholder: __t.popup_url_images + ' ' + displayPosition +
                                        '...',
                                    isArray: true
                                });
                            row.appendChild(widgetContainer);
                            uploadCaptionRows.appendChild(row);
                        }
                    }
                });

                updateUnifiedImageOrderInput(activeItems);
            }

            function updateUnifiedImageOrderInput(activeItems) {
                var input = document.getElementById('unifiedImageOrderInput');
                if (!input) return;

                var serializable = [];
                var orderCounter = 0;

                // Iterate through existingImagesArea children in DOM order to preserve unified_image_order
                var existingArea = document.getElementById('existingImagesArea');
                if (existingArea) {
                    var children = existingArea.children;
                    for (var i = 0; i < children.length; i++) {
                        var wrap = children[i];
                        if (wrap.classList.contains('existing-img-wrap')) {
                            var inp = wrap.querySelector('input[name^="existing_images["]:not([disabled])');
                            if (inp) {
                                var idx = parseInt(wrap.getAttribute('data-original-index'));
                                serializable.push({
                                    type: 'existing',
                                    existingIndex: idx,
                                    order: orderCounter++
                                });
                            }
                        } else if (wrap.classList.contains('existing-url-img-wrap')) {
                            // Check if not deleted (opacity indicates deleted)
                            if (wrap.style.opacity !== '0.3' && wrap.style.opacity !== '0.5') {
                                var idx = parseInt(wrap.getAttribute('data-url-original-index'));
                                serializable.push({
                                    type: 'existingUrl',
                                    existingUrlIndex: idx,
                                    order: orderCounter++
                                });
                            }
                        }
                    }
                }

                // Then append the active items (new uploads and URLs)
                activeItems.forEach(function(item) {
                    if (item.type === 'url') {
                        serializable.push({
                            type: 'url',
                            urlIndex: item.domIdx,
                            urlValue: item.originalUrl,
                            order: item.timestamp
                        });
                    } else if (item.type === 'upload') {
                        serializable.push({
                            type: 'newUpload',
                            newUploadIndex: item.domIdx,
                            order: item.timestamp
                        });
                    }
                });
                input.value = JSON.stringify(serializable);
            }

            window.removeUrlImage = function(domIdx) {
                // Only look at NEW image URL entries
                var newEntries = document.querySelectorAll(
                    '#image-url-list .image-url-entry:not([data-is-existing="1"])');
                var targetEntry = null;
                newEntries.forEach(function(ent) {
                    var inp = ent.querySelector('input[name^="new_image_urls"]');
                    if (inp && parseInt(inp.getAttribute('data-index')) === domIdx) {
                        targetEntry = ent;
                    }
                });
                if (!targetEntry) return;

                // Delete the tracker entry (keyed by domIdx) before removing
                delete urlImageCaptionTracker[domIdx];

                if (newEntries.length > 1) {
                    targetEntry.remove();
                    // Reindex data-index on remaining NEW entries
                    var remaining = document.querySelectorAll(
                        '#image-url-list .image-url-entry:not([data-is-existing="1"])');
                    remaining.forEach(function(ent, i) {
                        var inp = ent.querySelector('input[name^="new_image_urls"]');
                        if (inp) inp.setAttribute('data-index', i);
                    });
                    // Remove caption row and reindex remaining
                    var uploadCaptionRows = document.getElementById('infoPopupUploadRows');
                    if (uploadCaptionRows) {
                        var removedCaption = uploadCaptionRows.querySelector('[data-url-slot-index="' + domIdx +
                            '"]');
                        if (removedCaption) removedCaption.remove();
                        uploadCaptionRows.querySelectorAll('[data-url-slot-index]').forEach(function(container) {
                            var currentSlot = parseInt(container.getAttribute('data-url-slot-index'));
                            if (currentSlot > domIdx) {
                                container.setAttribute('data-url-slot-index', currentSlot - 1);
                            }
                        });
                    }
                } else {
                    var inputs = document.querySelectorAll('#image-url-list input[name^="new_image_urls"]');
                    if (inputs[0]) {
                        inputs[0].value = '';
                        inputs[0].setAttribute('data-caption', '');
                    }
                    // Also clear caption when single URL is cleared
                    var uploadCaptionRows = document.getElementById('infoPopupUploadRows');
                    if (uploadCaptionRows) {
                        var captionRow = uploadCaptionRows.querySelector('[data-url-slot-index="' + domIdx + '"]');
                        if (captionRow) captionRow.remove();
                    }
                }
                updateUrlImagePreviews();
            };

            var selectedNewImageFiles = [];

            window.previewNewImages = function(input) {
                var files = Array.from(input.files);
                var existingCount = {{ $slide->images ? count($slide->images) : 0 }};

                if (files.length === 0) return;

                // Hero: enforce 1 file max
                var typeInput = document.getElementById('slide_type_input');
                if (typeInput && typeInput.value === 'hero') {
                    if (typeof isHeroSingleImageMode === 'function' && isHeroSingleImageMode()) {
                        var warning = document.getElementById('heroImageLimitWarning');
                        if (warning) {
                            warning.textContent = '{{ __('cms.virtual_slideshow.hero_limit_warning') }}';
                            warning.style.display = 'block';
                        }
                        input.value = '';
                        return;
                    }
                    if (files.length > 1) {
                        files = [files[0]];
                        var warning = document.getElementById('heroImageLimitWarning');
                        if (warning) {
                            warning.textContent = '{{ __('cms.virtual_slideshow.hero_single_image') }}';
                            warning.style.display = 'block';
                        }
                    }
                }

                files.forEach(function(file) {
                    selectedNewImageFiles.push(file);
                });

                renderNewImagePreviews();

                // Reset file input so same file can be uploaded again
                input.value = '';
            };

            function getRemainingExistingCount() {
                var existingInputs = document.querySelectorAll(
                    '#existingImagesArea input[name^="existing_images["]:not([disabled])');
                return existingInputs.length;
            }

            function renderNewImagePreviews() {
                updateUrlImagePreviews();
            }

            window.removePreviewImage = function(idx) {
                // Remove caption row from DOM explicitly
                var uploadCaptionRows = document.getElementById('infoPopupUploadRows');
                if (uploadCaptionRows) {
                    var removedCaption = uploadCaptionRows.querySelector('[data-upload-slot-index="' + idx + '"]');
                    if (removedCaption) removedCaption.remove();
                    // Reindex remaining caption rows
                    uploadCaptionRows.querySelectorAll('[data-upload-slot-index]').forEach(function(container) {
                        var currentSlot = parseInt(container.getAttribute('data-upload-slot-index'));
                        if (currentSlot > idx) {
                            container.setAttribute('data-upload-slot-index', currentSlot - 1);
                        }
                    });
                }

                // Remove caption and shift remaining indices down
                delete newUploadImageCaptionTracker[idx];
                var newTracker = {};
                Object.keys(newUploadImageCaptionTracker).forEach(function(key) {
                    var k = parseInt(key);
                    if (k > idx) {
                        newTracker[k - 1] = newUploadImageCaptionTracker[k];
                    } else {
                        newTracker[k] = newUploadImageCaptionTracker[k];
                    }
                });
                newUploadImageCaptionTracker = newTracker;

                selectedNewImageFiles.splice(idx, 1);
                renderNewImagePreviews();
            };

            window.removeExisting = function(idx) {
                var wrap = document.getElementById('existing-wrap-' + idx);
                var input = document.getElementById('existing-input-' + idx);
                var popupRow = document.getElementById('existing-popup-row-' + idx);
                var deletedInput = document.getElementById('deleted-input-' + idx);

                if (wrap) {
                    wrap.style.opacity = '0.5';
                    wrap.style.pointerEvents = 'none';
                }
                if (input) {
                    input.disabled = true;
                    input.name = 'deleted_existing_images[' + idx + ']';
                }
                if (popupRow) {
                    popupRow.style.opacity = '0.5';
                    popupRow.style.pointerEvents = 'none';
                    // Disable ALL form fields in caption widget (select, input, textarea)
                    var selects = popupRow.querySelectorAll('select');
                    selects.forEach(function(sel) {
                        sel.disabled = true;
                    });
                    var inputs = popupRow.querySelectorAll('input, textarea');
                    inputs.forEach(function(field) {
                        field.disabled = true;
                    });
                }
                if (deletedInput) {
                    deletedInput.value = '1';
                    deletedInput.name = 'deleted_existing_images_marked[' + idx + ']';
                }

                // Re-render new image previews with updated count
                renderNewImagePreviews();
            };

            window.removeExistingUrl = function(idx) {
                var wrap = document.getElementById('existing-url-wrap-' + idx);
                var popupRow = document.getElementById('existing-url-popup-row-' + idx);
                var duplicateEntry = document.querySelector(
                    '#image-url-list .image-url-entry[data-is-existing="1"][data-index="' + idx + '"]');

                if (wrap) {
                    wrap.style.opacity = '0.5';
                    wrap.style.pointerEvents = 'none';
                }

                if (popupRow) {
                    popupRow.style.opacity = '0.5';
                    popupRow.style.pointerEvents = 'none';
                    // Disable ALL form fields in caption widget (select, input, textarea)
                    var selects = popupRow.querySelectorAll('select');
                    selects.forEach(function(sel) {
                        sel.disabled = true;
                    });
                    var inputs = popupRow.querySelectorAll('input, textarea');
                    inputs.forEach(function(field) {
                        field.disabled = true;
                    });
                }

                if (duplicateEntry) {
                    var dupInput = duplicateEntry.querySelector('input');
                    if (dupInput) {
                        dupInput.disabled = true;
                        // rename so backend can pick it up safely if needed, or omit it from image_urls
                        dupInput.name = 'deleted_existing_image_urls[' + idx + ']';
                    }
                    duplicateEntry.style.opacity = '0.5';
                    duplicateEntry.style.pointerEvents = 'none';
                }

                // Make sure to sync order
                renderNewImagePreviews();
            };

            window.toggleCarouselVideoMethod = function(method) {
                var urlSection = document.getElementById('carousel-video-url-section');
                var uploadSection = document.getElementById('carousel-video-upload-section');
                if (method === 'url') {
                    urlSection.classList.remove('hidden');
                    uploadSection.classList.add('hidden');
                } else {
                    urlSection.classList.add('hidden');
                    uploadSection.classList.remove('hidden');
                }
            };

            window.addCarouselVideoUrlEntry = function() {
                var list = document.getElementById('carousel-video-url-list');
                var entries = list.querySelectorAll('.carousel-video-url-entry');
                var newIndex = entries.length;

                var entry = document.createElement('div');
                entry.className = 'carousel-video-url-entry flex gap-2 items-start';
                entry.setAttribute('data-index', newIndex);
                entry.innerHTML =
                    '<input type="text" name="carousel_video_urls[]" class="form-input flex-1" ' +
                    'placeholder="' + __t.carousel_video_url_placeholder + '" data-index="' + newIndex +
                    '" data-caption="" oninput="updateCarouselUrlCaption(this)">' +
                    '<button type="button" onclick="removeCarouselVideoUrlEntry(this)" class="px-2 py-2 text-red-500 hover:bg-red-50 rounded-lg flex-shrink-0" title="' +
                    __t.delete + '">' +
                    '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>';
                list.appendChild(entry);

                // Add to allVideoEntries for tracking
                allVideoEntries.push({
                    type: 'url',
                    data: '',
                    domIndex: newIndex,
                    caption: ''
                });
            };

            window.updateCarouselUrlCaption = function(input) {
                var domIndex = parseInt(input.getAttribute('data-index'));
                // Update URL data only — do NOT set caption from input value
                allVideoEntries.forEach(function(entry) {
                    if (entry.type === 'url' && entry.domIndex === domIndex) {
                        entry.data = input.value;
                        // Caption is managed separately via urlCaptionTracker, not from URL input
                    }
                });
                updateCarouselVideoPreviews();
            };

            window.removeCarouselVideoUrlEntry = function(btn) {
                var entry = btn.closest('.carousel-video-url-entry');
                var domIndex = entry ? parseInt(entry.querySelector('input[name="carousel_video_urls[]"]')
                    .getAttribute('data-index')) : -1;

                var entries = document.querySelectorAll('.carousel-video-url-entry');
                if (entries.length > 1) {
                    entry.remove();
                } else {
                    var inputs = document.querySelectorAll(
                        '#carousel-video-url-list input[name="carousel_video_urls[]"]');
                    if (inputs[0]) {
                        inputs[0].value = '';
                        inputs[0].setAttribute('data-caption', '');
                    }
                }

                // Clear caption from tracker and allVideoEntries
                if (domIndex >= 0) {
                    delete urlCaptionTracker[domIndex];
                    allVideoEntries = allVideoEntries.filter(function(e) {
                        return !(e.type === 'url' && e.domIndex === domIndex);
                    });
                }
                updateCarouselVideoPreviews();
            };

            window.previewCarouselVideoUrl = function(input) {
                updateCarouselVideoPreviews();
            };

            function getYouTubeId(url) {
                var regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
                var match = url.match(regExp);
                return (match && match[2].length === 11) ? match[2] : null;
            }

            function convertGoogleDriveUrl(url) {
                var match = url.match(/\/file\/d\/([a-zA-Z0-9_-]+)/);
                if (match) {
                    return 'https://lh3.googleusercontent.com/d/' + match[1];
                }
                match = url.match(/[?&]id=([a-zA-Z0-9_-]+)/);
                if (match) {
                    return 'https://lh3.googleusercontent.com/d/' + match[1];
                }
                return null;
            }

            function updateCarouselVideoPreviews() {
                var previewArea = document.getElementById('carouselVideoUrlPreviewArea');
                var popupRows = document.getElementById('carouselVideoInfoPopupRows');
                var hint = document.getElementById('noCarouselVideosHint');

                // Sync allVideoEntries with current DOM state for URLs
                var urlInputs = document.querySelectorAll(
                    '#carousel-video-url-list input[name="carousel_video_urls[]"]');
                urlInputs.forEach(function(input, idx) {
                    var url = input.value.trim();
                    // Use urlCaptionTracker for caption so it survives re-renders
                    var caption = urlCaptionTracker[idx] || '';

                    // Check if this URL already exists in allVideoEntries
                    var existingEntry = null;
                    allVideoEntries.forEach(function(entry) {
                        if (entry.type === 'url' && entry.domIndex === idx) {
                            existingEntry = entry;
                        }
                    });

                    if (url && (url.startsWith('http://') || url.startsWith('https://'))) {
                        if (existingEntry) {
                            existingEntry.data = url;
                            existingEntry.caption = caption;
                        } else {
                            // New URL added via DOM
                            allVideoEntries.push({
                                type: 'url',
                                data: url,
                                domIndex: idx,
                                caption: caption
                            });
                        }
                    } else {
                        // URL was cleared, remove from entries but preserve caption in tracker
                        if (existingEntry) {
                            allVideoEntries = allVideoEntries.filter(function(e) {
                                return !(e.type === 'url' && e.domIndex === idx);
                            });
                        }
                    }
                });

                // Clean up: remove URL entries that no longer exist in DOM
                allVideoEntries = allVideoEntries.filter(function(entry) {
                    if (entry.type === 'url') {
                        var entries = document.querySelectorAll('.carousel-video-url-entry');
                        return entry.domIndex < entries.length;
                    }
                    return true;
                });

                // Count valid videos
                var validVideos = allVideoEntries.filter(function(entry) {
                    if (entry.type === 'url') {
                        return entry.data && (entry.data.startsWith('http://') || entry.data.startsWith(
                            'https://'));
                    }
                    // Upload is valid if it has data (new upload) or uploadPath (existing upload)
                    return entry.data !== null || entry.uploadPath;
                });

                // Save Carousel Video captions before clearing
                popupRows.querySelectorAll('select[name^="info_popup_mode_carousel_videos"]').forEach(function(select) {
                    var match = select.name.match(/info_popup_mode_carousel_videos\[([^\]]+)\]/);
                    if (match) {
                        var widgetContainer = select.parentElement.parentElement;
                        var state = extractWidgetState(widgetContainer);
                        allVideoEntries.forEach(function(entry) {
                            if (entry.lastCaptionKey === match[1]) {
                                entry.caption = state;
                            }
                        });
                    }
                });

                // Clear preview area
                previewArea.innerHTML = '';

                if (validVideos.length === 0) {
                    if (hint) hint.style.display = '';
                    popupRows.innerHTML = '<p class="text-xs text-gray-400 italic" id="noCarouselVideosHint">' + __t
                        .add_videos_first + '</p>';
                    return;
                }

                if (hint) hint.style.display = 'none';
                popupRows.innerHTML = '';

                // Track sequential indices for caption keys
                var urlRenderIdx = 0;
                var existingUploadIdx = 0;
                var newUploadIdx = 0;

                // Render all videos in order (allVideoEntries is already in chronological order)
                allVideoEntries.forEach(function(video, renderIndex) {
                    // Skip invalid entries
                    if (video.type === 'url' && (!video.data || (!video.data.startsWith('http://') && !video
                            .data.startsWith('https://')))) {
                        return;
                    }
                    if (video.type === 'upload' && !video.data && !video.uploadPath) {
                        return;
                    }

                    var wrap = document.createElement('div');
                    wrap.className = 'img-preview-wrap';
                    var displayPosition = renderIndex + 1;

                    // Generate caption key based on type and sequential index
                    var captionKey;
                    if (video.type === 'url') {
                        captionKey = 'url_' + urlRenderIdx;
                        urlRenderIdx++;
                    } else if (video.uploadPath) {
                        captionKey = 'upload_' + existingUploadIdx;
                        existingUploadIdx++;
                    } else {
                        captionKey = 'newUpload_' + newUploadIdx;
                        newUploadIdx++;
                    }

                    video.lastCaptionKey = captionKey;

                    if (video.type === 'url') {
                        // URL video
                        var youtubeId = getYouTubeId(video.data);

                        if (youtubeId) {
                            wrap.innerHTML = '<img src="https://img.youtube.com/vi/' + youtubeId +
                                '/1.jpg" style="height:60px;width:80px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb;" class="rounded-lg">' +
                                '<button type="button" class="remove-img" onclick="removeUrlVideo(' + video
                                .domIndex + ')">✕</button>';
                        } else if (video.data.endsWith('.mp4') || video.data.endsWith('.webm') || video.data
                            .endsWith('.ogg')) {
                            wrap.innerHTML = '<video src="' + video.data +
                                '" style="height:60px;width:80px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb;"></video>' +
                                '<button type="button" class="remove-img" onclick="removeUrlVideo(' + video
                                .domIndex + ')">✕</button>';
                        } else if (video.data.includes('drive.google.com')) {
                            var gdThumb = convertGoogleDriveUrl(video.data);
                            if (gdThumb) {
                                wrap.innerHTML = '<img src="' + gdThumb +
                                    '" style="height:60px;width:80px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb;" onerror="this.style.display=\'none\';this.nextElementSibling.style.display=\'flex\';">' +
                                    '<div class="w-20 h-16 rounded-lg border border-gray-200 bg-gray-100 flex items-center justify-center" style="display:none;"><svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg></div>' +
                                    '<button type="button" class="remove-img" onclick="removeUrlVideo(' + video
                                    .domIndex + ')">✕</button>';
                            } else {
                                wrap.innerHTML =
                                    '<div class="w-20 h-16 rounded-lg border border-gray-200 bg-gray-100 flex items-center justify-center"><svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg></div>' +
                                    '<button type="button" class="remove-img" onclick="removeUrlVideo(' + video
                                    .domIndex + ')">✕</button>';
                            }
                        } else {
                            wrap.innerHTML =
                                '<div class="w-20 h-16 rounded-lg border border-gray-200 bg-gray-100 flex items-center justify-center"><svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg></div>' +
                                '<button type="button" class="remove-img" onclick="removeUrlVideo(' + video
                                .domIndex + ')">✕</button>';
                        }

                        previewArea.appendChild(wrap);

                        // Add caption widget
                        var row = document.createElement('div');
                        row.className = 'info-popup-row';
                        var label = document.createElement('label');
                        label.className = 'form-label';
                        label.style.marginBottom = '4px';
                        label.textContent = __t.url_video_caption + ' ' + displayPosition;
                        row.appendChild(label);
                        var widgetContainer = document.createElement('div');
                        // Parse caption data for multi mode support
                        var captionData = video.caption || '';
                        if (typeof captionData === 'string' && captionData.charAt(0) === '{') {
                            try {
                                captionData = JSON.parse(captionData);
                            } catch (e) {}
                        }
                        var widget = createCaptionWidget(widgetContainer, 'info_popup_carousel_videos',
                            captionKey, captionData, {
                                singlePlaceholder: __t.single_video_caption + ' ' + displayPosition + '...',
                                isArray: true
                            });
                        widget.singleInput.addEventListener('input', function() {
                            video.caption = this.value;
                            if (video.type === 'url') {
                                urlCaptionTracker[video.domIndex] = this.value;
                            }
                        });
                        row.appendChild(widgetContainer);
                        popupRows.appendChild(row);
                    } else {
                        // Upload video
                        var videoSrc = video.data ? URL.createObjectURL(video.data) : (video.uploadPath ?
                            '{{ asset('storage/') }}/' + video.uploadPath : '');
                        var uploadWrap = document.createElement('div');
                        uploadWrap.className = 'img-preview-wrap';
                        uploadWrap.innerHTML = '<video src="' + videoSrc +
                            '" style="height:60px;width:80px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb;"></video>' +
                            '<button type="button" class="remove-img" onclick="removePreviewVideo(' + video
                            .uploadId + ')">✕</button>';
                        previewArea.appendChild(uploadWrap);

                        // Add caption widget
                        var row = document.createElement('div');
                        row.className = 'info-popup-row';
                        var label = document.createElement('label');
                        label.className = 'form-label';
                        label.style.marginBottom = '4px';
                        label.textContent = __t.single_video_caption + ' ' + displayPosition;
                        row.appendChild(label);
                        var widgetContainer = document.createElement('div');
                        var captionData = video.caption || '';
                        if (typeof captionData === 'string' && captionData.charAt(0) === '{') {
                            try {
                                captionData = JSON.parse(captionData);
                            } catch (e) {}
                        }
                        var widget = createCaptionWidget(widgetContainer, 'info_popup_carousel_videos',
                            captionKey, captionData, {
                                singlePlaceholder: __t.single_video_caption + ' ' + displayPosition + '...',
                                isArray: true
                            });
                        widget.singleInput.addEventListener('input', function() {
                            video.caption = this.value;
                        });
                        row.appendChild(widgetContainer);
                        popupRows.appendChild(row);
                    }
                });
            }

            // Single array to track ALL videos in chronological order
            var allVideoEntries = []; // [{type: 'url'|'upload', data: string|file, caption: string, domIndex: number}]
            var uploadCounter = 0; // Counter for unique upload IDs
            var selectedCarouselVideoFiles = []; // Separate array to reliably track new video files

            // Separate tracker for URL captions keyed by domIndex to survive re-renders
            var urlCaptionTracker = {};

            window.removeUrlVideo = function(domIndex) {
                // Remove from allVideoEntries
                allVideoEntries = allVideoEntries.filter(function(entry) {
                    return !(entry.type === 'url' && entry.domIndex === domIndex);
                });

                // Clear caption from tracker
                delete urlCaptionTracker[domIndex];

                // Also clear the input in DOM
                var entries = document.querySelectorAll('.carousel-video-url-entry');
                if (entries[domIndex]) {
                    var inputs = entries[domIndex].querySelectorAll('input[name="carousel_video_urls[]"]');
                    if (inputs.length > 0) {
                        inputs[0].value = '';
                        inputs[0].setAttribute('data-caption', '');
                    }
                }
                updateCarouselVideoPreviews();
            };

            window.previewCarouselVideos = function(input) {
                var files = Array.from(input.files);

                if (files.length === 0) return;

                files.forEach(function(file) {
                    uploadCounter++;
                    // Add to reliable file tracker first
                    selectedCarouselVideoFiles.push(file);
                    // Also add to allVideoEntries
                    allVideoEntries.push({
                        type: 'upload',
                        data: file,
                        uploadId: uploadCounter,
                        caption: ''
                    });
                });

                // Clear the input so browser doesn't auto-retain old files
                input.value = '';

                renderNewCarouselVideoPreviews();
            };

            function renderNewCarouselVideoPreviews() {
                updateCarouselVideoPreviews();
            }

            window.removePreviewVideo = function(uploadId) {
                // Find the entry first to get the file reference
                var removedEntry = null;
                allVideoEntries.forEach(function(entry) {
                    if (entry.type === 'upload' && entry.uploadId === uploadId) {
                        removedEntry = entry;
                    }
                });

                // Remove from allVideoEntries by uploadId
                allVideoEntries = allVideoEntries.filter(function(entry) {
                    return !(entry.type === 'upload' && entry.uploadId === uploadId);
                });

                // Remove from selectedCarouselVideoFiles if it's a new upload (has File data)
                if (removedEntry && removedEntry.data instanceof File) {
                    selectedCarouselVideoFiles = selectedCarouselVideoFiles.filter(function(f) {
                        return f !== removedEntry.data;
                    });
                }

                renderNewCarouselVideoPreviews();
            };

            selectType('{{ $slide->slide_type }}');

            // Initialize URL image previews and carousel video previews on load
            setTimeout(function() {
                // Get captions from info_popup (available in both branches)
                var captions = {};
                @if (!empty($slide->info_popup['carousel_videos']))
                    captions = {!! json_encode($slide->info_popup['carousel_videos']) !!};
                @endif

                // If carouselVideoOrder exists, use it to rebuild allVideoEntries in correct order
                if (typeof carouselVideoOrder !== 'undefined' && carouselVideoOrder.length > 0) {
                    // Get all URL inputs and captions from DOM
                    var urlInputs = document.querySelectorAll(
                        '#carousel-video-url-list input[name="carousel_video_urls[]"]');

                    // Build entries based on carouselVideoOrder
                    carouselVideoOrder.forEach(function(item, orderIdx) {
                        if (item.type === 'url') {
                            var urlIdx = item.urlIndex;
                            var urlInput = urlInputs[urlIdx];
                            if (urlInput && urlInput.value && urlInput.value.trim() !== '') {
                                var urlCaptionKey = 'url_' + urlIdx;
                                var caption = captions[urlCaptionKey] || '';
                                // Populate urlCaptionTracker so caption survives re-renders
                                urlCaptionTracker[urlIdx] = caption;
                                allVideoEntries.push({
                                    type: 'url',
                                    data: urlInput.value,
                                    domIndex: urlIdx,
                                    caption: caption
                                });
                            }
                        } else if (item.type === 'upload') {
                            var uploadPath = item.uploadPath;
                            if (uploadPath) {
                                uploadCounter++;
                                var uploadIdx = item.uploadIndex;
                                var uploadCaptionKey = 'upload_' + uploadIdx;
                                var caption = captions[uploadCaptionKey] || '';
                                allVideoEntries.push({
                                    type: 'upload',
                                    data: null,
                                    uploadId: uploadCounter,
                                    uploadPath: uploadPath,
                                    caption: caption
                                });
                            }
                        } else if (item.type === 'newUpload') {
                            // This shouldn't happen on page load, but handle it anyway
                            if (typeof existingCarouselVideos !== 'undefined' && existingCarouselVideos
                                .length > 0) {
                                var newUploadIdx = item.newUploadIndex;
                                if (existingCarouselVideos[newUploadIdx]) {
                                    uploadCounter++;
                                    var newUploadCaptionKey = 'newUpload_' + newUploadIdx;
                                    var caption = captions[newUploadCaptionKey] || '';
                                    allVideoEntries.push({
                                        type: 'upload',
                                        data: null,
                                        uploadId: uploadCounter,
                                        uploadPath: existingCarouselVideos[newUploadIdx],
                                        caption: caption
                                    });
                                }
                            }
                        }
                    });
                } else {
                    // Fallback: load URLs from DOM (existing behavior)
                    updateCarouselVideoPreviews();

                    // Add existing uploaded videos to allVideoEntries
                    if (typeof existingCarouselVideos !== 'undefined' && existingCarouselVideos.length > 0) {
                        existingCarouselVideos.forEach(function(videoPath, idx) {
                            var exists = allVideoEntries.some(function(entry) {
                                return entry.type === 'upload' && entry.uploadPath ===
                                    videoPath;
                            });
                            if (!exists) {
                                uploadCounter++;
                                var caption = captions['upload_' + idx] || '';
                                allVideoEntries.push({
                                    type: 'upload',
                                    data: null,
                                    uploadId: uploadCounter,
                                    uploadPath: videoPath,
                                    caption: caption
                                });
                            }
                        });
                    }
                }

                updateUrlImagePreviews();
                updateCarouselVideoPreviews();

                // Initialize unifiedVideoOrder hidden input from allVideoEntries for form submission
                (function syncUnifiedVideoOrder() {
                    var unifiedOrder = [];
                    var urlIdx = 0;
                    var uploadIdx = 0;
                    var newUploadIdx = 0;
                    allVideoEntries.forEach(function(entry, orderIdx) {
                        if (entry.type === 'url' && entry.data) {
                            unifiedOrder.push({
                                type: 'url',
                                urlValue: entry.data,
                                urlIndex: urlIdx,
                                order: orderIdx
                            });
                            urlIdx++;
                        } else if (entry.type === 'upload') {
                            if (entry.uploadPath) {
                                unifiedOrder.push({
                                    type: 'upload',
                                    uploadPath: entry.uploadPath,
                                    uploadIndex: uploadIdx,
                                    order: orderIdx
                                });
                                uploadIdx++;
                            } else if (entry.data) {
                                unifiedOrder.push({
                                    type: 'newUpload',
                                    newUploadIndex: newUploadIdx,
                                    order: orderIdx
                                });
                                newUploadIdx++;
                            }
                        }
                    });
                    var unifiedInput = document.getElementById('unifiedVideoOrder');
                    if (unifiedInput && unifiedOrder.length > 0) {
                        unifiedInput.value = JSON.stringify(unifiedOrder);
                    }
                })();
            }, 100);

            // Initialize video method visibility based on existing data
            var videoMethodRadios = document.querySelectorAll('input[name="video_method"]');
            videoMethodRadios.forEach(function(radio) {
                if (radio.checked) {
                    toggleVideoMethod(radio.value);
                }
            });

            // Initialize carousel video method visibility based on existing data
            var carouselVideoMethodRadios = document.querySelectorAll('input[name="carousel_video_method"]');
            carouselVideoMethodRadios.forEach(function(radio) {
                if (radio.checked) {
                    toggleCarouselVideoMethod(radio.value);
                }
            });

            var bgColorInput = document.querySelector('[name=bg_color]');
            if (bgColorInput) {
                bgColorInput.addEventListener('input', function() {
                    document.getElementById('bg_color_text').value = this.value;
                });
            }

            var editor1 = null;
            var initialDescriptionHtml = document.getElementById('div_editor1').innerHTML;

            function setRTEContent(editor, html) {
                if (!html || !html.trim()) return;
                try {
                    if (typeof editor.setHTMLCode === 'function') {
                        editor.setHTMLCode(html);
                    } else if (typeof editor.setHTML === 'function') {
                        editor.setHTML(html);
                    }
                } catch (e) {
                    console.warn('RTE setHTML error:', e);
                }
            }

            function initRTE() {
                if (typeof RichTextEditor === 'undefined') {
                    setTimeout(initRTE, 200);
                    return;
                }
                try {
                    editor1 = new RichTextEditor("#div_editor1", {
                        base_url: '/cms_rte',
                        editorBodyCssClass: 'rte-content-body',
                        height: 500
                    });
                    editor1.setHeight(500);
                    // Restore saved description content with multiple attempts
                    // The RTE iframe may not be ready immediately after construction
                    setRTEContent(editor1, initialDescriptionHtml);
                    setTimeout(function() {
                        setRTEContent(editor1, initialDescriptionHtml);
                    }, 300);
                    setTimeout(function() {
                        setRTEContent(editor1, initialDescriptionHtml);
                    }, 800);
                    setTimeout(function() {
                        // Final check: if editor still empty but we have content, try once more
                        try {
                            var currentContent = '';
                            if (typeof editor1.getHTMLCode === 'function') currentContent = editor1
                                .getHTMLCode();
                            else if (typeof editor1.getHTML === 'function') currentContent = editor1.getHTML();
                            if ((!currentContent || currentContent.trim() === '' || currentContent.trim() ===
                                    '<p><br></p>' || currentContent.trim() === '<br>') &&
                                initialDescriptionHtml && initialDescriptionHtml.trim() !== '') {
                                setRTEContent(editor1, initialDescriptionHtml);
                            }
                        } catch (e) {}
                    }, 1500);
                } catch (e) {
                    console.error('RTE init error:', e);
                }
            }

            if (document.readyState === 'complete' || document.readyState === 'interactive') {
                initRTE();
            } else {
                window.addEventListener('load', initRTE);
            }

            // Initialize existing image caption widgets
            document.querySelectorAll('.existing-caption-widget').forEach(function(el) {
                var captionIndex = el.getAttribute('data-caption-index');
                var rawData = el.getAttribute('data-caption-data');
                var captionData = '';
                try {
                    captionData = JSON.parse(rawData);
                } catch (e) {
                    captionData = rawData || '';
                }
                createCaptionWidget(el, 'info_popup_images', captionIndex, captionData, {
                    singlePlaceholder: __t.popup_existing_images + ' ' + (parseInt(captionIndex) + 1) +
                        '...',
                    isArray: true
                });
            });

            // Initialize existing URL image caption widgets
            document.querySelectorAll('.existing-url-caption-widget').forEach(function(el) {
                var urlIdx = parseInt(el.getAttribute('data-url-caption-index'));
                var rawData = el.getAttribute('data-url-caption-data');
                var captionData = '';
                try {
                    captionData = JSON.parse(rawData);
                } catch (e) {
                    captionData = rawData || '';
                }
                // Gunakan info_popup_existing_urls dengan index = urlIdx agar sesuai controller
                createCaptionWidget(el, 'info_popup_existing_urls', urlIdx, captionData, {
                    singlePlaceholder: 'Info Popup Caption (gambar URL) ' + (urlIdx + 1) + '...',
                    isArray: true
                });
            });

            // Initialize video caption widget (Upload)
            var videoCaptionEl = document.getElementById('videoCaptionWidget');
            if (videoCaptionEl) {
                var videoRawData = videoCaptionEl.getAttribute('data-caption-data');
                var videoCaptionData = '';
                try {
                    videoCaptionData = JSON.parse(videoRawData);
                } catch (e) {
                    videoCaptionData = videoRawData || '';
                }
                createCaptionWidget(videoCaptionEl, 'info_popup_video', null, videoCaptionData, {
                    singlePlaceholder: '{{ __('cms.virtual_slideshow.popup_video_upload') }}...',
                    isArray: false
                });
            }

            // Initialize video caption widget (URL)
            var videoCaptionUrlEl = document.getElementById('videoCaptionWidgetUrl');
            if (videoCaptionUrlEl) {
                var videoUrlRawData = videoCaptionUrlEl.getAttribute('data-caption-data');
                var videoUrlCaptionData = '';
                try {
                    videoUrlCaptionData = JSON.parse(videoUrlRawData);
                } catch (e) {
                    videoUrlCaptionData = videoUrlRawData || '';
                }
                createCaptionWidget(videoCaptionUrlEl, 'info_popup_video_url', null, videoUrlCaptionData, {
                    singlePlaceholder: '{{ __('cms.virtual_slideshow.popup_video_url') }}...',
                    isArray: false
                });
            }

            document.getElementById('slideForm').addEventListener('submit', function(e) {
                var descField = document.getElementById('hiddenDescription');
                if (editor1) {
                    try {
                        descField.value = editor1.getHTMLCode();
                    } catch (err) {
                        try {
                            descField.value = editor1.getHTML();
                        } catch (e2) {
                            console.error('RTE getHTML error:', e2);
                        }
                    }
                }

                // Sync all caption RTE instances
                if (window.allRteInstances && window.allRteInstances.length > 0) {
                    window.allRteInstances.forEach(function(editor) {
                        try {
                            if (document.body.contains(editor.getTargetElement())) {
                                editor.save();
                            }
                        } catch (err) {
                            console.warn('Caption RTE sync error:', err);
                        }
                    });
                }
                // Fallback: if hiddenDescription is still empty but we had saved content, preserve it
                if (!descField.value && initialDescriptionHtml && initialDescriptionHtml.trim() !== '') {
                    descField.value = initialDescriptionHtml;
                }

                var form = document.getElementById('slideForm');

                // For video type: ensure only one video method's data is sent (URL or Upload, not both)
                var videoMethodRadio = document.querySelector('input[name="video_method"]:checked');
                if (videoMethodRadio) {
                    if (videoMethodRadio.value === 'url') {
                        // Clear upload file input so it doesn't send a file alongside the URL
                        var videoInput = document.getElementById('videoInput');
                        if (videoInput) videoInput.value = '';
                    } else {
                        // Clear URL input so it doesn't send a URL alongside the upload
                        var videoUrlInput = document.querySelector('input[name="video_url"]');
                        if (videoUrlInput) videoUrlInput.value = '';
                    }
                }

                // Set files on the existing image input
                var imageInput = document.getElementById('imageInput');
                if (imageInput && selectedNewImageFiles.length > 0) {
                    var imageDataTransfer = new DataTransfer();
                    selectedNewImageFiles.forEach(function(file) {
                        imageDataTransfer.items.add(file);
                    });
                    imageInput.files = imageDataTransfer.files;
                    console.log('Set imageInput files:', imageInput.files.length);
                }

                // Set files on the existing carousel video input using reliable file array
                var carouselInput = document.getElementById('carouselVideoInput');
                if (carouselInput && selectedCarouselVideoFiles.length > 0) {
                    var videoDataTransfer = new DataTransfer();
                    selectedCarouselVideoFiles.forEach(function(file) {
                        videoDataTransfer.items.add(file);
                    });
                    carouselInput.files = videoDataTransfer.files;
                }

                // Build unified_video_order and existing_carousel_videos for form submission
                var unifiedOrder = [];
                var existingVideos = [];
                var urlIdx = 0;
                var uploadIdx = 0;
                var newUploadIdx = 0;

                allVideoEntries.forEach(function(entry, idx) {
                    if (entry.type === 'url' && entry.data) {
                        unifiedOrder.push({
                            type: 'url',
                            urlValue: entry.data,
                            urlIndex: urlIdx,
                            order: idx
                        });
                        urlIdx++;
                    } else if (entry.type === 'upload') {
                        if (entry.uploadPath) {
                            // Existing upload from database
                            unifiedOrder.push({
                                type: 'upload',
                                uploadPath: entry.uploadPath,
                                uploadIndex: uploadIdx,
                                order: idx
                            });
                            existingVideos.push(entry.uploadPath);
                            uploadIdx++;
                        } else if (entry.data) {
                            // New upload
                            unifiedOrder.push({
                                type: 'newUpload',
                                newUploadIndex: newUploadIdx,
                                order: idx
                            });
                            newUploadIdx++;
                        }
                    }
                });

                // Set hidden fields
                document.getElementById('unifiedVideoOrder').value = JSON.stringify(unifiedOrder);
                document.getElementById('existingCarouselVideosInput').value = JSON.stringify(existingVideos);
                console.log('Set unifiedVideoOrder:', unifiedOrder.length, 'existingCarouselVideos:',
                    existingVideos.length);
            });
        })();
    </script>
@endpush
