<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\PayrollPeriod;
use App\Models\PayrollRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PayrollRecordDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_record_includes_period_and_employee_no_induk()
    {
        $viewer = User::factory()->create(['no_induk' => 'KMS-001']);
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'view payroll']);
        $viewer->givePermissionTo('view payroll');

        $period = PayrollPeriod::create(['month' => 11, 'year' => 2025, 'status' => 'generated']);
        $record = PayrollRecord::create(['payroll_period_id' => $period->id, 'employee_id' => $viewer->id, 'status' => 'locked']);

        $res = $this->actingAs($viewer)->getJson("/admin/api/operasional/payroll/periods/{$period->id}/records/{$record->id}");
        $res->assertStatus(200);
        $res->assertJsonPath('data.employee.no_induk', 'KMS-001');
        $res->assertJsonPath('data.period.month', 11);
        $res->assertJsonPath('data.period.year', 2025);
    }
}
