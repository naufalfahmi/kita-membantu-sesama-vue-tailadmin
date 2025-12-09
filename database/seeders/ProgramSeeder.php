<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $programs = [
            [
                'id' => '13088e10-2235-4fb4-b2cf-dd3ec6d88ec7',
                'nama_program' => 'Kotak infaq ',
                'created_at' => '2025-05-19 02:52:35',
                'updated_at' => '2025-05-19 03:18:34',
            ],
            [
                'id' => '98946dd6-93da-4547-bcbb-244e8b5a2e39',
                'nama_program' => "Sedekah Qur'an khusus 0320",
                'created_at' => '2023-08-22 02:30:47',
                'updated_at' => '2023-08-22 02:30:47',
            ],
        ];

        foreach ($programs as $p) {
            DB::table('program')->updateOrInsert(
                ['id' => $p['id']],
                $p
            );
        }

        $this->command->info('Seeded ' . count($programs) . ' programs');
    }
}
