<?php
require __DIR__ . "/../vendor/autoload.php";
$app = require_once __DIR__ . "/../bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;

$user = \App\Models\User::first();
if (!$user) { echo "No user found\n"; exit(1); }
if (method_exists($user, 'givePermissionTo')) { try { $user->givePermissionTo('view laporan keuangan'); } catch (Exception $e) {} }

// set as request user
$req1 = Request::create('/', 'GET');
$req1->setUserResolver(function() use ($user) { return $user; });
$controller = new \App\Http\Controllers\LaporanKeuanganController();
$detailResp = $controller->mitraDetail($req1, '52c9c1c9-c502-4b33-84b0-3525024f2f61');
$detailData = $detailResp->getData();

echo "Mitra detail:\n" . json_encode($detailData, JSON_PRETTY_PRINT) . "\n\n";

// call transactions with a date-range that likely excludes the existing transaction
$req2 = Request::create('/', 'GET', ['page' => 1, 'per_page' => 20, 'tanggal_from' => '2026-01-01', 'tanggal_to' => '2026-01-07']);
$req2->setUserResolver(function() use ($user) { return $user; });
$txResp1 = $controller->mitraTransactions($req2, '52c9c1c9-c502-4b33-84b0-3525024f2f61');
$txData1 = $txResp1->getData();

echo "Transactions (filtered 2026-01-01..2026-01-07):\n" . json_encode($txData1, JSON_PRETTY_PRINT) . "\n\n";

if ((($txData1->data && count((array)$txData1->data) === 0) || empty($txData1->data)) && ($detailData->data->transaksi_count ?? 0) > 0) {
    echo "Fallback: fetching without date filters...\n";
    $req3 = Request::create('/', 'GET', ['page' => 1, 'per_page' => 20]);
    $req3->setUserResolver(function() use ($user) { return $user; });
    $txResp2 = $controller->mitraTransactions($req3, '52c9c1c9-c502-4b33-84b0-3525024f2f61');
    $txData2 = $txResp2->getData();
    echo "Transactions (no date filter):\n" . json_encode($txData2, JSON_PRETTY_PRINT) . "\n";
}
