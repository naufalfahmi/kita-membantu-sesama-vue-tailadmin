<template>
  <AdminLayout>
    <div
      class="min-h-screen rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12"
    >
      <!-- Top credit boxes (responsive, dynamic) - only show boxes with credit > 0 -->
      <div class="mb-6" v-if="visibleCredits.length > 0">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
          <div
            v-for="credit in visibleCredits"
            :key="credit.type"
            class="rounded-lg border border-gray-200 bg-white p-4 flex flex-col sm:items-center"
          >
            <div class="text-xs text-gray-500">{{ credit.label }}</div>
            <div class="text-lg font-medium text-gray-800 dark:text-white/90 mt-1">{{ credit.formatted }}</div>
          </div>
        </div>
      </div>

      <div class="mb-6 flex items-center justify-between">
        <h3 class="font-semibold text-gray-800 text-theme-xl dark:text-white/90 sm:text-2xl">
          {{ currentPageTitle }}
        </h3>
        <!-- credit boxes moved to top for responsiveness -->
        <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
          <button
            @click="handleExportExcel"
            class="w-full sm:w-auto flex items-center justify-center gap-2 rounded-lg bg-green-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-green-600"
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
            class="w-full sm:w-auto flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600"
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
            Tambah Penyaluran
          </button>
        </div>
      </div>

      <!-- Filter Section -->
      <div class="mb-6 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
          <div class="flex-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Dana Salur
            </label>
            <SearchableSelect
              v-model="filterProgramId"
              :options="programOptions"
              placeholder="Cari program..."
            />
          </div>
          <div class="flex-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              PIC
            </label>
            <input
              type="text"
              v-model="filterPIC"
              placeholder="Cari PIC..."
              class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
          </div>
                <div class="flex-1">
                  <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Tanggal</label>
                  <div class="relative">
                    <flat-pickr
                      v-model="filterTanggalRange"
                      :config="flatpickrRangeConfig"
                      class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pl-4 pr-11 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                      placeholder="Pilih rentang tanggal"
                    />
                  </div>
                </div>
                <div class="flex-1">
                  <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Tipe Penyaluran</label>
                  <SearchableSelect v-model="filterType" :options="typeOptions" placeholder="Cari tipe..." />
                </div>
        </div>
        <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
          <div class="flex items-end col-span-1 md:col-span-2 lg:col-span-1">
            <button
              @click="resetFilter"
              class="h-11 w-full rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]"
            >
              Reset Filter
            </button>
          </div>
        </div>
      </div>

      <div class="relative" style="width: 100%; height: 450px;">
        <!-- Loading Overlay -->
        <div
          v-if="isLoadingGrid"
          class="absolute inset-0 z-10 flex items-center justify-center bg-white/80 dark:bg-gray-900/80"
        >
          <div class="flex flex-col items-center gap-3">
            <div class="h-12 w-12 animate-spin rounded-full border-4 border-gray-200 border-t-brand-500"></div>
            <p class="text-sm text-gray-600 dark:text-gray-400">Memuat data...</p>
          </div>
        </div>
        
        <div class="ag-theme-alpine dark:ag-theme-alpine-dark" style="width: 100%; height: 100%;">
          <ag-grid-vue
            ref="agGridRef"
            class="ag-theme-alpine"
            style="width: 100%; height: 100%;"
            :columnDefs="columnDefs"
            :defaultColDef="defaultColDef"
            :rowModelType="'infinite'"
            :datasource="dataSource"
            :rowBuffer="10"
            :cacheBlockSize="10"
            :maxBlocksInCache="5"
            :maxConcurrentDatasourceRequests="2"
            :infiniteInitialRowCount="10"
            :suppressSorting="false"
            theme="legacy"
            :animateRows="true"
            :suppressHorizontalScroll="true"
            @sortChanged="onSortChanged"
          />
        </div>
        
        <!-- AG Grid built-in no-rows overlay will be shown when there is no data -->
      </div>
    </div>

    <ConfirmModal
      :isOpen="showDeleteModal"
      title="Hapus Penyaluran"
      message="Apakah Anda yakin ingin menghapus penyaluran ini? Tindakan ini tidak dapat dibatalkan."
      confirmText="Hapus"
      confirmButtonClass="bg-red-500 hover:bg-red-600"
      @confirm="confirmDelete"
      @cancel="cancelDelete"
    />
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted, computed, watch, nextTick } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { AgGridVue } from 'ag-grid-vue3'
import type { IDatasource, IGetRowsParams } from 'ag-grid-community'
import flatPickr from 'vue-flatpickr-component'
import * as XLSX from 'xlsx'
import 'flatpickr/dist/flatpickr.css'
import 'ag-grid-community/styles/ag-grid.css'
import 'ag-grid-community/styles/ag-theme-alpine.css'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import SearchableSelect from '@/components/forms/SearchableSelect.vue'
import ConfirmModal from '@/components/common/ConfirmModal.vue'
import { useToast } from 'vue-toastification'
import { useAuth } from '@/composables/useAuth'

