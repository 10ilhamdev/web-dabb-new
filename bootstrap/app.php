<?php

use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\TrackOnlineUsers;
use App\Http\Middleware\TrackPageView;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        // === TAMBAHKAN BARIS INI UNTUK MEMPERBAIKI MASALAH TUNNEL / EXPOSE ===
        $middleware->trustProxies(at: '*');

        $middleware->validateCsrfTokens(except: [
            'logout',
        ]);

        $middleware->web(append: [
            SetLocale::class,
            TrackPageView::class,
            TrackOnlineUsers::class,
        ]);

        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
