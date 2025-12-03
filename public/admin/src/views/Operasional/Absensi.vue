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
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
          <div class="flex-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Nama / No Induk
            </label>
            <input
              type="text"
              v-model="filterNama"
              placeholder="Cari nama atau no induk..."
              class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
          </div>
          <div class="flex-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Tanggal & Jam Masuk
            </label>
            <div class="relative">
              <flat-pickr
                v-model="filterTanggalMasuk"
                :config="flatpickrDateTimeConfig"
                class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pl-4 pr-11 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                placeholder="Pilih tanggal & jam masuk"
              />
              <span
                class="absolute text-gray-500 -translate-y-1/2 pointer-events-none right-3 top-1/2 dark:text-gray-400"
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
                    d="M10 18.3333C14.6024 18.3333 18.3333 14.6024 18.3333 9.99999C18.3333 5.39762 14.6024 1.66666 10 1.66666C5.39763 1.66666 1.66667 5.39762 1.66667 9.99999C1.66667 14.6024 5.39763 18.3333 10 18.3333Z"
                    stroke="currentColor"
                    stroke-width="1.5"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  />
                  <path
                    d="M10 5V10L13.3333 11.6667"
                    stroke="currentColor"
                    stroke-width="1.5"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  />
                </svg>
              </span>
            </div>
          </div>
          <div class="flex-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Tanggal & Jam Keluar
            </label>
            <div class="relative">
              <flat-pickr
                v-model="filterTanggalKeluar"
                :config="flatpickrDateTimeConfig"
                class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pl-4 pr-11 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                placeholder="Pilih tanggal & jam keluar"
              />
              <span
                class="absolute text-gray-500 -translate-y-1/2 pointer-events-none right-3 top-1/2 dark:text-gray-400"
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
                    d="M10 18.3333C14.6024 18.3333 18.3333 14.6024 18.3333 9.99999C18.3333 5.39762 14.6024 1.66666 10 1.66666C5.39763 1.66666 1.66667 5.39762 1.66667 9.99999C1.66667 14.6024 5.39763 18.3333 10 18.3333Z"
                    stroke="currentColor"
                    stroke-width="1.5"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  />
                  <path
                    d="M10 5V10L13.3333 11.6667"
                    stroke="currentColor"
                    stroke-width="1.5"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  />
                </svg>
              </span>
            </div>
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

      <div class="relative" style="width: 100%; height: 450px;">
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
        
        <!-- Custom empty state overlay -->
        <div
          v-if="showEmptyState"
          class="absolute inset-0 flex flex-col items-center justify-center bg-white dark:bg-gray-900 rounded-lg z-50 pointer-events-none"
          style="position: absolute; top: 0; left: 0; right: 0; bottom: 0;"
        >
          <svg class="w-16 h-16 text-gray-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path
              v-if="filterNama || filterTanggalMasuk || filterTanggalKeluar"
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
            {{ filterNama || filterTanggalMasuk || filterTanggalKeluar ? 'Tidak ada data ditemukan' : 'Tidak ada data' }}
          </p>
          <p class="text-gray-500 dark:text-gray-500 text-sm">
            {{ filterNama || filterTanggalMasuk || filterTanggalKeluar ? 'Coba ubah filter pencarian Anda' : 'Belum ada data yang tersedia' }}
          </p>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted, computed, watch, nextTick } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { AgGridVue } from 'ag-grid-vue3'
import { AllCommunityModule } from 'ag-grid-community'
import type { IDatasource, IGetRowsParams } from 'ag-grid-community'
import flatPickr from 'vue-flatpickr-component'
import * as XLSX from 'xlsx'
import 'flatpickr/dist/flatpickr.css'
import 'ag-grid-community/styles/ag-grid.css'
import 'ag-grid-community/styles/ag-theme-alpine.css'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'

const route = useRoute()
const router = useRouter()
const currentPageTitle = ref(route.meta.title || 'Absensi')
const agGridRef = ref<InstanceType<typeof AgGridVue> | null>(null)

// Flatpickr configuration for date and time
const flatpickrDateTimeConfig = {
  enableTime: true,
  dateFormat: 'Y-m-d H:i',
  altInput: true,
  altFormat: 'd/m/Y H:i',
  time_24hr: true,
  minuteIncrement: 1,
  wrap: false,
}

