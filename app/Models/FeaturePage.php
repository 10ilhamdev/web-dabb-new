<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeaturePage extends Model
{
    use \App\Traits\CleansRteMedia;

    protected $fillable = [
        'feature_id',
        'title',
        'title_en',
        'description',
        'description_en',
        'order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    protected static function booted()
    {
        static::deleting(function ($page) {
            foreach ($page->sections as $section) {
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
        return $this->hasMany(FeaturePageSection::class)->orderBy('order');
    }

}
