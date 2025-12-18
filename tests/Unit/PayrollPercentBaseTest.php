<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\PayrollRecord;
use App\Models\PayrollItem;
use App\Models\PayrollPeriod;
use App\Services\PayrollService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PayrollPercentBaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_percent_item_is_percent_of_its_unit_value()
    {
        $user = User::factory()->create(['tipe_user' => 'karyawan', 'is_active' => true]);
        $period = PayrollPeriod::create(['month' => 1, 'year' => 2026, 'status' => 'generated']);
        $record = PayrollRecord::create(['payroll_period_id' => $period->id, 'employee_id' => $user->id, 'status' => 'pending']);

        $base = PayrollItem::create(['payroll_record_id' => $record->id, 'description' => 'Gaji Pokok', 'qty' => 1, 'qty_type' => 'fixed', 'unit' => 'bulan', 'unit_value' => 1000000, 'amount' => 1000000]);
        // percent now uses its own unit_value: 10% of 100000 = 10000
        $percent = PayrollItem::create(['payroll_record_id' => $record->id, 'description' => 'Dana Kolekan', 'qty' => 10, 'qty_type' => 'percent', 'unit' => '%', 'unit_value' => 100000, 'amount' => 0]);

        $service = app(PayrollService::class);
        $total = $service->recomputeRecordTotal($record);

        $this->assertEquals(1010000, $total);
        $this->assertEquals(10000, PayrollItem::find($percent->id)->amount);
    }
}
