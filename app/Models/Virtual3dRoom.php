<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Virtual3dRoom extends Model
{
    protected $table = 'virtual3d_rooms';

    protected $fillable = [
        'feature_id',
        'name',
        'description',
        'thumbnail_path',
        'wall_color',
        'floor_color',
        'ceiling_color',
        'diameter_front',
        'diameter_back',
        'diameter_left',
        'diameter_right',
        'doors',
        'door_link_type',
        'door_wall',
        'door_target',
        'door_label',
    ];

    protected $casts = [
        'doors' => 'array',
    ];

    public function feature()
    {
        return $this->belongsTo(Feature::class);
    }

    public function media()
    {
        return $this->hasMany(Virtual3dMedia::class, 'virtual3d_room_id');
    }

    /**
     * Get translated door label for a specific wall.
     * Used by blade to build per-wall label maps in the JSON payload.
     */
    public function getTranslatedDoorLabelForWall(string $wall): string
    {
        $locale = app()->getLocale();
        $raw = $this->doors[$wall]['label'] ?? null;

        if (!$raw) {
            return '';
        }

        if ($locale === 'id') {
            return $raw;
        }

        return transRoomName($raw);
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
     * Uses transRoomName() helper which auto-registers missing keys
     * in both id/auth.php and en/auth.php via AutoLangService.
     */
    public function getTranslatedNameAttribute(): string
    {
        if (app()->getLocale() === 'en' && $this->name_en) {
            return $this->name_en;
        }

        $name = $this->name;

        if (app()->getLocale() === 'en') {
            $translated = transRoomName($name);
            if ($translated !== $name) {
                return $translated;
            }
        }

        return $name;
    }

    /**
     * Get the translated door label (back wall only — legacy compatibility).
     * @deprecated For per-wall labels use getTranslatedDoorLabelForWall($wall)
     */
    public function getTranslatedDoorLabelAttribute(): string
    {
        return $this->getTranslatedDoorLabelForWall('back');
    }
}
