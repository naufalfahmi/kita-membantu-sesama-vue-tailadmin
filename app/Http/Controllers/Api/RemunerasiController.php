<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Remunerasi;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class RemunerasiController extends Controller
{
    /**
     * Display a listing of remunerasi with optional filters.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Remunerasi::with(['kantorCabang', 'karyawan']);

        // Filtering by karyawan should be done via `karyawan_id` parameter

        if ($request->filled('bulan')) {
            $query->where('bulan_remunerasi', (int)$request->query('bulan'));
        }

        if ($request->filled('tahun')) {
            $query->where('tahun_remunerasi', (int)$request->query('tahun'));
        }

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->query('tanggal'));
        }

        if ($request->filled('min_take_home')) {
            $query->where('take_home_pay', '>=', (int)$request->query('min_take_home'));
        }

        if ($request->filled('max_take_home')) {
            $query->where('take_home_pay', '<=', (int)$request->query('max_take_home'));
        }

        if ($request->filled('kantor_cabang_id')) {
            $query->where('kantor_cabang_id', $request->query('kantor_cabang_id'));
        }

        if ($request->filled('karyawan_id')) {
            $query->where('karyawan_id', $request->query('karyawan_id'));
        }

        $perPage = (int)$request->query('per_page', 20);

        $paginated = $query->orderBy('tanggal', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $paginated->items(),
            'pagination' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
        ]);
    }

    /**
     * Store a newly created remunerasi.
     */
    public function store(Request $request): JsonResponse
    {
        $v = Validator::make($request->all(), [
            'karyawan_id' => 'required|integer|exists:users,id',
            'bulan_remunerasi' => 'required|integer|min:1|max:12',
            'tahun_remunerasi' => 'required|integer|min:2000',
            'gaji_pokok' => 'nullable|numeric|min:0',
            'take_home_pay' => 'required|numeric|min:0',
            'tanggal' => 'nullable|date',
            'kantor_cabang_id' => 'nullable|uuid|exists:kantor_cabang,id',
        ]);

        if ($v->fails()) {
            return response()->json(['errors' => $v->errors()], 422);
        }

        $remunerasi = Remunerasi::create($request->only([
            'karyawan_id',
            'bulan_remunerasi',
            'tahun_remunerasi',
            'gaji_pokok',
            'take_home_pay',
            'tanggal',
            'kantor_cabang_id',
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Remunerasi berhasil ditambahkan',
            'data' => $remunerasi,
        ], 201);
    }

    /**
     * Display the specified remunerasi.
     */
    public function show($id): JsonResponse
    {
        $rem = Remunerasi::with(['kantorCabang', 'karyawan'])->find($id);
        if (!$rem) {
            return response()->json([
                'success' => false,
                'message' => 'Remunerasi tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $rem,
        ]);
    }

    /**
     * Update the specified remunerasi.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $rem = Remunerasi::find($id);
        if (!$rem) {
            return response()->json([
                'success' => false,
                'message' => 'Remunerasi tidak ditemukan',
            ], 404);
        }

        $v = Validator::make($request->all(), [
            'karyawan_id' => 'sometimes|required|integer|exists:users,id',
            'bulan_remunerasi' => 'sometimes|required|integer|min:1|max:12',
            'tahun_remunerasi' => 'sometimes|required|integer|min:2000',
            'gaji_pokok' => 'nullable|numeric|min:0',
            'take_home_pay' => 'sometimes|required|numeric|min:0',
            'tanggal' => 'nullable|date',
            'kantor_cabang_id' => 'nullable|uuid|exists:kantor_cabang,id',
        ]);

        if ($v->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $v->errors(),
            ], 422);
        }

        $rem->fill($request->only([
            'karyawan_id',
            'bulan_remunerasi',
            'tahun_remunerasi',
            'gaji_pokok',
            'take_home_pay',
            'tanggal',
            'kantor_cabang_id',
        ]));

        $rem->save();

        return response()->json([
            'success' => true,
            'message' => 'Remunerasi berhasil diupdate',
            'data' => $rem,
        ]);
    }

    /**
     * Remove the specified remunerasi.
     */
    public function destroy($id): JsonResponse
    {
        $rem = Remunerasi::find($id);
        if (!$rem) {
            return response()->json([
                'success' => false,
                'message' => 'Remunerasi tidak ditemukan',
            ], 404);
        }

        $rem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Remunerasi berhasil dihapus',
        ]);
    }
}
