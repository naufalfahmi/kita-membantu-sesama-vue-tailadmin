<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class MenuPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Company permissions
        Permission::firstOrCreate(['name' => 'view landing profile']);
        Permission::firstOrCreate(['name' => 'view landing kegiatan']);
        Permission::firstOrCreate(['name' => 'view landing program']);
        Permission::firstOrCreate(['name' => 'view landing proposal']);
        Permission::firstOrCreate(['name' => 'view landing bulletin']);

        // Administrasi permissions
        Permission::firstOrCreate(['name' => 'view kantor cabang']);
        Permission::firstOrCreate(['name' => 'view program']);
        Permission::firstOrCreate(['name' => 'view jabatan']);
        Permission::firstOrCreate(['name' => 'view pangkat']);
        Permission::firstOrCreate(['name' => 'view tipe absensi']);
        Permission::firstOrCreate(['name' => 'view gaji']);
        Permission::firstOrCreate(['name' => 'view tipe donatur']);
        Permission::firstOrCreate(['name' => 'view form pesan']);
        Permission::firstOrCreate(['name' => 'view kelembagaan']);
        Permission::firstOrCreate(['name' => 'view sop']);
        Permission::firstOrCreate(['name' => 'view aturan kepegawaian']);

        // Konten & Publikasi permissions
        Permission::firstOrCreate(['name' => 'view program kami']);
        Permission::firstOrCreate(['name' => 'view profile kami']);
        Permission::firstOrCreate(['name' => 'view proposal data']);
        Permission::firstOrCreate(['name' => 'view bulletin data']);

        // User & Kepegawaian permissions
        Permission::firstOrCreate(['name' => 'view user']);
        Permission::firstOrCreate(['name' => 'view karyawan']);
        Permission::firstOrCreate(['name' => 'view mitra']);
        Permission::firstOrCreate(['name' => 'view donatur']);

        // Operasional permissions
        Permission::firstOrCreate(['name' => 'view absensi']);
        Permission::firstOrCreate(['name' => 'view remunerasi']);

        // Keuangan permissions
        Permission::firstOrCreate(['name' => 'view finansial']);
        Permission::firstOrCreate(['name' => 'view transaksi']);
        Permission::firstOrCreate(['name' => 'view penyaluran']);
        Permission::firstOrCreate(['name' => 'view pengajuan dana']);
        Permission::firstOrCreate(['name' => 'view keuangan']);

        // Laporan permissions
        Permission::firstOrCreate(['name' => 'view laporan transaksi']);
        Permission::firstOrCreate(['name' => 'view laporan keuangan']);

        $this->command->info('Menu permissions created successfully!');
    }
}



