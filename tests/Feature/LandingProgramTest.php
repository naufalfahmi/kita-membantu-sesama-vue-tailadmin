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

        // create initial program
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
}
