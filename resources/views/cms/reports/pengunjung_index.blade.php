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
                <p class="text-gray-500 text-sm">{{ __('cms.reports.pengunjung_index_subtitle') }}</p>
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

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Page Views Card -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition-all h-full">
                <div class="flex flex-col gap-4">
                    <div class="p-4 bg-blue-50 rounded-xl text-blue-600 self-start">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">{{ __('cms.reports.page_views_title') }}</h2>
                        <p class="text-sm text-gray-500 mt-1">{{ __('cms.reports.page_views_desc') }}</p>
                    </div>
                    <div class="text-4xl font-extrabold text-[#174E93] my-4">
                        {{ number_format($totalPageViews) }} <span class="text-base font-medium text-gray-400">views</span>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100 flex justify-end">
                    <a href="{{ route('cms.reports.pengunjung.page_views', ['tf' => $tf, 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="px-4 py-2 bg-gray-50 hover:bg-gray-100 text-blue-600 rounded-lg text-sm font-bold transition-colors flex items-center gap-2">
                        {{ __('cms.reports.btn_view_detail') }}
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>
            </div>

            <!-- Unique Visitors Card -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition-all h-full">
                <div class="flex flex-col gap-4">
                    <div class="p-4 bg-emerald-50 rounded-xl text-emerald-600 self-start">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">{{ __('cms.reports.unique_visitors_title') }}</h2>
                        <p class="text-sm text-gray-500 mt-1">{{ __('cms.reports.unique_visitors_desc') }}</p>
                    </div>
                    <div class="text-4xl font-extrabold text-emerald-600 my-4">
                        {{ number_format($totalUniqueVisitors) }} <span class="text-base font-medium text-gray-400">pengunjung</span>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100 flex justify-end">
                    <a href="{{ route('cms.reports.pengunjung.unique', ['tf' => $tf, 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="px-4 py-2 bg-gray-50 hover:bg-gray-100 text-emerald-600 rounded-lg text-sm font-bold transition-colors flex items-center gap-2">
                        {{ __('cms.reports.btn_view_detail') }}
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
