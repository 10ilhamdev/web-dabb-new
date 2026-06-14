@extends('layouts.app')

@section('title', __('dashboard.sidebar.reports_online'))

@push('styles')
<style>
    #tableActivity, #tableRealtime {
        table-layout: fixed !important;
        width: 100% !important;
    }
</style>
@endpush

@section('header')
    <div class="text-[13px] text-gray-500 font-medium">
        <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-600">{{ __('dashboard.header.breadcrumb_home') }}</a> /
        <span class="text-gray-400">{{ __('dashboard.sidebar.reports') }}</span> /
        <span class="text-[#0ea5e9]">{{ __('dashboard.sidebar.reports_online') }}</span>
    </div>
@endsection

@section('content')
    <div class="mb-8" x-data="{ tf: '{{ $tf }}', showCustom: {{ $tf === 'custom' ? 'true' : 'false' }} }">
        <!-- Title & Filter Header -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-[22px] font-bold text-[#1E293B] mb-1">{{ __('dashboard.sidebar.reports_online') }}</h1>
                <p class="text-gray-500 text-sm">{{ __('cms.reports.online_subtitle') }}</p>
            </div>

            <!-- Filter Controls -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 shrink-0">
                <!-- Preset Buttons -->
                <div class="inline-flex bg-white p-1.5 rounded-xl shadow-sm border border-gray-100 text-xs font-medium text-gray-600 overflow-x-auto">
                    <a href="{{ route('cms.reports.online', ['tf' => 'day']) }}" class="px-3 py-1.5 rounded-lg transition-colors whitespace-nowrap {{ $tf === 'day' ? 'bg-[#174E93] text-white font-bold' : 'hover:bg-gray-100' }}">{{ __('cms.reports.preset_today') }}</a>
                    <a href="{{ route('cms.reports.online', ['tf' => 'week']) }}" class="px-3 py-1.5 rounded-lg transition-colors whitespace-nowrap {{ $tf === 'week' ? 'bg-[#174E93] text-white font-bold' : 'hover:bg-gray-100' }}">{{ __('cms.reports.preset_week') }}</a>
                    <a href="{{ route('cms.reports.online', ['tf' => 'month']) }}" class="px-3 py-1.5 rounded-lg transition-colors whitespace-nowrap {{ $tf === 'month' ? 'bg-[#174E93] text-white font-bold' : 'hover:bg-gray-100' }}">{{ __('cms.reports.preset_month') }}</a>
                    <a href="{{ route('cms.reports.online', ['tf' => 'year']) }}" class="px-3 py-1.5 rounded-lg transition-colors whitespace-nowrap {{ $tf === 'year' ? 'bg-[#174E93] text-white font-bold' : 'hover:bg-gray-100' }}">{{ __('cms.reports.preset_year') }}</a>
                    <button @click="showCustom = true; tf = 'custom'" class="px-3 py-1.5 rounded-lg transition-colors whitespace-nowrap {{ $tf === 'custom' ? 'bg-[#174E93] text-white font-bold' : 'hover:bg-gray-100' }}">{{ __('cms.reports.preset_custom') }}</button>
                </div>

                <!-- Custom Date Range Form -->
                <form method="GET" action="{{ route('cms.reports.online') }}" x-show="showCustom" x-transition.opacity class="flex items-center gap-2 bg-white p-1.5 rounded-xl shadow-sm border border-gray-100 shrink-0 overflow-x-auto" x-cloak>
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
        </div>        @if($hasAllPermission)
        <!-- Summary Cards Grid (Admin/Pegawai Only) -->
        <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-3 gap-6 mb-8">
            <!-- Card 1: Realtime Online -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition-all">
                <div class="flex justify-between items-start">
                    <div class="min-w-0 flex-1 pr-2">
                        <div class="text-3xl font-bold text-emerald-600 leading-tight truncate">{{ number_format($onlineCount) }}</div>
                        <div class="text-xs font-medium text-gray-500 mt-1 truncate">{{ __('cms.reports.online_stat_realtime') }}</div>
                    </div>
                    <div class="p-3 bg-emerald-50 rounded-xl text-emerald-600 shrink-0 animate-pulse">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="mt-4 text-xs text-emerald-600 bg-emerald-50/50 px-3 py-2 rounded-xl font-medium flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping shrink-0"></span>
                    <span class="truncate">{{ __('cms.reports.online_stat_realtime_sub') }}</span>
                </div>
            </div>

            <!-- Card 2: Total Active Today -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition-all">
                <div class="flex justify-between items-start">
                    <div class="min-w-0 flex-1 pr-2">
                        <div class="text-3xl font-bold text-blue-600 leading-tight truncate">{{ number_format($totalActiveToday) }}</div>
                        <div class="text-xs font-medium text-gray-500 mt-1 truncate">{{ __('cms.reports.online_stat_active') }}</div>
                    </div>
                    <div class="p-3 bg-blue-50 rounded-xl text-blue-600 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2.5a.5.5 0 01-.5-.5v-1a2 2 0 012-2h3m10 0h3a2 2 0 012 2v1a.5.5 0 01-.5.5H17m-10 0v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                </div>
                <div class="mt-4 text-xs text-blue-600 bg-blue-50/50 px-3 py-2 rounded-xl font-medium flex items-center gap-1.5">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="truncate">{{ $subtitle }}</span>
                </div>
            </div>

            <!-- Card 3: Average Online Per Hour -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition-all">
                <div class="flex justify-between items-start">
                    <div class="min-w-0 flex-1 pr-2">
                        <div class="text-3xl font-bold text-purple-600 leading-tight truncate">{{ $avgOnlinePerHour }}</div>
                        <div class="text-xs font-medium text-gray-500 mt-1 truncate">{{ __('cms.reports.online_stat_avg') }}</div>
                    </div>
                    <div class="p-3 bg-purple-50 rounded-xl text-purple-600 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17V5a2 2 0 012-2h6a2 2 0 012 2v6h-4a1 1 0 000 2h4v1h-4a1 1 0 000 2h4v1h-4a1 1 0 000 2h4v3h-8l-3-5v-3h4a1 1 0 000-2H7v5zM11 6h2"></path></svg>
                    </div>
                </div>
                <div class="mt-4 text-xs text-purple-600 bg-purple-50/50 px-3 py-2 rounded-xl font-medium flex items-center gap-1.5">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span class="truncate">{{ __('cms.reports.online_stat_avg_sub') }}</span>
                </div>
            </div>
        </div>
        @endif

        <!-- Full Width Card: Peak Online Hours Detail -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 mb-8 hover:shadow-md transition-all">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-6 border-b border-gray-100">
                <div class="flex items-start gap-4">
                    <div class="p-3.5 bg-amber-50 rounded-2xl text-amber-600 shrink-0 mt-1 md:mt-0">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">{{ __('cms.reports.online_stat_peak') }}</h2>
                        <p class="text-xs text-gray-400 mt-1">{{ __('cms.reports.online_stat_peak_desc') }}</p>
                    </div>
                </div>
                @if($hasAllPermission)
                <div class="flex items-center gap-4 bg-amber-50/50 px-5 py-3 rounded-2xl border border-amber-100/50 shrink-0 self-start md:self-auto">
                    <div class="text-3xl font-extrabold text-amber-600">{{ number_format($maxOnline) }}</div>
                    <div class="text-xs font-semibold text-amber-700 uppercase tracking-wider leading-tight">{!! __('cms.reports.peak_active_users') !!}</div>
                </div>
                @endif
            </div>

            <div class="mt-6">
                <div class="flex items-center gap-2 mb-4">
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="text-xs font-bold text-gray-700 uppercase tracking-wider">{{ __('cms.reports.peak_hours_list', ['count' => count($peakHours)]) }}</span>
                </div>
                @if(empty($peakHours) || $maxOnline == 0)
                    <div class="text-sm text-gray-400 italic bg-gray-50/50 p-4 rounded-xl text-center">{{ __('cms.reports.empty_peak_hours') }}</div>
                @else
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                        @foreach($peakHours as $hour)
                            <div class="bg-amber-50/40 hover:bg-amber-100/60 border border-amber-100/80 rounded-xl p-3 flex items-center justify-between transition-colors shadow-2xs">
                                <span class="font-bold text-amber-800 text-sm tracking-wide">{{ $hour }}</span>
                                <span class="flex h-2 w-2 relative">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-50-500"></span>
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Line Chart: Hourly Trend -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 mb-8 flex flex-col justify-between">
            <div class="mb-4">
                <h2 class="text-lg font-bold text-gray-800">{{ __('cms.reports.chart_online_title') }}</h2>
                <p class="text-xs text-gray-400">{{ __('cms.reports.chart_online_sub') }}</p>
            </div>
            <div class="w-full relative" style="height: 400px;">
                <div id="lineChart"></div>
            </div>
        </div>

        <!-- Grid Tables: Realtime Users & Activity Logs -->
        @if($hasAllPermission)
        {{-- Admin/Pegawai: Tampilkan Daftar Pengguna Online + Riwayat Aktivitas Semua User --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left: Real-time Online Users List -->
            <div class="lg:col-span-1 bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col">
                <div class="mb-6">
                    <h2 class="text-lg font-bold text-gray-800">{{ __('cms.reports.table_realtime_title') }}</h2>
                    <p class="text-xs text-gray-400">{{ __('cms.reports.table_realtime_sub') }}</p>
                </div>

                <div class="overflow-x-auto flex-1">
                    <table class="w-full text-left border-collapse dataTable" id="tableRealtime">
                        <thead>
                            <tr class="border-b border-gray-100 text-[12px] font-semibold text-gray-500 uppercase bg-gray-50/50">
                                <th class="py-3 px-4 rounded-l-xl">{{ __('cms.reports.col_user') }}</th>
                                <th class="py-3 px-4 text-center rounded-r-xl">{{ __('cms.reports.col_last_activity') }}</th>
                            </tr>
                        </thead>
                        <tbody class="text-[13px] text-gray-600 divide-y divide-gray-50">
                            @forelse($onlineUsersList as $u)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="py-3 px-4 font-medium text-gray-900">
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 rounded-full bg-[#174E93]/10 text-[#174E93] flex items-center justify-center font-bold text-xs uppercase shrink-0">
                                                {{ substr($u['name'], 0, 2) }}
                                            </div>
                                            <div>
                                                <div class="font-bold text-gray-800 leading-tight">{{ $u['name'] }}</div>
                                                <div class="text-[11px] text-gray-400 uppercase tracking-wider">{{ $u['role'] }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 text-center text-xs font-medium text-emerald-600 whitespace-nowrap">
                                        <div class="inline-flex items-center gap-1.5 bg-emerald-50 px-2.5 py-1 rounded-full">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span>
                                            <span>{{ $u['last_activity'] }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="py-8 text-center text-gray-400">{{ __('cms.reports.empty_online') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Right: Today's Activity Logs -->
            <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col min-w-0">
                <div class="mb-6">
                    <h2 class="text-lg font-bold text-gray-800">{{ __('cms.reports.table_activity_title') }}</h2>
                    <p class="text-xs text-gray-400">{{ __('cms.reports.table_activity_sub') }}</p>
                </div>

                <div class="overflow-x-auto flex-1">
                    <table class="w-full text-left border-collapse dataTable table-fixed" id="tableActivity">
                        <thead>
                            <tr class="border-b border-gray-100 text-[12px] font-semibold text-gray-500 uppercase bg-gray-50/50">
                                <th class="py-3 px-4 rounded-l-xl w-[8%]">{{ __('cms.reports.col_no') }}</th>
                                <th class="py-3 px-4 w-[37%]">{{ __('cms.reports.col_user') }}</th>
                                <th class="py-3 px-4 text-center w-[20%]">{{ __('cms.reports.col_page_views') }}</th>
                                <th class="py-3 px-4 rounded-r-xl w-[35%]">{{ __('cms.reports.col_last_access') }}</th>
                            </tr>
                        </thead>
                        <tbody class="text-[13px] text-gray-600 divide-y divide-gray-50">
                            @forelse($todayActivityLogs as $index => $log)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="py-3.5 px-4 font-medium text-gray-900">{{ $loop->iteration }}</td>
                                    <td class="py-3.5 px-4 font-medium text-gray-900">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs uppercase shrink-0">
                                                {{ substr($log['name'], 0, 2) }}
                                            </div>
                                            <div>
                                                <div class="font-bold text-gray-800 leading-tight">{{ $log['name'] }}</div>
                                                <div class="text-[11px] text-gray-400 uppercase tracking-wider">{{ $log['role'] }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-50 text-blue-600 min-w-[2.5rem]">
                                            {{ number_format($log['total_views']) }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-xs text-gray-500">
                                        <div class="font-medium text-blue-600 truncate max-w-xs mb-0.5" title="{{ $log['last_path'] }}">
                                            {{ $log['last_path'] }}
                                        </div>
                                        <div class="text-[11px] text-gray-400">
                                            {{ $log['last_activity'] }}
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-gray-400">{{ __('cms.reports.empty_activity') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @else
        {{-- User Biasa: Tampilkan Data Diri Sendiri + Riwayat Aktivitas Sendiri --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left: Profil Saya -->
            <div class="lg:col-span-1 bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col">
                <div class="mb-6">
                    <h2 class="text-lg font-bold text-gray-800">{{ __('cms.reports.my_profile_title') }}</h2>
                    <p class="text-xs text-gray-400">{{ __('cms.reports.my_profile_sub') }}</p>
                </div>

                @php $me = $onlineUsersList->first(); @endphp
                <div class="flex flex-col items-center gap-4 py-4">
                    <!-- Avatar -->
                    <div class="w-20 h-20 rounded-full bg-[#174E93]/10 text-[#174E93] flex items-center justify-center font-bold text-2xl uppercase shrink-0 border-4 border-[#174E93]/20">
                        {{ substr(auth()->user()->name ?? 'U', 0, 2) }}
                    </div>
                    <div class="text-center">
                        <div class="font-bold text-gray-800 text-lg leading-tight">{{ auth()->user()->name }}</div>
                        <div class="text-xs text-gray-400 uppercase tracking-wider mt-0.5">{{ auth()->user()->role }}</div>
                        <div class="text-xs text-gray-500 mt-1">{{ auth()->user()->email }}</div>
                    </div>

                    @if($me)
                        <!-- Status Online -->
                        <div class="inline-flex items-center gap-2 bg-emerald-50 border border-emerald-100 px-4 py-2 rounded-full">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping shrink-0"></span>
                            <span class="text-xs font-semibold text-emerald-700">Online &bull; {{ $me['last_activity'] }}</span>
                        </div>
                    @else
                        <div class="inline-flex items-center gap-2 bg-gray-100 border border-gray-200 px-4 py-2 rounded-full">
                            <span class="w-2 h-2 rounded-full bg-gray-400 shrink-0"></span>
                            <span class="text-xs font-semibold text-gray-500">{{ __('cms.reports.empty_online') }}</span>
                        </div>
                    @endif
                </div>

                <!-- Statistik Singkat -->
                <div class="mt-6 grid grid-cols-2 gap-3">
                    @php $totalMyViews = collect($todayActivityLogs)->sum('total_views'); @endphp
                    <div class="bg-blue-50/60 rounded-xl p-3 text-center border border-blue-100/60">
                        <div class="text-2xl font-extrabold text-blue-600">{{ number_format($totalMyViews) }}</div>
                        <div class="text-[11px] text-blue-500 font-medium mt-0.5">{{ __('cms.reports.col_page_views') }}</div>
                    </div>
                    <div class="bg-amber-50/60 rounded-xl p-3 text-center border border-amber-100/60">
                        <div class="text-2xl font-extrabold text-amber-600">{{ $peakHourLabel }}</div>
                        <div class="text-[11px] text-amber-500 font-medium mt-0.5">{{ __('cms.reports.online_stat_peak') }}</div>
                    </div>
                </div>
            </div>

            <!-- Right: Riwayat Aktivitas Saya -->
            <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col min-w-0">
                <div class="mb-6">
                    <h2 class="text-lg font-bold text-gray-800">{{ __('cms.reports.my_activity_title') }}</h2>
                    <p class="text-xs text-gray-400">{{ __('cms.reports.my_activity_sub') }}</p>
                </div>

                <div class="overflow-x-auto flex-1">
                    <table class="w-full text-left border-collapse dataTable table-fixed" id="tableActivity">
                        <thead>
                            <tr class="border-b border-gray-100 text-[12px] font-semibold text-gray-500 uppercase bg-gray-50/50">
                                <th class="py-3 px-4 rounded-l-xl w-[10%]">{{ __('cms.reports.col_no') }}</th>
                                <th class="py-3 px-4 text-center w-[30%]">{{ __('cms.reports.col_page_views') }}</th>
                                <th class="py-3 px-4 rounded-r-xl w-[60%]">{{ __('cms.reports.col_last_access') }}</th>
                            </tr>
                        </thead>
                        <tbody class="text-[13px] text-gray-600 divide-y divide-gray-50">
                            @forelse($todayActivityLogs as $index => $log)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="py-3.5 px-4 font-medium text-gray-900">{{ $loop->iteration }}</td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-50 text-blue-600 min-w-[2.5rem]">
                                            {{ number_format($log['total_views']) }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-xs text-gray-500">
                                        <div class="font-medium text-blue-600 truncate max-w-xs mb-0.5" title="{{ $log['last_path'] }}">
                                            {{ $log['last_path'] }}
                                        </div>
                                        <div class="text-[11px] text-gray-400">
                                            {{ $log['last_activity'] }}
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-8 text-center text-gray-400">{{ __('cms.reports.empty_activity') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
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
        window.onlineI18n = {
            btnExport: @json(__('cms.pengguna.btn_export')),
            btnCopy: @json(__('cms.pengguna.btn_copy')),
            btnCsv: @json(__('cms.pengguna.btn_csv')),
            btnExcel: @json(__('cms.pengguna.btn_excel')),
            btnWord: @json(__('cms.pengguna.btn_word')),
            btnPdf: @json(__('cms.pengguna.btn_pdf')),
            btnPrint: @json(__('cms.pengguna.btn_print')),
            dtSearchPlaceholder: @json(__('cms.datatable.search_placeholder')),
            title: @json(__('cms.reports.online_title')),
            seriesName: @json(__('cms.reports.chart_online_title')),
            tooltipUnit: @json(__('cms.reports.tooltip_active_users')),
        };

        window.LaravelDT = {
            dtInfo: @json(__('cms.datatable.info')),
            dtInfoEmpty: @json(__('cms.datatable.info_empty')),
            dtInfoFiltered: @json(__('cms.datatable.info_filtered')),
            dtZeroRecords: @json(__('cms.datatable.zero_records')),
            dtSearchPlaceholder: @json(__('cms.datatable.search_placeholder')),
        };

        window.onlineChartData = {
            lineLabels: @json($lineLabels),
            lineSeries: @json($lineSeries),
        };
    </script>
    <script src="{{ asset('js/cms/features/reports/online.js') }}?v={{ time() }}"></script>
@endpush
