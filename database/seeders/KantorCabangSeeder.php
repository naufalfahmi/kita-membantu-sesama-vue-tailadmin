<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KantorCabangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branches = [
            [
                'id' => '3c8a8ede-5aef-438a-9130-a6cfb1b09ee2',
                'kode' => 'kms004',
                'nama' => 'Kantor Cabang Jakarta',
                'alamat' => 'Jl. Manggarai Selatan II Manggarai Jakarta Selatan',
                'kelurahan' => 'Manggarai',
                'kecamatan' => 'Tebet',
                'kota' => 'Jakarta Selatan',
                'provinsi' => 'DKI Jakarta',
                'kode_pos' => '12850',
                'latitude' => '-6.215626705410642',
                'longitude' => '106.85435525644948',
                'created_at' => '2023-01-31 06:44:14',
                'updated_at' => '2023-01-31 06:44:14',
            ],
            [
                'id' => 'd0435799-87c8-410c-8f71-7af3a1d5c799',
                'kode' => 'kms003',
                'nama' => 'Kantor Cabang Bogor',
                'alamat' => 'Jl. Anggrek III No. 27 Kp. Sukamulya Ciomas Bogor Jawa Barat',
                'kelurahan' => 'Ciomas',
                'kecamatan' => 'Ciomas',
                'kota' => 'Kabupaten Bogor',
                'provinsi' => 'Jawa Barat',
                'kode_pos' => '16610',
                'latitude' => '-6.603759233067166',
                'longitude' => '106.76734466774877',
                'created_at' => '2023-01-31 06:38:26',
                'updated_at' => '2023-01-31 06:38:26',
            ],
            [
                'id' => '99425ce7-e52b-4756-a871-d4c91154d2e1',
                'kode' => 'kms002',
                'nama' => 'Kantor Cabang Depok',
                'alamat' => 'Jl. Sawangan Elok Blok C1/7 Duren Seribu Bojong Sari Depok',
                'kelurahan' => 'Duren Seribu',
                'kecamatan' => 'Bojong Sari',
                'kota' => 'Depok',
                'provinsi' => 'Jawa Barat',
                'kode_pos' => '16518',
                'latitude' => '-6.42829552455504',
                'longitude' => '106.73666458250142',
                'created_at' => '2023-01-31 06:30:24',
                'updated_at' => '2023-01-31 06:30:24',
            ],
            [
                'id' => 'b6dcc143-3ca7-4164-8bbf-18fb32ecf337',
                'kode' => 'kms001',
                'nama' => 'Kantor Pusat',
                'alamat' => 'Jl. Flamboyan No.96 Komp. Alam Sinarsari Dramaga Bogor',
                'kelurahan' => 'Sinar Sari',
                'kecamatan' => 'Dramaga',
                'kota' => 'Bogor',
                'provinsi' => 'Jawa Barat',
                'kode_pos' => '16680',
                'latitude' => '-6.582624283349205',
                'longitude' => '106.73123220578032',
                'created_at' => '2023-01-31 06:13:35',
                'updated_at' => '2023-01-31 06:13:35',
            ],
        ];

        foreach ($branches as $b) {
            DB::table('kantor_cabang')->updateOrInsert(
                ['id' => $b['id']],
                $b
            );
        }

        $this->command->info('Seeded ' . count($branches) . ' kantor cabang');
    }
}
