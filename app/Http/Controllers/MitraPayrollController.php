<?php

namespace App\Http\Controllers;

use App\Models\MitraPayroll;
use App\Models\Program;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MitraPayrollController extends Controller
{
    public function index(Request $request)
    {
        $query = MitraPayroll::with(['mitra:id,nama', 'program:id,nama_program', 'creator:id,name']);

        $user = auth()->user();
        $mitraFilterApplied = false;

        if ($user && ! $this->userIsAdmin($user)) {
            [$mitra, $isMitraUser] = $this->resolveMitraContext($user);

            if ($isMitraUser) {
                if ($mitra) {
                    $query->where('mitra_id', $mitra->id);
                } else {
                    // User identified as mitra but has no linked record; hide data entirely
                    $query->whereRaw('1 = 0');
                }

                $mitraFilterApplied = true;
            }
        }

        if ($request->filled('mitra_id') && ! $mitraFilterApplied) {
            $query->where('mitra_id', $request->mitra_id);
        }

        if ($request->filled('program_id')) {
            $programId = $request->program_id;
            $query->where(function ($q) use ($programId) {
                $q->where('program_id', $programId)
                  ->orWhereRaw('JSON_CONTAINS(program_ids, ?)', [json_encode($programId)]);
            });
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

        // Attach programs and a comma-separated display string for each item
        $rows = $items->items();
        $allPids = collect($rows)->flatMap(function ($r) {
            $pids = [];
            if (! empty($r->program_ids) && is_array($r->program_ids)) $pids = $r->program_ids;
            elseif (! empty($r->program_id)) $pids = [$r->program_id];
            return $pids;
        })->unique()->filter()->values()->all();

        $programMap = [];
        if (! empty($allPids)) {
            $programMap = Program::whereIn('id', $allPids)->get()->mapWithKeys(function ($p) {
                return [$p->id => $p->nama_program];
            })->toArray();
        }

        foreach ($rows as $r) {
            $pids = [];
            if (! empty($r->program_ids) && is_array($r->program_ids)) $pids = $r->program_ids;
            elseif (! empty($r->program_id)) $pids = [$r->program_id];

            $names = array_values(array_filter(array_map(function ($id) use ($programMap) {
                return $programMap[$id] ?? null;
            }, $pids)));

            $r->setAttribute('programs', array_map(function ($id) use ($programMap) {
                return ['id' => $id, 'nama_program' => $programMap[$id] ?? null];
            }, $pids));
            $r->setAttribute('programs_display', empty($names) ? null : implode(', ', $names));
        }

        return response()->json([
            'success' => true,
            'data' => $rows,
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
            // new: accept array of program ids for multi-program submissions
            'program_ids' => 'nullable|array',
            'program_ids.*' => 'uuid',
            'nama_mitra' => 'nullable|string|max:255',
            'payroll_date' => 'required|date',
            'jumlah' => 'required|numeric',
            'persentase' => 'required|numeric',
            'total' => 'nullable|numeric',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        // avoid using rule keys with wildcard (e.g. 'program_ids.*') when picking input
        $allowedKeys = array_filter(array_keys($rules), function ($k) {
            return strpos($k, '.*') === false;
        });
        $data = $request->only($allowedKeys);
        if (empty($data['total'])) {
            $data['total'] = ($data['jumlah'] * $data['persentase']) / 100;
        }

        $data['created_by'] = auth()->id();

        // Store program_ids as JSON in single payroll record; keep program_id as first element for compatibility
        if (! empty($data['program_ids']) && is_array($data['program_ids'])) {
            $pids = array_values($data['program_ids']);
            $data['program_ids'] = $pids;
            if (empty($data['program_id']) && count($pids) > 0) {
                $data['program_id'] = $pids[0];
            }
        }

        $item = MitraPayroll::create($data);

        return response()->json(['success' => true, 'data' => $item], 201);
    }

    public function show($id)
    {
        $item = MitraPayroll::with(['mitra', 'program', 'creator'])->find($id);
        if (! $item) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        }
        // attach programs and display string
        $pids = [];
        if (! empty($item->program_ids) && is_array($item->program_ids)) $pids = $item->program_ids;
        elseif (! empty($item->program_id)) $pids = [$item->program_id];

        $programMap = [];
        if (! empty($pids)) {
            $programMap = Program::whereIn('id', $pids)->get()->mapWithKeys(function ($p) {
                return [$p->id => $p->nama_program];
            })->toArray();
        }

        $names = array_values(array_filter(array_map(function ($id) use ($programMap) {
            return $programMap[$id] ?? null;
        }, $pids)));

        $item->setAttribute('programs', array_map(function ($id) use ($programMap) {
            return ['id' => $id, 'nama_program' => $programMap[$id] ?? null];
        }, $pids));
        $item->setAttribute('programs_display', empty($names) ? null : implode(', ', $names));

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
            'program_ids' => 'nullable|array',
            'program_ids.*' => 'uuid',
            'nama_mitra' => 'nullable|string|max:255',
            'payroll_date' => 'required|date',
            'jumlah' => 'required|numeric',
            'persentase' => 'required|numeric',
            'total' => 'nullable|numeric',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        // avoid wildcard keys when picking input
        $allowedKeys = array_filter(array_keys($rules), function ($k) {
            return strpos($k, '.*') === false;
        });
        $data = $request->only($allowedKeys);
        if (empty($data['total'])) {
            $data['total'] = ($data['jumlah'] * $data['persentase']) / 100;
        }

        // handle program_ids -> store array; keep program_id as first element if present
        if (! empty($data['program_ids']) && is_array($data['program_ids'])) {
            $pids = array_values($data['program_ids']);
            $data['program_ids'] = $pids;
            if (empty($data['program_id']) && count($pids) > 0) {
                $data['program_id'] = $pids[0];
            }
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
