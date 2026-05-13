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
@endsection
@section('breadcrumb_active', __('cms.virtual_books.breadcrumb_create'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/cms/features/virtual_books/book-cover-editor.css') }}">
@endpush

@section('content')
    <div class="mb-4">
        <a href="{{ route('cms.features.virtual_books.index', $feature) }}"
            class="inline-flex items-center gap-2 px-3 py-2 rounded-lg text-white text-sm font-medium transition-colors shadow-sm"
            style="background-color: #818284;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            {{ __('cms.virtual_books.back_to_list') }}
        </a>
    </div>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">{{ __('cms.virtual_books.create_title') }}</h1>
        <p class="text-sm text-gray-500 mt-1">{{ __('cms.virtual_books.create_desc', ['name' => $feature->name]) }}</p>
    </div>

    <form action="{{ route('cms.features.virtual_books.store', $feature) }}" method="POST" enctype="multipart/form-data"
        class="space-y-6" id="bookForm">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Form Section -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="space-y-4">
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 mb-1">{{ __('cms.virtual_books.form.title') }}
                            <span class="text-red-500">*</span></label>
                        <input type="text" name="title" id="bookTitle" value="{{ old('title') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                            placeholder="{{ __('cms.virtual_books.form.title_placeholder') }}" required>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 mb-2">{{ __('cms.virtual_books.form.cover') }}</label>
                        <input type="file" name="cover_image" id="coverImageInput" accept="image/*"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                        <p class="text-xs text-gray-500 mt-1.5">{{ __('cms.virtual_books.form.cover_help') }}</p>
                    </div>

                    <!-- Additional Texts Section -->
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 mb-2">{{ __('cms.virtual_books.form.additional_text') }}</label>
                        <p class="text-xs text-gray-500 mb-2">{{ __('cms.virtual_books.form.additional_text_help') }}</p>

                        <div id="additionalTextsContainer" class="space-y-2">
                            <!-- Dynamic text fields will be added here -->
                        </div>

                        <button type="button" id="addTextBtn"
                            class="mt-2 inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 rounded-md hover:bg-blue-100 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            {{ __('cms.virtual_books.form.add_text') }}
                        </button>
                    </div>

                    <!-- Back Cover Section -->
                    <div class="pt-4 border-t border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-800 mb-3">{{ __('cms.virtual_books.form.back_cover') }}
                        </h4>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 mb-1">{{ __('cms.virtual_books.form.back_title') }}</label>
                        <input type="text" name="back_title" id="backBookTitle" value="{{ old('back_title') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                            placeholder="{{ __('cms.virtual_books.form.back_title_placeholder') }}">
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 mb-2">{{ __('cms.virtual_books.form.back_cover_label') }}</label>
                        <input type="file" name="back_cover_image" id="backCoverImageInput" accept="image/*"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 cursor-pointer">
                        <p class="text-xs text-gray-500 mt-1.5">{{ __('cms.virtual_books.form.cover_help_optional') }}</p>
                    </div>

                    <!-- Back Cover Additional Texts Section -->
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 mb-2">{{ __('cms.virtual_books.form.back_text') }}</label>
                        <p class="text-xs text-gray-500 mb-2">{{ __('cms.virtual_books.form.back_text_help') }}</p>

                        <div id="backAdditionalTextsContainer" class="space-y-2">
                            <!-- Dynamic text fields will be added here -->
                        </div>

                        <button type="button" id="addBackTextBtn"
                            class="mt-2 inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-green-600 bg-green-50 rounded-md hover:bg-green-100 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            {{ __('cms.virtual_books.form.add_text') }}
                        </button>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 mb-2">{{ __('cms.virtual_books.form.thumbnail') }}</label>
                        <input type="file" name="thumbnail" id="thumbnailInput" accept="image/*"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                        <input type="hidden" name="generated_thumbnail" id="generatedThumbnail">

                        <!-- Thumbnail Preview -->
                        <div id="thumbnailPreviewContainer" class="mt-2 hidden">
                            <p class="text-xs text-gray-500 mb-1">{{ __('cms.virtual_books.form.thumbnail_will_save') }}
                            </p>
                            <img id="thumbnailPreview" class="w-24 h-32 object-cover rounded-lg border border-gray-200"
                                alt="Thumbnail Preview">
                            <button type="button" id="removeThumbnail"
                                class="mt-1 text-xs text-red-500 hover:text-red-700">{{ __('cms.virtual_books.form.remove_thumbnail') }}</button>
                        </div>

                        <div class="flex items-center gap-2 mt-2">
                            <button type="button" id="generateThumbnailBtn"
                                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-green-700 bg-green-50 rounded-md hover:bg-green-100 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ __('cms.virtual_books.form.generate_thumbnail') }}
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-1.5">{{ __('cms.virtual_books.form.generate_help') }}</p>
                    </div>

                    <div class="pt-4 border-t border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-800 mb-3">Informasi Buku</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Penulis</label>
                                <input type="text" name="author" value="{{ old('author') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Dimensi</label>
                                <input type="text" name="dimensions" value="{{ old('dimensions') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Halaman</label>
                                <input type="text" name="total_pages" value="{{ old('total_pages') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Berat</label>
                                <input type="text" name="weight" value="{{ old('weight') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Bahasa</label>
                                <input type="text" name="language" value="{{ old('language') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Penerbit</label>
                                <input type="text" name="publisher" value="{{ old('publisher') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Terbit</label>
                                <input type="text" name="publication_year" value="{{ old('publication_year') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">ISBN</label>
                                <input type="text" name="isbn" value="{{ old('isbn') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            </div>
                        </div>
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sinopsis</label>
                            <textarea name="synopsis" rows="4"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">{{ old('synopsis') }}</textarea>
                        </div>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 mb-1">{{ __('cms.virtual_books.form.order') }}
                            <span class="text-red-500">*</span></label>
                        <input type="number" name="order" value="{{ $maxOrder + 1 }}" min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        <p class="text-xs text-gray-500 mt-1">{{ __('cms.virtual_books.form.order_help') }}</p>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <a href="{{ route('cms.features.virtual_books.index', $feature) }}"
                        class="px-5 py-2.5 bg-gray-100 border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 active:bg-gray-300 transition-colors shadow-sm">
                        {{ __('cms.common.cancel') }}
                    </a>
                    <button type="submit"
                        class="px-5 py-2.5 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm"
                        style="background-color:#1d4ed8;">
                        {{ __('cms.virtual_books.btn_save') }}
                    </button>
                </div>
            </div>

            <!-- Preview Section -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-4">{{ __('cms.virtual_books.preview_title') }}</h3>

                <div class="relative flex justify-center">
                    <!-- Book Container -->
                    <div id="bookPreview"
                        class="relative w-48 h-64 bg-gradient-to-b from-amber-700 to-amber-900 rounded-r-md shadow-lg overflow-hidden"
                        style="box-shadow: 4px 4px 15px rgba(0,0,0,0.3);">
                        <!-- Spine -->
                        <div class="absolute left-0 top-0 bottom-0 w-3 bg-gradient-to-r from-amber-900 to-amber-700"></div>

                        <!-- Cover Image Container - Draggable & Resizable -->
                        <div id="coverContainer"
                            class="absolute inset-3 left-6 cursor-move flex items-center justify-center bg-white/10">
                            <span id="coverPlaceholder" class="text-white/50 text-xs text-center px-4">
                                {{ __('cms.virtual_books.preview_placeholder') }}
                            </span>
                            <img id="coverPreview" class="max-w-full max-h-full object-contain pointer-events-none"
                                style="display: none;">
                            <!-- Resize Border - appears when image is uploaded -->
                            <div id="resizeBorder"
                                class="absolute inset-0 border-2 border-dashed border-gray-400/50 opacity-0 transition-opacity pointer-events-none"
                                style="display: none;"></div>
                        </div>

                        <!-- Draggable Title -->
                        <div id="titleContainer"
                            class="absolute top-4 left-0 right-0 text-center px-4 cursor-move select-none">
                            <span id="previewTitle" class="text-white text-xs font-semibold drop-shadow-md line-clamp-2">
                                {{ __('cms.virtual_books.preview_default_title') }}
                            </span>
                        </div>

                        <!-- Additional Texts Container - Draggable -->
                        <div id="additionalTextsPreview" class="absolute left-0 right-0 text-center px-4 cursor-move"
                            style="bottom: 16px;">
                            <!-- Dynamic text previews will be added here -->
                        </div>
                    </div>
                </div>

                <!-- Position Controls -->
                <div class="mt-4 space-y-3">
                    <!-- Zoom Controls -->
                    <div class="flex items-center justify-center gap-2">
                        <button type="button" id="zoomOutBtn"
                            class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 text-lg font-bold transition-colors"
                            title="Perkecil">−</button>
                        <input type="range" id="zoomSlider" min="30" max="250" value="100"
                            class="w-24 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                        <button type="button" id="zoomInBtn"
                            class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 text-lg font-bold transition-colors"
                            title="Perbesar">+</button>
                        <span id="zoomLevel" class="text-xs text-gray-500 ml-2 w-12">100%</span>
                    </div>
                    <div class="flex items-center justify-center gap-4">
                        <button type="button" id="resetPosition"
                            class="text-xs text-gray-500 hover:text-gray-700 underline">
                            {{ __('cms.virtual_books.reset_position') }}
                        </button>
                        <span class="text-xs text-gray-400">|</span>
                        <span class="text-xs text-gray-500">{{ __('cms.virtual_books.drag_hint') }}</span>
                    </div>
                </div>

                <!-- Back Cover Preview -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">{{ __('cms.virtual_books.preview_back_title') }}
                    </h3>

                    <div class="relative flex justify-center mb-4">
                        <!-- Back Book Container -->
                        <div id="backBookPreview"
                            class="relative w-48 h-64 bg-gradient-to-b from-amber-700 to-amber-900 rounded-l-md shadow-lg overflow-hidden"
                            style="box-shadow: -4px 4px 15px rgba(0,0,0,0.3);">
                            <!-- Spine (on left side for back cover) -->
                            <div class="absolute left-0 top-0 bottom-0 w-3 bg-gradient-to-r from-amber-900 to-amber-700">
                            </div>

                            <!-- Back Cover Image Container -->
                            <div id="backCoverContainer"
                                class="absolute inset-3 left-6 cursor-move flex items-center justify-center bg-white/10">
                                <span id="backCoverPlaceholder" class="text-white/50 text-xs text-center px-4">
                                    {{ __('cms.virtual_books.preview_back_placeholder') }}
                                </span>
                                <img id="backCoverPreview"
                                    class="max-w-full max-h-full object-contain pointer-events-none"
                                    style="display: none;">
                                <div id="backResizeBorder"
                                    class="absolute inset-0 border-2 border-dashed border-gray-400/50 opacity-0 transition-opacity pointer-events-none"
                                    style="display: none;"></div>
                            </div>

                            <!-- Draggable Title for Back Cover -->
                            <div id="backTitleContainer"
                                class="absolute top-4 left-0 right-0 text-center px-4 cursor-move select-none">
                                <span id="previewBackTitle"
                                    class="text-white text-xs font-semibold drop-shadow-md line-clamp-2">
                                    {{ __('cms.virtual_books.preview_default_title') }}
                                </span>
                            </div>

                            <!-- Back Additional Texts Container - Draggable -->
                            <div id="backAdditionalTextsPreview"
                                class="absolute left-0 right-0 text-center px-4 cursor-move" style="bottom: 16px;">
                                <!-- Dynamic back text previews will be added here -->
                            </div>
                        </div>
                    </div>

                    <!-- Position Controls for Back Cover -->
                    <div class="space-y-3">
                        <div class="flex items-center justify-center gap-2">
                            <button type="button" id="backZoomOutBtn"
                                class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 text-lg font-bold transition-colors"
                                title="Perkecil">−</button>
                            <input type="range" id="backZoomSlider" min="30" max="250" value="100"
                                class="w-24 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                            <button type="button" id="backZoomInBtn"
                                class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 text-lg font-bold transition-colors"
                                title="Perbesar">+</button>
                            <span id="backZoomLevel" class="text-xs text-gray-500 ml-2 w-12">100%</span>
                        </div>
                        <div class="flex items-center justify-center gap-4">
                            <button type="button" id="resetBackPosition"
                                class="text-xs text-gray-500 hover:text-gray-700 underline">
                                {{ __('cms.virtual_books.reset_position') }}
                            </button>
                            <span class="text-xs text-gray-400">|</span>
                            <span class="text-xs text-gray-500">{{ __('cms.virtual_books.drag_hint') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Hidden fields for positions -->
                <input type="hidden" name="cover_position" id="coverPosition" value='{"x":0,"y":0}'>
                <input type="hidden" name="cover_scale" id="coverScale" value="1">
                <input type="hidden" name="title_position" id="titlePosition" value='{"x":0,"y":0}'>
                <input type="hidden" name="cover_texts" id="coverTexts" value='[]'>
                <input type="hidden" name="back_cover_position" id="backCoverPosition" value='{"x":0,"y":0}'>
                <input type="hidden" name="back_cover_scale" id="backCoverScale" value="1">
                <input type="hidden" name="back_title_position" id="backTitlePosition" value='{"x":0,"y":0}'>
                <input type="hidden" name="back_cover_texts" id="backCoverTexts" value='[]'>
            </div>
        </div>
    </form>

    @push('scripts')
        <script>
            window.bookEditorConfig = {
                coverX: 0,
                coverY: 0,
                coverScale: 1,
                titleX: 0,
                titleY: 0,
                additionalTexts: [],
                backCoverX: 0,
                backCoverY: 0,
                backCoverScale: 1,
                backTitleX: 0,
                backTitleY: 0,
                backAdditionalTexts: []
            };
        </script>
        <script src="{{ asset('js/cms/features/virtual_books/book-cover-editor.js') }}"></script>
    @endpush

@endsection
