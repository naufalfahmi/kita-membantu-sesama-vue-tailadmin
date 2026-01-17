<template>
  <AdminLayout>
    <div class="min-h-screen rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12">
      <div class="mb-6 flex items-center justify-between">
        <h3 class="font-semibold text-gray-800 text-theme-xl dark:text-white/90 sm:text-2xl">{{ currentPageTitle }}</h3>
        <button @click="handleCancel" class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">Batal</button>
      </div>

      <form @submit.prevent="handleSave" class="flex flex-col">
        <div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2">
          <div class="lg:col-span-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Nama Tipe <span class="text-red-500">*</span></label>
            <input type="text" v-model="formData.name" placeholder="Masukkan nama tipe" required class="h-11 w-full rounded-lg border bg-transparent px-4 py-2.5 text-sm text-gray-800" :class="formErrors.name ? 'border-red-500' : 'border-gray-300'" />
            <p v-if="formErrors.name" class="mt-1 text-xs text-red-500">{{ formErrors.name }}</p>
          </div>

          <div class="lg:col-span-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Order</label>
            <input type="number" v-model.number="formData.orders" min="0" class="h-11 w-full rounded-lg border bg-transparent px-4 py-2.5 text-sm text-gray-800" />
          </div>
        </div>

        <div class="flex items-center gap-3 mt-6 lg:justify-end">
          <button @click="handleCancel" type="button" class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 sm:w-auto">Batal</button>
          <button type="submit" class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto">{{ isEditMode ? 'Simpan Perubahan' : 'Simpan' }}</button>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { reactive, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import { useToast } from 'vue-toastification'

const route = useRoute()
const router = useRouter()
const toast = useToast()

const isEditMode = computed(() => route.params.id !== undefined && route.params.id !== 'new')
const currentPageTitle = computed(() => (isEditMode.value ? 'Edit Tipe Program' : 'Tambah Tipe Program'))

const formData = reactive({ name: '', orders: 0 })
const formErrors = reactive({ name: '' })

const getCsrfToken = async (): Promise<string> => {
  const metaToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
  if (metaToken) return metaToken
  try {
    const response = await fetch('/admin/api/csrf-token', { method: 'GET', credentials: 'same-origin' })
    const data = await response.json()
    return data.csrf_token || ''
  } catch (e) { console.error(e); return '' }
}

const loadData = async () => {
  if (!isEditMode.value || !route.params.id) return
  try {
    const csrf = await getCsrfToken()
    const res = await fetch(`/admin/api/tipe-program/${route.params.id}`, { method: 'GET', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' })
    const j = await res.json()
    if (j.success && j.data) { formData.name = j.data.name || ''; formData.orders = j.data.orders ?? 0 } else { toast.error('Gagal memuat data tipe program'); router.push('/administrasi/program/tipe-program') }
  } catch (e) { console.error(e); toast.error('Terjadi kesalahan saat memuat data'); router.push('/administrasi/program/tipe-program') }
}

const handleCancel = () => router.push('/administrasi/program/tipe-program')

const handleSave = async () => {
  if (!formData.name.trim()) { toast.error('Nama tipe wajib diisi'); return }
  formErrors.name = ''
  const csrf = await getCsrfToken()
  if (!csrf) { toast.error('Gagal mendapatkan CSRF token. Silakan refresh.'); return }
  const payload = { name: formData.name.trim(), orders: formData.orders }
  const url = isEditMode.value ? `/admin/api/tipe-program/${route.params.id}` : '/admin/api/tipe-program'
  const method = isEditMode.value ? 'PUT' : 'POST'
  try {
    const res = await fetch(url, { method, headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin', body: JSON.stringify(payload) })
    const j = await res.json()
    if (j.success) { toast.success(isEditMode.value ? 'Tipe program berhasil diupdate' : 'Tipe program berhasil ditambahkan'); router.push('/administrasi/program/tipe-program') }
    else {
      if (j.errors) {
        if (j.errors.name) formErrors.name = j.errors.name[0]
        const err = Object.values(j.errors).flat().join(', ')
        toast.error(`Validasi gagal: ${err}`)
      } else {
        toast.error(j.message || 'Gagal menyimpan data')
      }
    }
  } catch (e) { console.error(e); toast.error('Terjadi kesalahan saat menyimpan data') }
}

onMounted(() => loadData())
</script>
