@extends('layouts.app')

@section('breadcrumb_items')
    <span class="text-gray-400">CMS</span>
    <span class="text-gray-300">/</span>
    <a href="{{ route('cms.features.index') }}"
        class="text-gray-400 hover:text-gray-600 transition-colors">{{ __('cms.features.title') }}</a>
    @if($feature->parent)
        <span class="text-gray-300">/</span>
        <a href="{{ route('cms.features.show', $feature->parent) }}"
            class="text-gray-400 hover:text-gray-600 transition-colors">{{ $feature->parent->name }}</a>
    @endif
@endsection
@section('breadcrumb_active', $feature->name)

@section('content')
<div class="space-y-6" x-data="profilePagesManager()">

    {{-- Header --}}
    <div class="flex items-center gap-3">
        <a href="{{ $feature->parent ? route('cms.features.show', $feature->parent) : route('cms.features.index') }}"
            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-white transition-colors shadow-sm"
            style="background-color: #818284;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Daftar Halaman: {{ $feature->name }}</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola halaman profil untuk menu ini</p>
        </div>
    </div>

    {{-- Page Navigation Preview --}}
    @if($pages->count() > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-base font-semibold text-gray-800">Preview Halaman Guest</h2>
                <p class="text-sm text-gray-500 mt-0.5">Navigasi halaman yang akan ditampilkan di halaman publik</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-500 mr-2">Halaman:</span>
                @foreach($pages as $index => $page)
                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-lg font-semibold text-sm
                        @if($index === 0) bg-[#174E93] text-white @else bg-gray-100 text-gray-600 hover:bg-gray-200 @endif
                        cursor-pointer transition-colors">
                        {{ $index + 1 }}
                    </span>
                @endforeach
            </div>
        </div>
        @if($pages->count() > 1)
        <p class="text-xs text-gray-400 mt-3">Tombol navigasi akan muncul di halaman publik untuk berpindah antar halaman</p>
        @endif
    </div>
    @endif

    {{-- Pages Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="px-6 py-5 border-b border-gray-100 flex items-start justify-between">
            <div>
                <h2 class="text-base font-semibold text-gray-800">Halaman Profil</h2>
                <p class="text-sm text-gray-500 mt-0.5">Kelola halaman profil. Section dikelola di halaman Edit.</p>
            </div>
            <a href="{{ route('cms.features.profile.pages.create', $feature) }}"
                class="flex items-center gap-2 bg-gray-800 hover:bg-gray-900 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Halaman
            </a>
        </div>

        <div>
            <table id="tableProfilePages" class="w-full text-sm text-left">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide w-12">No</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Judul</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide">Tipe</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide text-center">Section</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide text-center">Urutan</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wide text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($pages as $index => $page)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 text-gray-500 font-medium">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 font-medium text-gray-800">{{ $page->title }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $typeLabels = [
                                        'default' => 'Default',
                                        'sdm_chart' => 'SDM (Grafik)',
                                        'struktur_image' => 'Struktur Organisasi',
                                        'tugas_fungsi' => 'Tugas dan Fungsi',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                    @if($page->type === 'sdm_chart') bg-green-50 text-green-600 border border-green-100
                                    @elseif($page->type === 'struktur_image') bg-purple-50 text-purple-600 border border-purple-100
                                    @elseif($page->type === 'tugas_fungsi') bg-blue-50 text-blue-600 border border-blue-100
                                    @else bg-gray-100 text-gray-600 border border-gray-200
                                    @endif">
                                    {{ $typeLabels[$page->type] ?? 'Default' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center text-gray-600">{{ $page->sections_count ?? 0 }}</td>
                            <td class="px-6 py-4 text-center text-gray-600">{{ $page->order }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('cms.features.profile.pages.edit', [$feature, $page]) }}"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#174E93] hover:bg-blue-800 text-white text-xs font-semibold rounded-md transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Edit
                                    </a>
                                    <button @click="openDeleteModal({{ $page->id }}, '{{ addslashes($page->title) }}')"
                                        class="inline-flex items-center justify-center w-8 h-8 bg-red-500 hover:bg-red-600 text-white rounded-md transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="text-gray-400 text-sm">Belum ada halaman. Klik "Tambah Halaman" untuk menambahkan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ===== DELETE MODAL ===== --}}
    <div x-show="deleteModal.open" x-cloak
        class="fixed inset-0 flex items-center justify-center p-4"
        style="z-index: 9999;"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="deleteModal.open = false" style="position: fixed; top: 0; right: 0; bottom: 0; left: 0;"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm z-[9999] p-6"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
            <div class="flex flex-col items-center text-center gap-4">
                <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center">
                    <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-800">Hapus Halaman?</h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Anda yakin ingin menghapus <strong x-text="deleteModal.name" class="text-gray-700"></strong>?
                    </p>
                </div>
                <div class="flex items-center gap-3 w-full">
                    <button @click="deleteModal.open = false"
                        class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Batal</button>
                    <button type="button" @click="submitDelete()"
                        class="flex-1 px-4 py-2.5 text-sm font-semibold text-white bg-red-500 hover:bg-red-600 rounded-lg transition-colors">Hapus</button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function profilePagesManager() {
    return {
        deleteModal: { open: false, id: null, name: '' },

        openDeleteModal(id, name) {
            this.deleteModal = { open: true, id, name };
        },

        submitDelete() {
            if (!this.deleteModal.id) return;
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/cms/features/{{ $feature->id }}/profile/${this.deleteModal.id}`;
            form.innerHTML = `@csrf @method('DELETE')`;
            document.body.appendChild(form);
            form.submit();
        }
    }
}

$(document).ready(function() {
    $.fn.dataTable.ext.errMode = 'none';
    $('#tableProfilePages').DataTable({
        columnDefs: [{ orderable: false, targets: [5] }],
        order: [[0, 'asc']],
    });
});
</script>
@endpush
