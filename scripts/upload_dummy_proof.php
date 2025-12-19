<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

$recordId = $argv[1] ?? 'c43fcf80-b75e-47b2-a02a-b5aafe121975';
$path = 'payroll_proofs/' . Str::random(16) . '.png';
$contents = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVQYV2NgYGD4DwABBAEAfE8dSgAAAABJRU5ErkJggg==');
Storage::disk('public')->put($path, $contents);
$r = App\Models\PayrollRecord::find($recordId);
if (!$r) { echo "Record not found: {$recordId}\n"; exit(1); }
$r->transfer_proof = $path;
$r->save();
echo "Wrote $path and assigned to record {$recordId}\n";