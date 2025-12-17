<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class DonaturAddressFieldsTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_and_return_address_fields()
    {
        // ensure admin role exists
        if (!\Spatie\Permission\Models\Role::where('name', 'admin')->exists()) {
            \Spatie\Permission\Models\Role::create(['name' => 'admin']);
        }

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $payload = [
            'nama' => 'Donor Alamat',
            'jenis_donatur' => ['retail'],
            'provinsi' => 'Jawa Barat',
            'kota_kab' => 'Bandung',
            'kecamatan' => 'Coblong',
            'kelurahan' => 'Dago',
        ];

        $response = $this->actingAs($admin)->postJson('/admin/api/donatur', $payload);
        $response->assertStatus(201)->assertJson(['success' => true]);

        $data = $response->json('data');
        $this->assertEquals('Jawa Barat', $data['provinsi']);
        $this->assertEquals('Bandung', $data['kota_kab']);
        $this->assertEquals('Coblong', $data['kecamatan']);
        $this->assertEquals('Dago', $data['kelurahan']);
    }
}
