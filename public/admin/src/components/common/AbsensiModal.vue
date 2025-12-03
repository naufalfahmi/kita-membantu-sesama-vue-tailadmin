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
          <p class="text-gray-600 dark:text-gray-400">Mengambil lokasi Anda...</p>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="text-center py-8">
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
          <button
            @click="getLocation"
            class="mt-4 px-4 py-2 bg-brand-500 text-white rounded-lg hover:bg-brand-600"
          >
            Coba Lagi
          </button>
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
                <span :class="workSummary.isLessThanMinimum ? 'text-orange-600 dark:text-orange-400 font-semibold' : 'text-gray-800 dark:text-gray-200 font-semibold'">
                  {{ workSummary.totalHours }}
                </span>
              </p>
              <p v-if="workSummary.isLessThanMinimum" class="text-xs text-orange-600 dark:text-orange-400 mt-2">
                ⚠️ Jam kerja kurang dari {{ minimumWorkHours }} jam
              </p>
            </div>
          </div>
          <button
            @click="resetForm"
            class="mt-4 px-6 py-2 bg-brand-500 text-white rounded-lg hover:bg-brand-600"
          >
            Tutup
          </button>
        </div>

        <!-- Form State -->
        <div v-else>
          <!-- Manual Location Input (for desktop testing) -->
          <div class="mb-6 p-3 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800">
            <div class="flex items-center justify-between mb-2">
              <label class="text-sm font-medium text-blue-800 dark:text-blue-300">
                Input Lokasi Manual (Testing Desktop)
              </label>
              <div class="flex gap-2">
                <button
                  v-if="!useManualLocation"
                  @click="getLocation"
                  class="text-xs px-2 py-1 text-blue-600 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/30 rounded"
                >
                  Gunakan GPS
                </button>
                <button
                  @click="useManualLocation = !useManualLocation"
                  class="text-xs px-2 py-1 text-blue-600 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/30 rounded"
                >
                  {{ useManualLocation ? 'Gunakan GPS' : 'Input Manual' }}
                </button>
              </div>
            </div>
            <div v-if="useManualLocation" class="grid grid-cols-2 gap-3 mt-3">
              <div>
                <input
                  v-model.number="manualLocation.latitude"
                  type="number"
                  step="0.000001"
                  placeholder="Latitude"
                  class="h-10 w-full rounded-lg border border-blue-300 bg-white px-3 py-2 text-xs text-gray-800 placeholder:text-gray-400 focus:border-blue-500 focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 dark:border-blue-700 dark:bg-gray-800 dark:text-white/90 dark:placeholder:text-white/30"
                />
              </div>
              <div>
                <input
                  v-model.number="manualLocation.longitude"
                  type="number"
                  step="0.000001"
                  placeholder="Longitude"
                  class="h-10 w-full rounded-lg border border-blue-300 bg-white px-3 py-2 text-xs text-gray-800 placeholder:text-gray-400 focus:border-blue-500 focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 dark:border-blue-700 dark:bg-gray-800 dark:text-white/90 dark:placeholder:text-white/30"
                />
              </div>
              <div class="col-span-2">
                <button
                  @click="applyManualLocation"
                  class="w-full px-3 py-2 text-xs font-medium text-white bg-blue-500 rounded-lg hover:bg-blue-600"
                >
                  Terapkan Lokasi
                </button>
              </div>
            </div>
            <p v-else class="text-xs text-blue-700 dark:text-blue-400">
              Klik "Input Manual" untuk set koordinat secara manual (berguna untuk testing di desktop)
            </p>
          </div>

          <!-- Location Info -->
          <div v-if="userLocation" class="mb-6 p-4 rounded-lg bg-gray-50 dark:bg-gray-800">
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
                  {{ useManualLocation ? 'Lokasi Manual' : 'Lokasi Terdeteksi' }}
                </p>
                <p class="text-xs text-gray-600 dark:text-gray-400">
                  Lat: {{ userLocation.latitude.toFixed(6) }}, Lng:
                  {{ userLocation.longitude.toFixed(6) }}
                </p>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                  Jarak: <span :class="distanceClass">{{ distanceText }}</span>
                </p>
              </div>
            </div>
          </div>

          <!-- Office Location Config (Default: bisa diubah sesuai kebutuhan) -->
          <div class="mb-6">
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Lokasi Kantor
            </label>
            <div class="grid grid-cols-2 gap-3">
              <div>
                <input
                  v-model.number="officeLocation.latitude"
                  type="number"
                  step="0.000001"
                  placeholder="Latitude"
                  class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                />
              </div>
              <div>
                <input
                  v-model.number="officeLocation.longitude"
                  type="number"
                  step="0.000001"
                  placeholder="Longitude"
                  class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                />
              </div>
            </div>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-500">
              Radius maksimal: {{ maxRadius }} meter
            </p>
          </div>

          <!-- Attendance Type Selection -->
          <div class="mb-6">
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Tipe Absensi
            </label>
            <div class="grid grid-cols-2 gap-3">
              <button
                @click="attendanceType = 'masuk'"
                :class="[
                  attendanceType === 'masuk'
                    ? 'bg-brand-500 text-white border-brand-500'
                    : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700 dark:hover:bg-gray-700',
                ]"
                class="px-4 py-3 rounded-lg border font-medium transition-colors"
              >
                Masuk
              </button>
              <button
                @click="attendanceType = 'keluar'"
                :class="[
                  attendanceType === 'keluar'
                    ? 'bg-brand-500 text-white border-brand-500'
                    : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700 dark:hover:bg-gray-700',
                ]"
                class="px-4 py-3 rounded-lg border font-medium transition-colors"
              >
                Keluar
              </button>
            </div>
          </div>

          <!-- Work Summary & Reason (untuk absen keluar) -->
          <div v-if="attendanceType === 'keluar' && checkInData" class="mb-6 p-4 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800">
            <p class="text-sm font-medium text-blue-800 dark:text-blue-300 mb-3">Summary Jam Kerja:</p>
            <div class="space-y-2 text-sm mb-4">
              <div class="flex justify-between">
                <span class="text-gray-600 dark:text-gray-400">Masuk:</span>
                <span class="font-medium text-gray-800 dark:text-gray-200">{{ formatTime(new Date(checkInData.time)) }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600 dark:text-gray-400">Keluar:</span>
                <span class="font-medium text-gray-800 dark:text-gray-200">{{ formatTime(new Date()) }}</span>
              </div>
              <div class="flex justify-between pt-2 border-t border-blue-200 dark:border-blue-700">
                <span class="text-gray-600 dark:text-gray-400 font-medium">Total Jam Kerja:</span>
                <span :class="workHoursLessThanMinimum ? 'text-orange-600 dark:text-orange-400 font-semibold' : 'text-green-600 dark:text-green-400 font-semibold'">
                  {{ calculatedWorkHours }}
                </span>
              </div>
              <p v-if="workHoursLessThanMinimum" class="text-xs text-orange-600 dark:text-orange-400 mt-2">
                ⚠️ Jam kerja kurang dari {{ minimumWorkHours }} jam
              </p>
            </div>
            
            <!-- Input Alasan (jika kurang dari jam minimum) -->
            <div v-if="workHoursLessThanMinimum" class="mt-4">
              <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-400">
                Alasan (Opsional)
              </label>
              <textarea
                v-model="reason"
                rows="3"
                placeholder="Masukkan alasan jika jam kerja kurang dari yang ditetapkan..."
                class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
              ></textarea>
              <p class="mt-1 text-xs text-gray-500 dark:text-gray-500">
                Alasan ini opsional, namun disarankan untuk diisi
              </p>
            </div>
          </div>

          <!-- Warning jika belum ada absen masuk -->
          <div v-if="attendanceType === 'keluar' && !checkInData" class="mb-6 p-4 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
            <div class="flex items-start gap-2">
              <svg class="w-5 h-5 text-red-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
              <div>
                <p class="text-sm font-medium text-red-800 dark:text-red-300">Belum ada absen masuk</p>
                <p class="text-xs text-red-700 dark:text-red-400 mt-1">
                  Silakan lakukan absen masuk terlebih dahulu sebelum absen keluar
                </p>
              </div>
            </div>
          </div>

          <!-- Submit Button -->
          <button
            @click="submitAttendance"
            :disabled="!canSubmit || (attendanceType === 'keluar' && !checkInData)"
            :class="[
              canSubmit && !(attendanceType === 'keluar' && !checkInData)
                ? 'bg-brand-500 hover:bg-brand-600 text-white'
                : 'bg-gray-300 text-gray-500 cursor-not-allowed dark:bg-gray-700 dark:text-gray-500',
            ]"
            class="w-full px-4 py-3 rounded-lg font-medium transition-colors"
          >
            {{ attendanceType === 'masuk' ? 'Absensi Masuk' : 'Absensi Keluar' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { calculateDistance, isWithinRadius, getCurrentLocation, type Coordinates } from '@/utils/geolocation'

defineEmits(['close'])

const loading = ref(false)
const error = ref<string | null>(null)
const userLocation = ref<Coordinates | null>(null)
const attendanceSuccess = ref(false)
const attendanceType = ref<'masuk' | 'keluar'>('masuk')
const attendanceTime = ref('')
const useManualLocation = ref(false) // Input manual untuk desktop testing
const manualLocation = ref<Coordinates>({
  latitude: -6.471838579070418, // Default sama dengan office location untuk testing
  longitude: 106.7721259013444,
})
const reason = ref('') // Alasan jika jam kerja kurang
const minimumWorkHours = ref(8) // Jam kerja minimum (default 8 jam)
const workSummary = ref<any>(null) // Summary jam kerja untuk success state

// Office location (default: bisa disesuaikan dengan lokasi kantor sebenarnya)
const officeLocation = ref<Coordinates>({
  latitude: -6.471838579070418,
  longitude: 106.7721259013444,
})

const maxRadius = ref(50) // 50 meters

// Calculate distance and check if within radius
const distance = computed(() => {
  if (!userLocation.value) return null
  return calculateDistance(userLocation.value, officeLocation.value)
})

const isWithinAllowedRadius = computed(() => {
  if (!userLocation.value) return false
  return isWithinRadius(userLocation.value, officeLocation.value, maxRadius.value)
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

// Check if there's check-in data in localStorage
const checkInData = computed(() => {
  try {
    const stored = localStorage.getItem('attendance_checkin')
    if (stored) {
      return JSON.parse(stored)
    }
    return null
  } catch {
    return null
  }
})

// Calculate work hours
const calculatedWorkHours = computed(() => {
  if (!checkInData.value || attendanceType.value !== 'keluar') return null
  
  const checkInTime = new Date(checkInData.value.time).getTime()
  const checkOutTime = new Date().getTime()
  const diff = checkOutTime - checkInTime
  const hours = diff / (1000 * 60 * 60)
  
  const hoursInt = Math.floor(hours)
  const minutes = Math.floor((hours - hoursInt) * 60)
  
  if (minutes > 0) {
    return `${hoursInt} jam ${minutes} menit`
  }
  return `${hoursInt} jam`
})

// Check if work hours less than minimum
const workHoursLessThanMinimum = computed(() => {
  if (!checkInData.value || attendanceType.value !== 'keluar') return false
  
  const checkInTime = new Date(checkInData.value.time).getTime()
  const checkOutTime = new Date().getTime()
  const diff = checkOutTime - checkInTime
  const hours = diff / (1000 * 60 * 60)
  
  return hours < minimumWorkHours.value
})

// Format time helper
const formatTime = (date: Date) => {
  return date.toLocaleString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit',
  })
}

const canSubmit = computed(() => {
  // Harus dalam radius dan ada lokasi
  return userLocation.value !== null && isWithinAllowedRadius.value
})

const getLocation = async () => {
  loading.value = true
  error.value = null
  userLocation.value = null

  try {
    const location = await getCurrentLocation()
    userLocation.value = location
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

const submitAttendance = () => {
  if (!canSubmit.value) return
  if (attendanceType.value === 'keluar' && !checkInData.value) return

  const now = new Date()
  attendanceTime.value = now.toLocaleString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit',
  })

  if (attendanceType.value === 'masuk') {
    // Simpan data absen masuk ke localStorage
    const checkInData = {
      time: now.toISOString(),
      location: userLocation.value,
      distance: distance.value,
      officeLocation: officeLocation.value,
    }
    localStorage.setItem('attendance_checkin', JSON.stringify(checkInData))
    
    const attendanceData = {
      type: 'masuk',
      time: attendanceTime.value,
      location: userLocation.value,
      distance: distance.value,
      officeLocation: officeLocation.value,
    }
    
    console.log('Attendance Data (Masuk):', attendanceData)
    attendanceSuccess.value = true
  } else {
    // Absen keluar - hitung jam kerja
    const checkInTime = new Date(checkInData.value!.time).getTime()
    const checkOutTime = now.getTime()
    const diff = checkOutTime - checkInTime
    const hours = diff / (1000 * 60 * 60)
    
    const hoursInt = Math.floor(hours)
    const minutes = Math.floor((hours - hoursInt) * 60)
    const totalHoursText = minutes > 0 ? `${hoursInt} jam ${minutes} menit` : `${hoursInt} jam`
    
    // Simpan summary untuk ditampilkan
    workSummary.value = {
      masukTime: formatTime(new Date(checkInData.value!.time)),
      keluarTime: attendanceTime.value,
      totalHours: totalHoursText,
      totalHoursDecimal: hours,
      isLessThanMinimum: hours < minimumWorkHours.value,
    }
    
    const attendanceData = {
      type: 'keluar',
      time: attendanceTime.value,
      location: userLocation.value,
      distance: distance.value,
      officeLocation: officeLocation.value,
      checkInTime: checkInData.value!.time,
      workHours: hours,
      workHoursText: totalHoursText,
      isLessThanMinimum: hours < minimumWorkHours.value,
      reason: reason.value || null,
    }
    
    console.log('Attendance Data (Keluar):', attendanceData)
    
    // Hapus data check-in dari localStorage setelah absen keluar
    localStorage.removeItem('attendance_checkin')
    
    attendanceSuccess.value = true
  }
}

const applyManualLocation = () => {
  if (manualLocation.value.latitude && manualLocation.value.longitude) {
    userLocation.value = {
      latitude: manualLocation.value.latitude,
      longitude: manualLocation.value.longitude,
    }
    error.value = null
  } else {
    error.value = 'Silakan isi latitude dan longitude'
  }
}

const resetForm = () => {
  attendanceSuccess.value = false
  userLocation.value = null
  error.value = null
  attendanceType.value = 'masuk'
  useManualLocation.value = false
  reason.value = ''
  workSummary.value = null
}

onMounted(() => {
  // Tidak otomatis ambil GPS, biarkan user pilih manual atau GPS
  // User bisa klik "Gunakan GPS" atau input manual
})
</script>

