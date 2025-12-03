<?php

namespace App\Http\Controllers;

use App\Models\TipeDonatur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TipeDonaturController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TipeDonatur::with(['creator', 'updater']);

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where('nama', 'like', "%{$search}%");
        }

        $perPage = (int) $request->get('per_page', 20);
        $tipeDonatur = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $tipeDonatur->items(),
            'pagination' => [
                'current_page' => $tipeDonatur->currentPage(),
                'last_page' => $tipeDonatur->lastPage(),
                'per_page' => $tipeDonatur->perPage(),
                'total' => $tipeDonatur->total(),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255|unique:tipe_donatur,nama',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $data = [
                'nama' => $request->input('nama'),
                'created_by' => auth()->id(),
            ];

            $tipeDonatur = TipeDonatur::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Tipe donatur berhasil ditambahkan',
                'data' => $tipeDonatur->load('creator'),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan tipe donatur',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $tipeDonatur = TipeDonatur::with(['creator', 'updater'])->find($id);

        if (!$tipeDonatur) {
            return response()->json([
                'success' => false,
                'message' => 'Tipe donatur tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $tipeDonatur,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $tipeDonatur = TipeDonatur::find($id);

        if (!$tipeDonatur) {
            return response()->json([
                'success' => false,
                'message' => 'Tipe donatur tidak ditemukan',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255|unique:tipe_donatur,nama,' . $id . ',id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $data = [
                'nama' => $request->input('nama'),
                'updated_by' => auth()->id(),
            ];

            $tipeDonatur->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Tipe donatur berhasil diupdate',
                'data' => $tipeDonatur->fresh()->load(['creator', 'updater']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate tipe donatur',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $tipeDonatur = TipeDonatur::find($id);

        if (!$tipeDonatur) {
            return response()->json([
                'success' => false,
                'message' => 'Tipe donatur tidak ditemukan',
            ], 404);
        }

        try {
            $tipeDonatur->deleted_by = auth()->id();
            $tipeDonatur->save();
            $tipeDonatur->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tipe donatur berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus tipe donatur',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan',
            ], 500);
        }
    }
}
