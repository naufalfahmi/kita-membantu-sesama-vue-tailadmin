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
      </div>

      <!-- Filter Section -->
      <div class="mb-6 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3 lg:grid-cols-4">
          <div class="flex-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Nama / No Induk
            </label>
            <input
              type="text"
              v-model="filterSearch"
              placeholder="Cari nama atau no induk..."
              class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
          </div>
          <div class="flex-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Tanggal (Range)
            </label>
            <div class="relative">
              <FlatPickr
                v-model="filterTanggal"
                :config="flatpickrDateConfigRange"
                @on-change="handleFilterTanggalChange"
                class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-11 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                placeholder="Pilih rentang tanggal"
              />
              <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400 pointer-events-none">
                <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path fill-rule="evenodd" clip-rule="evenodd" d="M6.66659 1.5415C7.0808 1.5415 7.41658 1.87729 7.41658 2.2915V2.99984H12.5833V2.2915C12.5833 1.87729 12.919 1.5415 13.3333 1.5415C13.7475 1.5415 14.0833 1.87729 14.0833 2.2915V2.99984L15.4166 2.99984C16.5212 2.99984 17.4166 3.89527 17.4166 4.99984V7.49984V15.8332C17.4166 16.2754 17.3244 16.6993 17.0118 17.0118C16.6993 17.3244 16.2754 17.5 15.8333 17.5H4.58325C3.47868 17.5 2.58325 16.9377 2.58325 15.8332V7.49984V4.99984C2.58325 3.89527 3.47868 2.99984 4.58325 2.99984L5.91659 2.99984V2.2915C5.91659 1.87729 6.25237 1.5415 6.66659 1.5415ZM6.66659 4.49984H4.58325C4.30711 4.49984 4.08325 4.7237 4.08325 4.99984V6.74984H15.9166V4.99984C15.9166 4.7237 15.6927 4.49984 15.4166 4.49984H13.3333H6.66659ZM15.9166 8.24984H4.08325V15.8332C4.08325 16.1093 4.30711 16.3332 4.58325 16.3332H15.4166C15.6927 16.3332 15.9166 16.1093 15.9166 15.8332V8.24984Z" fill="currentColor"/>
                </svg>
              </span>
            </div>
          </div>
          <div class="flex-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Status
            </label>
            <SearchableSelect
              v-model="filterStatus"
              :options="statusOptions"
              placeholder="Semua Status"
              @update:model-value="handleFilterChange"
            />
          </div>
          <div class="flex-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Kantor Cabang
            </label>
            <SearchableSelect
              v-model="filterKantorCabang"
              :options="kantorCabangList"
              placeholder="Semua Kantor Cabang"
              @update:model-value="handleFilterChange"
            />
          </div>
          <div class="flex-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Tipe Absensi
            </label>
            <SearchableSelect
              v-model="filterTipeAbsensi"
              :options="tipeAbsensiList"
              placeholder="Semua Tipe Absensi"
              @update:model-value="handleFilterChange"
            />
          </div>
          <div class="md:col-span-3 lg:col-span-1 flex items-end">
            <button
              @click="resetFilter"
              class="h-11 w-full rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]"
            >
              Reset Filter
            </button>
          </div>
        </div>
        <div v-if="errorMessage" class="mt-3 text-sm text-red-600">{{ errorMessage }}</div>
        
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="flex items-center justify-center py-20">
        <div class="flex flex-col items-center gap-4">
          <div class="h-12 w-12 animate-spin rounded-full border-4 border-brand-500 border-t-transparent"></div>
          <p class="text-sm text-gray-600 dark:text-gray-400">Memuat data absensi...</p>
        </div>
      </div>

      <!-- AG Grid -->
      <div v-else>
        <div class="ag-theme-alpine dark:ag-theme-alpine-dark" style="width: 100%;">
        <ag-grid-vue
          ref="agGridRef"
          class="ag-theme-alpine"
          style="width: 100%; height: 480px; min-height: 300px;"
          :columnDefs="columnDefs"
          :defaultColDef="defaultColDef"
          :rowModelType="'infinite'"
          :datasource="dataSourceRef"
          :rowBuffer="1"
          :cacheBlockSize="10"
          :infiniteInitialRowCount="10"
          :maxBlocksInCache="20"
          theme="legacy"
          :animateRows="true"
          :suppressHorizontalScroll="true"
          @grid-ready="onGridReady"
          @sort-changed="onSortChanged"
        />
        </div>
      </div>

      <!-- Friendly placeholder when backend reports zero rows -->
      <div v-if="!loading && totalAbsensi === 0" class="py-10">
        <div class="flex flex-col items-center justify-center gap-3">
          
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import FlatPickr from 'vue-flatpickr-component'
import 'flatpickr/dist/flatpickr.css'
import SearchableSelect from '@/components/forms/SearchableSelect.vue'
import { useRoute, useRouter } from 'vue-router'
import { AgGridVue } from 'ag-grid-vue3'
import * as XLSX from 'xlsx'
import 'ag-grid-community/styles/ag-grid.css'
import 'ag-grid-community/styles/ag-theme-alpine.css'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'

