<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingKegiatanCsrfTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_and_update_with_csrf_header()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $tokenRes = $this->get('/admin/api/csrf-token');
        $tokenRes->assertStatus(200);
        $csrf = $tokenRes->json('csrf_token');

        $payload = [
            'title' => 'Test Kegiatan',
            'number_of_recipients' => 10,
            'address' => 'Test Address',
            'activity_date' => now()->toDateString(),
            'status' => 'active',
            'description' => 'Test description',
        ];

        $res = $this->withHeaders(['X-CSRF-TOKEN' => $csrf])->postJson('/admin/api/landing-kegiatan', $payload);
        $res->assertStatus(201)->assertJson(['success' => true]);

        $id = $res->json('data.id');
        $payload['title'] = 'Updated Title';

        $res2 = $this->withHeaders(['X-CSRF-TOKEN' => $csrf])->post('/admin/api/landing-kegiatan/'.$id, array_merge($payload, ['_method' => 'PUT']));
        $res2->assertStatus(200)->assertJson(['success' => true]);
    }
}
