<?php

namespace App\Http\Controllers;

use App\Models\Mitra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MitraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Mitra::with(['kantorCabang:id,nama']);

        // Filter berdasarkan user yang login (kecuali admin/superadmin)
        $user = auth()->user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
        }

        $isAdmin = $user->hasAnyRole(['admin', 'superadmin', 'super-admin']);
        if (! $isAdmin) {
            $subIds = $user->subordinates()->pluck('id')->toArray();
            $allowed = array_merge([$user->id], $subIds);
            $query->whereIn('created_by', $allowed);
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kantor_cabang_id')) {
            $query->where('kantor_cabang_id', $request->input('kantor_cabang_id'));
        }

        $mitras = $query
            ->orderByDesc('created_at')
            ->paginate($request->integer('per_page', 20));

        $mitras->getCollection()->transform(function (Mitra $mitra) {
            return $this->transformMitra($mitra);
        });

        return response()->json([
            'success' => true,
            'data' => $mitras->items(),
            'pagination' => [
                'current_page' => $mitras->currentPage(),
                'last_page' => $mitras->lastPage(),
                'per_page' => $mitras->perPage(),
                'total' => $mitras->total(),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:mitras,email',
            'no_handphone' => 'nullable|string|max:30',
            'nama_bank' => 'nullable|string|max:100',
            'no_rekening' => 'nullable|string|max:50',
            'tanggal_lahir' => 'nullable|date',
            'pendidikan' => 'nullable|string|max:100',
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
            $data = $request->only([
                'nama',
                'email',
                'no_handphone',
                'nama_bank',
                'no_rekening',
                'tanggal_lahir',
                'pendidikan',
                'kantor_cabang_id',
            ]);

            $data['created_by'] = auth()->id();

            $mitra = Mitra::create($this->sanitizePayload($data));

            return response()->json([
                'success' => true,
                'message' => 'Mitra berhasil ditambahkan',
                'data' => $this->transformMitra($mitra->fresh(['kantorCabang:id,nama'])),
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan mitra',
                'error' => config('app.debug') ? $th->getMessage() : 'Terjadi kesalahan',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Restrict show by created_by for non-admins (allow leaders to view subordinates' created items)
        $user = auth()->user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
        }

        $query = Mitra::with(['kantorCabang:id,nama']);
        if (! $user->hasAnyRole(['admin', 'superadmin', 'super-admin'])) {
            $subIds = $user->subordinates()->pluck('id')->toArray();
            $allowed = array_merge([$user->id], $subIds);
            $query->whereIn('created_by', $allowed);
        }

        $mitra = $query->find($id);

        if (! $mitra) {
            return response()->json([
                'success' => false,
                'message' => 'Mitra tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $this->transformMitra($mitra),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $query = Mitra::query();
        $user = auth()->user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
        }

        if (! $user->hasAnyRole(['admin', 'superadmin', 'super-admin'])) {
            $subIds = $user->subordinates()->pluck('id')->toArray();
            $allowed = array_merge([$user->id], $subIds);
            $query->whereIn('created_by', $allowed);
        }

        $mitra = $query->find($id);

        if (! $mitra) {
            return response()->json([
                'success' => false,
                'message' => 'Mitra tidak ditemukan',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'email' => ['nullable', 'email', 'max:255', Rule::unique('mitras', 'email')->ignore($id)],
            'no_handphone' => 'nullable|string|max:30',
            'nama_bank' => 'nullable|string|max:100',
            'no_rekening' => 'nullable|string|max:50',
            'tanggal_lahir' => 'nullable|date',
            'pendidikan' => 'nullable|string|max:100',
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
            $data = $request->only([
                'nama',
                'email',
                'no_handphone',
                'nama_bank',
                'no_rekening',
                'tanggal_lahir',
                'pendidikan',
                'kantor_cabang_id',
            ]);

            $data['updated_by'] = auth()->id();

            $mitra->update($this->sanitizePayload($data));

            return response()->json([
                'success' => true,
                'message' => 'Mitra berhasil diperbarui',
                'data' => $this->transformMitra($mitra->fresh(['kantorCabang:id,nama'])),
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui mitra',
                'error' => config('app.debug') ? $th->getMessage() : 'Terjadi kesalahan',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $mitra = Mitra::find($id);

        if (! $mitra) {
            return response()->json([
                'success' => false,
                'message' => 'Mitra tidak ditemukan',
            ], 404);
        }

        try {
            $mitra->deleted_by = auth()->id();
            $mitra->save();
            $mitra->delete();

            return response()->json([
                'success' => true,
                'message' => 'Mitra berhasil dihapus',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus mitra',
                'error' => config('app.debug') ? $th->getMessage() : 'Terjadi kesalahan',
            ], 500);
        }
    }

    /**
     * Transform mitra instance into API response structure.
     */
    protected function transformMitra(Mitra $mitra): array
    {
        return [
            'id' => $mitra->id,
            'nama' => $mitra->nama,
            'email' => $mitra->email,
            'no_handphone' => $mitra->no_handphone,
            'nama_bank' => $mitra->nama_bank,
            'no_rekening' => $mitra->no_rekening,
            'tanggal_lahir' => optional($mitra->tanggal_lahir)->toDateString(),
            'pendidikan' => $mitra->pendidikan,
            'tanggal_dibuat' => optional($mitra->created_at)->toDateString(),
            'kantor_cabang_id' => $mitra->kantor_cabang_id,
            'kantor_cabang' => $mitra->kantorCabang ? [
                'id' => $mitra->kantorCabang->id,
                'nama' => $mitra->kantorCabang->nama,
            ] : null,
            'created_at' => optional($mitra->created_at)->toIso8601String(),
            'updated_at' => optional($mitra->updated_at)->toIso8601String(),
        ];
    }

    /**
     * Sanitize optional payload values before persistence.
     */
    protected function sanitizePayload(array $data): array
    {
        $stringKeys = [
            'nama',
            'email',
            'no_handphone',
            'nama_bank',
            'no_rekening',
            'pendidikan',
        ];

        foreach ($stringKeys as $key) {
            if (array_key_exists($key, $data) && is_string($data[$key])) {
                $data[$key] = trim($data[$key]);
            }
        }

        $nullableKeys = [
            'email',
            'no_handphone',
            'nama_bank',
            'no_rekening',
            'tanggal_lahir',
            'pendidikan',
            'kantor_cabang_id',
        ];

        foreach ($nullableKeys as $key) {
            if (array_key_exists($key, $data)) {
                $value = $data[$key];
                if (is_string($value)) {
                    $value = trim($value);
                }

                $data[$key] = ($value === '' || $value === null) ? null : $value;
            }
        }

        if (array_key_exists('no_handphone', $data)) {
            $data['no_handphone'] = $data['no_handphone'] !== null
                ? preg_replace('/\s+/', '', (string) $data['no_handphone'])
                : null;
        }

        if (array_key_exists('no_rekening', $data)) {
            $data['no_rekening'] = $data['no_rekening'] !== null
                ? preg_replace('/\s+/', '', (string) $data['no_rekening'])
                : null;
        }

        return $data;
    }
}