const route = useRoute()
const router = useRouter()
const currentPageTitle = ref<string>(String(route.meta.title || 'Absensi'))

// State
const loading = ref(false)
const rowData = ref<any[]>([])
const gridApi = ref<any | null>(null)
const errorMessage = ref('')
// total count from API (null means unknown yet)
const totalAbsensi = ref<number | null>(null)
const handleFilterChange = () => {
  // reset total when filters change
  totalAbsensi.value = null
  if (gridApi.value) gridApi.value.purgeInfiniteCache()
  else fetchData()
}

const handleFilterTanggalChange = () => {
  if (filterTimeout) clearTimeout(filterTimeout)
  filterTimeout = window.setTimeout(() => {
    if (gridApi.value) gridApi.value.purgeInfiniteCache()
    else fetchData()
  }, 400) as unknown as ReturnType<typeof setTimeout>
}
// debug panel removed

// Build query params for infinite datasource
const buildQueryParams = (start: number, limit: number, sortModel?: any) => {
  const params = new URLSearchParams()
  params.append('start', String(start))
  params.append('limit', String(limit))
  if (filterSearch.value) params.append('search', filterSearch.value)

  // Date range handling: support array of Dates, 'from to to' string, or single date
  if (filterTanggal.value) {
    const val: any = filterTanggal.value
    if (Array.isArray(val)) {
      if (val[0]) params.append('date_from', formatDateString(val[0]))
      if (val[1]) params.append('date_to', formatDateString(val[1]))
    } else if (typeof val === 'string') {
      if (val.includes(' to ')) {
        const [from, to] = val.split(' to ').map((s) => s.trim())
        if (from) params.append('date_from', from)
        if (to) params.append('date_to', to)
      } else if (val.includes(' - ')) {
        const [from, to] = val.split(' - ').map((s) => s.trim())
        if (from) params.append('date_from', from)
        if (to) params.append('date_to', to)
      } else {
        params.append('date', val)
      }
    }
  }

  if (filterKantorCabang.value) params.append('kantor_cabang_id', filterKantorCabang.value)
  if (filterTipeAbsensi.value) params.append('tipe_absensi_id', filterTipeAbsensi.value)

  if (filterStatus.value) params.append('status', filterStatus.value)
  params.append('per_page', '10')
  if (sortModel && Array.isArray(sortModel) && sortModel.length > 0) {
    const s = sortModel[0]
    if (s.colId) params.append('sort_by', s.colId)
    if (s.sort) params.append('sort_direction', s.sort)
  }
  return params.toString()
}

