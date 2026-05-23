@extends('layouts.app')

@section('header')
    <div class="text-[13px] text-gray-500 font-medium">
        <a href="{{ route('dashboard') }}"
            class="text-gray-400 hover:text-gray-600">{{ __('dashboard.header.breadcrumb_home') }}</a> /
        <span class="text-[#0ea5e9]">{{ __("dashboard.{$role}.title") }}</span>
    </div>
@endsection

@section('content')
    <div class="mb-4 sm:mb-6">
        {{-- Dynamic greeting from lang file: dashboard.welcome.greeting_{role} --}}
        <h1 class="text-lg sm:text-xl md:text-[22px] font-bold text-[#1E293B] mb-1 sm:mb-2">
            @php
                $greetingKey = "dashboard.welcome.greeting_{$role}";
                $greeting = __($greetingKey, ['name' => $user->name]);
            @endphp
            {{ $greeting }}
        </h1>
        <p class="text-gray-500 text-sm">{{ __("dashboard.welcome.subtitle_{$role}") }}</p>

        {{-- ===== STATS CARDS: All roles (admin = all visitors, non-admin = user's own visits) ===== --}}
        @if (in_array($role, ['admin', 'pegawai']))
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 mt-6">

                <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 flex flex-col justify-between">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="text-3xl font-bold text-gray-900 leading-tight">
                                {{ number_format($totalVisitors ?? 0) }}</div>
                            <div class="text-xs font-medium text-gray-600 mt-1">
                                {{ __('dashboard.admin.stats.total_visitors') }}</div>
                        </div>
                        <div class="p-2 bg-gray-50 rounded-lg text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 text-right">
                        <a href="{{ route('cms.reports.pengunjung') }}"
                            class="text-[11px] font-medium text-gray-400 hover:text-blue-500">{{ __('dashboard.admin.stats.view_details') }}</a>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 flex flex-col justify-between">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="text-3xl font-bold text-gray-900 leading-tight"
                                id="avgDailyStat">{{ number_format($totalToday ?? 0) }}</div>
                            <div class="text-xs font-medium text-gray-600 mt-1" id="visitorStatTitle">
                                {{ __('dashboard.admin.stats.total_today') }}</div>
                        </div>
                        <div class="p-2 bg-gray-50 rounded-lg text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 text-right">
                        <a href="{{ route('cms.reports.pengunjung') }}"
                            class="text-[11px] font-medium text-gray-400 hover:text-blue-500">{{ __('dashboard.admin.stats.view_details') }}</a>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 flex flex-col justify-between">
                    <div class="flex justify-between items-start relative">
                        <div>
                            <div class="text-3xl font-bold text-gray-900 leading-tight">
                                {{ number_format($onlineUsers ?? 0) }}</div>
                            <div class="text-xs font-medium text-gray-600 mt-1">
                                {{ __('dashboard.admin.stats.online_users') }}</div>
                        </div>
                        <div class="p-2 bg-gray-50 rounded-lg text-gray-600 relative">
                            <div class="absolute top-1.5 right-1.5 w-2 h-2 bg-green-500 rounded-full border border-white">
                            </div>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 text-right">
                        <a href="{{ route('cms.reports.online') }}"
                            class="text-[11px] font-medium text-gray-400 hover:text-blue-500">{{ __('dashboard.admin.stats.view_details') }}</a>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 flex flex-col justify-between">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="text-3xl font-bold text-gray-900 leading-tight">
                                {{ number_format($totalUsers ?? 0) }}</div>
                            <div class="text-xs font-medium text-gray-600 mt-1">
                                {{ __('dashboard.admin.stats.total_users') }}</div>
                        </div>
                        <div class="p-2 bg-gray-50 rounded-lg text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 text-right">
                        <a href="{{ route('cms.pengguna.index') }}"
                            class="text-[11px] font-medium text-gray-400 hover:text-blue-500">{{ __('dashboard.admin.stats.view_details') }}</a>
                    </div>
                </div>

            </div>
        @else
            {{-- Non-admin: User's own visit stats --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 mt-6">

                <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 flex flex-col justify-between">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="text-3xl font-bold text-gray-900 leading-tight">
                                {{ number_format($totalVisits ?? 0) }}</div>
                            <div class="text-xs font-medium text-gray-600 mt-1">
                                {{ __('dashboard.user_stats.total_visits') }}</div>
                        </div>
                        <div class="p-2 bg-blue-50 rounded-lg text-blue-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 text-right">
                        <a href="#chartTitle"
                            class="text-[11px] font-medium text-gray-400 hover:text-blue-500">{{ __('dashboard.user_stats.view_details') }}</a>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 flex flex-col justify-between">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="text-3xl font-bold text-gray-900 leading-tight"
                                id="avgDailyStat">{{ number_format($totalToday ?? 0) }}</div>
                            <div class="text-xs font-medium text-gray-600 mt-1" id="visitorStatTitle">
                                {{ __('dashboard.user_stats.total_today') }}</div>
                        </div>
                        <div class="p-2 bg-green-50 rounded-lg text-green-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 text-right">
                        <a href="#chartTitle"
                            class="text-[11px] font-medium text-gray-400 hover:text-blue-500">{{ __('dashboard.user_stats.view_details') }}</a>
                    </div>
                </div>

            </div>
        @endif

        {{-- ===== ADMIN: Chart with dynamic roles ===== --}}
        @if (in_array($role, ['admin', 'pegawai']))
            <div class="bg-white rounded-xl p-4 sm:p-6 shadow-sm border border-gray-100">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-4 sm:mb-6">
                    <h2 class="text-base sm:text-lg font-bold text-gray-800" id="chartTitle">{{ __('dashboard.admin.chart.title') }}</h2>
                    {{-- Timeframe selector for ApexCharts --}}
                    <div class="flex flex-col gap-2 sm:gap-3">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3">
                            <div class="text-[11px] font-semibold text-gray-800">
                                {{ __('dashboard.admin.chart.user_type') }}</div>
                             <select id="chartTimeframe"
                                class="text-xs border border-gray-200 rounded-lg pl-3 pr-10 py-1.5 text-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-100 cursor-pointer bg-white">
                                <option value="day" selected>{{ __('dashboard.admin.chart.filter_day') }}</option>
                                <option value="week">{{ __('dashboard.admin.chart.filter_week') }}</option>
                                <option value="month">{{ __('dashboard.admin.chart.filter_month') }}</option>
                                <option value="year">{{ __('dashboard.admin.chart.filter_year') }}</option>
                            </select>
                        </div>
                        <div class="flex flex-wrap items-center gap-3 sm:gap-5 text-[11px] font-medium text-gray-600">
                            {{-- Guest (always first) --}}
                            <div class="flex items-center gap-1 sm:gap-2">
                                <span
                                    style="width:12px;height:12px;min-width:12px;border-radius:999px;background-color:{{ $guestColor ?? '#cececeff' }};display:inline-block;"></span>
                                <span>{{ __('dashboard.admin.chart.unregistered_user') }}</span>
                            </div>
                            {{-- Registered roles — dynamic from DB --}}
                            @foreach($chartRoles as $roleItem)
                            @php $roleColor = $chartColors[$roleItem->name] ?? '#6B7280'; @endphp
                            <div class="flex items-center gap-1 sm:gap-2">
                                <span
                                    style="width:12px;height:12px;min-width:12px;border-radius:999px;background-color:{{ $roleColor }};display:inline-block;"></span>
                                <span>{{ $roleItem->i18nLabel() }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="w-full relative" style="height: 300px; min-height: 250px;">
                    <div id="visitorChart"></div>
                </div>
            </div>
        @endif

        {{-- ===== NON-ADMIN: User's own visit chart ===== --}}
        @if (!in_array($role, ['admin', 'pegawai']))
            <div class="bg-white rounded-xl p-4 sm:p-6 shadow-sm border border-gray-100">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-4 sm:mb-6">
                    <h2 class="text-base sm:text-lg font-bold text-gray-800" id="chartTitle">{{ __('dashboard.user_chart.title') }}</h2>
                    <select id="chartTimeframe"
                        class="text-xs border border-gray-200 rounded-lg pl-3 pr-10 py-1.5 text-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-100 cursor-pointer bg-white">
                        <option value="day" selected>{{ __('dashboard.user_chart.filter_day') }}</option>
                        <option value="week">{{ __('dashboard.user_chart.filter_week') }}</option>
                        <option value="month">{{ __('dashboard.user_chart.filter_month') }}</option>
                        <option value="year">{{ __('dashboard.user_chart.filter_year') }}</option>
                    </select>
                </div>
                <div class="w-full relative" style="height: 300px; min-height: 250px;">
                    <div id="visitorChart"></div>
                </div>
            </div>
        @endif
    </div>
@endsection

{{-- Chart scripts --}}
@if (in_array($role, ['admin', 'pegawai']))
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var roleChartData = @json($roleJsData ?? []);
    var chartLabels7 = @json($chartLabels7 ?? []);
    var guestData7 = @json($guestData7 ?? []);
    var chartLabels30 = @json($chartLabels30 ?? []);
    var guestData30 = @json($guestData30 ?? []);
    var chartLabelsYear = @json($chartLabelsYear ?? []);
    var guestDataYear = @json($guestDataYear ?? []);
    var guestTodayHourly = @json($guestTodayHourly ?? []);
    var guestColor = '{{ $guestColor ?? '#6366F1' }}';

    var chartEl = document.getElementById('visitorChart');
    if (!chartEl) return;

    function getGuestData(tf) {
        if (tf === 'day')   return guestTodayHourly;
        if (tf === 'week')  return guestData7;
        if (tf === 'month') return guestData30;
        if (tf === 'year')  return guestDataYear;
        return guestData7;
    }

    function getTimeframe() {
        var sel = document.getElementById('chartTimeframe');
        return sel ? sel.value : 'day';
    }

    function buildSeries(tf) {
        var dataKey = tf === 'day' ? 'todayHourly' : tf === 'week' ? 'data7' : tf === 'month' ? 'data30' : 'dataYear';
        var series = [{
            name: "{{ __('dashboard.admin.chart.unregistered_user') }}",
            data: getGuestData(tf) || []
        }];
        roleChartData.forEach(function(role) {
            series.push({
                name: role.label,
                data: (role[dataKey] || [])
            });
        });
        return series;
    }

    function getCategories(tf) {
        if (tf === 'day') {
            return Array.from({length: 24}, function(_, i) { return i.toString().padStart(2,'0')+':00'; });
        }
        if (tf === 'month') return chartLabels30;
        if (tf === 'year')  return chartLabelsYear;
        return chartLabels7;
    }

    var chartInstance = null;

    function renderChart(tf) {
        var avgDailyStat = document.getElementById('avgDailyStat');
        var avgDailyLabel = document.getElementById('avgDailyLabel');
        var nf = function(n) { return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g,'.'); };
        var getTotal = function(t) {
            if (t === 'day')   return {{ $totalToday ?? 0 }};
            if (t === 'week')  return {{ $total7 ?? 0 }};
            if (t === 'month') return {{ $total30 ?? 0 }};
            if (t === 'year')  return {{ $total365 ?? 0 }};
            return 0;
        };
        var getTitle = function(t) {
            return {
                'day':   @json(__('dashboard.admin.chart.filter_day')),
                'week':  @json(__('dashboard.admin.chart.filter_week')),
                'month': @json(__('dashboard.admin.chart.filter_month')),
                'year':  @json(__('dashboard.admin.chart.filter_year'))
            }[t] || '';
        };
        var getStatTitle = function(t) {
            return {
                'day':   @json(__('dashboard.admin.stats.total_today')),
                'week':  @json(__('dashboard.admin.stats.total_7_days')),
                'month': @json(__('dashboard.admin.stats.total_30_days')),
                'year':  @json(__('dashboard.admin.stats.total_1_year'))
            }[t] || '';
        };

        if (avgDailyStat) avgDailyStat.textContent = nf(Math.round(getTotal(tf)));
        if (visitorStatTitle) visitorStatTitle.textContent = getStatTitle(tf);

        var chartTitleEl = document.getElementById('chartTitle');
        if (chartTitleEl) {
            chartTitleEl.textContent = @json(__('dashboard.admin.chart.title')) + ' - ' + getTitle(tf);
        }

        if (chartInstance) { chartInstance.destroy(); chartInstance = null; }

        var options = {
            series: buildSeries(tf),
            chart: {
                type: 'area',
                height: 350,
                toolbar: {
                    show: true,
                    tools: { download: true, selection: true, zoom: true, zoomin: true, zoomout: true, pan: true, reset: true },
                    export: { csv: { filename: 'visitor-report', columnDelimiter: ',', headerCategory: 'Tanggal', headerValue: 'Pengunjung' }, svg: { filename: 'visitor-report' }, png: { filename: 'visitor-report' } }
                },
                animations: { enabled: true, easing: 'easeinout', speed: 800 },
                zoom: { enabled: true },
                redrawOnParentResize: true,
            },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 2.5 },
            colors: [guestColor].concat(roleChartData.map(function(r) { return r.color; })),
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.3, opacityTo: 0.05, stops: [0, 90, 100] } },
            xaxis: {
                categories: getCategories(tf),
                tickAmount: window.innerWidth < 768 ? 6 : undefined,
                labels: { 
                    hideOverlappingLabels: true,
                    style: { colors: '#94a3b8', fontSize: '11px', fontFamily: 'Inter, sans-serif' } 
                },
                axisBorder: { show: false },
                axisTicks: { show: false },
            },
            yaxis: {
                min: 0,
                labels: { style: { colors: '#94a3b8', fontSize: '11px', fontFamily: 'Inter, sans-serif' }, formatter: function(v) { return Math.round(v).toString(); } },
                title: { text: "{{ __('dashboard.admin.chart.y_axis') }}", style: { color: '#475569', fontSize: '10px', fontWeight: 'bold', fontFamily: 'Inter, sans-serif' } },
            },
            grid: { borderColor: '#f1f5f9', strokeDashArray: 3, xaxis: { lines: { show: false } } },
            legend: { show: false },
            tooltip: { theme: 'light', x: { show: true }, y: { formatter: function(v) { return Math.round(v) + ' {{ __('dashboard.admin.chart.y_axis') }}'; } } },
        };

        chartInstance = new ApexCharts(chartEl, options);
        chartInstance.render();
        setTimeout(function() {
            window.dispatchEvent(new Event('resize'));
        }, 150);
    }

    renderChart(getTimeframe());

    var tfSel = document.getElementById('chartTimeframe');
    if (tfSel) tfSel.addEventListener('change', function() { renderChart(this.value); });
});
</script>
@endpush
@endif

{{-- Non-admin user chart script --}}
@if (!in_array($role, ['admin', 'pegawai']))
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var chartLabels7 = @json($chartLabels7 ?? []);
    var userData7 = @json($userData7 ?? []);
    var chartLabels30 = @json($chartLabels30 ?? []);
    var userData30 = @json($userData30 ?? []);
    var chartLabelsYear = @json($chartLabelsYear ?? []);
    var userDataYear = @json($userDataYear ?? []);
    var userTodayHourly = @json($userTodayHourly ?? []);

    var chartEl = document.getElementById('visitorChart');
    if (!chartEl) return;

    function getTimeframe() {
        var sel = document.getElementById('chartTimeframe');
        return sel ? sel.value : 'day';
    }

    function getCategories(tf) {
        if (tf === 'day') {
            return Array.from({length: 24}, function(_, i) { return i.toString().padStart(2,'0')+':00'; });
        }
        if (tf === 'month') return chartLabels30;
        if (tf === 'year')  return chartLabelsYear;
        return chartLabels7;
    }

    function getUserData(tf) {
        if (tf === 'day')   return userTodayHourly;
        if (tf === 'week')  return userData7;
        if (tf === 'month') return userData30;
        if (tf === 'year')  return userDataYear;
        return userData7;
    }

    var chartInstance = null;

    function renderChart(tf) {
        var avgDailyStat = document.getElementById('avgDailyStat');
        var visitorStatTitle = document.getElementById('visitorStatTitle');
        var nf = function(n) { return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g,'.'); };

        var getTotal = function(t) {
            if (t === 'day')   return {{ $totalToday ?? 0 }};
            if (t === 'week')  return {{ $total7 ?? 0 }};
            if (t === 'month') return {{ $total30 ?? 0 }};
            if (t === 'year')  return {{ $total365 ?? 0 }};
            return 0;
        };

        var getStatTitle = function(t) {
            return {
                'day':   @json(__('dashboard.user_stats.total_today')),
                'week':  @json(__('dashboard.user_stats.total_7_days')),
                'month': @json(__('dashboard.user_stats.total_30_days')),
                'year':  @json(__('dashboard.user_stats.total_1_year'))
            }[t] || '';
        };

        if (avgDailyStat) avgDailyStat.textContent = nf(Math.round(getTotal(tf)));
        if (visitorStatTitle) visitorStatTitle.textContent = getStatTitle(tf);

        if (chartInstance) { chartInstance.destroy(); chartInstance = null; }

        var options = {
            series: [{
                name: "{{ __('dashboard.user_stats.total_visits') }}",
                data: getUserData(tf) || []
            }],
            chart: {
                type: 'area',
                height: 350,
                toolbar: {
                    show: true,
                    tools: { download: true, selection: true, zoom: true, zoomin: true, zoomout: true, pan: true, reset: true },
                    export: { csv: { filename: 'my-visit-report', columnDelimiter: ',', headerCategory: 'Tanggal', headerValue: 'Kunjungan' }, svg: { filename: 'my-visit-report' }, png: { filename: 'my-visit-report' } }
                },
                animations: { enabled: true, easing: 'easeinout', speed: 800 },
                zoom: { enabled: true },
                redrawOnParentResize: true,
            },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 2.5 },
            colors: ['#0ea5e9'],
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.3, opacityTo: 0.05, stops: [0, 90, 100] } },
            xaxis: {
                categories: getCategories(tf),
                tickAmount: window.innerWidth < 768 ? 6 : undefined,
                labels: { 
                    hideOverlappingLabels: true,
                    style: { colors: '#94a3b8', fontSize: '11px', fontFamily: 'Inter, sans-serif' } 
                },
                axisBorder: { show: false },
                axisTicks: { show: false },
            },
            yaxis: {
                min: 0,
                labels: { style: { colors: '#94a3b8', fontSize: '11px', fontFamily: 'Inter, sans-serif' }, formatter: function(v) { return Math.round(v).toString(); } },
                title: { text: "{{ __('dashboard.user_chart.y_axis') }}", style: { color: '#475569', fontSize: '10px', fontWeight: 'bold', fontFamily: 'Inter, sans-serif' } },
            },
            grid: { borderColor: '#f1f5f9', strokeDashArray: 3, xaxis: { lines: { show: false } } },
            legend: { show: false },
            tooltip: { theme: 'light', x: { show: true }, y: { formatter: function(v) { return Math.round(v) + ' {{ __('dashboard.user_chart.y_axis') }}'; } } },
        };

        chartInstance = new ApexCharts(chartEl, options);
        chartInstance.render();
        setTimeout(function() {
            window.dispatchEvent(new Event('resize'));
        }, 150);
    }

    renderChart(getTimeframe());

    var tfSel = document.getElementById('chartTimeframe');
    if (tfSel) tfSel.addEventListener('change', function() { renderChart(this.value); });
});
</script>
@endpush
@endif
