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
        try {
            // determine base month (the month of usedAt or provided month), then subtract lookback months
            $baseMonth = $month ? \Carbon\Carbon::parse($month . '-01')->startOfMonth() : \Carbon\Carbon::now()->startOfMonth();
            $allocMonth = (clone $baseMonth)->subMonths($lookback)->startOfMonth();
            $start = $allocMonth;
            $end = (clone $start)->endOfMonth();

            // inflow: sum of transaksi.nominal for program in month
            $inflow = \App\Models\Transaksi::where('program_id', $program->id)
                ->whereBetween('tanggal_transaksi', [$start->toDateString(), $end->toDateString()])
                ->sum('nominal');

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

            // outflow: sum of disbursements for program in month
            $outflow = \App\Models\PengajuanDanaDisbursement::where('program_id', $program->id)
                ->whereBetween('tanggal_disburse', [$start->toDateString(), $end->toDateString()])
                ->sum('amount');

            $remaining = max(0, $allocated - $outflow);

            // provide breakdown of transaksi for the month
            $transaksis = \App\Models\Transaksi::where('program_id', $program->id)
                ->whereBetween('tanggal_transaksi', [$start->toDateString(), $end->toDateString()])
                ->orderBy('tanggal_transaksi')
                ->get(['id','kode','nominal','tanggal_transaksi']);

            // compute used per transaksi from disbursements
            $transaksiIds = $transaksis->pluck('id')->filter()->all();
            $usedMap = [];
            if (! empty($transaksiIds)) {
                $usedRows = \App\Models\PengajuanDanaDisbursement::whereIn('transaksi_id', $transaksiIds)
                    ->selectRaw('transaksi_id, SUM(amount) as used')
                    ->groupBy('transaksi_id')
                    ->get();
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
                    'month' => $start->format('Y-m'),
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
