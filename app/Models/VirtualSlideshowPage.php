<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VirtualSlideshowPage extends Model
{
    use \App\Traits\CleansRteMedia;

    protected $table = 'virtual_slideshow_pages';

    protected $fillable = [
        'feature_id',
        'title',
        'title_en',
        'description',
        'description_en',
        'order',
        'thumbnail_path',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        static::deleting(function ($page) {
            if ($page->thumbnail_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($page->thumbnail_path);
            }
            foreach ($page->slideshowSlides as $slide) {
                $slide->delete();
            }
        });
    }

    public function feature()
    {
        return $this->belongsTo(Feature::class);
    }

    public function slideshowSlides()
    {
        return $this->hasMany(VirtualSlideshowSlide::class, 'feature_page_id')->orderBy('order');
    }

    /**
     * Get the translated title based on current locale.
     * Uses stored title_en if available, otherwise auto-translate via Google Translate.
     */
    public function getTranslatedTitleAttribute(): string
    {
        if (app()->getLocale() === 'en') {
            if ($this->title_en) return $this->title_en;
            $title = $this->title ?? '';
            if ($title) {
                $key = preg_replace('/[^a-zA-Z0-9]+/', '_', trim($title));
                return \App\Services\AutoLangService::ensureKey($key, $title);
            }
        }
        return $this->title ?? '';
    }

    /**
     * Get the translated description based on current locale.
     * Uses stored description_en if available, otherwise auto-translate via Google Translate.
     */
    public function getTranslatedDescriptionAttribute(): string
    {
        if (app()->getLocale() === 'en') {
            if ($this->description_en) return $this->description_en;
            $desc = $this->description ?? '';
            if ($desc) {
                $key = preg_replace('/[^a-zA-Z0-9]+/', '_', trim($desc));
                return \App\Services\AutoLangService::ensureKey($key, $desc);
            }
        }
        return $this->description ?? '';
    }
}
