<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\KantorCabang;

class KantorCabangRadiusTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_kantor_with_radius()
    {
        $payload = [
            'nama' => 'Cabang Radius',
            'kota' => 'Jakarta',
            'provinsi' => 'DKI Jakarta',
            'latitude' => -6.2,
            'longitude' => 106.8,
            'radius' => 150,
        ];

        $admin = \App\Models\User::factory()->create();
        $res = $this->actingAs($admin)->postJson('/admin/api/kantor-cabang', $payload);
        $res->assertStatus(201);
        $this->assertDatabaseHas('kantor_cabang', ['nama' => 'Cabang Radius', 'radius' => 150]);
    }

    public function test_update_kantor_radius()
    {
        $kantor = KantorCabang::create(['kode' => 'KC99', 'nama' => 'Old Cabang']);

        $admin = \App\Models\User::factory()->create();
        $res = $this->actingAs($admin)->putJson('/admin/api/kantor-cabang/'.$kantor->id, ['nama' => 'Old Cabang', 'radius' => 250, 'kode' => $kantor->kode]);
        $res->assertStatus(200);
        $this->assertDatabaseHas('kantor_cabang', ['id' => $kantor->id, 'radius' => 250]);
    }

    public function test_index_includes_radius()
    {
        $k = KantorCabang::create(['kode' => 'KC50', 'nama' => 'R Cabang', 'radius' => 321]);
        $admin = \App\Models\User::factory()->create();
        $res = $this->actingAs($admin)->getJson('/admin/api/kantor-cabang');
        $res->assertStatus(200);
        $data = $res->json('data');
        $this->assertNotEmpty($data);
        $found = collect($data)->firstWhere('id', $k->id);
        $this->assertEquals(321, $found['radius']);
    }
}