// helper to format Date or string to YYYY-MM-DD
const formatDateString = (d: any) => {
  try {
    if (typeof d === 'string') return d
    const dt = new Date(d)
    const yyyy = dt.getFullYear()
    const mm = String(dt.getMonth() + 1).padStart(2, '0')
    const dd = String(dt.getDate()).padStart(2, '0')
    return `${yyyy}-${mm}-${dd}`
  } catch (e) {
    return String(d)
  }
}

// format total hours as "± X jam Y menit" from timestamps or raw decimal value
const formatTotalHours = (masuk: any, keluar: any, rawValue: any) => {
  // try timestamps first
  if (masuk && keluar) {
    try {
      const m = new Date(masuk)
      const k = new Date(keluar)
      let diffMinutes = Math.round((k.getTime() - m.getTime()) / 60000)
      if (diffMinutes < 0) diffMinutes += 24 * 60
      let hours = Math.floor(diffMinutes / 60)
      let minutes = diffMinutes % 60
      if (minutes === 0) return `± ${hours} jam`
      return `± ${hours} jam ${minutes} menit`
    } catch (e) {
      // fallback
    }
  }

  // fallback to raw decimal hours (e.g., 7.93)
  if (rawValue !== null && rawValue !== undefined && !isNaN(Number(rawValue))) {
    const dec = Number(rawValue)
    let hours = Math.floor(dec)
    let minutes = Math.round((dec - hours) * 60)
    if (minutes === 60) {
      hours += 1
      minutes = 0
    }
    if (minutes === 0) return `± ${hours} jam`
    return `± ${hours} jam ${minutes} menit`
  }

  return '-'
}

// AG Grid infinite datasource - created via factory so we can rebind easily like Keuangan
// This datasource implements a simple client-side block cache and in-flight request deduping
// so blocks already fetched won't be requested again (reduces load on backend while scrolling).
const createDataSource = () => {
  const blockCache = new Map<number, any[]>()
  const inFlight = new Map<number, Promise<any>>()
  let lastTotal: number | null = null

  return {
    getRows: async (params: any) => {
        console.log('[Absensi] getRows called', { start: params.startRow, end: params.endRow, sortModel: params.sortModel })
      const start = params.startRow
      const end = params.endRow
      const limit = Math.max(1, end - start)
      const blockKey = start

      try {
        // If we already have this block cached on client, return it immediately
        if (blockCache.has(blockKey)) {
          const cached = blockCache.get(blockKey) || []
          params.successCallback(cached, lastTotal)
          return
        }

        // If there's an in-flight request for this same block, await it
        if (inFlight.has(blockKey)) {
          await inFlight.get(blockKey)
          const cached = blockCache.get(blockKey) || []
          params.successCallback(cached, lastTotal)
          return
        }

        const url = `/admin/api/absensi?${buildQueryParams(start, limit, params.sortModel)}`
        
  // include sortModel in debug log
  console.log('[Absensi] fetching', url, { sortModel: params.sortModel })

        const p = (async () => {
          const res = await fetch(url, {
            method: 'GET',
            headers: {
              'Content-Type': 'application/json',
              'X-Requested-With': 'XMLHttpRequest',
              'Accept': 'application/json',
              'X-CSRF-TOKEN': getCsrfToken(),
            },
            credentials: 'same-origin',
          })

          if (!res.ok) throw new Error('Failed to fetch')
          const json = await res.json()

          if (json.success) {
            errorMessage.value = ''
            lastTotal = typeof json.total === 'number' ? json.total : (json.data ? json.data.length : null)
            // reflect total to reactive outside var so template can show placeholder
            totalAbsensi.value = lastTotal ?? null
            const data = JSON.parse(JSON.stringify(json.data || []))
            // cache by start index
            blockCache.set(blockKey, data)
            params.successCallback(data, lastTotal ?? null)

            // show friendly message if backend reports zero total
            if ((lastTotal === 0) || (!data || data.length === 0)) {
              errorMessage.value = ''
              // Show AG Grid 'no rows' overlay if api is available
              try { gridApi.value?.showNoRowsOverlay?.() } catch (e) { /* ignore */ }
            } else {
              try { gridApi.value?.hideOverlay?.() } catch (e) { /* ignore */ }
            }
          } else {
            errorMessage.value = json.message || 'Gagal memuat data absensi'
            totalAbsensi.value = 0
            params.successCallback([], 0)
          }
        })()

        inFlight.set(blockKey, p)
        try {
          await p
        } finally {
          inFlight.delete(blockKey)
        }
      } catch (error) {
        console.error('Infinite getRows error:', error)
        errorMessage.value = (error as any)?.message || String(error)
        params.failCallback()
      }
    },

    // expose cache for debugging/testing if needed
    _blockCache: blockCache,
  }
}

