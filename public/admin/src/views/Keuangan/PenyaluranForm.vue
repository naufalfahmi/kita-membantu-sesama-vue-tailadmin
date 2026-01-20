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
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
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
              />
              <span class="absolute text-gray-500 -translate-y-1/2 pointer-events-none right-4 top-1/2 dark:text-gray-400 text-sm">{{ formattedAmountDisplay }}</span>
            </div>
          </div>

          <!-- Program -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Nama Penyaluran <span class="text-red-500">*</span>
            </label>
            <input
              type="text"
              v-model="formData.program"
              placeholder="Masukkan nama program atau tahapannya"
              required
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
          </div>

          <!-- Tipe Kredit -->
          <div class="lg:col-span-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Tipe Penyaluran <span class="text-red-500">*</span></label>
            <SearchableSelect
              v-model="formData.submissionType"
              :options="submissionTypeList"
              placeholder="Pilih tipe penyaluran"
              :search-input="submissionTypeSearchInput"
              @update:search-input="submissionTypeSearchInput = $event"
            />
            <p class="mt-2 text-sm text-gray-600">Sisa kredit untuk <span class="font-medium">{{ formData.submissionType }}</span>: <span class="font-medium">{{ formattedCreditTotal }}</span></p>
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

          <!-- Kantor Cabang (auto from logged-in user) -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Kantor Cabang <span class="text-red-500">*</span>
            </label>
            <template v-if="currentUser?.is_admin">
              <SearchableSelect
                v-model="formData.branchId"
                :options="kantorCabangList"
                placeholder="Pilih atau cari kantor cabang"
                :search-input="kantorCabangSearchInput"
                @update:search-input="kantorCabangSearchInput = $event"
              />
            </template>
            <template v-else>
              <input
                type="text"
                :value="currentUser?.kantor_cabang?.nama || ''"
                placeholder="Kantor cabang Anda"
                readonly
                class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:outline-none dark:border-gray-700 dark:bg-gray-900/50 dark:text-white/90"
              />
              <!-- hidden input to send id -->
              <input type="hidden" v-model="formData.branchId" />
            </template>
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

              <!-- Existing Images (Edit Mode) -->
              <div v-if="existingImages.length > 0" class="mb-6">
                <h4 class="mb-3 text-sm font-medium text-gray-700 dark:text-gray-300">Gambar yang Sudah Ada</h4>
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                  <div
                    v-for="img in existingImages"
                    :key="img.id"
                    class="relative group"
                  >
                    <!-- Image Preview -->
                    <div class="aspect-square overflow-hidden rounded-lg border-2 border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-900/50">
                      <img
                        :src="`/storage/${img.path}`"
                        :alt="img.caption || 'Dokumentasi'"
                        class="h-full w-full object-cover"
                      />
                    </div>
                    
                    <!-- Remove Button -->
                    <button
                      type="button"
                      @click="removeExistingImage(img.id)"
                      class="absolute -right-2 -top-2 flex h-7 w-7 items-center justify-center rounded-full bg-red-500 text-white shadow-lg transition-transform hover:scale-110 hover:bg-red-600"
                      title="Hapus gambar"
                    >
                      <svg
                        class="h-4 w-4"
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

              <!-- Selected Files Preview -->
              <div v-if="selectedFiles.length > 0">
                <h4 class="mb-3 text-sm font-medium text-gray-700 dark:text-gray-300">Gambar Baru yang Akan Diupload</h4>
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                <div
                  v-for="(file, index) in selectedFiles"
                  :key="index"
                  class="relative group"
                >
                  <!-- Image Preview -->
                  <div class="aspect-square overflow-hidden rounded-lg border-2 border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-900/50">
                    <img
                      :src="getFilePreviewUrl(file)"
                      :alt="file.name"
                      class="h-full w-full object-cover"
                    />
                  </div>
                  
                  <!-- File Info -->
                  <div class="mt-2">
                    <p class="truncate text-xs font-medium text-gray-700 dark:text-gray-300">
                      {{ file.name }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                      {{ formatFileSize(file.size) }}
                    </p>
                  </div>
                  
                  <!-- Remove Button -->
                  <button
                    type="button"
                    @click="removeFile(index)"
                    class="absolute -right-2 -top-2 flex h-7 w-7 items-center justify-center rounded-full bg-red-500 text-white shadow-lg transition-transform hover:scale-110 hover:bg-red-600"
                    title="Hapus gambar"
                  >
                    <svg
                      class="h-4 w-4"
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
            Simpan
          </button>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { reactive, computed, ref, onMounted, watch } from 'vue'
import { useToast } from 'vue-toastification'
import { useRoute, useRouter } from 'vue-router'
import AdminLayout from '@/components/layout/AdminLayout.vue'
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

import { ref as vueRef } from 'vue'
const kantorCabangList = vueRef<Array<{ value: string; label: string }>>([])

const fetchKantorCabangOptions = async () => {
  try {
    const res = await fetch('/admin/api/kantor-cabang?per_page=1000', { credentials: 'same-origin' })
    if (!res.ok) return
    const json = await res.json()
    const dataArray = json.success && json.data ? (Array.isArray(json.data) ? json.data : json.data.data || []) : []
    kantorCabangList.value = dataArray.map((item: any) => ({ value: item.id, label: item.nama }))
  } catch (e) {
    console.error('Failed to fetch kantor cabang options', e)
  }
}

// Fetch submission types from API (same as PengajuanDanaForm)
const fetchSubmissionTypes = async () => {
  try {
    const res = await fetch('/admin/api/program-share-types/submission-types', {
      credentials: 'same-origin',
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    const json = await res.json()
    if (json.success && Array.isArray(json.data)) {
      submissionTypeList.value = json.data.map((item: any) => ({
        value: item.value,
        label: item.value,
      }))
      
      // Set default submission type to first item if not already set
      if (!formData.submissionType && submissionTypeList.value.length > 0) {
        formData.submissionType = submissionTypeList.value[0].value
      }
    } else {
      // Fallback to hardcoded values if API fails
      submissionTypeList.value = [
        { value: 'Program', label: 'Program' },
        { value: 'Operasional', label: 'Operasional' },
        { value: 'Gaji Karyawan', label: 'Gaji Karyawan' },
      ]
      if (!formData.submissionType) {
        formData.submissionType = 'Program'
      }
    }
  } catch (err) {
    console.error('Error fetching submission types', err)
    // Fallback to hardcoded values
    submissionTypeList.value = [
      { value: 'Program', label: 'Program' },
      { value: 'Operasional', label: 'Operasional' },
      { value: 'Gaji Karyawan', label: 'Gaji Karyawan' },
    ]
    if (!formData.submissionType) {
      formData.submissionType = 'Program'
    }
  }
}

// Search input refs
const programSearchInput = ref('')
const kantorCabangSearchInput = ref('')
const submissionTypeSearchInput = ref('')

// Submission types from API
const submissionTypeList = ref<Array<{ value: string; label: string }>>([])

// Current user
const currentUser = ref<any>(null)

const fetchCurrentUser = async () => {
  try {
    const res = await fetch('/admin/api/user', { credentials: 'same-origin' })
    if (res.ok) {
      const json = await res.json()
      if (json.success && json.user) {
        currentUser.value = json.user
        if (currentUser.value?.kantor_cabang?.id) {
          formData.branchId = String(currentUser.value.kantor_cabang.id)
        }
        // if admin, fetch full kantor cabang options
        if (currentUser.value?.is_admin) {
          await fetchKantorCabangOptions()
        }
      }
    }
  } catch (e) {
    console.error('Failed to fetch current user', e)
  }
}

// File upload
const fileInputRef = ref<HTMLInputElement | null>(null)
const selectedFiles = ref<File[]>([])
const existingImages = ref<Array<{ id: string; path: string; caption?: string }>>([])

// Form data
const formData = reactive({
  amount: null as number | null,
  program: '',
  submissionType: '',
  pic: '',
  village: '',
  district: '',
  city: '',
  province: '',
  postalCode: '',
  address: '',
  report: '',
  branchId: '',
  pengajuanId: '',
})

const creditTotal = ref<number>(0)
const formattedCreditTotal = computed(() => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(creditTotal.value || 0))

const formattedAmountDisplay = computed(() => {
  const v = Number(formData.amount || 0)
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(v)
})

const loadCreditForType = async (type: string) => {
  try {
    if (!type) {
      creditTotal.value = 0
      return
    }
    const res = await fetch(`/admin/api/penyaluran/my-credit?type=${encodeURIComponent(type)}`, { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    const json = await res.json()
    if (json && json.success && json.data && typeof json.data.remaining !== 'undefined') {
      creditTotal.value = Number(json.data.remaining) || 0
    } else {
      creditTotal.value = 0
    }
  } catch (e) {
    console.error('Failed to load credit total for type', type, e)
    creditTotal.value = 0
  }
}

// Format amount input (remove non-numeric characters except dots and commas)
  // formatAmount removed — using native number input with v-model.number

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

// Remove existing image
const removeExistingImage = async (imageId: string) => {
  if (!confirm('Hapus gambar ini?')) return
  
  try {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
    const res = await fetch(`/admin/api/penyaluran/images/${imageId}`, {
      method: 'DELETE',
      credentials: 'same-origin',
      headers: {
        'X-CSRF-TOKEN': csrfToken,
        'X-Requested-With': 'XMLHttpRequest',
      },
    })
    
    if (!res.ok) throw new Error('Failed to delete image')
    const json = await res.json()
    
    if (json.success) {
      // Remove from existingImages array
      existingImages.value = existingImages.value.filter(img => img.id !== imageId)
      toast.success('Gambar berhasil dihapus')
    } else {
      toast.error(json.message || 'Gagal menghapus gambar')
    }
  } catch (err) {
    console.error('Error deleting image', err)
    toast.error('Terjadi kesalahan saat menghapus gambar')
  }
}

// Get file preview URL
const getFilePreviewUrl = (file: File): string => {
  return URL.createObjectURL(file)
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
    try {
      const res = await fetch(`/admin/api/penyaluran/${id}`, { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      if (!res.ok) throw new Error('Failed to fetch penyaluran')
      const json = await res.json()
      if (!json.success || !json.data) {
        console.error('Penyaluran tidak ditemukan')
        return
      }

      const p = json.data
      // Map fields into formData
      formData.amount = p.amount != null ? Number(p.amount) : formData.amount
      formData.program = p.program_name || (p.pengajuan && (p.pengajuan.program?.nama_program || p.pengajuan.program?.nama)) || formData.program
      formData.pic = p.pic || (p.pengajuan && p.pengajuan.fundraiser ? p.pengajuan.fundraiser.name : formData.pic)
      formData.village = p.village || p.kelurahan || formData.village
      formData.district = p.district || p.kecamatan || formData.district
      formData.city = p.city || p.kota || formData.city
      formData.province = p.province || formData.province
      formData.postalCode = p.postal_code || formData.postalCode
      formData.address = p.address || formData.address
      formData.report = p.report || formData.report
      formData.branchId = p.kantor_cabang_id || (p.kantor_cabang && p.kantor_cabang.id) || formData.branchId
      formData.pengajuanId = p.pengajuan_dana_id || formData.pengajuanId

      // Load existing images
      if (p.images && Array.isArray(p.images)) {
        existingImages.value = p.images.map((img: any) => ({
          id: img.id,
          path: img.path,
          caption: img.caption || '',
        }))
      }
    } catch (err) {
      console.error('Error loading penyaluran', err)
    }
  }
}

// Handle cancel
const handleCancel = () => {
  router.push('/keuangan/penyaluran')
}

const toast = useToast()

// Handle save
const handleSave = async () => {
  if (!formData.amount) {
    toast.error('Nominal wajib diisi')
    return
  }

  if (!formData.program) {
    toast.error('Program wajib diisi')
    return
  }

  if (!formData.pic) {
    toast.error('PIC wajib diisi')
    return
  }

  if (!formData.branchId) {
    toast.error('Kantor Cabang wajib diisi')
    return
  }

  try {
    // Prepare FormData for file upload
    const formDataToSend = new FormData()
    
    // Add form fields
    formDataToSend.append('amount', String(formData.amount || '0'))
    if (formData.pengajuanId) {
      formDataToSend.append('pengajuan_dana_id', String(formData.pengajuanId))
    }
    if (formData.submissionType) {
      formDataToSend.append('submission_type', String(formData.submissionType))
    }
    formDataToSend.append('program_name', formData.program || '')
    formDataToSend.append('pic', formData.pic || '')
    formDataToSend.append('village', formData.village || '')
    formDataToSend.append('district', formData.district || '')
    formDataToSend.append('city', formData.city || '')
    formDataToSend.append('province', formData.province || '')
    formDataToSend.append('postal_code', formData.postalCode || '')
    formDataToSend.append('address', formData.address || '')
    formDataToSend.append('report', formData.report || '')
    formDataToSend.append('kantor_cabang_id', formData.branchId || '')
    
    // Add files
    selectedFiles.value.forEach((file) => {
      formDataToSend.append('images[]', file)
    })

    // TODO: Save to API
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
    const url = isEditMode.value ? `/admin/api/penyaluran/${route.params.id}` : '/admin/api/penyaluran'
    // When updating via FormData, use POST + _method=PUT so Laravel route matches
    if (isEditMode.value) {
      formDataToSend.append('_method', 'PUT')
    }
    const res = await fetch(url, { method: 'POST', body: formDataToSend, credentials: 'same-origin', headers: { 'X-CSRF-TOKEN': csrf } })
    const json = await res.json()
    if (!json.success) {
      toast.error(json.message || 'Gagal menyimpan penyaluran')
    } else {
      toast.success(isEditMode.value ? 'Penyaluran berhasil diupdate' : 'Penyaluran berhasil dibuat')
      // Redirect to list first so the list page mounts and registers its listeners,
      // then dispatch an event to tell it to refresh credit/list.
      try {
        await router.push('/keuangan/penyaluran')
        window.dispatchEvent(new CustomEvent('penyaluran:changed'))
        return
      } catch (e) {
        // fallback: navigate without awaiting then dispatch
        router.push('/keuangan/penyaluran')
        try { window.dispatchEvent(new CustomEvent('penyaluran:changed')) } catch (err) {}
      }
    }
  } catch (error) {
    console.error('Error saving:', error)
    toast.error('Terjadi kesalahan saat menyimpan data')
  }
}

onMounted(async () => {
  await fetchSubmissionTypes()
  await fetchCurrentUser()
  const pengajuanId = route.query.pengajuan_id
  if (pengajuanId) {
    try {
      const res = await fetch(`/admin/api/pengajuan-dana/${pengajuanId}`, { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      const json = await res.json()
      if (json.success && json.data) {
        const d = json.data
        // remember pengajuan id so we send it when saving
        formData.pengajuanId = String(pengajuanId)
        formData.submissionType = d.submission_type || formData.submissionType
        
        // If submission type from API is not in the list, add it as an option
        if (d.submission_type && !submissionTypeList.value.find(st => st.value === d.submission_type)) {
          submissionTypeList.value.unshift({ 
            value: d.submission_type, 
            label: d.submission_type,
          })
        }
        
        formData.program = (d.program && (d.program.nama || d.program.nama_program)) || formData.program
        formData.pic = d.fundraiser ? d.fundraiser.name : formData.pic
        formData.amount = (typeof d.amount !== 'undefined' && d.amount !== null) ? Number(d.amount) : formData.amount
        formData.report = d.purpose || formData.report
      }
    } catch (e) {
      console.error('Failed to load pengajuan', e)
    }
  }
  loadData()
    // load credit for the selected submission type (prefill may set it)
    loadCreditForType(formData.submissionType)
})

// reload credit when user changes the submission type in the form
watch(() => formData.submissionType, (val) => {
  loadCreditForType(String(val || ''))
})
</script>




