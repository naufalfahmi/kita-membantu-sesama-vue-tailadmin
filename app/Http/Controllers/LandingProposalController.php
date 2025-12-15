<?php

namespace App\Http\Controllers;

use App\Models\LandingProposal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class LandingProposalController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view landing proposal')->only(['index', 'show']);
        $this->middleware('permission:create landing proposal')->only('store');
        $this->middleware('permission:update landing proposal')->only('update');
        $this->middleware('permission:delete landing proposal')->only('destroy');
    }
    public function index(Request $request)
    {
        $query = LandingProposal::query();

        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where('name', 'like', "%{$s}%");
        }

        $items = $query->orderByDesc('proposal_date')->paginate($request->integer('per_page', 20));

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
            'name' => 'required|string|max:100',
            'proposal_date' => 'required|date',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:20480',
            'file_url' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        try {
            $data = $validator->validated();
            $data['created_by'] = auth()->id();

            // Handle uploaded file
            if ($request->hasFile('file')) {
                $path = $request->file('file')->store('landing-proposals', 'public');
                $data['file'] = $path;
                $data['file_name'] = $request->file('file')->getClientOriginalName();
                $data['file_size'] = $request->file('file')->getSize();
            } elseif (!empty($request->input('file_url'))) {
                $data['file'] = $request->input('file_url');
            }

            $proposal = LandingProposal::create($data);

            return response()->json(['success' => true, 'message' => 'Landing Proposal created', 'data' => $proposal], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal membuat proposal', 'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan'], 500);
        }
    }

    public function show(string $id)
    {
        $proposal = LandingProposal::find($id);
        if (! $proposal) return response()->json(['success' => false, 'message' => 'Not found'], 404);
        return response()->json(['success' => true, 'data' => $proposal]);
    }

    public function update(Request $request, string $id)
    {
        $proposal = LandingProposal::find($id);
        if (! $proposal) return response()->json(['success' => false, 'message' => 'Not found'], 404);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'proposal_date' => 'required|date',
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
                // delete old file if it's stored locally
                if ($proposal->file && !preg_match('/^https?:\/\//', $proposal->file)) {
                    Storage::disk('public')->delete($proposal->file);
                }
                $path = $request->file('file')->store('landing-proposals', 'public');
                $data['file'] = $path;
                $data['file_name'] = $request->file('file')->getClientOriginalName();
                $data['file_size'] = $request->file('file')->getSize();
            } elseif ($request->filled('file_url')) {
                $data['file'] = $request->input('file_url');
            }

            $proposal->update($data);

            return response()->json(['success' => true, 'message' => 'Landing Proposal updated', 'data' => $proposal->fresh()]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal mengupdate proposal', 'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan'], 500);
        }
    }

    public function destroy(string $id)
    {
        $proposal = LandingProposal::find($id);
        if (! $proposal) return response()->json(['success' => false, 'message' => 'Not found'], 404);

        try {
            // delete file if stored locally
            if ($proposal->file && !preg_match('/^https?:\/\//', $proposal->file)) {
                Storage::disk('public')->delete($proposal->file);
            }
            $proposal->deleted_by = auth()->id();
            $proposal->save();
            $proposal->delete();

            return response()->json(['success' => true, 'message' => 'Landing Proposal deleted']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus proposal', 'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan'], 500);
        }
    }
}
