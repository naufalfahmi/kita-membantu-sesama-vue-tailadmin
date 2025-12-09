<template>
  <AdminLayout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />

    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6">
      <h3 class="mb-5 text-lg font-semibold text-gray-800 dark:text-white/90 lg:mb-7">Ganti Password</h3>

      <div class="max-w-md">
        <div v-if="successMessage" class="mb-4 rounded-md bg-green-50 p-3 text-green-800">{{ successMessage }}</div>
        <div v-if="errorMessage" class="mb-4 rounded-md bg-red-50 p-3 text-red-800">{{ errorMessage }}</div>

        <form @submit.prevent="submit" class="space-y-4">
          <div class="relative">
            <label class="block text-sm font-medium text-gray-700">Password Saat Ini</label>
            <input :type="showCurrent ? 'text' : 'password'" v-model="form.current_password" class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
            <button type="button" @click="showCurrent = !showCurrent" class="absolute right-3 top-8 text-gray-500" :aria-label="showCurrent ? 'Hide password' : 'Show password'">
              <svg v-if="showCurrent" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-5.523 0-10-5-10-5s1.657-2.494 4.354-4.22M6.5 6.5L17.5 17.5M3 3l18 18" />
              </svg>
              <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
              </svg>
            </button>
            <p v-if="errors.current_password" class="text-sm text-red-600 mt-1">{{ errors.current_password }}</p>
          </div>

          <div class="relative">
            <label class="block text-sm font-medium text-gray-700">Password Baru</label>
            <input :type="showNew ? 'text' : 'password'" v-model="form.new_password" class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
            <button type="button" @click="showNew = !showNew" class="absolute right-3 top-8 text-gray-500" :aria-label="showNew ? 'Hide password' : 'Show password'">
              <svg v-if="showNew" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-5.523 0-10-5-10-5s1.657-2.494 4.354-4.22M6.5 6.5L17.5 17.5M3 3l18 18" />
              </svg>
              <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
              </svg>
            </button>
            <p v-if="errors.new_password" class="text-sm text-red-600 mt-1">{{ errors.new_password }}</p>
          </div>

          <div class="relative">
            <label class="block text-sm font-medium text-gray-700">Konfirmasi Password Baru</label>
            <input :type="showConfirm ? 'text' : 'password'" v-model="form.new_password_confirmation" class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" />
            <button type="button" @click="showConfirm = !showConfirm" class="absolute right-3 top-8 text-gray-500" :aria-label="showConfirm ? 'Hide password' : 'Show password'">
              <svg v-if="showConfirm" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-5.523 0-10-5-10-5s1.657-2.494 4.354-4.22M6.5 6.5L17.5 17.5M3 3l18 18" />
              </svg>
              <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
              </svg>
            </button>
          </div>

          <div class="flex items-center gap-3">
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-brand-500 text-white rounded-md">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'

const currentPageTitle = 'Account Settings'

const form = ref({ current_password: '', new_password: '', new_password_confirmation: '' })
const errors = ref<any>({})
const successMessage = ref('')
const errorMessage = ref('')

const getCsrf = async () => {
  const r = await fetch('/admin/api/csrf-token', { credentials: 'same-origin' })
  return (await r.json()).csrf_token
}

const submit = async () => {
  errors.value = {}
  successMessage.value = ''
  errorMessage.value = ''

  try {
    const csrf = await getCsrf()
    const res = await fetch('/admin/api/user/password', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrf,
      },
      credentials: 'same-origin',
      body: JSON.stringify(form.value),
    })

    const data = await res.json()
    if (res.ok && data.success) {
      successMessage.value = data.message || 'Password berhasil diubah.'
      form.value.current_password = ''
      form.value.new_password = ''
      form.value.new_password_confirmation = ''
    } else {
      errorMessage.value = data.message || 'Gagal mengubah password.'
      if (data.errors) {
        errors.value = Object.fromEntries(Object.entries(data.errors).map(([k, v]) => [k, Array.isArray(v) ? v[0] : v]))
      }
    }
  } catch (e) {
    errorMessage.value = 'Terjadi kesalahan. Coba lagi.'
    console.error(e)
  }
}

// show/hide toggles
const showCurrent = ref(false)
const showNew = ref(false)
const showConfirm = ref(false)
</script>

<style scoped></style>
