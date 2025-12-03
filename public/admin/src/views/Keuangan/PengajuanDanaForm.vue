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
          <!-- Pengaju -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Pengaju <span class="text-red-500">*</span>
            </label>
            <SearchableSelect
              v-model="formData.applicant"
              :options="applicantList"
              placeholder="Pilih atau cari pengaju"
              :search-input="applicantSearchInput"
              @update:search-input="applicantSearchInput = $event"
            />
          </div>

          <!-- Tipe Pengajuan -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Tipe Pengajuan <span class="text-red-500">*</span>
            </label>
            <SearchableSelect
              v-model="formData.submissionType"
              :options="submissionTypeList"
              placeholder="Pilih atau cari tipe pengajuan"
              :search-input="submissionTypeSearchInput"
              @update:search-input="submissionTypeSearchInput = $event"
            />
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
                @input="formatAmountInput"
              />
              <span
                class="absolute text-gray-500 -translate-y-1/2 pointer-events-none right-4 top-1/2 dark:text-gray-400 text-sm"
              >
                {{ formattedAmount }}
              </span>
            </div>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
              Minimal nominal: Rp 1
            </p>
          </div>

          <!-- Tanggal Digunakan -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Tanggal Digunakan <span class="text-red-500">*</span>
            </label>
            <div class="relative">
              <flat-pickr
                v-model="formData.usedAt"
                :config="flatpickrConfig"
                class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pl-4 pr-11 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                placeholder="Pilih tanggal digunakan"
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
          </div>

          <!-- Tujuan Pengajuan -->
          <div class="lg:col-span-2">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Tujuan Pengajuan
            </label>
            <textarea
              v-model="formData.purpose"
              placeholder="Masukkan tujuan atau alasan pengajuan dana"
              maxlength="1000"
              rows="4"
              class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            ></textarea>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
              {{ formData.purpose.length }}/1000 karakter
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
              v-model="formData.branchId"
              :options="kantorCabangList"
              placeholder="Pilih atau cari kantor cabang"
              :search-input="kantorCabangSearchInput"
              @update:search-input="kantorCabangSearchInput = $event"
            />
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
  return isEditMode.value ? 'Edit Pengajuan Dana' : 'Tambah Pengajuan Dana'
})

// Flatpickr configuration for single date
const flatpickrConfig = {
  dateFormat: 'Y-m-d',
  altInput: true,
  altFormat: 'd F Y',
  locale: 'id',
  wrap: true,
  clickOpens: true,
  allowInput: false,
  minDate: 'today',
}

// Options for select fields
const applicantList = [
  { value: 'ahmad_hidayat', label: 'Ahmad Hidayat' },
  { value: 'siti_nurhaliza', label: 'Siti Nurhaliza' },
  { value: 'budi_santoso', label: 'Budi Santoso' },
  { value: 'dewi_lestari', label: 'Dewi Lestari' },
  { value: 'eko_prasetyo', label: 'Eko Prasetyo' },
  { value: 'fitri_handayani', label: 'Fitri Handayani' },
  { value: 'guntur_wibowo', label: 'Guntur Wibowo' },
  { value: 'hesti_rahayu', label: 'Hesti Rahayu' },
  { value: 'indra_wijaya', label: 'Indra Wijaya' },
  { value: 'joko_susilo', label: 'Joko Susilo' },
]

const submissionTypeList = [
  { value: 'operasional', label: 'Operasional' },
  { value: 'program', label: 'Program' },
  { value: 'kegiatan', label: 'Kegiatan' },
  { value: 'pengembangan', label: 'Pengembangan' },
  { value: 'darurat', label: 'Darurat' },
  { value: 'lainnya', label: 'Lainnya' },
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
const applicantSearchInput = ref('')
const submissionTypeSearchInput = ref('')
const kantorCabangSearchInput = ref('')

// Form data
const formData = reactive({
  applicant: '',
  submissionType: '',
  amount: null as number | null,
  usedAt: '',
  purpose: '',
  branchId: '',
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
}

// Load data if edit mode
const loadData = async () => {
  if (isEditMode.value && route.params.id) {
    const id = route.params.id as string
    try {
      // TODO: Load data from API
      // const response = await fetch(`/admin/api/pengajuan-dana/${id}`)
      // const data = await response.json()
      
      // Sample data for demo - replace with actual API call
      const sampleData = {
        applicant: 'ahmad_hidayat',
        submission_type: 'operasional',
        amount: 5000000,
        used_at: '2024-12-25',
        purpose: 'Pengajuan dana untuk kebutuhan operasional bulanan',
        branch_id: 'jakarta',
      }
      
      // Populate form with loaded data
      formData.applicant = sampleData.applicant
      formData.submissionType = sampleData.submission_type
      formData.amount = sampleData.amount
      formData.usedAt = sampleData.used_at
      formData.purpose = sampleData.purpose
      formData.branchId = sampleData.branch_id
      
      console.log('Data loaded for edit:', id, sampleData)
    } catch (error) {
      console.error('Error loading data:', error)
      alert('Gagal memuat data pengajuan dana')
    }
  }
}

// Handle cancel
const handleCancel = () => {
  router.push('/keuangan/pengajuan-dana')
}

// Handle save
const handleSave = async () => {
  // Validation
  if (!formData.applicant) {
    alert('Pengaju wajib diisi')
    return
  }

  if (!formData.submissionType) {
    alert('Tipe Pengajuan wajib diisi')
    return
  }

  if (!formData.amount || formData.amount <= 0) {
    alert('Nominal wajib diisi dan harus lebih dari 0')
    return
  }

  if (!formData.usedAt) {
    alert('Tanggal Digunakan wajib diisi')
    return
  }

  if (!formData.branchId) {
    alert('Kantor Cabang wajib diisi')
    return
  }

  try {
    // Prepare data for API
    const payload = {
      applicant: formData.applicant,
      submission_type: formData.submissionType,
      amount: formData.amount,
      used_at: formData.usedAt,
      purpose: formData.purpose,
      branch_id: formData.branchId,
    }

    // TODO: Save to API
    if (isEditMode.value) {
      console.log('Updating pengajuan dana:', payload)
      // await updatePengajuanDana(route.params.id, payload)
      alert('Pengajuan dana berhasil diupdate')
    } else {
      console.log('Creating pengajuan dana:', payload)
      // await createPengajuanDana(payload)
      alert('Pengajuan dana berhasil ditambahkan')
    }
    
    // Redirect to list
    router.push('/keuangan/pengajuan-dana')
  } catch (error) {
    console.error('Error saving:', error)
    alert('Terjadi kesalahan saat menyimpan data')
  }
}

onMounted(() => {
  loadData()
})
</script>