interface PenyaluranRow {
  id: string
  namaProgram: string
  jumlahDana: number
  pic: string
  tanggal: string
}


const route = useRoute()
const router = useRouter()
const currentPageTitle = computed(() => (route.meta.title as string) || 'Penyaluran')
const agGridRef = ref<InstanceType<typeof AgGridVue> | null>(null)
const isLoadingGrid = ref(true)
const toast = useToast()
const { fetchUser, hasPermission, isAdmin } = useAuth()
const canCreate = computed(() => isAdmin() || hasPermission('create penyaluran'))
const canUpdate = computed(() => isAdmin() || hasPermission('update penyaluran'))
const canDelete = computed(() => isAdmin() || hasPermission('delete penyaluran'))
const canView = computed(() => isAdmin() || hasPermission('view penyaluran'))
const showDeleteModal = ref(false)
const deleteId = ref<string | null>(null)

// Flatpickr configuration for single date
const flatpickrDateConfig = {
  dateFormat: 'Y-m-d',
  altInput: true,
  altFormat: 'd/m/Y',
  wrap: false,
}

// Flatpickr configuration for range picker
const flatpickrRangeConfig = {
  ...flatpickrDateConfig,
  mode: 'range',
}

// Column definitions
const columnDefs = [
  {
    headerName: 'Nama Penyaluran',
    field: 'namaPenyaluran',
    sortable: true,
    filter: false,
    flex: 1,
  },
  {
    headerName: 'Dana Salur',
    field: 'namaProgram',
    sortable: true,
    filter: false,
    flex: 1,
    valueGetter: (params: any) => {
      const tipe = params.data?.tipe || ''
      const mapped = submissionTypeMeta.value.find((m: any) => String(m.value).toLowerCase() === String(tipe).toLowerCase())
      const label = mapped ? mapped.name : tipe
      
      // If label is 'Program', return the program name
      if (String(label).toLowerCase() === 'program') {
        const val = params.data?.namaProgram || ''
        return val ? val : 'Program'
      }
      
      // Otherwise return the mapped label (e.g. OPS 1, DP 1)
      return label ? label : (params.data?.namaProgram || 'Operasional')
    },
  },
  {
    headerName: 'Tipe POS',
    field: 'tipe',
    sortable: true,
    filter: false,
    width: 180,
  },
  {
    headerName: 'Jumlah Dana',
    field: 'jumlahDana',
    sortable: true,
    filter: false,
    flex: 1,
    valueFormatter: (params: any) => {
      if (params.value) {
        return new Intl.NumberFormat('id-ID', {
          style: 'currency',
          currency: 'IDR',
          minimumFractionDigits: 0,
        }).format(params.value)
      }
      return ''
    },
  },
  {
    headerName: 'PIC',
    field: 'pic',
    sortable: true,
    filter: false,
    flex: 1,
  },
  {
    headerName: 'Tanggal',
    field: 'tanggal',
    sortable: true,
    filter: false,
    width: 160,
    valueFormatter: (params: any) => {
      if (!params.value) return ''
      try {
        const d = new Date(params.value)
        return d.toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' })
      } catch (e) { return String(params.value) }
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
      div.className = 'flex items-center gap-2'

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

      if (canUpdate.value) div.appendChild(editBtn)
      if (canDelete.value) div.appendChild(deleteBtn)

      return div
    },
  },
]

