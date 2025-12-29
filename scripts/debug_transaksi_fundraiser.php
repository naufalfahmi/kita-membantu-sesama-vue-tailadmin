<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Transaksi;

if (!isset($argv[1]) || !isset($argv[2])) {
    echo "Usage: php debug_transaksi_fundraiser.php user@example.com fundraiser_id\n";
    exit(1);
}

$email = $argv[1];
$fid = $argv[2];

$user = User::where('email', $email)->first();
if (! $user) {
    echo json_encode(['error' => 'user_not_found', 'email' => $email], JSON_PRETTY_PRINT) . "\n";
    exit(1);
}

$isAdmin = in_array('super-admin', $user->getRoleNames()->toArray()) || in_array('admin', $user->getRoleNames()->toArray());
// Prefer controller helper if available
try {
    $allowed = \App\Models\User::descendantIdsOf($user->id);
} catch (Throwable $e) {
    $allowed = [$user->id];
}

if (! $isAdmin && ! in_array((string)$fid, array_map('strval', $allowed), true)) {
    echo json_encode(['allowed' => $allowed, 'requested_fid' => $fid, 'result' => 'forbidden_not_in_subtree', 'count' => 0], JSON_PRETTY_PRINT) . "\n";
    exit(0);
}

$q = Transaksi::query();
$q->where(function($q) use ($fid) {
    $q->where('fundraiser_id', $fid)->orWhereHas('donatur', fn($d) => $d->where('pic', $fid));
});

$count = $q->count();
$sample = $q->with(['donatur:id,nama,pic'])->limit(20)->get()->map(function($t){
    return ['id' => $t->id, 'kode' => $t->kode, 'fundraiser_id' => $t->fundraiser_id, 'donatur_pic' => $t->donatur?->pic];
});

echo json_encode(['user' => ['id' => $user->id, 'email' => $user->email], 'allowed' => $allowed, 'requested_fid' => $fid, 'count' => $count, 'sample' => $sample], JSON_PRETTY_PRINT) . "\n";
