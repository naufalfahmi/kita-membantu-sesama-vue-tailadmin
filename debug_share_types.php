<?php

use App\Models\ProgramShareType;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$types = ProgramShareType::all();

foreach ($types as $t) {
    echo "ID: {$t->id} | Key: {$t->key} | Name: {$t->name} | Alias: {$t->alias}\n";
}
