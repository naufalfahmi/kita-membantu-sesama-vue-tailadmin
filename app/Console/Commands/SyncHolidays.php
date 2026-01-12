<?php

namespace App\Console\Commands;

use App\Models\Holiday;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncHolidays extends Command
{
    protected $signature = 'holidays:sync {year?}';

    protected $description = 'Sync national holidays from dayoffapi.vercel.app and store locally';

    public function handle(): int
    {
        $year = $this->argument('year') ?: now('Asia/Jakarta')->year;
        $this->info("Fetching holidays for year {$year}");

        $response = Http::timeout(15)->get('https://dayoffapi.vercel.app/api');
        if ($response->failed()) {
            $this->error('Failed to fetch holiday API: ' . $response->status());
            return self::FAILURE;
        }

        $data = $response->json();
        $count = 0;
        foreach ($data as $item) {
            if (!isset($item['tanggal']) || !str_contains($item['tanggal'], (string) $year)) {
                continue;
            }

            $tanggal = date('Y-m-d', strtotime($item['tanggal']));
            Holiday::updateOrCreate(
                ['tanggal' => $tanggal],
                [
                    'keterangan' => $item['keterangan'] ?? null,
                    'is_cuti' => (bool) ($item['is_cuti'] ?? false),
                    'tahun' => (int) $year,
                    'source' => 'dayoffapi.vercel.app',
                    'raw_payload' => $item,
                ]
            );
            $count++;
        }

        $this->info("Synced {$count} holidays for {$year}");
        Log::info('Holiday sync completed', ['year' => $year, 'count' => $count]);

        return self::SUCCESS;
    }
}