const dataSourceRef = ref(createDataSource())

const onGridReady = (params: any) => {
  gridApi.value = params.api
  // Bind the reactive datasource to AG Grid (use .value on the ref)
  try {
    params.api.setDatasource(dataSourceRef.value)
  } catch (e) {
    console.error('Error setting datasource on grid ready:', e)
  }
}

const onSortChanged = () => {
  console.log('[Absensi] sort changed, recreating datasource and purging cache', { sortModel: gridApi.value?.getSortModel?.() })
  // Recreate the datasource to ensure internal block cache is cleared
  try {
    dataSourceRef.value = createDataSource()
    if (gridApi.value) {
      gridApi.value.setDatasource(dataSourceRef.value)
      // Purge AG Grid's internal cache as well
      try { gridApi.value.purgeInfiniteCache() } catch (e) { /* ignore */ }
    }
  } catch (e) {
    console.error('[Absensi] error recreating datasource:', e)
  }
}

// Filter state
const filterSearch = ref('')
const filterTanggal = ref('') // will hold range string or array in range mode
const filterStatus = ref('')
const filterKantorCabang = ref('')
const filterTipeAbsensi = ref('')

const kantorCabangList = ref<any[]>([{ value: '', label: 'Semua Kantor Cabang' }])
const tipeAbsensiList = ref<any[]>([{ value: '', label: 'Semua Tipe Absensi' }])

const flatpickrDateConfig = { dateFormat: 'Y-m-d', allowInput: true }
const flatpickrDateConfigRange = ({ mode: 'range', dateFormat: 'Y-m-d', altInput: true, altFormat: 'd/m/Y', wrap: false } as any)

const loadOptions = async () => {
  try {
    const r = await fetch('/admin/api/kantor-cabang?per_page=1000', { credentials: 'same-origin' })
    if (r.ok) {
      const j = await r.json()
      const mapped = (j.data || []).map((c: any) => ({ value: String(c.id), label: c.nama || c.name || '-' }))
      kantorCabangList.value = [{ value: '', label: 'Semua Kantor Cabang' }, ...mapped]
    }
    // load tipe absensi
    try {
      const rt = await fetch('/admin/api/tipe-absensi?per_page=1000', { credentials: 'same-origin' })
      if (rt.ok) {
        const jt = await rt.json()
        const mapped = (jt.data || []).map((t: any) => ({ value: String(t.id), label: t.nama || t.name || t.label || '-' }))
        tipeAbsensiList.value = [{ value: '', label: 'Semua Tipe Absensi' }, ...mapped]
      }
    } catch (e) {
      console.error('Error loading tipe absensi list:', e)
    }
  } catch (e) {
    console.error('Error loading kantor cabang list:', e)
  }
}

const statusOptions = [
  { value: '', label: 'Semua Status' },
  { value: 'hadir', label: 'Hadir' },
  { value: 'terlambat', label: 'Terlambat' },
  { value: 'pulang_awal', label: 'Pulang Awal' },
  { value: 'tidak_hadir', label: 'Tidak Hadir' },
  { value: 'izin', label: 'Izin' },
  { value: 'sakit', label: 'Sakit' },
  { value: 'cuti', label: 'Cuti' },
]

