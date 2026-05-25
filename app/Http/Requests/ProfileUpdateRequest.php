<?php

namespace App\Http\Requests;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     * Rules are dynamically generated from role_columns.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->user();
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user->id),
            ],
            'username' => ['required', 'string', 'max:100', 'unique:users,username,' . $user->id],
            'photo' => ['nullable', 'image', 'max:2048'],
        ];

        // Build dynamic rules from role_columns
        $profileColumns = User::roleProfileColumns($user->role);
        $roleModel = Role::where('name', $user->role)->first();

        foreach ($profileColumns as $col) {
            $field = $col->column_name;
            $type = $col->column_type;
            $length = $col->column_length;
            $isNullable = $col->is_nullable;

            // Skip if rule already exists (e.g., name, email)
            if (isset($rules[$field])) {
                continue;
            }

            // File fields
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
                'boolean' => ['nullable', 'boolean'],
                default => $isNullable
                    ? ['nullable', 'string']
                    : ['required', 'string'],
            };

            if ($col->is_unique && $roleModel) {
                if (in_array($field, ['nomor_whatsapp', 'nomor_telepon'])) {
                    $label = \App\Http\Controllers\ProfileController::colLabel($field, $col->column_label);
                    $rule[] = function ($attribute, $value, $fail) use ($user, $field, $label) {
                        $roles = \App\Models\Role::all();
                        foreach ($roles as $r) {
                            $query = \Illuminate\Support\Facades\DB::table($r->table_name)->where($field, $value);
                            if ($r->name === $user->role && $user->profile) {
                                $query->where('id', '!=', $user->profile->id);
                            }
                            if ($query->exists()) {
                                $fail(__('validation.unique', ['attribute' => $label]));
                                return;
                            }
                        }
                    };
                } else {
                    $profileId = $user->profile?->id;
                    $uniqueRule = Rule::unique($roleModel->table_name, $field);
                    if ($profileId) {
                        $uniqueRule->ignore($profileId);
                    }
                    $rule[] = $uniqueRule;
                }
            }

            $rules[$field] = $rule;
        }

        return $rules;
    }

    /**
     * Get custom attribute names for error messages.
     */
    public function attributes(): array
    {
        $user = $this->user();
        $attrs = [];

        $profileColumns = User::roleProfileColumns($user->role);
        foreach ($profileColumns as $col) {
            $attrs[$col->column_name] = $col->column_label ?? $col->column_name;
        }

        return $attrs;
    }
}