<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
                        $donorJenis = null;
                        if (!empty($item['donor']['jenis_donatur'])) {
                            $donorJenis = $item['donor']['jenis_donatur'];
                        } elseif (!empty($item['donor']['jenis'])) {
                            $donorJenis = $item['donor']['jenis'];
                        } elseif (!empty($item['donor']['type'])) {
                            $donorJenis = $item['donor']['type'];
                        }

                        // Normalize to array
                        // If donor provides a tipe_donatur id, prefer that (lookup name from table)
                        $donorJenisArr = null;
                        $tipeId = $item['donor']['tipe_donatur_id'] ?? $item['donor']['tipe_donatur']['id'] ?? $item['donor']['type_id'] ?? $item['donor']['donor_type_id'] ?? $item['donor']['donor_type']['id'] ?? $item['donor_type_id'] ?? $item['donor_type']['id'] ?? null;
                        if ($tipeId) {
                            $tipeNama = DB::table('tipe_donatur')->where('id', $tipeId)->value('nama');
                            if ($tipeNama) {
                                // normalize to lowercase underscore format, e.g. "Kotak infaq" => "kotak_infaq"
                                $donorJenisArr = [Str::slug($tipeNama, '_')];
                            }
                        }

                        // If donor provides donor_type as object or string, use its name if available
                        if (empty($donorJenisArr) && !empty($item['donor']['donor_type'])) {
                            $dt = $item['donor']['donor_type'];
                            if (is_array($dt)) {
                                $dtName = $dt['nama'] ?? $dt['name'] ?? ($dt['nama'] ?? null);
                                if (!empty($dtName)) {
                                    $donorJenisArr = [$dtName];
                                }
                            } elseif (is_string($dt)) {
                                $donorJenisArr = [$dt];
                            }
                        }

                        if (is_null($donorJenisArr)) {
                            if (empty($donorJenis)) {
                                $donorJenisArr = ['perorangan'];
                            } elseif (is_array($donorJenis)) {
                                $donorJenisArr = $donorJenis;
                            } else {
                                $donorJenisArr = [$donorJenis];
                            }
                        }

                        // map pic to internal user id if donor contains pic_id or pic_uuid
                        $picUserId = null;
                        $picUuid = $item['donor']['pic_id'] ?? $item['donor']['pic_uuid'] ?? $item['donor']['pic'] ?? null;
                        if ($picUuid) {
                            $picUserId = DB::table('users')->where('uuid', $picUuid)->value('id');

                            // if user with that UUID doesn't exist, create/update placeholder karyawan user
                            if (!$picUserId) {
                                $next = DB::table('users')->where('name', 'like', 'Karyawan Untitled%')->count() + 1;
                                $name = 'Karyawan Untitled ' . $next;
                                $email = substr($picUuid, 0, 8) . '@example.com';
                                $noInduk = $item['donor']['donor_index_number'] ?? null;

                                // if a user with this email already exists, reuse and ensure uuid/password are set
                                $existing = DB::table('users')->where('email', $email)->first();
                                if ($existing) {
                                    $picUserId = $existing->id;
                                    $updateData = ['uuid' => $picUuid, 'updated_at' => now(), 'password' => Hash::make('password123')];
                                    if ($noInduk) $updateData['no_induk'] = $noInduk;
                                    if ($item['donor']['branch_office_id'] ?? $item['branch_office_id'] ?? null) $updateData['kantor_cabang_id'] = $item['donor']['branch_office_id'] ?? $item['branch_office_id'] ?? null;
                                    DB::table('users')->where('id', $picUserId)->update($updateData);
                                } else {
                                    $picUserId = DB::table('users')->insertGetId([
                                        'name' => $name,
                                        'email' => $email,
                                        'password' => Hash::make('password123'),
                                        'no_induk' => $noInduk,
                                        'posisi' => 'karyawan',
                                        'tipe_user' => 'karyawan',
                                        'is_active' => 1,
                                        'uuid' => $picUuid,
                                        'kantor_cabang_id' => $item['donor']['branch_office_id'] ?? $item['branch_office_id'] ?? null,
                                        'created_at' => now(),
                                        'updated_at' => now(),
                                    ]);
                                }
                            }
                        }

                        // normalize donor status
                        $statusRaw = strtolower($item['donor']['status'] ?? '');
                        if ($statusRaw === 'active') {
                            $status = 'aktif';
                        } elseif ($statusRaw === 'inactive' || $statusRaw === 'tidak active' || $statusRaw === 'not active') {
                            $status = 'tidak_aktif';
                        } elseif ($statusRaw === 'pending') {
                            $status = 'pending';
                        } else {
                            $status = 'aktif';
                        }

                        $tanggalLahir = isset($item['donor']['date_of_birth']) && $item['donor']['date_of_birth'] ? Carbon::parse($item['donor']['date_of_birth'])->toDateString() : null;

                        DB::table('donaturs')->updateOrInsert(
                            ['id' => $item['donor_id']],
                            [
                                'kode' => 'DNT-' . Str::upper(substr($item['donor_id'], 0, 8)),
                                'nama' => $item['donor']['nama'] ?? $item['donor']['name'] ?? 'Imported Donatur ' . substr($item['donor_id'], 0, 8),
                                'jenis_donatur' => json_encode($donorJenisArr),
                                'alamat' => $item['donor']['address'] ?? null,
                                'no_handphone' => $item['donor']['phone_number'] ?? null,
                                'email' => $item['donor']['email'] ?? null,
                                'tanggal_lahir' => $tanggalLahir,
                                'status' => $status,
                                'pic' => $picUserId ? (int) $picUserId : null,
                                'kantor_cabang_id' => $item['donor']['branch_office_id'] ?? $item['branch_office_id'] ?? null,
                                'created_at' => $toDatetime($item['donor']['created_at'] ?? $item['created_at'] ?? null),
                                'updated_at' => $toDatetime($item['donor']['updated_at'] ?? $item['updated_at'] ?? null),
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
