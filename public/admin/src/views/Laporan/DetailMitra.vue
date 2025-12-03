<template>
  <AdminLayout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />
    <div
      class="min-h-screen rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12"
    >
      <!-- Header -->
      <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-4">
          <button
            @click="handleBack"
            class="flex h-10 w-10 items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]"
          >
            <svg
              class="h-5 w-5"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M15 19l-7-7 7-7"
              />
            </svg>
          </button>
          <div>
            <h3 class="font-semibold text-gray-800 text-theme-xl dark:text-white/90 sm:text-2xl">
              Detail Mitra
            </h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">
              Informasi lengkap dan history transaksi mitra
            </p>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="flex items-center justify-center py-20">
        <div class="text-center">
          <div class="mb-4 inline-block h-12 w-12 animate-spin rounded-full border-4 border-solid border-brand-500 border-r-transparent"></div>
          <p class="text-gray-600 dark:text-gray-400">Memuat data...</p>
        </div>
      </div>

      <!-- Content -->
      <div v-else-if="mitraData" class="space-y-6">
        <!-- Mitra Info Card -->
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-white/[0.03]">
          <div class="mb-6 flex items-center justify-between">
            <div class="flex items-center gap-4">
              <div class="flex h-16 w-16 items-center justify-center rounded-lg bg-brand-100 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
                <svg
                  class="h-8 w-8"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                  />
                </svg>
              </div>
              <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white/90">
                  {{ mitraData.nama }}
                </h2>
                <p class="text-gray-600 dark:text-gray-400">
                  {{ mitraData.program }}
                </p>
              </div>
            </div>
            <span
              class="rounded-full px-4 py-2 text-sm font-medium"
              :class="
                mitraData.status === 'Aktif'
                  ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400'
                  : 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400'
              "
            >
              {{ mitraData.status }}
            </span>
          </div>

          <!-- Stats Grid -->
          <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
              <p class="mb-1 text-sm font-medium text-gray-600 dark:text-gray-400">
                Nominal Kontrak
              </p>
              <p class="text-xl font-bold text-gray-800 dark:text-white/90">
                {{ formatCurrency(mitraData.nominal) }}
              </p>
            </div>
            <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
              <p class="mb-1 text-sm font-medium text-gray-600 dark:text-gray-400">
                Total Transaksi
              </p>
              <p class="text-xl font-bold text-gray-800 dark:text-white/90">
                {{ transactionHistory.length }}
              </p>
            </div>
            <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
              <p class="mb-1 text-sm font-medium text-gray-600 dark:text-gray-400">
                Total Nilai Transaksi
              </p>
              <p class="text-xl font-bold text-gray-800 dark:text-white/90">
                {{ formatCurrency(totalTransactionValue) }}
              </p>
            </div>
          </div>
        </div>

        <!-- History Transaksi -->
        <div class="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-white/[0.03]">
          <div class="border-b border-gray-200 p-6 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
              History Transaksi
            </h3>
          </div>
          <div class="p-6">
            <div v-if="transactionHistory.length > 0" class="overflow-x-auto">
              <table class="w-full">
                <thead>
                  <tr class="border-b border-gray-200 dark:border-gray-700">
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">
                      Tanggal
                    </th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">
                      Keterangan
                    </th>
                    <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700 dark:text-gray-300">
                      Nominal
                    </th>
                    <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700 dark:text-gray-300">
                      Status
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="(transaction, index) in transactionHistory"
                    :key="index"
                    class="border-b border-gray-100 transition-colors hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-white/[0.03]"
                  >
                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                      {{ formatDate(transaction.tanggal) }}
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                      {{ transaction.keterangan }}
                    </td>
                    <td class="px-4 py-3 text-right text-sm font-semibold text-gray-800 dark:text-white/90">
                      {{ formatCurrency(transaction.nominal) }}
                    </td>
                    <td class="px-4 py-3 text-center">
                      <span
                        class="inline-block rounded-full px-3 py-1 text-xs font-medium"
                        :class="
                          transaction.status === 'Selesai'
                            ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400'
                            : transaction.status === 'Pending'
                            ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400'
                            : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'
                        "
                      >
                        {{ transaction.status }}
                      </span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div v-else class="flex flex-col items-center justify-center py-12 text-center">
              <svg
                class="mb-4 h-16 w-16 text-gray-400 dark:text-gray-500"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                />
              </svg>
              <p class="text-lg font-medium text-gray-600 dark:text-gray-400">
                Belum ada transaksi
              </p>
              <p class="mt-2 text-sm text-gray-500 dark:text-gray-500">
                History transaksi akan muncul di sini
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Not Found State -->
      <div v-else class="flex flex-col items-center justify-center py-20 text-center">
        <svg
          class="mb-4 h-16 w-16 text-gray-400 dark:text-gray-500"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
          />
        </svg>
        <p class="text-lg font-medium text-gray-600 dark:text-gray-400">
          Data mitra tidak ditemukan
        </p>
        <button
          @click="handleBack"
          class="mt-4 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600"
        >
          Kembali ke Laporan Keuangan
        </button>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'

const route = useRoute()
const router = useRouter()
const currentPageTitle = computed(() => 'Detail Mitra')
const loading = ref(true)
const mitraData = ref<any>(null)

