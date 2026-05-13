@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/cms/virtual_book_pages.css') }}">
@endpush

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
@endsection
@section('breadcrumb_active', $feature->name)

@section('content')
    <div class="space-y-6" x-data="{ deleteModal: { open: false, id: null, name: '' } }">

        <!-- Header Section -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ $feature->parent ? route('cms.features.show', $feature->parent) : route('cms.features.index') }}"
                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-white transition-colors shadow-sm"
                    style="background-color: #818284;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">
                        {{ __('cms.virtual_books.page_title', ['name' => $feature->name]) }}</h1>
                    <p class="text-sm text-gray-500 mt-0.5">{{ __('cms.virtual_books.page_desc') }}</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('cms.features.virtual_books.create', $feature) }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-800 border border-transparent text-white text-sm font-semibold rounded-lg hover:bg-gray-900 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ __('cms.virtual_books.add_button') }}
                </a>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="px-6 py-5 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-800">{{ __('cms.virtual_books.table_title') }}</h2>
            </div>
            <div>
                <table id="tableBooks" class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 text-sm font-medium border-b border-gray-100">
                            <th class="px-6 py-4 w-12">{{ __('cms.virtual_rooms.col_no') }}</th>
                            <th class="px-6 py-4 w-28">{{ __('cms.virtual_books.col_cover') }}</th>
                            <th class="px-6 py-4">{{ __('cms.virtual_books.col_title') }}</th>
                            <th class="px-6 py-4 w-24">{{ __('cms.virtual_books.col_pages') }}</th>
                            <th class="px-6 py-4 w-24">{{ __('cms.virtual_books.col_order') }}</th>
                            <th class="px-6 py-4 w-48 text-right">{{ __('cms.virtual_books.col_action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($books as $book)
                            <tr class="hover:bg-gray-50/50 transition-colors group">
                                <td class="px-6 py-4 text-gray-500 font-medium">{{ $loop->iteration }}</td>
                                <td class="px-6 py-4">
                                    @if ($book->thumbnail)
                                        <img src="{{ asset('storage/' . $book->thumbnail) }}" alt="{{ $book->title }}"
                                            class="w-16 h-20 object-cover rounded-md border border-gray-200 shadow-sm">
                                    @elseif($book->cover_image)
                                        <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}"
                                            class="w-16 h-20 object-cover rounded-md border border-gray-200 shadow-sm">
                                    @else
                                        <div
                                            class="w-16 h-20 bg-gray-100 rounded-md border border-gray-200 flex items-center justify-center text-xs text-gray-400">
                                            {{ __('cms.virtual_books.no_cover') }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-semibold text-gray-800">{{ $book->title }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="text-sm text-gray-600">{{ __('cms.virtual_books.page_count', ['count' => $book->pages->count()]) }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-gray-600">{{ $book->order }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- Detail - ke daftar halaman -->
                                        <a href="{{ route('cms.features.virtual_books.pages.index', [$feature, $book]) }}"
                                            class="inline-flex items-center justify-center w-8 h-8 bg-blue-500 hover:bg-blue-600 text-white rounded-md transition-colors"
                                            title="{{ __('cms.virtual_books.detail_title') }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                                                </path>
                                            </svg>
                                        </a>
                                        <!-- Edit - edit cover buku -->
                                        <a href="{{ route('cms.features.virtual_books.edit', [$feature, $book]) }}"
                                            class="inline-flex items-center justify-center w-8 h-8 bg-yellow-400 hover:bg-yellow-500 text-white rounded-md transition-colors"
                                            title="{{ __('cms.virtual_books.edit_cover') }}">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>
                                        </a>
                                        <!-- Delete -->
                                        <button
                                            @click="deleteModal = { open: true, id: {{ $book->id }}, name: '{{ addslashes($book->title) }}' }"
                                            class="inline-flex items-center justify-center w-8 h-8 bg-red-500 hover:bg-red-600 text-white rounded-md transition-colors"
                                            title="{{ __('cms.virtual_books.delete.title') }}">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">
                                    {{ __('cms.virtual_books.empty') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ===== DELETE CONFIRMATION MODAL ===== -->
        <div x-show="deleteModal.open" x-cloak class="fixed inset-0 flex items-center justify-center p-4"
            style="z-index: 9999;" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="deleteModal.open = false"
                style="position: fixed; top: 0; right: 0; bottom: 0; left: 0;"></div>
            <!-- Modal -->
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm z-[9999] p-6"
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
                        <h3 class="text-base font-semibold text-gray-800">{{ __('cms.virtual_books.delete.title') }}</h3>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ __('cms.virtual_books.delete.confirm') }} <strong x-text="deleteModal.name"
                                class="text-gray-700"></strong>{{ __('cms.virtual_books.delete.confirm_warn') }}
                        </p>
                    </div>
                    <div class="flex items-center gap-3 w-full">
                        <button @click="deleteModal.open = false"
                            class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                            {{ __('cms.common.cancel') }}
                        </button>
                        <form :action="`{{ route('cms.features.virtual_books.index', $feature) }}/${deleteModal.id}`"
                            method="POST" class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="w-full px-4 py-2.5 text-sm font-semibold text-white bg-red-500 hover:bg-red-600 rounded-lg transition-colors">
                                {{ __('cms.virtual_books.delete.yes') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $.fn.dataTable.ext.errMode = 'none';
            $('#tableBooks').DataTable({
                columnDefs: [{
                    orderable: false,
                    targets: [1, 5]
                }],
                order: [
                    [0, 'asc']
                ],
            });
        });
    </script>
@endpush
