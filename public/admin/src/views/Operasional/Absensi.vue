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
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-5">
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
              Tanggal Dari
            </label>
            <input
              type="date"
              v-model="filterDateFrom"
              class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
          </div>
          <div class="flex-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Tanggal Sampai
            </label>
            <input
              type="date"
              v-model="filterDateTo"
              class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
          </div>
          <div class="flex-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Status
            </label>
            <select
              v-model="filterStatus"
              class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800"
            >
              <option value="">Semua Status</option>
              <option value="hadir">Hadir</option>
              <option value="terlambat">Terlambat</option>
              <option value="pulang_awal">Pulang Awal</option>
              <option value="tidak_hadir">Tidak Hadir</option>
              <option value="izin">Izin</option>
              <option value="sakit">Sakit</option>
              <option value="cuti">Cuti</option>
            </select>
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
      <div v-if="loading" class="flex items-center justify-center py-20">
        <div class="flex flex-col items-center gap-4">
          <div class="h-12 w-12 animate-spin rounded-full border-4 border-brand-500 border-t-transparent"></div>
          <p class="text-sm text-gray-600 dark:text-gray-400">Memuat data absensi...</p>
        </div>
      </div>

      <!-- AG Grid -->
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

      <!-- Empty State -->
      <div
        v-if="!loading && rowData.length === 0"
        class="flex flex-col items-center justify-center py-20"
      >
        <svg class="w-16 h-16 text-gray-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
          ></path>
        </svg>
        <p class="text-gray-600 dark:text-gray-400 text-lg font-medium mb-1">
          Tidak ada data absensi
        </p>
        <p class="text-gray-500 dark:text-gray-500 text-sm">
          Belum ada data absensi yang tersedia
        </p>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { AgGridVue } from 'ag-grid-vue3'
import * as XLSX from 'xlsx'
import 'ag-grid-community/styles/ag-grid.css'
import 'ag-grid-community/styles/ag-theme-alpine.css'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'

const route = useRoute()
const router = useRouter()
const currentPageTitle = ref(route.meta.title || 'Absensi')

// State
const loading = ref(false)
const rowData = ref<any[]>([])

// Filter state
const filterSearch = ref('')
const filterDateFrom = ref('')
const filterDateTo = ref('')
const filterStatus = ref('')

// Column definitions
const columnDefs = [
  {
    headerName: 'Nama',
    field: 'user.name',
    sortable: true,
    filter: false,
    flex: 1,
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
      if (params.value !== null && params.value !== undefined) {
        return `${params.value} jam`
      }
      return '-'
    },
  },
  {
    headerName: 'Status',
    field: 'status',
    sortable: true,
    filter: false,
    width: 120,
    cellRenderer: (params: any) => {
      const status = params.value || 'hadir'
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
  loading.value = true
  try {
    const params = new URLSearchParams()
    if (filterSearch.value) params.append('search', filterSearch.value)
    if (filterDateFrom.value) params.append('date_from', filterDateFrom.value)
    if (filterDateTo.value) params.append('date_to', filterDateTo.value)
    if (filterStatus.value) params.append('status', filterStatus.value)
    params.append('per_page', '100')

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
      rowData.value = result.data || []
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

// Handle export to Excel
const handleExportExcel = () => {
  const dataToExport = rowData.value.map((item) => {
    return {
      'Nama': item.user?.name || '-',
      'No Induk': item.user?.no_induk || '-',
      'Kantor Cabang': item.kantor_cabang?.nama || '-',
      'Jam Masuk': item.jam_masuk ? new Date(item.jam_masuk).toLocaleString('id-ID') : '-',
      'Jam Keluar': item.jam_keluar ? new Date(item.jam_keluar).toLocaleString('id-ID') : '-',
      'Total Jam Kerja': item.total_jam_kerja ? `${item.total_jam_kerja} jam` : '-',
      'Status': item.status || '-',
      'Catatan': item.catatan || '-',
    }
  })
  
  const worksheet = XLSX.utils.json_to_sheet(dataToExport)
  const workbook = XLSX.utils.book_new()
  XLSX.utils.book_append_sheet(workbook, worksheet, 'Absensi')
  
  const now = new Date()
  const filename = `Absensi_${now.toISOString().split('T')[0]}.xlsx`
  
  XLSX.writeFile(workbook, filename)
}

// Reset filter
const resetFilter = () => {
  filterSearch.value = ''
  filterDateFrom.value = ''
  filterDateTo.value = ''
  filterStatus.value = ''
  fetchData()
}

// Watch filter changes with debounce
let filterTimeout: ReturnType<typeof setTimeout> | null = null
watch([filterSearch, filterDateFrom, filterDateTo, filterStatus], () => {
  if (filterTimeout) {
    clearTimeout(filterTimeout)
  }
  filterTimeout = setTimeout(() => {
    fetchData()
  }, 500)
})

onMounted(() => {
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
