<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$periodId = '8f469af1-25d0-409c-8537-d8028bc87824';
$period = App\Models\PayrollPeriod::with('records.employee')->find($periodId);
if (!$period) {
    echo json_encode(['found' => false]) . PHP_EOL;
    exit(0);
}
$out = [];
foreach ($period->records as $r) {
    $out[] = [
        'id' => $r->id,
        'employee' => $r->employee ? $r->employee->name : null,
        'status' => $r->status,
        'transfer_proof' => $r->transfer_proof,
    ];
}
echo json_encode(['found' => true, 'period' => ['id' => $period->id, 'month' => $period->month, 'year' => $period->year], 'records' => $out], JSON_PRETTY_PRINT) . PHP_EOL;