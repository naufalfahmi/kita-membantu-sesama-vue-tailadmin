<?php

namespace Database\Seeders;

use App\Models\Pangkat;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PangkatSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Create some sample pangkat data
        $pangkats = [
            ['id' => (string) Str::uuid(), 'nama' => 'Pangkat I', 'created_by' => 1],
            ['id' => (string) Str::uuid(), 'nama' => 'Pangkat II', 'created_by' => 1],
            ['id' => (string) Str::uuid(), 'nama' => 'Pangkat III', 'created_by' => 1],
        ];

        foreach ($pangkats as $p) {
            Pangkat::updateOrCreate(['id' => $p['id']], $p);
        }
    }
}
