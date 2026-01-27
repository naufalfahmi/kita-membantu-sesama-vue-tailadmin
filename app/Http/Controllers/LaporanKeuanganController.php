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
        $programId = $request->query('program_id');
        $kantorCabangId = $request->query('kantor_cabang_id');

        try {
            $endDate = $end ? Carbon::parse($end)->endOfDay() : Carbon::now()->endOfDay();
            $startDate = $start ? Carbon::parse($start)->startOfDay() : $endDate->copy()->startOfMonth();
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Invalid date format'], 422);
        }

        // Totals in range
        $incomingInRangeQuery = Transaksi::whereBetween('tanggal_transaksi', [$startDate->toDateString(), $endDate->toDateString()]);
        if ($programId) {
            $incomingInRangeQuery->where('program_id', $programId);
        }
        if ($kantorCabangId) {
            $incomingInRangeQuery->where('kantor_cabang_id', $kantorCabangId);
        }
        $incomingInRange = $incomingInRangeQuery->sum('nominal');

        $disbursementsInRangeQuery = PengajuanDanaDisbursement::whereBetween('tanggal_disburse', [$startDate->toDateString(), $endDate->toDateString()]);
        if ($programId) {
            $disbursementsInRangeQuery->where('program_id', $programId);
        }
        if ($kantorCabangId) {
            $disbursementsInRangeQuery->whereHas('pengajuan', function ($q) use ($kantorCabangId) {
                $q->where('kantor_cabang_id', $kantorCabangId);
            });
        }
        $disbursementsInRange = $disbursementsInRangeQuery->sum('amount');

        $penyaluranInRangeQuery = Penyaluran::whereBetween(DB::raw('DATE(created_at)'), [$startDate->toDateString(), $endDate->toDateString()]);
        if ($programId) {
            $penyaluranInRangeQuery->whereHas('pengajuan', function ($q) use ($programId) {
                $q->where('program_id', $programId);
            });
        }
        if ($kantorCabangId) {
            $penyaluranInRangeQuery->where('kantor_cabang_id', $kantorCabangId);
        }
        $penyaluranInRange = $penyaluranInRangeQuery->sum('amount');

        $outgoingInRange = $disbursementsInRange + $penyaluranInRange;

        // Totals before start (for opening balance)
        $incomingBeforeQuery = Transaksi::where('tanggal_transaksi', '<', $startDate->toDateString());
        if ($programId) {
            $incomingBeforeQuery->where('program_id', $programId);
        }
        if ($kantorCabangId) {
            $incomingBeforeQuery->where('kantor_cabang_id', $kantorCabangId);
        }
        $incomingBefore = $incomingBeforeQuery->sum('nominal');

        $disbursementsBeforeQuery = PengajuanDanaDisbursement::where('tanggal_disburse', '<', $startDate->toDateString());
        if ($programId) {
            $disbursementsBeforeQuery->where('program_id', $programId);
        }
        if ($kantorCabangId) {
            $disbursementsBeforeQuery->whereHas('pengajuan', function ($q) use ($kantorCabangId) {
                $q->where('kantor_cabang_id', $kantorCabangId);
            });
        }
        $disbursementsBefore = $disbursementsBeforeQuery->sum('amount');

        $penyaluranBeforeQuery = Penyaluran::where(DB::raw('DATE(created_at)'), '<', $startDate->toDateString());
        if ($programId) {
            $penyaluranBeforeQuery->whereHas('pengajuan', function ($q) use ($programId) {
                $q->where('program_id', $programId);
            });
        }
        if ($kantorCabangId) {
            $penyaluranBeforeQuery->where('kantor_cabang_id', $kantorCabangId);
        }
        $penyaluranBefore = $penyaluranBeforeQuery->sum('amount');
        $outgoingBefore = $disbursementsBefore + $penyaluranBefore;

        $saldoAwal = (float)$incomingBefore - (float)$outgoingBefore;
        $saldoAkhir = $saldoAwal + (float)$incomingInRange - (float)$outgoingInRange;

        // Breakdown data for comprehensive reporting
        $breakdown = [
            'pengajuan_dana' => (float)$disbursementsInRange,
            'penyaluran' => (float)$penyaluranInRange,
            'pengajuan_percentage' => $outgoingInRange > 0 ? round(($disbursementsInRange / $outgoingInRange) * 100, 2) : 0,
            'penyaluran_percentage' => $outgoingInRange > 0 ? round(($penyaluranInRange / $outgoingInRange) * 100, 2) : 0,
        ];

        // Build transactions list (incoming and outgoing unified)
        $incomingRowsQuery = Transaksi::whereBetween('tanggal_transaksi', [$startDate->toDateString(), $endDate->toDateString()])->orderBy('tanggal_transaksi', 'asc');
        if ($programId) {
            $incomingRowsQuery->where('program_id', $programId);
        }
        if ($kantorCabangId) {
            $incomingRowsQuery->where('kantor_cabang_id', $kantorCabangId);
        }
        $incomingRows = $incomingRowsQuery
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

        $disbursementRowsQuery = PengajuanDanaDisbursement::whereBetween('tanggal_disburse', [$startDate->toDateString(), $endDate->toDateString()])->orderBy('tanggal_disburse', 'asc');
        if ($programId) {
            $disbursementRowsQuery->where('program_id', $programId);
        }
        if ($kantorCabangId) {
            $disbursementRowsQuery->whereHas('pengajuan', function ($q) use ($kantorCabangId) {
                $q->where('kantor_cabang_id', $kantorCabangId);
            });
        }
        $disbursementRows = $disbursementRowsQuery
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

        $penyaluranRowsQuery = Penyaluran::whereBetween(DB::raw('DATE(created_at)'), [$startDate->toDateString(), $endDate->toDateString()])->orderBy('created_at', 'asc');
        if ($programId) {
            $penyaluranRowsQuery->whereHas('pengajuan', function ($q) use ($programId) {
                $q->where('program_id', $programId);
            });
        }
        if ($kantorCabangId) {
            $penyaluranRowsQuery->where('kantor_cabang_id', $kantorCabangId);
        }
        $penyaluranRows = $penyaluranRowsQuery
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
                'breakdown' => $breakdown,
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

    /**
     * Return program-wise breakdown of transactions and disbursements.
     */
    public function programBreakdown(Request $request)
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

        // Get all active programs
        $programs = \App\Models\Program::all();

        $programData = [];
        
        // Add "Semua Program" (NULL program_id) entry first for transactions without program
        $pemasukanNull = Transaksi::whereNull('program_id')
            ->whereBetween('tanggal_transaksi', [$startDate->toDateString(), $endDate->toDateString()])
            ->sum('nominal');
        
        $pengajuanNull = \App\Models\PengajuanDanaDisbursement::whereNull('program_id')
            ->whereBetween('tanggal_disburse', [$startDate->toDateString(), $endDate->toDateString()])
            ->sum('amount');
        
        $penyaluranNull = \App\Models\Penyaluran::whereHas('pengajuan', function($q) {
            $q->whereNull('program_id');
        })->whereBetween(DB::raw('DATE(created_at)'), [$startDate->toDateString(), $endDate->toDateString()])
        ->sum('amount');

        if ($pemasukanNull > 0 || $pengajuanNull > 0 || $penyaluranNull > 0) {
            // Get detailed breakdown for "Semua Program" - FIFO based
            // These show where the "unassigned" transactions actually came from
            $pemasukanBreakdown = [];
            $pengajuanBreakdown = [];
            $penyaluranBreakdown = [];

            // For "Semua Program" (NULL program_id), we show ALL transactions grouped by program
            // to give visibility on where the money came from/went to
            
            // Breakdown pemasukan by actual programs (FIFO - earliest first)
            $pemasukanWithProgram = Transaksi::selectRaw('transaksis.program_id, program.nama_program, SUM(transaksis.nominal) as total')
                ->leftJoin('program', 'transaksis.program_id', '=', 'program.id')
                ->whereNotNull('transaksis.program_id')
                ->whereBetween('transaksis.tanggal_transaksi', [$startDate->toDateString(), $endDate->toDateString()])
                ->groupBy('transaksis.program_id', 'program.nama_program')
                ->orderBy(DB::raw('MIN(transaksis.tanggal_transaksi)'), 'asc') // FIFO
                ->get();
            
            foreach ($pemasukanWithProgram as $item) {
                $pemasukanBreakdown[] = [
                    'program_nama' => $item->nama_program ?: 'Unknown',
                    'amount' => (float)$item->total,
                ];
            }

            // Breakdown pengajuan dana by program (FIFO)
            $pengajuanDetails = \App\Models\PengajuanDanaDisbursement::selectRaw('pengajuan_dana_disbursements.program_id, program.nama_program, SUM(pengajuan_dana_disbursements.amount) as total')
                ->leftJoin('program', 'pengajuan_dana_disbursements.program_id', '=', 'program.id')
                ->whereNotNull('pengajuan_dana_disbursements.program_id')
                ->whereBetween('tanggal_disburse', [$startDate->toDateString(), $endDate->toDateString()])
                ->groupBy('pengajuan_dana_disbursements.program_id', 'program.nama_program')
                ->orderBy(DB::raw('MIN(tanggal_disburse)'), 'asc') // FIFO
                ->get();
            
            foreach ($pengajuanDetails as $item) {
                $pengajuanBreakdown[] = [
                    'program_nama' => $item->nama_program ?: 'Unknown',
                    'amount' => (float)$item->total,
                ];
            }

            // Breakdown penyaluran by program (FIFO)
            $penyaluranDetails = \App\Models\Penyaluran::selectRaw('pengajuan_danas.program_id, program.nama_program, SUM(penyalurans.amount) as total')
                ->join('pengajuan_danas', 'penyalurans.pengajuan_dana_id', '=', 'pengajuan_danas.id')
                ->leftJoin('program', 'pengajuan_danas.program_id', '=', 'program.id')
                ->whereNotNull('pengajuan_danas.program_id')
                ->whereBetween(DB::raw('DATE(penyalurans.created_at)'), [$startDate->toDateString(), $endDate->toDateString()])
                ->groupBy('pengajuan_danas.program_id', 'program.nama_program')
                ->orderBy(DB::raw('MIN(penyalurans.created_at)'), 'asc') // FIFO
                ->get();
            
            foreach ($penyaluranDetails as $item) {
                $penyaluranBreakdown[] = [
                    'program_nama' => $item->nama_program ?: 'Unknown',
                    'amount' => (float)$item->total,
                ];
            }

            $programData[] = [
                'id' => null,
                'nama' => 'Semua Program',
                'pemasukan' => (float)$pemasukanNull,
                'pengajuan_dana' => (float)$pengajuanNull,
                'penyaluran' => (float)$penyaluranNull,
                'total_pengeluaran' => (float)($pengajuanNull + $penyaluranNull),
                'saldo' => (float)($pemasukanNull - ($pengajuanNull + $penyaluranNull)),
                'breakdown' => [
                    'pemasukan' => $pemasukanBreakdown,
                    'pengajuan_dana' => $pengajuanBreakdown,
                    'penyaluran' => $penyaluranBreakdown,
                ],
            ];
        }

        foreach ($programs as $program) {
            $pemasukan = $program->transaksis()
                ->whereBetween('tanggal_transaksi', [$startDate->toDateString(), $endDate->toDateString()])
                ->sum('nominal');
            
            $pengajuan = \App\Models\PengajuanDanaDisbursement::where('program_id', $program->id)
                ->whereBetween('tanggal_disburse', [$startDate->toDateString(), $endDate->toDateString()])
                ->sum('amount');
            
            $penyaluran = \App\Models\Penyaluran::whereHas('pengajuan', function($q) use ($program) {
                $q->where('program_id', $program->id);
            })->whereBetween(DB::raw('DATE(created_at)'), [$startDate->toDateString(), $endDate->toDateString()])
            ->sum('amount');

            // Only include programs with activity
            if ($pemasukan > 0 || $pengajuan > 0 || $penyaluran > 0) {
                $programData[] = [
                    'id' => $program->id,
                    'nama' => $program->nama_program,
                    'pemasukan' => (float)$pemasukan,
                    'pengajuan_dana' => (float)$pengajuan,
                    'penyaluran' => (float)$penyaluran,
                    'total_pengeluaran' => (float)($pengajuan + $penyaluran),
                    'saldo' => (float)($pemasukan - ($pengajuan + $penyaluran)),
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => $programData,
        ]);
    }

    /**
     * Return timeline data (daily aggregates) for chart visualization.
     */
    public function timeline(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->can('view laporan keuangan')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $start = $request->query('start');
        $end = $request->query('end');
        $programId = $request->query('program_id');
        $kantorCabangId = $request->query('kantor_cabang_id');

        try {
            $endDate = $end ? Carbon::parse($end)->endOfDay() : Carbon::now()->endOfDay();
            $startDate = $start ? Carbon::parse($start)->startOfDay() : $endDate->copy()->startOfMonth();
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Invalid date format'], 422);
        }

        // Daily aggregates for pemasukan
        $pemasukanQuery = Transaksi::selectRaw('DATE(tanggal_transaksi) as date, SUM(nominal) as total')
            ->whereBetween('tanggal_transaksi', [$startDate->toDateString(), $endDate->toDateString()])
            ->groupBy('date');
        
        if ($programId) {
            $pemasukanQuery->where('program_id', $programId);
        }
        if ($kantorCabangId) {
            $pemasukanQuery->where('kantor_cabang_id', $kantorCabangId);
        }
        
        $pemasukanData = $pemasukanQuery->get()->keyBy('date');

        // Daily aggregates for pengajuan dana
        $pengajuanQuery = PengajuanDanaDisbursement::selectRaw('DATE(tanggal_disburse) as date, SUM(amount) as total')
            ->whereBetween('tanggal_disburse', [$startDate->toDateString(), $endDate->toDateString()])
            ->groupBy('date');
        
        if ($programId) {
            $pengajuanQuery->where('program_id', $programId);
        }
        if ($kantorCabangId) {
            $pengajuanQuery->whereHas('pengajuan', function ($q) use ($kantorCabangId) {
                $q->where('kantor_cabang_id', $kantorCabangId);
            });
        }
        
        $pengajuanData = $pengajuanQuery->get()->keyBy('date');

        // Daily aggregates for penyaluran
        $penyaluranQuery = Penyaluran::selectRaw('DATE(created_at) as date, SUM(amount) as total')
            ->whereBetween(DB::raw('DATE(created_at)'), [$startDate->toDateString(), $endDate->toDateString()])
            ->groupBy('date');
        
        if ($programId) {
            $penyaluranQuery->whereHas('pengajuan', function ($q) use ($programId) {
                $q->where('program_id', $programId);
            });
        }
        if ($kantorCabangId) {
            $penyaluranQuery->where('kantor_cabang_id', $kantorCabangId);
        }
        
        $penyaluranData = $penyaluranQuery->get()->keyBy('date');

        // Build timeline array with all dates in range
        $timeline = [];
        $current = $startDate->copy();
        while ($current <= $endDate) {
            $dateStr = $current->toDateString();
            $pemasukan = isset($pemasukanData[$dateStr]) ? (float)$pemasukanData[$dateStr]->total : 0;
            $pengajuan = isset($pengajuanData[$dateStr]) ? (float)$pengajuanData[$dateStr]->total : 0;
            $penyaluran = isset($penyaluranData[$dateStr]) ? (float)$penyaluranData[$dateStr]->total : 0;
            
            $timeline[] = [
                'date' => $dateStr,
                'pemasukan' => $pemasukan,
                'pengajuan_dana' => $pengajuan,
                'penyaluran' => $penyaluran,
                'pengeluaran' => $pengajuan + $penyaluran,
            ];
            
            $current->addDay();
        }

        return response()->json([
            'success' => true,
            'data' => $timeline,
        ]);
    }

    /**
     * Return penyaluran breakdown by alias (submission type).
     * Groups penyaluran by their submission_type alias.
     */
    public function penyaluranByAlias(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->can('view laporan keuangan')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $start = $request->query('start');
        $end = $request->query('end');
        $programId = $request->query('program_id');
        $kantorCabangId = $request->query('kantor_cabang_id');

        try {
            $endDate = $end ? Carbon::parse($end)->endOfDay() : Carbon::now()->endOfDay();
            $startDate = $start ? Carbon::parse($start)->startOfDay() : $endDate->copy()->startOfMonth();
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Invalid date format'], 422);
        }

        // Query penyaluran with filters
        $query = Penyaluran::whereBetween(DB::raw('DATE(created_at)'), [$startDate->toDateString(), $endDate->toDateString()]);
        
        if ($programId) {
            $query->whereHas('pengajuan', function($q) use ($programId) {
                $q->where('program_id', $programId);
            });
        }
        
        if ($kantorCabangId) {
            $query->where('kantor_cabang_id', $kantorCabangId);
        }

        // Get all penyaluran with submission types
        $penyalurans = $query->with('pengajuan')->get();

        // Group by submission_type (alias from program_share_types)
        $aliasGroups = [];
        $total = 0;

        foreach ($penyalurans as $penyaluran) {
            $submissionType = $penyaluran->pengajuan ? $penyaluran->pengajuan->submission_type : null;
            
            // Get alias from program_share_types (match by name, not id)
            $alias = 'Lainnya';
            if ($submissionType) {
                $shareType = \App\Models\ProgramShareType::where('name', $submissionType)->first();
                if ($shareType && $shareType->alias) {
                    $alias = $shareType->alias;
                } elseif ($shareType) {
                    $alias = $shareType->name;
                } else {
                    // If not found in program_share_types, use submission_type as-is
                    $alias = $submissionType;
                }
            }

            $amount = (float)$penyaluran->amount;
            $total += $amount;

            if (!isset($aliasGroups[$alias])) {
                $aliasGroups[$alias] = [
                    'alias' => $alias,
                    'amount' => 0,
                    'count' => 0,
                ];
            }

            $aliasGroups[$alias]['amount'] += $amount;
            $aliasGroups[$alias]['count'] += 1;
        }

        // Calculate percentages and sort by amount desc
        $breakdown = array_values($aliasGroups);
        usort($breakdown, function($a, $b) {
            return $b['amount'] <=> $a['amount'];
        });

        foreach ($breakdown as &$item) {
            $item['percentage'] = $total > 0 ? round(($item['amount'] / $total) * 100, 2) : 0;
        }
        unset($item);

        return response()->json([
            'success' => true,
            'data' => [
                'total' => (float)$total,
                'breakdown' => $breakdown,
            ],
        ]);
    }

    /**
     * Return detailed breakdown by expense type (submission_type).
     * Shows pengajuan dana, penyaluran, total pengeluaran, and saldo for each type.
     */
    public function expenseTypeBreakdown(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->can('view laporan keuangan')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $start = $request->query('start');
        $end = $request->query('end');
        $programId = $request->query('program_id');
        $kantorCabangId = $request->query('kantor_cabang_id');

        try {
            $endDate = $end ? Carbon::parse($end)->endOfDay() : Carbon::now()->endOfDay();
            $startDate = $start ? Carbon::parse($start)->startOfDay() : $endDate->copy()->startOfMonth();
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Invalid date format'], 422);
        }

        // Get all unique submission types from pengajuan_danas
        $submissionTypes = \App\Models\PengajuanDana::select('submission_type')
            ->whereNotNull('submission_type')
            ->distinct()
            ->pluck('submission_type')
            ->toArray();

        $breakdown = [];

        foreach ($submissionTypes as $submissionType) {
            // Get alias from program_share_types
            $alias = $submissionType;
            $shareType = \App\Models\ProgramShareType::where('name', $submissionType)->first();
            if ($shareType && $shareType->alias) {
                $alias = $shareType->alias;
            } elseif ($shareType) {
                $alias = $shareType->name;
            }

            // Calculate Pengajuan Dana for this submission type (Approved status)
            // Using PengajuanDana directly assuming disbursements might be missing/untabulated
            $pengajuanQuery = \App\Models\PengajuanDana::where('submission_type', $submissionType)
                ->where('status', 'Approved')
                ->whereBetween('created_at', [$startDate, $endDate]);

            if ($programId) {
                $pengajuanQuery->where('program_id', $programId);
            }
            if ($kantorCabangId) {
                $pengajuanQuery->where('kantor_cabang_id', $kantorCabangId);
            }
            
            $pengajuanDana = $pengajuanQuery->sum('amount');
            
            $penyaluranQuery = \App\Models\Penyaluran::whereHas('pengajuan', function($q) use ($submissionType) {
                $q->where('submission_type', $submissionType);
            })->whereBetween(DB::raw('DATE(created_at)'), [$startDate->toDateString(), $endDate->toDateString()]);

            if ($programId) {
                $penyaluranQuery->whereHas('pengajuan', function ($q) use ($programId) {
                    $q->where('program_id', $programId);
                });
            }
            if ($kantorCabangId) {
                $penyaluranQuery->where('kantor_cabang_id', $kantorCabangId);
            }

            // Get Details for Penyaluran (Activity in period)
            // Penyaluran doesn't have program/transaksi relations directly. using fields directly.
            $detailsQuery = clone $penyaluranQuery;
            $details = $detailsQuery->orderBy('created_at', 'desc')->get();

            $penyaluran = $penyaluranQuery->sum('amount');

            // Only include if there's activity in this period (Approved/Disbursed)
            // Saldo removed as requested
            if ($pengajuanDana > 0 || $penyaluran > 0) {
                // Total Pengeluaran removed as it was double counting and confusing

                $breakdown[] = [
                    'alias' => $alias,
                    'submission_type' => $submissionType,
                    'pengajuan_dana' => (float)$pengajuanDana,
                    'penyaluran' => (float)$penyaluran,
                    'details' => $details,
                ];
            }
        }

        // Sort by penyaluran desc
        usort($breakdown, function($a, $b) {
            return $b['penyaluran'] <=> $a['penyaluran'];
        });

        return response()->json([
            'success' => true,
            'data' => $breakdown,
        ]);
    }

    /**
     * Return list of mitra that have transactions (count + total nominal).
     * Supports optional search query `search` and pagination.
     */
    public function mitraList(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->can('view laporan keuangan')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $query = \App\Models\Transaksi::query()->whereNotNull('mitra_id')->with('mitra');

        if ($request->filled('search')) {
            $s = $request->query('search');
            $query->whereHas('mitra', function ($q) use ($s) {
                $q->where('nama', 'like', "%{$s}%");
            });
        }

        // Aggregate per mitra
        $items = $query
            ->selectRaw('mitra_id, COUNT(*) as transaksi_count, SUM(nominal) as transaksi_total')
            ->groupBy('mitra_id')
            ->orderByDesc('transaksi_total')
            ->get()
            ->map(function ($row) {
                $mitra = \App\Models\Mitra::find($row->mitra_id);
                return [
                    'id' => $row->mitra_id,
                    'nama' => $mitra ? $mitra->nama : null,
                    'transaksi_count' => (int)$row->transaksi_count,
                    'transaksi_total' => (float)$row->transaksi_total,
                ];
            })->toArray();

        // simple pagination in memory
        $page = max(1, (int)$request->query('page', 1));
        $perPage = max(1, min(200, (int)$request->query('per_page', 20)));
        $total = count($items);
        $slice = array_slice($items, ($page - 1) * $perPage, $perPage);

        return response()->json([
            'success' => true,
            'data' => $slice,
            'pagination' => [
                'current_page' => $page,
                'last_page' => (int)ceil($total / $perPage),
                'per_page' => $perPage,
                'total' => $total,
            ],
        ]);
    }

    /**
     * Return transactions for a specific mitra with totals and pagination.
     */
    public function mitraTransactions(Request $request, $mitraId)
    {
        $user = $request->user();
        if (!$user || !$user->can('view laporan keuangan')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $query = \App\Models\Transaksi::with(['donatur:id,nama', 'program:id,nama_program', 'kantorCabang:id,nama', 'fundraiser:id,name'])
            ->where('mitra_id', $mitraId);

        if ($request->filled('tanggal_from') && $request->filled('tanggal_to')) {
            $query->whereDate('tanggal_transaksi', '>=', $request->tanggal_from)
                ->whereDate('tanggal_transaksi', '<=', $request->tanggal_to);
        }

        $totalCount = (int)$query->count();
        $totalNominal = (float)$query->sum('nominal');

        $page = max(1, (int)$request->query('page', 1));
        $perPage = max(1, min(200, (int)$request->query('per_page', 20)));
        $txs = $query->orderByDesc('tanggal_transaksi')->paginate($perPage, ['*'], 'page', $page);

        $txs->getCollection()->transform(function ($t) {
            return [
                'id' => $t->id,
                'tanggal' => $t->tanggal_transaksi ? $t->tanggal_transaksi->toDateString() : null,
                'keterangan' => $t->keterangan ?: $t->kode,
                'nominal' => (float)$t->nominal,
                'donatur' => $t->donatur ? $t->donatur->nama : null,
                'program' => $t->program ? $t->program->nama_program : null,
                'kantor' => $t->kantorCabang ? $t->kantorCabang->nama : null,
                'status' => $t->status ?? null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $txs->items(),
            'pagination' => [
                'current_page' => $txs->currentPage(),
                'last_page' => $txs->lastPage(),
                'per_page' => $txs->perPage(),
                'total' => $txs->total(),
            ],
            'totals' => [
                'count' => $totalCount,
                'nominal' => $totalNominal,
            ],
        ]);
    }

    /**
     * Return basic mitra info and aggregates for a specific mitra.
     */
    public function mitraDetail(Request $request, $mitraId)
    {
        $user = $request->user();
        if (!$user || !$user->can('view laporan keuangan')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $mitra = \App\Models\Mitra::find($mitraId);
        if (! $mitra) {
            return response()->json(['success' => false, 'message' => 'Mitra not found'], 404);
        }

        $query = \App\Models\Transaksi::where('mitra_id', $mitraId);
        if ($request->filled('tanggal_from') && $request->filled('tanggal_to')) {
            $query->whereDate('tanggal_transaksi', '>=', $request->tanggal_from)
                  ->whereDate('tanggal_transaksi', '<=', $request->tanggal_to);
        }

        $count = (int)$query->count();
        $total = (float)$query->sum('nominal');

        // Compute fee (nominal) from mitra payroll totals if available
        $feeTotal = (float)\App\Models\MitraPayroll::where('mitra_id', $mitraId)->sum('total');

        // Derive status from soft delete flag (no explicit status column in migration)
        $status = $mitra->deleted_at ? 'Nonaktif' : 'Aktif';

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $mitra->id,
                'nama' => $mitra->nama,
                'status' => $status,
                'nominal' => $feeTotal,
                'transaksi_count' => $count,
                'transaksi_total' => $total,
            ],
        ]);
    }
}
