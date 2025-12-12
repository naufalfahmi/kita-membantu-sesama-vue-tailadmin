<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_endpoint_includes_is_admin_and_permissions()
    {
        // Create admin role and a permission
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $perm = Permission::firstOrCreate(['name' => 'create mitra']);

        $user = User::factory()->create();
        $user->assignRole($adminRole);
        $user->givePermissionTo($perm);

        $this->actingAs($user);

        $response = $this->getJson('/admin/api/user');

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure(['success', 'user' => ['id', 'name', 'is_admin', 'permissions']]);

        $data = $response->json('user');
        $this->assertTrue($data['is_admin']);
        $this->assertContains('create mitra', $data['permissions']);
    }
}
