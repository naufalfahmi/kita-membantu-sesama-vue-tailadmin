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
          <!-- POS Dana -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              POS Dana <span class="text-red-500">*</span>
            </label>
            <SearchableSelect
              v-model="formData.fund_pos_id"
              :options="fundPosList"
              placeholder="Pilih atau cari pos anggaran / kategori keuangan"
              :search-input="fundPosSearchInput"
              @update:search-input="fundPosSearchInput = $event"
            />
            <p v-if="errors.fund_pos_id" class="mt-1 text-xs text-red-500">
              {{ errors.fund_pos_id }}
            </p>
          </div>

          <!-- Pengaju -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Pengaju <span class="text-red-500">*</span>
            </label>
            <SearchableSelect
              v-model="formData.applicant_id"
              :options="applicantList"
              placeholder="Pilih atau cari user yang mengajukan"
              :search-input="applicantSearchInput"
              @update:search-input="applicantSearchInput = $event"
            />
            <p v-if="errors.applicant_id" class="mt-1 text-xs text-red-500">
              {{ errors.applicant_id }}
            </p>
          </div>

          <!-- Nominal -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Nominal <span class="text-red-500">*</span>
            </label>
            <div class="relative">
              <input
                type="number"
                v-model.number="formData.amount"
                placeholder="Masukkan nominal"
                min="1"
                step="1"
                required
                class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-24 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                :class="{ 'border-red-500': errors.amount }"
                @input="formatAmountInput"
              />
              <span
                class="absolute text-gray-500 -translate-y-1/2 pointer-events-none right-4 top-1/2 dark:text-gray-400 text-sm"
              >
                {{ formattedAmount }}
              </span>
            </div>
            <p v-if="errors.amount" class="mt-1 text-xs text-red-500">
              {{ errors.amount }}
            </p>
            <p v-else class="mt-1 text-xs text-gray-500 dark:text-gray-400">
              Total dana yang direalisasikan (minimal: Rp 1)
            </p>
          </div>

          <!-- Tanggal Keuangan -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Tanggal Keuangan <span class="text-red-500">*</span>
            </label>
            <div class="relative">
              <flat-pickr
                v-model="formData.financial_date"
                :config="flatpickrConfig"
                class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pl-4 pr-11 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                :class="{ 'border-red-500': errors.financial_date }"
                placeholder="Pilih tanggal transaksi keuangan"
                required
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
            <p v-if="errors.financial_date" class="mt-1 text-xs text-red-500">
              {{ errors.financial_date }}
            </p>
          </div>

          <!-- Kantor Cabang -->
          <div class="lg:col-span-1">
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

          <!-- Detail Permohonan -->
          <div class="lg:col-span-2">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Detail Permohonan
            </label>
            <textarea
              v-model="formData.purpose"
              placeholder="Masukkan keterangan penggunaan dana yang dimohon"
              maxlength="1000"
              rows="4"
              class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            ></textarea>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
              {{ formData.purpose.length }}/1000 karakter
            </p>
          </div>

          <!-- Laporan Penggunaan Dana -->
          <div class="lg:col-span-2">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Laporan Penggunaan Dana
            </label>
            <textarea
              v-model="formData.report"
              placeholder="Masukkan realisasi laporan penggunaan dana"
              maxlength="1000"
              rows="4"
              class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            ></textarea>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
              {{ formData.report.length }}/1000 karakter
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
  return isEditMode.value ? 'Edit Keuangan' : 'Tambah Keuangan'
})

// Flatpickr configuration for single date
const flatpickrConfig = {
  dateFormat: 'Y-m-d',
  altInput: true,
  altFormat: 'd F Y',
  locale: 'id' as any,
  wrap: true,
  clickOpens: true,
  allowInput: false,
}

// Options for select fields
const fundPosList = [
  { value: '1', label: 'Operasional - Gaji Karyawan' },
  { value: '2', label: 'Operasional - Biaya Kantor' },
  { value: '3', label: 'Operasional - Transportasi' },
  { value: '4', label: 'Program - Pendidikan' },
  { value: '5', label: 'Program - Kesehatan' },
  { value: '6', label: 'Program - Sosial' },
  { value: '7', label: 'Program - Infrastruktur' },
  { value: '8', label: 'Pengembangan - Investasi' },
  { value: '9', label: 'Pengembangan - Riset' },
  { value: '10', label: 'Darurat - Bencana' },
  { value: '11', label: 'Darurat - Kesehatan' },
  { value: '12', label: 'Lainnya - Donasi' },
]

const applicantList = [
  { value: '1', label: 'Ahmad Hidayat' },
  { value: '2', label: 'Siti Nurhaliza' },
  { value: '3', label: 'Budi Santoso' },
  { value: '4', label: 'Dewi Lestari' },
  { value: '5', label: 'Eko Prasetyo' },
  { value: '6', label: 'Fitri Handayani' },
  { value: '7', label: 'Guntur Wibowo' },
  { value: '8', label: 'Hesti Rahayu' },
  { value: '9', label: 'Indra Wijaya' },
  { value: '10', label: 'Joko Susilo' },
]

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
const fundPosSearchInput = ref('')
const applicantSearchInput = ref('')
const kantorCabangSearchInput = ref('')