// Column definitions
const columnDefs = [
  {
    headerName: 'Nama',
    field: 'nama',
    sortable: true,
    filter: false,
    flex: 1,
  },
  {
    headerName: 'No Induk',
    field: 'noInduk',
    sortable: true,
    filter: false,
    width: 120,
  },
  {
    headerName: 'Tanggal Absen Masuk',
    field: 'tanggalAbsenMasuk',
    sortable: true,
    filter: false,
    flex: 1,
    valueFormatter: (params: any) => {
      if (params.value) {
        return new Date(params.value).toLocaleString('id-ID', {
          year: 'numeric',
          month: 'long',
          day: 'numeric',
          hour: '2-digit',
          minute: '2-digit',
        })
      }
      return ''
    },
  },
  {
    headerName: 'Tanggal Absen Keluar',
    field: 'tanggalAbsenKeluar',
    sortable: true,
    filter: false,
    flex: 1,
    valueFormatter: (params: any) => {
      if (params.value) {
        return new Date(params.value).toLocaleString('id-ID', {
          year: 'numeric',
          month: 'long',
          day: 'numeric',
          hour: '2-digit',
          minute: '2-digit',
        })
      }
      return ''
    },
  },
  {
    headerName: 'Total Kerja',
    field: 'totalKerja',
    sortable: true,
    filter: false,
    width: 150,
    valueGetter: (params: any) => {
      if (!params.data) return 0
      if (params.data.tanggalAbsenMasuk && params.data.tanggalAbsenKeluar) {
        const masuk = new Date(params.data.tanggalAbsenMasuk).getTime()
        const keluar = new Date(params.data.tanggalAbsenKeluar).getTime()
        const diff = keluar - masuk
        const hours = diff / (1000 * 60 * 60)
        return hours
      }
      return 0
    },
    valueFormatter: (params: any) => {
      if (params.value) {
        const hours = Math.floor(params.value)
        const minutes = Math.floor((params.value - hours) * 60)
        if (minutes > 0) {
          return `${hours} jam ${minutes} menit`
        }
        return `${hours} jam`
      }
      return '0 jam'
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

// Sample data - generate 200 items for infinite scroll testing
const generateRowData = () => {
  const namas = [
    'Ahmad Hidayat', 'Siti Nurhaliza', 'Budi Santoso', 'Dewi Lestari', 'Eko Prasetyo',
    'Fitri Handayani', 'Guntur Wibowo', 'Hesti Rahayu', 'Indra Wijaya', 'Joko Susilo',
    'Kartika Putri', 'Lukman Hakim', 'Maya Sari', 'Nanda Pratama', 'Olivia Wijaya',
    'Putra Ramadhan', 'Qori Anisa', 'Rizky Pratama', 'Salsabila Putri', 'Taufik Hidayat',
  ]
  
  const rowData: Array<{ id: string; nama: string; noInduk: string; tanggalAbsenMasuk: string; tanggalAbsenKeluar: string }> = []
  const startDate = new Date('2024-01-01')
  startDate.setHours(0, 0, 0, 0) // Set to midnight to avoid timezone issues
  
  for (let i = 1; i <= 200; i++) {
    const namaIndex = (i - 1) % namas.length
    const date = new Date(startDate)
    date.setDate(date.getDate() + Math.floor((i - 1) / 20)) // Same date for 20 entries
    
    // Random time between 7:00 - 9:00 for masuk
    const masukHour = Math.floor(Math.random() * 2) + 7
    const masukMinute = Math.floor(Math.random() * 60)
    const masukDate = new Date(date)
    masukDate.setHours(masukHour, masukMinute, 0, 0)
    
    // Random time between 16:00 - 18:00 for keluar (must be after masuk)
    const keluarHour = Math.floor(Math.random() * 2) + 16
    const keluarMinute = Math.floor(Math.random() * 60)
    const keluarDate = new Date(date)
    keluarDate.setHours(keluarHour, keluarMinute, 0, 0)
    
    // Ensure keluar is after masuk
    if (keluarDate <= masukDate) {
      keluarDate.setHours(masukHour + 8, masukMinute, 0, 0)
    }
    
    rowData.push({
      id: i.toString(),
      nama: namas[namaIndex],
      noInduk: `K${String(i).padStart(3, '0')}`,
      tanggalAbsenMasuk: masukDate.toISOString(),
      tanggalAbsenKeluar: keluarDate.toISOString(),
    })
  }
  
  return rowData
}

const rowDataArray = generateRowData()

// Filter state
const filterNama = ref('')
const filterTanggalMasuk = ref('')
const filterTanggalKeluar = ref('')

// Filtered data based on filter
const filteredData = computed(() => {
  let filtered = [...rowDataArray]
  
  // Filter by nama or no induk
  if (filterNama.value) {
    const searchTerm = filterNama.value.toLowerCase()
    filtered = filtered.filter((item) =>
      item.nama.toLowerCase().includes(searchTerm) ||
      item.noInduk.toLowerCase().includes(searchTerm)
    )
  }
  
  // Filter by tanggal & jam masuk
  if (filterTanggalMasuk.value) {
    filtered = filtered.filter((item) => {
      const itemDateTime = new Date(item.tanggalAbsenMasuk)
      const filterDateTime = new Date(filterTanggalMasuk.value)
      
      // Compare date (year, month, day)
      const sameDate = 
        itemDateTime.getFullYear() === filterDateTime.getFullYear() &&
        itemDateTime.getMonth() === filterDateTime.getMonth() &&
        itemDateTime.getDate() === filterDateTime.getDate()
      
      if (!sameDate) return false
      
      // If time is specified (not midnight), compare hour and minute with 15 minutes tolerance
      const hasTime = filterDateTime.getHours() !== 0 || filterDateTime.getMinutes() !== 0
      if (hasTime) {
        const timeDiff = Math.abs(itemDateTime.getTime() - filterDateTime.getTime())
        return timeDiff < 15 * 60 * 1000 // Within 15 minutes
      }
      
      return true // Same date, no specific time filter
    })
  }
  
  // Filter by tanggal & jam keluar
  if (filterTanggalKeluar.value) {
    filtered = filtered.filter((item) => {
      const itemDateTime = new Date(item.tanggalAbsenKeluar)
      const filterDateTime = new Date(filterTanggalKeluar.value)
      
      // Compare date (year, month, day)
      const sameDate = 
        itemDateTime.getFullYear() === filterDateTime.getFullYear() &&
        itemDateTime.getMonth() === filterDateTime.getMonth() &&
        itemDateTime.getDate() === filterDateTime.getDate()
      
      if (!sameDate) return false
      
      // If time is specified (not midnight), compare hour and minute with 15 minutes tolerance
      const hasTime = filterDateTime.getHours() !== 0 || filterDateTime.getMinutes() !== 0
      if (hasTime) {
        const timeDiff = Math.abs(itemDateTime.getTime() - filterDateTime.getTime())
        return timeDiff < 15 * 60 * 1000 // Within 15 minutes
      }
      
      return true // Same date, no specific time filter
    })
  }
  
  return filtered
})

// Show empty state when filtered data is empty
const showEmptyState = computed(() => filteredData.value.length === 0)

// Handle detail
const handleDetail = (id: string) => {
  router.push(`/operasional/absensi/${id}`)
}

// Handle export to Excel
const handleExportExcel = () => {
  // Get all filtered data
  const dataToExport = filteredData.value.map((item) => {
    const masukDate = new Date(item.tanggalAbsenMasuk)
    const keluarDate = new Date(item.tanggalAbsenKeluar)
    
    // Calculate total kerja
    let totalKerja = '0 jam'
    if (item.tanggalAbsenMasuk && item.tanggalAbsenKeluar) {
      const masuk = masukDate.getTime()
      const keluar = keluarDate.getTime()
      const diff = keluar - masuk
      const hours = diff / (1000 * 60 * 60)
      const hoursInt = Math.floor(hours)
      const minutes = Math.floor((hours - hoursInt) * 60)
      if (minutes > 0) {
        totalKerja = `${hoursInt} jam ${minutes} menit`
      } else {
        totalKerja = `${hoursInt} jam`
      }
    }
    
    return {
      'Nama': item.nama,
      'No Induk': item.noInduk,
      'Tanggal Absen Masuk': masukDate.toLocaleString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
      }),
      'Tanggal Absen Keluar': keluarDate.toLocaleString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
      }),
      'Total Kerja': totalKerja,
    }
  })
  
  // Create workbook and worksheet
  const worksheet = XLSX.utils.json_to_sheet(dataToExport)
  const workbook = XLSX.utils.book_new()
  XLSX.utils.book_append_sheet(workbook, worksheet, 'Absensi')
  
  // Generate filename with current date
  const now = new Date()
  const filename = `Absensi_${now.toISOString().split('T')[0]}.xlsx`
  
  // Write file and download
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
      
      // Handle date/time sorting
      if (colId === 'tanggalAbsenMasuk' || colId === 'tanggalAbsenKeluar') {
        aValue = new Date(aValue).getTime()
        bValue = new Date(bValue).getTime()
      } else if (colId === 'totalKerja') {
        // Calculate total kerja for sorting
        if (a.tanggalAbsenMasuk && a.tanggalAbsenKeluar) {
          const masuk = new Date(a.tanggalAbsenMasuk).getTime()
          const keluar = new Date(a.tanggalAbsenKeluar).getTime()
          aValue = (keluar - masuk) / (1000 * 60 * 60)
        } else {
          aValue = 0
        }
        if (b.tanggalAbsenMasuk && b.tanggalAbsenKeluar) {
          const masuk = new Date(b.tanggalAbsenMasuk).getTime()
          const keluar = new Date(b.tanggalAbsenKeluar).getTime()
          bValue = (keluar - masuk) / (1000 * 60 * 60)
        } else {
          bValue = 0
        }
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
        const start = params.startRow ?? 0
        const end = params.endRow ?? (start + 10) // Default to 10 items if endRow not provided
        
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

// Set datasource after component is mounted
onMounted(() => {
  console.log('Component mounted, datasource:', dataSource.value)
  console.log('Total data:', rowDataArray.length)
})

// Clear debounce timer on component unmount
onUnmounted(() => {
  if (filterDebounceTimer) {
    clearTimeout(filterDebounceTimer)
  }
})

// Handle sort changes
const onSortChanged = () => {
  if (agGridRef.value && agGridRef.value.api) {
    // Update datasource to include new sort
    const newDataSource = createDataSource()
    dataSource.value.getRows = newDataSource.getRows
    
    // Refresh cache immediately - animation will be handled by AG Grid
    try {
      agGridRef.value.api.refreshInfiniteCache()
    } catch (error) {
      console.error('Error refreshing cache on sort:', error)
    }
  }
}

// Debounce timer for filter
let filterDebounceTimer: ReturnType<typeof setTimeout> | null = null

// Watch for filter changes and refresh grid with debounce
watch([filterNama, filterTanggalMasuk, filterTanggalKeluar], () => {
  // Clear existing timer
  if (filterDebounceTimer) {
    clearTimeout(filterDebounceTimer)
  }
  
  // Debounce filter update to prevent flickering
  filterDebounceTimer = setTimeout(() => {
    // Recreate datasource with new filter
    const newDataSource = createDataSource()
    dataSource.value.getRows = newDataSource.getRows
    
    // Refresh grid smoothly without multiple setTimeout
    nextTick(() => {
      if (agGridRef.value && agGridRef.value.api) {
        try {
          // Purge cache and refresh in one smooth operation
          agGridRef.value.api.purgeInfiniteCache()
          agGridRef.value.api.refreshInfiniteCache()
          
          // Scroll to top after a brief delay
          setTimeout(() => {
            if (agGridRef.value && agGridRef.value.api) {
              agGridRef.value.api.ensureIndexVisible(0, 'top')
            }
          }, 100)
        } catch (error) {
          console.error('Error refreshing cache:', error)
        }
      }
    })
  }, 300) // 300ms debounce delay to prevent flickering
})

// Reset filter
const resetFilter = () => {
  filterNama.value = ''
  filterTanggalMasuk.value = ''
  filterTanggalKeluar.value = ''
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
