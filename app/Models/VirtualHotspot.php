<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\VirtualRoom;

class VirtualHotspot extends Model
{
    protected $fillable = [
        'virtual_room_id',
        'target_room_id',
        'type',
        'yaw',
        'pitch',
        'text_tooltip',
    ];

    public function room()
    {
        return $this->belongsTo(VirtualRoom::class, 'virtual_room_id');
    }

    public function targetRoom()
    {
        return $this->belongsTo(VirtualRoom::class, 'target_room_id');
    }

    /**
     * Get the translated tooltip text based on current locale.
     * Uses AutoLangService to auto-register and translate via Google Translate.
     */
    public function getTranslatedTextTooltipAttribute(): string
    {
        $tooltip = $this->text_tooltip ?? '';
        if (!$tooltip) return $tooltip;

        if (app()->getLocale() === 'en') {
            $key = preg_replace('/[^a-zA-Z0-9]+/', '_', trim($tooltip));
            return \App\Services\AutoLangService::ensureKey($key, $tooltip);
        }
        return $tooltip;
    }
}
