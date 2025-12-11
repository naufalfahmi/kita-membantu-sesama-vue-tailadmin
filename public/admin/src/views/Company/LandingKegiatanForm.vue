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
          <!-- Nama Kegiatan -->
          <div class="lg:col-span-2">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Nama Kegiatan <span class="text-red-500">*</span>
            </label>
            <input
              type="text"
              v-model="formData.title"
              placeholder="Masukkan nama kegiatan"
              maxlength="100"
              required
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
              {{ formData.title.length }}/100 karakter
            </p>
            <p v-if="errors.title" class="mt-1 text-xs text-red-500">{{ errors.title }}</p>
          </div>

          <!-- Total Penerima Manfaat -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Total Penerima Manfaat <span class="text-red-500">*</span>
            </label>
            <input
              type="number"
              v-model.number="formData.number_of_recipients"
              placeholder="Masukkan total penerima manfaat"
              min="1"
              required
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
            <p v-if="formData.number_of_recipients" class="mt-1 text-xs text-gray-500 dark:text-gray-400">
              {{ formatNumber(formData.number_of_recipients) }} penerima
            </p>
            <p v-if="errors.number_of_recipients" class="mt-1 text-xs text-red-500">{{ errors.number_of_recipients }}</p>
          </div>

          <!-- Tanggal Kegiatan -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Tanggal Kegiatan <span class="text-red-500">*</span>
            </label>
            <flat-pickr
              v-model="formData.activity_date"
              :config="datePickerConfig"
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
              placeholder="Pilih tanggal kegiatan"
            />
            <p v-if="errors.activity_date" class="mt-1 text-xs text-red-500">{{ errors.activity_date }}</p>
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

          <!-- Kode Pos -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Kode Pos
            </label>
            <input
              type="text"
              v-model="formData.postal_code"
              placeholder="Masukkan kode pos"
              pattern="[0-9]*"
              @input="handlePostalCodeInput"
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
            <p v-if="errors.postal_code" class="mt-1 text-xs text-red-500">{{ errors.postal_code }}</p>
          </div>

          <!-- Alamat -->
          <div class="lg:col-span-2">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Alamat <span class="text-red-500">*</span>
            </label>
            <textarea
              v-model="formData.address"
              placeholder="Masukkan alamat lengkap"
              maxlength="1000"
              rows="3"
              required
              class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            ></textarea>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
              {{ formData.address.length }}/1000 karakter
            </p>
            <p v-if="errors.address" class="mt-1 text-xs text-red-500">{{ errors.address }}</p>
          </div>

          <!-- Status -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Status <span class="text-red-500">*</span>
            </label>
            <SearchableSelect
              v-model="formData.status"
              :options="statusOptions"
              placeholder="Pilih Status"
              :search-input="statusSearchInput"
              @update:search-input="statusSearchInput = $event"
            />
            <p v-if="errors.status" class="mt-1 text-xs text-red-500">{{ errors.status }}</p>
          </div>

          <!-- Gambar Program -->
          <div class="lg:col-span-2">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Gambar Program
            </label>
            <div class="space-y-4">
              <!-- File Input -->
              <div>
                <input
                  ref="fileInputRef"
                  type="file"
                  multiple
                  accept="image/jpeg,image/jpg,image/png,image/webp"
                  @change="handleFileSelect"
                  class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800"
                />
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                  Format: JPG, PNG, WebP. Maksimal 20MB per file.
                </p>
              </div>

              <!-- Preview Images -->
              <div v-if="selectedFiles.length > 0 || existingImages.length > 0" class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4">
                <!-- Existing Images -->
                <div
                  v-for="(image, index) in existingImages"
                  :key="`existing-${index}`"
                  class="relative group"
                >
                  <img
                    :src="`/storage/${image}`"
                    :alt="`Image ${index + 1}`"
                    class="h-32 w-full rounded-lg object-cover border border-gray-300 dark:border-gray-700"
                  />
                  <button
                    type="button"
                    @click="removeExistingImage(index)"
                    class="absolute top-2 right-2 flex items-center justify-center w-6 h-6 rounded-full bg-red-500 text-white opacity-0 group-hover:opacity-100 transition-opacity"
                  >
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M18 6L6 18M6 6l12 12"></path>
                    </svg>
                  </button>
                </div>

                <!-- New Images -->
                <div
                  v-for="(file, index) in selectedFiles"
                  :key="`new-${index}`"
                  class="relative group"
                >
                  <img
                    :src="getFilePreview(file)"
                    :alt="file.name"
                    class="h-32 w-full rounded-lg object-cover border border-gray-300 dark:border-gray-700"
                  />
                  <button
                    type="button"
                    @click="removeFile(index)"
                    class="absolute top-2 right-2 flex items-center justify-center w-6 h-6 rounded-full bg-red-500 text-white opacity-0 group-hover:opacity-100 transition-opacity"
                  >
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M18 6L6 18M6 6l12 12"></path>
                    </svg>
                  </button>
                  <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 truncate">
                    {{ file.name }}
                  </p>
                  <p class="text-xs text-gray-400 dark:text-gray-500">
                    {{ formatFileSize(file.size) }}
                  </p>
                </div>
              </div>
            </div>
            <p v-if="errors.images" class="mt-1 text-xs text-red-500">{{ errors.images }}</p>
          </div>

          <!-- Deskripsi -->
          <div class="lg:col-span-2">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Deskripsi <span class="text-red-500">*</span>
            </label>
            <div class="relative">
              <div
                ref="editorRef"
                contenteditable="true"
                v-html="formData.description"
                @input="handleDescriptionInput"
                class="dark:bg-dark-900 min-h-[200px] w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                style="white-space: pre-wrap;"
              ></div>
              <p v-if="!formData.description" class="absolute top-2.5 left-4 text-sm text-gray-400 pointer-events-none">
                Masukkan deskripsi kegiatan...
              </p>
            </div>
            <p v-if="errors.description" class="mt-1 text-xs text-red-500">{{ errors.description }}</p>
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
            :disabled="loading"
            class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 disabled:opacity-50 disabled:cursor-not-allowed sm:w-auto"
          >
            {{ loading ? 'Menyimpan...' : (isEditMode ? 'Simpan Perubahan' : 'Simpan') }}
          </button>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted } from 'vue'
