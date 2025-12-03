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
      </div>

      <form @submit.prevent="handleSubmit" class="flex flex-col">
        <div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2">
          <!-- Penerima Pesan -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Penerima Pesan <span class="text-red-500">*</span>
            </label>
            <SearchableSelect
              v-model="formData.receiver_id"
              :options="userList"
              placeholder="Pilih atau cari penerima pesan"
              :search-input="receiverSearchInput"
              @update:search-input="handleReceiverSearch"
            />
            <p v-if="errors.receiver_id" class="mt-1 text-xs text-red-500">
              {{ errors.receiver_id }}
            </p>
          </div>

          <!-- Subjek -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Subjek <span class="text-red-500">*</span>
            </label>
            <input
              type="text"
              v-model="formData.title"
              placeholder="Masukkan subjek pesan"
              maxlength="100"
              required
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
              :class="{ 'border-red-500': errors.title }"
              @input="clearError('title')"
            />
            <p v-if="errors.title" class="mt-1 text-xs text-red-500">
              {{ errors.title }}
            </p>
            <p v-else class="mt-1 text-xs text-gray-500 dark:text-gray-400">
              {{ formData.title.length }}/100 karakter
            </p>
          </div>

          <!-- Pesan -->
          <div class="lg:col-span-2">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Pesan <span class="text-red-500">*</span>
            </label>
            <textarea
              v-model="formData.message"
              placeholder="Masukkan isi pesan"
              maxlength="1000"
              rows="6"
              required
              class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
              :class="{ 'border-red-500': errors.message }"
              @input="clearError('message')"
            ></textarea>
            <p v-if="errors.message" class="mt-1 text-xs text-red-500">
              {{ errors.message }}
            </p>
            <p v-else class="mt-1 text-xs text-gray-500 dark:text-gray-400">
              {{ formData.message.length }}/1000 karakter
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
            :disabled="isSubmitting"
            class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 disabled:opacity-50 disabled:cursor-not-allowed sm:w-auto"
          >
            <span v-if="isSubmitting">Mengirim...</span>
            <span v-else>Kirim Pesan</span>
          </button>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { reactive, ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import SearchableSelect from '@/components/forms/SearchableSelect.vue'

const route = useRoute()
const router = useRouter()

const currentPageTitle = computed(() => (route.meta.title as string) || 'Form Pesan')

// User list - akan diisi dari API
const userList = ref<Array<{ value: string; label: string }>>([])
const receiverSearchInput = ref('')
const isSubmitting = ref(false)

// Form data
const formData = reactive({
  receiver_id: '',
  title: '',
  message: '',
})

// Error messages
const errors = reactive({
  receiver_id: '',
  title: '',
  message: '',
})

// Clear error for specific field
const clearError = (field: keyof typeof errors) => {
  errors[field] = ''
}

// Handle receiver search - bisa digunakan untuk API search
const handleReceiverSearch = (searchTerm: string) => {
  receiverSearchInput.value = searchTerm
  // TODO: Implement API search jika diperlukan
  // fetchUsers(searchTerm)
}

// Fetch users from API
const fetchUsers = async (searchQuery: string = '') => {
  try {
    // TODO: Ganti dengan endpoint API yang sesuai
    // Contoh: /admin/api/users?search=${searchQuery}
    const response = await fetch(`/admin/api/users${searchQuery ? `?search=${encodeURIComponent(searchQuery)}` : ''}`, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
      },
      credentials: 'same-origin',
    })

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`)
    }

    const data = await response.json()
    
    if (data.success && data.data) {
      // Format data sesuai dengan format SearchableSelect
      userList.value = data.data.map((user: any) => ({
        value: String(user.id),
        label: user.name || `${user.first_name} ${user.last_name}` || user.email || `User #${user.id}`,
      }))
    }
  } catch (error) {
    console.error('Error fetching users:', error)
    // Fallback ke sample data jika API error
    if (userList.value.length === 0) {
      userList.value = [
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
    }
  }
}

// Validation function
const validateForm = (): boolean => {
  let isValid = true

  // Reset errors
  errors.receiver_id = ''
  errors.title = ''
  errors.message = ''

  // Validate Penerima Pesan
  if (!formData.receiver_id) {
    errors.receiver_id = 'Penerima pesan wajib diisi'
    isValid = false
  }

  // Validate Subjek
  if (!formData.title || formData.title.trim() === '') {
    errors.title = 'Subjek wajib diisi'
    isValid = false
  } else if (formData.title.length > 100) {
    errors.title = 'Subjek tidak boleh lebih dari 100 karakter'
    isValid = false
  }

  // Validate Pesan
  if (!formData.message || formData.message.trim() === '') {
    errors.message = 'Pesan wajib diisi'
    isValid = false
  } else if (formData.message.length > 1000) {
    errors.message = 'Pesan tidak boleh lebih dari 1000 karakter'
    isValid = false
  }

  return isValid
}

// Handle cancel
const handleCancel = () => {
  // Reset form
  formData.receiver_id = ''
  formData.title = ''
  formData.message = ''
  Object.keys(errors).forEach((key) => {
    errors[key as keyof typeof errors] = ''
  })
  // Bisa redirect ke halaman lain jika diperlukan
  // router.push('/administrasi')
}

// Handle submit
const handleSubmit = async () => {
  // Validate form
  if (!validateForm()) {
    return
  }

  isSubmitting.value = true

  try {
    // Prepare data for API
    const payload = {
      receiver_id: formData.receiver_id,
      title: formData.title.trim(),
      message: formData.message.trim(),
    }

    // TODO: Ganti dengan endpoint API yang sesuai
    // Contoh: POST /admin/api/messages
    const response = await fetch('/admin/api/messages', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
      },
      credentials: 'same-origin',
      body: JSON.stringify(payload),
    })

    if (!response.ok) {
      const errorData = await response.json()
      throw new Error(errorData.message || 'Gagal mengirim pesan')
    }

    const data = await response.json()
    
    if (data.success) {
      alert('Pesan berhasil dikirim')
      // Reset form setelah berhasil
      handleCancel()
      // Bisa redirect ke inbox atau halaman lain jika diperlukan
      // router.push('/administrasi/inbox')
    } else {
      throw new Error(data.message || 'Gagal mengirim pesan')
    }
  } catch (error) {
    console.error('Error sending message:', error)
    alert(error instanceof Error ? error.message : 'Terjadi kesalahan saat mengirim pesan')
  } finally {
    isSubmitting.value = false
  }
}

onMounted(() => {
  // Load users saat component mounted
  fetchUsers()
})
</script>



