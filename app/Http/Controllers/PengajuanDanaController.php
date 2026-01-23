<?php

namespace App\Http\Controllers;

use App\Models\PengajuanDana;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\PengajuanDanaDisbursement;
use App\Models\PengajuanDanaApproval;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PengajuanDanaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PengajuanDana::with([
            'fundraiser:id,name',
            'kantorCabang:id,nama',
            'program:id,nama_program',
            'latestApproval.approver:id,name',
        ]);

        // Apply data visibility based on user role and permissions
        $user = auth()->user();
        if ($user) {
            // role name is used elsewhere as 'admin' (lowercase), check both for safety
            $isAdmin = $user->hasRole('admin') || $user->hasRole('Admin');
            $hasApprovalPermission = $user->can('approve pengajuan dana') || $user->can('approval pengajuan dana');
            
            if (! $isAdmin) {
                if ($hasApprovalPermission) {
                    // Users with approval permission should see records for their kantor_cabang
                    // AND always be able to see their own pengajuan regardless of kantor_cabang.
                    if ($user->kantor_cabang_id) {
                        $query->where(function ($q) use ($user) {
                            $q->where('kantor_cabang_id', $user->kantor_cabang_id)
                              ->orWhere('fundraiser_id', $user->id);
                        });
                    } else {
                        // If no kantor_cabang assigned, only see own data
                        $query->where('fundraiser_id', $user->id);
                    }
                } else {
                    // Regular users can only see their own data
                    $query->where('fundraiser_id', $user->id);
                }
            }
            // Admin can see all data (no filter applied)
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->whereHas('fundraiser', fn($q) => $q->where('name', 'like', "%{$search}%"));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('submission_type')) {
            $query->where('submission_type', $request->input('submission_type'));
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
            $latestApproval = $p->latestApproval;
            $approver = $latestApproval && $latestApproval->relationLoaded('approver') ? $latestApproval->getRelationValue('approver') : null;
            $latestDecision = $latestApproval ? (string) $latestApproval->decision : null;
            $approverName = $approver ? $approver->name : null;
            $latestDecisionLower = $latestDecision ? strtolower($latestDecision) : null;

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
                'latest_approval' => $latestApproval ? [
                    'id' => $latestApproval->id,
                    'decision' => $latestApproval->decision,
                    'comment' => $latestApproval->comment,
                    'approver' => $approver ? ['id' => $approver->id, 'name' => $approver->name] : null,
                    'created_at' => $latestApproval->created_at ? $latestApproval->created_at->toDateTimeString() : null,
                ] : null,
                'approved_by_name' => ($latestDecisionLower === 'approved') ? ($approverName ?? null) : null,
                'rejected_by_name' => ($latestDecisionLower === 'rejected') ? ($approverName ?? null) : null,
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
    private function allocateDisbursements(string $pengajuanId, string $programId, $usedAt, int $amount, ?int $createdBy = null, string $shareKey = 'program', int $lookback = 1): void
    {
        // allocation month is the usedAt month minus lookback months (default lookback=1 -> previous month)
        $base = \Carbon\Carbon::parse($usedAt)->startOfMonth()->subMonths($lookback);
        $start = $base->toDateString();
        $end = (clone $base)->endOfMonth()->toDateString();

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
                if ($pst && ($pst->key ?? null) === $shareKey) { $allocation = $s; break; }
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
     * Compute remaining allocation for a program in usedAt month.
     * Returns integer remaining amount (>=0).
     */
    private function computeProgramRemaining(string $programId, $usedAt, ?string $excludePengajuanId = null, string $shareKey = 'program', int $lookback = 1): int
    {
        // allocation month = usedAt month minus lookback months (default 1 => previous month)
        $base = \Carbon\Carbon::parse($usedAt)->startOfMonth()->subMonths($lookback);
        $start = $base->toDateString();
        $end = (clone $base)->endOfMonth()->toDateString();

        $inflow = \App\Models\Transaksi::where('program_id', $programId)
            ->whereBetween('tanggal_transaksi', [$start, $end])
            ->sum('nominal');

        $program = \App\Models\Program::with('shares.type')->find($programId);
        $allocation = null;
        if ($program) {
            foreach ($program->shares as $s) {
                $pst = $s->relationLoaded('type') ? $s->getRelationValue('type') : \App\Models\ProgramShareType::find($s->program_share_type_id);
                if ($pst && ($pst->key ?? null) === $shareKey) { $allocation = $s; break; }
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

        $outflowQuery = PengajuanDanaDisbursement::where('program_id', $programId)
            ->whereBetween('tanggal_disburse', [$start, $end]);
        if ($excludePengajuanId) {
            $outflowQuery->where('pengajuan_dana_id', '!=', $excludePengajuanId);
        }
        $outflow = $outflowQuery->sum('amount');

        return max(0, $allocated - $outflow);
    }

    /**
     * Calculate aggregate balance across all programs for a specific share key
     * Returns array with inflow, allocated, outflow, remaining, and program_breakdown
     */
    private function calculateAggregateBalance(string $shareKey, $usedAt, ?string $startDate = null, ?string $endDate = null): array
    {
        // Date range handling
        if ($startDate && $endDate) {
            $start = \Carbon\Carbon::parse($startDate)->startOfDay()->toDateString();
            $end = \Carbon\Carbon::parse($endDate)->endOfDay()->toDateString();
        } else {
            // Default: use previous month lookback
            $base = \Carbon\Carbon::parse($usedAt)->startOfMonth()->subMonths(1);
            $start = $base->toDateString();
            $end = (clone $base)->endOfMonth()->toDateString();
        }

        $programs = \App\Models\Program::with(['shares.type'])->get();
        $totalInflow = 0;
        $totalAllocated = 0;
        $totalOutflow = 0;
        $programBreakdown = [];

        foreach ($programs as $program) {
            $inflow = \App\Models\Transaksi::where('program_id', $program->id)
                ->whereBetween('tanggal_transaksi', [$start, $end])
                ->sum('nominal');

            if ($inflow <= 0) continue;

            // Find allocation share
            $allocation = null;
            foreach ($program->shares as $s) {
                $pst = $s->relationLoaded('type') ? $s->getRelationValue('type') : \App\Models\ProgramShareType::find($s->program_share_type_id);
                if ($pst && ($pst->key ?? null) === $shareKey) {
                    $allocation = $s;
                    break;
                }
            }

            $allocated = $inflow;
            if ($allocation) {
                if ($allocation->type === 'percentage' && $allocation->value !== null) {
                    $allocated = (int) floor($inflow * (float)$allocation->value / 100);
                } elseif ($allocation->type === 'nominal' && $allocation->value !== null) {
                    $allocated = (int) $allocation->value;
                }
            }

            $outflow = PengajuanDanaDisbursement::where('program_id', $program->id)
                ->whereBetween('tanggal_disburse', [$start, $end])
                ->sum('amount');

            $remaining = max(0, $allocated - $outflow);

            if ($remaining > 0) {
                $programBreakdown[] = [
                    'program_id' => $program->id,
                    'program_name' => $program->nama_program,
                    'remaining' => (int) $remaining,
                    'inflow' => (int) $inflow,
                    'allocated' => (int) $allocated,
                    'outflow' => (int) $outflow,
                ];
            }

            $totalInflow += (int) $inflow;
            $totalAllocated += (int) $allocated;
            $totalOutflow += (int) $outflow;
        }

        // Sort by remaining DESC
        usort($programBreakdown, function($a, $b) {
            return $b['remaining'] - $a['remaining'];
        });

        return [
            'inflow' => $totalInflow,
            'allocated' => $totalAllocated,
            'outflow' => $totalOutflow,
            'remaining' => max(0, $totalAllocated - $totalOutflow),
            'program_breakdown' => $programBreakdown,
            'start_date' => $start,
            'end_date' => $end,
        ];
    }

    /**
     * Allocate disbursements from multiple programs (FIFO by remaining balance)
     * Used when program_id is NULL - automatically allocates from programs with highest balance
     */
    private function allocateFromMultiplePrograms(string $pengajuanId, string $shareKey, $usedAt, int $amount, ?int $createdBy = null, ?string $startDate = null, ?string $endDate = null): void
    {
        $aggData = $this->calculateAggregateBalance($shareKey, $usedAt, $startDate, $endDate);
        $programBreakdown = $aggData['program_breakdown'];

        $need = $amount;

        foreach ($programBreakdown as $prog) {
            if ($need <= 0) break;

            $programId = $prog['program_id'];
            $available = $prog['remaining'];

            $take = min($available, $need);

            // Allocate from this program's transaksi FIFO
            $start = $aggData['start_date'];
            $end = $aggData['end_date'];

            $transaksis = \App\Models\Transaksi::where('program_id', $programId)
                ->whereBetween('tanggal_transaksi', [$start, $end])
                ->orderBy('tanggal_transaksi')
                ->lockForUpdate()
                ->get(['id','nominal','tanggal_transaksi']);

            $programNeed = $take;

            // Find allocation share for this program
            $program = \App\Models\Program::with('shares.type')->find($programId);
            $allocation = null;
            if ($program) {
                foreach ($program->shares as $s) {
                    $pst = $s->relationLoaded('type') ? $s->getRelationValue('type') : \App\Models\ProgramShareType::find($s->program_share_type_id);
                    if ($pst && ($pst->key ?? null) === $shareKey) {
                        $allocation = $s;
                        break;
                    }
                }
            }

            foreach ($transaksis as $t) {
                if ($programNeed <= 0) break;

                $used = PengajuanDanaDisbursement::where('transaksi_id', $t->id)->sum('amount');

                $nominal = (int)$t->nominal;
                $allocatedForTransaksi = $nominal;
                if ($allocation) {
                    if ($allocation->type === 'percentage' && $allocation->value !== null) {
                        $allocatedForTransaksi = (int) floor($nominal * (float)$allocation->value / 100);
                    } elseif ($allocation->type === 'nominal' && $allocation->value !== null) {
                        $allocatedForTransaksi = min($nominal, (int)$allocation->value);
                    }
                }

                $availableFromTransaksi = max(0, $allocatedForTransaksi - (int)$used);
                if ($availableFromTransaksi <= 0) continue;

                $takeFromTransaksi = min($availableFromTransaksi, $programNeed);

                PengajuanDanaDisbursement::create([
                    'pengajuan_dana_id' => $pengajuanId,
                    'transaksi_id' => $t->id,
                    'program_id' => $programId,
                    'amount' => $takeFromTransaksi,
                    'tanggal_disburse' => $t->tanggal_transaksi ?? $usedAt,
                    'created_by' => $createdBy,
                ]);

                $programNeed -= $takeFromTransaksi;
            }

            $need -= $take;
        }

        if ($need > 0) {
            throw new \Exception('Failed to allocate full amount from available programs');
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
            'submission_type' => 'required|string',  // Changed: no longer restricted to hardcoded values
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
            // Pre-save validation: if program, operasional, or gaji karyawan submission, ensure amount <= remaining
            if ($request->filled('used_at')) {
                try {
                    $stype = $request->input('submission_type');
                    
                    // Map submission_type to share_key
                    // Try alias mapping first (new dynamic approach)
                    $shareKey = \App\Http\Controllers\ProgramShareTypeController::getShareKeyFromAlias($stype);
                    
                    // Fallback to old hardcoded mapping for backward compatibility
                    if (!$shareKey) {
                        if ($stype === 'operasional') $shareKey = 'ops_2';
                        elseif ($stype === 'gaji karyawan') $shareKey = 'ops_1';
                        elseif ($stype === 'program') $shareKey = 'program';
                        else $shareKey = 'program'; // default fallback
                    }

                    // Determine if single program or all programs
                    $isSingleProgram = $request->filled('program_id');

                    if ($isSingleProgram) {
                        // EXISTING LOGIC: Single program validation

                    // If caller provided explicit date range, compute remaining in that window
                    if ($request->filled('start_date') && $request->filled('end_date')) {
                        $start = \Carbon\Carbon::parse($request->input('start_date'))->startOfDay();
                        $end = \Carbon\Carbon::parse($request->input('end_date'))->endOfDay();

                        // compute inflow in range
                        $inflow = \App\Models\Transaksi::where('program_id', $request->input('program_id'))
                            ->whereBetween('tanggal_transaksi', [$start->toDateString(), $end->toDateString()])
                            ->sum('nominal');

                        // determine allocation share
                        $program = \App\Models\Program::with('shares.type')->find($request->input('program_id'));
                        $allocation = null;
                        if ($program) {
                            foreach ($program->shares as $s) {
                                $pst = $s->relationLoaded('type') ? $s->getRelationValue('type') : \App\Models\ProgramShareType::find($s->program_share_type_id);
                                if ($pst && ($pst->key ?? null) === $shareKey) { $allocation = $s; break; }
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

                        $outflow = \App\Models\PengajuanDanaDisbursement::where('program_id', $request->input('program_id'))
                            ->whereBetween('tanggal_disburse', [$start->toDateString(), $end->toDateString()])
                            ->whereHas('pengajuan', function ($q) use ($stype) {
                                $q->where('submission_type', $stype);
                            })->sum('amount');

                        $remaining = max(0, $allocated - $outflow);

                    } else {
                        // If caller did not provide explicit range, mimic the balance endpoint behavior:
                        // - if the program has transaksi, compute across full history (min..max)
                        // - otherwise fall back to previous-month lookback (lookback = 1)
                        $minDate = \App\Models\Transaksi::where('program_id', $request->input('program_id'))->min('tanggal_transaksi');
                        $maxDate = \App\Models\Transaksi::where('program_id', $request->input('program_id'))->max('tanggal_transaksi');

                        if ($minDate && $maxDate) {
                            $start = \Carbon\Carbon::parse($minDate)->startOfDay();
                            $end = \Carbon\Carbon::parse($maxDate)->endOfDay();

                            // compute inflow for full history
                            $inflow = \App\Models\Transaksi::where('program_id', $request->input('program_id'))
                                ->whereBetween('tanggal_transaksi', [$start->toDateString(), $end->toDateString()])
                                ->sum('nominal');

                            // determine allocation share
                            $program = \App\Models\Program::with('shares.type')->find($request->input('program_id'));
                            $allocation = null;
                            if ($program) {
                                foreach ($program->shares as $s) {
                                    $pst = $s->relationLoaded('type') ? $s->getRelationValue('type') : \App\Models\ProgramShareType::find($s->program_share_type_id);
                                    if ($pst && ($pst->key ?? null) === $shareKey) { $allocation = $s; break; }
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

                            $outflow = \App\Models\PengajuanDanaDisbursement::where('program_id', $request->input('program_id'))
                                ->whereBetween('tanggal_disburse', [$start->toDateString(), $end->toDateString()])
                                ->whereHas('pengajuan', function ($q) use ($stype) {
                                    $q->where('submission_type', $stype);
                                })->sum('amount');

                            $remaining = max(0, $allocated - $outflow);
                        } else {
                            // fallback: use previous month lookback
                            $remaining = $this->computeProgramRemaining($request->input('program_id'), $request->input('used_at'), null, $shareKey, 1);
                        }
                    }

                    if ((int)$request->input('amount') > $remaining) {
                        return response()->json(['success' => false, 'message' => 'nominal melebihi batas tidak dapat menyimpan', 'remaining' => $remaining], 422);
                    }
                    } else {
                        // ALL PROGRAMS: Calculate aggregate remaining across all programs
                        $aggData = $this->calculateAggregateBalance($shareKey, $request->input('used_at'), $request->input('start_date'), $request->input('end_date'));
                        $remaining = $aggData['remaining'];

                        if ((int)$request->input('amount') > $remaining) {
                            return response()->json(['success' => false, 'message' => 'nominal melebihi batas tidak dapat menyimpan dari semua program', 'remaining' => $remaining], 422);
                        }
                    }
                } catch (\Exception $e) {
                    // fallback: allow save but log
                }
            }

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

                // If program/operasional/gaji_karyawan submission and already approved, allocate disbursements
                // Do NOT allocate for drafts/pending states — allocations should only be created when pengajuan is approved
                if (in_array($p->submission_type, ['program','operasional','gaji karyawan']) && $p->used_at) {
                    if (is_string($p->status) && strtolower(trim($p->status)) === 'approved') {
                        $stype = $p->submission_type;
                        if ($stype === 'operasional') $shareKey = 'ops_2';
                        elseif ($stype === 'gaji karyawan') $shareKey = 'ops_1';
                        else $shareKey = 'program';
                        
                        if ($p->program_id) {
                            // Single program: use existing allocation logic
                            $this->allocateDisbursements($p->id, $p->program_id, $p->used_at, $p->amount, auth()->id(), $shareKey, 1);
                        } else {
                            // Multi-program: allocate from programs with highest balance first
                            $this->allocateFromMultiplePrograms($p->id, $shareKey, $p->used_at, $p->amount, auth()->id(), $request->input('start_date'), $request->input('end_date'));
                        }
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
            'submission_type' => 'required|string',  // Changed: no longer restricted to hardcoded values
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
            // Pre-update validation: if program, operasional or gaji_karyawan submission, ensure amount <= remaining
            if (in_array($request->input('submission_type'), ['program','operasional','gaji karyawan']) && $request->filled('program_id') && $request->filled('used_at')) {
                try {
                    $stype = $request->input('submission_type');
                    if ($stype === 'operasional') $shareKey = 'ops_2';
                    elseif ($stype === 'gaji karyawan') $shareKey = 'ops_1';
                    else $shareKey = 'program';
                        // use default lookback = 1 (previous month)
                        $remaining = $this->computeProgramRemaining($request->input('program_id'), $request->input('used_at'), $p->id, $shareKey, 1);
                    if ((int)$request->input('amount') > $remaining) {
                        return response()->json(['success' => false, 'message' => 'nominal melebihi batas tidak dapat menyimpan', 'remaining' => $remaining], 422);
                    }
                } catch (\Exception $e) {
                    // fallback: allow update but log
                }
            }

            return DB::transaction(function () use ($request, $p) {
                $p->update(array_merge($request->only(['fundraiser_id','submission_type','program_id','amount','used_at','purpose','kantor_cabang_id','status']), ['updated_by' => auth()->id()]));

                // Re-allocate disbursements when program/used_at/amount changed.
                // Remove previous disbursements first. Only create new disbursements when the pengajuan is approved.
                if (in_array($p->submission_type, ['program','operasional','gaji karyawan']) && $p->program_id && $p->used_at) {
                    // remove previous disbursements for this pengajuan
                    PengajuanDanaDisbursement::where('pengajuan_dana_id', $p->id)->delete();
                    if (is_string($p->status) && strtolower(trim($p->status)) === 'approved') {
                        $stype = $p->submission_type;
                        if ($stype === 'operasional') $shareKey = 'ops_2';
                        elseif ($stype === 'gaji karyawan') $shareKey = 'ops_1';
                        else $shareKey = 'program';
                        // allocate using default lookback = 1 (previous month)
                        $this->allocateDisbursements($p->id, $p->program_id, $p->used_at, $p->amount, auth()->id(), $shareKey, 1);
                    }
                } else {
                    // if not program/operasional anymore, remove any previous disbursements
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

    /**
     * Approve or reject a pengajuan dana.
     * Expects JSON: { decision: 'Approved'|'Rejected', comment: '...' }
     */
    public function approve(Request $request, string $id)
    {
        $user = auth()->user();
        if (! $user) return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);

        // permission check (Spatie) - accept either 'approve pengajuan dana' or seeded 'approval pengajuan dana'
        if (! ($user->can('approve pengajuan dana') || $user->can('approval pengajuan dana'))) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'decision' => 'required|string|max:50',
            'comment' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $decision = trim((string) $request->input('decision'));
        $comment = (string) $request->input('comment');

        return DB::transaction(function () use ($id, $decision, $comment, $user) {
            $p = PengajuanDana::lockForUpdate()->find($id);
            if (! $p) return response()->json(['success' => false, 'message' => 'Pengajuan dana tidak ditemukan'], 404);

            $current = is_string($p->status) ? strtolower(trim($p->status)) : '';
            if (in_array($current, ['approved','rejected'])) {
                return response()->json(['success' => false, 'message' => 'Pengajuan sudah final'], 409);
            }

            // record approval row
            $approval = PengajuanDanaApproval::create([
                'id' => (string) Str::uuid(),
                'pengajuan_dana_id' => $p->id,
                'approver_id' => $user->id,
                'decision' => $decision,
                'comment' => $comment,
            ]);

            // update pengajuan status and updated_by
            $p->status = $decision;
            $p->updated_by = $user->id;
            $p->save();

            // if approved and program/operasional/gaji_karyawan submission, create disbursements
            if (is_string($decision) && strtolower($decision) === 'approved') {
                if (in_array($p->submission_type, ['program','operasional','gaji karyawan']) && $p->program_id && $p->used_at) {
                    $stype = $p->submission_type;
                    if ($stype === 'operasional') $shareKey = 'ops_2';
                    elseif ($stype === 'gaji karyawan') $shareKey = 'ops_1';
                    else $shareKey = 'program';
                    // allocate using default lookback = 1 (previous month)
                    $this->allocateDisbursements($p->id, $p->program_id, $p->used_at, $p->amount, $user->id, $shareKey, 1);
                }
            } else {
                // if rejected, ensure there are no disbursements
                PengajuanDanaDisbursement::where('pengajuan_dana_id', $p->id)->delete();
            }

            return response()->json(['success' => true, 'message' => 'Decision recorded', 'data' => ['approval' => $approval, 'pengajuan' => $p->fresh()]]);
        });
    }
}