import { useToast } from 'vue-toastification'
import { useRoute, useRouter } from 'vue-router'
import flatPickr from 'vue-flatpickr-component'
import 'flatpickr/dist/flatpickr.css'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import SearchableSelect from '@/components/forms/SearchableSelect.vue'

// Options for Status dropdown
const statusOptions = [
  { value: 'active', label: 'Active' },
  { value: 'inactive', label: 'Inactive' },
]
const statusSearchInput = ref('')

const route = useRoute()
const router = useRouter()
const fileInputRef = ref<HTMLInputElement | null>(null)
const editorRef = ref<HTMLElement | null>(null)

const isEditMode = computed(() => route.params.id !== undefined && route.params.id !== 'new')
const currentPageTitle = computed(() => {
  return isEditMode.value ? 'Edit Landing Kegiatan' : 'Tambah Landing Kegiatan'
})

const loading = ref(false)
const toast = useToast()

// Date picker config
const datePickerConfig = {
  dateFormat: 'Y-m-d',
  locale: {
    firstDayOfWeek: 1,
  },
}

// Form data
const formData = reactive({
  title: '',
  number_of_recipients: 0,
  village: '',
  district: '',
  city: '',
  province: '',
  postal_code: '',
  address: '',
  activity_date: '',
  status: '',
  description: '',
})

// Errors
const errors = reactive({
  title: '',
  number_of_recipients: '',
  postal_code: '',
  address: '',
  activity_date: '',
  status: '',
  description: '',
  images: '',
})

