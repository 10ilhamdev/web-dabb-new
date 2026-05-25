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

        $commonExcludedPaths = [
            'api/*', 'login', 'register', 'password/*', 'email/*',
            'storage/*', 'lang/*', 'vss-image-proxy*', '.well-known*', '.well-known/*',
            'favicon.ico', '_debugbar/*',
            'forgot-password', 'reset-password/*', 'verify-email/*', 'verification-status',
            'css/*', 'js/*', 'images/*', 'img/*', 'assets/*'
        ];

        $guestExcludedPaths = [
            'cms/*', 'dashboard', 'dashboard/*', 'profile', 'profile/*'
        ];

        if ($request->isMethod('GET') && !$request->ajax()) {
            $excludedPaths = $commonExcludedPaths;
            if (!$request->user()) {
                $excludedPaths = array_merge($excludedPaths, $guestExcludedPaths);
            }
            if (!$request->is(...$excludedPaths)) {
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
        }

        return $response;
    }
}
