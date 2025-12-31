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
            <select v-model="formData.submissionType" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800">
              <option value="program">Program</option>
              <option value="operasional">Operasional</option>
              <option value="gaji karyawan">Gaji Karyawan</option>
            </select>
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
import { reactive, computed, ref, onMounted, watch } from 'vue'
import { useToast } from 'vue-toastification'
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

// Search input refs
const programSearchInput = ref('')
const kantorCabangSearchInput = ref('')

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

// Form data
const formData = reactive({
  amount: '',
  program: '',
  submissionType: 'program',
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
      formData.amount = p.amount != null ? String(p.amount) : formData.amount
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

      // Note: existing images are not converted into File objects. Keep selectedFiles empty for uploads; images are available on server via p.images
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
        formData.program = (d.program && (d.program.nama || d.program.nama_program)) || formData.program
        formData.pic = d.fundraiser ? d.fundraiser.name : formData.pic
        formData.amount = d.amount || formData.amount
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




