<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProgramShareTypeSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $types = [
            ['name' => 'DP', 'key' => 'dp', 'orders' => 1],
            ['name' => 'OPS 1', 'key' => 'ops_1', 'orders' => 2],
            ['name' => 'OPS 2', 'key' => 'ops_2', 'orders' => 3],
            ['name' => 'Program', 'key' => 'program', 'orders' => 4],
            ['name' => 'Fee Mitra', 'key' => 'fee_mitra', 'orders' => 5],
            ['name' => 'Bonus', 'key' => 'bonus', 'orders' => 6],
            ['name' => 'Championship', 'key' => 'championship', 'orders' => 7],
        ];

        foreach ($types as $t) {
            DB::table('program_share_types')->insert([
                'id' => (string) Str::uuid(),
                'name' => $t['name'],
                'key' => $t['key'],
                'default_type' => 'percentage',
                'orders' => $t['orders'] ?? null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
