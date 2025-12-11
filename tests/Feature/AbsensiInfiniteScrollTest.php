<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AbsensiInfiniteScrollTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_slice_and_total_for_infinite_scroll()
    {
        $user = User::factory()->create();

        // Create 60 absensi entries
        for ($i = 0; $i < 60; $i++) {
            Absensi::create([
                'user_id' => $user->id,
                'jam_masuk' => now()->subDays($i),
            ]);
        }

        $res = $this->actingAs($user)->getJson('/admin/api/absensi?start=0&limit=10');

        $res->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonCount(10, 'data')
            ->assertJsonStructure(['success', 'data', 'total']);

        $this->assertEquals(60, $res->json('total'));
    }

    /** @test */
    public function it_filters_by_single_date_parameter()
    {
        $user = User::factory()->create();

        // Create some absensi entries with today and yesterday
        Absensi::create(['user_id' => $user->id, 'jam_masuk' => now()]);
        Absensi::create(['user_id' => $user->id, 'jam_masuk' => now()->subDay()]);

        $today = now()->format('Y-m-d');
        $res = $this->actingAs($user)->getJson('/admin/api/absensi?start=0&limit=10&date=' . $today);

        $res->assertStatus(200)->assertJson(['success' => true]);
        $this->assertEquals(1, count($res->json('data')));
    }
}
