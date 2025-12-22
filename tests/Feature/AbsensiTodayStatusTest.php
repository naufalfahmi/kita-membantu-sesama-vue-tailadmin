<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\KantorCabang;

class AbsensiTodayStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_today_status_without_param_returns_first_pivot()
    {
        $user = User::factory()->create();

        $k1 = KantorCabang::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'kode' => 'TS1',
            'nama' => 'Test Cabang 1',
            'alamat' => 'Test',
            'latitude' => -6.2,
            'longitude' => 106.8,
            'radius' => 1000,
        ]);

        $k2 = KantorCabang::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'kode' => 'TS2',
            'nama' => 'Test Cabang 2',
            'alamat' => 'Test',
            'latitude' => -6.21,
            'longitude' => 106.81,
            'radius' => 1000,
        ]);

        $user->kantorCabangs()->sync([$k1->id, $k2->id]);

        $response = $this->actingAs($user)
            ->getJson('/admin/api/absensi/today-status');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $payload = $response->json('data');
        $this->assertNotNull($payload['kantor_cabang']);
        $this->assertEquals($k1->id, $payload['kantor_cabang']['id']);
    }

    public function test_today_status_with_param_returns_requested_if_assigned()
    {
        $user = User::factory()->create();

        $k1 = KantorCabang::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'kode' => 'TS3',
            'nama' => 'Test Cabang 3',
            'alamat' => 'Test',
            'latitude' => -6.2,
            'longitude' => 106.8,
            'radius' => 1000,
        ]);

        $k2 = KantorCabang::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'kode' => 'TS4',
            'nama' => 'Test Cabang 4',
            'alamat' => 'Test',
            'latitude' => -6.21,
            'longitude' => 106.81,
            'radius' => 1000,
        ]);

        $user->kantorCabangs()->sync([$k1->id, $k2->id]);

        $response = $this->actingAs($user)
            ->getJson('/admin/api/absensi/today-status?kantor_cabang_id=' . $k2->id);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $payload = $response->json('data');
        $this->assertNotNull($payload['kantor_cabang']);
        $this->assertEquals($k2->id, $payload['kantor_cabang']['id']);
    }
}
