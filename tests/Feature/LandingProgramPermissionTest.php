<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\LandingProgram;
use App\Models\User;

class LandingProgramPermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_requires_permission()
    {
        $user = User::factory()->create();

        $payload = ['name' => 'Test Program', 'description' => 'Desc'];

        $response = $this->actingAs($user)->postJson('/admin/api/landing-program', $payload);
        $response->assertStatus(403);

        // Grant permission and try again
        $user->givePermissionTo('create landing program');
        $response = $this->actingAs($user)->postJson('/admin/api/landing-program', $payload);
        $response->assertStatus(201)->assertJson(['success' => true]);
    }

    public function test_delete_requires_permission()
    {
        $owner = User::factory()->create();
        $program = LandingProgram::create(['name' => 'X', 'description' => null, 'created_by' => $owner->id]);

        $user = User::factory()->create();
        $res = $this->actingAs($user)->deleteJson("/admin/api/landing-program/{$program->id}");
        $res->assertStatus(403);

        $user->givePermissionTo('delete landing program');
        $res = $this->actingAs($user)->deleteJson("/admin/api/landing-program/{$program->id}");
        $res->assertOk()->assertJson(['success' => true]);
    }
}
