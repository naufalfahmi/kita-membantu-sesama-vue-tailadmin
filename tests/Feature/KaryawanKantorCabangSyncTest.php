<?php

namespace Tests\Feature;

use App\Models\KantorCabang;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KaryawanKantorCabangSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_updating_with_single_kantor_cabang_syncs_pivot()
    {
        // create admin user to authenticate
        $admin = User::factory()->create();

        // create two kantor cabang
        $c1 = KantorCabang::create(['kode' => 'C1', 'nama' => 'Cabang 1']);
        $c2 = KantorCabang::create(['kode' => 'C2', 'nama' => 'Cabang 2']);

        // create karyawan with both kantor cabang assigned via pivot
        $k = User::create([
            'name' => 'Karyawan Test',
            'email' => 'kar@example.com',
            'password' => bcrypt('password123'),
            'tipe_user' => 'karyawan',
            'kantor_cabang_id' => $c1->id,
        ]);
        $k->kantorCabangs()->attach([$c1->id, $c2->id]);

        // verify initial state
        $this->assertCount(2, $k->kantorCabangs);

        // update karyawan to only have c2 via single kantor_cabang_id
        $payload = [
            'name' => 'Karyawan Test',
            'email' => 'kar@example.com',
            'kantor_cabang_id' => $c2->id,
        ];

        $resp = $this->actingAs($admin)->json('PUT', "/admin/api/karyawan/{$k->id}", $payload);
        $resp->assertStatus(200)->assertJson(['success' => true]);

        $k->refresh();
        $ids = $k->kantorCabangs()->pluck('id')->toArray();
        $this->assertCount(1, $ids);
        $this->assertEquals($c2->id, $ids[0]);
        $this->assertEquals($c2->id, $k->kantor_cabang_id);
    }
}
