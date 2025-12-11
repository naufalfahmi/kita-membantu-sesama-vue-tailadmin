<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run menu permissions seeder first
        $this->call(MenuPermissionSeeder::class);
        
        // Run roles/jabatan seeder
        $this->call(JabatanSeeder::class);

        // Run admin user seeder
        $this->call(AdminUserSeeder::class);
        
        // Seed pangkat
        $this->call(PangkatSeeder::class);
        
        // Seed program
        $this->call(ProgramSeeder::class);

        // Seed tipe donatur
        $this->call(TipeDonaturSeeder::class);

        // Seed tipe absensi
        $this->call(TipeAbsensiSeeder::class);
        
        // Seed kantor cabang
        $this->call(KantorCabangSeeder::class);
        
        // Seed mitra
        $this->call(MitraSeeder::class);
        
        // Seed karyawan
        $this->call(KaryawanSeeder::class);

        // Seed absensi for karyawan
        $this->call(AbsensiSeeder::class);

        // Seed transaksi (prefer fundraiser as fundraiser_id)
        $this->call(TransaksiSeeder::class);
        
        // Seed landing kegiatan
        $this->call(LandingKegiatanSeeder::class);

        // Seed landing program
        $this->call(LandingProgramSeeder::class);
        
        // Seed landing proposal
        $this->call(LandingProposalSeeder::class);
        
        // Seed landing profile
        $this->call(\Database\Seeders\LandingProfileSeeder::class);
        
    }
}
