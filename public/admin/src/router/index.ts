import { createRouter, createWebHistory } from 'vue-router'
import { useAuth } from '@/composables/useAuth'

const router = createRouter({
  history: createWebHistory('/admin/'),
  scrollBehavior(to, from, savedPosition) {
    return savedPosition || { left: 0, top: 0 }
  },
  routes: [
    {
      path: '/',
      name: 'Home',
      redirect: '/dashboard',
    },
    {
      path: '/calendar',
      name: 'Calendar',
      component: () => import('../views/Others/Calendar.vue'),
      meta: {
        title: 'Calendar',
      },
    },
    {
      path: '/profile',
      name: 'Profile',
      redirect: { path: '/welcome', hash: '#profile' },
      meta: {
        title: 'Profile',
      },
    },
    {
      path: '/account/settings',
      name: 'Account Settings',
      component: () => import('../views/Account/AccountSettings.vue'),
      meta: { title: 'Account Settings' },
    },
    {
      path: '/support',
      name: 'Support',
      component: () => import('../views/Support.vue'),
      meta: { title: 'Support' },
    },
    {
      path: '/form-elements',
      name: 'Form Elements',
      component: () => import('../views/Forms/FormElements.vue'),
      meta: {
        title: 'Form Elements',
      },
    },
    {
      path: '/basic-tables',
      name: 'Basic Tables',
      component: () => import('../views/Tables/BasicTables.vue'),
      meta: {
        title: 'Basic Tables',
      },
    },
    {
      path: '/line-chart',
      name: 'Line Chart',
      component: () => import('../views/Chart/LineChart/LineChart.vue'),
    },
    {
      path: '/bar-chart',
      name: 'Bar Chart',
      component: () => import('../views/Chart/BarChart/BarChart.vue'),
    },
    {
      path: '/alerts',
      name: 'Alerts',
      component: () => import('../views/UiElements/Alerts.vue'),
      meta: {
        title: 'Alerts',
      },
    },
    {
      path: '/avatars',
      name: 'Avatars',
      component: () => import('../views/UiElements/Avatars.vue'),
      meta: {
        title: 'Avatars',
      },
    },
    {
      path: '/badge',
      name: 'Badge',
      component: () => import('../views/UiElements/Badges.vue'),
      meta: {
        title: 'Badge',
      },
    },

    {
      path: '/buttons',
      name: 'Buttons',
      component: () => import('../views/UiElements/Buttons.vue'),
      meta: {
        title: 'Buttons',
      },
    },

    {
      path: '/images',
      name: 'Images',
      component: () => import('../views/UiElements/Images.vue'),
      meta: {
        title: 'Images',
      },
    },
    {
      path: '/videos',
      name: 'Videos',
      component: () => import('../views/UiElements/Videos.vue'),
      meta: {
        title: 'Videos',
      },
    },
    {
      path: '/blank',
      name: 'Blank',
      component: () => import('../views/Pages/BlankPage.vue'),
      meta: {
        title: 'Blank',
      },
    },
    {
      path: '/search',
      name: 'Search Results',
      component: () => import('../views/Pages/SearchResults.vue'),
      meta: {
        title: 'Hasil Pencarian',
      },
    },

    {
      path: '/error-404',
      name: '404 Error',
      component: () => import('../views/Errors/FourZeroFour.vue'),
      meta: {
        title: '404 Error',
      },
    },

    {
      path: '/signin',
      name: 'Signin',
      component: () => import('../views/Auth/Signin.vue'),
      meta: {
        title: 'Signin',
      },
    },
    {
      path: '/signup',
      name: 'Signup',
      component: () => import('../views/Auth/Signup.vue'),
      meta: {
        title: 'Signup',
      },
    },
    {
      path: '/welcome',
      name: 'Welcome',
      component: () => import('../views/Welcome.vue'),
      meta: {
        title: 'Welcome',
      },
    },
    {
      path: '/dashboard',
      name: 'Dashboard',
      component: () => import('../views/Dashboard.vue'),
      meta: { title: 'Dashboard' },
    },
    
    // Dynamic routes for all menu items - using BlankPage as default
    // Company routes
    {
      path: '/company/landing-profile',
      name: 'Landing Profile',
      component: () => import('../views/Company/LandingProfile.vue'),
      meta: { title: 'Landing Profile' },
    },
      {
        path: '/restricted',
        name: 'Restricted',
        component: () => import('../views/RestrictedPage.vue'),
        meta: { title: 'Halaman Terbatas' },
      },
    {
      path: '/company/landing-kegiatan',
      name: 'Landing Kegiatan',
      component: () => import('../views/Company/LandingKegiatan.vue'),
      meta: { title: 'Landing Kegiatan' },
    },
    {
      path: '/company/landing-kegiatan/new',
      name: 'Tambah Landing Kegiatan',
      component: () => import('../views/Company/LandingKegiatanForm.vue'),
      meta: { title: 'Tambah Landing Kegiatan' },
    },
    {
      path: '/company/landing-kegiatan/:id/edit',
      name: 'Edit Landing Kegiatan',
      component: () => import('../views/Company/LandingKegiatanForm.vue'),
      meta: { title: 'Edit Landing Kegiatan' },
    },
    {
      path: '/company/landing-program',
      name: 'Landing Program',
      component: () => import('../views/Company/LandingProgram.vue'),
      meta: { title: 'Landing Program' },
    },
    {
      path: '/company/landing-program/new',
      name: 'Tambah Landing Program',
      component: () => import('../views/Company/LandingProgramForm.vue'),
      meta: { title: 'Tambah Landing Program' },
    },
    {
      path: '/company/landing-program/:id/edit',
      name: 'Edit Landing Program',
      component: () => import('../views/Company/LandingProgramForm.vue'),
      meta: { title: 'Edit Landing Program' },
    },
    {
      path: '/company/landing-proposal',
      name: 'Landing Proposal',
      component: () => import('../views/Company/LandingProposal.vue'),
      meta: { title: 'Landing Proposal' },
    },
    {
      path: '/company/landing-proposal/new',
      name: 'Tambah Landing Proposal',
      component: () => import('../views/Company/LandingProposalForm.vue'),
      meta: { title: 'Tambah Landing Proposal' },
    },
    {
      path: '/company/landing-proposal/:id/edit',
      name: 'Edit Landing Proposal',
      component: () => import('../views/Company/LandingProposalForm.vue'),
      meta: { title: 'Edit Landing Proposal' },
    },
    {
      path: '/company/landing-bulletin',
      name: 'Landing Bulletin',
      component: () => import('../views/Company/LandingBulletin.vue'),
      meta: { title: 'Landing Bulletin' },
    },
    {
      path: '/company/landing-bulletin/new',
      name: 'Tambah Landing Bulletin',
      component: () => import('../views/Company/LandingBulletinForm.vue'),
      meta: { title: 'Tambah Landing Bulletin' },
    },
    {
      path: '/company/landing-bulletin/:id/edit',
      name: 'Edit Landing Bulletin',
      component: () => import('../views/Company/LandingBulletinForm.vue'),
      meta: { title: 'Edit Landing Bulletin' },
    },
    
    // Administrasi routes
    {
      path: '/administrasi/kantor-cabang',
      name: 'Kantor Cabang',
      component: () => import('../views/Administrasi/KantorCabang.vue'),
      meta: { title: 'Kantor Cabang' },
    },
    {
      path: '/administrasi/kantor-cabang/new',
      name: 'Tambah Kantor Cabang',
      component: () => import('../views/Administrasi/KantorCabangForm.vue'),
      meta: { title: 'Tambah Kantor Cabang' },
    },
    {
      path: '/administrasi/kantor-cabang/:id/edit',
      name: 'Edit Kantor Cabang',
      component: () => import('../views/Administrasi/KantorCabangForm.vue'),
      meta: { title: 'Edit Kantor Cabang' },
    },
    {
      path: '/administrasi/program',
      name: 'Program',
      component: () => import('../views/Administrasi/Program.vue'),
      meta: { title: 'Program' },
    },
    {
      path: '/administrasi/program/new',
      name: 'Tambah Program',
      component: () => import('../views/Administrasi/ProgramForm.vue'),
      meta: { title: 'Tambah Program' },
    },
    {
      path: '/administrasi/program/:id/edit',
      name: 'Edit Program',
      component: () => import('../views/Administrasi/ProgramForm.vue'),
      meta: { title: 'Edit Program' },
    },
    {
      path: '/administrasi/jabatan',
      name: 'Jabatan',
      component: () => import('../views/Administrasi/Jabatan.vue'),
      meta: { title: 'Jabatan' },
    },
    {
      path: '/administrasi/jabatan/:id',
      name: 'Detail Jabatan',
      component: () => import('../views/Administrasi/JabatanDetail.vue'),
      meta: { title: 'Detail Jabatan' },
    },
    {
      path: '/administrasi/jabatan/new',
      name: 'Tambah Jabatan',
      component: () => import('../views/Administrasi/JabatanForm.vue'),
      meta: { title: 'Tambah Jabatan' },
    },
    {
      path: '/administrasi/jabatan/:id/edit',
      name: 'Edit Jabatan',
      component: () => import('../views/Administrasi/JabatanForm.vue'),
      meta: { title: 'Edit Jabatan' },
    },
    {
      path: '/administrasi/pangkat',
      name: 'Pangkat',
      component: () => import('../views/Administrasi/Pangkat.vue'),
      meta: { title: 'Pangkat' },
    },
    {
      path: '/administrasi/pangkat/new',
      name: 'Tambah Pangkat',
      component: () => import('../views/Administrasi/PangkatForm.vue'),
      meta: { title: 'Tambah Pangkat' },
    },
    {
      path: '/administrasi/pangkat/:id/edit',
      name: 'Edit Pangkat',
      component: () => import('../views/Administrasi/PangkatForm.vue'),
      meta: { title: 'Edit Pangkat' },
    },
    {
      path: '/administrasi/tipe-absensi',
      name: 'Tipe Absensi',
      component: () => import('../views/Administrasi/TipeAbsensi.vue'),
      meta: { title: 'Tipe Absensi' },
    },
    {
      path: '/administrasi/tipe-absensi/new',
      name: 'Tambah Tipe Absensi',
      component: () => import('../views/Administrasi/TipeAbsensiForm.vue'),
      meta: { title: 'Tambah Tipe Absensi' },
    },
    {
      path: '/administrasi/tipe-absensi/:id/edit',
      name: 'Edit Tipe Absensi',
      component: () => import('../views/Administrasi/TipeAbsensiForm.vue'),
      meta: { title: 'Edit Tipe Absensi' },
    },
    {
      path: '/administrasi/gaji',
      name: 'Gaji',
      component: () => import('../views/Administrasi/Gaji.vue'),
      meta: { title: 'Gaji' },
    },
    {
      path: '/administrasi/gaji/new',
      name: 'Tambah Gaji',
      component: () => import('../views/Administrasi/GajiForm.vue'),
      meta: { title: 'Tambah Gaji' },
    },
    {
      path: '/administrasi/gaji/:id/edit',
      name: 'Edit Gaji',
      component: () => import('../views/Administrasi/GajiForm.vue'),
      meta: { title: 'Edit Gaji' },
    },
    {
      path: '/administrasi/tipe-donatur',
      name: 'Tipe Donatur',
      component: () => import('../views/Administrasi/TipeDonatur.vue'),
      meta: { title: 'Tipe Donatur' },
    },
    {
      path: '/administrasi/tipe-donatur/new',
      name: 'Tambah Tipe Donatur',
      component: () => import('../views/Administrasi/TipeDonaturForm.vue'),
      meta: { title: 'Tambah Tipe Donatur' },
    },
    {
      path: '/administrasi/tipe-donatur/:id/edit',
      name: 'Edit Tipe Donatur',
      component: () => import('../views/Administrasi/TipeDonaturForm.vue'),
      meta: { title: 'Edit Tipe Donatur' },
    },
    {
      path: '/administrasi/form-pesan',
      name: 'Form Pesan',
      component: () => import('../views/Administrasi/FormPesan.vue'),
      meta: { title: 'Form Pesan' },
    },
    {
      path: '/administrasi/sop',
      name: 'SOP',
      component: () => import('../views/Administrasi/SOP.vue'),
      meta: { title: 'SOP' },
    },
    {
      path: '/administrasi/aturan-kepegawaian',
      name: 'Aturan Kepegawaian',
      component: () => import('../views/Administrasi/AturanKepegawaian.vue'),
      meta: { title: 'Aturan Kepegawaian' },
    },
    
    // Konten & Publikasi routes
    {
      path: '/konten/program-kami',
      name: 'Program Kami',
      component: () => import('../views/Konten/ProgramKami.vue'),
      meta: { title: 'Program Kami' },
    },
    {
      path: '/konten/profile-kami',
      name: 'Profile Kami',
      component: () => import('../views/Konten/ProfileKami.vue'),
      meta: { title: 'Profile Kami' },
    },
    {
      path: '/konten/proposal-data',
      name: 'Proposal Data',
      component: () => import('../views/Konten/ProposalData.vue'),
      meta: { title: 'Proposal Data' },
    },
    {
      path: '/konten/bulletin-data',
      name: 'Bulletin Data',
      component: () => import('../views/Konten/BulletinData.vue'),
      meta: { title: 'Bulletin Data' },
    },
    
    // User & Kepegawaian routes
    {
      path: '/user-kepegawaian/karyawan',
      name: 'Karyawan',
      component: () => import('../views/UserKepegawaian/Karyawan.vue'),
      meta: { title: 'Karyawan' },
    },
    {
      path: '/user-kepegawaian/karyawan/new',
      name: 'Tambah Karyawan',
      component: () => import('../views/UserKepegawaian/KaryawanForm.vue'),
      meta: { title: 'Tambah Karyawan' },
    },
    {
      path: '/user-kepegawaian/karyawan/:id/edit',
      name: 'Edit Karyawan',
      component: () => import('../views/UserKepegawaian/KaryawanForm.vue'),
      meta: { title: 'Edit Karyawan' },
    },
    {
      path: '/user-kepegawaian/mitra',
      name: 'Mitra',
      component: () => import('../views/UserKepegawaian/Mitra.vue'),
      meta: { title: 'Mitra' },
    },
    {
      path: '/user-kepegawaian/mitra/new',
      name: 'Tambah Mitra',
      component: () => import('../views/UserKepegawaian/MitraForm.vue'),
      meta: { title: 'Tambah Mitra' },
    },
    {
      path: '/user-kepegawaian/mitra/:id/edit',
      name: 'Edit Mitra',
      component: () => import('../views/UserKepegawaian/MitraForm.vue'),
      meta: { title: 'Edit Mitra' },
    },
    {
      path: '/user-kepegawaian/payroll-mitra',
      name: 'Payroll Mitra',
      component: () => import('../views/UserKepegawaian/PayrollMitra.vue'),
      meta: { title: 'Payroll Mitra', permission: 'view payroll mitra' },
    },
    {
      path: '/user-kepegawaian/payroll-mitra/new',
      name: 'Tambah Payroll Mitra',
      component: () => import('../views/UserKepegawaian/PayrollMitraForm.vue'),
      meta: { title: 'Tambah Payroll Mitra', permission: 'create payroll mitra' },
    },
    {
      path: '/user-kepegawaian/payroll-mitra/:id/edit',
      name: 'Edit Payroll Mitra',
      component: () => import('../views/UserKepegawaian/PayrollMitraForm.vue'),
      meta: { title: 'Edit Payroll Mitra', permission: 'update payroll mitra' },
    },
    {
      path: '/user-kepegawaian/donatur',
      name: 'Donatur',
      component: () => import('../views/UserKepegawaian/Donatur.vue'),
      meta: { title: 'Donatur' },
    },
    {
      path: '/user-kepegawaian/donatur/new',
      name: 'Tambah Donatur',
      component: () => import('../views/UserKepegawaian/DonaturForm.vue'),
      meta: { title: 'Tambah Donatur' },
    },
    {
      path: '/user-kepegawaian/donatur/:id/edit',
      name: 'Edit Donatur',
      component: () => import('../views/UserKepegawaian/DonaturForm.vue'),
      meta: { title: 'Edit Donatur' },
    },
    
    // Operasional routes
    {
      path: '/operasional/absensi',
      name: 'Absensi',
      component: () => import('../views/Operasional/Absensi.vue'),
      meta: { title: 'Absensi', permission: 'view absensi' },
    },
    {
      path: '/operasional/absensi/:id',
      name: 'Detail Absensi',
      component: () => import('../views/Operasional/AbsensiDetail.vue'),
      meta: { title: 'Detail Absensi' },
    },

    {
      path: '/operasional/payroll',
      name: 'Payroll',
      component: () => import('../views/Operasional/Payroll.vue'),
      meta: { title: 'Rekap Gaji (Payroll)', permission: 'view remunerasi' },
    },
    {
      path: '/operasional/payroll/:id',
      name: 'Payroll Detail',
      component: () => import('../views/Operasional/PayrollDetail.vue'),
      meta: { title: 'Detail Payroll', permission: 'view remunerasi' },
    },
    {
      path: '/operasional/payroll/mine',
      name: 'Payroll Mine',
      component: () => import('../views/Operasional/PayrollMine.vue'),
      meta: { title: 'Slip Gaji Saya', permission: 'view remunerasi' },
    },
    {
      path: '/operasional/payroll/:periodId/records/:recordId/edit',
      name: 'Edit Payroll Record',
      component: () => import('../views/Operasional/PayrollRecordEdit.vue'),
      meta: { title: 'Edit Payroll Record', permission: 'update remunerasi' },
    },
    
    // Keuangan routes
    {
      path: '/keuangan/transaksi',
      name: 'Transaksi',
      component: () => import('../views/Keuangan/Transaksi.vue'),
      meta: { title: 'Transaksi', permission: 'view transaksi' },
    },
    {
      path: '/keuangan/transaksi/new',
      name: 'Tambah Transaksi',
      component: () => import('../views/Keuangan/TransaksiForm.vue'),
      meta: { title: 'Tambah Transaksi', permission: 'create transaksi' },
    },
    {
      path: '/keuangan/transaksi/:id/edit',
      name: 'Edit Transaksi',
      component: () => import('../views/Keuangan/TransaksiForm.vue'),
      meta: { title: 'Edit Transaksi', permission: 'update transaksi' },
    },
    {
      path: '/keuangan/penyaluran',
      name: 'Penyaluran',
      component: () => import('../views/Keuangan/Penyaluran.vue'),
      meta: { title: 'Penyaluran', permission: 'view penyaluran' },
    },
    {
      path: '/keuangan/penyaluran/new',
      name: 'Buat Penyaluran',
      component: () => import('../views/Keuangan/PenyaluranForm.vue'),
      meta: { title: 'Buat Penyaluran', permission: 'create penyaluran' },
    },
    {
      path: '/keuangan/penyaluran/:id/edit',
      name: 'Edit Penyaluran',
      component: () => import('../views/Keuangan/PenyaluranForm.vue'),
      meta: { title: 'Edit Penyaluran', permission: 'update penyaluran' },
    },
    {
      path: '/keuangan/pengajuan-dana',
      name: 'Pengajuan Dana',
      component: () => import('../views/Keuangan/PengajuanDana.vue'),
      meta: { title: 'Pengajuan Dana', permission: 'view pengajuan dana' },
    },
    {
      path: '/keuangan/pengajuan-dana/new',
      name: 'Tambah Pengajuan Dana',
      component: () => import('../views/Keuangan/PengajuanDanaForm.vue'),
      meta: { title: 'Tambah Pengajuan Dana', permission: 'create pengajuan dana' },
    },
    {
      path: '/keuangan/pengajuan-dana/:id/edit',
      name: 'Edit Pengajuan Dana',
      component: () => import('../views/Keuangan/PengajuanDanaForm.vue'),
      meta: { title: 'Edit Pengajuan Dana', permission: 'update pengajuan dana' },
    },
    {
      path: '/keuangan/keuangan',
      name: 'Keuangan',
      component: () => import('../views/Keuangan/Keuangan.vue'),
      meta: { title: 'Keuangan', permission: 'view keuangan' },
    },
    {
      path: '/keuangan/program-shares-summary',
      name: 'Program Shares Summary',
      component: () => import('../views/Keuangan/ProgramSharesSummary.vue'),
      meta: { title: 'Ringkasan Program - Shares', permission: 'view keuangan' },
    },
    {
      path: '/keuangan/keuangan/new',
      name: 'Tambah Keuangan',
      component: () => import('../views/Keuangan/KeuanganForm.vue'),
      meta: { title: 'Tambah Keuangan', permission: 'create keuangan' },
    },
    {
      path: '/keuangan/keuangan/:id/edit',
      name: 'Edit Keuangan',
      component: () => import('../views/Keuangan/KeuanganForm.vue'),
      meta: { title: 'Edit Keuangan', permission: 'update keuangan' },
    },
    
    // Laporan routes
    {
      path: '/laporan/laporan-transaksi',
      name: 'Laporan Transaksi',
      component: () => import('../views/Laporan/LaporanTransaksiKeuangan.vue'),
      meta: { title: 'Laporan Transaksi', permission: 'view laporan transaksi' },
    },
    {
      path: '/laporan/laporan-transaksi/new',
      name: 'Tambah Laporan Transaksi',
      component: () => import('../views/Laporan/LaporanTransaksiForm.vue'),
      meta: { title: 'Tambah Laporan Transaksi', permission: 'create laporan transaksi' },
    },
    {
      path: '/laporan/laporan-transaksi/:id/edit',
      name: 'Edit Laporan Transaksi',
      component: () => import('../views/Laporan/LaporanTransaksiForm.vue'),
      meta: { title: 'Edit Laporan Transaksi', permission: 'update laporan transaksi' },
    },
    {
      path: '/laporan/laporan-keuangan',
      name: 'Laporan Keuangan',
      component: () => import('../views/Laporan/LaporanKeuangan.vue'),
      meta: { title: 'Laporan Keuangan' },
    },
    {
      path: '/laporan/detail-mitra/:id',
      name: 'Detail Mitra',
      component: () => import('../views/Laporan/DetailMitra.vue'),
      meta: { title: 'Detail Mitra' },
    },
    
    // Catch-all route - redirect unknown routes to blank page
    {
      path: '/:pathMatch(.*)*',
      name: 'NotFound',
      component: () => import('../views/Pages/BlankPage.vue'),
      meta: { title: 'Page Not Found' },
    },
  ],
})

