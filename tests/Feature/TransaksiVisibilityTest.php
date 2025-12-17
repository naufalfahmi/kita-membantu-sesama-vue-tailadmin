<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Transaksi;
use App\Models\User;
use App\Models\KantorCabang;

class TransaksiVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_leader_sees_only_their_own_transaksi()
    {
        $kantor = KantorCabang::create(['kode' => 'KC10', 'nama' => 'Cabang X']);

        $userA = User::factory()->create(['kantor_cabang_id' => $kantor->id, 'tipe_user' => 'karyawan']);
        $userB = User::factory()->create(['kantor_cabang_id' => $kantor->id, 'tipe_user' => 'karyawan']);

        $don1 = \App\Models\Donatur::create(['kode' => 'D1', 'nama' => 'Donatur 1', 'jenis_donatur' => 'perorangan']);
        $prog1 = \App\Models\Program::create(['nama_program' => 'Program 1']);

        Transaksi::create(['kantor_cabang_id' => $kantor->id, 'donatur_id' => $don1->id, 'program_id' => $prog1->id, 'nominal' => 100, 'tanggal_transaksi' => now(), 'created_by' => $userA->id, 'kode' => 'T1']);
        Transaksi::create(['kantor_cabang_id' => $kantor->id, 'donatur_id' => $don1->id, 'program_id' => $prog1->id, 'nominal' => 200, 'tanggal_transaksi' => now(), 'created_by' => $userB->id, 'kode' => 'T2']);

        $res = $this->actingAs($userA)->getJson('/admin/api/transaksi?per_page=10');
        $res->assertStatus(200);
        $data = $res->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('T1', $data[0]['kode']);
    }

    public function test_leader_sees_subordinates_transactions()
    {
        $kantor = KantorCabang::create(['kode' => 'KC11', 'nama' => 'Cabang Y']);

        $leader = User::factory()->create(['kantor_cabang_id' => $kantor->id, 'tipe_user' => 'karyawan']);
        $sub1 = User::factory()->create(['leader_id' => $leader->id, 'kantor_cabang_id' => $kantor->id, 'tipe_user' => 'karyawan']);
        $sub2 = User::factory()->create(['leader_id' => $leader->id, 'kantor_cabang_id' => $kantor->id, 'tipe_user' => 'karyawan']);

        $don2 = \App\Models\Donatur::create(['kode' => 'D2', 'nama' => 'Donatur 2', 'jenis_donatur' => 'perorangan']);
        $prog2 = \App\Models\Program::create(['nama_program' => 'Program 2']);

        Transaksi::create(['kantor_cabang_id' => $kantor->id, 'donatur_id' => $don2->id, 'program_id' => $prog2->id, 'nominal' => 100, 'tanggal_transaksi' => now(), 'created_by' => $leader->id, 'kode' => 'TL']);
        Transaksi::create(['kantor_cabang_id' => $kantor->id, 'donatur_id' => $don2->id, 'program_id' => $prog2->id, 'nominal' => 150, 'tanggal_transaksi' => now(), 'created_by' => $sub1->id, 'kode' => 'TS1']);
        Transaksi::create(['kantor_cabang_id' => $kantor->id, 'donatur_id' => $don2->id, 'program_id' => $prog2->id, 'nominal' => 200, 'tanggal_transaksi' => now(), 'created_by' => $sub2->id, 'kode' => 'TS2']);

        $res = $this->actingAs($leader)->getJson('/admin/api/transaksi?per_page=10');
        $res->assertStatus(200);
        $data = $res->json('data');
        $kodes = array_map(fn($r) => $r['kode'], $data);
        $this->assertContains('TL', $kodes);
        $this->assertContains('TS1', $kodes);
        $this->assertContains('TS2', $kodes);
    }

    public function test_user_who_is_pic_sees_transaksi_even_if_not_creator()
    {
        $kantor = KantorCabang::create(['kode' => 'KC12', 'nama' => 'Cabang PIC TX']);

        $creator = User::factory()->create(['kantor_cabang_id' => $kantor->id, 'tipe_user' => 'karyawan']);
        $picUser = User::factory()->create(['kantor_cabang_id' => $kantor->id, 'tipe_user' => 'karyawan']);

        $don = \App\Models\Donatur::create(['kode' => 'DPIC', 'nama' => 'Donatur PIC TX', 'jenis_donatur' => 'perorangan', 'pic' => $picUser->id]);
        $prog = \App\Models\Program::create(['nama_program' => 'Program TX']);

        $trx = Transaksi::create([
            'kantor_cabang_id' => $kantor->id,
            'donatur_id' => $don->id,
            'program_id' => $prog->id,
            'nominal' => 500,
            'tanggal_transaksi' => now(),
            'created_by' => $creator->id,
            'kode' => 'TPIC'
        ]);

        // Index should include trx for PIC
        $res = $this->actingAs($picUser)->getJson('/admin/api/transaksi?per_page=10');
        $res->assertStatus(200);
        $data = $res->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals('TPIC', $data[0]['kode']);

        // Show should also be accessible
        $show = $this->actingAs($picUser)->getJson('/admin/api/transaksi/' . $trx->id);
        $show->assertStatus(200)->assertJson(['success' => true]);
    }
}
