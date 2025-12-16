<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Transaksi;
use App\Models\Donatur;
use App\Models\Program;
use App\Models\KantorCabang;
use App\Models\User;

class TransaksiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // If a JSON export exists in database/seeders/data, import that instead of generating random data.
        $dataDir = database_path('seeders/data');
        $files = [];
        if (is_dir($dataDir)) {
            $files = glob($dataDir . '/transaksi*.json');
        }

        if (!empty($files)) {
            $imported = 0;
            foreach ($files as $file) {
                $content = json_decode(file_get_contents($file), true);
                if (!$content) {
                    $this->command->warn("Failed to parse JSON file: {$file}");
                    continue;
                }

                $rows = $content['data'] ?? $content;
                if (!is_array($rows)) {
                    $this->command->warn("No data array found in: {$file}");
                    continue;
                }

                $toDatetime = function ($v) {
                    if (empty($v)) {
                        return now()->toDateTimeString();
                    }
                    try {
                        return Carbon::parse($v)->toDateTimeString();
                    } catch (\Exception $e) {
                        return now()->toDateTimeString();
                    }
                };


                foreach ($rows as $item) {
                    $id = $item['id'] ?? (string) Str::uuid();

                    // create minimal related records if missing so foreign keys won't fail
                    if (!empty($item['donor_id'])) {
                        DB::table('donaturs')->updateOrInsert(
                            ['id' => $item['donor_id']],
                            [
                                'kode' => 'DNT-' . Str::upper(substr($item['donor_id'], 0, 8)),
                                'nama' => $item['donor']['nama'] ?? $item['donor']['name'] ?? 'Imported Donatur ' . substr($item['donor_id'], 0, 8),
                                'jenis_donatur' => json_encode(['perorangan']),
                                'status' => 'aktif',
                                'created_at' => $toDatetime($item['created_at'] ?? null),
                                'updated_at' => $toDatetime($item['updated_at'] ?? null),
                            ]
                        );
                    }

                    if (!empty($item['program_id'])) {
                        DB::table('program')->updateOrInsert(
                            ['id' => $item['program_id']],
                            [
                                'nama_program' => $item['program']['nama_program'] ?? $item['program']['name'] ?? 'Imported Program ' . substr($item['program_id'], 0, 8),
                                'created_at' => $toDatetime($item['created_at'] ?? null),
                                'updated_at' => $toDatetime($item['updated_at'] ?? null),
                            ]
                        );
                    }

                    if (!empty($item['branch_office_id'])) {
                        DB::table('kantor_cabang')->updateOrInsert(
                            ['id' => $item['branch_office_id']],
                            [
                                'kode' => 'BR-' . Str::upper(substr($item['branch_office_id'], 0, 8)),
                                'nama' => $item['branch_office']['nama'] ?? $item['branch_office']['name'] ?? 'Imported Branch ' . substr($item['branch_office_id'], 0, 8),
                                'created_at' => $toDatetime($item['created_at'] ?? null),
                                'updated_at' => $toDatetime($item['updated_at'] ?? null),
                            ]
                        );
                    }

                    if (!empty($item['fundraiser_id'])) {
                        $fundEmail = substr($item['fundraiser_id'], 0, 8) . '@example.com';
                        DB::table('users')->updateOrInsert(
                            ['email' => $fundEmail],
                            [
                                'name' => $item['fundraiser']['name'] ?? 'Imported Fundraiser ' . substr($item['fundraiser_id'], 0, 8),
                                'email' => $fundEmail,
                                'password' => bcrypt('password'),
                                'created_at' => $toDatetime($item['created_at'] ?? null),
                                'updated_at' => $toDatetime($item['updated_at'] ?? null),
                            ]
                        );
                        $mappedFundraiserId = DB::table('users')->where('email', $fundEmail)->value('id');
                    } else {
                        $mappedFundraiserId = null;
                    }

                    $row = [
                        'id' => $id,
                        'kode' => $item['kode'] ?? ('TRX-' . Str::upper(substr($id, 0, 8))),
                        'kantor_cabang_id' => $item['branch_office_id'] ?? null,
                        'donatur_id' => $item['donor_id'] ?? null,
                        'program_id' => $item['program_id'] ?? null,
                        'fundraiser_id' => $mappedFundraiserId ?? ($item['fundraiser_id'] ?? null),
                        'nominal' => $item['amount'] ?? ($item['nominal'] ?? 0),
                        'tanggal_transaksi' => isset($item['transaction_date']) ? Carbon::parse($item['transaction_date'])->toDateString() : ($item['tanggal_transaksi'] ?? now()->toDateString()),
                        'keterangan' => $item['description'] ?? $item['keterangan'] ?? null,
                        'status' => $item['status'] ?? 'verified',
                        'created_at' => $toDatetime($item['created_at'] ?? null),
                        'updated_at' => $toDatetime($item['updated_at'] ?? null),
                    ];

                    // use query builder to ensure we can set the id explicitly
                    DB::table('transaksis')->updateOrInsert(['id' => $id], $row);
                    $imported++;
                }
            }

            $this->command->info("Imported {$imported} transaksi from JSON files in database/seeders/data");
            return;
        }

        // Fallback: original random sample data (kept for compatibility)
        $this->command->warn('No transaksi JSON files found in database/seeders/data — falling back to random sample seed');
        // ensure there are some donatur entries to reference
        if (Donatur::count() < 5) {
            for ($i = 1; $i <= 5; $i++) {
                Donatur::updateOrCreate(
                    ['nama' => "Donatur Sample {$i}"],
                    [
                        'kode' => 'DNT-' . Str::upper(Str::random(6)),
                        'nama' => "Donatur Sample {$i}",
                        'jenis_donatur' => ['perorangan'],
                        'no_handphone' => '62811' . rand(10000000, 99999999),
                        'status' => 'aktif',
                    ]
                );
            }
        }

        $programs = Program::all();
        $branches = KantorCabang::all();

        // find fundraiser users by posisi containing 'fund' (case-insensitive)
        $fundraisers = User::whereRaw("LOWER(posisi) LIKE ?", ['%fund%'])->get();

        // if none, try role named Fundrising (as used in karyawan seeder)
        if ($fundraisers->isEmpty()) {
            $fundraisers = User::whereHas('roles', function ($q) {
                $q->whereRaw("LOWER(name) LIKE ?", ['%fund%']);
            })->get();
        }

        $donaturIds = Donatur::pluck('id')->toArray();
        $programIds = $programs->pluck('id')->toArray();
        $branchIds = $branches->pluck('id')->toArray();

        if (empty($donaturIds) || empty($programIds) || empty($branchIds)) {
            $this->command->warn('Not enough data to create transaksi (need donatur, program, kantor cabang).');
            return;
        }

        // create 30 transactions
        for ($i = 0; $i < 30; $i++) {
            $donaturId = $donaturIds[array_rand($donaturIds)];
            $programId = $programIds[array_rand($programIds)];
            $branchId = $branchIds[array_rand($branchIds)];

            $fundraiserId = null;
            if (!$fundraisers->isEmpty()) {
                $fundraiserId = $fundraisers->random()->id;
            }

            $nominal = rand(100000, 5000000);
            $tanggal = Carbon::today()->subDays(rand(0, 90));

            Transaksi::create([
                'kode' => 'TRX-' . strtoupper(Str::random(8)),
                'kantor_cabang_id' => $branchId,
                'donatur_id' => $donaturId,
                'program_id' => $programId,
                'fundraiser_id' => $fundraiserId,
                'nominal' => $nominal,
                'tanggal_transaksi' => $tanggal,
                'keterangan' => 'Seeded transaksi sample',
                // status must match enum in migration: pending, verified, cancelled
                'status' => 'verified',
            ]);
        }

        $this->command->info('Seeded 30 transaksi (with fundraiser if available)');
    }
}
