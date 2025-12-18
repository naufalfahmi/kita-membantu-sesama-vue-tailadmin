<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class RemunerasiPermissionsSeeder extends Seeder
{
    public function run()
    {
        $perms = [
            // legacy
            'view remunerasi',
            'create remunerasi',
            'update remunerasi',
            'delete remunerasi',
            'generate remunerasi',
            'manage remunerasi',
            'transfer remunerasi',

            // payroll (preferred naming)
            'view payroll',
            'create payroll',
            'update payroll',
            'delete payroll',
            'generate payroll',
            'manage payroll',
            'transfer payroll',
        ];

        foreach ($perms as $p) {
            Permission::firstOrCreate(['name' => $p]);
        }
    }
}
