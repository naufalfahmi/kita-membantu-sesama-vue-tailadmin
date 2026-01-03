<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class KaryawanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::with([
                'pangkat:id,nama',
                'tipeAbsensi:id,nama',
                'kantorCabangs:id,nama',
                'leader:id,name',
                'roles:id,name',
            ])
            ->karyawan();

        // Support filtering by role name (case-insensitive). When provided,
        // return users having that role. This is used by frontend pickers
        // (e.g. Fundraiser select) to list all users with a given role.
        $roleName = $request->input('role_name') ?? $request->input('role');
        $skipSubtreeForRoleFilter = false;
        if (!empty($roleName)) {
            $lower = mb_strtolower((string) $roleName);
            $query->whereHas('roles', function ($q) use ($lower) {
                $q->whereRaw('LOWER(name) = ?', [$lower]);
            });
            // When explicitly requesting by role name we skip the default
            // subtree visibility restriction so the frontend can get the
            // full list of users with that role.
            $skipSubtreeForRoleFilter = true;
        }

        // If caller requests only_subtree, restrict visible users to the caller's subtree
        // (applies even for admin callers when requested). Otherwise, keep existing
        // behavior: non-admins see limited subtree while admins see all.
        $authUser = auth()->user();
        if ($request->boolean('only_subtree') && $authUser) {
            if ($authUser->subordinates()->exists()) {
                $allowed = User::descendantIdsOf($authUser->id);
                $allowed = array_values(array_diff($allowed, [$authUser->id]));
                if (empty($allowed)) {
                    $query->whereRaw('1 = 0');
                } else {
                    $query->whereIn('id', $allowed);
                }
            } else {
                $query->where('id', $authUser->id);
            }
        } else {
            // If caller is not admin, restrict visible users to the caller's subtree.
            if ($authUser && ! $this->userIsAdmin($authUser)) {
                // If a role_name filter was provided we skip this subtree
                // restriction so that callers requesting a specific role get
                // the full list of users with that role.
                if (!empty($roleName)) {
                    // do nothing (skip subtree restriction)
                } else {
                    // apply original subtree restriction
                    // If user has subordinates, show only descendants excluding self (leaders)
                    if ($authUser->subordinates()->exists()) {
                        $allowed = User::descendantIdsOf($authUser->id);
                        $allowed = array_values(array_diff($allowed, [$authUser->id]));
                        if (empty($allowed)) {
                            $query->whereRaw('1 = 0');
                        } else {
                            $query->whereIn('id', $allowed);
                        }
                    } else {
                        // Subordinate users see only themselves
                        $query->where('id', $authUser->id);
                    }
                }
            }
        }

        // If caller requests only assigned karyawan, restrict to users assigned to
        // the current user's kantor cabang assignments (via pivot) or primary kantor_cabang_id.
        if ($request->boolean('only_assigned') && auth()->check()) {
            $user = auth()->user();
            try {
                $assignedIds = $user->kantorCabangs()->pluck('kantor_cabang.id')->toArray();
                if (!empty($assignedIds)) {
                    $query->where(function ($q) use ($assignedIds) {
                        $q->whereIn('kantor_cabang_id', $assignedIds)
                          ->orWhereHas('kantorCabangs', function ($q2) use ($assignedIds) {
                              $q2->whereIn('kantor_cabang.id', $assignedIds);
                          });
                    });
                } else {
                    if ($user->kantor_cabang_id) {
                        $query->where('kantor_cabang_id', $user->kantor_cabang_id);
                    } else {
                        // No assignments -> return empty result set
                        $query->whereRaw('1 = 0');
                    }
                }
            } catch (\Exception $e) {
                // swallow to avoid breaking consumers; leave query unfiltered on unexpected error
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('no_induk', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('posisi')) {
            $query->where('posisi', $request->posisi);
        }

        // Filter by pangkat
        if ($request->filled('pangkat_id')) {
            $query->where('pangkat_id', $request->pangkat_id);
        }

        // Filter by jabatan (role)
        if ($request->filled('role_id')) {
            $roleId = $request->role_id;
            $query->whereHas('roles', function ($q) use ($roleId) {
                $q->where('id', $roleId);
            });
        }

        // Filter by leader
        if ($request->filled('leader_id')) {
            $query->where('leader_id', $request->leader_id);
        }

        if ($request->filled('kantor_cabang_id')) {
            $query->where(function ($q) use ($request) {
                $q->where('kantor_cabang_id', $request->kantor_cabang_id)
                  ->orWhereHas('kantorCabangs', function ($q2) use ($request) {
                      $q2->where('kantor_cabang.id', $request->kantor_cabang_id);
                  });
            });
        }

        $karyawans = $query
            ->orderByDesc('created_at')
            ->paginate($request->integer('per_page', 20));

        $karyawans->getCollection()->transform(function (User $user) {
            return $this->transformKaryawan($user);
        });

        return response()->json([
            'success' => true,
            'data' => $karyawans->items(),
            'pagination' => [
                'current_page' => $karyawans->currentPage(),
                'last_page' => $karyawans->lastPage(),
                'per_page' => $karyawans->perPage(),
                'total' => $karyawans->total(),
            ],
        ]);
    }

    /**
     * Get the next available no_induk for karyawan
     */
    public function getNextNoInduk()
    {
        $lastKaryawan = User::where('tipe_user', 'karyawan')
                            ->whereNotNull('no_induk')
                            ->orderByRaw('CAST(SUBSTRING(no_induk, 2) AS UNSIGNED) DESC')
                            ->first();

        if ($lastKaryawan && preg_match('/K(\d+)/', $lastKaryawan->no_induk, $matches)) {
            $nextNumber = (int)$matches[1] + 1;
        } else {
            $nextNumber = 1;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'no_induk' => 'K' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'nullable|string|min:8',
            'no_induk' => 'nullable|string|unique:users,no_induk',
            'posisi' => 'nullable|string|max:100',
            'pangkat_id' => 'nullable|uuid|exists:pangkats,id',
            'tipe_absensi_id' => 'nullable|uuid|exists:tipe_absensi,id',
            'no_handphone' => 'nullable|string|max:20',
            'nama_bank' => 'nullable|string|max:100',
            'no_rekening' => 'nullable|string|max:50',
            'tanggal_lahir' => 'nullable|date',
            'pendidikan' => 'nullable|string|max:100',
            'tanggal_masuk' => 'nullable|date',
            'kantor_cabang_id' => 'nullable|uuid|exists:kantor_cabang,id',
            'leader_id' => 'nullable|string|exists:users,id',
            'kantor_cabang_ids' => 'nullable|array',
            'kantor_cabang_ids.*' => 'uuid|exists:kantor_cabang,id',
            'subordinate_ids' => 'nullable|array',
            'subordinate_ids.*' => 'string|exists:users,id',
            'is_active' => 'boolean',
            'role_id' => 'nullable|exists:roles,id',
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
                'name',
                'email',
                'no_induk',
                'posisi',
                'pangkat_id',
                'tipe_absensi_id',
                'no_handphone',
                'nama_bank',
                'no_rekening',
                'tanggal_lahir',
                'pendidikan',
                'tanggal_masuk',
                'kantor_cabang_id',
                'leader_id',
                'kantor_cabang_ids',
            ]);

            $data['no_induk'] = $this->prepareNoInduk($data['no_induk'] ?? null);
            $data['tipe_user'] = 'karyawan';
            $data['is_active'] = $request->boolean('is_active', true);
            $data['created_by'] = auth()->id();
            $data['password'] = Hash::make($request->password ?? 'password123');

            $karyawan = User::create($this->sanitizePayload($data));

            // Sync many-to-many kantor cabang assignments if provided
            if ($request->has('kantor_cabang_ids')) {
                $ids = $request->input('kantor_cabang_ids', []);
                if (!is_array($ids)) {
                    if (is_string($ids)) {
                        $ids = $ids === '' ? [] : array_filter(array_map('trim', explode(',', $ids)));
                    } else {
                        $ids = (array) $ids;
                    }
                }
                $karyawan->kantorCabangs()->sync($ids);
                // If the legacy single `kantor_cabang_id` column is not in the new set, clear it
                if ($karyawan->kantor_cabang_id && !in_array($karyawan->kantor_cabang_id, $ids)) {
                    $karyawan->kantor_cabang_id = null;
                    $karyawan->save();
                }
            }
            // Also support syncing pivot when single `kantor_cabang_id` provided
            if ($request->has('kantor_cabang_id') && !$request->has('kantor_cabang_ids')) {
                $single = $request->input('kantor_cabang_id');
                if ($single === null || $single === '') {
                    $karyawan->kantorCabangs()->sync([]);
                } else {
                    $karyawan->kantorCabangs()->sync([$single]);
                }
            }
            // If caller provided only the legacy single `kantor_cabang_id`, make sure pivot is in sync
            if ($request->has('kantor_cabang_id') && !$request->has('kantor_cabang_ids')) {
                $single = $request->input('kantor_cabang_id');
                if ($single === null || $single === '') {
                    $karyawan->kantorCabangs()->sync([]);
                } else {
                    $karyawan->kantorCabangs()->sync([$single]);
                }
            }

            if ($request->filled('role_id')) {
                $role = Role::find($request->role_id);
                if ($role) {
                    $karyawan->syncRoles($role);
                }
            }

            // Sync subordinate relationship: set leader_id on selected users
            if ($request->has('subordinate_ids')) {
                $ids = $request->input('subordinate_ids', []);
                if (!is_array($ids)) {
                    if (is_string($ids)) {
                        $ids = $ids === '' ? [] : array_filter(array_map('trim', explode(',', $ids)));
                    } else {
                        $ids = (array) $ids;
                    }
                }
                // prevent self-assignment
                $ids = array_values(array_filter($ids, function ($i) use ($karyawan) { return $i !== (string) $karyawan->id; }));

                // Clear existing subordinates that are not in the new list
                User::where('leader_id', $karyawan->id)->whereNotIn('id', $ids)->update(['leader_id' => null]);

                // Assign selected users to this leader
                if (!empty($ids)) {
                    User::whereIn('id', $ids)->update(['leader_id' => $karyawan->id]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Karyawan berhasil ditambahkan',
                'data' => $this->transformKaryawan($karyawan->fresh([
                    'pangkat:id,nama',
                    'tipeAbsensi:id,nama',
                    'kantorCabang:id,nama',
                    'roles:id,name',
                ])),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan karyawan',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $karyawan = User::with([
                'pangkat:id,nama',
                'tipeAbsensi:id,nama',
                'kantorCabang:id,nama',
                'roles:id,name',
            ])
            ->karyawan()
            ->find($id);

        if (!$karyawan) {
            return response()->json([
                'success' => false,
                'message' => 'Karyawan tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $this->transformKaryawan($karyawan),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $karyawan = User::where('tipe_user', 'karyawan')->find($id);

        if (!$karyawan) {
            return response()->json([
                'success' => false,
                'message' => 'Karyawan tidak ditemukan',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($id)],
            'password' => 'nullable|string|min:8',
            'no_induk' => ['nullable', 'string', Rule::unique('users')->ignore($id)],
            'posisi' => 'nullable|string|max:100',
            'pangkat_id' => 'nullable|uuid|exists:pangkats,id',
            'tipe_absensi_id' => 'nullable|uuid|exists:tipe_absensi,id',
            'no_handphone' => 'nullable|string|max:20',
            'nama_bank' => 'nullable|string|max:100',
            'no_rekening' => 'nullable|string|max:50',
            'tanggal_lahir' => 'nullable|date',
            'pendidikan' => 'nullable|string|max:100',
            'tanggal_masuk' => 'nullable|date',
            'kantor_cabang_id' => 'nullable|uuid|exists:kantor_cabang,id',
            'leader_id' => 'nullable|string|exists:users,id',
            'kantor_cabang_ids' => 'nullable|array',
            'kantor_cabang_ids.*' => 'uuid|exists:kantor_cabang,id',
            'subordinate_ids' => 'nullable|array',
            'subordinate_ids.*' => 'string|exists:users,id',
            'is_active' => 'boolean',
            'role_id' => 'nullable|exists:roles,id',
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
                'name',
                'email',
                'no_induk',
                'posisi',
                'pangkat_id',
                'tipe_absensi_id',
                'no_handphone',
                'nama_bank',
                'no_rekening',
                'tanggal_lahir',
                'pendidikan',
                'tanggal_masuk',
                'kantor_cabang_id',
                'leader_id',
                'kantor_cabang_ids',
            ]);

            $data['no_induk'] = $this->prepareNoInduk($data['no_induk'] ?? null);
            $data['is_active'] = $request->boolean('is_active', $karyawan->is_active);
            $data['updated_by'] = auth()->id();

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $karyawan->update($this->sanitizePayload($data));

            // Sync kantor cabang assignments (normalize input to array)
            if ($request->has('kantor_cabang_ids')) {
                $ids = $request->input('kantor_cabang_ids', []);
                if (!is_array($ids)) {
                    if (is_string($ids)) {
                        $ids = $ids === '' ? [] : array_filter(array_map('trim', explode(',', $ids)));
                    } else {
                        $ids = (array) $ids;
                    }
                }
                $karyawan->kantorCabangs()->sync($ids);
                // If the legacy single `kantor_cabang_id` column is not in the new set, clear it
                if ($karyawan->kantor_cabang_id && !in_array($karyawan->kantor_cabang_id, $ids)) {
                    $karyawan->kantor_cabang_id = null;
                    $karyawan->save();
                }
            }
            // Also allow syncing via single `kantor_cabang_id` for backwards compatibility
            if ($request->has('kantor_cabang_id') && !$request->has('kantor_cabang_ids')) {
                $single = $request->input('kantor_cabang_id');
                if ($single === null || $single === '') {
                    $karyawan->kantorCabangs()->sync([]);
                } else {
                    $karyawan->kantorCabangs()->sync([$single]);
                }
            }
            if ($request->has('role_id')) {
                if ($request->filled('role_id')) {
                    $role = Role::find($request->role_id);
                    if ($role) {
                        $karyawan->syncRoles($role);
                    }
                } else {
                    $karyawan->syncRoles([]);
                }
            }

            // Sync subordinate relationship: set leader_id on selected users
            if ($request->has('subordinate_ids')) {
                $ids = $request->input('subordinate_ids', []);
                if (!is_array($ids)) {
                    if (is_string($ids)) {
                        $ids = $ids === '' ? [] : array_filter(array_map('trim', explode(',', $ids)));
                    } else {
                        $ids = (array) $ids;
                    }
                }
                // prevent self-assignment
                $ids = array_values(array_filter($ids, function ($i) use ($karyawan) { return $i !== (string) $karyawan->id; }));

                // Clear existing subordinates that are not in the new list
                User::where('leader_id', $karyawan->id)->whereNotIn('id', $ids)->update(['leader_id' => null]);

                // Assign selected users to this leader
                if (!empty($ids)) {
                    User::whereIn('id', $ids)->update(['leader_id' => $karyawan->id]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Karyawan berhasil diupdate',
                'data' => $this->transformKaryawan($karyawan->fresh([
                    'pangkat:id,nama',
                    'tipeAbsensi:id,nama',
                    'kantorCabang:id,nama',
                    'roles:id,name',
                ])),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate karyawan',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $karyawan = User::where('tipe_user', 'karyawan')->find($id);

        if (!$karyawan) {
            return response()->json([
                'success' => false,
                'message' => 'Karyawan tidak ditemukan',
            ], 404);
        }

        try {
            $karyawan->deleted_by = auth()->id();
            $karyawan->save();
            $karyawan->syncRoles([]);
            $karyawan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Karyawan berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus karyawan',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan',
            ], 500);
        }
    }

    /**
     * Transform user data to API response structure.
     */
    protected function transformKaryawan(User $user): array
    {
        $role = $user->roles->first();

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'no_induk' => $user->no_induk,
            'posisi' => $user->posisi,
            'no_handphone' => $user->no_handphone,
            'nama_bank' => $user->nama_bank,
            'no_rekening' => $user->no_rekening,
            'tanggal_lahir' => optional($user->tanggal_lahir)->toDateString(),
            'pendidikan' => $user->pendidikan,
            'tanggal_masuk' => optional($user->tanggal_masuk)->toDateString(),
            'is_active' => (bool) $user->is_active,
            'pangkat_id' => $user->pangkat_id,
            'tipe_absensi_id' => $user->tipe_absensi_id,
            'kantor_cabang_id' => $user->kantor_cabang_id,
            'kantor_cabang_ids' => (function () use ($user) {
                $ids = $user->kantorCabangs->pluck('id')->toArray();
                if ($user->kantor_cabang_id && !in_array($user->kantor_cabang_id, $ids)) {
                    $ids[] = $user->kantor_cabang_id;
                }
                // ensure uniqueness and reindex
                return array_values(array_unique($ids));
            })(),
            'role_id' => $role?->id,
            'pangkat' => $user->pangkat ? [
                'id' => $user->pangkat->id,
                'nama' => $user->pangkat->nama,
            ] : null,
            'tipe_absensi' => $user->tipeAbsensi ? [
                'id' => $user->tipeAbsensi->id,
                'nama' => $user->tipeAbsensi->nama,
            ] : null,
            'kantor_cabangs' => $user->kantorCabangs->map(function ($c) {
                return ['id' => $c->id, 'nama' => $c->nama];
            })->values()->all(),
            'kantor_cabang' => $user->kantorCabang ? [
                'id' => $user->kantorCabang->id,
                'nama' => $user->kantorCabang->nama,
            ] : null,
            'leader' => $user->leader ? ['id' => $user->leader->id, 'name' => $user->leader->name] : null,
            'role' => $role ? [
                'id' => $role->id,
                'name' => $role->name,
            ] : null,
            'subordinates' => $user->subordinates->map(function ($u) {
                return ['id' => $u->id, 'name' => $u->name];
            })->values()->all(),
            'created_at' => optional($user->created_at)->toIso8601String(),
            'updated_at' => optional($user->updated_at)->toIso8601String(),
        ];
    }

    /**
     * Prepare and auto-generate no_induk value.
     */
    protected function prepareNoInduk(?string $noInduk): string
    {
        $trimmed = trim((string) $noInduk);

        if ($trimmed === '') {
            return $this->generateNextNoInduk();
        }

        return strtoupper($trimmed);
    }

    /**
     * Generate the next sequential no_induk (e.g., K001).
     */
    protected function generateNextNoInduk(): string
    {
        $lastKaryawan = User::karyawan()
            ->whereNotNull('no_induk')
            ->orderByRaw('CAST(SUBSTRING(no_induk, 2) AS UNSIGNED) DESC')
            ->first();

        if ($lastKaryawan && preg_match('/^K(\d{1,})$/', $lastKaryawan->no_induk, $matches)) {
            $nextNumber = ((int) $matches[1]) + 1;
        } else {
            $nextNumber = 1;
        }

        return 'K' . str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Sanitize optional payload values before persistence.
     */
    protected function sanitizePayload(array $data): array
    {
        $nullableKeys = [
            'posisi',
            'pangkat_id',
            'tipe_absensi_id',
            'no_handphone',
            'nama_bank',
            'no_rekening',
            'tanggal_lahir',
            'pendidikan',
            'tanggal_masuk',
            'kantor_cabang_id',
            'leader_id',
            'kantor_cabang_ids',
        ];

        foreach ($nullableKeys as $key) {
            if (array_key_exists($key, $data)) {
                $data[$key] = $data[$key] === '' ? null : $data[$key];
            }
        }

        if (isset($data['no_handphone'])) {
            $data['no_handphone'] = $data['no_handphone'] ? preg_replace('/\s+/', '', $data['no_handphone']) : null;
        }

        return $data;
    }
}
