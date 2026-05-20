<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VisitRegistration;
use App\Models\ArchivalConsultation;
use App\Models\PageView;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * 1. Laporan Kunjungan (Pendaftaran Kunjungan)
     * Pie chart (tujuan kunjungan) & Line chart (tren kunjungan) dgn filter tanggal
     */
    public function kunjungan(Request $request)
    {
        $user = $request->user();
        $role = $user?->role ?? 'admin';
        $userRoleObj = $user ? \App\Models\Role::where('name', $role)->first() : null;
        $isAdminOrPegawai = in_array($role, ['admin', 'pegawai'], true) || ($userRoleObj && $userRoleObj->hasPermission('cms.reports'));

        $tf = $request->input('tf', 'day'); // day, week, month, year, custom
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = VisitRegistration::query();
        if (!$isAdminOrPegawai && $user) {
            $query->where('email', $user->email);
        }

        $now = Carbon::now();
        $subtitle = __('dashboard.admin.chart.filter_day', ['default' => 'Hari Ini']);
        if ($tf === 'custom' && $startDate && $endDate) {
            $query->whereBetween('created_at', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()]);
            $subtitle = 'Periode: ' . Carbon::parse($startDate)->format('d M Y') . ' - ' . Carbon::parse($endDate)->format('d M Y');
        } elseif ($tf === 'day') {
            $query->whereDate('created_at', $now->toDateString());
        } elseif ($tf === 'week') {
            $query->where('created_at', '>=', (clone $now)->subDays(7));
            $subtitle = __('dashboard.admin.chart.filter_week', ['default' => '7 Hari Terakhir']);
        } elseif ($tf === 'month') {
            $query->where('created_at', '>=', (clone $now)->subDays(30));
            $subtitle = __('dashboard.admin.chart.filter_month', ['default' => '30 Hari Terakhir']);
        } elseif ($tf === 'year') {
            $query->where('created_at', '>=', (clone $now)->subDays(365));
            $subtitle = __('dashboard.admin.chart.filter_year', ['default' => '1 Tahun Terakhir']);
        }

        // Summary stats
        $totalKunjungan = (clone $query)->sum('visitor_count');
        $totalEdukasi = (clone $query)->where('visit_purpose', 'edukasi')->sum('visitor_count');
        $totalPenelitian = (clone $query)->where('visit_purpose', 'penelitian')->sum('visitor_count');
        $totalKunker = (clone $query)->where('visit_purpose', 'kunker')->sum('visitor_count');

        // Pie chart data
        $pieData = [
            $totalEdukasi,
            $totalPenelitian,
            $totalKunker,
        ];
        $pieLabels = [
            __('home.layanan_publik.form_purpose_edukasi', ['default' => 'Edukasi']),
            __('home.layanan_publik.form_purpose_penelitian', ['default' => 'Penelitian']),
            __('home.layanan_publik.form_purpose_kunker', ['default' => 'Kunjungan Kerja']),
        ];

        // Line chart data (Dynamic date range based on tf)
        $lineLabels = [];
        $lineSeries = [];

        if ($tf === 'custom' && $startDate && $endDate) {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();
        } elseif ($tf === 'day') {
            $start = Carbon::now()->startOfDay();
            $end = Carbon::now()->endOfDay();
        } elseif ($tf === 'week') {
            $start = Carbon::now()->subDays(7)->startOfDay();
            $end = Carbon::now()->endOfDay();
        } elseif ($tf === 'month') {
            $start = Carbon::now()->subDays(30)->startOfDay();
            $end = Carbon::now()->endOfDay();
        } elseif ($tf === 'year') {
            $start = Carbon::now()->subDays(365)->startOfDay();
            $end = Carbon::now()->endOfDay();
        } else {
            $start = Carbon::now()->startOfDay();
            $end = Carbon::now()->endOfDay();
        }

        // Cap at 365 days to prevent performance issues on massive ranges
        $diffDays = $start->diffInDays($end);
        if ($diffDays > 365) {
            $start = (clone $end)->subDays(365)->startOfDay();
            $diffDays = 365;
        }

        for ($i = 0; $i <= $diffDays; $i++) {
            $current = (clone $start)->addDays($i)->toDateString();
            $lineLabels[] = Carbon::parse($current)->format('d M');
            $subQ = VisitRegistration::whereDate('created_at', $current);
            if (!$isAdminOrPegawai && $user) {
                $subQ->where('email', $user->email);
            }
            $lineSeries[] = (int) $subQ->sum('visitor_count');
        }

        // All data for DataTables client-side handling
        $registrations = (clone $query)->latest()->get();

        return view('cms.reports.kunjungan', compact(
            'role',
            'tf',
            'startDate',
            'endDate',
            'subtitle',
            'totalKunjungan',
            'totalEdukasi',
            'totalPenelitian',
            'totalKunker',
            'pieData',
            'pieLabels',
            'lineLabels',
            'lineSeries',
            'registrations'
        ));
    }

    /**
     * 2. Monitoring Pengunjung Website - Index Page
     * Menampilkan ringkasan total page views dan total unique visitors.
     */
    public function pengunjungIndex(Request $request)
    {
        $user = $request->user();
        $role = $user?->role ?? 'admin';
        $userRoleObj = $user ? \App\Models\Role::where('name', $role)->first() : null;
        $isAdminOrPegawai = in_array($role, ['admin', 'pegawai'], true) || ($userRoleObj && $userRoleObj->hasPermission('cms.reports'));

        $tf = $request->input('tf', 'day'); // day, week, month, year, custom
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = PageView::query();
        if (!$isAdminOrPegawai && $user) {
            $query->where('user_id', $user->id);
        }

        $now = Carbon::now();

        if ($tf === 'custom' && $startDate && $endDate) {
            $query->whereBetween('created_at', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()]);
        } elseif ($tf === 'day') {
            $query->whereDate('created_at', $now->toDateString());
        } elseif ($tf === 'week') {
            $query->where('created_at', '>=', (clone $now)->subDays(7));
        } elseif ($tf === 'month') {
            $query->where('created_at', '>=', (clone $now)->subDays(30));
        } elseif ($tf === 'year') {
            $query->where('created_at', '>=', (clone $now)->subDays(365));
        }

        $totalPageViews = (clone $query)->count();
        $totalUniqueVisitors = (clone $query)->distinct('ip', DB::raw('DATE(created_at)'))->count('ip');

        return view('cms.reports.pengunjung_index', compact(
            'role', 'tf', 'startDate', 'endDate', 'totalPageViews', 'totalUniqueVisitors'
        ));
    }

    /**
     * 2a. Monitoring Pengunjung Website - Page Views
     * Bar chart (kunjungan per halaman) dgn filter hari, minggu, bulan, tahun, custom date range
     */
    public function pengunjungPageViews(Request $request)
    {
        $user = $request->user();
        $role = $user?->role ?? 'admin';
        $userRoleObj = $user ? \App\Models\Role::where('name', $role)->first() : null;
        $isAdminOrPegawai = in_array($role, ['admin', 'pegawai'], true) || ($userRoleObj && $userRoleObj->hasPermission('cms.reports'));

        $tf = $request->input('tf', 'day'); // day, week, month, year, custom
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = PageView::query();
        if (!$isAdminOrPegawai && $user) {
            $query->where('user_id', $user->id);
        }

        $now = Carbon::now();

        $subtitle = __('dashboard.admin.chart.filter_day', ['default' => 'Hari Ini']);
        if ($tf === 'custom' && $startDate && $endDate) {
            $query->whereBetween('created_at', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()]);
            $subtitle = 'Periode: ' . Carbon::parse($startDate)->format('d M Y') . ' - ' . Carbon::parse($endDate)->format('d M Y');
        } elseif ($tf === 'day') {
            $query->whereDate('created_at', $now->toDateString());
        } elseif ($tf === 'week') {
            $query->where('created_at', '>=', (clone $now)->subDays(7));
            $subtitle = __('dashboard.admin.chart.filter_week', ['default' => '7 Hari Terakhir']);
        } elseif ($tf === 'month') {
            $query->where('created_at', '>=', (clone $now)->subDays(30));
            $subtitle = __('dashboard.admin.chart.filter_month', ['default' => '30 Hari Terakhir']);
        } elseif ($tf === 'year') {
            $query->where('created_at', '>=', (clone $now)->subDays(365));
            $subtitle = __('dashboard.admin.chart.filter_year', ['default' => '1 Tahun Terakhir']);
        }

        $views = $query->get();
        $totalViews = $views->count();

        // Define page mapping
        $pages = [
            'beranda' => ['label' => 'Beranda', 'match' => '/', 'count' => 0],
            'pameran' => ['label' => 'Pameran Arsip', 'match' => 'pameran-arsip', 'count' => 0],
            'pengumuman' => ['label' => 'Pengumuman', 'match' => 'pengumuman', 'count' => 0],
            'berita' => ['label' => 'Berita', 'match' => 'berita', 'count' => 0],
            'galeri' => ['label' => 'Galeri', 'match' => 'galeri', 'count' => 0],
            'layanan' => ['label' => 'Layanan Publik', 'match' => 'layanan-publik', 'count' => 0],
            'pengelolaan' => ['label' => 'Pengelolaan', 'match' => 'pengelolaan', 'count' => 0],
            'kontak' => ['label' => 'Kontak Kami', 'match' => 'kontak-kami', 'count' => 0],
        ];

        foreach ($views as $v) {
            $path = $v->path;
            $normalizedPath = trim($path, '/');
            if ($normalizedPath === '') {
                $normalizedPath = '/';
            }

            // Skip main menus that have no pages of their own
            if ($normalizedPath === 'pameran-arsip' || $normalizedPath === 'layanan-publik' || $normalizedPath === 'pengelolaan') {
                continue;
            }

            if ($normalizedPath === '/') {
                $pages['beranda']['count']++;
            } elseif (str_contains($normalizedPath, 'pameran-arsip')) {
                $pages['pameran']['count']++;
            } elseif (str_contains($normalizedPath, 'pengumuman')) {
                $pages['pengumuman']['count']++;
            } elseif (str_contains($normalizedPath, 'berita')) {
                $pages['berita']['count']++;
            } elseif (str_contains($normalizedPath, 'galeri')) {
                $pages['galeri']['count']++;
            } elseif (str_contains($normalizedPath, 'layanan-publik')) {
                $pages['layanan']['count']++;
            } elseif (str_contains($normalizedPath, 'pengelolaan')) {
                $pages['pengelolaan']['count']++;
            } elseif (str_contains($normalizedPath, 'kontak-kami')) {
                $pages['kontak']['count']++;
            }
        }

        $barLabels = array_column($pages, 'label');
        $barSeries = array_column($pages, 'count');

        // All recent logs for DataTables client-side handling
        $recentLogs = (clone $query)->latest()->get();

        return view('cms.reports.pengunjung_page_views', compact(
            'role',
            'tf',
            'startDate',
            'endDate',
            'subtitle',
            'totalViews',
            'pages',
            'barLabels',
            'barSeries',
            'recentLogs'
        ));
    }

    /**
     * 2b. Monitoring Pengunjung Website - Unique Visitors
     * Bar chart (pengunjung unik per halaman) dgn filter hari, minggu, bulan, tahun, custom date range
     */
    public function pengunjungUnique(Request $request)
    {
        $user = $request->user();
        $role = $user?->role ?? 'admin';
        $userRoleObj = $user ? \App\Models\Role::where('name', $role)->first() : null;
        $isAdminOrPegawai = in_array($role, ['admin', 'pegawai'], true) || ($userRoleObj && $userRoleObj->hasPermission('cms.reports'));

        $tf = $request->input('tf', 'day'); // day, week, month, year, custom
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = PageView::query();
        if (!$isAdminOrPegawai && $user) {
            $query->where('user_id', $user->id);
        }

        $now = Carbon::now();

        $subtitle = __('dashboard.admin.chart.filter_day', ['default' => 'Hari Ini']);
        if ($tf === 'custom' && $startDate && $endDate) {
            $query->whereBetween('created_at', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()]);
            $subtitle = 'Periode: ' . Carbon::parse($startDate)->format('d M Y') . ' - ' . Carbon::parse($endDate)->format('d M Y');
        } elseif ($tf === 'day') {
            $query->whereDate('created_at', $now->toDateString());
        } elseif ($tf === 'week') {
            $query->where('created_at', '>=', (clone $now)->subDays(7));
            $subtitle = __('dashboard.admin.chart.filter_week', ['default' => '7 Hari Terakhir']);
        } elseif ($tf === 'month') {
            $query->where('created_at', '>=', (clone $now)->subDays(30));
            $subtitle = __('dashboard.admin.chart.filter_month', ['default' => '30 Hari Terakhir']);
        } elseif ($tf === 'year') {
            $query->where('created_at', '>=', (clone $now)->subDays(365));
            $subtitle = __('dashboard.admin.chart.filter_year', ['default' => '1 Tahun Terakhir']);
        }

        // Get raw views to group by category later
        $views = (clone $query)->select('path', 'ip', DB::raw('DATE(created_at) as date'))->get();
        // Total unique visitors is count of unique IP + Date combinations overall
        $totalViews = (clone $query)->select('ip', DB::raw('DATE(created_at) as date'))->distinct()->get()->count();

        // Define page mapping
        $pages = [
            'beranda' => ['label' => 'Beranda', 'match' => '/', 'count' => 0],
            'pameran' => ['label' => 'Pameran Arsip', 'match' => 'pameran-arsip', 'count' => 0],
            'pengumuman' => ['label' => 'Pengumuman', 'match' => 'pengumuman', 'count' => 0],
            'berita' => ['label' => 'Berita', 'match' => 'berita', 'count' => 0],
            'galeri' => ['label' => 'Galeri', 'match' => 'galeri', 'count' => 0],
            'layanan' => ['label' => 'Layanan Publik', 'match' => 'layanan-publik', 'count' => 0],
            'pengelolaan' => ['label' => 'Pengelolaan', 'match' => 'pengelolaan', 'count' => 0],
            'kontak' => ['label' => 'Kontak Kami', 'match' => 'kontak-kami', 'count' => 0],
        ];

        // Track unique page visits per IP+Date
        $uniquePageVisits = [];

        foreach ($views as $v) {
            $path = $v->path;
            $normalizedPath = trim($path, '/');
            if ($normalizedPath === '') {
                $normalizedPath = '/';
            }

            // Skip main menus that have no pages of their own
            if ($normalizedPath === 'pameran-arsip' || $normalizedPath === 'layanan-publik' || $normalizedPath === 'pengelolaan') {
                continue;
            }

            // Determine category
            if ($normalizedPath === '/') {
                $category = 'beranda';
            } elseif (str_contains($normalizedPath, 'pameran-arsip')) {
                $category = 'pameran';
            } elseif (str_contains($normalizedPath, 'pengumuman')) {
                $category = 'pengumuman';
            } elseif (str_contains($normalizedPath, 'berita')) {
                $category = 'berita';
            } elseif (str_contains($normalizedPath, 'galeri')) {
                $category = 'galeri';
            } elseif (str_contains($normalizedPath, 'layanan-publik')) {
                $category = 'layanan';
            } elseif (str_contains($normalizedPath, 'pengelolaan')) {
                $category = 'pengelolaan';
            } elseif (str_contains($normalizedPath, 'kontak-kami')) {
                $category = 'kontak';
            } else {
                continue; // Skip other unmatched pages
            }

            // Create unique key for this IP, Date, and specific Path
            $key = $normalizedPath . '_' . $v->ip . '_' . $v->date;
            $uniquePageVisits[$key] = $category;
        }

        // Increment counts based on unique page visits
        foreach ($uniquePageVisits as $category) {
            $pages[$category]['count']++;
        }

        $barLabels = array_column($pages, 'label');
        $barSeries = array_column($pages, 'count');

        // Recent logs for DataTables client-side handling
        // For unique visitors logs, we can group by IP and Date and show the latest access
        $recentLogs = (clone $query)->select('ip', 'path', DB::raw('MAX(created_at) as created_at'))
            ->groupBy('ip', 'path', DB::raw('DATE(created_at)'))
            ->orderBy('created_at', 'desc')
            ->get();

        return view('cms.reports.pengunjung_unique', compact(
            'role',
            'tf',
            'startDate',
            'endDate',
            'subtitle',
            'totalViews',
            'pages',
            'barLabels',
            'barSeries',
            'recentLogs'
        ));
    }

    /**
     * 3. Laporan Konsultasi Kearsipan
     * Data tabel monitoring form konsultasi dgn filter tanggal
     */
    public function konsultasi(Request $request)
    {
        $user = $request->user();
        $role = $user?->role ?? 'admin';
        $userRoleObj = $user ? \App\Models\Role::where('name', $role)->first() : null;
        $isAdminOrPegawai = in_array($role, ['admin', 'pegawai'], true) || ($userRoleObj && $userRoleObj->hasPermission('cms.reports'));

        $tf = $request->input('tf', 'day'); // day, week, month, year, custom
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = ArchivalConsultation::query();
        if (!$isAdminOrPegawai && $user) {
            $query->where('email', $user->email);
        }

        $now = Carbon::now();
        $subtitle = __('dashboard.admin.chart.filter_day', ['default' => 'Hari Ini']);
        if ($tf === 'custom' && $startDate && $endDate) {
            $query->whereBetween('created_at', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()]);
            $subtitle = 'Periode: ' . Carbon::parse($startDate)->format('d M Y') . ' - ' . Carbon::parse($endDate)->format('d M Y');
        } elseif ($tf === 'day') {
            $query->whereDate('created_at', $now->toDateString());
        } elseif ($tf === 'week') {
            $query->where('created_at', '>=', (clone $now)->subDays(7));
            $subtitle = __('dashboard.admin.chart.filter_week', ['default' => '7 Hari Terakhir']);
        } elseif ($tf === 'month') {
            $query->where('created_at', '>=', (clone $now)->subDays(30));
            $subtitle = __('dashboard.admin.chart.filter_month', ['default' => '30 Hari Terakhir']);
        } elseif ($tf === 'year') {
            $query->where('created_at', '>=', (clone $now)->subDays(365));
            $subtitle = __('dashboard.admin.chart.filter_year', ['default' => '1 Tahun Terakhir']);
        }

        $totalKonsultasi = (clone $query)->count();
        $consultations = (clone $query)->latest()->get();

        return view('cms.reports.konsultasi', compact(
            'role',
            'tf',
            'startDate',
            'endDate',
            'subtitle',
            'totalKonsultasi',
            'consultations'
        ));
    }

    /**
     * 4. Laporan Pengguna Online
     * Line chart tracking riwayat pengguna online per jam, status online realtime,
     * serta metrik dan data riwayat aktivitas pengguna.
     */
    public function online(Request $request)
    {
        $user = $request->user();
        $role = $user?->role ?? 'admin';
        $userRoleObj = $user ? \App\Models\Role::where('name', $role)->first() : null;
        $isAdminOrPegawai = in_array($role, ['admin', 'pegawai'], true) || ($userRoleObj && $userRoleObj->hasPermission('cms.reports'));

        $tf = $request->input('tf', 'day'); // day, week, month, year, custom
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // 1. Realtime Online Users (Active in last 5 mins via Cache)
        $now = Carbon::now('Asia/Jakarta');
        $fiveMinAgo = $now->copy()->subMinutes(5)->timestamp;
        $onlineUserIds = [];

        if ($isAdminOrPegawai) {
            $allUsers = \App\Models\User::all();
        } else {
            $allUsers = $user ? \App\Models\User::where('id', $user->id)->get() : collect([]);
        }
        
        foreach ($allUsers as $u) {
            $cached = \Illuminate\Support\Facades\Cache::get("online_user_{$u->id}");
            if ($cached && $cached >= $fiveMinAgo) {
                $onlineUserIds[] = $u->id;
                $u->last_activity = Carbon::createFromTimestamp($cached)->format('H:i:s');
            } else {
                $u->last_activity = $cached ? Carbon::createFromTimestamp($cached)->format('d M Y H:i:s') : '-';
            }
        }
        
        $onlineUsersList = $allUsers->whereIn('id', $onlineUserIds)->values();
        $onlineCount = $onlineUsersList->count();

        // Base Query untuk filter TF
        $baseQuery = PageView::whereNotNull('user_id');
        if (!$isAdminOrPegawai && $user) {
            $baseQuery->where('user_id', $user->id);
        }

        $subtitle = __('dashboard.admin.chart.filter_day', ['default' => 'Hari Ini']);
        if ($tf === 'custom' && $startDate && $endDate) {
            $baseQuery->whereBetween('created_at', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()]);
            $subtitle = 'Periode: ' . Carbon::parse($startDate)->format('d M Y') . ' - ' . Carbon::parse($endDate)->format('d M Y');
        } elseif ($tf === 'day') {
            $baseQuery->whereDate('created_at', $now->toDateString());
        } elseif ($tf === 'week') {
            $baseQuery->where('created_at', '>=', (clone $now)->subDays(7));
            $subtitle = __('dashboard.admin.chart.filter_week', ['default' => '7 Hari Terakhir']);
        } elseif ($tf === 'month') {
            $baseQuery->where('created_at', '>=', (clone $now)->subDays(30));
            $subtitle = __('dashboard.admin.chart.filter_month', ['default' => '30 Hari Terakhir']);
        } elseif ($tf === 'year') {
            $baseQuery->where('created_at', '>=', (clone $now)->subDays(365));
            $subtitle = __('dashboard.admin.chart.filter_year', ['default' => '1 Tahun Terakhir']);
        }

        // 2. Historical Online Users (Distinct users per hour in selected timeframe)
        $hourlyOnlineCounts = (clone $baseQuery)
            ->selectRaw('HOUR(created_at) as hr, COUNT(DISTINCT user_id) as total')
            ->groupBy('hr')
            ->pluck('total', 'hr')
            ->toArray();

        $lineLabels = array_map(fn($h) => str_pad($h, 2, '0', STR_PAD_LEFT) . ':00', range(0, 23));
        $lineSeries = array_map(fn($h) => (int) ($hourlyOnlineCounts[$h] ?? 0), range(0, 23));

        // 3. Additional Relevant Metrics
        $totalActiveToday = (clone $baseQuery)->distinct('user_id')->count('user_id');

        $maxOnline = max($lineSeries);
        $peakHours = [];
        foreach ($lineSeries as $h => $val) {
            if ($val === $maxOnline && $maxOnline > 0) {
                $peakHours[] = str_pad($h, 2, '0', STR_PAD_LEFT) . ':00';
            }
        }
        
        $peakHourTooltip = !empty($peakHours) ? implode(', ', $peakHours) : '-';
        
        if (empty($peakHours)) {
            $peakHourLabel = '-';
        } elseif (count($peakHours) === 24) {
            $peakHourLabel = 'Sepanjang Hari';
        } elseif (count($peakHours) > 2) {
            $peakHourLabel = count($peakHours) . ' Jam Berbeda';
        } elseif (count($peakHours) === 2) {
            $peakHourLabel = implode(' & ', $peakHours);
        } else {
            $peakHourLabel = $peakHours[0];
        }

        $activeHoursCount = count(array_filter($lineSeries, fn($v) => $v > 0));
        $avgOnlinePerHour = $activeHoursCount > 0 ? round(array_sum($lineSeries) / $activeHoursCount, 1) : 0;

        // 4. User Activity Logs for DataTable
        $activitySummary = (clone $baseQuery)
            ->with('user')
            ->selectRaw('user_id, COUNT(*) as page_views, MAX(created_at) as last_access')
            ->groupBy('user_id')
            ->get();

        $todayActivityLogs = $activitySummary->map(function ($row) use ($baseQuery) {
            $latestView = (clone $baseQuery)
                ->where('user_id', $row->user_id)
                ->latest('created_at')
                ->first();

            return [
                'name' => $row->user?->name ?? 'Pengguna #' . $row->user_id,
                'role' => $row->user?->role ?? 'user',
                'total_views' => $row->page_views,
                'last_path' => $latestView?->path ?? '/',
                'last_activity' => $latestView?->created_at ? $latestView->created_at->format('H:i:s') : '-',
            ];
        });

        return view('cms.reports.online', compact(
            'role',
            'tf',
            'startDate',
            'endDate',
            'subtitle',
            'onlineCount',
            'onlineUsersList',
            'lineLabels',
            'lineSeries',
            'totalActiveToday',
            'maxOnline',
            'peakHourLabel',
            'peakHourTooltip',
            'peakHours',
            'avgOnlinePerHour',
            'todayActivityLogs'
        ));
    }
}
