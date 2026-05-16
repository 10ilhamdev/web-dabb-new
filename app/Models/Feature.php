<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    protected $fillable = [
        'name',
        'name_en',
        'type',
        'parent_id',
        'path',
        'order',
        'content',
        'content_en',
        'is_virtual_book',
        'book_cover',
        'book_thumbnail',
        'virtual_room_type',
        'page_type',
        'is_active',
    ];

    public function parent()
    {
        return $this->belongsTo(Feature::class, 'parent_id');
    }

    public function subfeatures()
    {
        return $this->hasMany(Feature::class, 'parent_id')->orderBy('order');
    }

    public function pages()
    {
        return $this->hasMany(FeaturePage::class)->orderBy('order');
    }

    public function profiles()
    {
        return $this->hasMany(Profile::class)->orderBy('order');
    }

    public function publications()
    {
        return $this->hasMany(Publication::class)->orderBy('order');
    }

    public function layananPubliks()
    {
        return $this->hasMany(LayananPublik::class)->orderBy('order');
    }

    public function virtualRooms()
    {
        return $this->hasMany(VirtualRoom::class);
    }

    public function virtual3dRooms()
    {
        return $this->hasMany(Virtual3dRoom::class);
    }

    public function virtualBookPages()
    {
        return $this->hasMany(VirtualBookPage::class)->orderBy('order');
    }

    public function books()
    {
        return $this->hasMany(Book::class)->orderBy('order');
    }

    public function slideshowSlides()
    {
        return $this->hasMany(VirtualSlideshowSlide::class)->orderBy('order');
    }

    public function slideshowPages()
    {
        return $this->hasMany(VirtualSlideshowPage::class)->orderBy('order');
    }

    public function allSlideshowSlides()
    {
        return $this->hasMany(VirtualSlideshowSlide::class, 'feature_id')->orderBy('order');
    }

    /**
     * Get the translated name based on current locale.
     */
    public function getTranslatedNameAttribute(): string
    {
        if (app()->getLocale() === 'en' && $this->name_en) {
            return $this->name_en;
        }
        return $this->name;
    }

    /**
     * Get the translated parent name based on current locale.
     */
    public function getTranslatedParentNameAttribute(): ?string
    {
        $parent = $this->parent;
        if (!$parent) return null;
        if (app()->getLocale() === 'en' && $parent->name_en) {
            return $parent->name_en;
        }
        return $parent->name;
    }
}
