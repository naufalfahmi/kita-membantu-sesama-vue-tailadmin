<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\PayrollPeriod;
use App\Models\PayrollRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PayrollTransferProofTest extends TestCase
{
    use RefreshDatabase;

    public function test_upload_transfer_proof_and_save_record()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'manage payroll']);
        $user->givePermissionTo('manage payroll');

        $period = PayrollPeriod::create(['month' => 12, 'year' => 2025, 'status' => 'generated']);
        $record = PayrollRecord::create(['payroll_period_id' => $period->id, 'employee_id' => User::factory()->create()->id, 'status' => 'pending']);

        $file = UploadedFile::fake()->image('proof.jpg');

        $res = $this->actingAs($user)->call('PUT', "/admin/api/operasional/payroll/periods/{$period->id}/records/{$record->id}", [], [], ['transfer_proof' => $file], ['HTTP_X-CSRF-TOKEN' => csrf_token()]);
        $res->assertStatus(200);
        $res->assertJson(['success' => true]);

        $record->refresh();
        $this->assertNotNull($record->transfer_proof);
        Storage::disk('public')->assertExists($record->transfer_proof);
    }

    public function test_upload_transfer_proof_and_set_transferred_status()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'manage payroll']);
        $user->givePermissionTo('manage payroll');

        $period = PayrollPeriod::create(['month' => 11, 'year' => 2025, 'status' => 'generated']);
        $record = PayrollRecord::create(['payroll_period_id' => $period->id, 'employee_id' => User::factory()->create()->id, 'status' => 'locked']);

        $file = UploadedFile::fake()->image('proof2.jpg');

        // send status as form parameter and file in the files array
        $res = $this->actingAs($user)->call('PUT', "/admin/api/operasional/payroll/periods/{$period->id}/records/{$record->id}", ['status' => 'transferred'], [], ['transfer_proof' => $file], ['HTTP_X-CSRF-TOKEN' => csrf_token()]);
        $res->assertStatus(200);
        $res->assertJson(['success' => true]);

        $record->refresh();
        $this->assertEquals('transferred', $record->status);
        $this->assertNotNull($record->transfer_proof);
        Storage::disk('public')->assertExists($record->transfer_proof);
    }

    public function test_my_records_includes_transfer_proof_flag()
    {
        $user = User::factory()->create();
        $period = PayrollPeriod::create(['month' => 10, 'year' => 2025, 'status' => 'generated']);
        $record = PayrollRecord::create(['payroll_period_id' => $period->id, 'employee_id' => $user->id, 'status' => 'pending', 'transfer_proof' => 'payroll_proofs/proof.jpg']);

        $res = $this->actingAs($user)->getJson('/admin/api/operasional/payroll/me/list');
        $res->assertStatus(200);
        $res->assertJson(['success' => true]);
        $json = $res->json();
        $this->assertCount(1, $json['data']);
        $this->assertEquals($record->id, $json['data'][0]['id']);
        $this->assertTrue($json['data'][0]['has_transfer_proof']);
        $this->assertEquals('payroll_proofs/proof.jpg', $json['data'][0]['transfer_proof']);
    }
}

