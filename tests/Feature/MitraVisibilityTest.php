<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Mitra;
use App\Models\KantorCabang;

class MitraVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_leader_sees_only_their_own_mitra()
    {
        $kantor = KantorCabang::create(['kode' => 'KC10', 'nama' => 'Cabang X']);

        $userA = User::factory()->create(['kantor_cabang_id' => $kantor->id, 'tipe_user' => 'karyawan']);
        $userB = User::factory()->create(['kantor_cabang_id' => $kantor->id, 'tipe_user' => 'karyawan']);

        Mitra::create(['nama' => 'Mitra A1', 'kantor_cabang_id' => $kantor->id, 'created_by' => $userA->id]);
        Mitra::create(['nama' => 'Mitra B1', 'kantor_cabang_id' => $kantor->id, 'created_by' => $userB->id]);

        $res = $this->actingAs($userA)->getJson('/admin/api/mitra?per_page=10');
        $res->assertStatus(200);
        $data = $res->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('Mitra A1', $data[0]['nama']);
    }

    public function test_leader_sees_subordinates_and_self()
    {
        $kantor = KantorCabang::create(['kode' => 'KC11', 'nama' => 'Cabang Y']);

        $leader = User::factory()->create(['kantor_cabang_id' => $kantor->id, 'tipe_user' => 'karyawan']);
        $sub1 = User::factory()->create(['leader_id' => $leader->id, 'kantor_cabang_id' => $kantor->id, 'tipe_user' => 'karyawan']);
        $sub2 = User::factory()->create(['leader_id' => $leader->id, 'kantor_cabang_id' => $kantor->id, 'tipe_user' => 'karyawan']);

        Mitra::create(['nama' => 'Mitra L', 'kantor_cabang_id' => $kantor->id, 'created_by' => $leader->id]);
        Mitra::create(['nama' => 'Mitra S1', 'kantor_cabang_id' => $kantor->id, 'created_by' => $sub1->id]);
        Mitra::create(['nama' => 'Mitra S2', 'kantor_cabang_id' => $kantor->id, 'created_by' => $sub2->id]);

        $res = $this->actingAs($leader)->getJson('/admin/api/mitra?per_page=10');
        $res->assertStatus(200);
        $data = $res->json('data');
        $names = array_map(fn($r) => $r['nama'], $data);
        $this->assertContains('Mitra L', $names);
        $this->assertContains('Mitra S1', $names);
        $this->assertContains('Mitra S2', $names);
    }
}
