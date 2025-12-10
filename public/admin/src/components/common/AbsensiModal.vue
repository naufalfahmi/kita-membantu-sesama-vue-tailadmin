<template>
  <div class="fixed inset-0 flex items-center justify-center overflow-y-auto z-99999">
    <!-- Backdrop -->
    <div
      class="fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-[32px]"
      aria-hidden="true"
      @click="$emit('close')"
    ></div>

    <!-- Modal Content -->
    <div
      class="relative w-full max-w-md mx-4 bg-white rounded-2xl shadow-theme-lg dark:bg-gray-900 z-50"
      @click.stop
    >
      <!-- Header -->
      <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-800">
        <h3 class="text-xl font-semibold text-gray-800 dark:text-white/90">Absensi</h3>
        <button
          @click="$emit('close')"
          class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
        >
          <svg
            class="fill-current"
            width="24"
            height="24"
            viewBox="0 0 24 24"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              fill-rule="evenodd"
              clip-rule="evenodd"
              d="M6.21967 7.28131C5.92678 6.98841 5.92678 6.51354 6.21967 6.22065C6.51256 5.92775 6.98744 5.92775 7.28033 6.22065L11.999 10.9393L16.7176 6.22078C17.0105 5.92789 17.4854 5.92788 17.7782 6.22078C18.0711 6.51367 18.0711 6.98855 17.7782 7.28144L13.0597 12L17.7782 16.7186C18.0711 17.0115 18.0711 17.4863 17.7782 17.7792C17.4854 18.0721 17.0105 18.0721 16.7176 17.7792L11.999 13.0607L7.28033 17.7794C6.98744 18.0722 6.51256 18.0722 6.21967 17.7794C5.92678 17.4865 5.92678 17.0116 6.21967 16.7187L10.9384 12L6.21967 7.28131Z"
              fill=""
            />
          </svg>
        </button>
      </div>

      <!-- Body -->
      <div class="p-6">
        <!-- Loading State -->
        <div v-if="loading" class="text-center py-8">
          <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-brand-500 mb-4"></div>
          <p class="text-gray-600 dark:text-gray-400">{{ loadingMessage }}</p>
        </div>

        <!-- Error State -->
        <div v-else-if="error && !showManualLocation" class="text-center py-8">
          <div class="mb-4">
            <svg
              class="mx-auto h-12 w-12 text-red-500"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
          </div>
          <p class="text-red-600 dark:text-red-400 font-medium mb-2">{{ error }}</p>
          <div class="flex flex-col gap-2 mt-4">
            <button
              @click="retryGetLocation"
              class="px-4 py-2 bg-brand-500 text-white rounded-lg hover:bg-brand-600"
            >
              Coba Lagi
            </button>
            <button
              v-if="isLocalhost"
              @click="showManualLocation = true; error = null"
              class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 text-sm"
            >
              Input Lokasi Manual (Dev Mode)
            </button>
          </div>
        </div>

        <!-- Manual Location Input (Dev Mode) -->
        <div v-else-if="showManualLocation" class="py-4">
          <div class="mb-4 p-3 rounded-lg bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800">
            <p class="text-xs text-yellow-700 dark:text-yellow-300">
              ⚠️ Mode Development: Input lokasi manual karena geolocation tidak tersedia di localhost tanpa HTTPS.
            </p>
          </div>
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">Latitude</label>
              <input
                v-model.number="manualLatitude"
                type="number"
                step="any"
                placeholder="-6.2088"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-brand-500 dark:bg-gray-800 dark:text-white"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">Longitude</label>
              <input
                v-model.number="manualLongitude"
                type="number"
                step="any"
                placeholder="106.8456"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-brand-500 dark:bg-gray-800 dark:text-white"
              />
            </div>
            <div class="flex gap-2">
              <button
                @click="useKantorLocation"
                class="flex-1 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 text-sm"
              >
                Gunakan Lokasi Kantor
              </button>
              <button
                @click="applyManualLocation"
                :disabled="!manualLatitude || !manualLongitude"
                class="flex-1 px-4 py-2 bg-brand-500 text-white rounded-lg hover:bg-brand-600 text-sm disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Terapkan
              </button>
            </div>
            <button
              @click="showManualLocation = false"
              class="w-full px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 text-sm dark:bg-gray-700 dark:text-gray-300"
            >
              Batal
            </button>
          </div>
        </div>

        <!-- Success State -->
        <div v-else-if="attendanceSuccess" class="text-center py-8">
          <div class="mb-4">
            <svg
              class="mx-auto h-16 w-16 text-green-500"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
          </div>
          <p class="text-green-600 dark:text-green-400 font-semibold text-lg mb-2">
            {{ attendanceType === 'masuk' ? 'Absensi Masuk Berhasil!' : 'Absensi Keluar Berhasil!' }}
          </p>
          <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
            {{ attendanceTime }}
          </p>
          <!-- Summary Jam Kerja (untuk absen keluar) -->
          <div v-if="attendanceType === 'keluar' && workSummary" class="mt-4 p-4 rounded-lg bg-gray-50 dark:bg-gray-800 text-left">
            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Summary Jam Kerja:</p>
            <div class="space-y-1 text-sm">
              <p class="text-gray-600 dark:text-gray-400">
                <span class="font-medium">Masuk:</span> {{ workSummary.masukTime }}
              </p>
              <p class="text-gray-600 dark:text-gray-400">
                <span class="font-medium">Keluar:</span> {{ workSummary.keluarTime }}
              </p>
              <p class="text-gray-600 dark:text-gray-400">
                <span class="font-medium">Total Jam Kerja:</span> 
                <span class="text-gray-800 dark:text-gray-200 font-semibold">
                  {{ workSummary.totalHours }} jam
                </span>
              </p>
            </div>
          </div>
          <button
            @click="handleClose"
            class="mt-4 px-6 py-2 bg-brand-500 text-white rounded-lg hover:bg-brand-600"
          >
            Tutup
          </button>
        </div>

        <!-- Form State -->
        <div v-else>
          <!-- User Info -->
          <div v-if="todayStatus" class="mb-6 p-4 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800">
            <p class="text-sm font-medium text-blue-800 dark:text-blue-300 mb-2">Informasi Anda:</p>
            <div class="space-y-1 text-sm">
              <p v-if="todayStatus.tipe_absensi" class="text-gray-600 dark:text-gray-400">
                <span class="font-medium">Tipe Absensi:</span> {{ todayStatus.tipe_absensi.nama }}
                <span v-if="todayStatus.tipe_absensi.jam_masuk">
                  ({{ formatTimeOnly(todayStatus.tipe_absensi.jam_masuk) }} - {{ formatTimeOnly(todayStatus.tipe_absensi.jam_keluar) }})
                </span>
              </p>
              <p v-if="todayStatus.kantor_cabang" class="text-gray-600 dark:text-gray-400">
                <span class="font-medium">Kantor:</span> {{ todayStatus.kantor_cabang.nama }}
              </p>
              <p v-if="todayStatus.kantor_cabang?.radius" class="text-gray-600 dark:text-gray-400">
                <span class="font-medium">Radius:</span> {{ todayStatus.kantor_cabang.radius }} meter
              </p>
            </div>
          </div>

          <!-- No Kantor Cabang Warning -->
          <div v-if="todayStatus && !todayStatus.kantor_cabang" class="mb-6 p-4 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
            <div class="flex items-start gap-2">
              <svg class="w-5 h-5 text-red-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
              <div>
                <p class="text-sm font-medium text-red-800 dark:text-red-300">Kantor Cabang Belum Diatur</p>
                <p class="text-xs text-red-700 dark:text-red-400 mt-1">
                  Silakan hubungi admin untuk mengatur kantor cabang Anda terlebih dahulu.
                </p>
              </div>
            </div>
          </div>

          <!-- Location Info -->
          <div v-if="userLocation && todayStatus?.kantor_cabang" class="mb-6 p-4 rounded-lg bg-gray-50 dark:bg-gray-800">
            <div class="flex items-start gap-3">
              <svg
                class="w-5 h-5 text-brand-500 mt-0.5"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                />
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                />
              </svg>
              <div class="flex-1">
                <p class="text-sm font-medium text-gray-800 dark:text-white/90 mb-1">
                  Lokasi Terdeteksi
                </p>
                <p class="text-xs text-gray-600 dark:text-gray-400">
                  Lat: {{ userLocation.latitude.toFixed(6) }}, Lng: {{ userLocation.longitude.toFixed(6) }}
                </p>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                  Jarak dari kantor: <span :class="distanceClass">{{ distanceText }}</span>
                </p>
              </div>
            </div>
          </div>

          <!-- Get Location Button -->
          <div v-if="!userLocation && todayStatus?.kantor_cabang" class="mb-6">
            <button
              @click="getLocation"
              class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              Dapatkan Lokasi GPS
            </button>
          </div>

          <!-- Attendance Type Selection (based on today's status) -->
          <div v-if="todayStatus && todayStatus.kantor_cabang" class="mb-6">
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Tipe Absensi
            </label>
            <div class="grid grid-cols-2 gap-3">
              <button
                @click="attendanceType = 'masuk'"
                :disabled="todayStatus.has_clock_in"
                :class="[
                  attendanceType === 'masuk' && !todayStatus.has_clock_in
                    ? 'bg-brand-500 text-white border-brand-500'
                    : todayStatus.has_clock_in
                    ? 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed dark:bg-gray-800 dark:text-gray-600 dark:border-gray-700'
                    : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700 dark:hover:bg-gray-700',
                ]"
                class="px-4 py-3 rounded-lg border font-medium transition-colors"
              >
                <span v-if="todayStatus.has_clock_in">✓ Sudah Masuk</span>
                <span v-else>Masuk</span>
              </button>
              <button
                @click="attendanceType = 'keluar'"
                :disabled="!todayStatus.has_clock_in || todayStatus.has_clock_out"
                :class="[
                  attendanceType === 'keluar' && todayStatus.has_clock_in && !todayStatus.has_clock_out
                    ? 'bg-brand-500 text-white border-brand-500'
                    : !todayStatus.has_clock_in || todayStatus.has_clock_out
                    ? 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed dark:bg-gray-800 dark:text-gray-600 dark:border-gray-700'
                    : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700 dark:hover:bg-gray-700',
                ]"
                class="px-4 py-3 rounded-lg border font-medium transition-colors"
              >
                <span v-if="todayStatus.has_clock_out">✓ Sudah Keluar</span>
                <span v-else-if="!todayStatus.has_clock_in">Belum Masuk</span>
                <span v-else>Keluar</span>
              </button>
            </div>
          </div>

          <!-- Today's Attendance Info -->
          <div v-if="todayStatus?.attendance" class="mb-6 p-4 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800">
            <p class="text-sm font-medium text-green-800 dark:text-green-300 mb-2">Absensi Hari Ini:</p>
            <div class="space-y-1 text-sm">
              <p class="text-gray-600 dark:text-gray-400">
                <span class="font-medium">Masuk:</span> {{ formatDateTime(todayStatus.attendance.jam_masuk) }}
              </p>
              <p v-if="todayStatus.attendance.jam_keluar" class="text-gray-600 dark:text-gray-400">
                <span class="font-medium">Keluar:</span> {{ formatDateTime(todayStatus.attendance.jam_keluar) }}
              </p>
              <p class="text-gray-600 dark:text-gray-400">
                <span class="font-medium">Status:</span> 
                <span :class="getStatusClass(todayStatus.attendance.status)">
                  {{ getStatusLabel(todayStatus.attendance.status) }}
                </span>
              </p>
            </div>
          </div>

          <!-- Catatan / Alasan -->
          <div v-if="userLocation && canSubmit" class="mb-6">
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Catatan (Opsional)
            </label>
            <textarea
              v-model="catatan"
              rows="2"
              placeholder="Masukkan catatan jika diperlukan..."
              class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            ></textarea>
          </div>

          <!-- Out of Radius Warning -->
          <div v-if="userLocation && !isWithinAllowedRadius" class="mb-6 p-4 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
            <div class="flex items-start gap-2">
              <svg class="w-5 h-5 text-red-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
              <div>
                <p class="text-sm font-medium text-red-800 dark:text-red-300">Di Luar Radius Kantor</p>
                <p class="text-xs text-red-700 dark:text-red-400 mt-1">
                  Anda harus berada dalam radius {{ todayStatus?.kantor_cabang?.radius || 100 }} meter dari kantor untuk absen.
                </p>
              </div>
            </div>
          </div>

          <!-- Submit Button -->
          <button
            v-if="todayStatus?.kantor_cabang"
            @click="submitAttendance"
            :disabled="!canSubmit || submitting"
            :class="[
              canSubmit && !submitting
                ? 'bg-brand-500 hover:bg-brand-600 text-white'
                : 'bg-gray-300 text-gray-500 cursor-not-allowed dark:bg-gray-700 dark:text-gray-500',
            ]"
            class="w-full px-4 py-3 rounded-lg font-medium transition-colors flex items-center justify-center gap-2"
          >
            <span v-if="submitting" class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white"></span>
            {{ submitting ? 'Memproses...' : (attendanceType === 'masuk' ? 'Absensi Masuk' : 'Absensi Keluar') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'

const emit = defineEmits(['close'])

// State
const loading = ref(false)
const loadingMessage = ref('Memuat data...')
const error = ref<string | null>(null)
const userLocation = ref<{ latitude: number; longitude: number } | null>(null)
const attendanceSuccess = ref(false)
const attendanceType = ref<'masuk' | 'keluar'>('masuk')
const attendanceTime = ref('')
const catatan = ref('')
const submitting = ref(false)
const workSummary = ref<any>(null)
const todayStatus = ref<any>(null)

// Manual location state (for development)
const showManualLocation = ref(false)
const manualLatitude = ref<number | null>(null)
const manualLongitude = ref<number | null>(null)

// Check if running on localhost
const isLocalhost = computed(() => {
  return window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1'
})

// Get CSRF token
const getCsrfToken = (): string => {
  return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
}

// Fetch today's attendance status
const fetchTodayStatus = async () => {
  loading.value = true
  loadingMessage.value = 'Memuat data absensi...'
  error.value = null

  try {
    const response = await fetch('/admin/api/absensi/today-status', { credentials: 'same-origin' })

    if (!response.ok) {
      if (response.status === 401) {
        error.value = 'Sesi telah berakhir. Silakan login ulang.'
        return
      }
      throw new Error(`HTTP error! status: ${response.status}`)
    }

    const result = await response.json()
    if (result.success) {
      todayStatus.value = result.data
      
      // Auto-select attendance type based on status
      if (result.data.has_clock_in && !result.data.has_clock_out) {
        attendanceType.value = 'keluar'
      } else {
        attendanceType.value = 'masuk'
      }
    } else {
      error.value = result.message || 'Gagal memuat data absensi'
    }
  } catch (err: any) {
    error.value = err.message || 'Terjadi kesalahan saat memuat data'
  } finally {
    loading.value = false
  }
}

// Calculate distance using Haversine formula
const calculateDistance = (lat1: number, lon1: number, lat2: number, lon2: number): number => {
  const earthRadius = 6371000 // Earth's radius in meters
  const latDiff = (lat2 - lat1) * Math.PI / 180
  const lonDiff = (lon2 - lon1) * Math.PI / 180

  const a = Math.sin(latDiff / 2) * Math.sin(latDiff / 2) +
            Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
            Math.sin(lonDiff / 2) * Math.sin(lonDiff / 2)

  const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a))
  return earthRadius * c
}

