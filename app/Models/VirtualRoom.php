<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VirtualRoom extends Model
{
    use \App\Traits\CleansRteMedia;

    protected $fillable = [
        'feature_id',
        'name',
        'description',
        'thumbnail_path',
        'image_360_path',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        static::deleting(function ($room) {
            if ($room->thumbnail_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($room->thumbnail_path);
            }
            if ($room->image_360_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($room->image_360_path);
            }
        });
    }

    public function feature()
    {
        return $this->belongsTo(Feature::class);
    }

    public function hotspots()
    {
        return $this->hasMany(VirtualHotspot::class, 'virtual_room_id');
    }

    /**
     * Get the translated description based on current locale.
     * Uses AutoLangService to auto-register and translate via Google Translate.
     */
    public function getTranslatedDescriptionAttribute(): string
    {
        $desc = $this->description ?? '';
        if (!$desc) return $desc;

        if (app()->getLocale() === 'en') {
            $key = preg_replace('/[^a-zA-Z0-9]+/', '_', trim($desc));
            return \App\Services\AutoLangService::ensureKey($key, $desc);
        }
        return $desc;
    }

    /**
     * Get the translated name based on current locale.
     * Uses stored name_en if available, otherwise auto-translate via transRoomName.
     */
    public function getTranslatedNameAttribute(): string
    {
        if (app()->getLocale() === 'en') {
            if ($this->name_en) return $this->name_en;
            $name = $this->name;
            if ($name) {
                $translated = transRoomName($name);
                if ($translated !== $name) return $translated;
            }
        }
        return $this->name;
    }
}
