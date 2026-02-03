<?php

use App\Models\PengajuanDana;
use App\Models\Penyaluran;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$startDate = Carbon::create(2024, 1, 1);
$endDate = Carbon::now();

echo "--- Pengajuan Dana Details (Grouping by Program + Submission Type) ---\n";
$pengajuanDetails = PengajuanDana::selectRaw('pengajuan_danas.program_id, program.nama_program, pengajuan_danas.submission_type, SUM(pengajuan_danas.amount) as total')
    ->leftJoin('program', 'pengajuan_danas.program_id', '=', 'program.id')
    ->where('status', 'Approved')
//    ->whereBetween('pengajuan_danas.created_at', [$startDate, $endDate])
    ->groupBy('pengajuan_danas.program_id', 'program.nama_program', 'pengajuan_danas.submission_type')
    ->orderBy('program.nama_program')
    ->get();

foreach ($pengajuanDetails as $item) {
    echo "ProgID: " . ($item->program_id ?? 'NULL') . 
         " | Name: " . ($item->nama_program ?? 'NULL') . 
         " | Type: " . ($item->submission_type ?? 'NULL') . 
         " | Total: " . number_format($item->total) . "\n";
}

echo "\n--- Penyaluran Details (Grouping by Program + Submission Type) ---\n";
$penyaluranDetails = Penyaluran::selectRaw('pengajuan_danas.program_id, program.nama_program, pengajuan_danas.submission_type, SUM(penyalurans.amount) as total')
    ->leftJoin('pengajuan_danas', 'penyalurans.pengajuan_dana_id', '=', 'pengajuan_danas.id')
    ->leftJoin('program', 'pengajuan_danas.program_id', '=', 'program.id')
//    ->whereBetween(DB::raw('DATE(penyalurans.created_at)'), [$startDate->toDateString(), $endDate->toDateString()])
    ->groupBy('pengajuan_danas.program_id', 'program.nama_program', 'pengajuan_danas.submission_type')
    ->orderBy('program.nama_program')
    ->get();

foreach ($penyaluranDetails as $item) {
    echo "ProgID: " . ($item->program_id ?? 'NULL') . 
         " | Name: " . ($item->nama_program ?? 'NULL') . 
         " | Type: " . ($item->submission_type ?? 'NULL') . 
         " | Total: " . number_format($item->total) . "\n";
}

echo "\n--- Testing Share Type Lookup ---\n";
$typesToTest = ['Gaji Mitra', 'Operasional', 'Gaji Karyawan', 'Program'];
foreach ($typesToTest as $t) {
    $st = \App\Models\ProgramShareType::where('name', $t)
          ->orWhere('alias', $t)
          ->first();
    echo "Type '$t' maps to key: " . ($st ? $st->key : 'NOT FOUND') . "\n";
}
