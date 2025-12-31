<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class ApprovalPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $keuanganModules = [
            'keuangan',
            'penyaluran',
            'pengajuan dana',
        ];

        foreach ($keuanganModules as $module) {
            Permission::firstOrCreate([
                'name' => "approval {$module}",
                'guard_name' => 'web',
            ]);
        }

        // Clear cache
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        $this->command->info('Approval permissions for Keuangan created');
    }
}
