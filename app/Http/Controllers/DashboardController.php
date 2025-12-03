<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
                        'kantorCabang' => DB::table('kantor_cabangs')->count(),
                        'program' => DB::table('programs')->count(),
                        'jabatan' => DB::table('roles')->count(),
                        'pangkat' => DB::table('pangkats')->count(),
                        'gaji' => DB::table('gajis')->count(),
                        'sop' => DB::table('sops')->count(),
                    ],
                    'konten' => [
                        'programKami' => DB::table('program_kamis')->count(),
                        'profileKami' => DB::table('profile_kamis')->count(),
                        'proposalData' => DB::table('proposal_data')->count(),
                        'bulletinData' => DB::table('bulletin_data')->count(),
                    ],
                    'userKepegawaian' => [
                        'karyawan' => DB::table('karyawans')->count(),
                        'mitra' => DB::table('mitras')->count(),
                        'donatur' => DB::table('donaturs')->count(),
                    ],
                    'operasional' => [
                        'absensi' => DB::table('absensis')->count(),
                        'remunerasi' => DB::table('remunerasis')->count(),
                    ],
                    'keuangan' => [
                        'transaksi' => DB::table('transaksis')->count(),
                        'penyaluran' => DB::table('penyalurans')->count(),
                        'pengajuanDana' => DB::table('pengajuan_danas')->count(),
                        'total' => DB::table('keuangans')->sum('jumlah') ?? 0,
                    ],
                    'laporan' => [
                        'laporanTransaksi' => DB::table('laporan_transaksis')->count(),
                        'laporanKeuangan' => DB::table('laporan_keuangans')->count(),
                    ],
                ],
            ]);
        } else {
            // Non-admin dashboard - show limited statistics
            return response()->json([
                'success' => true,
                'data' => [
                    'role' => 'user',
                    'userKepegawaian' => [
                        'karyawan' => DB::table('karyawans')->where('user_id', $user->id)->count(),
                    ],
                    'operasional' => [
                        'absensi' => DB::table('absensis')->where('user_id', $user->id)->count(),
                        'remunerasi' => DB::table('remunerasis')->where('user_id', $user->id)->count(),
                    ],
                ],
            ]);
        }
    }
}
