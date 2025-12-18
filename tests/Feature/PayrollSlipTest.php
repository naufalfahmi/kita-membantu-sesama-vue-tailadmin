<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\PayrollPeriod;
use App\Models\PayrollRecord;
use App\Models\PayrollItem;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PayrollSlipTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_is_redirected_to_signin()
    {
        $period = PayrollPeriod::create(['month' => 1, 'year' => 2026, 'status' => 'generated']);
        $record = PayrollRecord::create(['payroll_period_id' => $period->id, 'employee_id' => null, 'status' => 'pending']);

        $res = $this->get("/admin/operasional/payroll/periods/{$period->id}/records/{$record->id}/slip");

        $res->assertStatus(302);
        $this->assertStringContainsString('/admin/signin', $res->headers->get('Location'));
    }

    public function test_authenticated_user_receives_pdf()
    {
        $user = User::factory()->create();
        $period = PayrollPeriod::create(['month' => 1, 'year' => 2026, 'status' => 'generated', 'created_by' => $user->id]);
        $record = PayrollRecord::create(['payroll_period_id' => $period->id, 'employee_id' => $user->id, 'status' => 'locked']);

        PayrollItem::create(['payroll_record_id' => $record->id, 'description' => 'Gaji Pokok', 'qty' => 1, 'unit_value' => 5000000, 'amount' => 5000000, 'order_index' => 0]);

        $res = $this->actingAs($user)->get("/admin/operasional/payroll/periods/{$period->id}/records/{$record->id}/slip");

        $res->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $res->headers->get('content-type'));
        // ensure PDF filename in content-disposition header
        $this->assertStringContainsString('slip-', $res->headers->get('content-disposition'));
        $this->assertStringContainsString('.pdf', $res->headers->get('content-disposition'));

    }

    public function test_html_preview_has_header()
    {
        $user = User::factory()->create();
        $period = PayrollPeriod::create(['month' => 1, 'year' => 2026, 'status' => 'generated', 'created_by' => $user->id]);
        $record = PayrollRecord::create(['payroll_period_id' => $period->id, 'employee_id' => $user->id, 'status' => 'locked']);
        PayrollItem::create(['payroll_record_id' => $record->id, 'description' => 'Gaji Pokok', 'qty' => 1, 'unit_value' => 5000000, 'amount' => 5000000, 'order_index' => 0]);

        PayrollItem::create(['payroll_record_id' => $record->id, 'description' => 'Tidak tampil', 'qty' => 0, 'unit_value' => 0, 'amount' => 0, 'order_index' => 1]);
        PayrollItem::create(['payroll_record_id' => $record->id, 'description' => 'BPJS Kesehatan', 'qty' => 1, 'unit_value' => 0, 'amount' => -150000, 'order_index' => 1]);

        $res = $this->actingAs($user)->get("/admin/operasional/payroll/periods/{$period->id}/records/{$record->id}/slip?format=html");
        $res->assertStatus(200);
        $res->assertSee('SLIP GAJI');
        $res->assertSee('Nama Karyawan');
        $res->assertSee('Pendapatan');
        $res->assertSee('Potongan');
        $res->assertSee('Rp 5.000.000');
        $res->assertSee('Rp 150.000');
        $res->assertSee('Rp 4.850.000');
        $res->assertSee(config('app.name'));
        $res->assertDontSee('Tidak tampil');
    }
}

