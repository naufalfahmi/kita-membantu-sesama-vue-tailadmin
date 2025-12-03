<?php

namespace App\Http\Controllers;

use App\Models\Pangkat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PangkatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Pangkat::with(['creator', 'updater']);

        // Search filter by nama
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('nama', 'like', "%{$search}%");
        }

        $pangkats = $query->orderBy('created_at', 'desc')
                          ->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $pangkats->items(),
            'pagination' => [
                'current_page' => $pangkats->currentPage(),
                'last_page' => $pangkats->lastPage(),
                'per_page' => $pangkats->perPage(),
                'total' => $pangkats->total(),
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
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $data = $request->only(['nama']);
            $data['created_by'] = auth()->id();

            $pangkat = Pangkat::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Pangkat berhasil ditambahkan',
                'data' => $pangkat->load(['creator']),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan pangkat',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $pangkat = Pangkat::with(['creator', 'updater'])->find($id);

        if (!$pangkat) {
            return response()->json([
                'success' => false,
                'message' => 'Pangkat tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $pangkat,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $pangkat = Pangkat::find($id);

        if (!$pangkat) {
            return response()->json([
                'success' => false,
                'message' => 'Pangkat tidak ditemukan',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $data = $request->only(['nama']);
            $data['updated_by'] = auth()->id();

            $pangkat->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Pangkat berhasil diupdate',
                'data' => $pangkat->fresh()->load(['creator', 'updater']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate pangkat',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pangkat = Pangkat::find($id);

        if (!$pangkat) {
            return response()->json([
                'success' => false,
                'message' => 'Pangkat tidak ditemukan',
            ], 404);
        }

        try {
            $pangkat->deleted_by = auth()->id();
            $pangkat->save();
            $pangkat->delete();

            return response()->json([
                'success' => true,
                'message' => 'Pangkat berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus pangkat',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan',
            ], 500);
        }
    }
}
