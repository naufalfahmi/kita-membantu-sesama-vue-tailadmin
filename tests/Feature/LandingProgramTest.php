<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\LandingProgram;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LandingProgramTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_landing_program_with_image()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'create landing program']);
        $user->givePermissionTo('create landing program');

        $file = UploadedFile::fake()->image('program.jpg');

        $response = $this->actingAs($user)
            ->withHeaders(['X-CSRF-TOKEN' => csrf_token()])
            ->post('/admin/api/landing-program', [
                'name' => 'Program Test',
                'description' => '<p>desc</p>',
                'status' => 'active',
                'is_highlight' => '1',
                'image' => $file,
            ]);

        $response->assertStatus(201)->assertJson(['success' => true]);

        $this->assertDatabaseHas('landing_programs', ['name' => 'Program Test']);

        $program = LandingProgram::first();
        $this->assertNotNull($program->image_url);
        Storage::disk('public')->assertExists($program->image_url);
        $this->assertTrue((bool) $program->is_highlight);
    }

    public function test_can_update_landing_program_and_replace_image()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'create landing program']);
        $user->givePermissionTo('create landing program');

        // create initial program
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'update landing program']);
        $user->givePermissionTo('update landing program');
        $initialFile = UploadedFile::fake()->image('a.jpg');
        $this->actingAs($user)->withHeaders(['X-CSRF-TOKEN' => csrf_token()])->post('/admin/api/landing-program', [
            'name' => 'Program Update',
            'description' => 'old',
            'status' => 'active',
            'image' => $initialFile,
        ]);

        $program = LandingProgram::first();
        $this->assertNotNull($program->image_url);

        // update with new image
        $newFile = UploadedFile::fake()->image('b.jpg');

        $res = $this->actingAs($user)
            ->withHeaders(['X-CSRF-TOKEN' => csrf_token()])
            ->post('/admin/api/landing-program/' . $program->id, [
                '_method' => 'PUT',
                'name' => 'Program Update',
                'description' => 'new',
                'status' => 'active',
                'image' => $newFile,
                'is_highlight' => '1',
            ]);

        $res->assertStatus(200)->assertJson(['success' => true]);

        $program->refresh();
        $this->assertEquals('new', strip_tags($program->description));
        $this->assertTrue((bool) $program->is_highlight);
        Storage::disk('public')->assertExists($program->image_url);
    }

    public function test_public_index_returns_default_four_and_has_more_flag()
    {
        $total = 6;
        for ($i = 1; $i <= $total; $i++) {
            LandingProgram::create([
                'id' => \Illuminate\Support\Str::uuid()->toString(),
                'name' => 'Program '.$i,
                'description' => 'Desc '.$i,
                'image_url' => null,
            ]);
        }

        $res = $this->getJson('/api/landing-programs');
        $res->assertStatus(200)->assertJson(['success' => true]);
        $json = $res->json();
        $this->assertCount(4, $json['data']);
        $this->assertEquals($total, $json['total']);
        $this->assertTrue($json['has_more']);
    }
}
