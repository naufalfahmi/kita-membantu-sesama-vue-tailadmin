<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\PayrollPeriod;
use App\Models\PayrollRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PayrollUploadProofEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_transfer_proof_via_endpoint()
    {
        Storage::fake('public');

        $admin = User::factory()->create();
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'manage payroll']);
        $admin->givePermissionTo('manage payroll');

        $period = PayrollPeriod::create(['month' => 12, 'year' => 2025, 'status' => 'generated']);
        $record = PayrollRecord::create(['payroll_period_id' => $period->id, 'employee_id' => User::factory()->create()->id, 'status' => 'locked']);

        $file = UploadedFile::fake()->image('proof_ep.jpg');

        $res = $this->actingAs($admin)->call('POST', "/admin/api/operasional/payroll/periods/{$period->id}/records/{$record->id}/transfer-proof", ['status' => 'transferred'], [], ['transfer_proof' => $file], ['HTTP_X-CSRF-TOKEN' => csrf_token()]);
        $res->assertStatus(200);
        $res->assertJson(['success' => true]);

        $record->refresh();
        $this->assertNotNull($record->transfer_proof);
        Storage::disk('public')->assertExists($record->transfer_proof);
        $this->assertEquals('transferred', $record->status);
    }

    public function test_replace_transfer_proof_deletes_old_file()
    {
        Storage::fake('public');

        $admin = User::factory()->create();
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'manage payroll']);
        $admin->givePermissionTo('manage payroll');

        $period = PayrollPeriod::create(['month' => 12, 'year' => 2025, 'status' => 'generated']);
        $record = PayrollRecord::create(['payroll_period_id' => $period->id, 'employee_id' => User::factory()->create()->id, 'status' => 'locked', 'transfer_proof' => 'payroll_proofs/old.jpg']);

        // create existing file
        Storage::disk('public')->put('payroll_proofs/old.jpg', 'old');
        Storage::disk('public')->assertExists('payroll_proofs/old.jpg');

        $newFile = UploadedFile::fake()->image('new.jpg');

        $res = $this->actingAs($admin)->call('POST', "/admin/api/operasional/payroll/periods/{$period->id}/records/{$record->id}/transfer-proof", [], [], ['transfer_proof' => $newFile], ['HTTP_X-CSRF-TOKEN' => csrf_token()]);
        $res->assertStatus(200);
        $res->assertJson(['success' => true]);

        $record->refresh();
        $this->assertNotNull($record->transfer_proof);
        Storage::disk('public')->assertExists($record->transfer_proof);
        Storage::disk('public')->assertMissing('payroll_proofs/old.jpg');
    }
}
