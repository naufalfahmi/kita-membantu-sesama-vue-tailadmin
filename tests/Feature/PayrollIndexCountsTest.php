<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\PayrollPeriod;
use App\Models\PayrollRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PayrollIndexCountsTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_includes_counts_by_status()
    {
        $user = User::factory()->create();
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'view payroll']);
        $user->givePermissionTo('view payroll');

        $period = PayrollPeriod::create(['month' => 12, 'year' => 2025, 'status' => 'generated']);

        PayrollRecord::create(['payroll_period_id' => $period->id, 'employee_id' => User::factory()->create()->id, 'status' => 'pending']);
        PayrollRecord::create(['payroll_period_id' => $period->id, 'employee_id' => User::factory()->create()->id, 'status' => 'locked']);
        PayrollRecord::create(['payroll_period_id' => $period->id, 'employee_id' => User::factory()->create()->id, 'status' => 'locked']);
        PayrollRecord::create(['payroll_period_id' => $period->id, 'employee_id' => User::factory()->create()->id, 'status' => 'transferred']);

        $res = $this->actingAs($user)->getJson('/admin/api/operasional/payroll/periods?month=12&year=2025');
        $res->assertStatus(200);
        $data = $res->json('data')[0];

        $this->assertEquals(1, $data['pending_count']);
        $this->assertEquals(2, $data['locked_count']);
        $this->assertEquals(1, $data['transferred_count']);
    }
}
