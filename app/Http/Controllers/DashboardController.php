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
                        'remunerasi' => $this->safeCount('remunerasis'),
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
            // Admin Cabang role - provide branch-specific dashboard
            $isAdminCabang = false;
            try {
                $roleNames = $user->roles()->pluck('name')->map(fn($n) => strtolower(trim($n)))->toArray();
                foreach ($roleNames as $rn) {
                    if (str_contains($rn, 'admin cabang') || str_contains($rn, 'admincabang') || str_contains($rn, 'admin-cabang')) {
                        $isAdminCabang = true;
                        break;
                    }
                }
            } catch (\Throwable $e) {
                $isAdminCabang = $user->hasRole('admin cabang') || $user->hasRole('admin-cabang');
            }

            if ($isAdminCabang) {
                $branchStats = $this->getAdminCabangStats($request, $user);
                return response()->json([
                    'success' => true,
                    'data' => array_merge(['role' => 'admin_cabang'], $branchStats),
                ]);
            }

            // Director Fundraising: treat like admin_cabang but across multiple branches assigned to the user
            $isDirectorFund = false;
            try {
                $roleNames = $user->roles()->pluck('name')->map(fn($n) => strtolower(trim($n)))->toArray();
                foreach ($roleNames as $rn) {
                    if (str_contains($rn, 'direktur') && str_contains($rn, 'fundr')) {
                        $isDirectorFund = true;
                        break;
                    }
                }
            } catch (\Throwable $e) {
                $isDirectorFund = $user->hasRole('direktur fundraising') || $user->hasRole('director fundraising');
            }

            if ($isDirectorFund) {
                // collect branch ids from relation/kantor_cabang_user
                $branchIds = [];
                try {
                    $branchIds = $user->kantorCabangs()->pluck('id')->toArray();
                } catch (\Throwable $e) {
                    $branchIds = [];
                }
                if (!empty($branchIds)) {
                    $multi = $this->getAdminCabangStatsForBranchIds($request, $branchIds);
                    return response()->json([
                        'success' => true,
                        'data' => array_merge(['role' => 'admin_cabang'], $multi),
                    ]);
                }
            }

            // Mitra role - detect linked mitra profile
            $isMitra = false;
            try {
                $isMitra = (bool) $user->mitra()->exists();
            } catch (\Throwable $e) {
                $isMitra = $user->hasRole('mitra') || $user->hasRole('partner');
            }

            if ($isMitra) {
                $mitraStats = $this->getMitraStats($request, $user);
                return response()->json([
                    'success' => true,
                    'data' => array_merge(['role' => 'mitra'], $mitraStats),
                ]);
            }

            // Fundraising role - provide fundraising-specific dashboard
            // Accept common role name variants/spellings (fundraising, fundrising, fundraiser)
            $isFundraising = false;
            try {
                $roleNames = $user->roles()->pluck('name')->map(fn($n) => strtolower(trim($n)))->toArray();
                foreach ($roleNames as $rn) {
                    if (str_starts_with($rn, 'fundr') || str_contains($rn, 'fundr')) {
                        $isFundraising = true;
                        break;
                    }
                }
            } catch (\Throwable $e) {
                $isFundraising = $user->hasRole('fundraising');
            }

            if ($isFundraising) {
                $fundraising = $this->getFundraisingStats($request, $user);
                return response()->json([
                    'success' => true,
                    'data' => array_merge(['role' => 'fundraising'], $fundraising),
                ]);
            }

            // Non-admin default dashboard - show limited statistics
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
     * Build fundraising-specific statistics for a fundraiser user
     */
    protected function getFundraisingStats(Request $request, $user)
    {
        $days = $request->integer('days', 30);
        $to = now()->endOfDay();
        $from = now()->subDays(max(1, $days - 1))->startOfDay();

        // Current period totals for this fundraiser
        $currentTotal = DB::table('transaksis')
            ->where('fundraiser_id', $user->id)
            ->whereBetween('tanggal_transaksi', [$from->toDateString(), $to->toDateString()])
            ->sum('nominal');

        // Previous period (same length immediately before current period)
        $prevFrom = $from->copy()->subDays($days)->startOfDay();
        $prevTo = $from->copy()->subDay()->endOfDay();

        $previousTotal = DB::table('transaksis')
            ->where('fundraiser_id', $user->id)
            ->whereBetween('tanggal_transaksi', [$prevFrom->toDateString(), $prevTo->toDateString()])
            ->sum('nominal');

        // Growth calculation
        $growth = null;
        if ($previousTotal == 0) {
            $growth = $currentTotal > 0 ? 100 : 0;
        } else {
            $growth = (($currentTotal - $previousTotal) / max(1, $previousTotal)) * 100;
        }

        // Donor count and average donation
        $donorCount = DB::table('transaksis')
            ->where('fundraiser_id', $user->id)
            ->whereNotNull('donatur_id')
            ->distinct()
            ->count('donatur_id');

        $avgDonation = DB::table('transaksis')
            ->where('fundraiser_id', $user->id)
            ->avg('nominal') ?? 0;

        // Recent transactions
        $recent = DB::table('transaksis')
            ->where('fundraiser_id', $user->id)
            ->orderBy('tanggal_transaksi', 'desc')
            ->limit(10)
            ->get();

        // Time series (daily) for the period
        $rawSeries = DB::table('transaksis')
            ->select(DB::raw('DATE(tanggal_transaksi) as date'), DB::raw('COALESCE(SUM(nominal),0) as total'))
            ->where('fundraiser_id', $user->id)
            ->whereBetween('tanggal_transaksi', [$from->toDateString(), $to->toDateString()])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $period = [];
        for ($d = $from->copy(); $d->lte($to); $d->addDay()) {
            $key = $d->toDateString();
            $period[] = [
                'date' => $key,
                'total' => isset($rawSeries[$key]) ? (float) $rawSeries[$key]->total : 0.0,
            ];
        }

        // Per campaign (program) breakdown
        $perCampaign = [];
        if (Schema::hasTable('programs') || Schema::hasTable('program')) {
            $programTable = Schema::hasTable('programs') ? 'programs' : 'program';
            $perCampaign = DB::table('transaksis')
                ->select('program_id', DB::raw('COALESCE(SUM(nominal),0) as total'), DB::raw('COUNT(*) as count'))
                ->where('fundraiser_id', $user->id)
                ->groupBy('program_id')
                ->get()
                ->map(function ($row) use ($programTable) {
                    $program = DB::table($programTable)->where('id', $row->program_id)->first();
                    return [
                        'program_id' => $row->program_id,
                        'program_name' => $program->nama ?? ($program->title ?? 'Unknown'),
                        'total' => (float) $row->total,
                        'count' => (int) $row->count,
                    ];
                })->values();
        }

        // Channel performance (if column exists)
        $channelPerformance = [];
        if (Schema::hasColumn('transaksis', 'channel')) {
            $channelPerformance = DB::table('transaksis')
                ->select('channel', DB::raw('COALESCE(SUM(nominal),0) as total'), DB::raw('COUNT(*) as count'))
                ->where('fundraiser_id', $user->id)
                ->groupBy('channel')
                ->get();
        }

        // Conversion rate placeholder (requires funnel/visitor data)
        $conversionRate = null;

        return [
            'fundraising' => [
                'total_collected' => (float) $currentTotal,
                'donor_count' => (int) $donorCount,
                'avg_donation' => (float) $avgDonation,
                'growth_percent' => (float) $growth,
                'recent_transactions' => $recent,
                'timeseries' => $period,
                'per_campaign' => $perCampaign,
                'channel_performance' => $channelPerformance,
                'conversion_rate' => $conversionRate,
            ],
        ];
    }

    /**
     * Build Admin Cabang (branch) specific statistics
     */
    protected function getAdminCabangStats(Request $request, $user)
    {
        // determine branch id from pivot or legacy column
        $branchId = null;
        try {
            $first = $user->kantorCabangs->first();
            $branchId = $first ? $first->id : ($user->kantor_cabang_id ?? null);
        } catch (\Throwable $e) {
            $branchId = $user->kantor_cabang_id ?? null;
        }

        // If no branch assigned, return empty metrics
        if (! $branchId) {
            return ['admin_cabang' => [
                'mitra_count' => 0,
                'donatur_count' => 0,
                'absensi_today' => 0,
                'payroll_records' => 0,
                'transaksi_count' => 0,
                'transaksi_total' => 0,
                'timeseries' => [],
                'per_mitra' => [],
                'recent_transactions' => [],
            ]];
        }

        // Mitra & Donatur counts scoped to branch (tolerant about branch column)
        $mitraCount = 0;
        $donaturCount = 0;
        if (Schema::hasTable('mitras')) {
            $col = $this->detectBranchColumn('mitras');
            if ($col) {
                $mitraCount = DB::table('mitras')->where($col, $branchId)->count();
            }
        }
        if (Schema::hasTable('donaturs')) {
            $col = $this->detectBranchColumn('donaturs');
            if ($col) {
                $donaturCount = DB::table('donaturs')->where($col, $branchId)->count();
            }
        }

        // Absensi today - be tolerant about the date column name
        $absensiToday = 0;
        if (Schema::hasTable('absensis')) {
            $dateColumn = null;
            try {
                $cols = Schema::getColumnListing('absensis');
                $candidates = ['tanggal', 'tanggal_absen', 'tanggal_absensi', 'date', 'created_at', 'waktu', 'tanggal_masuk'];
                foreach ($candidates as $cand) {
                    if (in_array($cand, $cols)) {
                        $dateColumn = $cand;
                        break;
                    }
                }
            } catch (\Throwable $e) {
                $dateColumn = null;
            }

            if (! $dateColumn) {
                $dateColumn = 'created_at';
            }

            $branchCol = $this->detectBranchColumn('absensis');
            if ($branchCol) {
                $absensiToday = DB::table('absensis')
                    ->where($branchCol, $branchId)
                    ->whereDate($dateColumn, now()->toDateString())
                    ->count();
            } else {
                $absensiToday = 0;
            }
        }

        // Payroll records count for branch (best-effort)
        $payrollRecords = 0;
        if (Schema::hasTable('payroll_records')) {
            $col = $this->detectBranchColumn('payroll_records');
            if ($col) {
                $payrollRecords = DB::table('payroll_records')->where($col, $branchId)->count();
            }
        }

        // Transactions scoped to branch
        $transaksiQuery = null;
        $transaksiCount = 0;
        $transaksiTotal = 0;
        if (Schema::hasTable('transaksis')) {
            $col = $this->detectBranchColumn('transaksis');
            if ($col) {
                $transaksiQuery = DB::table('transaksis')->where($col, $branchId);
                $transaksiCount = $transaksiQuery->count();
                $transaksiTotal = $transaksiQuery->sum('nominal');
            }
        }

        // Branch name (if available)
        $branchName = null;
        if (Schema::hasTable('kantor_cabangs')) {
            $b = DB::table('kantor_cabangs')->where('id', $branchId)->first();
            if ($b) {
                $branchName = $b->nama ?? ($b->name ?? ($b->title ?? null));
            }
        } elseif (Schema::hasTable('kantor_cabang')) {
            $b = DB::table('kantor_cabang')->where('id', $branchId)->first();
            if ($b) {
                $branchName = $b->nama ?? ($b->name ?? ($b->title ?? null));
            }
        }

        // Timeseries (last N days)
        $days = $request->integer('days', 30);
        $to = now()->endOfDay();
        $from = now()->subDays(max(1, $days - 1))->startOfDay();

        $rawSeries = [];
        if ($transaksiQuery) {
            $col = $this->detectBranchColumn('transaksis');
            $rawSeries = DB::table('transaksis')
                ->select(DB::raw('DATE(tanggal_transaksi) as date'), DB::raw('COALESCE(SUM(nominal),0) as total'))
                ->where($col, $branchId)
                ->whereBetween('tanggal_transaksi', [$from->toDateString(), $to->toDateString()])
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->keyBy('date');
        }

        $period = [];
        for ($d = $from->copy(); $d->lte($to); $d->addDay()) {
            $key = $d->toDateString();
            $period[] = [
                'date' => $key,
                'total' => isset($rawSeries[$key]) ? (float) $rawSeries[$key]->total : 0.0,
            ];
        }

        // Per-mitra breakdown
        $perMitra = [];
        if (Schema::hasTable('mitras') && Schema::hasTable('transaksis')) {
            $col = $this->detectBranchColumn('transaksis');
            if ($col) {
                $perMitra = DB::table('transaksis')
                    ->select('mitra_id', DB::raw('COALESCE(SUM(nominal),0) as total'), DB::raw('COUNT(*) as count'))
                    ->where($col, $branchId)
                    ->groupBy('mitra_id')
                    ->get()
                    ->map(function ($row) {
                        $mitra = DB::table('mitras')->where('id', $row->mitra_id)->first();
                        return [
                            'mitra_id' => $row->mitra_id,
                            'mitra_name' => $mitra->nama ?? 'Unknown',
                            'total' => (float) $row->total,
                            'count' => (int) $row->count,
                        ];
                    })->values();
            }
        }

        // Recent transactions (include donor name when available)
        $recent = [];
        if (Schema::hasTable('transaksis')) {
            $branchCol = $this->detectBranchColumn('transaksis');
            if ($branchCol) {
                $query = DB::table('transaksis as t')->where('t.' . $branchCol, $branchId);
            } else {
                $query = null;
            }

            $selects = ['t.*'];
            if ($query) {
                if (Schema::hasTable('donaturs')) {
                    $query = $query->leftJoin('donaturs as d', 't.donatur_id', '=', 'd.id');
                    $selects[] = DB::raw("COALESCE(d.nama, '') as donatur_name");
                }
                if (Schema::hasTable('mitras')) {
                    $query = $query->leftJoin('mitras as m', 't.mitra_id', '=', 'm.id');
                    $selects[] = DB::raw("COALESCE(m.nama, '') as mitra_name");
                }

                $query = $query->select($selects);
            }

            if ($query) {
                $recent = $query->orderBy('t.tanggal_transaksi', 'desc')->limit(10)->get();
            } else {
                $recent = [];
            }
        }

        return ['admin_cabang' => [
            'mitra_count' => (int) $mitraCount,
            'donatur_count' => (int) $donaturCount,
            'absensi_today' => (int) $absensiToday,
            'payroll_records' => (int) $payrollRecords,
            'transaksi_count' => (int) $transaksiCount,
            'transaksi_total' => (float) $transaksiTotal,
            'branch_name' => $branchName,
            'timeseries' => $period,
            'per_mitra' => $perMitra,
            'recent_transactions' => $recent,
        ]];
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
     * Build Mitra-specific statistics for a mitra user
     */
    protected function getMitraStats(Request $request, $user)
    {
        // attempt to resolve mitra id from relation or user_id
        $mitraId = null;
        try {
            $mitra = $user->mitra()->first();
            $mitraId = $mitra ? $mitra->id : ($user->mitra_id ?? null);
        } catch (\Throwable $e) {
            $mitraId = $user->mitra_id ?? null;
        }

        if (! $mitraId) {
            return ['mitra' => [
                'total_transaksi' => 0,
                'total_fee' => 0,
                'donor_count' => 0,
                'transaksi_count' => 0,
            ]];
        }

        // Total transaksi (sum of nominal)
        $totalTransaksi = Schema::hasTable('transaksis') ? DB::table('transaksis')->where('mitra_id', $mitraId)->sum('nominal') : 0;

        // Total fee - detect fee-like column
        $totalFee = 0;
        if (Schema::hasTable('transaksis')) {
            $feeColCandidates = ['fee', 'komisi', 'commission', 'admin_fee', 'fee_nominal'];
            $cols = Schema::getColumnListing('transaksis');
            $feeCol = null;
            foreach ($feeColCandidates as $c) {
                if (in_array($c, $cols)) {
                    $feeCol = $c;
                    break;
                }
            }
            if ($feeCol) {
                $totalFee = DB::table('transaksis')->where('mitra_id', $mitraId)->sum($feeCol) ?: 0;
            }
        }

        // Donor count (distinct donatur_id)
        $donorCount = 0;
        if (Schema::hasTable('transaksis')) {
            $donorCount = DB::table('transaksis')->where('mitra_id', $mitraId)->whereNotNull('donatur_id')->distinct()->count('donatur_id');
        }

        // Transaksi count
        $transaksiCount = Schema::hasTable('transaksis') ? DB::table('transaksis')->where('mitra_id', $mitraId)->count() : 0;

        return ['mitra' => [
            'total_transaksi' => (float) $totalTransaksi,
            'total_fee' => (float) $totalFee,
            'donor_count' => (int) $donorCount,
            'transaksi_count' => (int) $transaksiCount,
        ]];
    }

    /**
     * Build Admin Cabang stats for multiple branch ids (aggregation)
     */
    protected function getAdminCabangStatsForBranchIds(Request $request, array $branchIds)
    {
        // normalize branch ids
        $branchIds = array_values(array_unique($branchIds));

        // Mitra & Donatur counts scoped to branches
        $mitraCount = 0;
        $donaturCount = 0;
        if (Schema::hasTable('mitras')) {
            $col = $this->detectBranchColumn('mitras');
            if ($col) {
                $mitraCount = DB::table('mitras')->whereIn($col, $branchIds)->count();
            }
        }
        if (Schema::hasTable('donaturs')) {
            $col = $this->detectBranchColumn('donaturs');
            if ($col) {
                $donaturCount = DB::table('donaturs')->whereIn($col, $branchIds)->count();
            }
        }

        // Absensi today (aggregate)
        $absensiToday = 0;
        if (Schema::hasTable('absensis')) {
            $dateColumn = null;
            try {
                $cols = Schema::getColumnListing('absensis');
                $candidates = ['tanggal', 'tanggal_absen', 'tanggal_absensi', 'date', 'created_at', 'waktu', 'tanggal_masuk'];
                foreach ($candidates as $cand) {
                    if (in_array($cand, $cols)) {
                        $dateColumn = $cand;
                        break;
                    }
                }
            } catch (\Throwable $e) {
                $dateColumn = null;
            }
            if (! $dateColumn) {
                $dateColumn = 'created_at';
            }
            $branchCol = $this->detectBranchColumn('absensis');
            if ($branchCol) {
                $absensiToday = DB::table('absensis')
                    ->whereIn($branchCol, $branchIds)
                    ->whereDate($dateColumn, now()->toDateString())
                    ->count();
            }
        }

        // Payroll records
        $payrollRecords = 0;
        if (Schema::hasTable('payroll_records')) {
            $col = $this->detectBranchColumn('payroll_records');
            if ($col) {
                $payrollRecords = DB::table('payroll_records')->whereIn($col, $branchIds)->count();
            }
        }

        // Transactions scoped to branches
        $transaksiCount = 0;
        $transaksiTotal = 0;
        if (Schema::hasTable('transaksis')) {
            $col = $this->detectBranchColumn('transaksis');
            if ($col) {
                $q = DB::table('transaksis')->whereIn($col, $branchIds);
                $transaksiCount = $q->count();
                $transaksiTotal = $q->sum('nominal');
            }
        }

        // Time series
        $days = $request->integer('days', 30);
        $to = now()->endOfDay();
        $from = now()->subDays(max(1, $days - 1))->startOfDay();

        $rawSeries = [];
        if (Schema::hasTable('transaksis')) {
            $col = $this->detectBranchColumn('transaksis');
            if ($col) {
                $rawSeries = DB::table('transaksis')
                    ->select(DB::raw('DATE(tanggal_transaksi) as date'), DB::raw('COALESCE(SUM(nominal),0) as total'))
                    ->whereIn($col, $branchIds)
                    ->whereBetween('tanggal_transaksi', [$from->toDateString(), $to->toDateString()])
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get()
                    ->keyBy('date');
            }
        }

        $period = [];
        for ($d = $from->copy(); $d->lte($to); $d->addDay()) {
            $key = $d->toDateString();
            $period[] = [
                'date' => $key,
                'total' => isset($rawSeries[$key]) ? (float) $rawSeries[$key]->total : 0.0,
            ];
        }

        // Per-mitra aggregated across branches
        $perMitra = [];
        if (Schema::hasTable('mitras') && Schema::hasTable('transaksis')) {
            $col = $this->detectBranchColumn('transaksis');
            if ($col) {
                $perMitra = DB::table('transaksis')
                    ->select('mitra_id', DB::raw('COALESCE(SUM(nominal),0) as total'), DB::raw('COUNT(*) as count'))
                    ->whereIn($col, $branchIds)
                    ->groupBy('mitra_id')
                    ->get()
                    ->map(function ($row) {
                        $mitra = DB::table('mitras')->where('id', $row->mitra_id)->first();
                        return [
                            'mitra_id' => $row->mitra_id,
                            'mitra_name' => $mitra->nama ?? 'Unknown',
                            'total' => (float) $row->total,
                            'count' => (int) $row->count,
                        ];
                    })->values();
            }
        }

        // Recent transactions (include names)
        $recent = [];
        if (Schema::hasTable('transaksis')) {
            $col = $this->detectBranchColumn('transaksis');
            if ($col) {
                $query = DB::table('transaksis as t')->whereIn('t.' . $col, $branchIds);
                if (Schema::hasTable('donaturs')) {
                    $query = $query->leftJoin('donaturs as d', 't.donatur_id', '=', 'd.id');
                    $query = $query->select('t.*', DB::raw("COALESCE(d.nama, '') as donatur_name"));
                } else {
                    $query = $query->select('t.*');
                }
                if (Schema::hasTable('mitras')) {
                    $query = $query->leftJoin('mitras as m', 't.mitra_id', '=', 'm.id');
                    $query = $query->addSelect(DB::raw("COALESCE(m.nama, '') as mitra_name"));
                }
                $recent = $query->orderBy('t.tanggal_transaksi', 'desc')->limit(10)->get();
            }
        }

        // Branch names
        $branchName = null;
        if (Schema::hasTable('kantor_cabangs')) {
            $names = DB::table('kantor_cabangs')->whereIn('id', $branchIds)->pluck('nama')->toArray();
            $branchName = implode(', ', $names);
        }

        return ['admin_cabang' => [
            'mitra_count' => (int) $mitraCount,
            'donatur_count' => (int) $donaturCount,
            'absensi_today' => (int) $absensiToday,
            'payroll_records' => (int) $payrollRecords,
            'transaksi_count' => (int) $transaksiCount,
            'transaksi_total' => (float) $transaksiTotal,
            'branch_name' => $branchName,
            'timeseries' => $period,
            'per_mitra' => $perMitra,
            'recent_transactions' => $recent,
        ]];
    }

    /**
     * Detect a branch/Kantor column name for a given table.
     * Returns the column name or null if none found.
     */
    protected function detectBranchColumn(string $table): ?string
    {
        try {
            if (! Schema::hasTable($table)) {
                return null;
            }
            $cols = Schema::getColumnListing($table);
            $candidates = ['kantor_cabang_id', 'kantor_id', 'branch_id', 'cabang_id', 'kantor_cabang', 'kantor_id_uuid', 'kantor_id'];
            foreach ($candidates as $cand) {
                if (in_array($cand, $cols)) {
                    return $cand;
                }
            }
        } catch (\Throwable $e) {
            // ignore and return null
        }
        return null;
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
