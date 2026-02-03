<?php

use App\Models\Penyaluran;
use App\Models\PengajuanDana;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Debug "OPS 1" / "Gaji Karyawan" disbursements
echo "Checking disbursements for OPS 1 / Gaji Karyawan...\n";

// Fetch Penyaluran with joined Pengajuan info
$penyalurans = Penyaluran::selectRaw('penyalurans.id, penyalurans.amount, pengajuan_danas.submission_type, pengajuan_danas.program_id')
    ->leftJoin('pengajuan_danas', 'pengajuan_danas.id', '=', 'penyalurans.pengajuan_dana_id')
    ->where(function($q) {
        $q->where('submission_type', 'like', '%Gaji Karyawan%')
          ->orWhere('submission_type', 'like', '%OPS 1%');
    })
    ->get();

$totalAmount = 0;
$withProgram = 0;
$withoutProgram = 0;
$programsFound = [];

foreach ($penyalurans as $p) {
    $totalAmount += $p->amount;
    if ($p->program_id) {
        $withProgram += $p->amount;
        $programsFound[$p->program_id] = ($programsFound[$p->program_id] ?? 0) + $p->amount;
    } else {
        $withoutProgram += $p->amount;
    }
}

echo "Total Amount: " . number_format($totalAmount, 2) . "\n";
echo "With Program ID: " . number_format($withProgram, 2) . "\n";
echo "Without Program ID: " . number_format($withoutProgram, 2) . "\n";

if (!empty($programsFound)) {
    echo "Breakdown by Program ID:\n";
    foreach ($programsFound as $pid => $amt) {
        echo " - Program $pid: " . number_format($amt, 2) . "\n";
    }
}
