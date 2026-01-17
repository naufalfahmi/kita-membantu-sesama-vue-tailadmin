<template>
  <AdminLayout>
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
            <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M5 3h10v2H5V3zm0 12h10v2H5v-2zM3 7h14v6H3V7z" fill="currentColor"/>
            </svg>
            Export Excel
          </button>
        </div>
      </div>

      <!-- Filter Section: date-range, fundraiser, mitra -->
      <div class="mb-6 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3 lg:grid-cols-3">
          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Rentang Tanggal Transaksi</label>
            <div class="relative">
              <flat-pickr
                v-model="filterTanggalKeuangan"
                :config="flatpickrDateConfig"
                class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pl-4 pr-11 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                placeholder="Pilih rentang tanggal transaksi"
              />
            </div>
          </div>
          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Fundraiser</label>
            <AsyncSearchableSelect
              v-model="filterFundraiser"
              :fetch-url="'/admin/api/karyawan'"
              :label-key="'nama'"
              :value-key="'id'"
              placeholder="Pilih Fundraiser"
              :allow-clear="true"
              :include-all="true"
              all-label="Semua Fundraiser"
              :params="{ role: 'fundraiser' }"
            />
          </div>
          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Mitra</label>
            <AsyncSearchableSelect
              v-model="filterMitra"
              :fetch-url="'/admin/api/mitra'"
              :label-key="'nama'"
              :value-key="'id'"
              placeholder="Pilih Mitra"
              :allow-clear="true"
              :include-all="true"
              all-label="Semua Mitra"
            />
          </div>
        </div>
        <div class="mt-4">
          <button @click="resetFilter" class="h-11 rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">Reset Filter</button>
        </div>
      </div>

      <!-- Loading indicator -->
      <div v-if="isLoading" class="mb-6 flex items-center justify-center gap-3 rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.02]">
        <svg class="animate-spin h-5 w-5 text-brand-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span class="text-sm font-medium text-gray-700 dark:text-gray-400">Memuat data...</span>
      </div>

      <!-- Summary boxes (dynamic, update with date filter) -->
      <div v-if="!isLoading" class="mb-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        <div v-for="(box, idx) in summaryBoxes" :key="`summary-${idx}`" class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.02]">
          <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ box.label }}</p>
          <p class="mt-2 text-lg font-semibold text-gray-800 dark:text-white">{{ formatCurrency(box.value) }}</p>
        </div>
      </div>

      <!-- Per-Program Breakdown (form-style layout similar to JabatanForm) -->
      <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="mb-4 flex items-center justify-between">
          <h4 class="font-semibold text-gray-800 dark:text-white/90">Per-Program Breakdown</h4>
          <div class="flex items-center gap-3">
            <button @click="handleRefresh" class="h-9 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">Refresh</button>
            <button @click="handleExportExcel" class="h-9 rounded-lg bg-green-500 px-3 py-1.5 text-sm text-white hover:bg-green-600">Export Excel</button>
          </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-2">
          <div v-for="p in programsList" :key="p.program_id" class="rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="bg-gray-100 dark:bg-gray-800/50 px-4 py-3 border-b border-gray-200 dark:border-gray-700">
              <div class="flex items-center justify-between">
                <h4 class="font-semibold text-gray-800 dark:text-white/90">
                  {{ p.program_name || 'Program' }}
                  <span v-if="p.tipe_name" class="text-sm font-normal text-gray-600 dark:text-gray-400">({{ p.tipe_name }})</span>
                </h4>
              </div>
            </div>
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-800/30">
                  <tr>
                    <th class="sticky left-0 z-10 min-w-[200px] border-r border-gray-200 bg-gray-50 px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:border-gray-700 dark:bg-gray-800/30 dark:text-gray-300">Pembagian</th>
                    <th class="border-r border-gray-200 px-4 py-3 text-center text-xs font-semibold text-gray-700 dark:border-gray-700 dark:text-gray-300"><div class="flex flex-col items-center gap-1"><span>Total</span></div></th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                  <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/20">
                    <td class="sticky left-0 z-10 border-r border-gray-200 bg-white px-4 py-3 text-sm font-medium text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">Nominal Transaksi</td>
                    <td class="border-r border-gray-200 px-4 py-3 text-center dark:border-gray-700">{{ formatCurrency(p?.total_transaksi || 0) }}</td>
                  </tr>

                  <tr v-for="(k, idx) in activeShareKeys" :key="`share-row-${p.program_id}-${k}-${idx}`" class="hover:bg-gray-50 dark:hover:bg-gray-800/20">
                    <td class="sticky left-0 z-10 border-r border-gray-200 bg-white px-4 py-3 text-sm font-medium text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                      {{ shareTypeLabels.value?.[k] || getShareLabel(k, programsList.value) }}
                      <span v-if="p?.shares_meta && p.shares_meta[k] && (p.shares_meta[k].type === 'percentage' && p.shares_meta[k].value !== null)" class="text-xs text-gray-500 ml-2">({{ formatPercent(p.shares_meta[k].value) }})</span>
                      <span v-else-if="p?.shares_meta && p.shares_meta[k] && (p.shares_meta[k].type === 'nominal' && p.shares_meta[k].value !== null)" class="text-xs text-gray-500 ml-2">({{ formatCurrency(p.shares_meta[k].value) }})</span>
                    </td>
                    <td class="border-r border-gray-200 px-4 py-3 text-center dark:border-gray-700">{{ formatCurrency(p?.[k] || 0) }}</td>
                  </tr>

                  <tr v-if="computeUnallocated(p) > 0" class="hover:bg-gray-50 dark:hover:bg-gray-800/20">
                    <td class="sticky left-0 z-10 border-r border-gray-200 bg-white px-4 py-3 text-sm font-medium text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">Sisa Belum Teralokasi</td>
                    <td class="border-r border-gray-200 px-4 py-3 text-center dark:border-gray-700">{{ formatCurrency(computeUnallocated(p)) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <ConfirmModal
      :isOpen="showDeleteModal"
      title="Hapus Keuangan"
      message="Apakah Anda yakin ingin menghapus data keuangan ini? Tindakan ini tidak dapat dibatalkan."
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
import ConfirmModal from '@/components/common/ConfirmModal.vue'
import AsyncSearchableSelect from '@/components/forms/AsyncSearchableSelect.vue'
import { useToast } from 'vue-toastification'
import { useAuth } from '@/composables/useAuth'


interface KeuanganRow {
  id: string
  pic: string
  jumlah: number
  kantorCabang: string
  tanggalKeuangan: string
}

const route = useRoute()
const router = useRouter()
const currentPageTitle = ref<string>(String(route.meta.title || 'Keuangan'))
const agGridRef = ref<InstanceType<typeof AgGridVue> | null>(null)
const gridApiRef = ref<any>(null)
const gridColumnApiRef = ref<any>(null)
const toast = useToast()
const { fetchUser, hasPermission, isAdmin } = useAuth()
const canCreate = computed(() => isAdmin() || hasPermission('create keuangan'))
const canUpdate = computed(() => isAdmin() || hasPermission('update keuangan'))
const canDelete = computed(() => isAdmin() || hasPermission('delete keuangan'))
const showDeleteModal = ref(false)
const deleteId = ref<string | null>(null)
const isLoading = ref(false)

// Flatpickr configuration for date
const flatpickrDateConfig = {
  dateFormat: 'Y-m-d',
  altInput: true,
  altFormat: 'd/m/Y',
  wrap: false,
  mode: 'range',
}

// Column definitions for program-shares summary
const columnDefs = [
  { headerName: 'Total Transaksi', field: 'total_transaksi', flex: 1, valueFormatter: currencyFormatter },
  {
    headerName: 'Nama Program',
    children: [
      { headerName: 'DP', field: 'dp', valueFormatter: currencyFormatter },
      { headerName: 'Operasional (OPS 1)', field: 'ops_1', valueFormatter: currencyFormatter },
      { headerName: 'Gaji Karyawan (OPS 2)', field: 'ops_2', valueFormatter: currencyFormatter },
      { headerName: 'Program', field: 'program', valueFormatter: currencyFormatter },
      { headerName: 'Fee Mitra', field: 'fee_mitra', valueFormatter: currencyFormatter },
      { headerName: 'Bonus', field: 'bonus', valueFormatter: currencyFormatter },
      { headerName: 'Championship', field: 'championship', valueFormatter: currencyFormatter },
    ],
  },
]

// Default column definition
const defaultColDef = {
  resizable: true,
  sortable: true,
  filter: false,
}
// enforce a minimum column width so columns don't collapse too small
defaultColDef.minWidth = 120
defaultColDef.cellClass = 'text-sm'

// Currency formatter used by column defs
function currencyFormatter(params: any) {
  if (params && params.value !== undefined && params.value !== null) {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(params.value)
  }
  return ''
}

// Helper used in template to format raw numbers
const formatCurrency = (v: number) => {
  return currencyFormatter({ value: v })
}

// Format percentage values: show no decimals for whole numbers (e.g. 20 -> "20%"),
// otherwise show up to 2 decimals (e.g. 20.23 -> "20.23%").
const formatPercent = (raw: any) => {
  if (raw === null || raw === undefined) return ''
  const num = Number(raw)
  if (!Number.isFinite(num)) return `${String(raw)}%`
  if (Number.isInteger(num)) return `${num}%`
  return `${parseFloat(num.toFixed(2))}%`
}

// Map share key to header label
function toHeader(key: string) {
  switch ((key || '').toString()) {
    case 'dp':
      return 'DP'
    case 'ops_1':
      return 'OPS 1'
    case 'ops_2':
      return 'OPS 2'
    case 'program':
      return 'Program'
    case 'fee_mitra':
      return 'Fee Mitra'
    case 'bonus':
      return 'Bonus'
    case 'championship':
      return 'Championship'
    default:
      return String(key)
  }
}

// Normalize keys by removing custom_ prefix
function stripKey(key: any) {
  return String(key || '').replace(/^custom_/, '')
}

// Get a friendly label for a share key. Prefer: server-provided mapping, then program metadata, then humanized fallback.
function getShareLabel(key: string, programs: any[]) {
  const cleaned = stripKey(key)

  // Prefer global mapping if available
  try {
    if (shareTypeLabels.value && (shareTypeLabels.value[key] || shareTypeLabels.value[cleaned])) {
      return shareTypeLabels.value[key] || shareTypeLabels.value[cleaned]
    }
  } catch (e) { /* ignore */ }

  // Known header names (dp, ops_1, etc.)
  const basic = toHeader(cleaned)
  if (basic && basic !== String(cleaned)) return basic

  try {
    for (const p of (programs || [])) {
      if (!p) continue
      // try exact key then cleaned key
      const meta = p?.shares_meta?.[key] || p?.shares_meta?.[cleaned]
      if (meta) {
        if (typeof meta === 'string') return meta
        if (meta.label || meta.name || meta.title) return meta.label || meta.name || meta.title
      }
      // Some payloads may provide label mappings on the program object directly
      if (p && p.share_labels && (p.share_labels[key] || p.share_labels[cleaned])) return p.share_labels[key] || p.share_labels[cleaned]
    }
  } catch (e) { /* ignore */ }

  // Fallback: humanize cleaned key
  let label = String(cleaned).replace(/_/g, ' ')
  label = label.replace(/\b\w/g, (c) => c.toUpperCase())
  return label
}

// Pinned bottom rows for totals
const pinnedBottomRowData = ref<any[]>([])

// Programs and share keys for form-style layout
const programsList = ref<any[]>([])
const activeShareKeys = ref<string[]>([])

// Global mapping of share key -> friendly name provided by the API
const shareTypeLabels = ref<Record<string,string>>({})

// Summary boxes shown above the grid (dynamically built from program summaries)
const summaryBoxes = ref<any[]>([])

// Sample data - generate 200 items for infinite scroll testing
const generateRowData = (): KeuanganRow[] => {
  const pics = [
    'Ahmad Hidayat', 'Siti Nurhaliza', 'Budi Santoso', 'Dewi Lestari', 'Eko Prasetyo',
    'Fitri Handayani', 'Guntur Wibowo', 'Hesti Rahayu', 'Indra Wijaya', 'Joko Susilo',
    'Kartika Putri', 'Lukman Hakim', 'Maya Sari', 'Nanda Pratama', 'Olivia Wijaya',
  ]
  
  const kantorCabangs = [
    'Kantor Pusat Jakarta', 'Kantor Cabang Bandung', 'Kantor Cabang Surabaya',
    'Kantor Cabang Yogyakarta', 'Kantor Cabang Medan', 'Kantor Cabang Makassar',
    'Kantor Cabang Semarang', 'Kantor Cabang Palembang', 'Kantor Cabang Denpasar',
    'Kantor Cabang Banjarmasin',
  ]
  
  const rowData: KeuanganRow[] = []
  const startDate = new Date('2024-01-01')
  
  for (let i = 1; i <= 200; i++) {
    const picIndex = (i - 1) % pics.length
    const kantorIndex = (i - 1) % kantorCabangs.length
    const date = new Date(startDate)
    date.setDate(date.getDate() + (i - 1) * 2) // Increment by 2 days for each entry
    
    // Random amount between 25M and 100M
    const jumlah = Math.floor(Math.random() * 75000000) + 25000000
    
    rowData.push({
      id: i.toString(),
      pic: pics[picIndex],
      jumlah: jumlah,
      kantorCabang: kantorCabangs[kantorIndex],
      tanggalKeuangan: date.toISOString().split('T')[0],
    })
  }
  
  return rowData
}

const rowDataArray = ref<KeuanganRow[]>(generateRowData())

// Filter state
const filterTanggalKeuangan = ref('')
const filterFundraiser = ref('')
const filterMitra = ref('')

// Filtered data is the current rowDataArray (server-side filtered by date range)
const filteredData = computed(() => rowDataArray.value)

// Show empty state when filtered data is empty
const showEmptyState = computed(() => filteredData.value.length === 0)

// Handle add button
const handleAdd = () => {
  router.push('/keuangan/keuangan/new')
}

// Handle edit
const handleEdit = (id: string) => {
  router.push(`/keuangan/keuangan/${id}/edit`)
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

  rowDataArray.value = rowDataArray.value.filter((item) => item.id !== deleteId.value)
  toast.success('Data keuangan berhasil dihapus')
  showDeleteModal.value = false
  deleteId.value = null
  refreshGrid()
}

const cancelDelete = () => {
  showDeleteModal.value = false
  deleteId.value = null
}

// Helper: compute unallocated amount for a program safely
const computeUnallocated = (p: any) => {
  if (!p) return 0
  const total = Number(p.total_transaksi || 0)
  const allocated = (activeShareKeys.value || []).reduce((s: number, k: string) => {
    const v = Number(p?.[k] || 0)
    return s + (Number.isFinite(v) ? v : 0)
  }, 0)
  return Math.max(0, total - allocated)
}

// Refresh handler for form layout
const handleRefresh = async () => {
  try {
    await fetchSummary()
    refreshGrid(true)
    toast.success('Data berhasil diperbarui')
  } catch (e) {
    toast.error('Gagal memperbarui data')
  }
}

// Handle export to Excel - export exactly the AG Grid view including pinned rows
const handleExportExcel = () => {
  // Export the current Per-Program Breakdown table
  const headers: string[] = ['Program', 'Nominal Transaksi', ...activeShareKeys.value.map((k: string) => (shareTypeLabels.value[k] || toHeader(k)))]

  const aoa: any[] = []
  aoa.push(headers)

  // rows per program
  programsList.value.forEach((p: any) => {
    const row: any[] = []
    row.push(p?.program_name || '')
    row.push(Number(p?.total_transaksi || 0))
    activeShareKeys.value.forEach((k: string) => {
      row.push(Number(p?.[k] || 0))
    })
    aoa.push(row)
  })

  // add totals row
  const totalsRow: any[] = []
  totalsRow.push('TOTAL')
  const totalNominal = programsList.value.reduce((s: number, p: any) => s + Number(p?.total_transaksi || 0), 0)
  totalsRow.push(totalNominal)
  activeShareKeys.value.forEach((k: string) => {
    const sum = programsList.value.reduce((s: number, p: any) => s + Number(p?.[k] || 0), 0)
    totalsRow.push(sum)
  })
  aoa.push(totalsRow)

  const worksheet = XLSX.utils.aoa_to_sheet(aoa)
  const workbook = XLSX.utils.book_new()
  XLSX.utils.book_append_sheet(workbook, worksheet, 'Keuangan')
  const now = new Date()
  const filename = `Keuangan_${now.toISOString().split('T')[0]}.xlsx`
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
      if (colId === 'tanggalKeuangan') {
        aValue = new Date(aValue).getTime()
        bValue = new Date(bValue).getTime()
      } else if (colId === 'jumlah') {
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
      }, 50)
    },
  }
}

