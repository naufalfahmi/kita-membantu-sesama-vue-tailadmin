<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class KaryawanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            [
                'id' => 'ea37aa66-ba3a-48f0-aebd-867eb94035fc',
                'name' => 'Abdul Hamid',
                'email' => 'abdulhamidsumarjo@gmail.com',
                'account_index_number' => '01-2025731-kms002-41',
                'phone_number' => '6285288599925',
                'bank_name' => 'bsi',
                'bank_account_number' => '7066429894',
                'date_of_birth' => '1984-04-05',
                'education' => "S1 Lipia",
                'entry_date' => '2025-07-30',
                'position_name' => 'Fundrising',
                'rank_id' => 'e1b0d2b4-1b1e-4af1-a103-cf18fd740a33',
                'branch_office_id' => '99425ce7-e52b-4756-a871-d4c91154d2e1',
                'attendance_type_id' => '00356e90-c673-4d24-a786-aee985b0c038',
            ],
            [
                'id' => '6627d353-87f0-46f1-9e9b-424b05306f6c',
                'name' => 'Divia Zahra Choirunnisa',
                'email' => 'diviazahra63@gmail.com',
                'account_index_number' => '01-202562-kms003-40',
                'phone_number' => '628558858117',
                'bank_name' => 'BSI',
                'bank_account_number' => '7311270348',
                'date_of_birth' => '2007-07-15',
                'education' => 'SLTA',
                'entry_date' => '2025-05-13',
                'position_name' => 'Admin Cabang',
                'rank_id' => 'e1b0d2b4-1b1e-4af1-a103-cf18fd740a33',
                'branch_office_id' => 'd0435799-87c8-410c-8f71-7af3a1d5c799',
                'attendance_type_id' => '00356e90-c673-4d24-a786-aee985b0c038',
            ],
            [
                'id' => '61e401a5-40fd-463d-b981-d3ce57adcdea',
                'name' => 'Jivanti Zuleika khoirunnisa',
                'email' => 'zuleikajivanti@gmail.com',
                'account_index_number' => '01-202562-kms002-38',
                'phone_number' => '6282112124504',
                'bank_name' => 'bsi',
                'bank_account_number' => '1190525249',
                'date_of_birth' => '2003-10-18',
                'education' => 'SLTA',
                'entry_date' => '2025-04-23',
                'position_name' => 'Program Offficer',
                'rank_id' => 'e1b0d2b4-1b1e-4af1-a103-cf18fd740a33',
                'branch_office_id' => 'd0435799-87c8-410c-8f71-7af3a1d5c799',
                'attendance_type_id' => '00356e90-c673-4d24-a786-aee985b0c038',
            ],
            [
                'id' => '22702d34-7ef7-4e1b-b7d7-c387817f8abe',
                'name' => 'Fadli Darmadi',
                'email' => 'abu.hafizha@gmail.com',
                'account_index_number' => '01-2025114-kms003-32',
                'phone_number' => '628988443187',
                'bank_name' => 'BSI',
                'bank_account_number' => '123456789',
                'date_of_birth' => '1977-07-16',
                'education' => 'SLTA',
                'entry_date' => '2025-01-06',
                'position_name' => 'Fundrising',
                'rank_id' => 'e1b0d2b4-1b1e-4af1-a103-cf18fd740a33',
                'branch_office_id' => 'd0435799-87c8-410c-8f71-7af3a1d5c799',
                'attendance_type_id' => '00356e90-c673-4d24-a786-aee985b0c038',
            ],
            [
                'id' => 'c74be476-bee2-4592-a0aa-4a7941ea5aaa',
                'name' => 'Putrie Intan',
                'email' => 'putrie.intannurh@gmail.com',
                'account_index_number' => '01-2024823-kms003-27',
                'phone_number' => '6287809961362',
                'bank_name' => 'Seabank',
                'bank_account_number' => '901773655292',
                'date_of_birth' => '1995-09-08',
                'education' => "Tahfidzul Qur'an",
                'entry_date' => '2024-08-21',
                'position_name' => 'Fundrising',
                'rank_id' => 'e1b0d2b4-1b1e-4af1-a103-cf18fd740a33',
                'branch_office_id' => 'd0435799-87c8-410c-8f71-7af3a1d5c799',
                'attendance_type_id' => '0e44a46d-7e39-486e-864f-5d8c38532ef7',
            ],
            [
                'id' => '16a118ac-f276-4e0b-a19f-8f0d94d2a21a',
                'name' => 'Haryadi kurniawan',
                'email' => 'kurniawanyadi74@gmail.com',
                'account_index_number' => '01-2024813-kms002-26',
                'phone_number' => '6281219317355',
                'bank_name' => 'BSI',
                'bank_account_number' => '7002783503',
                'date_of_birth' => '1974-01-19',
                'education' => 'Sarjana ( S1) manajemen',
                'entry_date' => '2024-08-12',
                'position_name' => 'Fundrising',
                'rank_id' => 'e1b0d2b4-1b1e-4af1-a103-cf18fd740a33',
                'branch_office_id' => '99425ce7-e52b-4756-a871-d4c91154d2e1',
                'attendance_type_id' => '00356e90-c673-4d24-a786-aee985b0c038',
            ],
            [
                'id' => '99708226-8a05-4894-8140-927379cd3b97',
                'name' => 'Aldi Lustandial',
                'email' => 'bisnisaldi2@gmail.com',
                'account_index_number' => '01-2024610-kms003-25',
                'phone_number' => '6281297655877',
                'bank_name' => 'Bca',
                'bank_account_number' => '7360294107',
                'date_of_birth' => '1985-11-09',
                'education' => 'Diploma 1',
                'entry_date' => '2024-06-10',
                'position_name' => 'Fundrising',
                'rank_id' => 'e1b0d2b4-1b1e-4af1-a103-cf18fd740a33',
                'branch_office_id' => 'd0435799-87c8-410c-8f71-7af3a1d5c799',
                'attendance_type_id' => '00356e90-c673-4d24-a786-aee985b0c038',
            ],
            [
                'id' => 'c20053ce-7ddf-469f-8c31-dd8970bf4ab6',
                'name' => 'Ahmad Firdaus',
                'email' => 'ahmadfirdausfirdaus56@gmail.com',
                'account_index_number' => '01-202419-kms003-21',
                'phone_number' => '6289688138283',
                'bank_name' => 'b',
                'bank_account_number' => '12345678',
                'date_of_birth' => '1983-08-31',
                'education' => 'SMK',
                'entry_date' => '2024-01-08',
                'position_name' => 'Fundrising',
                'rank_id' => 'e1b0d2b4-1b1e-4af1-a103-cf18fd740a33',
                'branch_office_id' => 'd0435799-87c8-410c-8f71-7af3a1d5c799',
                'attendance_type_id' => '00356e90-c673-4d24-a786-aee985b0c038',
            ],
            [
                'id' => 'a030b21d-3b33-4036-9f3d-cd78cd3037e2',
                'name' => 'Safina Hana Azizah',
                'email' => 'finahana190@gmail.com',
                'account_index_number' => '01-2023720-kms002-18',
                'phone_number' => '6287779152412',
                'bank_name' => 'BSI',
                'bank_account_number' => '7174590952',
                'date_of_birth' => '2014-03-12',
                'education' => 'SLTA',
                'entry_date' => '2023-07-01',
                'position_name' => 'Guru Pondok Tahfizh',
                'rank_id' => 'e1b0d2b4-1b1e-4af1-a103-cf18fd740a33',
                'branch_office_id' => '99425ce7-e52b-4756-a871-d4c91154d2e1',
                'attendance_type_id' => '0e44a46d-7e39-486e-864f-5d8c38532ef7',
            ],
            [
                'id' => '0b2893fc-36fa-4199-a803-5ca1c0650b84',
                'name' => 'ARYA BRAMANTYO',
                'email' => 'aryabramantyo8781@gmail.com',
                'account_index_number' => '01-202365-kms002-16',
                'phone_number' => '6287771679152',
                'bank_name' => 'BCA',
                'bank_account_number' => '5500487145',
                'date_of_birth' => '1987-07-01',
                'education' => 'S1',
                'entry_date' => '2023-06-01',
                'position_name' => 'Fundrising',
                'rank_id' => 'e1b0d2b4-1b1e-4af1-a103-cf18fd740a33',
                'branch_office_id' => '99425ce7-e52b-4756-a871-d4c91154d2e1',
                'attendance_type_id' => '00356e90-c673-4d24-a786-aee985b0c038',
            ],
            [
                'id' => 'f30eb0fd-45d7-41fb-9ee0-dc88d68c4b7e',
                'name' => 'Isnah dayu wati',
                'email' => 'isnahbramantyo@gmail.com',
                'account_index_number' => '01-2023523-kms002-14',
                'phone_number' => '6281316818713',
                'bank_name' => 'bsi',
                'bank_account_number' => '7258776407',
                'date_of_birth' => '1981-06-03',
                'education' => 'S1',
                'entry_date' => '2023-05-22',
                'position_name' => 'Admin Cabang',
                'rank_id' => 'e1b0d2b4-1b1e-4af1-a103-cf18fd740a33',
                'branch_office_id' => '99425ce7-e52b-4756-a871-d4c91154d2e1',
                'attendance_type_id' => '00356e90-c673-4d24-a786-aee985b0c038',
            ],
            [
                'id' => 'e3da229d-ce53-4668-beb9-9364093ce367',
                'name' => 'Rina Nurlaela',
                'email' => 'rinanurlaela804@gmail.com',
                'account_index_number' => '01-2023221-kms003-12',
                'phone_number' => '6283811260406',
                'bank_name' => 'Bsi',
                'bank_account_number' => '1023468162',
                'date_of_birth' => '1981-11-11',
                'education' => 'Slta',
                'entry_date' => '2023-02-20',
                'position_name' => 'Guru Pondok Tahfizh',
                'rank_id' => 'e1b0d2b4-1b1e-4af1-a103-cf18fd740a33',
                'branch_office_id' => 'd0435799-87c8-410c-8f71-7af3a1d5c799',
                'attendance_type_id' => '0e44a46d-7e39-486e-864f-5d8c38532ef7',
            ],
            [
                'id' => 'e6bdbda3-a32a-4008-8bc2-d5ec1e0a43f8',
                'name' => 'Siti Mawadah',
                'email' => 'symawadah05@gmail.com',
                'account_index_number' => '01-202321-kms002-7',
                'phone_number' => '6285715023645',
                'bank_name' => 'BSI',
                'bank_account_number' => '7898765',
                'date_of_birth' => '2000-05-05',
                'education' => 'S1',
                'entry_date' => '2023-01-31',
                'position_name' => 'Guru Pondok Tahfizh',
                'rank_id' => 'e1b0d2b4-1b1e-4af1-a103-cf18fd740a33',
                'branch_office_id' => '99425ce7-e52b-4756-a871-d4c91154d2e1',
                'attendance_type_id' => '0e44a46d-7e39-486e-864f-5d8c38532ef7',
            ],
            [
                'id' => '6d8f9f92-a490-4cda-a7e9-73c4139d4ef0',
                'name' => 'Dian Jumardiansyah',
                'email' => 'dede.zahrah212@gmail.com',
                'account_index_number' => '01-2023131-kms003-4',
                'phone_number' => '6281298723110',
                'bank_name' => 'BSI',
                'bank_account_number' => '98767890',
                'date_of_birth' => '1985-03-22',
                'education' => 'SLTA',
                'entry_date' => '2023-01-31',
                'position_name' => 'Admin Cabang',
                'rank_id' => 'e1b0d2b4-1b1e-4af1-a103-cf18fd740a33',
                'branch_office_id' => 'd0435799-87c8-410c-8f71-7af3a1d5c799',
                'attendance_type_id' => '00356e90-c673-4d24-a786-aee985b0c038',
            ],
            [
                'id' => 'bbe3f191-5ea9-4df1-b413-45ce1dbd4668',
                'name' => 'Helly Armiandri',
                'email' => 'hellyarmiandri2401@gmail.com',
                'account_index_number' => '01-2023131-kms002-3',
                'phone_number' => '6281212856394',
                'bank_name' => 'BSI',
                'bank_account_number' => '12345432',
                'date_of_birth' => '1978-01-24',
                'education' => 'SLTA',
                'entry_date' => '2023-01-31',
                'position_name' => 'Fundrising',
                'rank_id' => 'e1b0d2b4-1b1e-4af1-a103-cf18fd740a33',
                'branch_office_id' => '99425ce7-e52b-4756-a871-d4c91154d2e1',
                'attendance_type_id' => '00356e90-c673-4d24-a786-aee985b0c038',
            ],
            [
                'id' => '17fa4c8d-8afd-4d33-bb33-889ae67ebf66',
                'name' => 'Agustari',
                'email' => 'agussukses72@gmail.com',
                'account_index_number' => '01-2023131-kms002-2',
                'phone_number' => '6285214960961',
                'bank_name' => 'BSI',
                'bank_account_number' => '12341234',
                'date_of_birth' => '1972-08-14',
                'education' => 'S1',
                'entry_date' => '2023-01-31',
                'position_name' => 'Direktur Fundrising',
                'rank_id' => '62c21575-a5b3-452f-819b-7a666ab943f9',
                'branch_office_id' => '99425ce7-e52b-4756-a871-d4c91154d2e1',
                'attendance_type_id' => '00356e90-c673-4d24-a786-aee985b0c038',
            ],
            [
                'id' => '741b88ac-133e-453d-9efe-f186e7aa9a91',
                'name' => 'Sugeng Riyadi',
                'email' => 'suririyade@gmail.com',
                'account_index_number' => '01-2023131-kms001-1',
                'phone_number' => '6281311628088',
                'bank_name' => '01-2023131-kms001-1',
                'bank_account_number' => '1111111111',
                'date_of_birth' => '1972-12-23',
                'education' => 'SLTA',
                'entry_date' => '2023-01-31',
                'position_name' => 'Fundrising',
                'rank_id' => '62c21575-a5b3-452f-819b-7a666ab943f9',
                'branch_office_id' => '99425ce7-e52b-4756-a871-d4c91154d2e1',
                'attendance_type_id' => '00356e90-c673-4d24-a786-aee985b0c038',
            ],
        ];

        foreach ($items as $it) {
            $data = [
                'no_induk' => $it['account_index_number'] ?? null,
                'name' => $it['name'],
                'email' => $it['email'],
                'password' => Hash::make('password123'),
                'no_handphone' => $it['phone_number'] ?? null,
                'nama_bank' => $it['bank_name'] ?? null,
                'no_rekening' => $it['bank_account_number'] ?? null,
                'tanggal_lahir' => $it['date_of_birth'] ?? null,
                'pendidikan' => $it['education'] ?? null,
                'tanggal_masuk' => $it['entry_date'] ?? null,
                'posisi' => $it['position_name'] ?? null,
                'pangkat_id' => $it['rank_id'] ?? null,
                'tipe_absensi_id' => $it['attendance_type_id'] ?? null,
                'kantor_cabang_id' => $it['branch_office_id'] ?? null,
                'tipe_user' => 'karyawan',
                'is_active' => true,
            ];

            // Look up existing user by email
            $user = User::where('email', $it['email'])->first();
            if ($user) {
                // Ensure uuid is set on existing user if missing or different
                if (!empty($it['id']) && ($user->uuid !== $it['id'])) {
                    $user->update(array_merge($data, ['uuid' => $it['id']]));
                } else {
                    $user->update($data);
                }
            } else {
                // For new users, set the provided UUID in `uuid` column
                $data['uuid'] = $it['id'] ?? null;
                $user = User::create($data);
            }

            // assign role by position name if exists
            if (!empty($it['position_name'])) {
                $role = Role::where('name', $it['position_name'])->first();
                if ($role) {
                    $user->syncRoles($role);
                }
            }
            // Ensure branch pivot is synced for existing seed data
            if (!empty($it['branch_office_id'])) {
                $user->kantorCabangs()->sync([$it['branch_office_id']]);
            }
        }

        $this->command->info('Seeded ' . count($items) . ' karyawan');
    }
}
