@extends('layouts.app')

@section('title', __('dashboard.sidebar.reports_pengunjung'))

@section('header')
    <div class="text-[13px] text-gray-500 font-medium">
        <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-600">{{ __('dashboard.header.breadcrumb_home') }}</a> /
        <span class="text-gray-400">{{ __('dashboard.sidebar.reports') }}</span> /
        <span class="text-[#0ea5e9]">{{ __('dashboard.sidebar.reports_pengunjung') }}</span>
    </div>
@endsection

@section('content')
    <div class="mb-8" x-data="{ tf: '{{ $tf }}', showCustom: {{ $tf === 'custom' ? 'true' : 'false' }} }">
        <!-- Title & Filter Header -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-[22px] font-bold text-[#1E293B] mb-1">{{ __('dashboard.sidebar.reports_pengunjung') }}</h1>
                <p class="text-gray-500 text-sm">{{ __('cms.reports.pengunjung_subtitle') }}</p>
            </div>

            <!-- Filter Controls -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 shrink-0">
                <!-- Preset Buttons -->
                <div class="inline-flex bg-white p-1.5 rounded-xl shadow-sm border border-gray-100 text-xs font-medium text-gray-600 overflow-x-auto">
                    <a href="{{ route('cms.reports.pengunjung', ['tf' => 'day']) }}" class="px-3 py-1.5 rounded-lg transition-colors whitespace-nowrap {{ $tf === 'day' ? 'bg-[#174E93] text-white font-bold' : 'hover:bg-gray-100' }}">{{ __('cms.reports.preset_today') }}</a>
                    <a href="{{ route('cms.reports.pengunjung', ['tf' => 'week']) }}" class="px-3 py-1.5 rounded-lg transition-colors whitespace-nowrap {{ $tf === 'week' ? 'bg-[#174E93] text-white font-bold' : 'hover:bg-gray-100' }}">{{ __('cms.reports.preset_week') }}</a>
                    <a href="{{ route('cms.reports.pengunjung', ['tf' => 'month']) }}" class="px-3 py-1.5 rounded-lg transition-colors whitespace-nowrap {{ $tf === 'month' ? 'bg-[#174E93] text-white font-bold' : 'hover:bg-gray-100' }}">{{ __('cms.reports.preset_month') }}</a>
                    <a href="{{ route('cms.reports.pengunjung', ['tf' => 'year']) }}" class="px-3 py-1.5 rounded-lg transition-colors whitespace-nowrap {{ $tf === 'year' ? 'bg-[#174E93] text-white font-bold' : 'hover:bg-gray-100' }}">{{ __('cms.reports.preset_year') }}</a>
                    <button @click="showCustom = true; tf = 'custom'" class="px-3 py-1.5 rounded-lg transition-colors whitespace-nowrap {{ $tf === 'custom' ? 'bg-[#174E93] text-white font-bold' : 'hover:bg-gray-100' }}">{{ __('cms.reports.preset_custom') }}</button>
                </div>

                <!-- Custom Date Range Form -->
                <form method="GET" action="{{ route('cms.reports.pengunjung') }}" x-show="showCustom" x-transition.opacity class="flex items-center gap-2 bg-white p-1.5 rounded-xl shadow-sm border border-gray-100 shrink-0 overflow-x-auto" x-cloak>
                    <input type="hidden" name="tf" value="custom">
                    <div class="flex items-center gap-2 px-2 shrink-0">
                        <span class="text-xs text-gray-400 font-medium">{{ __('cms.reports.filter_start') }}</span>
                        <input type="date" name="start_date" value="{{ $startDate ?? '' }}" required class="text-xs border-none bg-gray-50 px-2.5 py-1 rounded-lg focus:ring-1 focus:ring-blue-500 text-gray-700 font-medium">
                    </div>
                    <div class="flex items-center gap-2 px-2 border-l border-gray-100 shrink-0">
                        <span class="text-xs text-gray-400 font-medium">{{ __('cms.reports.filter_end') }}</span>
                        <input type="date" name="end_date" value="{{ $endDate ?? '' }}" required class="text-xs border-none bg-gray-50 px-2.5 py-1 rounded-lg focus:ring-1 focus:ring-blue-500 text-gray-700 font-medium">
                    </div>
                    <button type="submit" class="px-3 py-1 bg-[#174E93] hover:bg-blue-800 text-white rounded-lg font-semibold text-xs transition-colors shadow-sm shrink-0">{{ __('cms.reports.btn_filter') }}</button>
                    <button type="button" @click="showCustom = false" class="px-2 py-1 text-gray-400 hover:text-gray-600 text-xs shrink-0" title="{{ __('cms.reports.btn_cancel') }}">✕</button>
                </form>
            </div>
        </div>

        <!-- Summary & Chart Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-8">
            <!-- Left: Summary Card & Page Breakdown -->
            <div class="lg:col-span-1 flex flex-col gap-6">
                <!-- Total Card -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition-all">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="text-3xl font-bold text-gray-900 leading-tight">{{ number_format($totalViews) }}</div>
                            <div class="text-xs font-medium text-gray-500 mt-1">{{ __('cms.reports.total_views') }}</div>
                        </div>
                        <div class="p-3 bg-blue-50 rounded-xl text-blue-600 shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        </div>
                    </div>
                    <div class="mt-4 text-xs text-blue-600 bg-blue-50/50 px-3 py-2 rounded-xl font-medium flex items-center gap-1.5">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>{{ $subtitle }}</span>
                    </div>
                </div>

                <!-- Page Breakdown Table -->
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex-1 flex flex-col">
                    <h3 class="text-sm font-bold text-gray-800 mb-3 border-b border-gray-100 pb-2">{{ __('cms.reports.page_breakdown') }}</h3>
                    <div class="space-y-3 flex-1 overflow-y-auto pr-1" style="max-height: 290px;">
                        @foreach($pages as $key => $p)
                            @php
                                $pct = $totalViews > 0 ? round(($p['count'] / $totalViews) * 100, 1) : 0;
                            @endphp
                            <div>
                                <div class="flex justify-between text-xs font-semibold mb-1">
                                    <span class="text-gray-700">{{ $p['label'] }}</span>
                                    <span class="text-gray-900">{{ number_format($p['count']) }} <span class="text-gray-400 font-normal">({{ $pct }}%)</span></span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-[#174E93] h-1.5 rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Right: Bar Chart -->
            <div class="lg:col-span-3 bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between">
                <div class="mb-4">
                    <h2 class="text-lg font-bold text-gray-800">{{ __('cms.reports.chart_views_title') }}</h2>
                    <p class="text-xs text-gray-400">{{ __('cms.reports.chart_views_sub') }}</p>
                </div>
                <div class="w-full relative my-auto" style="min-height: 380px;">
                    <div id="barChart"></div>
                </div>
            </div>
        </div>

        <!-- Data Table: Recent Logs -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-lg font-bold text-gray-800">{{ __('cms.reports.table_pengunjung_title') }}</h2>
                    <p class="text-xs text-gray-400">{{ __('cms.reports.table_pengunjung_sub') }}</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse dataTable" id="tablePengunjung">
                    <thead>
                        <tr class="border-b border-gray-100 text-[12px] font-semibold text-gray-500 uppercase bg-gray-50/50">
                            <th class="py-3 px-4 rounded-l-xl">{{ __('cms.reports.col_no') }}</th>
                            <th class="py-3 px-4">{{ __('cms.reports.col_ip') }}</th>
                            <th class="py-3 px-4">{{ __('cms.reports.col_path') }}</th>
                            <th class="py-3 px-4">{{ __('cms.reports.col_device') }}</th>
                            <th class="py-3 px-4 text-center rounded-r-xl">{{ __('cms.reports.col_access_time') }}</th>
                        </tr>
                    </thead>
                    <tbody class="text-[13px] text-gray-600 divide-y divide-gray-50">
                        @forelse($recentLogs as $index => $log)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="py-3.5 px-4 font-medium text-gray-900">{{ $loop->iteration }}</td>
                                <td class="py-3.5 px-4 font-mono text-xs text-gray-800 font-bold">
                                    {{ $log->ip_address ?: '127.0.0.1' }}
                                </td>
                                <td class="py-3.5 px-4 font-medium text-blue-600">
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                        <span class="truncate max-w-xs">{{ $log->path ?: '/' }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 text-xs text-gray-500 max-w-md truncate" title="{{ $log->user_agent }}">
                                    {{ $log->user_agent ?: '-' }}
                                </td>
                                <td class="py-3.5 px-4 text-center text-xs font-medium text-gray-700 whitespace-nowrap">
                                    {{ $log->created_at ? $log->created_at->format('d M Y, H:i:s') : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-gray-400">{{ __('cms.reports.empty_pengunjung') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
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
        window.pengunjungI18n = {
            btnExport: @json(__('cms.pengguna.btn_export')),
            btnCopy: @json(__('cms.pengguna.btn_copy')),
            btnCsv: @json(__('cms.pengguna.btn_csv')),
            btnExcel: @json(__('cms.pengguna.btn_excel')),
            btnWord: @json(__('cms.pengguna.btn_word')),
            btnPdf: @json(__('cms.pengguna.btn_pdf')),
            btnPrint: @json(__('cms.pengguna.btn_print')),
            dtSearchPlaceholder: @json(__('cms.datatable.search_placeholder')),
            title: @json(__('cms.reports.pengunjung_title')),
            seriesName: @json(__('cms.reports.chart_views_title')),
            tooltipUnit: @json(__('cms.reports.total_views')),
        };

        window.LaravelDT = {
            dtInfo: @json(__('cms.datatable.info')),
            dtInfoEmpty: @json(__('cms.datatable.info_empty')),
            dtInfoFiltered: @json(__('cms.datatable.info_filtered')),
            dtZeroRecords: @json(__('cms.datatable.zero_records')),
            dtSearchPlaceholder: @json(__('cms.datatable.search_placeholder')),
        };

        window.pengunjungChartData = {
            barLabels: @json($barLabels),
            barSeries: @json($barSeries),
        };
    </script>
    <script src="{{ asset('js/cms/features/reports/pengunjung.js') }}"></script>
@endpush
