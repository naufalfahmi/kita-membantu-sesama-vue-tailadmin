<?php

namespace App\Http\Controllers;

use App\Models\KantorCabang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class KantorCabangController extends Controller
{
    /**
     * Generate next kode kantor cabang
     */
    private function generateKode(): string
    {
        // Get all kodes that match pattern KC## (case insensitive)
        $allKodes = KantorCabang::pluck('kode')->toArray();
        
        $maxNumber = 0;
        foreach ($allKodes as $kode) {
            if (preg_match('/^KC(\d+)$/i', $kode, $matches)) {
                $number = (int)$matches[1];
                if ($number > $maxNumber) {
                    $maxNumber = $number;
                }
            }
        }
        
        // Increment and format
        $nextNumber = $maxNumber + 1;
        return 'KC' . str_pad($nextNumber, 2, '0', STR_PAD_LEFT);
    }

    /**
     * Get next kode for new kantor cabang
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
        $query = KantorCabang::with(['creator', 'updater']);

        // Search filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%")
                  ->orWhere('kota', 'like', "%{$search}%")
                  ->orWhere('provinsi', 'like', "%{$search}%");
            });
        }

        // Filter by kota
        if ($request->has('kota') && $request->kota) {
            $query->where('kota', 'like', "%{$request->kota}%");
        }

        // Filter by provinsi
        if ($request->has('provinsi') && $request->provinsi) {
            $query->where('provinsi', 'like', "%{$request->provinsi}%");
        }

        $kantorCabang = $query->orderBy('created_at', 'desc')
                             ->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $kantorCabang->items(),
            'pagination' => [
                'current_page' => $kantorCabang->currentPage(),
                'last_page' => $kantorCabang->lastPage(),
                'per_page' => $kantorCabang->perPage(),
                'total' => $kantorCabang->total(),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Auto-generate kode if not provided
        $kode = $request->input('kode');
        if (empty($kode)) {
            $kode = $this->generateKode();
        }

        $validator = Validator::make(array_merge($request->all(), ['kode' => $kode]), [
            'kode' => 'required|string|max:50|unique:kantor_cabang,kode',
            'nama' => 'required|string|max:255',
            'kelurahan' => 'nullable|string|max:100',
            'kecamatan' => 'nullable|string|max:100',
            'kota' => 'nullable|string|max:100',
            'provinsi' => 'nullable|string|max:100',
            'kode_pos' => 'nullable|string|max:10',
            'alamat' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
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
                'kelurahan',
                'kecamatan',
                'kota',
                'provinsi',
                'kode_pos',
                'alamat',
                'latitude',
                'longitude',
            ]);

            $data['kode'] = $kode;
            $data['created_by'] = auth()->id();

            $kantorCabang = KantorCabang::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Kantor cabang berhasil ditambahkan',
                'data' => $kantorCabang->load(['creator']),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan kantor cabang',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $kantorCabang = KantorCabang::with(['creator', 'updater'])->find($id);

        if (!$kantorCabang) {
            return response()->json([
                'success' => false,
                'message' => 'Kantor cabang tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $kantorCabang,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $kantorCabang = KantorCabang::find($id);

        if (!$kantorCabang) {
            return response()->json([
                'success' => false,
                'message' => 'Kantor cabang tidak ditemukan',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'kode' => 'required|string|max:50|unique:kantor_cabang,kode,' . $id . ',id',
            'nama' => 'required|string|max:255',
            'kelurahan' => 'nullable|string|max:100',
            'kecamatan' => 'nullable|string|max:100',
            'kota' => 'nullable|string|max:100',
            'provinsi' => 'nullable|string|max:100',
            'kode_pos' => 'nullable|string|max:10',
            'alamat' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
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
                'kode',
                'nama',
                'kelurahan',
                'kecamatan',
                'kota',
                'provinsi',
                'kode_pos',
                'alamat',
                'latitude',
                'longitude',
            ]);

            $data['updated_by'] = auth()->id();

            $kantorCabang->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Kantor cabang berhasil diupdate',
                'data' => $kantorCabang->fresh()->load(['creator', 'updater']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate kantor cabang',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $kantorCabang = KantorCabang::find($id);

        if (!$kantorCabang) {
            return response()->json([
                'success' => false,
                'message' => 'Kantor cabang tidak ditemukan',
            ], 404);
        }

        try {
            $kantorCabang->deleted_by = auth()->id();
            $kantorCabang->save();
            $kantorCabang->delete();

            return response()->json([
                'success' => true,
                'message' => 'Kantor cabang berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus kantor cabang',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan',
            ], 500);
        }
    }
}
