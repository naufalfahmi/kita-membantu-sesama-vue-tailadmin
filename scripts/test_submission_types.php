<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Test the submission types endpoint
echo "=== Testing Submission Types Endpoint ===" . PHP_EOL . PHP_EOL;

$controller = new App\Http\Controllers\ProgramShareTypeController();
$request = new Illuminate\Http\Request();

$response = $controller->submissionTypes();
$json = json_decode($response->getContent(), true);

if ($json['success']) {
    echo "✓ Success!" . PHP_EOL;
    echo "Total types found: " . count($json['data']) . PHP_EOL . PHP_EOL;
    
    foreach ($json['data'] as $type) {
        echo sprintf("- %-20s (share_key: %s)", $type['label'], $type['share_key']) . PHP_EOL;
    }
} else {
    echo "✗ Failed: " . $json['message'] . PHP_EOL;
}

echo PHP_EOL . "=== Raw Database Check ===" . PHP_EOL;
$types = App\Models\ProgramShareType::whereNotNull('alias')
    ->orderBy('alias')
    ->get(['id', 'name', 'key', 'alias']);

echo "Found " . $types->count() . " share types with alias:" . PHP_EOL;
foreach ($types as $t) {
    echo sprintf("  - Alias: %-20s | Key: %-10s | Name: %s", $t->alias, $t->key, $t->name) . PHP_EOL;
}
