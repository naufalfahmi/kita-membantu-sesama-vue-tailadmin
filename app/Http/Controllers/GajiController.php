<?php

namespace App\Http\Controllers;

use App\Models\Gaji;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GajiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Gaji::with(['creator', 'updater', 'jabatan', 'pangkat']);

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('nama', 'like', "%{$search}%");
        }

        $gajis = $query->orderBy('created_at', 'desc')
                       ->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $gajis->items(),
            'pagination' => [
                'current_page' => $gajis->currentPage(),
                'last_page' => $gajis->lastPage(),
                'per_page' => $gajis->perPage(),
                'total' => $gajis->total(),
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
            'nominal' => 'required|numeric|min:0',
            'tipe' => 'nullable|in:bulanan,tunjangan,bonus',
            'tanggal_efektif' => 'nullable|date',
            'keterangan' => 'nullable|string',
            'jabatan_id' => 'nullable|exists:roles,id',
            'pangkat_id' => 'nullable|uuid|exists:pangkats,id',
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
                'nominal',
                'tipe',
                'tanggal_efektif',
                'keterangan',
                'jabatan_id',
                'pangkat_id',
            ]);
            $data['created_by'] = auth()->id();

            $gaji = Gaji::create($this->sanitizePayload($data));

            return response()->json([
                'success' => true,
                'message' => 'Gaji berhasil ditambahkan',
                'data' => $gaji->load(['creator', 'jabatan', 'pangkat']),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan gaji',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $gaji = Gaji::with(['creator', 'updater', 'jabatan', 'pangkat'])->find($id);

        if (!$gaji) {
            return response()->json([
                'success' => false,
                'message' => 'Gaji tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $gaji,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $gaji = Gaji::find($id);

        if (!$gaji) {
            return response()->json([
                'success' => false,
                'message' => 'Gaji tidak ditemukan',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'nominal' => 'required|numeric|min:0',
            'tipe' => 'nullable|in:bulanan,tunjangan,bonus',
            'tanggal_efektif' => 'nullable|date',
            'keterangan' => 'nullable|string',
            'jabatan_id' => 'nullable|exists:roles,id',
            'pangkat_id' => 'nullable|uuid|exists:pangkats,id',
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
                'nominal',
                'tipe',
                'tanggal_efektif',
                'keterangan',
                'jabatan_id',
                'pangkat_id',
            ]);
            $data['updated_by'] = auth()->id();

            $gaji->update($this->sanitizePayload($data));

            return response()->json([
                'success' => true,
                'message' => 'Gaji berhasil diupdate',
                'data' => $gaji->fresh()->load(['creator', 'updater', 'jabatan', 'pangkat']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate gaji',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $gaji = Gaji::find($id);

        if (!$gaji) {
            return response()->json([
                'success' => false,
                'message' => 'Gaji tidak ditemukan',
            ], 404);
        }

        try {
            $gaji->deleted_by = auth()->id();
            $gaji->save();
            $gaji->delete();

            return response()->json([
                'success' => true,
                'message' => 'Gaji berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus gaji',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan',
            ], 500);
        }
    }

    /**
     * Normalize payload values before persisting.
     */
    protected function sanitizePayload(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $trimmed = trim($value);
                $data[$key] = $trimmed === '' ? null : $trimmed;
            }
        }

        if (array_key_exists('jabatan_id', $data) && $data['jabatan_id'] !== null) {
            $data['jabatan_id'] = (string) $data['jabatan_id'];
        }

        if (array_key_exists('pangkat_id', $data) && $data['pangkat_id'] !== null) {
            $data['pangkat_id'] = (string) $data['pangkat_id'];
        }

        if (array_key_exists('nominal', $data)) {
            $data['nominal'] = (int) $data['nominal'];
        }

        return $data;
    }
}
