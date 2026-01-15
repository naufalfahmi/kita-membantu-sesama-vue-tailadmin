<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Testing aggregateBalance for Bonus ===" . PHP_EOL . PHP_EOL;

// Simulate request with alias=Bonus
$request = new Illuminate\Http\Request();
$request->merge(['alias' => 'Bonus']);

$controller = new App\Http\Controllers\ProgramController();
$response = $controller->aggregateBalance($request);
$data = json_decode($response->getContent(), true);

if ($data['success']) {
    $result = $data['data'];
    echo "Share Key: {$result['share_key']}" . PHP_EOL;
    echo "Date Range: {$result['start_date']} to {$result['end_date']}" . PHP_EOL;
    echo "Total Inflow: Rp " . number_format($result['inflow']) . PHP_EOL;
    echo "Total Allocated: Rp " . number_format($result['allocated']) . PHP_EOL;
    echo "Total Outflow: Rp " . number_format($result['outflow']) . PHP_EOL;
    echo "Total Remaining: Rp " . number_format($result['remaining']) . PHP_EOL;
    echo PHP_EOL;
    
    echo "Program Breakdown:" . PHP_EOL;
    foreach ($result['program_breakdown'] as $prog) {
        echo sprintf(
            "  - %-30s: Inflow Rp %12s, Allocated Rp %12s, Remaining Rp %12s (%.2f%%)\n",
            $prog['program_name'],
            number_format($prog['inflow']),
            number_format($prog['allocated']),
            number_format($prog['remaining']),
            $prog['share_value'] ?? 0
        );
    }
} else {
    echo "Error: " . $data['message'] . PHP_EOL;
}