// Default column definition
const defaultColDef = {
  resizable: true,
  sortable: true,
  filter: false,
}

// Row data will come from `penyalurans` table via API
const rowDataArray = ref<PenyaluranRow[]>([])
const myCredit = ref<number>(0)
// Dynamic credits storage
interface CreditInfo {
  type: string
  label: string
  value: number
  formatted: string
}
const dynamicCredits = ref<CreditInfo[]>([])

// Only show credits that have a positive remaining value
const visibleCredits = computed(() => dynamicCredits.value.filter((c) => Number(c.value) > 0))

// Backward compatibility refs (deprecated, use dynamicCredits instead)
const creditProgram = ref<number>(0)
const creditOperasional = ref<number>(0)
const creditGaji = ref<number>(0)

// program select options for filter
const programOptions = ref<Array<{value:string,label:string}>>([])
// tipe options for searchable select (dynamic from API)
const typeOptions = ref<Array<{value:string,label:string}>>([])

const formattedMyCredit = computed(() => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(myCredit.value)
})
const formattedCreditProgram = computed(() => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(creditProgram.value || 0))
const formattedCreditOperasional = computed(() => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(creditOperasional.value || 0))
const formattedCreditGaji = computed(() => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(creditGaji.value || 0))

const loadPenyalurans = async () => {
  try {
    const res = await fetch('/admin/api/penyaluran?per_page=1000', { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    const json = await res.json()
    if (!json.success) {
      toast.error('Gagal memuat data penyaluran')
      return
    }

    // Map penyaluran items into PenyaluranRow shape
    const items = (json.data || []).map((p: any) => ({
      id: p.id,
      namaPenyaluran: p.program_name || (p.pengajuan && (p.pengajuan.program?.nama_program || p.pengajuan.program?.nama)) || '',
      namaProgram: (p.pengajuan && (p.pengajuan.program?.nama_program || p.pengajuan.program?.nama)) || '',
      programId: p.pengajuan && p.pengajuan.program ? (p.pengajuan.program.id || null) : (p.program_id || null),
      tipe: p.submission_type || (p.pengajuan && p.pengajuan.submission_type) || '',
      jumlahDana: p.amount || 0,
      pic: p.pic || (p.pengajuan && p.pengajuan.fundraiser ? p.pengajuan.fundraiser.name : ''),
      tanggal: p.created_at || p.tanggal || null,
      raw: p,
    }))

    rowDataArray.value = items
    refreshGrid(true)
  } catch (err) {
    console.error('Error loading penyaluran', err)
    toast.error('Gagal memuat data')
  }
}

// Filter state
const filterPIC = ref('')
// single range picker value (flatpickr returns array of Date objects)
const filterTanggalRange = ref<any>('')
const filterType = ref('')
const filterProgramId = ref('')

// Filtered data based on filter
const filteredData = computed(() => {
  let filtered = [...rowDataArray.value]
  
  // Filter by selected Program
  if (filterProgramId.value) {
    filtered = filtered.filter((item) => String(item.programId || '') === String(filterProgramId.value))
  }
  
  // Filter by PIC
  if (filterPIC.value) {
    const searchTerm = filterPIC.value.toLowerCase()
    filtered = filtered.filter((item) =>
      item.pic.toLowerCase().includes(searchTerm)
    )
  }
  
  // Filter by Tanggal (single range picker)
  if (filterTanggalRange.value) {
    let from: Date | null = null
    let to: Date | null = null
    const v = filterTanggalRange.value
    // flatpickr range commonly returns an array of Date objects
    if (Array.isArray(v)) {
      from = v[0] ? new Date(v[0]) : null
      to = v[1] ? new Date(v[1]) : from
    } else if (v && typeof v === 'string') {
      // fallback: try to parse comma or " to " separated strings
      const parts = v.split(/\s+to\s+|,| - /)
      if (parts.length >= 1) {
        from = parts[0] ? new Date(parts[0]) : null
        to = parts[1] ? new Date(parts[1]) : from
      }
    }

    if (from || to) {
      filtered = filtered.filter((item) => {
        if (!item.tanggal) return false
        const itemDate = new Date(item.tanggal)
        if (from && to) {
          return itemDate >= new Date(from.getFullYear(), from.getMonth(), from.getDate()) && itemDate <= new Date(to.getFullYear(), to.getMonth(), to.getDate(), 23, 59, 59)
        }
        if (from) return itemDate >= new Date(from.getFullYear(), from.getMonth(), from.getDate())
        if (to) return itemDate <= new Date(to.getFullYear(), to.getMonth(), to.getDate(), 23, 59, 59)
        return true
      })
    }
  }

  // Filter by tipe penyaluran
  if (filterType.value) {
    filtered = filtered.filter((item) => {
      try {
        return String(item.tipe || '').toLowerCase() === String(filterType.value).toLowerCase()
      } catch (e) { return false }
    })
  }
  
  // (Jumlah Dana min/max filters removed)
  
  return filtered
})

// Show empty state when filtered data is empty
const showEmptyState = computed(() => filteredData.value.length === 0)

// Handle add button - redirect to form page
const handleAdd = () => {
  router.push('/keuangan/penyaluran/new')
}

// Handle edit - redirect to form page
const handleEdit = (id: string) => {
  router.push(`/keuangan/penyaluran/${id}/edit`)
}

// Handle delete
const handleDelete = (id: string) => {
  deleteId.value = id
  showDeleteModal.value = true
}

const confirmDelete = () => {
  if (!deleteId.value) {
    return
  }
  const id = deleteId.value
  try {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
    fetch(`/admin/api/penyaluran/${id}`, { method: 'DELETE', credentials: 'same-origin', headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' } })
      .then((res) => res.json())
      .then((json) => {
        if (!json.success) {
          toast.error(json.message || 'Gagal menghapus penyaluran')
          return
        }

        rowDataArray.value = rowDataArray.value.filter((item) => item.id !== id)
        toast.success('Penyaluran berhasil dihapus')
        showDeleteModal.value = false
        deleteId.value = null
        try { window.dispatchEvent(new CustomEvent('penyaluran:changed')) } catch (e) {}
        refreshGrid()
      })
      .catch((err) => {
        console.error('Delete failed', err)
        toast.error('Gagal menghapus penyaluran')
      })
  } catch (err) {
    console.error('Delete error', err)
    toast.error('Gagal menghapus penyaluran')
  }
}

const cancelDelete = () => {
  showDeleteModal.value = false
  deleteId.value = null
}

// Handle export to Excel
const handleExportExcel = () => {
  const dataToExport = filteredData.value.map((item) => {
    const tanggal = new Date(item.tanggal)
    
    return {
      'Nama Program': item.namaProgram,
      'Jumlah Dana': new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
      }).format(item.jumlahDana),
      'PIC': item.pic,
      'Tanggal': tanggal.toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
      }),
    }
  })
  
  const worksheet = XLSX.utils.json_to_sheet(dataToExport)
  const workbook = XLSX.utils.book_new()
  XLSX.utils.book_append_sheet(workbook, worksheet, 'Penyaluran')
  
  const now = new Date()
  const filename = `Penyaluran_${now.toISOString().split('T')[0]}.xlsx`
  
  XLSX.writeFile(workbook, filename)
}

