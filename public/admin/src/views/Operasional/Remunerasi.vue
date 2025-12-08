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
            Tambah Remunerasi
          </button>
        </div>
      </div>

      <!-- Filter Section -->
      <div class="mb-6 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
          <div class="flex-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Nama Karyawan
            </label>
            <input
              type="text"
              v-model="filterNamaKaryawan"
              placeholder="Cari nama karyawan..."
              class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
          </div>
          <div class="flex-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Bulan
            </label>
            <SearchableSelect
              v-model="filterBulan"
              :options="bulanFilterOptions"
              placeholder="Semua Bulan"
              :search-input="bulanFilterSearchInput"
              @update:search-input="bulanFilterSearchInput = $event"
            />
          </div>
          <div class="flex-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Tahun
            </label>
            <input
              type="number"
              v-model="filterTahun"
              placeholder="Tahun..."
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
        </div>
        <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
          <div class="flex-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Take Home Pay Min
            </label>
            <input
              type="number"
              v-model="filterTakeHomePayMin"
              placeholder="Take home pay minimum..."
              class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
          </div>
          <div class="flex-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Take Home Pay Max
            </label>
            <input
              type="number"
              v-model="filterTakeHomePayMax"
              placeholder="Take home pay maksimum..."
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

      <div class="ag-theme-alpine dark:ag-theme-alpine-dark" style="width: 100%;">
        <ag-grid-vue
          class="ag-theme-alpine"
          style="width: 100%;"
          :columnDefs="columnDefs"
          :rowData="gridRowData"
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

    <!-- Confirm Delete Modal -->
    <ConfirmModal
      :isOpen="showDeleteModal"
      title="Hapus Remunerasi"
      message="Apakah Anda yakin ingin menghapus remunerasi ini? Tindakan ini tidak dapat dibatalkan."
      confirmText="Hapus"
      confirmButtonClass="bg-red-500 hover:bg-red-600"
      @confirm="confirmDelete"
      @cancel="cancelDelete"
    />
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { AgGridVue } from 'ag-grid-vue3'
import flatPickr from 'vue-flatpickr-component'
import * as XLSX from 'xlsx'
import 'flatpickr/dist/flatpickr.css'
import 'ag-grid-community/styles/ag-grid.css'
import 'ag-grid-community/styles/ag-theme-alpine.css'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import ConfirmModal from '@/components/common/ConfirmModal.vue'
import SearchableSelect from '@/components/forms/SearchableSelect.vue'
import { useToast } from 'vue-toastification'

// Options for Bulan filter
const bulanFilterOptions = [
  { value: '', label: 'Semua Bulan' },
  { value: '1', label: 'Januari' },
  { value: '2', label: 'Februari' },
  { value: '3', label: 'Maret' },
  { value: '4', label: 'April' },
  { value: '5', label: 'Mei' },
  { value: '6', label: 'Juni' },
  { value: '7', label: 'Juli' },
  { value: '8', label: 'Agustus' },
  { value: '9', label: 'September' },
  { value: '10', label: 'Oktober' },
  { value: '11', label: 'November' },
  { value: '12', label: 'Desember' },
]
const bulanFilterSearchInput = ref('')

const route = useRoute()
const router = useRouter()
const currentPageTitle = computed(() => (route.meta.title as string) || 'Remunerasi')

