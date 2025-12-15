<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Absensi;
use App\Models\User;

class AbsensiSortTest extends TestCase
{
    use RefreshDatabase;

    public function test_sort_by_jam_masuk_asc()
    {
        $user = User::factory()->create();

        Absensi::create(['user_id' => $user->id, 'jam_masuk' => '2025-12-01 08:00:00']);
        Absensi::create(['user_id' => $user->id, 'jam_masuk' => '2025-12-01 09:00:00']);
        Absensi::create(['user_id' => $user->id, 'jam_masuk' => '2025-12-01 07:30:00']);

        $response = $this->actingAs($user)->getJson('/admin/api/absensi?per_page=100&sort_by=jam_masuk&sort_direction=asc');
        $response->assertOk()->assertJson(['success' => true]);
        $data = $response->json('data');
        $this->assertCount(3, $data);
        $this->assertEquals('2025-12-01 07:30:00', $data[0]['jam_masuk']);
    }

    public function test_sort_by_user_name_desc()
    {
        $alice = User::factory()->create(['name' => 'Alice']);
        $bob = User::factory()->create(['name' => 'Bob']);
        $charlie = User::factory()->create(['name' => 'Charlie']);

        Absensi::create(['user_id' => $alice->id, 'jam_masuk' => now()]);
        Absensi::create(['user_id' => $bob->id, 'jam_masuk' => now()]);
        Absensi::create(['user_id' => $charlie->id, 'jam_masuk' => now()]);

        $admin = User::factory()->create();
        $admin->assignRole('admin');
        // make request sorting by user.name desc
        $response = $this->actingAs($admin)->getJson('/admin/api/absensi?per_page=100&sort_by=user.name&sort_direction=desc');
        $response->assertOk()->assertJson(['success' => true]);
        $data = $response->json('data');
        $this->assertCount(3, $data);
        // Descending by name: Charlie, Bob, Alice
        $this->assertEquals('Charlie', $data[0]['user']['name']);
        $this->assertEquals('Bob', $data[1]['user']['name']);
        $this->assertEquals('Alice', $data[2]['user']['name']);
    }

    public function test_infinite_scroll_sort_by_user_name_asc()
    {
        $alice = User::factory()->create(['name' => 'Alice']);
        $bob = User::factory()->create(['name' => 'Bob']);
        $charlie = User::factory()->create(['name' => 'Charlie']);

        Absensi::create(['user_id' => $alice->id, 'jam_masuk' => now()]);
        Absensi::create(['user_id' => $bob->id, 'jam_masuk' => now()]);
        Absensi::create(['user_id' => $charlie->id, 'jam_masuk' => now()]);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->getJson('/admin/api/absensi?start=0&limit=2&sort_by=user.name&sort_direction=asc');
        $response->assertOk()->assertJson(['success' => true]);
        $data = $response->json('data');
        $this->assertCount(2, $data);
        $this->assertEquals('Alice', $data[0]['user']['name']);
        $this->assertEquals('Bob', $data[1]['user']['name']);
    }
}
