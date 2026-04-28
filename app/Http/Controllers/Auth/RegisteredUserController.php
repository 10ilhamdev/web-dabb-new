<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
        $this->ensureLangEntry("id/auth.php", $key, $this->generateLabel($columnName, 'id'));
        $this->ensureLangEntry("en/auth.php", $key, $this->generateLabel($columnName, 'en'));
        return $this->generateLabel($columnName, 'id');
    }

    /**
     * Translate a placeholder using lang, auto-registering missing keys.
     */
    private function transEnumOption(string $option, string $columnName): string
    {
        // Map enum option values to translation keys
        $transMap = [
            'jenis_keperluan' => [
                'Hanya Daftar Akun' => 'auth.purpose_register_only',
                'Penelitian' => 'auth.purpose_research',
                'Kunjungan' => 'auth.purpose_visit',
            ],
            'jenis_kelamin' => [
                'Laki-Laki' => 'auth.male',
                'Perempuan' => 'auth.female',
            ],
            'agama' => [
                'Islam' => 'religion_islam',
                'Kristen' => 'religion_christian',
                'Katolik' => 'religion_catholic',
                'Hindu' => 'religion_hindu',
                'Buddha' => 'religion_buddha',
                'Konghucu' => 'religion_confucian',
            ],
        ];
        if (isset($transMap[$columnName][$option])) {
            $translated = __($transMap[$columnName][$option]);
            // Only return translated if it actually exists as a key
            if ($translated !== $transMap[$columnName][$option]) {
                return $translated;
            }
        }
        return $option;
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
        $this->ensureLangEntry("id/auth.php", $key, $this->generatePlaceholder($columnName, 'id'));
        $this->ensureLangEntry("en/auth.php", $key, $this->generatePlaceholder($columnName, 'en'));
        return $this->generatePlaceholder($columnName, 'id');
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

    private function ensureLangEntry(string $filePath, string $key, string $value): void
    {
        $fullPath = base_path("resources/lang/{$filePath}");
        if (!file_exists($fullPath)) {
            return;
        }
        $content = file_get_contents($fullPath);
        if (strpos($content, "'{$key}'") !== false) {
            return;
        }
        $insertPos = strrpos($content, "];");
        if ($insertPos === false) {
            return;
        }
        $newEntry = PHP_EOL . "    '{$key}' => '{$value}'," . PHP_EOL;
        $newContent = substr($content, 0, $insertPos) . $newEntry . substr($content, $insertPos);
        file_put_contents($fullPath, $newContent);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Base validation rules
        $rules = [
            'role' => ['required', 'string', Rule::in(Role::registerable()->pluck('name')->toArray())],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ];

        // 2. Dynamic validation based on role_columns
        $role = $request->role;
        $profileColumns = User::roleProfileColumns($role);

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

            // File field (kartu_identitas)
            if ($type === 'blob' || $field === 'kartu_identitas') {
                $rules[$field] = $isNullable
                    ? ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048']
                    : ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'];
                continue;
            }

            // Enum/set fields
            if (in_array($type, ['enum', 'set'])) {
                $options = [];
                if (!empty($col->options)) {
                    $options = $col->options;
                } else {
                    $roleModel = Role::where('name', $role)->first();
                    if ($roleModel) {
                        $options = User::getEnumValues($roleModel->table_name, $field);
                    }
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
        }

        $request->validate($rules);

        // 3. Upload file fields
        $profileData = [];
        $roleModel = Role::where('name', $role)->first();

        if ($roleModel) {
            $fileColumns = $roleModel->profileColumns()->filter(fn($c) => $c->column_type === 'blob');
            foreach ($fileColumns as $col) {
                $fieldName = $col->column_name;
                if ($request->hasFile($fieldName)) {
                    $profileData[$fieldName] = $request->file($fieldName)->store('kartu_identitas', 'public');
                }
            }

            // 4. Collect profile data from role_columns
            $profileColumnNames = $profileColumns->pluck('column_name')->toArray();
            foreach ($profileColumnNames as $field) {
                if (in_array($field, ['name', 'email', 'password', 'photo', 'kartu_identitas'])) {
                    continue;
                }
                if ($request->has($field)) {
                    $profileData[$field] = $request->input($field);
                }
            }
        }

        // 5. Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $role,
            'password' => Hash::make($request->password),
        ]);

        // 6. Create profile dynamically
        if ($roleModel && $roleModel->relation_name) {
            $relation = $roleModel->relation_name;
            if (method_exists($user, $relation)) {
                $user->{$relation}()->create($profileData);
            }
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}