<?php

namespace App\Http\Controllers;

use App\Models\LandingProgram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LandingProgramController extends Controller
{
    public function index(Request $request)
    {
        $query = LandingProgram::query();

        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where('name', 'like', "%{$s}%");
        }

        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'active') $query->where('is_active', true);
            if ($status === 'inactive') $query->where('is_active', false);
        }

        $items = $query->orderByDesc('created_at')->paginate($request->integer('per_page', 20));

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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image_url' => 'nullable|url',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:20480',
            'is_active' => 'nullable|boolean',
            'is_highlight' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        try {
            $data = $validator->validated();
            $data['created_by'] = auth()->id();

            // Sanitize description
            $data['description'] = isset($data['description']) ? strip_tags($data['description'], '<p><br><strong><em><u><h1><h2><h3><h4><h5><h6><ul><ol><li><a>') : null;

            // Handle single image upload (frontend sends 'image')
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('landing-program', 'public');
                // store path as image_url
                $data['image_url'] = $path;
            }

            $program = LandingProgram::create($data);

            return response()->json(['success' => true, 'message' => 'Landing Program created', 'data' => $program], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal membuat landing program', 'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan'], 500);
        }
    }

    public function show(string $id)
    {
        $program = LandingProgram::find($id);
        if (! $program) return response()->json(['success' => false, 'message' => 'Not found'], 404);
        return response()->json(['success' => true, 'data' => $program]);
    }

    public function update(Request $request, string $id)
    {
        $program = LandingProgram::find($id);
        if (! $program) return response()->json(['success' => false, 'message' => 'Not found'], 404);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image_url' => 'nullable|url',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:20480',
            'is_active' => 'nullable|boolean',
            'is_highlight' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        try {
            $data = $validator->validated();
            $data['updated_by'] = auth()->id();

            // Sanitize description
            $data['description'] = isset($data['description']) ? strip_tags($data['description'], '<p><br><strong><em><u><h1><h2><h3><h4><h5><h6><ul><ol><li><a>') : null;

            // Handle single image upload (frontend sends 'image')
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($program->image_url) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($program->image_url);
                }
                $path = $request->file('image')->store('landing-program', 'public');
                $data['image_url'] = $path;
            }

            $program->update($data);

            return response()->json(['success' => true, 'message' => 'Landing Program updated', 'data' => $program->fresh()]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal mengupdate landing program', 'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan'], 500);
        }
    }

    public function destroy(string $id)
    {
        $program = LandingProgram::find($id);
        if (! $program) return response()->json(['success' => false, 'message' => 'Not found'], 404);

        $program->deleted_by = auth()->id();
        $program->save();
        $program->delete();

        return response()->json(['success' => true, 'message' => 'Landing Program deleted']);
    }
}