// Column definitions
const columnDefs = [
  {
    headerName: 'Nama',
    field: 'user.name',
    sortable: true,
    filter: false,
    // flex: 1,
    valueGetter: (params: any) => params.data?.user?.name || '-',
  },
  {
    headerName: 'No Induk',
    field: 'user.no_induk',
    sortable: true,
    filter: false,
    width: 120,
    valueGetter: (params: any) => params.data?.user?.no_induk || '-',
  },
  {
    headerName: 'Kantor Cabang',
    field: 'kantor_cabang.nama',
    sortable: true,
    filter: false,
    width: 150,
    valueGetter: (params: any) => params.data?.kantor_cabang?.nama || '-',
  },
  {
    headerName: 'Jam Masuk',
    field: 'jam_masuk',
    sortable: true,
    filter: false,
    width: 180,
    valueFormatter: (params: any) => {
      if (params.value) {
        return new Date(params.value).toLocaleString('id-ID', {
          year: 'numeric',
          month: 'short',
          day: 'numeric',
          hour: '2-digit',
          minute: '2-digit',
        })
      }
      return '-'
    },
  },
  {
    headerName: 'Jam Keluar',
    field: 'jam_keluar',
    sortable: true,
    filter: false,
    width: 180,
    valueFormatter: (params: any) => {
      if (params.value) {
        return new Date(params.value).toLocaleString('id-ID', {
          year: 'numeric',
          month: 'short',
          day: 'numeric',
          hour: '2-digit',
          minute: '2-digit',
        })
      }
      return '-'
    },
  },
  {
    headerName: 'Total Jam',
    field: 'total_jam_kerja',
    sortable: true,
    filter: false,
    width: 100,
    valueFormatter: (params: any) => {
      return formatTotalHours(params.data?.jam_masuk, params.data?.jam_keluar, params.value)
    },
  },
  {
    headerName: 'Status',
    field: 'status',
    sortable: true,
    filter: false,
    width: 120,
    cellRenderer: (params: any) => {
      // derive a safe status based on timestamps and tipe_absensi if available
      let status = params.value || 'hadir'
      const masuk = params.data?.jam_masuk
      const keluar = params.data?.jam_keluar
      const tipe = params.data?.tipe_absensi

      if (!masuk) {
        status = 'tidak_hadir'
      } else {
        // check terlambat using tipe_absensi jam_masuk if available
          if (tipe && tipe.jam_masuk) {
          try {
            const actualMasuk = new Date(masuk).toTimeString().slice(0, 8)
            if (actualMasuk > tipe.jam_masuk) {
              status = 'terlambat'
            } else {
              status = 'hadir'
            }
          } catch (e) {
            status = params.value || 'hadir'
          }
        } else {
          status = params.value || 'hadir'
        }
      
      // Treat 'terlambat' as 'hadir' for display purposes
      if (status === 'terlambat') status = 'hadir'
      }

      if (masuk && keluar) {
        try {
          const m = new Date(masuk)
          const k = new Date(keluar)
          if (k.getTime() < m.getTime()) {
            // if keluar earlier than masuk assume early leave / cross-midnight; mark as pulang_awal
            status = 'pulang_awal'
          } else if (tipe && tipe.jam_keluar) {
            const actualKeluar = new Date(keluar).toTimeString().slice(0, 8)
            if (actualKeluar < tipe.jam_keluar && status !== 'terlambat') {
              status = 'pulang_awal'
            }
          }
        } catch (e) {
          // ignore parse errors
        }
      }

      const statusConfig: Record<string, { label: string; class: string }> = {
        hadir: { label: 'Hadir', class: 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' },
        terlambat: { label: 'Terlambat', class: 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400' },
        pulang_awal: { label: 'Pulang Awal', class: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' },
        tidak_hadir: { label: 'Tidak Hadir', class: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' },
        izin: { label: 'Izin', class: 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' },
        sakit: { label: 'Sakit', class: 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400' },
        cuti: { label: 'Cuti', class: 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400' },
      }
      const config = statusConfig[status] || statusConfig.hadir

      const span = document.createElement('span')
      span.className = `inline-flex px-2 py-1 rounded-full text-xs font-medium ${config.class}`
      span.textContent = config.label
      return span
    },
  },
  {
    headerName: 'Actions',
    field: 'actions',
    sortable: false,
    filter: false,
    width: 100,
    pinned: 'right',
    cellRenderer: (params: any) => {
      const div = document.createElement('div')
      div.className = 'flex items-center gap-3'
      
      const detailBtn = document.createElement('button')
      detailBtn.className = 'flex items-center justify-center w-8 h-8 rounded-lg text-brand-500 hover:bg-brand-50 dark:hover:bg-brand-500/10 transition-colors'
      detailBtn.innerHTML = `
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
          <circle cx="12" cy="12" r="3"></circle>
        </svg>
      `
      detailBtn.onclick = () => handleDetail(params.data.id)
      
      div.appendChild(detailBtn)
      
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

// Get CSRF token
const getCsrfToken = (): string => {
  return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
}

// Fetch data from API
const fetchData = async () => {
  // Keep for export and fallback; infinite grid will use dataSource instead
  loading.value = true
  try {
    const params = new URLSearchParams()
    if (filterSearch.value) params.append('search', filterSearch.value)
    if (filterTanggal.value) params.append('date', filterTanggal.value)
    if (filterStatus.value) params.append('status', filterStatus.value)
    params.append('per_page', '10')

    const url = `/admin/api/absensi${params.toString() ? '?' + params.toString() : ''}`
    
    const response = await fetch(url, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': getCsrfToken(),
      },
      credentials: 'same-origin',
    })

    const result = await response.json()
    if (result.success) {
      rowData.value = JSON.parse(JSON.stringify(result.data || []))
    } else {
      console.error('Failed to fetch data:', result.message)
      rowData.value = []
    }
  } catch (error) {
    console.error('Error fetching data:', error)
    rowData.value = []
  } finally {
    loading.value = false
  }
}

// Handle detail
const handleDetail = (id: string) => {
  router.push(`/operasional/absensi/${id}`)
}

// Handle export to Excel — we fetch all matching rows (per_page=1000) to respect current filters
const handleExportExcel = async () => {
  try {
    const exportParams = new URLSearchParams()
    if (filterSearch.value) exportParams.append('search', filterSearch.value)
    if (filterTanggal.value) {
      const val: any = filterTanggal.value
      if (Array.isArray(val)) {
        if (val[0]) exportParams.append('date_from', formatDateString(val[0]))
        if (val[1]) exportParams.append('date_to', formatDateString(val[1]))
      } else if (typeof val === 'string') {
        if (val.includes(' to ')) {
          const [from, to] = val.split(' to ').map((s) => s.trim())
          if (from) exportParams.append('date_from', from)
          if (to) exportParams.append('date_to', to)
        } else if (val.includes(' - ')) {
          const [from, to] = val.split(' - ').map((s) => s.trim())
          if (from) exportParams.append('date_from', from)
          if (to) exportParams.append('date_to', to)
        } else {
          exportParams.append('date', val)
        }
      }
    }
    if (filterKantorCabang.value) exportParams.append('kantor_cabang_id', filterKantorCabang.value)
    if (filterTipeAbsensi.value) exportParams.append('tipe_absensi_id', filterTipeAbsensi.value)
    if (filterStatus.value) exportParams.append('status', filterStatus.value)
    // fetch up to 1000 rows for export
    exportParams.append('per_page', '1000')

    const url = `/admin/api/absensi?${exportParams.toString()}`
    const res = await fetch(url, { method: 'GET', credentials: 'same-origin' })
    if (!res.ok) throw new Error('Failed to fetch export data')
    const json = await res.json()
    const data = json.success ? JSON.parse(JSON.stringify(json.data || [])) : JSON.parse(JSON.stringify(rowData.value || []))

    const dataToExport = data.map((item: any) => {
      return {
        'Nama': item.user?.name || '-',
        'No Induk': item.user?.no_induk || '-',
        'Kantor Cabang': item.kantor_cabang?.nama || '-',
        'Tipe Absensi': item.tipe_absensi?.nama || item.tipe_absensi?.label || '-',
        'Jam Masuk': item.jam_masuk ? new Date(item.jam_masuk).toLocaleString('id-ID') : '-',
        'Jam Keluar': item.jam_keluar ? new Date(item.jam_keluar).toLocaleString('id-ID') : '-',
        'Total Jam Kerja': formatTotalHours(item.jam_masuk, item.jam_keluar, item.total_jam_kerja),
        'Status': (item.status === 'terlambat' ? 'hadir' : (item.status || '-')),
        'Catatan': item.catatan || '-',
      }
    })

    const worksheet = XLSX.utils.json_to_sheet(dataToExport)
    const workbook = XLSX.utils.book_new()
    XLSX.utils.book_append_sheet(workbook, worksheet, 'Absensi')

    const now = new Date()
    const filename = `Absensi_${now.toISOString().split('T')[0]}.xlsx`

    XLSX.writeFile(workbook, filename)
  } catch (e) {
    console.error('Export failed:', e)
    // fallback to local rowData export
    const dataToExport = rowData.value.map((item) => ({
      'Nama': item.user?.name || '-',
      'No Induk': item.user?.no_induk || '-',
      'Kantor Cabang': item.kantor_cabang?.nama || '-',
      'Tipe Absensi': item.tipe_absensi?.nama || item.tipe_absensi?.label || '-',
      'Jam Masuk': item.jam_masuk ? new Date(item.jam_masuk).toLocaleString('id-ID') : '-',
      'Jam Keluar': item.jam_keluar ? new Date(item.jam_keluar).toLocaleString('id-ID') : '-',
      'Total Jam Kerja': formatTotalHours(item.jam_masuk, item.jam_keluar, item.total_jam_kerja),
      'Status': (item.status === 'terlambat' ? 'hadir' : (item.status || '-')),
      'Catatan': item.catatan || '-',
    }))
    const worksheet = XLSX.utils.json_to_sheet(dataToExport)
    const workbook = XLSX.utils.book_new()
    XLSX.utils.book_append_sheet(workbook, worksheet, 'Absensi')
    const now = new Date()
    const filename = `Absensi_${now.toISOString().split('T')[0]}.xlsx`
    XLSX.writeFile(workbook, filename)
  }
}

// Reset filter
const resetFilter = () => {
  filterSearch.value = ''
  filterTanggal.value = ''
  filterStatus.value = ''
  filterKantorCabang.value = ''
  filterTipeAbsensi.value = ''
  // Recreate datasource and refresh the grid so filters reset
  dataSourceRef.value = createDataSource()
  if (gridApi.value) {
    try {
      gridApi.value.purgeInfiniteCache()
    } catch (e) {
      fetchData()
    }
  } else {
    fetchData()
  }
}

// Watch filter changes with debounce
let filterTimeout: ReturnType<typeof setTimeout> | null = null
watch([filterSearch, filterTanggal, filterStatus, filterKantorCabang, filterTipeAbsensi], () => {
  if (filterTimeout) {
    clearTimeout(filterTimeout)
  }
  filterTimeout = setTimeout(() => {
    // Recreate datasource and refresh cache so filters apply
    dataSourceRef.value = createDataSource()
    if (gridApi.value) {
      try {
        // Purge cache so filter changes will cause fresh loads. Do not call refreshInfiniteCache
        // here because it will trigger immediate re-requests for blocks; AG Grid will request
        // blocks as needed which keeps network load lower.
        gridApi.value.purgeInfiniteCache()
      } catch (e) {
        // fallback to fetchData
        fetchData()
      }
    } else {
      fetchData()
    }
  }, 500)
})

onMounted(() => {
  loadOptions()
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
