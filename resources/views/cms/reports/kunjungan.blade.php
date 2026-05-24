@extends('layouts.app')

@section('title', __('dashboard.sidebar.reports_kunjungan'))

@section('header')
    <div class="text-[13px] text-gray-500 font-medium">
        <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-600">{{ __('dashboard.header.breadcrumb_home') }}</a> /
        <span class="text-gray-400">{{ __('dashboard.sidebar.reports') }}</span> /
        <span class="text-[#0ea5e9]">{{ __('dashboard.sidebar.reports_kunjungan') }}</span>
    </div>
@endsection

@section('content')
    @php
        $userRole = auth()->user() ? \App\Models\Role::where('name', auth()->user()->role)->first() : null;
        $hasAllPermission = ($userRole && $userRole->hasPermission('cms.reports.all'));
    @endphp
    <div class="mb-8" x-data="kunjunganReportComponent()">
        <!-- Title & Description -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-[22px] font-bold text-[#1E293B] mb-1">{{ __('dashboard.sidebar.reports_kunjungan') }}</h1>
                <p class="text-gray-500 text-sm">{{ __('cms.reports.kunjungan_subtitle') }}</p>
            </div>
            
            <!-- Filter Controls -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 shrink-0">
                <!-- Preset Buttons -->
                <div class="inline-flex bg-white p-1.5 rounded-xl shadow-sm border border-gray-100 text-xs font-medium text-gray-600 overflow-x-auto">
                    <a href="{{ route('cms.reports.kunjungan', ['tf' => 'day']) }}" class="px-3 py-1.5 rounded-lg transition-colors whitespace-nowrap {{ $tf === 'day' ? 'bg-[#174E93] text-white font-bold' : 'hover:bg-gray-100' }}">{{ __('cms.reports.preset_today') }}</a>
                    <a href="{{ route('cms.reports.kunjungan', ['tf' => 'week']) }}" class="px-3 py-1.5 rounded-lg transition-colors whitespace-nowrap {{ $tf === 'week' ? 'bg-[#174E93] text-white font-bold' : 'hover:bg-gray-100' }}">{{ __('cms.reports.preset_week') }}</a>
                    <a href="{{ route('cms.reports.kunjungan', ['tf' => 'month']) }}" class="px-3 py-1.5 rounded-lg transition-colors whitespace-nowrap {{ $tf === 'month' ? 'bg-[#174E93] text-white font-bold' : 'hover:bg-gray-100' }}">{{ __('cms.reports.preset_month') }}</a>
                    <a href="{{ route('cms.reports.kunjungan', ['tf' => 'year']) }}" class="px-3 py-1.5 rounded-lg transition-colors whitespace-nowrap {{ $tf === 'year' ? 'bg-[#174E93] text-white font-bold' : 'hover:bg-gray-100' }}">{{ __('cms.reports.preset_year') }}</a>
                    <button @click="showCustom = true; tf = 'custom'" class="px-3 py-1.5 rounded-lg transition-colors whitespace-nowrap {{ $tf === 'custom' ? 'bg-[#174E93] text-white font-bold' : 'hover:bg-gray-100' }}">{{ __('cms.reports.preset_custom') }}</button>
                </div>

                <!-- Custom Date Range Form -->
                <form method="GET" action="{{ route('cms.reports.kunjungan') }}" x-show="showCustom" x-transition.opacity class="flex items-center gap-2 bg-white p-1.5 rounded-xl shadow-sm border border-gray-100 shrink-0 overflow-x-auto" x-cloak>
                    <input type="hidden" name="tf" value="custom">
                    <div class="flex items-center gap-2 px-2 shrink-0">
                        <span class="text-xs text-gray-400 font-medium">{{ __('cms.reports.filter_start') }}</span>
                        <input type="date" name="start_date" value="{{ $startDate ?? '' }}" required class="text-xs border-none bg-gray-50 px-2.5 py-1 rounded-lg focus:ring-1 focus:ring-blue-50 text-gray-700 font-medium">
                    </div>
                    <div class="flex items-center gap-2 px-2 border-l border-gray-100 shrink-0">
                        <span class="text-xs text-gray-400 font-medium">{{ __('cms.reports.filter_end') }}</span>
                        <input type="date" name="end_date" value="{{ $endDate ?? '' }}" required class="text-xs border-none bg-gray-50 px-2.5 py-1 rounded-lg focus:ring-1 focus:ring-blue-50 text-gray-700 font-medium">
                    </div>
                    <button type="submit" class="px-3 py-1 bg-[#174E93] hover:bg-blue-800 text-white rounded-lg font-semibold text-xs transition-colors shadow-sm shrink-0">{{ __('cms.reports.btn_filter') }}</button>
                    <button type="button" @click="showCustom = false" class="px-2 py-1 text-gray-400 hover:text-gray-600 text-xs shrink-0" title="{{ __('cms.reports.btn_cancel') }}">✕</button>
                </form>
            </div>
        </div>

        <!-- Summary Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition-all">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="text-3xl font-bold text-gray-900 leading-tight">{{ number_format($totalKunjungan) }}</div>
                        <div class="text-xs font-medium text-gray-500 mt-1">{{ __('cms.reports.total_visitor') }}</div>
                    </div>
                    <div class="p-3 bg-blue-50 rounded-xl text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 text-xs text-blue-600 bg-blue-50/50 px-3 py-2 rounded-xl font-medium flex items-center gap-1.5">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ $subtitle }}</span>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition-all">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="text-3xl font-bold text-gray-900 leading-tight">{{ number_format($totalEdukasi) }}</div>
                        <div class="text-xs font-medium text-gray-500 mt-1">{{ __('cms.reports.purpose_edukasi') }}</div>
                    </div>
                    <div class="p-3 bg-green-50 rounded-xl text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.5a11.952 11.952 0 00-6.824-2.942 12.083 12.083 0 01.665-6.479L12 14z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 text-xs text-gray-400 flex items-center gap-1">
                    <span>{{ __('cms.reports.purpose_edukasi_sub') }}</span>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition-all">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="text-3xl font-bold text-gray-900 leading-tight">{{ number_format($totalPenelitian) }}</div>
                        <div class="text-xs font-medium text-gray-500 mt-1">{{ __('cms.reports.purpose_penelitian') }}</div>
                    </div>
                    <div class="p-3 bg-purple-50 rounded-xl text-purple-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 text-xs text-gray-400 flex items-center gap-1">
                    <span>{{ __('cms.reports.purpose_penelitian_sub') }}</span>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition-all">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="text-3xl font-bold text-gray-900 leading-tight">{{ number_format($totalKunker) }}</div>
                        <div class="text-xs font-medium text-gray-500 mt-1">{{ __('cms.reports.purpose_kunker') }}</div>
                    </div>
                    <div class="p-3 bg-amber-50 rounded-xl text-amber-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 text-xs text-gray-400 flex items-center gap-1">
                    <span>{{ __('cms.reports.purpose_kunker_sub') }}</span>
                </div>
            </div>
        </div>

        <!-- Charts Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Pie Chart: Tujuan Kunjungan -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between lg:col-span-1">
                <div class="mb-4">
                    <h2 class="text-lg font-bold text-gray-800">{{ __('cms.reports.chart_purpose_title') }}</h2>
                    <p class="text-xs text-gray-400">{{ __('cms.reports.chart_purpose_sub') }}</p>
                </div>
                <div class="w-full relative flex items-center justify-center my-auto" style="min-height: 280px;">
                    <div id="pieChart" class="w-full flex justify-center"></div>
                </div>
            </div>

            <!-- Line Chart: Tren Kunjungan -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between lg:col-span-2">
                <div class="mb-4 flex justify-between items-center">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">{{ __('cms.reports.chart_trend_title') }}</h2>
                        <p class="text-xs text-gray-400">{{ __('cms.reports.chart_trend_sub') }}</p>
                    </div>
                </div>
                <div class="w-full relative" style="height: 280px;">
                    <div id="lineChart"></div>
                </div>
            </div>
        </div>

        <!-- Data Table: Pendaftaran Kunjungan -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-lg font-bold text-gray-800">{{ __('cms.reports.table_kunjungan_title') }}</h2>
                    <p class="text-xs text-gray-400">{{ __('cms.reports.table_kunjungan_sub') }}</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse dataTable" id="tableKunjungan">
                    <thead>
                        <tr class="border-b border-gray-100 text-[12px] font-semibold text-gray-500 uppercase bg-gray-50/50">
                            <th class="py-3 px-4 rounded-l-xl">{{ __('cms.reports.col_no') }}</th>
                            <th class="py-3 px-4">{{ __('cms.reports.col_name_inst') }}</th>
                            <th class="py-3 px-4">{{ __('cms.reports.col_contact') }}</th>
                            <th class="py-3 px-4">{{ __('cms.reports.col_date_time') }}</th>
                            <th class="py-3 px-4 text-center">{{ __('cms.reports.col_count') }}</th>
                            <th class="py-3 px-4">{{ __('cms.reports.col_purpose') }}</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-center rounded-r-xl">{{ __('cms.reports.col_action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="text-[13px] text-gray-600 divide-y divide-gray-50">
                        @forelse($registrations as $index => $reg)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="py-3.5 px-4 font-medium text-gray-900">{{ $loop->iteration }}</td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-gray-900 text-[14px]">{{ $reg->name }}</div>
                                    <div class="text-xs text-gray-500 flex items-center gap-1 mt-0.5">
                                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                        {{ $reg->institution ?: '-' }} ({{ $reg->position ?: '-' }})
                                    </div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div>{{ $reg->email }}</div>
                                    <div class="text-xs text-gray-400">{{ $reg->phone ?: '-' }}</div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-medium text-gray-800">{{ $reg->visit_date ? $reg->visit_date->format('d M Y') : '-' }}</div>
                                    <div class="text-xs text-gray-400 font-medium">{{ $reg->visit_time ? __('home.layanan_publik.form_time_' . strtolower($reg->visit_time)) : __('home.layanan_publik.form_time_pagi') }}</div>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="px-2.5 py-1 bg-blue-50 text-[#174E93] font-bold rounded-lg text-xs">{{ $reg->visitor_count }} {{ __('cms.reports.label_org') }}</span>
                                </td>
                                <td class="py-3.5 px-4 text-xs font-semibold">
                                     @php
                                         $purposeLower = strtolower($reg->visit_purpose ?? '');
                                         $colorData = $purposeColors[$purposeLower] ?? null;
                                     @endphp
                                     @if($colorData)
                                         <span class="px-2.5 py-1 rounded-lg whitespace-nowrap inline-block" style="color: {{ $colorData['color'] }}; background-color: {{ $colorData['color'] }}1A;">{{ $colorData['label'] }}</span>
                                     @else
                                         <span class="text-blue-600 bg-blue-50 px-2.5 py-1 rounded-lg whitespace-nowrap inline-block">{{ ucwords(str_replace('_', ' ', $reg->visit_purpose ?? '-')) }}</span>
                                     @endif
                                </td>
                                <td class="py-3.5 px-4 text-center text-xs font-bold">
                                    @if($reg->status === 'approved')
                                        <span class="text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-lg whitespace-nowrap inline-block">{{ app()->getLocale() === 'en' ? 'Approved' : 'Disetujui' }}</span>
                                    @elseif($reg->status === 'rejected')
                                        <span class="text-red-600 bg-red-50 px-2.5 py-1 rounded-lg whitespace-nowrap inline-block">{{ app()->getLocale() === 'en' ? 'Rejected' : 'Ditolak' }}</span>
                                    @else
                                        <span class="text-amber-600 bg-amber-50 px-2.5 py-1 rounded-lg whitespace-nowrap inline-block">{{ app()->getLocale() === 'en' ? 'Pending' : 'Menunggu' }}</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button @click="selectedReg = {{ json_encode($reg) }}; showModal = true" class="p-1.5 bg-gray-100 hover:bg-blue-100 text-gray-600 hover:text-[#174E93] rounded-lg transition-colors" title="{{ __('cms.reports.btn_detail') }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </button>
                                        @if($reg->surat_file)
                                            <a href="{{ asset('storage/' . $reg->surat_file) }}" target="_blank" class="p-1.5 bg-gray-100 hover:bg-green-100 text-gray-600 hover:text-green-600 rounded-lg transition-colors" title="{{ __('cms.reports.btn_download') }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                            </a>
                                        @endif
                                        @if($hasAllPermission && $reg->status === 'pending')
                                            <button @click="updateVisitStatus({{ json_encode($reg) }}, 'approved')" class="p-1.5 bg-gray-100 hover:bg-emerald-100 text-gray-600 hover:text-emerald-600 rounded-lg transition-colors" title="Setujui Kunjungan">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </button>
                                            <button @click="updateVisitStatus({{ json_encode($reg) }}, 'rejected')" class="p-1.5 bg-gray-100 hover:bg-red-100 text-gray-600 hover:text-red-600 rounded-lg transition-colors" title="Tolak Kunjungan">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        @endif
                                        @if($hasAllPermission)
                                            <button @click="deleteVisitReg({{ $reg->id }}, '{{ addslashes($reg->name) }}')" class="p-1.5 bg-gray-100 hover:bg-red-100 text-gray-600 hover:text-red-600 rounded-lg transition-colors" title="Hapus Data">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-gray-400">{{ __('cms.reports.empty_kunjungan') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Detail Modal -->
        <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" @click.self="showModal = false">
            <div x-show="showModal" x-transition.opacity.scale.95 class="bg-white rounded-2xl shadow-xl max-w-lg w-full p-6 max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center border-b border-gray-100 pb-4 mb-4">
                    <h3 class="text-lg font-bold text-gray-900">{{ __('cms.reports.modal_kunjungan_title') }}</h3>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg focus:outline-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="space-y-4 text-sm" x-if="selectedReg">
                    <template x-if="selectedReg">
                        <div class="space-y-3">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-1 sm:gap-2 border-b border-gray-50 pb-2">
                                <span class="font-semibold text-gray-500">{{ __('cms.reports.modal_name') }}</span>
                                <span class="sm:col-span-2 text-gray-800 font-bold" x-text="selectedReg.name"></span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-1 sm:gap-2 border-b border-gray-50 pb-2">
                                <span class="font-semibold text-gray-500">{{ __('cms.reports.modal_email') }}</span>
                                <span class="sm:col-span-2 text-gray-800" x-text="selectedReg.email"></span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-1 sm:gap-2 border-b border-gray-50 pb-2">
                                <span class="font-semibold text-gray-500">{{ __('cms.reports.modal_phone') }}</span>
                                <span class="sm:col-span-2 text-gray-800" x-text="selectedReg.phone || '-'"></span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-1 sm:gap-2 border-b border-gray-50 pb-2">
                                <span class="font-semibold text-gray-500">{{ __('cms.reports.modal_inst') }}</span>
                                <span class="sm:col-span-2 text-gray-800" x-text="selectedReg.institution || '-'"></span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-1 sm:gap-2 border-b border-gray-50 pb-2">
                                <span class="font-semibold text-gray-500">{{ __('cms.reports.modal_position') }}</span>
                                <span class="sm:col-span-2 text-gray-800" x-text="selectedReg.position || '-'"></span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-1 sm:gap-2 border-b border-gray-50 pb-2">
                                <span class="font-semibold text-gray-500">{{ __('cms.reports.modal_visit_date') }}</span>
                                <span class="sm:col-span-2 text-gray-800 font-medium" x-text="selectedReg.visit_date ? selectedReg.visit_date.substring(0,10) : '-'"></span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-1 sm:gap-2 border-b border-gray-50 pb-2">
                                <span class="font-semibold text-gray-500">{{ __('cms.reports.modal_visit_time') }}</span>
                                <span class="sm:col-span-2 text-gray-800 font-medium" x-text="selectedReg.visit_time ? (selectedReg.visit_time.toLowerCase() == 'siang' ? translations.time_siang : translations.time_pagi) : translations.time_pagi"></span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-1 sm:gap-2 border-b border-gray-50 pb-2">
                                <span class="font-semibold text-gray-500">{{ __('cms.reports.modal_visitor_count') }}</span>
                                <span class="sm:col-span-2 text-blue-600 font-bold" x-text="(selectedReg.visitor_count || 1) + ' ' + translations.label_org_full"></span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-1 sm:gap-2 border-b border-gray-50 pb-2">
                                <span class="font-semibold text-gray-500">{{ __('cms.reports.modal_purpose') }}</span>
                                <span class="sm:col-span-2 text-gray-800 font-semibold" x-text="
                                    !selectedReg.visit_purpose ? '-' :
                                    (selectedReg.visit_purpose.toLowerCase() == 'edukasi' ? translations.purpose_edukasi : 
                                    (selectedReg.visit_purpose.toLowerCase() == 'penelitian' ? translations.purpose_penelitian : 
                                    (selectedReg.visit_purpose.toLowerCase() == 'kunker' ? translations.purpose_kunker : 
                                    selectedReg.visit_purpose)))
                                "></span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-1 sm:gap-2 border-b border-gray-50 pb-2">
                                <span class="font-semibold text-gray-500">{{ __('cms.reports.status') }}</span>
                                <span class="sm:col-span-2 text-gray-800 font-bold" x-text="selectedReg.status === 'approved' ? translations.status_approved : (selectedReg.status === 'rejected' ? translations.status_rejected : translations.status_pending)"></span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-1 sm:gap-2 border-b border-gray-50 pb-2" x-show="selectedReg.keterangan">
                                <span class="font-semibold text-gray-500">{{ __('cms.reports.remarks') }}</span>
                                <span class="sm:col-span-2 text-gray-800" x-text="selectedReg.keterangan || '-'"></span>
                            </div>

                            <template x-if="selectedReg.surat_file">
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-1 sm:gap-2 border-b border-gray-50 pb-2">
                                    <span class="font-semibold text-gray-500">{{ __('cms.reports.file_attachment') }}</span>
                                    <div class="sm:col-span-2">
                                        <a :href="'/storage/' + selectedReg.surat_file" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 text-blue-600 rounded-lg text-xs font-semibold hover:bg-blue-100 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                            {{ __('cms.reports.view_file') }}
                                        </a>
                                    </div>
                                </div>
                            </template>

                            <template x-if="selectedReg.form_data">
                                <div class="mt-4 pt-2 border-t border-gray-100">
                                    <h4 class="font-bold text-gray-700 mb-2">{{ __('cms.reports.modal_form_data') }}</h4>
                                    <div class="bg-gray-50 p-3 rounded-xl space-y-2 text-xs">
                                        <template x-for="(val, key) in selectedReg.form_data" :key="key">
                                            <div x-show="isFieldActive(key) && typeof val !== 'object'" class="flex flex-col border-b border-gray-200/60 pb-1.5 last:border-0 last:pb-0">
                                                <span class="font-bold text-gray-500" x-text="getFormFieldLabel(key)"></span>
                                                <span class="text-gray-800 mt-0.5" x-text="val || '-'"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                <div class="mt-6 pt-4 border-t border-gray-100 flex justify-end gap-3">
                    <button @click="showModal = false" class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-colors text-sm">{{ __('cms.reports.btn_close') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- DataTables Buttons (export) --}}
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/pdfmake@0.2.7/build/vfs_fonts.js"></script>

    <script>
        function kunjunganReportComponent() {
            return {
                tf: '{{ $tf }}', 
                showCustom: {{ $tf === 'custom' ? 'true' : 'false' }}, 
                selectedReg: null, 
                showModal: false,
                translations: {
                    time_pagi: @json(__('home.layanan_publik.form_time_pagi')),
                    time_siang: @json(__('home.layanan_publik.form_time_siang')),
                    label_org_full: @json(__('cms.reports.label_org_full')),
                    purpose_edukasi: @json(__('cms.reports.purpose_edukasi')),
                    purpose_penelitian: @json(__('cms.reports.purpose_penelitian')),
                    purpose_kunker: @json(__('cms.reports.purpose_kunker')),
                    status_approved: @json(__('cms.reports.status_approved')),
                    status_rejected: @json(__('cms.reports.status_rejected')),
                    status_pending: @json(__('cms.reports.status_pending'))
                },
                isFieldActive(key) {
                    const fields = window.formFields || [];
                    return fields.some(f => f.id === key || f.name === key);
                },
                getFormFieldLabel(key) {
                    const locale = '{{ app()->getLocale() }}';
                    const fields = window.formFields || [];
                    const field = fields.find(f => f.id === key || f.name === key);
                    if (field) {
                        if (locale === 'en' && field.label_en) {
                            return field.label_en;
                        }
                        return field.label || field.id;
                    }
                    const commonTranslations = {
                        'name': { id: 'Nama Lengkap', en: 'Full Name' },
                        'email': { id: 'Surel / Email', en: 'Email' },
                        'phone': { id: 'Telepon / WhatsApp', en: 'Phone / WhatsApp' },
                        'institution': { id: 'Instansi / Organisasi', en: 'Institution / Organization' },
                        'position': { id: 'Jabatan / Pekerjaan', en: 'Position' },
                        'visit_date': { id: 'Tanggal Kunjungan', en: 'Visit Date' },
                        'visit_time': { id: 'Waktu Kunjungan', en: 'Visit Time' },
                        'visitor_count': { id: 'Jumlah Peserta', en: 'Visitor Count' },
                        'visit_purpose': { id: 'Tujuan Kunjungan', en: 'Visit Purpose' },
                        'surat_file': { id: 'Surat Permohonan', en: 'Request Letter' }
                    };
                    if (commonTranslations[key]) {
                        return locale === 'en' ? commonTranslations[key].en : commonTranslations[key].id;
                    }
                    return key.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
                },
                updateVisitStatus(reg, newStatus) {
                    const statusText = newStatus === 'approved' ? 'menyetujui' : 'menolak';
                    const confirmButtonColor = newStatus === 'approved' ? '#10B981' : '#EF4444';
                    const confirmButtonText = newStatus === 'approved' ? 'Setujui' : 'Tolak';
                    
                    Swal.fire({
                        title: `Konfirmasi ${newStatus === 'approved' ? 'Persetujuan' : 'Penolakan'}`,
                        text: `Apakah Anda yakin ingin ${statusText} permohonan kunjungan dari ${reg.name}?`,
                        input: 'textarea',
                        inputPlaceholder: 'Tulis catatan/keterangan tambahan di sini (opsional)...',
                        inputAttributes: {
                            autocapitalize: 'off'
                        },
                        showCancelButton: true,
                        confirmButtonText: confirmButtonText,
                        cancelButtonText: 'Batal',
                        confirmButtonColor: confirmButtonColor,
                        cancelButtonColor: '#6B7280',
                        showLoaderOnConfirm: true,
                        preConfirm: (keterangan) => {
                            return fetch(`/cms/reports/kunjungan/${reg.id}/status`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    status: newStatus,
                                    keterangan: keterangan
                                })
                            })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error(response.statusText);
                                }
                                return response.json();
                            })
                            .catch(error => {
                                Swal.showValidationMessage(`Request failed: ${error}`);
                            });
                        },
                        allowOutsideClick: () => !Swal.isLoading()
                    }).then((result) => {
                        if (result.isConfirmed && result.value && result.value.success) {
                            Swal.fire({
                                title: 'Sukses!',
                                text: result.value.message,
                                icon: 'success',
                                confirmButtonColor: '#174E93'
                            }).then(() => {
                                window.location.reload();
                            });
                        }
                    });
                },
                deleteVisitReg(id, name) {
                    Swal.fire({
                        title: 'Hapus Data?',
                        html: `Anda akan menghapus data pendaftaran kunjungan dari <strong>${name}</strong>.<br><span class="text-sm text-red-500">Tindakan ini tidak dapat dibatalkan.</span>`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Hapus',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#EF4444',
                        cancelButtonColor: '#6B7280',
                        showLoaderOnConfirm: true,
                        preConfirm: () => {
                            return fetch(`/cms/reports/kunjungan/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                    'Accept': 'application/json'
                                }
                            })
                            .then(response => {
                                if (!response.ok) throw new Error(response.statusText);
                                return response.json();
                            })
                            .catch(error => {
                                Swal.showValidationMessage(`Gagal menghapus: ${error}`);
                            });
                        },
                        allowOutsideClick: () => !Swal.isLoading()
                    }).then((result) => {
                        if (result.isConfirmed && result.value && result.value.success) {
                            Swal.fire({
                                title: 'Berhasil Dihapus!',
                                text: result.value.message,
                                icon: 'success',
                                confirmButtonColor: '#174E93'
                            }).then(() => {
                                window.location.reload();
                            });
                        }
                    });
                }
            };
        }

        window.kunjunganI18n = {
            btnExport: @json(__('cms.pengguna.btn_export')),
            btnCopy: @json(__('cms.pengguna.btn_copy')),
            btnCsv: @json(__('cms.pengguna.btn_csv')),
            btnExcel: @json(__('cms.pengguna.btn_excel')),
            btnWord: @json(__('cms.pengguna.btn_word')),
            btnPdf: @json(__('cms.pengguna.btn_pdf')),
            btnPrint: @json(__('cms.pengguna.btn_print')),
            dtSearchPlaceholder: @json(__('cms.datatable.search_placeholder')),
            title: @json(__('cms.reports.kunjungan_title')),
            labelOrg: @json(__('cms.reports.label_org')),
            lineSeriesName: @json(__('cms.reports.chart_trend_title')),
        };

        window.LaravelDT = {
            dtInfo: @json(__('cms.datatable.info')),
            dtInfoEmpty: @json(__('cms.datatable.info_empty')),
            dtInfoFiltered: @json(__('cms.datatable.info_filtered')),
            dtZeroRecords: @json(__('cms.datatable.zero_records')),
            dtSearchPlaceholder: @json(__('cms.datatable.search_placeholder')),
        };

        window.kunjunganChartData = {
            pieData: @json($pieData),
            pieLabels: @json($pieLabels),
            pieColors: @json($pieColors),
            lineLabels: @json($lineLabels),
            lineSeries: @json($lineSeries),
        };
        window.formFields = @json($formFields ?? []);
    </script>
    <script src="{{ asset('js/cms/features/reports/kunjungan.js') }}" defer></script>
@endpush
