<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class PenggunaController extends Controller
{
    public function index()
    {
        $users = User::orderByDesc('created_at')->get();
        $allRoles = Role::all();

        // Dynamic stats: build per-role counts from DB
        $stats = [
            'total' => $users->count(),
            'verified' => $users->whereNotNull('email_verified_at')->count(),
            'by_role' => [],
        ];
        foreach ($allRoles as $r) {
            $stats['by_role'][$r->name] = [
                'label' => $r->label,
                'badge_color' => $r->badge_color ?? 'gray',
                'count' => $users->where('role', $r->name)->count(),
            ];
        }

        return view('cms.pengguna.page.index', [
            'users' => $users,
            'allRoles' => $allRoles,
            'stats' => $stats,
        ]);
    }

    public function create()
    {
        $allRoles = Role::all();
        // Pre-compute enum options for each role
        $enumOptions = [];
        foreach ($allRoles as $r) {
            $enumOptions[$r->name] = $this->buildRoleEnumOptions($r);
        }

        return view('cms.pengguna.page.create', [
            'allRoles' => $allRoles,
            'profile' => null,
            'enumOptions' => $enumOptions,
        ]);
    }

    public function store(Request $request)
    {
        $role = $request->input('role');
        $roleModel = Role::where('name', $role)->first();

        if (!$roleModel) {
            return back()->withInput()->with('error', 'Role tidak valid.')->withInput();
        }

        // Build dynamic validation
        $rules = $this->buildValidationRules($roleModel, null);

        // Username always required for all roles
        $rules['username'] = ['required', 'string', 'max:255', Rule::unique('users', 'username')];

        $data = $request->validate($rules);

        DB::transaction(function () use ($request, $data, $role, $roleModel) {
            // Handle photo upload
            if ($request->hasFile('photo')) {
                $data['photo'] = $request->file('photo')->store('user_photos', 'public');
            }

            // Handle profile file fields
            $profileColumns = $roleModel->profileColumns();
            $fileColumns = $profileColumns->filter(fn($c) => $c->column_type === 'blob');
            $profileFileData = [];
            foreach ($fileColumns as $col) {
                $fieldName = $col->column_name;
                if ($request->hasFile($fieldName)) {
                    $profileFileData[$fieldName] = $request->file($fieldName)->store('identitas', 'public');
                }
            }

            // Generate username if not provided (for non-registerable roles like pegawai)
            if (empty($data['username'])) {
                $emailUsername = explode('@', $data['email'])[0];
                $baseUsername = preg_replace('/[^a-zA-Z0-9]/', '', $emailUsername);
                $username = $baseUsername;
                $counter = 1;
                while (User::where('username', $username)->exists()) {
                    $username = $baseUsername . $counter;
                    $counter++;
                }
                $data['username'] = $username;
            }

            // Create user
            $userPayload = collect($data)->only(['name', 'username', 'email', 'role', 'photo'])->all();
            $userPayload['password'] = Hash::make($data['password']);

            $user = User::create($userPayload);

            // Create profile dynamically
            $this->syncRoleProfile($user, $roleModel, $data, $profileFileData, false);
        });

        return redirect()
            ->route('cms.pengguna.index')
            ->with('success', __('cms.pengguna.created_successfully'));
    }

    public function edit(User $pengguna)
    {
        $allRoles = Role::all();
        $enumOptions = [];
        foreach ($allRoles as $r) {
            $enumOptions[$r->name] = $this->buildRoleEnumOptions($r);
        }

        // Load profile dynamically based on user's role
        $profile = null;
        $roleModel = $allRoles->firstWhere('name', $pengguna->role);
        if ($roleModel && $roleModel->relation_name) {
            try {
                $profile = $pengguna->{$roleModel->relation_name};
            } catch (\Throwable) {
                $profile = null;
            }
        }

        return view('cms.pengguna.page.edit', [
            'user' => $pengguna,
            'allRoles' => $allRoles,
            'profile' => $profile,
            'enumOptions' => $enumOptions,
        ]);
    }

    public function update(Request $request, User $pengguna)
    {
        $role = $request->input('role');
        $roleModel = Role::where('name', $role)->first();

        if (!$roleModel) {
            return back()->withInput()->with('error', 'Role tidak valid.')->withInput();
        }

        $rules = $this->buildValidationRules($roleModel, $pengguna);

        // Username rules
        $rules['username'] = ['nullable', 'string', 'max:255', Rule::unique('users', 'username')->ignore($pengguna->id)];

        $data = $request->validate($rules);

        DB::transaction(function () use ($request, $data, $role, $roleModel, $pengguna) {
            // Handle photo upload
            if ($request->hasFile('photo')) {
                $data['photo'] = $request->file('photo')->store('user_photos', 'public');
            }

            // Handle profile file fields
            $profileColumns = $roleModel->profileColumns();
            $fileColumns = $profileColumns->filter(fn($c) => $c->column_type === 'blob');
            $profileFileData = [];
            foreach ($fileColumns as $col) {
                $fieldName = $col->column_name;
                if ($request->hasFile($fieldName)) {
                    $profileFileData[$fieldName] = $request->file($fieldName)->store('identitas', 'public');
                }
            }

            // Update user
            $userPayload = collect($data)->only(['name', 'username', 'email', 'role'])->all();

            if ($request->hasFile('photo')) {
                $userPayload['photo'] = $data['photo'];
            }

            if (empty($userPayload['username'])) {
                $userPayload['username'] = $pengguna->username;
            }

            if (!empty($data['password'])) {
                $userPayload['password'] = Hash::make($data['password']);
            }

            $pengguna->update($userPayload);

            // Sync profile
            $this->syncRoleProfile($pengguna, $roleModel, $data, $profileFileData, true);
        });

        return redirect()
            ->route('cms.pengguna.index')
            ->with('success', __('cms.pengguna.updated_successfully'));
    }

    public function destroy(User $pengguna)
    {
        if (Auth::id() === $pengguna->id) {
            return redirect()
                ->route('cms.pengguna.index')
                ->with('error', __('cms.pengguna.cannot_delete_self'));
        }

        $pengguna->delete();

        return redirect()
            ->route('cms.pengguna.index')
            ->with('success', __('cms.pengguna.deleted_successfully'));
    }

    /**
     * Build validation rules from role_columns for a given role.
     */
    private function buildValidationRules(Role $roleModel, ?User $existing): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'string', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($existing?->id),
            ],
            'role' => ['required', Rule::in(Role::pluck('name')->toArray())],
            'password' => $existing
                ? ['nullable', 'confirmed', Password::min(8)]
                : ['required', 'confirmed', Password::min(8)],
            'photo' => ['nullable', 'image', 'max:2048'],
        ];

        $profileColumns = $roleModel->profileColumns();
        $tableName = $roleModel->table_name;
        $profileId = null;
        if ($existing && $roleModel->relation_name) {
            try {
                $profileId = $existing->{$roleModel->relation_name}?->id;
            } catch (\Throwable) {
                $profileId = null;
            }
        }

        foreach ($profileColumns as $col) {
            $field = $col->column_name;
            $type = $col->column_type;
            $length = $col->column_length;
            $isNullable = $col->is_nullable;

            // File field (blob)
            if ($type === 'blob') {
                $rules[$field] = ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'];
                continue;
            }

            // Enum/set field
            if (in_array($type, ['enum', 'set'])) {
                $options = [];
                if (!empty($col->options)) {
                    $options = $col->options;
                } else {
                    $options = User::getEnumValues($tableName, $field);
                }
                $rules[$field] = $isNullable
                    ? ['nullable', Rule::in($options)]
                    : ['required', Rule::in($options)];
                continue;
            }

            // Generate rule based on type
            $rule = match ($type) {
                'varchar', 'char' => $isNullable
                    ? ['nullable', 'string', 'max:' . ($length ?? 255)]
                    : ['required', 'string', 'max:' . ($length ?? 255)],
                'text' => $isNullable ? ['nullable', 'string'] : ['required', 'string'],
                'int', 'bigint', 'smallint', 'tinyint' => $isNullable
                    ? ['nullable', 'integer']
                    : ['required', 'integer'],
                'decimal', 'float', 'double' => $isNullable
                    ? ['nullable', 'numeric']
                    : ['required', 'numeric'],
                'date' => $isNullable
                    ? ['nullable', 'date']
                    : ['required', 'date'],
                'datetime', 'timestamp' => $isNullable
                    ? ['nullable', 'date']
                    : ['required', 'date'],
                default => $isNullable
                    ? ['nullable', 'string']
                    : ['required', 'string'],
            };

            // Unique constraint: dynamically from role_columns is_unique flag
            if ($col->is_unique) {
                $uniqueRule = Rule::unique($tableName, $field);
                if ($profileId) {
                    $uniqueRule->ignore($profileId);
                }
                $rule[] = $uniqueRule;
            }

            $rules[$field] = $rule;
        }

        return $rules;
    }

    /**
     * Build enum/set options for a role from role_columns.
     */
    private function buildRoleEnumOptions(Role $role): array
    {
        $options = [];
        $profileColumns = $role->profileColumns();

        foreach ($profileColumns as $col) {
            if (in_array($col->column_type, ['enum', 'set'])) {
                if (!empty($col->options)) {
                    $options[$col->column_name] = $col->options;
                } else {
                    $options[$col->column_name] = User::getEnumValues($role->table_name, $col->column_name);
                }
            }
        }

        return $options;
    }

    /**
     * Resend email verification link to a user from CMS.
     */
    public function resendVerification(User $pengguna): RedirectResponse
    {
        if ($pengguna->hasVerifiedEmail()) {
            return redirect()
                ->route('cms.pengguna.index')
                ->with('error', __('cms.pengguna.already_verified'));
        }

        $pengguna->sendEmailVerificationNotification();

        return redirect()
            ->route('cms.pengguna.index')
            ->with('success', __('cms.pengguna.verification_sent'));
    }

    /**
     * Manually mark a user's email as verified (admin action).
     */
    public function markVerified(User $pengguna): RedirectResponse
    {
        if ($pengguna->hasVerifiedEmail()) {
            return redirect()
                ->route('cms.pengguna.index')
                ->with('error', __('cms.pengguna.already_verified'));
        }

        $pengguna->email_verified_at = now();
        $pengguna->save();

        return redirect()
            ->route('cms.pengguna.index')
            ->with('success', __('cms.pengguna.marked_verified', ['name' => $pengguna->name]));
    }

    /**
     * Sync role profile - dynamically handles all role-specific profile tables.
     */
    private function syncRoleProfile(User $user, Role $roleModel, array $data, array $fileData, bool $isUpdate): void
    {
        $relation = $roleModel->relation_name;
        if (!$relation) return;

        // Delete other role profiles on update
        if ($isUpdate) {
            $allRoles = Role::whereNotNull('relation_name')
                ->whereNotNull('table_name')
                ->where('name', '!=', $roleModel->name)
                ->get();

            foreach ($allRoles as $otherRole) {
                $otherRelation = $otherRole->relation_name;
                if (method_exists($user, $otherRelation)) {
                    $user->{$otherRelation}()->delete();
                }
            }
        }

        // Collect profile data from role_columns
        $profileColumns = $roleModel->profileColumns();
        $skipFields = ['user_id', 'id', 'created_at', 'updated_at', 'name', 'email', 'password', 'photo'];

        $payload = [];
        foreach ($profileColumns as $col) {
            $field = $col->column_name;
            if (in_array($field, $skipFields)) continue;

            if ($col->column_type === 'blob') {
                if (array_key_exists($field, $fileData)) {
                    $payload[$field] = $fileData[$field];
                } elseif (!$isUpdate) {
                    $payload[$field] = null;
                }
                continue;
            }

            if (array_key_exists($field, $data)) {
                $payload[$field] = $data[$field];
            }
        }

        // Create or update profile
        if (method_exists($user, $relation) || \App\Models\Role::where('relation_name', $relation)->exists()) {
            $user->{$relation}()->updateOrCreate(
                ['user_id' => $user->id],
                $payload
            );
        }
    }
}