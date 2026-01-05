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
          Tambah Donatur
        </button>
      </div>

      <!-- Filter Section -->
      <div class="mb-6 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
          <div class="md:col-span-2 xl:col-span-2">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Cari
            </label>
            <input
              type="text"
              v-model="filterNama"
              placeholder="Cari nama, kode, email, no hp..."
              @input="debouncedFetch"
              class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Fundraiser</label>
            <SearchableSelect v-model="filterPic" :options="picSelectOptions" placeholder="Semua Fundraiser" />
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Mitra</label>
            <SearchableSelect v-model="filterMitra" :options="mitraSelectOptions" placeholder="Semua Mitra" />
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Jenis Donatur</label>
            <SearchableMultiSelect
              v-model="filterJenis"
              :options="jenisSelectOptions"
              placeholder="Semua Tipe"
              @update:modelValue="handleJenisFilterChange"
            />
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Kantor Cabang</label>
            <SearchableSelect v-model="filterKantorCabang" :options="kantorCabangSelectOptions" placeholder="Semua Kantor" />
          </div>

          <div class="flex items-end md:col-span-2 xl:col-span-1">
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
      title="Hapus Donatur"
      message="Apakah Anda yakin ingin menghapus donatur ini? Tindakan ini tidak dapat dibatalkan."
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
import { useToast } from 'vue-toastification'
import { AgGridVue } from 'ag-grid-vue3'
import 'ag-grid-community/styles/ag-grid.css'
import 'ag-grid-community/styles/ag-theme-alpine.css'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import ConfirmModal from '@/components/common/ConfirmModal.vue'
import SearchableSelect from '@/components/forms/SearchableSelect.vue'
import SearchableMultiSelect from '@/components/forms/SearchableMultiSelect.vue'
import { useAuth } from '@/composables/useAuth'

interface DonaturRow {
  id: string
  kode: string | null
  nama: string
  jenis_donatur: string[]
  pic: string | null
  pic_user?: { id: string; nama?: string }
  mitra?: { id: string; nama?: string } | null
  provinsi?: string | null
  kota_kab?: string | null
  kecamatan?: string | null
  kelurahan?: string | null
  status: string
  tanggal_dibuat: string | null
  kantor_cabang: { id: string; nama: string } | null
}

const route = useRoute()
const router = useRouter()
const toast = useToast()
const { fetchUser, hasPermission, isAdmin, user } = useAuth()

const isFundraiser = computed(() => {
  const r = (user.value && (user.value as any).role) || null
  const name = r ? ((r.name && String(r.name)) || String(r)) : ''
  const n = String(name || '').trim().toLowerCase()
  return n === 'fundrising' || n === 'fundraising' || n === 'fundraiser'
})
const currentPageTitle = computed(() => (route.meta.title as string) || 'Donatur')
const canCreate = computed(() => isAdmin() || hasPermission('create donatur'))
const canUpdate = computed(() => isAdmin() || hasPermission('update donatur'))
const canDelete = computed(() => isAdmin() || hasPermission('delete donatur'))
const canView = computed(() => isAdmin() || hasPermission('view donatur'))

// Loading & data state
const loading = ref(false)
const rowData = ref<DonaturRow[]>([])
const showDeleteModal = ref(false)
const deleteId = ref<string | null>(null)

// Filter state
const filterNama = ref('')
const filterPic = ref('')
const filterJenis = ref<string[]>([])
const filterMitra = ref('')
const filterKantorCabang = ref('')
let debounceTimer: ReturnType<typeof setTimeout> | undefined

const ALL_JENIS_OPTION = '__ALL_JENIS__'

const baseJenisOptions = [
  { value: 'komunitas', label: 'Komunitas' },
  { value: 'kotak_infaq', label: 'Kotak Infaq' },
  { value: 'retail', label: 'Retail' },
]

const jenisSelectOptions = computed(() => [
  { value: ALL_JENIS_OPTION, label: 'Semua Jenis Donatur' },
  ...baseJenisOptions,
])

const kantorCabangOptions = ref<any[]>([])
const karyawanOptions = ref<any[]>([])
const mitraOptions = ref<any[]>([])

const picSelectOptions = computed(() => [
  { value: '', label: 'Semua Fundraiser' },
  ...karyawanOptions.value.map((item: any) => ({ value: String(item.id), label: item.nama || item.name || '-' })),
])

const mitraSelectOptions = computed(() => [
  { value: '', label: 'Semua Mitra' },
  ...mitraOptions.value.map((item: any) => ({ value: String(item.id), label: item.nama || item.name || '-' })),
])

