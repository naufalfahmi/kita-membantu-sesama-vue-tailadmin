<template>
  <AdminLayout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />
    <div
      class="min-h-screen rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12"
    >
      <div class="mb-6 flex items-center justify-between">
        <h3 class="font-semibold text-gray-800 text-theme-xl dark:text-white/90 sm:text-2xl">
          {{ currentPageTitle }}
        </h3>
        <button
          @click="handleBack"
          class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]"
        >
          <svg
            class="fill-current"
            width="20"
            height="20"
            viewBox="0 0 20 20"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              d="M8.33333 15L3.33333 10M3.33333 10L8.33333 5M3.33333 10H16.6667"
              stroke="currentColor"
              stroke-width="1.5"
              stroke-linecap="round"
              stroke-linejoin="round"
            />
          </svg>
          Kembali
        </button>
      </div>

      <div v-if="loading" class="flex items-center justify-center py-20">
        <div class="flex flex-col items-center gap-4">
          <div class="h-12 w-12 animate-spin rounded-full border-4 border-brand-500 border-t-transparent"></div>
          <p class="text-sm text-gray-600 dark:text-gray-400">Memuat data absensi...</p>
        </div>
      </div>

      <div v-else-if="absensiData" class="space-y-6">
        <!-- Informasi Karyawan -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
          <h4 class="mb-6 text-lg font-semibold text-gray-800 dark:text-white/90">
            Informasi Karyawan
          </h4>
          <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div>
              <p class="mb-2 text-xs font-medium text-gray-500 dark:text-gray-400">Nama</p>
              <p class="text-sm font-semibold text-gray-800 dark:text-white/90">
                {{ absensiData.nama }}
              </p>
            </div>
            <div>
              <p class="mb-2 text-xs font-medium text-gray-500 dark:text-gray-400">No Induk</p>
              <p class="text-sm font-semibold text-gray-800 dark:text-white/90">
                {{ absensiData.noInduk }}
              </p>
            </div>
          </div>
        </div>

        <!-- Detail Absensi Masuk & Keluar -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
          <!-- Detail Absensi Masuk -->
          <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="mb-6 flex items-center gap-3">
              <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-100 dark:bg-green-900/30">
                <svg
                  class="h-5 w-5 text-green-600 dark:text-green-400"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M13 10V3L4 14h7v7l9-11h-7z"
                  />
                </svg>
              </div>
              <h4 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                Absensi Masuk
              </h4>
            </div>
            <div class="space-y-6">
              <div>
                <p class="mb-2 text-xs font-medium text-gray-500 dark:text-gray-400">Tanggal</p>
                <p class="text-sm font-semibold text-gray-800 dark:text-white/90">
                  {{ formatDate(absensiData.tanggalAbsenMasuk) }}
                </p>
              </div>
              <div>
                <p class="mb-2 text-xs font-medium text-gray-500 dark:text-gray-400">Waktu</p>
                <p class="text-sm font-semibold text-gray-800 dark:text-white/90">
                  {{ formatTime(absensiData.tanggalAbsenMasuk) }}
                </p>
              </div>
              <div v-if="absensiData.lokasiMasuk">
                <p class="mb-2 text-xs font-medium text-gray-500 dark:text-gray-400">Lokasi</p>
                <p class="text-sm font-semibold text-gray-800 dark:text-white/90">
                  {{ absensiData.lokasiMasuk }}
                </p>
              </div>
            </div>
          </div>

          <!-- Detail Absensi Keluar -->
          <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="mb-6 flex items-center gap-3">
              <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-orange-100 dark:bg-orange-900/30">
                <svg
                  class="h-5 w-5 text-orange-600 dark:text-orange-400"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                  />
                </svg>
              </div>
              <h4 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                Absensi Keluar
              </h4>
            </div>
            <div class="space-y-6">
              <div>
                <p class="mb-2 text-xs font-medium text-gray-500 dark:text-gray-400">Tanggal</p>
                <p class="text-sm font-semibold text-gray-800 dark:text-white/90">
                  {{ formatDate(absensiData.tanggalAbsenKeluar) }}
                </p>
              </div>
              <div>
                <p class="mb-2 text-xs font-medium text-gray-500 dark:text-gray-400">Waktu</p>
                <p class="text-sm font-semibold text-gray-800 dark:text-white/90">
                  {{ formatTime(absensiData.tanggalAbsenKeluar) }}
                </p>
              </div>
              <div v-if="absensiData.lokasiKeluar">
                <p class="mb-2 text-xs font-medium text-gray-500 dark:text-gray-400">Lokasi</p>
                <p class="text-sm font-semibold text-gray-800 dark:text-white/90">
                  {{ absensiData.lokasiKeluar }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Ringkasan -->
        <div class="rounded-2xl border border-gray-200 bg-gradient-to-br from-brand-50 to-brand-100 p-6 dark:border-gray-800 dark:from-brand-900/20 dark:to-brand-900/10">
          <div class="mb-6 flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-brand-500">
              <svg
                class="h-5 w-5 text-white"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
            </div>
            <h4 class="text-lg font-semibold text-gray-800 dark:text-white/90">
              Ringkasan
            </h4>
          </div>
          <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div>
              <p class="mb-2 text-xs font-medium text-gray-600 dark:text-gray-400">Total Jam Kerja</p>
              <p class="text-2xl font-bold text-brand-600 dark:text-brand-400">
                {{ calculateTotalKerja() }}
              </p>
            </div>
            <div>
              <p class="mb-2 text-xs font-medium text-gray-600 dark:text-gray-400">Status</p>
              <span
                :class="getStatusClass()"
                class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-medium"
              >
                <span
                  :class="getStatusDotClass()"
                  class="h-2 w-2 rounded-full"
                ></span>
                {{ getStatus() }}
              </span>
            </div>
          </div>
        </div>

        <!-- Informasi Tambahan -->
        <div v-if="absensiData.catatan" class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
          <h4 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white/90">
            Catatan
          </h4>
          <p class="text-sm text-gray-700 dark:text-gray-300">
            {{ absensiData.catatan }}
          </p>
        </div>
      </div>

      <div v-else class="flex flex-col items-center justify-center py-20">
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
          Data absensi tidak ditemukan
        </p>
        <button
          @click="handleBack"
          class="mt-4 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600"
        >
          Kembali ke Daftar Absensi
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

const currentPageTitle = computed(() => 'Detail Absensi')
const loading = ref(true)
const absensiData = ref<any>(null)

// Sample data - in production, fetch from API
const generateSampleData = () => {
  const namas = [
    'Ahmad Hidayat', 'Siti Nurhaliza', 'Budi Santoso', 'Dewi Lestari', 'Eko Prasetyo',
    'Fitri Handayani', 'Guntur Wibowo', 'Hesti Rahayu', 'Indra Wijaya', 'Joko Susilo',
    'Kartika Putri', 'Lukman Hakim', 'Maya Sari', 'Nanda Pratama', 'Olivia Wijaya',
    'Putra Ramadhan', 'Qori Anisa', 'Rizky Pratama', 'Salsabila Putri', 'Taufik Hidayat',
  ]
  
  const id = route.params.id as string
  const idNum = parseInt(id) || 1
  const namaIndex = (idNum - 1) % namas.length
  
  const date = new Date('2024-01-01')
  date.setDate(date.getDate() + Math.floor((idNum - 1) / 20))
  
  // Random time between 7:00 - 9:00 for masuk
  const masukHour = Math.floor(Math.random() * 2) + 7
  const masukMinute = Math.floor(Math.random() * 60)
  const masukDate = new Date(date)
  masukDate.setHours(masukHour, masukMinute, 0, 0)
  
  // Random time between 16:00 - 18:00 for keluar
  const keluarHour = Math.floor(Math.random() * 2) + 16
  const keluarMinute = Math.floor(Math.random() * 60)
  const keluarDate = new Date(date)
  keluarDate.setHours(keluarHour, keluarMinute, 0, 0)
  
  // Ensure keluar is after masuk
  if (keluarDate <= masukDate) {
    keluarDate.setHours(masukHour + 8, masukMinute, 0, 0)
  }
  
  return {
    id: id,
    nama: namas[namaIndex],
    noInduk: `K${String(idNum).padStart(3, '0')}`,
    tanggalAbsenMasuk: masukDate.toISOString(),
    tanggalAbsenKeluar: keluarDate.toISOString(),
    lokasiMasuk: 'Kantor Pusat - Jakarta',
    lokasiKeluar: 'Kantor Pusat - Jakarta',
    catatan: idNum % 3 === 0 ? 'Tidak ada catatan khusus' : null,
  }
}

// Load data
const loadData = async () => {
  loading.value = true
  try {
    // TODO: Fetch from API
    // const response = await fetch(`/admin/api/absensi/${route.params.id}`)
    // const data = await response.json()
    // absensiData.value = data
    
    // For now, use sample data
    setTimeout(() => {
      absensiData.value = generateSampleData()
      loading.value = false
    }, 500)
  } catch (error) {
    console.error('Error loading absensi data:', error)
    loading.value = false
  }
}

// Format date
const formatDate = (dateString: string) => {
  if (!dateString) return '-'
  return new Date(dateString).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}

// Format time
const formatTime = (dateString: string) => {
  if (!dateString) return '-'
  return new Date(dateString).toLocaleTimeString('id-ID', {
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit',
  })
}

// Calculate total kerja
const calculateTotalKerja = () => {
  if (!absensiData.value?.tanggalAbsenMasuk || !absensiData.value?.tanggalAbsenKeluar) {
    return '0 jam'
  }
  
  const masuk = new Date(absensiData.value.tanggalAbsenMasuk).getTime()
  const keluar = new Date(absensiData.value.tanggalAbsenKeluar).getTime()
  const diff = keluar - masuk
  const hours = diff / (1000 * 60 * 60)
  const hoursInt = Math.floor(hours)
  const minutes = Math.floor((hours - hoursInt) * 60)
  
  if (minutes > 0) {
    return `${hoursInt} jam ${minutes} menit`
  }
  return `${hoursInt} jam`
}

// Get status
const getStatus = () => {
  if (!absensiData.value?.tanggalAbsenMasuk) {
    return 'Belum Absen Masuk'
  }
  if (!absensiData.value?.tanggalAbsenKeluar) {
    return 'Belum Absen Keluar'
  }
  
  // Check if masuk is on time (before 9:00)
  const masukDate = new Date(absensiData.value.tanggalAbsenMasuk)
  const jamMasuk = masukDate.getHours()
  const menitMasuk = masukDate.getMinutes()
  
  if (jamMasuk > 9 || (jamMasuk === 9 && menitMasuk > 0)) {
    return 'Terlambat'
  }
  
  return 'Hadir'
}

// Get status class
const getStatusClass = () => {
  const status = getStatus()
  if (status === 'Hadir') {
    return 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'
  }
  if (status === 'Terlambat') {
    return 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400'
  }
  return 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-400'
}

// Get status dot class
const getStatusDotClass = () => {
  const status = getStatus()
  if (status === 'Hadir') {
    return 'bg-green-500'
  }
  if (status === 'Terlambat') {
    return 'bg-orange-500'
  }
  return 'bg-gray-500'
}

// Handle back
const handleBack = () => {
  router.push('/operasional/absensi')
}

// Load data on mount
onMounted(() => {
  loadData()
})
</script>

<style scoped>
/* Custom styles if needed */
</style>

