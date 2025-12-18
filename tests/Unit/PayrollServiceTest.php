<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Services\PayrollService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PayrollServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_generate_period_creates_records_for_active_karyawan()
    {
        // create 2 active karyawan
        User::factory()->create(['tipe_user' => 'karyawan', 'is_active' => true]);
        User::factory()->create(['tipe_user' => 'karyawan', 'is_active' => true]);

        $service = app(PayrollService::class);
        $period = $service->generatePeriod(2026, 2, 1);

        $this->assertEquals(2, $period->records()->count());
    }
}
