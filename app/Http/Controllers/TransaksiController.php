<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TransaksiController extends Controller
{
    /**
     * Allowed status values.
     */
    protected array $allowedStatuses = ['pending', 'verified', 'cancelled'];

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Transaksi::with([
            'kantorCabang:id,nama',
            'donatur:id,nama',
            'program:id,nama_program',
            'fundraiser:id,name',
        ]);

        // Filter berdasarkan user yang login (kecuali admin/superadmin)
        $user = auth()->user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
        }

        $isAdmin = $user->hasAnyRole(['admin', 'superadmin', 'super-admin']);
        if (! $isAdmin) {
            $subIds = $user->subordinates()->pluck('id')->toArray();
            $allowed = array_merge([$user->id], $subIds);
            $query->whereIn('created_by', $allowed);
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                    ->orWhere('keterangan', 'like', "%{$search}%")
                    ->orWhereHas('donatur', fn ($q) => $q->where('nama', 'like', "%{$search}%"))
                    ->orWhereHas('program', fn ($q) => $q->where('nama_program', 'like', "%{$search}%"))
                    ->orWhereHas('fundraiser', fn ($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('donatur')) {
            $query->whereHas('donatur', fn ($q) => $q->where('nama', 'like', "%{$request->donatur}%"));
        }

        if ($request->filled('donatur_id')) {
            $query->where('donatur_id', $request->donatur_id);
        }

        if ($request->filled('fundraiser')) {
            $query->whereHas('fundraiser', fn ($q) => $q->where('name', 'like', "%{$request->fundraiser}%"));
        }

        if ($request->filled('fundraiser_id')) {
            $query->where('fundraiser_id', $request->fundraiser_id);
        }

        if ($request->filled('program')) {
            $query->whereHas('program', fn ($q) => $q->where('nama_program', 'like', "%{$request->program}%"));
        }

        if ($request->filled('program_id')) {
            $query->where('program_id', $request->program_id);
        }

        // Support both single-date filter (`tanggal=YYYY-MM-DD`) and
        // a date range via `tanggal_from` and `tanggal_to`.
        if ($request->filled('tanggal_from') && $request->filled('tanggal_to')) {
            $from = $request->input('tanggal_from');
            $to = $request->input('tanggal_to');
            $query->whereBetween('tanggal_transaksi', [$from, $to]);
        } elseif ($request->filled('tanggal')) {
            $query->whereDate('tanggal_transaksi', $request->tanggal);
        }

        if ($request->filled('status')) {
            $status = $request->input('status');
            if (in_array($status, $this->allowedStatuses, true)) {
                $query->where('status', $status);
            }
        }

        if ($request->filled('kantor_cabang_id')) {
            $query->where('kantor_cabang_id', $request->kantor_cabang_id);
        }

        $transaksis = $query
            ->orderByDesc('tanggal_transaksi')
            ->orderByDesc('created_at')
            ->paginate($request->integer('per_page', 20));

        $transaksis->getCollection()->transform(function (Transaksi $transaksi) {
            return $this->transformTransaksi($transaksi);
        });

        return response()->json([
            'success' => true,
            'data' => $transaksis->items(),
            'pagination' => [
                'current_page' => $transaksis->currentPage(),
                'last_page' => $transaksis->lastPage(),
                'per_page' => $transaksis->perPage(),
                'total' => $transaksis->total(),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kantor_cabang_id' => 'required|uuid|exists:kantor_cabang,id',
            'donatur_id' => 'required|uuid|exists:donaturs,id',
            'program_id' => 'required|uuid|exists:program,id',
            'fundraiser_id' => 'nullable|exists:users,id',
            'nominal' => 'required|numeric|min:0',
            'tanggal_transaksi' => 'required|date',
            'keterangan' => 'nullable|string|max:1000',
            'status' => ['nullable', 'string', Rule::in($this->allowedStatuses)],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $data = $this->sanitizePayload($validator->validated());
            $data['kode'] = $this->generateKode();
            $data['status'] = $data['status'] ?? 'verified';
            $data['created_by'] = auth()->id();
            
            // Auto-set fundraiser_id to current user if not provided
            if (empty($data['fundraiser_id'])) {
                $data['fundraiser_id'] = auth()->id();
            }

            $transaksi = Transaksi::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil ditambahkan',
                'data' => $this->transformTransaksi($transaksi->fresh([
                    'kantorCabang:id,nama',
                    'donatur:id,nama',
                    'program:id,nama_program',
                    'fundraiser:id,name',
                ])),
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan transaksi',
                'error' => config('app.debug') ? $th->getMessage() : 'Terjadi kesalahan',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $query = Transaksi::with([
            'kantorCabang:id,nama',
            'donatur:id,nama',
            'program:id,nama_program',
            'fundraiser:id,name',
        ]);

        // Filter berdasarkan user yang login (kecuali admin/superadmin)
        $user = auth()->user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
        }

        $isAdmin = $user->hasAnyRole(['admin', 'superadmin', 'super-admin']);
        if (! $isAdmin) {
            $subIds = $user->subordinates()->pluck('id')->toArray();
            $allowed = array_merge([$user->id], $subIds);
            $query->whereIn('created_by', $allowed);
        }

        $transaksi = $query->find($id);

        if (! $transaksi) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $this->transformTransaksi($transaksi),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $query = Transaksi::query();

        // Filter berdasarkan user yang login (kecuali admin/superadmin)
        $user = auth()->user();
        if (! $user->hasAnyRole(['admin', 'superadmin', 'super-admin'])) {
            $query->where('created_by', $user->id);
        }

        $transaksi = $query->find($id);

        if (! $transaksi) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'kantor_cabang_id' => 'required|uuid|exists:kantor_cabang,id',
            'donatur_id' => 'required|uuid|exists:donaturs,id',
            'program_id' => 'required|uuid|exists:program,id',
            'fundraiser_id' => 'nullable|exists:users,id',
            'nominal' => 'required|numeric|min:0',
            'tanggal_transaksi' => 'required|date',
            'keterangan' => 'nullable|string|max:1000',
            'status' => ['nullable', 'string', Rule::in($this->allowedStatuses)],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $data = $this->sanitizePayload($validator->validated());
            $data['updated_by'] = auth()->id();
            
            // Auto-set fundraiser_id to current user if not provided and transaksi doesn't have one
            if (empty($data['fundraiser_id']) && empty($transaksi->fundraiser_id)) {
                $data['fundraiser_id'] = auth()->id();
            }

            $transaksi->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil diperbarui',
                'data' => $this->transformTransaksi($transaksi->fresh([
                    'kantorCabang:id,nama',
                    'donatur:id,nama',
                    'program:id,nama_program',
                    'fundraiser:id,name',
                ])),
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui transaksi',
                'error' => config('app.debug') ? $th->getMessage() : 'Terjadi kesalahan',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $query = Transaksi::query();

        // Filter berdasarkan user yang login (kecuali admin/superadmin)
        $user = auth()->user();
        if (! $user->hasAnyRole(['admin', 'superadmin', 'super-admin'])) {
            $query->where('created_by', $user->id);
        }

        $transaksi = $query->find($id);

        if (! $transaksi) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan',
            ], 404);
        }

        try {
            $transaksi->deleted_by = auth()->id();
            $transaksi->save();
            $transaksi->delete();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dihapus',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus transaksi',
                'error' => config('app.debug') ? $th->getMessage() : 'Terjadi kesalahan',
            ], 500);
        }
    }

    /**
     * Generate the next sequential kode.
     */
    protected function generateKode(): string
    {
        $prefix = 'TRX';
        $date = now()->format('Ymd');

        $lastTransaksi = Transaksi::withTrashed()
            ->where('kode', 'like', "{$prefix}{$date}%")
            ->orderByRaw('CAST(SUBSTRING(kode, ' . (strlen($prefix) + strlen($date) + 1) . ') AS UNSIGNED) DESC')
            ->first();

        if ($lastTransaksi && preg_match("/{$prefix}{$date}(\d+)/", $lastTransaksi->kode, $matches)) {
            $nextNumber = ((int) $matches[1]) + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . $date . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Transform transaksi instance for API responses.
     */
    protected function transformTransaksi(Transaksi $transaksi): array
    {
        return [
            'id' => $transaksi->id,
            'kode' => $transaksi->kode,
            'nominal' => (float) $transaksi->nominal,
            'nominal_formatted' => 'Rp ' . number_format((float) $transaksi->nominal, 0, ',', '.'),
            'tanggal_transaksi' => optional($transaksi->tanggal_transaksi)->toDateString(),
            'keterangan' => $transaksi->keterangan,
            'status' => $transaksi->status,
            'kantor_cabang_id' => $transaksi->kantor_cabang_id,
            'kantor_cabang' => $transaksi->kantorCabang ? [
                'id' => $transaksi->kantorCabang->id,
                'nama' => $transaksi->kantorCabang->nama,
            ] : null,
            'donatur_id' => $transaksi->donatur_id,
            'donatur' => $transaksi->donatur ? [
                'id' => $transaksi->donatur->id,
                'nama' => $transaksi->donatur->nama,
            ] : null,
            'program_id' => $transaksi->program_id,
            'program' => $transaksi->program ? [
                'id' => $transaksi->program->id,
                'nama' => $transaksi->program->nama_program,
            ] : null,
            'fundraiser_id' => $transaksi->fundraiser_id,
            'fundraiser' => $transaksi->fundraiser ? [
                'id' => $transaksi->fundraiser->id,
                'nama' => $transaksi->fundraiser->name,
            ] : null,
            'created_at' => optional($transaksi->created_at)->toIso8601String(),
            'updated_at' => optional($transaksi->updated_at)->toIso8601String(),
        ];
    }

    /**
     * Sanitize payload before persistence.
     */
    protected function sanitizePayload(array $data): array
    {
        if (array_key_exists('keterangan', $data) && is_string($data['keterangan'])) {
            $data['keterangan'] = trim($data['keterangan']);
            if ($data['keterangan'] === '') {
                $data['keterangan'] = null;
            }
        }

        $nullableKeys = ['fundraiser_id', 'keterangan', 'status'];

        foreach ($nullableKeys as $key) {
            if (array_key_exists($key, $data)) {
                $value = $data[$key];
                if (is_string($value)) {
                    $value = trim($value);
                }

                $data[$key] = ($value === '' || $value === null) ? null : $value;
            }
        }

        if (array_key_exists('status', $data) && empty($data['status'])) {
            unset($data['status']);
        }

        return $data;
    }
}
