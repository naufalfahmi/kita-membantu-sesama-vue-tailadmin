<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_and_update_landing_profile()
    {
        // Seed necessary data like admin user
        $user = User::factory()->create();

        $this->actingAs($user);

        // Get CSRF token via route
        $tokenRes = $this->get('/admin/api/csrf-token');
        $tokenRes->assertStatus(200);
        $csrf = $tokenRes->json('csrf_token');

        $payload = [
            'email' => 'test@example.com',
            'phone_number' => '08123456789',
            'bank_account_1' => [ ['label' => 'Bank BRI', 'value' => '123456'] ],
            'address' => [ ['label' => 'Kantor Pusat', 'value' => 'Jl Test'] ],
        ];

        $res = $this->withHeaders([
            'X-CSRF-TOKEN' => $csrf,
        ])->postJson('/admin/api/company/landing-profile', $payload);

        $res->assertStatus(201)->assertJson(['success' => true]);

        // Update
        $payload['phone_number'] = '0822334455';
        $res2 = $this->withHeaders([
            'X-CSRF-TOKEN' => $csrf,
        ])->putJson('/admin/api/company/landing-profile', $payload);

        $res2->assertStatus(200)->assertJson(['success' => true]);
    }
}
