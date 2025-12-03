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
          @click="handleCancel"
          class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]"
        >
          Batal
        </button>
      </div>

      <form @submit.prevent="handleSave" class="flex flex-col">
        <div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2">
          <!-- Nama Karyawan -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Nama Karyawan <span class="text-red-500">*</span>
            </label>
            <SearchableSelect
              v-model="formData.namaKaryawan"
              :options="karyawanList"
              placeholder="Pilih atau cari nama karyawan"
              :search-input="karyawanSearchInput"
              @update:search-input="karyawanSearchInput = $event"
              @update:model-value="handleKaryawanChange"
            />
          </div>

          <!-- Bulan -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Bulan <span class="text-red-500">*</span>
            </label>
            <input
              type="number"
              v-model.number="formData.bulan"
              placeholder="Masukkan bulan (1-12)"
              min="1"
              max="12"
              required
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
          </div>

          <!-- Tahun -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Tahun <span class="text-red-500">*</span>
            </label>
            <input
              type="number"
              v-model.number="formData.tahun"
              placeholder="Masukkan tahun"
              min="2000"
              :max="new Date().getFullYear() + 1"
              required
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
          </div>

          <!-- Kantor Cabang -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Kantor Cabang
            </label>
            <SearchableSelect
              v-model="formData.kantorCabang"
              :options="kantorCabangList"
              placeholder="Pilih atau cari kantor cabang"
              :search-input="kantorCabangSearchInput"
              @update:search-input="kantorCabangSearchInput = $event"
            />
          </div>
        </div>

        <!-- Bagian Kalkulasi Gaji -->
        <div class="mt-8 rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
          <h4 class="mb-6 text-lg font-semibold text-gray-800 dark:text-white/90">
            Bagian Kalkulasi Gaji
          </h4>
          
          <div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2">
            <!-- Gaji Pokok - Nominal (Display Only) -->
            <div class="lg:col-span-1">
              <label
                class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
              >
                Gaji Pokok - Nominal
              </label>
              <div
                class="dark:bg-dark-900 h-11 w-full flex items-center rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
              >
                <span class="font-medium">{{ formatCurrency(gajiPokok) }}</span>
              </div>
              <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                Display only (readonly)
              </p>
            </div>
          </div>
        </div>

        <!-- Bagian Bawah - Take Home Pay -->
        <div class="mt-8 rounded-lg border border-gray-200 bg-gray-50 p-6 dark:border-gray-800 dark:bg-gray-900/50">
          <div class="flex items-center justify-between">
            <label
              class="text-lg font-semibold text-gray-800 dark:text-white/90"
            >
              Take Home Pay
            </label>
            <span class="text-xl font-bold text-brand-500 dark:text-brand-400">
              {{ formatCurrency(takeHomePay) }}
            </span>
          </div>
          <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
            Display only
          </p>
        </div>

        <div class="flex items-center gap-3 mt-6 lg:justify-end">
          <button
            @click="handleCancel"
            type="button"
            class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] sm:w-auto"
          >
            Batal
          </button>
          <button
            type="submit"
            class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto"
          >
            {{ isEditMode ? 'Simpan Perubahan' : 'Simpan' }}
          </button>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { reactive, computed, ref, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import SearchableSelect from '@/components/forms/SearchableSelect.vue'

const route = useRoute()
const router = useRouter()

const isEditMode = computed(() => route.params.id !== undefined && route.params.id !== 'new')
const currentPageTitle = computed(() => {
  return isEditMode.value ? 'Edit Remunerasi' : 'Tambah Remunerasi'
})

// Options for select fields
const karyawanList = [
  { value: 'ahmad_hidayat', label: 'Ahmad Hidayat' },
  { value: 'siti_nurhaliza', label: 'Siti Nurhaliza' },
  { value: 'budi_santoso', label: 'Budi Santoso' },
  { value: 'dewi_lestari', label: 'Dewi Lestari' },
  { value: 'eko_prasetyo', label: 'Eko Prasetyo' },
  { value: 'fitri_handayani', label: 'Fitri Handayani' },
  { value: 'guntur_wibowo', label: 'Guntur Wibowo' },
  { value: 'hesti_rahayu', label: 'Hesti Rahayu' },
]

const kantorCabangList = [
  { value: 'jakarta', label: 'Jakarta' },
  { value: 'bandung', label: 'Bandung' },
  { value: 'surabaya', label: 'Surabaya' },
  { value: 'yogyakarta', label: 'Yogyakarta' },
  { value: 'medan', label: 'Medan' },
  { value: 'makassar', label: 'Makassar' },
  { value: 'semarang', label: 'Semarang' },
  { value: 'palembang', label: 'Palembang' },
  { value: 'denpasar', label: 'Denpasar' },
  { value: 'batam', label: 'Batam' },
]

// Search input refs
const karyawanSearchInput = ref('')
const kantorCabangSearchInput = ref('')

// Form data
const formData = reactive({
  namaKaryawan: '',
  bulan: null as number | null,
  tahun: null as number | null,
  kantorCabang: '',
})

// Gaji data (will be loaded from API based on selected karyawan)
const gajiPokok = ref(0)

// Handle karyawan change - load gaji data
const handleKaryawanChange = () => {
  // This will be called when v-model updates
  loadGajiPokok()
}

// Load gaji pokok based on selected karyawan
const loadGajiPokok = () => {
  // TODO: Load gaji pokok from API based on selected karyawan
  // For now, using sample data
  if (formData.namaKaryawan) {
    // Simulate loading gaji pokok based on karyawan
    const sampleGaji: Record<string, number> = {
      'ahmad_hidayat': 12000000,
      'siti_nurhaliza': 10000000,
      'budi_santoso': 8000000,
      'dewi_lestari': 7000000,
      'eko_prasetyo': 9000000,
      'fitri_handayani': 8500000,
      'guntur_wibowo': 9500000,
      'hesti_rahayu': 8800000,
    }
    gajiPokok.value = sampleGaji[formData.namaKaryawan] || 0
  } else {
    gajiPokok.value = 0
  }
}

// Calculate Take Home Pay (for now, same as gaji pokok)
// In real implementation, this would calculate based on deductions, allowances, etc.
const takeHomePay = computed(() => {
  // TODO: Implement proper calculation with deductions, allowances, etc.
  return gajiPokok.value
})

// Format currency
const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(amount)
}

