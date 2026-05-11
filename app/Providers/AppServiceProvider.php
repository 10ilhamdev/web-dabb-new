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
            $view->with('navFeatures', Feature::whereNull('parent_id')
                ->with('subfeatures.subfeatures')
                ->orderBy('order')
                ->get());
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
