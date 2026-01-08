<?php

namespace App\Http\Controllers;

use App\Models\Mitra;
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

    /**
     * Resolve the mitra record linked to the current user (if any).
     *
     * @return array{0: Mitra|null, 1: bool} Tuple of (mitra record, is mitra user)
     */
    protected function resolveMitraContext($user): array
    {
        if (! $user) {
            return [null, false];
        }

        $tipeUser = strtolower((string) ($user->tipe_user ?? ''));
        $isMitraUser = $tipeUser === 'mitra';

        if (! $isMitraUser && method_exists($user, 'hasRole')) {
            try {
                $isMitraUser = $user->hasRole('mitra');
            } catch (\Throwable $e) {
                $isMitraUser = false;
            }
        }

        if (! $isMitraUser) {
            return [null, false];
        }

        $hasIdentifier = ! empty($user->id) || ! empty($user->email);
        if (! $hasIdentifier) {
            return [null, true];
        }

        $mitraQuery = Mitra::query();
        $mitraQuery->where(function ($q) use ($user) {
            $added = false;

            if (! empty($user->id)) {
                $q->where('user_id', $user->id);
                $added = true;
            }

            if (! empty($user->email)) {
                if ($added) {
                    $q->orWhere('email', $user->email);
                } else {
                    $q->where('email', $user->email);
                }
            }
        });

        return [$mitraQuery->first(), true];
    }
}
