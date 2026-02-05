<?php

namespace App\Http\Controllers;

use App\Models\Penyaluran;
use App\Models\PengajuanDanaDisbursement;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use App\Models\Program;
use App\Models\ProgramShareType;

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
            $startDate = $start ? Carbon::parse($start)->startOfDay() : Carbon::create(2020, 1, 1)->startOfDay();
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

        $disbursementsInRangeQuery = \App\Models\PengajuanDana::where('status', 'Approved')
            ->whereBetween('created_at', [$startDate, $endDate]);
        if ($programId) {
            $disbursementsInRangeQuery->where('program_id', $programId);
        }
        if ($kantorCabangId) {
            $disbursementsInRangeQuery->where('kantor_cabang_id', $kantorCabangId);
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
        $outgoingInRange = (float)$penyaluranInRange;

        // Totals before start (for opening balance)
        $incomingBeforeQuery = Transaksi::where('tanggal_transaksi', '<', $startDate->toDateString());
        if ($programId) {
            $incomingBeforeQuery->where('program_id', $programId);
        }
        if ($kantorCabangId) {
            $incomingBeforeQuery->where('kantor_cabang_id', $kantorCabangId);
        }
        $incomingBefore = $incomingBeforeQuery->sum('nominal');

        $disbursementsBeforeQuery = \App\Models\PengajuanDana::where('status', 'Approved')
            ->where('created_at', '<', $startDate);
        if ($programId) {
            $disbursementsBeforeQuery->where('program_id', $programId);
        }
        if ($kantorCabangId) {
            $disbursementsBeforeQuery->where('kantor_cabang_id', $kantorCabangId);
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
        $outgoingBefore = (float)$penyaluranBefore;

        $saldoAwal = (float)$incomingBefore - (float)$outgoingBefore;
        $saldoAkhir = $saldoAwal + (float)$incomingInRange - (float)$outgoingInRange;

        // Breakdown data for comprehensive reporting
        $breakdown = [
            'pengajuan_dana' => (float)$disbursementsInRange,
            'penyaluran' => (float)$penyaluranInRange,
            'pengajuan_percentage' => ($disbursementsInRange + $penyaluranInRange) > 0 ? round(($disbursementsInRange / ($disbursementsInRange + $penyaluranInRange)) * 100, 2) : 0,
            'penyaluran_percentage' => ($disbursementsInRange + $penyaluranInRange) > 0 ? round(($penyaluranInRange / ($disbursementsInRange + $penyaluranInRange)) * 100, 2) : 0,
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

        $disbursementRowsQuery = \App\Models\PengajuanDana::where('status', 'Approved')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'asc');
        if ($programId) {
            $disbursementRowsQuery->where('program_id', $programId);
        }
        if ($kantorCabangId) {
            $disbursementRowsQuery->where('kantor_cabang_id', $kantorCabangId);
        }
        $disbursementRows = $disbursementRowsQuery
            ->get(['id', 'created_at', 'amount', 'submission_type'])
            ->map(function ($d) {
                return [
                    'id' => $d->id,
                    'tanggal' => $d->created_at ? $d->created_at->format('Y-m-d') : null,
                    'keterangan' => 'Pengajuan Dana (' . $d->submission_type . ')',
                    'masuk' => 0.0,
                    'keluar' => 0.0, // Informational only, does not affect balance
                    'source' => 'disbursement', // keep key for frontend compat
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
            $startDate = $start ? Carbon::parse($start)->startOfDay() : Carbon::create(2020, 1, 1)->startOfDay();
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Invalid date format'], 422);
        }

        // Get all active programs with their shares
        $programs = Program::with(['shares.type'])->get();

        $programDataEntries = [];
        $totalDanaSiapSalur = 0;
        
        // 1. Calculate assigned programs first to get their allocated "Dana Siap Salur"
        /** @var Program $program */
        foreach ($programs as $program) {
            $rawInflow = $program->transaksis()
                ->whereBetween('tanggal_transaksi', [$startDate->toDateString(), $endDate->toDateString()])
                ->sum('nominal');
            
            $pengajuan = \App\Models\PengajuanDana::where('program_id', $program->id)
                ->where('status', 'Approved')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('amount');
            
            $penyaluran = \App\Models\Penyaluran::whereHas('pengajuan', function($q) use ($program) {
                $q->where('program_id', $program->id);
            })->whereBetween(DB::raw('DATE(created_at)'), [$startDate->toDateString(), $endDate->toDateString()])
            ->sum('amount');

            // Calculate Dana Siap Salur based on Program Percentage
            $programShareAllocated = 0;
            $allSharesAllocated = 0;
            
            foreach ($program->shares as $s) {
                $pst = $s->relationLoaded('type') ? $s->getRelation('type') : \App\Models\ProgramShareType::find($s->program_share_type_id);
                $key = $pst->key ?? 'unknown';
                
                $allocated = 0;
                if ($s->type === 'percentage' && $s->value !== null) {
                    $allocated = floor($rawInflow * (float)$s->value / 100);
                } elseif ($s->type === 'nominal' && $s->value !== null) {
                    $allocated = (float)$s->value;
                }
                
                if ($key === 'program') {
                    $programShareAllocated = $allocated;
                }
                $allSharesAllocated += $allocated;
            }

            // If no explicit 'program' share percentage, use remainder (matching allocationSummary logic)
            if ($programShareAllocated == 0 && $allSharesAllocated < $rawInflow) {
                $programShareAllocated = $rawInflow - $allSharesAllocated;
            }

            $danaSiapSalur = (float)$programShareAllocated;
            
            // Only include programs with activity
            if ($rawInflow > 0 || $pengajuan > 0 || $penyaluran > 0) {
                $programDataEntries[] = [
                    'id' => $program->id,
                    'nama' => $program->nama_program,
                    'pemasukan' => $danaSiapSalur, // Display as Dana Siap Salur
                    'pengajuan_dana' => (float)$pengajuan,
                    'penyaluran' => (float)$penyaluran,
                    'selisih' => (float)($pengajuan - $penyaluran), // Undisbursed approved funds
                    'saldo' => (float)($danaSiapSalur - $penyaluran), // Dana Siap Salur minus actual disbursements
                ];
                $totalDanaSiapSalur += $danaSiapSalur;
            }
        }

        // 2. Calculate Unassigned
        $pemasukanUnassigned = Transaksi::whereNull('program_id')
            ->whereBetween('tanggal_transaksi', [$startDate->toDateString(), $endDate->toDateString()])
            ->sum('nominal');
        
        $pengajuanUnassigned = \App\Models\PengajuanDana::whereNull('program_id')
            ->where('status', 'Approved')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');
        
        $penyaluranUnassigned = \App\Models\Penyaluran::whereHas('pengajuan', function($q) {
            $q->whereNull('program_id');
        })->whereBetween(DB::raw('DATE(created_at)'), [$startDate->toDateString(), $endDate->toDateString()])
        ->sum('amount');

        $unassignedRow = null;
        if ($pemasukanUnassigned > 0 || $pengajuanUnassigned > 0 || $penyaluranUnassigned > 0) {
            $danaSiapSalurUnassigned = (float)$pemasukanUnassigned; // 100% for unassigned
            $unassignedRow = [
                'id' => 'unassigned',
                'nama' => 'Tanpa Program / Operasional Umum',
                'pemasukan' => $danaSiapSalurUnassigned,
                'pengajuan_dana' => (float)$pengajuanUnassigned,
                'penyaluran' => (float)$penyaluranUnassigned,
                'selisih' => (float)($pengajuanUnassigned - $penyaluranUnassigned),
                'saldo' => (float)($danaSiapSalurUnassigned - $penyaluranUnassigned),
            ];
            $totalDanaSiapSalur += $danaSiapSalurUnassigned;
        }

        // 3. Calculate Global Totals for "Semua Program"
        // For "Semua Program", Dana Siap Salur should be the total of ALL transactions (not just program allocations)
        $totalTransaksiKeseluruhan = Transaksi::whereBetween('tanggal_transaksi', [$startDate->toDateString(), $endDate->toDateString()])
            ->sum('nominal');
        
        $pengajuanTotal = \App\Models\PengajuanDana::where('status', 'Approved')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');
        
        $penyaluranTotal = \App\Models\Penyaluran::whereBetween(DB::raw('DATE(created_at)'), [$startDate->toDateString(), $endDate->toDateString()])
            ->sum('amount');

        $programData = [];
        if ($totalTransaksiKeseluruhan > 0 || $pengajuanTotal > 0 || $penyaluranTotal > 0) {
            // Detailed breakdown for "Semua Inflow" - use raw transaction totals per program
            $pemasukanBreakdown = [];
            foreach ($programs as $program) {
                $rawInflow = $program->transaksis()
                    ->whereBetween('tanggal_transaksi', [$startDate->toDateString(), $endDate->toDateString()])
                    ->sum('nominal');
                
                if ($rawInflow > 0) {
                    $pemasukanBreakdown[] = [
                        'program_nama' => $program->nama_program,
                        'amount' => (float)$rawInflow,
                    ];
                }
            }
            // Add unassigned/operasional transactions
            if ($pemasukanUnassigned > 0) {
                $pemasukanBreakdown[] = [
                    'program_nama' => 'Tanpa Program / Operasional Umum',
                    'amount' => (float)$pemasukanUnassigned,
                ];
            }

            // We still need the original breakdowns for expenses to keep the UI rich
            // Mapping for aliases to clear names (e.g. "Gaji Karyawan" -> "OPS 1")
            $shareTypes = \App\Models\ProgramShareType::all();
            $typeMap = [];
            foreach ($shareTypes as $st) {
                 if ($st->alias) $typeMap[strtolower($st->alias)] = $st->name;
                 if ($st->name) $typeMap[strtolower($st->name)] = $st->name;
            }

            // We still need the original breakdowns for expenses to keep the UI rich
            $pengajuanBreakdown = [];
            $pengajuanDetails = \App\Models\PengajuanDana::selectRaw('pengajuan_danas.program_id, program.nama_program, pengajuan_danas.submission_type, SUM(pengajuan_danas.amount) as total')
                ->leftJoin('program', 'pengajuan_danas.program_id', '=', 'program.id')
                ->where('status', 'Approved')
                ->whereBetween('pengajuan_danas.created_at', [$startDate, $endDate])
                ->groupBy('pengajuan_danas.program_id', 'program.nama_program', 'pengajuan_danas.submission_type')
                ->orderBy('program.nama_program', 'desc') // Programs first, then nulls
                ->get();
            
            foreach ($pengajuanDetails as $item) {
                $label = $item->nama_program;
                if (!$label) {
                    $stype = $item->submission_type;
                    $label = $typeMap[strtolower((string)$stype)] ?? $stype ?? 'Operasional';
                }
                $pengajuanBreakdown[] = ['program_nama' => $label, 'amount' => (float)$item->total];
            }

            $penyaluranBreakdown = [];
            $penyaluranDetails = \App\Models\Penyaluran::selectRaw('pengajuan_danas.program_id, program.nama_program, pengajuan_danas.submission_type, SUM(penyalurans.amount) as total')
                ->leftJoin('pengajuan_danas', 'penyalurans.pengajuan_dana_id', '=', 'pengajuan_danas.id')
                ->leftJoin('program', 'pengajuan_danas.program_id', '=', 'program.id')
                ->whereBetween(DB::raw('DATE(penyalurans.created_at)'), [$startDate->toDateString(), $endDate->toDateString()])
                ->groupBy('pengajuan_danas.program_id', 'program.nama_program', 'pengajuan_danas.submission_type')
                ->orderBy('program.nama_program', 'desc')
                ->get();
            
            foreach ($penyaluranDetails as $item) {
                 $label = $item->nama_program;
                if (!$label) {
                    $stype = $item->submission_type;
                    $label = $typeMap[strtolower((string)$stype)] ?? $stype ?? 'Operasional';
                }
                $penyaluranBreakdown[] = ['program_nama' => $label, 'amount' => (float)$item->total];
            }

            $programData[] = [
                'id' => null,
                'nama' => 'Semua Inflow',
                'pemasukan' => (float)$totalTransaksiKeseluruhan, // Total ALL transactions (not just program allocations)
                'pengajuan_dana' => (float)$pengajuanTotal,
                'penyaluran' => (float)$penyaluranTotal,
                'selisih' => (float)($pengajuanTotal - $penyaluranTotal),
                'saldo' => (float)($totalTransaksiKeseluruhan - $penyaluranTotal),
                'breakdown' => [
                    'pemasukan' => $pemasukanBreakdown,
                    'pengajuan_dana' => $pengajuanBreakdown,
                    'penyaluran' => $penyaluranBreakdown,
                ],
            ];

            /*
            if ($unassignedRow) {
                $programData[] = $unassignedRow;
            }
            */
            
            // Add individual programs
            foreach ($programDataEntries as $entry) {
                $programData[] = $entry;
            }
        }

        return response()->json([
            'success' => true,
            'data' => $programData,
        ]);
    }

    /**
     * Return aggregated allocation totals per program share type for summary boxes.
     * Respects start, end, program_id, and kantor_cabang_id filters.
     */
    public function allocationSummary(Request $request)
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
            $startDate = $start ? Carbon::parse($start)->startOfDay() : Carbon::create(2020, 1, 1)->startOfDay();
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Invalid date format'], 422);
        }

        // Get relevant programs
        $programQuery = \App\Models\Program::with(['shares.type']);
        if ($programId) {
            $programQuery->where('id', $programId);
        }
        $programs = $programQuery->get();

        // Preferred keys for summary
        $shareKeys = ['dp', 'ops_1', 'ops_2', 'program', 'fee_mitra', 'bonus', 'championship'];
        
        // Fetch full share type objects for label and code mapping
        $shareTypesByKey = \App\Models\ProgramShareType::all()->keyBy('key');
        
        $totalNominal = 0;
        $allocatedTotals = [];
        foreach ($shareKeys as $k) $allocatedTotals[$k] = 0;
        
        // Find all other keys present in these programs
        foreach ($programs as $p) {
            foreach ($p->shares as $s) {
                $pst = $s->relationLoaded('type') ? $s->getRelationValue('type') : \App\Models\ProgramShareType::find($s->program_share_type_id);
                $key = $pst->key ?? ($s->program_share_type_key ?? null) ?? 'unknown';
                if (!in_array($key, $shareKeys)) {
                    $shareKeys[] = $key;
                    $allocatedTotals[$key] = 0;
                }
            }
        }

        foreach ($programs as $program) {
            // Inflow for this program in range
            $inflowQuery = Transaksi::where('program_id', $program->id)
                ->whereBetween('tanggal_transaksi', [$startDate->toDateString(), $endDate->toDateString()]);
            
            if ($kantorCabangId) {
                $inflowQuery->where('kantor_cabang_id', $kantorCabangId);
            }
            
            $inflow = (int) $inflowQuery->sum('nominal');
            $totalNominal += $inflow;

            $programAllocated = 0;
            foreach ($program->shares as $s) {
                $pst = $s->relationLoaded('type') ? $s->getRelationValue('type') : \App\Models\ProgramShareType::find($s->program_share_type_id);
                $key = $pst->key ?? ($s->program_share_type_key ?? null) ?? 'unknown';
                
                $allocated = 0;
                if ($s->type === 'percentage' && $s->value !== null) {
                    $allocated = (int) floor($inflow * (float)$s->value / 100);
                } elseif ($s->type === 'nominal' && $s->value !== null) {
                    $allocated = (int) $s->value;
                }
                
                $allocatedTotals[$key] += $allocated;
                $programAllocated += $allocated;
            }
            
            // Handle unallocated for 'program' share if it has no specific config
            $unallocated = $inflow - $programAllocated;
            if ($unallocated > 0) {
                $programShare = $program->shares->first(function($s) {
                    $pst = $s->relationLoaded('type') ? $s->getRelationValue('type') : \App\Models\ProgramShareType::find($s->program_share_type_id);
                    $key = $pst->key ?? ($s->program_share_type_key ?? null) ?? 'unknown';
                    return $key === 'program' && ($s->type === null || $s->value === null);
                });
                
                if ($programShare || !isset($allocatedTotals['program']) || (isset($allocatedTotals['program']) && $allocatedTotals['program'] == 0)) {
                    if (!isset($allocatedTotals['program'])) $allocatedTotals['program'] = 0;
                    $allocatedTotals['program'] += $unallocated;
                }
            }
        }

        // Check for transactions WITHOUT program
        if (!$programId) {
            $inflowNoProgramQuery = Transaksi::whereNull('program_id')
                ->whereBetween('tanggal_transaksi', [$startDate->toDateString(), $endDate->toDateString()]);
            
            if ($kantorCabangId) {
                $inflowNoProgramQuery->where('kantor_cabang_id', $kantorCabangId);
            }
            
            $inflowNoProgram = (int) $inflowNoProgramQuery->sum('nominal');
            $totalNominal += $inflowNoProgram;
            // Unassigned transactions are typically "unallocated" or go to a default bucket if defined
        }

        // NEW: Calculate Penyaluran in range for matching allocation categories
        $penyaluranQuery = Penyaluran::whereBetween(DB::raw('DATE(created_at)'), [$startDate->toDateString(), $endDate->toDateString()]);
        if ($programId) {
            $penyaluranQuery->whereHas('pengajuan', function ($q) use ($programId) {
                $q->where('program_id', $programId);
            });
        }
        if ($kantorCabangId) {
            $penyaluranQuery->where('kantor_cabang_id', $kantorCabangId);
        }

        $penyalurans = $penyaluranQuery->get();
        $penyaluranTotals = [];
        foreach ($shareKeys as $k) $penyaluranTotals[$k] = 0;
        $grandTotalPenyaluran = 0;

        foreach ($penyalurans as $p) {
            $amount = (float)$p->amount;
            $grandTotalPenyaluran += $amount;
            
            $submissionType = $p->pengajuan ? $p->pengajuan->submission_type : null;
            if ($submissionType) {
                // Find ProgramShareType by name OR alias to get the key
                $shareType = \App\Models\ProgramShareType::where('name', $submissionType)
                    ->orWhere('alias', $submissionType)
                    ->first();
                $key = $shareType->key ?? 'unknown';
                
                if (!isset($penyaluranTotals[$key])) {
                    $penyaluranTotals[$key] = 0;
                }
                $penyaluranTotals[$key] += $amount;
            } else {
                if (!isset($penyaluranTotals['unknown'])) {
                    $penyaluranTotals['unknown'] = 0;
                }
                $penyaluranTotals['unknown'] += $amount;
            }
        }

        // Build boxes
        $boxes = [];
        $boxes[] = ['label' => 'Nominal Keseluruhan Transaksi', 'value' => $totalNominal, 'penyaluran' => $grandTotalPenyaluran, 'alias_code' => '-'];
        
        $totalSharesSum = 0;
        foreach ($shareKeys as $k) {
            $val = $allocatedTotals[$k];
            $peny = $penyaluranTotals[$k] ?? 0;
            if ($val <= 0 && $peny <= 0 && !in_array($k, ['dp', 'ops_1', 'ops_2', 'program'])) continue;
            
            $meta = $shareTypesByKey[$k] ?? null;
            $label = $meta ? ($meta->alias ?: ucwords(str_replace('_', ' ', $k))) : ucwords(str_replace('_', ' ', $k));
            $aliasCode = $meta ? $meta->name : '-';
            
            $boxes[] = [
                'label' => $label,
                'value' => $val,
                'penyaluran' => $peny,
                'alias_code' => $aliasCode
            ];
            $totalSharesSum += $val;
        }
        
        $remainder = $totalNominal - $totalSharesSum;
        if ($remainder > 0) {
            $boxes[] = ['label' => 'Sisa Belum Teralokasi', 'value' => $remainder, 'penyaluran' => $penyaluranTotals['unknown'] ?? 0];
        }

        return response()->json([
            'success' => true,
            'data' => $boxes,
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
            $startDate = $start ? Carbon::parse($start)->startOfDay() : Carbon::create(2020, 1, 1)->startOfDay();
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

        // Daily aggregates for pengajuan dana (Approved)
        $pengajuanQuery = \App\Models\PengajuanDana::selectRaw('DATE(created_at) as date, SUM(amount) as total')
            ->where('status', 'Approved')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date');
        
        if ($programId) {
            $pengajuanQuery->where('program_id', $programId);
        }
        if ($kantorCabangId) {
            $pengajuanQuery->where('kantor_cabang_id', $kantorCabangId);
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
            $startDate = $start ? Carbon::parse($start)->startOfDay() : Carbon::create(2020, 1, 1)->startOfDay();
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
            
            // Get alias from program_share_types (match by name or alias)
            $alias = 'Lainnya';
            if ($submissionType) {
                $shareType = \App\Models\ProgramShareType::where('name', $submissionType)
                    ->orWhere('alias', $submissionType)
                    ->first();
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
            $startDate = $start ? Carbon::parse($start)->startOfDay() : Carbon::create(2020, 1, 1)->startOfDay();
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
            $shareType = \App\Models\ProgramShareType::where('name', $submissionType)
                ->orWhere('alias', $submissionType)
                ->first();
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
