<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Virtual3dMedia extends Model
{
    protected $table = 'virtual3d_media';

    protected $fillable = [
        'virtual3d_room_id',
        'wall',
        'type',
        'file_path',
        'title',
        'description',
        'position_x',
        'position_y',
        'width',
        'height',
    ];
    protected $casts = [
        'description' => 'array',
    ];

    public function getDescriptionAttribute($value)
    {
        if (is_array($value)) return $value;
        if (is_null($value) || $value === '' || $value === 'null') return '';
        $decoded = json_decode($value, true);
        return json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
    }

    public function room()
    {
        return $this->belongsTo(Virtual3dRoom::class, 'virtual3d_room_id');
    }
}
