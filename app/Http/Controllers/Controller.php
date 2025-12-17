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

        // Treat any role name that contains "admin"/"administrator"/"super" as admin-like
        foreach ($user->roles as $role) {
            $name = strtolower((string) $role->name);
            if (stripos($name, 'admin') !== false || stripos($name, 'administrator') !== false || stripos($name, 'super') !== false) {
                return true;
            }
        }

        // Also allow checking the user's `posisi` (jabatan) field for admin-like titles
        if (property_exists($user, 'posisi') && is_string($user->posisi)) {
            $posisi = strtolower($user->posisi);
            if (stripos($posisi, 'admin') !== false || stripos($posisi, 'administrator') !== false) {
                return true;
            }
        }

        return false;
    }
}
