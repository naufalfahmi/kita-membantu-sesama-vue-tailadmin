<?php

namespace App\Http\Controllers;

use App\Models\LandingKegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class LandingKegiatanController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view landing kegiatan')->only(['index', 'show']);
        $this->middleware('permission:create landing kegiatan')->only('store');
        $this->middleware('permission:update landing kegiatan')->only('update');
        $this->middleware('permission:delete landing kegiatan')->only('destroy');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = LandingKegiatan::query();

        // Search filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('province', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $kegiatan = $query->orderBy('activity_date', 'desc')
                         ->orderBy('created_at', 'desc')
                         ->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $kegiatan->items(),
            'pagination' => [
                'current_page' => $kegiatan->currentPage(),
                'last_page' => $kegiatan->lastPage(),
                'per_page' => $kegiatan->perPage(),
                'total' => $kegiatan->total(),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:100',
            'number_of_recipients' => 'required|integer|min:1',
            'village' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:10|regex:/^[0-9]+$/',
            'address' => 'required|string|max:1000',
            'activity_date' => 'required|date',
            'status' => 'required|in:active,inactive',
            'description' => 'required|string',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:20480', // 20MB
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
                'title',
                'number_of_recipients',
                'village',
                'district',
                'city',
                'province',
                'postal_code',
                'address',
                'activity_date',
                'status',
            ]);

            // Sanitize HTML description
            $data['description'] = strip_tags($request->description, '<p><br><strong><em><u><h1><h2><h3><h4><h5><h6><ul><ol><li><a>');

            $kegiatan = LandingKegiatan::create($data);

            // Handle image uploads
            if ($request->hasFile('images')) {
                $imagePaths = [];
                foreach ($request->file('images') as $image) {
                    $path = $image->store('landing-kegiatan', 'public');
                    $imagePaths[] = $path;
                }
                // Store image paths as JSON (you might want to create a separate table for images)
                $kegiatan->update(['images' => json_encode($imagePaths)]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Kegiatan berhasil ditambahkan',
                'data' => $kegiatan->fresh(),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan kegiatan',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $kegiatan = LandingKegiatan::find($id);

        if (!$kegiatan) {
            return response()->json([
                'success' => false,
                'message' => 'Kegiatan tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $kegiatan,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $kegiatan = LandingKegiatan::find($id);

        if (!$kegiatan) {
            return response()->json([
                'success' => false,
                'message' => 'Kegiatan tidak ditemukan',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:100',
            'number_of_recipients' => 'required|integer|min:1',
            'village' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:10|regex:/^[0-9]+$/',
            'address' => 'required|string|max:1000',
            'activity_date' => 'required|date',
            'status' => 'required|in:active,inactive',
            'description' => 'required|string',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:20480', // 20MB
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
                'title',
                'number_of_recipients',
                'village',
                'district',
                'city',
                'province',
                'postal_code',
                'address',
                'activity_date',
                'status',
            ]);

            // Sanitize HTML description
            $data['description'] = strip_tags($request->description, '<p><br><strong><em><u><h1><h2><h3><h4><h5><h6><ul><ol><li><a>');

            $kegiatan->update($data);

            // Handle image uploads
            if ($request->hasFile('images')) {
                // Get existing images
                $existingImages = json_decode($kegiatan->images ?? '[]', true);
                if (!is_array($existingImages)) {
                    $existingImages = [];
                }

                // Get images to delete from request
                $imagesToDelete = $request->input('images_to_delete', []);
                if (is_string($imagesToDelete)) {
                    $imagesToDelete = json_decode($imagesToDelete, true) ?? [];
                }

                // Delete specified old images
                foreach ($imagesToDelete as $imageToDelete) {
                    if (in_array($imageToDelete, $existingImages)) {
                        Storage::disk('public')->delete($imageToDelete);
                        $existingImages = array_values(array_filter($existingImages, function($img) use ($imageToDelete) {
                            return $img !== $imageToDelete;
                        }));
                    }
                }

                // Add new images
                $newImagePaths = [];
                foreach ($request->file('images') as $image) {
                    $path = $image->store('landing-kegiatan', 'public');
                    $newImagePaths[] = $path;
                }

                // Merge existing and new images
                $allImages = array_merge($existingImages, $newImagePaths);
                $kegiatan->update(['images' => json_encode($allImages)]);
            } elseif ($request->has('images_to_delete')) {
                // Handle deletion only (no new uploads)
                $existingImages = json_decode($kegiatan->images ?? '[]', true);
                if (!is_array($existingImages)) {
                    $existingImages = [];
                }

                $imagesToDelete = $request->input('images_to_delete', []);
                if (is_string($imagesToDelete)) {
                    $imagesToDelete = json_decode($imagesToDelete, true) ?? [];
                }

                foreach ($imagesToDelete as $imageToDelete) {
                    if (in_array($imageToDelete, $existingImages)) {
                        Storage::disk('public')->delete($imageToDelete);
                        $existingImages = array_values(array_filter($existingImages, function($img) use ($imageToDelete) {
                            return $img !== $imageToDelete;
                        }));
                    }
                }

                $kegiatan->update(['images' => json_encode($existingImages)]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Kegiatan berhasil diupdate',
                'data' => $kegiatan->fresh(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate kegiatan',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $kegiatan = LandingKegiatan::find($id);

        if (!$kegiatan) {
            return response()->json([
                'success' => false,
                'message' => 'Kegiatan tidak ditemukan',
            ], 404);
        }

        try {
            // Delete associated images
            $images = json_decode($kegiatan->images ?? '[]', true);
            if (is_array($images)) {
                foreach ($images as $image) {
                    Storage::disk('public')->delete($image);
                }
            }

            $kegiatan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Kegiatan berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus kegiatan',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan',
            ], 500);
        }
    }
}
