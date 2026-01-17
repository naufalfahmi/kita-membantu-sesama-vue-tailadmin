<?php

namespace App\Http\Controllers;

use App\Models\TipeProgram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TipeProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TipeProgram::with(['creator', 'updater']);

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%");
        }

        $perPage = (int) $request->get('per_page', 20);
        $items = $query->orderBy('orders', 'asc')->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $items->items(),
            'pagination' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:tipe_program,name',
            'orders' => 'nullable|integer|min:0',
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
                'name' => $request->input('name'),
                'orders' => $request->input('orders') ?? 0,
                'created_by' => auth()->id(),
            ];

            $item = TipeProgram::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Tipe program berhasil ditambahkan',
                'data' => $item->load('creator'),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan tipe program',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $item = TipeProgram::with(['creator', 'updater'])->find($id);

        if (! $item) {
            return response()->json([
                'success' => false,
                'message' => 'Tipe program tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $item,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $item = TipeProgram::find($id);

        if (! $item) {
            return response()->json([
                'success' => false,
                'message' => 'Tipe program tidak ditemukan',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:tipe_program,name,' . $id . ',id',
            'orders' => 'nullable|integer|min:0',
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
                'name' => $request->input('name'),
                'orders' => $request->input('orders') ?? 0,
                'updated_by' => auth()->id(),
            ];

            $item->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Tipe program berhasil diupdate',
                'data' => $item->fresh()->load(['creator', 'updater']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate tipe program',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = TipeProgram::find($id);

        if (! $item) {
            return response()->json([
                'success' => false,
                'message' => 'Tipe program tidak ditemukan',
            ], 404);
        }

        try {
            $item->deleted_by = auth()->id();
            $item->save();
            $item->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tipe program berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus tipe program',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan',
            ], 500);
        }
    }
}
