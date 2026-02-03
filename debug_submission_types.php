<?php

use App\Models\ProgramShareType;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // Replicating the logic from ProgramShareTypeController::submissionTypes
    $aliases = ProgramShareType::whereNotNull('alias')
        ->orderBy('alias')
        ->get()
        ->groupBy('alias')
        ->map(function ($items, $alias) {
            $first = $items->first();
            
            return [
                'value' => $alias,
                'label' => $alias,
                'share_key' => $first->key,
                'name' => $first->name,
            ];
        })
        ->values();

    echo json_encode(['success' => true, 'data' => $aliases], JSON_PRETTY_PRINT);

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
