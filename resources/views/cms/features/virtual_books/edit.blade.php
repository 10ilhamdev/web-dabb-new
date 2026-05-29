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
@section('breadcrumb_active', __('cms.virtual_books.breadcrumb_edit'))

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
        <h1 class="text-2xl font-bold text-gray-800">{{ __('cms.virtual_books.edit_title', ['name' => $book->title]) }}
        </h1>
        <p class="text-sm text-gray-500 mt-1">{{ __('cms.virtual_books.edit_desc') }}</p>
    </div>

    <form action="{{ route('cms.features.virtual_books.update', [$feature, $book]) }}" method="POST"
        enctype="multipart/form-data" class="space-y-6" id="bookForm">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Left Column: Form -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-sm font-semibold text-gray-800 mb-4">{{ __('cms.virtual_books.book_settings') }}</h3>

                <div class="space-y-4">
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 mb-1">{{ __('cms.virtual_books.form.title') }}
                            <span class="text-red-500">*</span></label>
                        <input type="text" name="title" id="bookTitle" value="{{ old('title', $book->title) }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" required>
                    </div>

                    <!-- Cover Image -->
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 mb-2">{{ __('cms.virtual_books.form.cover') }}</label>
                        @if ($book->cover_image)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $book->cover_image) }}" alt="Cover"
                                    class="w-32 h-40 object-cover rounded-lg border border-gray-200">
                            </div>
                            <label class="flex items-center">
                                <input type="checkbox" name="remove_cover_image" value="1"
                                    class="rounded border-gray-300">
                                <span
                                    class="ml-2 text-sm text-gray-500">{{ __('cms.virtual_books.form.remove_cover') }}</span>
                            </label>
                            <hr class="my-3 border-gray-200">
                        @endif
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
                            @php
                                $coverTextsArray = [];
                                if ($book->cover_texts) {
                                    if (is_string($book->cover_texts)) {
                                        $decoded = json_decode($book->cover_texts, true);
                                        $coverTextsArray = is_array($decoded) ? $decoded : [];
                                    } elseif (is_array($book->cover_texts)) {
                                        $coverTextsArray = $book->cover_texts;
                                    }
                                }
                            @endphp
                            @if (count($coverTextsArray) > 0)
                                @foreach ($coverTextsArray as $index => $coverText)
                                    <div class="flex items-center gap-2" data-id="{{ $index }}">
                                        <input type="text" name="cover_text_{{ $index }}"
                                            value="{{ $coverText['text'] ?? '' }}"
                                            placeholder="{{ __('cms.virtual_books.form.additional_text_placeholder', ['number' => $index + 1]) }}"
                                            class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm"
                                            data-text-id="{{ $index }}">
                                        <button type="button"
                                            class="remove-text-btn p-1.5 text-red-500 hover:bg-red-50 rounded-md transition-colors"
                                            data-id="{{ $index }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                @endforeach
                            @endif
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
                        <input type="text" name="back_title" id="backBookTitle"
                            value="{{ old('back_title', $book->back_title ?? '') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"
                            placeholder="{{ __('cms.virtual_books.form.back_title_placeholder') }}">
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 mb-2">{{ __('cms.virtual_books.form.back_cover_label') }}</label>
                        @if ($book->back_cover_image)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $book->back_cover_image) }}" alt="Back Cover"
                                    class="w-32 h-40 object-cover rounded-lg border border-gray-200">
                            </div>
                            <label class="flex items-center">
                                <input type="checkbox" name="remove_back_cover_image" value="1"
                                    class="rounded border-gray-300">
                                <span
                                    class="ml-2 text-sm text-gray-500">{{ __('cms.virtual_books.form.remove_back_cover') }}</span>
                            </label>
                            <hr class="my-3 border-gray-200">
                        @endif
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
                            @php
                                $backCoverTextsArray = [];
                                if ($book->back_cover_texts) {
                                    if (is_string($book->back_cover_texts)) {
                                        $decoded = json_decode($book->back_cover_texts, true);
                                        $backCoverTextsArray = is_array($decoded) ? $decoded : [];
                                    } elseif (is_array($book->back_cover_texts)) {
                                        $backCoverTextsArray = $book->back_cover_texts;
                                    }
                                }
                            @endphp
                            @if (count($backCoverTextsArray) > 0)
                                @foreach ($backCoverTextsArray as $index => $coverText)
                                    <div class="flex items-center gap-2" data-id="{{ $index }}">
                                        <input type="text" name="back_cover_text_{{ $index }}"
                                            value="{{ $coverText['text'] ?? '' }}"
                                            placeholder="{{ __('cms.virtual_books.form.additional_text_placeholder', ['number' => $index + 1]) }}"
                                            class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm"
                                            data-back-text-id="{{ $index }}">
                                        <button type="button"
                                            class="remove-back-text-btn p-1.5 text-red-500 hover:bg-red-50 rounded-md transition-colors"
                                            data-id="{{ $index }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <button type="button" id="addBackTextBtn"
                            class="mt-2 inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-green-600 bg-green-50 rounded-md hover:bg-green-100 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            {{ __('cms.virtual_books.form.add_text') }}
                        </button>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 mb-2">{{ __('cms.virtual_books.form.thumbnail') }}</label>
                        @if ($book->thumbnail)
                            <div class="mb-2" id="existingThumbnail">
                                <img src="{{ asset('storage/' . $book->thumbnail) }}" alt="Thumbnail"
                                    class="w-24 h-32 object-cover rounded-lg border border-gray-200">
                            </div>
                            <label class="flex items-center">
                                <input type="checkbox" name="remove_thumbnail" value="1"
                                    class="rounded border-gray-300">
                                <span
                                    class="ml-2 text-sm text-gray-500">{{ __('cms.virtual_books.form.remove_thumbnail') }}</span>
                            </label>
                            <hr class="my-3 border-gray-200">
                        @endif

                        <input type="file" name="thumbnail" id="thumbnailInput" accept="image/*"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                        <input type="hidden" name="generated_thumbnail" id="generatedThumbnail">

                        <!-- Thumbnail Preview -->
                        <div id="thumbnailPreviewContainer" class="mt-2 hidden">
                            <p class="text-xs text-gray-500 mb-1">
                                {{ __('cms.virtual_books.form.thumbnail_new_will_save') }}</p>
                            <img id="thumbnailPreview" class="w-24 h-32 object-cover rounded-lg border border-gray-200"
                                alt="Thumbnail Preview">
                            <button type="button" id="removeThumbnail"
                                class="mt-1 text-xs text-red-500 hover:text-red-700">{{ __('cms.virtual_books.form.cancel_remove') }}</button>
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
                        <h4 class="text-sm font-semibold text-gray-800 mb-3">{{ __('cms.virtual_books.form.pdf_section') }}</h4>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('cms.virtual_books.form.upload_pdf') }}</label>
                                <input type="file" name="pdf_path" id="pdfInput" accept="application/pdf"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 cursor-pointer">

                                <div id="pdfInfo" class="mt-2 hidden">
                                    <p class="text-xs text-blue-600 flex items-center gap-1">
                                        <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        {{ __('cms.virtual_books.pdf_loading') }}
                                    </p>
                                </div>

                                @if($book->pdf_path)
                                    <div class="mt-2 flex items-center gap-2 p-2 bg-gray-50 rounded-lg border border-gray-200">
                                        <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9 2a2 2 0 00-2 2v8a2 2 0 002 2h6a2 2 0 002-2V6.414A2 2 0 0016.414 5L14 2.586A2 2 0 0012.586 2H9z"></path><path d="M3 8a2 2 0 012-2v10h8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"></path></svg>
                                        <span class="text-xs text-gray-600 truncate max-w-[200px]">{{ basename($book->pdf_path) }}</span>
                                        <label class="flex items-center gap-1 cursor-pointer ml-auto">
                                            <input type="checkbox" name="remove_pdf" value="1" class="rounded text-red-600 focus:ring-red-500">
                                            <span class="text-xs text-red-600 font-medium">{{ __('cms.common.delete') }}</span>
                                        </label>
                                    </div>
                                @endif
                                <p class="text-xs text-gray-500 mt-1.5">{{ __('cms.virtual_books.form.pdf_desc') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-800 mb-3">{{ __('cms.virtual_books.form.book_info') }}</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('cms.virtual_books.form.author') }}</label>
                                <input type="text" name="author" value="{{ old('author', $book->author) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('cms.virtual_books.form.dimensions') }}</label>
                                <input type="text" name="dimensions" value="{{ old('dimensions', $book->dimensions) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('cms.virtual_books.form.total_pages') }}</label>
                                <input type="text" name="total_pages" id="totalPagesInput" value="{{ old('total_pages', $book->total_pages) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('cms.virtual_books.form.weight') }}</label>
                                <input type="text" name="weight" value="{{ old('weight', $book->weight) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('cms.virtual_books.form.language') }}</label>
                                <input type="text" name="language" value="{{ old('language', $book->language) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('cms.virtual_books.form.publisher') }}</label>
                                <input type="text" name="publisher" value="{{ old('publisher', $book->publisher) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('cms.virtual_books.form.publication_year') }}</label>
                                <input type="text" name="publication_year" value="{{ old('publication_year', $book->publication_year) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('cms.virtual_books.form.isbn') }}</label>
                                <input type="text" name="isbn" value="{{ old('isbn', $book->isbn) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            </div>
                        </div>
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('cms.virtual_books.form.synopsis') }}</label>
                            <textarea name="synopsis" rows="4"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">{{ old('synopsis', $book->synopsis) }}</textarea>
                        </div>
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('cms.virtual_books.form.description') }}</label>
                            <div class="rte-wrapper" style="overflow-x: auto; width: 100%;">
                                <div id="div_editor_desc" style="min-width: 100%;">
                                    {!! old('description', $book->description) !!}
                                </div>
                            </div>
                            <input type="hidden" name="description" id="book_description" />
                        </div>
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 mb-1">{{ __('cms.virtual_books.form.order') }}
                            <span class="text-red-500">*</span></label>
                        <input type="number" name="order" value="{{ old('order', $book->order) }}" min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
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
                        {{ __('cms.virtual_books.btn_save_changes') }}
                    </button>
                </div>
            </div>

            <!-- Right Column: Preview -->
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
                            @if ($book->cover_image)
                                <img id="coverPreview" src="{{ asset('storage/' . $book->cover_image) }}"
                                    class="max-w-full max-h-full object-contain pointer-events-none"
                                    style="transform: translate({{ $book->cover_position['x'] ?? 0 }}px, {{ $book->cover_position['y'] ?? 0 }}px) scale({{ $book->cover_scale ?? 1 }});">
                            @else
                                <span id="coverPlaceholder" class="text-white/50 text-xs text-center px-4">
                                    {{ __('cms.virtual_books.preview_placeholder') }}
                                </span>
                                <img id="coverPreview" class="max-w-full max-h-full object-contain pointer-events-none"
                                    style="display: none;">
                            @endif
                            <!-- Resize Border -->
                            <div id="resizeBorder"
                                class="absolute inset-0 border-2 border-dashed border-gray-400/50 transition-opacity pointer-events-none"
                                style="display: {{ $book->cover_image ? 'block' : 'none' }}; opacity: {{ $book->cover_image ? '1' : '0' }};">
                            </div>
                        </div>

                        <!-- Draggable Title -->
                        @php
                            $titlePositionX = 0;
                            $titlePositionY = 0;
                            if ($book->title_position) {
                                if (is_array($book->title_position)) {
                                    $titlePositionX = $book->title_position['x'] ?? 0;
                                    $titlePositionY = $book->title_position['y'] ?? 0;
                                } else {
                                    $decoded = json_decode($book->title_position, true);
                                    if (is_array($decoded)) {
                                        $titlePositionX = $decoded['x'] ?? 0;
                                        $titlePositionY = $decoded['y'] ?? 0;
                                    }
                                }
                            }
                        @endphp
                        <div id="titleContainer"
                            class="absolute top-4 left-0 right-0 text-center px-4 cursor-move select-none"
                            style="transform: translate({{ $titlePositionX }}px, {{ $titlePositionY }}px);">
                            <span id="previewTitle" class="text-white text-xs font-semibold drop-shadow-md line-clamp-2">
                                {{ $book->title }}
                            </span>
                        </div>

                        <!-- Additional Texts Container - Draggable -->
                        <div id="additionalTextsPreview" class="absolute left-0 right-0 text-center px-4 cursor-move"
                            style="bottom: 16px;">
                            @if (count($coverTextsArray) > 0)
                                @foreach ($coverTextsArray as $index => $coverText)
                                    <span id="textPreview_{{ $index }}"
                                        class="block text-white/80 text-[10px] drop-shadow-md line-clamp-1 mt-1 cursor-move"
                                        data-text-id="{{ $index }}"
                                        style="transform: translate({{ $coverText['position']['x'] ?? 0 }}px, {{ $coverText['position']['y'] ?? 0 }}px);">
                                        {{ $coverText['text'] ?? 'Teks ' . ($index + 1) }}
                                    </span>
                                @endforeach
                            @endif
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
                        <input type="range" id="zoomSlider" min="30" max="250"
                            value="{{ ($book->cover_scale ?? 1) * 100 }}"
                            class="w-24 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                        <button type="button" id="zoomInBtn"
                            class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 text-lg font-bold transition-colors"
                            title="Perbesar">+</button>
                        <span id="zoomLevel"
                            class="text-xs text-gray-500 ml-2 w-12">{{ round(($book->cover_scale ?? 1) * 100) }}%</span>
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
                                @if ($book->back_cover_image)
                                    <img id="backCoverPreview" src="{{ asset('storage/' . $book->back_cover_image) }}"
                                        class="max-w-full max-h-full object-contain pointer-events-none"
                                        style="transform: translate({{ $book->back_cover_position['x'] ?? 0 }}px, {{ $book->back_cover_position['y'] ?? 0 }}px) scale({{ $book->back_cover_scale ?? 1 }});">
                                @else
                                    <span id="backCoverPlaceholder" class="text-white/50 text-xs text-center px-4">
                                        {{ __('cms.virtual_books.preview_back_placeholder') }}
                                    </span>
                                    <img id="backCoverPreview"
                                        class="max-w-full max-h-full object-contain pointer-events-none"
                                        style="display: none;">
                                @endif
                                <div id="backResizeBorder"
                                    class="absolute inset-0 border-2 border-dashed border-gray-400/50 transition-opacity pointer-events-none"
                                    style="display: {{ $book->back_cover_image ? 'block' : 'none' }}; opacity: {{ $book->back_cover_image ? '1' : '0' }};">
                                </div>
                            </div>

                            <!-- Draggable Title for Back Cover -->
                            <div id="backTitleContainer"
                                class="absolute top-4 left-0 right-0 text-center px-4 cursor-move select-none"
                                style="transform: translate({{ $book->back_title_position['x'] ?? 0 }}px, {{ $book->back_title_position['y'] ?? 0 }}px);">
                                <span id="previewBackTitle"
                                    class="text-white text-xs font-semibold drop-shadow-md line-clamp-2">
                                    {{ $book->back_title ?? $book->title }}
                                </span>
                            </div>

                            <!-- Back Additional Texts Container - Draggable -->
                            <div id="backAdditionalTextsPreview"
                                class="absolute left-0 right-0 text-center px-4 cursor-move" style="bottom: 16px;">
                                @if (count($backCoverTextsArray) > 0)
                                    @foreach ($backCoverTextsArray as $index => $backText)
                                        <span id="backTextPreview_{{ $index }}"
                                            class="block text-white/80 text-[10px] drop-shadow-md line-clamp-1 mt-1 cursor-move"
                                            data-back-text-id="{{ $index }}"
                                            style="transform: translate({{ $backText['position']['x'] ?? 0 }}px, {{ $backText['position']['y'] ?? 0 }}px);">
                                            {{ $backText['text'] ?? 'Teks ' . ($index + 1) }}
                                        </span>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Position Controls for Back Cover -->
                    <div class="space-y-3">
                        <div class="flex items-center justify-center gap-2">
                            <button type="button" id="backZoomOutBtn"
                                class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 text-lg font-bold transition-colors"
                                title="Perkecil">−</button>
                            <input type="range" id="backZoomSlider" min="30" max="250"
                                value="{{ ($book->back_cover_scale ?? 1) * 100 }}"
                                class="w-24 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                            <button type="button" id="backZoomInBtn"
                                class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 text-lg font-bold transition-colors"
                                title="Perbesar">+</button>
                            <span id="backZoomLevel"
                                class="text-xs text-gray-500 ml-2 w-12">{{ round(($book->back_cover_scale ?? 1) * 100) }}%</span>
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
                @php
                    $coverPos = $book->cover_position;
                    if (is_string($coverPos)) {
                        $coverPos = json_decode($coverPos, true) ?? ['x' => 0, 'y' => 0];
                    }
                    $coverPos = $coverPos ?: ['x' => 0, 'y' => 0];

                    $titlePos = $book->title_position;
                    if (is_string($titlePos)) {
                        $titlePos = json_decode($titlePos, true) ?? ['x' => 0, 'y' => 0];
                    }
                    $titlePos = $titlePos ?: ['x' => 0, 'y' => 0];
                @endphp
                <input type="hidden" name="cover_position" id="coverPosition" value='{{ json_encode($coverPos) }}'>
                <input type="hidden" name="cover_scale" id="coverScale" value="{{ $book->cover_scale ?? 1 }}">
                <input type="hidden" name="title_position" id="titlePosition" value='{{ json_encode($titlePos) }}'>
                <input type="hidden" name="cover_texts" id="coverTexts"
                    value='{{ json_encode($book->cover_texts ?? []) }}'>
                @php
                    $backCoverPos = $book->back_cover_position ?? ['x' => 0, 'y' => 0];
                    if (is_string($backCoverPos)) {
                        $backCoverPos = json_decode($backCoverPos, true) ?? ['x' => 0, 'y' => 0];
                    }
                    $backTitlePos = $book->back_title_position ?? ['x' => 0, 'y' => 0];
                    if (is_string($backTitlePos)) {
                        $backTitlePos = json_decode($backTitlePos, true) ?? ['x' => 0, 'y' => 0];
                    }
                @endphp
                <input type="hidden" name="back_cover_position" id="backCoverPosition"
                    value='{{ json_encode($backCoverPos) }}'>
                <input type="hidden" name="back_cover_scale" id="backCoverScale"
                    value="{{ $book->back_cover_scale ?? 1 }}">
                <input type="hidden" name="back_title_position" id="backTitlePosition"
                    value='{{ json_encode($backTitlePos) }}'>
                <input type="hidden" name="back_cover_texts" id="backCoverTexts"
                    value='{{ json_encode($book->back_cover_texts ?? []) }}'>
            </div>
        </div>
    </form>

    @push('scripts')
        <script>
            @php
                $initCoverPos = ['x' => 0, 'y' => 0];
                if ($book->cover_position) {
                    if (is_array($book->cover_position)) {
                        $initCoverPos = $book->cover_position;
                    } else {
                        $decoded = json_decode($book->cover_position, true);
                        if (is_array($decoded)) {
                            $initCoverPos = $decoded;
                        }
                    }
                }

                $initTitlePos = ['x' => 0, 'y' => 0];
                if ($book->title_position) {
                    if (is_array($book->title_position)) {
                        $initTitlePos = $book->title_position;
                    } else {
                        $decoded = json_decode($book->title_position, true);
                        if (is_array($decoded)) {
                            $initTitlePos = $decoded;
                        }
                    }
                }

                $initCoverTexts = [];
                if ($book->cover_texts) {
                    if (is_string($book->cover_texts)) {
                        $decoded = json_decode($book->cover_texts, true);
                        $initCoverTexts = is_array($decoded) ? $decoded : [];
                    } elseif (is_array($book->cover_texts)) {
                        $initCoverTexts = $book->cover_texts;
                    }
                }

                $initBackCoverPos = ['x' => 0, 'y' => 0];
                if ($book->back_cover_position) {
                    if (is_array($book->back_cover_position)) {
                        $initBackCoverPos = $book->back_cover_position;
                    } else {
                        $decoded = json_decode($book->back_cover_position, true);
                        if (is_array($decoded)) {
                            $initBackCoverPos = $decoded;
                        }
                    }
                }

                $initBackTitlePos = ['x' => 0, 'y' => 0];
                if (isset($book->back_title_position) && $book->back_title_position) {
                    if (is_array($book->back_title_position)) {
                        $initBackTitlePos = $book->back_title_position;
                    } else {
                        $decoded = json_decode($book->back_title_position, true);
                        if (is_array($decoded)) {
                            $initBackTitlePos = $decoded;
                        }
                    }
                }

                $initBackCoverTexts = [];
                if ($book->back_cover_texts) {
                    if (is_string($book->back_cover_texts)) {
                        $decoded = json_decode($book->back_cover_texts, true);
                        $initBackCoverTexts = is_array($decoded) ? $decoded : [];
                    } elseif (is_array($book->back_cover_texts)) {
                        $initBackCoverTexts = $book->back_cover_texts;
                    }
                }
            @endphp
            window.bookEditorConfig = {
                coverX: {{ $initCoverPos['x'] ?? 0 }},
                coverY: {{ $initCoverPos['y'] ?? 0 }},
                coverScale: {{ is_numeric($book->cover_scale ?? 1) ? $book->cover_scale ?? 1 : 1 }},
                titleX: {{ $initTitlePos['x'] ?? 0 }},
                titleY: {{ $initTitlePos['y'] ?? 0 }},
                additionalTexts: {!! json_encode(
                    array_values(
                        array_map(
                            function ($i, $ct) {
                                return [
                                    'id' => $i,
                                    'text' => $ct['text'] ?? '',
                                    'position' => ['x' => $ct['position']['x'] ?? 0, 'y' => $ct['position']['y'] ?? 0],
                                ];
                            },
                            array_keys($initCoverTexts),
                            $initCoverTexts,
                        ),
                    ),
                ) !!},
                backCoverX: {{ $initBackCoverPos['x'] ?? 0 }},
                backCoverY: {{ $initBackCoverPos['y'] ?? 0 }},
                backCoverScale: {{ is_numeric($book->back_cover_scale ?? 1) ? $book->back_cover_scale ?? 1 : 1 }},
                backTitleX: {{ $initBackTitlePos['x'] ?? 0 }},
                backTitleY: {{ $initBackTitlePos['y'] ?? 0 }},
                backAdditionalTexts: {!! json_encode(
                    array_values(
                        array_map(
                            function ($i, $bt) {
                                return [
                                    'id' => $i,
                                    'text' => $bt['text'] ?? '',
                                    'position' => ['x' => $bt['position']['x'] ?? 0, 'y' => $bt['position']['y'] ?? 0],
                                ];
                            },
                            array_keys($initBackCoverTexts),
                            $initBackCoverTexts,
                        ),
                    ),
                ) !!}
            };
        </script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
        <script>
            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

            document.getElementById('pdfInput').addEventListener('change', async function(e) {
                const file = e.target.files[0];
                if (!file || file.type !== 'application/pdf') return;

                const info = document.getElementById('pdfInfo');
                const totalInput = document.getElementById('totalPagesInput');
                
                info.classList.remove('hidden');
                
                try {
                    const arrayBuffer = await file.arrayBuffer();
                    const pdf = await pdfjsLib.getDocument({data: arrayBuffer}).promise;
                    totalInput.value = pdf.numPages;
                    
                    // Show success feedback
                    const infoText = info.querySelector('p');
                    infoText.classList.remove('text-blue-600');
                    infoText.classList.add('text-green-600');
                    infoText.innerHTML = `<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg> {{ __('cms.virtual_books.pdf_success', ['count' => '${pdf.numPages}']) }}`;
                } catch (err) {
                    console.error('PDF error:', err);
                    info.querySelector('p').innerText = '{{ __('cms.virtual_books.pdf_failed') }}';
                    info.querySelector('p').classList.add('text-red-600');
                }
            });
        </script>
        <script src="{{ asset('js/cms/features/virtual_books/book-cover-editor.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var editorDesc = new RichTextEditor("#div_editor_desc", {
                    base_url: '/cms_rte',
                    toolbar: [
                        [{ 'header': [1, 2, 3, false] }],
                        [{ 'font': [] }, { 'size': [] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'color': [] }, { 'background': [] }],
                        [{ 'align': [] }],
                        [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                        ['link', 'image'],
                        ['clean'],
                    ],
                    editorBodyCssClass: 'rte-content-body',
                    file_upload_handler: function(file, callback, errorCallback) {
                        var formData = new FormData();
                        formData.append('file', file);
                        formData.append('_token', '{{ csrf_token() }}');

                        fetch('{{ route("cms.settings.rte.upload") }}', {
                            method: 'POST',
                            body: formData,
                        })
                        .then(response => {
                            if (!response.ok) throw new Error('Upload gagal.');
                            return response.json();
                        })
                        .then(result => { callback(result.url); })
                        .catch(error => {
                            console.error('Error saat upload:', error);
                            alert('{{ __('cms.virtual_books.upload_failed') }}');
                            if (errorCallback) errorCallback(error);
                        });
                    }
                });

                document.getElementById('bookForm').addEventListener('submit', function() {
                    var html = editorDesc.getHTMLCode();
                    document.getElementById('book_description').value = html;
                });
            });
        </script>
    @endpush
@endsection
