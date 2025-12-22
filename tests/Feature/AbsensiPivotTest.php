<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\KantorCabang;
use App\Models\Absensi;

class AbsensiPivotTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_clock_in_with_specified_pivot_kantor_cabang()
    {
        // Create user and kantors
        $user = User::factory()->create();
        $k1 = KantorCabang::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'kode' => 'K001',
            'nama' => 'Cabang A',
            'alamat' => 'Test',
            'latitude' => -6.200000,
            'longitude' => 106.816666,
            'radius' => 2000,
        ]);
        $k2 = KantorCabang::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'kode' => 'K002',
            'nama' => 'Cabang B',
            'latitude' => -6.210000,
            'longitude' => 106.820000,
            'radius' => 2000,
        ]);

        // Attach via pivot
        $user->kantorCabangs()->sync([$k1->id, $k2->id]);

        $response = $this->actingAs($user)
            ->postJson('/admin/api/absensi/clock-in', [
                'latitude' => -6.200000,
                'longitude' => 106.816666,
                'kantor_cabang_id' => $k2->id,
            ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('absensis', [
            'user_id' => $user->id,
            'kantor_cabang_id' => $k2->id,
        ]);
    }

    public function test_clock_out_uses_absensi_kantor_or_fallback()
    {
        $user = User::factory()->create();
        $k1 = KantorCabang::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'kode' => 'K003',
            'nama' => 'Cabang A',
            'alamat' => 'Test',
            'latitude' => -6.200000,
            'longitude' => 106.816666,
            'radius' => 2000,
        ]);

        $user->kantorCabangs()->sync([$k1->id]);

        // Create an existing absensi (clock-in) with kantor_cabang_id = k1
        $abs = Absensi::create([
            'user_id' => $user->id,
            'tipe_absensi_id' => null,
            'kantor_cabang_id' => $k1->id,
            'jam_masuk' => now(),
            'latitude_masuk' => -6.200000,
            'longitude_masuk' => 106.816666,
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->postJson('/admin/api/absensi/clock-out', [
                'latitude' => -6.200000,
                'longitude' => 106.816666,
            ]);

        $response->dump();

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('absensis', [
            'user_id' => $user->id,
            'jam_keluar' => null,
        ]);
    }
}
