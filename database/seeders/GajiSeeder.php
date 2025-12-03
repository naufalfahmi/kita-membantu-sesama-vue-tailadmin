<?php

namespace Database\Seeders;

use App\Models\Gaji;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GajiSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $samples = [
            ['nama' => 'Gaji Pokok Golongan I', 'nominal' => 3000000, 'tipe' => 'bulanan', 'jabatan_id' => null, 'pangkat_id' => null],
            ['nama' => 'Gaji Pokok Golongan II', 'nominal' => 4500000, 'tipe' => 'bulanan', 'jabatan_id' => null, 'pangkat_id' => null],
            ['nama' => 'Tunjangan Transport', 'nominal' => 250000, 'tipe' => 'tunjangan', 'jabatan_id' => null, 'pangkat_id' => null],
            ['nama' => 'Bonus Kinerja', 'nominal' => 1000000, 'tipe' => 'bonus', 'jabatan_id' => null, 'pangkat_id' => null],
        ];

        foreach ($samples as $s) {
            Gaji::create($s);
        }
    }
}
