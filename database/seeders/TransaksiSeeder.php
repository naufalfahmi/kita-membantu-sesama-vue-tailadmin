<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Transaksi;
use App\Models\Donatur;
use App\Models\Program;
use App\Models\KantorCabang;
use App\Models\User;

class TransaksiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ensure there are some donatur entries to reference
        if (Donatur::count() < 5) {
            for ($i = 1; $i <= 5; $i++) {
                Donatur::updateOrCreate(
                    ['nama' => "Donatur Sample {$i}"],
                    [
                        'kode' => 'DNT-' . Str::upper(Str::random(6)),
                        'nama' => "Donatur Sample {$i}",
                        'jenis_donatur' => ['perorangan'],
                        'no_handphone' => '62811' . rand(10000000, 99999999),
                        'status' => 'aktif',
                    ]
                );
            }
        }

        $programs = Program::all();
        $branches = KantorCabang::all();

        // find fundraiser users by posisi containing 'fund' (case-insensitive)
        $fundraisers = User::whereRaw("LOWER(posisi) LIKE ?", ['%fund%'])->get();

        // if none, try role named Fundrising (as used in karyawan seeder)
        if ($fundraisers->isEmpty()) {
            $fundraisers = User::whereHas('roles', function ($q) {
                $q->whereRaw("LOWER(name) LIKE ?", ['%fund%']);
            })->get();
        }

        $donaturIds = Donatur::pluck('id')->toArray();
        $programIds = $programs->pluck('id')->toArray();
        $branchIds = $branches->pluck('id')->toArray();

        if (empty($donaturIds) || empty($programIds) || empty($branchIds)) {
            $this->command->warn('Not enough data to create transaksi (need donatur, program, kantor cabang).');
            return;
        }

        // create 30 transactions
        for ($i = 0; $i < 30; $i++) {
            $donaturId = $donaturIds[array_rand($donaturIds)];
            $programId = $programIds[array_rand($programIds)];
            $branchId = $branchIds[array_rand($branchIds)];

            $fundraiserId = null;
            if (!$fundraisers->isEmpty()) {
                $fundraiserId = $fundraisers->random()->id;
            }

            $nominal = rand(100000, 5000000);
            $tanggal = Carbon::today()->subDays(rand(0, 90));

            Transaksi::create([
                'kode' => 'TRX-' . strtoupper(Str::random(8)),
                'kantor_cabang_id' => $branchId,
                'donatur_id' => $donaturId,
                'program_id' => $programId,
                'fundraiser_id' => $fundraiserId,
                'nominal' => $nominal,
                'tanggal_transaksi' => $tanggal,
                'keterangan' => 'Seeded transaksi sample',
                // status must match enum in migration: pending, verified, cancelled
                'status' => 'verified',
            ]);
        }

        $this->command->info('Seeded 30 transaksi (with fundraiser if available)');
    }
}
