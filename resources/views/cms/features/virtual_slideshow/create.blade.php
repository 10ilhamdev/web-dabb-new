@extends('layouts.app')

@section('breadcrumb_items')
    <span class="text-gray-400">CMS</span>
    <span class="text-gray-300">/</span>
    <a href="{{ route('cms.features.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">{{ __('cms.features.title') }}</a>
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
    <a href="{{ route('cms.features.show', $feature) }}" class="text-gray-400 hover:text-gray-600 transition-colors">{{ $feature->name }}</a>
@endsection
@section('breadcrumb_active', __('cms.virtual_slideshow.add_page'))

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('cms.features.slideshow.index', $feature) }}"
            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-white transition-colors shadow-sm" style="background-color: #818284;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ __('cms.virtual_slideshow.create_page_title') }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $feature->name }}</p>
        </div>
    </div>

    <form action="{{ route('cms.features.slideshow.pages.store', $feature) }}" method="POST" class="space-y-6" enctype="multipart/form-data">
        @csrf

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
            <h2 class="text-base font-semibold text-gray-800">{{ __('cms.virtual_slideshow.page_info') }}</h2>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('cms.virtual_slideshow.page_title_label') }} <span class="text-red-500">*</span></label>
                <input type="text" name="title" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="{{ __('cms.virtual_slideshow.page_title_placeholder') }}" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('cms.virtual_slideshow.page_desc_label') }}</label>
                <textarea name="description" rows="3" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="{{ __('cms.virtual_slideshow.page_desc_placeholder') }}"></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('cms.virtual_slideshow.page_order_label') }} <span class="text-red-500">*</span></label>
                <input type="number" name="order" min="0" value="{{ $nextOrder ?? 1 }}" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                <p class="text-xs text-gray-500 mt-1">{{ __('cms.virtual_slideshow.page_order_help') }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('cms.virtual_slideshow.page_thumbnail_label') }}</label>
                <div class="flex items-start gap-4">
                    <div class="flex-1">
                        <div class="relative">
                            <input type="file" name="thumbnail" id="thumbnail" accept="image/*" class="hidden" onchange="previewThumbnail(this)">
                            <label for="thumbnail" class="flex items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-[#174E93] transition-colors">
                                <div class="text-center">
                                    <svg class="w-8 h-8 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <p class="mt-1 text-xs text-gray-500">{{ __('cms.virtual_slideshow.upload_image_hint') }}</p>
                                </div>
                            </label>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ __('cms.virtual_slideshow.thumbnail_optional') }}</p>
                    </div>
                    <div id="thumbnailPreview" class="hidden relative w-24 h-24 rounded-lg overflow-hidden border border-gray-200">
                        <img src="" alt="Preview" class="w-full h-full object-cover">
                        <button type="button" onclick="document.getElementById('thumbnail').value=''; document.getElementById('thumbnailPreview').classList.add('hidden');" class="absolute top-1 right-1 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600 transition-colors">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script>
        function previewThumbnail(input) {
            const preview = document.getElementById("thumbnailPreview");
            const previewImg = preview.querySelector("img");

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.classList.remove("hidden");
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
        </script>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('cms.features.slideshow.index', $feature) }}"
                class="px-5 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                {{ __('cms.virtual_slideshow.cancel') }}
            </a>
            <button type="submit"
                class="px-6 py-2.5 text-sm font-semibold text-white bg-[#174E93] hover:bg-blue-800 rounded-lg transition-colors shadow-sm">
                {{ __('cms.virtual_slideshow.save_page') }}
            </button>
        </div>
    </form>
</div>
@endsection
