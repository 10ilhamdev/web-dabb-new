<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VirtualSlideshowSlide extends Model
{
    use \App\Traits\CleansRteMedia;

    protected $fillable = [
        'feature_id',
        'feature_page_id',
        'slide_type',
        'title',
        'title_en',
        'subtitle',
        'subtitle_en',
        'description',
        'description_en',
        'images',
        'image_urls',
        'video_url',
        'video_file',
        'carousel_video_urls',
        'layout',
        'bg_color',
        'info_popup',
        'order',
    ];

    protected $casts = [
        'images'     => 'array',
        'image_urls' => 'array',
        'carousel_video_urls' => 'array',
        'info_popup' => 'array',
    ];

    protected static function booted()
    {
        static::deleting(function ($slide) {
            $disk = \Illuminate\Support\Facades\Storage::disk('public');
            if ($slide->images && is_array($slide->images)) {
                foreach ($slide->images as $img) {
                    $disk->delete($img);
                }
            }
            if ($slide->video_file) {
                if (is_string($slide->video_file) && str_starts_with($slide->video_file, '[')) {
                    $decoded = json_decode($slide->video_file, true);
                    if (is_array($decoded)) {
                        foreach ($decoded as $oldFile) {
                            $disk->delete($oldFile);
                        }
                    }
                } else {
                    $disk->delete($slide->video_file);
                }
            }
        });
    }

    public function getImagesAttribute($value)
    {
        if (is_array($value)) return $value;
        if (is_null($value) || $value === '' || $value === 'null') return [];
        $decoded = json_decode($value, true);
        return json_last_error() === JSON_ERROR_NONE ? $decoded : [];
    }

    public function getImageUrlsAttribute($value)
    {
        if (is_array($value)) return $value;
        if (is_null($value) || $value === '' || $value === 'null') return [];
        $decoded = json_decode($value, true);
        return json_last_error() === JSON_ERROR_NONE ? $decoded : [];
    }

    public function getCarouselVideoUrlsAttribute($value)
    {
        if (is_array($value)) return $value;
        if (is_null($value) || $value === '' || $value === 'null') return [];
        $decoded = json_decode($value, true);
        return json_last_error() === JSON_ERROR_NONE ? $decoded : [];
    }

    public function getInfoPopupAttribute($value)
    {
        if (is_array($value)) return $value;
        if (is_null($value) || $value === '' || $value === 'null') return [];
        $decoded = json_decode($value, true);
        return json_last_error() === JSON_ERROR_NONE ? $decoded : [];
    }

    public function feature()
    {
        return $this->belongsTo(Feature::class);
    }

    public function featurePage()
    {
        return $this->belongsTo(VirtualSlideshowPage::class);
    }

    public function slideshowPage()
    {
        return $this->belongsTo(VirtualSlideshowPage::class, 'feature_page_id');
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
     * Get the translated subtitle based on current locale.
     * Uses stored subtitle_en if available, otherwise auto-translate via Google Translate.
     */
    public function getTranslatedSubtitleAttribute(): string
    {
        if (app()->getLocale() === 'en') {
            if ($this->subtitle_en) return $this->subtitle_en;
            $subtitle = $this->subtitle ?? '';
            if ($subtitle) {
                $key = preg_replace('/[^a-zA-Z0-9]+/', '_', trim($subtitle));
                return \App\Services\AutoLangService::ensureKey($key, $subtitle);
            }
        }
        return $this->subtitle ?? '';
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

    /**
     * Get translated info_popup — recursively translate every text string.
     * Handles: single caption strings, Q&A item strings.
     */
    public function getTranslatedInfoPopupAttribute(): array
    {
        $popup = $this->info_popup ?? [];
        if (app()->getLocale() !== 'en' || empty($popup)) {
            return $popup;
        }

        return $this->translatePopupRecursive($popup);
    }

    private function translatePopupRecursive(mixed $value): mixed
    {
        if (is_array($value)) {
            $result = [];
            foreach ($value as $k => $v) {
                // Skip structural keys (unified_image_order, carousel_video_order, etc.)
                if (in_array($k, ['unified_image_order', 'carousel_video_order'], true)) {
                    $result[$k] = $v;
                } else {
                    $result[$k] = $this->translatePopupRecursive($v);
                }
            }
            return $result;
        }

        if (is_string($value) && trim($value) !== '') {
            $key = preg_replace('/[^a-zA-Z0-9]+/', '_', trim($value));
            return \App\Services\AutoLangService::ensureKey($key, $value);
        }

        return $value;
    }
}
