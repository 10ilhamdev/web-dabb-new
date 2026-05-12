<?php

namespace App\Http\Middleware;

use App\Models\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * This middleware checks if the user has permission to access a route.
     * It supports two modes:
     * 1. Role-based: middleware('role:admin,pegawai') - checks if user role matches
     * 2. Permission-based: middleware('role:menu_key') - checks role_permissions table
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Admin always has access to everything
        if ($user->role === 'admin') {
            return $next($request);
        }

        // Check if any of the provided roles is actually a menu_key (contains dot notation)
        $menuKeys = array_filter($roles, fn($r) => str_contains($r, '.'));
        $roleNames = array_filter($roles, fn($r) => !str_contains($r, '.'));

        // If menu keys are provided, check permissions table
        if (!empty($menuKeys)) {
            $userRole = Role::where('name', $user->role)->first();

            if (!$userRole) {
                $slug = str_replace('_', '-', $user->role);
                return redirect()->route('dashboard.role', ['slug' => $slug])
                    ->with('error', 'Role tidak ditemukan.');
            }

            // Check if user has permission for any of the menu keys
            foreach ($menuKeys as $menuKey) {
                if ($userRole->hasPermission($menuKey)) {
                    return $next($request);
                }
            }
        }

        // If role names are provided, check if user role matches
        if (!empty($roleNames) && in_array($user->role, $roleNames, true)) {
            return $next($request);
        }

        // No permission - redirect to user's dashboard
        $slug = str_replace('_', '-', $user->role);
        return redirect()->route('dashboard.role', ['slug' => $slug])
            ->with('error', 'Anda tidak memiliki akses ke halaman ini.');
    }
}
