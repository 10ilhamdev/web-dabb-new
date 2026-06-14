@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/cms/virtual_3d_rooms.css') }}">
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
    <div class="space-y-6">

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
                    <h1 class="text-2xl font-bold text-gray-800">{!! __('cms.virtual_3d_rooms.page_title', ['name' => $feature->name]) !!}</h1>
                    <p class="text-sm text-gray-500 mt-0.5">{{ __('cms.virtual_3d_rooms.page_desc') }}</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                @if ($feature->path)
                    <a href="{{ url($feature->path) }}" target="_blank"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-800 border border-transparent text-white text-sm font-medium rounded-lg hover:bg-blue-900 transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        {{ __('cms.virtual_3d_rooms.view_exhibition') }}
                    </a>
                @endif
                <a href="{{ route('cms.features.virtual_3d_rooms.create', $feature) }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-800 border border-transparent text-white text-sm font-semibold rounded-lg hover:bg-gray-900 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ __('cms.virtual_3d_rooms.add_room') }}
                </a>
            </div>
        </div>



        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <p class="text-sm font-medium text-gray-600">{{ __('cms.virtual_3d_rooms.stat_total_rooms') }}</p>
                <div class="mt-4">
                    <h3 class="text-3xl font-bold text-gray-800">{{ $virtual3dRooms->count() }}</h3>
                    <p class="text-xs text-gray-500 mt-1">{{ __('cms.virtual_3d_rooms.stat_total_rooms_sub') }}</p>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <p class="text-sm font-medium text-gray-600">{{ __('cms.virtual_3d_rooms.stat_total_media') }}</p>
                <div class="mt-4">
                    <h3 class="text-3xl font-bold text-gray-800">{{ $virtual3dRooms->sum(fn($r) => $r->media()->count()) }}
                    </h3>
                    <p class="text-xs text-gray-500 mt-1">{!! __('cms.virtual_3d_rooms.stat_total_media_sub') !!}</p>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <p class="text-sm font-medium text-gray-600">{{ __('cms.virtual_3d_rooms.stat_avg_media') }}</p>
                <div class="mt-4">
                    <h3 class="text-3xl font-bold text-gray-800">
                        {{ $virtual3dRooms->count() ? number_format($virtual3dRooms->sum(fn($r) => $r->media()->count()) / $virtual3dRooms->count(), 1) : '0' }}
                    </h3>
                    <p class="text-xs text-gray-500 mt-1">{{ __('cms.virtual_3d_rooms.stat_avg_media_sub') }}</p>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="px-6 py-5 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-800">{{ __('cms.virtual_3d_rooms.table_title') }}</h2>
            </div>
            <div>
                <table id="tableVirtual3dRooms" class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 text-sm font-medium border-b border-gray-100">
                            <th class="px-6 py-4 w-12">{{ __('cms.virtual_3d_rooms.col_no') }}</th>
                            <th class="px-6 py-4 w-28">{{ __('cms.virtual_3d_rooms.col_thumbnail') }}</th>
                            <th class="px-6 py-4">{{ __('cms.virtual_3d_rooms.col_name') }}</th>
                            <th class="px-6 py-4">{{ __('cms.virtual_3d_rooms.col_desc') }}</th>
                            <th class="px-6 py-4 w-32">{{ __('cms.virtual_3d_rooms.col_media') }}</th>
                            <th class="px-6 py-4 w-32 text-center">Status</th>
                            <th class="px-6 py-4 w-32 text-right">{{ __('cms.virtual_3d_rooms.col_action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($virtual3dRooms as $room)
                            <tr class="hover:bg-gray-50/50 transition-colors group">
                                <td class="px-6 py-4 text-gray-500 font-medium">{{ $loop->iteration }}</td>
                                <td class="px-6 py-4">
                                    @if ($room->thumbnail_path)
                                        <img src="{{ asset('storage/' . $room->thumbnail_path) }}"
                                            alt="{{ $room->name }}"
                                            class="w-16 h-12 object-cover rounded-md border border-gray-200 shadow-sm">
                                    @else
                                        <div
                                            class="w-16 h-12 bg-gray-100 rounded-md border border-gray-200 flex items-center justify-center text-xs text-gray-400">
                                            No Img</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-semibold text-gray-800">{{ $room->name }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-gray-600 line-clamp-2 w-72">{{ $room->description ?: '-' }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-1.5 text-sm text-gray-600">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        {{ $room->media()->count() }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <form action="{{ route('cms.features.virtual_3d_rooms.toggle_visibility', [$feature, $room]) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" title="{{ $room->is_active ? 'Sembunyikan' : 'Tampilkan' }}"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold border transition-colors
                                            {{ $room->is_active ? 'bg-green-50 text-green-600 border-green-200 hover:bg-green-100' : 'bg-gray-100 text-gray-500 border-gray-200 hover:bg-gray-200' }}">
                                            @if($room->is_active)
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                Aktif
                                            @else
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                                Tersembunyi
                                            @endif
                                        </button>
                                    </form>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('cms.features.virtual_3d_rooms.edit', [$feature, $room]) }}"
                                            class="inline-flex items-center justify-center w-8 h-8 bg-yellow-400 hover:bg-yellow-500 text-white rounded-md transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>
                                        </a>
                                        <form
                                            action="{{ route('cms.features.virtual_3d_rooms.destroy', [$feature, $room]) }}"
                                            method="POST" class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                onclick="confirmDelete(this.closest('form'), '{{ $room->name }}')"
                                                class="inline-flex items-center justify-center w-8 h-8 bg-red-500 hover:bg-red-600 text-white rounded-md transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-sm text-gray-500">
                                    {{ __('cms.virtual_3d_rooms.empty') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function confirmDelete(form, name) {
            Swal.fire({
                title: '{{ __("cms.features.delete.title") }}',
                html: `{{ __("cms.virtual_3d_rooms.delete_confirm") }}<br><strong class="text-gray-700">${name}</strong>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '{{ __("cms.features.delete.yes") }}',
                cancelButtonText: '{{ __("cms.common.cancel") }}',
                reverseButtons: true,
                borderRadius: '12px',
                customClass: {
                    confirmButton: 'px-5 py-2.5 text-sm font-semibold text-white bg-red-500 hover:bg-red-600 rounded-lg transition-colors',
                    cancelButton: 'px-5 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors mr-3'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }

        $(document).ready(function() {
            $.fn.dataTable.ext.errMode = 'none';
            $('#tableVirtual3dRooms').DataTable({
                columnDefs: [{
                    orderable: false,
                    targets: [1, 5, 6]
                }],
                order: [
                    [0, 'asc']
                ],
            });
        });
    </script>
@endpush
