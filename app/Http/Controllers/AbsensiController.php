<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\KantorCabang;
use App\Models\TipeAbsensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AbsensiController extends Controller
{
    /**
     * Display a listing of the resource with pagination.
     */
    public function index(Request $request)
    {
        $query = Absensi::with(['user', 'tipeAbsensi', 'kantorCabang', 'creator', 'updater']);

        $authUser = auth()->user();
        if (!$authUser) {
            return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
        }

        $isAdmin = $authUser->hasAnyRole(['admin', 'superadmin', 'super-admin']);
        // Build allowed user ids for non-admin: self + subordinates
        $allowedUserIds = null;
        if (!$isAdmin) {
            $subordinateIds = $authUser->subordinates()->pluck('id')->toArray();
            $allowedUserIds = array_merge([$authUser->id], $subordinateIds);
        }

        // Search filter by user name or no induk
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('no_induk', 'like', "%{$search}%");
            });
        }

        // Filter by user_id / visibility
            if ($request->filled('user_id')) {
            $requestedUserId = $request->user_id;
            if ($isAdmin) {
                $query->where('user_id', $requestedUserId);
            } else {
                if (in_array($requestedUserId, $allowedUserIds)) {
                    $query->where('user_id', $requestedUserId);
                } else {
                    // Unauthorized to view other users; restrict to allowed set
                    $query->whereIn('user_id', $allowedUserIds);
                }
            }
        } else {
            if (!$isAdmin) {
                // By default non-admins see only themselves and their subordinates
                $query->whereIn('user_id', $allowedUserIds);
            }
        }

        // Filter by single date or range (jam_masuk)
        if ($request->filled('date')) {
            $query->whereDate('jam_masuk', $request->date);
        } else {
            if ($request->filled('date_from')) {
                $query->whereDate('jam_masuk', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('jam_masuk', '<=', $request->date_to);
            }
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by kantor_cabang_id
        if ($request->filled('kantor_cabang_id')) {
            $query->where('kantor_cabang_id', $request->kantor_cabang_id);
        }

        // Filter by tipe_absensi_id
        if ($request->filled('tipe_absensi_id')) {
            $query->where('tipe_absensi_id', $request->tipe_absensi_id);
        }
        // Sorting support: allow `sort_by` and `sort_direction` (asc|desc).
        // Supports related fields like `user.name`, `kantor_cabang.nama`, `tipe_absensi.nama`.
        $appliedSort = false;
        if ($request->filled('sort_by')) {
            $sortBy = $request->input('sort_by');
            $direction = strtolower($request->input('sort_direction', 'desc')) === 'asc' ? 'asc' : 'desc';

            if (str_contains($sortBy, '.')) {
                [$relation, $col] = explode('.', $sortBy, 2);
                switch ($relation) {
                    case 'user':
                        $query->leftJoin('users as u', 'absensis.user_id', '=', 'u.id')
                              ->orderBy("u.{$col}", $direction)
                              ->select('absensis.*');
                        $appliedSort = true;
                        break;
                    case 'kantor_cabang':
                        $query->leftJoin('kantor_cabang as k', 'absensis.kantor_cabang_id', '=', 'k.id')
                              ->orderBy("k.{$col}", $direction)
                              ->select('absensis.*');
                        $appliedSort = true;
                        break;
                    case 'tipe_absensi':
                        $query->leftJoin('tipe_absensis as t', 'absensis.tipe_absensi_id', '=', 't.id')
                              ->orderBy("t.{$col}", $direction)
                              ->select('absensis.*');
                        $appliedSort = true;
                        break;
                    default:
                        // fallback to order by column on absensis
                        $query->orderBy($sortBy, $direction);
                        $appliedSort = true;
                }
            } else {
                $query->orderBy($sortBy, $direction);
                $appliedSort = true;
            }
        }

        // Support both classic pagination and infinite scroll (start/limit)
        if ($request->filled('start') && $request->filled('limit')) {
            $start = max(0, intval($request->start));
            $limit = max(1, intval($request->limit));

            $total = $query->count();

            if (! $appliedSort) {
                $query->orderBy('jam_masuk', 'desc');
            }

            $items = $query->skip($start)
                           ->take($limit)
                           ->get();

            return response()->json([
                'success' => true,
                'data' => $items,
                'total' => $total,
            ]);
        }

        if (! isset($appliedSort) || ! $appliedSort) {
            $query->orderBy('jam_masuk', 'desc');
        }

        $absensi = $query->paginate($request->get('per_page', 10));

        return response()->json([
            'success' => true,
            'data' => $absensi->items(),
            'pagination' => [
                'current_page' => $absensi->currentPage(),
                'last_page' => $absensi->lastPage(),
                'per_page' => $absensi->perPage(),
                'total' => $absensi->total(),
            ],
        ]);
    }

    /**
     * Get user's today attendance status.
     */
    public function todayStatus(Request $request)
    {
        try {
            $user = auth()->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated',
                ], 401);
            }
            
            $userId = $request->filled('user_id') ? $request->user_id : $user->id;

            // If requesting another user's today status, ensure the requester is admin or leader of that user
            if ($request->filled('user_id') && $request->user_id != $user->id) {
                $isAdmin = $user->hasAnyRole(['admin', 'superadmin', 'super-admin']);
                $isLeaderOf = $user->subordinates()->where('id', $request->user_id)->exists();
                if (! $isAdmin && ! $isLeaderOf) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized to view other user attendance',
                    ], 403);
                }
            }

            $todayAttendance = Absensi::getTodayAttendance($userId);

            // Get user's tipe absensi and kantor cabang(s)
            $userWithRelations = \App\Models\User::with(['tipeAbsensi', 'kantorCabangs', 'kantorCabang'])->find($userId);

            return response()->json([
                'success' => true,
                'data' => [
                    'has_clock_in' => $todayAttendance !== null,
                    'has_clock_out' => $todayAttendance && $todayAttendance->jam_keluar !== null,
                    'attendance' => $todayAttendance,
                            'tipe_absensi' => $userWithRelations?->tipeAbsensi,
                    'kantor_cabang' => $userWithRelations?->kantorCabang,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Clock in (Absen Masuk).
     */
    public function clockIn(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'catatan' => 'nullable|string|max:500',
            'kantor_cabang_id' => 'nullable|uuid|exists:kantor_cabang,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = auth()->user();

        // Check if already clocked in today
        if (Absensi::hasClockInToday($user->id)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan absen masuk hari ini',
            ], 400);
        }

        // Determine kantor cabang to use: request > first pivot assignment > legacy column
        $selectedId = $request->input('kantor_cabang_id');
        $assignedIds = $user->kantorCabangs()->pluck('kantor_cabang.id')->toArray();

        if ($selectedId) {
            // ensure user is assigned to the selected kantor cabang (or has legacy single assignment)
            if (!in_array($selectedId, $assignedIds) && $user->kantor_cabang_id !== $selectedId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke kantor cabang yang dipilih',
                ], 400);
            }
            $kantorCabang = KantorCabang::find($selectedId);
        } else {
            $kantorCabang = $user->kantorCabangs()->first() ?? $user->kantorCabang;
        }

        if (!$kantorCabang) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum memiliki kantor cabang yang ditentukan',
            ], 400);
        }

        // Get user's tipe absensi
        $tipeAbsensi = $user->tipeAbsensi;

        // Calculate distance from office
        $distance = Absensi::calculateDistance(
            $request->latitude,
            $request->longitude,
            $kantorCabang->latitude,
            $kantorCabang->longitude
        );

        // Check if within allowed radius
        $allowedRadius = $kantorCabang->radius ?? 100;
        if ($distance > $allowedRadius) {
            return response()->json([
                'success' => false,
                'message' => "Anda berada di luar radius kantor ({$allowedRadius}m). Jarak Anda: " . round($distance) . "m",
                'data' => [
                    'distance' => round($distance, 2),
                    'allowed_radius' => $allowedRadius,
                    'office_location' => [
                        'latitude' => $kantorCabang->latitude,
                        'longitude' => $kantorCabang->longitude,
                    ],
                ],
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Determine status (late or on time)
            $status = 'hadir';
            if ($tipeAbsensi && $tipeAbsensi->jam_masuk) {
                $jamMasukSeharusnya = $tipeAbsensi->jam_masuk;
                $now = now()->format('H:i:s');
                if ($now > $jamMasukSeharusnya) {
                    $status = 'terlambat';
                }
            }

            $absensi = Absensi::create([
                'user_id' => $user->id,
                'tipe_absensi_id' => $tipeAbsensi?->id,
                'kantor_cabang_id' => $kantorCabang->id,
                'jam_masuk' => now(),
                'latitude_masuk' => $request->latitude,
                'longitude_masuk' => $request->longitude,
                'jarak_masuk' => round($distance, 2),
                'status' => $status,
                'catatan' => $request->catatan,
                'created_by' => $user->id,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Absen masuk berhasil',
                'data' => $absensi->load(['user', 'tipeAbsensi', 'kantorCabang']),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan absen masuk',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan',
            ], 500);
        }
    }

    /**
     * Clock out (Absen Keluar).
     */
    public function clockOut(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'alasan' => 'nullable|string|max:500',
            'catatan' => 'nullable|string|max:500',
            'kantor_cabang_id' => 'nullable|uuid|exists:kantor_cabang,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = auth()->user();

        // Get today's attendance
        $absensi = Absensi::getTodayAttendance($user->id);

        if (!$absensi) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum melakukan absen masuk hari ini',
            ], 400);
        }

        if ($absensi->jam_keluar) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan absen keluar hari ini',
            ], 400);
        }

        // Determine kantor cabang to use: request > today's absensi's kantor > first pivot assignment > legacy column
        $selectedId = $request->input('kantor_cabang_id');
        $assignedIds = $user->kantorCabangs()->pluck('kantor_cabang.id')->toArray();

        if ($selectedId) {
            if (!in_array($selectedId, $assignedIds) && $user->kantor_cabang_id !== $selectedId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke kantor cabang yang dipilih',
                ], 400);
            }
            $kantorCabang = KantorCabang::find($selectedId);
        } else {
            // Prefer kantor set on today's absensi if available
            $kantorCabang = $absensi->kantorCabang ?? $user->kantorCabangs()->first() ?? $user->kantorCabang;
        }

        if (! $kantorCabang) {
            return response()->json([
                'success' => false,
                'message' => 'Kantor cabang tidak ditemukan',
            ], 400);
        }

        // Calculate distance from office
        $distance = Absensi::calculateDistance(
            $request->latitude,
            $request->longitude,
            $kantorCabang->latitude,
            $kantorCabang->longitude
        );

        // Check if within allowed radius
        $allowedRadius = $kantorCabang->radius ?? 100;
        if ($distance > $allowedRadius) {
            return response()->json([
                'success' => false,
                'message' => "Anda berada di luar radius kantor ({$allowedRadius}m). Jarak Anda: " . round($distance) . "m",
                'data' => [
                    'distance' => round($distance, 2),
                    'allowed_radius' => $allowedRadius,
                ],
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Update clock out time
            $absensi->jam_keluar = now();
            $absensi->latitude_keluar = $request->latitude;
            $absensi->longitude_keluar = $request->longitude;
            $absensi->jarak_keluar = round($distance, 2);
            $absensi->updated_by = $user->id;

            // Calculate total work hours
            $totalHours = $absensi->calculateWorkHours();
            $absensi->total_jam_kerja = $totalHours;

            // Add reason if provided
            if ($request->filled('alasan')) {
                $absensi->alasan = $request->alasan;
            }

            // Append catatan if provided
            if ($request->filled('catatan')) {
                $existingCatatan = $absensi->catatan;
                $absensi->catatan = $existingCatatan 
                    ? $existingCatatan . "\n[Keluar] " . $request->catatan 
                    : "[Keluar] " . $request->catatan;
            }

            // Check for early leave
            $tipeAbsensi = $absensi->tipeAbsensi;
            if ($tipeAbsensi && $tipeAbsensi->jam_keluar) {
                $jamKeluarSeharusnya = $tipeAbsensi->jam_keluar;
                $jamKeluarActual = now()->format('H:i:s');
                if ($jamKeluarActual < $jamKeluarSeharusnya && $absensi->status !== 'terlambat') {
                    $absensi->status = 'pulang_awal';
                }
            }

            $absensi->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Absen keluar berhasil',
                'data' => $absensi->fresh()->load(['user', 'tipeAbsensi', 'kantorCabang']),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan absen keluar',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $absensi = Absensi::with(['user', 'tipeAbsensi', 'kantorCabang', 'creator', 'updater'])->find($id);

        if (!$absensi) {
            return response()->json([
                'success' => false,
                'message' => 'Data absensi tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $absensi,
        ]);
    }

    /**
     * Update the specified resource in storage (admin only).
     */
    public function update(Request $request, string $id)
    {
        $absensi = Absensi::find($id);

        if (!$absensi) {
            return response()->json([
                'success' => false,
                'message' => 'Data absensi tidak ditemukan',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'jam_masuk' => 'nullable|date',
            'jam_keluar' => 'nullable|date',
            'status' => 'nullable|in:hadir,terlambat,pulang_awal,tidak_hadir,izin,sakit,cuti',
            'catatan' => 'nullable|string|max:1000',
            'alasan' => 'nullable|string|max:500',
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
                'jam_masuk',
                'jam_keluar',
                'status',
                'catatan',
                'alasan',
            ]);

            $data['updated_by'] = auth()->id();

            // Recalculate work hours if both times are present
            if (isset($data['jam_masuk']) || isset($data['jam_keluar'])) {
                $jamMasuk = $data['jam_masuk'] ?? $absensi->jam_masuk;
                $jamKeluar = $data['jam_keluar'] ?? $absensi->jam_keluar;
                
                if ($jamMasuk && $jamKeluar) {
                    $diff = \Carbon\Carbon::parse($jamKeluar)->diffInMinutes(\Carbon\Carbon::parse($jamMasuk));
                    $data['total_jam_kerja'] = round($diff / 60, 2);
                }
            }

            $absensi->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Data absensi berhasil diupdate',
                'data' => $absensi->fresh()->load(['user', 'tipeAbsensi', 'kantorCabang', 'creator', 'updater']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate data absensi',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $absensi = Absensi::find($id);

        if (!$absensi) {
            return response()->json([
                'success' => false,
                'message' => 'Data absensi tidak ditemukan',
            ], 404);
        }

        try {
            $absensi->deleted_by = auth()->id();
            $absensi->save();
            $absensi->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data absensi berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data absensi',
                'error' => config('app.debug') ? $e->getMessage() : 'Terjadi kesalahan',
            ], 500);
        }
    }

    /**
     * Get attendance report/summary.
     */
    public function report(Request $request)
    {
        $query = Absensi::query();

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('jam_masuk', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('jam_masuk', '<=', $request->date_to);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by kantor cabang
        if ($request->filled('kantor_cabang_id')) {
            $query->where('kantor_cabang_id', $request->kantor_cabang_id);
        }

        // Get summary statistics
        $total = $query->count();
        $hadir = (clone $query)->where('status', 'hadir')->count();
        $terlambat = (clone $query)->where('status', 'terlambat')->count();
        $pulangAwal = (clone $query)->where('status', 'pulang_awal')->count();
        $tidakHadir = (clone $query)->where('status', 'tidak_hadir')->count();
        $izin = (clone $query)->where('status', 'izin')->count();
        $sakit = (clone $query)->where('status', 'sakit')->count();
        $cuti = (clone $query)->where('status', 'cuti')->count();

        $avgWorkHours = (clone $query)->whereNotNull('total_jam_kerja')->avg('total_jam_kerja');

        return response()->json([
            'success' => true,
            'data' => [
                'total' => $total,
                'hadir' => $hadir,
                'terlambat' => $terlambat,
                'pulang_awal' => $pulangAwal,
                'tidak_hadir' => $tidakHadir,
                'izin' => $izin,
                'sakit' => $sakit,
                'cuti' => $cuti,
                'rata_rata_jam_kerja' => round($avgWorkHours ?? 0, 2),
            ],
        ]);
    }
}
