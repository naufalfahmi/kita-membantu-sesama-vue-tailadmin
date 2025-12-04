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
            Tambah Penyaluran
          </button>
        </div>
      </div>

      <!-- Filter Section -->
      <div class="mb-6 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
          <div class="flex-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Nama Program
            </label>
            <input
              type="text"
              v-model="filterNamaProgram"
              placeholder="Cari nama program..."
              class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
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
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Tanggal
            </label>
            <div class="relative">
              <flat-pickr
                v-model="filterTanggal"
                :config="flatpickrDateConfig"
                class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pl-4 pr-11 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                placeholder="Pilih tanggal"
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
              Jumlah Dana Min
            </label>
            <input
              type="number"
              v-model="filterJumlahMin"
              placeholder="Jumlah minimum..."
              class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
          </div>
        </div>
        <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
          <div class="flex-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Jumlah Dana Max
            </label>
            <input
              type="number"
              v-model="filterJumlahMax"
              placeholder="Jumlah maksimum..."
              class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
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
              v-if="filterNamaProgram || filterPIC || filterTanggal || filterJumlahMin || filterJumlahMax"
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
            {{ filterNamaProgram || filterPIC || filterTanggal || filterJumlahMin || filterJumlahMax ? 'Tidak ada data ditemukan' : 'Tidak ada data' }}
          </p>
          <p class="text-gray-500 dark:text-gray-500 text-sm">
            {{ filterNamaProgram || filterPIC || filterTanggal || filterJumlahMin || filterJumlahMax ? 'Coba ubah filter pencarian Anda' : 'Belum ada data yang tersedia' }}
          </p>
        </div>
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
import ConfirmModal from '@/components/common/ConfirmModal.vue'
import { useToast } from 'vue-toastification'

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
const toast = useToast()
const showDeleteModal = ref(false)
const deleteId = ref<string | null>(null)

// Flatpickr configuration for date
const flatpickrDateConfig = {
  dateFormat: 'Y-m-d',
  altInput: true,
  altFormat: 'd/m/Y',
  wrap: false,
}

// Column definitions
const columnDefs = [
  {
    headerName: 'Nama Program',
    field: 'namaProgram',
    sortable: true,
    filter: false,
    flex: 1,
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
      
      div.appendChild(editBtn)
      div.appendChild(deleteBtn)
      
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
const generateRowData = (): PenyaluranRow[] => {
  const programs = [
    'Program Beasiswa Pendidikan', 'Program Kesehatan Masyarakat', 'Program Pemberdayaan Ekonomi',
    'Program Bantuan Pangan', 'Program Pengembangan SDM', 'Program Lingkungan Hidup',
    'Program Sosial Budaya', 'Program Infrastruktur', 'Program Teknologi Informasi',
    'Program Pertanian Berkelanjutan', 'Program Perumahan Rakyat', 'Program Air Bersih',
  ]
  
  const pics = [
    'Ahmad Hidayat', 'Siti Nurhaliza', 'Budi Santoso', 'Dewi Lestari', 'Eko Prasetyo',
    'Fitri Handayani', 'Guntur Wibowo', 'Hesti Rahayu', 'Indra Wijaya', 'Joko Susilo',
    'Kartika Putri', 'Lukman Hakim', 'Maya Sari', 'Nanda Pratama', 'Olivia Wijaya',
  ]
  
  const rowData: PenyaluranRow[] = []
  const startDate = new Date('2024-01-01')
  
  for (let i = 1; i <= 200; i++) {
    const programIndex = (i - 1) % programs.length
    const picIndex = (i - 1) % pics.length
    const date = new Date(startDate)
    date.setDate(date.getDate() + (i - 1) * 2) // Increment by 2 days for each entry
    
    // Random amount between 5M and 20M
    const jumlahDana = Math.floor(Math.random() * 15000000) + 5000000
    
    rowData.push({
      id: i.toString(),
      namaProgram: programs[programIndex],
      jumlahDana: jumlahDana,
      pic: pics[picIndex],
      tanggal: date.toISOString().split('T')[0],
    })
  }
  
  return rowData
}

const rowDataArray = ref<PenyaluranRow[]>(generateRowData())

// Filter state
const filterNamaProgram = ref('')
const filterPIC = ref('')
const filterTanggal = ref('')
const filterJumlahMin = ref('')
const filterJumlahMax = ref('')

// Filtered data based on filter
const filteredData = computed(() => {
  let filtered = [...rowDataArray.value]
  
  // Filter by Nama Program
  if (filterNamaProgram.value) {
    const searchTerm = filterNamaProgram.value.toLowerCase()
    filtered = filtered.filter((item) =>
      item.namaProgram.toLowerCase().includes(searchTerm)
    )
  }
  
  // Filter by PIC
  if (filterPIC.value) {
    const searchTerm = filterPIC.value.toLowerCase()
    filtered = filtered.filter((item) =>
      item.pic.toLowerCase().includes(searchTerm)
    )
  }
  
  // Filter by Tanggal
  if (filterTanggal.value) {
    filtered = filtered.filter((item) => {
      const itemDate = new Date(item.tanggal)
      const filterDate = new Date(filterTanggal.value)
      
      return (
        itemDate.getFullYear() === filterDate.getFullYear() &&
        itemDate.getMonth() === filterDate.getMonth() &&
        itemDate.getDate() === filterDate.getDate()
      )
    })
  }
  
  // Filter by Jumlah Dana Min
  if (filterJumlahMin.value) {
    const minAmount = parseFloat(filterJumlahMin.value)
    if (!isNaN(minAmount)) {
      filtered = filtered.filter((item) => item.jumlahDana >= minAmount)
    }
  }
  
  // Filter by Jumlah Dana Max
  if (filterJumlahMax.value) {
    const maxAmount = parseFloat(filterJumlahMax.value)
    if (!isNaN(maxAmount)) {
      filtered = filtered.filter((item) => item.jumlahDana <= maxAmount)
    }
  }
  
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

  rowDataArray.value = rowDataArray.value.filter((item) => item.id !== deleteId.value)
  toast.success('Penyaluran berhasil dihapus')
  showDeleteModal.value = false
  deleteId.value = null
  refreshGrid()
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
    if (agGridRef.value && agGridRef.value.api) {
      try {
        agGridRef.value.api.purgeInfiniteCache()
        agGridRef.value.api.refreshInfiniteCache()

        if (scrollToTop) {
          setTimeout(() => {
            if (agGridRef.value && agGridRef.value.api) {
              agGridRef.value.api.ensureIndexVisible(0, 'top')
            }
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
  refreshGrid()
})

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
watch([filterNamaProgram, filterPIC, filterTanggal, filterJumlahMin, filterJumlahMax], () => {
  // Clear existing timer
  if (filterDebounceTimer) {
    clearTimeout(filterDebounceTimer)
  }
  
  // Debounce filter update to prevent flickering
  filterDebounceTimer = setTimeout(() => {
    refreshGrid(true)
  }, 300) // 300ms debounce delay to prevent flickering
})

// Reset filter
const resetFilter = () => {
  filterNamaProgram.value = ''
  filterPIC.value = ''
  filterTanggal.value = ''
  filterJumlahMin.value = ''
  filterJumlahMax.value = ''
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
