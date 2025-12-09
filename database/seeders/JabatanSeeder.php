<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class JabatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['id' => 'e0829cc5-f0f1-40d0-99bf-b3210ccbf8dd', 'name' => 'Customer Service', 'created_at' => '2025-04-24 04:09:31', 'updated_at' => '2025-04-24 04:09:31'],
            ['id' => '721742d5-a7c7-499c-8b7c-a6f2e7a7b1a1', 'name' => 'Guru Pondok Tahfizh', 'created_at' => '2023-01-31 14:28:29', 'updated_at' => '2023-02-27 07:26:58'],
            ['id' => '0a5fdec7-fcb7-476f-9349-73b0b77c8991', 'name' => 'Fundrising', 'created_at' => '2023-01-31 13:28:05', 'updated_at' => '2023-03-01 05:17:04'],
            ['id' => 'de31e310-0595-4c40-abca-b3ccf2572d55', 'name' => 'Program Offficer', 'created_at' => '2023-01-31 08:56:22', 'updated_at' => '2023-07-28 03:57:02'],
            ['id' => '72caba45-e5c7-4826-92f1-96fe99e3a203', 'name' => 'SDM Keuangan', 'created_at' => '2023-01-31 08:45:35', 'updated_at' => '2023-01-31 08:45:35'],
            ['id' => '1771f830-8c0f-4daa-9cbc-ef35f90e3ee2', 'name' => 'Direktur Fundrising', 'created_at' => '2023-01-31 08:45:16', 'updated_at' => '2023-01-31 13:26:17'],
            ['id' => '7233e2ab-0c0d-4458-9bf8-48d749183ada', 'name' => 'Kepala Cabang', 'created_at' => '2023-01-31 08:44:54', 'updated_at' => '2025-05-19 03:10:47'],
            ['id' => 'eca85be2-8339-4772-bbcc-0d70e107ee12', 'name' => 'Admin Cabang', 'created_at' => '2023-01-31 08:43:58', 'updated_at' => '2025-05-19 02:45:50'],
            ['id' => '6f3cfdf3-39f1-4040-a7d2-478be16001a6', 'name' => 'Finance', 'created_at' => '2023-01-31 08:43:39', 'updated_at' => '2023-01-31 08:43:39'],
            ['id' => 'cb9edead-7f62-4ab2-a9d1-6489dc154abb', 'name' => 'Mitra', 'created_at' => '2023-01-31 08:43:09', 'updated_at' => '2023-02-01 05:56:42'],
            // Add more roles if needed
        ];

        foreach ($roles as $r) {
            // Use the Role model and create by name; Spatie roles use integer ids, so we don't set id here.
            Role::firstOrCreate(
                ['name' => $r['name'], 'guard_name' => 'web'],
                ['created_at' => $r['created_at'], 'updated_at' => $r['updated_at']]
            );
        }

        $this->command->info('Jabatan (roles) seeded: ' . count($roles));
    }
}
