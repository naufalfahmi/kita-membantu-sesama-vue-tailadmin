<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\PayrollPeriod;
use App\Models\PayrollRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

class PayrollDeleteProofTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_delete_transfer_proof()
    {
        Storage::fake('public');

        $admin = User::factory()->create();
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'manage payroll']);
        $admin->givePermissionTo('manage payroll');

        $period = PayrollPeriod::create(['month' => 12, 'year' => 2025, 'status' => 'generated']);
        $employee = User::factory()->create();
        $record = PayrollRecord::create(['payroll_period_id' => $period->id, 'employee_id' => $employee->id, 'status' => 'locked', 'transfer_proof' => 'payroll_proofs/dummy.jpg']);

        Storage::disk('public')->put('payroll_proofs/dummy.jpg', 'contents');
        Storage::disk('public')->assertExists('payroll_proofs/dummy.jpg');

        $res = $this->actingAs($admin)->call('DELETE', "/admin/api/operasional/payroll/periods/{$period->id}/records/{$record->id}/transfer-proof", [], [], [], ['HTTP_X-CSRF-TOKEN' => csrf_token()]);
        $res->assertStatus(200);
        $res->assertJson(['success' => true]);

        $record->refresh();
        $this->assertNull($record->transfer_proof);
        Storage::disk('public')->assertMissing('payroll_proofs/dummy.jpg');
    }

    public function test_non_admin_cannot_delete_transfer_proof()
    {
        Storage::fake('public');

        $viewer = User::factory()->create();

        $period = PayrollPeriod::create(['month' => 12, 'year' => 2025, 'status' => 'generated']);
        $employee = User::factory()->create();
        $record = PayrollRecord::create(['payroll_period_id' => $period->id, 'employee_id' => $employee->id, 'status' => 'locked', 'transfer_proof' => 'payroll_proofs/dummy2.jpg']);

        Storage::disk('public')->put('payroll_proofs/dummy2.jpg', 'contents');
        Storage::disk('public')->assertExists('payroll_proofs/dummy2.jpg');

        $res = $this->actingAs($viewer)->call('DELETE', "/admin/api/operasional/payroll/periods/{$period->id}/records/{$record->id}/transfer-proof", [], [], [], ['HTTP_X-CSRF-TOKEN' => csrf_token()]);
        // should be forbidden via middleware permission
        $res->assertStatus(403);

        $record->refresh();
        $this->assertNotNull($record->transfer_proof);
        Storage::disk('public')->assertExists('payroll_proofs/dummy2.jpg');
    }
}
