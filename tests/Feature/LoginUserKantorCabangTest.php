<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\KantorCabang;

class LoginUserKantorCabangTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_endpoint_returns_kantor_cabangs_and_primary()
    {
        $user = User::factory()->create();
        $k1 = KantorCabang::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'kode' => 'LU1',
            'nama' => 'Cabang A',
            'alamat' => 'Test',
            'latitude' => -6.2,
            'longitude' => 106.8,
        ]);
        $k2 = KantorCabang::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'kode' => 'LU2',
            'nama' => 'Cabang B',
            'alamat' => 'Test',
            'latitude' => -6.21,
            'longitude' => 106.81,
        ]);

        $user->kantorCabangs()->sync([$k1->id, $k2->id]);

        $response = $this->actingAs($user)
            ->getJson('/admin/api/user')
            ->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'user' => [
                    'id',
                    'kantor_cabang',
                ],
            ])
            ->assertJson(['success' => true]);

        $payload = $response->json('user');
        $this->assertEquals($k1->id, $payload['kantor_cabang']['id']);
    }
}
