<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Donatur;
use App\Models\User;

class DonaturPicFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_filter_by_pic_returns_matching_donatur()
    {
        $user = User::factory()->create();
        $picUser = User::factory()->create(['name' => 'PIC User']);

        Donatur::create(['nama' => 'Donatur A', 'jenis_donatur' => ['retail'], 'pic' => $picUser->id, 'created_by' => $user->id]);
        Donatur::create(['nama' => 'Donatur B', 'jenis_donatur' => ['retail'], 'created_by' => $user->id]);

        $response = $this->actingAs($user)->getJson('/admin/api/donatur?pic=' . $picUser->id);

        $response->assertOk()->assertJson(['success' => true]);
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals($picUser->id, $data[0]['pic']);
        $this->assertNotNull($data[0]['pic_user']);
        $this->assertEquals('PIC User', $data[0]['pic_user']['nama']);
    }

    public function test_store_validates_pic_must_be_existing_user()
    {
        $user = User::factory()->create();

        $payload = [
            'nama' => 'Donatur X',
            'jenis_donatur' => ['komunitas'],
            'pic' => '00000000-0000-0000-0000-000000000000', // non existing
        ];

        $response = $this->actingAs($user)->postJson('/admin/api/donatur', $payload);

        $response->assertStatus(422)->assertJson(['success' => false]);
        $this->assertArrayHasKey('pic', $response->json('errors'));
    }
}
