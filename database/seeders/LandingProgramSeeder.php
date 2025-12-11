<?php

namespace Database\Seeders;

use App\Models\LandingProgram;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LandingProgramSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        LandingProgram::truncate();

        $items = [
            [
                'id' => '4d6c47da-ed6a-4e55-9149-cfaa0b54abb8',
                'name' => 'qurban kms 2025',
                'description' => 'qurban kms 2025',
                'image_url' => 'https://is3.cloudhost.id/kitamembantusesama.com/kitamembantusesama.com/4d6c47da-ed6a-4e55-9149-cfaa0b54abb8-lp-program-image',
                'is_active' => true,
                'is_highlight' => true,
                'created_by' => 1,
                'created_at' => '2025-07-03 04:55:40',
                'updated_at' => '2025-07-03 04:55:40',
            ],
            [
                'id' => '276db3c9-46e4-434f-a902-91d1254f81db',
                'name' => 'Qurban Domba KMS 2025',
                'description' => '<p>Qurban Domba KMS 2025</p>',
                'image_url' => 'https://is3.cloudhost.id/kitamembantusesama.com/kitamembantusesama.com/276db3c9-46e4-434f-a902-91d1254f81db-lp-program-image',
                'is_active' => true,
                'is_highlight' => true,
                'created_by' => 1,
                'created_at' => '2025-04-09 02:43:09',
                'updated_at' => '2025-05-29 15:51:40',
            ],
            // ... you can add rest items similarly
        ];

        foreach ($items as $item) {
            LandingProgram::create($item);
        }
    }
}
