<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProgramShareTypeAliasSeeder extends Seeder
{
    public function run(): void
    {
        // Update existing share types with default aliases
        $mappings = [
            'program' => 'Program',
            'ops_1' => 'Gaji Karyawan',
            'ops_2' => 'Operasional',
        ];

        foreach ($mappings as $key => $alias) {
            DB::table('program_share_types')
                ->where('key', $key)
                ->whereNull('deleted_at')
                ->update(['alias' => $alias]);
        }

        // Other keys (dp, fee_mitra, bonus, championship, etc) remain NULL
        // so they won't appear in submission type dropdown
    }
}
