<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Test different scenarios that user might be encountering

// Scenario 1: January 2026 (where transaksi exists)
echo "=== SCENARIO 1: January 2026 ===" . PHP_EOL;
testAggregate('program', '2026-01-01', '2026-01-31');

// Scenario 2: Default lookback from now (Dec 2024)
echo PHP_EOL . "=== SCENARIO 2: Lookback 1 month from now (Dec 2024) ===" . PHP_EOL;
$now = \Carbon\Carbon::now()->startOfMonth()->subMonths(1);
testAggregate('program', $now->startOfMonth()->toDateString(), $now->endOfMonth()->toDateString());

// Scenario 3: Full history for Kemanusiaan Nasional
echo PHP_EOL . "=== SCENARIO 3: Single Program Balance (Kemanusiaan Nasional) ===" . PHP_EOL;
$prog = App\Models\Program::where('nama_program', 'like', '%kemanusiaan nasional%')->first();
echo 'Checking full history...' . PHP_EOL;
$minDate = App\Models\Transaksi::where('program_id', $prog->id)->min('tanggal_transaksi');
$maxDate = App\Models\Transaksi::where('program_id', $prog->id)->max('tanggal_transaksi');
echo "Date range: {$minDate} to {$maxDate}" . PHP_EOL;
if ($minDate && $maxDate) {
    testSingleProgram($prog->id, 'program', $minDate, $maxDate);
}

function testAggregate($shareKey, $start, $end) {
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
                'remaining' => (int) $remaining,
            ];
        }
    }
    
    echo "Date range: {$start} to {$end}" . PHP_EOL;
    echo 'Total Allocated: ' . number_format($totalAllocated) . PHP_EOL;
    echo 'Total Remaining: ' . number_format($totalAllocated - $totalOutflow) . PHP_EOL;
    foreach ($breakdown as $b) {
        echo '  - ' . $b['program'] . ': ' . number_format($b['remaining']) . PHP_EOL;
    }
}

function testSingleProgram($programId, $shareKey, $start, $end) {
    $prog = App\Models\Program::with(['shares.type'])->find($programId);
    
    $inflow = App\Models\Transaksi::where('program_id', $programId)
        ->whereBetween('tanggal_transaksi', [$start, $end])
        ->sum('nominal');
    
    // Find allocation share
    $allocation = null;
    foreach ($prog->shares as $s) {
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
        }
    }
    
    $outflow = App\Models\PengajuanDanaDisbursement::where('program_id', $programId)
        ->whereBetween('tanggal_disburse', [$start, $end])
        ->sum('amount');
    
    $remaining = max(0, $allocated - $outflow);
    
    echo "Program: {$prog->nama_program}" . PHP_EOL;
    echo "Inflow: " . number_format($inflow) . PHP_EOL;
    echo "Allocated: " . number_format($allocated) . PHP_EOL;
    echo "Remaining: " . number_format($remaining) . PHP_EOL;
}
