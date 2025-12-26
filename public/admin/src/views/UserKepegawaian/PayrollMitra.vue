<template>
  <AdminLayout>
    <div class="min-h-screen rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12">
      <div class="mb-6 flex items-center justify-between">
        <h3 class="font-semibold text-gray-800 text-theme-xl dark:text-white/90 sm:text-2xl">Payroll Mitra</h3>
        <button
          v-if="canCreate"
          @click="handleAdd"
          class="flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600"
        >
          Tambah Payroll Mitra
        </button>
      </div>

      <div class="mb-6 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="flex gap-4">
          <div class="flex-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Cari</label>
            <input 
              v-model="filter" 
              @input="debouncedFetch" 
              placeholder="Cari nama mitra atau program..." 
              class="h-11 w-full rounded-lg border px-4 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:bg-gray-900 dark:border-gray-700" 
            />
          </div>
          <div class="flex items-end">
            <button @click="resetFilter" class="h-11 rounded-lg border px-6 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
              Reset Filter
            </button>
          </div>
        </div>
      </div>

      <div v-if="loading" class="flex items-center justify-center py-12">
        <div class="h-8 w-8 animate-spin rounded-full border-4 border-brand-500 border-t-transparent"></div>
        <span class="ml-3 text-gray-600 dark:text-gray-400">Memuat data...</span>
      </div>

      <div v-else class="ag-theme-alpine dark:ag-theme-alpine-dark w-full">
        <ag-grid-vue
          style="width: 100%;"
          :columnDefs="columnDefs"
          :rowData="rowData"
          :pinnedBottomRowData="pinnedBottomRowData"
          :defaultColDef="defaultColDef"
          :pagination="true"
          :paginationPageSize="20"
          :animateRows="true"
          :domLayout="'autoHeight'"
          class="ag-theme-alpine"
        />
      </div>

      <ConfirmModal
        :isOpen="showDeleteModal"
        title="Hapus Payroll Mitra"
        message="Apakah Anda yakin ingin menghapus data payroll mitra ini?"
        confirmText="Hapus"
        confirmButtonClass="bg-red-500 hover:bg-red-600"
        @confirm="confirmDelete"
        @cancel="cancelDelete"
      />
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import ConfirmModal from '@/components/common/ConfirmModal.vue'
import { AgGridVue } from 'ag-grid-vue3'
import 'ag-grid-community/styles/ag-grid.css'
import 'ag-grid-community/styles/ag-theme-alpine.css'
import { useToast } from 'vue-toastification'
import { useAuth } from '@/composables/useAuth'

const router = useRouter()
const toast = useToast()
const { fetchUser, hasPermission, isAdmin } = useAuth()

const loading = ref(false)
const rowData = ref<any[]>([])
const filter = ref('')
const showDeleteModal = ref(false)
const deleteId = ref<string | null>(null)
let debounceTimer: ReturnType<typeof setTimeout> | undefined

// Permissions
const canCreate = computed(() => isAdmin() || hasPermission('create payroll mitra'))
const canUpdate = computed(() => isAdmin() || hasPermission('update payroll mitra'))
const canDelete = computed(() => isAdmin() || hasPermission('delete payroll mitra'))

