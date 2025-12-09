<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PangkatSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'id' => 'e1b0d2b4-1b1e-4af1-a103-cf18fd740a33',
                'nama' => 'Oficer',
            ],
            [
                'id' => '62e4ab5a-38a3-4cbb-9c4f-469e9ba3903a',
                'nama' => 'Supervisor',
            ],
            [
                'id' => '624e2e7b-c5bd-4177-a915-440ebab13cf8',
                'nama' => 'Manager',
            ],
            [
                'id' => '62c21575-a5b3-452f-819b-7a666ab943f9',
                'nama' => 'Direktur',
            ],
        ];

        foreach ($data as $row) {
            DB::table('pangkats')->updateOrInsert(
                ['id' => $row['id']],
                $row
            );
        }
    }
}
