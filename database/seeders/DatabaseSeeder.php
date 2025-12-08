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
        
        // Run admin user seeder
        $this->call(AdminUserSeeder::class);
        
        // Seed pangkat
        $this->call(PangkatSeeder::class);
        
        // Seed gaji
        // $this->call(GajiSeeder::class);
    }
}
