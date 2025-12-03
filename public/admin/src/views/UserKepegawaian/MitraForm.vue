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

          <!-- Nama Bank -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Nama Bank
            </label>
            <input
              type="text"
              v-model="formData.namaBank"
              placeholder="Masukkan nama bank"
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
          </div>

          <!-- No. Rekening -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              No. Rekening
            </label>
            <input
              type="number"
              v-model.number="formData.noRekening"
              placeholder="Masukkan nomor rekening"
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

          <!-- Pendidikan -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Pendidikan
            </label>
            <input
              type="text"
              v-model="formData.pendidikan"
              placeholder="Masukkan pendidikan"
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
          </div>

          <!-- Kantor Cabang (Searchable Select) -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Kantor Cabang
            </label>
            <div class="relative z-20 bg-transparent" ref="kantorCabangRef">
              <div
                @click="showKantorCabangDropdown = !showKantorCabangDropdown"
                class="dark:bg-dark-900 h-11 w-full flex items-center rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 shadow-theme-xs cursor-pointer hover:border-brand-300 focus-within:border-brand-300 focus-within:outline-hidden focus-within:ring-3 focus-within:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus-within:border-brand-800"
              >
                <span :class="{ 'text-gray-800 dark:text-white/90': kantorCabangSearch, 'text-gray-400': !kantorCabangSearch }">
                  {{ kantorCabangSearch || 'Pilih atau cari kantor cabang' }}
                </span>
              </div>
              <span
                class="absolute z-30 text-gray-500 -translate-y-1/2 pointer-events-none right-4 top-1/2 dark:text-gray-400"
              >
                <svg
                  class="stroke-current"
                  width="20"
                  height="20"
                  viewBox="0 0 20 20"
                  fill="none"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <path
                    d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396"
                    stroke=""
                    stroke-width="1.5"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  />
                </svg>
              </span>
              
              <!-- Dropdown untuk Kantor Cabang -->
              <transition
                enter-active-class="transition duration-100 ease-out"
                enter-from-class="transform scale-95 opacity-0"
                enter-to-class="transform scale-100 opacity-100"
                leave-active-class="transition duration-75 ease-in"
                leave-from-class="transform scale-100 opacity-100"
                leave-to-class="transform scale-95 opacity-0"
              >
                <div
                  v-if="showKantorCabangDropdown"
                  class="absolute z-50 w-full mt-1 bg-white rounded-lg shadow-lg border border-gray-200 dark:bg-gray-900 dark:border-gray-700 max-h-60 overflow-hidden"
                >
                  <!-- Search input di dalam dropdown -->
                  <div class="p-2 border-b border-gray-200 dark:border-gray-700">
                    <input
                      type="text"
                      v-model="kantorCabangSearchInput"
                      @input="filterKantorCabang"
                      placeholder="Cari kantor cabang..."
                      class="w-full h-9 rounded-lg border border-gray-300 bg-transparent px-3 py-1.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90"
                      @click.stop
                    />
                  </div>
                  
                  <!-- List options -->
                  <div class="overflow-y-auto max-h-48 custom-scrollbar">
                    <button
                      v-for="kantor in filteredKantorCabangList"
                      :key="kantor.value"
                      type="button"
                      @click="selectKantorCabang(kantor)"
                      class="w-full px-4 py-2 text-left text-sm text-gray-800 hover:bg-gray-100 dark:text-white/90 dark:hover:bg-gray-800 transition-colors"
                      :class="{ 'bg-gray-100 dark:bg-gray-800': formData.kantorCabang === kantor.value }"
                    >
                      {{ kantor.label }}
                    </button>
                    <div
                      v-if="filteredKantorCabangList.length === 0"
                      class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400"
                    >
                      Tidak ada data ditemukan
                    </div>
                  </div>
                </div>
              </transition>
            </div>
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
import { reactive, computed, ref, onMounted, onBeforeUnmount } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import flatPickr from 'vue-flatpickr-component'
import 'flatpickr/dist/flatpickr.css'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'

const route = useRoute()
const router = useRouter()

const isEditMode = computed(() => route.params.id !== undefined && route.params.id !== 'new')
const currentPageTitle = computed(() => {
  return isEditMode.value ? 'Edit Mitra' : 'Tambah Mitra'
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

// Searchable select state
const showKantorCabangDropdown = ref(false)
const kantorCabangSearch = ref('')
const kantorCabangSearchInput = ref('')
const kantorCabangRef = ref<HTMLElement | null>(null)

// Filtered kantor cabang list
const filteredKantorCabangList = computed(() => {
  const searchTerm = kantorCabangSearchInput.value.toLowerCase()
  if (!searchTerm) {
    return kantorCabangList
  }
  return kantorCabangList.filter((kantor) =>
    kantor.label.toLowerCase().includes(searchTerm)
  )
})

// Select kantor cabang
const selectKantorCabang = (kantor: { value: string; label: string }) => {
  formData.kantorCabang = kantor.value
  kantorCabangSearch.value = kantor.label
  kantorCabangSearchInput.value = ''
  showKantorCabangDropdown.value = false
}

// Filter kantor cabang
const filterKantorCabang = () => {
  // Filter already handled in computed
}

// Form data
const formData = reactive({
  nama: '',
  noHandphone: null as number | null,
  email: '',
  namaBank: '',
  noRekening: null as number | null,
  tanggalLahir: '',
  pendidikan: '',
  kantorCabang: '',
})

// Handle click outside dropdown
const handleClickOutside = (event: MouseEvent) => {
  if (kantorCabangRef.value && !kantorCabangRef.value.contains(event.target as Node)) {
    showKantorCabangDropdown.value = false
    kantorCabangSearchInput.value = ''
  }
}

// Load data if edit mode
const loadData = async () => {
  if (isEditMode.value && route.params.id) {
    const id = route.params.id as string
    // TODO: Load data from API
    console.log('Loading data for ID:', id)
    
    // Set kantor cabang search value if data loaded
    if (formData.kantorCabang) {
      const selected = kantorCabangList.find((k) => k.value === formData.kantorCabang)
      if (selected) {
        kantorCabangSearch.value = selected.label
      }
    }
  }
}

// Handle cancel
const handleCancel = () => {
  router.push('/user-kepegawaian/mitra')
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
      console.log('Updating mitra:', formData)
      // await updateMitra(route.params.id, formData)
      alert('Mitra berhasil diupdate')
    } else {
      console.log('Creating mitra:', formData)
      // await createMitra(formData)
      alert('Mitra berhasil ditambahkan')
    }
    
    // Redirect to list
    router.push('/user-kepegawaian/mitra')
  } catch (error) {
    console.error('Error saving:', error)
    alert('Terjadi kesalahan saat menyimpan data')
  }
}

// Initialize kantor cabang search value
const updateKantorCabangSearch = () => {
  if (formData.kantorCabang) {
    const selected = kantorCabangList.find((k) => k.value === formData.kantorCabang)
    if (selected) {
      kantorCabangSearch.value = selected.label
    }
  }
}

onMounted(() => {
  loadData()
  updateKantorCabangSearch()
  document.addEventListener('click', handleClickOutside)
})

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<style>
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 3px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #888;
  border-radius: 3px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #555;
}

.dark .custom-scrollbar::-webkit-scrollbar-track {
  background: #1f2937;
}

.dark .custom-scrollbar::-webkit-scrollbar-thumb {
  background: #4b5563;
}

.dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #6b7280;
}
</style>

