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
          Batal
        </button>
      </div>

      <form @submit.prevent="handleSave" class="flex flex-col">
        <div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2">
          <!-- Kode Tipe Absensi -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Kode Tipe Absensi
            </label>
            <input
              type="text"
              v-model="formData.kode"
              placeholder="Kode akan di-generate otomatis"
              :readonly="!isEditMode"
              :disabled="!isEditMode"
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
              :class="{ 'cursor-not-allowed opacity-60': !isEditMode }"
            />
            <p v-if="!isEditMode" class="mt-1 text-xs text-gray-500 dark:text-gray-400">
              Kode akan di-generate otomatis
            </p>
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
              placeholder="Masukkan nama tipe absensi"
              required
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
          </div>

          <!-- Jam Masuk -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Jam Masuk
            </label>
            <input
              type="time"
              v-model="formData.jamMasuk"
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
          </div>

          <!-- Jam Keluar -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Jam Keluar
            </label>
            <input
              type="time"
              v-model="formData.jamKeluar"
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
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
import { reactive, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import { useToast } from 'vue-toastification'

const route = useRoute()
const router = useRouter()
const toast = useToast()

const isEditMode = computed(() => route.params.id !== undefined && route.params.id !== 'new')
const currentPageTitle = computed(() => {
  return isEditMode.value ? 'Edit Tipe Absensi' : 'Tambah Tipe Absensi'
})

// Form data
const formData = reactive({
  kode: '',
  nama: '',
  jamMasuk: '',
  jamKeluar: '',
})

// Get CSRF token
const getCsrfToken = async (): Promise<string> => {
  try {
    // Try to get from meta tag first
    const metaToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    if (metaToken) {
      return metaToken
    }
    
    // If not available, fetch from API
    const response = await fetch('/admin/api/csrf-token', {
      method: 'GET',
      credentials: 'same-origin',
    })
    
    if (!response.ok) {
      throw new Error(`Failed to get CSRF token: ${response.status}`)
    }
    
    const data = await response.json()
    return data.csrf_token || ''
  } catch (e) {
    console.error('Failed to get CSRF token:', e)
    return ''
  }
}

// Load next kode for new tipe absensi
const loadNextKode = async () => {
  if (!isEditMode.value) {
    try {
      const csrfToken = await getCsrfToken()
      const response = await fetch('/admin/api/tipe-absensi-next-kode', {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
        },
        credentials: 'same-origin',
      })
      
      const result = await response.json()
      if (result.success && result.data) {
        formData.kode = result.data.kode || ''
      }
    } catch (error) {
      console.error('Error loading next kode:', error)
    }
  }
}

// Load data if edit mode
const loadData = async () => {
  if (isEditMode.value && route.params.id) {
    const id = route.params.id as string
    try {
      const csrfToken = await getCsrfToken()
      const response = await fetch(`/admin/api/tipe-absensi/${id}`, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
        },
        credentials: 'same-origin',
      })
      
      const result = await response.json()
      if (result.success && result.data) {
        const data = result.data
        formData.kode = data.kode || ''
        formData.nama = data.nama || ''
        // Format time from database (HH:mm:ss) to input format (HH:mm)
        if (data.jam_masuk) {
          formData.jamMasuk = data.jam_masuk.substring(0, 5)
        }
        if (data.jam_keluar) {
          formData.jamKeluar = data.jam_keluar.substring(0, 5)
        }
      } else {
        toast.error('Gagal memuat data tipe absensi')
        router.push('/administrasi/tipe-absensi')
      }
    } catch (error) {
      console.error('Error loading data:', error)
      toast.error('Terjadi kesalahan saat memuat data')
      router.push('/administrasi/tipe-absensi')
    }
  }
}

// Handle cancel
const handleCancel = () => {
  router.push('/administrasi/tipe-absensi')
}

// Handle save
const handleSave = async () => {
  if (!formData.nama) {
    toast.error('Nama wajib diisi')
    return
  }

  try {
    // Get CSRF token
    const csrfToken = await getCsrfToken()
    if (!csrfToken) {
      toast.error('Gagal mendapatkan CSRF token. Silakan refresh halaman.')
      return
    }

    const payload: any = {
      nama: formData.nama,
      jam_masuk: formData.jamMasuk || null,
      jam_keluar: formData.jamKeluar || null,
    }

    // Only include kode in edit mode, let backend generate in create mode
    if (isEditMode.value && formData.kode) {
      payload.kode = formData.kode
    }

    const url = isEditMode.value 
      ? `/admin/api/tipe-absensi/${route.params.id}`
      : '/admin/api/tipe-absensi'
    
    const method = isEditMode.value ? 'PUT' : 'POST'

    const response = await fetch(url, {
      method: method,
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
      },
      credentials: 'same-origin',
      body: JSON.stringify(payload),
    })

    const result = await response.json()
    
    if (result.success) {
      toast.success(isEditMode.value ? 'Tipe absensi berhasil diupdate' : 'Tipe absensi berhasil ditambahkan')
      router.push('/administrasi/tipe-absensi')
    } else {
      if (result.errors) {
        const errorMessages = Object.values(result.errors).flat().join(', ')
        toast.error(`Validasi gagal: ${errorMessages}`)
      } else {
        toast.error(result.message || 'Gagal menyimpan data')
      }
    }
  } catch (error) {
    console.error('Error saving:', error)
    toast.error('Terjadi kesalahan saat menyimpan data')
  }
}

onMounted(async () => {
  if (!isEditMode.value) {
    await loadNextKode()
  } else {
    await loadData()
  }
})
</script>
