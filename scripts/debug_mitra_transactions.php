<?php
require __DIR__ . "/../vendor/autoload.php";
$app = require_once __DIR__ . "/../bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;

$user = \App\Models\User::first();
if (!$user) {
    echo "No user found\n";
    exit(1);
}
// Ensure permission exists
if (method_exists($user, 'givePermissionTo')) {
    try { $user->givePermissionTo('view laporan keuangan'); } catch (Exception $e) {}
}

auth()->loginUsingId($user->id);

$controller = new \App\Http\Controllers\LaporanKeuanganController();
$req = Request::create('/', 'GET', ['page' => 1, 'per_page' => 20, 'tanggal_from' => '2025-01-01', 'tanggal_to' => '2026-01-07']);
// Ensure request->user() returns our authenticated user
$req->setUserResolver(function() use ($user) { return $user; });

$response = $controller->mitraTransactions($req, '52c9c1c9-c502-4b33-84b0-3525024f2f61');
$payload = $response->getData();

echo json_encode($payload, JSON_PRETTY_PRINT) . PHP_EOL;