const kantorCabangSelectOptions = computed(() => [
  { value: '', label: 'Semua Kantor Cabang' },
  ...kantorCabangOptions.value.map((item: any) => ({ value: String(item.id), label: item.nama || item.name || '-' })),
])

// Column definitions
const columnDefs = computed(() => {
  const cols: any[] = [
    {
      headerName: 'No',
      field: '__no',
      width: 80,
      sortable: false,
      valueGetter: (params: any) => {
        try {
          const api: any = params.api
          let page = 0
          let pageSizeLocal = 0
          if (api && typeof api.paginationGetCurrentPage === 'function') {
            page = api.paginationGetCurrentPage() || 0
            pageSizeLocal = api.paginationGetPageSize ? api.paginationGetPageSize() : 0
          }
          const idx = params.node && typeof params.node.rowIndex === 'number' ? params.node.rowIndex : 0
          return pageSizeLocal ? page * pageSizeLocal + idx + 1 : idx + 1
        } catch (e) {
          return (params.node && typeof params.node.rowIndex === 'number') ? params.node.rowIndex + 1 : '-'
        }
      },
    },
    {
      headerName: 'Kode',
      field: 'kode',
      sortable: true,
      width: 120,
      valueFormatter: (params: any) => params.value || '-',
    },
    {
      headerName: 'Nama',
      field: 'nama',
      sortable: true,
      flex: 1,
    },
    {
      headerName: 'Fundraiser',
      field: 'pic',
      sortable: true,
      flex: 1,
      valueFormatter: (params: any) => params.data?.pic_user?.nama || params.value || '-',
    },
    {
      headerName: 'Jenis Donatur',
      field: 'jenis_donatur',
      sortable: true,
      flex: 1,
      valueFormatter: (params: any) => {
        const jenis = params.value as string[]
        if (!jenis || jenis.length === 0) return '-'
        return jenis.map((j: string) => j.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())).join(', ')
      },
    },
    {
      headerName: 'Kantor Cabang',
      field: 'kantor_cabang',
      sortable: true,
      flex: 1,
      valueFormatter: (params: any) => params.value?.nama || '-',
    },
    {
      headerName: 'Mitra',
      field: 'mitra',
      sortable: true,
      flex: 1,
      valueFormatter: (params: any) => params.value?.nama || '-',
    },
    {
      headerName: 'Status',
      field: 'status',
      sortable: true,
      width: 120,
      cellRenderer: (params: any) => {
        const status = params.value || 'aktif'
        const statusColors: Record<string, string> = {
          'aktif': 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
          'tidak_aktif': 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
          'pending': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
        }
        const colorClass = statusColors[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400'
        const displayStatus = status.replace('_', ' ').replace(/\b\w/g, (l: string) => l.toUpperCase())
        
        const span = document.createElement('span')
        span.className = `px-2 py-1 rounded-full text-xs font-medium ${colorClass}`
        span.textContent = displayStatus
        return span
      },
    },
    {
      headerName: 'Tanggal',
      field: 'tanggal_dibuat',
      sortable: true,
      width: 150,
      valueFormatter: (params: any) => {
        if (params.value) {
          return new Date(params.value).toLocaleDateString('id-ID', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
          })
        }
        return '-'
      },
    },
  ]

  if (canUpdate.value || canDelete.value) {
    cols.push({
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
    })
  }

  return cols
})

// Default column definition
const defaultColDef = {
  resizable: true,
  sortable: true,
  filter: false,
}

// Fetch data from API
const fetchData = async () => {
  loading.value = true
  try {
    const params = new URLSearchParams()
    params.append('per_page', '100')
    
    if (filterNama.value) {
      params.append('search', filterNama.value)
    }

    // If current user is a fundraiser (and not admin), restrict list to their PIC
    if (!isAdmin() && isFundraiser.value && user.value && (user.value as any).id) {
      const uid = String((user.value as any).id)
      params.append('pic', uid)
      // reflect in UI filter if not explicitly set
      if (!filterPic.value) filterPic.value = uid
    } else if (filterPic.value) {
      params.append('pic', filterPic.value)
    }
    if (filterJenis.value && filterJenis.value.length) {
      params.append('jenis_donatur', filterJenis.value.join(','))
    }
    if (filterKantorCabang.value) {
      params.append('kantor_cabang_id', filterKantorCabang.value)
    }
    if (filterMitra.value) {
      params.append('mitra_id', filterMitra.value)
    }

    const res = await fetch(`/admin/api/donatur?${params.toString()}`, {
      credentials: 'same-origin',
    })

    if (!res.ok) throw new Error('Failed to fetch donatur')

    const json = await res.json()

    if (json.success) {
      rowData.value = json.data || []
    }
  } catch (error) {
    toast.error('Gagal memuat data donatur')
  } finally {
    loading.value = false
  }
}

