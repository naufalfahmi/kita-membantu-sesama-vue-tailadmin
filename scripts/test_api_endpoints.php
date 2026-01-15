<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Authenticate as a user (simulate request)
$user = App\Models\User::first();
if (!$user) {
    echo "No user found for testing" . PHP_EOL;
    exit(1);
}

echo "Testing as user: {$user->name} (ID: {$user->id})" . PHP_EOL . PHP_EOL;

// Test 1: Aggregate balance without parameters (should use full history now)
echo "=== TEST 1: Aggregate Balance (no params - should use full history) ===" . PHP_EOL;
$request = new Illuminate\Http\Request();
$request->replace([
    'share_key' => 'program',
    'submission_type' => 'program'
]);

$controller = new App\Http\Controllers\ProgramController();
$response = $controller->aggregateBalance($request);
$json = json_decode($response->getContent(), true);

if ($json['success']) {
    echo "✓ Success" . PHP_EOL;
    echo "Date range: {$json['data']['start_date']} to {$json['data']['end_date']}" . PHP_EOL;
    echo "Total Allocated: Rp " . number_format($json['data']['allocated']) . PHP_EOL;
    echo "Total Remaining: Rp " . number_format($json['data']['remaining']) . PHP_EOL;
    echo "Programs in breakdown: " . count($json['data']['program_breakdown']) . PHP_EOL;
    
    // Check if Kemanusiaan Nasional is included
    $hasKemanusiaan = false;
    foreach ($json['data']['program_breakdown'] as $prog) {
        if (stripos($prog['program_name'], 'kemanusiaan nasional') !== false) {
            $hasKemanusiaan = true;
            echo "  ✓ Contains Kemanusiaan Nasional: Rp " . number_format($prog['remaining']) . PHP_EOL;
        }
    }
    if (!$hasKemanusiaan) {
        echo "  ✗ WARNING: Kemanusiaan Nasional NOT in breakdown!" . PHP_EOL;
    }
} else {
    echo "✗ Failed: " . $json['message'] . PHP_EOL;
}

// Test 2: Single program balance (Kemanusiaan Nasional) with full history
echo PHP_EOL . "=== TEST 2: Single Program Balance (Kemanusiaan Nasional - full history) ===" . PHP_EOL;
$prog = App\Models\Program::where('nama_program', 'like', '%kemanusiaan nasional%')->first();

$request2 = new Illuminate\Http\Request();
$request2->replace([
    'share_key' => 'program'
]);

$response2 = $controller->balance($request2, $prog->id);
$json2 = json_decode($response2->getContent(), true);

if ($json2['success']) {
    echo "✓ Success" . PHP_EOL;
    echo "Date range: {$json2['data']['start_date']} to {$json2['data']['end_date']}" . PHP_EOL;
    echo "Allocated: Rp " . number_format($json2['data']['allocated']) . PHP_EOL;
    echo "Remaining: Rp " . number_format($json2['data']['remaining']) . PHP_EOL;
} else {
    echo "✗ Failed: " . $json2['message'] . PHP_EOL;
}

// Verify aggregate >= single
echo PHP_EOL . "=== VERIFICATION ===" . PHP_EOL;
$aggregateRemaining = $json['data']['remaining'] ?? 0;
$singleRemaining = $json2['data']['remaining'] ?? 0;

if ($aggregateRemaining >= $singleRemaining) {
    echo "✓ PASS: Aggregate ({$aggregateRemaining}) >= Single Program ({$singleRemaining})" . PHP_EOL;
    echo "✓ Difference (other programs): Rp " . number_format($aggregateRemaining - $singleRemaining) . PHP_EOL;
} else {
    echo "✗ FAIL: Aggregate ({$aggregateRemaining}) < Single Program ({$singleRemaining})" . PHP_EOL;
    echo "  This indicates aggregate is missing some data!" . PHP_EOL;
}
