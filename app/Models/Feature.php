<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    use \App\Traits\CleansRteMedia;

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
        'is_login_required',
    ];

    protected $casts = [
        'is_active'         => 'boolean',
        'is_virtual_book'   => 'boolean',
        'is_login_required' => 'boolean',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::saved(function () {
            \Illuminate\Support\Facades\Cache::forget('navFeatures');
        });

        static::deleting(function ($feature) {
            \Illuminate\Support\Facades\Cache::forget('navFeatures');

            $disk = \Illuminate\Support\Facades\Storage::disk('public');
            if ($feature->book_cover) {
                $disk->delete($feature->book_cover);
            }
            if ($feature->book_thumbnail) {
                $disk->delete($feature->book_thumbnail);
            }

            // Hapus file bahasa/terjemahan khusus home_{id}.php jika ada
            $locales = ['id', 'en'];
            foreach ($locales as $locale) {
                $langFile = resource_path("lang/{$locale}/home_{$feature->id}.php");
                if (file_exists($langFile)) {
                    @unlink($langFile);
                }
            }

            // Hapus seluruh sub-fitur / sub-menu secara rekursif (akan memicu event deleting di sub-fitur tersebut)
            foreach ($feature->subfeatures as $sub) {
                $sub->delete();
            }

            // Hapus seluruh data relasi (file fisik akan dihapus otomatis oleh event deleting/deleted di masing-masing model)
            // 1. Pages (FeaturePage)
            foreach ($feature->pages as $page) {
                $page->delete();
            }

            // 2. Slideshow Pages
            foreach ($feature->slideshowPages as $slideshowPage) {
                $slideshowPage->delete();
            }

            // 3. Virtual 3D Rooms
            foreach ($feature->virtual3dRooms as $room) {
                $room->delete();
            }

            // 4. Virtual Rooms (360)
            foreach ($feature->virtualRooms as $vRoom) {
                $vRoom->delete();
            }

            // 5. Publications
            foreach ($feature->publications as $pub) {
                $pub->delete();
            }

            // 6. Books
            foreach ($feature->books as $book) {
                $book->delete();
            }

            // 7. Slideshow Slides
            foreach ($feature->slideshowSlides as $slide) {
                $slide->delete();
            }

            // 8. Profiles
            foreach ($feature->profiles as $profile) {
                $profile->delete();
            }

            // 9. Layanan Publiks
            foreach ($feature->layananPubliks as $layanan) {
                $layanan->delete();
            }

            // 10. Pengelolaans
            foreach ($feature->pengelolaans as $pengelolaan) {
                $pengelolaan->delete();
            }

            // 11. Kontak Kamis
            foreach ($feature->kontakKamis as $kontak) {
                $kontak->delete();
            }
        });

        static::updating(function ($feature) {
            // Jika tipe halaman diubah dari 'home'/'beranda' ke tipe lain, hapus file terjemahan home_{id}.php
            if ($feature->isDirty('page_type') && ($feature->getOriginal('page_type') === 'home' || $feature->getOriginal('page_type') === 'beranda')) {
                $locales = ['id', 'en'];
                foreach ($locales as $locale) {
                    $langFile = resource_path("lang/{$locale}/home_{$feature->id}.php");
                    if (file_exists($langFile)) {
                        @unlink($langFile);
                    }
                }
            }

            // Jika page_type diubah, hapus juga data lama dari page_type sebelumnya beserta file fisiknya (via model delete)
            if ($feature->isDirty('page_type')) {
                $oldType = $feature->getOriginal('page_type');
                
                if ($oldType === '3d') {
                    foreach ($feature->virtual3dRooms as $room) {
                        $room->delete();
                    }
                } elseif ($oldType === 'real') {
                    foreach ($feature->virtualRooms as $vRoom) {
                        $vRoom->delete();
                    }
                } elseif ($oldType === 'publication') {
                    foreach ($feature->publications as $pub) {
                        $pub->delete();
                    }
                } elseif ($oldType === 'book') {
                    foreach ($feature->books as $book) {
                        $book->delete();
                    }
                } elseif ($oldType === 'slideshow') {
                    foreach ($feature->slideshowSlides as $slide) {
                        $slide->delete();
                    }
                    foreach ($feature->slideshowPages as $slideshowPage) {
                        $slideshowPage->delete();
                    }
                } elseif ($oldType === 'onsite') {
                    foreach ($feature->pages as $page) {
                        $page->delete();
                    }
                } elseif ($oldType === 'profile') {
                    foreach ($feature->profiles as $profile) {
                        $profile->delete();
                    }
                } elseif ($oldType === 'layanan_publik') {
                    foreach ($feature->layananPubliks as $layanan) {
                        $layanan->delete();
                    }
                } elseif ($oldType === 'pengelolaan') {
                    foreach ($feature->pengelolaans as $pengelolaan) {
                        $pengelolaan->delete();
                    }
                } elseif ($oldType === 'kontak_kami') {
                    foreach ($feature->kontakKamis as $kontak) {
                        $kontak->delete();
                    }
                }
            }
        });
    }

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

    public function pengelolaans()
    {
        return $this->hasMany(Pengelolaan::class)->orderBy('order');
    }

    public function kontakKamis()
    {
        return $this->hasMany(KontakKami::class)->orderBy('order');
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
