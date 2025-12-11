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
          <!-- Nama Proposal -->
          <div class="lg:col-span-2">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Nama Proposal <span class="text-red-500">*</span>
            </label>
            <input
              type="text"
              v-model="formData.name"
              placeholder="Masukkan nama proposal"
              maxlength="100"
              required
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
              {{ formData.name.length }}/100 karakter
            </p>
            <p v-if="errors.name" class="mt-1 text-xs text-red-500">{{ errors.name }}</p>
          </div>

          <!-- Tanggal Proposal -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Tanggal Proposal <span class="text-red-500">*</span>
            </label>
            <flat-pickr
              v-model="formData.proposal_date"
              :config="datePickerConfig"
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
              placeholder="Pilih tanggal proposal"
            />
            <p v-if="errors.proposal_date" class="mt-1 text-xs text-red-500">{{ errors.proposal_date }}</p>
          </div>

          <!-- File Proposal -->
          <div class="lg:col-span-2">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              File Proposal <span class="text-red-500">*</span>
            </label>
            <div class="space-y-4">
              <!-- File Input -->
              <div>
                <input
                  ref="fileInputRef"
                  type="file"
                  accept=".pdf,.doc,.docx"
                  @change="handleFileSelect"
                  class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800"
                />
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                  Format: PDF, DOC, DOCX. Maksimal 20MB. Hanya 1 file per proposal.
                </p>
              </div>

              <!-- Preview File -->
              <div v-if="selectedFile || existingFile" class="space-y-2">
                <!-- Existing File -->
                <div v-if="existingFile && !selectedFile" class="flex items-center gap-3 rounded-lg border border-gray-300 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                  <div class="flex-shrink-0">
                    <svg class="h-10 w-10 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                  </div>
                  <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-800 dark:text-white/90 truncate">
                      {{ existingFile.name || 'File Proposal' }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                      {{ existingFile.size ? formatFileSize(existingFile.size) : '' }}
                    </p>
                  </div>
                  <div class="flex items-center gap-2">
                    <a
                      v-if="existingFile.url"
                      :href="existingFile.url"
                      target="_blank"
                      class="flex items-center gap-1 rounded-lg bg-brand-500 px-3 py-1.5 text-xs font-medium text-white hover:bg-brand-600"
                    >
                      <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                      Lihat
                    </a>
                    <button
                      type="button"
                      @click="removeExistingFile"
                      class="flex items-center justify-center w-8 h-8 rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors"
                    >
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 6L6 18M6 6l12 12"></path>
                      </svg>
                    </button>
                  </div>
                </div>

                <!-- New File -->
                <div v-if="selectedFile" class="flex items-center gap-3 rounded-lg border border-gray-300 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                  <div class="flex-shrink-0">
                    <svg class="h-10 w-10 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                  </div>
                  <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-800 dark:text-white/90 truncate">
                      {{ selectedFile.name }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                      {{ formatFileSize(selectedFile.size) }}
                    </p>
                  </div>
                  <button
                    type="button"
                    @click="removeFile"
                    class="flex items-center justify-center w-8 h-8 rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors"
                  >
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M18 6L6 18M6 6l12 12"></path>
                    </svg>
                  </button>
                </div>
              </div>
            </div>
            <p v-if="errors.file" class="mt-1 text-xs text-red-500">{{ errors.file }}</p>
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

const route = useRoute()
const router = useRouter()
const fileInputRef = ref<HTMLInputElement | null>(null)

const isEditMode = computed(() => route.params.id !== undefined && route.params.id !== 'new')
const currentPageTitle = computed(() => {
  return isEditMode.value ? 'Edit Landing Proposal' : 'Tambah Landing Proposal'
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
  name: '',
  proposal_date: '',
})

// Errors
const errors = reactive({
  name: '',
  proposal_date: '',
  file: '',
})

// File handling
const selectedFile = ref<File | null>(null)
const existingFile = ref<{ name: string; url?: string; size?: number } | null>(null)

// Format file size
const formatFileSize = (bytes: number): string => {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i]
}

// Handle file selection
const handleFileSelect = (event: Event) => {
  const target = event.target as HTMLInputElement
  if (target.files && target.files.length > 0) {
    const file = target.files[0]
    
    // Validate file type
    const validTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']
    const validExtensions = ['.pdf', '.doc', '.docx']
    const fileExtension = '.' + file.name.split('.').pop()?.toLowerCase()
    
    if (!validTypes.includes(file.type) && !validExtensions.includes(fileExtension)) {
      errors.file = 'Format file tidak didukung. Hanya PDF, DOC, dan DOCX yang diizinkan.'
      if (fileInputRef.value) {
        fileInputRef.value.value = ''
      }
      return
    }
    
    // Validate file size (max 20MB)
    const maxSize = 20 * 1024 * 1024 // 20MB in bytes
    if (file.size > maxSize) {
      errors.file = 'Ukuran file melebihi 20MB. Mohon pilih file yang lebih kecil.'
      if (fileInputRef.value) {
        fileInputRef.value.value = ''
      }
      return
    }
    
    // Clear previous errors
    errors.file = ''
    
    // Set selected file
    selectedFile.value = file
    existingFile.value = null // Clear existing file when new file is selected
    
    // Reset input to allow selecting the same file again
    if (fileInputRef.value) {
      fileInputRef.value.value = ''
    }
  }
}

// Remove file
const removeFile = () => {
  selectedFile.value = null
  if (fileInputRef.value) {
    fileInputRef.value.value = ''
  }
  errors.file = ''
}

// Remove existing file
const removeExistingFile = () => {
  existingFile.value = null
  errors.file = ''
}

// Validation
const validateForm = (): boolean => {
  let isValid = true
  
  // Reset errors
  Object.keys(errors).forEach(key => {
    errors[key as keyof typeof errors] = ''
  })
  
  // Validate name
  if (!formData.name || formData.name.trim() === '') {
    errors.name = 'Nama proposal wajib diisi'
    isValid = false
  } else if (formData.name.length > 100) {
    errors.name = 'Nama proposal maksimal 100 karakter'
    isValid = false
  }
  
  // Validate proposal date
  if (!formData.proposal_date) {
    errors.proposal_date = 'Tanggal proposal wajib diisi'
    isValid = false
  } else {
    // Validate date format
    const date = new Date(formData.proposal_date)
    if (isNaN(date.getTime())) {
      errors.proposal_date = 'Format tanggal tidak valid'
      isValid = false
    }
  }
  
  // Validate file
  if (!selectedFile.value && !existingFile.value) {
    errors.file = 'File proposal wajib diupload'
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
      const response = await fetch(`/admin/api/landing-proposal/${id}`, {
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
        formData.name = data.name || ''
        formData.proposal_date = data.proposal_date || ''
        
        // Load existing file
        if (data.file) {
          existingFile.value = {
            name: data.file_name || 'File Proposal',
            url: data.file_url || (String(data.file).startsWith('http') ? data.file : `/storage/${data.file}`),
            size: data.file_size || undefined,
          }
        }
      } else {
        toast.error(result.message || 'Gagal memuat data')
        router.push('/company/landing-proposal')
      }
    } catch (error) {
      console.error('Error loading data:', error)
      toast.error('Terjadi kesalahan saat memuat data')
      router.push('/company/landing-proposal')
    } finally {
      loading.value = false
    }
  }
}

// Handle cancel
const handleCancel = () => {
  router.push('/company/landing-proposal')
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
    formDataToSend.append('name', formData.name)
    formDataToSend.append('proposal_date', formData.proposal_date)
    
    // Add file if new file is selected
    if (selectedFile.value) {
      formDataToSend.append('file', selectedFile.value)
    }
    
    // If editing and no new file selected, keep existing file
    // (backend should handle this)

    // CSRF handling: prefer meta tag, fallback to API endpoint
    const metaToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    let csrfToken = metaToken || ''
    if (!csrfToken) {
      try {
        const tokenRes = await fetch('/admin/api/csrf-token')
        const tokenJson = await tokenRes.json()
        csrfToken = tokenJson?.csrf_token || tokenJson?.token || ''
      } catch (e) {
        // ignore
      }
    }

    // Append CSRF token to form for multipart requests
    if (csrfToken) {
      formDataToSend.append('_token', csrfToken)
    }

    // For multipart/form-data updates, Laravel expects POST + _method=PUT
    const url = isEditMode.value ? `/admin/api/landing-proposal/${route.params.id}` : '/admin/api/landing-proposal'
    // Always send as POST (multipart), use method override for update
    if (isEditMode.value) {
      formDataToSend.append('_method', 'PUT')
    }

    const headers: Record<string, string> = {
      'X-Requested-With': 'XMLHttpRequest',
      'Accept': 'application/json',
    }
    if (csrfToken) headers['X-CSRF-TOKEN'] = csrfToken

    const response = await fetch(url, {
      method: 'POST',
      headers,
      credentials: 'same-origin',
      body: formDataToSend,
    })

    const result = await response.json()
    
    if (result.success) {
      toast.success(isEditMode.value ? 'Proposal berhasil diupdate' : 'Proposal berhasil ditambahkan')
      router.push('/company/landing-proposal')
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
</style>




