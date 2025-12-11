<?php

namespace Database\Seeders;

use App\Models\LandingProposal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LandingProposalSeeder extends Seeder
{
    public function run(): void
    {
        LandingProposal::create([
            'id' => Str::uuid()->toString(),
            'name' => 'Proposal Ramadhan 2023 / 1444 H',
            'proposal_date' => '2023-02-01',
            'file' => 'https://is3.cloudhost.id/kitamembantusesama.com/kitamembantusesama.com/d38876dc-05d9-4162-b5d3-f8d0e42e3228-proposal?X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=6H6CG29RVK4D06787YO5%2F20251211%2Fap-southeast-3%2Fs3%2Faws4_request&X-Amz-Date=20251211T064842Z&X-Amz-Expires=1200&X-Amz-SignedHeaders=host&X-Amz-Signature=063f20f7e0e34a3e9e112cbe8065cbe4ade0d2d462bc430e60bf5e9a54f43c10',
            'file_name' => 'proposal-ramadhan-2023.pdf',
            'file_size' => 0,
            'created_by' => null,
        ]);
    }
}
