<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up()
    {
        // Only run if both source and target tables exist
        if (!Schema::hasTable('remunerasis') || !Schema::hasTable('payroll_periods')) {
            return;
        }

        // Group remunerasis by year and month
        $periods = DB::table('remunerasis')
            ->select('tahun_remunerasi as year', 'bulan_remunerasi as month')
            ->distinct()->get();

        foreach ($periods as $p) {
            $period = DB::table('payroll_periods')->where('year', $p->year)->where('month', $p->month)->first();
            if (!$period) {
                $id = (string)Str::uuid();
                DB::table('payroll_periods')->insert([
                    'id' => (string) Str::uuid(),
                    'year' => $p->year,
                    'month' => $p->month,
                    'status' => 'transferred',
                    'generated_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $period = DB::table('payroll_periods')->where('year', $p->year)->where('month', $p->month)->first();
            }

            $remRows = DB::table('remunerasis')
                ->where('tahun_remunerasi', $p->year)
                ->where('bulan_remunerasi', $p->month)
                ->get();

            foreach ($remRows as $rem) {
                $recordId = (string) Str::uuid();
                DB::table('payroll_records')->insert([
                    'id' => $recordId,
                    'payroll_period_id' => $period->id,
                    'employee_id' => $rem->karyawan_id,
                    'status' => 'transferred',
                    'total_amount' => $rem->take_home_pay ?? ($rem->gaji_pokok ?? 0),
                    'notes' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('payroll_items')->insert([
                    'id' => (string) Str::uuid(),
                    'payroll_record_id' => $recordId,
                    'description' => 'Gaji Pokok',
                    'qty' => 1,
                    'qty_type' => 'fixed',
                    'unit' => 'bulan',
                    'unit_value' => $rem->gaji_pokok ?? 0,
                    'amount' => $rem->gaji_pokok ?? 0,
                    'order_index' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down()
    {
        // no-op; we don't want to remove migrated historical data automatically
    }
};
