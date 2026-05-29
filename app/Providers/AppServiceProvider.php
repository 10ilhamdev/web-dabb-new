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
    }
}
