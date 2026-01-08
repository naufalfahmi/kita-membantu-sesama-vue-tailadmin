<?php

namespace Tests\Feature;

use App\Models\Donatur;
use App\Models\KantorCabang;
use App\Models\Mitra;
use App\Models\Program;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class TransaksiMitraVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_mitra_user_sees_only_their_mitra_transactions(): void
    {
        $kantor = KantorCabang::create(['kode' => 'KC01', 'nama' => 'Cabang Mitra']);
        $program = Program::create(['nama_program' => 'Program Mitra']);
        $donatur = Donatur::create([
            'kode' => 'DNR-' . Str::upper(Str::random(4)),
            'nama' => 'Donatur Mitra',
            'jenis_donatur' => ['perorangan'],
            'status' => 'aktif',
        ]);

        $creator = User::factory()->create();

        $mitraUser = User::factory()->create([
            'email' => 'mitra@example.test',
            'tipe_user' => 'mitra',
        ]);

        $otherMitraUser = User::factory()->create([
            'email' => 'mitra-other@example.test',
            'tipe_user' => 'mitra',
        ]);

        $mitra = Mitra::create([
            'nama' => 'Mitra A',
            'email' => $mitraUser->email,
            'user_id' => $mitraUser->id,
            'kantor_cabang_id' => $kantor->id,
        ]);

        $otherMitra = Mitra::create([
            'nama' => 'Mitra B',
            'email' => $otherMitraUser->email,
            'user_id' => $otherMitraUser->id,
            'kantor_cabang_id' => $kantor->id,
        ]);

        $mine = Transaksi::create([
            'kode' => 'TRX-' . Str::upper(Str::random(6)),
            'kantor_cabang_id' => $kantor->id,
            'donatur_id' => $donatur->id,
            'program_id' => $program->id,
            'mitra_id' => $mitra->id,
            'nominal' => 1000000,
            'tanggal_transaksi' => now(),
            'status' => 'verified',
            'created_by' => $creator->id,
        ]);

        Transaksi::create([
            'kode' => 'TRX-' . Str::upper(Str::random(6)),
            'kantor_cabang_id' => $kantor->id,
            'donatur_id' => $donatur->id,
            'program_id' => $program->id,
            'mitra_id' => $otherMitra->id,
            'nominal' => 2000000,
            'tanggal_transaksi' => now(),
            'status' => 'verified',
            'created_by' => $creator->id,
        ]);

        $response = $this->actingAs($mitraUser)->getJson('/admin/api/transaksi?per_page=10');

        $response->assertOk()->assertJson(['success' => true]);
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertSame($mine->id, $data[0]['id']);
    }

    public function test_mitra_user_sees_transactions_when_mitra_matched_by_email(): void
    {
        $kantor = KantorCabang::create(['kode' => 'KC02', 'nama' => 'Cabang Email']);
        $program = Program::create(['nama_program' => 'Program Email']);
        $donatur = Donatur::create([
            'kode' => 'DNR-' . Str::upper(Str::random(4)),
            'nama' => 'Donatur Email',
            'jenis_donatur' => ['perorangan'],
            'status' => 'aktif',
        ]);

        $creator = User::factory()->create();

        $mitraUser = User::factory()->create([
            'email' => 'email-only@example.test',
            'tipe_user' => 'mitra',
        ]);

        $mitra = Mitra::create([
            'nama' => 'Mitra Email Only',
            'email' => $mitraUser->email,
            'kantor_cabang_id' => $kantor->id,
        ]);

        $otherMitra = Mitra::create([
            'nama' => 'Mitra Lain',
            'email' => 'lain@example.test',
            'kantor_cabang_id' => $kantor->id,
        ]);

        $mine = Transaksi::create([
            'kode' => 'TRX-' . Str::upper(Str::random(6)),
            'kantor_cabang_id' => $kantor->id,
            'donatur_id' => $donatur->id,
            'program_id' => $program->id,
            'mitra_id' => $mitra->id,
            'nominal' => 1500000,
            'tanggal_transaksi' => now(),
            'status' => 'verified',
            'created_by' => $creator->id,
        ]);

        Transaksi::create([
            'kode' => 'TRX-' . Str::upper(Str::random(6)),
            'kantor_cabang_id' => $kantor->id,
            'donatur_id' => $donatur->id,
            'program_id' => $program->id,
            'mitra_id' => $otherMitra->id,
            'nominal' => 2500000,
            'tanggal_transaksi' => now(),
            'status' => 'verified',
            'created_by' => $creator->id,
        ]);

        $response = $this->actingAs($mitraUser)->getJson('/admin/api/transaksi?per_page=10');

        $response->assertOk()->assertJson(['success' => true]);
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertSame($mine->id, $data[0]['id']);
    }
}
