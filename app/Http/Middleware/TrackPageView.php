<?php

namespace App\Http\Middleware;

use App\Models\PageView;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackPageView
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Exclude internal paths, APIs, assets, and auth routes
        $excludedPaths = [
            'cms/*', 'api/*', 'login', 'register', 'password/*', 'email/*',
            'dashboard', 'dashboard/*', 'profile', 'profile/*', 'storage/*',
            'lang/*', 'vss-image-proxy*'
        ];

        // Only track GET requests for public HTML pages
        if ($request->isMethod('GET') && !$request->ajax() && !$request->is(...$excludedPaths)) {
            try {
                PageView::create([
                    'user_id' => $request->user()?->id,
                    'path' => $request->path(),
                    'ip' => $request->ip(),
                    'viewed_date' => now('Asia/Jakarta')->toDateString(),
                ]);
            } catch (\Throwable $e) {
                // Silently fail - don't break the page for tracking issues
            }
        }

        return $response;
    }
}
