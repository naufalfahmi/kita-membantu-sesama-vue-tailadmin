<template>
  <AdminLayout>
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
          <!-- Jangka Tanggal Report (Date Range) -->
          <div class="lg:col-span-2">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Jangka Tanggal Report <span class="text-red-500">*</span>
            </label>
            <div class="relative">
              <flat-pickr
                v-model="formData.report_date_range"
                :config="flatpickrRangeConfig"
                class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pl-4 pr-11 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                placeholder="Pilih jangka tanggal report"
                :class="{ 'border-red-500': errors.report_date_range }"
              />
              <span
                class="absolute text-gray-500 -translate-y-1/2 pointer-events-none right-3 top-1/2 dark:text-gray-400"
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
                    fill-rule="evenodd"
                    clip-rule="evenodd"
                    d="M6.66659 1.5415C7.0808 1.5415 7.41658 1.87729 7.41658 2.2915V2.99984H12.5833V2.2915C12.5833 1.87729 12.919 1.5415 13.3333 1.5415C13.7475 1.5415 14.0833 1.87729 14.0833 2.2915V2.99984L15.4166 2.99984C16.5212 2.99984 17.4166 3.89527 17.4166 4.99984V7.49984V15.8332C17.4166 16.9377 16.5212 17.8332 15.4166 17.8332H4.58325C3.47868 17.8332 2.58325 16.9377 2.58325 15.8332V7.49984V4.99984C2.58325 3.89527 3.47868 2.99984 4.58325 2.99984L5.91659 2.99984V2.2915C5.91659 1.87729 6.25237 1.5415 6.66659 1.5415ZM6.66659 4.49984H4.58325C4.30711 4.49984 4.08325 4.7237 4.08325 4.99984V6.74984H15.9166V4.99984C15.9166 4.7237 15.6927 4.49984 15.4166 4.49984H13.3333H6.66659ZM15.9166 8.24984H4.08325V15.8332C4.08325 16.1093 4.30711 16.3332 4.58325 16.3332H15.4166C15.6927 16.3332 15.9166 16.1093 15.9166 15.8332V8.24984Z"
                    fill=""
                  />
                </svg>
              </span>
            </div>
            <p v-if="errors.report_date_range" class="mt-1 text-xs text-red-500">
              {{ errors.report_date_range }}
            </p>
          </div>

          <!-- Nominal (Readonly / Display Only) -->
          <div class="lg:col-span-2">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Nominal
            </label>
            <div
              class="dark:bg-dark-900 h-11 w-full flex items-center rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
            >
              <span class="font-semibold text-lg">{{ formatCurrency(calculatedNominal) }}</span>
            </div>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
              Otomatis dihitung hasil total pecahan
            </p>
          </div>

          <!-- Jumlah Pecahan Seratus Ribu -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Jumlah Pecahan Seratus Ribu
            </label>
            <input
              type="number"
              v-model.number="formData.count_100k"
              placeholder="0"
              min="0"
              step="1"
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
              Pecahan Rp 100.000
            </p>
          </div>

          <!-- Jumlah Pecahan Lima Puluh Ribu -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Jumlah Pecahan Lima Puluh Ribu
            </label>
            <input
              type="number"
              v-model.number="formData.count_50k"
              placeholder="0"
              min="0"
              step="1"
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
              Pecahan Rp 50.000
            </p>
          </div>

          <!-- Jumlah Pecahan Dua Puluh Ribu -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Jumlah Pecahan Dua Puluh Ribu
            </label>
            <input
              type="number"
              v-model.number="formData.count_20k"
              placeholder="0"
              min="0"
              step="1"
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
              Pecahan Rp 20.000
            </p>
          </div>

          <!-- Jumlah Pecahan Sepuluh Ribu -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Jumlah Pecahan Sepuluh Ribu
            </label>
            <input
              type="number"
              v-model.number="formData.count_10k"
              placeholder="0"
              min="0"
              step="1"
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
              Pecahan Rp 10.000
            </p>
          </div>

          <!-- Jumlah Pecahan Lima Ribu -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Jumlah Pecahan Lima Ribu
            </label>
            <input
              type="number"
              v-model.number="formData.count_5k"
              placeholder="0"
              min="0"
              step="1"
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
              Pecahan Rp 5.000
            </p>
          </div>

          <!-- Jumlah Pecahan Seribu -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Jumlah Pecahan Seribu
            </label>
            <input
              type="number"
              v-model.number="formData.count_1k"
              placeholder="0"
              min="0"
              step="1"
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
              Pecahan Rp 1.000
            </p>
          </div>

          <!-- Jumlah Pecahan Lima Ratus -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Jumlah Pecahan Lima Ratus
            </label>
            <input
              type="number"
              v-model.number="formData.count_500"
              placeholder="0"
              min="0"
              step="1"
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
              Pecahan Rp 500
            </p>
          </div>

          <!-- Jumlah Pecahan Seratus -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Jumlah Pecahan Seratus
            </label>
            <input
              type="number"
              v-model.number="formData.count_100"
              placeholder="0"
              min="0"
              step="1"
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
              Pecahan Rp 100
            </p>
          </div>

          <!-- Kantor Cabang -->
          <div class="lg:col-span-2">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Kantor Cabang <span class="text-red-500">*</span>
            </label>
            <SearchableSelect
              v-model="formData.branch_id"
              :options="kantorCabangList"
              placeholder="Pilih atau cari kantor cabang"
              :search-input="kantorCabangSearchInput"
              @update:search-input="kantorCabangSearchInput = $event"
            />
            <p v-if="errors.branch_id" class="mt-1 text-xs text-red-500">
              {{ errors.branch_id }}
            </p>
          </div>
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
import { reactive, computed, ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import flatPickr from 'vue-flatpickr-component'
import 'flatpickr/dist/flatpickr.css'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import SearchableSelect from '@/components/forms/SearchableSelect.vue'

const route = useRoute()
const router = useRouter()

const isEditMode = computed(() => route.params.id !== undefined && route.params.id !== 'new')
const currentPageTitle = computed(() => {
  return isEditMode.value ? 'Edit Laporan Transaksi' : 'Tambah Laporan Transaksi'
})

// Flatpickr configuration for date range
const flatpickrRangeConfig = ({
  mode: 'range',
  dateFormat: 'Y-m-d',
  altInput: true,
  altFormat: 'd F Y',
  locale: 'id' as any,
  wrap: true,
  clickOpens: true,
  allowInput: false,
} as any)

// Kantor Cabang options
const kantorCabangList = [
  { value: '1', label: 'Kantor Pusat Jakarta' },
  { value: '2', label: 'Kantor Cabang Bandung' },
  { value: '3', label: 'Kantor Cabang Surabaya' },
  { value: '4', label: 'Kantor Cabang Yogyakarta' },
  { value: '5', label: 'Kantor Cabang Medan' },
  { value: '6', label: 'Kantor Cabang Makassar' },
  { value: '7', label: 'Kantor Cabang Semarang' },
  { value: '8', label: 'Kantor Cabang Palembang' },
  { value: '9', label: 'Kantor Cabang Denpasar' },
  { value: '10', label: 'Kantor Cabang Batam' },
]

// Search input refs
const kantorCabangSearchInput = ref('')

// Form data
const formData = reactive({
  report_date_range: '' as string | string[],
  count_100k: 0,
  count_50k: 0,
  count_20k: 0,
  count_10k: 0,
  count_5k: 0,
  count_1k: 0,
  count_500: 0,
  count_100: 0,
  branch_id: '',
})

// Errors
const errors = reactive({
  report_date_range: '',
  branch_id: '',
})

// Calculate nominal automatically
const calculatedNominal = computed(() => {
  const total =
    (formData.count_100k || 0) * 100000 +
    (formData.count_50k || 0) * 50000 +
    (formData.count_20k || 0) * 20000 +
    (formData.count_10k || 0) * 10000 +
    (formData.count_5k || 0) * 5000 +
    (formData.count_1k || 0) * 1000 +
    (formData.count_500 || 0) * 500 +
    (formData.count_100 || 0) * 100

  return total
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

// Validate form
const validateForm = () => {
  let isValid = true

  // Reset errors
  errors.report_date_range = ''
  errors.branch_id = ''

  // Validate date range
  if (!formData.report_date_range || (Array.isArray(formData.report_date_range) && formData.report_date_range.length === 0)) {
    errors.report_date_range = 'Jangka Tanggal Report wajib diisi'
    isValid = false
  } else if (Array.isArray(formData.report_date_range) && formData.report_date_range.length !== 2) {
    errors.report_date_range = 'Pilih tanggal mulai dan tanggal akhir'
    isValid = false
  }

  // Validate branch
  if (!formData.branch_id) {
    errors.branch_id = 'Kantor Cabang wajib diisi'
    isValid = false
  }

  return isValid
}

// Load data if edit mode
const loadData = async () => {
  if (isEditMode.value && route.params.id) {
    const id = route.params.id as string
    // TODO: Load data from API
    console.log('Loading data for ID:', id)
    
    // Sample data for edit mode
    // formData.report_date_range = ['2024-01-01', '2024-01-31']
    // formData.count_100k = 10
    // formData.count_50k = 20
    // formData.count_20k = 30
    // formData.count_10k = 40
    // formData.count_5k = 50
    // formData.count_1k = 60
    // formData.count_500 = 70
    // formData.count_100 = 80
    // formData.branch_id = '1'
  }
}

// Handle cancel
const handleCancel = () => {
  router.push('/laporan/laporan-transaksi')
}

import { useToast } from 'vue-toastification'

// Handle save
const toast = useToast()
const handleSave = async () => {
  if (!validateForm()) {
    return
  }

  try {
    // Prepare data for API
    const payload = {
      report_date_range: formData.report_date_range,
      total_nominal: calculatedNominal.value,
      count_100k: formData.count_100k || 0,
      count_50k: formData.count_50k || 0,
      count_20k: formData.count_20k || 0,
      count_10k: formData.count_10k || 0,
      count_5k: formData.count_5k || 0,
      count_1k: formData.count_1k || 0,
      count_500: formData.count_500 || 0,
      count_100: formData.count_100 || 0,
      branch_id: formData.branch_id,
    }

    // TODO: Save to API
    if (isEditMode.value) {
      console.log('Updating laporan transaksi:', payload)
      // await updateLaporanTransaksi(route.params.id, payload)
      toast.success('Laporan transaksi berhasil diupdate')
    } else {
      console.log('Creating laporan transaksi:', payload)
      // await createLaporanTransaksi(payload)
      toast.success('Laporan transaksi berhasil ditambahkan')
    }
    
    // Redirect to list
    router.push('/laporan/laporan-transaksi')
  } catch (error) {
    console.error('Error saving:', error)
    toast.error('Terjadi kesalahan saat menyimpan data')
  }
}

onMounted(() => {
  loadData()
})
</script>