// File handling
const selectedFiles = ref<File[]>([])
const existingImages = ref<string[]>([])
const imagesToDelete = ref<string[]>([])

// Format number with thousand separator
const formatNumber = (num: number): string => {
  return new Intl.NumberFormat('id-ID').format(num)
}

// Handle postal code input (numeric only)
const handlePostalCodeInput = (event: Event) => {
  const target = event.target as HTMLInputElement
  formData.postal_code = target.value.replace(/[^0-9]/g, '')
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
      // Validate MIME type
      const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp']
      if (!validTypes.includes(file.type)) {
        invalidFiles.push(`${file.name} (format tidak didukung)`)
        return
      }
      
      if (file.size > maxSize) {
        invalidFiles.push(`${file.name} (melebihi 20MB)`)
      } else {
        validFiles.push(file)
      }
    })
    
    if (invalidFiles.length > 0) {
          toast.error(`File berikut tidak valid:\n${invalidFiles.join('\n')}`)
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
const removeExistingImage = (index: number) => {
  const image = existingImages.value[index]
  imagesToDelete.value.push(image)
  existingImages.value.splice(index, 1)
}

// Get file preview URL
const getFilePreview = (file: File): string => {
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

// Handle description input
const handleDescriptionInput = (event: Event) => {
  const target = event.target as HTMLElement
  formData.description = target.innerHTML || ''
}

// Validation
const validateForm = (): boolean => {
  let isValid = true
  
  // Reset errors
  Object.keys(errors).forEach(key => {
    errors[key as keyof typeof errors] = ''
  })
  
  // Validate title
  if (!formData.title || formData.title.trim() === '') {
    errors.title = 'Nama kegiatan wajib diisi'
    isValid = false
  } else if (formData.title.length > 100) {
    errors.title = 'Nama kegiatan maksimal 100 karakter'
    isValid = false
  }
  
  // Validate number of recipients
  if (!formData.number_of_recipients || formData.number_of_recipients <= 0) {
    errors.number_of_recipients = 'Total penerima manfaat wajib diisi dan harus lebih dari 0'
    isValid = false
  }
  
  // Validate postal code
  if (formData.postal_code && !/^[0-9]+$/.test(formData.postal_code)) {
    errors.postal_code = 'Kode pos harus berupa angka'
    isValid = false
  }
  
  // Validate address
  if (!formData.address || formData.address.trim() === '') {
    errors.address = 'Alamat wajib diisi'
    isValid = false
  } else if (formData.address.length > 1000) {
    errors.address = 'Alamat maksimal 1000 karakter'
    isValid = false
  }
  
  // Validate activity date
  if (!formData.activity_date) {
    errors.activity_date = 'Tanggal kegiatan wajib diisi'
    isValid = false
  }
  
  // Validate status
  if (!formData.status) {
    errors.status = 'Status wajib diisi'
    isValid = false
  }
  
  // Validate description
  if (!formData.description || formData.description.trim() === '') {
    errors.description = 'Deskripsi wajib diisi'
    isValid = false
  }
  
  return isValid
}

// Load data if edit mode
const loadData = async () => {
  if (isEditMode.value && route.params.id) {
    const id = route.params.id as string
    loading.value = true
    
    try {
      const response = await fetch(`/admin/api/landing-kegiatan/${id}`, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
        },
        credentials: 'same-origin',
      })
      
      const result = await response.json()
      if (result.success && result.data) {
        const data = result.data
        formData.title = data.title || ''
        formData.number_of_recipients = data.number_of_recipients || 0
        formData.village = data.village || ''
        formData.district = data.district || ''
        formData.city = data.city || ''
        formData.province = data.province || ''
        formData.postal_code = data.postal_code || ''
        formData.address = data.address || ''
        formData.activity_date = data.activity_date || ''
        formData.status = data.status || ''
        formData.description = data.description || ''
        
        // Load existing images
        if (data.images) {
          try {
            const images = JSON.parse(data.images)
            if (Array.isArray(images)) {
              existingImages.value = images
            }
          } catch (e) {
            console.error('Error parsing images:', e)
          }
        }
      } else {
        toast.error(result.message || 'Gagal memuat data')
        router.push('/company/landing-kegiatan')
      }
    } catch (error) {
      console.error('Error loading data:', error)
      toast.error('Terjadi kesalahan saat memuat data')
      router.push('/company/landing-kegiatan')
    } finally {
      loading.value = false
    }
  }
}