// Load data if edit mode
const loadData = async () => {
  if (isEditMode.value && route.params.id) {
    const id = route.params.id as string
    // TODO: Load data from API
    console.log('Loading data for ID:', id)
  }
}

// Handle cancel
const handleCancel = () => {
  router.push('/operasional/remunerasi')
}

// Handle save
const handleSave = async () => {
  if (!formData.namaKaryawan) {
    alert('Nama Karyawan wajib diisi')
    return
  }

  if (!formData.bulan || formData.bulan < 1 || formData.bulan > 12) {
    alert('Bulan harus antara 1-12')
    return
  }

  if (!formData.tahun) {
    alert('Tahun wajib diisi')
    return
  }

  try {
    // TODO: Save to API
    if (isEditMode.value) {
      console.log('Updating remunerasi:', formData)
      // await updateRemunerasi(route.params.id, formData)
      alert('Remunerasi berhasil diupdate')
    } else {
      console.log('Creating remunerasi:', formData)
      // await createRemunerasi(formData)
      alert('Remunerasi berhasil ditambahkan')
    }
    
    // Redirect to list
    router.push('/operasional/remunerasi')
  } catch (error) {
    console.error('Error saving:', error)
    alert('Terjadi kesalahan saat menyimpan data')
  }
}

// Watch for namaKaryawan changes to load gaji pokok
watch(() => formData.namaKaryawan, () => {
  loadGajiPokok()
})

onMounted(() => {
  loadData()
})
</script>

