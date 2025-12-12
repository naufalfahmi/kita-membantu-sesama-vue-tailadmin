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
          v-if="canCreate"
          @click="handleAdd"
          class="flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600"
        >
          <svg
            class="fill-current"
            width="20"
            height="20"
            viewBox="0 0 20 20"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              fill-rule="evenodd"
              clip-rule="evenodd"
              d="M10 3.33333C10.4602 3.33333 10.8333 3.70643 10.8333 4.16667V9.16667H15.8333C16.2936 9.16667 16.6667 9.53976 16.6667 10C16.6667 10.4602 16.2936 10.8333 15.8333 10.8333H10.8333V15.8333C10.8333 16.2936 10.4602 16.6667 10 16.6667C9.53976 16.6667 9.16667 16.2936 9.16667 15.8333V10.8333H4.16667C3.70643 10.8333 3.33333 10.4602 3.33333 10C3.33333 9.53976 3.70643 9.16667 4.16667 9.16667H9.16667V4.16667C9.16667 3.70643 9.53976 3.33333 10 3.33333Z"
              fill="currentColor"
            />
          </svg>
          Tambah Karyawan
        </button>
      </div>

      <!-- Filter Section -->
      <div class="mb-6 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="flex gap-4">
          <div class="flex-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Nama
            </label>
            <input
              type="text"
              v-model="filterNama"
              placeholder="Cari nama karyawan..."
        const { fetchUser, hasPermission } = useAuth()
        const canCreate = computed(() => hasPermission('create karyawan'))
        const canUpdate = computed(() => hasPermission('update karyawan'))
        const canDelete = computed(() => hasPermission('delete karyawan'))
            />
          </div>
          <div class="flex items-end">
            <button
              @click="resetFilter"
              class="h-11 rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]"
            >
              Reset Filter
            </button>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="flex items-center justify-center py-12">
        <div class="h-8 w-8 animate-spin rounded-full border-4 border-brand-500 border-t-transparent"></div>
        <span class="ml-3 text-gray-600 dark:text-gray-400">Memuat data...</span>
      </div>

      <div v-else class="ag-theme-alpine dark:ag-theme-alpine-dark" style="width: 100%;">
        <ag-grid-vue
          class="ag-theme-alpine"
          style="width: 100%;"
          :columnDefs="columnDefs"
          :rowData="rowData"
          :defaultColDef="defaultColDef"
          :pagination="true"
          :paginationPageSize="20"
          theme="legacy"
          :animateRows="true"
          :suppressHorizontalScroll="true"
          :domLayout="'autoHeight'"
        />
      </div>
    </div>

    <ConfirmModal
      :isOpen="showDeleteModal"
      title="Hapus Karyawan"
      message="Apakah Anda yakin ingin menghapus karyawan ini? Tindakan ini tidak dapat dibatalkan."
      confirmText="Hapus"
      confirmButtonClass="bg-red-500 hover:bg-red-600"
      @confirm="confirmDelete"
      @cancel="cancelDelete"
    />
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useAuth } from '@/composables/useAuth'
import { useRoute, useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'
import { AgGridVue } from 'ag-grid-vue3'
import 'ag-grid-community/styles/ag-grid.css'
import 'ag-grid-community/styles/ag-theme-alpine.css'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import ConfirmModal from '@/components/common/ConfirmModal.vue'

interface RelOption {
  id: string | number
  name?: string
  nama?: string
}

interface KaryawanRow {
  id: number
  name: string
  email: string
  no_induk: string | null
  posisi: string | null
  no_handphone: string | null
  tanggal_masuk: string | null
  is_active: boolean
  role?: RelOption | null
  pangkat?: RelOption | null
  tipe_absensi?: RelOption | null
  kantor_cabang?: RelOption | null
}

const route = useRoute()
const router = useRouter()
const toast = useToast()
const { fetchUser, hasPermission } = useAuth()
const canCreate = computed(() => hasPermission('create karyawan'))

const currentPageTitle = computed(() => (route.meta.title as string) || 'Karyawan')

const loading = ref(false)
const rowData = ref<KaryawanRow[]>([])
const filterNama = ref('')
              if (canUpdate.value) editBtn.addEventListener('click', () => handleEdit(params.data.id))
const deleteId = ref<number | null>(null)
let debounceTimer: ReturnType<typeof setTimeout> | undefined

const columnDefs = [
  {
    headerName: 'Nama',
    field: 'name',
    sortable: true,
    flex: 1,
  },
  {
    headerName: 'No Induk',
              if (canDelete.value) deleteBtn.addEventListener('click', () => handleDelete(params.data.id))
              if (canUpdate.value) container.appendChild(editBtn)
              if (canDelete.value) container.appendChild(deleteBtn)
    sortable: true,
    width: 140,
    valueFormatter: (params: any) => params.value || '-',
  },
  {
    headerName: 'Pangkat',
    valueGetter: (params: any) => params.data?.pangkat?.nama || '-',
    sortable: true,
    flex: 1,
  },
  {
    headerName: 'Jabatan',
    valueGetter: (params: any) => params.data?.role?.name || '-',
    flex: 1,
    sortable: true,
  },
  {
    headerName: 'Kantor Cabang',
    valueGetter: (params: any) => params.data?.kantor_cabang?.nama || '-',
    flex: 1,
    sortable: true,
  },
  {
    headerName: 'Tanggal Masuk',
    field: 'tanggal_masuk',
    sortable: true,
    flex: 1,
    valueFormatter: (params: any) => {
      if (params.value) {
        return new Date(params.value).toLocaleDateString('id-ID', {
          year: 'numeric',
          month: 'long',
          day: 'numeric',
        })
      }
      return '-'
    },
  },
  {
    headerName: 'Status',
    field: 'is_active',
    sortable: true,
    width: 120,
    cellRenderer: (params: any) => {
      const span = document.createElement('span')
      if (params.value) {
        span.className = 'inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900/30 dark:text-green-400'
        span.textContent = 'Aktif'
      } else {
        span.className = 'inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900/30 dark:text-red-400'
        span.textContent = 'Nonaktif'
      }
      return span
    },
  },
  {
    headerName: 'Actions',
    field: 'actions',
    sortable: false,
    filter: false,
    width: 120,
    cellRenderer: (params: any) => {
      const container = document.createElement('div')
      container.className = 'flex items-center gap-3'

      const editBtn = document.createElement('button')
      editBtn.className = 'flex items-center justify-center w-8 h-8 rounded-lg text-brand-500 hover:bg-brand-50 dark:hover:bg-brand-500/10 transition-colors'
      editBtn.innerHTML = `
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
        </svg>
      `
      editBtn.addEventListener('click', () => handleEdit(params.data.id))

      const deleteBtn = document.createElement('button')
      deleteBtn.className = 'flex items-center justify-center w-8 h-8 rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors'
      deleteBtn.innerHTML = `
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M3 6h18"></path>
          <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
          <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
          <line x1="10" y1="11" x2="10" y2="17"></line>
          <line x1="14" y1="11" x2="14" y2="17"></line>
        </svg>
      `
      deleteBtn.addEventListener('click', () => handleDelete(params.data.id))

      container.appendChild(editBtn)
      container.appendChild(deleteBtn)

      return container
    },
  },
]

const defaultColDef = {
  resizable: true,
  sortable: true,
  filter: true,
}

const fetchData = async () => {
  loading.value = true
  try {
    const params = new URLSearchParams({ per_page: '1000' })
    if (filterNama.value.trim()) {
      params.set('search', filterNama.value.trim())
    }

    const response = await fetch(`/admin/api/karyawan?${params.toString()}`, {
      credentials: 'same-origin',
    })
    const json = await response.json()

    if (json.success) {
      rowData.value = json.data ?? []
    } else {
      toast.error(json.message || 'Gagal memuat data karyawan')
    }
  } catch (error) {
    toast.error('Gagal memuat data karyawan')
  } finally {
    loading.value = false
  }
}

const debouncedFetch = () => {
  if (debounceTimer) {
    clearTimeout(debounceTimer)
  }
  debounceTimer = setTimeout(() => {
    fetchData()
  }, 300)
}

const handleAdd = () => {
  router.push('/user-kepegawaian/karyawan/new')
}

const handleEdit = (id: number) => {
  router.push(`/user-kepegawaian/karyawan/${id}/edit`)
}

const handleDelete = (id: number) => {
  deleteId.value = id
  showDeleteModal.value = true
}

const confirmDelete = async () => {
  if (!deleteId.value) {
    return
  }

  try {
    const tokenResponse = await fetch('/admin/api/csrf-token', { credentials: 'same-origin' })
    const tokenJson = await tokenResponse.json()

    const response = await fetch(`/admin/api/karyawan/${deleteId.value}`, {
      method: 'DELETE',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': tokenJson.csrf_token,
      },
      credentials: 'same-origin',
    })

    const json = await response.json()
    if (json.success) {
      toast.success(json.message || 'Karyawan berhasil dihapus')
      await fetchData()
    } else {
      toast.error(json.message || 'Gagal menghapus karyawan')
    }
  } catch (error) {
    toast.error('Gagal menghapus karyawan')
  } finally {
    showDeleteModal.value = false
    deleteId.value = null
  }
}

const cancelDelete = () => {
  showDeleteModal.value = false
  deleteId.value = null
}

const resetFilter = () => {
  filterNama.value = ''
  fetchData()
}

onMounted(() => {
  fetchUser()
  fetchData()
})
</script>

<style>
.ag-theme-alpine {
  --ag-header-background-color: #f9fafb;
  --ag-header-foreground-color: #374151;
  --ag-border-color: #e5e7eb;
  --ag-row-hover-color: #f3f4f6;
}

.dark .ag-theme-alpine {
  --ag-header-background-color: #1f2937;
  --ag-header-foreground-color: #f9fafb;
  --ag-border-color: #374151;
  --ag-row-hover-color: #374151;
  --ag-background-color: #111827;
  --ag-odd-row-background-color: #1f2937;
  --ag-row-background-color: #111827;
  --ag-foreground-color: #f9fafb;
}
</style>