// Helper function to sort data
const sortData = (data: Array<any>, sortModel: any[]) => {
  if (!sortModel || sortModel.length === 0) {
    return data
  }
  
  const sortedData = [...data]
  sortModel.forEach((sort) => {
    const { colId, sort: sortDirection } = sort
    sortedData.sort((a, b) => {
      let aValue = a[colId]
      let bValue = b[colId]
      
      // Handle date sorting
      if (colId === 'tanggal') {
        aValue = new Date(aValue).getTime()
        bValue = new Date(bValue).getTime()
      } else if (colId === 'jumlahDana') {
        aValue = aValue || 0
        bValue = bValue || 0
      } else if (typeof aValue === 'string') {
        aValue = aValue.toLowerCase()
        bValue = bValue.toLowerCase()
      }
      
      if (aValue < bValue) {
        return sortDirection === 'asc' ? -1 : 1
      }
      if (aValue > bValue) {
        return sortDirection === 'asc' ? 1 : -1
      }
      return 0
    })
  })
  
  return sortedData
}

// Create datasource function for infinite scroll
const createDataSource = (): IDatasource => {
  return {
    getRows: (params: IGetRowsParams) => {
      setTimeout(() => {
        const start = params.startRow || 0
        const end = params.endRow || 0
        
        // Get filtered data
        let allData = filteredData.value
        
        // Apply sorting if sortModel is provided
        if (params.sortModel && params.sortModel.length > 0) {
          allData = sortData(allData, params.sortModel)
        }
        
        // Get the chunk of data for this page
        const rowsThisPage = allData.slice(start, end)
        
        // Check if there's more data
        let lastRow: number | undefined
        if (allData.length === 0) {
          lastRow = 0
        } else if (allData.length <= end) {
          lastRow = allData.length
        } else {
          lastRow = undefined
        }
        
        // Provide data to AG Grid
        params.successCallback(rowsThisPage, lastRow)
        // Hide loading overlay after first successful load
        isLoadingGrid.value = false
      }, 50)
    },
  }
}

