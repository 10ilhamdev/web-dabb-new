<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleColumn extends Model
{
    protected $fillable = [
        'role_id',
        'column_name',
        'column_type',
        'column_label',
        'column_length',
        'is_nullable',
        'is_unique',
        'is_primary',
        'is_foreign',
        'references_table',
        'references_column',
        'on_delete',
        'on_update',
        'is_unsigned',
        'is_auto_increment',
        'default_value',
        'options',
        'sort_order',
    ];

    protected $casts = [
        'is_nullable' => 'boolean',
        'is_unique' => 'boolean',
        'is_primary' => 'boolean',
        'is_foreign' => 'boolean',
        'is_unsigned' => 'boolean',
        'is_auto_increment' => 'boolean',
        'options' => 'array',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
