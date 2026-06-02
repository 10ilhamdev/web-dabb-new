<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VirtualBookPage extends Model
{
    use \App\Traits\CleansRteMedia;

    protected $fillable = [
        'feature_id',
        'book_id',
        'title',
        'title_en',
        'content',
        'content_en',
        'image',
        'images',
        'image_height',
        'image_fit_mode',
        'image_positions',
        'text_position',
        'thumbnail',
        'page_number',
        'is_cover',
        'is_back_cover',
        'order',
    ];

    protected $casts = [
        'is_cover' => 'boolean',
        'is_back_cover' => 'boolean',
        'images' => 'array',
        'image_positions' => 'array',
        'text_position' => 'array',
    ];

    protected static function booted()
    {
        static::deleting(function ($page) {
            $disk = \Illuminate\Support\Facades\Storage::disk('public');
            if ($page->thumbnail) {
                $disk->delete($page->thumbnail);
            }
            if ($page->images && is_array($page->images)) {
                foreach ($page->images as $img) {
                    $disk->delete($img);
                }
            }
            if ($page->image) {
                $disk->delete($page->image);
            }
        });
    }

    public function feature()
    {
        return $this->belongsTo(Feature::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Get images array for display - handles both old single image and new images array
     */
    public function getPageImagesAttribute()
    {
        // Get the raw images value from database
        $imagesFromDb = $this->attributes['images'] ?? null;

        // If images array exists in DB and is not empty, decode and use it
        if ($imagesFromDb) {
            $decoded = is_array($imagesFromDb) ? $imagesFromDb : json_decode($imagesFromDb, true);
            if ($decoded && is_array($decoded) && count($decoded) > 0) {
                return $decoded;
            }
        }

        // Backward compatibility: if old single image exists, convert to array
        if (isset($this->attributes['image']) && $this->attributes['image']) {
            return [$this->attributes['image']];
        }

        return [];
    }

    /**
     * Get first image for backward compatibility
     */
    public function getImageAttribute($value)
    {
        $images = $this->page_images;
        return $images[0] ?? null;
    }

    /**
     * Get the translated title based on current locale.
     * Uses AutoLangService to auto-register and translate via Google Translate.
     */
    public function getTranslatedTitleAttribute(): string
    {
        $title = $this->title ?? '';
        if (!$title) return $title;

        if (app()->getLocale() === 'en') {
            $key = preg_replace('/[^a-zA-Z0-9]+/', '_', trim($title));
            return \App\Services\AutoLangService::ensureKey($key, $title);
        }
        return $title;
    }

    /**
     * Get the translated content based on current locale.
     * Uses AutoLangService to auto-register and translate via Google Translate.
     */
    public function getTranslatedContentAttribute(): string
    {
        $content = $this->content ?? '';
        if (!$content) return $content;

        if (app()->getLocale() === 'en') {
            // For long content, use Google Translate directly
            try {
                $ts = app(\App\Services\TranslationService::class);
                $translated = $ts->translate($content);
                if ($translated !== $content && strpos($translated, 'TODO') === false) {
                    return $translated;
                }
            } catch (\Throwable $e) {}
        }
        return $content;
    }
}
