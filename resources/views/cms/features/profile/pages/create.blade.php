@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/cms/profile/profile.css') }}">
    <link rel="stylesheet" href="{{ asset('richtexteditor/runtime/guest_richtexteditor_content.css') }}">
    <style>
        /* Mirror guest page profile.blade.php styles for preview pane */
        #preview-container.profile-section-desc {
            color: #414141 !important;
            line-height: 1.6 !important;
            font-size: 14px !important;
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif !important;
            width: 100%;
            max-width: calc((min(1170px, 94vw) - 2rem) / 2) !important;
            padding: 0;
            margin: 0;
        }
        #preview-container.profile-section-desc p { margin-bottom: 1rem !important; }
        #preview-container.profile-section-desc ul { list-style-type: disc !important; margin: 1em 0 !important; padding-left: 1.5rem !important; }
        #preview-container.profile-section-desc ol { list-style-type: decimal !important; margin: 1em 0 !important; padding-left: 1.5rem !important; }
        #preview-container.profile-section-desc li { margin: 0.25em 0 !important; display: list-item !important; }
        #preview-container.profile-section-desc a:hover { text-decoration: underline !important; color: #009ac9 !important; }
        #preview-container.profile-section-desc h1 { font-size: 2em !important; font-weight: bold !important; margin: 0.67em 0 !important; color: #1e293b !important; }
        #preview-container.profile-section-desc h2 { font-size: 1.5em !important; font-weight: bold !important; margin: 0.83em 0 !important; color: #1e293b !important; }
        #preview-container.profile-section-desc h3 { font-size: 1.17em !important; font-weight: bold !important; margin: 1em 0 !important; color: #1e293b !important; }
        #preview-container.profile-section-desc h4 { font-size: 1em !important; font-weight: bold !important; margin: 1.33em 0 !important; color: #1e293b !important; }
        #preview-container.profile-section-desc h5 { font-size: 0.83em !important; font-weight: bold !important; margin: 1.67em 0 !important; color: #1e293b !important; }
        #preview-container.profile-section-desc h6 { font-size: 0.67em !important; font-weight: bold !important; margin: 2.33em 0 !important; color: #1e293b !important; }
        #preview-container.profile-section-desc table { border-collapse: collapse !important; margin: 1rem 0 !important; width: 100%; }
        #preview-container.profile-section-desc table td,
        #preview-container.profile-section-desc table th { padding: 0.75rem !important; border: 1px solid #e5e7eb !important; }
        #preview-container.profile-section-desc img { max-width: 100%; height: auto !important; margin: 1rem 0 !important; }

        /* Title & Section Parity */
        #preview-container .profile-section-title {
            font-size: 1.5rem !important;
            font-weight: 700 !important;
            color: #1e293b !important;
            margin-bottom: 0.5rem !important;
            font-family: 'Montserrat', sans-serif !important;
        }
        #preview-container .profile-section-subtitle {
            font-size: 1.1rem !important;
            color: #174E93 !important;
            font-weight: 600 !important;
            margin-bottom: 1.5rem !important;
            font-family: 'Montserrat', sans-serif !important;
        }
        #preview-container .section-block { margin-bottom: 1.5rem !important; }
        #preview-container .section-block h3 {
            font-size: 1.1rem !important;
            font-weight: 600 !important;
            color: #1e293b !important;
            margin-bottom: 0.5rem !important;
            font-family: 'Montserrat', sans-serif !important;
        }
        #preview-container .section-block p {
            color: #475569 !important;
            line-height: 1.75 !important;
            margin-bottom: 0.75rem !important;
            font-family: 'Montserrat', sans-serif !important;
        }
        #preview-container .page-link-btn {
            display: inline-flex !important;
            align-items: center !important;
            gap: 0.5rem !important;
            margin-top: 1.5rem !important;
            padding: 0.75rem 1.5rem !important;
            background: #174E93 !important;
            color: white !important;
            border-radius: 0.5rem !important;
            font-weight: 600 !important;
            text-decoration: none !important;
            font-size: 0.9rem !important;
            font-family: 'Montserrat', sans-serif !important;
        }

        /* Prevent text column from stretching - CRITICAL FIX */
        #preview-container .preview-text-col {
            overflow: hidden;
            word-break: break-word;
            overflow-wrap: break-word;
            hyphens: auto;
            min-width: 0;
            flex-shrink: 1;
        }

        /* Ensure grid layout preserves column structure */
        #preview-container [style*="grid-template-columns"] {
            align-items: start;
        }
    </style>
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
@section('breadcrumb_active', 'Tambah Halaman')

