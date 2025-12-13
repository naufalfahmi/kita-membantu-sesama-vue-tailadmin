<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class JabatanDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_jabatan_returns_permissions()
    {
        $role = Role::create(['name' => 'tester', 'guard_name' => 'web']);
        $perm = Permission::create(['name' => 'view program', 'guard_name' => 'web']);
        $role->givePermissionTo($perm);

        $admin = \App\Models\User::factory()->create();
        $res = $this->actingAs($admin)->getJson('/admin/api/jabatan/'.$role->id);
        $res->assertStatus(200);
        $res->assertJsonStructure(['success', 'data' => ['id', 'name', 'permissions']]);
        $this->assertTrue(in_array('view program', $res->json('data.permissions')));
    }
}
