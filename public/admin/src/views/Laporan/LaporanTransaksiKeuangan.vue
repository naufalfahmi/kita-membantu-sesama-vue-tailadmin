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

      <!-- Filter Section: only date-range picker -->
      <div class="mb-6 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-1 lg:grid-cols-1">
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
        </div>
        <div class="mt-4">
          <button @click="resetFilter" class="h-11 rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">Reset Filter</button>
        </div>
      </div>

      <div class="relative" style="width: 100%; height: 450px;">
        <div class="ag-theme-alpine dark:ag-theme-alpine-dark" style="width: 100%; height: 100%;">
              <ag-grid-vue
                ref="agGridRef"
                @grid-ready="onGridReady"
                @first-data-rendered="onFirstDataRendered"
                class="ag-theme-alpine"
                style="width: 100%; height: 100%;"
            :columnDefs="columnDefs"
            :defaultColDef="defaultColDef"
            :pinnedBottomRowData="pinnedBottomRowData"
            :rowModelType="'infinite'"
            :cacheBlockSize="50"
            :maxBlocksInCache="10"
              :suppressSorting="false"
              theme="legacy"
              :animateRows="true"
              :suppressHorizontalScroll="false"
            @sortChanged="onSortChanged"
          />
        </div>
        
        <!-- Custom empty state overlay -->
        <div
          v-if="showEmptyState"
          class="absolute inset-0 flex flex-col items-center justify-center bg-white dark:bg-gray-900 rounded-lg z-50 pointer-events-none"
          style="position: absolute; top: 0; left: 0; right: 0; bottom: 0;"
        >
          <svg class="w-16 h-16 text-gray-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path
              v-if="filterPIC || filterKantorCabang || filterTanggalKeuangan || filterJumlahMin || filterJumlahMax"
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
            ></path>
            <path
              v-else
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
            ></path>
          </svg>
                  <p class="text-gray-600 dark:text-gray-400 text-lg font-medium mb-1">
                    {{ filterTanggalKeuangan ? 'Tidak ada data ditemukan' : 'Tidak ada data' }}
                  </p>
                  <p class="text-gray-500 dark:text-gray-500 text-sm">
                    {{ filterTanggalKeuangan ? 'Coba ubah filter pencarian Anda' : 'Belum ada data yang tersedia' }}
                  </p>
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

// Pinned bottom rows for totals
const pinnedBottomRowData = ref<any[]>([])

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

// Filter state (only date-range)
const filterTanggalKeuangan = ref('')

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

