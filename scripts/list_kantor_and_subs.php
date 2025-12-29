<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$email = 'hellyarmiandri2401@gmail.com';
$user = User::where('email', $email)->first();
if (! $user) {
    echo "not found\n";
    exit(1);
}
$subs = $user->subordinates()->pluck('id')->toArray();
$assigned = [];
try {
    $assigned = $user->kantorCabangs()->pluck('id')->toArray();
} catch (Exception $e) {
}

echo json_encode(['user_id' => $user->id, 'kantor_cabang_id' => $user->kantor_cabang_id, 'assigned_pivot' => $assigned, 'subordinates' => $subs], JSON_PRETTY_PRINT) . "\n";
