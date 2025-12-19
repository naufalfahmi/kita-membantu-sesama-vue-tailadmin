<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$recordId = $argv[1] ?? null;
if (!$recordId) {
    echo "Usage: php scripts/remove_transfer_proof.php <recordId>\n";
    exit(1);
}

$record = App\Models\PayrollRecord::find($recordId);
if (!$record) {
    echo "Record not found: {$recordId}\n";
    exit(1);
}

$path = $record->transfer_proof;
if (empty($path)) {
    echo "No transfer_proof set on record {$recordId}\n";
    exit(0);
}

if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
    \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
    echo "Deleted file: {$path}\n";
} else {
    echo "File not found: {$path}\n";
}

$record->transfer_proof = null;
$record->save();

echo "Cleared transfer_proof on record {$recordId}\n";