// Computed properties
const distance = computed(() => {
  if (!userLocation.value || !todayStatus.value?.kantor_cabang) return null
  return calculateDistance(
    userLocation.value.latitude,
    userLocation.value.longitude,
    parseFloat(todayStatus.value.kantor_cabang.latitude),
    parseFloat(todayStatus.value.kantor_cabang.longitude)
  )
})

const isWithinAllowedRadius = computed(() => {
  if (distance.value === null) return false
  const allowedRadius = todayStatus.value?.kantor_cabang?.radius || 100
  return distance.value <= allowedRadius
})

const distanceText = computed(() => {
  if (distance.value === null) return 'Menghitung...'
  if (distance.value < 1000) {
    return `${distance.value.toFixed(0)} meter`
  }
  return `${(distance.value / 1000).toFixed(2)} km`
})

const distanceClass = computed(() => {
  if (!isWithinAllowedRadius.value) {
    return 'text-red-600 dark:text-red-400 font-semibold'
  }
  return 'text-green-600 dark:text-green-400 font-semibold'
})

const canSubmit = computed(() => {
  if (!userLocation.value || !todayStatus.value?.kantor_cabang) return false
  if (!isWithinAllowedRadius.value) return false
  
  if (attendanceType.value === 'masuk') {
    return !todayStatus.value.has_clock_in
  } else {
    return todayStatus.value.has_clock_in && !todayStatus.value.has_clock_out
  }
})

