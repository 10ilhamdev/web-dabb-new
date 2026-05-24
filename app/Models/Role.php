<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'name',
        'label',
        'table_name',
        'relation_name',
        'is_system',
        'is_registerable',
        'badge_color',
        'description',
        'dashboard_route',
        'dashboard_view',
    ];

    protected $casts = [
        'is_system' => 'boolean',
        'is_registerable' => 'boolean',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'role', 'name');
    }

    public function columns()
    {
        return $this->hasMany(RoleColumn::class)->orderBy('sort_order');
    }

    public function permissions()
    {
        return $this->hasMany(RolePermission::class);
    }

    public function hasPermission(string $menuKey): bool
    {
        
        $permission = $this->permissions()->where('menu_key', $menuKey)->first();
        return $permission ? $permission->can_access : false;
    }

    public static function roleLabels(): array
    {
        return self::pluck('label', 'name')->toArray();
    }

    /**
     * Get profile columns for this role from role_columns.
     * Excludes technical columns: user_id, id, timestamps.
     */
    public function profileColumns(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->columns()
            ->whereNotIn('column_name', ['user_id', 'id', 'created_at', 'updated_at'])
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Get profile columns for a given role name — static helper.
     */
    public static function profileColumnsFor(string $roleName): \Illuminate\Database\Eloquent\Collection
    {
        $role = self::where('name', $roleName)->first();
        return $role ? $role->profileColumns() : new \Illuminate\Database\Eloquent\Collection;
    }

    /**
     * Build form field metadata from profile columns.
     * Returns array keyed by column_name with type, label, options, etc.
     */
    public static function profileFieldMeta(string $roleName): array
    {
        $columns = self::profileColumnsFor($roleName);
        $meta = [];

        foreach ($columns as $col) {
            $meta[$col->column_name] = [
                'label' => $col->column_label ?? ucwords(str_replace('_', ' ', $col->column_name)),
                'type' => $col->column_type,
                'length' => $col->column_length,
                'options' => $col->options,
                'is_nullable' => $col->is_nullable,
            ];
        }

        return $meta;
    }

    /**
     * Scope: only roles that users can register as.
     */
    public function scopeRegisterable($query)
    {
        return $query->where('is_registerable', true);
    }

    public function canBeDeleted(): bool
    {
        return ! $this->is_system;
    }

    /**
     * Get the localised label for this role.
     * Falls back to $this->label if no translation key exists.
     */
    public function i18nLabel(): string
    {
        $key = "cms.roles.labels.{$this->name}";
        $translated = __($key);
        // If translation returns the key itself (no match), fall back to DB label
        return $translated === $key ? $this->label : $translated;
    }
}
