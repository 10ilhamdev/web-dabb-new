<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Publication extends Model
{
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
        'link_url',
        'images',
        'extra_data',
        'order',
        'is_active',
    ];

    protected $casts = [
        'images' => 'array',
        'extra_data' => 'array',
        'is_active' => 'boolean',
        'published_at' => 'date',
    ];

    public function feature()
    {
        return $this->belongsTo(Feature::class);
    }
}
