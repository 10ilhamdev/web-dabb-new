<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengelolaan extends Model
{
    use \App\Traits\CleansRteMedia;

    protected $fillable = [
        'feature_id',
        'title',
        'title_en',
        'description',
        'description_en',
        'type',
        'published_at',
        'subtitle',
        'subtitle_en',
        'link_text',
        'link_url',
        'images',
        'extra_data',
        'order',
        'is_active',
        'views',
        'shares',
    ];

    protected $casts = [
        'images' => 'array',
        'extra_data' => 'array',
        'is_active' => 'boolean',
        'published_at' => 'date',
        'views' => 'integer',
        'shares' => 'integer',
    ];

    protected static function booted()
    {
        static::deleting(function ($model) {
            $disk = \Illuminate\Support\Facades\Storage::disk('public');
            if ($model->images && is_array($model->images)) {
                foreach ($model->images as $img) {
                    $disk->delete($img);
                }
            }
            if (!empty($model->extra_data['file'])) {
                $disk->delete($model->extra_data['file']);
            }
        });
    }

    public function feature()
    {
        return $this->belongsTo(Feature::class);
    }

    public function getImagesAttribute($value)
    {
        if (is_array($value)) return $value;
        if (empty($value) || $value === 'null') return [];
        $decoded = json_decode($value, true);
        return json_last_error() === JSON_ERROR_NONE ? $decoded : [];
    }

    public function getExtraDataAttribute($value)
    {
        if (is_array($value)) return $value;
        if (empty($value) || $value === 'null') return [];
        $decoded = json_decode($value, true);
        return json_last_error() === JSON_ERROR_NONE ? $decoded : [];
    }
}
