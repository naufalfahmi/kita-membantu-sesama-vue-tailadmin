<?php

namespace Tests\Feature;

use App\Models\LandingProposal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingProposalTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_seeded_proposal()
    {
        // Seed one proposal
        LandingProposal::create([
            'id' => \Illuminate\Support\Str::uuid()->toString(),
            'name' => 'Proposal Ramadhan 2023 / 1444 H',
            'proposal_date' => '2023-02-01',
            'file' => 'https://example.com/proposal.pdf',
            'file_name' => 'proposal.pdf',
        ]);

        $user = User::factory()->create();

        $res = $this->actingAs($user)->getJson('/admin/api/landing-proposal');
        $res->assertStatus(200)->assertJsonFragment(['name' => 'Proposal Ramadhan 2023 / 1444 H']);
    }

    public function test_can_create_proposal_with_file_url()
    {
        $user = User::factory()->create();

        $res = $this->actingAs($user)->postJson('/admin/api/landing-proposal', [
            'name' => 'New Proposal',
            'proposal_date' => '2025-01-01',
            'file_url' => 'https://example.com/new.pdf',
        ]);

        $res->assertStatus(201)->assertJson(['success' => true]);
        $this->assertDatabaseHas('landing_proposals', ['name' => 'New Proposal']);
    }
}