// Handle cancel
const handleCancel = () => {
  router.push('/company/landing-kegiatan')
}

// Handle save
const handleSave = async () => {
  if (!validateForm()) {
    return
  }

  loading.value = true

  try {
    // Prepare FormData for file upload
    const formDataToSend = new FormData()
    
    // Add form fields
    formDataToSend.append('title', formData.title)
    formDataToSend.append('number_of_recipients', formData.number_of_recipients.toString())
    formDataToSend.append('village', formData.village || '')
    formDataToSend.append('district', formData.district || '')
    formDataToSend.append('city', formData.city || '')
    formDataToSend.append('province', formData.province || '')
    formDataToSend.append('postal_code', formData.postal_code || '')
    formDataToSend.append('address', formData.address)
    formDataToSend.append('activity_date', formData.activity_date)
    formDataToSend.append('status', formData.status)
    formDataToSend.append('description', formData.description)
    
    // Add new files
    selectedFiles.value.forEach((file, index) => {
      formDataToSend.append(`images[${index}]`, file)
    })
    
    // Add images to delete
    if (imagesToDelete.value.length > 0) {
      formDataToSend.append('images_to_delete', JSON.stringify(imagesToDelete.value))
    }

    // Ensure CSRF token is included for FormData requests
    const getCsrfToken = (): string => {
      return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
    }

    let token = getCsrfToken()
    if (!token) {
      try {
        const tokenRes = await fetch('/admin/api/csrf-token', { credentials: 'same-origin' })
        if (tokenRes.ok) {
          const tokenJson = await tokenRes.json()
          token = tokenJson.csrf_token || token
        }
      } catch (e) {
        // ignore; request may fail later with CSRF mismatch
      }
    }

    if (token) {
      formDataToSend.append('_token', token)
    }

    const url = isEditMode.value
      ? `/admin/api/landing-kegiatan/${route.params.id}`
      : '/admin/api/landing-kegiatan'
    
    const method = isEditMode.value ? 'PUT' : 'POST'

    // For multipart + method override: use POST with _method=PUT when updating
    const fetchMethod = method === 'PUT' ? 'POST' : method
    if (method === 'PUT') {
      formDataToSend.append('_method', 'PUT')
    }

    const response = await fetch(url, {
      method: fetchMethod,
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
        ...(token ? { 'X-CSRF-TOKEN': token } : {}),
      },
      credentials: 'same-origin',
      body: formDataToSend,
    })

    const result = await response.json()
    
    if (result.success) {
      toast.success(isEditMode.value ? 'Kegiatan berhasil diupdate' : 'Kegiatan berhasil ditambahkan')
      router.push('/company/landing-kegiatan')
    } else {
      // Handle validation errors
      if (result.errors) {
        Object.keys(result.errors).forEach(key => {
          if (errors.hasOwnProperty(key)) {
            errors[key as keyof typeof errors] = Array.isArray(result.errors[key])
              ? result.errors[key][0]
              : result.errors[key]
          }
        })
      }
      toast.error(result.message || 'Gagal menyimpan data')
    }
  } catch (error) {
    console.error('Error saving:', error)
    toast.error('Terjadi kesalahan saat menyimpan data')
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadData()
})
</script>

<style>
/* Flatpickr styling */
.flatpickr-input {
  background: transparent;
}

/* Contenteditable placeholder */
[contenteditable]:empty:before {
  content: attr(placeholder);
  color: #9ca3af;
}
</style>

