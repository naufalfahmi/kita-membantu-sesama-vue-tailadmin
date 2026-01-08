<?php

namespace App\Http\Controllers;

use App\Models\Mitra;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TransaksiController extends Controller
{
    /**
     * Allowed status values.
     */
    protected array $allowedStatuses = ['pending', 'verified', 'cancelled'];

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Transaksi::with([
            'kantorCabang:id,nama',
            // include donatur.pic so we can show PIC (pic_user) in transactions
            'donatur:id,nama,pic',
            'donatur.picUser:id,name',
            'program:id,nama_program',
            'mitra:id,nama',
            'fundraiser:id,name',
        ]);

        // Filter berdasarkan user yang login (kecuali admin/superadmin)
        $user = auth()->user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
        }

        [$mitraContext, $isMitraUser] = $this->resolveMitraContext($user);

        $isAdmin = $this->userIsAdmin($user);
        $allowed = null;

        if ($isMitraUser) {
            if ($mitraContext) {
                $query->where('transaksis.mitra_id', $mitraContext->id);
            } else {
                $query->whereRaw('0 = 1');
            }
        } elseif (! $isAdmin) {
            // Restrict by kantor cabang assignments (pivot `kantor_cabang_user`) when available.
            try {
                $assignedBranchIds = $user->kantorCabangs()->pluck('kantor_cabang.id')->toArray();
            } catch (\Exception $e) {
                $assignedBranchIds = [];
            }

            // Fallback to primary branch if pivot empty
            if (empty($assignedBranchIds) && $user->kantor_cabang_id) {
                $assignedBranchIds = [$user->kantor_cabang_id];
            }

            // Detect Admin Cabang and Direktur Fundrising roles via DB-backed role names
            $isAdminCabang = false;
            $isDirekturFundrising = false;
            $userRoleNames = [];
            try {
                $userRoleNames = $user->roles()->pluck('name')->map(fn ($n) => strtolower(trim($n)))->toArray();
                $isAdminCabang = in_array('admin cabang', $userRoleNames, true);
                // Accept common role name variants for fundraising director
                $isDirekturFundrising = in_array('direktur fundrising', $userRoleNames, true)
                    || in_array('direktur fundraising', $userRoleNames, true)
                    || in_array('director fundrising', $userRoleNames, true)
                    || in_array('director fundraising', $userRoleNames, true);
            } catch (\Throwable $e) {
                // ignore
            }

            // If Admin Cabang with assigned branches, allow viewing all transaksis
            // in those branches and skip descendant-created_by filtering.
            if (! empty($assignedBranchIds) && $isAdminCabang) {
                $query->whereIn('transaksis.kantor_cabang_id', $assignedBranchIds);
            } else {
                // Direktur Fundrising should only see their own and their subordinates' transaksis.
                if ($isDirekturFundrising) {
                    $allowed = User::descendantIdsOf($user->id);
                    $query->where(function ($q) use ($allowed) {
                        $q->whereIn('transaksis.created_by', $allowed)
                            ->orWhereHas('donatur', fn ($q2) => $q2->whereIn('pic', $allowed));
                    });
                } else {
                    if (! empty($assignedBranchIds)) {
                        $query->whereIn('transaksis.kantor_cabang_id', $assignedBranchIds);
                    } elseif ($user->kantor_cabang_id) {
                        $query->where('transaksis.kantor_cabang_id', $user->kantor_cabang_id);
                    }

                    $allowed = User::descendantIdsOf($user->id);

                    // Allow users to see transaksis they created, their subordinates created,
                    // or any transaksi where the related donatur's PIC is the user or a subordinate.
                    $query->where(function ($q) use ($allowed, $user) {
                        // Qualify column to avoid ambiguity when query uses joins
                        $q->whereIn('transaksis.created_by', $allowed)
                            ->orWhereHas('donatur', fn ($q2) => $q2->whereIn('pic', $allowed));
                    });
                }
            }

            // Log role/assignment for debugging
            try {
                \Log::debug('TransaksiController@index visibility', [
                    'user_id' => $user->id ?? null,
                    'is_admin' => $isAdmin,
                    'is_admin_cabang' => $isAdminCabang,
                    'assigned_branch_ids' => $assignedBranchIds,
                    'role_names' => $userRoleNames,
                ]);
            } catch (\Throwable $e) {
                // ignore
            }
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                    ->orWhere('keterangan', 'like', "%{$search}%")
                    ->orWhereHas('donatur', fn ($q) => $q->where('nama', 'like', "%{$search}%"))
                    ->orWhereHas('program', fn ($q) => $q->where('nama_program', 'like', "%{$search}%"))
                    ->orWhereHas('fundraiser', fn ($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('donatur')) {
            $query->whereHas('donatur', fn ($q) => $q->where('nama', 'like', "%{$request->donatur}%"));
        }

        if ($request->filled('donatur_id')) {
            $query->where('donatur_id', $request->donatur_id);
        }

        if ($request->filled('fundraiser')) {
            $searchName = trim((string) $request->fundraiser);
            if (! $isAdmin && is_array($allowed)) {
                $query->whereHas('fundraiser', function ($q) use ($searchName, $allowed) {
                    $q->where('name', 'like', "%{$searchName}%")->whereIn('id', $allowed);
                });
            } else {
                $query->whereHas('fundraiser', fn ($q) => $q->where('name', 'like', "%{$searchName}%"));
            }
        }

        if ($request->filled('fundraiser_id')) {
            $fid = $request->fundraiser_id;
            // If non-admin restrict the selected fundraiser to user's subtree
            if (! $isAdmin && is_array($allowed) && ! in_array((string) $fid, array_map('strval', $allowed), true)) {
                // Force empty result set
                $query->whereRaw('0 = 1');
            } else {
                // Allow filtering by the transaction's fundraiser (creator) OR by the donatur's PIC
                $query->where(function ($q) use ($fid) {
                    $q->where('fundraiser_id', $fid)
                        ->orWhereHas('donatur', fn ($q2) => $q2->where('pic', $fid));
                });
            }
        }

        if ($request->filled('program')) {
            $query->whereHas('program', fn ($q) => $q->where('nama_program', 'like', "%{$request->program}%"));
        }

        if ($request->filled('program_id')) {
            $query->where('program_id', $request->program_id);
        }

        if (! $isMitraUser && $request->filled('mitra_id')) {
            $query->where('mitra_id', $request->mitra_id);
        }

        // Support both single-date filter (`tanggal=YYYY-MM-DD`) and
        // a date range via `tanggal_from`/`tanggal_to` or a range passed
        // in the `tanggal` parameter like `YYYY-MM-DD to YYYY-MM-DD`.
        if ($request->filled('tanggal_from') && $request->filled('tanggal_to')) {
            $from = $request->input('tanggal_from');
            $to = $request->input('tanggal_to');
            // Use date-only comparison to be DB-agnostic (SQLite stores timestamps as
            // ISO strings which can break string BETWEEN comparisons). Compare using
            // WHERE DATE(tanggal_transaksi) >= from AND DATE(tanggal_transaksi) <= to.
            $query->whereDate('tanggal_transaksi', '>=', $from)
                ->whereDate('tanggal_transaksi', '<=', $to);
        } elseif ($request->filled('tanggal')) {
            $tanggal = trim((string) $request->input('tanggal'));

            // Accept common range separators: " to " or " - "
            $parts = preg_split('/\s+(?:to|-)\s+/i', $tanggal);
            if (is_array($parts) && count($parts) === 2) {
                [$from, $to] = array_map('trim', $parts);
                // Basic YYYY-MM-DD validation
                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $from) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $to)) {
                    $query->whereDate('tanggal_transaksi', '>=', $from)
                        ->whereDate('tanggal_transaksi', '<=', $to);
                } else {
                    // fallback to equality if parsing fails
                    $query->whereDate('tanggal_transaksi', $tanggal);
                }
            } else {
                $query->whereDate('tanggal_transaksi', $tanggal);
            }
        }

        if ($request->filled('status')) {
            $status = $request->input('status');
            if (in_array($status, $this->allowedStatuses, true)) {
                $query->where('status', $status);
            }
        }

        if ($request->filled('kantor_cabang_id')) {
            $query->where('kantor_cabang_id', $request->kantor_cabang_id);
        }

        // Server-side sorting support via `sort_by` and `sort_dir` params
        $sortBy = $request->input('sort_by') ? (string) $request->input('sort_by') : null;
        $sortDir = strtolower($request->input('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        $joined = false;
        if ($sortBy) {
            // Map frontend field names to DB columns
            $sortMapping = [
                'kode' => 'transaksis.kode',
                'nominal' => 'transaksis.nominal',
                'mitra' => ['table' => 'mitras', 'local' => 'mitras.id', 'foreign' => 'transaksis.mitra_id', 'column' => 'mitras.nama'],
                'tanggal_transaksi' => 'transaksis.tanggal_transaksi',
                'tanggal_dibuat' => 'transaksis.created_at',
                'donatur' => ['table' => 'donaturs', 'local' => 'donaturs.id', 'foreign' => 'transaksis.donatur_id', 'column' => 'donaturs.nama'],
                'program' => ['table' => 'program', 'local' => 'program.id', 'foreign' => 'transaksis.program_id', 'column' => 'program.nama_program'],
                'kantor_cabang' => ['table' => 'kantor_cabang', 'local' => 'kantor_cabang.id', 'foreign' => 'transaksis.kantor_cabang_id', 'column' => 'kantor_cabang.nama'],
                'fundraiser' => ['table' => 'users', 'alias' => 'fundraiser', 'local' => 'fundraiser.id', 'foreign' => 'transaksis.fundraiser_id', 'column' => 'fundraiser.name'],
            ];

            if (isset($sortMapping[$sortBy])) {
                $map = $sortMapping[$sortBy];
                if (is_array($map)) {
                    // join
                    $table = $map['table'];
                    $alias = $map['alias'] ?? $table;
                    $local = $map['local'];
                    $foreign = $map['foreign'];
                    $column = $map['column'];

                    // Avoid joining multiple times
                    $query->leftJoin("{$table} as {$alias}", $local, '=', $foreign);
                    $query->select('transaksis.*');
                    $query->orderBy($column, $sortDir);
                    $joined = true;
                } else {
                    $query->orderBy($map, $sortDir);
                }
            }
        }

        if (! $sortBy) {
            $query->orderByDesc('tanggal_transaksi')->orderByDesc('created_at');
        }

        // Compute total nominal for the current filtered query (unpaginated)
        try {
            $sumQuery = (clone $query);
            $totalNominal = $sumQuery->sum('nominal') ?: 0;
        } catch (\Throwable $e) {
            $totalNominal = 0;
        }

        $totalNominalFormatted = number_format($totalNominal, 0, ',', '.');

        $transaksis = $query->paginate($request->integer('per_page', 20));

        $transaksis->getCollection()->transform(function (Transaksi $transaksi) {
            return $this->transformTransaksi($transaksi);
        });

        return response()->json([
            'success' => true,
            'data' => $transaksis->items(),
            'pagination' => [
                'current_page' => $transaksis->currentPage(),
                'last_page' => $transaksis->lastPage(),
                'per_page' => $transaksis->perPage(),
                'total' => $transaksis->total(),
            ],
            'total_nominal' => $totalNominal,
            'total_nominal_formatted' => $totalNominalFormatted,
        ]);
    }

    /**
     * Export transaksi by current filters as CSV (program-focused export).
     */
    public function exportProgram(Request $request)
    {
        $query = Transaksi::with([
            'donatur:id,nama,pic',
            'donatur.picUser:id,name',
            'program:id,nama_program',
            'program.shares.type',
        ]);

        $user = auth()->user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
        }

        [$mitraContext, $isMitraUser] = $this->resolveMitraContext($user);
        $isAdmin = $this->userIsAdmin($user);
        if ($isMitraUser) {
            if ($mitraContext) {
                $query->where('transaksis.mitra_id', $mitraContext->id);
            } else {
                $query->whereRaw('0 = 1');
            }
        } elseif (! $isAdmin) {
            try {
                $assignedBranchIds = $user->kantorCabangs()->pluck('kantor_cabang.id')->toArray();
            } catch (\Exception $e) {
                $assignedBranchIds = [];
            }

            if (empty($assignedBranchIds) && $user->kantor_cabang_id) {
                $assignedBranchIds = [$user->kantor_cabang_id];
            }

            $isAdminCabang = false;
            try {
                $userRoleNames = $user->roles()->pluck('name')->map(fn ($n) => strtolower(trim($n)))->toArray();
                $isAdminCabang = in_array('admin cabang', $userRoleNames, true);
            } catch (\Throwable $e) {
                $isAdminCabang = false;
            }

            if (! empty($assignedBranchIds) && $isAdminCabang) {
                $query->whereIn('transaksis.kantor_cabang_id', $assignedBranchIds);
            } else {
                if (! empty($assignedBranchIds)) {
                    $query->whereIn('transaksis.kantor_cabang_id', $assignedBranchIds);
                } elseif ($user->kantor_cabang_id) {
                    $query->where('transaksis.kantor_cabang_id', $user->kantor_cabang_id);
                }

                $allowed = User::descendantIdsOf($user->id);

                $query->where(function ($q) use ($allowed, $user) {
                    $q->whereIn('transaksis.created_by', $allowed)
                        ->orWhereHas('donatur', fn ($q2) => $q2->whereIn('pic', $allowed));
                });
            }
        }

        // Apply same filters as index
        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                    ->orWhere('keterangan', 'like', "%{$search}%")
                    ->orWhereHas('donatur', fn ($q) => $q->where('nama', 'like', "%{$search}%"))
                    ->orWhereHas('program', fn ($q) => $q->where('nama_program', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('donatur_id')) {
            $query->where('donatur_id', $request->donatur_id);
        }

        if ($request->filled('program_id')) {
            $query->where('program_id', $request->program_id);
        }

        if (! $isMitraUser && $request->filled('mitra_id')) {
            $query->where('mitra_id', $request->mitra_id);
        }

        if ($request->filled('tanggal_from') && $request->filled('tanggal_to')) {
            $from = $request->input('tanggal_from');
            $to = $request->input('tanggal_to');
            $query->whereDate('tanggal_transaksi', '>=', $from)
                ->whereDate('tanggal_transaksi', '<=', $to);
        } elseif ($request->filled('tanggal')) {
            $tanggal = trim((string) $request->input('tanggal'));
            $parts = preg_split('/\s+(?:to|-)\s+/i', $tanggal);
            if (is_array($parts) && count($parts) === 2) {
                [$from, $to] = array_map('trim', $parts);
                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $from) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $to)) {
                    $query->whereDate('tanggal_transaksi', '>=', $from)
                        ->whereDate('tanggal_transaksi', '<=', $to);
                } else {
                    $query->whereDate('tanggal_transaksi', $tanggal);
                }
            } else {
                $query->whereDate('tanggal_transaksi', $tanggal);
            }
        }

        if ($request->filled('status')) {
            $status = $request->input('status');
            if (in_array($status, $this->allowedStatuses, true)) {
                $query->where('status', $status);
            }
        }

        if ($request->filled('kantor_cabang_id')) {
            $query->where('kantor_cabang_id', $request->kantor_cabang_id);
        }

        $transaksis = $query->orderByDesc('tanggal_transaksi')->orderByDesc('created_at')->get();

        // Build CSV
        // We'll pivot program shares to columns: for each unique share type across the result set,
        // create two columns: "{TypeName} (%)" and "{TypeName} (Rp)" so data goes sideways.

        // First, collect unique share type identifiers and display names in encountered order.
        $typeOrder = [];
        $typeMap = []; // key => display name
        foreach ($transaksis as $t) {
            if (! ($t->program && $t->program->shares)) continue;
            foreach ($t->program->shares as $share) {
                $typeKey = null;
                // prefer program_share_type_id
                if (!empty($share->program_share_type_id)) {
                    $typeKey = (string) $share->program_share_type_id;
                } elseif (is_string($share->type) && $share->type !== '') {
                    $typeKey = 'key:' . $share->type;
                } elseif (is_object($share->type) && isset($share->type->id)) {
                    $typeKey = (string) $share->type->id;
                }

                if ($typeKey === null) continue;

                if (! isset($typeMap[$typeKey])) {
                    // determine display name
                    $display = null;
                    if (!empty($share->program_share_type_id)) {
                        try {
                            $pst = \App\Models\ProgramShareType::find($share->program_share_type_id);
                            if ($pst) $display = $pst->name;
                        } catch (\Throwable $e) {
                        }
                    }
                    if ($display === null) {
                        if (is_object($share->type) && isset($share->type->name)) {
                            $display = $share->type->name;
                        } elseif (is_string($share->type)) {
                            // try match by key/name
                            try {
                                $pst = \App\Models\ProgramShareType::where('key', $share->type)->orWhere('name', $share->type)->first();
                                if ($pst) $display = $pst->name; else $display = $share->type;
                            } catch (\Throwable $e) {
                                $display = $share->type;
                            }
                        }
                    }

                    $typeMap[$typeKey] = $display ?? 'Share';
                    $typeOrder[] = $typeKey;
                }
            }
        }

        // Build headers: main fields + for each type two columns
        $headers = ['Donatur', 'Fundraiser', 'Program', 'Nominal', 'Tanggal Transaksi'];
        foreach ($typeOrder as $tk) {
            $label = $typeMap[$tk] ?? 'Share';
            $headers[] = $label . ' (%)';
            $headers[] = $label . ' (Rp)';
        }

        $callback = function () use ($transaksis, $headers, $typeOrder, $typeMap) {
            $out = fopen('php://output', 'w');
            // Use semicolon delimiter as frontend expects
            fputs($out, implode(';', $headers) . "\n");

            foreach ($transaksis as $t) {
                $donatur = $t->donatur ? $t->donatur->nama : '';
                $fundraiser = null;
                if ($t->donatur && $t->donatur->picUser) {
                    $fundraiser = $t->donatur->picUser->name;
                }
                $program = $t->program ? $t->program->nama_program : '';
                $nominal = $t->nominal;
                $tanggal = $t->tanggal_transaksi ? $t->tanggal_transaksi->toDateString() : '';

                // Build a row aligned with headers: main fields then one (%) and one (Rp) column per typeOrder
                $row = [$donatur, $fundraiser, $program, $nominal, $tanggal];

                // prepare a quick lookup of this transaction's shares by resolved typeKey
                $shareLookup = [];
                if ($t->program && $t->program->shares) {
                    foreach ($t->program->shares as $share) {
                        $typeKey = null;
                        if (!empty($share->program_share_type_id)) {
                            $typeKey = (string) $share->program_share_type_id;
                        } elseif (is_string($share->type) && $share->type !== '') {
                            $typeKey = 'key:' . $share->type;
                        } elseif (is_object($share->type) && isset($share->type->id)) {
                            $typeKey = (string) $share->type->id;
                        }
                        if ($typeKey === null) continue;
                        $shareLookup[$typeKey] = $share;
                    }
                }

                foreach ($typeOrder as $tk) {
                    if (isset($shareLookup[$tk])) {
                        $s = $shareLookup[$tk];
                        $value = $s->value;
                        $amount = (int) round(((float) $value / 100.0) * (float) $nominal);
                        $percentLabel = (floor($value) == $value) ? sprintf('%d%%', $value) : rtrim(rtrim(number_format($value, 2, '.', ''), '0'), '.').'%';
                        $row[] = $percentLabel;
                        $row[] = (string) $amount;
                    } else {
                        $row[] = '';
                        $row[] = '';
                    }
                }

                $escaped = array_map(function ($v) {
                    $s = is_null($v) ? '' : (string) $v;
                    $s = str_replace('"', '""', $s);
                    if (strpos($s, ';') !== false || strpos($s, "\n") !== false || strpos($s, '"') !== false) {
                        return '"' . $s . '"';
                    }
                    return $s;
                }, $row);

                fputs($out, implode(';', $escaped) . "\n");
            }

            fclose($out);
        };

        $filename = 'transaksi_export_program_' . date('Ymd_His') . '.csv';

        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kantor_cabang_id' => 'required|uuid|exists:kantor_cabang,id',
            'donatur_id' => 'required|uuid|exists:donaturs,id',
            'program_id' => 'required|uuid|exists:program,id',
            'mitra_id' => 'nullable|uuid|exists:mitras,id',
            'fundraiser_id' => 'nullable|exists:users,id',
            'nominal' => 'required|numeric|min:0',
            'tanggal_transaksi' => 'required|date',
            'keterangan' => 'nullable|string|max:1000',
            'status' => ['nullable', 'string', Rule::in($this->allowedStatuses)],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $data = $this->sanitizePayload($validator->validated());
            $data['kode'] = $this->generateKode();
            $data['status'] = $data['status'] ?? 'verified';
            $data['created_by'] = auth()->id();
            
            // Auto-set fundraiser_id to current user if not provided
            if (empty($data['fundraiser_id'])) {
                $data['fundraiser_id'] = auth()->id();
            }

            $transaksi = Transaksi::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil ditambahkan',
                'data' => $this->transformTransaksi($transaksi->fresh([
                    'kantorCabang:id,nama',
                    'donatur:id,nama',
                    'program:id,nama_program',
                    'mitra:id,nama',
                    'fundraiser:id,name',
                ])),
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan transaksi',
                'error' => config('app.debug') ? $th->getMessage() : 'Terjadi kesalahan',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $query = Transaksi::with([
            'kantorCabang:id,nama',
            'donatur:id,nama',
            'program:id,nama_program',
            'mitra:id,nama',
            'fundraiser:id,name',
        ]);

        // Filter berdasarkan user yang login (kecuali admin/superadmin)
        $user = auth()->user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
        }

        [$mitraContext, $isMitraUser] = $this->resolveMitraContext($user);
        $isAdmin = $this->userIsAdmin($user);
        if ($isMitraUser) {
            if ($mitraContext) {
                $query->where('transaksis.mitra_id', $mitraContext->id);
            } else {
                $query->whereRaw('0 = 1');
            }
        } elseif (! $isAdmin) {
            try {
                $assignedBranchIds = $user->kantorCabangs()->pluck('kantor_cabang.id')->toArray();
            } catch (\Exception $e) {
                $assignedBranchIds = [];
            }

            if (empty($assignedBranchIds) && $user->kantor_cabang_id) {
                $assignedBranchIds = [$user->kantor_cabang_id];
            }

            $isAdminCabang = false;
            $userRoleNames = [];
            try {
                $userRoleNames = $user->roles()->pluck('name')->map(fn ($n) => strtolower(trim($n)))->toArray();
                $isAdminCabang = in_array('admin cabang', $userRoleNames, true);
            } catch (\Throwable $e) {
                // ignore
            }

            if (! empty($assignedBranchIds) && $isAdminCabang) {
                // Admin Cabang: allow viewing by branch only
                $query->whereIn('transaksis.kantor_cabang_id', $assignedBranchIds);
            } else {
                $allowed = User::descendantIdsOf($user->id);
                $query->where(function ($q) use ($allowed, $user) {
                    // Qualify column to avoid ambiguity when query uses joins
                    $q->whereIn('transaksis.created_by', $allowed)
                        ->orWhereHas('donatur', fn ($q2) => $q2->whereIn('pic', $allowed));
                });
            }
        }

        $transaksi = $query->find($id);

        if (! $transaksi) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $this->transformTransaksi($transaksi),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $query = Transaksi::query();

        // Filter berdasarkan user yang login (kecuali admin/superadmin)
        $user = auth()->user();
        if (! $this->userIsAdmin($user)) {
            $query->where('created_by', $user->id);
        }

        $transaksi = $query->find($id);

        if (! $transaksi) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'kantor_cabang_id' => 'required|uuid|exists:kantor_cabang,id',
            'donatur_id' => 'required|uuid|exists:donaturs,id',
            'program_id' => 'required|uuid|exists:program,id',
            'mitra_id' => 'nullable|uuid|exists:mitras,id',
            'fundraiser_id' => 'nullable|exists:users,id',
            'nominal' => 'required|numeric|min:0',
            'tanggal_transaksi' => 'required|date',
            'keterangan' => 'nullable|string|max:1000',
            'status' => ['nullable', 'string', Rule::in($this->allowedStatuses)],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $data = $this->sanitizePayload($validator->validated());
            $data['updated_by'] = auth()->id();
            
            // Auto-set fundraiser_id to current user if not provided and transaksi doesn't have one
            if (empty($data['fundraiser_id']) && empty($transaksi->fundraiser_id)) {
                $data['fundraiser_id'] = auth()->id();
            }

            $transaksi->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil diperbarui',
                'data' => $this->transformTransaksi($transaksi->fresh([
                    'kantorCabang:id,nama',
                    'donatur:id,nama',
                    'program:id,nama_program',
                    'mitra:id,nama',
                    'fundraiser:id,name',
                ])),
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui transaksi',
                'error' => config('app.debug') ? $th->getMessage() : 'Terjadi kesalahan',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $query = Transaksi::query();

        // Filter berdasarkan user yang login (kecuali admin/superadmin)
        $user = auth()->user();
        if (! $this->userIsAdmin($user)) {
            $query->where('created_by', $user->id);
        }

        $transaksi = $query->find($id);

        if (! $transaksi) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan',
            ], 404);
        }

        try {
            $transaksi->deleted_by = auth()->id();
            $transaksi->save();
            $transaksi->delete();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dihapus',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus transaksi',
                'error' => config('app.debug') ? $th->getMessage() : 'Terjadi kesalahan',
            ], 500);
        }
    }

    /**
     * Generate the next sequential kode.
     */
    protected function generateKode(): string
    {
        $prefix = 'TRX';
        $date = now()->format('Ymd');

        $lastTransaksi = Transaksi::withTrashed()
            ->where('kode', 'like', "{$prefix}{$date}%")
            ->orderByRaw('CAST(SUBSTRING(kode, ' . (strlen($prefix) + strlen($date) + 1) . ') AS UNSIGNED) DESC')
            ->first();

        if ($lastTransaksi && preg_match("/{$prefix}{$date}(\d+)/", $lastTransaksi->kode, $matches)) {
            $nextNumber = ((int) $matches[1]) + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . $date . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Resolve the mitra record linked to the current user (if any).
     *
     * @return array{0: Mitra|null, 1: bool} Tuple of (mitra record, is mitra user)
     */
    protected function resolveMitraContext($user): array
    {
        if (! $user) {
            return [null, false];
        }

        $tipeUser = strtolower((string) ($user->tipe_user ?? ''));
        $isMitraUser = $tipeUser === 'mitra';

        if (! $isMitraUser && method_exists($user, 'hasRole')) {
            try {
                $isMitraUser = $user->hasRole('mitra');
            } catch (\Throwable $e) {
                $isMitraUser = false;
            }
        }

        if (! $isMitraUser) {
            return [null, false];
        }

        $hasIdentifier = ! empty($user->id) || ! empty($user->email);
        if (! $hasIdentifier) {
            return [null, true];
        }

        $mitraQuery = Mitra::query();
        $mitraQuery->where(function ($q) use ($user) {
            $added = false;

            if (! empty($user->id)) {
                $q->where('user_id', $user->id);
                $added = true;
            }

            if (! empty($user->email)) {
                if ($added) {
                    $q->orWhere('email', $user->email);
                } else {
                    $q->where('email', $user->email);
                }
            }
        });

        return [$mitraQuery->first(), true];
    }

    /**
     * Transform transaksi instance for API responses.
     */
    protected function transformTransaksi(Transaksi $transaksi): array
    {
        return [
            'id' => $transaksi->id,
            'kode' => $transaksi->kode,
            'nominal' => (float) $transaksi->nominal,
            'nominal_formatted' => 'Rp ' . number_format((float) $transaksi->nominal, 0, ',', '.'),
            'tanggal_transaksi' => optional($transaksi->tanggal_transaksi)->toDateString(),
            'keterangan' => $transaksi->keterangan,
            'status' => $transaksi->status,
            'kantor_cabang_id' => $transaksi->kantor_cabang_id,
            'kantor_cabang' => $transaksi->kantorCabang ? [
                'id' => $transaksi->kantorCabang->id,
                'nama' => $transaksi->kantorCabang->nama,
            ] : null,
            'donatur_id' => $transaksi->donatur_id,
            'donatur' => $transaksi->donatur ? [
                'id' => $transaksi->donatur->id,
                'nama' => $transaksi->donatur->nama,
            ] : null,
            // PIC info for the donatur (if available)
            'donatur_pic' => $transaksi->donatur && $transaksi->donatur->picUser ? [
                'id' => $transaksi->donatur->picUser->id,
                'nama' => $transaksi->donatur->picUser->name,
            ] : null,
            'program_id' => $transaksi->program_id,
            'program' => $transaksi->program ? [
                'id' => $transaksi->program->id,
                'nama' => $transaksi->program->nama_program,
            ] : null,
            'mitra_id' => $transaksi->mitra_id,
            'mitra' => $transaksi->mitra ? [
                'id' => $transaksi->mitra->id,
                'nama' => $transaksi->mitra->nama,
            ] : null,
            'fundraiser_id' => $transaksi->fundraiser_id,
            'fundraiser' => $transaksi->fundraiser ? [
                'id' => $transaksi->fundraiser->id,
                'nama' => $transaksi->fundraiser->name,
            ] : null,
            'created_at' => optional($transaksi->created_at)->toIso8601String(),
            'updated_at' => optional($transaksi->updated_at)->toIso8601String(),
        ];
    }

    /**
     * Sanitize payload before persistence.
     */
    protected function sanitizePayload(array $data): array
    {
        if (array_key_exists('keterangan', $data) && is_string($data['keterangan'])) {
            $data['keterangan'] = trim($data['keterangan']);
            if ($data['keterangan'] === '') {
                $data['keterangan'] = null;
            }
        }

        $nullableKeys = ['fundraiser_id', 'keterangan', 'status', 'mitra_id'];

        foreach ($nullableKeys as $key) {
            if (array_key_exists($key, $data)) {
                $value = $data[$key];
                if (is_string($value)) {
                    $value = trim($value);
                }

                $data[$key] = ($value === '' || $value === null) ? null : $value;
            }
        }

        if (array_key_exists('status', $data) && empty($data['status'])) {
            unset($data['status']);
        }

        return $data;
    }
}
