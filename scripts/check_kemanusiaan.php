<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$prog = App\Models\Program::where('nama_program', 'like', '%kemanusiaan nasional%')->first();
echo 'Program ID: ' . $prog->id . PHP_EOL;

// Check transaksi for this program
$trans = App\Models\Transaksi::where('program_id', $prog->id)
    ->orderByDesc('tanggal_transaksi')
    ->limit(5)
    ->get(['id', 'kode', 'nominal', 'tanggal_transaksi']);

echo 'Recent transaksi for Kemanusiaan Nasional:' . PHP_EOL;
foreach ($trans as $t) {
    echo '- ' . $t->tanggal_transaksi . ': ' . number_format($t->nominal) . ' (' . $t->kode . ')' . PHP_EOL;
}

// Check shares
echo PHP_EOL . 'Shares for Kemanusiaan Nasional:' . PHP_EOL;
$shares = $prog->shares()->with('type')->get();
foreach ($shares as $s) {
    $pst = $s->getRelationValue('type');
    $key = $pst ? $pst->key : 'NULL';
    echo '- Key: ' . $key . ', Type: ' . $s->type . ', Value: ' . $s->value . PHP_EOL;
}
