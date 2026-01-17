<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class TipeProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Use upsert so running seeds multiple times won't create duplicates
        DB::table('tipe_program')->upsert([
            [
                'id' => (string) Str::uuid(),
                'name' => 'Mitra',
                'orders' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Zisco',
                'orders' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ], ['name'], ['orders', 'updated_at']);
    }
}
