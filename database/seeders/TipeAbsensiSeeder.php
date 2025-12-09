<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipeAbsensiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'id' => '0e44a46d-7e39-486e-864f-5d8c38532ef7',
                'kode' => 'guru_pondok_tahfizh',
                'nama' => 'Guru Pondok Tahfizh',
                'jam_masuk' => '15:00:00',
                'jam_keluar' => '18:00:00',
            ],
            [
                'id' => '00356e90-c673-4d24-a786-aee985b0c038',
                'kode' => 'karyawan_umum',
                'nama' => 'Karyawan Umum',
                'jam_masuk' => '09:00:00',
                'jam_keluar' => '17:00:00',
            ],
        ];

        foreach ($data as $row) {
            DB::table('tipe_absensi')->updateOrInsert(
                ['id' => $row['id']],
                $row
            );
        }
    }
}
