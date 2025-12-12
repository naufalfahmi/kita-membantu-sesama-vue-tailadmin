<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class MenuPermissionSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_menu_permission_seeder_creates_crud_permissions()
    {
        // Run the seeder
        $this->seed(\Database\Seeders\MenuPermissionSeeder::class);

        // Check a few permissions exist
        $this->assertDatabaseHas('permissions', ['name' => 'create mitra']);
        $this->assertDatabaseHas('permissions', ['name' => 'delete laporan transaksi']);
        $this->assertDatabaseHas('permissions', ['name' => 'show landing profile']);

        // Ensure number of permissions is at least modules * actions (sanity)
        $permissionsCount = Permission::count();
        $this->assertGreaterThanOrEqual( (int) (20 * 5), $permissionsCount );
    }

    public function test_admin_role_gets_all_permissions_after_admin_seeder()
    {
        // Run the menu seeder and admin seeder
        $this->seed(\Database\Seeders\MenuPermissionSeeder::class);
        $this->seed(\Database\Seeders\AdminUserSeeder::class);

        $adminRole = Role::where('name', 'admin')->first();
        $this->assertNotNull($adminRole);

        $this->assertEquals(Permission::count(), $adminRole->permissions()->count());
    }
}
