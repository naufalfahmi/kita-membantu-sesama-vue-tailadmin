<?php

namespace App\Http\Controllers;

use App\Models\Penyaluran;
use App\Models\PengajuanDanaDisbursement;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class LaporanKeuanganController extends Controller
{
    /**
     * Return aggregated keuangan data and transactions for a date range.
     *
     * Query params:
     * - start (YYYY-MM-DD)
     * - end (YYYY-MM-DD)
     * - page, per_page
     */
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->can('view laporan keuangan')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $start = $request->query('start');
        $end = $request->query('end');

        try {
            $endDate = $end ? Carbon::parse($end)->endOfDay() : Carbon::now()->endOfDay();
            $startDate = $start ? Carbon::parse($start)->startOfDay() : $endDate->copy()->startOfMonth();
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Invalid date format'], 422);
        }

        // Totals in range
        $incomingInRange = Transaksi::whereBetween('tanggal_transaksi', [$startDate->toDateString(), $endDate->toDateString()])
            ->sum('nominal');

        $disbursementsInRange = PengajuanDanaDisbursement::whereBetween('tanggal_disburse', [$startDate->toDateString(), $endDate->toDateString()])
            ->sum('amount');

        $penyaluranInRange = Penyaluran::whereBetween(DB::raw('DATE(created_at)'), [$startDate->toDateString(), $endDate->toDateString()])
            ->sum('amount');

        $outgoingInRange = $disbursementsInRange + $penyaluranInRange;

        // Totals before start (for opening balance)
        $incomingBefore = Transaksi::where('tanggal_transaksi', '<', $startDate->toDateString())->sum('nominal');
        $disbursementsBefore = PengajuanDanaDisbursement::where('tanggal_disburse', '<', $startDate->toDateString())->sum('amount');
        $penyaluranBefore = Penyaluran::where(DB::raw('DATE(created_at)'), '<', $startDate->toDateString())->sum('amount');
        $outgoingBefore = $disbursementsBefore + $penyaluranBefore;

        $saldoAwal = (float)$incomingBefore - (float)$outgoingBefore;
        $saldoAkhir = $saldoAwal + (float)$incomingInRange - (float)$outgoingInRange;

        // Build transactions list (incoming and outgoing unified)
        $incomingRows = Transaksi::whereBetween('tanggal_transaksi', [$startDate->toDateString(), $endDate->toDateString()])
            ->orderBy('tanggal_transaksi', 'asc')
            ->get(['id', 'tanggal_transaksi', 'keterangan', 'nominal'])
            ->map(function ($t) {
                return [
                    'id' => $t->id,
                    'tanggal' => $t->tanggal_transaksi ? $t->tanggal_transaksi->format('Y-m-d') : null,
                    'keterangan' => $t->keterangan ?: ('Transaksi ' . ($t->id ?? '')), 
                    'masuk' => (float)$t->nominal,
                    'keluar' => 0.0,
                    'source' => 'transaksi',
                ];
            })->toArray();

        $disbursementRows = PengajuanDanaDisbursement::whereBetween('tanggal_disburse', [$startDate->toDateString(), $endDate->toDateString()])
            ->orderBy('tanggal_disburse', 'asc')
            ->get(['id', 'tanggal_disburse', 'amount', 'pengajuan_dana_id'])
            ->map(function ($d) {
                return [
                    'id' => $d->id,
                    'tanggal' => $d->tanggal_disburse ? $d->tanggal_disburse->format('Y-m-d') : null,
                    'keterangan' => 'Pengajuan Dana',
                    'masuk' => 0.0,
                    'keluar' => (float)$d->amount,
                    'source' => 'disbursement',
                ];
            })->toArray();

        $penyaluranRows = Penyaluran::whereBetween(DB::raw('DATE(created_at)'), [$startDate->toDateString(), $endDate->toDateString()])
            ->orderBy('created_at', 'asc')
            ->get(['id', 'amount', 'program_name', 'created_at'])
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'tanggal' => $p->created_at ? $p->created_at->toDateString() : null,
                    'keterangan' => 'Penyaluran: ' . ($p->program_name ?: ''),
                    'masuk' => 0.0,
                    'keluar' => (float)$p->amount,
                    'source' => 'penyaluran',
                ];
            })->toArray();

        $rows = array_merge($incomingRows, $disbursementRows, $penyaluranRows);

        // Sort by tanggal asc (nulls at end)
        usort($rows, function ($a, $b) {
            return strcmp($a['tanggal'] ?? '', $b['tanggal'] ?? '');
        });

        // Compute running balance
        $running = $saldoAwal;
        foreach ($rows as &$r) {
            $running += ($r['masuk'] - $r['keluar']);
            $r['saldo'] = $running;
        }
        unset($r);

        // Simple pagination (memory based) to keep API implementation simple
        $page = max(1, (int)$request->query('page', 1));
        $perPage = max(1, min(200, (int)$request->query('per_page', 20)));
        $total = count($rows);
        $slice = array_slice($rows, ($page - 1) * $perPage, $perPage);

        $paginator = new LengthAwarePaginator($slice, $total, $perPage, $page, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'totals' => [
                    'saldo_awal' => (float)$saldoAwal,
                    'totalMasuk' => (float)$incomingInRange,
                    'totalKeluar' => (float)$outgoingInRange,
                    'saldo_akhir' => (float)$saldoAkhir,
                ],
                'transactions' => $paginator->items(),
                'pagination' => [
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                ],
            ],
        ]);
    }
}
