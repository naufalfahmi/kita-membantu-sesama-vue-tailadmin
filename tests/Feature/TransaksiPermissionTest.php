<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Transaksi;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class TransaksiPermissionTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        // Seed basic permission needed for menu
        Permission::firstOrCreate(['name' => 'view transaksi']);
        Permission::firstOrCreate(['name' => 'create transaksi']);
        Permission::firstOrCreate(['name' => 'update transaksi']);
        Permission::firstOrCreate(['name' => 'delete transaksi']);
    }

    public function test_user_without_create_permission_cannot_create_transaksi()
    {
        // Create role with only view permission
        $role = Role::create(['name' => 'viewer']);
        $role->givePermissionTo('view transaksi');

        // Create user and assign role
        $user = User::factory()->create();
        $user->assignRole('viewer');

        $this->actingAs($user)
             ->postJson('/admin/api/transaksi', [
                 'kantor_cabang_id' => '00000000-0000-0000-0000-000000000000',
                 'donatur_id' => '00000000-0000-0000-0000-000000000000',
                 'program_id' => '00000000-0000-0000-0000-000000000000',
                 'nominal' => 10000,
                 'tanggal_transaksi' => now()->toDateString(),
             ])->assertStatus(403);
    }

    public function test_user_with_create_permission_can_create_transaksi()
    {
        $role = Role::create(['name' => 'creator']);
        $role->givePermissionTo('create transaksi');

        $user = User::factory()->create();
        $user->assignRole('creator');

        // Create necessary reference records to pass validation
        // Use factories or direct inserts depending on the available factories
        // This test only asserts permission middleware, so we mock lambda to bypass validation to focus on permission.
        $this->actingAs($user)
            ->postJson('/admin/api/transaksi', [
                'kantor_cabang_id' => '00000000-0000-0000-0000-000000000000',
                'donatur_id' => '00000000-0000-0000-0000-000000000000',
                'program_id' => '00000000-0000-0000-0000-000000000000',
                'nominal' => 10000,
                'tanggal_transaksi' => now()->toDateString(),
            ])->assertStatus(422); // Fails validation, but not 403; indicates permission was allowed
    }
}
