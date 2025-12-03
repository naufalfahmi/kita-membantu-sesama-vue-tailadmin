<?php

namespace App\Http\Controllers;

use App\Models\TipeAbsensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TipeAbsensiController extends Controller
{
    /**
     * Generate next kode tipe absensi
     */
    private function generateKode(): string
    {
        // Get all kodes that match pattern TA## (case insensitive)
        $allKodes = TipeAbsensi::pluck('kode')->toArray();
        
        $maxNumber = 0;
        foreach ($allKodes as $kode) {
            if (preg_match('/^TA(\d+)$/i', $kode, $matches)) {
                $number = (int)$matches[1];
                if ($number > $maxNumber) {
                    $maxNumber = $number;
                }
            }
        }
        
        // Increment and format
        $nextNumber = $maxNumber + 1;
        return 'TA' . str_pad($nextNumber, 2, '0', STR_PAD_LEFT);
    }

    /**
     * Get next kode for new tipe absensi
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
        $query = TipeAbsensi::with(['creator', 'updater']);

        // Search filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        $tipeAbsensi = $query->orderBy('created_at', 'desc')
                             ->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $tipeAbsensi->items(),
            'pagination' => [
                'current_page' => $tipeAbsensi->currentPage(),
                'last_page' => $tipeAbsensi->lastPage(),
                'per_page' => $tipeAbsensi->perPage(),
                'total' => $tipeAbsensi->total(),
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
            'kode' => 'required|string|max:50|unique:tipe_absensi,kode',
            'nama' => 'required|string|max:255',
            'jam_masuk' => 'nullable|date_format:H:i',
            'jam_keluar' => 'nullable|date_format:H:i',
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
                'jam_masuk',
                'jam_keluar',
            ]);

            $data['kode'] = $kode;
            $data['created_by'] = auth()->id();

            $tipeAbsensi = TipeAbsensi::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Tipe absensi berhasil ditambahkan',
                'data' => $tipeAbsensi->load(['creator']),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan tipe absensi',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $tipeAbsensi = TipeAbsensi::with(['creator', 'updater'])->find($id);

        if (!$tipeAbsensi) {
            return response()->json([
                'success' => false,
                'message' => 'Tipe absensi tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $tipeAbsensi,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $tipeAbsensi = TipeAbsensi::find($id);

        if (!$tipeAbsensi) {
            return response()->json([
                'success' => false,
                'message' => 'Tipe absensi tidak ditemukan',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'kode' => 'required|string|max:50|unique:tipe_absensi,kode,' . $id . ',id',
            'nama' => 'required|string|max:255',
            'jam_masuk' => 'nullable|date_format:H:i',
            'jam_keluar' => 'nullable|date_format:H:i',
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
                'jam_masuk',
                'jam_keluar',
            ]);

            $data['updated_by'] = auth()->id();

            $tipeAbsensi->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Tipe absensi berhasil diupdate',
                'data' => $tipeAbsensi->fresh()->load(['creator', 'updater']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate tipe absensi',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $tipeAbsensi = TipeAbsensi::find($id);

        if (!$tipeAbsensi) {
            return response()->json([
                'success' => false,
                'message' => 'Tipe absensi tidak ditemukan',
            ], 404);
        }

        try {
            $tipeAbsensi->deleted_by = auth()->id();
            $tipeAbsensi->save();
            $tipeAbsensi->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tipe absensi berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus tipe absensi',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan',
            ], 500);
        }
    }
}
