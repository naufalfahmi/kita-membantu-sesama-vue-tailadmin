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
        $this->middleware('permission:manage payroll|manage remunerasi')->only(['addItem', 'updateItem', 'deleteItem', 'updateRecord']);
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
    public function myRecords()
    {
        $user = auth()->user();

        $records = PayrollRecord::with('period')
            ->where('employee_id', $user->id)
            ->orderByDesc('id')
            ->get()
            ->map(function ($r) {
                return [
                    'id' => $r->id,
                    'period_id' => $r->payroll_period_id,
                    'period_label' => optional($r->period) ? optional($r->period)->month . '/' . optional($r->period)->year : null,
                    'status' => $r->status,
                    'total' => $r->total_amount ?? 0,
                ];
            });

        return response()->json(['success' => true, 'data' => $records]);
    }

    public function addItem(Request $request, $periodId, $recordId)
    {
        $record = PayrollRecord::where('payroll_period_id', $periodId)->where('id', $recordId)->first();
        if (!$record) return response()->json(['success' => false, 'message' => 'Record not found'], 404);
        if ($record->status !== PayrollRecord::STATUS_PENDING) return response()->json(['success' => false, 'message' => 'Cannot edit locked or transferred records'], 403);

        // accept legacy 'fixed' qty_type by normalizing to 'multiplier'
        if ($request->input('qty_type') === 'fixed') $request->merge(['qty_type' => 'multiplier']);

        // ensure qty is an integer to avoid storing trailing decimals
        if ($request->filled('qty')) $request->merge(['qty' => (int) $request->input('qty')]);

        $v = Validator::make($request->all(), [
            'description' => 'required|string',
            'qty' => 'required|integer|min:0',
            'qty_type' => 'required|in:percent,multiplier',
            'unit' => 'nullable|string',
            'unit_value' => 'required|numeric|min:0',
        ]);

        if ($v->fails()) return response()->json(['success' => false, 'errors' => $v->errors()], 422);

        $payload = array_merge($v->validated(), ['payroll_record_id' => $record->id]);

        // Note: base_item_id support has been removed; percent now uses the item's own unit_value


        $item = PayrollItem::create($payload);

        // recompute total
        $this->service->recomputeRecordTotal($record);

        return response()->json(['success' => true, 'data' => $item]);
    }

    public function updateItem(Request $request, $periodId, $recordId, $itemId)
    {
        $record = PayrollRecord::where('payroll_period_id', $periodId)->where('id', $recordId)->first();
        if (!$record) return response()->json(['success' => false, 'message' => 'Record not found'], 404);
        if ($record->status !== PayrollRecord::STATUS_PENDING) return response()->json(['success' => false, 'message' => 'Cannot edit locked or transferred records'], 403);

        $item = PayrollItem::where('payroll_record_id', $record->id)->where('id', $itemId)->first();
        if (!$item) return response()->json(['success' => false, 'message' => 'Item not found'], 404);

        // accept legacy 'fixed' qty_type by normalizing to 'multiplier'
        if ($request->input('qty_type') === 'fixed') $request->merge(['qty_type' => 'multiplier']);

        // ensure qty is an integer to avoid storing trailing decimals
        if ($request->filled('qty')) $request->merge(['qty' => (int) $request->input('qty')]);

        $v = Validator::make($request->all(), [
            'description' => 'sometimes|required|string',
            'qty' => 'sometimes|required|integer|min:0',
            'qty_type' => 'sometimes|required|in:percent,multiplier',
            'unit' => 'nullable|string',
            'unit_value' => 'sometimes|required|numeric|min:0',
        ]);

        if ($v->fails()) return response()->json(['success' => false, 'errors' => $v->errors()], 422);

        $payload = $v->validated();
        // Note: base_item_id no longer used; percent uses item's unit_value


        $item->fill($payload);
        $item->save();

        $this->service->recomputeRecordTotal($record);

        return response()->json(['success' => true, 'data' => $item]);
    }

    public function deleteItem($periodId, $recordId, $itemId)
    {
        $record = PayrollRecord::where('payroll_period_id', $periodId)->where('id', $recordId)->first();
        if (!$record) return response()->json(['success' => false, 'message' => 'Record not found'], 404);
        if ($record->status !== PayrollRecord::STATUS_PENDING) return response()->json(['success' => false, 'message' => 'Cannot edit locked or transferred records'], 403);

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

        $v = Validator::make($request->all(), [
            'status' => ['sometimes','required', \Illuminate\Validation\Rule::in(PayrollRecord::getStatuses())],
            'notes' => 'nullable|string',
        ]);

        if ($v->fails()) return response()->json(['success' => false, 'errors' => $v->errors()], 422);

        $payload = $v->validated();

        $record->fill($payload);
        $record->save();

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
}
