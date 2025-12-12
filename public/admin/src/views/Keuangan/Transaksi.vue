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
        <div class="flex items-center gap-3">
          <button
            @click="handleExportExcel"
            class="flex items-center gap-2 rounded-lg bg-green-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-green-600"
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
                d="M10.8333 3.33333V9.16667H16.6667L10.8333 3.33333ZM4.16667 2.5H11.6667L17.5 8.33333V15.8333C17.5 16.2754 17.3244 16.6993 17.0118 17.0118C16.6993 17.3244 16.2754 17.5 15.8333 17.5H4.16667C3.72464 17.5 3.30072 17.3244 2.98816 17.0118C2.67559 16.6993 2.5 16.2754 2.5 15.8333V4.16667C2.5 3.72464 2.67559 3.30072 2.98816 2.98816C3.30072 2.67559 3.72464 2.5 4.16667 2.5Z"
                fill="currentColor"
              />
            </svg>
            Export Excel
          </button>
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
            Tambah Transaksi
          </button>
        </div>
      </div>

      <!-- Filter Section -->
      <div class="mb-6 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
          <div class="flex-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Cari
            </label>
            <input
              type="text"
              v-model="filterSearch"
              placeholder="Cari kode, keterangan..."
              @input="debouncedFetch"
              class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
          </div>
          <div class="flex-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Tanggal
            </label>
            <div class="relative">
              <FlatPickr
                v-model="filterTanggal"
                :config="flatpickrDateConfig"
                @on-change="debouncedFetch"
                class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-11 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                placeholder="Pilih tanggal"
              />
              <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400 pointer-events-none">
                <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path fill-rule="evenodd" clip-rule="evenodd" d="M6.66659 1.5415C7.0808 1.5415 7.41658 1.87729 7.41658 2.2915V2.99984H12.5833V2.2915C12.5833 1.87729 12.919 1.5415 13.3333 1.5415C13.7475 1.5415 14.0833 1.87729 14.0833 2.2915V2.99984L15.4166 2.99984C16.5212 2.99984 17.4166 3.89527 17.4166 4.99984V7.49984V15.8332C17.4166 16.9377 16.5212 17.8332 15.4166 17.8332H4.58325C3.47868 17.8332 2.58325 16.9377 2.58325 15.8332V7.49984V4.99984C2.58325 3.89527 3.47868 2.99984 4.58325 2.99984L5.91659 2.99984V2.2915C5.91659 1.87729 6.25237 1.5415 6.66659 1.5415ZM6.66659 4.49984H4.58325C4.30711 4.49984 4.08325 4.7237 4.08325 4.99984V6.74984H15.9166V4.99984C15.9166 4.7237 15.6927 4.49984 15.4166 4.49984H13.3333H6.66659ZM15.9166 8.24984H4.08325V15.8332C4.08325 16.1093 4.30711 16.3332 4.58325 16.3332H15.4166C15.6927 16.3332 15.9166 16.1093 15.9166 15.8332V8.24984Z" fill="currentColor"/>
                </svg>
              </span>
            </div>
          </div>

          <div class="flex-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Donatur
            </label>
            <SearchableSelect
              v-model="filterDonatur"
              :options="donaturOptions"
              placeholder="Semua Donatur"
              :search-input="donaturSearchInput"
              @update:search-input="donaturSearchInput = $event"
              @update:model-value="fetchData"
            />
          </div>
          <div class="flex-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Program
            </label>
            <SearchableSelect
              v-model="filterProgram"
              :options="programOptions"
              placeholder="Semua Program"
              :search-input="programSearchInput"
              @update:search-input="programSearchInput = $event"
              @update:model-value="fetchData"
            />
          </div>
          <div class="flex-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Kantor Cabang
            </label>
            <SearchableSelect
              v-model="filterKantorCabang"
              :options="kantorCabangOptions"
              placeholder="Semua Kantor Cabang"
              :search-input="kantorCabangSearchInput"
              @update:search-input="kantorCabangSearchInput = $event"
              @update:model-value="fetchData"
            />
          </div>
          <div class="flex-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Fundraiser
            </label>
            <SearchableSelect
              v-model="filterFundraiser"
              :options="fundraiserOptions"
              placeholder="Semua Fundraiser"
              :search-input="fundraiserSearchInput"
              @update:search-input="fundraiserSearchInput = $event"
              @update:model-value="fetchData"
            />
          </div>
          <div class="flex items-end">
            <button
              @click="resetFilter"
              class="h-11 w-full rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]"
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
      title="Hapus Transaksi"
      message="Apakah Anda yakin ingin menghapus transaksi ini? Tindakan ini tidak dapat dibatalkan."
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
import FlatPickr from 'vue-flatpickr-component'
import * as XLSX from 'xlsx'
import 'flatpickr/dist/flatpickr.css'
import 'ag-grid-community/styles/ag-grid.css'
import 'ag-grid-community/styles/ag-theme-alpine.css'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import ConfirmModal from '@/components/common/ConfirmModal.vue'
import SearchableSelect from '@/components/forms/SearchableSelect.vue'

