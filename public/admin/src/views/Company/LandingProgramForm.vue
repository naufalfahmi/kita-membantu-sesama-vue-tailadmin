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
          <!-- Nama Program -->
          <div class="lg:col-span-2">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Nama Program <span class="text-red-500">*</span>
            </label>
            <input
              type="text"
              v-model="formData.name"
              @input="handleNameInput"
              placeholder="Masukkan nama program"
              required
              maxlength="100"
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
              :class="{ 'border-red-300 dark:border-red-500': errors.name }"
            />
            <div class="mt-1 flex items-center justify-between">
              <span v-if="errors.name" class="text-xs text-red-500">{{ errors.name }}</span>
              <span v-else class="text-xs text-gray-500 dark:text-gray-400"></span>
              <span class="text-xs text-gray-500 dark:text-gray-400">
                {{ formData.name.length }}/100 karakter
              </span>
            </div>
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
              placeholder="Pilih status"
              :searchInput="statusSearchInput"
              @update:search-input="statusSearchInput = $event"
            />
            <span v-if="errors.status" class="mt-1 block text-xs text-red-500">{{ errors.status }}</span>
          </div>

          <!-- Highlight -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Highlight
            </label>
            <SearchableSelect
              v-model="formData.highlight"
              :options="highlightOptions"
              placeholder="Pilih highlight"
              :searchInput="highlightSearchInput"
              @update:search-input="highlightSearchInput = $event"
            />
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
              <div class="relative">
                <input
                  type="file"
                  ref="fileInputRef"
                  @change="handleFileSelect"
                  multiple
                  accept="image/*"
                  class="hidden"
                  id="image-upload"
                  :disabled="selectedFiles.length >= 5"
                />
                <label
                  for="image-upload"
                  class="flex cursor-pointer items-center justify-center rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 px-6 py-8 text-center transition-colors hover:border-brand-300 hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-900/50 dark:hover:border-brand-500"
                  :class="{ 'opacity-50 cursor-not-allowed': selectedFiles.length >= 5 }"
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
                      PNG, JPG, GIF, WEBP hingga 20MB (Maksimal 5 gambar)
                    </p>
                    <p v-if="selectedFiles.length >= 5" class="mt-1 text-xs text-red-500">
                      Maksimal 5 gambar sudah tercapai
                    </p>
                  </div>
                </label>
              </div>

              <!-- Image Preview Grid -->
              <div v-if="selectedFiles.length > 0 || existingImages.length > 0" class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5">
                <!-- Existing Images -->
                <div
                  v-for="(image, index) in existingImages"
                  :key="`existing-${index}`"
                  class="group relative aspect-square overflow-hidden rounded-lg border border-gray-200 bg-gray-100 dark:border-gray-700 dark:bg-gray-800"
                >
                  <img
                    :src="image.url"
                    :alt="image.name || `Image ${index + 1}`"
                    class="h-full w-full object-cover"
                  />
                  <button
                    type="button"
                    @click="removeExistingImage(index)"
                    class="absolute right-2 top-2 rounded-lg bg-red-500 p-1.5 text-white opacity-0 transition-opacity hover:bg-red-600 group-hover:opacity-100"
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

                <!-- New Uploaded Files -->
                <div
                  v-for="(file, index) in selectedFiles"
                  :key="`new-${index}`"
                  class="group relative aspect-square overflow-hidden rounded-lg border border-gray-200 bg-gray-100 dark:border-gray-700 dark:bg-gray-800"
                >
                  <img
                    :src="file.preview"
                    :alt="file.name"
                    class="h-full w-full object-cover"
                  />
                  <button
                    type="button"
                    @click="removeFile(index)"
                    class="absolute right-2 top-2 rounded-lg bg-red-500 p-1.5 text-white opacity-0 transition-opacity hover:bg-red-600 group-hover:opacity-100"
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
                  <div class="absolute bottom-0 left-0 right-0 bg-black/50 px-2 py-1">
                    <p class="truncate text-xs text-white">{{ file.name }}</p>
                    <p class="text-xs text-gray-300">{{ formatFileSize(file.size) }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
            <!-- Deskripsi -->
          <div class="lg:col-span-2">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Deskripsi <span class="text-red-500">*</span>
            </label>
            <div
              ref="quillEditorRef"
              class="min-h-[180px] rounded-lg border border-gray-300 dark:border-gray-700"
              :class="{ 'border-red-300 dark:border-red-500': errors.description }"
            ></div>
            <span v-if="errors.description" class="mt-1 block text-xs text-red-500">{{ errors.description }}</span>
            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
              Gunakan editor untuk memformat deskripsi program. Konten akan disanitasi untuk keamanan.
            </p>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-16 flex items-center gap-3 lg:justify-end">
          
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
import { ref, reactive, computed, onMounted, onBeforeUnmount, watch, nextTick } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import SearchableSelect from '@/components/forms/SearchableSelect.vue'
import { useToast } from 'vue-toastification'

const route = useRoute()
const router = useRouter()

const isEditMode = computed(() => route.params.id !== undefined && route.params.id !== 'new')
const currentPageTitle = computed(() => {
  return isEditMode.value ? 'Edit Landing Program' : 'Tambah Landing Program'
})
const toast = useToast()

// Options for select fields
const statusOptions = [
  { value: 'active', label: 'Aktif' },
  { value: 'inactive', label: 'Tidak Aktif' },
]

const highlightOptions = [
  { value: 'yes', label: 'Ya' },
  { value: 'no', label: 'Tidak' },
]

// Search input refs
const statusSearchInput = ref('')
const highlightSearchInput = ref('')

// File upload
const fileInputRef = ref<HTMLInputElement | null>(null)
const selectedFiles = ref<Array<File & { preview: string }>>([])
const existingImages = ref<Array<{ url: string; name?: string }>>([])

// Quill editor
const quillEditorRef = ref<HTMLElement | null>(null)
let quillInstance: any = null

// Form data
const formData = reactive({
  name: '',
  status: '',
  highlight: '',
  description: '',
})

// Errors
const errors = reactive({
  name: '',
  status: '',
  description: '',
})

// Initialize Quill Editor
const initQuill = async () => {
  if (!quillEditorRef.value) return

  try {
    // Load Quill from CDN if not already loaded
    if (!(window as any).Quill) {
      await new Promise<void>((resolve, reject) => {
        if (document.querySelector('script[src*="quill"]')) {
          const checkInterval = setInterval(() => {
            if ((window as any).Quill) {
              clearInterval(checkInterval)
              resolve()
            }
          }, 100)
          return
        }

        const script = document.createElement('script')
        script.src = 'https://cdn.quilljs.com/1.3.6/quill.js'
        script.onload = () => resolve()
        script.onerror = () => reject(new Error('Failed to load Quill'))
        document.head.appendChild(script)

        const link = document.createElement('link')
        link.rel = 'stylesheet'
        link.href = 'https://cdn.quilljs.com/1.3.6/quill.snow.css'
        document.head.appendChild(link)
      })
    }

    await nextTick()

    const Quill = (window as any).Quill
    quillInstance = new Quill(quillEditorRef.value, {
      theme: 'snow',
      modules: {
        toolbar: [
          [{ header: [1, 2, 3, false] }],
          ['bold', 'italic', 'underline', 'strike'],
          [{ list: 'ordered' }, { list: 'bullet' }],
          [{ align: [] }],
          ['link', 'image'],
          ['clean'],
        ],
      },
      placeholder: 'Masukkan deskripsi program...',
    })

    // Set initial content if editing
    if (formData.description) {
      quillInstance.root.innerHTML = formData.description
    }

    // Update formData when editor content changes
    quillInstance.on('text-change', () => {
      formData.description = quillInstance.root.innerHTML
    })
    
    // Trigger initial update
    formData.description = quillInstance.root.innerHTML
  } catch (error) {
    console.error('Error initializing Quill:', error)
  }
}

// Sanitize HTML to prevent XSS
// For production, consider using DOMPurify library (npm install dompurify)
const sanitizeHTML = (html: string): string => {
  if (!html) return ''
  
  try {
    // Quill already sanitizes HTML, but we can add additional safety
    // Strip script tags and event handlers
    const tempDiv = document.createElement('div')
    tempDiv.innerHTML = html
    
    // Remove script tags
    const scripts = tempDiv.querySelectorAll('script')
    scripts.forEach(script => script.remove())
    
    // Remove event handlers from attributes (onclick, onerror, etc)
    const allElements = tempDiv.querySelectorAll('*')
    allElements.forEach(el => {
      Array.from(el.attributes).forEach(attr => {
        if (attr.name.startsWith('on') || attr.name === 'javascript:') {
          el.removeAttribute(attr.name)
        }
      })
    })
    
    // Remove iframe tags (security risk)
    const iframes = tempDiv.querySelectorAll('iframe')
    iframes.forEach(iframe => iframe.remove())
    
    return tempDiv.innerHTML
  } catch (error) {
    console.error('Error sanitizing HTML:', error)
    // Fallback: strip all HTML tags
    const div = document.createElement('div')
    div.textContent = html
    return div.innerHTML
  }
}

// Handle name input with validation
const handleNameInput = () => {
  if (formData.name.length > 100) {
    formData.name = formData.name.substring(0, 100)
  }
  if (formData.name.trim()) {
    errors.name = ''
  }
}

// Handle file selection
const handleFileSelect = (event: Event) => {
  const target = event.target as HTMLInputElement
  if (target.files) {
    const files = Array.from(target.files)
    
    // Check max 5 images limit
    const remainingSlots = 5 - (selectedFiles.value.length + existingImages.value.length)
    if (files.length > remainingSlots) {
      toast.warning(`Maksimal 5 gambar. Anda sudah memiliki ${selectedFiles.value.length + existingImages.value.length} gambar. Hanya ${remainingSlots} gambar yang akan ditambahkan.`)
      files.splice(remainingSlots)
    }

    // Validate file size (max 20MB per file) and type
    const maxSize = 20 * 1024 * 1024 // 20MB in bytes
    const validFiles: (File & { preview: string })[] = []
    const invalidFiles: string[] = []
    
    files.forEach((file) => {
      // Check if file is an image
      if (!file.type.startsWith('image/')) {
        invalidFiles.push(`${file.name} (bukan file gambar)`)
        return
      }
      
      // Check file size
      if (file.size > maxSize) {
        invalidFiles.push(`${file.name} (melebihi 20MB)`)
        return
      }
      
      // Create preview URL
      const preview = URL.createObjectURL(file)
      validFiles.push(Object.assign(file, { preview }))
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
  // Revoke object URL to free memory
  URL.revokeObjectURL(selectedFiles.value[index].preview)
  selectedFiles.value.splice(index, 1)
}

// Remove existing image
const removeExistingImage = (index: number) => {
  existingImages.value.splice(index, 1)
}

// Format file size
const formatFileSize = (bytes: number): string => {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i]
}

// Validate form
const validateForm = (): boolean => {
  let isValid = true
  
  // Reset errors
  errors.name = ''
  errors.status = ''
  errors.description = ''

  // Validate name
  if (!formData.name || !formData.name.trim()) {
    errors.name = 'Nama program wajib diisi'
    isValid = false
  } else if (formData.name.length > 100) {
    errors.name = 'Nama program maksimal 100 karakter'
    isValid = false
  }

  // Validate status
  if (!formData.status) {
    errors.status = 'Status wajib dipilih'
    isValid = false
  }

  // Validate description
  let descriptionText = ''
  if (quillInstance) {
    descriptionText = quillInstance.getText().trim()
  } else {
    // Fallback: extract text from HTML
    const div = document.createElement('div')
    div.innerHTML = formData.description || ''
    descriptionText = div.textContent?.trim() || ''
  }
  
  if (!descriptionText) {
    errors.description = 'Deskripsi wajib diisi'
    isValid = false
  }

  return isValid
}

// Load data if edit mode
const loadData = async () => {
  if (isEditMode.value && route.params.id) {
    const id = route.params.id as string
    // TODO: Load data from API
    try {
      const res = await fetch(`/admin/api/landing-program/${id}`, {
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
      })
      const json = await res.json()
      if (json.success && json.data) {
        const data = json.data
        formData.name = data.name || ''
        formData.status = data.is_active ? 'active' : 'inactive'
        formData.highlight = data.is_highlight ? 'yes' : 'no'
        formData.description = data.description || ''
        if (data.image_url) {
          const url = data.image_url.startsWith('http') ? data.image_url : `/storage/${data.image_url}`
          existingImages.value = [{ url, name: data.name }]
        }
      }
    } catch (e) {
      // ignore load errors for now
      console.error('Failed to load landing program', e)
    }
    
    // Sample data for testing (uncomment to test)
    // formData.name = 'Program Beasiswa Pendidikan'
    // formData.status = 'active'
    // formData.highlight = 'yes'
    // formData.description = '<p>Deskripsi program beasiswa...</p>'
    // existingImages.value = [
    //   { url: '/path/to/image1.jpg', name: 'Image 1' },
    //   { url: '/path/to/image2.jpg', name: 'Image 2' },
    // ]
    
    // If data is loaded, update quill editor after it's initialized
    // This will be handled in onMounted after initQuill
  }
}

// Handle cancel
const handleCancel = () => {
  // Cleanup preview URLs
  selectedFiles.value.forEach((file) => {
    URL.revokeObjectURL(file.preview)
  })
  router.push('/company/landing-program')
}

// Handle save
const handleSave = async () => {
  if (!validateForm()) {
    return
  }

  try {
    // Get description from Quill editor and sanitize
    let descriptionHTML = formData.description
    if (quillInstance) {
      descriptionHTML = quillInstance.root.innerHTML
      formData.description = descriptionHTML
    }
    const sanitizedDescription = sanitizeHTML(descriptionHTML)
    
    // Prepare FormData for file upload
    const formDataToSend = new FormData()
    
    // Add form fields
    formDataToSend.append('name', formData.name.trim())
    formDataToSend.append('status', formData.status)
    // Map highlight selection to boolean field expected by backend
    if (formData.highlight) {
      const isHighlight = formData.highlight === 'yes' ? '1' : '0'
      formDataToSend.append('is_highlight', isHighlight)
    }
    formDataToSend.append('description', sanitizedDescription)
    
    // Add new files (send only the first selected file as 'image')
    if (selectedFiles.value.length > 0) {
      formDataToSend.append('image', selectedFiles.value[0])
    }

    // Add CSRF token (meta tag preferred, fallback to API) and include as form field
    const metaToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    let csrfToken = metaToken || ''
    if (!csrfToken) {
      // fallback: fetch from API
      try {
        const res = await fetch('/admin/api/csrf-token')
        const json = await res.json()
        csrfToken = json?.token || ''
      } catch (e) {
        // ignore
      }
    }
    if (csrfToken) {
      formDataToSend.append('_token', csrfToken)
    }

    // Add existing images to keep (you might want to track which ones to keep)
    // formDataToSend.append('existing_images', JSON.stringify(existingImages.value.map(img => img.url)))

    // Save to API
    try {
      const headers = {}
      if (csrfToken) headers['X-CSRF-TOKEN'] = csrfToken

      if (isEditMode.value) {
        // Use method override for PUT when sending multipart/form-data
        formDataToSend.append('_method', 'PUT')
        const res = await fetch(`/admin/api/landing-program/${route.params.id}`, {
          method: 'POST', // POST + _method=PUT for multipart
          headers,
          body: formDataToSend,
          credentials: 'same-origin',
        })
        const result = await res.json()
        if (result.success) {
          toast.success('Landing program berhasil diupdate')
        } else {
          toast.error(result.message || 'Validation failed')
          throw new Error(result.message || 'Validation failed')
        }
      } else {
        const res = await fetch('/admin/api/landing-program', {
          method: 'POST',
          headers,
          body: formDataToSend,
          credentials: 'same-origin',
        })
        const result = await res.json()
        if (result.success) {
          toast.success('Landing program berhasil dibuat')
        } else {
          toast.error(result.message || 'Validation failed')
          throw new Error(result.message || 'Validation failed')
        }
      }
    } catch (e) {
      console.error('Save error:', e)
      toast.error('Terjadi kesalahan saat menyimpan data: ' + (e.message || ''))
      return
    }
    
    // Redirect to list
    router.push('/company/landing-program')
  } catch (error) {
    console.error('Error saving:', error)
    toast.error('Terjadi kesalahan saat menyimpan data')
  }
}

// Action Buttons are placed at the end of the form template (rendered below)

onMounted(async () => {
  await loadData()
  await nextTick()
  await initQuill()
  
  // After Quill is initialized, set description if it exists
  await nextTick()
  if (quillInstance && formData.description) {
    quillInstance.root.innerHTML = formData.description
  }
})

onBeforeUnmount(() => {
  // Cleanup preview URLs
  selectedFiles.value.forEach((file) => {
    URL.revokeObjectURL(file.preview)
  })
  
  // Cleanup Quill instance
  if (quillInstance) {
    quillInstance = null
  }
})
</script>

<style>
/* Quill Editor Styles */
.ql-container {
  font-family: inherit;
  font-size: 14px;
}

.ql-editor {
  min-height: 180px;
  max-height: 420px;
  overflow-y: auto;
}

.ql-snow .ql-toolbar {
  border-top-left-radius: 0.5rem;
  border-top-right-radius: 0.5rem;
  border-bottom: 1px solid #e5e7eb;
}

.dark .ql-snow .ql-toolbar {
  background-color: #1f2937;
  border-color: #374151;
}

.dark .ql-snow .ql-stroke {
  stroke: #9ca3af;
}

.dark .ql-snow .ql-fill {
  fill: #9ca3af;
}

.dark .ql-snow .ql-picker-label {
  color: #9ca3af;
}

.ql-container {
  border-bottom-left-radius: 0.5rem;
  border-bottom-right-radius: 0.5rem;
}

.dark .ql-container {
  background-color: #111827;
  border-color: #374151;
  color: #f9fafb;
}

.dark .ql-editor.ql-blank::before {
  color: rgba(255, 255, 255, 0.3);
}
</style>

