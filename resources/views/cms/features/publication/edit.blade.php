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
@section('breadcrumb_active', __('cms.publication.edit_title'))

@section('content')
<div class="px-4 py-6 max-w-7xl mx-auto sm:px-6 lg:px-8" x-data="publicationForm('{{ $publication->type }}')">

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
            <h1 class="text-2xl font-bold text-gray-800">{{ __('cms.publication.edit_title') }}</h1>
        </div>
    </div>

    <form action="{{ route('cms.features.publication.pages.update', [$feature, $publication]) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

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
                                <option value="pengumuman" {{ $publication->type === 'pengumuman' ? 'selected' : '' }}>{{ __('cms.publication.type_announcement') }}</option>
                                <option value="berita" {{ $publication->type === 'berita' ? 'selected' : '' }}>{{ __('cms.publication.type_news') }}</option>
                                <option value="galeri" {{ $publication->type === 'galeri' ? 'selected' : '' }}>{{ __('cms.publication.type_gallery') }}</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.publication.label_title') }} <span class="text-red-500">*</span></label>
                            <input type="text" name="title" value="{{ $publication->title }}" required placeholder="{{ __('cms.publication.placeholder_title') }}"
                                class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                {{-- Description / Content --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" x-show="type !== 'pengumuman'" x-transition>
                    <label class="block text-sm font-medium text-gray-700 mb-3">{{ __('cms.publication.label_description') }}</label>
                    <div class="rte-wrapper">
                        <div id="div_editor1">{!! $publication->description !!}</div>
                    </div>
                    <input type="hidden" name="description" id="description_input">
                </div>

                {{-- Gallery Section --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" x-show="type === 'galeri' || type === 'berita'" x-transition>
                    <label class="block text-sm font-medium text-gray-700 mb-3">{{ __('cms.publication.label_gallery') }}</label>
                    <div x-show="type === 'galeri'" class="mb-4 p-3 bg-blue-50 border border-blue-100 rounded-lg text-xs text-blue-700">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Halaman Galeri akan secara otomatis mengumpulkan seluruh media gambar dan video dari sistem. Media yang Anda tambahkan/simpan di bawah ini akan muncul sebagai prioritas di urutan awal.
                    </div>
                    
                    {{-- Existing Images --}}
                    @if($publication->images)
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                        @foreach($publication->images as $img)
                        <div class="relative group aspect-square rounded-lg overflow-hidden border border-gray-200 bg-gray-50">
                            @if(Str::endsWith($img, ['.mp4', '.webm', '.ogg']))
                                <video src="{{ asset('storage/' . $img) }}" class="w-full h-full object-cover"></video>
                            @else
                                <img src="{{ asset('storage/' . $img) }}" class="w-full h-full object-cover">
                            @endif
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <input type="hidden" name="existing_images[]" value="{{ $img }}">
                                <button type="button" onclick="this.closest('.relative').remove()" 
                                    class="bg-red-500 text-white p-2 rounded-full hover:bg-red-600 transition-colors shadow-lg"
                                    title="Hapus Media">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    <div class="border-2 border-dashed border-gray-200 rounded-xl p-8 text-center bg-gray-50 hover:bg-gray-100 transition-colors cursor-pointer relative group">
                        <input type="file" name="images[]" multiple accept="image/*,video/*"
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                            @change="handleFiles($event)">
                        <svg class="w-10 h-10 mx-auto text-gray-400 group-hover:text-blue-500 transition-colors mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p class="text-sm font-medium text-gray-600">{{ __('cms.publication.hint_gallery') }}</p>
                    </div>
                    <div id="file-previews" class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4"></div>
                </div>

                {{-- Pengumuman File --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" x-show="type === 'pengumuman'" x-transition>
                    <label class="block text-sm font-medium text-gray-700 mb-3">{{ __('cms.publication.label_pdf') }}</label>
                    
                    @if(isset($publication->extra_data['file']))
                    <div class="mb-4 p-3 bg-blue-50 border border-blue-100 rounded-lg flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <svg class="w-8 h-8 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"></path>
                            </svg>
                            <div>
                                <p class="text-sm font-semibold text-blue-800">{{ __('cms.publication.current_doc') }}</p>
                                <a href="{{ asset('storage/' . $publication->extra_data['file']) }}" target="_blank" class="text-xs text-blue-600 hover:underline">{{ __('cms.publication.view_pdf') }}</a>
                            </div>
                        </div>
                        <input type="hidden" name="extra_data[file]" value="{{ $publication->extra_data['file'] }}">
                    </div>
                    @endif

                    <div class="flex items-center gap-4">
                        <div class="flex-1">
                            <input type="file" name="extra_data[file]" accept="application/pdf"
                                class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-all">
                        </div>
                        <p class="text-xs text-gray-400 italic">{{ __('cms.publication.hint_pdf_edit') }}</p>
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
                            <input type="number" name="order" value="{{ $publication->order }}" required
                                class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.publication.label_date') }} <span class="text-red-500">*</span></label>
                            <input type="date" name="published_at" value="{{ $publication->published_at ? $publication->published_at->format('Y-m-d') : date('Y-m-d') }}" required
                                class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div x-show="type === 'berita'">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('cms.publication.label_subtitle') }}</label>
                            <textarea name="subtitle" rows="3" placeholder="{{ __('cms.publication.placeholder_subtitle') }}"
                                class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $publication->subtitle }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Action --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col gap-3">
                    <button type="submit" class="w-full py-3 px-4 bg-[#174E93] hover:bg-blue-800 text-white font-semibold rounded-xl transition-all shadow-sm">
                        {{ __('cms.publication.btn_update') }}
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
