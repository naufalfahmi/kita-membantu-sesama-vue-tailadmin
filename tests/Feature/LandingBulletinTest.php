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
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'view landing bulletin']);
        $user->givePermissionTo('view landing bulletin');

        $res = $this->actingAs($user)->getJson('/admin/api/landing-bulletin');
        $res->assertStatus(200)->assertJsonFragment(['name' => 'BULLETIN TEST 2025']);
    }

    public function test_can_create_bulletin_with_file_url()
    {
        $user = User::factory()->create();
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'create landing bulletin']);
        $user->givePermissionTo('create landing bulletin');

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
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'view landing bulletin']);
        $user->givePermissionTo('view landing bulletin');

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
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'update landing bulletin']);
        $user->givePermissionTo('update landing bulletin');

        $res = $this->actingAs($user)->postJson('/admin/api/landing-bulletin/' . $bulletin->id, [
            '_method' => 'PUT',
            'name' => 'Updated Bulletin',
            'date' => '2025-11-01',
            'file_url' => 'https://example.com/updated.pdf',
        ]);

        $res->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseHas('landing_bulletins', ['id' => $bulletin->id, 'name' => 'Updated Bulletin']);
    }

    public function test_public_index_returns_default_six_and_has_more_flag()
    {
        $total = 8;
        for ($i = 1; $i <= $total; $i++) {
            LandingBulletin::create([
                'id' => \Illuminate\Support\Str::uuid()->toString(),
                'name' => 'Bulletin '.$i,
                'date' => now()->subDays($i)->toDateString(),
                'file' => null,
            ]);
        }

        $res = $this->getJson('/api/landing-bulletins');
        $res->assertStatus(200)->assertJson(['success' => true]);
        $json = $res->json();
        $this->assertCount(6, $json['data']);
        $this->assertEquals($total, $json['total']);
        $this->assertTrue($json['has_more']);
    }
}
