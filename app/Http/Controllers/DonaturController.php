<?php

namespace App\Http\Controllers;

use App\Models\Donatur;
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
        $query = Donatur::with(['kantorCabang:id,nama']);

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('kode', 'like', "%{$search}%")
                    ->orWhere('no_handphone', 'like', "%{$search}%");
            });
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

        if ($request->filled('jenis_donatur')) {
            $jenis = $request->input('jenis_donatur');
            $jenisArray = is_array($jenis) ? $jenis : explode(',', (string) $jenis);
            $filteredJenis = array_intersect($jenisArray, $this->allowedDonorTypes);

            foreach ($filteredJenis as $value) {
                $query->whereJsonContains('jenis_donatur', $value);
            }
        }

        $donaturs = $query
            ->orderByDesc('created_at')
            ->paginate($request->integer('per_page', 20));

        $donaturs->getCollection()->transform(function (Donatur $donatur) {
            return $this->transformDonatur($donatur);
        });

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
            'pic' => 'nullable|string|max:255',
            'alamat' => 'nullable|string|max:1000',
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
            $data['created_by'] = auth()->id();

            $donatur = Donatur::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Donatur berhasil ditambahkan',
                'data' => $this->transformDonatur($donatur->fresh(['kantorCabang:id,nama'])),
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
        $donatur = Donatur::with(['kantorCabang:id,nama'])->find($id);

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
        $donatur = Donatur::find($id);

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
            'pic' => 'nullable|string|max:255',
            'alamat' => 'nullable|string|max:1000',
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
                'data' => $this->transformDonatur($donatur->fresh(['kantorCabang:id,nama'])),
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
        $donatur = Donatur::find($id);

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
            'alamat' => $donatur->alamat,
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
        $stringKeys = ['kode', 'nama', 'pic', 'alamat', 'no_handphone', 'email'];

        foreach ($stringKeys as $key) {
            if (array_key_exists($key, $data) && is_string($data[$key])) {
                $data[$key] = trim($data[$key]);
            }
        }

        if (array_key_exists('alamat', $data) && $data['alamat'] === '') {
            $data['alamat'] = null;
        }

        $nullableKeys = ['kode', 'pic', 'alamat', 'no_handphone', 'email', 'tanggal_lahir', 'kantor_cabang_id', 'status'];

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
