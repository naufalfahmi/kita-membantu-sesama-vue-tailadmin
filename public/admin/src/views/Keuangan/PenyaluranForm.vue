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
          Kembali
        </button>
      </div>

      <form @submit.prevent="handleSave" class="flex flex-col">
        <div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2">
          <!-- Nominal -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Nominal <span class="text-red-500">*</span>
            </label>
            <div class="relative">
              <span
                class="absolute left-4 top-1/2 -translate-y-1/2 text-sm text-gray-500 dark:text-gray-400"
              >
                Rp
              </span>
              <input
                type="text"
                v-model="formData.amount"
                @input="formatAmount"
                placeholder="Masukkan nominal penyaluran"
                required
                class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pl-12 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
              />
            </div>
          </div>

          <!-- Program -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Program <span class="text-red-500">*</span>
            </label>
            <SearchableSelect
              v-model="formData.program"
              :options="programList"
              placeholder="Pilih atau cari program"
              :search-input="programSearchInput"
              @update:search-input="programSearchInput = $event"
            />
          </div>

          <!-- PIC -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              PIC <span class="text-red-500">*</span>
            </label>
            <input
              type="text"
              v-model="formData.pic"
              placeholder="Masukkan penanggung jawab penyaluran"
              required
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
          </div>

          <!-- Kelurahan -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Kelurahan
            </label>
            <input
              type="text"
              v-model="formData.village"
              placeholder="Masukkan kelurahan"
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
          </div>

          <!-- Kecamatan -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Kecamatan
            </label>
            <input
              type="text"
              v-model="formData.district"
              placeholder="Masukkan kecamatan"
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
          </div>

          <!-- Kota -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Kota
            </label>
            <input
              type="text"
              v-model="formData.city"
              placeholder="Masukkan kota"
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
          </div>

          <!-- Provinsi -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Provinsi
            </label>
            <input
              type="text"
              v-model="formData.province"
              placeholder="Masukkan provinsi"
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
          </div>

          <!-- Kode Pos -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Kode Pos
            </label>
            <input
              type="text"
              v-model="formData.postalCode"
              placeholder="Masukkan kode pos"
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
              v-model="formData.address"
              placeholder="Masukkan detail alamat lengkap"
              rows="3"
              class="dark:bg-dark-900 h-auto w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            ></textarea>
          </div>

          <!-- Laporan -->
          <div class="lg:col-span-2">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Laporan
            </label>
            <textarea
              v-model="formData.report"
              placeholder="Masukkan catatan laporan hasil penyaluran"
              rows="4"
              class="dark:bg-dark-900 h-auto w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            ></textarea>
          </div>

          <!-- Kantor Cabang -->
          <div class="lg:col-span-1">
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

          <!-- Gambar Dokumentasi -->
          <div class="lg:col-span-2">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Gambar Dokumentasi
            </label>
            <div class="space-y-4">
              <!-- File Input -->
              <div class="relative">
                <input
                  type="file"
                  ref="fileInputRef"
                  @change="handleFileSelect"
                  multiple
                  accept="image/*"
                  class="hidden"
                  id="image-upload"
                />
                <label
                  for="image-upload"
                  class="flex cursor-pointer items-center justify-center rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 px-6 py-8 text-center transition-colors hover:border-brand-300 hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-900/50 dark:hover:border-brand-500"
                >
                  <div class="text-center">
                    <svg
                      class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"
                      />
                    </svg>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                      <span class="font-medium text-brand-500">Klik untuk upload</span> atau drag and drop
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-500">
                      PNG, JPG, GIF hingga 20MB (Multiple files)
                    </p>
                  </div>
                </label>
              </div>

              <!-- Selected Files Preview -->
              <div v-if="selectedFiles.length > 0" class="space-y-2">
                <div
                  v-for="(file, index) in selectedFiles"
                  :key="index"
                  class="flex items-center justify-between rounded-lg border border-gray-200 bg-white p-3 dark:border-gray-700 dark:bg-gray-900/50"
                >
                  <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded bg-gray-100 dark:bg-gray-800">
                      <svg
                        class="h-6 w-6 text-gray-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                        />
                      </svg>
                    </div>
                    <div>
                      <p class="text-sm font-medium text-gray-800 dark:text-white/90">
                        {{ file.name }}
                      </p>
                      <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ formatFileSize(file.size) }}
                      </p>
                    </div>
                  </div>
                  <button
                    type="button"
                    @click="removeFile(index)"
                    class="rounded-lg p-1 text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10"
                  >
                    <svg
                      class="h-5 w-5"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"
                      />
                    </svg>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="flex items-center gap-3 mt-6 lg:justify-end">
          <button
            @click="handleCancel"
            type="button"
            class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] sm:w-auto"
          >
            Kembali
          </button>
          <button
            type="submit"
            class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto"
          >
            Buat
          </button>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { reactive, computed, ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import SearchableSelect from '@/components/forms/SearchableSelect.vue'

const route = useRoute()
const router = useRouter()

const isEditMode = computed(() => route.params.id !== undefined && route.params.id !== 'new')
const currentPageTitle = computed(() => {
  return isEditMode.value ? 'Edit Penyaluran' : 'Buat Penyaluran'
})

// Options for select fields
const programList = [
  { value: 'program-beasiswa', label: 'Program Beasiswa Pendidikan' },
  { value: 'program-kesehatan', label: 'Program Kesehatan Masyarakat' },
  { value: 'program-ekonomi', label: 'Program Pemberdayaan Ekonomi' },
  { value: 'program-pangan', label: 'Program Bantuan Pangan' },
  { value: 'program-sdm', label: 'Program Pengembangan SDM' },
  { value: 'program-lingkungan', label: 'Program Lingkungan Hidup' },
  { value: 'program-sosial', label: 'Program Sosial Budaya' },
  { value: 'program-infrastruktur', label: 'Program Infrastruktur' },
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
const programSearchInput = ref('')
const kantorCabangSearchInput = ref('')

// File upload
const fileInputRef = ref<HTMLInputElement | null>(null)
const selectedFiles = ref<File[]>([])

// Form data
const formData = reactive({
  amount: '',
  program: '',
  pic: '',
  village: '',
  district: '',
  city: '',
  province: '',
  postalCode: '',
  address: '',
  report: '',
  branchId: '',
})

// Format amount input (remove non-numeric characters except dots and commas)
const formatAmount = (event: Event) => {
  const target = event.target as HTMLInputElement
  let value = target.value.replace(/[^\d.,]/g, '')
  
  // Replace comma with dot for decimal
  value = value.replace(/,/g, '.')
  
  // Only allow one dot
  const parts = value.split('.')
  if (parts.length > 2) {
    value = parts[0] + '.' + parts.slice(1).join('')
  }
  
  formData.amount = value
}

// Handle file selection
const handleFileSelect = (event: Event) => {
  const target = event.target as HTMLInputElement
  if (target.files) {
    const files = Array.from(target.files)
    
    // Validate file size (max 20MB per file)
    const maxSize = 20 * 1024 * 1024 // 20MB in bytes
    const validFiles: File[] = []
    const invalidFiles: string[] = []
    
    files.forEach((file) => {
      if (file.size > maxSize) {
        invalidFiles.push(file.name)
      } else {
        validFiles.push(file)
      }
    })
    
    if (invalidFiles.length > 0) {
      alert(`File berikut melebihi 20MB: ${invalidFiles.join(', ')}`)
    }
    
    // Add valid files to selected files
    selectedFiles.value.push(...validFiles)
    
    // Reset input
    if (fileInputRef.value) {
      fileInputRef.value.value = ''
    }
  }
}

// Remove file
const removeFile = (index: number) => {
  selectedFiles.value.splice(index, 1)
}

// Format file size
const formatFileSize = (bytes: number): string => {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i]
}

// Load data if edit mode
const loadData = async () => {
  if (isEditMode.value && route.params.id) {
    const id = route.params.id as string
    // TODO: Load data from API
  }
}

// Handle cancel
const handleCancel = () => {
  router.push('/keuangan/penyaluran')
}

// Handle save
const handleSave = async () => {
  if (!formData.amount) {
    alert('Nominal wajib diisi')
    return
  }

  if (!formData.program) {
    alert('Program wajib diisi')
    return
  }

  if (!formData.pic) {
    alert('PIC wajib diisi')
    return
  }

  if (!formData.branchId) {
    alert('Kantor Cabang wajib diisi')
    return
  }

  try {
    // Prepare FormData for file upload
    const formDataToSend = new FormData()
    
    // Add form fields
    formDataToSend.append('amount', formData.amount)
    formDataToSend.append('program', formData.program)
    formDataToSend.append('pic', formData.pic)
    formDataToSend.append('village', formData.village)
    formDataToSend.append('district', formData.district)
    formDataToSend.append('city', formData.city)
    formDataToSend.append('province', formData.province)
    formDataToSend.append('postalCode', formData.postalCode)
    formDataToSend.append('address', formData.address)
    formDataToSend.append('report', formData.report)
    formDataToSend.append('branchId', formData.branchId)
    
    // Add files
    selectedFiles.value.forEach((file, index) => {
      formDataToSend.append(`images[${index}]`, file)
    })

    // TODO: Save to API
    if (isEditMode.value) {
      // await updatePenyaluran(route.params.id, formDataToSend)
      alert('Penyaluran berhasil diupdate')
    } else {
      // await createPenyaluran(formDataToSend)
      alert('Penyaluran berhasil dibuat')
    }
    
    // Redirect to list
    router.push('/keuangan/penyaluran')
  } catch (error) {
    console.error('Error saving:', error)
    alert('Terjadi kesalahan saat menyimpan data')
  }
}

onMounted(() => {
  loadData()
})
</script>




