<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class PenggunaController extends Controller
{
    /** Map role → [relation method, table] on User model. */
    private const ROLE_PROFILES = [
        'admin' => ['relation' => 'userAdmin', 'table' => 'user_admins'],
        'pegawai' => ['relation' => 'userPegawai', 'table' => 'user_pegawais'],
        'umum' => ['relation' => 'userUmum', 'table' => 'user_umums'],
        'pelajar_mahasiswa' => ['relation' => 'userPelajar', 'table' => 'user_pelajars'],
        'instansi_swasta' => ['relation' => 'userInstansi', 'table' => 'user_instansis'],
    ];

    /** Fields used by each role profile table. */
    private function profileFieldsFor(string $role): array
    {
        return match ($role) {
            'admin', 'pegawai' => [
                'nip', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir',
                'nomor_kartu_identitas', 'alamat', 'nomor_whatsapp',
                'agama', 'jabatan', 'pangkat_golongan',
            ],
            'umum', 'pelajar_mahasiswa' => [
                'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir',
                'nomor_kartu_identitas', 'alamat', 'nomor_whatsapp',
                'jenis_keperluan', 'judul_keperluan',
            ],
            'instansi_swasta' => [
                'tempat_lahir', 'tanggal_lahir',
                'nomor_kartu_identitas', 'alamat', 'nomor_whatsapp',
                'jenis_keperluan', 'judul_keperluan',
            ],
            default => [],
        };
    }

    public function index()
    {
        $users = User::orderByDesc('created_at')->get();
        $roles = User::roleLabels();

        $stats = [
            'total' => $users->count(),
            'admin' => $users->where('role', 'admin')->count(),
            'pegawai' => $users->where('role', 'pegawai')->count(),
            'eksternal' => $users->filter(fn($u) => !in_array($u->role, ['admin', 'pegawai']))->count(),
            'verified' => $users->whereNotNull('email_verified_at')->count(),
        ];

        return view('cms.pengguna.index', compact('users', 'roles', 'stats'));
    }

    public function create()
    {
        return view('cms.pengguna.create', [
            'roles' => User::roleLabels(),
            'profile' => null,
            ...$this->profileOptions(),
        ]);
    }

    public function store(Request $request)
    {
        $role = $request->input('role');

        $data = $request->validate($this->validationRules($role));

        DB::transaction(function () use ($request, $data, $role) {
            if ($request->hasFile('photo')) {
                $data['photo'] = $request->file('photo')->store('user_photos', 'public');
            }

            $kartuPath = null;
            if ($request->hasFile('kartu_identitas_file')) {
                $kartuPath = $request->file('kartu_identitas_file')->store('identitas', 'public');
            }

            $userPayload = collect($data)
                ->only(['name', 'username', 'email', 'role', 'photo'])
                ->all();
            $userPayload['password'] = Hash::make($data['password']);

            $user = User::create($userPayload);

            $this->syncRoleProfile($user, $role, $data, $kartuPath, false);
        });

        return redirect()
            ->route('cms.pengguna.index')
            ->with('success', __('cms.pengguna.created_successfully'));
    }

    public function edit(User $pengguna)
    {
        return view('cms.pengguna.edit', [
            'user' => $pengguna,
            'roles' => User::roleLabels(),
            'profile' => $pengguna->profile,
            ...$this->profileOptions(),
        ]);
    }

    public function update(Request $request, User $pengguna)
    {
        $role = $request->input('role');

        $data = $request->validate($this->validationRules($role, $pengguna));

        DB::transaction(function () use ($request, $data, $role, $pengguna) {
            if ($request->hasFile('photo')) {
                $data['photo'] = $request->file('photo')->store('user_photos', 'public');
            }

            $kartuPath = null;
            if ($request->hasFile('kartu_identitas_file')) {
                $kartuPath = $request->file('kartu_identitas_file')->store('identitas', 'public');
            }

            $userPayload = collect($data)
                ->only(['name', 'username', 'email', 'role', 'photo'])
                ->all();

            if (!empty($data['password'])) {
                $userPayload['password'] = Hash::make($data['password']);
            }

            $pengguna->update($userPayload);

            $this->syncRoleProfile($pengguna, $role, $data, $kartuPath, true);
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

    /** Enum & select options shared by create/edit views. */
    private function profileOptions(): array
    {
        return [
            'jenisKelaminOptions' => User::getEnumValues('user_umums', 'jenis_kelamin'),
            'agamaOptions' => User::getEnumValues('user_admins', 'agama'),
            'jabatanOptions' => User::getEnumValues('user_admins', 'jabatan'),
            'pangkatOptions' => User::getEnumValues('user_admins', 'pangkat_golongan'),
            'jenisKeperluanOptions' => [
                'Hanya Daftar Akun' => __('cms.pengguna.keperluan_register_only'),
                'Penelitian' => __('cms.pengguna.keperluan_research'),
                'Kunjungan' => __('cms.pengguna.keperluan_visit'),
            ],
        ];
    }

    private function validationRules(?string $role, ?User $existing = null): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:255', Rule::unique('users', 'username')->ignore($existing?->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($existing?->id)],
            'role' => ['required', Rule::in(array_keys(User::roleLabels()))],
            'password' => $existing
                ? ['nullable', 'confirmed', Password::min(8)]
                : ['required', 'confirmed', Password::min(8)],
            'photo' => ['nullable', 'image', 'max:2048'],
            'kartu_identitas_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ];

        if (!$role || !isset(self::ROLE_PROFILES[$role])) {
            return $rules;
        }

        $table = self::ROLE_PROFILES[$role]['table'];
        $profileId = $existing?->profile?->id;
        $fields = $this->profileFieldsFor($role);

        $profileRules = [
            'nip' => ['nullable', 'string', 'max:18'],
            'jenis_kelamin' => ['nullable', 'in:Laki-Laki,Perempuan'],
            'tempat_lahir' => ['nullable', 'string', 'max:100'],
            'tanggal_lahir' => ['nullable', 'date'],
            'nomor_kartu_identitas' => [
                'nullable', 'string', 'max:25',
                Rule::unique($table, 'nomor_kartu_identitas')->ignore($profileId),
            ],
            'alamat' => ['nullable', 'string'],
            'nomor_whatsapp' => [
                'nullable', 'string', 'max:20',
                Rule::unique($table, 'nomor_whatsapp')->ignore($profileId),
            ],
            'agama' => ['nullable', 'string', 'max:50'],
            'jabatan' => ['nullable', 'string', 'max:100'],
            'pangkat_golongan' => ['nullable', 'string', 'max:100'],
            'jenis_keperluan' => ['nullable', 'string', 'max:255'],
            'judul_keperluan' => ['nullable', 'string', 'max:255'],
        ];

        foreach ($fields as $field) {
            if (isset($profileRules[$field])) {
                $rules[$field] = $profileRules[$field];
            }
        }

        return $rules;
    }

    /**
     * Persist the role-specific profile record. Removes profiles on other
     * role tables when the user's role changes.
     */
    private function syncRoleProfile(User $user, string $role, array $data, ?string $kartuPath, bool $isUpdate): void
    {
        if (!isset(self::ROLE_PROFILES[$role])) {
            return;
        }

        // Delete profile rows on tables that no longer match the current role.
        if ($isUpdate) {
            foreach (self::ROLE_PROFILES as $otherRole => $meta) {
                if ($otherRole === $role) {
                    continue;
                }
                $user->{$meta['relation']}()->delete();
            }
        }

        $payload = [];
        foreach ($this->profileFieldsFor($role) as $field) {
            if (array_key_exists($field, $data)) {
                $payload[$field] = $data[$field];
            }
        }

        if ($kartuPath) {
            $payload['kartu_identitas'] = $kartuPath;
        }

        $relation = self::ROLE_PROFILES[$role]['relation'];
        $user->{$relation}()->updateOrCreate(
            ['user_id' => $user->id],
            $payload
        );
    }
}
