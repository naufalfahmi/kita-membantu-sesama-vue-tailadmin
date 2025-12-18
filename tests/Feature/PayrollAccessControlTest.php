<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\PayrollPeriod;
use App\Models\PayrollRecord;
use App\Models\PayrollItem;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PayrollAccessControlTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Ensure relevant permissions exist in the test DB
        $perms = ['manage payroll','view payroll','create payroll','update payroll','delete payroll','create remunerasi','update remunerasi','delete remunerasi'];
        foreach ($perms as $p) { \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $p]); }
    }

    public function test_manage_permission_sees_all_records()
    {
        $manager = User::factory()->create();
        $manager->givePermissionTo('create payroll'); // admin-like

        $period = PayrollPeriod::create(['month' => 1, 'year' => 2026, 'status' => 'generated']);

        $a = User::factory()->create(['name' => 'Alice']);
        $b = User::factory()->create(['name' => 'Bob']);

        $ra = PayrollRecord::create(['payroll_period_id' => $period->id, 'employee_id' => $a->id, 'status' => 'locked']);
        $rb = PayrollRecord::create(['payroll_period_id' => $period->id, 'employee_id' => $b->id, 'status' => 'locked']);

        $res = $this->actingAs($manager)->getJson("/admin/api/operasional/payroll/periods/{$period->id}");
        $res->assertStatus(200);
        $data = $res->json('data');
        $this->assertCount(2, $data['records']);
    }

    public function test_view_permission_sees_only_own_record_and_cannot_view_others()
    {
        $viewer = User::factory()->create();
        $viewer->givePermissionTo('view payroll');

        $period = PayrollPeriod::create(['month' => 2, 'year' => 2026, 'status' => 'generated']);

        $me = $viewer;
        $other = User::factory()->create();

        $mine = PayrollRecord::create(['payroll_period_id' => $period->id, 'employee_id' => $me->id, 'status' => 'locked']);
        $theirs = PayrollRecord::create(['payroll_period_id' => $period->id, 'employee_id' => $other->id, 'status' => 'locked']);

        $res = $this->actingAs($viewer)->getJson("/admin/api/operasional/payroll/periods/{$period->id}");
        $res->assertStatus(200);
        $data = $res->json('data');
        $this->assertCount(1, $data['records']);
        $this->assertEquals($me->id, $data['records'][0]['employee']['id']);

        // trying to fetch other record directly should be forbidden
        $res2 = $this->actingAs($viewer)->getJson("/admin/api/operasional/payroll/periods/{$period->id}/records/{$theirs->id}");
        $res2->assertStatus(403);
    }

    public function test_me_endpoint_returns_latest_record_for_viewer()
    {
        $viewer = User::factory()->create();
        $viewer->givePermissionTo('view payroll');

        $period1 = PayrollPeriod::create(['month' => 1, 'year' => 2026, 'status' => 'generated']);
        $period2 = PayrollPeriod::create(['month' => 2, 'year' => 2026, 'status' => 'generated']);

        $r1 = PayrollRecord::create(['payroll_period_id' => $period1->id, 'employee_id' => $viewer->id, 'status' => 'locked', 'total' => 1000]);
        $r2 = PayrollRecord::create(['payroll_period_id' => $period2->id, 'employee_id' => $viewer->id, 'status' => 'locked', 'total' => 2000]);

        $res = $this->actingAs($viewer)->getJson('/admin/api/operasional/payroll/me');
        $res->assertStatus(200);
        $res->assertJsonPath('data.period.id', $period2->id);
        $res->assertJsonPath('data.record.id', $r2->id);
    }

    public function test_my_records_list_returns_all_user_records()
    {
        $viewer = User::factory()->create();
        $viewer->givePermissionTo('view payroll');

        $p1 = PayrollPeriod::create(['month' => 1, 'year' => 2026, 'status' => 'generated']);
        $p2 = PayrollPeriod::create(['month' => 2, 'year' => 2026, 'status' => 'generated']);

        $r1 = PayrollRecord::create(['payroll_period_id' => $p1->id, 'employee_id' => $viewer->id, 'status' => 'locked', 'total_amount' => 100]);
        $r2 = PayrollRecord::create(['payroll_period_id' => $p2->id, 'employee_id' => $viewer->id, 'status' => 'locked', 'total_amount' => 200]);

        $res = $this->actingAs($viewer)->getJson('/admin/api/operasional/payroll/me/list');
        $res->assertStatus(200);
        $this->assertCount(2, $res->json('data'));
        // order: latest (r2) first
        $this->assertEquals(200, $res->json('data')[0]['total']);
        $this->assertEquals(100, $res->json('data')[1]['total']);
    }
}
