<?php

namespace Tests\Feature;

use App\Models\LandingBulletin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingBulletinTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_seeded_bulletin()
    {
        LandingBulletin::create([
            'id' => \Illuminate\Support\Str::uuid()->toString(),
            'name' => 'BULLETIN TEST 2025',
            'date' => '2025-11-28',
            'file' => 'https://example.com/bulletin.pdf',
        ]);

        $user = User::factory()->create();

        $res = $this->actingAs($user)->getJson('/admin/api/landing-bulletin');
        $res->assertStatus(200)->assertJsonFragment(['name' => 'BULLETIN TEST 2025']);
    }

    public function test_can_create_bulletin_with_file_url()
    {
        $user = User::factory()->create();

        $res = $this->actingAs($user)->postJson('/admin/api/landing-bulletin', [
            'name' => 'New Bulletin',
            'date' => '2025-12-01',
            'file_url' => 'https://example.com/new.pdf',
        ]);

        $res->assertStatus(201)->assertJson(['success' => true]);
        $this->assertDatabaseHas('landing_bulletins', ['name' => 'New Bulletin']);
    }

    public function test_show_returns_existing_bulletin()
    {
        $bulletin = LandingBulletin::create([
            'id' => \Illuminate\Support\Str::uuid()->toString(),
            'name' => 'Show Bulletin',
            'date' => '2025-10-01',
            'file' => 'https://example.com/show.pdf',
        ]);

        $user = User::factory()->create();

        $res = $this->actingAs($user)->getJson('/admin/api/landing-bulletin/' . $bulletin->id);
        $res->assertStatus(200)->assertJsonFragment(['name' => 'Show Bulletin']);
    }

    public function test_can_update_bulletin_with_file_url_using_method_override()
    {
        $bulletin = LandingBulletin::create([
            'id' => \Illuminate\Support\Str::uuid()->toString(),
            'name' => 'Old Bulletin',
            'date' => '2025-10-01',
            'file' => 'https://example.com/old.pdf',
        ]);

        $user = User::factory()->create();

        $res = $this->actingAs($user)->postJson('/admin/api/landing-bulletin/' . $bulletin->id, [
            '_method' => 'PUT',
            'name' => 'Updated Bulletin',
            'date' => '2025-11-01',
            'file_url' => 'https://example.com/updated.pdf',
        ]);

        $res->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseHas('landing_bulletins', ['id' => $bulletin->id, 'name' => 'Updated Bulletin']);
    }
}
