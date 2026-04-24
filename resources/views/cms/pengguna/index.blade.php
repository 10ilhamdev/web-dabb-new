@extends('layouts.app')

@section('breadcrumb_items')
    <span class="text-gray-400">CMS</span>
@endsection
@section('breadcrumb_active', __('cms.pengguna.breadcrumb'))

@push('styles')
    {{-- DataTables Buttons CSS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
    <style>
        /* Table styling is handled globally by public/css/datatables.css (AdminLTE style).
           Do not override table borders here — keep markup plain so global CSS applies. */

        /* Filter row (merged into card) */
        .pengguna-filter-row {
            display: grid;
            grid-template-columns: repeat(1, minmax(0, 1fr));
            gap: 1rem;
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #f3f4f6;
        }
        @media (min-width: 768px) {
            .pengguna-filter-row { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        .pengguna-filter-row label {
            display: block;
            font-size: .7rem;
            font-weight: 600;
            color: #6b7280;
            margin-bottom: .375rem;
            text-transform: uppercase;
            letter-spacing: .05em;
        }
        .pengguna-filter-row select {
            width: 100%;
            padding: .625rem 2.25rem .625rem .875rem;
            border: 1px solid #e5e7eb;
            border-radius: .5rem;
            font-size: .8125rem;
            background-color: white;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right .625rem center;
            background-size: 1rem 1rem;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            transition: all .15s ease;
        }
        .pengguna-filter-row select:focus {
            outline: none;
            border-color: transparent;
            box-shadow: 0 0 0 2px #3b82f6;
        }

        /* Top toolbar (length left, search + buttons right) */
        #tablePengguna_wrapper .dt-top-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #f3f4f6;
        }
        #tablePengguna_wrapper .dt-top-row .dataTables_length { margin: 0; }
        #tablePengguna_wrapper .dt-top-row .dt-top-right {
            display: flex;
            align-items: center;
            gap: .625rem;
            flex-wrap: wrap;
        }
        #tablePengguna_wrapper .dt-top-row .dataTables_filter { margin: 0; }
        #tablePengguna_wrapper .dataTables_length label,
        #tablePengguna_wrapper .dataTables_filter label {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            font-size: .8125rem;
            color: #6b7280;
            margin: 0;
            font-weight: 500;
        }
        #tablePengguna_wrapper .dataTables_length select,
        #tablePengguna_wrapper .dataTables_filter input {
            border: 1px solid #e5e7eb;
            border-radius: .5rem;
            padding: .5rem .75rem;
            font-size: .8125rem;
            background: white;
        }
        #tablePengguna_wrapper .dataTables_length select {
            padding-right: 2rem;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right .5rem center;
            background-size: 1rem 1rem;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }
        #tablePengguna_wrapper .dataTables_filter input {
            min-width: 220px;
        }

        /* Export dropdown button */
        div.dt-buttons {
            display: inline-flex;
            gap: .5rem;
            margin: 0;
        }
        div.dt-buttons .dt-button {
            background: #fff !important;
            border: 1px solid #e5e7eb !important;
            color: #374151 !important;
            padding: .5rem .875rem !important;
            border-radius: .5rem !important;
            font-size: .8125rem !important;
            font-weight: 500 !important;
            box-shadow: none !important;
            margin: 0 !important;
            display: inline-flex;
            align-items: center;
            gap: .375rem;
        }
        div.dt-buttons .dt-button:hover {
            background: #f9fafb !important;
            border-color: #d1d5db !important;
            color: #111827 !important;
        }
        /* Remove dark overlay behind export dropdown */
        div.dt-button-background,
        .dt-button-background {
            display: none !important;
            background: transparent !important;
            opacity: 0 !important;
        }

        /* Collection dropdown menu */
        div.dt-button-collection {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: .5rem;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,.1);
            padding: .375rem;
            min-width: 160px;
        }
        div.dt-button-collection .dt-button {
            display: block !important;
            width: 100% !important;
            text-align: left !important;
            border: 0 !important;
            border-radius: .375rem !important;
            margin: 0 !important;
            padding: .5rem .75rem !important;
        }
        div.dt-button-collection .dt-button:hover { background: #f3f4f6 !important; }

        /* Add User button inside toolbar */
        .btn-add-user {
            display: inline-flex;
            align-items: center;
            gap: .375rem;
            background: #174E93;
            color: white;
            font-size: .8125rem;
            font-weight: 600;
            padding: .5rem .875rem;
            border-radius: .5rem;
            transition: background-color .15s ease;
            text-decoration: none;
        }
        .btn-add-user:hover { background: #1e40af; }

        /* Bottom row */
        #tablePengguna_wrapper .dt-bottom-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
            padding: 1rem 1.5rem;
            border-top: 1px solid #f3f4f6;
        }
        #tablePengguna_wrapper .dataTables_info {
            font-size: .8125rem;
            color: #6b7280;
        }
        #tablePengguna_wrapper .dataTables_paginate .paginate_button {
            padding: .375rem .75rem !important;
            margin: 0 .125rem !important;
            border: 1px solid #e5e7eb !important;
            border-radius: .375rem !important;
            font-size: .8125rem !important;
            background: white !important;
            color: #374151 !important;
        }
        #tablePengguna_wrapper .dataTables_paginate .paginate_button.current {
            background: #174E93 !important;
            color: white !important;
            border-color: #174E93 !important;
        }
        #tablePengguna_wrapper .dataTables_paginate .paginate_button:hover {
            background: #f3f4f6 !important;
        }
        #tablePengguna_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: #174E93 !important;
            color: white !important;
        }
    </style>