@section('content')
    {{-- PERBAIKAN: max-w-5xl diubah menjadi max-w-7xl agar lebarnya sama dengan Edit --}}
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
                <h1 class="text-2xl font-bold text-gray-800">Tambah Halaman</h1>
            </div>
        </div>

        {{-- Main Grid: Form (Left) + Preview (Right) --}}
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            {{-- Left Column: Form (2 columns) --}}
            <div class="lg:col-span-2">
                <form action="{{ route('cms.features.profile.pages.store', [$feature, $sub]) }}" method="POST"
                    enctype="multipart/form-data" id="pageForm" class="space-y-6">
                    @csrf

                    {{-- Page Type + Title Row --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Tipe Halaman <span class="text-red-500">*</span>
                                </label>
                                <select name="type" x-model="pageType" @change="onTypeChange()"
                                    class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                                    <option value="tugas_fungsi">Tugas dan Fungsi</option>
                                    <option value="struktur_image">Struktur Organisasi</option>
                                    <option value="sdm_chart">SDM (Grafik)</option>
                                </select>
                                <p class="mt-1 text-xs text-gray-400">Pilih tipe sesuai konten yang akan ditampilkan</p>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Judul Halaman
                                </label>
                                <input type="text" name="title" x-model="title"
                                    placeholder="Contoh: Tugas Pokok dan Fungsi"
                                    class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>

                    {{-- Description (RTE) - ALL TYPES --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Deskripsi / Konten
                        </label>
                        <div class="rte-wrapper">
                            <div id="div_editor1" style="min-width: 100%;">
                                {!! old('description', '') !!}
                            </div>
                        </div>
                        <input type="hidden" name="description" id="description_input">
                        <p class="mt-2 text-xs text-gray-400">Format teks menggunakan Rich Text Editor.</p>
                    </div>

                    {{-- Type-specific fields --}}

                    {{-- Link fields (Tugas Fungsi) --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6"
                        x-show="pageType === 'tugas_fungsi'" x-transition>

                        <h3 class="text-sm font-semibold text-gray-700 mb-4">Pengaturan Tautan</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Teks Tautan
                                </label>
                                <input type="text" name="link_text" x-model="linkText"
                                    placeholder="Contoh: Pelajari Lebih Lanjut"
                                    class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                    URL Tautan
                                </label>
                                <input type="url" name="link_url" x-model="linkUrl" placeholder="https://..."
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
                            <input type="text" name="subtitle" x-model="subtitle"
                                placeholder="Contoh: Grafik Jumlah Pegawai Berdasarkan Usia"
                                class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="mt-1 text-xs text-gray-400">Sub-judul yang akan ditampilkan di bawah judul utama</p>
                        </div>
                    </div>

                    {{-- Chart (SDM) - Dynamic Field Selection --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" x-show="pageType === 'sdm_chart'"
                        x-transition>
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-sm font-semibold text-gray-700">Grafik SDM</h3>
                                <p class="text-xs text-gray-400 mt-0.5">Pilih data dan tipe grafik yang akan ditampilkan</p>
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
                                menambahkan beberapa field.</p>
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
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                            </template>
                            Generate Grafik
                        </button>

                        <input type="hidden" name="chart_data" id="chart_data_input">
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
                        <div id="logo-upload-area" class="image-upload-box"
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
                        <div id="logo_preview" class="hidden image-upload-box mt-3">
                            <img id="logo_preview_img" src="" class="max-h-24 mx-auto">
                            <button type="button" onclick="removeLogo()"
                                class="mt-2 text-xs text-red-500 hover:text-red-700">Hapus Logo</button>
                        </div>
                    </div>

                    {{-- Images (Gambar section) - Disamakan struktur formnya dengan Edit --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                        <h3 class="text-sm font-semibold text-gray-700 mb-1">Gambar Pendukung</h3>
                        <p class="text-xs text-gray-400 mb-3">Drag untuk ubah posisi focal point atau klik Posisi untuk preset. Max 10MB per file.</p>

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
                                        d="M12 4v16m8-8H4"></path>
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
                        <input type="number" name="order" min="0" value="{{ ($pages->count() ?? 0) + 1 }}"
                            required
                            class="w-32 px-3.5 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="mt-1 text-xs text-gray-400">Halaman dengan urutan lebih kecil akan ditampilkan lebih dulu
                        </p>
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

            {{-- Right Column: Live Preview (Wider, disamakan dengan style dan div di Edit) --}}
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
                            style="min-height: 380px; max-height: 400px;">
                            <div id="preview-wrapper" style="display: block; width: 1140px; padding: 0 15px;">
                                <div id="preview-container" class="profile-section-desc"
                                    style="background: transparent; width: 100%; border: none; padding: 0;">
                                    <div style="color: #999; text-align: center; padding: 2rem; font-style: italic;">
                                        <p style="margin: 0; font-size: 13px;">Tambahkan konten dan/atau gambar untuk
                                            melihat preview</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <p class="text-xs text-gray-400 mt-3 italic">Preview otomatis terupdate saat Anda mengedit</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Info: Section management after save --}}
        <div class="bg-blue-50 border border-blue-100 rounded-xl p-5 mt-6">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <h4 class="text-sm font-semibold text-blue-800">Kelola Section Setelah Disimpan</h4>
                    <p class="text-sm text-blue-600 mt-1">Setelah halaman disimpan, Anda dapat mengelola section
                        (sub-konten) melalui halaman Edit.</p>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        window.featureId = {{ $feature->id }};
        window.chartGenerateUrl = '{{ route('cms.features.profile.generate_chart', $feature) }}';
        window.dataFieldsUrl = '{{ route('cms.features.profile.data_fields', $feature) }}';
        window.rteUploadUrl = '{{ route('cms.settings.rte.upload') }}';
        window.csrfToken = '{{ csrf_token() }}';
    </script>
    <script
        src="{{ asset('js/cms/features/profile/create.js?v=' . md5_file(public_path('js/cms/features/profile/create.js'))) }}">
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('[RTE] DOMContentLoaded fired, initProfileCreateForm exists:',
                typeof initProfileCreateForm !== 'undefined');
            if (typeof initProfileCreateForm === 'function') {
                initProfileCreateForm();
            }
        });
    </script>
@endpush
