<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Donatur;

$email = 'abu.hafizha@gmail.com';
$user = User::where('email', $email)->first();

if (! $user) {
    echo json_encode(['error' => 'user_not_found']);
    exit(0);
}

// Determine isAdmin similar to Controller::userIsAdmin
$isAdmin = false;
if ($user->hasAnyRole(['admin', 'superadmin', 'super-admin'])) {
    $isAdmin = true;
} else {
    foreach ($user->roles as $role) {
        $name = strtolower((string) $role->name);
        if (stripos($name, 'admin') !== false || stripos($name, 'administrator') !== false || stripos($name, 'super') !== false) {
            $isAdmin = true;
            break;
        }
    }
    if (! $isAdmin && property_exists($user, 'posisi') && is_string($user->posisi)) {
        $posisi = strtolower($user->posisi);
        if (stripos($posisi, 'admin') !== false || stripos($posisi, 'administrator') !== false) {
            $isAdmin = true;
        }
    }
}

$out = [];
$out['user'] = [
    'id' => $user->id,
    'email' => $user->email,
    'name' => $user->name,
    'leader_id' => $user->leader_id,
    'kantor_cabang_id' => $user->kantor_cabang_id,
    'is_admin_guess' => $isAdmin,
];

if ($isAdmin) {
    $donaturs = Donatur::withTrashed()->take(50)->get();
    $out['note'] = 'user is admin-like; listing sample donaturs';
} else {
    if (! empty($user->leader_id)) {
        $query = Donatur::query();
        $query->where(function ($q) use ($user) {
            $q->where('donaturs.created_by', $user->id)->orWhere('pic', $user->id);
        });
        $out['visibility_rule'] = 'subordinate_strict';
    } else {
        $subIds = $user->subordinates()->pluck('id')->toArray();
        $allowed = array_merge([$user->id], $subIds);
        try {
            $assignedIds = $user->kantorCabangs()->pluck('id')->toArray();
        } catch (Exception $e) {
            $assignedIds = [];
        }

        $query = Donatur::query();
        if (! empty($assignedIds)) {
            $query->where(function ($q) use ($allowed, $user, $assignedIds) {
                $q->whereIn('donaturs.kantor_cabang_id', $assignedIds)
                  ->orWhereIn('donaturs.created_by', $allowed)
                  ->orWhere('pic', $user->id);
            });
            $out['visibility_rule'] = 'leader_with_assigned_kantor_cabang';
        } else {
            // Match updated controller: do NOT use primary kantor_cabang_id fallback —
            // restrict to created_by (self + subordinates) or pic only.
            $query->where(function ($q) use ($allowed, $user) {
                $q->whereIn('donaturs.created_by', $allowed)
                  ->orWhere('pic', $user->id);
            });
            $out['visibility_rule'] = 'leader_no_assigned_kantor_cabang';
        }
    }

    $donaturs = $query->get();
}

$out['donaturs_count'] = $donaturs->count();
$out['donaturs_sample'] = $donaturs->map(function($d) {
    return [
        'id' => $d->id,
        'kode' => $d->kode,
        'nama' => $d->nama,
        'created_by' => $d->created_by,
        'pic' => $d->pic,
        'kantor_cabang_id' => $d->kantor_cabang_id,
    ];
})->take(50)->values();

echo json_encode($out, JSON_PRETTY_PRINT);
