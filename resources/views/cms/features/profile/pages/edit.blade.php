@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/cms/profile/profile.css') }}">
    <link rel="stylesheet" href="{{ asset('richtexteditor/runtime/guest_richtexteditor_content.css') }}">
@endpush

@push('scripts')
    {{-- RTE scripts are loaded globally via layouts/app.blade.php --}}
@endpush

@section('breadcrumb_items')
    <span class="text-gray-400">CMS</span>
    <span class="text-gray-300">/</span>
    <a href="{{ route('cms.features.index') }}"
        class="text-gray-400 hover:text-gray-600 transition-colors">{{ __('cms.features.title') }}</a>
    <span class="text-gray-300">/</span>
    <a href="{{ route('cms.features.show', $feature) }}"
        class="text-gray-400 hover:text-gray-600 transition-colors">{{ $feature->name }}</a>
    <span class="text-gray-300">/</span>
    <a href="{{ route('cms.features.profile.index', [$feature, $sub]) }}"
        class="text-gray-400 hover:text-gray-600 transition-colors">{{ $sub->name }}</a>
@endsection
@section('breadcrumb_active', 'Edit Halaman')

@section('content')
    <div class="px-4 py-6 max-w-7xl mx-auto sm:px-6 lg:px-8" x-data="profilePageForm()">

        {{-- Header --}}
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('cms.features.profile.index', [$feature, $sub]) }}"
                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-white transition-colors shadow-sm"
                style="background-color: #818284;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Edit Halaman</h1>
                <p class="text-sm text-gray-500">{{ $page->title }}</p>
            </div>
        </div>

        {{-- Flash --}}
        @if (session('success'))
            <div
                class="mb-4 bg-green-50 border border-green-200 text-green-700 px-5 py-3 rounded-xl text-sm flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- Main Grid: Form (Left) + Preview (Right) --}}
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            {{-- Left Column: Form (2 columns) --}}
            <div class="lg:col-span-2">
                <form action="{{ route('cms.features.profile.pages.update', [$feature, $sub, $page]) }}" method="POST"
                    enctype="multipart/form-data" id="pageForm" class="space-y-6">
                    @csrf
                    @method('PUT')

                    {{-- Page Type + Title Row --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Tipe Halaman
                                </label>
                                <select name="type" x-model="pageType" @change="onTypeChange()"
                                    class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                                    <option value="tugas_fungsi" {{ $page->type === 'tugas_fungsi' ? 'selected' : '' }}>
                                        Tugas dan
                                        Fungsi</option>
                                    <option value="struktur_image"
                                        {{ $page->type === 'struktur_image' ? 'selected' : '' }}>
                                        Struktur Organisasi</option>
                                    <option value="sdm_chart" {{ $page->type === 'sdm_chart' ? 'selected' : '' }}>SDM
                                        (Grafik)
                                    </option>
                                </select>
                                <p class="mt-1 text-xs text-gray-400">Pilih tipe sesuai konten yang akan ditampilkan</p>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Judul Halaman
                                </label>
                                <input type="text" name="title" x-model="title" value="{{ $page->title }}"
                                    class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>

                    {{-- Description (RTE) --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Deskripsi / Konten
                        </label>
                        <div class="rte-wrapper">
                            <div id="div_editor1" style="min-width: 100%;">
                                {!! old('description', $page->description ?? '') !!}
                            </div>
                        </div>
                        <input type="hidden" name="description" id="description_input">
                        <p class="mt-2 text-xs text-gray-400">Format teks menggunakan Rich Text Editor.</p>
                    </div>

                    {{-- Type-specific fields --}}

                    {{-- Link fields (Default / Tugas Fungsi) --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6"
                        x-show="pageType === 'tugas_fungsi'" x-transition>
                        <h3 class="text-sm font-semibold text-gray-700 mb-4">Pengaturan Tautan</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Teks Tautan
                                </label>
                                <input type="text" name="link_text" x-model="linkText"
                                    value="{{ $page->link_text ?? '' }}" placeholder="Contoh: Pelajari Lebih Lanjut"
                                    class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                    URL Tautan
                                </label>
                                <input type="url" name="link_url" x-model="linkUrl" value="{{ $page->link_url ?? '' }}"
                                    placeholder="https://..."
                                    class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>

                    {{-- Subtitle (SDM) --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" x-show="pageType === 'sdm_chart'"
                        x-transition>
                        <h3 class="text-sm font-semibold text-gray-700 mb-4">Sub-judul</h3>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                Judul Tambahan
                            </label>
                            <input type="text" name="subtitle" x-model="subtitle" value="{{ $page->subtitle ?? '' }}"
                                placeholder="Contoh: Grafik Jumlah Pegawai Berdasarkan Usia"
                                class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    {{-- Chart (SDM) - Dynamic Field Selection --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" x-show="pageType === 'sdm_chart'"
                        x-transition>
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-sm font-semibold text-gray-700">Grafik SDM</h3>
                                <p class="text-xs text-gray-400 mt-0.5">Pilih data dan tipe grafik yang akan ditampilkan
                                </p>
                            </div>
                        </div>

                        {{-- Field Selection --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Data yang Akan
                                Ditampilkan:</label>
                            <div class="flex gap-3">
                                <select x-model="selectedField"
                                    class="flex-1 px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                                    <option value="">-- Pilih Field Data --</option>
                                    <template x-for="(label, field) in availableFields" :key="field">
                                        <option :value="field" x-text="label"></option>
                                    </template>
                                </select>
                                <button type="button" @click="addField()" :disabled="!selectedField"
                                    class="px-4 py-2.5 bg-gray-800 hover:bg-gray-900 disabled:opacity-50 text-white text-sm font-medium rounded-lg transition-colors">
                                    Tambah
                                </button>
                            </div>
                            <p class="text-xs text-gray-400 mt-1">Pilih field data untuk menambahkan grafik. Anda dapat
                                menambahkan
                                beberapa field.</p>
                        </div>

                        {{-- Chart Config List --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Konfigurasi Grafik:</label>
                            <div id="chart-config-list" class="space-y-3">
                                <p class="text-xs text-gray-400 py-2">Pilih field data di atas untuk menambahkan grafik</p>
                            </div>
                        </div>

                        {{-- Generate Button --}}
                        <button type="button" @click="generateChart()" :disabled="isGeneratingChart"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-[#174E93] hover:bg-blue-800 disabled:opacity-50 text-white text-sm font-semibold rounded-lg transition-colors">
                            <template x-if="!isGeneratingChart">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                    </path>
                                </svg>
                            </template>
                            <template x-if="isGeneratingChart">
                                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z">
                                    </path>
                                </svg>
                            </template>
                            Generate Grafik
                        </button>

                        <input type="hidden" name="chart_data" id="chart_data_input"
                            value="{{ $page->chart_data ? json_encode($page->chart_data) : '' }}">
                        <div id="chart_preview"
                            class="chart-preview-box min-h-[150px] flex items-center justify-center mt-4">
                            <p class="text-xs text-gray-400">Pilih field data dan tipe grafik, lalu klik "Generate Grafik"
                            </p>
                        </div>
                    </div>

                    {{-- Logo (Struktur Organisasi) --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6"
                        x-show="pageType === 'struktur_image'" x-transition>
                        <h3 class="text-sm font-semibold text-gray-700 mb-4">Logo / Gambar</h3>
                        <div id="logo-upload-area" class="{{ $page->logo_path ? 'hidden' : 'image-upload-box' }}"
                            onclick="document.getElementById('logo_input').click()">
                            <input type="file" name="logo" id="logo_input" accept="image/png,image/webp"
                                class="hidden" onchange="previewLogo(this)">
                            <svg class="w-10 h-10 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <p class="text-xs text-gray-500">Klik untuk upload logo (PNG atau WebP)</p>
                        </div>
                        <div id="logo_preview" class="{{ $page->logo_path ? '' : 'hidden' }} image-upload-box mt-3">
                            <img id="logo_preview_img"
                                src="{{ $page->logo_path ? asset('storage/' . $page->logo_path) : '' }}"
                                class="max-h-24 mx-auto">
                            <button type="button" onclick="removeLogo()"
                                class="mt-2 text-xs text-red-500 hover:text-red-700">Hapus Logo</button>
                        </div>
                        <label class="flex items-center gap-2 mt-2 cursor-pointer">
                            <input type="checkbox" name="remove_logo" value="1"
                                class="rounded border-gray-300 text-red-500">
                            <span class="text-xs text-red-500">Hapus logo saat ini</span>
                        </label>
                    </div>

                    {{-- Images (Gambar) --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                        <h3 class="text-sm font-semibold text-gray-700 mb-1">Gambar Pendukung</h3>
                        <p class="text-xs text-gray-400 mb-3">Drag untuk ubah posisi focal point atau klik Posisi untuk
                            preset. Max 10MB per file.
                        </p>

                        <div id="existing_images_container"></div>
                        <input type="hidden" name="removed_images" id="removed_images_input">
                        <input type="hidden" name="image_positions" id="image_positions_input" value="{}">

                        <div id="gambar-previews" class="mb-3"></div>

                        <div class="relative">
                            <input type="file" name="gambar_files" multiple accept="image/jpeg,image/png,image/webp"
                                @change="handleGambarChange($event)"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                data-max-file-size="10485760">
                            <div
                                class="w-full px-3 py-2.5 border-2 border-dashed border-gray-200 rounded-lg text-center bg-gray-50 hover:bg-gray-100 hover:border-blue-400 transition-all group cursor-pointer">
                                <svg class="w-5 h-5 mx-auto text-gray-400 group-hover:text-blue-500 transition-colors mb-0.5"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4">
                                    </path>
                                </svg>
                                <span class="text-xs font-medium text-gray-600">Klik atau drag untuk upload gambar</span>
                            </div>
                        </div>
                    </div>

                    {{-- Order --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Urutan <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="order" min="0" value="{{ $page->order }}" required
                            class="w-32 px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-end gap-3 pb-4">
                        <a href="{{ route('cms.features.profile.index', [$feature, $sub]) }}"
                            class="px-5 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                            Batal
                        </a>
                        <button type="submit" id="submitBtn"
                            class="px-5 py-2.5 text-sm font-semibold text-white bg-[#174E93] hover:bg-blue-800 rounded-lg transition-colors">
                            Simpan
                        </button>
                    </div>

                </form>
            </div>

            {{-- Right Column: Live Preview (wider) --}}
            <div class="lg:col-span-2">
                <div class="sticky top-6">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-sm font-semibold text-gray-700 mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            Preview Halaman
                        </h3>
                        <p class="text-xs text-gray-500 mb-3 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            Anda bisa drag gambar untuk mengubah posisinya atau ubah focal point
                        </p>


                        <div class="border border-gray-300 rounded-lg bg-white overflow-y-auto overflow-x-auto"
                            style="min-height: 380px; max-height: 380px;">
                            <div id="preview-wrapper" style="display: block; width: 1350px; padding: 0;">
                                <div id="preview-container"
                                    style="background: transparent; width: 88%; margin: 0 1.5rem;">
                                    {{-- Preview content akan diisi oleh JavaScript --}}
                                </div>
                            </div>
                        </div>

                        <p class="text-xs text-gray-400 mt-3 italic">Preview otomatis terupdate saat Anda mengedit</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section Management --}}
        <div class="mt-8">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Section Halaman</h2>
                    <p class="text-sm text-gray-500 mt-0.5">Kelola sub-konten atau section untuk halaman ini</p>
                </div>
                <button type="button" @click="openAddSection()"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white text-sm font-semibold rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Section
                </button>
            </div>

            {{-- Sections List --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="divide-y divide-gray-50">
                    @forelse($page->sections->sortBy('order') as $index => $section)
                        <div class="p-5 hover:bg-gray-50/50 transition-colors">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-3 mb-2">
                                        <span
                                            class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-blue-100 text-blue-700 text-xs font-bold">{{ $index + 1 }}</span>
                                        <h3 class="text-sm font-semibold text-gray-800">{{ $section->title }}</h3>
                                        <span class="text-xs text-gray-400">Urutan: {{ $section->order }}</span>
                                    </div>
                                    @if ($section->description)
                                        <p class="text-sm text-gray-600 mt-1 line-clamp-2">
                                            {{ \Illuminate\Support\Str::limit(strip_tags($section->description), 120) }}
                                        </p>
                                    @endif
                                    @if ($section->images && count($section->images))
                                        <div class="flex flex-wrap gap-2 mt-3">
                                            @foreach ($section->images as $imgIndex => $img)
                                                <img src="{{ asset('storage/' . $img) }}" alt=""
                                                    class="w-12 h-12 object-cover rounded-lg border border-gray-200 cursor-pointer"
                                                    style="object-position: {{ isset($section->image_positions[$imgIndex]) ? $section->image_positions[$imgIndex] : 'center' }}">
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <button
                                        @click="openEditSection({{ $section->id }}, '{{ addslashes($section->title) }}', `{{ addslashes($section->description ?? '') }}`, {{ $section->order }})"
                                        class="inline-flex items-center justify-center w-8 h-8 bg-yellow-400 hover:bg-yellow-500 text-white rounded-md transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                    </button>
                                    <button
                                        @click="openDeleteSection({{ $section->id }}, '{{ addslashes($section->title) }}')"
                                        class="inline-flex items-center justify-center w-8 h-8 bg-red-500 hover:bg-red-600 text-white rounded-md transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-12 text-center">
                            <svg class="w-12 h-12 mx-auto text-gray-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                            <p class="text-gray-400 text-sm mt-3">Belum ada section. Klik "Tambah Section" untuk
                                menambahkan.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- ===== ADD SECTION MODAL ===== --}}
        <div x-show="sectionModal.open" x-cloak class="fixed inset-0 flex items-center justify-center p-4"
            style="z-index: 9999;" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
            <div class="absolute inset-0 bg-black/60" @click="sectionModal.open = false"
                style="position: fixed; top: 0; right: 0; bottom: 0; left: 0;"></div>
            <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-md z-[9999] max-h-[90vh] overflow-y-auto"
                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100">
                <div
                    class="flex items-center justify-between px-6 py-4 border-b border-gray-100 sticky top-0 bg-white z-10">
                    <h3 class="text-base font-semibold text-gray-800">Tambah Section</h3>
                    <button @click="sectionModal.open = false"
                        class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12">
                        </svg>
                    </button>
                </div>
                <form action="{{ route('cms.features.profile.sections.store', [$feature, $sub, $page]) }}" method="POST"
                    enctype="multipart/form-data" @submit.prevent="submitSectionForm($event, 'add')"
                    class="px-6 py-5 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Judul Section <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="title" required placeholder="Contoh: Tugas Pokok"
                            class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Deskripsi
                        </label>
                        <textarea name="description" rows="4" placeholder="Deskripsi section..."
                            class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-y"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Urutan <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="order" min="0"
                            value="{{ ($page->sections->count() ?? 0) + 1 }}" required
                            class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>
                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" @click="sectionModal.open = false"
                            class="px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Batal</button>
                        <button type="submit"
                            class="px-4 py-2.5 text-sm font-semibold text-white bg-[#174E93] hover:bg-blue-800 rounded-lg transition-colors">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ===== EDIT SECTION MODAL ===== --}}
        <div x-show="editSectionModal.open" x-cloak class="fixed inset-0 flex items-center justify-center p-4"
            style="z-index: 9999;" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
            <div class="absolute inset-0 bg-black/60" @click="editSectionModal.open = false"
                style="position: fixed; top: 0; right: 0; bottom: 0; left: 0;"></div>
            <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-md z-[9999] max-h-[90vh] overflow-y-auto"
                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100">
                <div
                    class="flex items-center justify-between px-6 py-4 border-b border-gray-100 sticky top-0 bg-white z-10">
                    <h3 class="text-base font-semibold text-gray-800">Edit Section</h3>
                    <button @click="editSectionModal.open = false"
                        class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12">
                        </svg>
                    </button>
                </div>
                <form
                    :action="`/cms/features/{{ $feature->id }}/profile/{{ $sub->id }}/{{ $page->id }}/sections/${editSectionModal.id}`"
                    method="POST" enctype="multipart/form-data" @submit.prevent="submitSectionForm($event, 'edit')"
                    class="px-6 py-5 space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Judul Section <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="title" x-model="editSectionModal.title" required
                            class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Deskripsi
                        </label>
                        <textarea name="description" rows="4" x-model="editSectionModal.description"
                            class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-y"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Urutan <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="order" x-model="editSectionModal.order" min="0" required
                            class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    </div>
                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" @click="editSectionModal.open = false"
                            class="px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Batal</button>
                        <button type="submit"
                            class="px-4 py-2.5 text-sm font-semibold text-white bg-[#174E93] hover:bg-blue-800 rounded-lg transition-colors">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ===== DELETE SECTION MODAL ===== --}}
        <div x-show="deleteSectionModal.open" x-cloak class="fixed inset-0 flex items-center justify-center p-4"
            style="z-index: 9999;" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
            <div class="absolute inset-0 bg-black/60" @click="deleteSectionModal.open = false"
                style="position: fixed; top: 0; right: 0; bottom: 0; left: 0;"></div>
            <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-sm z-[9999] p-6"
                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100">
                <div class="flex flex-col items-center text-center gap-4">
                    <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center">
                        <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-800">Hapus Section?</h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Anda yakin ingin menghapus <strong x-text="deleteSectionModal.name"
                                class="text-gray-700"></strong>?
                        </p>
                    </div>
                    <div class="flex items-center gap-3 w-full">
                        <button @click="deleteSectionModal.open = false"
                            class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Batal</button>
                        <button type="button" @click="submitDeleteSection()"
                            class="flex-1 px-4 py-2.5 text-sm font-semibold text-white bg-red-500 hover:bg-red-600 rounded-lg transition-colors">Hapus</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    {{-- Pass data to JS --}}
    <script>
        console.group('[PHP-DATA] Server Data');
        console.log('Pages image_positions raw:', {!! json_encode($page->image_positions) !!});
        console.groupEnd();

        @php

            if ($page->images && is_array($page->images)) {
                foreach ($page->images as $index => $img) {
                    $cleanPath = $img;
                    if (strpos($img, 'storage/') === 0) {
                        $cleanPath = substr($img, 8);
                    }
                    $imageUrl = asset('storage/' . $cleanPath);

                    $posData = $page->image_positions[$index] ?? null;
                    $x = 50;
                    $y = 50;
                    $width = 200;
                    $height = 150;
                    $offsetX = 0;
                    $offsetY = 0;

                    if (is_array($posData)) {
                        $x = isset($posData['position']) ? floatval(explode(' ', $posData['position'])[0]) : 50;
                        $y = isset($posData['position']) ? floatval(explode(' ', $posData['position'])[1]) : 50;
                        $width = isset($posData['width']) ? intval($posData['width']) : 200;
                        $height = isset($posData['height']) ? intval($posData['height']) : 150;
                        $offsetX = isset($posData['offsetX']) ? intval($posData['offsetX']) : 0;
                        $offsetY = isset($posData['offsetY']) ? intval($posData['offsetY']) : 0;
                    }

                    $existingImagesData[] = [
                        'path' => $cleanPath,
                        'url' => $imageUrl,
                        'x' => $x,
                        'y' => $y,
                        'width' => $width,
                        'height' => $height,
                        'offsetX' => $offsetX,
                        'offsetY' => $offsetY,
                    ];
                }
            }
        @endphp

        window.existingImages = {!! json_encode($existingImagesData) !!};

        console.group('[IMG] Existing Images Debug');
        console.log('Total images:', window.existingImages.length);
        if (window.existingImages.length > 0) {
            console.table(window.existingImages.map(function(img, idx) {
                return {
                    'Index': idx,
                    'Path': img.path,
                    'URL': img.url,
                    'Position': img.x + '%, ' + img.y + '%',
                    'Dimensions': img.width + 'x' + img.height,
                    'Offset': img.offsetX + ', ' + img.offsetY
                };
            }));
            // Try to fetch first image to check if URL is accessible
            if (window.existingImages.length > 0) {
                const firstImg = window.existingImages[0];
                fetch(firstImg.url, {
                        method: 'HEAD'
                    })
                    .then(r => {
                        console.log('[IMG CHECK] First image URL check - Status:', r.status, r.ok ? 'OK' : 'NOT OK');
                        if (!r.ok) console.error('[IMG CHECK] HTTP Status', r.status, 'for:', firstImg.url);
                    })
                    .catch(e => console.error('[IMG CHECK] Failed to fetch:', firstImg.url, 'Error:', e.message));
            }
        } else {
            console.log('No existing images found');
        }
        console.groupEnd();

        window.featureId = {{ $feature->id }};
        window.chartGenerateUrl = '{{ route('cms.features.profile.generate_chart', $feature) }}';
        window.dataFieldsUrl = '{{ route('cms.features.profile.data_fields', $feature) }}';
        window.sectionsCount = {{ $page->sections->count() ?? 0 }};
        window.rteUploadUrl = '{{ route('cms.settings.rte.upload') }}';
        window.csrfToken = '{{ csrf_token() }}';
        window.pageType = '{{ $page->type ?? 'tugas_fungsi' }}';
        window.pageTitle = @json($page->title ?? '');
        window.pageLinkText = @json($page->link_text ?? '');
        window.pageLinkUrl = @json($page->link_url ?? '');
        window.pageSubtitle = @json($page->subtitle ?? '');
        window.pageDescription = @json($page->description ?? '');
        window.sectionDeleteUrl =
            '{{ route('cms.features.profile.sections.destroy', [$feature, $sub, $page, '__SECTION_ID__']) }}';
    </script>
    <script
        src="{{ asset('js/cms/features/profile/edit.js?v=' . md5_file(public_path('js/cms/features/profile/edit.js'))) }}">
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('[RTE] DOMContentLoaded fired, initProfileEditForm exists:', typeof initProfileEditForm !==
                'undefined');
            if (typeof initProfileEditForm === 'function') {
                initProfileEditForm();
            }
        });
    </script>
@endpush