// Methods
const getLocation = async () => {
  loading.value = true
  loadingMessage.value = 'Mengambil lokasi GPS...'
  error.value = null

  try {
    const position = await new Promise<GeolocationPosition>((resolve, reject) => {
      navigator.geolocation.getCurrentPosition(resolve, reject, {
        enableHighAccuracy: true,
        timeout: 10000,
        maximumAge: 0,
      })
    })

    userLocation.value = {
      latitude: position.coords.latitude,
      longitude: position.coords.longitude,
    }
  } catch (err: any) {
    if (err.code === 1) {
      error.value = 'Akses lokasi ditolak. Silakan izinkan akses lokasi di pengaturan browser.'
    } else if (err.code === 2) {
      error.value = 'Lokasi tidak dapat ditentukan. Pastikan GPS aktif.'
    } else if (err.code === 3) {
      error.value = 'Waktu tunggu habis. Silakan coba lagi.'
    } else {
      error.value = err.message || 'Gagal mengambil lokasi. Silakan coba lagi.'
    }
  } finally {
    loading.value = false
  }
}

const retryGetLocation = () => {
  error.value = null
  showManualLocation.value = false
  fetchTodayStatus()
}

// Manual location methods (for development)
const useKantorLocation = () => {
  if (todayStatus.value?.kantor_cabang) {
    manualLatitude.value = parseFloat(todayStatus.value.kantor_cabang.latitude)
    manualLongitude.value = parseFloat(todayStatus.value.kantor_cabang.longitude)
  }
}

