<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\PageView;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class RoleDashboardController extends Controller
{
    private static function fromSlug(string $slug): string
    {
        return Str::replace('-', '_', $slug);
    }

    public function index(Request $request): RedirectResponse
    {
        $roleName = $request->user()->role;
        $slug = Str::slug($roleName, '-');
        return redirect()->route('dashboard.role', ['slug' => $slug]);
    }

    /**
     * Build dashboard statistics and chart data for admin users.
     *
     * Fully dynamic — reads roles from `roles` table. If a role is added, removed,
     * or its badge_color changed, chart automatically adjusts without code changes.
     */
    private function buildAdminStats(): array
    {
        $now = now('Asia/Jakarta');
        $today = $now->toDateString();

        // === ONLINE USERS ===
        $fiveMinAgo = $now->copy()->subMinutes(5)->timestamp;
        $onlineCount = 0;
        foreach (User::pluck('id') as $uid) {
            $cached = Cache::get("online_user_{$uid}");
            if ($cached && $cached >= $fiveMinAgo) {
                $onlineCount++;
            }
        }

        $totalUsers = User::count();
        $totalVisitors = PageView::count();

        // === DYNAMIC ROLES: Load all from DB ===
        $allRoles = collect();
        $fallbackMode = false;

        try {
            $rolesFromDb = Role::all()->keyBy('name');
            if ($rolesFromDb->isNotEmpty()) {
                $allRoles = $rolesFromDb;
            } else {
                $fallbackMode = true;
            }
        } catch (\Throwable $e) {
            $fallbackMode = true;
        }

        if ($fallbackMode) {
            // Create anonymous role objects from seed data so chart still renders
            $makeRole = function ($name, $label, $badge) {
                return new class($name, $label, $badge) {
                    public string $name; public string $label; public string $badge_color;
                    public function __construct($n, $l, $b) { $this->name = $n; $this->label = $l; $this->badge_color = $b; }
                    public function i18nLabel(): string { return $this->label; }
                };
            };
            $allRoles = collect([
                'admin'             => $makeRole('admin', 'Administrator', 'red'),
                'pegawai'           => $makeRole('pegawai', 'Pegawai', 'yellow'),
                'umum'              => $makeRole('umum', 'Umum', 'gray'),
                'pelajar_mahasiswa' => $makeRole('pelajar_mahasiswa', 'Pelajar / Mahasiswa', 'blue'),
                'instansi_swasta'   => $makeRole('instansi_swasta', 'Instansi / Swasta', 'purple'),
            ]);
        }

        $roleNames = $allRoles->pluck('name')->toArray(); // ['admin', 'pegawai', ...]

        // Map badge_color → hex
        $colorMap = [
            'red' => '#EF4444', 'yellow' => '#EAB308', 'gray' => '#6B7280',
            'blue' => '#3B82F6', 'purple' => '#8B5CF6', 'green' => '#10B981',
            'orange' => '#F97316', 'cyan' => '#06B6D4', 'pink' => '#EC4899',
            'indigo' => '#6366F1', 'teal' => '#14B8A6',
        ];
        $chartColors = [];
        foreach ($allRoles as $name => $role) {
            $chartColors[$name] = $colorMap[$role->badge_color] ?? '#6B7280';
        }

        // === DATE RANGES ===
        $last7Days = [];
        for ($i = 6; $i >= 0; $i--) {
            $last7Days[] = $now->copy()->subDays($i)->format('Y-m-d');
        }
        $last30Days = [];
        for ($i = 29; $i >= 0; $i--) {
            $last30Days[] = $now->copy()->subDays($i)->format('Y-m-d');
        }
        $last365Days = [];
        for ($i = 364; $i >= 0; $i--) {
            $last365Days[] = $now->copy()->subDays($i)->format('Y-m-d');
        }

        // === TOTAL VISITORS PER TIMEFRAME ===
        $total7   = PageView::whereIn('viewed_date', $last7Days)->count();
        $total30  = PageView::whereIn('viewed_date', $last30Days)->count();
        $total365 = PageView::whereIn('viewed_date', $last365Days)->count();
        $avgDaily7   = $total7 > 0   ? round($total7 / 7, 1)   : 0;
        $avgDaily30   = $total30 > 0  ? round($total30 / 30, 1) : 0;
        $avgDaily365  = $total365 > 0 ? round($total365 / 365, 1) : 0;

        // === BUILD DATA PER ROLE (loop, not hardcoded) ===
        $guestData7 = $guestData30 = $guestDataYear = $guestTodayHourly = [];
        $roleData7 = $roleData30 = $roleDataYear = $roleTodayHourly = [];

        $guestCounts7  = PageView::whereIn('viewed_date', $last7Days)->whereNull('user_id')
            ->selectRaw('viewed_date, COUNT(*) as total')->groupBy('viewed_date')
            ->pluck('total', 'viewed_date')->toArray();
        $guestCounts30 = PageView::whereIn('viewed_date', $last30Days)->whereNull('user_id')
            ->selectRaw('viewed_date, COUNT(*) as total')->groupBy('viewed_date')
            ->pluck('total', 'viewed_date')->toArray();
        $guestCountsYear = [];
        for ($i = 11; $i >= 0; $i--) {
            $mStart = $now->copy()->subMonths($i)->startOfMonth()->format('Y-m-d');
            $mEnd   = $now->copy()->subMonths($i)->endOfMonth()->format('Y-m-d');
            $guestCountsYear[] = PageView::whereBetween('viewed_date', [$mStart, $mEnd])->whereNull('user_id')->count();
        }

        // Guest: fill missing dates with 0
        $guestData7  = array_map(fn($d) => (int) ($guestCounts7[$d] ?? 0), $last7Days);
        $guestData30 = array_map(fn($d) => (int) ($guestCounts30[$d] ?? 0), $last30Days);

        // Guest: Today Hourly (Real Data)
        // Group by hour (0-23) using created_at with Asia/Jakarta offset
        $guestHourlyCounts = PageView::where('viewed_date', $today)
            ->whereNull('user_id')
            ->selectRaw('HOUR(created_at) as hr, COUNT(*) as total')
            ->groupBy('hr')
            ->pluck('total', 'hr')->toArray();

        $guestTodayHourly = array_map(fn($h) => (int) ($guestHourlyCounts[$h] ?? 0), range(0, 23));

        // Per-role: loop dynamically
        foreach ($roleNames as $rname) {
            // 7-day
            $counts7 = PageView::whereIn('viewed_date', $last7Days)
                ->whereHas('user', fn($q) => $q->where('role', $rname))
                ->selectRaw('viewed_date, COUNT(*) as total')->groupBy('viewed_date')
                ->pluck('total', 'viewed_date')->toArray();
            $roleData7[$rname] = array_map(fn($d) => (int) ($counts7[$d] ?? 0), $last7Days);

            // 30-day
            $counts30 = PageView::whereIn('viewed_date', $last30Days)
                ->whereHas('user', fn($q) => $q->where('role', $rname))
                ->selectRaw('viewed_date, COUNT(*) as total')->groupBy('viewed_date')
                ->pluck('total', 'viewed_date')->toArray();
            $roleData30[$rname] = array_map(fn($d) => (int) ($counts30[$d] ?? 0), $last30Days);

            // 12-month
            $monthCounts = [];
            for ($i = 11; $i >= 0; $i--) {
                $mStart = $now->copy()->subMonths($i)->startOfMonth()->format('Y-m-d');
                $mEnd   = $now->copy()->subMonths($i)->endOfMonth()->format('Y-m-d');
                $monthCounts[] = PageView::whereBetween('viewed_date', [$mStart, $mEnd])
                    ->whereHas('user', fn($q) => $q->where('role', $rname))->count();
            }
            $roleDataYear[$rname] = $monthCounts;

            // Today Hourly (Real Data)
            $roleHourlyCounts = PageView::where('viewed_date', $today)
                ->whereHas('user', fn($q) => $q->where('role', $rname))
                ->selectRaw('HOUR(created_at) as hr, COUNT(*) as total')
                ->groupBy('hr')
                ->pluck('total', 'hr')->toArray();

            $roleTodayHourly[$rname] = array_map(fn($h) => (int) ($roleHourlyCounts[$h] ?? 0), range(0, 23));
        }

        // Guest 12-month
        $guestDataYear = $guestCountsYear;

        // Today's total
        $todayGuestCount = PageView::where('viewed_date', $today)->whereNull('user_id')->count();
        $todayTotalRoles = 0;
        foreach ($roleNames as $rname) {
            $todayTotalRoles += PageView::where('viewed_date', $today)
                ->whereHas('user', fn($q) => $q->where('role', $rname))->count();
        }
        $totalToday = $todayGuestCount + $todayTotalRoles;

        // === CHART LABELS ===
        $chartLabels7   = array_map(fn($d) => \Carbon\Carbon::parse($d)->format('d M'), $last7Days);
        $chartLabels30  = array_map(fn($d) => \Carbon\Carbon::parse($d)->format('d M'), $last30Days);
        $monthLabels = [];
        for ($i = 11; $i >= 0; $i--) {
            $monthLabels[] = $now->copy()->subMonths($i)->format('M');
        }

        // === JS DATA (structured for blade) ===
        $roleJsData = [];
        foreach ($roleNames as $rname) {
            $role = $allRoles[$rname] ?? null;
            $roleJsData[] = [
                'name'    => $rname,
                'label'   => $role ? $role->i18nLabel() : ucwords(str_replace('_', ' ', $rname)),
                'color'   => $chartColors[$rname] ?? '#6B7280',
                'data7'   => $roleData7[$rname] ?? [],
                'data30'  => $roleData30[$rname] ?? [],
                'dataYear'=> $roleDataYear[$rname] ?? array_fill(0, 12, 0),
                'todayHourly' => $roleTodayHourly[$rname] ?? array_fill(0, 24, 0),
            ];
        }

        return [
            // === Chart Config ===
            'chartRoles'  => $allRoles,
            'chartColors' => $chartColors,
            'guestColor'  => '#cececeff',
            'roleJsData'  => $roleJsData,

            // === Chart Labels ===
            'chartLabels7'    => $chartLabels7,
            'chartLabels30'   => $chartLabels30,
            'chartLabelsYear' => $monthLabels,

            // === Guest Data ===
            'guestData7'    => $guestData7,
            'guestData30'   => $guestData30,
            'guestDataYear' => $guestDataYear,
            'guestTodayHourly' => $guestTodayHourly,

            // === Stats ===
            'totalVisitors' => $totalVisitors,
            'total7'   => $total7,
            'total30'  => $total30,
            'total365' => $total365,
            'avgDaily7'   => $avgDaily7,
            'avgDaily30'  => $avgDaily30,
            'avgDaily365' => $avgDaily365,
            'onlineUsers'  => $onlineCount,
            'totalUsers'   => $totalUsers,
            'totalToday'   => $totalToday,
        ];
    }

    /**
     * Build dashboard statistics and chart data for non-admin users (pegawai, umum, pelajar, instansi).
     * Shows the user's own visit data across the entire site.
     */
    private function buildUserStats($user): array
    {
        $now = now('Asia/Jakarta');
        $today = $now->toDateString();
        $userId = $user->id;

        // === USER VISIT STATS ===
        // Total visits for this user (all time)
        $totalUserVisits = PageView::where('user_id', $userId)->count();

        // Today's visits for this user
        $userTodayVisits = PageView::where('user_id', $userId)
            ->where('viewed_date', $today)
            ->count();

        // Last 7 days
        $last7Days = [];
        for ($i = 6; $i >= 0; $i--) {
            $last7Days[] = $now->copy()->subDays($i)->format('Y-m-d');
        }

        // Last 30 days
        $last30Days = [];
        for ($i = 29; $i >= 0; $i--) {
            $last30Days[] = $now->copy()->subDays($i)->format('Y-m-d');
        }

        // Last 365 days
        $last365Days = [];
        for ($i = 364; $i >= 0; $i--) {
            $last365Days[] = $now->copy()->subDays($i)->format('Y-m-d');
        }

        // 7-day visits
        $userVisits7 = PageView::where('user_id', $userId)
            ->whereIn('viewed_date', $last7Days)
            ->count();

        // 30-day visits
        $userVisits30 = PageView::where('user_id', $userId)
            ->whereIn('viewed_date', $last30Days)
            ->count();

        // 365-day visits
        $userVisits365 = PageView::where('user_id', $userId)
            ->whereIn('viewed_date', $last365Days)
            ->count();

        // === CHART DATA: User daily visits ===
        // 7-day data
        $userCounts7 = PageView::where('user_id', $userId)
            ->whereIn('viewed_date', $last7Days)
            ->selectRaw('viewed_date, COUNT(*) as total')
            ->groupBy('viewed_date')
            ->pluck('total', 'viewed_date')
            ->toArray();

        $userData7 = array_map(fn($d) => (int) ($userCounts7[$d] ?? 0), $last7Days);

        // 30-day data
        $userCounts30 = PageView::where('user_id', $userId)
            ->whereIn('viewed_date', $last30Days)
            ->selectRaw('viewed_date, COUNT(*) as total')
            ->groupBy('viewed_date')
            ->pluck('total', 'viewed_date')
            ->toArray();

        $userData30 = array_map(fn($d) => (int) ($userCounts30[$d] ?? 0), $last30Days);

        // Year data (monthly)
        $userCountsYear = [];
        for ($i = 11; $i >= 0; $i--) {
            $mStart = $now->copy()->subMonths($i)->startOfMonth()->format('Y-m-d');
            $mEnd = $now->copy()->subMonths($i)->endOfMonth()->format('Y-m-d');
            $userCountsYear[] = PageView::where('user_id', $userId)
                ->whereBetween('viewed_date', [$mStart, $mEnd])
                ->count();
        }

        // Today hourly data
        $userHourlyCounts = PageView::where('user_id', $userId)
            ->where('viewed_date', $today)
            ->selectRaw('HOUR(created_at) as hr, COUNT(*) as total')
            ->groupBy('hr')
            ->pluck('total', 'hr')
            ->toArray();

        $userTodayHourly = array_map(fn($h) => (int) ($userHourlyCounts[$h] ?? 0), range(0, 23));

        // === CHART LABELS ===
        $chartLabels7 = array_map(fn($d) => \Carbon\Carbon::parse($d)->format('d M'), $last7Days);
        $chartLabels30 = array_map(fn($d) => \Carbon\Carbon::parse($d)->format('d M'), $last30Days);
        $monthLabels = [];
        for ($i = 11; $i >= 0; $i--) {
            $monthLabels[] = $now->copy()->subMonths($i)->format('M');
        }

        return [
            'totalVisits' => $totalUserVisits,
            'totalToday' => $userTodayVisits,
            'total7' => $userVisits7,
            'total30' => $userVisits30,
            'total365' => $userVisits365,
            'userData7' => $userData7,
            'userData30' => $userData30,
            'userDataYear' => $userCountsYear,
            'userTodayHourly' => $userTodayHourly,
            'chartLabels7' => $chartLabels7,
            'chartLabels30' => $chartLabels30,
            'chartLabelsYear' => $monthLabels,
        ];
    }

    public function show(Request $request, string $roleIdentifier): View
    {
        $roleName = self::fromSlug($roleIdentifier);

        if ($request->user()->role !== $roleName) {
            abort(403, 'Anda tidak memiliki akses ke dashboard ini.');
        }

        $roleModel = Role::where('name', $roleName)->first();

        if (!$roleModel) {
            abort(404, 'Role tidak ditemukan.');
        }

        $viewData = [
            'role'       => $roleName,
            'roleLabel'  => $roleModel->label,
            'badgeColor' => $roleModel->badge_color,
            'user'       => $request->user(),
        ];

        if (in_array($roleName, ['admin', 'pegawai'])) {
            $viewData = array_merge($viewData, $this->buildAdminStats());
        } else {
            $viewData = array_merge($viewData, $this->buildUserStats($request->user()));
        }

        return view('dashboards.index', $viewData);
    }
}
