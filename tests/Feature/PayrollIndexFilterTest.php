<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\PayrollPeriod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PayrollIndexFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_filters_by_month_and_year()
    {
        $user = User::factory()->create();
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'view payroll']);
        $user->givePermissionTo('view payroll');

        PayrollPeriod::create(['month' => 11, 'year' => 2025, 'status' => 'generated']);
        PayrollPeriod::create(['month' => 12, 'year' => 2025, 'status' => 'generated']);

        $res = $this->actingAs($user)->getJson('/admin/api/operasional/payroll/periods?month=11&year=2025');
        $res->assertStatus(200);
        $data = $res->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals(11, $data[0]['month']);
    }
}
