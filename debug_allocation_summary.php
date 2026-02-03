<?php

use App\Models\Penyaluran;
use App\Models\ProgramShareType;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$startDate = Carbon::create(2020, 1, 1);
$endDate = Carbon::now();

$penyaluranQuery = Penyaluran::whereBetween(DB::raw('DATE(created_at)'), [$startDate->toDateString(), $endDate->toDateString()]);
$penyalurans = $penyaluranQuery->get();

echo "Total Penyaluran Records: " . $penyalurans->count() . "\n";

$penyaluranTotals = [];
$shareKeys = ['dp', 'ops_1', 'ops_2', 'program', 'fee_mitra', 'bonus', 'championship'];
foreach ($shareKeys as $k) $penyaluranTotals[$k] = 0;

foreach ($penyalurans as $p) {
    $amount = (float)$p->amount;
    
    // Check eager loading
    $pengajuan = $p->pengajuan;
    $submissionType = $pengajuan ? $pengajuan->submission_type : null;
    
    if (!$pengajuan) {
        echo "Warning: Penyaluran ID {$p->id} has NO Pengajuan (Amount: $amount)\n";
    }

    if ($submissionType) {
        $shareType = ProgramShareType::where('name', $submissionType)
            ->orWhere('alias', $submissionType)
            ->first();
        $key = $shareType->key ?? 'unknown';
        
        if (!isset($penyaluranTotals[$key])) {
            $penyaluranTotals[$key] = 0;
        }
        $penyaluranTotals[$key] += $amount;
        
        if ($key === 'fee_mitra') {
            echo "Found Fee Mitra! Amount: $amount\n";
        }
    } else {
        if (!isset($penyaluranTotals['unknown'])) {
            $penyaluranTotals['unknown'] = 0;
        }
        $penyaluranTotals['unknown'] += $amount;
    }
}

echo "\n--- Totals ---\n";
print_r($penyaluranTotals);
