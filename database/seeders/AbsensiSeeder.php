<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Absensi;
use App\Models\User;
use Carbon\Carbon;

class AbsensiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // reset table
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('absensis')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $karyawans = User::karyawan()->where('is_active', true)->get();
        if ($karyawans->isEmpty()) {
            $this->command->warn('No karyawan found to seed absensi.');
            return;
        }

        $recordsPerUser = 300; // 17 karyawan * 300 = ~5100 rows
        $batchSize = 500;
        $today = Carbon::today();
        $insertBatch = [];

        foreach ($karyawans as $user) {
            $tipe = $user->tipeAbsensi;

            $jamMasukBase = $tipe && $tipe->jam_masuk ? $tipe->jam_masuk : '09:00:00';
            $jamKeluarBase = $tipe && $tipe->jam_keluar ? $tipe->jam_keluar : '17:00:00';

            // normalize base times
            $jamMasukTime = $this->safeTime($jamMasukBase, '09:00:00');
            $jamKeluarTime = $this->safeTime($jamKeluarBase, '17:00:00');

            for ($i = 0; $i < $recordsPerUser; $i++) {
                $dayOffset = rand(0, 120); // last 4 months
                $date = $today->copy()->subDays($dayOffset);

                $jamMasukCarbon = Carbon::parse($date->toDateString() . ' ' . $jamMasukTime)->addMinutes(rand(-10, 20));
                $jamKeluarCarbon = Carbon::parse($date->toDateString() . ' ' . $jamKeluarTime)->addMinutes(rand(-20, 40));

                if ($jamKeluarCarbon->lessThanOrEqualTo($jamMasukCarbon)) {
                    $jamKeluarCarbon = $jamMasukCarbon->copy()->addHours(8);
                }

                $totalJam = round($jamKeluarCarbon->diffInMinutes($jamMasukCarbon) / 60, 2);

                $lat = $user->kantorCabang->latitude ?? null;
                $lon = $user->kantorCabang->longitude ?? null;

                $insertBatch[] = [
                    'id' => (string) Str::uuid(),
                    'user_id' => $user->id,
                    'tipe_absensi_id' => $user->tipe_absensi_id,
                    'kantor_cabang_id' => $user->kantor_cabang_id,
                    'jam_masuk' => $jamMasukCarbon,
                    'latitude_masuk' => $lat,
                    'longitude_masuk' => $lon,
                    'jarak_masuk' => 0,
                    'jam_keluar' => $jamKeluarCarbon,
                    'latitude_keluar' => $lat,
                    'longitude_keluar' => $lon,
                    'jarak_keluar' => 0,
                    'total_jam_kerja' => $totalJam,
                    'status' => 'hadir',
                    'catatan' => null,
                    'alasan' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if (count($insertBatch) >= $batchSize) {
                    DB::table('absensis')->insert($insertBatch);
                    $insertBatch = [];
                }
            }
        }

        if (!empty($insertBatch)) {
            DB::table('absensis')->insert($insertBatch);
        }

        $totalRows = Absensi::count();
        $this->command->info("Seeded {$totalRows} absensi rows (status: hadir)");
    }

    private function safeTime(string $value, string $fallback): string
    {
        try {
            return Carbon::parse($value)->format('H:i:s');
        } catch (\Throwable $e) {
            return substr($fallback, 0, 8);
        }
    }

}
