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
      <div v-else class="space-y-6">
        <FundraisingDashboard v-if="userRole === 'fundraising'" :days="transDays"></FundraisingDashboard>
        <AdminCabangDashboard v-else-if="userRole === 'admin_cabang'" />
        <MitraDashboard v-else-if="userRole === 'mitra'" />
        <div v-else>
        <!-- Transactions Overview (Admin Only) -->
        <div v-if="userRole === 'admin'" class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
          <div class="mb-4 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800 text-lg dark:text-white/90">Transaksi</h3>
            <div class="flex items-center gap-3">
              <label class="text-sm text-gray-500">Rentang</label>
              <select v-model.number="transDays" @change="loadStats" class="h-9 rounded-lg border border-gray-300 bg-white px-3 text-sm">
                <option :value="7">7 Hari</option>
                <option :value="30">30 Hari</option>
                <option :value="90">90 Hari</option>
              </select>
            </div>
          </div>

          <div class="mb-6">
            <apex-chart v-if="chartSeries.length" type="line" height="220" :options="chartOptions" :series="chartSeries"></apex-chart>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div v-for="item in transactions.by_jabatan" :key="item.jabatan" class="rounded-lg border border-gray-100 p-4">
              <div class="flex items-center justify-between">
                <div>
                  <div class="text-sm text-gray-600">{{ item.jabatan }}</div>
                  <div class="mt-1 text-lg font-semibold text-gray-800">{{ item.count }} transaksi</div>
                </div>
                <div class="text-right">
                  <div class="text-sm text-gray-500">Total</div>
                  <div class="mt-1 font-semibold">{{ formatCurrency(item.total) }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
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
    </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
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

// Transactions / Chart state
const transactions = ref({ by_jabatan: [], timeseries: [] })
const transDays = ref(30)
const chartSeries = ref([])
const chartOptions = ref({})

import ApexChart from 'vue3-apexcharts'
import FundraisingDashboard from '@/views/dashboard_roles/FundraisingDashboard.vue'
import AdminCabangDashboard from '@/views/dashboard_roles/AdminCabangDashboard.vue'
import MitraDashboard from '@/views/dashboard_roles/MitraDashboard.vue'

const loadStats = async () => {
  try {
    const response = await fetch(`/admin/api/dashboard/stats?days=${transDays.value}`, {
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
      if (data.success && data.data && data.data.transactions) {
        transactions.value = data.data.transactions

        const seriesData = (transactions.value.timeseries || []).map((t) => t.count ?? t.value ?? 0)
        const categories = (transactions.value.timeseries || []).map((t) => t.date || t.label || '')

        if (seriesData.length) {
          chartSeries.value = [{ name: 'Transaksi', data: seriesData }]
          chartOptions.value = {
            chart: { toolbar: { show: false }, zoom: { enabled: false } },
            stroke: { curve: 'smooth', width: 2 },
            xaxis: { categories, labels: { style: { colors: '#6B7280' } } },
            yaxis: { labels: { formatter: (v) => v }, forceNiceScale: true },
            tooltip: { y: { formatter: (v) => `${v}` } },
            colors: ['#06B6D4'],
            markers: { size: 0 },
          }
        } else {
          chartSeries.value = []
        }
      }
    }
  } catch (err) {
    console.error('Error loading transaction stats:', err)
  }
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
        // If dashboard stats already selected a role (non-default), don't overwrite it here.
        if (userRole.value && userRole.value !== 'user') {
          return
        }

        // prefer explicit admin flag, otherwise map known role names (e.g. fundraising)
        if (data.user.is_admin) {
          userRole.value = 'admin'
        } else {
          const roleName = (data.user.role && data.user.role.name) ? (data.user.role.name || '').toLowerCase().trim() : ''
          // detect admin cabang variants
          if (roleName.includes('admin cabang') || roleName.includes('admincabang') || roleName.includes('admin-cabang')) {
            userRole.value = 'admin_cabang'
          } else if (roleName.includes('fundr')) {
            userRole.value = 'fundraising'
          } else if (roleName.includes('mitra')) {
            userRole.value = 'mitra'
          } else if (Array.isArray(data.user.permissions) && data.user.permissions.includes('view transaksi')) {
            userRole.value = 'user'
          } else {
            if (!userRole.value || userRole.value === 'user') {
              userRole.value = 'user'
            }
          }
        }
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
  loadStats()
})

watch(transDays, () => loadStats())
</script>

<style></style>

