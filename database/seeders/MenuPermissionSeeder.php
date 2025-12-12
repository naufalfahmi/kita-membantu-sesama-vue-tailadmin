<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class MenuPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define modules and actions to generate permissions for
        $modules = [
            // Company
            'landing profile', 'landing kegiatan', 'landing program', 'landing proposal', 'landing bulletin',
            // Administrasi
            'kantor cabang', 'program', 'jabatan', 'pangkat', 'tipe absensi', 'gaji', 'tipe donatur', 'form pesan', 'kelembagaan', 'sop', 'aturan kepegawaian',
            // Konten & Publikasi
            'program kami', 'profile kami', 'proposal data', 'bulletin data',
            // User & Kepegawaian
            'user', 'karyawan', 'mitra', 'donatur',
            // Operasional
            'absensi', 'remunerasi',
            // Keuangan
            'finansial', 'transaksi', 'penyaluran', 'pengajuan dana', 'keuangan',
            // Laporan
            'laporan transaksi', 'laporan keuangan',
        ];

        $actions = ['view', 'create', 'update', 'show', 'delete'];

        foreach ($modules as $module) {
            foreach ($actions as $action) {
                Permission::firstOrCreate([
                    'name' => "{$action} {$module}",
                    'guard_name' => 'web',
                ]);
            }
        }

        // Clear permission cache after creating
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        $this->command->info('CRUD menu permissions created successfully!');

        // Ensure admin role has all permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions(Permission::all());
        $this->command->info('Admin role synced with all permissions');
    }
}




