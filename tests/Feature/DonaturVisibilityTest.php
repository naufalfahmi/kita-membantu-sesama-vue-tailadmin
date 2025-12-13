<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Donatur;
use App\Models\KantorCabang;

class DonaturVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_leader_sees_only_their_own_donatur()
    {
        $kantor = KantorCabang::create(['kode' => 'KC20', 'nama' => 'Cabang Z']);

        $userA = User::factory()->create(['kantor_cabang_id' => $kantor->id, 'tipe_user' => 'karyawan']);
        $userB = User::factory()->create(['kantor_cabang_id' => $kantor->id, 'tipe_user' => 'karyawan']);

        Donatur::create(['nama' => 'Donatur A1', 'jenis_donatur' => ['komunitas'], 'kantor_cabang_id' => $kantor->id, 'created_by' => $userA->id]);
        Donatur::create(['nama' => 'Donatur B1', 'jenis_donatur' => ['komunitas'], 'kantor_cabang_id' => $kantor->id, 'created_by' => $userB->id]);

        $res = $this->actingAs($userA)->getJson('/admin/api/donatur?per_page=10');
        $res->assertStatus(200);
        $data = $res->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('Donatur A1', $data[0]['nama']);
    }

    public function test_leader_sees_subordinates_and_self()
    {
        $kantor = KantorCabang::create(['kode' => 'KC21', 'nama' => 'Cabang ZZ']);

        $leader = User::factory()->create(['kantor_cabang_id' => $kantor->id, 'tipe_user' => 'karyawan']);
        $sub1 = User::factory()->create(['leader_id' => $leader->id, 'kantor_cabang_id' => $kantor->id, 'tipe_user' => 'karyawan']);
        $sub2 = User::factory()->create(['leader_id' => $leader->id, 'kantor_cabang_id' => $kantor->id, 'tipe_user' => 'karyawan']);

        Donatur::create(['nama' => 'Donatur L', 'jenis_donatur' => ['komunitas'], 'kantor_cabang_id' => $kantor->id, 'created_by' => $leader->id]);
        Donatur::create(['nama' => 'Donatur S1', 'jenis_donatur' => ['komunitas'], 'kantor_cabang_id' => $kantor->id, 'created_by' => $sub1->id]);
        Donatur::create(['nama' => 'Donatur S2', 'jenis_donatur' => ['komunitas'], 'kantor_cabang_id' => $kantor->id, 'created_by' => $sub2->id]);

        $res = $this->actingAs($leader)->getJson('/admin/api/donatur?per_page=10');
        $res->assertStatus(200);
        $data = $res->json('data');
        $names = array_map(fn($r) => $r['nama'], $data);
        $this->assertContains('Donatur L', $names);
        $this->assertContains('Donatur S1', $names);
        $this->assertContains('Donatur S2', $names);
    }
}
