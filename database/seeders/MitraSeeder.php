<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MitraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mitras = [
            [
                'id' => 'a528a033-0839-4d64-a3eb-bafdb635cad8',
                'nama' => 'Aswalludin',
                'no_handphone' => '6281210480765',
                'email' => 'aswalludin@gmail.com',
                'nama_bank' => 'BSI',
                'no_rekening' => '567890987',
                'tanggal_lahir' => '2023-01-01',
                'pendidikan' => 'S2',
                'kantor_cabang_id' => '99425ce7-e52b-4756-a871-d4c91154d2e1',
                'created_at' => '2023-02-01 06:17:58',
                'updated_at' => '2023-02-01 06:17:58',
            ],
            [
                'id' => 'dbb09f8e-3d1f-456c-9866-406ec2669523',
                'nama' => 'Budiman',
                'no_handphone' => '62817214843',
                'email' => 'budi.taajir@gmail.com',
                'nama_bank' => 'BSI',
                'no_rekening' => '98098709',
                'tanggal_lahir' => '2023-01-01',
                'pendidikan' => 'S1',
                'kantor_cabang_id' => '99425ce7-e52b-4756-a871-d4c91154d2e1',
                'created_at' => '2023-02-01 06:01:34',
                'updated_at' => '2023-02-01 06:01:34',
            ],
            [
                'id' => '52c9c1c9-c502-4b33-84b0-3525024f2f61',
                'nama' => 'Ida Parida',
                'no_handphone' => '6289603156181',
                'email' => 'su_ri23@yahoo.com',
                'nama_bank' => 'BSI',
                'no_rekening' => '98765876',
                'tanggal_lahir' => '1978-08-18',
                'pendidikan' => 'S2',
                'kantor_cabang_id' => 'd0435799-87c8-410c-8f71-7af3a1d5c799',
                'created_at' => '2023-02-01 05:32:12',
                'updated_at' => '2023-02-01 05:55:39',
            ],
        ];

        foreach ($mitras as $m) {
            DB::table('mitras')->updateOrInsert(
                ['id' => $m['id']],
                $m
            );
        }

        $this->command->info('Seeded ' . count($mitras) . ' mitra records');
    }
}
