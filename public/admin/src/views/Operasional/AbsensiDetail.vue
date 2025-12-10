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
          <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div>
              <p class="mb-2 text-xs font-medium text-gray-500 dark:text-gray-400">Nama</p>
              <p class="text-sm font-semibold text-gray-800 dark:text-white/90">
                {{ absensiData.user?.name || '-' }}
              </p>
            </div>
            <div>
              <p class="mb-2 text-xs font-medium text-gray-500 dark:text-gray-400">No Induk</p>
              <p class="text-sm font-semibold text-gray-800 dark:text-white/90">
                {{ absensiData.user?.no_induk || '-' }}
              </p>
            </div>
            <div>
              <p class="mb-2 text-xs font-medium text-gray-500 dark:text-gray-400">Kantor Cabang</p>
              <p class="text-sm font-semibold text-gray-800 dark:text-white/90">
                {{ absensiData.kantor_cabang?.nama || '-' }}
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
            <div class="space-y-4">
              <div>
                <p class="mb-1 text-xs font-medium text-gray-500 dark:text-gray-400">Waktu</p>
                <p class="text-sm font-semibold text-gray-800 dark:text-white/90">
                  {{ formatDateTime(absensiData.jam_masuk) }}
                </p>
              </div>
              <div v-if="absensiData.latitude_masuk && absensiData.longitude_masuk">
                <p class="mb-1 text-xs font-medium text-gray-500 dark:text-gray-400">Koordinat</p>
                <p class="text-sm text-gray-800 dark:text-white/90">
                  {{ absensiData.latitude_masuk }}, {{ absensiData.longitude_masuk }}
                </p>
              </div>
              <div v-if="absensiData.jarak_masuk !== null">
                <p class="mb-1 text-xs font-medium text-gray-500 dark:text-gray-400">Jarak dari Kantor</p>
                <p class="text-sm text-gray-800 dark:text-white/90">
                  {{ absensiData.jarak_masuk }} meter
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
            <div v-if="absensiData.jam_keluar" class="space-y-4">
              <div>
                <p class="mb-1 text-xs font-medium text-gray-500 dark:text-gray-400">Waktu</p>
                <p class="text-sm font-semibold text-gray-800 dark:text-white/90">
                  {{ formatDateTime(absensiData.jam_keluar) }}
                </p>
              </div>
              <div v-if="absensiData.latitude_keluar && absensiData.longitude_keluar">
                <p class="mb-1 text-xs font-medium text-gray-500 dark:text-gray-400">Koordinat</p>
                <p class="text-sm text-gray-800 dark:text-white/90">
                  {{ absensiData.latitude_keluar }}, {{ absensiData.longitude_keluar }}
                </p>
              </div>
              <div v-if="absensiData.jarak_keluar !== null">
                <p class="mb-1 text-xs font-medium text-gray-500 dark:text-gray-400">Jarak dari Kantor</p>
                <p class="text-sm text-gray-800 dark:text-white/90">
                  {{ absensiData.jarak_keluar }} meter
                </p>
              </div>
            </div>
            <div v-else class="text-center py-8 text-gray-500 dark:text-gray-400">
              <p>Belum absen keluar</p>
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
          <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div>
              <p class="mb-2 text-xs font-medium text-gray-600 dark:text-gray-400">Total Jam Kerja</p>
              <p class="text-2xl font-bold text-brand-600 dark:text-brand-400">
                {{ absensiData.total_jam_kerja !== null ? `${absensiData.total_jam_kerja} jam` : '-' }}
              </p>
            </div>
            <div>
              <p class="mb-2 text-xs font-medium text-gray-600 dark:text-gray-400">Status</p>
              <span
                :class="getStatusClass(absensiData.status)"
                class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-medium"
              >
                <span
                  :class="getStatusDotClass(absensiData.status)"
                  class="h-2 w-2 rounded-full"
                ></span>
                {{ getStatusLabel(absensiData.status) }}
              </span>
            </div>
            <div>
              <p class="mb-2 text-xs font-medium text-gray-600 dark:text-gray-400">Tipe Absensi</p>
              <p class="text-sm font-semibold text-gray-800 dark:text-white/90">
                {{ absensiData.tipe_absensi?.nama || '-' }}
              </p>
            </div>
          </div>
        </div>

        <!-- Catatan & Alasan -->
        <div v-if="absensiData.catatan || absensiData.alasan" class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
          <h4 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white/90">
            Catatan & Alasan
          </h4>
          <div class="space-y-4">
            <div v-if="absensiData.catatan">
              <p class="mb-1 text-xs font-medium text-gray-500 dark:text-gray-400">Catatan</p>
              <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">
                {{ absensiData.catatan }}
              </p>
            </div>
            <div v-if="absensiData.alasan">
              <p class="mb-1 text-xs font-medium text-gray-500 dark:text-gray-400">Alasan</p>
              <p class="text-sm text-gray-700 dark:text-gray-300">
                {{ absensiData.alasan }}
              </p>
            </div>
          </div>
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
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'

const route = useRoute()
const router = useRouter()

const currentPageTitle = computed(() => 'Detail Absensi')
const loading = ref(true)
const absensiData = ref<any>(null)

// Get CSRF token
const getCsrfToken = (): string => {
  return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
}

// Load data from API
const loadData = async () => {
  loading.value = true
  try {
    const id = route.params.id as string
    const response = await fetch(`/admin/api/absensi/${id}`, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': getCsrfToken(),
      },
      credentials: 'same-origin',
    })

    const result = await response.json()
    if (result.success) {
      absensiData.value = result.data
    } else {
      console.error('Failed to load data:', result.message)
      absensiData.value = null
    }
  } catch (error) {
    console.error('Error loading absensi data:', error)
    absensiData.value = null
  } finally {
    loading.value = false
  }
}

// Format datetime
const formatDateTime = (dateString: string | null) => {
  if (!dateString) return '-'
  return new Date(dateString).toLocaleString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit',
  })
}

// Get status label
const getStatusLabel = (status: string) => {
  const labels: Record<string, string> = {
    hadir: 'Hadir',
    terlambat: 'Terlambat',
    pulang_awal: 'Pulang Awal',
    tidak_hadir: 'Tidak Hadir',
    izin: 'Izin',
    sakit: 'Sakit',
    cuti: 'Cuti',
  }
  return labels[status] || status
}

// Get status class
const getStatusClass = (status: string) => {
  const classes: Record<string, string> = {
    hadir: 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
    terlambat: 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400',
    pulang_awal: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
    tidak_hadir: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
    izin: 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
    sakit: 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400',
    cuti: 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400',
  }
  return classes[status] || 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-400'
}

// Get status dot class
const getStatusDotClass = (status: string) => {
  const classes: Record<string, string> = {
    hadir: 'bg-green-500',
    terlambat: 'bg-orange-500',
    pulang_awal: 'bg-yellow-500',
    tidak_hadir: 'bg-red-500',
    izin: 'bg-blue-500',
    sakit: 'bg-purple-500',
    cuti: 'bg-indigo-500',
  }
  return classes[status] || 'bg-gray-500'
}

// Handle back
const handleBack = () => {
  router.push('/operasional/absensi')
}

onMounted(() => {
  loadData()
})
</script>
