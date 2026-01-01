<?php

namespace App\Http\Controllers;

use App\Models\PayrollRecord;
use Illuminate\Http\Request;

class PayrollSlipController extends Controller
{
    public function slip($periodId, $recordId, Request $request)
    {
        $record = PayrollRecord::with(['items', 'employee', 'period'])->where('payroll_period_id', $periodId)->where('id', $recordId)->first();

        // Log request for debugging (temporary)
        \Illuminate\Support\Facades\Log::info('PayrollSlipController: slip requested', [
            'periodId' => $periodId,
            'recordId' => $recordId,
            'user_id' => auth()->id(),
            'ip' => request()->ip(),
            'query' => request()->query(),
        ]);

        if (!$record) {
            \Illuminate\Support\Facades\Log::warning('PayrollSlipController: record not found', ['periodId' => $periodId, 'recordId' => $recordId]);
            abort(404);
        }

        // Ensure amounts are up-to-date (recompute to correct any legacy sign issues)
        $record->load('items');
        try {
            $service = app(\App\Services\PayrollService::class);
            $service->recomputeRecordTotal($record);
            $record->load('items');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('PayrollSlipController: failed to recompute record totals', ['exception' => $e->getMessage(), 'record_id' => $record->id]);
        }

        // Filter out items with qty == 0 (don't show in slips)
        $items = $record->items->filter(function($it) {
            return (int) ($it->qty ?? 0) !== 0;
        })->values();

        // Group items into Penghasilan, Fundraising, Other (Lain-lain), and Potongan
        $penghasilan = collect();
        $fundraising = collect();
        $other = collect();
        $deductions = collect();

        $pengKeys = ['gaji pokok','uang makan','uang transport'];
        $fundKeys = ['dana kolekan','kotak home','kotak publik','qurban'];
        $otherKeys = ['tunjangan jabatan','thr','perjalanan dinas','lembur'];
        $dedKeys = ['tidak masuk','terlambat'];

        foreach ($items as $it) {
            $desc = strtolower(trim((string)($it->description ?? '')));
            if (in_array($desc, $pengKeys)) {
                $penghasilan->push($it);
                continue;
            }
            if (in_array($desc, $fundKeys)) {
                $fundraising->push($it);
                continue;
            }
            if (in_array($desc, $otherKeys)) {
                $other->push($it);
                continue;
            }
            if (in_array($desc, $dedKeys) || ((int)($it->amount ?? 0) < 0)) {
                $deductions->push($it);
                continue;
            }
            // default: treat positive amounts as other earnings
            if ((int)($it->amount ?? 0) >= 0) {
                $other->push($it);
            } else {
                $deductions->push($it);
            }
        }

        $pengTotal = $penghasilan->sum('amount');
        $fundTotal = $fundraising->sum('amount');
        $otherTotal = $other->sum('amount');
        $deductionsTotal = abs($deductions->sum('amount'));
        $gross = $pengTotal + $fundTotal + $otherTotal;
        $net = $gross - $deductionsTotal;
        // Try to include a site logo if available (public/favicon.svg)
        $logoSvg = null;
        try {
            $faviconPath = public_path('favicon.svg');
            if (file_exists($faviconPath)) {
                $logoSvg = file_get_contents($faviconPath);
            }
        } catch (\Throwable $e) {
            // ignore if unable to read file
        }

        // If explicitly requested for HTML view (format=html), return the blade for manual print.
        if ($request->query('format') === 'html') {
            return view('admin.payroll.slip_template', compact('record', 'items', 'penghasilan', 'fundraising', 'other', 'deductions', 'pengTotal', 'fundTotal', 'otherTotal', 'deductionsTotal', 'gross', 'net', 'logoSvg'));
        }

        // Otherwise generate PDF and return inline so browser opens it in a new tab
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.payroll.slip_template', compact('record', 'items', 'penghasilan', 'fundraising', 'other', 'deductions', 'pengTotal', 'fundTotal', 'otherTotal', 'deductionsTotal', 'gross', 'net', 'logoSvg'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream("slip-{$record->id}.pdf");
    }
}
