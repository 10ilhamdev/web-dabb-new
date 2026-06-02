<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use \App\Traits\CleansRteMedia;

    protected $fillable = ['key', 'value'];

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::saved(function () {
            \Illuminate\Support\Facades\Cache::forget('settings_all');
        });

        static::deleted(function () {
            \Illuminate\Support\Facades\Cache::forget('settings_all');
        });
    }

    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }
}
