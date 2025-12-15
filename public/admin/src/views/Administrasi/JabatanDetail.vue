<template>
  <AdminLayout>
    <div class="min-h-screen rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12">
      <div class="mb-6 flex items-center justify-between">
        <h3 class="font-semibold text-gray-800 text-theme-xl dark:text-white/90 sm:text-2xl">{{ currentPageTitle }}</h3>
        <div class="flex items-center gap-3">
          <button @click="handleBack" class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
            <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M8.33333 15L3.33333 10M3.33333 10L8.33333 5M3.33333 10H16.6667" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            Kembali
          </button>
          <button v-if="canEdit" @click="handleEdit" class="flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
            Edit
          </button>
        </div>
      </div>

      <div v-if="loading" class="flex items-center justify-center py-20">
        <div class="flex flex-col items-center gap-4">
          <div class="h-12 w-12 animate-spin rounded-full border-4 border-brand-500 border-t-transparent"></div>
          <p class="text-sm text-gray-600 dark:text-gray-400">Memuat data jabatan...</p>
        </div>
      </div>

      <div v-else-if="role" class="space-y-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
          <h4 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white/90">Informasi Jabatan</h4>
          <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div>
              <p class="mb-2 text-xs font-medium text-gray-500 dark:text-gray-400">Nama</p>
              <p class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ role.name }}</p>
            </div>
            <div>
              <p class="mb-2 text-xs font-medium text-gray-500 dark:text-gray-400">Tanggal Dibuat</p>
              <p class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ formatDate(role.created_at) }}</p>
            </div>
            <div>
              <p class="mb-2 text-xs font-medium text-gray-500 dark:text-gray-400">Total Permissions</p>
              <p class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ role.permissions.length }}</p>
            </div>
          </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
          <h4 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white/90">Hak Akses</h4>
          <div class="space-y-4">
            <div v-for="(actions, module) in groupedPermissions" :key="module" class="rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
              <div class="bg-gray-100 dark:bg-gray-800/50 px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                  <h5 class="font-semibold text-gray-800 dark:text-white/90">{{ module }}</h5>
                </div>
              </div>
              <div class="px-4 py-3">
                <div class="grid grid-cols-1 gap-2 sm:grid-cols-3">
                  <div v-for="(permName, action) in actions" :key="action" class="flex items-center gap-3">
                    <span :class="roleHasPermission(permName) ? 'text-brand-600' : 'text-gray-400'" class="inline-flex items-center gap-2">
                      <svg v-if="roleHasPermission(permName)" width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M7 11l3 3 7-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                      </svg>
                      <svg v-else width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="10" cy="10" r="8" stroke="currentColor" stroke-width="1.2" opacity="0.25"/>
                      </svg>
                      <span class="text-sm">{{ capitalize(action) }}</span>
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-else class="flex flex-col items-center justify-center py-20">
        <svg class="mb-4 h-16 w-16 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 20h.01"/>
        </svg>
        <p class="text-sm text-gray-500 dark:text-gray-400">Jabatan tidak ditemukan.</p>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import { useToast } from 'vue-toastification'
import { useAuth } from '@/composables/useAuth'

const route = useRoute()
const router = useRouter()
const toast = useToast()
const { isAdmin, hasPermission } = useAuth()

const currentPageTitle = ref('Detail Jabatan')
const loading = ref(true)
const role = ref<any | null>(null)
const groupedPermissions = ref<Record<string, Record<string, string>>>({})

const canEdit = computed(() => isAdmin() || hasPermission('update jabatan'))

const id = String(route.params.id || '')

const fetchRole = async () => {
  loading.value = true
  try {
    const [rRes, pRes] = await Promise.all([
      fetch(`/admin/api/jabatan/${id}`, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' }),
      fetch('/admin/api/jabatan-permissions', { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' }),
    ])

    if (!rRes.ok) throw new Error('Failed to fetch')
    if (!pRes.ok) throw new Error('Failed to fetch permissions')

    const rData = await rRes.json()
    const pData = await pRes.json()

    if (rData.success) role.value = rData.data
    if (pData.success) groupedPermissions.value = pData.data
  } catch (err) {
    console.error('Error loading jabatan detail', err)
    toast.error('Gagal memuat detail jabatan')
  } finally {
    loading.value = false
  }
}

const handleBack = () => {
  router.push('/administrasi/jabatan')
}

const handleEdit = () => {
  router.push(`/administrasi/jabatan/${id}/edit`)
}

const roleHasPermission = (permissionName: string) => {
  return role.value && Array.isArray(role.value.permissions) && role.value.permissions.includes(permissionName)
}

const capitalize = (s: string) => s.charAt(0).toUpperCase() + s.slice(1)

const formatDate = (d: string | null) => {
  if (!d) return '-' 
  return new Date(d).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' })
}

onMounted(() => {
  fetchRole()
})
</script>

<style scoped>
.permission-grid {
  display: grid;
}
</style>