// Infinite scroll datasource - create as ref for reactivity
const dataSource = ref<IDatasource>(createDataSource())

const refreshGrid = (scrollToTop = false) => {
  const newDataSource = createDataSource()
  dataSource.value.getRows = newDataSource.getRows

  nextTick(() => {
    const gridApi = (agGridRef.value as any)?.api
    if (gridApi) {
      try {
        gridApi.purgeInfiniteCache()
        gridApi.refreshInfiniteCache()

          // Show AG Grid's no-rows overlay when filtered data is empty,
          // otherwise hide any overlay.
          if (filteredData.value.length === 0) {
            try {
              gridApi.showNoRowsOverlay()
            } catch (e) {
              // some AG Grid builds may use different overlay API, ignore errors
            }
          } else {
            try {
              gridApi.hideOverlay()
            } catch (e) {
              // ignore
            }
          }

          if (scrollToTop) {
            window.setTimeout(() => {
              const inner = (agGridRef.value as any)?.api
              if (inner) inner.ensureIndexVisible(0, 'top')
            }, 100)
          }
      } catch (error) {
        console.error('Error refreshing cache:', error)
      }
    }
  })
}

// Set datasource after component is mounted
onMounted(() => {
  fetchUser()
  loadPenyalurans()
  loadProgramOptions()
  loadMyCredit()
  // listen for global changes when penyaluran is created/updated/deleted
  window.addEventListener('penyaluran:changed', () => {
    loadPenyalurans()
    loadMyCredit()
  })
  // listen for delete requests coming from grid cell renderers
  window.addEventListener('penyaluran:delete', (e: any) => {
    const id = e?.detail || null
    if (id) {
      deleteId.value = id
      showDeleteModal.value = true
    }
  })
})

// Store full meta for column mapping
const submissionTypeMeta = ref<any[]>([])