const applyManualLocation = () => {
  if (manualLatitude.value && manualLongitude.value) {
    userLocation.value = {
      latitude: manualLatitude.value,
      longitude: manualLongitude.value,
    }
    showManualLocation.value = false
    error.value = null
  }
}

const submitAttendance = async () => {
  if (!canSubmit.value || !userLocation.value) return

  submitting.value = true
  error.value = null

  try {
    // Get CSRF token first
    const tokenRes = await fetch('/admin/api/csrf-token', { credentials: 'same-origin' })
    const tokenJson = await tokenRes.json()

    const endpoint = attendanceType.value === 'masuk' 
      ? '/admin/api/absensi/clock-in' 
      : '/admin/api/absensi/clock-out'

    const payload: any = {
      latitude: userLocation.value.latitude,
      longitude: userLocation.value.longitude,
    }

    if (catatan.value) {
      if (attendanceType.value === 'masuk') {
        payload.catatan = catatan.value
      } else {
        payload.alasan = catatan.value
      }
    }

    const response = await fetch(endpoint, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': tokenJson.csrf_token,
      },
      credentials: 'same-origin',
      body: JSON.stringify(payload),
    })

    const result = await response.json()

    if (result.success) {
      attendanceSuccess.value = true
      attendanceTime.value = new Date().toLocaleString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
      })

      // Set work summary for clock out
      if (attendanceType.value === 'keluar' && result.data) {
        workSummary.value = {
          masukTime: formatDateTime(result.data.jam_masuk),
          keluarTime: formatDateTime(result.data.jam_keluar),
          totalHours: result.data.total_jam_kerja,
        }
      }
    } else {
      error.value = result.message || 'Gagal melakukan absensi'
    }
  } catch (err: any) {
    error.value = err.message || 'Terjadi kesalahan saat mengirim data'
  } finally {
    submitting.value = false
  }
}

