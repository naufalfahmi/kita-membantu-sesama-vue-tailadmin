<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== AGGREGATE BALANCE (Full History) ===" . PHP_EOL;

$shareKey = 'program';

// Use full history
$minDate = App\Models\Transaksi::min('tanggal_transaksi');
$maxDate = App\Models\Transaksi::max('tanggal_transaksi');

$start = \Carbon\Carbon::parse($minDate)->startOfDay()->toDateString();
$end = \Carbon\Carbon::parse($maxDate)->endOfDay()->toDateString();

echo "Date range: {$start} to {$end}" . PHP_EOL . PHP_EOL;

$programs = App\Models\Program::with(['shares.type'])->get();

$totalInflow = 0;
$totalAllocated = 0;
$totalOutflow = 0;
$breakdown = [];

foreach ($programs as $program) {
    $inflow = App\Models\Transaksi::where('program_id', $program->id)
        ->whereBetween('tanggal_transaksi', [$start, $end])
        ->sum('nominal');
    
    // Find allocation share
    $allocation = null;
    foreach ($program->shares as $s) {
        $pst = $s->relationLoaded('type') ? $s->getRelationValue('type') : App\Models\ProgramShareType::find($s->program_share_type_id);
        if ($pst && ($pst->key ?? null) === $shareKey) {
            $allocation = $s;
            break;
        }
    }
    
    $allocated = $inflow;
    if ($allocation) {
        if ($allocation->type === 'percentage' && $allocation->value !== null) {
            $allocated = (int) floor($inflow * (float)$allocation->value / 100);
        } elseif ($allocation->type === 'nominal' && $allocation->value !== null) {
            $allocated = (int) $allocation->value;
        }
    }
    
    $outflow = App\Models\PengajuanDanaDisbursement::where('program_id', $program->id)
        ->whereBetween('tanggal_disburse', [$start, $end])
        ->sum('amount');
    
    $remaining = max(0, $allocated - $outflow);
    
    $totalInflow += (int) $inflow;
    $totalAllocated += (int) $allocated;
    $totalOutflow += (int) $outflow;
    
    if ($inflow > 0 || $allocated > 0 || $remaining > 0) {
        $breakdown[] = [
            'program' => $program->nama_program,
            'inflow' => (int) $inflow,
            'allocated' => (int) $allocated,
            'outflow' => (int) $outflow,
            'remaining' => (int) $remaining,
        ];
    }
}

$totalRemaining = max(0, $totalAllocated - $totalOutflow);

echo 'Total Inflow: Rp ' . number_format($totalInflow) . PHP_EOL;
echo 'Total Allocated: Rp ' . number_format($totalAllocated) . PHP_EOL;
echo 'Total Outflow: Rp ' . number_format($totalOutflow) . PHP_EOL;
echo 'Total Remaining: Rp ' . number_format($totalRemaining) . PHP_EOL;
echo PHP_EOL . 'Program Breakdown:' . PHP_EOL;
foreach ($breakdown as $b) {
    echo sprintf("  %-35s: Rp %12s (allocated: Rp %12s)", 
        $b['program'], 
        number_format($b['remaining']), 
        number_format($b['allocated'])
    ) . PHP_EOL;
}

echo PHP_EOL . "=== SINGLE PROGRAM (Kemanusiaan Nasional) ===" . PHP_EOL;
$prog = App\Models\Program::where('nama_program', 'like', '%kemanusiaan nasional%')->first();
$progInflow = App\Models\Transaksi::where('program_id', $prog->id)->whereBetween('tanggal_transaksi', [$start, $end])->sum('nominal');
$progAllocation = null;
foreach ($prog->shares()->with('type')->get() as $s) {
    $pst = $s->getRelationValue('type');
    if ($pst && ($pst->key ?? null) === 'program') {
        $progAllocation = $s;
        break;
    }
}
$progAllocated = $progInflow;
if ($progAllocation && $progAllocation->type === 'percentage' && $progAllocation->value !== null) {
    $progAllocated = (int) floor($progInflow * (float)$progAllocation->value / 100);
}
$progOutflow = App\Models\PengajuanDanaDisbursement::where('program_id', $prog->id)->whereBetween('tanggal_disburse', [$start, $end])->sum('amount');
$progRemaining = max(0, $progAllocated - $progOutflow);

echo "Remaining: Rp " . number_format($progRemaining) . " (allocated: Rp " . number_format($progAllocated) . ")" . PHP_EOL;
echo PHP_EOL . "✓ Aggregate total includes Kemanusiaan Nasional: " . ($totalAllocated >= $progAllocated ? "YES" : "NO") . PHP_EOL;
echo "✓ Difference: Rp " . number_format($totalRemaining - $progRemaining) . " (other programs)" . PHP_EOL;
