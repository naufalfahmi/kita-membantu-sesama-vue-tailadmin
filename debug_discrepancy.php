<?php

use App\Models\Penyaluran;
use App\Models\PengajuanDanaDisbursement;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$start = '2020-01-01'; // Broad range
$end = date('Y-m-d');

$penyaluranTotal = Penyaluran::whereBetween(DB::raw('DATE(created_at)'), [$start, $end])->sum('amount');
$disbursementTotal = PengajuanDanaDisbursement::selectRaw('SUM(amount) as total')->value('total');

echo "Penyaluran Total: " . number_format($penyaluranTotal, 2) . PHP_EOL;
echo "Disbursement Total: " . number_format($disbursementTotal, 2) . PHP_EOL;

// Check "Program" specific
// Assuming 'Program' maps to empty submission_type or 'program'
$penyaluranProgram = Penyaluran::whereHas('pengajuan', function($q) {
    $q->where('submission_type', 'program')->orWhereNull('submission_type');
})->sum('amount');

$disbursementProgram = PengajuanDanaDisbursement::whereHas('pengajuan', function($q) {
    $q->where('submission_type', 'program')->orWhereNull('submission_type');
})->sum('amount');

echo "Penyaluran (Program type): " . number_format($penyaluranProgram, 2) . PHP_EOL;
echo "Disbursement (Program type): " . number_format($disbursementProgram, 2) . PHP_EOL;
