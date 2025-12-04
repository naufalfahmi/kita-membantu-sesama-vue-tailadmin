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
          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Kantor Cabang <span class="text-red-500">*</span>
            </label>
            <SearchableSelect
              v-model="formData.branchId"
              :options="kantorCabangList"
              placeholder="Kantor Cabang"
              :search-input="kantorCabangSearchInput"
              @update:search-input="kantorCabangSearchInput = $event"
            />
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Nominal <span class="text-red-500">*</span>
            </label>
            <input
              type="number"
              v-model.number="formData.nominal"
              placeholder="Nominal"
              min="0"
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Donatur <span class="text-red-500">*</span>
            </label>
            <SearchableSelect
              v-model="formData.donorId"
              :options="donaturList"
              placeholder="Donatur"
              :search-input="donaturSearchInput"
              @update:search-input="donaturSearchInput = $event"
            />
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Program <span class="text-red-500">*</span>
            </label>
            <SearchableSelect
              v-model="formData.programId"
              :options="programList"
              placeholder="Program"
              :search-input="programSearchInput"
              @update:search-input="programSearchInput = $event"
            />
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Tanggal Transaksi <span class="text-red-500">*</span>
            </label>
            <div class="relative">
              <flat-pickr
                v-model="formData.transactionDate"
                :config="flatpickrDateConfig"
                class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-11 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                placeholder="Tanggal Transaksi"
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
                    d="M10 18.3333C14.6024 18.3333 18.3333 14.6024 18.3333 9.99999C18.3333 5.39762 14.6024 1.66666 10 1.66666C5.39763 1.66666 1.66667 5.39762 1.66667 9.99999C1.66667 14.6024 5.39763 18.3333 10 18.3333Z"
                    stroke="currentColor"
                    stroke-width="1.5"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  />
                  <path
                    d="M10 5V10L13.3333 11.6667"
                    stroke="currentColor"
                    stroke-width="1.5"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  />
                </svg>
              </span>
            </div>
          </div>

          <div class="lg:col-span-2">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Keterangan
            </label>
            <textarea
              v-model="formData.notes"
              placeholder="Keterangan"
              maxlength="1000"
              rows="4"
              class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            ></textarea>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
              Maksimal 1000 karakter.
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
  return isEditMode.value ? 'Edit Transaksi' : 'Tambah Transaksi'
})

// Flatpickr configuration for single date selection
const flatpickrDateConfig = {
  dateFormat: 'Y-m-d',
  altInput: true,
  altFormat: 'd/m/Y',
  allowInput: false,
}

// Kantor Cabang options
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

const donaturList = [
  { value: 'donatur-1', label: 'PT Dermawan Sejati' },
  { value: 'donatur-2', label: 'Yayasan Berbagi Kasih' },
  { value: 'donatur-3', label: 'CV Amal Mulia' },
  { value: 'donatur-4', label: 'PT Cinta Indonesia' },
]

const programList = [
  { value: 'program-1', label: 'Program Beasiswa Pendidikan' },
  { value: 'program-2', label: 'Program Kesehatan Masyarakat' },
  { value: 'program-3', label: 'Program Pemberdayaan Ekonomi' },
  { value: 'program-4', label: 'Program Bantuan Pangan' },
]

// Search input refs
const kantorCabangSearchInput = ref('')
const donaturSearchInput = ref('')
const programSearchInput = ref('')

// Form data
const formData = reactive({
  branchId: '',
  nominal: null as number | null,
  donorId: '',
  programId: '',
  transactionDate: '',
  notes: '',
})

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
  router.push('/keuangan/transaksi')
}

// Handle save
const handleSave = async () => {
  if (!formData.branchId) {
    alert('Kantor Cabang wajib diisi')
    return
  }

  if (!formData.nominal || formData.nominal <= 0) {
    alert('Nominal wajib diisi')
    return
  }

  if (!formData.donorId) {
    alert('Donatur wajib diisi')
    return
  }

  if (!formData.programId) {
    alert('Program wajib diisi')
    return
  }

  if (!formData.transactionDate) {
    alert('Tanggal transaksi wajib diisi')
    return
  }

  try {
    // Prepare data for API
    const payload = {
      branch_id: formData.branchId,
      nominal: formData.nominal,
      donor_id: formData.donorId,
      program_id: formData.programId,
      transaction_date: formData.transactionDate,
      notes: formData.notes,
    }

    // TODO: Save to API
    if (isEditMode.value) {
      console.log('Updating transaksi:', payload)
      // await updateTransaksi(route.params.id, payload)
      alert('Transaksi berhasil diupdate')
    } else {
      console.log('Creating transaksi:', payload)
      // await createTransaksi(payload)
      alert('Transaksi berhasil ditambahkan')
    }
    
    // Redirect to list
    router.push('/keuangan/transaksi')
  } catch (error) {
    console.error('Error saving:', error)
    alert('Terjadi kesalahan saat menyimpan data')
  }
}

onMounted(() => {
  loadData()
})
</script>




