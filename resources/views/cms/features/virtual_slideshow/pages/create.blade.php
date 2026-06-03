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
@section('breadcrumb_active', __('cms.virtual_slideshow.add_slide'))

@push('styles')
    {{-- RTE CSS loaded globally via layouts/app.blade.php --}}
    <link rel="stylesheet" href="{{ asset('css/cms/virtual_slideshow/pages/create.css') }}">
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
                <h1 class="text-2xl font-bold text-gray-800">{{ __('cms.virtual_slideshow.create_slide_title') }}</h1>
                <p class="text-sm text-gray-500 mt-0.5">{{ $feature->name }}</p>
                @if (isset($page))
                    <p class="text-sm text-blue-600 mt-0.5">
                        {{ __('cms.virtual_slideshow.page_label', ['title' => $page->title]) }}</p>
                @endif
            </div>
        </div>

        <form
            action="{{ isset($page) ? route('cms.features.slideshow.pages.slides.store', [$feature, $page]) : route('cms.features.slideshow.store', $feature) }}"
            method="POST" enctype="multipart/form-data" id="slideForm" class="space-y-6"
            data-redirect="{{ isset($page) ? route('cms.features.slideshow.pages.slides.index', [$feature, $page]) : route('cms.features.slideshow.index', $feature) }}">
            <div class="hidden">
                @csrf
                @if (isset($page))
                    <input type="hidden" name="feature_page_id" value="{{ $page->id }}">
                @endif
            </div>

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                    <div class="font-semibold">{{ __('cms.virtual_slideshow.errors_found') }}</div>
                    <ul class="list-disc list-inside mt-1 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Step 1: Tipe Slide --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                <h2 class="text-base font-semibold text-gray-800">{{ __('cms.virtual_slideshow.step1_type') }}</h2>
                <input type="hidden" name="slide_type" id="slide_type_input" value="text">
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3">
                    <div class="slide-type-card active" data-type="text" onclick="selectType('text')">
                        <div class="icon">📝</div>
                        <div class="label">{{ __('cms.virtual_slideshow.type_text') }}</div>
                        <div class="desc">{{ __('cms.virtual_slideshow.type_text_desc') }}</div>
                    </div>
                    <div class="slide-type-card" data-type="hero" onclick="trySelectHero()">
                        <div class="icon">🌟</div>
                        <div class="label">{{ __('cms.virtual_slideshow.type_hero') }}</div>
                        <div class="desc">{{ __('cms.virtual_slideshow.type_hero_desc') }}</div>
                    </div>
                    <div class="slide-type-card" data-type="carousel" onclick="selectType('carousel')">
                        <div class="icon">🖼️</div>
                        <div class="label">{{ __('cms.virtual_slideshow.type_carousel') }}</div>
                        <div class="desc">{{ __('cms.virtual_slideshow.type_carousel_desc') }}</div>
                    </div>
                    <div class="slide-type-card" data-type="video" onclick="selectType('video')">
                        <div class="icon">🎬</div>
                        <div class="label">{{ __('cms.virtual_slideshow.type_video') }}</div>
                        <div class="desc">{{ __('cms.virtual_slideshow.type_video_desc') }}</div>
                    </div>
                    <div class="slide-type-card" data-type="text_carousel" onclick="selectType('text_carousel')">
                        <div class="icon">📋</div>
                        <div class="label">{{ __('cms.virtual_slideshow.type_text_carousel') }}</div>
                        <div class="desc">{{ __('cms.virtual_slideshow.type_text_carousel_desc') }}</div>
                    </div>
                </div>
            </div>

            {{-- Step 2: Konten Umum --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
                <h2 class="text-base font-semibold text-gray-800">{{ __('cms.virtual_slideshow.step2_content') }}</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">{{ __('cms.virtual_slideshow.slide_title_label') }} <span
                                class="text-gray-400 text-xs">({{ __('cms.virtual_slideshow.optional') }})</span></label>
                        <input type="text" name="title" class="form-input" placeholder="Judul slide..."
                            value="{{ old('title') }}">
                    </div>
                    <div>
                        <label class="form-label">{{ __('cms.virtual_slideshow.slide_subtitle_label') }} <span
                                class="text-gray-400 text-xs">({{ __('cms.virtual_slideshow.optional') }})</span></label>
                        <input type="text" name="subtitle" class="form-input" placeholder="Sub-judul..."
                            value="{{ old('subtitle') }}">
                    </div>
                </div>

                <div class="rte-wrapper">
                    <label class="form-label">{{ __('cms.virtual_slideshow.slide_desc_label') }} <span
                            class="text-gray-400 text-xs">({{ __('cms.virtual_slideshow.desc_toolbar_hint') }})</span></label>
                    <div id="div_editor1" style="min-width:100%; min-height: 500px;">{!! old('description') !!}</div>
                    <input type="hidden" name="description" id="hiddenDescription">
                </div>

                {{-- Layout --}}
                <div class="panel-layout" style="display:none;">
                    <label class="form-label">{{ __('cms.virtual_slideshow.layout_label') }}</label>
                    <div class="flex gap-3">
                        @foreach (['left' => __('cms.virtual_slideshow.layout_left'), 'center' => __('cms.virtual_slideshow.layout_center'), 'right' => __('cms.virtual_slideshow.layout_right')] as $val => $lbl)
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="layout" value="{{ $val }}"
                                    {{ old('layout', 'center') === $val ? 'checked' : '' }}>
                                <span class="text-sm text-gray-700">{{ $lbl }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div class="panel-layout-center">
                    <input type="hidden" name="layout" value="center" id="layout_center_hidden">
                </div>

                {{-- BG Color --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="form-label">{{ __('cms.virtual_slideshow.bg_color_label') }}</label>
                        <div class="flex items-center gap-2">
                            <input type="color" name="bg_color" value="{{ old('bg_color', '#ffffff') }}"
                                class="w-10 h-10 rounded border border-gray-200 cursor-pointer">
                            <input type="text" id="bg_color_text" value="{{ old('bg_color', '#ffffff') }}"
                                class="form-input" style="width:140px;"
                                onchange="document.querySelector('[name=bg_color]').value=this.value">
                        </div>
                    </div>
                    <div>
                        <label class="form-label">{{ __('cms.virtual_slideshow.order_label') }}</label>
                        <input type="number" name="order" min="0" value="{{ old('order', $nextOrder ?? 1) }}"
                            class="form-input" required>
                    </div>
                </div>
            </div>

            {{-- Step 3: Media --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4 mt-4 section-panel active"
                id="panel-images">
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
                    <div id="imagePreviewArea" class="flex flex-wrap gap-3 mb-3"></div>

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
                        <input type="hidden" name="unified_image_order" id="unifiedImageOrderInput" value="">
                        <label
                            class="flex items-center gap-3 px-4 py-3 border-2 border-dashed border-gray-200 rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition-colors">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="text-sm text-gray-500"
                                id="uploadHintText">{{ __('cms.virtual_slideshow.image_upload_hint') }}</span>
                            <input type="file" name="images[]" accept="image/*" multiple class="hidden"
                                id="imageInput" onchange="previewImages(this)">
                        </label>
                    </div>

                    <div id="image-url-section" class="hidden">
                        <div id="image-url-list" class="space-y-2 mb-3">
                            <div class="image-url-entry flex gap-2 items-start" data-index="0">
                                <a href="#" target="_blank"
                                    class="url-link-btn px-2 py-2 text-blue-600 hover:bg-blue-50 rounded-lg flex-shrink-0 opacity-30 cursor-not-allowed"
                                    title="Buka link" onclick="return false;">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                </a>
                                <input type="text" name="image_urls[]" class="form-input flex-1"
                                    placeholder="{{ __('cms.virtual_slideshow.image_url_placeholder') }}" data-index="0"
                                    oninput="updateUrlLink(this)">
                                <button type="button" onclick="removeImageUrlEntry(this)"
                                    class="px-2 py-2 text-red-500 hover:bg-red-50 rounded-lg flex-shrink-0"
                                    title="Hapus">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <button type="button" onclick="addImageUrlEntry()" id="addImageUrlBtn"
                            class="flex items-center gap-2 px-4 py-2 text-sm text-blue-600 border border-blue-300 rounded-lg hover:bg-blue-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Tambah URL Gambar
                        </button>
                    </div>

                    <div id="urlImagePreviewArea" class="flex flex-wrap gap-3 mb-3"></div>

                    <div id="infoPopupImageArea">
                        <label class="form-label mt-2">Keterangan Info Popup per Gambar <span
                                class="text-gray-400 text-xs">({{ __('cms.virtual_slideshow.popup_caption_hint') }})</span></label>
                        <div id="infoPopupRows" class="space-y-2">
                            <p class="text-xs text-gray-400 italic" id="noImagesHint">
                                {{ __('cms.virtual_slideshow.upload_images_first') }}</p>
                        </div>
                    </div>

                    <div id="heroImageLimitWarning"
                        class="hidden mt-2 px-3 py-2 bg-amber-50 border border-amber-200 rounded-lg text-xs text-amber-700">
                        Hanya boleh menyimpan 1 gambar untuk Hero.
                    </div>
                </div>

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
                            <div class="carousel-video-url-entry flex gap-2 items-start" data-index="0">
                                <input type="text" name="carousel_video_urls[]" class="form-input flex-1"
                                    placeholder="{{ __('cms.virtual_slideshow.carousel_video_url_placeholder') }}"
                                    data-index="0" data-caption="" oninput="updateCarouselUrlCaption(this)">
                                <button type="button" onclick="removeCarouselVideoUrlEntry(this)"
                                    class="px-2 py-2 text-red-500 hover:bg-red-50 rounded-lg flex-shrink-0"
                                    title="Hapus">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <button type="button" onclick="addCarouselVideoUrlEntry()"
                            class="flex items-center gap-2 px-4 py-2 text-sm text-blue-600 border border-blue-300 rounded-lg hover:bg-blue-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Tambah URL Video
                        </button>
                    </div>

                    <div id="carouselVideoUrlPreviewArea" class="flex flex-wrap gap-3 mb-3"></div>

                    <div id="carousel-video-upload-section" class="hidden">
                        <input type="hidden" name="unified_video_order" id="unifiedVideoOrderInput" value="">
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
                        <label class="form-label mt-2">Keterangan Info Popup per Video <span
                                class="text-gray-400 text-xs">({{ __('cms.virtual_slideshow.popup_caption_hint') }})</span></label>
                        <div id="carouselVideoInfoPopupRows" class="space-y-2">
                            <p class="text-xs text-gray-400 italic" id="noCarouselVideosHint">
                                {{ __('cms.virtual_slideshow.add_videos_first') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Step 4: Video --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4 mt-4 section-panel"
                id="panel-video">
                <h2 class="text-base font-semibold text-gray-800">{{ __('cms.virtual_slideshow.step4_video') }}</h2>

                <div class="flex gap-4 mb-3">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="video_method" value="url" checked
                            onchange="toggleVideoMethod('url')">
                        <span class="text-sm text-gray-700">URL</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="video_method" value="upload" onchange="toggleVideoMethod('upload')">
                        <span class="text-sm text-gray-700">Upload File</span>
                    </label>
                </div>

                <div id="video-url-section">
                    <div class="flex gap-2 items-start">
                        <input type="text" name="video_url" class="form-input flex-1"
                            placeholder="{{ __('cms.virtual_slideshow.single_video_url_placeholder') }}"
                            oninput="previewVideoUrl(this)">
                        <div
                            class="url-preview-placeholder w-24 h-16 rounded-lg border border-gray-200 bg-gray-50 flex items-center justify-center overflow-hidden flex-shrink-0">
                            <span class="text-xs text-gray-400">{{ __('cms.virtual_slideshow.preview') }}</span>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="form-label">{{ __('cms.virtual_slideshow.popup_video_url') }}</label>
                        <div id="videoCaptionWidgetUrl"></div>
                    </div>
                </div>

                <div id="video-upload-section" class="hidden">
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
                        <div id="videoCaptionWidget"></div>
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
                    {{ __('cms.virtual_slideshow.save_slide') }}
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript" src="{{ asset('cms_rte/rte.js?v=' . (file_exists(public_path('cms_rte/rte.js')) ? filemtime(public_path('cms_rte/rte.js')) : time())) }}"></script>
    <script type="text/javascript" src="{{ asset('cms_rte/all_plugins.js?v=' . (file_exists(public_path('cms_rte/all_plugins.js')) ? filemtime(public_path('cms_rte/all_plugins.js')) : time())) }}"></script>
    </style>
    <script>
        window.allRteInstances = [];
        // Translation strings for JS
        var __t = {
            upload_images_first: '{{ __('cms.virtual_slideshow.upload_images_first') }}',
            add_videos_first: '{{ __('cms.virtual_slideshow.add_videos_first') }}',
            preview: '{{ __('cms.virtual_slideshow.preview') }}',
            view: '{{ __('cms.virtual_slideshow.view') }}',
            google_drive: 'Google Drive',
            video_url: '{{ __('cms.virtual_slideshow.method_url') }}',
            image_url_placeholder: '{{ __('cms.virtual_slideshow.image_url_placeholder') }}',
            carousel_video_url_placeholder: '{{ __('cms.virtual_slideshow.carousel_video_url_placeholder') }}',
        };
        /**
         * Reusable Caption Widget: supports Single caption or Multi Q&A mode
         * @param {HTMLElement} containerEl - container to render into
         * @param {string} namePrefix - form name prefix (e.g. 'info_popup_images', 'info_popup_video', 'info_popup_carousel_videos')
         * @param {string|number} captionIndex - index/key for this caption (e.g. 0, 1, 'video', 'url_0')
         * @param {object|null} existingData - existing caption data to pre-populate
         * @param {object} options - { singlePlaceholder, isArray }
         *   isArray: true means mode/qa use array notation [captionIndex], false means no array (for video)
         */
        function createCaptionWidget(containerEl, namePrefix, captionIndex, existingData, options) {
            options = options || {};
            var singlePlaceholder = options.singlePlaceholder || 'Keterangan (opsional)...';
            var isArray = options.isArray !== false;

            // Detect existing mode
            var existingMode = 'single';
            var existingSingle = '';
            var existingQa = [];
            if (existingData && typeof existingData === 'object' && existingData.type === 'multi') {
                existingMode = 'multi';
                existingQa = existingData.items || [];
            } else if (existingData && typeof existingData === 'string') {
                existingSingle = existingData;
            }

            // Build name parts
            var modeNamePrefix = namePrefix.replace('info_popup_', 'info_popup_mode_');
            var qaNamePrefix = namePrefix.replace('info_popup_', 'info_popup_qa_');
            var modeName = isArray ? modeNamePrefix + '[' + captionIndex + ']' : modeNamePrefix;
            var singleName = namePrefix + (isArray ? '[' + captionIndex + ']' : '');

            containerEl.innerHTML = '';

            // Mode selector
            var modeDiv = document.createElement('div');
            modeDiv.className = 'caption-widget-mode';
            var modeSelect = document.createElement('select');
            modeSelect.name = modeName;
            modeSelect.innerHTML = '<option value="single"' + (existingMode === 'single' ? ' selected' : '') +
                '>{{ __('cms.virtual_slideshow.caption_single') }}</option>' +
                '<option value="multi"' + (existingMode === 'multi' ? ' selected' : '') +
                '>{{ __('cms.virtual_slideshow.caption_multi_qa') }}</option>';
            modeDiv.appendChild(modeSelect);
            containerEl.appendChild(modeDiv);

            // Single caption section
            var singleDiv = document.createElement('div');
            singleDiv.className = 'caption-single-section';
            singleDiv.style.display = existingMode === 'single' ? 'block' : 'none';
            
            var singleWrap = document.createElement('div');
            singleWrap.className = 'rte-compact-container';
            
            var singleInput = document.createElement('textarea');
            singleInput.name = singleName;
            singleInput.className = 'form-input rte-caption-editor';
            singleInput.placeholder = singlePlaceholder;
            singleInput.rows = 3;
            singleInput.value = existingSingle;
            
            singleWrap.appendChild(singleInput);
            singleDiv.appendChild(singleWrap);
            containerEl.appendChild(singleDiv);

            // Function to safely initialize RTE on an element
            function attachRTE(el, initialVal) {
                if (typeof RichTextEditor === 'undefined') {
                    setTimeout(function() { attachRTE(el, initialVal); }, 200);
                    return;
                }
                
                var editor = new RichTextEditor(el, {
                    base_url: '/cms_rte',
                    editorBodyCssClass: 'rte-content-body',
                    height: 100,
                    showStatusBar: false,
                    toolbar: "bold,italic,underline,|,forecolor,backcolor,|,justifyleft,justifycenter,|,insertorderedlist,insertunorderedlist,|,link,insertimage,|,undo,redo,codeview"
                });

                function setContentSafe(val) {
                    try {
                        if (typeof editor.setHTMLCode === 'function') {
                            editor.setHTMLCode(val);
                        } else if (typeof editor.setHTML === 'function') {
                            editor.setHTML(val);
                        }
                    } catch (e) {
                        console.warn('attachRTE setHTML error:', e);
                    }
                }

                if (initialVal) {
                    setContentSafe(initialVal);
                    setTimeout(function() { setContentSafe(initialVal); }, 300);
                    setTimeout(function() { setContentSafe(initialVal); }, 800);
                }
                
                if (!window.allRteInstances) window.allRteInstances = [];
                window.allRteInstances.push(editor);
                return editor;
            }

            // Initialize RTE for single input if visible
            var singleEditor = null;
            if (existingMode === 'single') {
                singleEditor = attachRTE(singleInput, existingSingle);
            }

            // Multi Q&A section
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

                var removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'caption-qa-remove';
                removeBtn.textContent = '✕';
                removeBtn.addEventListener('click', function() {
                    pair.remove();
                });

                var qLabel = document.createElement('label');
                qLabel.style.cssText = 'font-size:0.75rem;color:#6b7280;margin-bottom:2px;display:block;';
                qLabel.textContent = 'Pertanyaan';

                var qInput = document.createElement('input');
                qInput.type = 'text';
                qInput.name = qaBaseName + '[question]';
                qInput.placeholder = 'Pertanyaan...';
                qInput.value = q || '';

                var aLabel = document.createElement('label');
                aLabel.style.cssText = 'font-size:0.75rem;color:#6b7280;margin:6px 0 2px;display:block;';
                aLabel.textContent = 'Jawaban';

                var aWrap = document.createElement('div');
                aWrap.className = 'rte-compact-container';

                var aTextarea = document.createElement('textarea');
                aTextarea.name = qaBaseName + '[answer]';
                aTextarea.className = 'rte-caption-editor';
                aTextarea.placeholder = 'Jawaban...';
                aTextarea.textContent = a || '';

                pair.appendChild(removeBtn);
                pair.appendChild(qLabel);
                pair.appendChild(qInput);
                pair.appendChild(aLabel);
                aWrap.appendChild(aTextarea);
                pair.appendChild(aWrap);
                qaList.appendChild(pair);

                // Attach RTE to answer textarea
                attachRTE(aTextarea, a);
            }

            // Pre-populate existing Q&A items
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
            addBtn.innerHTML = '+ Tambah Q&A';
            addBtn.addEventListener('click', function() {
                addQaPair('', '');
            });
            multiDiv.appendChild(addBtn);

            containerEl.appendChild(multiDiv);

            // Toggle handler
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

            document.addEventListener('DOMContentLoaded', function() {
                window.trySelectHero = function() {
                    var hasHeroSlide =
                        @isset($hasHeroSlide)
                            {{ $hasHeroSlide ? 'true' : 'false' }}
                        @else
                            false
                        @endisset ;
                    if (hasHeroSlide) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Tidak dapat memilih Hero',
                            text: 'Halaman ini sudah memiliki slide Hero. Hanya 1 slide Hero yang diizinkan per halaman.',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#ef4444',
                        });
                        return;
                    }
                    selectType('hero');
                };
            });

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

                // Show/hide image caption area and add-URL button (hero: hide both)
                var captionArea = document.getElementById('infoPopupImageArea');
                if (captionArea) {
                    captionArea.style.display = (cfg.showImages && cfg.showImageCaption !== false) ? 'block' :
                        'none';
                }
                var addUrlBtn = document.getElementById('addImageUrlBtn');
                if (addUrlBtn) {
                    addUrlBtn.style.display = (cfg.showImages && cfg.showImageCaption !== false) ? 'inline-flex' :
                        'none';
                }

                // Show/hide image/video sections based on type
                var imageSections = document.getElementById('imageSections');
                var videoSections = document.getElementById('videoSections');
                var carouselToggle = document.getElementById('carouselMediaToggle');

                if (type === 'text_carousel') {
                    // Show toggle and default to images
                    if (carouselToggle) carouselToggle.classList.remove('hidden');
                    if (imageSections) imageSections.classList.remove('hidden');
                    if (videoSections) videoSections.classList.add('hidden');
                    // Reset to images
                    var imgRadio = carouselToggle ? carouselToggle.querySelector('input[value="images"]') : null;
                    if (imgRadio) imgRadio.checked = true;
                    toggleCarouselMediaType('images');
                } else {
                    // Hide toggle, show images by default
                    if (carouselToggle) carouselToggle.classList.add('hidden');
                    if (imageSections) imageSections.classList.remove('hidden');
                    if (videoSections) videoSections.classList.add('hidden');
                }

                // Hero: conditionally show URL section based on existing images
                var isHero = (type === 'hero');
                if (isHero) {
                    // Check for existing uploaded images
                    var existingInputs = document.querySelectorAll(
                        '#existingImagesArea input[name^="existing_images"]');
                    var existingCount = 0;
                    existingInputs.forEach(function(inp) {
                        if (!inp.disabled) existingCount++;
                    });
                    var hasExistingImage = existingCount > 0;

                    // URL section: visible only if no existing uploaded image
                    var imageUrlSection = document.getElementById('image-url-section');
                    if (imageUrlSection) {
                        imageUrlSection.style.display = hasExistingImage ? 'none' : '';
                    }

                    var heroLimitWarning = document.getElementById('heroImageLimitWarning');
                    if (heroLimitWarning) {
                        heroLimitWarning.style.display = hasExistingImage ? 'block' : 'none';
                    }
                }
                var uploadHint = document.getElementById('uploadHintText');
                if (uploadHint) uploadHint.textContent = isHero ? 'Klik untuk pilih gambar (hanya 1)' :
                    'Klik untuk pilih gambar (bisa lebih dari 1)';
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
                var typeInput = document.getElementById('slide_type_input');
                var isHero = typeInput && typeInput.value === 'hero';

                if (method === 'url') {
                    // HERO: jika sudah ada gambar upload, tolak switch ke URL
                    if (isHero) {
                        var hasNewUpload = typeof selectedImageFiles !== 'undefined' && selectedImageFiles.length >
                            0;
                        var existingInputs = document.querySelectorAll(
                            '#existingImagesArea input[name^="existing_images"]:not([disabled])');
                        var hasExistingUpload = existingInputs.length > 0;

                        if (hasNewUpload || hasExistingUpload) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Tidak dapat menggunakan URL',
                                text: 'Hero hanya boleh memiliki 1 gambar. Hapus gambar upload terlebih dahulu untuk bisa menggunakan URL gambar.',
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
                    urlSection.classList.remove('hidden');
                } else {
                    // HERO: jika sudah ada URL yang diisi, tolak switch ke Upload
                    if (isHero) {
                        var urlEntries = document.querySelectorAll('#image-url-list .image-url-entry');
                        var hasFilledUrl = false;
                        urlEntries.forEach(function(entry) {
                            var input = entry.querySelector('input[name="image_urls[]"]');
                            if (input && input.value.trim() !== '') hasFilledUrl = true;
                        });

                        if (hasFilledUrl) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Tidak dapat menggunakan Upload File',
                                text: 'Hero hanya boleh memiliki 1 gambar. Hapus URL gambar terlebih dahulu untuk bisa menggunakan upload file.',
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
                    urlSection.classList.add('hidden');
                }
            };

            window.toggleVideoMethod = function(method) {
                var uploadSection = document.getElementById('video-upload-section');
                var urlSection = document.getElementById('video-url-section');
                if (method === 'url') {
                    uploadSection.classList.add('hidden');
                    urlSection.classList.remove('hidden');
                } else {
                    uploadSection.classList.remove('hidden');
                    urlSection.classList.add('hidden');
                }
            };

            // Hero: hanya boleh 1 gambar total (upload + URL)
            window.isHeroSingleImageMode = function() {
                var typeInput = document.getElementById('slide_type_input');
                if (!typeInput || typeInput.value !== 'hero') return false;

                var uploadedCount = (typeof selectedImageFiles !== 'undefined') ? selectedImageFiles.length : 0;
                var urlEntries = document.querySelectorAll('#image-url-list .image-url-entry');
                var urlCount = 0;
                urlEntries.forEach(function(entry) {
                    var input = entry.querySelector('input[name="image_urls[]"]');
                    if (input && input.value.trim() !== '') urlCount++;
                });

                return (uploadedCount + urlCount) >= 1;
            };

            window.addImageUrlEntry = function() {
                // Hero: hanya 1 gambar, tidak boleh tambah URL lagi
                if (typeof isHeroSingleImageMode === 'function' && isHeroSingleImageMode()) {
                    var warning = document.getElementById('heroImageLimitWarning');
                    if (warning) {
                        warning.textContent =
                            'Hanya boleh upload 1 gambar untuk Hero. Hapus gambar yang ada terlebih dahulu.';
                        warning.style.display = 'block';
                    }
                    return;
                }
                var list = document.getElementById('image-url-list');
                var entries = list.querySelectorAll('.image-url-entry');
                var newIndex = entries.length;

                var uid = 'url_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
                window.mediaTimeline = window.mediaTimeline || [];
                window.mediaTimeline.push({
                    type: 'url',
                    uid: uid,
                    timestamp: Date.now()
                });

                var entry = document.createElement('div');
                entry.className = 'image-url-entry flex gap-2 items-start';
                entry.setAttribute('data-index', newIndex);
                entry.innerHTML =
                    '<a href="#" target="_blank" class="url-link-btn px-2 py-2 text-blue-600 hover:bg-blue-50 rounded-lg flex-shrink-0 opacity-30 cursor-not-allowed" title="' +
                    __t.open_link + '" onclick="return false;">' +
                    '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg></a>' +
                    '<input type="text" name="image_urls[]" class="form-input flex-1" data-uid="' + uid + '" ' +
                    'placeholder="' + __t.image_url_placeholder + '" data-index="' + newIndex +
                    '" oninput="updateUrlLink(this)">' +
                    '<button type="button" onclick="removeImageUrlEntry(this)" class="px-2 py-2 text-red-500 hover:bg-red-50 rounded-lg flex-shrink-0" title="Hapus">' +
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

                updateUrlImagePreviews();
            };

            // Caption trackers to persist values across re-renders
            var urlImageCaptionTracker = {}; // keyed by url index
            var uploadImageCaptionTracker = {}; // keyed by upload index

            function extractWidgetState(widgetContainer) {
                if (!widgetContainer) return '';
                
                // Sync all active RichTextEditor instances first
                if (window.allRteInstances && window.allRteInstances.length > 0) {
                    window.allRteInstances.forEach(function(editor) {
                        try {
                            if (document.body.contains(editor.getTargetElement())) {
                                editor.save();
                            }
                        } catch (err) {}
                    });
                }

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

            window.removeImageUrlEntry = function(btn) {
                var entry = btn.closest('.image-url-entry');
                var entries = document.querySelectorAll('.image-url-entry');
                // Find this entry's index among all URL entries before removing
                var entryIdx = Array.prototype.indexOf.call(entries, entry);

                var uploadedCount = selectedImageFiles.length;
                var removedBackendIdx = uploadedCount + entryIdx; // backendIdx for this URL

                if (entries.length > 1) {
                    // Remove the caption row explicitly
                    var popupRows = document.getElementById('infoPopupRows');
                    if (popupRows) {
                        // Find caption row with matching data-url-slot-index
                        var captionRow = popupRows.querySelector('[data-url-slot-index="' + entryIdx + '"]');
                        if (captionRow) {
                            // Remove the entire row element
                            captionRow.parentElement.remove();
                        }
                    }

                    // Remove the input entry
                    entry.remove();

                    // Reindex data-index on remaining entries
                    var remaining = document.querySelectorAll('.image-url-entry');
                    remaining.forEach(function(ent, i) {
                        ent.setAttribute('data-index', i);
                        var inp = ent.querySelector('input[name="image_urls[]"]');
                        if (inp) inp.setAttribute('data-index', i);
                    });

                    // Reindex remaining caption containers
                    if (popupRows) {
                        popupRows.querySelectorAll('[data-url-slot-index]').forEach(function(container) {
                            var currentSlot = parseInt(container.getAttribute('data-url-slot-index'));
                            if (currentSlot > entryIdx) {
                                container.setAttribute('data-url-slot-index', currentSlot - 1);
                            }
                        });
                    }

                    // Clean up tracker
                    delete urlImageCaptionTracker[removedBackendIdx];
                    var newTracker = {};
                    Object.keys(urlImageCaptionTracker).forEach(function(key) {
                        var k = parseInt(key);
                        if (k > removedBackendIdx) {
                            newTracker[k - 1] = urlImageCaptionTracker[k];
                        } else {
                            newTracker[k] = urlImageCaptionTracker[k];
                        }
                    });
                    urlImageCaptionTracker = newTracker;
                } else {
                    // Last entry — clear the value instead of removing
                    var input = entry.querySelector('input[name="image_urls[]"]');
                    if (input) input.value = '';

                    // Also clear the caption for last entry
                    var popupRows = document.getElementById('infoPopupRows');
                    if (popupRows) {
                        var captionRow = popupRows.querySelector('[data-url-slot-index="' + entryIdx + '"]');
                        if (captionRow) {
                            captionRow.parentElement.remove();
                        }
                    }

                    delete urlImageCaptionTracker[removedBackendIdx];
                }
                updateUrlImagePreviews();
            };

            window.previewImageUrl = function(input) {
                var url = input.value.trim();
                updateUrlImagePreviews();
            };

            // Helper function to convert Google Drive URL to direct image URL
            function convertGoogleDriveUrl(url) {
                // Google Drive
                var match = url.match(/\/file\/d\/([a-zA-Z0-9_-]+)/);
                if (match) {
                    return 'https://lh3.googleusercontent.com/d/' + match[1];
                }
                match = url.match(/[?&]id=([a-zA-Z0-9_-]+)/);
                if (match) {
                    return 'https://lh3.googleusercontent.com/d/' + match[1];
                }
                // Wikimedia Commons: /wiki/File:NAME → Special:FilePath/NAME
                match = url.match(/commons\.wikimedia\.org\/wiki\/File:(.+)/);
                if (match) {
                    return 'https://commons.wikimedia.org/wiki/Special:FilePath/' + match[1];
                }
                return url;
            }

            function updateUrlImagePreviews() {
                var previewArea = document.getElementById('urlImagePreviewArea');
                var popupRows = document.getElementById('infoPopupRows');
                var popupArea = document.getElementById('infoPopupImageArea');
                var hint = document.getElementById('noImagesHint');

                // Save current caption values before clearing by reading widget state
                // Use data-url-slot-index attr set during render so we can stable-map back
                popupRows.querySelectorAll('[data-url-slot-index]').forEach(function(container) {
                    var slotIdx = parseInt(container.getAttribute('data-url-slot-index'));
                    if (!isNaN(slotIdx)) {
                        urlImageCaptionTracker[slotIdx] = extractWidgetState(container);
                    }
                });
                popupRows.querySelectorAll('[data-upload-slot-index]').forEach(function(container) {
                    var slotIdx = parseInt(container.getAttribute('data-upload-slot-index'));
                    var backendIdx = parseInt(container.getAttribute('data-upload-backend-idx'));
                    if (!isNaN(slotIdx) && !isNaN(backendIdx)) {
                        uploadImageCaptionTracker[backendIdx] = extractWidgetState(container);
                    }
                });

                window.mediaTimeline = window.mediaTimeline || [];
                var activeItems = [];
                var numValidUrls = 0;

                var urlInputs = document.querySelectorAll('#image-url-list input[name="image_urls[]"]');
                var uploadedCount = selectedImageFiles.length;

                urlInputs.forEach(function(input, idx) {
                    var url = input.value.trim();
                    var entry = input.closest('.image-url-entry');
                    var linkBtn = entry ? entry.querySelector('.url-link-btn') : null;

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
                            url: convertGoogleDriveUrl(url),
                            originalUrl: url,
                            uid: uid,
                            domIdx: idx,
                            backendIdx: uploadedCount + numValidUrls, // N to N+M-1
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

                selectedImageFiles.forEach(function(file, idx) {
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
                        domIdx: idx,
                        newUploadIndex: idx, // original position in unified order (used for form field name)
                        backendIdx: idx, // 0 to N-1
                        timestamp: timelineEntry ? timelineEntry.timestamp : Date.now()
                    });
                });

                var totalImages = activeItems.length;

                // Clear both areas
                previewArea.innerHTML = '';
                popupRows.innerHTML = '';

                if (totalImages === 0) {
                    previewArea.style.display = 'none';
                    if (hint) hint.style.display = '';
                    if (popupArea) popupArea.style.display = 'none';
                    popupRows.innerHTML = '<p class="text-xs text-gray-400 italic" id="noImagesHint">' + __t
                        .upload_images_first + '</p>';
                    return;
                }

                previewArea.style.display = 'flex';
                if (hint) hint.style.display = 'none';

                var typeInput = document.getElementById('slide_type_input');
                var isHeroType = typeInput && typeInput.value === 'hero';
                if (isHeroType) {
                    if (popupArea) popupArea.style.display = 'none';
                } else {
                    if (popupArea) popupArea.style.display = 'block';
                }

                // Sort purely chronologically by timestamp
                activeItems.sort(function(a, b) {
                    return a.timestamp - b.timestamp;
                });

                // Render logic in chronological order
                activeItems.forEach(function(item) {
                    if (item.type === 'upload') {
                        // Render upload preview synchronously to preserve order
                        var wrap = document.createElement('div');
                        wrap.className = 'img-preview-wrap';
                        wrap.innerHTML =
                            '<div style="height:60px;width:60px;background:#f3f4f6;border-radius:8px;border:1px solid #e5e7eb;display:flex;align-items:center;justify-content:center;"><span class="text-xs text-gray-400">...</span></div>';
                        previewArea.appendChild(wrap);

                        var reader = new FileReader();
                        (function(idx, backendIdx, fileReader, container) {
                            fileReader.onload = function(e) {
                                container.innerHTML = '<img src="' + e.target.result +
                                    '" alt="" style="height:60px;width:60px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb;">' +
                                    '<button type="button" class="remove-img" onclick="removePreviewImage(' +
                                    idx + ')">✕</button>';
                            };
                        })(item.domIdx, item.backendIdx, reader, wrap);
                        reader.readAsDataURL(item.file);

                        // Render upload caption
                        var savedCaption = uploadImageCaptionTracker[item.domIdx] || '';
                        var row = document.createElement('div');
                        row.className = 'info-popup-row';
                        var label = document.createElement('label');
                        label.className = 'form-label';
                        label.style.marginBottom = '4px';
                        label.textContent = 'Gambar Upload ' + (item.domIdx + 1);
                        row.appendChild(label);
                        var widgetContainer = document.createElement('div');
                        widgetContainer.setAttribute('data-upload-slot-index', item.domIdx);
                        widgetContainer.setAttribute('data-upload-backend-idx', item.newUploadIndex);
                        // Use newUploadIndex (original position in unified order) for form field name
                        // so backend's captionToStorage['newUploads_X'] lookup finds the right slot
                        createCaptionWidget(widgetContainer, 'info_popup_new_images', item.newUploadIndex,
                            savedCaption, {
                                singlePlaceholder: 'Keterangan gambar ' + (item.domIdx + 1) +
                                    ' (opsional)...',
                                isArray: true
                            });
                        row.appendChild(widgetContainer);
                        popupRows.appendChild(row);

                    } else if (item.type === 'url') {
                        // Render URL preview
                        var wrap = document.createElement('div');
                        wrap.className = 'img-preview-wrap';

                        wrap.innerHTML = '<img src="' + item.url +
                            '" alt="" style="height:60px;width:60px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb;" onerror="this.style.display=\'none\'; this.nextElementSibling.style.display=\'flex\';">' +
                            '<div class="flex flex-col items-center justify-center" style="height:60px;width:60px;background:#f3f4f6;border-radius:8px;border:1px solid #e5e7eb;display:none;">' +
                            '<svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>' +
                            '<a href="' + item.originalUrl +
                            '" target="_blank" class="text-xs text-blue-500 hover:text-blue-700 mt-1">' + __t
                            .view + '</a></div>' +
                            '<button type="button" class="remove-img" onclick="removeUrlImage(' + item.domIdx +
                            ')">✕</button>';
                        previewArea.appendChild(wrap);

                        // Render URL caption
                        var savedCaption = urlImageCaptionTracker[item.domIdx] || '';
                        var row = document.createElement('div');
                        row.className = 'info-popup-row';
                        var label = document.createElement('label');
                        label.className = 'form-label';
                        label.style.marginBottom = '4px';
                        label.textContent = 'Gambar URL ' + (item.domIdx + 1);
                        row.appendChild(label);
                        var widgetContainer = document.createElement('div');
                        widgetContainer.setAttribute('data-url-slot-index', item.domIdx);
                        // Force Backend Index for precise matching
                        createCaptionWidget(widgetContainer, 'info_popup_images', item.backendIdx,
                            savedCaption, {
                                singlePlaceholder: 'Keterangan gambar ' + (item.domIdx + 1) +
                                    ' (opsional)...',
                                isArray: true
                            });
                        row.appendChild(widgetContainer);
                        popupRows.appendChild(row);
                    }
                });

                updateUnifiedImageOrderInput(activeItems);
            }

            function updateUnifiedImageOrderInput(activeItems) {
                var input = document.getElementById('unifiedImageOrderInput');
                if (!input) return;
                var serializable = activeItems.map(function(item) {
                    if (item.type === 'url') {
                        // Use data-index (current DOM position) instead of domIdx (original position)
                        // because removeImageUrlEntry reindexes data-index after removal
                        var urlInputs = document.querySelectorAll('#image-url-list input[name="image_urls[]"]');
                        var urlIndex = -1;
                        for (var i = 0; i < urlInputs.length; i++) {
                            if (urlInputs[i].value.trim() && urlInputs[i].getAttribute('data-uid') === item
                                .uid) {
                                urlIndex = parseInt(urlInputs[i].getAttribute('data-index'));
                                break;
                            }
                        }
                        return {
                            type: 'url',
                            urlIndex: urlIndex >= 0 ? urlIndex : item.domIdx,
                            urlValue: item.originalUrl,
                            order: item.timestamp
                        };
                    } else if (item.type === 'upload') {
                        // Use current position in selectedImageFiles, not stored domIdx
                        // because removePreviewImage reindexes selectedImageFiles after splice
                        var currentIdx = -1;
                        for (var j = 0; j < selectedImageFiles.length; j++) {
                            if (selectedImageFiles[j]._uid === item.uid) {
                                currentIdx = j;
                                break;
                            }
                        }
                        return {
                            type: 'newUpload',
                            newUploadIndex: currentIdx >= 0 ? currentIdx : item.domIdx,
                            order: item.timestamp
                        };
                    }
                });
                input.value = JSON.stringify(serializable);
            }

            window.removeUrlImage = function(idx) {
                var uploadedCount = selectedImageFiles.length;
                var inputs = document.querySelectorAll('#image-url-list input[name="image_urls[]"]');
                // Find input by data-index (not array position, since reindexing changes positions)
                var targetInput = null;
                for (var i = 0; i < inputs.length; i++) {
                    if (parseInt(inputs[i].getAttribute('data-index')) === idx) {
                        targetInput = inputs[i];
                        break;
                    }
                }
                if (targetInput) {
                    targetInput.value = '';
                }

                // Remove caption row from DOM explicitly - remove the entire info-popup-row div
                var popupRows = document.getElementById('infoPopupRows');
                if (popupRows) {
                    var removedCaption = popupRows.querySelector('[data-url-slot-index="' + idx + '"]');
                    if (removedCaption) {
                        // Remove parent row element which contains the entire caption widget
                        var rowParent = removedCaption.parentElement;
                        if (rowParent && rowParent.classList && rowParent.classList.contains('info-popup-row')) {
                            rowParent.remove();
                        } else {
                            removedCaption.remove();
                        }
                    }
                    // Reindex remaining caption rows
                    popupRows.querySelectorAll('[data-url-slot-index]').forEach(function(container) {
                        var currentSlot = parseInt(container.getAttribute('data-url-slot-index'));
                        if (currentSlot > idx) {
                            container.setAttribute('data-url-slot-index', currentSlot - 1);
                        }
                    });
                }

                delete urlImageCaptionTracker[uploadedCount + idx];
                updateUrlImagePreviews();
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
                            '" class="w-full h-full object-cover rounded-lg" onerror="this.parentElement.innerHTML=\'<div class=\\\'flex flex-col items-center justify-center w-full h-full\\\'><svg class=\\\'w-5 h-5 text-blue-500\\\' fill=\\\'none\\\' stroke=\\\'currentColor\\\' viewBox=\\\'0 0 24 24\\\'><path stroke-linecap=\\\'round\\\' stroke-linejoin=\\\'round\\\' stroke-width=\\\'2\\\' d=\\\'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z\\\'/></svg><span class=\\\'text-xs text-blue-500 mt-1\\\'>Google Drive</span></div>\';">';
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
            window.removePreviewImage = function(idx) {
                // Remove caption row from DOM explicitly
                var popupRows = document.getElementById('infoPopupRows');
                if (popupRows) {
                    var removedCaption = popupRows.querySelector('[data-upload-slot-index="' + idx + '"]');
                    if (removedCaption) {
                        // Remove parent row element which contains the entire caption widget
                        var rowParent = removedCaption.parentElement;
                        if (rowParent && rowParent.classList && rowParent.classList.contains('info-popup-row')) {
                            rowParent.remove();
                        } else {
                            removedCaption.remove();
                        }
                    }
                    // Reindex remaining caption rows
                    popupRows.querySelectorAll('[data-upload-slot-index]').forEach(function(container) {
                        var currentSlot = parseInt(container.getAttribute('data-upload-slot-index'));
                        if (currentSlot > idx) {
                            container.setAttribute('data-upload-slot-index', currentSlot - 1);
                        }
                    });
                }

                // Remove caption and shift remaining upload captions down
                delete uploadImageCaptionTracker[idx];
                var newTracker = {};
                Object.keys(uploadImageCaptionTracker).forEach(function(key) {
                    var k = parseInt(key);
                    if (k > idx) {
                        newTracker[k - 1] = uploadImageCaptionTracker[k];
                    } else {
                        newTracker[k] = uploadImageCaptionTracker[k];
                    }
                });
                uploadImageCaptionTracker = newTracker;

                selectedImageFiles.splice(idx, 1);
                updateUrlImagePreviews();
            };
            // Carousel Video Variables and Functions
            var keptCarouselCaptions = {};
            var selectedCarouselVideoFiles = [];
            var unifiedVideoOrder = [];

            function updateUnifiedVideoOrderInput() {
                var input = document.getElementById('unifiedVideoOrderInput');
                if (!input) return;
                // Serialize order data (exclude File objects for JSON serialization)
                var serializable = unifiedVideoOrder.map(function(v) {
                    return {
                        type: v.type,
                        urlIndex: v.urlIndex,
                        newUploadIndex: v.newUploadIndex,
                        urlValue: v.urlValue,
                        order: v.order
                    };
                });
                input.value = JSON.stringify(serializable);
            }

            window.toggleCarouselVideoMethod = function(method) {
                document.getElementById('carousel-video-url-section').classList.toggle('hidden', method !== 'url');
                document.getElementById('carousel-video-upload-section').classList.toggle('hidden', method !==
                    'upload');
            };

            window.addCarouselVideoUrlEntry = function() {
                var list = document.getElementById('carousel-video-url-list');
                var entries = list.querySelectorAll('.carousel-video-url-entry');
                var newIndex = entries.length;
                var currentTime = Date.now();

                var entry = document.createElement('div');
                entry.className = 'carousel-video-url-entry flex gap-2 items-start';
                entry.setAttribute('data-index', newIndex);
                entry.innerHTML =
                    '<input type="text" name="carousel_video_urls[' + newIndex + ']" class="form-input flex-1" ' +
                    'placeholder="' + __t.carousel_video_url_placeholder + '" data-index="' + newIndex +
                    '" data-caption="" oninput="updateCarouselUrlCaption(this)">' +
                    '<button type="button" onclick="removeCarouselVideoUrlEntry(this)" class="px-2 py-2 text-red-500 hover:bg-red-50 rounded-lg flex-shrink-0" title="Hapus">' +
                    '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>';
                list.appendChild(entry);

                // Add to unifiedVideoOrder with timestamp
                unifiedVideoOrder.push({
                    type: 'url',
                    urlIndex: newIndex,
                    urlValue: '',
                    order: currentTime
                });
                updateUnifiedVideoOrderInput();
            };

            window.updateCarouselUrlCaption = function(input) {
                updateCarouselVideoPreviews();
            };

            window.removeCarouselVideoUrlEntry = function(btn) {
                var entry = btn.closest('.carousel-video-url-entry');
                var entries = document.querySelectorAll('.carousel-video-url-entry');
                var urlIndex = entry ? parseInt(entry.getAttribute('data-index')) : -1;

                if (entries.length > 1) {
                    // Remove caption from keptCarouselCaptions
                    if (urlIndex >= 0) {
                        delete keptCarouselCaptions['url_' + urlIndex];
                        // Remove from unifiedVideoOrder
                        unifiedVideoOrder = unifiedVideoOrder.filter(function(v) {
                            return !(v.type === 'url' && v.urlIndex === urlIndex);
                        });
                    }
                    entry.remove();
                    // Reindex remaining entries to maintain proper order
                    var remainingEntries = document.querySelectorAll('.carousel-video-url-entry');
                    remainingEntries.forEach(function(ent, newIdx) {
                        var input = ent.querySelector('input[name^="carousel_video_urls"]');
                        if (input) {
                            var oldIndex = parseInt(input.getAttribute('data-index'));
                            // Migrate caption to new key
                            var oldCaptionKey = 'url_' + oldIndex;
                            var newCaptionKey = 'url_' + newIdx;
                            if (keptCarouselCaptions[oldCaptionKey] && oldIndex !== newIdx) {
                                keptCarouselCaptions[newCaptionKey] = keptCarouselCaptions[oldCaptionKey];
                                delete keptCarouselCaptions[oldCaptionKey];
                            }
                            input.setAttribute('data-index', newIdx);
                            input.name = 'carousel_video_urls[' + newIdx + ']';
                            // Update urlIndex in unifiedVideoOrder
                            unifiedVideoOrder.forEach(function(v) {
                                if (v.type === 'url' && v.urlIndex === oldIndex) {
                                    v.urlIndex = newIdx;
                                }
                            });
                        }
                    });
                } else {
                    // Just clear the value if it's the last entry
                    var inputs = document.querySelectorAll(
                        '#carousel-video-url-list input[name^="carousel_video_urls"]');
                    if (inputs[0]) {
                        inputs[0].value = '';
                        inputs[0].setAttribute('data-caption', '');
                    }
                    // Clear caption from keptCarouselCaptions and update unifiedVideoOrder
                    if (urlIndex >= 0) {
                        delete keptCarouselCaptions['url_' + urlIndex];
                        unifiedVideoOrder.forEach(function(v) {
                            if (v.type === 'url' && v.urlIndex === urlIndex) {
                                v.urlValue = '';
                            }
                        });
                    }
                }
                updateUnifiedVideoOrderInput();
                updateCarouselVideoPreviews();
            };

            window.previewCarouselVideoUrl = function(input) {
                updateCarouselVideoPreviews();
            };

            var selectedImageFiles = [];

            window.previewImages = function(input) {
                var files = Array.from(input.files);

                if (files.length === 0) return;

                files.forEach(function(file) {
                    selectedImageFiles.push(file);
                });

                updateUrlImagePreviews();
                updateUrlImagePreviews();

                // Reset file input so same file can be uploaded again
                input.value = '';
            };

            window.previewCarouselVideos = function(input) {
                var files = Array.from(input.files);

                if (files.length === 0) return;

                files.forEach(function(file) {
                    var currentTime = Date.now();
                    var fileIndex = selectedCarouselVideoFiles.length;
                    selectedCarouselVideoFiles.push(file);
                    // Add to unifiedVideoOrder with timestamp
                    unifiedVideoOrder.push({
                        type: 'newUpload',
                        newUploadIndex: fileIndex,
                        file: file,
                        order: currentTime
                    });
                });

                updateUnifiedVideoOrderInput();
                renderCarouselVideoPreviews();

                // Reset file input so same file can be uploaded again
                input.value = '';
            };

            function getYouTubeId(url) {
                var regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
                var match = url.match(regExp);
                return (match && match[2].length === 11) ? match[2] : null;
            }

            function updateCarouselVideoPreviews() {
                var previewArea = document.getElementById('carouselVideoUrlPreviewArea');
                var popupRows = document.getElementById('carouselVideoInfoPopupRows');
                var hint = document.getElementById('noCarouselVideosHint');

                // Save current caption values before clearing by reading widget state using data attributes
                popupRows.querySelectorAll('[data-url-slot-index]').forEach(function(container) {
                    var slotIdx = parseInt(container.getAttribute('data-url-slot-index'));
                    if (!isNaN(slotIdx)) {
                        var urlInput = document.querySelector(
                            'input[name^="carousel_video_urls"][data-index="' + slotIdx + '"]');
                        if (urlInput && urlInput.value.trim()) {
                            keptCarouselCaptions['url_' + slotIdx] = extractWidgetState(container);
                        } else {
                            delete keptCarouselCaptions['url_' + slotIdx];
                        }
                    }
                });
                popupRows.querySelectorAll('[data-upload-slot-index]').forEach(function(container) {
                    var slotIdx = parseInt(container.getAttribute('data-upload-slot-index'));
                    if (!isNaN(slotIdx)) {
                        keptCarouselCaptions['newUpload_' + slotIdx] = extractWidgetState(container);
                    }
                });

                // Sync URL entries from DOM to unifiedVideoOrder
                var urlEntries = document.querySelectorAll('.carousel-video-url-entry');

                // Update existing URL entries in unifiedVideoOrder
                urlEntries.forEach(function(entry) {
                    var input = entry.querySelector('input[name^="carousel_video_urls"]');
                    if (input) {
                        var urlIndex = parseInt(input.getAttribute('data-index'));
                        var urlValue = input.value.trim();

                        // Find and update existing entry
                        var existingEntry = unifiedVideoOrder.find(function(v) {
                            return v.type === 'url' && v.urlIndex === urlIndex;
                        });

                        if (existingEntry) {
                            existingEntry.urlValue = urlValue;
                        } else {
                            // Register the initial/implicit entry
                            unifiedVideoOrder.push({
                                type: 'url',
                                urlIndex: urlIndex,
                                urlValue: urlValue,
                                order: Date.now()
                            });
                        }
                    }
                });

                // Sort unifiedVideoOrder by order timestamp
                unifiedVideoOrder.sort(function(a, b) {
                    return a.order - b.order;
                });

                // Filter videos that have content for display
                var displayableVideos = unifiedVideoOrder.filter(function(video) {
                    if (video.type === 'url') {
                        return video.urlValue && (video.urlValue.startsWith('http://') || video.urlValue
                            .startsWith('https://'));
                    }
                    return true;
                });

                var totalVideos = displayableVideos.length;

                // Clear preview areas
                previewArea.innerHTML = '';

                if (totalVideos === 0) {
                    if (hint) hint.style.display = '';
                    popupRows.innerHTML = '<p class="text-xs text-gray-400 italic" id="noCarouselVideosHint">' + __t
                        .add_videos_first + '</p>';
                    return;
                }

                if (hint) hint.style.display = 'none';
                popupRows.innerHTML = '';

                // Render videos based on sorted unifiedVideoOrder
                var displayIndex = 0;
                var processedNewUploads = {};

                unifiedVideoOrder.forEach(function(video) {
                    // Only render previews for videos that have actual content
                    var hasContent = (video.type === 'url' && video.urlValue && (video.urlValue.startsWith(
                            'http://') || video.urlValue.startsWith('https://'))) ||
                        video.type === 'newUpload';

                    if (!hasContent) {
                        return; // Skip entries without content
                    }

                    var wrap = document.createElement('div');
                    wrap.className = 'img-preview-wrap';

                    if (video.type === 'url') {
                        var youtubeId = getYouTubeId(video.urlValue);
                        if (youtubeId) {
                            wrap.innerHTML = '<img src="https://img.youtube.com/vi/' + youtubeId +
                                '/1.jpg" style="height:60px;width:80px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb;" class="rounded-lg">' +
                                '<button type="button" class="remove-img" onclick="removeUrlVideo(' + video
                                .urlIndex + ')">✕</button>';
                        } else if (video.urlValue.endsWith('.mp4') || video.urlValue.endsWith('.webm') || video
                            .urlValue.endsWith('.ogg')) {
                            wrap.innerHTML = '<video src="' + video.urlValue +
                                '" style="height:60px;width:80px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb;"></video>' +
                                '<button type="button" class="remove-img" onclick="removeUrlVideo(' + video
                                .urlIndex + ')">✕</button>';
                        } else if (video.urlValue.includes('drive.google.com')) {
                            var gdThumb = convertGoogleDriveUrl(video.urlValue);
                            if (gdThumb) {
                                wrap.innerHTML = '<img src="' + gdThumb +
                                    '" style="height:60px;width:80px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb;" onerror="this.style.display=\'none\';this.nextElementSibling.style.display=\'flex\';">' +
                                    '<div class="w-20 h-16 rounded-lg border border-gray-200 bg-gray-100 flex items-center justify-center" style="display:none;"><svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg></div>' +
                                    '<button type="button" class="remove-img" onclick="removeUrlVideo(' + video
                                    .urlIndex + ')">✕</button>';
                            } else {
                                wrap.innerHTML =
                                    '<div class="w-20 h-16 rounded-lg border border-gray-200 bg-gray-100 flex items-center justify-center"><svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg></div>' +
                                    '<button type="button" class="remove-img" onclick="removeUrlVideo(' + video
                                    .urlIndex + ')">✕</button>';
                            }
                        } else {
                            wrap.innerHTML =
                                '<div class="w-20 h-16 rounded-lg border border-gray-200 bg-gray-100 flex items-center justify-center"><svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg></div>' +
                                '<button type="button" class="remove-img" onclick="removeUrlVideo(' + video
                                .urlIndex + ')">✕</button>';
                        }

                        var row = document.createElement('div');
                        row.className = 'info-popup-row';
                        var captionKey = 'url_' + video.urlIndex;
                        var rowLabel = document.createElement('label');
                        rowLabel.className = 'form-label';
                        rowLabel.style.marginBottom = '4px';
                        rowLabel.textContent = 'Video ' + (displayIndex + 1);
                        row.appendChild(rowLabel);
                        var widgetContainer = document.createElement('div');
                        widgetContainer.setAttribute('data-url-slot-index', video.urlIndex);
                        var savedCaption = keptCarouselCaptions['url_' + video.urlIndex] || null;
                        createCaptionWidget(widgetContainer, 'info_popup_carousel_videos', captionKey,
                            savedCaption, {
                                singlePlaceholder: 'Keterangan video ' + (displayIndex + 1) +
                                    ' (opsional)...',
                                isArray: true
                            });
                        row.appendChild(widgetContainer);
                        popupRows.appendChild(row);
                        previewArea.appendChild(wrap);
                        displayIndex++;

                    } else if (video.type === 'newUpload') {
                        // Track processed new uploads to avoid duplicates
                        if (processedNewUploads[video.newUploadIndex]) {
                            return;
                        }
                        processedNewUploads[video.newUploadIndex] = true;

                        var videoFile = video.file || selectedCarouselVideoFiles[video.newUploadIndex];
                        if (videoFile) {
                            // Ensure synchronous appendage to preserve chronological ordering
                            var newWrap = document.createElement('div');
                            newWrap.className = 'img-preview-wrap';
                            newWrap.innerHTML =
                                '<div style="height:60px;width:80px;background:#f3f4f6;border-radius:8px;border:1px solid #e5e7eb;display:flex;align-items:center;justify-content:center;"><span class="text-xs text-gray-400">...</span></div>';
                            previewArea.appendChild(newWrap);

                            var reader = new FileReader();
                            (function(displayIdx, file, videoIdx, container) {
                                reader.onload = function(e) {
                                    container.innerHTML = '<video src="' + e.target.result +
                                        '" style="height:60px;width:80px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb;"></video>' +
                                        '<button type="button" class="remove-img" onclick="removePreviewVideo(' +
                                        videoIdx + ')">✕</button>';
                                };
                            })(displayIndex, videoFile, video.newUploadIndex, newWrap);
                            reader.readAsDataURL(videoFile);

                            var row = document.createElement('div');
                            row.className = 'info-popup-row';
                            var captionKey = 'newUpload_' + video.newUploadIndex;
                            var rowLabel = document.createElement('label');
                            rowLabel.className = 'form-label';
                            rowLabel.style.marginBottom = '4px';
                            rowLabel.textContent = 'Video ' + (displayIndex + 1);
                            row.appendChild(rowLabel);
                            var widgetContainer = document.createElement('div');
                            widgetContainer.setAttribute('data-upload-slot-index', video.newUploadIndex);
                            widgetContainer.setAttribute('data-upload-backend-idx', video.newUploadIndex);
                            var savedCaption = keptCarouselCaptions['newUpload_' + video.newUploadIndex] || null;
                            createCaptionWidget(widgetContainer, 'info_popup_carousel_videos', captionKey,
                                savedCaption, {
                                    singlePlaceholder: 'Keterangan video ' + (displayIndex + 1) +
                                        ' (opsional)...',
                                    isArray: true
                                });
                            row.appendChild(widgetContainer);
                            popupRows.appendChild(row);
                            displayIndex++;
                        }
                    }
                });
            }

            window.removeUrlVideo = function(idx) {
                var entries = document.querySelectorAll('.carousel-video-url-entry');
                var targetEntry = null;
                entries.forEach(function(entry) {
                    if (parseInt(entry.getAttribute('data-index')) === idx) {
                        targetEntry = entry;
                    }
                });
                if (targetEntry) {
                    var inputs = targetEntry.querySelectorAll('input[name^="carousel_video_urls"]');
                    if (inputs.length > 0) {
                        inputs[0].value = '';
                        inputs[0].setAttribute('data-caption', '');
                    }
                }

                // Save caption key to delete
                var captionKey = 'url_' + idx;

                // Remove caption row from DOM explicitly BEFORE updateCarouselVideoPreviews
                var popupRows = document.getElementById('carouselVideoInfoPopupRows');
                if (popupRows) {
                    // Find all caption widgets and find the one with matching caption key
                    var allWidgets = popupRows.querySelectorAll('[name^="info_popup_mode_carousel_videos"]');
                    allWidgets.forEach(function(select) {
                        var match = select.name.match(/info_popup_mode_carousel_videos\[([^\]]+)\]/);
                        if (match && match[1] === captionKey) {
                            var rowParent = select.closest('.info-popup-row');
                            if (rowParent) rowParent.remove();
                        }
                    });
                }

                // Delete caption from keptCarouselCaptions BEFORE updateCarouselVideoPreviews
                // so it won't be saved/restored from DOM
                delete keptCarouselCaptions[captionKey];

                // Update unifiedVideoOrder
                unifiedVideoOrder.forEach(function(v) {
                    if (v.type === 'url' && v.urlIndex === idx) {
                        v.urlValue = '';
                    }
                });

                updateUnifiedVideoOrderInput();
                updateCarouselVideoPreviews();
            }

            function renderCarouselVideoPreviews() {
                updateCarouselVideoPreviews();
            }

            window.removePreviewVideo = function(idx) {
                // Remove caption for this new upload
                var captionKey = 'newUpload_' + idx;
                delete keptCarouselCaptions[captionKey];

                // Remove caption row from DOM explicitly
                var popupRows = document.getElementById('carouselVideoInfoPopupRows');
                if (popupRows) {
                    // Find all caption widgets and find the one with matching caption key
                    var allWidgets = popupRows.querySelectorAll('[name^="info_popup_mode_carousel_videos"]');
                    allWidgets.forEach(function(select) {
                        var match = select.name.match(/info_popup_mode_carousel_videos\[([^\]]+)\]/);
                        if (match && match[1] === captionKey) {
                            var rowParent = select.closest('.info-popup-row');
                            if (rowParent) rowParent.remove();
                        }
                    });
                }

                // Remove from unifiedVideoOrder first
                unifiedVideoOrder = unifiedVideoOrder.filter(function(v) {
                    return !(v.type === 'newUpload' && v.newUploadIndex === idx);
                });

                // Reindex remaining newUpload entries and migrate captions
                unifiedVideoOrder.forEach(function(v) {
                    if (v.type === 'newUpload' && v.newUploadIndex > idx) {
                        // Migrate caption to new index
                        var oldCaptionKey = 'newUpload_' + v.newUploadIndex;
                        var newCaptionKey = 'newUpload_' + (v.newUploadIndex - 1);
                        if (keptCarouselCaptions[oldCaptionKey]) {
                            keptCarouselCaptions[newCaptionKey] = keptCarouselCaptions[oldCaptionKey];
                            delete keptCarouselCaptions[oldCaptionKey];
                        }
                        v.newUploadIndex--;
                    }
                });

                // Remove from selectedCarouselVideoFiles
                selectedCarouselVideoFiles.splice(idx, 1);
                updateUnifiedVideoOrderInput();
                renderCarouselVideoPreviews();
            };

            selectType('text');

            var bgColorInput = document.querySelector('[name=bg_color]');
            if (bgColorInput) {
                bgColorInput.addEventListener('input', function() {
                    document.getElementById('bg_color_text').value = this.value;
                });
            }

            var editor1 = null;

            function initRTE() {
                if (typeof RichTextEditor === 'undefined') {
                    setTimeout(initRTE, 200);
                    return;
                }
                try {
                    editor1 = new RichTextEditor("#div_editor1", {
                        base_url: '/cms_rte',
                        editorBodyCssClass: 'rte-content-body',
                        height: '500px'
                    });
                    editor1.setHeight('500px');
                } catch (e) {
                    console.error('RTE init error:', e);
                }
            }

            if (document.readyState === 'complete' || document.readyState === 'interactive') {
                initRTE();
            } else {
                window.addEventListener('load', initRTE);
            }

            // Initialize video caption widget (Upload)
            var videoCaptionEl = document.getElementById('videoCaptionWidget');
            if (videoCaptionEl) {
                createCaptionWidget(videoCaptionEl, 'info_popup_video', null, {!! json_encode(old('info_popup_video', '')) !!}, {
                    singlePlaceholder: '{{ __('cms.virtual_slideshow.popup_caption_hint') }}',
                    isArray: false
                });
            }

            // Initialize video caption widget (URL)
            var videoCaptionUrlEl = document.getElementById('videoCaptionWidgetUrl');
            if (videoCaptionUrlEl) {
                createCaptionWidget(videoCaptionUrlEl, 'info_popup_video_url', null, {!! json_encode(old('info_popup_video_url', '')) !!}, {
                    singlePlaceholder: '{{ __('cms.virtual_slideshow.popup_video_url') }}',
                    isArray: false
                });
            }

            document.getElementById('slideForm').addEventListener('submit', function(e) {
                if (editor1) {
                    try {
                        document.getElementById('hiddenDescription').value = editor1.getHTMLCode();
                    } catch (err) {
                        try {
                            document.getElementById('hiddenDescription').value = editor1.getHTML();
                        } catch (e2) {
                            console.error('RTE getHTML error:', e2);
                        }
                    }
                }

                // Sync all caption RTE instances
                if (window.allRteInstances && window.allRteInstances.length > 0) {
                    window.allRteInstances.forEach(function(editor) {
                        try {
                            // Check if editor is still in DOM (attached to an element that's in DOM)
                            if (document.body.contains(editor.getTargetElement())) {
                                editor.save(); // Built-in method to sync back to textarea
                            }
                        } catch (err) {
                            console.warn('Caption RTE sync error:', err);
                        }
                    });
                }

                var form = document.getElementById('slideForm');

                // Set files on the existing image input
                var imageInput = document.getElementById('imageInput');
                if (imageInput && selectedImageFiles.length > 0) {
                    var imageDataTransfer = new DataTransfer();
                    selectedImageFiles.forEach(function(file) {
                        imageDataTransfer.items.add(file);
                    });
                    imageInput.files = imageDataTransfer.files;
                    console.log('Set imageInput files:', imageInput.files.length);
                }

                // Set files on the existing carousel video input
                var carouselInput = document.getElementById('carouselVideoInput');
                if (carouselInput && selectedCarouselVideoFiles.length > 0) {
                    var videoDataTransfer = new DataTransfer();
                    selectedCarouselVideoFiles.forEach(function(file) {
                        videoDataTransfer.items.add(file);
                    });
                    carouselInput.files = videoDataTransfer.files;
                    console.log('Set carouselInput files:', carouselInput.files.length);
                }
            });
        })();
    </script>
@endpush
