<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use \App\Traits\CleansRteMedia;

    protected $table = 'profiles';

    protected $fillable = [
        'feature_id',
        'title',
        'title_en',
        'description',
        'description_en',
        'type',
        'subtitle',
        'subtitle_en',
        'link_text',
        'link_url',
        'logo_path',
        'chart_data',
        'images',
        'image_positions',
        'order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'images' => 'array',
            'image_positions' => 'array',
            'chart_data' => 'array',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted()
    {
        static::deleting(function ($profile) {
            $disk = \Illuminate\Support\Facades\Storage::disk('public');
            if ($profile->logo_path) {
                $disk->delete($profile->logo_path);
            }
            if ($profile->images && is_array($profile->images)) {
                foreach ($profile->images as $img) {
                    $disk->delete($img);
                }
            }
            foreach ($profile->sections as $section) {
                $section->delete();
            }
        });
    }

    public function feature()
    {
        return $this->belongsTo(Feature::class);
    }

    public function sections()
    {
        return $this->hasMany(ProfileSection::class)->orderBy('order');
    }

    public function getImagesAttribute($value)
    {
        if (is_array($value)) return $value;
        if (empty($value) || $value === 'null') return [];
        $decoded = json_decode($value, true);
        return json_last_error() === JSON_ERROR_NONE ? $decoded : [];
    }

    public function getChartDataAttribute($value)
    {
        if (is_array($value)) return $value;
        if (empty($value) || $value === 'null') return [];
        $decoded = json_decode($value, true);
        return json_last_error() === JSON_ERROR_NONE ? $decoded : [];
    }
}