// Form data
const formData = reactive({
  fund_pos_id: '',
  applicant_id: '',
  purpose: '',
  report: '',
  amount: null as number | null,
  financial_date: '',
  branch_id: '',
})

// Error messages
const errors = reactive({
  fund_pos_id: '',
  applicant_id: '',
  amount: '',
  financial_date: '',
  branch_id: '',
})

// Format currency for display
const formattedAmount = computed(() => {
  if (!formData.amount || formData.amount <= 0) {
    return ''
  }
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(formData.amount)
})

// Format amount input - ensure it's a valid number
const formatAmountInput = (event: Event) => {
  const target = event.target as HTMLInputElement
  const value = parseFloat(target.value)
  if (isNaN(value) || value < 0) {
    formData.amount = null
  } else {
    formData.amount = Math.floor(value)
  }
  // Clear error when user types
  if (errors.amount) {
    errors.amount = ''
  }
}

// Validation function
const validateForm = (): boolean => {
  let isValid = true

  // Reset errors
  errors.fund_pos_id = ''
  errors.applicant_id = ''
  errors.amount = ''
  errors.financial_date = ''
  errors.branch_id = ''

  // Validate POS Dana
  if (!formData.fund_pos_id) {
    errors.fund_pos_id = 'POS Dana wajib diisi'
    isValid = false
  }

  // Validate Pengaju
  if (!formData.applicant_id) {
    errors.applicant_id = 'Pengaju wajib diisi'
    isValid = false
  }

  // Validate Nominal
  if (!formData.amount || formData.amount <= 0) {
    errors.amount = 'Nominal wajib diisi dan harus lebih dari 0'
    isValid = false
  } else if (isNaN(formData.amount)) {
    errors.amount = 'Nominal harus berupa angka'
    isValid = false
  }

  // Validate Tanggal Keuangan
  if (!formData.financial_date) {
    errors.financial_date = 'Tanggal Keuangan wajib diisi'
    isValid = false
  } else {
    // Validate date format
    const date = new Date(formData.financial_date)
    if (isNaN(date.getTime())) {
      errors.financial_date = 'Format tanggal tidak valid'
      isValid = false
    }
  }

  // Validate Kantor Cabang
  if (!formData.branch_id) {
    errors.branch_id = 'Kantor Cabang wajib diisi'
    isValid = false
  }

  // Validate textarea max length (optional fields)
  if (formData.purpose.length > 1000) {
    alert('Detail Permohonan tidak boleh lebih dari 1000 karakter')
    isValid = false
  }

  if (formData.report.length > 1000) {
    alert('Laporan Penggunaan Dana tidak boleh lebih dari 1000 karakter')
    isValid = false
  }

  return isValid
}

// Load data if edit mode
const loadData = async () => {
  if (isEditMode.value && route.params.id) {
    const id = route.params.id as string
    try {
      // TODO: Load data from API
      // const response = await fetch(`/admin/api/keuangan/${id}`)
      // const data = await response.json()
      
      // Sample data for demo - replace with actual API call
      const sampleData = {
        fund_pos_id: '1',
        applicant_id: '1',
        purpose: 'Penggunaan dana untuk kebutuhan operasional bulanan',
        report: 'Dana telah digunakan sesuai dengan rencana yang telah ditetapkan',
        amount: 5000000,
        financial_date: '2024-12-25',
        branch_id: '1',
      }
      
      // Populate form with loaded data
      formData.fund_pos_id = sampleData.fund_pos_id
      formData.applicant_id = sampleData.applicant_id
      formData.purpose = sampleData.purpose
      formData.report = sampleData.report
      formData.amount = sampleData.amount
      formData.financial_date = sampleData.financial_date
      formData.branch_id = sampleData.branch_id
      
      console.log('Data loaded for edit:', id, sampleData)
    } catch (error) {
      console.error('Error loading data:', error)
      alert('Gagal memuat data keuangan')
    }
  }
}

// Handle cancel
const handleCancel = () => {
  router.push('/keuangan/keuangan')
}

// Handle save
const handleSave = async () => {
  // Validate form
  if (!validateForm()) {
    return
  }

  try {
    // Prepare data for API
    const payload = {
      fund_pos_id: formData.fund_pos_id,
      applicant_id: formData.applicant_id,
      purpose: formData.purpose,
      report: formData.report,
      amount: formData.amount,
      financial_date: formData.financial_date,
      branch_id: formData.branch_id,
    }

    // TODO: Save to API
    if (isEditMode.value) {
      console.log('Updating keuangan:', payload)
      // await updateKeuangan(route.params.id, payload)
      alert('Data keuangan berhasil diupdate')
    } else {
      console.log('Creating keuangan:', payload)
      // await createKeuangan(payload)
      alert('Data keuangan berhasil ditambahkan')
    }
    
    // Redirect to list
    router.push('/keuangan/keuangan')
  } catch (error) {
    console.error('Error saving:', error)
    alert('Terjadi kesalahan saat menyimpan data')
  }
}

onMounted(() => {
  loadData()
})
</script>




