<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Services\AutoLangService;
use App\Services\LangSyncService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        // Pass dynamic role profile columns for each registerable role (from DB)
        $registerableRoles = Role::registerable()->get();
        $rolesData = [];

        foreach ($registerableRoles as $roleModel) {
            $profileColumns = $roleModel->profileColumns();
            $enumOptions = [];

            foreach ($profileColumns as $col) {
                if (in_array($col->column_type, ['enum', 'set'])) {
                    $enumOptions[$col->column_name] = !empty($col->options)
                        ? $col->options
                        : User::getEnumValues($roleModel->table_name, $col->column_name);
                }
            }

            $rolesData[$roleModel->name] = [
                'label' => $roleModel->label,
                'columns' => $profileColumns,
                'enumOptions' => $enumOptions,
            ];
        }

        return view('auth.register', [
            'rolesData' => $rolesData,
            'colLabel' => fn(string $name) => $this->transCol($name),
            'colPlaceholder' => fn(string $name) => $this->transPlaceholder($name),
            'colEnumOption' => fn(string $option, string $column) => $this->transEnumOption($option, $column),
        ]);
    }

    /**
     * Translate a column name using lang, auto-registering missing keys.
     */
    private function transCol(string $columnName): string
    {
        $key = "auth.col_{$columnName}";
        $translated = __($key);
        if ($translated !== $key) {
            return $translated;
        }
        $idLabel = $this->generateLabel($columnName, 'id');
        $enLabel = $this->generateLabel($columnName, 'en');
        AutoLangService::ensureKey("col_{$columnName}", $idLabel);
        return $idLabel;
    }

    /**
     * Translate an enum option value using LangSyncService.
     * Auto-registers missing keys in both id/en lang files.
     */
    private function transEnumOption(string $option, string $columnName): string
    {
        // Use LangSyncService — it auto-registers missing keys via AutoLangService
        $translated = LangSyncService::translateEnum($columnName, $option);
        return $translated !== $option ? $translated : $option;
    }

    /**
     * Translate a placeholder using lang, auto-registering missing keys.
     */
    private function transPlaceholder(string $columnName): string
    {
        $key = "auth.placeholder_{$columnName}";
        $translated = __($key);
        if ($translated !== $key) {
            return $translated;
        }
        $idPlaceholder = $this->generatePlaceholder($columnName, 'id');
        AutoLangService::ensureKey("placeholder_{$columnName}", $idPlaceholder);
        return $idPlaceholder;
    }

    private function generateLabel(string $columnName, string $locale = 'id'): string
    {
        $labels = [
            'nomor_kartu_identitas' => ['id' => 'Nomor Kartu Identitas', 'en' => 'Identity Card Number'],
            'alamat' => ['id' => 'Alamat', 'en' => 'Address'],
            'nomor_whatsapp' => ['id' => 'Nomor WhatsApp', 'en' => 'WhatsApp Number'],
            'tempat_lahir' => ['id' => 'Tempat Lahir', 'en' => 'Place of Birth'],
            'tanggal_lahir' => ['id' => 'Tanggal Lahir', 'en' => 'Date of Birth'],
            'jenis_kelamin' => ['id' => 'Jenis Kelamin', 'en' => 'Gender'],
            'agama' => ['id' => 'Agama', 'en' => 'Religion'],
            'jabatan' => ['id' => 'Jabatan', 'en' => 'Position'],
            'pangkat_golongan' => ['id' => 'Pangkat / Golongan', 'en' => 'Rank / Class'],
            'jenis_keperluan' => ['id' => 'Jenis Keperluan', 'en' => 'Purpose Type'],
            'judul_keperluan' => ['id' => 'Judul Keperluan', 'en' => 'Purpose Title'],
            'nama_instansi' => ['id' => 'Nama Instansi', 'en' => 'Institution Name'],
            'nip' => ['id' => 'NIP', 'en' => 'Employee ID (NIP)'],
            'kartu_identitas' => ['id' => 'Kartu Identitas', 'en' => 'Identity Card'],
            'username' => ['id' => 'Username', 'en' => 'Username'],
            'name' => ['id' => 'Nama Lengkap', 'en' => 'Full Name'],
        ];
        if (isset($labels[$columnName])) {
            return $labels[$columnName][$locale] ?? $labels[$columnName]['id'];
        }
        return ucwords(str_replace('_', ' ', $columnName));
    }

    private function generatePlaceholder(string $columnName, string $locale = 'id'): string
    {
        $placeholders = [
            'email' => ['id' => 'Contoh: nama@gmail.com', 'en' => 'Example: name@gmail.com'],
            'username' => ['id' => 'Masukkan username', 'en' => 'Enter username'],
            'nomor_kartu_identitas' => ['id' => 'Contoh: 1234567890123456', 'en' => 'Example: 1234567890123456'],
            'alamat' => ['id' => 'Masukkan alamat lengkap', 'en' => 'Enter full address'],
            'nomor_whatsapp' => ['id' => 'Contoh: 081234567890', 'en' => 'Example: 081234567890'],
            'tempat_lahir' => ['id' => 'Contoh: Bandung', 'en' => 'Example: Bandung'],
            'jenis_keperluan' => ['id' => 'Pilih jenis keperluan', 'en' => 'Select purpose type'],
            'judul_keperluan' => ['id' => 'Masukkan judul keperluan', 'en' => 'Enter purpose title'],
            'nama_instansi' => ['id' => 'Masukkan nama instansi', 'en' => 'Enter institution name'],
            'nip' => ['id' => 'Masukkan NIP', 'en' => 'Enter Employee ID'],
        ];
        if (isset($placeholders[$columnName])) {
            return $placeholders[$columnName][$locale] ?? $placeholders[$columnName]['id'];
        }
        return $this->generateLabel($columnName, $locale);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $role = $request->role;
        $profileColumns = User::roleProfileColumns($role);
        $profileColumnNames = $profileColumns->pluck('column_name')->toArray();
        $roleModel = Role::where('name', $role)->first();

        // 1. Base validation rules
        $rules = [
            'role' => ['required', 'string', Rule::in(Role::registerable()->pluck('name')->toArray())],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'username' => ['required', 'string', 'max:100', 'unique:users,username'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ];

        foreach ($profileColumns as $col) {
            $field = $col->column_name;
            $type = $col->column_type;
            $length = $col->column_length;
            $isNullable = $col->is_nullable;

            // Skip user table fields (handled above)
            if (in_array($field, ['name', 'email', 'password', 'photo'])) {
                if ($field === 'name') {
                    $rules['name'] = ['required', 'string', 'max:255'];
                }
                continue;
            }

            // File field (blob — kartu_identitas): always nullable, not required
            if ($type === 'blob' || $field === 'kartu_identitas') {
                $rules[$field] = ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'];
                continue;
            }

            // Enum/set fields
            if (in_array($type, ['enum', 'set'])) {
                $options = [];
                if (!empty($col->options)) {
                    $options = $col->options;
                } elseif ($roleModel) {
                    $options = User::getEnumValues($roleModel->table_name, $field);
                }
                $rules[$field] = $isNullable
                    ? ['nullable', Rule::in($options)]
                    : ['required', Rule::in($options)];
                continue;
            }

            // Generate rule based on column type
            $rules[$field] = match ($type) {
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

            if ($col->is_unique) {
                if (!is_array($rules[$field])) {
                    $rules[$field] = [$rules[$field]];
                }

                if (in_array($field, ['nomor_whatsapp', 'nomor_telepon'])) {
                    $label = $this->transCol($field);
                    $rules[$field][] = function ($attribute, $value, $fail) use ($field, $label) {
                        $roles = \App\Models\Role::all();
                        foreach ($roles as $r) {
                            if (\Illuminate\Support\Facades\DB::table($r->table_name)->where($field, $value)->exists()) {
                                $fail(__('validation.unique', ['attribute' => $label]));
                                return;
                            }
                        }
                    };
                } else if ($roleModel) {
                    $rules[$field][] = Rule::unique($roleModel->table_name, $field);
                }
            }
        }

        $request->validate($rules);

        // 2. Build profile data
        $profileData = [];

        // 2a. Handle blob/file columns: store file to disk and save path string in blob column
        $fileColumns = $profileColumns->filter(fn($c) => $c->column_type === 'blob');
        foreach ($fileColumns as $col) {
            $fieldName = $col->column_name;
            if ($request->hasFile($fieldName) && $request->file($fieldName)->isValid()) {
                $profileData[$fieldName] = $request->file($fieldName)->store('kartu-identitas', 'public');
            }
            // If no file uploaded and column is nullable → skip (null is fine)
        }

        // 2b. Collect all non-user-table, non-file fields from profile columns
        $skipFields = ['name', 'email', 'username', 'password', 'password_confirmation',
                      'photo', 'role', '_token'];
        foreach ($profileColumnNames as $field) {
            if (in_array($field, $skipFields)) continue;
            if ($fileColumns->contains('column_name', $field)) continue;
            if ($request->filled($field)) {
                $profileData[$field] = $request->input($field);
            }
        }

        // 3. Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'role' => $role,
            'password' => Hash::make($request->password),
        ]);

        // 4. Create profile dynamically
        if ($roleModel && $roleModel->relation_name) {
            $relation = $roleModel->relation_name;
            if (method_exists($user, $relation)) {
                $user->{$relation}()->create($profileData);
            }
        }

        event(new Registered($user));

        Auth::login($user);

        // Redirect to role-specific dashboard dynamically
        $slug = Str::slug($role, '-');

        return redirect()->route('dashboard.role', ['slug' => $slug]);
    }
}