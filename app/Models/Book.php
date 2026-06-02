<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use \App\Traits\CleansRteMedia;

    protected $fillable = [
        'feature_id',
        'title',
        'title_en',
        'author',
        'dimensions',
        'total_pages',
        'weight',
        'language',
        'publisher',
        'publication_year',
        'isbn',
        'synopsis',
        'description',
        'description_en',
        'cover_image',
        'cover_position',
        'cover_scale',
        'cover_texts',
        'title_position',
        'back_title',
        'back_cover_image',
        'back_cover_position',
        'back_cover_scale',
        'back_title_position',
        'back_cover_texts',
        'thumbnail',
        'pdf_path',
        'order',
    ];

    protected $casts = [
        'cover_position' => 'array',
        'cover_texts' => 'array',
        'title_position' => 'array',
        'back_cover_position' => 'array',
        'back_cover_texts' => 'array',
        'back_title_position' => 'array',
    ];

    protected static function booted()
    {
        static::deleting(function ($book) {
            $disk = \Illuminate\Support\Facades\Storage::disk('public');
            if ($book->cover_image) {
                $disk->delete($book->cover_image);
            }
            if ($book->back_cover_image) {
                $disk->delete($book->back_cover_image);
            }
            if ($book->thumbnail) {
                $disk->delete($book->thumbnail);
            }
            if ($book->pdf_path) {
                $disk->delete($book->pdf_path);
            }
            foreach ($book->pages as $page) {
                $page->delete();
            }
        });
    }

    public function feature()
    {
        return $this->belongsTo(Feature::class);
    }

    public function pages()
    {
        return $this->hasMany(VirtualBookPage::class, 'book_id')->orderBy('order');
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
     * Get the translated description based on current locale.
     * Uses stored description_en if available, otherwise auto-translate via Google Translate.
     */
    public function getTranslatedDescriptionAttribute(): string
    {
        if (app()->getLocale() === 'en') {
            if ($this->description_en) return $this->description_en;
            $desc = $this->description ?? '';
            if (!$desc) return '';
            $key = 'book_desc_' . $this->id . '_' . md5(substr($desc, 0, 50));
            return \App\Services\AutoLangService::ensureKey($key, $desc);
        }
        return $this->description ?? '';
    }
}
