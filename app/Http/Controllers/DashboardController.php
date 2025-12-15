<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics
     * Returns different data for admin and non-admin users
     */
    public function stats(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated',
            ], 401);
        }

        $isAdmin = $user->hasRole('admin');

        if ($isAdmin) {
            // Admin dashboard - show all statistics
            return response()->json([
                'success' => true,
                'data' => [
                    'role' => 'admin',
                    'company' => [
                        'landingProfile' => DB::table('landing_profiles')->count(),
                        'landingKegiatan' => DB::table('landing_kegiatan')->count(),
                        'landingProgram' => DB::table('landing_programs')->count(),
                        'landingProposal' => DB::table('landing_proposals')->count(),
                        'landingBulletin' => DB::table('landing_bulletins')->count(),
                    ],
                    'administrasi' => [
                        'kantorCabang' => $this->safeCount('kantor_cabang'),
                        'program' => $this->safeCount('program'),
                        'jabatan' => $this->safeCount('roles'),
                        'pangkat' => $this->safeCount('pangkats'),
                        'gaji' => $this->safeCount('gajis'),
                        'sop' => $this->safeCount('sops'),
                    ],
                    'konten' => [
                        'programKami' => $this->safeCount('program_kamis'),
                        'profileKami' => $this->safeCount('profile_kamis'),
                        'proposalData' => $this->safeCount('proposal_data'),
                        'bulletinData' => $this->safeCount('bulletin_data'),
                    ],
                    'userKepegawaian' => [
                        'karyawan' => $this->safeCount('karyawans'),
                        'mitra' => $this->safeCount('mitras'),
                        'donatur' => $this->safeCount('donaturs'),
                    ],
                    'operasional' => [
                        'absensi' => DB::table('absensis')->count(),
                        'remunerasi' => DB::table('remunerasis')->count(),
                    ],
                    'keuangan' => [
                        'transaksi' => $this->safeCount('transaksis'),
                        'penyaluran' => $this->safeCount('penyalurans'),
                        'pengajuanDana' => $this->safeCount('pengajuan_danas'),
                        'total' => $this->safeSum('keuangans', 'jumlah'),
                    ],
                    'laporan' => [
                        'laporanTransaksi' => $this->safeCount('laporan_transaksis'),
                        'laporanKeuangan' => $this->safeCount('laporan_keuangans'),
                    ],
                    // Transactions breakdown for admin dashboard
                    'transactions' => $this->getTransactionStats($request),
                ],
            ]);
        } else {
            // Non-admin dashboard - show limited statistics
            return response()->json([
                'success' => true,
                'data' => [
                    'role' => 'user',
                    'userKepegawaian' => [
                        'karyawan' => Schema::hasTable('karyawans') ? DB::table('karyawans')->where('user_id', $user->id)->count() : 0,
                    ],
                    'operasional' => [
                        'absensi' => Schema::hasTable('absensis') ? DB::table('absensis')->where('user_id', $user->id)->count() : 0,
                        'remunerasi' => Schema::hasTable('remunerasis') ? DB::table('remunerasis')->where('user_id', $user->id)->count() : 0,
                    ],
                ],
            ]);
        }
    }

    /**
     * Build transaction statistics grouped by jabatan and time series
     */
    protected function getTransactionStats(Request $request)
    {
        $days = $request->integer('days', 30);
        $to = now()->endOfDay();
        $from = now()->subDays(max(1, $days - 1))->startOfDay();

        // By jabatan (role)
        $byJabatan = DB::table('transaksis')
            ->select('roles.name as jabatan', DB::raw('COUNT(*) as count'), DB::raw('COALESCE(SUM(nominal),0) as total'))
            ->leftJoin('users', 'transaksis.fundraiser_id', '=', 'users.id')
            ->leftJoin('model_has_roles', function ($join) {
                $join->on('model_has_roles.model_id', '=', 'users.id')
                    ->where('model_has_roles.model_type', '=', 'App\\Models\\User');
            })
            ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->whereBetween('tanggal_transaksi', [$from->toDateString(), $to->toDateString()])
            ->groupBy('roles.name')
            ->get();

        // Time series (daily totals)
        $rawSeries = DB::table('transaksis')
            ->select(DB::raw('DATE(tanggal_transaksi) as date'), DB::raw('COALESCE(SUM(nominal),0) as total'))
            ->whereBetween('tanggal_transaksi', [$from->toDateString(), $to->toDateString()])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Build full date range series to ensure zero values are present
        $period = [];
        for ($d = $from->copy(); $d->lte($to); $d->addDay()) {
            $key = $d->toDateString();
            $period[] = [
                'date' => $key,
                'total' => isset($rawSeries[$key]) ? (float) $rawSeries[$key]->total : 0.0,
            ];
        }

        // Normalize byJabatan to include label when null
        $byJabatan = $byJabatan->map(function ($row) {
            return [
                'jabatan' => $row->jabatan ?? 'Tanpa Jabatan',
                'count' => (int) $row->count,
                'total' => (float) $row->total,
            ];
        })->values();

        return [
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'by_jabatan' => $byJabatan,
            'timeseries' => $period,
        ];
    }

    /**
     * Safe count helper which returns 0 when table does not exist or on error
     */
    protected function safeCount(string $table): int
    {
        try {
            if (!Schema::hasTable($table)) {
                return 0;
            }

            return (int) DB::table($table)->count();
        } catch (\Throwable $e) {
            // Log silently and return zero to avoid breaking dashboard
            report($e);
            return 0;
        }
    }

    /**
     * Safe sum helper which returns 0 when table does not exist or on error
     */
    protected function safeSum(string $table, string $column)
    {
        try {
            if (!Schema::hasTable($table)) {
                return 0;
            }

            return DB::table($table)->sum($column) ?? 0;
        } catch (\Throwable $e) {
            report($e);
            return 0;
        }
    }
}
