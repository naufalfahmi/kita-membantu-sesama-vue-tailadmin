<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Pangkat;
use App\Models\KantorCabang;
use Spatie\Permission\Models\Role;

class KaryawanFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_filters_by_name_and_no_induk()
    {
        $kantor = KantorCabang::create(['kode' => 'KC30', 'nama' => 'Cabang A']);

        $alice = User::factory()->create(['name' => 'Alice', 'no_induk' => 'K001', 'kantor_cabang_id' => $kantor->id, 'tipe_user' => 'karyawan']);
        $bob = User::factory()->create(['name' => 'Bob', 'no_induk' => 'K002', 'kantor_cabang_id' => $kantor->id, 'tipe_user' => 'karyawan']);

        $admin = User::factory()->create();
        $res = $this->actingAs($admin)->getJson('/admin/api/karyawan?search=Alice&per_page=10');
        $res->assertStatus(200);
        $data = $res->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('Alice', $data[0]['name']);

        $res2 = $this->actingAs($admin)->getJson('/admin/api/karyawan?search=K002&per_page=10');
        $res2->assertStatus(200);
        $data2 = $res2->json('data');
        $this->assertCount(1, $data2);
        $this->assertEquals('Bob', $data2[0]['name']);
    }

    public function test_filter_by_pangkat()
    {
        $kantor = KantorCabang::create(['kode' => 'KC31', 'nama' => 'Cabang B']);
        $p1 = Pangkat::create(['nama' => 'P1']);
        $p2 = Pangkat::create(['nama' => 'P2']);

        $u1 = User::factory()->create(['name' => 'U1', 'pangkat_id' => $p1->id, 'kantor_cabang_id' => $kantor->id, 'tipe_user' => 'karyawan']);
        $u2 = User::factory()->create(['name' => 'U2', 'pangkat_id' => $p2->id, 'kantor_cabang_id' => $kantor->id, 'tipe_user' => 'karyawan']);

        $admin = User::factory()->create();
        $res = $this->actingAs($admin)->getJson('/admin/api/karyawan?pangkat_id='.$p1->id.'&per_page=10');
        $res->assertStatus(200);
        $data = $res->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('U1', $data[0]['name']);
    }

    public function test_filter_by_role_jabatan()
    {
        $kantor = KantorCabang::create(['kode' => 'KC32', 'nama' => 'Cabang C']);
        $roleA = Role::create(['name' => 'staff']);
        $roleB = Role::create(['name' => 'lead']);

        $u1 = User::factory()->create(['name' => 'R1', 'kantor_cabang_id' => $kantor->id, 'tipe_user' => 'karyawan']);
        $u2 = User::factory()->create(['name' => 'R2', 'kantor_cabang_id' => $kantor->id, 'tipe_user' => 'karyawan']);

        $u1->assignRole($roleA);
        $u2->assignRole($roleB);

        $admin = User::factory()->create();
        $res = $this->actingAs($admin)->getJson('/admin/api/karyawan?role_id='.$roleB->id.'&per_page=10');
        $res->assertStatus(200);
        $data = $res->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('R2', $data[0]['name']);
    }

    public function test_filter_by_leader_and_kantor()
    {
        $kantor = KantorCabang::create(['kode' => 'KC33', 'nama' => 'Cabang D']);
        $leader = User::factory()->create(['name' => 'Lead', 'kantor_cabang_id' => $kantor->id, 'tipe_user' => 'karyawan']);
        $sub = User::factory()->create(['name' => 'Sub', 'leader_id' => $leader->id, 'kantor_cabang_id' => $kantor->id, 'tipe_user' => 'karyawan']);

        $admin = User::factory()->create();
        $res = $this->actingAs($admin)->getJson('/admin/api/karyawan?leader_id='.$leader->id.'&per_page=10');
        $res->assertStatus(200);
        $data = $res->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('Sub', $data[0]['name']);

        $res2 = $this->actingAs($admin)->getJson('/admin/api/karyawan?kantor_cabang_id='.$kantor->id.'&per_page=10');
        $res2->assertStatus(200);
        $data2 = $res2->json('data');
        $this->assertGreaterThanOrEqual(2, count($data2));
    }
}
