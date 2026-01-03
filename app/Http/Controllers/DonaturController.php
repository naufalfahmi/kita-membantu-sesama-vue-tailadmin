<?php

namespace App\Http\Controllers;

use App\Models\Donatur;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DonaturController extends Controller
{
    /**
     * Allowed tipe donatur values.
     */
    protected array $allowedDonorTypes = ['komunitas', 'kotak_infaq', 'retail'];

    /**
     * Allowed status values.
     */
    protected array $allowedStatuses = ['aktif', 'tidak_aktif', 'pending'];

    /**
     * Generate next kode donatur
     */
    private function generateKode(): string
    {
        // Get all kodes that match pattern DNT### (case insensitive)
        $allKodes = Donatur::withTrashed()->pluck('kode')->toArray();

        $maxNumber = 0;
        foreach ($allKodes as $kode) {
            if (preg_match('/^DNT(\d+)$/i', $kode, $matches)) {
                $number = (int) $matches[1];
                if ($number > $maxNumber) {
                    $maxNumber = $number;
                }
            }
        }

        // Increment and format
        $nextNumber = $maxNumber + 1;

        return 'DNT'.str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Get next kode for new donatur
     */
    public function getNextKode()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'kode' => $this->generateKode(),
            ],
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Donatur::with(['kantorCabang:id,nama', 'picUser:id,name']);

        // Filter berdasarkan user yang login (kecuali admin/superadmin)
        $user = auth()->user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
        }

        $isAdmin = $this->userIsAdmin($user);

            // Track whether we've applied an explicit visibility filter
            $skipVisibility = false;

            // Debug logging to help trace visibility/filtering issues
            if ($request->boolean('only_assigned') || $request->filled('kantor_cabang_id')) {
                try {
                    \Log::debug('DonaturController@index called', [
                        'user_id' => $user->id ?? null,
                        'is_admin' => $isAdmin,
                        'only_assigned' => $request->boolean('only_assigned'),
                        'requested_kantor_cabang_id' => $request->input('kantor_cabang_id'),
                    ]);
                } catch (\Throwable $e) {
                    // ignore logging errors
                }
            }

        // If caller requested only assigned kantor cabang, restrict to user's
        // assigned branches (via pivot) or their primary kantor_cabang_id.
        if ($request->boolean('only_assigned') && auth()->check()) {
            try {
                $assignedIds = $user->kantorCabangs()->pluck('id')->toArray();
            } catch (\Exception $e) {
                $assignedIds = [];
            }

            // If the pivot assignment is empty but the user has a primary
            // `kantor_cabang_id`, treat that as an assigned branch as well.
            if (empty($assignedIds) && $user->kantor_cabang_id) {
                $assignedIds = [$user->kantor_cabang_id];
            }

            if (! empty($assignedIds)) {
                $query->whereIn('donaturs.kantor_cabang_id', $assignedIds);
                $skipVisibility = true;
            } elseif ($user->kantor_cabang_id) {
                $query->where('donaturs.kantor_cabang_id', $user->kantor_cabang_id);
                $skipVisibility = true;
            } else {
                // No assigned branches and no primary branch -> return empty
                $query->whereRaw('1 = 0');
                $skipVisibility = true;
            }
        }

        // If caller requested explicit kantor_cabang_id, allow filtering by that
        // branch only when caller is admin or assigned to that branch (or
        // when their primary kantor_cabang_id matches). When this param is
        // present and allowed, skip the usual per-user visibility widening
        // logic to avoid contradictory filters.
        if ($request->filled('kantor_cabang_id')) {
            $requestedBranch = $request->input('kantor_cabang_id');
            if ($isAdmin) {
                $query->where('kantor_cabang_id', $requestedBranch);
                $skipVisibility = true;
            } else {
                try {
                    $assignedIds = $user->kantorCabangs()->pluck('id')->toArray();
                } catch (\Exception $e) {
                    $assignedIds = [];
                }

                if (empty($assignedIds) && $user->kantor_cabang_id) {
                    $assignedIds = [$user->kantor_cabang_id];
                }

                // Allow if user is assigned to the branch or their primary matches
                if (in_array($requestedBranch, $assignedIds) || ($user->kantor_cabang_id && $user->kantor_cabang_id == $requestedBranch)) {
                    $query->where('kantor_cabang_id', $requestedBranch);
                    $skipVisibility = true;
                } else {
                    // Not allowed to view this branch -> return empty
                    $query->whereRaw('1 = 0');
                    $skipVisibility = true;
                }
            }
        }

        if (! $isAdmin && ! ($skipVisibility)) {
            // Always consider explicit kantor cabang assignments (pivot).
            // Users who are assigned to branches should see donaturs in
            // those branches regardless of leader/subordinate status.
            try {
                $assignedIds = $user->kantorCabangs()->pluck('id')->toArray();
            } catch (\Exception $e) {
                $assignedIds = [];
            }

            if (empty($assignedIds) && $user->kantor_cabang_id) {
                $assignedIds = [$user->kantor_cabang_id];
            }
            $hasSubordinates = $user->subordinates()->exists();

            // If the user's leader_id is NULL and they have NO subordinates,
            // restrict them to only see donaturs where they are PIC. If they
            // DO have subordinates (e.g., Director), allow visibility over
            // descendants per usual rules. Additionally, users with the
            // "Admin Cabang" role should be able to view all donaturs in
            // their assigned branches.
            // Determine whether user has 'Admin Cabang' role by querying
            // role names from the DB (robust against different shapes).
            $isAdminCabang = false;
            $userRoleNames = [];
            try {
                $userRoleNames = $user->roles()->pluck('name')->map(function ($n) {
                    return strtolower(trim($n));
                })->toArray();
                $isAdminCabang = in_array('admin cabang', $userRoleNames, true);
            } catch (\Throwable $e) {
                // fall back to any in-memory roles property
                $rolesSource = $user->roles ?? ($user->role ? [$user->role] : []);
                if (is_array($rolesSource)) {
                    foreach ($rolesSource as $r) {
                        $name = is_string($r) ? $r : ($r->name ?? null);
                        if ($name && strtolower(trim($name)) === 'admin cabang') {
                            $isAdminCabang = true;
                            break;
                        }
                    }
                }
            }

            // Log user role names and primary kantor_cabang for debugging
            try {
                \Log::debug('DonaturController@index user roles', [
                    'user_id' => $user->id ?? null,
                    'role_names' => $userRoleNames,
                    'primary_kantor_cabang_id' => $user->kantor_cabang_id ?? null,
                ]);
            } catch (\Throwable $e) {
                // ignore logging errors
            }

            // Detect if user is Director of Fundraising (role name variants)
            $isDirectorFundrising = false;
            $directorNames = ['direktur fundrising', 'direktur fundraising'];
            foreach ($userRoleNames as $rn) {
                if (in_array($rn, $directorNames, true)) {
                    $isDirectorFundrising = true;
                    break;
                }
            }

            if (! empty($assignedIds) && $isAdminCabang) {
                // Admin Cabang: show all donaturs in the assigned branches
                $query->whereIn('donaturs.kantor_cabang_id', $assignedIds);
            } elseif (is_null($user->leader_id) && ! $hasSubordinates) {
                $query->where('pic', $user->id);
            } elseif (! empty($assignedIds)) {
                if ($hasSubordinates) {
                    // Leaders with branch assignments: normally show donaturs in
                    // assigned branches OR donaturs where PIC is within user's descendants.
                    // However, Directors of Fundraising should only see donaturs
                    // where PIC is themselves or a descendant (no full-branch view).
                    $allowed = User::descendantIdsOf($user->id);
                    if ($isDirectorFundrising) {
                        $query->whereIn('donaturs.pic', $allowed);
                    } else {
                        $query->where(function ($q) use ($assignedIds, $allowed) {
                            $q->whereIn('donaturs.kantor_cabang_id', $assignedIds)
                              ->orWhereIn('donaturs.pic', $allowed);
                        });
                    }
                } else {
                    // Non-leaders with branch assignments: show donaturs in
                    // assigned branches OR donaturs where caller is PIC.
                    $query->where(function ($q) use ($assignedIds, $user) {
                        $q->whereIn('donaturs.kantor_cabang_id', $assignedIds)
                          ->orWhere('donaturs.pic', $user->id);
                    });
                }
            } else {
                // Log visibility decision for debugging Admin Cabang issues
                try {
                    \Log::debug('DonaturController@index visibility decision', [
                        'user_id' => $user->id ?? null,
                        'is_admin' => $isAdmin,
                        'is_admin_cabang' => $isAdminCabang ?? false,
                        'has_subordinates' => $hasSubordinates,
                        'assigned_branch_ids' => $assignedIds ?? [],
                        'request_only_assigned' => $request->boolean('only_assigned'),
                        'request_kantor_cabang_id' => $request->input('kantor_cabang_id'),
                        'skip_visibility' => $skipVisibility,
                    ]);
                } catch (\Throwable $e) {
                    // ignore logging errors
                }
                // No explicit branch assignments: fall back to previous rules
                if (! $hasSubordinates) {
                    // No subordinates: restrict to donaturs where caller is PIC only
                    $query->where('pic', $user->id);
                } else {
                    // Leaders without pivot assignments: allow visibility for
                    // descendants' PIC values.
                    $allowed = User::descendantIdsOf($user->id);
                    $query->where(function ($q) use ($allowed) {
                        $q->whereIn('donaturs.pic', $allowed);
                    });
                }
            }
        }

        // General search (nama, email, kode, no_handphone)
        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('kode', 'like', "%{$search}%")
                    ->orWhere('no_handphone', 'like', "%{$search}%");
            });
        }

        // Explicit nama filter (for dedicated Nama filter field)
        if ($request->filled('nama')) {
            $nama = trim((string) $request->input('nama'));
            $query->where('nama', 'like', "%{$nama}%");
        }

        if ($request->filled('status')) {
            $status = $request->input('status');
            if (in_array($status, $this->allowedStatuses, true)) {
                $query->where('status', $status);
            }
        }

        if ($request->filled('kantor_cabang_id')) {
            $query->where('kantor_cabang_id', $request->input('kantor_cabang_id'));
        }

        // Filter by PIC (expects user id)
        if ($request->filled('pic')) {
            $pic = $request->input('pic');
            // If a uuid-like value provided, match directly to pic column
            $query->where('pic', $pic);
        }

        if ($request->filled('jenis_donatur')) {
            $jenis = $request->input('jenis_donatur');
            $jenisArray = is_array($jenis) ? $jenis : explode(',', (string) $jenis);
            $filteredJenis = array_intersect($jenisArray, $this->allowedDonorTypes);

            foreach ($filteredJenis as $value) {
                $query->whereJsonContains('jenis_donatur', $value);
            }
        }

        try {
            \Log::debug('DonaturController@index sql', [
                'sql' => $query->toSql(),
                'bindings' => $query->getBindings(),
            ]);
        } catch (\Throwable $_) {
            // ignore
        }

        $donaturs = $query
            ->orderByDesc('created_at')
            ->paginate($request->integer('per_page', 20));

        $donaturs->getCollection()->transform(function (Donatur $donatur) {
            return $this->transformDonatur($donatur);
        });

        try {
            // Helpful debug information for visibility issues in frontend
            $assignedIdsForLog = [];
            try {
                $assignedIdsForLog = auth()->user() ? auth()->user()->kantorCabangs()->pluck('id')->toArray() : [];
            } catch (\Throwable $_) {
                $assignedIdsForLog = [];
            }
            \Log::debug('DonaturController@index response', [
                'user_id' => $user->id ?? null,
                'is_admin' => $isAdmin,
                'assigned_ids' => $assignedIdsForLog,
                'skip_visibility' => $skipVisibility,
                'result_count' => $donaturs->total(),
                'requested_params' => $request->all(),
            ]);
        } catch (\Throwable $_) {
            // ignore logging errors
        }

        return response()->json([
            'success' => true,
            'data' => $donaturs->items(),
            'pagination' => [
                'current_page' => $donaturs->currentPage(),
                'last_page' => $donaturs->lastPage(),
                'per_page' => $donaturs->perPage(),
                'total' => $donaturs->total(),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode' => 'nullable|string|max:50|unique:donaturs,kode',
            'nama' => 'required|string|max:255',
            'jenis_donatur' => 'required|array|min:1',
            'jenis_donatur.*' => ['string', Rule::in($this->allowedDonorTypes)],
            'pic' => 'nullable|exists:users,id',
            'alamat' => 'nullable|string|max:1000',
            'provinsi' => 'nullable|string|max:255',
            'kota_kab' => 'nullable|string|max:255',
            'kecamatan' => 'nullable|string|max:255',
            'kelurahan' => 'nullable|string|max:255',
            'no_handphone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255|unique:donaturs,email',
            'tanggal_lahir' => 'nullable|date',
            'status' => ['nullable', 'string', Rule::in($this->allowedStatuses)],
            'kantor_cabang_id' => 'nullable|uuid|exists:kantor_cabang,id',
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
            
            // Auto-generate kode if not provided
            if (empty($data['kode'])) {
                $data['kode'] = $this->generateKode();
            }
            
            $data['status'] = $data['status'] ?? 'aktif';
            // Set created_by to PIC so ownership is tied to the fundraiser PIC
            // if PIC is provided; fall back to current user if not.
            $data['created_by'] = $data['pic'] ?? auth()->id();

            $donatur = Donatur::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Donatur berhasil ditambahkan',
                'data' => $this->transformDonatur($donatur->fresh(['kantorCabang:id,nama','picUser:id,name'])),
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan donatur',
                'error' => config('app.debug') ? $th->getMessage() : 'Terjadi kesalahan',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $query = Donatur::with(['kantorCabang:id,nama', 'picUser:id,name']);

        // Restrict show by created_by for non-admins (allow leaders to view subordinates' created items)
        $user = auth()->user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
        }

        if (! $this->userIsAdmin($user)) {
            // Allow visibility when user is assigned to the donor's kantor cabang
                try {
                    $assignedIds = $user->kantorCabangs()->pluck('id')->toArray();
                } catch (\Exception $e) {
                    $assignedIds = [];
                }

                if (empty($assignedIds) && $user->kantor_cabang_id) {
                    $assignedIds = [$user->kantor_cabang_id];
                }

            $hasSubordinates = $user->subordinates()->exists();

            // Detect 'Admin Cabang' role in a case-insensitive manner.
            $isAdminCabang = false;
            $rolesSource = $user->roles ?? ($user->role ? [$user->role] : []);
            if (is_array($rolesSource)) {
                foreach ($rolesSource as $r) {
                    $name = is_string($r) ? $r : ($r->name ?? null);
                    if ($name && strtolower(trim($name)) === 'admin cabang') {
                        $isAdminCabang = true;
                        break;
                    }
                }
            }

            // Admin Cabang users with assigned branches can view all donaturs
            // in those branches. Do that check before falling back to PIC-only.
            if (! empty($assignedIds) && $isAdminCabang) {
                $query->whereIn('donaturs.kantor_cabang_id', $assignedIds);
            } elseif (is_null($user->leader_id) && ! $hasSubordinates) {
                $query->where('pic', $user->id);
            } elseif (! empty($assignedIds)) {
                if (! $hasSubordinates) {
                    // Non-leaders with branch assignments: allow if donor is in assigned branch or caller is PIC
                    $query->where(function ($q) use ($assignedIds, $user) {
                        $q->whereIn('donaturs.kantor_cabang_id', $assignedIds)
                          ->orWhere('donaturs.pic', $user->id);
                    });
                } else {
                    // Leaders with branch assignments: allow if donor is in assigned branch or PIC is descendant
                    $allowed = User::descendantIdsOf($user->id);
                    $query->where(function ($q) use ($assignedIds, $allowed) {
                        $q->whereIn('donaturs.kantor_cabang_id', $assignedIds)
                          ->orWhereIn('donaturs.pic', $allowed);
                    });
                }
            } else {
                // No assigned branches: fall back to previous PIC/descendant rules
                if (! $hasSubordinates) {
                    $query->where('pic', $user->id);
                } else {
                    $allowed = User::descendantIdsOf($user->id);
                    $query->where(function ($q) use ($allowed, $user) {
                        $q->whereIn('donaturs.pic', $allowed);
                    });
                }
            }
        }

        $donatur = $query->find($id);

        if (! $donatur) {
            return response()->json([
                'success' => false,
                'message' => 'Donatur tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $this->transformDonatur($donatur),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $query = Donatur::query();

        // Filter berdasarkan user yang login (kecuali admin/superadmin)
        $user = auth()->user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
        }

        if (! $this->userIsAdmin($user)) {
            // If the user has no subordinates, restrict updates to donaturs
            // where they are PIC. If they do have subordinates (e.g. leader
            // or director), allow updates for descendants.
            $hasSubordinates = $user->subordinates()->exists();

            if (! $hasSubordinates) {
                $query->where('pic', $user->id);
            } else {
                $allowed = User::descendantIdsOf($user->id);
                $query->where(function ($q) use ($allowed, $user) {
                    // Qualify column to avoid ambiguity
                    $q->whereIn('donaturs.pic', $allowed);
                });
            }
        }

        $donatur = $query->find($id);

        if (! $donatur) {
            return response()->json([
                'success' => false,
                'message' => 'Donatur tidak ditemukan',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'kode' => ['nullable', 'string', 'max:50', Rule::unique('donaturs', 'kode')->ignore($id)],
            'nama' => 'required|string|max:255',
            'jenis_donatur' => 'required|array|min:1',
            'jenis_donatur.*' => ['string', Rule::in($this->allowedDonorTypes)],
            'pic' => 'nullable|exists:users,id',
            'alamat' => 'nullable|string|max:1000',
            'provinsi' => 'nullable|string|max:255',
            'kota_kab' => 'nullable|string|max:255',
            'kecamatan' => 'nullable|string|max:255',
            'kelurahan' => 'nullable|string|max:255',
            'no_handphone' => 'nullable|string|max:30',
            'email' => ['nullable', 'email', 'max:255', Rule::unique('donaturs', 'email')->ignore($id)],
            'tanggal_lahir' => 'nullable|date',
            'status' => ['nullable', 'string', Rule::in($this->allowedStatuses)],
            'kantor_cabang_id' => 'nullable|uuid|exists:kantor_cabang,id',
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

            $donatur->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Donatur berhasil diperbarui',
                'data' => $this->transformDonatur($donatur->fresh(['kantorCabang:id,nama','picUser:id,name'])),
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui donatur',
                'error' => config('app.debug') ? $th->getMessage() : 'Terjadi kesalahan',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $query = Donatur::query();

        // Filter berdasarkan user yang login (kecuali admin/superadmin)
        $user = auth()->user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
        }

        if (! $this->userIsAdmin($user)) {
            // If the user has no subordinates, restrict deletes to donaturs
            // where they are PIC. Leaders with subordinates may delete
            // donaturs owned by descendants.
            $hasSubordinates = $user->subordinates()->exists();

            if (! $hasSubordinates) {
                $query->where('pic', $user->id);
            } else {
                $allowed = User::descendantIdsOf($user->id);
                $query->whereIn('pic', $allowed);
            }
        }

        $donatur = $query->find($id);

        if (! $donatur) {
            return response()->json([
                'success' => false,
                'message' => 'Donatur tidak ditemukan',
            ], 404);
        }

        try {
            $donatur->deleted_by = auth()->id();
            $donatur->save();
            $donatur->delete();

            return response()->json([
                'success' => true,
                'message' => 'Donatur berhasil dihapus',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus donatur',
                'error' => config('app.debug') ? $th->getMessage() : 'Terjadi kesalahan',
            ], 500);
        }
    }

    /**
     * Transform donatur instance for API responses.
     */
    protected function transformDonatur(Donatur $donatur): array
    {
        return [
            'id' => $donatur->id,
            'kode' => $donatur->kode,
            'nama' => $donatur->nama,
            'jenis_donatur' => array_values($donatur->jenis_donatur ?? []),
            'pic' => $donatur->pic,
            'pic_user' => $donatur->picUser ? [
                'id' => $donatur->picUser->id,
                'nama' => $donatur->picUser->name,
            ] : null,
            'alamat' => $donatur->alamat,
            'provinsi' => $donatur->provinsi,
            'kota_kab' => $donatur->kota_kab,
            'kecamatan' => $donatur->kecamatan,
            'kelurahan' => $donatur->kelurahan,
            'no_handphone' => $donatur->no_handphone,
            'email' => $donatur->email,
            'tanggal_lahir' => optional($donatur->tanggal_lahir)->toDateString(),
            'status' => $donatur->status,
            'kantor_cabang_id' => $donatur->kantor_cabang_id,
            'kantor_cabang' => $donatur->kantorCabang ? [
                'id' => $donatur->kantorCabang->id,
                'nama' => $donatur->kantorCabang->nama,
            ] : null,
            'tanggal_dibuat' => optional($donatur->created_at)->toDateString(),
            'created_at' => optional($donatur->created_at)->toIso8601String(),
            'updated_at' => optional($donatur->updated_at)->toIso8601String(),
        ];
    }

    /**
     * Sanitize payload before persistence.
     */
    protected function sanitizePayload(array $data): array
    {
        $stringKeys = ['kode', 'nama', 'pic', 'alamat', 'provinsi', 'kota_kab', 'kecamatan', 'kelurahan', 'no_handphone', 'email'];

        foreach ($stringKeys as $key) {
            if (array_key_exists($key, $data) && is_string($data[$key])) {
                $data[$key] = trim($data[$key]);
            }
        }

        if (array_key_exists('alamat', $data) && $data['alamat'] === '') {
            $data['alamat'] = null;
        }

        $nullableKeys = ['kode', 'pic', 'alamat', 'provinsi', 'kota_kab', 'kecamatan', 'kelurahan', 'no_handphone', 'email', 'tanggal_lahir', 'kantor_cabang_id', 'status'];

        foreach ($nullableKeys as $key) {
            if (array_key_exists($key, $data)) {
                $value = $data[$key];
                if (is_string($value)) {
                    $value = trim($value);
                }

                $data[$key] = ($value === '' || $value === null) ? null : $value;
            }
        }

        if (array_key_exists('no_handphone', $data) && $data['no_handphone'] !== null) {
            $data['no_handphone'] = preg_replace('/\s+/', '', (string) $data['no_handphone']);
        }

        if (array_key_exists('jenis_donatur', $data)) {
            $data['jenis_donatur'] = collect($data['jenis_donatur'] ?? [])
                ->filter(fn ($value) => in_array($value, $this->allowedDonorTypes, true))
                ->unique()
                ->values()
                ->all();
        }

        if (array_key_exists('status', $data) && empty($data['status'])) {
            unset($data['status']);
        }

        return $data;
    }
}
