<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$shareKey = 'program';
$start = '2025-01-01';
$end = '2025-01-31';

$programs = App\Models\Program::with(['shares.type'])->get();
echo 'Total programs: ' . $programs->count() . PHP_EOL;

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

echo 'Total Inflow: ' . number_format($totalInflow) . PHP_EOL;
echo 'Total Allocated: ' . number_format($totalAllocated) . PHP_EOL;
echo 'Total Outflow: ' . number_format($totalOutflow) . PHP_EOL;
echo 'Total Remaining: ' . number_format($totalAllocated - $totalOutflow) . PHP_EOL;
echo PHP_EOL . 'Breakdown:' . PHP_EOL;
foreach ($breakdown as $b) {
    echo '- ' . $b['program'] . ': Remaining = ' . number_format($b['remaining']) . PHP_EOL;
}
