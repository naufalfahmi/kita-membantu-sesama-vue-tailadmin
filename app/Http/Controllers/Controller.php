<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Check whether the given user should be treated as an admin for
     * purposes of bypassing per-user visibility filters.
     */
    protected function userIsAdmin($user): bool
    {
        if (! $user) {
            return false;
        }

        // Keep the existing explicit role checks (backwards-compatible)
        if ($user->hasAnyRole(['admin', 'superadmin', 'super-admin'])) {
            return true;
        }

        // Only treat explicit admin roles as admin-like. Avoid matching
        // generic role/posisi names like "Admin Cabang" which are local
        // operational roles and should not bypass visibility filters.
        // Keep the explicit allowed role names for backward compatibility.

        return false;
    }
}
