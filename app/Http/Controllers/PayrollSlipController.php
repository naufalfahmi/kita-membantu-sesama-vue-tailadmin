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

        // Ensure amounts are up-to-date
        $record->load('items');

        // Filter out items with qty == 0 (don't show in slips)
        $items = $record->items->filter(function($it) {
            return (int) ($it->qty ?? 0) !== 0;
        })->values();

        // Separate earnings (>=0) and deductions (<0)
        $earnings = $items->filter(function($it) { return (int) ($it->amount ?? 0) >= 0; })->values();
        $deductions = $items->filter(function($it) { return (int) ($it->amount ?? 0) < 0; })->values();

        $gross = $earnings->sum('amount');
        $deductionsTotal = abs($deductions->sum('amount'));
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
            return view('admin.payroll.slip_template', compact('record', 'items', 'earnings', 'deductions', 'gross', 'deductionsTotal', 'net', 'logoSvg'));
        }

        // Otherwise generate PDF and return inline so browser opens it in a new tab
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.payroll.slip_template', compact('record', 'items', 'earnings', 'deductions', 'gross', 'deductionsTotal', 'net', 'logoSvg'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream("slip-{$record->id}.pdf");
    }
}