const formatDateTime = (dateString: string | null) => {
  if (!dateString) return '-'
  return new Date(dateString).toLocaleString('id-ID', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

const formatTimeOnly = (timeString: string | null) => {
  if (!timeString) return '-'
  // Handle time string like "08:00:00" or datetime
  if (timeString.includes('T') || timeString.includes(' ')) {
    return new Date(timeString).toLocaleTimeString('id-ID', {
      hour: '2-digit',
      minute: '2-digit',
    })
  }
  // Just time string
  return timeString.substring(0, 5)
}

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

const getStatusClass = (status: string) => {
  const classes: Record<string, string> = {
    hadir: 'text-green-600 dark:text-green-400 font-medium',
    terlambat: 'text-orange-600 dark:text-orange-400 font-medium',
    pulang_awal: 'text-yellow-600 dark:text-yellow-400 font-medium',
    tidak_hadir: 'text-red-600 dark:text-red-400 font-medium',
    izin: 'text-blue-600 dark:text-blue-400 font-medium',
    sakit: 'text-purple-600 dark:text-purple-400 font-medium',
    cuti: 'text-indigo-600 dark:text-indigo-400 font-medium',
  }
  return classes[status] || 'text-gray-600 dark:text-gray-400'
}

const handleClose = () => {
  emit('close')
}

onMounted(() => {
  fetchTodayStatus()
})
</script>

