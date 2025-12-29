<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Donatur;

if (!isset($argv[1]) || empty($argv[1])) {
    echo "Usage: php debug_visibility_reason_cli.php user@example.com\n";
    exit(1);
}

$email = $argv[1];
$user = User::where('email', $email)->first();
if (! $user) {
    echo json_encode(['error' => 'user_not_found', 'email' => $email], JSON_PRETTY_PRINT) . "\n";
    exit(0);
}

// Use descendantIdsOf to mirror controller logic (multi-level subtree)
$allowed = \App\Models\User::descendantIdsOf($user->id);
try {
    $assignedIds = $user->kantorCabangs()->pluck('id')->toArray();
} catch (Exception $e) {
    $assignedIds = [];
}

// Build query using current controller logic
$query = Donatur::query();

// If user has no subordinates => PIC-only visibility
if (! $user->subordinates()->exists()) {
    $query->where('pic', $user->id);
} else {
    if (! empty($assignedIds)) {
        $query->where(function ($q) use ($allowed, $user, $assignedIds) {
            $q->whereIn('donaturs.kantor_cabang_id', $assignedIds)
                ->orWhereIn('donaturs.created_by', $allowed)
                ->orWhereIn('donaturs.pic', $allowed);
        });
    } else {
        $query->where(function ($q) use ($allowed, $user) {
            $q->whereIn('donaturs.created_by', $allowed)
                ->orWhereIn('donaturs.pic', $allowed);
        });
    }
}

$donaturs = $query->get();

$report = [];
foreach ($donaturs as $d) {
    $reasons = [];
    if (in_array($d->created_by, $allowed, true)) {
        $reasons[] = 'created_by_allowed';
    }
    if ((string)$d->pic === (string)$user->id) {
        $reasons[] = 'pic_is_user';
    } elseif (in_array((string)$d->pic, array_map('strval', $allowed), true)) {
        $reasons[] = 'pic_in_allowed';
    }
    if (! empty($assignedIds) && in_array($d->kantor_cabang_id, $assignedIds, true)) {
        $reasons[] = 'kantor_cabang_assigned';
    }

    $report[] = [
        'id' => $d->id,
        'kode' => $d->kode,
        'nama' => $d->nama,
        'created_by' => $d->created_by,
        'pic' => $d->pic,
        'kantor_cabang_id' => $d->kantor_cabang_id,
        'reasons' => $reasons,
    ];
}

$summary = ['total' => $donaturs->count(), 'by_reason' => []];
foreach ($report as $r) {
    foreach ($r['reasons'] as $reason) {
        if (! isset($summary['by_reason'][$reason])) $summary['by_reason'][$reason] = 0;
        $summary['by_reason'][$reason]++;
    }
}

echo json_encode(['user' => ['id' => $user->id, 'email' => $user->email, 'leader_id' => $user->leader_id], 'summary' => $summary, 'sample' => array_slice($report, 0, 50)], JSON_PRETTY_PRINT) . "\n";
