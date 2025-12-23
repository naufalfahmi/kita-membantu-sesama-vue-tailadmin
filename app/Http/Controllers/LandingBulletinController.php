<?php

namespace App\Http\Controllers;

use App\Models\LandingBulletin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class LandingBulletinController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view landing bulletin')->only(['index', 'show']);
        $this->middleware('permission:create landing bulletin')->only('store');
        $this->middleware('permission:update landing bulletin')->only('update');
        $this->middleware('permission:delete landing bulletin')->only('destroy');
    }
    public function index(Request $request)
    {
        $query = LandingBulletin::query();

        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where('name', 'like', "%{$s}%");
        }

        $items = $query->orderByDesc('date')->paginate($request->integer('per_page', 20));

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

    // Public endpoint for the frontend to fetch bulletins with pagination (default 6 per page)
    public function publicIndex(Request $request)
    {
        $perPage = $request->integer('per_page', 6);
        $page = max(1, $request->integer('page', 1));

        $total = LandingBulletin::count();
        $items = LandingBulletin::orderByDesc('date')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $items,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'has_more' => $total > $page * $perPage,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:20480',
            'file_url' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        try {
            $data = $validator->validated();
            $data['created_by'] = auth()->id();

            if ($request->hasFile('file')) {
                $path = $request->file('file')->store('bulletins', 'public');
                $data['file'] = $path;
                $data['file_name'] = $request->file('file')->getClientOriginalName();
                $data['file_size'] = $request->file('file')->getSize();
            } elseif (!empty($request->input('file_url'))) {
                $data['file'] = $request->input('file_url');
            }

            $bulletin = LandingBulletin::create($data);

            return response()->json(['success' => true, 'message' => 'Landing Bulletin created', 'data' => $bulletin], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal membuat bulletin', 'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan'], 500);
        }
    }

    public function show(string $id)
    {
        $bulletin = LandingBulletin::find($id);
        if (! $bulletin) return response()->json(['success' => false, 'message' => 'Not found'], 404);
        return response()->json(['success' => true, 'data' => $bulletin]);
    }

    public function update(Request $request, string $id)
    {
        $bulletin = LandingBulletin::find($id);
        if (! $bulletin) return response()->json(['success' => false, 'message' => 'Not found'], 404);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:20480',
            'file_url' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        try {
            $data = $validator->validated();
            $data['updated_by'] = auth()->id();

            if ($request->hasFile('file')) {
                if ($bulletin->file && !preg_match('/^https?:\/\//', $bulletin->file)) {
                    Storage::disk('public')->delete($bulletin->file);
                }
                $path = $request->file('file')->store('bulletins', 'public');
                $data['file'] = $path;
                $data['file_name'] = $request->file('file')->getClientOriginalName();
                $data['file_size'] = $request->file('file')->getSize();
            } elseif ($request->filled('file_url')) {
                $data['file'] = $request->input('file_url');
            }

            $bulletin->update($data);

            return response()->json(['success' => true, 'message' => 'Landing Bulletin updated', 'data' => $bulletin->fresh()]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal mengupdate bulletin', 'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan'], 500);
        }
    }

    public function destroy(string $id)
    {
        $bulletin = LandingBulletin::find($id);
        if (! $bulletin) return response()->json(['success' => false, 'message' => 'Not found'], 404);

        try {
            if ($bulletin->file && !preg_match('/^https?:\/\//', $bulletin->file)) {
                Storage::disk('public')->delete($bulletin->file);
            }
            $bulletin->deleted_by = auth()->id();
            $bulletin->save();
            $bulletin->delete();

            return response()->json(['success' => true, 'message' => 'Landing Bulletin deleted']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus bulletin', 'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan'], 500);
        }
    }
}
