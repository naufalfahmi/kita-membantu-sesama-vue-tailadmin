<?php

namespace App\Jobs;

use App\Models\PayrollPeriod;
use Illuminate\Bus\Dispatchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ExecutePayrollTransfer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $periodId;

    public function __construct(string $periodId)
    {
        $this->periodId = $periodId;
    }

    public function handle()
    {
        $period = PayrollPeriod::with('records')->find($this->periodId);
        if (!$period) return;

        // Placeholder for real transfer logic (e.g., export to bank file, call external API)
        foreach ($period->records as $rec) {
            // In real implementation: perform transfer, create payment logs, handle failures.
            Log::info('ExecutePayrollTransfer: processed record', ['record_id' => $rec->id, 'employee_id' => $rec->employee_id, 'amount' => $rec->total_amount]);
            $rec->status = 'transferred';
            $rec->save();
        }

        $period->status = 'transferred';
        $period->save();

        Log::info('ExecutePayrollTransfer: completed for period', ['period_id' => $this->periodId]);
    }
}
