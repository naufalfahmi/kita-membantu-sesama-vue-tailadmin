<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     * Usage: 'permission:view something'
     */
    public function handle(Request $request, Closure $next, $permission = null)
    {
        $user = $request->user();

        if (! $user) {
            abort(403);
        }

        // If Spatie's package is available, prefer its check, otherwise fallback to basic can()
        if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
            return $next($request);
        }

        if ($permission && $user->can($permission)) {
            return $next($request);
        }

        abort(403);
    }
}
