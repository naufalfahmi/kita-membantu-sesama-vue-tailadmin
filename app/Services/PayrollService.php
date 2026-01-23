<?php

namespace App\Services;

use App\Models\PayrollPeriod;
use App\Models\PayrollRecord;
use App\Models\PayrollItem;
use App\Models\User;
use App\Models\Remunerasi;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PayrollService
{
    /**
     * Generate payroll period for a given year and month.
     * Creates PayrollPeriod, PayrollRecord per active employee, and initial PayrollItem(s).
     * Re-generating an existing period in 'draft' will delete its records and recreate.
     * If period already 'generated' or 'transferred' this will throw an exception.
     */
    public function generatePeriod(int $year, int $month, ?int $actorId = null): PayrollPeriod
    {
        return DB::transaction(function () use ($year, $month, $actorId) {
            $period = PayrollPeriod::where('year', $year)->where('month', $month)->first();

            // If period exists and is 'transferred' we should not modify it.
            if ($period && $period->status === 'transferred') {
                throw new \RuntimeException('Payroll period already generated or transferred');
            }

            // Keep track of existing employee records so we only add missing ones
            $existingEmployeeIds = [];

            if (!$period) {
                $period = PayrollPeriod::create([
                    'year' => $year,
                    'month' => $month,
                    'status' => 'generated',
                    'generated_at' => now(),
                    'created_by' => $actorId,
                ]);
            } else {
                // If period exists and is 'generated', don't delete existing records — only add missing employees.
                if ($period->status === 'generated') {
                    $existingEmployeeIds = $period->records()->pluck('employee_id')->map(function ($id) {
                        return (string)$id;
                    })->toArray();
                    $period->update(['generated_at' => now()]);
                } else {
                    // cleanup existing draft records and mark generated
                    $period->records()->delete();
                    $period->update(['status' => 'generated', 'generated_at' => now()]);
                }
            }

            // fetch all active employees (karyawan)
            $employees = User::karyawan()->active()->get();

            foreach ($employees as $emp) {
                // skip if employee already has a record in this period
                if (in_array((string)$emp->id, $existingEmployeeIds, true)) {
                    continue;
                }

                $record = PayrollRecord::create([
                    'payroll_period_id' => $period->id,
                    'employee_id' => $emp->id,
                    'status' => PayrollRecord::STATUS_PENDING,
                    'total_amount' => 0,
                    'created_by' => $actorId,
                ]);

                // Try find existing Remunerasi entry as base salary for the given month/year
                $gajiValue = 0;
                if (\Illuminate\Support\Facades\Schema::hasTable('remunerasis')) {
                    $rem = Remunerasi::where('karyawan_id', $emp->id)
                        ->where('bulan_remunerasi', $month)
                        ->where('tahun_remunerasi', $year)
                        ->first();

                    if ($rem) {
                        $gajiValue = (int)($rem->gaji_pokok ?? 0);
                    }
                }

                // create single payroll item for gaji pokok (use found value or 0)
                PayrollItem::create([
                    'payroll_record_id' => $record->id,
                    'description' => 'Gaji Pokok',
                    'qty' => 1,
                    'qty_type' => 'fixed',
                    'unit' => 'bulan',
                    'unit_value' => $gajiValue,
                    'amount' => $gajiValue,
                    'order_index' => 1,
                ]);

                $record->total_amount = $gajiValue;
                $record->save();
            }

            return $period->load('records.items');
        });
    }

    /**
     * Recomputes total of a payroll record based on its items.
     */
    public function recomputeRecordTotal(PayrollRecord $record): int
    {
        $total = 0;
        foreach ($record->items as $item) {
            $amount = $this->computeItemAmount($item, $record);
            $item->amount = (int)round($amount);
            $item->save();
            $total += $item->amount;
        }

        $record->total_amount = $total;
        $record->save();

        return $total;
    }

    /**
     * Compute single item amount.
     * - For 'multiplier' (previously fixed) we multiply qty * unit_value
     * - For 'percent' we treat qty as a percentage of the item's own unit_value (qty% * unit_value)
     */
    public function computeItemAmount(PayrollItem $item, PayrollRecord $record): float
    {
        if ($item->qty_type === 'fixed' || $item->qty_type === 'multiplier') {
            $amount = (float)$item->qty * (float)$item->unit_value;
            $desc = strtolower(trim((string)($item->description ?? '')));
            $deductions = ['tidak masuk', 'terlambat'];
            if (in_array($desc, $deductions) && $amount > 0) {
                return -abs($amount);
            }
            return $amount;
        }

        if ($item->qty_type === 'percent') {
            // percent now applies to the item's own unit_value
            $amount = ($item->qty / 100.0) * (float)$item->unit_value;
            $desc = strtolower(trim((string)($item->description ?? '')));
            $deductions = ['tidak masuk', 'terlambat'];
            if (in_array($desc, $deductions) && $amount > 0) {
                return -abs($amount);
            }
            return $amount;
        }

        return 0.0;
    }
}