// Delete modal state
const showDeleteModal = ref(false)
const deleteId = ref<string | null>(null)
const toast = useToast()

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
    headerName: 'Nama Karyawan',
    field: 'namaKaryawan',
    sortable: true,
    flex: 1,
  },
  {
    headerName: 'Bulan Remunerasi',
    field: 'bulanRemunerasi',
    sortable: true,
    width: 150,
    valueFormatter: (params: any) => {
      if (params.value) {
        const months = [
          'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
          'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ]
        return months[params.value - 1] || params.value
      }
      return ''
    },
  },
  {
    headerName: 'Tahun Remunerasi',
    field: 'tahunRemunerasi',
    sortable: true,
    width: 150,
  },
  {
    headerName: 'Take Home Pay',
    field: 'takeHomePay',
    sortable: true,
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
    headerName: 'Tanggal',
    field: 'tanggal',
    sortable: true,
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
  filter: true,
}

// row data (loaded from API)
const rowDataArray = ref([])
const loading = ref(false)

// pagination state (optional)
const pagination = ref({ current_page: 1, per_page: 20, total: 0 })

const buildQuery = () => {
  const params = new URLSearchParams()
  params.append('per_page', String(pagination.value.per_page || 1000))
  // name filtering is done client-side against loaded data
  if (filterBulan.value) params.append('bulan', filterBulan.value)
  if (filterTahun.value) params.append('tahun', filterTahun.value)
  if (filterTanggal.value) params.append('tanggal', filterTanggal.value)
  if (filterTakeHomePayMin.value) params.append('min_take_home', filterTakeHomePayMin.value)
  if (filterTakeHomePayMax.value) params.append('max_take_home', filterTakeHomePayMax.value)
  if (filterKantorCabangId && filterKantorCabangId.value) params.append('kantor_cabang_id', filterKantorCabangId.value)
  return params.toString()
}

const loadData = async () => {
  loading.value = true
  try {
    const qs = buildQuery()
    const url = `/admin/api/operasional/remunerasi?${qs}`
    const res = await fetch(url, { credentials: 'same-origin' })
    if (!res.ok) throw new Error('Failed to load remunerasi')
    const json = await res.json()
    const items = json.data || []
    // map to table shape (for backward compatibility)
    rowDataArray.value = items.map((it) => ({
      id: it.id,
      namaKaryawan: it.karyawan ? (it.karyawan.name || '') : '',
      bulanRemunerasi: it.bulan_remunerasi,
      tahunRemunerasi: it.tahun_remunerasi,
      takeHomePay: it.take_home_pay,
      tanggal: it.tanggal,
      raw: it,
    }))
    if (json.pagination) {
      pagination.value.current_page = json.pagination.current_page || 1
      pagination.value.per_page = json.pagination.per_page || pagination.value.per_page
      pagination.value.total = json.pagination.total || 0
    }
  } catch (e) {
    console.error('Error loading remunerasi:', e)
    toast.error('Gagal memuat data remunerasi')
  } finally {
    loading.value = false
  }
}

// optional filter: kantor cabang id (not in template yet, but keep)
const filterKantorCabangId = ref('')

// Handle add button - redirect to form page
const handleAdd = () => {
  router.push('/operasional/remunerasi/new')
}

// Handle edit - redirect to form page
const handleEdit = (id: string) => {
  router.push(`/operasional/remunerasi/${id}/edit`)
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
    // fetch CSRF token
    const t = await fetch('/admin/api/csrf-token', { credentials: 'same-origin' })
    const tk = await t.json()
    const r = await fetch(`/admin/api/operasional/remunerasi/${deleteId.value}`, {
      method: 'DELETE',
      credentials: 'same-origin',
      headers: {
        'X-CSRF-TOKEN': tk.csrf_token,
      },
    })

    const j = await r.json().catch(() => ({}))
    if (!r.ok) throw new Error((j && j.message) || 'Gagal menghapus remunerasi')

    toast.success((j && j.message) || 'Remunerasi berhasil dihapus')
    // reload
    await loadData()
  } catch (error: any) {
    console.error('Error deleting remunerasi:', error)
    toast.error('Gagal menghapus remunerasi')
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

// Filter state
const filterNamaKaryawan = ref('')
const filterBulan = ref('')
const filterTahun = ref('')
const filterTanggal = ref('')
const filterTakeHomePayMin = ref('')
const filterTakeHomePayMax = ref('')

// Filtered data for AG Grid
const gridRowData = computed(() => {
  let filtered = [...rowDataArray.value]
  
  // Filter by nama karyawan
  if (filterNamaKaryawan.value) {
    filtered = filtered.filter((item) =>
      item.namaKaryawan.toLowerCase().includes(filterNamaKaryawan.value.toLowerCase())
    )
  }
  
  // Filter by bulan
  if (filterBulan.value) {
    const bulan = parseInt(filterBulan.value)
    filtered = filtered.filter((item) => item.bulanRemunerasi === bulan)
  }
  
  // Filter by tahun
  if (filterTahun.value) {
    const tahun = parseInt(filterTahun.value)
    filtered = filtered.filter((item) => item.tahunRemunerasi === tahun)
  }
  
  // Filter by tanggal
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
  
  // Filter by Take Home Pay Min
  if (filterTakeHomePayMin.value) {
    const minAmount = parseFloat(filterTakeHomePayMin.value)
    if (!isNaN(minAmount)) {
      filtered = filtered.filter((item) => item.takeHomePay >= minAmount)
    }
  }
  
  // Filter by Take Home Pay Max
  if (filterTakeHomePayMax.value) {
    const maxAmount = parseFloat(filterTakeHomePayMax.value)
    if (!isNaN(maxAmount)) {
      filtered = filtered.filter((item) => item.takeHomePay <= maxAmount)
    }
  }
  
  return filtered
})

// Reset filter
const resetFilter = () => {
  filterNamaKaryawan.value = ''
  filterBulan.value = ''
  filterTahun.value = ''
  filterTanggal.value = ''
  filterTakeHomePayMin.value = ''
  filterTakeHomePayMax.value = ''
}

// Handle export to Excel
const handleExportExcel = () => {
  const months = [
    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
  ]
  
  const dataToExport = gridRowData.value.map((item) => {
    const tanggal = new Date(item.tanggal)
    
    return {
      'Nama Karyawan': item.namaKaryawan,
      'Bulan Remunerasi': months[item.bulanRemunerasi - 1] || item.bulanRemunerasi,
      'Tahun Remunerasi': item.tahunRemunerasi,
      'Take Home Pay': new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
      }).format(item.takeHomePay),
      'Tanggal': tanggal.toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
      }),
    }
  })
  
  const worksheet = XLSX.utils.json_to_sheet(dataToExport)
  const workbook = XLSX.utils.book_new()
  XLSX.utils.book_append_sheet(workbook, worksheet, 'Remunerasi')
  
  const now = new Date()
  const filename = `Remunerasi_${now.toISOString().split('T')[0]}.xlsx`
  
  XLSX.writeFile(workbook, filename)
}

// load initial data after all refs/computed are declared to avoid TDZ issues
loadData()
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

