<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfileSection extends Model
{
    use \App\Traits\CleansRteMedia;

    protected $table = 'profile_sections';

    protected $fillable = [
        'profile_id',
        'title',
        'title_en',
        'description',
        'description_en',
        'images',
        'image_positions',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'images' => 'array',
            'image_positions' => 'array',
        ];
    }

    protected static function booted()
    {
        static::deleting(function ($section) {
            $disk = \Illuminate\Support\Facades\Storage::disk('public');
            if ($section->images && is_array($section->images)) {
                foreach ($section->images as $img) {
                    $disk->delete($img);
                }
            }
        });
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    public function getImagesAttribute($value)
    {
        if (is_array($value)) return $value;
        if (empty($value) || $value === 'null') return [];
        $decoded = json_decode($value, true);
        return json_last_error() === JSON_ERROR_NONE ? $decoded : [];
    }

    public function getImagePositionsAttribute($value)
    {
        if (is_array($value)) return $value;
        if (empty($value) || $value === 'null') return [];
        $decoded = json_decode($value, true);
        return json_last_error() === JSON_ERROR_NONE ? $decoded : [];
    }
}
