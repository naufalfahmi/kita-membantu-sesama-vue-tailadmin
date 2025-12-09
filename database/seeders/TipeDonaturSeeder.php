<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TipeDonaturSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            [
                'id' => '9ec45a01-a568-434b-bb1f-13df5a433882',
                'name' => 'Komunitas',
                'created_at' => '2023-02-06T02:04:19.61Z',
                'updated_at' => '2023-02-06T02:04:19.61Z',
            ],
            [
                'id' => '8fc86598-a47f-43d3-8d6e-8e9750837510',
                'name' => 'Kotak infaq',
                'created_at' => '2023-02-06T02:03:59.558Z',
                'updated_at' => '2023-02-06T02:03:59.558Z',
            ],
            [
                'id' => '0daa713f-d7ae-4e53-817b-d70102b49e72',
                'name' => 'Retail',
                'created_at' => '2023-02-06T02:03:41.36Z',
                'updated_at' => '2023-02-06T02:03:41.36Z',
            ],
        ];

        foreach ($items as $it) {
            $row = [
                'id' => $it['id'],
                'nama' => $it['name'],
                'created_at' => Carbon::parse($it['created_at'])->toDateTimeString(),
                'updated_at' => Carbon::parse($it['updated_at'])->toDateTimeString(),
            ];

            DB::table('tipe_donatur')->updateOrInsert(
                ['id' => $row['id']],
                $row
            );
        }

        $this->command->info('Seeded ' . count($items) . ' tipe donatur');
    }
}
