<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$email = $argv[1] ?? null;
if (! $email) {
    echo json_encode(['error' => 'email required']) . PHP_EOL;
    exit(1);
}

$user = User::where('email', $email)->first();
if (! $user) {
    echo json_encode(['error' => 'not found']) . PHP_EOL;
    exit(0);
}

echo json_encode([
    'id' => $user->id,
    'email' => $user->email,
    'leader_id' => $user->leader_id,
    'kantor_cabang_id' => $user->kantor_cabang_id,
], JSON_PRETTY_PRINT) . PHP_EOL;
