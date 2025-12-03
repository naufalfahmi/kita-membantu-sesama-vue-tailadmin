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
          <!-- Donor Type -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Donor Type
            </label>
            <SearchableSelect
              v-model="formData.donorType"
              :options="donorTypeList"
              placeholder="Pilih atau cari donor type"
              :search-input="donorTypeSearchInput"
              @update:search-input="donorTypeSearchInput = $event"
            />
          </div>

          <!-- PIC -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              PIC
            </label>
            <SearchableSelect
              v-model="formData.pic"
              :options="picList"
              placeholder="Pilih atau cari PIC"
              :search-input="picSearchInput"
              @update:search-input="picSearchInput = $event"
            />
          </div>

          <!-- Nama -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Nama <span class="text-red-500">*</span>
            </label>
            <input
              type="text"
              v-model="formData.nama"
              placeholder="Masukkan nama"
              required
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
          </div>

          <!-- Alamat -->
          <div class="lg:col-span-2">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Alamat
            </label>
            <textarea
              v-model="formData.alamat"
              placeholder="Masukkan alamat"
              rows="3"
              class="dark:bg-dark-900 h-auto w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            ></textarea>
          </div>

          <!-- No. Handphone -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              No. Handphone
            </label>
            <input
              type="number"
              v-model.number="formData.noHandphone"
              placeholder="Masukkan nomor handphone"
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
          </div>

          <!-- Email -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Email
            </label>
            <input
              type="email"
              v-model="formData.email"
              placeholder="Masukkan email"
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
          </div>

          <!-- Tanggal Lahir -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Tanggal Lahir
            </label>
            <div class="relative">
              <flat-pickr
                v-model="formData.tanggalLahir"
                :config="flatpickrConfigTanggalLahir"
                class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pl-4 pr-11 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                placeholder="Pilih tanggal lahir"
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

          <!-- Status -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Status
            </label>
            <SearchableSelect
              v-model="formData.status"
              :options="statusList"
              placeholder="Pilih atau cari status"
              :search-input="statusSearchInput"
              @update:search-input="statusSearchInput = $event"
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
  return isEditMode.value ? 'Edit Donatur' : 'Tambah Donatur'
})

// Flatpickr configuration
const flatpickrConfigTanggalLahir = {
  dateFormat: 'Y-m-d',
  altInput: true,
  altFormat: 'd F Y',
  locale: 'id',
  wrap: true,
  clickOpens: true,
  allowInput: false,
}

// Options for select fields
const donorTypeList = [
  { value: 'donatur_tetap', label: 'Donatur Tetap' },
  { value: 'donatur_bulanan', label: 'Donatur Bulanan' },
  { value: 'donatur_rutin', label: 'Donatur Rutin' },
  { value: 'donatur_insidental', label: 'Donatur Insidental' },
  { value: 'donatur_korporasi', label: 'Donatur Korporasi' },
  { value: 'donatur_individu', label: 'Donatur Individu' },
  { value: 'donatur_organisasi', label: 'Donatur Organisasi' },
  { value: 'donatur_spesial', label: 'Donatur Spesial' },
]

const picList = [
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

const statusList = [
  { value: 'aktif', label: 'Aktif' },
  { value: 'tidak_aktif', label: 'Tidak Aktif' },
  { value: 'pending', label: 'Pending' },
]

// Search input refs
const donorTypeSearchInput = ref('')
const picSearchInput = ref('')
const kantorCabangSearchInput = ref('')
const statusSearchInput = ref('')

// Form data
const formData = reactive({
  donorType: '',
  pic: '',
  nama: '',
  alamat: '',
  noHandphone: null as number | null,
  email: '',
  tanggalLahir: '',
  kantorCabang: '',
  status: '',
})

// Search inputs will be cleared automatically when option is selected

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
  router.push('/user-kepegawaian/donatur')
}

// Handle save
const handleSave = async () => {
  if (!formData.nama) {
    alert('Nama wajib diisi')
    return
  }

  try {
    // TODO: Save to API
    if (isEditMode.value) {
      console.log('Updating donatur:', formData)
      // await updateDonatur(route.params.id, formData)
      alert('Donatur berhasil diupdate')
    } else {
      console.log('Creating donatur:', formData)
      // await createDonatur(formData)
      alert('Donatur berhasil ditambahkan')
    }
    
    // Redirect to list
    router.push('/user-kepegawaian/donatur')
  } catch (error) {
    console.error('Error saving:', error)
    alert('Terjadi kesalahan saat menyimpan data')
  }
}

onMounted(() => {
  loadData()
})
</script>

