<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Absensi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

class AbsensiMassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Target total number of absensi records to generate (env override supported)
        $target = (int) env('ABSENSI_TOTAL', 5000);

        $karyawan = User::karyawan()->where('is_active', true)->pluck('id')->toArray();

        if (empty($karyawan)) {
            $this->command->warn('No active karyawan found. Aborting mass absensi seeder.');
            return;
        }

        $created = 0;
        $attempts = 0;
        $maxAttempts = $target * 10; // fail-safe to avoid infinite loops

        $this->command->info("Starting mass absensi seeder: target={$target}");

        while ($created < $target && $attempts < $maxAttempts) {
            $attempts++;

            $userId = $karyawan[array_rand($karyawan)];

            // random date within last 365 days
            $date = Carbon::today()->subDays(rand(0, 365));

            // avoid duplicates: check existing on DB for that user/date
            $exists = Absensi::where('user_id', $userId)
                ->whereDate('jam_masuk', $date->toDateString())
                ->exists();

            if ($exists) {
                continue;
            }

            // generate jam_masuk between 07:00 and 10:00
            $hour = rand(7, 10);
            $minute = rand(0, 59);
            $jamMasuk = Carbon::parse($date->toDateString() . " {$hour}:{$minute}:00");

            // jam keluar 7-10 hours later
            $jamKeluar = (clone $jamMasuk)->addHours(rand(7, 10))->addMinutes(rand(-30, 60));

            $totalJam = round($jamKeluar->diffInMinutes($jamMasuk) / 60, 2);

            // optional: pull user kantor cabang coords for lat/lon
            $user = User::find($userId);
            $lat = null;
            $lon = null;
            if ($user && $user->kantorCabang) {
                $lat = (float) $user->kantorCabang->latitude;
                $lon = (float) $user->kantorCabang->longitude;

                // add small random noise to coords
                $lat += (rand(-50, 50) / 100000.0);
                $lon += (rand(-50, 50) / 100000.0);
            }

            try {
                Absensi::create([
                    'user_id' => $userId,
                    'tipe_absensi_id' => $user->tipe_absensi_id ?? null,
                    'kantor_cabang_id' => $user->kantor_cabang_id ?? null,
                    'jam_masuk' => $jamMasuk,
                    'latitude_masuk' => $lat,
                    'longitude_masuk' => $lon,
                    'jarak_masuk' => 0,
                    'jam_keluar' => $jamKeluar,
                    'latitude_keluar' => $lat,
                    'longitude_keluar' => $lon,
                    'jarak_keluar' => 0,
                    'total_jam_kerja' => $totalJam,
                    'status' => 'hadir',
                ]);

                $created++;

                if ($created % 500 === 0) {
                    $this->command->info("Created {$created}/{$target} absensi records...");
                }
            } catch (\Throwable $e) {
                // log and continue
                $this->command->warn("Failed to create absensi record (attempt {$attempts}): " . $e->getMessage());
            }
        }

        $this->command->info("Mass absensi seeding finished. Created: {$created}, Attempts: {$attempts}");
    }
}
