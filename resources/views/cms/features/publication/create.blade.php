@extends('layouts.app')

@section('breadcrumb_items')
    <span class="text-gray-400">CMS</span>
    <span class="text-gray-300">/</span>
    <a href="{{ route('cms.features.index') }}"
        class="text-gray-400 hover:text-gray-600 transition-colors">{{ __('cms.features.title') }}</a>
    <span class="text-gray-300">/</span>
    <a href="{{ route('cms.features.publication.index', $feature) }}"
        class="text-gray-400 hover:text-gray-600 transition-colors">{{ $feature->name }}</a>
@endsection
@section('breadcrumb_active', __('cms.publication.add_button'))

@section('content')
<div class="px-4 py-6 max-w-7xl mx-auto sm:px-6 lg:px-8" x-data="publicationForm('pengumuman')">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('cms.features.publication.index', $feature) }}"
            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-white transition-colors shadow-sm"
            style="background-color: #818284;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ __('cms.publication.create_title') }}</h1>
        </div>
    </div>

    <form action="{{ route('cms.features.publication.pages.store', $feature) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left: Main Info --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Type & Title --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.publication.label_type') }} <span class="text-red-500">*</span></label>
                            <select name="type" x-model="type" required
                                class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                                <option value="pengumuman">{{ __('cms.publication.type_announcement') }}</option>
                                <option value="berita">{{ __('cms.publication.type_news') }}</option>
                                <option value="galeri">{{ __('cms.publication.type_gallery') }}</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.publication.label_title') }} <span class="text-red-500">*</span></label>
                            <input type="text" name="title" required placeholder="{{ __('cms.publication.placeholder_title') }}"
                                class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                {{-- Description / Content --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" x-show="type !== 'pengumuman'" x-transition>
                    <label class="block text-sm font-medium text-gray-700 mb-3">{{ __('cms.publication.label_description') }}</label>
                    <div class="rte-wrapper">
                        <div id="div_editor1"></div>
                    </div>
                    <input type="hidden" name="description" id="description_input">
                </div>

                {{-- Gallery Section (x-show="type === 'galeri' || type === 'berita'") --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" x-show="type === 'galeri' || type === 'berita'" x-transition>
                    <label class="block text-sm font-medium text-gray-700 mb-3">{{ __('cms.publication.label_gallery') }}</label>
                    <div x-show="type === 'galeri'" class="mb-4 p-3 bg-blue-50 border border-blue-100 rounded-lg text-xs text-blue-700">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Halaman Galeri akan secara otomatis mengumpulkan seluruh media gambar dan video dari sistem. Media yang Anda tambahkan di bawah ini akan muncul sebagai prioritas di urutan awal.
                    </div>
                    <div class="border-2 border-dashed border-gray-200 rounded-xl p-8 text-center bg-gray-50 hover:bg-gray-100 transition-colors cursor-pointer relative group">
                        <input type="file" name="images[]" multiple accept="image/*,video/*"
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                            @change="handleFiles($event)">
                        <svg class="w-10 h-10 mx-auto text-gray-400 group-hover:text-blue-500 transition-colors mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p class="text-sm font-medium text-gray-600">{{ __('cms.publication.hint_gallery') }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ __('cms.publication.hint_gallery_sub') }}</p>
                    </div>
                    <div id="file-previews" class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4"></div>
                </div>

                {{-- Pengumuman File (x-show="type === 'pengumuman'") --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" x-show="type === 'pengumuman'" x-transition>
                    <label class="block text-sm font-medium text-gray-700 mb-3">{{ __('cms.publication.label_pdf') }}</label>
                    <div class="flex items-center gap-4">
                        <div class="flex-1">
                            <input type="file" name="extra_data[file]" accept="application/pdf"
                                class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-all">
                        </div>
                        <p class="text-xs text-gray-400 italic">{{ __('cms.publication.hint_pdf') }}</p>
                    </div>
                </div>
            </div>

            {{-- Right: Sidebar Info --}}
            <div class="space-y-6">
                {{-- Metadata --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">{{ __('cms.publication.sidebar_title') }}</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.publication.label_order') }} <span class="text-red-500">*</span></label>
                            <input type="number" name="order" value="{{ $nextOrder }}" required
                                class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.publication.label_date') }} <span class="text-red-500">*</span></label>
                            <input type="date" name="published_at" value="{{ date('Y-m-d') }}" required
                                class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div x-show="type === 'berita'">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.publication.label_subtitle') }}</label>
                            <textarea name="subtitle" rows="3" placeholder="{{ __('cms.publication.placeholder_subtitle') }}"
                                class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                    </div>
                </div>

                {{-- Action --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col gap-3">
                    <button type="submit" class="w-full py-3 px-4 bg-[#174E93] hover:bg-blue-800 text-white font-semibold rounded-xl transition-all shadow-sm">
                        {{ __('cms.publication.btn_save') }}
                    </button>
                    <a href="{{ route('cms.features.publication.index', $feature) }}"
                        class="w-full py-3 px-4 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl text-center transition-all">
                        {{ __('cms.publication.btn_cancel') }}
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    window.rteUploadUrl = '{{ route('cms.settings.rte.upload') }}';
</script>
<script src="{{ asset('js/cms/features/publication/create.js') }}"></script>
@endpush
