<?php

namespace App\Http\Controllers;

use App\Models\MitraPayroll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MitraPayrollController extends Controller
{
    public function index(Request $request)
    {
        $query = MitraPayroll::with(['mitra:id,nama', 'program:id,nama_program', 'creator:id,name']);

        if ($request->filled('mitra_id')) {
            $query->where('mitra_id', $request->mitra_id);
        }

        if ($request->filled('program_id')) {
            $query->where('program_id', $request->program_id);
        }

        if ($request->filled('search')) {
            $s = trim((string) $request->search);
            $query->where(function ($q) use ($s) {
                $q->where('nama_mitra', 'like', "%{$s}%")
                  ->orWhereHas('mitra', fn ($q2) => $q2->where('nama', 'like', "%{$s}%"))
                  ->orWhereHas('program', fn ($q2) => $q2->where('nama_program', 'like', "%{$s}%"));
            });
        }

        $perPage = $request->integer('per_page', 20);
        $items = $query->orderByDesc('created_at')->paginate($perPage);

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

    public function store(Request $request)
    {
        $rules = [
            'mitra_id' => 'nullable|uuid',
            'program_id' => 'nullable|uuid',
            'nama_mitra' => 'nullable|string|max:255',
            'jumlah' => 'required|numeric',
            'persentase' => 'required|numeric',
            'total' => 'nullable|numeric',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $data = $request->only(array_keys($rules));
        if (empty($data['total'])) {
            $data['total'] = ($data['jumlah'] * $data['persentase']) / 100;
        }

        $data['created_by'] = auth()->id();

        $item = MitraPayroll::create($data);

        return response()->json(['success' => true, 'data' => $item], 201);
    }

    public function show($id)
    {
        $item = MitraPayroll::with(['mitra', 'program', 'creator'])->find($id);
        if (! $item) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        }
        return response()->json(['success' => true, 'data' => $item]);
    }

    public function update(Request $request, $id)
    {
        $item = MitraPayroll::find($id);
        if (! $item) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        }

        $rules = [
            'mitra_id' => 'nullable|uuid',
            'program_id' => 'nullable|uuid',
            'nama_mitra' => 'nullable|string|max:255',
            'jumlah' => 'required|numeric',
            'persentase' => 'required|numeric',
            'total' => 'nullable|numeric',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $data = $request->only(array_keys($rules));
        if (empty($data['total'])) {
            $data['total'] = ($data['jumlah'] * $data['persentase']) / 100;
        }

        $data['updated_by'] = auth()->id();
        $item->update($data);

        return response()->json(['success' => true, 'data' => $item]);
    }

    public function destroy($id)
    {
        $item = MitraPayroll::find($id);
        if (! $item) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        }
        $item->deleted_by = auth()->id();
        $item->save();
        $item->delete();
        return response()->json(['success' => true]);
    }
}
