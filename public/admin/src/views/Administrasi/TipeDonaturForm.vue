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
          <!-- Tipe Donatur -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Tipe Donatur <span class="text-red-500">*</span>
            </label>
            <input
              type="text"
              v-model="formData.nama"
              placeholder="Masukkan tipe donatur"
              required
              class="dark:bg-dark-900 h-11 w-full rounded-lg border bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
              :class="formErrors.nama ? 'border-red-500 focus:border-red-500 dark:border-red-500 dark:focus:border-red-500' : 'border-gray-300 focus:border-brand-300 dark:border-gray-700 dark:focus:border-brand-800'"
            />
            <p v-if="formErrors.nama" class="mt-1 text-xs text-red-500">{{ formErrors.nama }}</p>
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
import { reactive, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import { useToast } from 'vue-toastification'

const route = useRoute()
const router = useRouter()
const toast = useToast()

const isEditMode = computed(() => route.params.id !== undefined && route.params.id !== 'new')
const currentPageTitle = computed(() => (isEditMode.value ? 'Edit Tipe Donatur' : 'Tambah Tipe Donatur'))

const formData = reactive({
  nama: '',
})

const formErrors = reactive({
  nama: '',
})

const getCsrfToken = async (): Promise<string> => {
  const metaToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
  if (metaToken) {
    return metaToken
  }

  try {
    const response = await fetch('/admin/api/csrf-token', {
      method: 'GET',
      credentials: 'same-origin',
    })
    if (!response.ok) {
      throw new Error(`Failed to fetch CSRF token: ${response.status}`)
    }
    const data = await response.json()
    return data.csrf_token || ''
  } catch (error) {
    console.error('Error fetching CSRF token:', error)
    return ''
  }
}

const loadData = async () => {
  if (!isEditMode.value || !route.params.id) {
    return
  }

  try {
    const csrfToken = await getCsrfToken()
    const response = await fetch(`/admin/api/tipe-donatur/${route.params.id}`, {
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
      formData.nama = result.data.nama || ''
    } else {
      toast.error('Gagal memuat data tipe donatur')
      router.push('/administrasi/tipe-donatur')
    }
  } catch (error) {
    console.error('Error loading tipe donatur:', error)
    toast.error('Terjadi kesalahan saat memuat data')
    router.push('/administrasi/tipe-donatur')
  }
}

const handleCancel = () => {
  router.push('/administrasi/tipe-donatur')
}

const handleSave = async () => {
  if (!formData.nama.trim()) {
    toast.error('Tipe Donatur wajib diisi')
    return
  }

  formErrors.nama = ''

  const csrfToken = await getCsrfToken()
  if (!csrfToken) {
    toast.error('Gagal mendapatkan CSRF token. Silakan refresh halaman.')
    return
  }

  const payload = {
    nama: formData.nama.trim(),
  }

  const url = isEditMode.value
    ? `/admin/api/tipe-donatur/${route.params.id}`
    : '/admin/api/tipe-donatur'
  const method = isEditMode.value ? 'PUT' : 'POST'

  try {
    const response = await fetch(url, {
      method,
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
      formErrors.nama = ''
      toast.success(isEditMode.value ? 'Tipe donatur berhasil diupdate' : 'Tipe donatur berhasil ditambahkan')
      router.push('/administrasi/tipe-donatur')
    } else {
      if (result.errors) {
        if (result.errors.nama) {
          formErrors.nama = result.errors.nama[0]
        }
        const errorMessages = Object.values(result.errors).flat().join(', ')
        toast.error(`Validasi gagal: ${errorMessages}`)
      } else {
        toast.error(result.message || 'Gagal menyimpan data tipe donatur')
      }
    }
  } catch (error) {
    console.error('Error saving tipe donatur:', error)
    toast.error('Terjadi kesalahan saat menyimpan data')
  }
}

onMounted(() => {
  loadData()
})

watch(
  () => formData.nama,
  () => {
    formErrors.nama = ''
  }
)
</script>



