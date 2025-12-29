<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use App\Models\User;

if (!isset($argv[1]) || empty($argv[1])) { echo "Usage: php inspect_user_full.php user@example.com\n"; exit(1); }
$email = $argv[1];
$user = User::where('email', $email)->with(['roles','kantorCabangs','subordinates'])->first();
if (! $user) { echo json_encode(['error'=>'user_not_found','email'=>$email], JSON_PRETTY_PRINT) . "\n"; exit(0); }

$roles = $user->roles->pluck('name')->all();
$kantorIds = $user->kantorCabangs->pluck('id')->all();
$subs = $user->subordinates->pluck('id')->all();

echo json_encode([
    'id' => $user->id,
    'email' => $user->email,
    'posisi' => $user->posisi,
    'roles' => $roles,
    'kantor_cabang_id' => $user->kantor_cabang_id,
    'kantor_cabang_pivot_ids' => $kantorIds,
    'leader_id' => $user->leader_id,
    'has_subordinates' => !empty($subs),
    'subordinates' => $subs,
], JSON_PRETTY_PRINT) . "\n";
