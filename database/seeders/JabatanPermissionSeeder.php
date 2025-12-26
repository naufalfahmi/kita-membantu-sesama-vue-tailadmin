<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class JabatanPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define menu structure with sub items
        $menuStructure = [
            'Company' => [
                'Landing Profile' => 'landing profile',
                'Landing Kegiatan' => 'landing kegiatan',
                'Landing Program' => 'landing program',
                'Landing Proposal' => 'landing proposal',
                'Landing Bulletin' => 'landing bulletin',
            ],
            'Administrasi' => [
                'Kantor Cabang' => 'kantor cabang',
                'Program' => 'program',
                'Jabatan' => 'jabatan',
                'Pangkat' => 'pangkat',
                'Tipe Absensi' => 'tipe absensi',
                'Gaji' => 'gaji',
                'Tipe Donatur' => 'tipe donatur',
                'Form Pesan' => 'form pesan',
                'SOP' => 'sop',
                'Aturan Kepegawaian' => 'aturan kepegawaian',
            ],
            'Konten & Publikasi' => [
                'Program Kami' => 'program kami',
                'Profile Kami' => 'profile kami',
                'Proposal Data' => 'proposal data',
                'Bulletin Data' => 'bulletin data',
            ],
            'User & Kepegawaian' => [
                'Karyawan' => 'karyawan',
                'Mitra' => 'mitra',
                'Payroll Mitra' => 'payroll mitra',
                'Donatur' => 'donatur',
            ],
            'Operasional' => [
                'Absensi' => 'absensi',
                'Remunerasi' => 'remunerasi',
            ],
            'Keuangan' => [
                'Transaksi' => 'transaksi',
                'Penyaluran' => 'penyaluran',
                'Pengajuan Dana' => 'pengajuan dana',
                'Keuangan' => 'keuangan',
            ],
            'Laporan' => [
                'Laporan Transaksi' => 'laporan transaksi',
                'Laporan Keuangan' => 'laporan keuangan',
            ],
        ];

        $actions = ['view', 'create', 'update', 'show', 'delete'];

        // Create permissions for each menu item and action
        foreach ($menuStructure as $mainMenu => $subItems) {
            foreach ($subItems as $subMenuName => $permissionName) {
                foreach ($actions as $action) {
                    $permission = "{$action} {$permissionName}";
                    Permission::firstOrCreate([
                        'name' => $permission,
                        'guard_name' => 'web',
                    ]);
                }
            }
        }

        $totalPermissions = array_sum(array_map('count', $menuStructure)) * count($actions);
        $this->command->info('Jabatan permissions created successfully!');
        $this->command->info("Total permissions: {$totalPermissions}");
    }
}
