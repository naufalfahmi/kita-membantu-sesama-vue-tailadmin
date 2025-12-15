<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     * Usage: 'role:admin'
     */
    public function handle(Request $request, Closure $next, $role = null)
    {
        $user = $request->user();

        if (! $user) {
            abort(403);
        }

        if (! $role) {
            abort(403);
        }

        if (method_exists($user, 'hasRole') && $user->hasRole($role)) {
            return $next($request);
        }

        abort(403);
    }
}
