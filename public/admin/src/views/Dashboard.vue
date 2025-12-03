<template>
  <AdminLayout>
    <div class="space-y-6">
      <!-- Page Header -->
      <div>
        <h1 class="mb-2 font-semibold text-gray-800 text-title-sm dark:text-white/90 sm:text-title-md">
          Dashboard
        </h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">
          <span v-if="userRole === 'admin'">Ringkasan dan statistik dari semua modul aplikasi</span>
          <span v-else>Ringkasan data dan aktivitas Anda</span>
        </p>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="flex items-center justify-center py-12">
        <div class="text-center">
          <div class="inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-brand-500 border-r-transparent"></div>
          <p class="mt-4 text-sm text-gray-500">Memuat data...</p>
        </div>
      </div>

      <!-- Dashboard Content -->
      <div v-else class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        <!-- Company Section - Admin Only -->
        <div v-if="userRole === 'admin'" class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
          <div class="mb-4 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800 text-lg dark:text-white/90">Company</h3>
            <PageIcon class="h-6 w-6 text-brand-500" />
          </div>
          <div class="space-y-3">
            <div class="flex items-center justify-between">
              <span class="text-sm text-gray-600 dark:text-gray-400">Landing Profile</span>
              <span class="font-semibold text-gray-800 dark:text-white/90">{{ stats.company.landingProfile || 0 }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-sm text-gray-600 dark:text-gray-400">Landing Kegiatan</span>
              <span class="font-semibold text-gray-800 dark:text-white/90">{{ stats.company.landingKegiatan || 0 }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-sm text-gray-600 dark:text-gray-400">Landing Program</span>
              <span class="font-semibold text-gray-800 dark:text-white/90">{{ stats.company.landingProgram || 0 }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-sm text-gray-600 dark:text-gray-400">Landing Proposal</span>
              <span class="font-semibold text-gray-800 dark:text-white/90">{{ stats.company.landingProposal || 0 }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-sm text-gray-600 dark:text-gray-400">Landing Bulletin</span>
              <span class="font-semibold text-gray-800 dark:text-white/90">{{ stats.company.landingBulletin || 0 }}</span>
            </div>
          </div>
        </div>

        <!-- Administrasi Section - Admin Only -->
        <div v-if="userRole === 'admin'" class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
          <div class="mb-4 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800 text-lg dark:text-white/90">Administrasi</h3>
            <FolderIcon class="h-6 w-6 text-brand-500" />
          </div>
          <div class="space-y-3">
            <div class="flex items-center justify-between">
              <span class="text-sm text-gray-600 dark:text-gray-400">Kantor Cabang</span>
              <span class="font-semibold text-gray-800 dark:text-white/90">{{ stats.administrasi.kantorCabang || 0 }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-sm text-gray-600 dark:text-gray-400">Program</span>
              <span class="font-semibold text-gray-800 dark:text-white/90">{{ stats.administrasi.program || 0 }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-sm text-gray-600 dark:text-gray-400">Jabatan</span>
              <span class="font-semibold text-gray-800 dark:text-white/90">{{ stats.administrasi.jabatan || 0 }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-sm text-gray-600 dark:text-gray-400">Pangkat</span>
              <span class="font-semibold text-gray-800 dark:text-white/90">{{ stats.administrasi.pangkat || 0 }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-sm text-gray-600 dark:text-gray-400">Gaji</span>
              <span class="font-semibold text-gray-800 dark:text-white/90">{{ stats.administrasi.gaji || 0 }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-sm text-gray-600 dark:text-gray-400">SOP</span>
              <span class="font-semibold text-gray-800 dark:text-white/90">{{ stats.administrasi.sop || 0 }}</span>
            </div>
          </div>
        </div>

        <!-- Konten & Publikasi Section - Admin Only -->
        <div v-if="userRole === 'admin'" class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
          <div class="mb-4 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800 text-lg dark:text-white/90">Konten & Publikasi</h3>
            <ListIcon class="h-6 w-6 text-brand-500" />
          </div>
          <div class="space-y-3">
            <div class="flex items-center justify-between">
              <span class="text-sm text-gray-600 dark:text-gray-400">Program Kami</span>
              <span class="font-semibold text-gray-800 dark:text-white/90">{{ stats.konten.programKami || 0 }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-sm text-gray-600 dark:text-gray-400">Profile Kami</span>
              <span class="font-semibold text-gray-800 dark:text-white/90">{{ stats.konten.profileKami || 0 }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-sm text-gray-600 dark:text-gray-400">Proposal Data</span>
              <span class="font-semibold text-gray-800 dark:text-white/90">{{ stats.konten.proposalData || 0 }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-sm text-gray-600 dark:text-gray-400">Bulletin Data</span>
              <span class="font-semibold text-gray-800 dark:text-white/90">{{ stats.konten.bulletinData || 0 }}</span>
            </div>
          </div>
        </div>

        <!-- User & Kepegawaian Section -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
          <div class="mb-4 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800 text-lg dark:text-white/90">User & Kepegawaian</h3>
            <UserCircleIcon class="h-6 w-6 text-brand-500" />
          </div>
          <div class="space-y-3">
            <div class="flex items-center justify-between">
              <span class="text-sm text-gray-600 dark:text-gray-400">Karyawan</span>
              <span class="font-semibold text-gray-800 dark:text-white/90">{{ stats.userKepegawaian?.karyawan || 0 }}</span>
            </div>
            <div v-if="userRole === 'admin'" class="flex items-center justify-between">
              <span class="text-sm text-gray-600 dark:text-gray-400">Mitra</span>
              <span class="font-semibold text-gray-800 dark:text-white/90">{{ stats.userKepegawaian?.mitra || 0 }}</span>
            </div>
            <div v-if="userRole === 'admin'" class="flex items-center justify-between">
              <span class="text-sm text-gray-600 dark:text-gray-400">Donatur</span>
              <span class="font-semibold text-gray-800 dark:text-white/90">{{ stats.userKepegawaian?.donatur || 0 }}</span>
            </div>
          </div>
        </div>

        <!-- Operasional Section -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
          <div class="mb-4 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800 text-lg dark:text-white/90">Operasional</h3>
            <CalenderIcon class="h-6 w-6 text-brand-500" />
          </div>
          <div class="space-y-3">
            <div class="flex items-center justify-between">
              <span class="text-sm text-gray-600 dark:text-gray-400">Absensi</span>
              <span class="font-semibold text-gray-800 dark:text-white/90">{{ stats.operasional.absensi || 0 }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-sm text-gray-600 dark:text-gray-400">Remunerasi</span>
              <span class="font-semibold text-gray-800 dark:text-white/90">{{ stats.operasional.remunerasi || 0 }}</span>
            </div>
          </div>
        </div>

        <!-- Keuangan Section - Admin Only -->
        <div v-if="userRole === 'admin'" class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
          <div class="mb-4 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800 text-lg dark:text-white/90">Keuangan</h3>
            <PieChartIcon class="h-6 w-6 text-brand-500" />
          </div>
          <div class="space-y-3">
            <div class="flex items-center justify-between">
              <span class="text-sm text-gray-600 dark:text-gray-400">Transaksi</span>
              <span class="font-semibold text-gray-800 dark:text-white/90">{{ stats.keuangan.transaksi || 0 }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-sm text-gray-600 dark:text-gray-400">Penyaluran</span>
              <span class="font-semibold text-gray-800 dark:text-white/90">{{ stats.keuangan.penyaluran || 0 }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-sm text-gray-600 dark:text-gray-400">Pengajuan Dana</span>
              <span class="font-semibold text-gray-800 dark:text-white/90">{{ stats.keuangan.pengajuanDana || 0 }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-sm text-gray-600 dark:text-gray-400">Total Keuangan</span>
              <span class="font-semibold text-gray-800 dark:text-white/90">{{ formatCurrency(stats.keuangan.total || 0) }}</span>
            </div>
          </div>
        </div>

        <!-- Laporan Section - Admin Only -->
        <div v-if="userRole === 'admin'" class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
          <div class="mb-4 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800 text-lg dark:text-white/90">Laporan</h3>
            <BarChartIcon class="h-6 w-6 text-brand-500" />
          </div>
          <div class="space-y-3">
            <div class="flex items-center justify-between">
              <span class="text-sm text-gray-600 dark:text-gray-400">Laporan Transaksi</span>
              <span class="font-semibold text-gray-800 dark:text-white/90">{{ stats.laporan.laporanTransaksi || 0 }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-sm text-gray-600 dark:text-gray-400">Laporan Keuangan</span>
              <span class="font-semibold text-gray-800 dark:text-white/90">{{ stats.laporan.laporanKeuangan || 0 }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import {
  PageIcon,
  FolderIcon,
  ListIcon,
  UserCircleIcon,
  CalenderIcon,
  PieChartIcon,
  BarChartIcon,
} from '@/icons'

const loading = ref(true)
const userRole = ref('user') // 'admin' or 'user'
const stats = ref({
  company: {
    landingProfile: 0,
    landingKegiatan: 0,
    landingProgram: 0,
    landingProposal: 0,
    landingBulletin: 0,
  },
  administrasi: {
    kantorCabang: 0,
    program: 0,
    jabatan: 0,
    pangkat: 0,
    gaji: 0,
    sop: 0,
  },
  konten: {
    programKami: 0,
    profileKami: 0,
    proposalData: 0,
    bulletinData: 0,
  },
  userKepegawaian: {
    karyawan: 0,
    mitra: 0,
    donatur: 0,
  },
  operasional: {
    absensi: 0,
    remunerasi: 0,
  },
  keuangan: {
    transaksi: 0,
    penyaluran: 0,
    pengajuanDana: 0,
    total: 0,
  },
  laporan: {
    laporanTransaksi: 0,
    laporanKeuangan: 0,
  },
})

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
  }).format(amount)
}

const fetchUser = async () => {
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

    if (response.ok) {
      const data = await response.json()
      if (data.success && data.user) {
        userRole.value = data.user.is_admin ? 'admin' : 'user'
      }
    }
  } catch (error) {
    console.error('Error fetching user:', error)
  }
}

const fetchDashboardStats = async () => {
  loading.value = true
  try {
    const response = await fetch('/admin/api/dashboard/stats', {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
      },
      credentials: 'same-origin',
    })

    if (response.ok) {
      const data = await response.json()
      if (data.success && data.data) {
        userRole.value = data.data.role || 'user'
        stats.value = { ...stats.value, ...data.data }
      }
    }
  } catch (error) {
    console.error('Error fetching dashboard stats:', error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchUser()
  fetchDashboardStats()
})
</script>

<style></style>

