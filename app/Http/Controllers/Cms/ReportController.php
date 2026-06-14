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

        $hasAllPermission = ($userRoleObj && $userRoleObj->hasPermission('cms.reports.all'));
        $hasOwnPermission = ($userRoleObj && $userRoleObj->hasPermission('cms.reports.own'));

        if (!$hasAllPermission && !$hasOwnPermission) {
            abort(403, 'Anda tidak memiliki akses ke halaman laporan.');
        }

        $isAdminOrPegawai = $hasAllPermission;

        $tf = $request->input('tf', 'day'); // day, week, month, year, custom
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = VisitRegistration::query();
        if (!$isAdminOrPegawai && $user) {
            $query->where(DB::raw('LOWER(trim(email))'), strtolower(trim($user->email)));
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
        $totalKunjungan = (int) (clone $query)->sum('visitor_count');

        // Fetch dynamic visit purposes from DB to include 0-count ones
        $purposeCounts = (clone $query)
            ->selectRaw('LOWER(visit_purpose) as purpose, SUM(visitor_count) as total')
            ->groupBy('purpose')
            ->pluck('total', 'purpose')
            ->toArray();

        // Get all configured purposes from LayananPublik CMS
        $kunjunganPage = \App\Models\LayananPublik::where('type', 'kunjungan')
            ->orWhere('title', 'like', '%kunjungan%')
            ->first();
        
        $configuredPurposes = [];
        if ($kunjunganPage && !empty($kunjunganPage->extra_data['form_fields'])) {
            foreach ($kunjunganPage->extra_data['form_fields'] as $field) {
                if (($field['id'] ?? '') === 'visit_purpose' && !empty($field['options'])) {
                    $opts = array_map('trim', explode(',', $field['options']));
                    foreach ($opts as $opt) {
                        $configuredPurposes[strtolower($opt)] = $opt;
                    }
                }
            }
        }
        
        // Add default ones if empty
        if (empty($configuredPurposes)) {
            $configuredPurposes = [
                'edukasi' => 'Edukasi',
                'penelitian' => 'Penelitian',
                'kunker' => 'Kunjungan Kerja'
            ];
        }

        // Default stats for the top 3 cards (get first 3 configured)
        $confKeys = array_keys($configuredPurposes);
        $totalEdukasi = $purposeCounts[$confKeys[0] ?? 'edukasi'] ?? 0;
        $totalPenelitian = $purposeCounts[$confKeys[1] ?? 'penelitian'] ?? 0;
        $totalKunker = $purposeCounts[$confKeys[2] ?? 'kunker'] ?? 0;

        // Color Palette
        $palette = ['#22c55e', '#a855f7', '#f59e0b', '#3b82f6', '#ec4899', '#14b8a6', '#f43f5e', '#8b5cf6'];
        $purposeColors = [];
        
        // Pie chart data (Dynamic)
        $pieData = [];
        $pieLabels = [];
        $pieColors = [];
        $i = 0;
        foreach ($configuredPurposes as $purposeKey => $purposeLabel) {
            $count = $purposeCounts[$purposeKey] ?? 0;
            $pieData[] = (int) $count;
            $pieLabels[] = $purposeLabel;
            
            $color = $palette[$i % count($palette)];
            $pieColors[] = $color;
            $purposeColors[$purposeKey] = [
                'label' => $purposeLabel,
                'color' => $color,
                'bg_class' => 'bg-opacity-10', // To be styled dynamically
                'text_color' => $color
            ];
            $i++;
        }


        // Line chart data (Dynamic date range based on tf)
        $lineLabels = [];
        $lineSeriesApproved = [];
        $lineSeriesRejected = [];

        if ($tf === 'day') {
            // Group by hour for today
            $today = Carbon::today()->toDateString();
            
            // Approved
            $hourlyQueryApproved = VisitRegistration::whereDate('created_at', $today)->where('status', 'approved');
            if (!$isAdminOrPegawai && $user) {
                $hourlyQueryApproved->where(DB::raw('LOWER(trim(email))'), strtolower(trim($user->email)));
            }
            $hourlyCountsApproved = $hourlyQueryApproved
                ->selectRaw('HOUR(created_at) as hr, SUM(visitor_count) as total')
                ->groupBy('hr')
                ->pluck('total', 'hr')
                ->toArray();

            // Rejected
            $hourlyQueryRejected = VisitRegistration::whereDate('created_at', $today)->where('status', 'rejected');
            if (!$isAdminOrPegawai && $user) {
                $hourlyQueryRejected->where(DB::raw('LOWER(trim(email))'), strtolower(trim($user->email)));
            }
            $hourlyCountsRejected = $hourlyQueryRejected
                ->selectRaw('HOUR(created_at) as hr, SUM(visitor_count) as total')
                ->groupBy('hr')
                ->pluck('total', 'hr')
                ->toArray();

            $lineLabels = array_map(fn($h) => str_pad($h, 2, '0', STR_PAD_LEFT) . ':00', range(0, 23));
            $lineSeriesApproved = array_map(fn($h) => (int) ($hourlyCountsApproved[$h] ?? 0), range(0, 23));
            $lineSeriesRejected = array_map(fn($h) => (int) ($hourlyCountsRejected[$h] ?? 0), range(0, 23));

        } elseif ($tf === 'year') {
            // Group by month for the last 12 months
            $yearAgo = Carbon::now()->subDays(365)->startOfDay();
            
            // Approved
            $monthlyQueryApproved = VisitRegistration::where('created_at', '>=', $yearAgo)->where('status', 'approved');
            if (!$isAdminOrPegawai && $user) {
                $monthlyQueryApproved->where(DB::raw('LOWER(trim(email))'), strtolower(trim($user->email)));
            }
            $monthlyCountsApproved = $monthlyQueryApproved
                ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as mth, SUM(visitor_count) as total')
                ->groupBy('mth')
                ->pluck('total', 'mth')
                ->toArray();

            // Rejected
            $monthlyQueryRejected = VisitRegistration::where('created_at', '>=', $yearAgo)->where('status', 'rejected');
            if (!$isAdminOrPegawai && $user) {
                $monthlyQueryRejected->where(DB::raw('LOWER(trim(email))'), strtolower(trim($user->email)));
            }
            $monthlyCountsRejected = $monthlyQueryRejected
                ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as mth, SUM(visitor_count) as total')
                ->groupBy('mth')
                ->pluck('total', 'mth')
                ->toArray();

            for ($i = 11; $i >= 0; $i--) {
                $m = Carbon::now()->subMonths($i);
                $key = $m->format('Y-m');
                $lineLabels[] = $m->translatedFormat('M Y');
                $lineSeriesApproved[] = (int) ($monthlyCountsApproved[$key] ?? 0);
                $lineSeriesRejected[] = (int) ($monthlyCountsRejected[$key] ?? 0);
            }

        } else {
            // Group by day for week, month, and custom
            if ($tf === 'custom' && $startDate && $endDate) {
                $start = Carbon::parse($startDate)->startOfDay();
                $end = Carbon::parse($endDate)->endOfDay();
            } elseif ($tf === 'week') {
                $start = Carbon::now()->subDays(7)->startOfDay();
                $end = Carbon::now()->endOfDay();
            } else { // month or default fallback
                $start = Carbon::now()->subDays(30)->startOfDay();
                $end = Carbon::now()->endOfDay();
            }

            $diffDays = $start->diffInDays($end);
            if ($diffDays > 365) {
                $start = (clone $end)->subDays(365)->startOfDay();
                $diffDays = 365;
            }

            for ($i = 0; $i <= $diffDays; $i++) {
                $current = (clone $start)->addDays($i)->toDateString();
                $lineLabels[] = Carbon::parse($current)->format('d M');
                
                // Approved
                $subQApproved = VisitRegistration::whereDate('created_at', $current)->where('status', 'approved');
                if (!$isAdminOrPegawai && $user) {
                    $subQApproved->where(DB::raw('LOWER(trim(email))'), strtolower(trim($user->email)));
                }
                $lineSeriesApproved[] = (int) $subQApproved->sum('visitor_count');

                // Rejected
                $subQRejected = VisitRegistration::whereDate('created_at', $current)->where('status', 'rejected');
                if (!$isAdminOrPegawai && $user) {
                    $subQRejected->where(DB::raw('LOWER(trim(email))'), strtolower(trim($user->email)));
                }
                $lineSeriesRejected[] = (int) $subQRejected->sum('visitor_count');
            }
        }

        // All data for DataTables client-side handling
        $registrations = (clone $query)->latest()->get();

        $currentPage = \App\Models\LayananPublik::where('type', 'kunjungan')->first() 
            ?? \App\Models\LayananPublik::where('title', 'like', '%kunjungan%')->first();
        $formFields = [];
        if ($currentPage && !empty($currentPage->extra_data['form_fields'])) {
            $formFields = $currentPage->extra_data['form_fields'];
        }

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
            'pieColors',
            'purposeColors',
            'configuredPurposes',
            'lineLabels',
            'lineSeriesApproved',
            'lineSeriesRejected',
            'registrations',
            'formFields'
        ));
    }

    /**
     * Update status pendaftaran kunjungan (disetujui/ditolak) dan kirim email
     */
    public function updateStatus(Request $request, $id)
    {
        $user = $request->user();
        $role = $user?->role ?? 'admin';
        $userRoleObj = $user ? \App\Models\Role::where('name', $role)->first() : null;

        $hasAllPermission = ($userRoleObj && $userRoleObj->hasPermission('cms.reports.all'));
        if (!$hasAllPermission) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki hak akses untuk mengubah status permohonan.'
            ], 403);
        }

        $request->validate([
            'status' => 'required|in:approved,rejected',
            'keterangan' => 'nullable|string',
        ]);

        $registration = VisitRegistration::findOrFail($id);
        
        $status = $request->input('status');
        $keterangan = $request->input('keterangan');

        $registration->update([
            'status' => $status,
            'keterangan' => $keterangan,
        ]);

        // Kirim email notifikasi
        try {
            \Illuminate\Support\Facades\Notification::route('mail', $registration->email)
                ->notify(new \App\Notifications\VisitStatusNotification($registration, $status, $keterangan));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal mengirim email update status kunjungan: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Status pendaftaran kunjungan berhasil diperbarui dan email notifikasi telah dikirim.',
            'registration' => $registration
        ]);
    }

    /**
     * Hapus data pendaftaran kunjungan
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $role = $user?->role ?? 'admin';
        $userRoleObj = $user ? \App\Models\Role::where('name', $role)->first() : null;

        $hasAllPermission = ($userRoleObj && $userRoleObj->hasPermission('cms.reports.all'));
        if (!$hasAllPermission) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki hak akses untuk menghapus data pendaftaran kunjungan.'
            ], 403);
        }

        $registration = VisitRegistration::findOrFail($id);
        $registration->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data pendaftaran kunjungan berhasil dihapus.'
        ]);
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

        $hasAllPermission = ($userRoleObj && $userRoleObj->hasPermission('cms.reports.all'));
        $hasOwnPermission = ($userRoleObj && $userRoleObj->hasPermission('cms.reports.own'));

        if (!$hasAllPermission && !$hasOwnPermission) {
            abort(403, 'Anda tidak memiliki akses ke halaman laporan.');
        }

        $isAdminOrPegawai = $hasAllPermission;

        $tf = $request->input('tf', 'day'); // day, week, month, year, custom
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = PageView::query();
        if (!$isAdminOrPegawai && $user) {
            $query->where('user_id', $user->id);
        }

        // Exclude admin, cms, dashboard, profile, and api pages
        $query->where(function ($q) {
            $q->where('path', 'not like', 'cms%')
              ->where('path', 'not like', '/cms%')
              ->where('path', 'not like', 'dashboard%')
              ->where('path', 'not like', '/dashboard%')
              ->where('path', 'not like', 'profile%')
              ->where('path', 'not like', '/profile%')
              ->where('path', 'not like', 'api%')
              ->where('path', 'not like', '/api%')
              ->where('path', 'not like', '_debugbar%')
              ->where('path', 'not like', '/_debugbar%')
              ->where('path', 'not like', 'profil/admin%')
              ->where('path', 'not like', '/profil/admin%');
        });

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

        $hasAllPermission = ($userRoleObj && $userRoleObj->hasPermission('cms.reports.all'));
        $hasOwnPermission = ($userRoleObj && $userRoleObj->hasPermission('cms.reports.own'));

        if (!$hasAllPermission && !$hasOwnPermission) {
            abort(403, 'Anda tidak memiliki akses ke halaman laporan.');
        }

        $isAdminOrPegawai = $hasAllPermission;

        $tf = $request->input('tf', 'day'); // day, week, month, year, custom
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = PageView::query();
        if (!$isAdminOrPegawai && $user) {
            $query->where('user_id', $user->id);
        }

        // Exclude admin, cms, dashboard, profile, and api pages
        $query->where(function ($q) {
            $q->where('path', 'not like', 'cms%')
              ->where('path', 'not like', '/cms%')
              ->where('path', 'not like', 'dashboard%')
              ->where('path', 'not like', '/dashboard%')
              ->where('path', 'not like', 'profile%')
              ->where('path', 'not like', '/profile%')
              ->where('path', 'not like', 'api%')
              ->where('path', 'not like', '/api%')
              ->where('path', 'not like', '_debugbar%')
              ->where('path', 'not like', '/_debugbar%')
              ->where('path', 'not like', 'profil/admin%')
              ->where('path', 'not like', '/profil/admin%');
        });

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

        // Get all active features (including ancestor active status check)
        $features = \App\Models\Feature::with('parent')->get()->filter(function($f) {
            if ($f->type !== 'link') {
                return false;
            }
            if (strtolower($f->name) === 'admin' || str_contains(strtolower($f->path), 'admin') || str_contains(strtolower($f->path), 'cms')) {
                return false;
            }
            $curr = $f;
            while ($curr !== null) {
                if (!$curr->is_active) {
                    return false;
                }
                $curr = $curr->parent;
            }
            return true;
        });

        $getSortKey = function($f) use (&$getSortKey) {
            $order = $f->order ?? 0;
            $key = str_pad($order, 5, '0', STR_PAD_LEFT);
            if ($f->parent) {
                return $getSortKey($f->parent) . '.' . $key;
            }
            return $key;
        };

        $orderedFeatures = $features->sortBy($getSortKey);
        $matchingFeatures = $features->sortByDesc(fn($f) => strlen(trim($f->path, '/')));

        $pages = [];
        $hasBeranda = false;
        foreach ($orderedFeatures as $f) {
            $fPath = trim($f->path, '/');
            if ($fPath === '' || $fPath === 'beranda') {
                $fPath = 'beranda';
                $hasBeranda = true;
            }
            
            // Find root parent
            $root = $f;
            while ($root->parent !== null) {
                $root = $root->parent;
            }
            $rootLabel = $root->translated_name ?: $root->name;

            $pages[$fPath] = [
                'label' => $f->translated_name ?: $f->name,
                'root_label' => $rootLabel,
                'count' => 0
            ];
        }
        if (!$hasBeranda) {
            $pages['beranda'] = [
                'label' => 'Beranda',
                'root_label' => 'Beranda',
                'count' => 0
            ];
        }

        foreach ($views as $v) {
            $path = trim($v->path, '/');
            if ($path === '' || $path === 'beranda') {
                $path = 'beranda';
            }

            foreach ($matchingFeatures as $f) {
                $fPath = trim($f->path, '/');
                if ($fPath === '' || $fPath === 'beranda') {
                    $fPath = 'beranda';
                }

                if ($fPath === 'beranda' && $path === 'beranda') {
                    $pages['beranda']['count']++;
                    break;
                } elseif ($fPath !== 'beranda' && str_starts_with($path, $fPath)) {
                    $pages[$fPath]['count']++;
                    break;
                }
            }
        }

        $barLabels = array_values(array_column($pages, 'label'));
        $barSeries = array_values(array_column($pages, 'count'));

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

        $hasAllPermission = ($userRoleObj && $userRoleObj->hasPermission('cms.reports.all'));
        $hasOwnPermission = ($userRoleObj && $userRoleObj->hasPermission('cms.reports.own'));

        if (!$hasAllPermission && !$hasOwnPermission) {
            abort(403, 'Anda tidak memiliki akses ke halaman laporan.');
        }

        $isAdminOrPegawai = $hasAllPermission;

        $tf = $request->input('tf', 'day'); // day, week, month, year, custom
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = PageView::query();
        if (!$isAdminOrPegawai && $user) {
            $query->where('user_id', $user->id);
        }

        // Exclude admin, cms, dashboard, profile, and api pages
        $query->where(function ($q) {
            $q->where('path', 'not like', 'cms%')
              ->where('path', 'not like', '/cms%')
              ->where('path', 'not like', 'dashboard%')
              ->where('path', 'not like', '/dashboard%')
              ->where('path', 'not like', 'profile%')
              ->where('path', 'not like', '/profile%')
              ->where('path', 'not like', 'api%')
              ->where('path', 'not like', '/api%')
              ->where('path', 'not like', '_debugbar%')
              ->where('path', 'not like', '/_debugbar%')
              ->where('path', 'not like', 'profil/admin%')
              ->where('path', 'not like', '/profil/admin%');
        });

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

        // Get all active features (including ancestor active status check)
        $features = \App\Models\Feature::with('parent')->get()->filter(function($f) {
            if ($f->type !== 'link') {
                return false;
            }
            if (strtolower($f->name) === 'admin' || str_contains(strtolower($f->path), 'admin') || str_contains(strtolower($f->path), 'cms')) {
                return false;
            }
            $curr = $f;
            while ($curr !== null) {
                if (!$curr->is_active) {
                    return false;
                }
                $curr = $curr->parent;
            }
            return true;
        });

        $getSortKey = function($f) use (&$getSortKey) {
            $order = $f->order ?? 0;
            $key = str_pad($order, 5, '0', STR_PAD_LEFT);
            if ($f->parent) {
                return $getSortKey($f->parent) . '.' . $key;
            }
            return $key;
        };

        $orderedFeatures = $features->sortBy($getSortKey);
        $matchingFeatures = $features->sortByDesc(fn($f) => strlen(trim($f->path, '/')));

        $pages = [];
        $hasBeranda = false;
        foreach ($orderedFeatures as $f) {
            $fPath = trim($f->path, '/');
            if ($fPath === '' || $fPath === 'beranda') {
                $fPath = 'beranda';
                $hasBeranda = true;
            }

            // Find root parent
            $root = $f;
            while ($root->parent !== null) {
                $root = $root->parent;
            }
            $rootLabel = $root->translated_name ?: $root->name;

            $pages[$fPath] = [
                'label' => $f->translated_name ?: $f->name,
                'root_label' => $rootLabel,
                'count' => 0
            ];
        }
        if (!$hasBeranda) {
            $pages['beranda'] = [
                'label' => 'Beranda',
                'root_label' => 'Beranda',
                'count' => 0
            ];
        }

        // Track unique page visits per IP+Date
        $uniquePageVisits = [];

        foreach ($views as $v) {
            $path = trim($v->path, '/');
            if ($path === '' || $path === 'beranda') {
                $path = 'beranda';
            }

            foreach ($matchingFeatures as $f) {
                $fPath = trim($f->path, '/');
                if ($fPath === '' || $fPath === 'beranda') {
                    $fPath = 'beranda';
                }

                if ($fPath === 'beranda' && $path === 'beranda') {
                    $category = 'beranda';
                    $key = $category . '_' . $v->ip . '_' . $v->date;
                    $uniquePageVisits[$key] = $category;
                    break;
                } elseif ($fPath !== 'beranda' && str_starts_with($path, $fPath)) {
                    $category = $fPath;
                    $key = $category . '_' . $v->ip . '_' . $v->date;
                    $uniquePageVisits[$key] = $category;
                    break;
                }
            }
        }

        // Increment counts based on unique page visits
        foreach ($uniquePageVisits as $category) {
            if (isset($pages[$category])) {
                $pages[$category]['count']++;
            }
        }

        $barLabels = array_values(array_column($pages, 'label'));
        $barSeries = array_values(array_column($pages, 'count'));

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

        $hasAllPermission = ($userRoleObj && $userRoleObj->hasPermission('cms.reports.all'));
        $hasOwnPermission = ($userRoleObj && $userRoleObj->hasPermission('cms.reports.own'));

        if (!$hasAllPermission && !$hasOwnPermission) {
            abort(403, 'Anda tidak memiliki akses ke halaman laporan.');
        }

        $isAdminOrPegawai = $hasAllPermission;

        $tf = $request->input('tf', 'day'); // day, week, month, year, custom
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = ArchivalConsultation::query();
        if (!$isAdminOrPegawai && $user) {
            $query->where(DB::raw('LOWER(trim(email))'), strtolower(trim($user->email)));
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

        $currentPage = \App\Models\LayananPublik::where('type', 'konsultasi')->first() 
            ?? \App\Models\LayananPublik::where('title', 'like', '%konsultasi%')->first();
        $formFields = [];
        if ($currentPage && !empty($currentPage->extra_data['form_fields'])) {
            $formFields = $currentPage->extra_data['form_fields'];
        }

        return view('cms.reports.konsultasi', compact(
            'role',
            'tf',
            'startDate',
            'endDate',
            'subtitle',
            'totalKonsultasi',
            'consultations',
            'formFields',
            'hasAllPermission'
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

        $hasAllPermission = ($userRoleObj && $userRoleObj->hasPermission('cms.reports.all'));
        $hasOwnPermission = ($userRoleObj && $userRoleObj->hasPermission('cms.reports.own'));

        if (!$hasAllPermission && !$hasOwnPermission) {
            abort(403, 'Anda tidak memiliki akses ke halaman laporan.');
        }

        $isAdminOrPegawai = $hasAllPermission;

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
                $u->last_activity = Carbon::createFromTimestamp($cached, 'Asia/Jakarta')->format('H:i:s');
            } else {
                $u->last_activity = $cached ? Carbon::createFromTimestamp($cached, 'Asia/Jakarta')->format('d M Y H:i:s') : '-';
            }
        }
        
        $onlineUsersList = $allUsers->whereIn('id', $onlineUserIds)->values();
        $onlineCount = $onlineUsersList->count();

        // Base Query untuk filter TF
        $baseQuery = PageView::query();
        if (!$isAdminOrPegawai && $user) {
            $baseQuery->where('user_id', $user->id);
        }

        $subtitle = __('dashboard.admin.chart.filter_day', ['default' => 'Hari Ini']);
        if ($tf === 'custom' && $startDate && $endDate) {
            $baseQuery->whereBetween('viewed_date', [$startDate, $endDate]);
            $subtitle = 'Periode: ' . Carbon::parse($startDate)->format('d M Y') . ' - ' . Carbon::parse($endDate)->format('d M Y');
        } elseif ($tf === 'day') {
            $baseQuery->where('viewed_date', $now->toDateString());
        } elseif ($tf === 'week') {
            $baseQuery->where('viewed_date', '>=', (clone $now)->subDays(7)->toDateString());
            $subtitle = __('dashboard.admin.chart.filter_week', ['default' => '7 Hari Terakhir']);
        } elseif ($tf === 'month') {
            $baseQuery->where('viewed_date', '>=', (clone $now)->subDays(30)->toDateString());
            $subtitle = __('dashboard.admin.chart.filter_month', ['default' => '30 Hari Terakhir']);
        } elseif ($tf === 'year') {
            $baseQuery->where('viewed_date', '>=', (clone $now)->subDays(365)->toDateString());
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
        if ($hasAllPermission) {
            $activitySummary = (clone $baseQuery)
                ->with('user')
                ->selectRaw('user_id, COUNT(*) as page_views, MAX(created_at) as last_access')
                ->groupBy('user_id')
                ->get();

            $todayActivityLogs = $activitySummary->map(function ($row) use ($baseQuery) {
                $latestView = (clone $baseQuery)
                    ->when($row->user_id, function($q) use ($row) {
                        $q->where('user_id', $row->user_id);
                    }, function($q) {
                        $q->whereNull('user_id');
                    })
                    ->latest('created_at')
                    ->first();

                return [
                    'name' => $row->user?->name ?? ($row->user_id ? 'Pengguna #' . $row->user_id : 'Pengunjung Umum (Guest)'),
                    'role' => $row->user?->role ?? ($row->user_id ? 'user' : 'guest'),
                    'total_views' => $row->page_views,
                    'last_path' => $latestView?->path ?? '/',
                    'last_activity' => $latestView?->created_at ? $latestView->created_at->format('d M Y H:i:s') : '-',
                ];
            });
        } else {
            $activitySummary = (clone $baseQuery)
                ->selectRaw('path, COUNT(*) as page_views, MAX(created_at) as last_access')
                ->groupBy('path')
                ->get();

            $todayActivityLogs = $activitySummary->map(function ($row) {
                return [
                    'last_path' => $row->path,
                    'total_views' => $row->page_views,
                    'last_activity' => \Carbon\Carbon::parse($row->last_access)->format('d M Y H:i:s'),
                ];
            })->sortByDesc('last_access')->values();
        }

        return view('cms.reports.online', compact(
            'role',
            'hasAllPermission',
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

    /**
     * Balas pesan Konsultasi Kearsipan
     */
    public function replyKonsultasi(Request $request, $id)
    {
        $user = $request->user();
        $role = $user?->role ?? 'admin';
        $userRoleObj = $user ? \App\Models\Role::where('name', $role)->first() : null;

        $hasAllPermission = ($userRoleObj && $userRoleObj->hasPermission('cms.reports.all'));
        if (!$hasAllPermission) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki hak akses untuk membalas konsultasi.'
            ], 403);
        }

        $request->validate([
            'message' => 'required|string'
        ]);

        $consultation = ArchivalConsultation::findOrFail($id);

        if ($consultation->is_replied) {
            return response()->json([
                'success' => false,
                'message' => __('cms.reports.msg_replied_already')
            ], 400);
        }

        try {
            \Illuminate\Support\Facades\Notification::route('mail', $consultation->email)
                ->notify(new \App\Notifications\ConsultationReplyNotification($consultation, $request->input('message')));

            $consultation->is_replied = true;
            $consultation->reply_message = $request->input('message');
            $consultation->save();

            return response()->json([
                'success' => true,
                'message' => __('cms.reports.msg_reply_success')
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Mail Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => __('cms.reports.msg_reply_fail')
            ], 500);
        }
    }

    /**
     * Hapus data Konsultasi Kearsipan
     */
    public function destroyKonsultasi(Request $request, $id)
    {
        $user = $request->user();
        $role = $user?->role ?? 'admin';
        $userRoleObj = $user ? \App\Models\Role::where('name', $role)->first() : null;

        $hasAllPermission = ($userRoleObj && $userRoleObj->hasPermission('cms.reports.all'));
        if (!$hasAllPermission) {
            return response()->json([
                'success' => false,
                'message' => __('cms.reports.msg_del_no_access')
            ], 403);
        }

        $consultation = ArchivalConsultation::findOrFail($id);

        if ($consultation->attachment) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($consultation->attachment);
        }

        $consultation->delete();

        return response()->json([
            'success' => true,
            'message' => __('cms.reports.msg_del_success')
        ]);
    }
}
