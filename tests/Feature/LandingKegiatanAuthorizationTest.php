<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Permission;

class LandingKegiatanAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_without_permission_gets_403()
    {
        $user = \App\Models\User::factory()->create();

        $res = $this->actingAs($user)->getJson('/admin/api/landing-kegiatan');
        $res->assertStatus(403);
    }

    public function test_user_with_permission_can_access_index()
    {
        Permission::create(['name' => 'view landing kegiatan', 'guard_name' => 'web']);

        $user = \App\Models\User::factory()->create();
        $user->givePermissionTo('view landing kegiatan');

        $res = $this->actingAs($user)->getJson('/admin/api/landing-kegiatan');
        $res->assertStatus(200);
    }
}