// Sample mitra data - in production, fetch from API
const mitraList = [
  {
    id: '1',
    nama: 'PT ABC Foundation',
    program: 'Program Pendidikan Anak',
    nominal: 50000000,
    status: 'Aktif',
  },
  {
    id: '2',
    nama: 'Yayasan XYZ',
    program: 'Program Pemberdayaan Masyarakat',
    nominal: 75000000,
    status: 'Aktif',
  },
  {
    id: '3',
    nama: 'CV Sejahtera',
    program: 'Program Kesehatan',
    nominal: 30000000,
    status: 'Aktif',
  },
  {
    id: '4',
    nama: 'PT Harmoni',
    program: 'Program Bantuan Sosial',
    nominal: 100000000,
    status: 'Aktif',
  },
  {
    id: '5',
    nama: 'Yayasan Peduli',
    program: 'Program Lingkungan',
    nominal: 25000000,
    status: 'Aktif',
  },
  {
    id: '6',
    nama: 'PT Berkah',
    program: 'Program Kemanusiaan',
    nominal: 60000000,
    status: 'Aktif',
  },
  {
    id: '7',
    nama: 'PT Cinta Indonesia',
    program: 'Program Beasiswa',
    nominal: 80000000,
    status: 'Aktif',
  },
  {
    id: '8',
    nama: 'Yayasan Kasih Ibu',
    program: 'Program Kesehatan Ibu dan Anak',
    nominal: 45000000,
    status: 'Aktif',
  },
  {
    id: '9',
    nama: 'CV Maju Bersama',
    program: 'Program Pemberdayaan Ekonomi',
    nominal: 55000000,
    status: 'Aktif',
  },
  {
    id: '10',
    nama: 'PT Harapan Bangsa',
    program: 'Program Infrastruktur',
    nominal: 120000000,
    status: 'Aktif',
  },
  {
    id: '11',
    nama: 'Yayasan Bantu Sesama',
    program: 'Program Bantuan Pangan',
    nominal: 35000000,
    status: 'Aktif',
  },
  {
    id: '12',
    nama: 'PT Gotong Royong',
    program: 'Program Air Bersih',
    nominal: 70000000,
    status: 'Aktif',
  },
  {
    id: '13',
    nama: 'CV Jaya Abadi',
    program: 'Program Teknologi',
    nominal: 90000000,
    status: 'Aktif',
  },
  {
    id: '14',
    nama: 'Yayasan Damai Sentosa',
    program: 'Program Perdamaian',
    nominal: 40000000,
    status: 'Aktif',
  },
  {
    id: '15',
    nama: 'PT Harmoni Sejahtera',
    program: 'Program Sosial Budaya',
    nominal: 65000000,
    status: 'Aktif',
  },
]

// Sample transaction history data - in production, fetch from API
const generateTransactionHistory = (mitraId: string) => {
  const histories: Array<{ tanggal: string; keterangan: string; nominal: number; status: string }> = []
  const startDate = new Date('2024-01-01')
  const statuses = ['Selesai', 'Pending', 'Selesai', 'Selesai', 'Pending']
  
  // Generate 5-10 transactions per mitra
  const numTransactions = Math.floor(Math.random() * 6) + 5
  
  for (let i = 0; i < numTransactions; i++) {
    const date = new Date(startDate)
    date.setDate(date.getDate() + i * 15) // Every 15 days
    
    const statusIndex = i % statuses.length
    const nominal = Math.floor(Math.random() * 50000000) + 10000000
    
    histories.push({
      tanggal: date.toISOString().split('T')[0],
      keterangan: `Pembayaran angsuran ke-${i + 1} untuk ${mitraList.find(m => m.id === mitraId)?.program || 'Program'}`,
      nominal: nominal,
      status: statuses[statusIndex],
    })
  }
  
  return histories
}

const transactionHistory = ref<Array<{ tanggal: string; keterangan: string; nominal: number; status: string }>>([])

// Computed total transaction value
const totalTransactionValue = computed(() => {
  return transactionHistory.value.reduce((sum, transaction) => sum + transaction.nominal, 0)
})

// Format currency helper
const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
  }).format(value)
}

// Format date helper
const formatDate = (dateString: string) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}

// Load data
const loadData = async () => {
  loading.value = true
  
  try {
    const id = route.params.id as string
    
    // TODO: Fetch from API
    // const response = await fetch(`/api/laporan/mitra/${id}`)
    // const data = await response.json()
    // mitraData.value = data.mitra
    // transactionHistory.value = data.transactions
    
    // For now, use sample data
    const mitra = mitraList.find((m) => m.id === id)
    if (mitra) {
      mitraData.value = mitra
      transactionHistory.value = generateTransactionHistory(id)
    }
  } catch (error) {
    console.error('Error loading data:', error)
  } finally {
    loading.value = false
  }
}

// Handle back
const handleBack = () => {
  router.push('/laporan/laporan-keuangan?tab=mitra')
}

onMounted(() => {
  loadData()
})
</script>

<style scoped>
/* Table styles */
table {
  border-collapse: collapse;
}

tbody tr:last-child {
  border-bottom: none;
}
</style>



