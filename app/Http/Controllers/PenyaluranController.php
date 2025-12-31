<?php

namespace App\Http\Controllers;

use App\Models\Penyaluran;
use App\Models\PenyaluranImage;
use App\Models\PengajuanDana;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PenyaluranController extends Controller
{
    public function index(Request $request)
    {
        // Eager-load pengajuan relations (program, fundraiser) so frontend can read nested program data
        $query = Penyaluran::with(['pengajuan.program', 'pengajuan.fundraiser', 'images', 'kantorCabang']);

        // Date range filter: optional start_date and end_date (YYYY-MM-DD)
        if ($request->filled('start_date') || $request->filled('end_date')) {
            $start = $request->filled('start_date') ? Carbon::parse($request->input('start_date'))->startOfDay() : null;
            $end = $request->filled('end_date') ? Carbon::parse($request->input('end_date'))->endOfDay() : null;
            if ($start && $end) {
                $query->whereBetween('created_at', [$start, $end]);
            } elseif ($start) {
                $query->where('created_at', '>=', $start);
            } elseif ($end) {
                $query->where('created_at', '<=', $end);
            }
        }

        if ($request->filled('search')) {
            $s = trim((string)$request->input('search'));
            $query->where('program_name', 'like', "%{$s}%")->orWhere('pic', 'like', "%{$s}%");
        }

        $perPage = (int)$request->get('per_page', 20);
        $page = (int)$request->get('page', 1);
        $items = $query->orderByDesc('created_at')->paginate($perPage, ['*'], 'page', $page);

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

    public function show(string $id)
    {
        $p = Penyaluran::with(['images', 'pengajuan'])->find($id);
        if (! $p) return response()->json(['success' => false, 'message' => 'Penyaluran tidak ditemukan'], 404);
        return response()->json(['success' => true, 'data' => $p]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pengajuan_dana_id' => 'nullable|uuid|exists:pengajuan_danas,id',
            'submission_type' => 'nullable|string',
            'program_name' => 'nullable|string|max:255',
            'pic' => 'nullable|string|max:255',
            'village' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'report' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'kantor_cabang_id' => 'nullable|exists:kantor_cabang,id',
            'images' => 'nullable|array',
            'images.*.path' => 'required_with:images|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $data = $request->only(['program_name','pic','village','district','city','province','postal_code','address','report','amount','kantor_cabang_id']);
        $data['created_by'] = auth()->id();

        $amount = (float) $request->input('amount');

        // If pengajuan_dana_id provided, validate it and ensure it has enough remaining balance
        if ($request->filled('pengajuan_dana_id')) {
            $pjId = $request->input('pengajuan_dana_id');
            $pengajuan = PengajuanDana::where('id', $pjId)
                ->whereRaw("LOWER(status) IN ('approved','disetujui')")
                ->first();
            if (! $pengajuan) {
                return response()->json(['success' => false, 'message' => 'Pengajuan tidak ditemukan atau belum disetujui'], 422);
            }

            // total already used for this pengajuan (covers both explicit pengajuan_dana_id and relations)
            $used = Penyaluran::whereHas('pengajuan', function ($q) use ($pengajuan) {
                $q->where('id', $pengajuan->id);
            })->sum('amount');

            $available = max(0, $pengajuan->amount - $used);
            if ($available < $amount) {
                return response()->json(['success' => false, 'message' => 'Dana pengajuan tidak cukup. Sisa: ' . $available], 422);
            }

            $data['pengajuan_dana_id'] = $pengajuan->id;
        } else {
            // No pengajuan specified: find one approved pengajuan of the user with enough remaining amount
            $userId = auth()->id();
            $found = null;
            $pengajuanQuery = PengajuanDana::where('fundraiser_id', $userId)
                ->whereRaw("LOWER(status) IN ('approved','disetujui')")
                ->orderBy('created_at');
            if ($request->filled('submission_type')) {
                $submissionType = (string) $request->input('submission_type');
                $pengajuanQuery->where('submission_type', $submissionType);
            }
            $pengajuans = $pengajuanQuery->get();

            foreach ($pengajuans as $pengajuan) {
                $used = Penyaluran::whereHas('pengajuan', function ($q) use ($pengajuan) {
                    $q->where('id', $pengajuan->id);
                })->sum('amount');
                $available = max(0, $pengajuan->amount - $used);
                if ($available >= $amount) {
                    $found = $pengajuan;
                    break;
                }
            }

            if (! $found) {
                return response()->json(['success' => false, 'message' => 'Tidak ada pengajuan disetujui dengan dana yang cukup'], 422);
            }

            $data['pengajuan_dana_id'] = $found->id;
        }

        $penyaluran = Penyaluran::create($data);

        // handle uploaded files if provided
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                if (! $file->isValid()) continue;
                // store in public disk under penyaluran
                $path = $file->store('penyaluran', 'public');
                PenyaluranImage::create([
                    'penyaluran_id' => $penyaluran->id,
                    'path' => $path,
                    'caption' => null,
                    'created_by' => auth()->id(),
                ]);
            }
        } elseif ($request->filled('images') && is_array($request->images)) {
            foreach ($request->images as $img) {
                PenyaluranImage::create([
                    'penyaluran_id' => $penyaluran->id,
                    'path' => $img['path'] ?? '',
                    'caption' => $img['caption'] ?? null,
                    'created_by' => auth()->id(),
                ]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Penyaluran berhasil dibuat', 'data' => $penyaluran], 201);
    }

    public function update(Request $request, string $id)
    {
        $penyaluran = Penyaluran::find($id);
        if (! $penyaluran) return response()->json(['success' => false, 'message' => 'Penyaluran tidak ditemukan'], 404);

        $validator = Validator::make($request->all(), [
            'program_name' => 'nullable|string|max:255',
            'pic' => 'nullable|string|max:255',
            'amount' => 'nullable|numeric|min:0',
        ]);
        if ($validator->fails()) return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);

        $penyaluran->update(array_merge($request->only(['program_name','pic','village','district','city','province','postal_code','address','report','amount','kantor_cabang_id']), ['updated_by' => auth()->id()]));

        return response()->json(['success' => true, 'message' => 'Penyaluran updated', 'data' => $penyaluran]);
    }

    public function destroy(string $id)
    {
        $p = Penyaluran::find($id);
        if (! $p) return response()->json(['success' => false, 'message' => 'Penyaluran tidak ditemukan'], 404);
        $p->deleted_by = auth()->id();
        $p->save();
        $p->delete();
        return response()->json(['success' => true, 'message' => 'Penyaluran dihapus']);
    }

    // Helper: return approved pengajuans for frontend selection
    public function approvedPengajuans(Request $request)
    {
        $query = PengajuanDana::with(['fundraiser:id,name','program:id,nama_program'])->where('status', 'Approved');
        if ($request->filled('search')) {
            $s = trim((string)$request->input('search'));
            $query->whereHas('fundraiser', function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%");
            });
        }
        $items = $query->orderByDesc('created_at')->limit(100)->get();
        return response()->json(['success' => true, 'data' => $items]);
    }

    // Return remaining credit for the authenticated user (approved pengajuan total minus penyaluran used)
    public function myCredit(Request $request)
    {
        $user = auth()->user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Not authenticated'], 401);
        }

        // Get approved pengajuan IDs for this user
        // accept common variants of approved status (case-insensitive)
        $pengajuanQuery = PengajuanDana::where('fundraiser_id', $user->id)
            ->whereRaw("LOWER(status) IN ('approved','disetujui')");

        // If frontend requests a specific submission type, only count that type
        if ($request->filled('type')) {
            $t = (string) $request->input('type');
            $pengajuanQuery->where('submission_type', $t);
        }

        $pengajuanIds = $pengajuanQuery->pluck('id');

        $approvedTotal = PengajuanDana::whereIn('id', $pengajuanIds)->sum('amount');

        // Sum penyalurans that reference these pengajuan ids
        $usedTotalById = Penyaluran::whereIn('pengajuan_dana_id', $pengajuanIds)->sum('amount');

        // Also sum penyalurans that are linked via the pengajuan relationship (in case pengajuan_dana_id wasn't set)
        $usedTotalByRelation = Penyaluran::whereHas('pengajuan', function ($q) use ($user) {
            $q->where('fundraiser_id', $user->id);
        })->sum('amount');

        // Combine (avoid double-counting by subtracting overlap if any)
        // Overlap = penyalurans with pengajuan_dana_id in $pengajuanIds that also have pengajuan relation (they're the same), so we can take the max of both sums or dedupe via query.
        // Simpler: count distinct sums: sum by id + sum of those without pengajuan_dana_id but linked via relation
        $usedTotalWithoutId = Penyaluran::whereNull('pengajuan_dana_id')->whereHas('pengajuan', function ($q) use ($user) {
            $q->where('fundraiser_id', $user->id);
        })->sum('amount');

        $usedTotal = $usedTotalById + $usedTotalWithoutId;

        $remaining = max(0, $approvedTotal - $usedTotal);

        return response()->json(['success' => true, 'data' => ['remaining' => (int)$remaining]]);
    }
}
