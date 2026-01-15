<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Checking Bonus Allocation (1%) ===" . PHP_EOL . PHP_EOL;

// Get bonus share type
$bonusType = App\Models\ProgramShareType::where('key', 'bonus')->first();
if (!$bonusType) {
    echo "Bonus share type not found!" . PHP_EOL;
    exit(1);
}

echo "Bonus Share Type: {$bonusType->name} (key: {$bonusType->key})" . PHP_EOL;
echo "Alias: {$bonusType->alias}" . PHP_EOL . PHP_EOL;

// Check which programs use bonus share
$programs = App\Models\Program::with(['shares' => function($q) use ($bonusType) {
    $q->where('program_share_type_id', $bonusType->id);
}])->get();

echo "Programs with Bonus share:" . PHP_EOL;
$totalInflow = 0;
$totalAllocatedBonus = 0;

foreach ($programs as $program) {
    if ($program->shares->count() > 0) {
        $bonusShare = $program->shares->first();
        
        // Calculate total inflow for this program (full history)
        $inflow = App\Models\Transaksi::where('program_id', $program->id)->sum('nominal');
        
        // Calculate bonus allocation
        $bonusAllocated = 0;
        if ($bonusShare->type === 'percentage' && $bonusShare->value !== null) {
            $bonusAllocated = (int) floor($inflow * (float)$bonusShare->value / 100);
        }
        
        $totalInflow += $inflow;
        $totalAllocatedBonus += $bonusAllocated;
        
        echo sprintf(
            "  - %-30s: Inflow Rp %15s → Bonus %s%% = Rp %15s\n",
            $program->nama_program,
            number_format($inflow),
            $bonusShare->value,
            number_format($bonusAllocated)
        );
    }
}

echo PHP_EOL;
echo "Total Inflow (all programs): Rp " . number_format($totalInflow) . PHP_EOL;
echo "Total Bonus Allocated (1%):  Rp " . number_format($totalAllocatedBonus) . PHP_EOL;

// Check bonus disbursements
$disbursed = App\Models\PengajuanDanaDisbursement::whereHas('pengajuan', function($q) {
    $q->where('submission_type', 'Bonus');
})->sum('amount');

echo "Bonus Disbursed:             Rp " . number_format($disbursed) . PHP_EOL;
echo "Bonus Remaining:             Rp " . number_format($totalAllocatedBonus - $disbursed) . PHP_EOL;

// Verify calculation: 1% of what total = 104,189,400?
$reverseTotal = 104189400 * 100;
echo PHP_EOL;
echo "Verification: If remaining is Rp 104.189.400, then total inflow must be around Rp " . number_format($reverseTotal) . PHP_EOL;
