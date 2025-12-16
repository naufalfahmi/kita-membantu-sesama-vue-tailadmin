<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Absensi;
use App\Models\User;

class AbsensiPermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_sees_only_self_and_subordinates()
    {
        $leader = User::factory()->create();
        $subordinate = User::factory()->create(['leader_id' => $leader->id]);
        $other = User::factory()->create();

        Absensi::create(['user_id' => $leader->id, 'jam_masuk' => '2025-12-01 08:00:00', 'created_by' => $leader->id]);
        Absensi::create(['user_id' => $subordinate->id, 'jam_masuk' => '2025-12-01 08:10:00', 'created_by' => $subordinate->id]);
        Absensi::create(['user_id' => $other->id, 'jam_masuk' => '2025-12-01 08:20:00', 'created_by' => $other->id]);

        $res = $this->actingAs($leader)->getJson('/admin/api/absensi?per_page=100');
        $res->assertOk()->assertJson(['success' => true]);
        $data = $res->json('data');
        $this->assertCount(2, $data);
        $userIds = array_column($data, 'user_id');
        $this->assertContains($leader->id, $userIds);
        $this->assertContains($subordinate->id, $userIds);
        $this->assertNotContains($other->id, $userIds);
    }

    public function test_admin_can_see_all_absensi()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        Absensi::create(['user_id' => $user1->id, 'jam_masuk' => '2025-12-01 08:00:00', 'created_by' => $user1->id]);
        Absensi::create(['user_id' => $user2->id, 'jam_masuk' => '2025-12-01 08:10:00', 'created_by' => $user2->id]);

        $res = $this->actingAs($admin)->getJson('/admin/api/absensi?per_page=100');
        $res->assertOk()->assertJson(['success' => true]);
        $data = $res->json('data');
        $this->assertCount(2, $data);
    }
}
