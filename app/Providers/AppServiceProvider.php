<?php

namespace App\Providers;

use App\Models\Feature;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Password::defaults(function () {
            return Password::min(9)
                ->max(100)
                ->mixedCase()
                ->symbols();
        });

        View::composer('navbar', function ($view) {
            $navFeatures = \Illuminate\Support\Facades\Cache::remember('navFeatures', 600, function () {
                return Feature::whereNull('parent_id')
                    ->where('is_active', true)
                    ->with(['subfeatures' => function($q) {
                        $q->where('is_active', true)->orderBy('order')->with(['subfeatures' => function($q) {
                            $q->where('is_active', true)->orderBy('order');
                        }]);
                    }])
                    ->orderBy('order')
                    ->get();
            });
            $view->with('navFeatures', $navFeatures);
        });

        // Customize VerifyEmail notification with ANRI branding
        VerifyEmail::toMailUsing(function ($notifiable, $url) {
            return (new MailMessage)
                ->subject('Verifikasi Alamat Email - Depot Arsip Berkualitas Bandung')
                ->view('vendor.mail.html.verify', [
                    'name' => $notifiable->name ?? 'Pengguna',
                    'url' => $url,
                ]);
        });

        // Global Eloquent Model Events untuk menghapus file yang tidak digunakan
        \Illuminate\Support\Facades\Event::listen('eloquent.deleting: *', function ($eventName, array $data) {
            $model = $data[0] ?? null;
            if ($model instanceof \Illuminate\Database\Eloquent\Model) {
                foreach ($model->getAttributes() as $key => $value) {
                    $val = $model->getAttribute($key);
                    self::deleteFileOrArray($val);
                }
            }
        });

        \Illuminate\Support\Facades\Event::listen('eloquent.updating: *', function ($eventName, array $data) {
            $model = $data[0] ?? null;
            if ($model instanceof \Illuminate\Database\Eloquent\Model) {
                foreach ($model->getDirty() as $key => $newValue) {
                    $oldValue = $model->getOriginal($key);
                    if ($oldValue !== $newValue) {
                        self::deleteOldFiles($oldValue, $newValue);
                    }
                }
            }
        });
    }

    /**
     * Hapus file tunggal atau array file jika ada di storage public.
     */
    protected static function deleteFileOrArray($value): void
    {
        $paths = self::extractPaths($value);
        foreach ($paths as $path) {
            $disk = \Illuminate\Support\Facades\Storage::disk('public');
            if ($disk->exists($path)) {
                $fullPath = $disk->path($path);
                if (is_file($fullPath)) {
                    $disk->delete($path);
                }
            }
        }
    }

    /**
     * Hapus file lama yang digantikan oleh file baru.
     */
    protected static function deleteOldFiles($oldValue, $newValue): void
    {
        $oldPaths = self::extractPaths($oldValue);
        $newPaths = self::extractPaths($newValue);

        $pathsToDelete = array_diff($oldPaths, $newPaths);

        foreach ($pathsToDelete as $path) {
            $disk = \Illuminate\Support\Facades\Storage::disk('public');
            if ($disk->exists($path)) {
                $fullPath = $disk->path($path);
                if (is_file($fullPath)) {
                    $disk->delete($path);
                }
            }
        }
    }

    /**
     * Ekstrak path file dari berbagai tipe data (string, array, JSON).
     */
    protected static function extractPaths($value): array
    {
        if (empty($value)) {
            return [];
        }

        if (is_array($value)) {
            $paths = [];
            foreach ($value as $item) {
                $paths = array_merge($paths, self::extractPaths($item));
            }
            return $paths;
        }

        if (is_string($value)) {
            // Cek jika nilainya JSON array/object
            $trimmed = trim($value);
            if (str_starts_with($trimmed, '[') || str_starts_with($trimmed, '{')) {
                $decoded = json_decode($trimmed, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    return self::extractPaths($decoded);
                }
            }

            // Pastikan format path memiliki subfolder (mengandung slash / atau \) dan aman dari path traversal
            if ((str_contains($value, '/') || str_contains($value, '\\')) && !str_contains($value, '..')) {
                return [$value];
            }
        }

        return [];
    }
}

