<?php

namespace App\Http\Controllers;

use App\Models\PengajuanDana;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\PengajuanDanaDisbursement;

class PengajuanDanaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PengajuanDana::with(['fundraiser:id,name', 'kantorCabang:id,nama', 'program:id,nama_program']);

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->whereHas('fundraiser', fn($q) => $q->where('name', 'like', "%{$search}%"));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('tanggal')) {
            $tanggal = $request->input('tanggal');
            $query->whereDate('created_at', $tanggal);
        }

        if ($request->filled('amount_min')) {
            $query->where('amount', '>=', (int)$request->input('amount_min'));
        }

        if ($request->filled('amount_max')) {
            $query->where('amount', '<=', (int)$request->input('amount_max'));
        }

        if ($request->filled('kantor_cabang_id')) {
            $query->where('kantor_cabang_id', $request->input('kantor_cabang_id'));
        }

        $perPage = (int) $request->get('per_page', 20);
        $page = (int) $request->get('page', 1);

        $pengajuans = $query->orderByDesc('created_at')->paginate($perPage, ['*'], 'page', $page);

        $pengajuans->getCollection()->transform(function (PengajuanDana $p) {
            return [
                'id' => $p->id,
                'fundraiser' => $p->fundraiser ? ['id' => $p->fundraiser->id, 'name' => $p->fundraiser->name] : null,
                'submission_type' => $p->submission_type,
                'amount' => $p->amount,
                'used_at' => $p->used_at ? $p->used_at->format('Y-m-d') : null,
                'purpose' => $p->purpose,
                'kantor_cabang' => $p->kantorCabang ? ['id' => $p->kantorCabang->id, 'nama' => $p->kantorCabang->nama] : null,
                'program' => $p->program ? ['id' => $p->program->id, 'nama' => $p->program->nama_program] : null,
                'status' => $p->status,
                'created_at' => $p->created_at ? $p->created_at->toDateTimeString() : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $pengajuans->items(),
            'pagination' => [
                'current_page' => $pengajuans->currentPage(),
                'last_page' => $pengajuans->lastPage(),
                'per_page' => $pengajuans->perPage(),
                'total' => $pengajuans->total(),
            ],
        ]);
    }

    /**
     * Allocate disbursements for a pengajuan from transaksi in the used_at month.
     * This will create PengajuanDanaDisbursement rows consuming transaksi nominal FIFO.
     * Throws exception if insufficient funds.
     */
    private function allocateDisbursements(string $pengajuanId, string $programId, $usedAt, int $amount, ?int $createdBy = null): void
    {
        $start = \Carbon\Carbon::parse($usedAt)->startOfMonth()->toDateString();
        $end = \Carbon\Carbon::parse($usedAt)->endOfMonth()->toDateString();

        // compute allocated inflow per ProgramController logic
        $inflow = \App\Models\Transaksi::where('program_id', $programId)
            ->whereBetween('tanggal_transaksi', [$start, $end])
            ->sum('nominal');

        // find allocation share
        $program = \App\Models\Program::with('shares.type')->find($programId);
        $allocation = null;
        if ($program) {
            foreach ($program->shares as $s) {
                $pst = $s->relationLoaded('type') ? $s->getRelationValue('type') : \App\Models\ProgramShareType::find($s->program_share_type_id);
                if ($pst && ($pst->key ?? null) === 'program') { $allocation = $s; break; }
            }
            if (! $allocation && count($program->shares) > 0) $allocation = $program->shares[0];
        }

        $allocated = $inflow;
        if ($allocation) {
            if ($allocation->type === 'percentage' && $allocation->value !== null) {
                $allocated = (int) floor($inflow * (float)$allocation->value / 100);
            } elseif ($allocation->type === 'nominal' && $allocation->value !== null) {
                $allocated = (int) $allocation->value;
            }
        }

        // existing outflow (including other pengajuan disbursements)
        $outflow = PengajuanDanaDisbursement::where('program_id', $programId)
            ->whereBetween('tanggal_disburse', [$start, $end])
            ->sum('amount');

        $remaining = max(0, $allocated - $outflow);
        if ($amount > $remaining) {
            throw new \Exception('Insufficient program funds for selected month: remaining ' . $remaining);
        }

        // allocate from transaksi FIFO
        $transaksis = \App\Models\Transaksi::where('program_id', $programId)
            ->whereBetween('tanggal_transaksi', [$start, $end])
            ->orderBy('tanggal_transaksi')
            ->lockForUpdate()
            ->get(['id','nominal','tanggal_transaksi']);

        $need = $amount;
        foreach ($transaksis as $t) {
            if ($need <= 0) break;

            // compute already used from this transaksi
            $used = PengajuanDanaDisbursement::where('transaksi_id', $t->id)->sum('amount');

            // compute allocated amount for this transaksi based on program share
            $nominal = (int)$t->nominal;
            $allocatedForTransaksi = $nominal;
            if ($allocation) {
                if ($allocation->type === 'percentage' && $allocation->value !== null) {
                    $allocatedForTransaksi = (int) floor($nominal * (float)$allocation->value / 100);
                } elseif ($allocation->type === 'nominal' && $allocation->value !== null) {
                    $allocatedForTransaksi = min($nominal, (int)$allocation->value);
                }
            }

            $available = max(0, $allocatedForTransaksi - (int)$used);
            if ($available <= 0) continue;

            $take = min($available, $need);

            PengajuanDanaDisbursement::create([
                'pengajuan_dana_id' => $pengajuanId,
                'transaksi_id' => $t->id,
                'program_id' => $programId,
                'amount' => $take,
                'tanggal_disburse' => $t->tanggal_transaksi ?? $usedAt,
                'created_by' => $createdBy,
            ]);

            $need -= $take;
        }

        if ($need > 0) {
            // should not happen due to earlier check, but guard
            throw new \Exception('Failed to allocate full amount for pengajuan');
        }
    }

    /**
     * Return options for frontend form: eligible fundraisers (self + descendants)
     * and user's kantor cabang assignments.
     */
    public function options(Request $request)
    {
        $user = auth()->user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
        }

        try {
            // admins should see all users
            if ($this->userIsAdmin($user)) {
                $users = \App\Models\User::orderBy('name')->get(['id', 'name']);
            } else {
                $allowed = \App\Models\User::descendantIdsOf($user->id);
                $users = \App\Models\User::whereIn('id', $allowed)->orderBy('name')->get(['id', 'name']);
            }

            // Kantor cabang: admins see all branches, others see assigned branches only
            if ($this->userIsAdmin($user)) {
                $kantorCabangs = \App\Models\KantorCabang::whereNull('deleted_at')->orderBy('nama')->get(['id', 'nama']);
            } else {
                // qualify columns to avoid ambiguous `id` when joining pivot table
                $kantorCabangs = $user->kantorCabangs()->select('kantor_cabang.id', 'kantor_cabang.nama')->get();
            }

            return response()->json([
                'success' => true,
                'data' => [
                        'users' => $users->map(fn($u) => ['id' => $u->id, 'name' => $u->name])->values(),
                        'kantor_cabangs' => $kantorCabangs->map(fn($k) => ['id' => $k->id, 'nama' => $k->nama])->values(),
                        'programs' => \App\Models\Program::orderBy('nama_program')->get(['id','nama_program'])->map(fn($p) => ['id' => $p->id, 'nama' => $p->nama_program])->values(),
                    ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal mengambil opsi', 'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fundraiser_id' => 'nullable|exists:users,id',
            'submission_type' => 'required|string|max:100',
            'program_id' => 'nullable|uuid|exists:program,id',
            'amount' => 'required|integer|min:1',
            'used_at' => 'nullable|date',
            'purpose' => 'nullable|string',
            'kantor_cabang_id' => 'nullable|uuid',
            'status' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            return DB::transaction(function () use ($request) {
                $data = [
                    'fundraiser_id' => $request->input('fundraiser_id'),
                    'submission_type' => $request->input('submission_type'),
                    'amount' => (int)$request->input('amount'),
                    'used_at' => $request->input('used_at'),
                    'purpose' => $request->input('purpose'),
                    'kantor_cabang_id' => $request->input('kantor_cabang_id'),
                    'program_id' => $request->input('program_id'),
                    'status' => $request->input('status') ?? 'Draft',
                    'created_by' => auth()->id(),
                ];

                $p = PengajuanDana::create($data);

                // If program submission and already approved, allocate disbursements from transaksi in the used_at month
                // Do NOT allocate for drafts/pending states — allocations should only be created when pengajuan is approved
                if ($p->submission_type === 'program' && $p->program_id && $p->used_at) {
                    if (is_string($p->status) && strtolower(trim($p->status)) === 'approved') {
                        $this->allocateDisbursements($p->id, $p->program_id, $p->used_at, $p->amount, auth()->id());
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Pengajuan dana berhasil ditambahkan',
                    'data' => $p,
                ], 201);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan pengajuan dana',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $p = PengajuanDana::with(['fundraiser:id,name', 'kantorCabang:id,nama', 'program:id,nama_program', 'disbursements'])->find($id);
        if (! $p) {
            return response()->json(['success' => false, 'message' => 'Pengajuan dana tidak ditemukan'], 404);
        }

        $data = [
            'id' => $p->id,
            'fundraiser' => $p->fundraiser ? ['id' => $p->fundraiser->id, 'name' => $p->fundraiser->name] : null,
            'submission_type' => $p->submission_type,
            'amount' => $p->amount,
            'used_at' => $p->used_at ? $p->used_at->format('Y-m-d') : null,
            'purpose' => $p->purpose,
            'kantor_cabang' => $p->kantorCabang ? ['id' => $p->kantorCabang->id, 'nama' => $p->kantorCabang->nama] : null,
            'program' => $p->program ? ['id' => $p->program->id, 'nama' => $p->program->nama_program] : null,
            'status' => $p->status,
            'disbursements' => $p->disbursements->map(function ($d) {
                return [
                    'id' => $d->id,
                    'transaksi_id' => $d->transaksi_id,
                    'amount' => $d->amount,
                    'tanggal_disburse' => $d->tanggal_disburse ? $d->tanggal_disburse->format('Y-m-d') : null,
                ];
            })->values(),
        ];

        return response()->json(['success' => true, 'data' => $data]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $p = PengajuanDana::find($id);
        if (! $p) {
            return response()->json(['success' => false, 'message' => 'Pengajuan dana tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'fundraiser_id' => 'nullable|exists:users,id',
            'submission_type' => 'required|string|max:100',
            'program_id' => 'nullable|uuid|exists:program,id',
            'amount' => 'required|integer|min:1',
            'used_at' => 'nullable|date',
            'purpose' => 'nullable|string',
            'kantor_cabang_id' => 'nullable|uuid',
            'status' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            return DB::transaction(function () use ($request, $p) {
                $p->update(array_merge($request->only(['fundraiser_id','submission_type','program_id','amount','used_at','purpose','kantor_cabang_id','status']), ['updated_by' => auth()->id()]));

                // Re-allocate disbursements when program/used_at/amount changed.
                // Remove previous disbursements first. Only create new disbursements when the pengajuan is approved.
                if ($p->submission_type === 'program' && $p->program_id && $p->used_at) {
                    // remove previous disbursements for this pengajuan
                    PengajuanDanaDisbursement::where('pengajuan_dana_id', $p->id)->delete();
                    if (is_string($p->status) && strtolower(trim($p->status)) === 'approved') {
                        $this->allocateDisbursements($p->id, $p->program_id, $p->used_at, $p->amount, auth()->id());
                    }
                } else {
                    // if not program anymore, remove any previous disbursements
                    PengajuanDanaDisbursement::where('pengajuan_dana_id', $p->id)->delete();
                }

                return response()->json(['success' => true, 'message' => 'Pengajuan dana berhasil diupdate', 'data' => $p->fresh()], 200);
            });
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal mengupdate pengajuan dana', 'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $p = PengajuanDana::find($id);
        if (! $p) {
            return response()->json(['success' => false, 'message' => 'Pengajuan dana tidak ditemukan'], 404);
        }

        try {
            $p->deleted_by = auth()->id();
            $p->save();
            $p->delete();

            return response()->json(['success' => true, 'message' => 'Pengajuan dana berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus pengajuan dana', 'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan'], 500);
        }
    }
}