// Debounced fetch for search
const debouncedFetch = () => {
  if (debounceTimer) clearTimeout(debounceTimer)
  debounceTimer = setTimeout(() => {
    fetchData()
  }, 300)
}

// Handle add button - redirect to form page
const handleAdd = () => {
  if (!canCreate.value) {
    toast.error('Anda tidak memiliki izin untuk membuat donatur')
    return
  }
  router.push('/user-kepegawaian/donatur/new')
}

// Handle edit - redirect to form page
const handleEdit = (id: string) => {
  if (!canUpdate.value) {
    toast.error('Anda tidak memiliki izin untuk mengubah donatur')
    return
  }
  router.push(`/user-kepegawaian/donatur/${id}/edit`)
}

// Handle delete
const handleDelete = (id: string) => {
  deleteId.value = id
  showDeleteModal.value = true
}

// Confirm delete
const confirmDelete = async () => {
  if (!deleteId.value) return

  try {
    const tokenRes = await fetch('/admin/api/csrf-token', { credentials: 'same-origin' })
    if (!tokenRes.ok) throw new Error('Failed to fetch CSRF token')
    const tokenJson = await tokenRes.json()

    const res = await fetch(`/admin/api/donatur/${deleteId.value}`, {
      method: 'DELETE',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': tokenJson.csrf_token,
      },
      credentials: 'same-origin',
    })

    const json = await res.json()

    if (json.success) {
      toast.success(json.message || 'Donatur berhasil dihapus')
      fetchData()
    } else {
      toast.error(json.message || 'Gagal menghapus donatur')
    }
  } catch (error) {
    toast.error('Gagal menghapus donatur')
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

// Reset filter
const resetFilter = () => {
  filterNama.value = ''
  filterPic.value = ''
  filterJenis.value = []
  filterKantorCabang.value = ''
  filterMitra.value = ''
  fetchData()
}

const handleJenisFilterChange = (values: string[]) => {
  if (values.includes(ALL_JENIS_OPTION)) {
    filterJenis.value = []
    return
  }
  filterJenis.value = values
}

// Trigger fetch automatically whenever non-text filters change
watch([filterPic, filterJenis, filterKantorCabang, filterMitra], () => {
  fetchData()
}, { deep: true })

const fetchReferenceData = async () => {
  try {
    const isRoleAdminCabang = (() => {
      if (!user.value) return false
      const roles = user.value.roles || (user.value.role ? [user.value.role] : [])
      return Array.isArray(roles) && roles.some((r: any) => {
        const name = typeof r === 'string' ? r : r?.name
        return typeof name === 'string' && ['admin', 'admin cabang'].includes(name.trim().toLowerCase())
      })
    })()

    const kantorUrl = (isAdmin() || isRoleAdminCabang) ? '/admin/api/kantor-cabang?per_page=1000' : '/admin/api/kantor-cabang?per_page=1000&only_assigned=1'
    const [kantorRes, karyawanRes, mitraRes] = await Promise.all([
      fetch(kantorUrl, { credentials: 'same-origin' }),
      fetch('/admin/api/karyawan?per_page=1000', { credentials: 'same-origin' }),
      fetch('/admin/api/mitra?per_page=1000', { credentials: 'same-origin' }),
    ])

    if (kantorRes.ok) {
      const json = await kantorRes.json()
      if (json.success) {
        const payload = Array.isArray(json.data) ? json.data : json.data?.data
        kantorCabangOptions.value = Array.isArray(payload) ? payload : []
      }
    }

    if (karyawanRes.ok) {
      const json = await karyawanRes.json()
      if (json.success) {
        const payload = Array.isArray(json.data) ? json.data : json.data?.data
        karyawanOptions.value = Array.isArray(payload) ? payload : []
      }
    }

    if (mitraRes && mitraRes.ok) {
      const json = await mitraRes.json()
      if (json.success) {
        const payload = Array.isArray(json.data) ? json.data : json.data?.data
        mitraOptions.value = Array.isArray(payload) ? payload : []
      }
    }
  } catch (error) {
    // ignore
  }
}

onMounted(async () => {
  await fetchUser()
  await fetchReferenceData()
  await fetchData()
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