const loadMyCredit = async () => {
  try {
    // First fetch submission types from API
    const typesRes = await fetch('/admin/api/program-share-types/submission-types', {
      credentials: 'same-origin',
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    const typesJson = await typesRes.json()
    
    let types: Array<{value: string, label: string}> = []
    if (typesJson.success && Array.isArray(typesJson.data)) {
      submissionTypeMeta.value = typesJson.data
      types = typesJson.data.map((item: any) => ({ value: item.value, label: item.value }))
    } else {
      // Fallback to hardcoded if API fails
      types = [
        { value: 'Program', label: 'Program' },
        { value: 'Operasional', label: 'Operasional' },
        { value: 'Gaji Karyawan', label: 'Gaji Karyawan' },
      ]
    }
    
    // Update typeOptions for filter
    typeOptions.value = types
    
    // Fetch credits for each type in parallel
    const promises = types.map((t) => 
      fetch(`/admin/api/penyaluran/my-credit?type=${encodeURIComponent(t.value)}`, { 
        credentials: 'same-origin', 
        headers: { 'X-Requested-With': 'XMLHttpRequest' } 
      })
      .then(r => r.json())
      .catch(() => null)
    )
    const results = await Promise.all(promises)
    
    // Build dynamic credits array
    dynamicCredits.value = types.map((t, idx) => {
      const result = results[idx]
      const value = (result && result.success && result.data && typeof result.data.remaining !== 'undefined') 
        ? Number(result.data.remaining) || 0 
        : 0
      const formatted = new Intl.NumberFormat('id-ID', { 
        style: 'currency', 
        currency: 'IDR', 
        minimumFractionDigits: 0 
      }).format(value)
      
      return {
        type: t.value,
        label: t.label,
        value,
        formatted
      }
    })
    
    // Backward compatibility: set individual refs for first 3 items
    if (dynamicCredits.value.length > 0) creditProgram.value = dynamicCredits.value[0].value
    if (dynamicCredits.value.length > 1) creditOperasional.value = dynamicCredits.value[1].value
    if (dynamicCredits.value.length > 2) creditGaji.value = dynamicCredits.value[2].value
    
    // set combined myCredit as sum of all
    myCredit.value = dynamicCredits.value.reduce((sum, c) => sum + c.value, 0)
  } catch (err) {
    console.error('Error loading my credit', err)
  }
}

const loadProgramOptions = async () => {
  try {
    const res = await fetch('/admin/api/program?per_page=100', { credentials: 'same-origin' })
    const json = await res.json()
    if (!json.success) return
    const list = Array.isArray(json.data) ? json.data : (json.data && json.data.data) || []
    // include an 'All' option labeled 'Semua' at the top
    programOptions.value = [{ value: '', label: 'Semua' }, ...list.map((p: any) => ({ value: p.id, label: p.nama_program || p.nama || p.title || '' }))]
  } catch (err) {
    console.error('Failed to load program options', err)
  }
}

// Clear debounce timer on component unmount
onUnmounted(() => {
  if (filterDebounceTimer) {
    clearTimeout(filterDebounceTimer)
  }
  window.removeEventListener('penyaluran:changed', () => {
    loadPenyalurans()
    loadMyCredit()
  })
  window.removeEventListener('penyaluran:delete', () => {})
})

// Handle sort changes
const onSortChanged = () => {
  refreshGrid()
}

// Debounce timer for filter
let filterDebounceTimer: ReturnType<typeof setTimeout> | null = null

// Watch for filter changes and refresh grid with debounce
watch([filterProgramId, filterPIC, filterTanggalRange, filterType], () => {
  // Clear existing timer
  if (filterDebounceTimer) {
    clearTimeout(filterDebounceTimer)
  }
  
  // Debounce filter update to prevent flickering
  filterDebounceTimer = setTimeout(() => {
    refreshGrid(true)
  }, 300) // 300ms debounce delay to prevent flickering
})

// No-op: credits updated via event or onMounted; keep function available to refresh UI

// Reset filter
const resetFilter = () => {
  filterProgramId.value = ''
  filterPIC.value = ''
  filterTanggalRange.value = ''
  filterType.value = ''
}
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

/* Ensure row animations work for sorting */
.ag-theme-alpine .ag-row {
  transition: transform 0.3s ease, opacity 0.3s ease;
}

.ag-theme-alpine-dark .ag-row {
  transition: transform 0.3s ease, opacity 0.3s ease;
}

</style>
