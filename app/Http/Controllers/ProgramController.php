<?php

namespace App\Http\Controllers;

use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Program::with(['creator', 'updater']);

        // Search filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_program', 'like', "%{$search}%");
            });
        }

        // Filter by tipe pembagian marketing
        if ($request->has('tipe_pembagian_marketing') && $request->tipe_pembagian_marketing) {
            $query->where('tipe_pembagian_marketing', $request->tipe_pembagian_marketing);
        }

        $program = $query->orderBy('created_at', 'desc')
                        ->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $program->items(),
            'pagination' => [
                'current_page' => $program->currentPage(),
                'last_page' => $program->lastPage(),
                'per_page' => $program->perPage(),
                'total' => $program->total(),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_program' => 'required|string|max:255',
            'persentase_hak_program' => 'nullable|numeric|min:0|max:100',
            'persentase_hak_program_operasional' => 'nullable|numeric|min:0|max:100',
            'persentase_hak_championship' => 'nullable|numeric|min:0|max:100',
            'tipe_pembagian_marketing' => 'nullable|in:percentage,nominal',
            'persentase_hak_marketing' => 'nullable|numeric|min:0|max:100',
            'persentase_hak_operasional_1' => 'nullable|numeric|min:0|max:100',
            'persentase_hak_iklan' => 'nullable|numeric|min:0|max:100',
            'persentase_hak_operasional_2' => 'nullable|numeric|min:0|max:100',
            'persentase_hak_operasional_3' => 'nullable|numeric|min:0|max:100',
            'jumlah_persentase' => 'nullable|numeric|min:0|max:100',
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
                'nama_program',
                'persentase_hak_program',
                'persentase_hak_program_operasional',
                'persentase_hak_championship',
                'tipe_pembagian_marketing',
                'persentase_hak_marketing',
                'persentase_hak_operasional_1',
                'persentase_hak_iklan',
                'persentase_hak_operasional_2',
                'persentase_hak_operasional_3',
                'jumlah_persentase',
            ]);

            $data['created_by'] = auth()->id();

            $program = Program::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Program berhasil ditambahkan',
                'data' => $program->load(['creator']),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan program',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $program = Program::with(['creator', 'updater'])->find($id);

        if (!$program) {
            return response()->json([
                'success' => false,
                'message' => 'Program tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $program,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $program = Program::find($id);

        if (!$program) {
            return response()->json([
                'success' => false,
                'message' => 'Program tidak ditemukan',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama_program' => 'required|string|max:255',
            'persentase_hak_program' => 'nullable|numeric|min:0|max:100',
            'persentase_hak_program_operasional' => 'nullable|numeric|min:0|max:100',
            'persentase_hak_championship' => 'nullable|numeric|min:0|max:100',
            'tipe_pembagian_marketing' => 'nullable|in:percentage,nominal',
            'persentase_hak_marketing' => 'nullable|numeric|min:0|max:100',
            'persentase_hak_operasional_1' => 'nullable|numeric|min:0|max:100',
            'persentase_hak_iklan' => 'nullable|numeric|min:0|max:100',
            'persentase_hak_operasional_2' => 'nullable|numeric|min:0|max:100',
            'persentase_hak_operasional_3' => 'nullable|numeric|min:0|max:100',
            'jumlah_persentase' => 'nullable|numeric|min:0|max:100',
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
                'nama_program',
                'persentase_hak_program',
                'persentase_hak_program_operasional',
                'persentase_hak_championship',
                'tipe_pembagian_marketing',
                'persentase_hak_marketing',
                'persentase_hak_operasional_1',
                'persentase_hak_iklan',
                'persentase_hak_operasional_2',
                'persentase_hak_operasional_3',
                'jumlah_persentase',
            ]);

            $data['updated_by'] = auth()->id();

            $program->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Program berhasil diupdate',
                'data' => $program->fresh()->load(['creator', 'updater']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate program',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $program = Program::find($id);

        if (!$program) {
            return response()->json([
                'success' => false,
                'message' => 'Program tidak ditemukan',
            ], 404);
        }

        try {
            $program->deleted_by = auth()->id();
            $program->save();
            $program->delete();

            return response()->json([
                'success' => true,
                'message' => 'Program berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus program',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan',
            ], 500);
        }
    }
}
