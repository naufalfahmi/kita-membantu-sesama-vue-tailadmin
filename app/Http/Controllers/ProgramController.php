<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\ProgramShare;
use App\Models\ProgramShareType;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Program::with(['creator', 'updater']);

        // Search filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_program', 'like', "%{$search}%");
            });
        }

        // Filter by tipe pembagian marketing
        if ($request->has('tipe_pembagian_marketing') && $request->tipe_pembagian_marketing) {
            $query->where('tipe_pembagian_marketing', $request->tipe_pembagian_marketing);
        }

        $program = $query->orderBy('created_at', 'desc')
                        ->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $program->items(),
            'pagination' => [
                'current_page' => $program->currentPage(),
                'last_page' => $program->lastPage(),
                'per_page' => $program->perPage(),
                'total' => $program->total(),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_program' => 'required|string|max:255',
            'persentase_hak_program' => 'nullable|numeric|min:0|max:100',
            'persentase_hak_program_operasional' => 'nullable|numeric|min:0|max:100',
            'persentase_hak_championship' => 'nullable|numeric|min:0|max:100',
            'tipe_pembagian_marketing' => 'nullable|in:percentage,nominal',
            'persentase_hak_marketing' => 'nullable|numeric|min:0|max:100',
            'persentase_hak_operasional_1' => 'nullable|numeric|min:0|max:100',
            'persentase_hak_iklan' => 'nullable|numeric|min:0|max:100',
            'persentase_hak_operasional_2' => 'nullable|numeric|min:0|max:100',
            'persentase_hak_operasional_3' => 'nullable|numeric|min:0|max:100',
            'jumlah_persentase' => 'nullable|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $data = $request->only([
                'nama_program',
                'persentase_hak_program',
                'persentase_hak_program_operasional',
                'persentase_hak_championship',
                'tipe_pembagian_marketing',
                'persentase_hak_marketing',
                'persentase_hak_operasional_1',
                'persentase_hak_iklan',
                'persentase_hak_operasional_2',
                'persentase_hak_operasional_3',
                'jumlah_persentase',
            ]);

            $data['created_by'] = auth()->id();

            $program = Program::create($data);

            // handle shares if provided
            if ($request->has('shares') && is_array($request->shares)) {
                DB::transaction(function () use ($request, $program) {
                    foreach ($request->shares as $s) {
                        $pstId = null;

                        if (!empty($s['program_share_type_id'])) {
                            $pst = ProgramShareType::find($s['program_share_type_id']);
                            if ($pst) $pstId = $pst->id;
                        }

                        if (!$pstId && !empty($s['program_share_type_key'])) {
                            $pst = ProgramShareType::where('key', $s['program_share_type_key'])->first();
                            if ($pst) $pstId = $pst->id;
                        }

                        // create custom program share type when name provided and no existing type
                        if (!$pstId && !empty($s['name'])) {
                            $new = ProgramShareType::create([
                                'name' => $s['name'],
                                'key' => $s['program_share_type_key'] ?? Str::slug($s['name']) . '-' . Str::random(4),
                                'default_type' => $s['type'] ?? 'percentage',
                                'program_id' => $program->id,
                                'orders' => $s['orders'] ?? null,
                                'created_by' => auth()->id(),
                            ]);
                            $pstId = $new->id;
                        }

                        if ($pstId) {
                            ProgramShare::create([
                                'program_id' => $program->id,
                                'program_share_type_id' => $pstId,
                                'type' => $s['type'] ?? 'percentage',
                                'value' => isset($s['value']) ? $s['value'] : null,
                                'created_by' => auth()->id(),
                            ]);
                        }
                    }
                });
            }

            return response()->json([
                'success' => true,
                'message' => 'Program berhasil ditambahkan',
                'data' => $program->load(['creator']),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan program',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $program = Program::with(['creator', 'updater', 'shares.type'])->find($id);

        if (!$program) {
            return response()->json([
                'success' => false,
                'message' => 'Program tidak ditemukan',
            ], 404);
        }

        // return program with shares and their program_share_type object
        $program->load('shares.type');
        $data = $program->toArray();
        // normalize shares to include program_share_type fields at top-level for frontend convenience
        $data['shares'] = collect($program->shares)->map(function ($s) {
            // Avoid conflict between the share's `type` attribute and the relation named `type`.
            // Try to use the eager-loaded relation first, otherwise fall back to lookup by id.
            $pst = null;
            if ($s->relationLoaded('type')) {
                $pst = $s->getRelationValue('type');
            }
            if (! $pst && ! empty($s->program_share_type_id)) {
                $pst = ProgramShareType::find($s->program_share_type_id);
            }

            return [
                'id' => $s->id,
                'program_share_type_id' => $s->program_share_type_id,
                'program_share_type_key' => $pst->key ?? null,
                'name' => $pst->name ?? null,
                'type' => $s->type,
                'value' => $s->value,
            ];
        })->values()->all();

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Return balance details for a program and month.
     * Query params: month=YYYY-MM (optional, defaults to month of today)
     */
    public function balance(Request $request, string $id)
    {
        $program = Program::with(['shares.type'])->find($id);
        if (! $program) {
            return response()->json(['success' => false, 'message' => 'Program tidak ditemukan'], 404);
        }
        $month = $request->get('month');
        // allow caller to request a specific share key (e.g. 'ops_1','ops_2','program')
        $shareKey = $request->get('share_key', 'program');
        // lookback: how many months before the provided month to take inflow from (default 1 => previous month)
        $lookback = (int) $request->get('lookback', 1);
        // allow explicit date range (start_date, end_date) to compute allocation across arbitrary ranges
        $startDateParam = $request->get('start_date');
        $endDateParam = $request->get('end_date');
        try {
            // determine start/end for allocation window
            if ($startDateParam || $endDateParam) {
                $start = $startDateParam ? \Carbon\Carbon::parse($startDateParam)->startOfDay() : null;
                $end = $endDateParam ? \Carbon\Carbon::parse($endDateParam)->endOfDay() : null;
                // if only one side provided, ensure both are set (use same day for missing side)
                if (! $start && $end) $start = (clone $end)->startOfDay();
                if (! $end && $start) $end = (clone $start)->endOfDay();
            } else {
                // If caller did not provide a date range, compute allocation across the full transaksi history
                // for this program: from earliest transaksi to latest transaksi. If no transaksi exist, fall
                // back to previous month-based behavior.
                $minDate = \App\Models\Transaksi::where('program_id', $program->id)->min('tanggal_transaksi');
                $maxDate = \App\Models\Transaksi::where('program_id', $program->id)->max('tanggal_transaksi');
                if ($minDate && $maxDate) {
                    $start = \Carbon\Carbon::parse($minDate)->startOfDay();
                    $end = \Carbon\Carbon::parse($maxDate)->endOfDay();
                } else {
                    // determine base month (the month of usedAt or provided month), then subtract lookback months
                    $baseMonth = $month ? \Carbon\Carbon::parse($month . '-01')->startOfMonth() : \Carbon\Carbon::now()->startOfMonth();
                    $allocMonth = (clone $baseMonth)->subMonths($lookback)->startOfMonth();
                    $start = $allocMonth;
                    $end = (clone $start)->endOfMonth();
                }
            }

            // inflow: sum of transaksi.nominal for program in month
            $inflowQuery = \App\Models\Transaksi::where('program_id', $program->id);
            if ($start && $end) {
                $inflowQuery->whereBetween('tanggal_transaksi', [$start->toDateString(), $end->toDateString()]);
            }
            $inflow = $inflowQuery->sum('nominal');

            // find share applicable to program pool - prefer program_share_type key matching $shareKey if exists
            $allocation = null;
            foreach ($program->shares as $s) {
                $pst = $s->relationLoaded('type') ? $s->getRelationValue('type') : \App\Models\ProgramShareType::find($s->program_share_type_id);
                if ($pst && ($pst->key ?? null) === $shareKey) {
                    $allocation = $s;
                    break;
                }
            }
            // fallback to first share
            if (! $allocation && count($program->shares) > 0) $allocation = $program->shares[0];

            $allocated = $inflow;
            if ($allocation) {
                if ($allocation->type === 'percentage' && $allocation->value !== null) {
                    $allocated = (int) floor($inflow * (float)$allocation->value / 100);
                } elseif ($allocation->type === 'nominal' && $allocation->value !== null) {
                    $allocated = (int) $allocation->value;
                }
            }

            // determine which submission_type(s) correspond to the requested share key
            $submissionTypeForShare = null;
            if ($shareKey === 'program') $submissionTypeForShare = 'program';
            elseif ($shareKey === 'ops_2') $submissionTypeForShare = 'operasional';
            elseif ($shareKey === 'ops_1') $submissionTypeForShare = 'gaji karyawan';

            // outflow: sum of disbursements for program in window, filtered by matching pengajuan.submission_type when possible
            $outflowQuery = \App\Models\PengajuanDanaDisbursement::where('program_id', $program->id);
            if ($start && $end) {
                $outflowQuery->whereBetween('tanggal_disburse', [$start->toDateString(), $end->toDateString()]);
            }
            if ($submissionTypeForShare) {
                $outflowQuery->whereHas('pengajuan', function ($q) use ($submissionTypeForShare) {
                    $q->where('submission_type', $submissionTypeForShare);
                });
            }
            $outflow = $outflowQuery->sum('amount');

            $remaining = max(0, $allocated - $outflow);

            // provide breakdown of transaksi for the month
            $transaksisQuery = \App\Models\Transaksi::where('program_id', $program->id)->orderBy('tanggal_transaksi');
            if ($start && $end) {
                $transaksisQuery->whereBetween('tanggal_transaksi', [$start->toDateString(), $end->toDateString()]);
            }
            $transaksis = $transaksisQuery->get(['id','kode','nominal','tanggal_transaksi']);

            // compute used per transaksi from disbursements
            $transaksiIds = $transaksis->pluck('id')->filter()->all();
            $usedMap = [];
            if (! empty($transaksiIds)) {
                $usedRowsQuery = \App\Models\PengajuanDanaDisbursement::whereIn('transaksi_id', $transaksiIds);
                if ($submissionTypeForShare) {
                    $usedRowsQuery->whereHas('pengajuan', function ($q) use ($submissionTypeForShare) {
                        $q->where('submission_type', $submissionTypeForShare);
                    });
                }
                $usedRows = $usedRowsQuery->selectRaw('transaksi_id, SUM(amount) as used')->groupBy('transaksi_id')->get();
                foreach ($usedRows as $ur) {
                    $usedMap[$ur->transaksi_id] = (int) $ur->used;
                }
            }

            // determine per-transaksi allocation based on selected share type (key = $shareKey)
            $shareType = $allocation ?? null;

            // enrich transaksis with used/available
            $transaksisOut = $transaksis->map(function ($t) use ($usedMap, $shareType) {
                $used = isset($usedMap[$t->id]) ? (int)$usedMap[$t->id] : 0;
                $nominal = (int)$t->nominal;

                // compute allocated amount for this transaksi based on shareType
                $allocatedForTransaksi = $nominal;
                if ($shareType) {
                    if ($shareType->type === 'percentage' && $shareType->value !== null) {
                        $allocatedForTransaksi = (int) floor($nominal * (float)$shareType->value / 100);
                    } elseif ($shareType->type === 'nominal' && $shareType->value !== null) {
                        $allocatedForTransaksi = min($nominal, (int)$shareType->value);
                    }
                }

                $available = max(0, $allocatedForTransaksi - $used);
                return [
                    'id' => $t->id,
                    'kode' => $t->kode ?? null,
                    'nominal' => $nominal,
                    'allocated' => $allocatedForTransaksi,
                    'tanggal_transaksi' => $t->tanggal_transaksi,
                    'used' => $used,
                    'available' => $available,
                ];
            })->values();

            return response()->json([
                'success' => true,
                'data' => [
                    'program_id' => $program->id,
                    'month' => $start ? $start->format('Y-m') : null,
                    'start_date' => $start ? $start->toDateString() : null,
                    'end_date' => $end ? $end->toDateString() : null,
                    'inflow' => (int) $inflow,
                    'allocated' => (int) $allocated,
                    'outflow' => (int) $outflow,
                    'remaining' => (int) $remaining,
                    'transaksis' => $transaksisOut,
                    'shares' => $program->shares->map(function ($s) {
                        $pst = $s->relationLoaded('type') ? $s->getRelationValue('type') : \App\Models\ProgramShareType::find($s->program_share_type_id);
                        return [
                            'id' => $s->id,
                            'program_share_type_id' => $s->program_share_type_id,
                            'program_share_type_key' => $pst->key ?? null,
                            'name' => $pst->name ?? null,
                            'type' => $s->type,
                            'value' => $s->value,
                        ];
                    })->values(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghitung saldo', 'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan'], 500);
        }
    }

    /**
     * Return per-program aggregated allocation totals per program share type.
     * Optional query params: start_date, end_date
     */
    public function programSharesSummary(Request $request)
    {
        $startDateParam = $request->get('start_date');
        $endDateParam = $request->get('end_date');

        try {
            $start = $startDateParam ? \Carbon\Carbon::parse($startDateParam)->startOfDay() : null;
            $end = $endDateParam ? \Carbon\Carbon::parse($endDateParam)->endOfDay() : null;

            // define preferred order of known share keys
            $preferred = ['dp','ops_1','ops_2','program','fee_mitra','bonus','championship'];
            $columns = $preferred;

            $programs = \App\Models\Program::with(['shares.type'])->orderBy('nama_program')->get();

            $rows = [];
            $totals = ['total_transaksi' => 0];
            $disbursedTotals = ['total_transaksi' => 0];

            foreach ($programs as $program) {
                // inflow for this program
                $inflowQuery = \App\Models\Transaksi::where('program_id', $program->id);
                if ($start && $end) {
                    $inflowQuery->whereBetween('tanggal_transaksi', [$start->toDateString(), $end->toDateString()]);
                }
                $inflow = (int) $inflowQuery->sum('nominal');

                $row = [
                    'program_id' => $program->id,
                    'program_name' => $program->nama_program ?? $program->nama ?? '',
                    'total_transaksi' => $inflow,
                ];

                // include shares metadata for frontend (key => {type, value})
                $sharesMeta = [];
                foreach ($program->shares as $smeta) {
                    $pst = $smeta->relationLoaded('type') ? $smeta->getRelationValue('type') : \App\Models\ProgramShareType::find($smeta->program_share_type_id);
                    $keym = $pst->key ?? ($smeta->program_share_type_key ?? null) ?? 'unknown';
                    $sharesMeta[$keym] = [
                        'type' => $smeta->type,
                        'value' => $smeta->value,
                    ];
                }
                $row['shares_meta'] = $sharesMeta;

                // initialize columns for this row
                foreach ($columns as $c) $row[$c] = 0;

                // gather any additional share keys present in this program
                foreach ($program->shares as $s) {
                    $pst = $s->relationLoaded('type') ? $s->getRelationValue('type') : \App\Models\ProgramShareType::find($s->program_share_type_id);
                    $key = $pst->key ?? ($s->program_share_type_key ?? null) ?? 'unknown';
                    if (! in_array($key, $columns)) {
                        $columns[] = $key;
                        // ensure totals and existing rows have this key (initialize later)
                    }
                }

                // compute allocated per share based on inflow (same logic as balance)
                foreach ($program->shares as $s) {
                    $pst = $s->relationLoaded('type') ? $s->getRelationValue('type') : \App\Models\ProgramShareType::find($s->program_share_type_id);
                    $key = $pst->key ?? ($s->program_share_type_key ?? null) ?? 'unknown';
                    $allocated = $inflow;
                    if ($s->type === 'percentage' && $s->value !== null) {
                        $allocated = (int) floor($inflow * (float)$s->value / 100);
                    } elseif ($s->type === 'nominal' && $s->value !== null) {
                        $allocated = (int) $s->value;
                    }
                    if (! isset($row[$key])) $row[$key] = 0;
                    $row[$key] = $row[$key] + $allocated;
                }

                // compute disbursed amounts per submission_type mapping
                // map share keys to submission_type
                $map = ['program' => 'program', 'ops_1' => 'gaji karyawan', 'ops_2' => 'operasional'];

                $disbursedQuery = \App\Models\PengajuanDanaDisbursement::where('program_id', $program->id);
                if ($start && $end) {
                    $disbursedQuery->whereBetween('tanggal_disburse', [$start->toDateString(), $end->toDateString()]);
                }
                $disbursedRows = $disbursedQuery->selectRaw('pengajuan_dana_id, SUM(amount) as total')
                    ->groupBy('pengajuan_dana_id')
                    ->get();

                // attach disbursed amounts to corresponding share keys by looking up pengajuan submission_type
                foreach ($disbursedRows as $dr) {
                    $peng = \App\Models\PengajuanDana::find($dr->pengajuan_dana_id);
                    $stype = $peng ? ($peng->submission_type ?? null) : null;
                    // find key for this submission_type
                    $keyFor = array_search($stype, $map, true);
                    if ($keyFor === false) {
                        // if not mapped, try to map common names
                        if ($stype === 'operasional') $keyFor = 'ops_2';
                        elseif ($stype === 'gaji karyawan') $keyFor = 'ops_1';
                        else $keyFor = 'program';
                    }
                    if (! isset($row[$keyFor])) $row[$keyFor] = 0;
                    $row[$keyFor] += (int) $dr->total;
                }

                // accumulate totals
                $totals['total_transaksi'] += $row['total_transaksi'];
                foreach ($columns as $c) {
                    if (! isset($totals[$c])) $totals[$c] = 0;
                    $totals[$c] += isset($row[$c]) ? (int)$row[$c] : 0;
                }

                $rows[] = $row;
            }

            // Also build per-transaksi pivot rows: for each transaksi in range, include allocated amount per program
            $transQuery = \App\Models\Transaksi::query();
            if ($start && $end) {
                $transQuery->whereBetween('tanggal_transaksi', [$start->toDateString(), $end->toDateString()]);
            }
            $transQuery->orderByDesc('tanggal_transaksi');
            $transaksis = $transQuery->get(['id','kode','nominal','tanggal_transaksi','program_id']);

            $programMap = [];
            foreach ($programs as $p) {
                $programMap[$p->id] = $p;
            }

            $transRows = [];
            foreach ($transaksis as $t) {
                $prow = [
                    'id' => $t->id,
                    'kode' => $t->kode ?? null,
                    'tanggal_transaksi' => $t->tanggal_transaksi,
                    'nominal' => (int) $t->nominal,
                    'program_id' => $t->program_id ?? null,
                ];

                // initialize per-program and per-share columns to 0
                foreach ($programs as $p) {
                    // legacy total-per-program field
                    $prow['p_' . ($p->id)] = 0;
                    // per-share fields for each known column
                    foreach ($columns as $c) {
                        $prow['p_' . ($p->id) . '_' . $c] = 0;
                    }
                }

                // compute allocation for the program that owns this transaksi
                if (! empty($t->program_id) && isset($programMap[$t->program_id])) {
                    $prog = $programMap[$t->program_id];

                    // For each share of this program, compute allocated amount for this transaksi
                    foreach ($prog->shares as $s) {
                        $pst = $s->relationLoaded('type') ? $s->getRelationValue('type') : \App\Models\ProgramShareType::find($s->program_share_type_id);
                        $key = $pst->key ?? ($s->program_share_type_key ?? null) ?? 'program';

                        $allocatedForTrans = (int) $t->nominal;
                        if ($s->type === 'percentage' && $s->value !== null) {
                            $allocatedForTrans = (int) floor((int)$t->nominal * (float)$s->value / 100);
                        } elseif ($s->type === 'nominal' && $s->value !== null) {
                            $allocatedForTrans = min((int)$t->nominal, (int)$s->value);
                        }

                        $fldShare = 'p_' . ($t->program_id) . '_' . $key;
                        $prow[$fldShare] = $allocatedForTrans;

                        // If this share represents the 'program' pool, also set legacy per-program field
                        if ($key === 'program') {
                            $prow['p_' . ($t->program_id)] = $allocatedForTrans;
                        }
                    }
                }

                $transRows[] = $prow;
            }

            // prepare disbursedTotals separately by summing pengajuan_dana_disbursements grouped by mapped key across all programs
            $disbursedTotals = ['total_transaksi' => $totals['total_transaksi']];
            foreach ($columns as $c) $disbursedTotals[$c] = 0;
            $disq = \App\Models\PengajuanDanaDisbursement::query();
            if ($start && $end) {
                $disq->whereBetween('tanggal_disburse', [$start->toDateString(), $end->toDateString()]);
            }
            $disrows = $disq->selectRaw('pengajuan_dana_disbursements.*, pengajuan_danas.submission_type')
                ->leftJoin('pengajuan_danas', 'pengajuan_danas.id', '=', 'pengajuan_dana_disbursements.pengajuan_dana_id')
                ->get();

            // Build both overall disbursed totals by share key and per-program-per-share mapping
            $disbursedTotalsByProgram = [];
            // initialize keys
            foreach ($programs as $p) {
                $base = 'p_' . $p->id;
                $disbursedTotalsByProgram[$base] = 0;
                $disbursedTotalsByProgram[$base . '_nominal'] = 0;
                foreach ($columns as $c) {
                    $disbursedTotalsByProgram[$base . '_' . $c] = 0;
                }
            }

            foreach ($disrows as $dr) {
                $stype = $dr->submission_type ?? null;
                $keyFor = 'program';
                if ($stype === 'operasional') $keyFor = 'ops_2';
                elseif ($stype === 'gaji karyawan') $keyFor = 'ops_1';

                // accumulate overall by share-type
                if (! isset($disbursedTotals[$keyFor])) $disbursedTotals[$keyFor] = 0;
                $disbursedTotals[$keyFor] += (int) $dr->amount;

                // accumulate per-program-per-share if program_id exists on disbursement
                $progId = $dr->program_id ?? null;
                if ($progId) {
                    $base = 'p_' . $progId;
                    // ensure key exists
                    if (! isset($disbursedTotalsByProgram[$base])) {
                        $disbursedTotalsByProgram[$base] = 0;
                        $disbursedTotalsByProgram[$base . '_nominal'] = 0;
                    }
                    $disbursedTotalsByProgram[$base] += (int) $dr->amount;
                    // also add to per-share key under program
                    $shareKey = $base . '_' . $keyFor;
                    if (! isset($disbursedTotalsByProgram[$shareKey])) $disbursedTotalsByProgram[$shareKey] = 0;
                    $disbursedTotalsByProgram[$shareKey] += (int) $dr->amount;
                    // nominal disbursed per program (use same as base total)
                    $disbursedTotalsByProgram[$base . '_nominal'] = $disbursedTotalsByProgram[$base];
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'columns' => $columns,
                    'rows' => $rows,
                    'totals' => $totals,
                    'disbursed_totals' => $disbursedTotals,
                    'disbursed_totals_by_program' => $disbursedTotalsByProgram,
                    'transaksis' => $transRows ?? [],
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghitung ringkasan', 'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $program = Program::find($id);

        if (!$program) {
            return response()->json([
                'success' => false,
                'message' => 'Program tidak ditemukan',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama_program' => 'required|string|max:255',
            'persentase_hak_program' => 'nullable|numeric|min:0|max:100',
            'persentase_hak_program_operasional' => 'nullable|numeric|min:0|max:100',
            'persentase_hak_championship' => 'nullable|numeric|min:0|max:100',
            'tipe_pembagian_marketing' => 'nullable|in:percentage,nominal',
            'persentase_hak_marketing' => 'nullable|numeric|min:0|max:100',
            'persentase_hak_operasional_1' => 'nullable|numeric|min:0|max:100',
            'persentase_hak_iklan' => 'nullable|numeric|min:0|max:100',
            'persentase_hak_operasional_2' => 'nullable|numeric|min:0|max:100',
            'persentase_hak_operasional_3' => 'nullable|numeric|min:0|max:100',
            'jumlah_persentase' => 'nullable|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $data = $request->only([
                'nama_program',
                'persentase_hak_program',
                'persentase_hak_program_operasional',
                'persentase_hak_championship',
                'tipe_pembagian_marketing',
                'persentase_hak_marketing',
                'persentase_hak_operasional_1',
                'persentase_hak_iklan',
                'persentase_hak_operasional_2',
                'persentase_hak_operasional_3',
                'jumlah_persentase',
            ]);

            $data['updated_by'] = auth()->id();

            $program->update($data);

            // handle shares: remove existing program_shares for this program and recreate from payload
            if ($request->has('shares') && is_array($request->shares)) {
                DB::transaction(function () use ($request, $program) {
                    // delete existing shares
                    ProgramShare::where('program_id', $program->id)->delete();

                    foreach ($request->shares as $s) {
                        $pstId = null;

                        if (!empty($s['program_share_type_id'])) {
                            $pst = ProgramShareType::find($s['program_share_type_id']);
                            if ($pst) $pstId = $pst->id;
                        }

                        if (!$pstId && !empty($s['program_share_type_key'])) {
                            $pst = ProgramShareType::where('key', $s['program_share_type_key'])->first();
                            if ($pst) $pstId = $pst->id;
                        }

                        if (!$pstId && !empty($s['name'])) {
                            $new = ProgramShareType::create([
                                'name' => $s['name'],
                                'key' => $s['program_share_type_key'] ?? Str::slug($s['name']) . '-' . Str::random(4),
                                'default_type' => $s['type'] ?? 'percentage',
                                'program_id' => $program->id,
                                'orders' => $s['orders'] ?? null,
                                'created_by' => auth()->id(),
                            ]);
                            $pstId = $new->id;
                        }

                        if ($pstId) {
                            ProgramShare::create([
                                'program_id' => $program->id,
                                'program_share_type_id' => $pstId,
                                'type' => $s['type'] ?? 'percentage',
                                'value' => isset($s['value']) ? $s['value'] : null,
                                'created_by' => auth()->id(),
                            ]);
                        }
                    }
                });
            }

            return response()->json([
                'success' => true,
                'message' => 'Program berhasil diupdate',
                'data' => $program->fresh()->load(['creator', 'updater']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate program',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $program = Program::find($id);

        if (!$program) {
            return response()->json([
                'success' => false,
                'message' => 'Program tidak ditemukan',
            ], 404);
        }

        try {
            $program->deleted_by = auth()->id();
            $program->save();
            $program->delete();

            return response()->json([
                'success' => true,
                'message' => 'Program berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus program',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan',
            ], 500);
        }
    }
}
