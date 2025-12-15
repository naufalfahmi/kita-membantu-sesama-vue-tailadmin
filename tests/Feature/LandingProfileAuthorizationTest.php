<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Permission;

class LandingProfileAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_without_permission_cannot_view_landing_profile()
    {
        $user = \App\Models\User::factory()->create();

        // Authenticated users should be able to view the landing profile (read-only)
        $res = $this->actingAs($user)->getJson('/admin/api/company/landing-profile');
        $res->assertStatus(200);
        $res->assertJson(['success' => true]);
    }

    public function test_user_with_permission_can_view_and_update_landing_profile()
    {
        Permission::create(['name' => 'view landing profile', 'guard_name' => 'web']);
        Permission::create(['name' => 'update landing profile', 'guard_name' => 'web']);

        $user = \App\Models\User::factory()->create();
        $user->givePermissionTo('view landing profile');
        $user->givePermissionTo('update landing profile');

        $res = $this->actingAs($user)->getJson('/admin/api/company/landing-profile');
        $res->assertStatus(200);

        $payload = ['title' => 'New Title From Test'];
        $res2 = $this->actingAs($user)->putJson('/admin/api/company/landing-profile', $payload);
        $res2->assertStatus(200);

        $this->assertDatabaseHas('landing_profiles', ['title' => 'New Title From Test']);
    }

    public function test_admin_can_view_landing_profile_without_explicit_permission()
    {
        // create landing profile data
        \App\Models\LandingProfile::create(['title' => 'Admin View Test']);

        // create admin role and assign to user
        $role = \Spatie\Permission\Models\Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $user = \App\Models\User::factory()->create();
        $user->assignRole('admin');

        $res = $this->actingAs($user)->getJson('/admin/api/company/landing-profile');
        $res->assertStatus(200);
        $res->assertJsonPath('data.title', 'Admin View Test');
    }
}
