<?php

namespace Database\Seeders;

use App\Models\LandingProfile;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LandingProfileSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        LandingProfile::truncate();

        LandingProfile::create([
            'title' => 'Kita Membantu Sesama Edited',
            'subtitle' => 'Layanan Zakat',
            'description' => 'test',
            'email' => 'kitamembantusesama@gmail.com',
            'phone_number' => '62895621093500',
            'address' => [
                ['label' => 'Kantor Cabang Depok', 'value' => 'Jl Sawangan Elok Raya Blok C-1 No.7 Duren Seribu, Kec. Bojongsari, Kota Depok, Jawa Barat 16518'],
                ['label' => 'Kantor Cabang Bogor', 'value' => 'Jl. Anggrek III No. 27 Kp. Sukamulya Ciomas Bogor Jawa Barat 16610'],
            ],
            'bank_account_1' => [
                ['label' => 'Bank BSI', 'value' => 2402501235],
                ['label' => 'Bank Mandiri', 'value' => 1330024797557],
                ['label' => 'Bank BRI', 'value' => 761701012584531],
            ],
            'bank_account_2' => 'BRI : 87878787878',
            'about' => 'test',
            'created_by' => 1,
        ]);
    }
}
