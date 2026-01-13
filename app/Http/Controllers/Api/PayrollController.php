<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PayrollPeriod;
use App\Models\PayrollRecord;
use App\Models\PayrollItem;
use App\Services\PayrollService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PayrollController extends Controller
{
    protected $service;

    public function __construct(PayrollService $service)
    {
        $this->service = $service;

        // Permissions (Spatie)
        // Allow users with view *or* admin-like permissions (create/update/delete/manage) to list/show records
        $this->middleware('permission:view payroll|view remunerasi|manage payroll|manage remunerasi|create payroll|update payroll|delete payroll|create remunerasi|update remunerasi|delete remunerasi')->only(['index', 'show', 'showRecord', 'me']);

        $this->middleware('permission:generate payroll|generate remunerasi')->only(['generate']);
        $this->middleware('permission:manage payroll|manage remunerasi')->only(['addItem', 'updateItem', 'deleteItem', 'updateRecord', 'uploadTransferProof', 'deleteTransferProof']);
        $this->middleware('permission:transfer payroll|transfer remunerasi')->only(['transferPeriod']);
    }

    public function index(Request $request)
    {
        // include counts of records by status to show totals in the grid
        $query = PayrollPeriod::withCount([
            'records as pending_count' => function ($q) { $q->where('status', PayrollRecord::STATUS_PENDING); },
            'records as locked_count' => function ($q) { $q->where('status', PayrollRecord::STATUS_LOCKED); },
            'records as transferred_count' => function ($q) { $q->where('status', PayrollRecord::STATUS_TRANSFERRED); },
        ]);

        if ($request->filled('year')) $query->where('year', (int)$request->query('year'));
        if ($request->filled('month')) $query->where('month', (int)$request->query('month'));
        if ($request->filled('status')) $query->where('status', $request->query('status'));

        $perPage = (int)$request->query('per_page', 20);

        $paginated = $query->orderBy('year', 'desc')->orderBy('month', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $paginated->items(),
            'pagination' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
        ]);
    }

    public function generate(Request $request)
    {
        $v = Validator::make($request->all(), [
            'year' => 'required|integer|min:2000',
            'month' => 'required|integer|min:1|max:12',
        ]);

        if ($v->fails()) return response()->json(['success' => false, 'errors' => $v->errors()], 422);

        $year = (int)$request->input('year');
        $month = (int)$request->input('month');

        // Check for existing period and return clearer error when already generated/transferred
        $existing = \App\Models\PayrollPeriod::where('year', $year)->where('month', $month)->first();
        if ($existing && in_array($existing->status, ['generated', 'transferred'])) {
            return response()->json([
                'success' => false,
                'message' => 'Payroll period already generated or transferred',
                'period' => [
                    'id' => $existing->id,
                    'year' => $existing->year,
                    'month' => $existing->month,
                    'status' => $existing->status,
                ]
            ], 422);
        }

        $period = $this->service->generatePeriod($year, $month, auth()->id());

        return response()->json(['success' => true, 'data' => $period]);
    }

    public function show($id)
    {
        $period = PayrollPeriod::with(['records.employee', 'records.items'])->find($id);
        if (!$period) return response()->json(['success' => false, 'message' => 'Payroll period not found'], 404);

        $user = auth()->user();
        // Users with higher-level permissions can see all records. Treat create/update/delete/manage/transfer as admin-level access.
        $adminPerms = [
            'manage payroll','manage remunerasi','transfer payroll',
            'create payroll','update payroll','delete payroll',
            'create remunerasi','update remunerasi','delete remunerasi',
        ];

        $isAdminLike = false;
        foreach ($adminPerms as $perm) {
            if ($user->can($perm)) { $isAdminLike = true; break; }
        }

        if (! $isAdminLike) {
            $period->setRelation('records', $period->records->filter(function ($r) use ($user) {
                return $r->employee_id && $r->employee_id == $user->id;
            })->values());
        }

        return response()->json(['success' => true, 'data' => $period]);
    }

    public function showRecord($periodId, $recordId)
    {
        $record = PayrollRecord::with('items', 'employee', 'period')->where('payroll_period_id', $periodId)->where('id', $recordId)->first();
        if (!$record) return response()->json(['success' => false, 'message' => 'Record not found'], 404);

        $user = auth()->user();
        // treat create/update/delete/manage/transfer as admin-like
        $adminPerms = [
            'manage payroll','manage remunerasi','transfer payroll',
            'create payroll','update payroll','delete payroll',
            'create remunerasi','update remunerasi','delete remunerasi',
        ];
        $isAdminLike = false;
        foreach ($adminPerms as $perm) { if ($user->can($perm)) { $isAdminLike = true; break; } }

        if (! $isAdminLike) {
            // only allow viewing own record
            if (!$record->employee_id || $record->employee_id !== $user->id) {
                return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
            }
        }

        // Only attach a transient `group` for deduction rows (negative values).
        // For non-deduction items we avoid injecting a group so the frontend
        // `getGroup()` function can classify items based on its own presets
        // (this preserves existing client-side behavior for Fundraising/Lain-lain).
        if ($record->items && $record->items->count() > 0) {
            $items = $record->items->map(function ($it) {
                if (empty($it->group) && floatval($it->unit_value) < 0) {
                    $it->group = 'deduction';
                }
                return $it;
            });
            $record->setRelation('items', $items);
        }

        return response()->json(['success' => true, 'data' => $record]);
    }

    /**
     * Return the current authenticated user's latest payroll period + their record
     */
    public function me()
    {
        $user = auth()->user();

        $period = PayrollPeriod::with('records.employee')
            ->whereHas('records', function ($q) use ($user) {
                $q->where('employee_id', $user->id);
            })
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->first();

        if (!$period) {
            return response()->json(['success' => false, 'message' => 'No payroll record found for current user'], 404);
        }

        $record = $period->records->firstWhere('employee_id', $user->id);
        if (!$record) {
            return response()->json(['success' => false, 'message' => 'No payroll record found for current user'], 404);
        }

        return response()->json(['success' => true, 'data' => [
            'period' => [
                'id' => $period->id,
                'year' => $period->year,
                'month' => $period->month,
                'status' => $period->status,
            ],
            'record' => [
                'id' => $record->id,
                'employee_id' => $record->employee_id,
                'employee_name' => optional($record->employee)->name ?? null,
                'total' => $record->total ?? 0,
                'status' => $record->status ?? null,
            ],
        ]]);
    }

    /**
     * Return all payroll records belonging to the current authenticated user across periods
     */
    public function myRecords(Request $request)
    {
        $user = auth()->user();

        $query = PayrollRecord::with(['period', 'items'])
            ->where('employee_id', $user->id);

        // optional filter by year (e.g., ?year=2026)
        if ($request->filled('year')) {
            $year = (int) $request->query('year');
            $query->whereHas('period', function ($q) use ($year) {
                $q->where('year', $year);
            });
        }

        $records = $query->get()
            // sort by period year/month descending so the latest period appears first
            ->sortByDesc(function ($r) {
                $year = optional($r->period)->year ?? 0;
                $month = optional($r->period)->month ?? 0;
                return ($year * 100) + $month;
            })->values()
            ->map(function ($r) {
                $items = $r->items ?? collect([]);
                $earnings = $items->filter(function ($it) { return (int) ($it->amount ?? 0) >= 0; });
                $deductions = $items->filter(function ($it) { return (int) ($it->amount ?? 0) < 0; });
                $gross = $earnings->sum('amount');
                $deductionsTotal = abs($deductions->sum('amount'));
                $net = $gross - $deductionsTotal;

                $monthNames = [
                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                ];
                $monthNumber = optional($r->period)->month;
                $periodLabel = optional($r->period) 
                    ? ($monthNames[$monthNumber] ?? $monthNumber) . ' ' . optional($r->period)->year 
                    : null;

                return [
                    'id' => $r->id,
                    'period_id' => $r->payroll_period_id,
                    'period_label' => $periodLabel,
                    'status' => $r->status,
                    'total' => $net ?? 0,
                    'gross' => $gross ?? 0,
                    'deductions' => $deductionsTotal ?? 0,
                    'transfer_proof' => $r->transfer_proof ?? null,
                    'has_transfer_proof' => !empty($r->transfer_proof),
                ];
            });

        return response()->json(['success' => true, 'data' => $records]);
    }

    public function addItem(Request $request, $periodId, $recordId)
    {
        $record = PayrollRecord::where('payroll_period_id', $periodId)->where('id', $recordId)->first();
        if (!$record) return response()->json(['success' => false, 'message' => 'Record not found'], 404);

        // accept legacy 'fixed' qty_type by normalizing to 'multiplier'
        if ($request->input('qty_type') === 'fixed') $request->merge(['qty_type' => 'multiplier']);

        // ensure qty is an integer to avoid storing trailing decimals
        if ($request->filled('qty')) $request->merge(['qty' => (int) $request->input('qty')]);

        $v = Validator::make($request->all(), [
            'description' => 'required|string',
            'qty' => 'required|numeric|min:0',
            'qty_type' => 'required|in:percent,multiplier',
            'unit' => 'nullable|string',
            'unit_value' => 'required|numeric',
            'group' => 'nullable|string',
        ]);

        if ($v->fails()) return response()->json(['success' => false, 'errors' => $v->errors()], 422);

        $payload = $v->validated();
        $payload['payroll_record_id'] = $record->id;

        // If this item is a deduction, store its unit_value as negative so recompute uses negative amounts
        if (!empty($payload['group']) && $payload['group'] === 'deduction') {
            $payload['unit_value'] = -abs($payload['unit_value']);
        } else {
            $payload['unit_value'] = $payload['unit_value'] ?? 0;
        }

        // Ensure new items are appended rather than accepting client-provided order_index which
        // may reorder existing items unexpectedly. Compute next order_index server-side.
        $maxIndex = (int) $record->items()->max('order_index');
        $payload['order_index'] = $maxIndex + 1;

        $item = PayrollItem::create($payload);
        // include a transient `group` attribute so frontend can classify without recomputing
        $item->group = $payload['group'] ?? $this->guessGroupFromDescription($item->description);

        // recompute total (this will use PayrollService::computeItemAmount and respect negative unit_value)
        $this->service->recomputeRecordTotal($record);

        return response()->json(['success' => true, 'data' => $item]);
    }

    public function updateItem(Request $request, $periodId, $recordId, $itemId)
    {
        $record = PayrollRecord::where('payroll_period_id', $periodId)->where('id', $recordId)->first();
        if (!$record) return response()->json(['success' => false, 'message' => 'Record not found'], 404);

        $item = PayrollItem::where('payroll_record_id', $record->id)->where('id', $itemId)->first();
        if (!$item) return response()->json(['success' => false, 'message' => 'Item not found'], 404);

        // accept legacy 'fixed' qty_type by normalizing to 'multiplier'
        if ($request->input('qty_type') === 'fixed') $request->merge(['qty_type' => 'multiplier']);

        // ensure qty is an integer to avoid storing trailing decimals
        if ($request->filled('qty')) $request->merge(['qty' => (int) $request->input('qty')]);

        $v = Validator::make($request->all(), [
            'description' => 'sometimes|required|string',
            'qty' => 'sometimes|required|numeric|min:0',
            'qty_type' => 'sometimes|required|in:percent,multiplier',
            'unit' => 'nullable|string',
            'unit_value' => 'sometimes|required|numeric',
            'group' => 'nullable|string',
        ]);

        if ($v->fails()) return response()->json(['success' => false, 'errors' => $v->errors()], 422);

        $payload = $v->validated();

        // Apply group-based sign for unit_value when provided; if group is deduction, ensure unit_value stored negative
        if (isset($payload['group']) && !isset($payload['unit_value'])) {
            // group provided but no unit_value in payload — adjust existing unit_value
            if ($payload['group'] === 'deduction') {
                $item->unit_value = -abs($item->unit_value);
            } else {
                $item->unit_value = abs($item->unit_value);
            }
        }

        if (isset($payload['unit_value'])) {
            if (isset($payload['group']) && $payload['group'] === 'deduction') {
                $payload['unit_value'] = -abs($payload['unit_value']);
            } else {
                $payload['unit_value'] = $payload['unit_value'];
            }
        }
        // Do not accept client-provided order_index on update to avoid accidental reordering.
        if (isset($payload['order_index'])) unset($payload['order_index']);

        $item->fill($payload);
        $item->save();

        $this->service->recomputeRecordTotal($record);

        // include group hint in response
        $item->group = $payload['group'] ?? $this->guessGroupFromDescription($item->description);

        return response()->json(['success' => true, 'data' => $item]);
    }

    public function deleteItem($periodId, $recordId, $itemId)
    {
        $record = PayrollRecord::where('payroll_period_id', $periodId)->where('id', $recordId)->first();
        if (!$record) return response()->json(['success' => false, 'message' => 'Record not found'], 404);

        $item = PayrollItem::where('payroll_record_id', $record->id)->where('id', $itemId)->first();
        if (!$item) return response()->json(['success' => false, 'message' => 'Item not found'], 404);

        $item->delete();
        $this->service->recomputeRecordTotal($record);

        return response()->json(['success' => true, 'message' => 'Item deleted']);
    }

    /**
     * Update record attributes such as status or notes
     */
    public function updateRecord(Request $request, $periodId, $recordId)
    {
        $record = PayrollRecord::where('payroll_period_id', $periodId)->where('id', $recordId)->first();
        if (!$record) return response()->json(['success' => false, 'message' => 'Record not found'], 404);

        // Log incoming request for debugging transfer_proof uploads
        \Illuminate\Support\Facades\Log::info('PayrollController@updateRecord called', [
            'user_id' => optional(auth()->user())->id,
            'period_id' => $periodId,
            'record_id' => $recordId,
            'has_file' => $request->hasFile('transfer_proof')
        ]);

        $v = Validator::make($request->all(), [
            'status' => ['sometimes','required', \Illuminate\Validation\Rule::in(PayrollRecord::getStatuses())],
            'notes' => 'nullable|string',
            'transfer_proof' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120'
        ]);

        if ($v->fails()) {
            \Illuminate\Support\Facades\Log::warning('PayrollController@updateRecord validation failed', [
                'errors' => $v->errors()->toArray(),
                'input' => $request->except('transfer_proof')
            ]);
            return response()->json(['success' => false, 'errors' => $v->errors()], 422);
        }

        $payload = $v->validated();

        // Handle file upload if present
        if ($request->hasFile('transfer_proof')) {
            $file = $request->file('transfer_proof');
            \Illuminate\Support\Facades\Log::info('PayrollController@updateRecord storing file', [
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime' => $file->getMimeType()
            ]);
            $path = $file->store('payroll_proofs', 'public');
            \Illuminate\Support\Facades\Log::info('PayrollController@updateRecord file stored', ['path' => $path]);
            $payload['transfer_proof'] = $path;
        }

        $record->fill($payload);
        $record->save();

        \Illuminate\Support\Facades\Log::info('PayrollController@updateRecord saved record', ['record_id' => $record->id, 'transfer_proof' => $record->transfer_proof]);

        return response()->json(['success' => true, 'data' => $record]);
    }

    public function transferPeriod(Request $request, $id)
    {
        $period = PayrollPeriod::with('records')->find($id);
        if (!$period) return response()->json(['success' => false, 'message' => 'Period not found'], 404);
        if ($period->status === 'transferred') return response()->json(['success' => false, 'message' => 'Already transferred'], 422);

        // Dispatch a job to execute transfer (handles batch processing and external integrations)
        \App\Jobs\ExecutePayrollTransfer::dispatch($period->id);

        return response()->json(['success' => true, 'message' => 'Transfer job dispatched']);
    }

    /**
     * Guess a logical group for an item based on its description.
     * This is a best-effort helper used to provide a transient `group`
     * attribute in API responses so the frontend can classify items
     * without requiring additional mapping client-side.
     */
    private function guessGroupFromDescription(?string $desc)
    {
        $desc = strtolower((string) $desc);
        if ($desc === '') return 'penghasilan';

        $deductionKeywords = ['tidak masuk', 'terlambat', 'potongan', 'cuti', 'absen', 'pajak', 'pph', 'bpjs'];
        foreach ($deductionKeywords as $kw) {
            if (strpos($desc, $kw) !== false) return 'deduction';
        }

        $fundKeywords = ['fundraising', 'donasi', 'campaign', 'galang'];
        foreach ($fundKeywords as $kw) {
            if (strpos($desc, $kw) !== false) return 'fundraising';
        }

        $otherKeywords = ['lain', 'lain-lain', 'lainlain', 'lainnya'];
        foreach ($otherKeywords as $kw) {
            if (strpos($desc, $kw) !== false) return 'lain-lain';
        }

        return 'penghasilan';
    }

    /**
     * Admin-only: upload a transfer proof file for a specific record
     */
    public function uploadTransferProof(Request $request, $periodId, $recordId)
    {
        \Illuminate\Support\Facades\Log::info('PayrollController@uploadTransferProof called', [
            'user_id' => optional(auth()->user())->id,
            'period_id' => $periodId,
            'record_id' => $recordId,
            'has_file' => $request->hasFile('transfer_proof')
        ]);

        $record = PayrollRecord::where('payroll_period_id', $periodId)->where('id', $recordId)->first();
        if (!$record) return response()->json(['success' => false, 'message' => 'Record not found'], 404);

        $v = Validator::make($request->all(), [
            'transfer_proof' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'status' => ['sometimes', \Illuminate\Validation\Rule::in(PayrollRecord::getStatuses())]
        ]);

        if ($v->fails()) {
            \Illuminate\Support\Facades\Log::warning('PayrollController@uploadTransferProof validation failed', [
                'errors' => $v->errors()->toArray(),
            ]);
            return response()->json(['success' => false, 'errors' => $v->errors()], 422);
        }

        if ($request->hasFile('transfer_proof')) {
            $file = $request->file('transfer_proof');

            // if an existing proof exists, try to delete it to avoid orphaned files
            if (!empty($record->transfer_proof)) {
                try {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($record->transfer_proof);
                    \Illuminate\Support\Facades\Log::info('PayrollController@uploadTransferProof deleted previous proof', ['old_path' => $record->transfer_proof]);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::warning('Failed deleting previous transfer_proof during replace', ['path' => $record->transfer_proof, 'exception' => $e->getMessage()]);
                }
            }

            $path = $file->store('payroll_proofs', 'public');
            $record->transfer_proof = $path;
        }

        if ($request->filled('status')) {
            $record->status = $request->input('status');
        }

        $record->save();

        \Illuminate\Support\Facades\Log::info('PayrollController@uploadTransferProof saved', ['record_id' => $record->id, 'transfer_proof' => $record->transfer_proof]);

        return response()->json(['success' => true, 'data' => $record]);
    }

    /**
     * Admin-only: delete transfer proof file for a specific record
     */
    public function deleteTransferProof(Request $request, $periodId, $recordId)
    {
        \Illuminate\Support\Facades\Log::info('PayrollController@deleteTransferProof called', [
            'user_id' => optional(auth()->user())->id,
            'period_id' => $periodId,
            'record_id' => $recordId,
        ]);

        $record = PayrollRecord::where('payroll_period_id', $periodId)->where('id', $recordId)->first();
        if (!$record) return response()->json(['success' => false, 'message' => 'Record not found'], 404);

        if (empty($record->transfer_proof)) {
            return response()->json(['success' => false, 'message' => 'No transfer proof to delete'], 422);
        }

        // delete file from public disk if exists
        try {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($record->transfer_proof);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to delete transfer_proof file', ['path' => $record->transfer_proof, 'exception' => $e->getMessage()]);
        }

        $record->transfer_proof = null;
        $record->save();

        \Illuminate\Support\Facades\Log::info('PayrollController@deleteTransferProof removed proof', ['record_id' => $record->id]);

        return response()->json(['success' => true, 'data' => $record]);
    }
}