interface TransaksiRow {
  id: string
  kode: string | null
  donatur: string | null
  fundraiser: string | null
  program: string | null
  kantor_cabang: string | null
  nominal: number
  nominal_formatted: string
  tanggal_transaksi: string | null
  keterangan: string | null
}

const route = useRoute()
const router = useRouter()
const { hasPermission, fetchUser } = useAuth()

const canCreate = computed(() => hasPermission('create transaksi'))
const toast = useToast()

const currentPageTitle = computed(() => (route.meta.title as string) || 'Transaksi')

const loading = ref(false)
const rowData = ref<TransaksiRow[]>([])
const filterSearch = ref('')
const filterTanggal = ref('')
const filterDonatur = ref('')
const filterProgram = ref('')
const filterFundraiser = ref('')
const filterKantorCabang = ref('')
const showDeleteModal = ref(false)
const deleteId = ref<string | null>(null)
let debounceTimer: ReturnType<typeof setTimeout> | undefined

// Dropdown options
const donaturList = ref<any[]>([])
const programList = ref<any[]>([])
const fundraiserList = ref<any[]>([])
const kantorCabangList = ref<any[]>([])

// Search inputs for SearchableSelect
const donaturSearchInput = ref('')
const programSearchInput = ref('')
const fundraiserSearchInput = ref('')
const kantorCabangSearchInput = ref('')

// Computed options for SearchableSelect
const donaturOptions = computed(() =>
  donaturList.value.map((item) => ({
    value: item.id,
    label: item.nama || '-',
  }))
)

const programOptions = computed(() =>
  programList.value.map((item) => ({
    value: item.id,
    label: item.nama_program || '-',
  }))
)

const fundraiserOptions = computed(() =>
  fundraiserList.value.map((item) => ({
    value: String(item.id),
    label: item.name || '-',
  }))
)

const kantorCabangOptions = computed(() =>
  kantorCabangList.value.map((item) => ({
    value: item.id,
    label: item.nama || '-',
  }))
)


const flatpickrDateConfig = {
  dateFormat: 'Y-m-d',
  altInput: true,
  altFormat: 'd/m/Y',
  wrap: false,
}

// permissions flags used in cell renderer (filled in onMounted)
const canPerms = {
  create: false,
  update: false,
  delete: false,
}

