<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::first();
$request = new \Illuminate\Http\Request();
$request->setUserResolver(function() use ($user) { return $user; });

$controller = new \App\Http\Controllers\LaporanKeuanganController();
$response = $controller->programBreakdown($request);

$data = json_decode($response->getContent(), true);
if ($data['success']) {
    echo "Program Breakdown Data:\n\n";
    foreach ($data['data'] as $program) {
        echo "Program: {$program['nama']}\n";
        echo "  Pemasukan: " . number_format($program['pemasukan']) . "\n";
        echo "  Pengajuan Dana: " . number_format($program['pengajuan_dana']) . "\n";
        echo "  Penyaluran: " . number_format($program['penyaluran']) . "\n";
        
        if (isset($program['breakdown'])) {
            echo "  Breakdown Detail:\n";
            if (!empty($program['breakdown']['pemasukan'])) {
                echo "    Pemasukan:\n";
                foreach ($program['breakdown']['pemasukan'] as $item) {
                    echo "      - {$item['program_nama']}: " . number_format($item['amount']) . "\n";
                }
            }
            if (!empty($program['breakdown']['pengajuan_dana'])) {
                echo "    Pengajuan Dana:\n";
                foreach ($program['breakdown']['pengajuan_dana'] as $item) {
                    echo "      - {$item['program_nama']}: " . number_format($item['amount']) . "\n";
                }
            }
            if (!empty($program['breakdown']['penyaluran'])) {
                echo "    Penyaluran:\n";
                foreach ($program['breakdown']['penyaluran'] as $item) {
                    echo "      - {$item['program_nama']}: " . number_format($item['amount']) . "\n";
                }
            }
        }
        echo "\n";
    }
} else {
    echo "Error: " . ($data['message'] ?? 'Unknown');
}