export default router

// Store auth state
let authChecked = false
let isAuthenticated = false

// Check authentication status
const checkAuth = async (force = false) => {
  if (authChecked && !force) {
    return isAuthenticated
  }
  
  try {
    const response = await fetch('/admin/api/user', {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
      },
      credentials: 'same-origin',
    })
    
    
    // If response is redirect (302) or unauthorized (401), user is not authenticated
    if (response.status === 401 || response.status === 302 || response.redirected) {
      isAuthenticated = false
      authChecked = true
      return false
    }
    
    const data = await response.json()
    isAuthenticated = data.success && data.user
    authChecked = true
    return isAuthenticated
  } catch (err) {
    console.error('checkAuth: error', err)
    isAuthenticated = false
    authChecked = true
    return false
  }
}

// Export function to reset auth state (for use after login/logout)
export const resetAuthState = () => {
  authChecked = false
  isAuthenticated = false
}

// Export checkAuth function for use in components
export { checkAuth }

router.beforeEach(async (to, from, next) => {
  document.title = `Kita Membantu Sesama - Admin | ${to.meta.title}`
  
  // Public routes that don't require authentication
  const publicRoutes = ['/signin', '/signup', '/profile']
  const isPublicRoute = publicRoutes.includes(to.path)
  
  // Always check authentication status for non-public routes
  const authenticated = await checkAuth()
  
  
  if (!authenticated && !isPublicRoute) {
    // Redirect to signin if not authenticated and trying to access protected route
    next('/signin')
    return
  }
  
  if (authenticated && isPublicRoute) {
    // Redirect to dashboard if already authenticated and trying to access signin/signup
    next('/')
    return
  }
  
  // Determine required permission. If `meta.permission` is present, use it.
  // Otherwise derive a sensible default from the route title (covers all menus).
  let permission = to.meta?.permission as string | undefined
  if (!permission && to.meta?.title) {
    const rawTitle = String(to.meta.title).toLowerCase()
    // Skip deriving for known public pages
    const publicTitles = ['signin', 'signup', 'welcome', 'dashboard', 'support', 'calendar', 'blank', '404', 'halaman terbatas', 'account settings', 'profile']
    if (!publicTitles.includes(rawTitle)) {
      // Determine action
      const nameStr = String(to.name || '')
      if (nameStr.startsWith('Tambah') || to.path.includes('/new')) {
        // Strip leading action word (e.g., 'tambah') from title for resource name
        const resource = rawTitle.replace(/^(tambah|edit|detail|lihat)\s+/, '').trim()
        permission = `create ${resource}`
      } else if (nameStr.startsWith('Edit') || to.path.includes('/edit')) {
        const resource = rawTitle.replace(/^(tambah|edit|detail|lihat)\s+/, '').trim()
        permission = `update ${resource}`
      } else if (nameStr.startsWith('Detail') || to.path.includes('/:id')) {
        const resource = rawTitle.replace(/^(tambah|edit|detail|lihat)\s+/, '').trim()
        permission = `show ${resource}`
      } else {
        const resource = rawTitle.replace(/^(tambah|edit|detail|lihat)\s+/, '').trim()
        permission = `view ${resource}`
      }
    }
  }
  if (!permission) {
    return next()
  }

  try {
    const { fetchUser, hasPermission, isAdmin, user } = useAuth()
    await fetchUser()

    // Build permission candidates (support aliases and 'show'->'view')
    const permsToCheck = new Set<string>()
    permsToCheck.add(permission)
    // alias remunerasi <-> payroll
    permsToCheck.add(permission.replace('remunerasi', 'payroll'))
    permsToCheck.add(permission.replace('payroll', 'remunerasi'))
    // map show -> view
    permsToCheck.add(permission.replace(/^show\s+/, 'view '))
    permsToCheck.add(permission.replace(/^view\s+/, 'show '))

    let allowed = isAdmin()
    if (!allowed) {
      for (const p of permsToCheck) {
        if (!p) continue
        if (hasPermission(p)) { allowed = true; break }
      }
    }

    if (allowed) return next()
    console.warn('Router guard: access denied', { path: to.path, permission, permsToCheck: Array.from(permsToCheck), userPermissions: (user.value as any)?.permissions })
    return next({ name: 'Restricted' })
  } catch (e) {
    // On error (e.g., not authenticated) redirect to signin
    return next({ name: 'Signin' })
  }
})