const columnDefs = [
  {
    headerName: 'Kode',
    field: 'kode',
    sortable: true,
    width: 160,
    valueFormatter: (params: any) => params.value || '-',
  },
  {
    headerName: 'Donatur',
    field: 'donatur',
    sortable: true,
    flex: 1,
    valueFormatter: (params: any) => params.value || '-',
  },
  {
    headerName: 'Kantor Cabang',
    field: 'kantor_cabang',
    sortable: true,
    flex: 1,
    valueFormatter: (params: any) => params.value || '-',
  },
  {
    headerName: 'Fundraiser',
    field: 'fundraiser',
    sortable: true,
    flex: 1,
    valueFormatter: (params: any) => params.value || '-',
  },
  {
    headerName: 'Program',
    field: 'program',
    sortable: true,
    flex: 1,
    valueFormatter: (params: any) => params.value || '-',
  },
  {
    headerName: 'Nominal',
    field: 'nominal_formatted',
    sortable: true,
    flex: 1,
  },
  {
    headerName: 'Tanggal',
    field: 'tanggal_transaksi',
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
  {
    headerName: 'Actions',
    field: 'actions',
    sortable: false,
    filter: false,
    width: 120,
    cellRenderer: (params: any) => {
      const div = document.createElement('div')
      div.className = 'flex items-center gap-3'

      if (canPerms.update) {
        const editBtn = document.createElement('button')
        editBtn.className = 'flex items-center justify-center w-8 h-8 rounded-lg text-brand-500 hover:bg-brand-50 dark:hover:bg-brand-500/10 transition-colors'
        editBtn.innerHTML = `
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
          </svg>
        `
        editBtn.onclick = () => handleEdit(params.data.id)
        div.appendChild(editBtn)
      }

      if (canPerms.delete) {
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
        div.appendChild(deleteBtn)
      }

      return div
    },
  },
]

const defaultColDef = {
  resizable: true,
  sortable: true,
  filter: false,
}

const fetchFilterOptions = async () => {
  try {
    const [kantorRes, donaturRes, programRes, fundraiserRes] = await Promise.all([
      fetch('/admin/api/kantor-cabang?per_page=1000', { credentials: 'same-origin' }),
      fetch('/admin/api/donatur?per_page=1000', { credentials: 'same-origin' }),
      fetch('/admin/api/program?per_page=1000', { credentials: 'same-origin' }),
      fetch('/admin/api/karyawan?per_page=1000', { credentials: 'same-origin' }),
    ])

    if (kantorRes.ok) {
      const json = await kantorRes.json()
      if (json.success) {
        kantorCabangList.value = Array.isArray(json.data) ? json.data : json.data?.data || []
      }
    }

    if (donaturRes.ok) {
      const json = await donaturRes.json()
      if (json.success) {
        donaturList.value = Array.isArray(json.data) ? json.data : json.data?.data || []
      }
    }

    if (programRes.ok) {
      const json = await programRes.json()
      if (json.success) {
        programList.value = Array.isArray(json.data) ? json.data : json.data?.data || []
      }
    }

    if (fundraiserRes.ok) {
      const json = await fundraiserRes.json()
      if (json.success) {
        fundraiserList.value = Array.isArray(json.data) ? json.data : json.data?.data || []
      }
    }
  } catch (error) {
    console.error('Error fetching filter options:', error)
  }
}

const fetchData = async () => {
  loading.value = true

  try {
    const params = new URLSearchParams()
    params.append('per_page', '100')

    if (filterSearch.value) {
      params.append('search', filterSearch.value)
    }

    if (filterTanggal.value) {
      params.append('tanggal', filterTanggal.value)
    }

    if (filterDonatur.value) {
      params.append('donatur_id', filterDonatur.value)
    }

    if (filterProgram.value) {
      params.append('program_id', filterProgram.value)
    }

    if (filterKantorCabang.value) {
      params.append('kantor_cabang_id', filterKantorCabang.value)
    }

    if (filterFundraiser.value) {
      params.append('fundraiser_id', filterFundraiser.value)
    }

    const res = await fetch(`/admin/api/transaksi?${params.toString()}`, {
      credentials: 'same-origin',
    })

    if (!res.ok) throw new Error('Failed to fetch transaksi')

    const json = await res.json()

    if (json.success) {
      rowData.value = (json.data || []).map((item: any) => ({
        id: item.id,
        kode: item.kode,
        donatur: item.donatur?.nama || null,
        kantor_cabang: item.kantor_cabang?.nama || null,
        fundraiser: item.fundraiser?.nama || null,
        program: item.program?.nama || null,
        nominal: item.nominal,
        nominal_formatted: item.nominal_formatted,
        tanggal_transaksi: item.tanggal_transaksi,
        keterangan: item.keterangan,
      }))
    }
  } catch (error) {
    toast.error('Gagal memuat data transaksi')
  } finally {
    loading.value = false
  }
}

const debouncedFetch = () => {
  if (debounceTimer) clearTimeout(debounceTimer)
  debounceTimer = setTimeout(() => {
    fetchData()
  }, 300)
}

const resetFilter = () => {
  filterSearch.value = ''
  filterTanggal.value = ''
  filterDonatur.value = ''
  filterProgram.value = ''
  filterKantorCabang.value = ''
  filterFundraiser.value = ''
  fetchData()
}

const handleAdd = () => {
  router.push('/keuangan/transaksi/new')
}

const handleEdit = (id: string) => {
  router.push(`/keuangan/transaksi/${id}/edit`)
}

const handleDelete = (id: string) => {
  deleteId.value = id
  showDeleteModal.value = true
}

const confirmDelete = async () => {
  if (!deleteId.value) return

  try {
    const tokenRes = await fetch('/admin/api/csrf-token', { credentials: 'same-origin' })
    if (!tokenRes.ok) throw new Error('Failed to fetch CSRF token')
    const tokenJson = await tokenRes.json()

    const res = await fetch(`/admin/api/transaksi/${deleteId.value}`, {
      method: 'DELETE',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': tokenJson.csrf_token,
      },
      credentials: 'same-origin',
    })

    const json = await res.json()

    if (json.success) {
      toast.success(json.message || 'Transaksi berhasil dihapus')
      fetchData()
    } else {
      toast.error(json.message || 'Gagal menghapus transaksi')
    }
  } catch (error) {
    toast.error('Gagal menghapus transaksi')
  } finally {
    showDeleteModal.value = false
    deleteId.value = null
  }
}

const cancelDelete = () => {
  showDeleteModal.value = false
  deleteId.value = null
}

const handleExportExcel = () => {
  const dataToExport = rowData.value.map((item) => ({
    'Kode': item.kode || '-',
    'Donatur': item.donatur || '-',
    'Kantor Cabang': item.kantor_cabang || '-',
    'Fundraiser': item.fundraiser || '-',
    'Program': item.program || '-',
    'Nominal': item.nominal_formatted,
    'Tanggal': item.tanggal_transaksi
      ? new Date(item.tanggal_transaksi).toLocaleDateString('id-ID', {
          year: 'numeric',
          month: 'long',
          day: 'numeric',
        })
      : '-',
  }))

  const worksheet = XLSX.utils.json_to_sheet(dataToExport)
  const workbook = XLSX.utils.book_new()
  XLSX.utils.book_append_sheet(workbook, worksheet, 'Transaksi')

  const now = new Date()
  const filename = `Transaksi_${now.toISOString().split('T')[0]}.xlsx`

  XLSX.writeFile(workbook, filename)
}

onMounted(async () => {
  await fetchUser()
  // Setup permission flags for grid row actions
  canPerms.create = hasPermission('create transaksi')
  canPerms.update = hasPermission('update transaksi')
  canPerms.delete = hasPermission('delete transaksi')
  fetchFilterOptions()
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

.ag-theme-alpine-dark {
  --ag-header-background-color: #1f2937;
  --ag-header-foreground-color: #f9fafb;
  --ag-border-color: #374151;
  --ag-row-hover-color: #374151;
}
</style>
