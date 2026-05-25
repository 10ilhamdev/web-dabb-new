<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Role;
use App\Models\User;
use App\Services\LangSyncService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile view.
     */
    public function show(Request $request): View
    {
        $user = $request->user();
        $profileColumns = User::roleProfileColumns($user->role);
        $profileData = self::buildProfileData($user->profile, $profileColumns->all());

        return view('profile.show', [
            'user'          => $user,
            'profileColumns'=> $profileColumns,
            'profileData'   => $profileData,
        ]);
    }

    /**
     * Build translated profile data array for the view.
     * Translates ALL column values:
     *  - Enum/set columns via LangSyncService
     *  - Text/varchar columns via __() lookup (if value matches a lang key)
     *  - Address field: translate Indonesian admin terms → English equivalents
     */
    public static function buildProfileData($profile, array $profileColumns): array
    {
        $result = [];

        foreach ($profileColumns as $col) {
            $name  = $col->column_name;
            $type  = $col->column_type;
            $value = $profile?->{$name};

            if ($value === null || $value === '') {
                $result[$name] = ['value' => null, 'type' => $type];
                continue;
            }

            if (in_array($type, ['date', 'datetime', 'timestamp'])) {
                try {
                    $result[$name] = [
                        'value' => \Carbon\Carbon::parse($value)->translatedFormat('d F Y'),
                        'type'  => $type,
                    ];
                } catch (\Throwable) {
                    $result[$name] = ['value' => $value, 'type' => $type];
                }
                continue;
            }

            if ($type === 'blob') {
                $result[$name] = ['value' => $value, 'type' => $type];
                continue;
            }

            // LangSyncService handles ALL column types — enum, varchar, text, etc.
            // translateEnum() now includes translateRawValue() internally.
            $translated = LangSyncService::translateEnum($name, (string) $value);

            // Address term translation: ONLY translate ID→EN when locale=EN
            if (app()->getLocale() === 'en' && (in_array($name, ['alamat']) || str_contains($name, 'alamat'))) {
                $translated = self::translateAddressTerms($translated);
            }

            $result[$name] = [
                'value' => $translated,
                'type'  => $type,
            ];
        }

        return $result;
    }

    /**
     * Translate Indonesian address terms to English equivalents.
     * Handles patterns like "Kabupaten Garut", "Kota Bandung", "Provinsi Jawa Barat"
     */
    private static function translateAddressTerms(string $value): string
    {
        $terms = [
            // Admin levels (trim leading space — these appear at word-start or after "di", "ke", etc.)
            'Kabupaten'  => 'Regency of',
            'Kota'        => 'City of',
            'Provinsi'    => 'Province of',
            'Kecamatan'  => 'District of',
            'Kelurahan'   => 'Village of',
            'Desa'        => 'Village of',
            'RT'          => 'RT ',
            'RW'          => ' RW ',
            'Jalan'       => 'Jl. ',
            'No.'         => 'No. ',
            'Gedung'      => 'Building ',
            'Lantai'      => 'Floor ',
        ];

        foreach ($terms as $id => $en) {
            $value = preg_replace('/\b' . preg_quote($id, '/') . '\b/ui', $en, $value);
        }

        return $value;
    }

    /**
     * Get column label: use dashboard.lang keys first, then DB label, then headline.
     */
    public static function colLabel(string $col, ?string $dbLabel): string
    {
        // Try dashboard.php keys (dashboard.profile.col_X)
        $key = 'dashboard.profile.col_' . $col;
        $trans = __($key);
        if ($trans !== $key) return $trans;

        // Try auth.php keys (auth.col_X)
        $authKey = 'auth.col_' . $col;
        $authTrans = __($authKey);
        if ($authTrans !== $authKey) return $authTrans;

        return $dbLabel ?? \Illuminate\Support\Str::headline($col);
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $profileColumns = User::roleProfileColumns($user->role);

        // Pre-fetch enum options for enum-type columns
        $role = Role::where('name', $user->role)->first();
        $enumOptions = [];
        if ($role) {
            foreach ($profileColumns as $col) {
                if (in_array($col->column_type, ['enum', 'set'])) {
                    if (!empty($col->options)) {
                        $enumOptions[$col->column_name] = $col->options;
                    } else {
                        $tableName = $role->table_name;
                        $enumOptions[$col->column_name] = User::getEnumValues($tableName, $col->column_name);
                    }
                }
            }
        }

        // Translate enum option labels for display in select
        $translatedEnumOptions = [];
        foreach ($enumOptions as $colName => $options) {
            foreach ($options as $opt) {
                $translatedEnumOptions[$colName][$opt] = LangSyncService::translateEnum($colName, $opt);
            }
        }

        return view('profile.edit', [
            'user' => $user,
            'profileColumns' => $profileColumns,
            'enumOptions' => $enumOptions,
            'translatedEnumOptions' => $translatedEnumOptions,
        ]);
    }

    /**
     * Display the user's password edit form.
     */
    public function password(Request $request): View
    {
        return view('profile.password', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Display the user's login activity.
     */
    public function activity(Request $request): View
    {
        $sessions = collect();

        if (config('session.driver') === 'database') {
            $sessions = DB::table('sessions')
                ->where('user_id', $request->user()->getAuthIdentifier())
                ->orderBy('last_activity', 'desc')
                ->get()
                ->map(function ($session) use ($request) {
                    $agent = $this->createAgent($session->user_agent);

                    return (object) [
                        'agent' => $agent,
                        'ip_address' => $session->ip_address,
                        'is_current_device' => $session->id === $request->session()->getId(),
                        'last_active' => Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
                    ];
                });
        }

        return view('profile.activity', [
            'user' => $request->user(),
            'sessions' => $sessions,
        ]);
    }

    /**
     * Parse the user agent string into OS and Browser.
     */
    protected function createAgent($userAgent)
    {
        $platform = 'Unknown';
        $browser = 'Unknown';
        $isDesktop = true;

        if (! $userAgent) {
            return (object) compact('platform', 'browser', 'isDesktop');
        }

        // Platform
        if (preg_match('/windows/i', $userAgent)) {
            $platform = 'Windows';
        } elseif (preg_match('/macintosh|mac os x/i', $userAgent)) {
            $platform = 'Mac';
        } elseif (preg_match('/linux/i', $userAgent)) {
            $platform = 'Linux';
        } elseif (preg_match('/android/i', $userAgent)) {
            $platform = 'AndroidOS';
            $isDesktop = false;
        } elseif (preg_match('/iphone|ipad|ipod/i', $userAgent)) {
            $platform = 'iOS';
            $isDesktop = false;
        }

        // Browser
        if (preg_match('/edg/i', $userAgent)) {
            $browser = 'Edge';
        } elseif (preg_match('/opr|opera/i', $userAgent)) {
            $browser = 'Opera';
        } elseif (preg_match('/chrome/i', $userAgent)) {
            $browser = 'Chrome';
        } elseif (preg_match('/safari/i', $userAgent)) {
            $browser = 'Safari';
        } elseif (preg_match('/firefox/i', $userAgent)) {
            $browser = 'Firefox';
        } elseif (preg_match('/msie|trident/i', $userAgent)) {
            $browser = 'IE';
        }

        return (object) compact('platform', 'browser', 'isDesktop');
    }

    /**
     * Log out other browser sessions.
     */
    public function logoutOtherBrowserSessions(Request $request): RedirectResponse
    {
        if (config('session.driver') === 'database') {
            DB::table('sessions')
                ->where('user_id', $request->user()->getAuthIdentifier())
                ->where('id', '!=', $request->session()->getId())
                ->delete();
        }

        return Redirect::route('profile.activity')->with('status', 'sessions-terminated');
    }

    /**
     * Translate a value BACK to Indonesian — used before saving to DB
     * when user is in EN locale, so DB always stores ID values.
     */
    public static function reverseTranslate(string $val, string $colName = ''): string
    {
        if (!$val) return $val;

        // Address term replacement: EN → ID (always — old() may hold EN values from any locale)
        $isAddress = $colName === 'alamat' || str_contains($colName, 'alamat');
        if ($isAddress) {
            $terms = [
                'Regency of'  => 'Kabupaten',
                'City of'     => 'Kota',
                'Province of'  => 'Provinsi',
                'District of'  => 'Kecamatan',
                'Village of'  => 'Kelurahan',
                'RT '         => 'RT',
                ' RW '        => ' RW',
                'Jl. '        => 'Jalan ',
                'No. '        => 'No. ',
                'Building '   => 'Gedung ',
                'Floor '      => 'Lantai ',
            ];
            foreach ($terms as $en => $id) {
                $val = str_replace($en, $id, $val);
            }
        }

        // Reverse-lookup via lang files: EN value → ID value
        // NO locale guard — handles old() values submitted from EN locale
        $authEn = require resource_path('lang/en/auth.php');
        $authId = require resource_path('lang/id/auth.php');

        foreach ($authEn as $key => $enVal) {
            if (!isset($authId[$key])) continue;
            $idVal = $authId[$key];
            if ($idVal === $enVal) continue;

            if ($val === $enVal) return $idVal;
            if (preg_replace('/\s+/', '', $val) === preg_replace('/\s+/', '', $enVal)) return $idVal;
        }

        return $val;
    }

    /**
     * Update the user's profile information.
     * Dynamically handles all fields defined in role_columns.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        try {
            $user = $request->user();
            $data = $request->validated();

            // 1. Update user table fields
            $userData = [
                'name' => $data['name'] ?? $user->name,
                'email' => $data['email'] ?? $user->email,
            ];

            // Update username
            if (isset($data['username'])) {
                $userData['username'] = $data['username'];
            }

            if ($request->hasFile('photo')) {
                if ($user->photo) {
                    Storage::disk('public')->delete($user->photo);
                }
                $userData['photo'] = $request->file('photo')->store('profile-photos', 'public');
            }

            $user->fill($userData);

            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }

            $user->save();

            // 2. Handle profile data dynamically from role_columns
            $role = Role::where('name', $user->role)->first();
            if ($role) {
                $profileColumns = $role->profileColumns();
                $profileColumnNames = $profileColumns->pluck('column_name')->toArray();
                $fileColumns = $profileColumns->filter(fn($c) => $c->column_type === 'blob');

                // Collect only fields that exist in role_columns (skip file fields for now)
                // Reverse-translate text/textarea values from EN→ID so DB always stores ID
                $textTypes = ['varchar', 'char', 'text', 'longtext', 'mediumtext'];
                $profileData = [];
                foreach ($profileColumnNames as $field) {
                    if (in_array($field, ['kartu_identitas'])) continue;
                    if (array_key_exists($field, $data)) {
                        $col = $profileColumns->firstWhere('column_name', $field);
                        $isText = $col && in_array($col->column_type, $textTypes);
                        $profileData[$field] = $isText
                            ? self::reverseTranslate($data[$field], $field)
                            : $data[$field];
                    }
                }

                // Handle blob/file columns: store file to disk and save path string in blob column
                foreach ($fileColumns as $col) {
                    $fieldName = $col->column_name;
                    if ($request->hasFile($fieldName) && $request->file($fieldName)->isValid()) {
                        // Delete old file if exists
                        if ($user->profile && $user->profile->{$fieldName}) {
                            Storage::disk('public')->delete($user->profile->{$fieldName});
                        }
                        $profileData[$fieldName] = $request->file($fieldName)->store('kartu-identitas', 'public');
                    }
                    // If no new file uploaded → do NOT overwrite existing value
                }

                if (!empty($profileData)) {
                    if ($user->profile) {
                        $user->profile->update($profileData);
                    } else {
                        $relation = $role->relation_name;
                        if (method_exists($user, $relation)) {
                            $user->{$relation}()->create($profileData);
                        }
                    }
                }
            }

            return Redirect::route('profile.show')->with('success', 'profile-updated');
        } catch (\Throwable $e) {
            return Redirect::route('profile.edit')
                 ->withInput()
                 ->withErrors(['general' => __('dashboard.profile.profile_update_failed') . ' ' . $e->getMessage()]);
        }
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Send a fresh verification link to the user's email.
     */
    public function sendVerificationNotification(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return Redirect::route('profile.show')
                ->with('status', 'already-verified');
        }

        $user->sendEmailVerificationNotification();

        return Redirect::route('profile.show')
            ->with('status', 'verification-sent');
    }
}
