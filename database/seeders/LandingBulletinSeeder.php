<?php

namespace Database\Seeders;

use App\Models\LandingBulletin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LandingBulletinSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'id' => 'e2cf5d38-caa8-4acc-8681-f3736b03edbc',
                'name' => 'BULLETIN NOVEMBER 2025',
                'date' => '2025-11-28',
                'file' => 'https://is3.cloudhost.id/kitamembantusesama.com/kitamembantusesama.com/e2cf5d38-caa8-4acc-8681-f3736b03edbc-bulletin?X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=6H6CG29RVK4D06787YO5%2F20251211%2Fap-southeast-3%2Fs3%2Faws4_request&X-Amz-Date=20251211T071331Z&X-Amz-Expires=1200&X-Amz-SignedHeaders=host&X-Amz-Signature=1a6f223967a39e4cacdb1223d0e79fc5b09bd78486cc56bd11617c031542fb59',
            ],
            [
                'id' => '5d068a7e-77a7-441d-bca3-8212e6d8e375',
                'name' => 'BULLETIN NOVEMBER 2025',
                'date' => '2025-11-28',
                'file' => 'https://is3.cloudhost.id/kitamembantusesama.com/kitamembantusesama.com/5d068a7e-77a7-441d-bca3-8212e6d8e375-bulletin?X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=6H6CG29RVK4D06787YO5%2F20251211%2Fap-southeast-3%2Fs3%2Faws4_request&X-Amz-Date=20251211T071331Z&X-Amz-Expires=1200&X-Amz-SignedHeaders=host&X-Amz-Signature=8588cf6cf259ccfbad69d5e831db4b27f28c7a03b2123319ee72fe94623583b2',
            ],
            [
                'id' => 'e1d20e09-c931-4434-8a8b-be1169f1b71b',
                'name' => 'BULLETIN OKTOBER 2025',
                'date' => '2025-10-31',
                'file' => 'https://is3.cloudhost.id/kitamembantusesama.com/kitamembantusesama.com/e1d20e09-c931-4434-8a8b-be1169f1b71b-bulletin?X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Credential=6H6CG29RVK4D06787YO5%2F20251211%2Fap-southeast-3%2Fs3%2Faws4_request&X-Amz-Date=20251211T071331Z&X-Amz-Expires=1200&X-Amz-SignedHeaders=host&X-Amz-Signature=eeda1e7c51b797240bc07a88bbc6bfe496e2195506a52cb9fa86f2515061ff48',
            ],
            // ... more items can be added here if desired
        ];

        foreach ($items as $item) {
            if (! LandingBulletin::where('id', $item['id'])->exists()) {
                LandingBulletin::create(array_merge($item, [
                    'created_by' => null,
                ]));
            }
        }
    }
}
