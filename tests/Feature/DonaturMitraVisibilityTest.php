<?php

namespace Tests\Feature;

use App\Models\Donatur;
use App\Models\KantorCabang;
use App\Models\Mitra;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DonaturMitraVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_mitra_user_sees_only_their_donaturs(): void
    {
        $kantor = KantorCabang::create(['kode' => 'KD01', 'nama' => 'Kantor Mitra']);

        $mitraUser = User::factory()->create([
            'email' => 'mitra-visible@example.test',
            'tipe_user' => 'mitra',
        ]);

        $otherMitraUser = User::factory()->create([
            'email' => 'mitra-hidden@example.test',
            'tipe_user' => 'mitra',
        ]);

        $mitra = Mitra::create([
            'nama' => 'Mitra Visible',
            'email' => $mitraUser->email,
            'user_id' => $mitraUser->id,
            'kantor_cabang_id' => $kantor->id,
        ]);

        $otherMitra = Mitra::create([
            'nama' => 'Mitra Hidden',
            'email' => $otherMitraUser->email,
            'user_id' => $otherMitraUser->id,
            'kantor_cabang_id' => $kantor->id,
        ]);

        $mine = Donatur::create([
            'nama' => 'Donatur Milik Mitra',
            'jenis_donatur' => ['komunitas'],
            'kantor_cabang_id' => $kantor->id,
            'mitra_id' => $mitra->id,
            'status' => 'aktif',
        ]);

        Donatur::create([
            'nama' => 'Donatur Mitra Lain',
            'jenis_donatur' => ['komunitas'],
            'kantor_cabang_id' => $kantor->id,
            'mitra_id' => $otherMitra->id,
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($mitraUser)->getJson('/admin/api/donatur?per_page=10');

        $response->assertOk()->assertJson(['success' => true]);
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertSame($mine->id, $data[0]['id']);
    }

    public function test_mitra_user_matches_donaturs_by_email_when_no_user_link(): void
    {
        $kantor = KantorCabang::create(['kode' => 'KD02', 'nama' => 'Kantor Email']);

        $mitraUser = User::factory()->create([
            'email' => 'email-only-mitra@example.test',
            'tipe_user' => 'mitra',
        ]);

        $mitra = Mitra::create([
            'nama' => 'Mitra Email Only',
            'email' => $mitraUser->email,
            'kantor_cabang_id' => $kantor->id,
        ]);

        $otherMitra = Mitra::create([
            'nama' => 'Mitra Tidak Sama',
            'email' => 'other@example.test',
            'kantor_cabang_id' => $kantor->id,
        ]);

        $mine = Donatur::create([
            'nama' => 'Donatur Email Match',
            'jenis_donatur' => ['komunitas'],
            'kantor_cabang_id' => $kantor->id,
            'mitra_id' => $mitra->id,
            'status' => 'aktif',
        ]);

        Donatur::create([
            'nama' => 'Donatur Tidak Terlihat',
            'jenis_donatur' => ['komunitas'],
            'kantor_cabang_id' => $kantor->id,
            'mitra_id' => $otherMitra->id,
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($mitraUser)->getJson('/admin/api/donatur?per_page=10');

        $response->assertOk()->assertJson(['success' => true]);
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertSame($mine->id, $data[0]['id']);
    }
}
