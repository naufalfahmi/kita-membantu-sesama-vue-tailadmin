<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\PayrollPeriod;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PayrollGenerateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // ensure permissions
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'generate payroll']);
    }

    public function test_generate_returns_422_when_period_already_generated()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('generate payroll');

        $period = PayrollPeriod::create(['month' => 11, 'year' => 2025, 'status' => 'generated']);

        $res = $this->actingAs($user)->postJson('/admin/api/operasional/payroll/periods/generate', ['year' => 2025, 'month' => 11]);
        $res->assertStatus(422);
        $res->assertJsonPath('message', 'Payroll period already generated or transferred');
        $res->assertJsonPath('period.id', $period->id);
        $res->assertJsonPath('period.status', 'generated');
    }
}