// AG Grid Column Definitions
const columnDefs = [
  { headerName: 'Nama Mitra', field: 'nama_mitra', sortable: true, flex: 1 },
  // 'Mitra' column removed as requested
  { 
    headerName: 'Program', 
    valueGetter: (p: any) => p.data?.program?.nama_program || '-', 
    flex: 1 
  },
  { 
    headerName: 'Jumlah', 
    field: 'jumlah', 
    width: 140, 
    valueFormatter: (p: any) => p.value ? Number(p.value).toLocaleString('id-ID') : '0' 
  },
  { 
    headerName: 'Persentase', 
    field: 'persentase', 
    width: 120,
    valueFormatter: (p: any) => {
      // don't show percent for pinned bottom row
      if (p.node && p.node.rowPinned === 'bottom') return ''
      const v = Number(p.value) || 0
      return `${v.toFixed(0)}%`
    }
  },
  { 
    headerName: 'Total', 
    field: 'total', 
    width: 160, 
    valueFormatter: (p: any) => p.value ? Number(p.value).toLocaleString('id-ID') : '0' 
  },
  { 
    headerName: 'Actions', 
    field: 'actions', 
    width: 140, 
    sortable: false, 
    filter: false,
    cellRenderer: (params: any) => {
      // don't render action buttons for pinned bottom row
      if (params.node && params.node.rowPinned === 'bottom') {
        return document.createElement('div')
      }
      const div = document.createElement('div')
      div.className = 'flex items-center gap-3 h-full'
      
      if (canUpdate.value) {
        const editBtn = document.createElement('button')
        editBtn.className = 'flex items-center justify-center w-8 h-8 rounded-lg text-brand-500 hover:bg-brand-50'
        editBtn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>'
        editBtn.onclick = () => handleEdit(params.data.id)
        div.appendChild(editBtn)
      }
      
      if (canDelete.value) {
        const delBtn = document.createElement('button')
        delBtn.className = 'flex items-center justify-center w-8 h-8 rounded-lg text-red-500 hover:bg-red-50'
        delBtn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>'
        delBtn.onclick = () => handleDelete(params.data.id)
        div.appendChild(delBtn)
      }
      return div
    } 
  }
]

const defaultColDef = { resizable: true, sortable: true, filter: true }

const pinnedBottomRowData = computed(() => {
  if (!rowData.value || rowData.value.length === 0) return []
  const sumJumlah = rowData.value.reduce((acc:any, r:any) => acc + (Number(r.jumlah) || 0), 0)
  const sumTotal = rowData.value.reduce((acc:any, r:any) => acc + (Number(r.total) || 0), 0)
  return [{ nama_mitra: 'Total', jumlah: sumJumlah, total: sumTotal }]
})

// Data Fetching
const fetchData = async () => {
  loading.value = true
  try {
    const params = new URLSearchParams({ per_page: '1000' })
    if (filter.value.trim()) params.set('search', filter.value.trim())
    
    const res = await fetch(`/admin/api/mitra-payroll?${params.toString()}`, { credentials: 'same-origin' })
    const json = await res.json()
    
    if (json.success) {
      rowData.value = json.data
    } else {
      toast.error(json.message || 'Gagal memuat data')
    }
  } catch (e) {
    toast.error('Gagal memuat data')
  } finally {
    loading.value = false
  }
}

const debouncedFetch = () => {
  if (debounceTimer) clearTimeout(debounceTimer)
  debounceTimer = setTimeout(fetchData, 300)
}

const resetFilter = () => {
  filter.value = ''
  fetchData()
}

// CRUD Handlers
const handleAdd = () => {
  if (!canCreate.value) {
    toast.error('Anda tidak memiliki izin untuk menambah data')
    return
  }
  router.push('/user-kepegawaian/payroll-mitra/new')
}

const handleEdit = (id: string) => {
  if (!canUpdate.value) {
    toast.error('Anda tidak memiliki izin untuk mengubah data')
    return
  }
  router.push(`/user-kepegawaian/payroll-mitra/${id}/edit`)
}

const handleDelete = (id: string) => {
  deleteId.value = id
  showDeleteModal.value = true
}

const confirmDelete = async () => {
  if (!deleteId.value) return
  try {
    const tokenRes = await fetch('/admin/api/csrf-token', { credentials: 'same-origin' })
    const { csrf_token } = await tokenRes.json()
    
    const res = await fetch(`/admin/api/mitra-payroll/${deleteId.value}`, { 
      method: 'DELETE', 
      headers: { 'X-CSRF-TOKEN': csrf_token }, 
      credentials: 'same-origin' 
    })
    
    const json = await res.json()
    if (json.success) {
      toast.success('Data berhasil dihapus')
      fetchData()
    } else {
      toast.error(json.message || 'Gagal menghapus data')
    }
  } catch (e) {
    toast.error('Gagal menghapus data')
  } finally {
    showDeleteModal.value = false
    deleteId.value = null
  }
}

const cancelDelete = () => {
  showDeleteModal.value = false
  deleteId.value = null
}

onMounted(() => {
  fetchUser()
  fetchData()
})
</script>

<style scoped>
.ag-theme-alpine {
  --ag-header-background-color: #f9fafb;
}
</style>