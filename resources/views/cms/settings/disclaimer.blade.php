@extends('layouts.app')

@section('breadcrumb_items')
    <span class="text-gray-400">CMS</span>
@endsection
@section('breadcrumb_active', __('dashboard.disclaimer.title'))

@push('styles')
{{-- RTE styles are loaded globally via layouts/app.blade.php --}}
@endpush

@section('content')
<div class="px-4 py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('dashboard.disclaimer.title') }}</h1>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <form action="{{ route('cms.settings.disclaimer.update') }}" method="POST" class="p-6 space-y-8" id="disclaimerForm">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('dashboard.disclaimer.title_label') }}</label>
                <input type="text" name="disclaimer_title" value="{{ old('disclaimer_title', $settings['disclaimer_title'] ?? '') }}"
                    placeholder="{{ __('dashboard.disclaimer.title_placeholder') }}"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                <p class="mt-1.5 text-xs text-gray-400">{{ __('dashboard.disclaimer.title_help') }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('dashboard.disclaimer.content_label') }}</label>

                <div class="rte-wrapper">
                    <div id="div_editor1" style="min-width: 100%;">
                        {!! old('disclaimer_content', $settings['disclaimer_content'] ?? '<p style="margin-bottom: 0.5rem;"><strong>Disclaimer:</strong> Seluruh informasi, dokumen, dan arsip digital yang tersedia di situs web Depot Arsip Berkelanjutan Bandung (DABB) disajikan berdasarkan data saat informasi itu dibuat semata-mata untuk tujuan pelestarian sejarah, edukasi, dan informasi umum.</p><p style="margin-bottom: 1rem;">Dengan mengakses dan menggunakan situs web ini, Anda menyetujui ketentuan berikut:</p><p style="margin-bottom: 1rem;"><strong>Akurasi dan Konteks Historis:</strong> DABB berupaya semaksimal mungkin untuk menjaga keaslian visual dan tekstual dari setiap arsip yang didigitalkan. Namun, kami tidak memberikan jaminan mutlak atas kelengkapan, keakuratan, atau relevansi informasi di dalam dokumen tersebut dengan kondisi masa kini. Arsip historis mungkin memuat pandangan, bahasa, atau norma pada masanya yang tidak selalu mencerminkan nilai dan pandangan DABB saat ini.</p><p style="margin-bottom: 1rem;"><strong>Hak Cipta dan Penggunaan Publik:</strong> Sebagian besar materi yang ditampilkan mungkin dilindungi oleh hak cipta dari masing-masing pembuat, organisasi asal, atau pemilik sah sebelumnya. Pengunjung diperbolehkan menggunakan arsip untuk keperluan studi dan penelitian pribadi. Penggunaan, reproduksi, atau distribusi ulang untuk tujuan komersial tanpa izin tertulis yang sah sangat dilarang.</p><p style="margin-bottom: 0;"><strong>Batasan Tanggung Jawab:</strong> Segala tindakan atau keputusan yang diambil berdasarkan informasi dari arsip di situs web ini sepenuhnya merupakan risiko dan tanggung jawab pengguna. DABB dan seluruh pihak pengelola tidak bertanggung jawab atas segala bentuk kerugian, baik langsung maupun tidak langsung, yang timbul dari penggunaan atau ketidakmampuan dalam mengakses repositori ini.</p>') !!}
                    </div>
                </div>

                <input type="hidden" name="disclaimer_content" id="disclaimer_content" />
                <p class="mt-2 text-sm text-gray-500">{{ __('dashboard.disclaimer.content_help') }}</p>
            </div>

            <div class="flex justify-end pt-5 mt-8 border-t border-gray-100">
                <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white text-sm font-semibold px-6 py-2.5 rounded-lg transition-colors shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    {{ __('dashboard.disclaimer.save') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var editor1 = new RichTextEditor("#div_editor1", {
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

                // Manually include the CSRF token
                formData.append('_token', '{{ csrf_token() }}');

                fetch('{{ route("cms.settings.rte.upload") }}', {
                    method: 'POST',
                    body: formData,
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Upload gagal.');
                    }
                    return response.json();
                })
                .then(result => {
                    // Panggil callback milik Editor dengan string URL file tersebut agar di-inject/disisipkan
                    callback(result.url);
                })
                .catch(error => {
                    console.error('Error saat upload:', error);
                    alert('Gagal mengunggah file.');
                    if (errorCallback) errorCallback(error);
                });
            }
        });

        document.getElementById('disclaimerForm').addEventListener('submit', function() {
            var html = editor1.getHTMLCode();
            document.getElementById('disclaimer_content').value = html;
        });
    });
</script>
@endpush
