<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LandingProfile;

class LandingProfileContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            'vision_title' => 'Menjadi Organisasi Sosial Kemanusiaan Internasional yang Unggul dan Profesional',
            'vision_description' => 'Menjadi organisasi sosial kemanusiaan internasional yang unggul dan profesional untuk kehidupan kemanusiaan yang lebih baik.',
            'mission_description' => "Memberikan bantuan kemanusiaan yang tepat sasaran\nMemberdayakan masyarakat untuk hidup mandiri\nMembangun kemitraan strategis dengan berbagai pihak\nMengelola organisasi secara profesional dan transparan",
            'vision_mission_image' => null,

            'features' => [
                [
                    'title' => 'Transparansi',
                    'description' => 'Keterbukaan penuh dalam pengelolaan dana dan program untuk membangun kepercayaan publik.',
                ],
                [
                    'title' => 'Amanah',
                    'description' => 'Menjalankan tanggung jawab dengan penuh integritas dan akuntabilitas kepada donatur.',
                ],
                [
                    'title' => 'Profesional',
                    'description' => 'Dikelola oleh tim berpengalaman dengan standar tata kelola organisasi yang baik.',
                ],
            ],

            'cta_title' => 'Sudah 353+ donatur mari mulai donasi untuk Membantu Sesama',
            'cta_description' => 'Bergabunglah untuk mendukung program-program kami — donasi Anda membantu pendidikan, kesehatan, dan respon darurat bagi komunitas yang membutuhkan.',
            'cta_button_active' => false,
            'cta_button_link' => null,

            // Hero defaults
            'hero_title' => 'Kita Membantu Sesama',
            'hero_description' => 'menjadi organisasi sosial kemanusiaan internasional yang unggul dan profesional untuk kehidupan kemanusiaan yang lebih baik',
            'hero_button_active' => true,
            'hero_button_link' => '#program',
            'hero_whatsapp_active' => true,
            'hero_whatsapp_number' => '62895621093500',
            'hero_image' => null,
        ];

        // Update existing landing profile or create a new one
        $profile = LandingProfile::first() ?? new LandingProfile();
        $profile->fill($data);
        $profile->save();

        $this->command->info('LandingProfile content seeded.');
    }
}