@endpush

@section('content')
<div class="space-y-6" x-data="penggunaManager()">

    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ __('cms.pengguna.title') }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ __('cms.pengguna.subtitle') }}</p>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="pengguna-stats-grid" style="display:grid;grid-template-columns:repeat(5, minmax(0, 1fr));gap:0.75rem;">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-start justify-between gap-2">
            <div>
                <p class="text-sm text-gray-500">{{ __('cms.pengguna.stats_total') }}</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total'] }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ __('cms.pengguna.stats_total_sub') }}</p>
            </div>
            <div class="w-11 h-11 rounded-lg bg-blue-50 text-[#174E93] flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-5.13a4 4 0 11-8 0 4 4 0 018 0zm6 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-start justify-between gap-2">
            <div>
                <p class="text-sm text-gray-500">{{ __('cms.pengguna.stats_admin') }}</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['admin'] }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ __('cms.pengguna.stats_admin_sub') }}</p>
            </div>
            <div class="w-11 h-11 rounded-lg bg-red-50 text-red-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-start justify-between gap-2">
            <div>
                <p class="text-sm text-gray-500">{{ __('cms.pengguna.stats_pegawai') }}</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['pegawai'] }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ __('cms.pengguna.stats_pegawai_sub') }}</p>
            </div>
            <div class="w-11 h-11 rounded-lg bg-yellow-50 text-yellow-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-start justify-between gap-2">
            <div>
                <p class="text-sm text-gray-500">{{ __('cms.pengguna.stats_eksternal') }}</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['eksternal'] }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ __('cms.pengguna.stats_eksternal_sub') }}</p>
            </div>
            <div class="w-11 h-11 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex items-start justify-between gap-2">
            <div>
                <p class="text-sm text-gray-500">{{ __('cms.pengguna.stats_verified') }}</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['verified'] }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ __('cms.pengguna.stats_verified_sub') }}</p>
            </div>
            <div class="w-11 h-11 rounded-lg bg-green-50 text-green-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
        </div>
    </div>

    {{-- Unified Table Card: Header + Filters + Toolbar + Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-800">{{ __('cms.pengguna.filter_section_title') }}</h2>
        </div>

        {{-- Merged Filter Row --}}
        <div class="pengguna-filter-row">
            <div>
                <label>{{ __('cms.pengguna.filter_role') }}</label>
                <select id="filter-role">
                    <option value="">{{ __('cms.pengguna.filter_role') }}</option>
                    @foreach ($roles as $key => $label)
                        <option value="{{ $label }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>{{ __('cms.pengguna.filter_status') }}</label>
                <select id="filter-status">
                    <option value="">{{ __('cms.pengguna.filter_verified_all') }}</option>
                    <option value="verified">{{ __('cms.pengguna.filter_verified_yes') }}</option>
                    <option value="pending">{{ __('cms.pengguna.filter_verified_no') }}</option>
                </select>
            </div>
        </div>

        {{-- Add User button stays outside the DataTable dom but absolutely positioned.
             We'll let DataTable render: [length] ... [search][export-dropdown]
             and append the Add User button via JS into the top-right region. --}}
        <div class="overflow-x-auto">
            <table id="tablePengguna" class="w-full text-left">
                <thead>
                    <tr>
                        <th class="w-12">No</th>
                        <th>{{ __('cms.pengguna.col_user') }}</th>
                        <th>{{ __('cms.pengguna.col_username') }}</th>
                        <th>{{ __('cms.pengguna.col_role') }}</th>
                        <th>{{ __('cms.pengguna.col_status') }}</th>
                        <th>{{ __('cms.pengguna.col_joined') }}</th>
                        <th class="text-center">{{ __('cms.pengguna.col_action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $index => $user)
                    @php
                        $roleLabel = $roles[$user->role] ?? $user->role;
                        $isVerified = !is_null($user->email_verified_at);
                        $initials = collect(explode(' ', trim($user->name)))->map(fn($p) => mb_substr($p, 0, 1))->take(2)->implode('');
                        $colors = ['bg-blue-100 text-blue-700','bg-purple-100 text-purple-700','bg-pink-100 text-pink-700','bg-yellow-100 text-yellow-700','bg-green-100 text-green-700','bg-red-100 text-red-700'];
                        $color = $colors[$user->id % count($colors)];
                    @endphp
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 text-gray-500 font-medium">{{ $index + 1 }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if ($user->photo)
                                    <img src="{{ asset('storage/' . $user->photo) }}" alt="" class="w-9 h-9 rounded-full object-cover shrink-0">
                                @else
                                    <div class="w-9 h-9 rounded-full {{ $color }} flex items-center justify-center text-xs font-semibold shrink-0">
                                        {{ strtoupper($initials ?: 'U') }}
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <div class="font-semibold text-gray-800 truncate">{{ $user->name }}</div>
                                    <div class="text-xs text-gray-500 truncate">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-600">{{ $user->username ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                @switch($user->role)
                                    @case('admin') bg-red-50 text-red-600 border border-red-100 @break
                                    @case('pegawai') bg-yellow-50 text-yellow-700 border border-yellow-100 @break
                                    @case('pelajar_mahasiswa') bg-blue-50 text-blue-600 border border-blue-100 @break
                                    @case('instansi_swasta') bg-purple-50 text-purple-600 border border-purple-100 @break
                                    @default bg-gray-100 text-gray-600 border border-gray-200
                                @endswitch">
                                {{ $roleLabel }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if ($isVerified)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-50 text-green-600 border border-green-100">
                                    {{ __('cms.pengguna.status_verified') }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-600 border border-amber-100">
                                    {{ __('cms.pengguna.status_pending') }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-600 text-sm whitespace-nowrap">{{ optional($user->created_at)->format('d M Y') }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('cms.pengguna.edit', $user) }}"
                                    class="inline-flex items-center justify-center w-8 h-8 bg-yellow-400 hover:bg-yellow-500 text-white rounded-md transition-colors" title="{{ __('cms.pengguna.edit_button') }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <button type="button"
                                    @click="openDeleteModal({{ $user->id }}, '{{ addslashes($user->name) }}')"
                                    class="inline-flex items-center justify-center w-8 h-8 bg-red-500 hover:bg-red-600 text-white rounded-md transition-colors" title="{{ __('cms.pengguna.delete_button') }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-5.13a4 4 0 11-8 0 4 4 0 018 0zm6 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                                <p class="text-gray-400 text-sm">{{ __('cms.pengguna.empty') }}</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div x-show="deleteModal.open" x-cloak
        class="fixed inset-0 flex items-center justify-center p-4"
        style="z-index: 9999;"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="deleteModal.open = false" style="position: fixed; top: 0; right: 0; bottom: 0; left: 0;"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm z-[9999] p-6"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100">
            <div class="flex flex-col items-center text-center gap-4">
                <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center">
                    <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-800">{{ __('cms.pengguna.delete_title') }}</h3>
                    <p class="text-sm text-gray-500 mt-1">
                        {{ __('cms.pengguna.delete_confirm', ['name' => '']) }}
                        <strong x-text="deleteModal.name" class="text-gray-700"></strong>
                    </p>
                </div>
                <div class="flex items-center gap-3 w-full">
                    <button @click="deleteModal.open = false" type="button"
                        class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                        {{ __('cms.pengguna.cancel') }}
                    </button>
                    <form :action="`/cms/pengguna/${deleteModal.id}`" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="w-full px-4 py-2.5 text-sm font-semibold text-white bg-red-500 hover:bg-red-600 rounded-lg transition-colors">
                            {{ __('cms.pengguna.delete_yes') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    {{-- DataTables Buttons (export) --}}
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

    <script>
        window.penggunaI18n = {
            btnExport: @json(__('cms.pengguna.btn_export')),
            btnCopy: @json(__('cms.pengguna.btn_copy')),
            btnCsv: @json(__('cms.pengguna.btn_csv')),
            btnExcel: @json(__('cms.pengguna.btn_excel')),
            btnWord: @json(__('cms.pengguna.btn_word')),
            btnPdf: @json(__('cms.pengguna.btn_pdf')),
            btnPrint: @json(__('cms.pengguna.btn_print')),
            btnAddUser: @json(__('cms.pengguna.add_button')),
            urlCreate: @json(route('cms.pengguna.create')),
        };
    </script>
    <script src="{{ asset('js/cms/features/pengguna/index.js') }}"></script>

    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                if (window.Swal) {
                    Swal.fire({ icon: 'success', title: @json(session('success')), toast: true, position: 'top-end', timer: 2500, showConfirmButton: false });
                }
            });
        </script>
    @endif
    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                if (window.Swal) {
                    Swal.fire({ icon: 'error', title: @json(session('error')), toast: true, position: 'top-end', timer: 3000, showConfirmButton: false });
                }
            });
        </script>
    @endif
@endpush
@endsection
