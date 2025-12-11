<?php

namespace Database\Seeders;

use App\Models\LandingKegiatan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LandingKegiatanSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data (careful in production)
        LandingKegiatan::truncate();

        $items = [
            [
                'title' => 'Makanan tambahan bergizi untuk penghafal Al-Quran',
                'description' => '<p class="ql-align-justify"><span style="background-color: transparent;">Alhamdulillah, pada Rabu, 05 November 2025, Kita Membantu Sesama telah memberikan makanan tambahan bergizi berupa bakso kepada anak-anak binaan sebelum kegiatan belajar al-quran.</span></p><p><br></p><p class="ql-align-justify"><span style="background-color: transparent;">Anak-anak menikmati hidangan tersebut bersama para pembina dan ustadzah dalam suasana yang hangat dan penuh kebersamaan.</span></p>',
                'number_of_recipients' => 42,
                'address' => 'Ciomas, Bogor',
                'village' => 'CIOMAS',
                'district' => 'CIOMAS',
                'city' => 'BOGOR',
                'province' => 'JAWA BARAT',
                'postal_code' => '12345',
                'activity_date' => '2025-11-05 03:50:00',
                'status' => 'active',
                'images' => json_encode(['https://is3.cloudhost.id/kitamembantusesama.com/kitamembantusesama.com/5f58206d-7d62-48b6-b5ea-6d65c789849b-activity-image']),
                'created_at' => '2025-11-11 03:58:09',
                'updated_at' => '2025-11-19 04:25:51',
            ],
            [
                'title' => 'Bantuan Sepatu Sekolah Untuk Ramzy',
                'description' => '<p><strong>Bantuan Sepatu Sekolah untuk Ramzy</strong></p><p><br></p><p>Alhamdulillah, pada Rabu, 15 Oktober 2025, Pondok Tahfizh Kita Membantu Sesama (KMS) telah menyalurkan bantuan berupa sepatu sekolah untuk Ramzy.</p>',
                'number_of_recipients' => 1,
                'address' => 'Bogor',
                'village' => 'Ciomas',
                'district' => 'Ciomas',
                'city' => 'Bogor',
                'province' => 'Jawa Barat',
                'postal_code' => '12345',
                'activity_date' => '2025-10-15 15:01:00',
                'status' => 'active',
                'images' => json_encode(['https://is3.cloudhost.id/kitamembantusesama.com/kitamembantusesama.com/d9076345-efa9-4b91-80e7-742565e07530-activity-image']),
                'created_at' => '2025-10-16 15:02:16',
                'updated_at' => '2025-10-22 02:26:33',
            ],
            // You can add more items here following the same structure
        ];

        foreach ($items as $item) {
            LandingKegiatan::create($item);
        }
    }
}
