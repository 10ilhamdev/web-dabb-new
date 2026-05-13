<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeaturePage extends Model
{
    protected $fillable = [
        'feature_id',
        'title',
        'title_en',
        'description',
        'description_en',
        'order',
        'images',
        'image_positions',
        // Profile-specific fields
        'type',
        'subtitle',
        'subtitle_en',
        'link_text',
        'link_url',
        'logo_path',
        'chart_data',
        'extra_data',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'images' => 'array',
            'image_positions' => 'array',
            'chart_data' => 'array',
            'extra_data' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function feature()
    {
        return $this->belongsTo(Feature::class);
    }

    public function sections()
    {
        return $this->hasMany(FeaturePageSection::class)->orderBy('order');
    }

    public function slideshowSlides()
    {
        return $this->hasMany(VirtualSlideshowSlide::class)->orderBy('order');
    }
}