// Handle export to Excel - export exactly the AG Grid view including pinned rows
const handleExportExcel = () => {
  // Build flattened fields and a two-row header with merges for program groups
  const fields: string[] = []
  const headerRow1: any[] = []
  const headerRow2: any[] = []
  const merges: any[] = []

  // iterate top-level columnDefs in order
  let colIndex = 0
  columnDefs.forEach((c: any) => {
    if (c.field) {
      // single column (e.g., Tanggal Transaksi, Nominal Transaksi) -> merge vertically across 2 rows
      const title = c.headerName || c.field
      headerRow1.push(title)
      headerRow2.push('')
      // merge rows 0..1 at this column
      merges.push({ s: { r: 0, c: colIndex }, e: { r: 1, c: colIndex } })
      fields.push(c.field)
      colIndex += 1
    } else if (c.children && Array.isArray(c.children)) {
      const groupTitle = c.headerName || ''
      const childCount = c.children.length
      // place group title spanning childCount columns in headerRow1
      for (let i = 0; i < childCount; i++) {
        headerRow1.push(i === 0 ? groupTitle : '')
      }
      // merge the group title across the children columns
      merges.push({ s: { r: 0, c: colIndex }, e: { r: 0, c: colIndex + childCount - 1 } })

      // second header row contains child headers
      c.children.forEach((ch: any) => {
        headerRow2.push(ch.headerName || ch.field)
        fields.push(ch.field)
        colIndex += 1
      })
    }
  })

  // Build AOA rows: headerRow1, headerRow2, then data rows
  const aoa: any[] = []
  aoa.push(headerRow1)
  aoa.push(headerRow2)

  // add data rows in display order
  rowDataArray.value.forEach((r: any) => {
    const row: any[] = []
    fields.forEach((f: string) => {
      const raw = r[f]
      row.push((typeof raw === 'number') ? raw : (raw ?? ''))
    })
    aoa.push(row)
  })

  // append pinned rows
  if (pinnedBottomRowData.value && pinnedBottomRowData.value.length) {
    pinnedBottomRowData.value.forEach((pr: any) => {
      const prow: any[] = []
      fields.forEach((f: string) => {
        const raw = pr[f]
        prow.push((typeof raw === 'number') ? raw : (raw ?? ''))
      })
      aoa.push(prow)
    })
  }

  const worksheet = XLSX.utils.aoa_to_sheet(aoa)
  // apply merges
  worksheet['!merges'] = worksheet['!merges'] || []
  merges.forEach((m: any) => worksheet['!merges'].push(m))

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
  try {
    // include optional date range from filterTanggalKeuangan
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
      const qp = new URLSearchParams(params).toString()
      if (qp) url += `?${qp}`
    } catch (e) { /* ignore parsing errors and call without range */ }
    const res = await fetch(url, { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    const payload = await res.json()
    if (!payload.success) {
      toast.error('Gagal memuat ringkasan program')
      return
    }

    // Extract share type labels from API response
    const shareTypeLabels: Record<string, string> = (payload.data && payload.data.share_type_labels) ? payload.data.share_type_labels : {}

    // Pivot: build group columns per program and rows per share key
    const programs = payload.data.rows || []
    const shareKeys = payload.data.columns && payload.data.columns.length ? payload.data.columns : ['dp','ops_1','ops_2','program','fee_mitra','bonus','championship']

    // Build dynamic columnDefs: first column is 'Tanggal Transaksi' (left pinned), then one group column per program with children for each share key
    const dynamicCols: any[] = []
    dynamicCols.push({ headerName: 'Tanggal Transaksi', field: 'tanggal', pinned: 'left', width: 250 })
      // nominal transaksi column next to tanggal
      dynamicCols.push({ headerName: 'Nominal Transaksi', field: 'nominal', pinned: 'left', width: 160, valueFormatter: currencyFormatter, resizable: true })

    programs.forEach((p: any) => {
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
              const num = Number(meta.value)
              if (!Number.isFinite(num)) {
                childMeta = ` (${meta.value}%)`
              } else {
                // show integer when whole, otherwise show up to 2 decimals
                childMeta = Number.isInteger(num) ? ` (${num}%)` : ` (${num % 1 === 0 ? num : parseFloat(num.toFixed(2))}%)`
              }
            } else if (meta.type === 'nominal' && meta.value !== null && meta.value !== undefined) {
              childMeta = ` (${new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(meta.value)})`
            }
          }
        } catch (e) { /* ignore */ }

        return {
          headerName: `${shareTypeLabels[k] || toHeader(k)}${childMeta}`,
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

    // Build rows: aggregate transaksi per tanggal_transaksi (one row per date)
    const transaksis = payload.data.transaksis || []
    const rowsByDate: Record<string, any> = {}
    const formatDateDisplay = (d: string) => {
      if (!d) return ''
      const dt = new Date(d)
      if (isNaN(dt.getTime())) return d
      return dt.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
    }

    transaksis.forEach((t: any) => {
      const rawDate = (t.tanggal_transaksi || t.tanggal || t.date || '').toString()
      const dateKey = rawDate
      if (! rowsByDate[dateKey]) {
        rowsByDate[dateKey] = { tanggal: formatDateDisplay(rawDate), kode: null, nominal: 0 }
        // initialize program-share fields and per-program nominal
        programs.forEach((p: any) => {
          // per-program nominal
          rowsByDate[dateKey][`p_${p.program_id || p.id}_nominal`] = 0
          shareKeys.forEach((k: string) => {
            rowsByDate[dateKey][`p_${p.program_id || p.id}_${k}`] = 0
          })
        })
      }

      const row = rowsByDate[dateKey]
      row.kode = row.kode || t.kode || null
      row.nominal = (row.nominal || 0) + (t.nominal || 0)

      programs.forEach((p: any) => {
        shareKeys.forEach((k: string) => {
          const fld = `p_${p.program_id || p.id}_${k}`
          const transField = `p_${p.program_id || p.id}`
          if (k === 'program' && (t[transField] !== undefined)) {
            row[fld] += (t[transField] || 0)
          } else {
            row[fld] += (t[fld] || 0)
          }
        })
        // accumulate per-program nominal (transaction nominal belongs to the program)
        try {
          const progId = p.program_id || p.id
          if (t.program_id && String(t.program_id) === String(progId)) {
            row[`p_${progId}_nominal`] = (row[`p_${progId}_nominal`] || 0) + (t.nominal || 0)
          }
        } catch (e) { /* ignore */ }
      })
    })

    const pivotRows = Object.values(rowsByDate)
    rowDataArray.value = pivotRows

    // compute total nominal across pivot rows for pinned TOTAL
    const totalNominal = pivotRows.reduce((s: number, r: any) => s + (r.nominal || 0), 0)

    // Pinned totals per program and pinned disbursed per program (children fields)
    const totalsRow: any = { tanggal: 'TOTAL' }
    const disbRow: any = { tanggal: 'DISBURSED TOTALS' }
    const disbByProgram = (payload.data && payload.data.disbursed_totals_by_program) ? payload.data.disbursed_totals_by_program : {}
    programs.forEach((p: any) => {
      // totals for per-program nominal
      const nominalFld = `p_${p.program_id || p.id}_nominal`
      totalsRow[nominalFld] = p.total_transaksi || 0
      disbRow[nominalFld] = 0
      shareKeys.forEach((k: string) => {
        const fld = `p_${p.program_id || p.id}_${k}`
        totalsRow[fld] = p[k] || 0
        // prefer per-program-per-share disbursed value, fallback to per-program total mapped to 'program' share
        const perShareKey = fld
        const perProgramKey = `p_${p.program_id || p.id}`
        let disbVal = 0
        if (disbByProgram && disbByProgram[perShareKey] !== undefined) disbVal = disbByProgram[perShareKey]
        else if (k === 'program' && disbByProgram && disbByProgram[perProgramKey] !== undefined) disbVal = disbByProgram[perProgramKey]
        disbRow[fld] = disbVal || 0
      })
    })

    // set total nominal on totalsRow
    totalsRow.nominal = totalNominal

    pinnedBottomRowData.value = [totalsRow, disbRow]

    // refresh grid datasource
    refreshGrid(true)
  } catch (e) {
    console.error(e)
    toast.error('Terjadi kesalahan saat memuat ringkasan program')
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
  // attach infinite datasource when grid is ready
  try {
    const ds = dataSource.value || createDataSource()
    if (params.api.setDatasource) params.api.setDatasource(ds as any)
  } catch (e) {
    console.error('Failed to set datasource for infinite row model', e)
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
watch([filterTanggalKeuangan], () => {
  // Clear existing timer
  if (filterDebounceTimer) {
    clearTimeout(filterDebounceTimer)
  }

  // Debounce filter update to prevent flickering and refetch summary when date range changes
  filterDebounceTimer = setTimeout(async () => {
    try {
      await fetchSummary()
    } catch (e) { /* ignore */ }
    refreshGrid(true)
  }, 300) // 300ms debounce delay to prevent flickering
})
// Reset filter (only date-range)
const resetFilter = () => {
  filterTanggalKeuangan.value = ''
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