// Infinite scroll datasource - create as ref for reactivity
const dataSource = ref<IDatasource>(createDataSource())

// Rows will be populated from program-shares summary endpoint
const fetchSummary = async () => {
  isLoading.value = true
  try {
    // include optional date range and filters
    let url = '/admin/api/keuangan/program-shares-summary'
    try {
      const fr = filterTanggalKeuangan.value
      let startDate: any = null
      let endDate: any = null
      if (fr) {
        if (Array.isArray(fr)) {
          startDate = fr[0] ? (fr[0] instanceof Date ? fr[0].toISOString().split('T')[0] : String(fr[0])) : null
          endDate = fr[1] ? (fr[1] instanceof Date ? fr[1].toISOString().split('T')[0] : String(fr[1])) : null
        } else if (typeof fr === 'string') {
          const sep = fr.includes(' to ') ? ' to ' : (fr.includes(' - ') ? ' - ' : '')
          if (sep) {
            const parts = fr.split(sep)
            startDate = parts[0]?.trim() || null
            endDate = parts[1]?.trim() || null
          } else {
            startDate = fr || null
            endDate = fr || null
          }
        }
      }
      const params: any = {}
      if (startDate) params.start_date = startDate
      if (endDate) params.end_date = endDate
      if (filterFundraiser.value) params.fundraiser_id = filterFundraiser.value
      if (filterMitra.value) params.mitra_id = filterMitra.value
      const qp = new URLSearchParams(params).toString()
      if (qp) url += `?${qp}`
    } catch (e) { /* ignore parsing errors and call without range */ }
    const res = await fetch(url, { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    const payload = await res.json()
    if (!payload.success) {
      toast.error('Gagal memuat ringkasan program')
      return
    }

    // Pivot: build group columns per program and rows per share key
    const programs = payload.data.rows || []
    const shareKeys = payload.data.columns && payload.data.columns.length ? payload.data.columns : ['dp','ops_1','ops_2','program','fee_mitra','bonus','championship']

    // Save into reactive refs used by the form layout
    programsList.value = (programs || []).filter(Boolean)
    activeShareKeys.value = shareKeys

    // Build dynamic columnDefs: first column is a summary label (left pinned), then one group column per program with children for each share key
    const dynamicCols: any[] = []
    dynamicCols.push({ headerName: 'Ringkasan', field: 'summary_label', pinned: 'left', width: 250 })
      // nominal transaksi column next to summary
      dynamicCols.push({ headerName: 'Nominal Transaksi Keseluruhan', field: 'nominal', pinned: 'left', width: 160, valueFormatter: currencyFormatter, resizable: true })

    // Global mapping of share key -> friendly name provided by the API
    shareTypeLabels.value = (payload.data && payload.data.share_type_labels) ? payload.data.share_type_labels : {}

    programs.forEach((p: any) => {
      if (!p) return
      // build children starting with per-program nominal transaksi
      const children: any[] = []
      children.push({ headerName: 'Nominal Transaksi', field: `p_${p.program_id || p.id}_nominal`, valueFormatter: currencyFormatter, resizable: true, width: 160 })
      const shareChildren = shareKeys.map((k: string) => {
        // include share meta (percentage/nominal) in the child header, not in the program group header
        let childMeta = ''
        try {
          const sharesMeta = p.shares_meta || {}
          const meta = sharesMeta[k] || sharesMeta[k.toString()] || null
          if (meta) {
            if (meta.type === 'percentage' && meta.value !== null && meta.value !== undefined) {
              childMeta = ` (${formatPercent(meta.value)})`
            } else if (meta.type === 'nominal' && meta.value !== null && meta.value !== undefined) {
              childMeta = ` (${new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(meta.value)})`
            }
          }
        } catch (e) { /* ignore */ }

        const labelName = shareTypeLabels.value[k] || getShareLabel(k, programs)

        return {
          headerName: `${labelName}${childMeta}`,
          field: `p_${p.program_id || p.id}_${k}`,
          valueFormatter: currencyFormatter,
          resizable: true,
          minWidth: 120,
        }
      })

      // append share children after nominal
      children.push(...shareChildren)

      const header = `${p.program_name || p.nama_program || p.nama || 'Program'}`
      dynamicCols.push({ headerName: header, children })
    })

    columnDefs.splice(0, columnDefs.length, ...dynamicCols)

    // Ensure AG Grid updates with new columns and data
    nextTick(() => {
      const gridApi = gridApiRef.value || (agGridRef.value as any)?.api
      if (gridApi) {
        try {
          gridApi.setColumnDefs(columnDefs)
          // If using infinite row model, set datasource, otherwise set row data
          try {
            if (gridApi.setDatasource && dataSource.value) {
              gridApi.setDatasource(dataSource.value)
            } else {
              gridApi.setRowData(rowDataArray.value)
            }
          } catch (e) {
            // fallback
            try { gridApi.setRowData(rowDataArray.value) } catch (err) { /* ignore */ }
          }
          gridApi.setPinnedBottomRowData(pinnedBottomRowData.value)
          // adjust column sizes
          try { gridApi.sizeColumnsToFit() } catch (e) { /* ignore */ }
          // give the grid a moment to render then autosize columns to content
          setTimeout(() => {
            try { autoSizeAllColumns() } catch (err) { /* ignore */ }
          }, 80)
        } catch (e) {
          console.error('Failed to apply dynamic columns to grid', e)
        }
      }
    })

    // Build a single totals row (summary across the selected date range)
    const totalsRow: any = { summary_label: 'TOTAL' }
    // total nominal can be taken from program totals
    const totalNominal = programs.reduce((s: number, p: any) => s + Number(p?.total_transaksi || 0), 0)

    programs.forEach((p: any) => {
      if (!p) return
      const nominalFld = `p_${p.program_id || p.id}_nominal`
      totalsRow[nominalFld] = Number(p.total_transaksi || 0)
      shareKeys.forEach((k: string) => {
        const fld = `p_${p.program_id || p.id}_${k}`
        totalsRow[fld] = Number(p?.[k] || 0)
      })
    })

    totalsRow.nominal = totalNominal

    // Build summary boxes: overall nominal and aggregated per-share totals
    const boxes: any[] = []
    boxes.push({ label: 'Nominal Keseluruhan Transaksi', value: totalNominal })

    // Include aggregated totals for each share key
    // Note: These represent allocated amounts per share type across all programs
    let totalSharesSum = 0
    shareKeys.forEach((k: string) => {
      const sum = programs.reduce((s: number, p: any) => s + Number(p?.[k] || 0), 0)
      const labelName = shareTypeLabels.value[k] || getShareLabel(k, programs)
      boxes.push({ label: `Nominal All ${labelName}`, value: sum })
      totalSharesSum += sum
    })
    
    // Calculate unallocated (remainder)
    const unallocated = totalNominal - totalSharesSum
    if (unallocated > 0) {
      boxes.push({ label: 'Sisa Belum Teralokasi', value: unallocated })
    }

    summaryBoxes.value = boxes

    // single row dataset (summary)
    rowDataArray.value = [totalsRow]

    // don't use pinned rows for this summary view
    pinnedBottomRowData.value = []

    // refresh grid data
    refreshGrid(true)
  } catch (e) {
    console.error(e)
    toast.error('Terjadi kesalahan saat memuat ringkasan program')
  } finally {
    isLoading.value = false
  }
}

const refreshGrid = (scrollToTop = false) => {
  nextTick(() => {
    const gridApi = gridApiRef.value || (agGridRef.value as any)?.api
    if (gridApi) {
      try {
        // For infinite row model, purge cache so grid will request fresh blocks
        try {
          if (gridApi.purgeInfiniteCache) {
            gridApi.purgeInfiniteCache()
          } else if (gridApi.setDatasource && dataSource.value) {
            gridApi.setDatasource(dataSource.value)
          } else {
            gridApi.setRowData(rowDataArray.value)
          }
        } catch (e) { /* ignore */ }

        gridApi.setPinnedBottomRowData(pinnedBottomRowData.value)

        if (scrollToTop) {
          window.setTimeout(() => {
            const innerApi = gridApiRef.value || (agGridRef.value as any)?.api
            if (innerApi) {
              try { innerApi.ensureIndexVisible(0, 'top') } catch (e) { /* ignore */ }
            }
          }, 100)
        }
      } catch (error) {
        console.error('Error refreshing grid:', error)
      }
    }
  })
}

// Set datasource after component is mounted
onMounted(async () => {
  await fetchUser()
  await fetchSummary()
  refreshGrid()
})

// AG Grid lifecycle handlers
function onGridReady(params: any) {
  gridApiRef.value = params.api
  gridColumnApiRef.value = params.columnApi
  // For client-side data we set the row data directly when the grid is ready
  try {
    if (params.api.setRowData) params.api.setRowData(rowDataArray.value)
  } catch (e) {
    /* ignore */
  }
}

function autoSizeAllColumns() {
  const colApi = gridColumnApiRef.value
  if (!colApi) return
  const allCols = colApi.getAllColumns()
  if (!allCols || !allCols.length) return
  const colIds = allCols.map((c: any) => c.getColId())
  try {
    colApi.autoSizeColumns(colIds, false)
  } catch (e) {
    // fallback: ensure columns fit container
    try { gridApiRef.value?.sizeColumnsToFit() } catch (err) { /* ignore */ }
  }
}

function onFirstDataRendered(params: any) {
  try { params.api.sizeColumnsToFit() } catch (e) { /* ignore */ }
  // then auto-size by content where possible
  autoSizeAllColumns()
}

// Clear debounce timer on component unmount
onUnmounted(() => {
  if (filterDebounceTimer) {
    clearTimeout(filterDebounceTimer)
  }
})

// Handle sort changes
const onSortChanged = () => {
  refreshGrid()
}

// Debounce timer for filter
let filterDebounceTimer: ReturnType<typeof setTimeout> | null = null

// Watch for filter changes and refresh grid with debounce
watch([filterTanggalKeuangan, filterFundraiser, filterMitra], () => {
  // Clear existing timer
  if (filterDebounceTimer) {
    clearTimeout(filterDebounceTimer)
  }

  // Debounce filter update to prevent flickering and refetch summary when filters change
  filterDebounceTimer = setTimeout(async () => {
    try {
      await fetchSummary()
    } catch (e) { /* ignore */ }
    refreshGrid(true)
  }, 300) // 300ms debounce delay to prevent flickering
})
// Reset filter
const resetFilter = () => {
  filterTanggalKeuangan.value = ''
  filterFundraiser.value = ''
  filterMitra.value = ''
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
