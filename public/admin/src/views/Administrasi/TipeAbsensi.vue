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
          Tambah Tipe Absensi
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
              placeholder="Cari nama tipe absensi..."
              class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
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

      <div class="ag-theme-alpine dark:ag-theme-alpine-dark" style="width: 100%;">
        <ag-grid-vue
          class="ag-theme-alpine"
          style="width: 100%;"
          :columnDefs="columnDefs"
          :rowData="gridRowData"
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

    <!-- Confirm Delete Modal -->
    <ConfirmModal
      :isOpen="showDeleteModal"
      title="Hapus Tipe Absensi"
      message="Apakah Anda yakin ingin menghapus tipe absensi ini? Tindakan ini tidak dapat dibatalkan."
      confirmText="Hapus"
      confirmButtonClass="bg-red-500 hover:bg-red-600"
      @confirm="confirmDelete"
      @cancel="cancelDelete"
    />
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuth } from '@/composables/useAuth'
import { AgGridVue } from 'ag-grid-vue3'
import 'ag-grid-community/styles/ag-grid.css'
import 'ag-grid-community/styles/ag-theme-alpine.css'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import ConfirmModal from '@/components/common/ConfirmModal.vue'
import { useToast } from 'vue-toastification'

const route = useRoute()
const router = useRouter()
const toast = useToast()
const { fetchUser, hasPermission, isAdmin } = useAuth()
const currentPageTitle = computed(() => (route.meta.title as string) || 'Tipe Absensi')
const canCreate = computed(() => isAdmin() || hasPermission('create tipe absensi'))
const canUpdate = computed(() => isAdmin() || hasPermission('update tipe absensi'))
const canDelete = computed(() => isAdmin() || hasPermission('delete tipe absensi'))
const canView = computed(() => isAdmin() || hasPermission('view tipe absensi'))
const loading = ref(false)

// Delete modal state
const showDeleteModal = ref(false)
const deleteId = ref<string | null>(null)

// Column definitions
const columnDefs = [
  {
    headerName: 'Kode',
    field: 'kode',
    sortable: true,
    width: 120,
  },
  {
    headerName: 'Nama',
    field: 'nama',
    sortable: true,
    flex: 1,
  },
  {
    headerName: 'Jam Masuk',
    field: 'jam_masuk',
    sortable: true,
    width: 150,
    valueFormatter: (params: any) => {
      if (params.value) {
        return params.value.substring(0, 5) // Format HH:mm
      }
      return '-'
    },
  },
  {
    headerName: 'Jam Keluar',
    field: 'jam_keluar',
    sortable: true,
    width: 150,
    valueFormatter: (params: any) => {
      if (params.value) {
        return params.value.substring(0, 5) // Format HH:mm
      }
      return '-'
    },
  },
  {
    headerName: 'Tanggal Dibuat',
    field: 'created_at',
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
      return ''
    },
  },
  {
    headerName: 'Actions',
    field: 'actions',
    sortable: false,
    filter: false,
    width: 120,
    cellRenderer: (params: any) => {
      const div = document.createElement('div')
      div.className = 'flex items-center gap-3'
      
      const editBtn = document.createElement('button')
      editBtn.className = 'flex items-center justify-center w-8 h-8 rounded-lg text-brand-500 hover:bg-brand-50 dark:hover:bg-brand-500/10 transition-colors'
      editBtn.innerHTML = `
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
        </svg>
      `
      editBtn.onclick = () => handleEdit(params.data.id)
      
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
      deleteBtn.onclick = () => handleDelete(params.data.id)
      
      if (canUpdate.value) {
        div.appendChild(editBtn)
      }
      if (canDelete.value) {
        div.appendChild(deleteBtn)
      }
      
      return div
    },
  },
]

// Default column definition
const defaultColDef = {
  resizable: true,
  sortable: true,
  filter: true,
}

// Create ref for rowData
const rowData = ref<any[]>([])

// Get CSRF token
const getCsrfToken = (): string => {
  const metaToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
  return metaToken || ''
}

// Fetch data from API
const fetchData = async () => {
  loading.value = true
  try {
    const searchParams = new URLSearchParams()
    if (filterNama.value) {
      searchParams.append('search', filterNama.value)
    }
    
    const url = `/admin/api/tipe-absensi${searchParams.toString() ? '?' + searchParams.toString() : ''}`
    const csrfToken = getCsrfToken()
    const response = await fetch(url, {
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
    if (result.success) {
      rowData.value = result.data || []
    } else {
      console.error('Failed to fetch data:', result.message)
      rowData.value = []
      toast.error('Gagal memuat data tipe absensi')
    }
  } catch (error) {
    console.error('Error fetching data:', error)
    rowData.value = []
    toast.error('Terjadi kesalahan saat memuat data')
  } finally {
    loading.value = false
  }
}

// Filtered data for AG Grid
const gridRowData = computed(() => {
  return rowData.value
})

// Handle add button - redirect to form page
const handleAdd = () => {
  router.push('/administrasi/tipe-absensi/new')
}

// Handle edit - redirect to form page
const handleEdit = (id: string) => {
  router.push(`/administrasi/tipe-absensi/${id}/edit`)
}

// Handle delete - show modal
const handleDelete = (id: string) => {
  deleteId.value = id
  showDeleteModal.value = true
}

// Confirm delete
const confirmDelete = async () => {
  if (!deleteId.value) return

  try {
    const csrfToken = getCsrfToken()
    const response = await fetch(`/admin/api/tipe-absensi/${deleteId.value}`, {
      method: 'DELETE',
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
      },
      credentials: 'same-origin',
    })

    const result = await response.json()
    if (result.success) {
      toast.success('Tipe absensi berhasil dihapus')
      await fetchData()
    } else {
      toast.error(result.message || 'Gagal menghapus tipe absensi')
    }
  } catch (error) {
    console.error('Error deleting:', error)
    toast.error('Terjadi kesalahan saat menghapus tipe absensi')
  } finally {
    showDeleteModal.value = false
    deleteId.value = null
  }
}

// Cancel delete
const cancelDelete = () => {
  showDeleteModal.value = false
  deleteId.value = null
}

// Filter state
const filterNama = ref('')

// Watch filter changes and refetch data with debounce
let filterTimeout: ReturnType<typeof setTimeout> | null = null
watch(filterNama, () => {
  if (filterTimeout) {
    clearTimeout(filterTimeout)
  }
  filterTimeout = setTimeout(() => {
    fetchData()
  }, 500) // Debounce 500ms
})

// Reset filter
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
