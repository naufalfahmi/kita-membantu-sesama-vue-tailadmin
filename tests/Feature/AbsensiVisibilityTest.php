<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Absensi;
use App\Models\KantorCabang;

class AbsensiVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_leader_sees_only_their_own_absensi()
    {
        $kantor = KantorCabang::create(['kode' => 'KC01', 'nama' => 'Cabang 1']);

        $userA = User::factory()->create(['kantor_cabang_id' => $kantor->id, 'tipe_user' => 'karyawan']);
        $userB = User::factory()->create(['kantor_cabang_id' => $kantor->id, 'tipe_user' => 'karyawan']);

        Absensi::create(['user_id' => $userA->id, 'kantor_cabang_id' => $kantor->id, 'jam_masuk' => now(), 'created_by' => $userA->id]);
        Absensi::create(['user_id' => $userB->id, 'kantor_cabang_id' => $kantor->id, 'jam_masuk' => now(), 'created_by' => $userB->id]);

        $res = $this->actingAs($userA)->getJson('/admin/api/absensi?per_page=10');
        $res->assertStatus(200);
        $data = $res->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals($userA->email, $data[0]['user']['email']);
    }

    public function test_leader_sees_subordinates_and_self()
    {
        $kantor = KantorCabang::create(['kode' => 'KC02', 'nama' => 'Cabang 2']);

        $leader = User::factory()->create(['kantor_cabang_id' => $kantor->id, 'tipe_user' => 'karyawan']);
        $sub1 = User::factory()->create(['leader_id' => $leader->id, 'kantor_cabang_id' => $kantor->id, 'tipe_user' => 'karyawan']);
        $sub2 = User::factory()->create(['leader_id' => $leader->id, 'kantor_cabang_id' => $kantor->id, 'tipe_user' => 'karyawan']);

        Absensi::create(['user_id' => $leader->id, 'kantor_cabang_id' => $kantor->id, 'jam_masuk' => now(), 'created_by' => $leader->id]);
        Absensi::create(['user_id' => $sub1->id, 'kantor_cabang_id' => $kantor->id, 'jam_masuk' => now(), 'created_by' => $sub1->id]);
        Absensi::create(['user_id' => $sub2->id, 'kantor_cabang_id' => $kantor->id, 'jam_masuk' => now(), 'created_by' => $sub2->id]);

        $res = $this->actingAs($leader)->getJson('/admin/api/absensi?per_page=10');
        $res->assertStatus(200);
        $data = $res->json('data');
        $ids = array_map(fn($r) => $r['user']['id'], $data);
        $this->assertContains($leader->id, $ids);
        $this->assertContains($sub1->id, $ids);
        $this->assertContains($sub2->id, $ids);
    }
}
