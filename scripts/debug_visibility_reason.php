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
    echo "User not found\n";
    exit(1);
}

$subIds = $user->subordinates()->pluck('id')->toArray();
$allowed = array_merge([$user->id], $subIds);
try {
    $assignedIds = $user->kantorCabangs()->pluck('id')->toArray();
} catch (Exception $e) {
    $assignedIds = [];
}

// Build query using current controller logic
$query = Donatur::query();
if (! empty($assignedIds)) {
    $query->where(function ($q) use ($allowed, $user, $assignedIds) {
        $q->whereIn('donaturs.kantor_cabang_id', $assignedIds)
          ->orWhereIn('donaturs.created_by', $allowed)
          ->orWhere('pic', $user->id);
    });
} else {
    $query->where(function ($q) use ($allowed, $user) {
        $q->whereIn('donaturs.created_by', $allowed)
          ->orWhere('pic', $user->id);
    });
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

// Summarize counts by reason
$summary = ['total' => $donaturs->count(), 'by_reason' => []];
foreach ($report as $r) {
    foreach ($r['reasons'] as $reason) {
        if (! isset($summary['by_reason'][$reason])) $summary['by_reason'][$reason] = 0;
        $summary['by_reason'][$reason]++;
    }
}

echo json_encode(['user' => ['id' => $user->id, 'email' => $user->email], 'summary' => $summary, 'sample' => array_slice($report, 0, 50)], JSON_PRETTY_PRINT);
