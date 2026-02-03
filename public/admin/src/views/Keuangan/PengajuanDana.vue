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
            Tambah Pengajuan Dana
          </button>
        </div>
      </div>

      <!-- Filter Section -->
      <div class="mb-6 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
          <div class="flex-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Nama Pengaju
            </label>
            <input
              type="text"
              v-model="filterNamaPengaju"
              placeholder="Cari nama pengaju..."
              class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
          </div>
          <div class="flex-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Status
            </label>
            <SearchableSelect
              v-model="filterStatus"
              :options="statusFilterOptions"
              placeholder="Semua Status"
              :search-input="statusFilterSearchInput"
              @update:search-input="statusFilterSearchInput = $event"
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
              Tipe POS
            </label>
            <SearchableSelect
              v-model="filterTipePenyaluran"
              :options="tipePenyaluranOptions"
              placeholder="Semua Tipe"
              :search-input="tipePenyaluranSearchInput"
              @update:search-input="tipePenyaluranSearchInput = $event"
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
            :overlayNoRowsTemplate="overlayNoRowsTemplate"
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
        
      </div>
    </div>

    <ConfirmModal
      :isOpen="showDeleteModal"
      title="Hapus Pengajuan Dana"
      message="Apakah Anda yakin ingin menghapus pengajuan dana ini? Tindakan ini tidak dapat dibatalkan."
      confirmText="Hapus"
      confirmButtonClass="bg-red-500 hover:bg-red-600"
      @confirm="confirmDelete"
      @cancel="cancelDelete"
    />
    <ApprovalModal
      :isOpen="showApprovalModal"
      title="Justifikasi Persetujuan"
      message="Masukkan alasan atau catatan sebelum menyetujui/menolak pengajuan ini."
      confirmText="Kirim"
      confirmButtonClass="bg-brand-500 hover:bg-brand-600"
      @confirm="handleApprovalConfirm"
      @cancel="() => { showApprovalModal = false; approvalTargetId = null; approvalDecision = null }"
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
import ApprovalModal from '@/components/common/ApprovalModal.vue'
import SearchableSelect from '@/components/forms/SearchableSelect.vue'
import { useToast } from 'vue-toastification'
import { useAuth } from '@/composables/useAuth'
import { getCsrfTokenSafe } from '@/utils/getCsrfToken'

// Options for Status filter (value = DB value)
const statusFilterOptions = [
   { value: '', label: 'Semua' },
   { value: 'Approved', label: 'Disetujui' },
   { value: 'Rejected', label: 'Ditolak' },
   { value: 'Pending', label: 'Diajukan' },
   { value: 'Draft', label: 'Draft' },
]
const statusFilterSearchInput = ref('')

const tipePenyaluranOptions = ref<Array<{ value: string; label: string }>>([
   { value: '', label: 'Semua' },
])
const tipePenyaluranSearchInput = ref('')

const resolveApprovalLabel = (record: any): string => {
  if (!record) return '-'
  const latest = record.latest_approval ?? record.latestApproval ?? null
  const statusRaw = record.status ?? latest?.decision ?? ''
  const statusLowerRaw = typeof statusRaw === 'string' ? statusRaw.toLowerCase() : ''
  const statusLower = statusLowerRaw === 'disetujui'
    ? 'approved'
    : statusLowerRaw === 'ditolak'
      ? 'rejected'
      : statusLowerRaw
  const approvedName = record.approved_by_name ?? record.approvedByName ?? (statusLower === 'approved' ? latest?.approver?.name : null)
  const rejectedName = record.rejected_by_name ?? record.rejectedByName ?? (statusLower === 'rejected' ? latest?.approver?.name : null)

  if (statusLower === 'approved' && approvedName) {
    return `Disetujui oleh ${approvedName}`
  }

  if (statusLower === 'rejected' && rejectedName) {
    return `Ditolak oleh ${rejectedName}`
  }

  if (latest && latest.approver && latest.approver.name) {
    const decision = latest.decision || statusRaw || ''
    return decision ? `${decision} oleh ${latest.approver.name}` : latest.approver.name
  }

  return '-'
}

interface PengajuanDanaRow {
  id: string
  namaPengaju: string
  tanggalPemakaian: string
  submissionType: string
  purpose: string
  jumlahDana: number
  status: string
  tanggal: string
  persetujuan: string
  latestApproval?: any
}


const route = useRoute()
const router = useRouter()
const currentPageTitle = ref<string>(String(route.meta.title || 'Pengajuan Dana'))
const agGridRef = ref<InstanceType<typeof AgGridVue> | null>(null)
const isLoadingGrid = ref(true)
const toast = useToast()
const { fetchUser, hasPermission, isAdmin } = useAuth()
const canCreate = computed(() => isAdmin() || hasPermission('create pengajuan dana'))
const canUpdate = computed(() => isAdmin() || hasPermission('update pengajuan dana'))
const canDelete = computed(() => isAdmin() || hasPermission('delete pengajuan dana'))
const canView = computed(() => isAdmin() || hasPermission('view pengajuan dana'))
// allow either permission key (legacy 'approve pengajuan dana' or seeded 'approval pengajuan dana')
const canApprove = computed(() => isAdmin() || hasPermission('approve pengajuan dana') || hasPermission('approval pengajuan dana'))
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
    headerName: 'Nama Pengaju',
    field: 'namaPengaju',
    sortable: true,
    filter: false,
    // flex: 1,
  },
  {
    headerName: 'Jumlah Dana',
    field: 'jumlahDana',
    sortable: true,
    filter: false,
    // flex: 1,
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
    headerName: 'Tipe POS',
    field: 'submissionType',
    sortable: true,
    filter: false,
    // flex: 1,
    valueFormatter: (params: any) => {
      if (!params.value) return ''
      return String(params.value).charAt(0).toUpperCase() + String(params.value).slice(1)
    },
  },
  {
    headerName: 'Tujuan Pengajuan',
    field: 'purpose',
    sortable: true,
    filter: false,
    // flex: 1,
    cellRenderer: (params: any) => {
      if (!params.value) return ''
      const text = String(params.value)
      const div = document.createElement('div')
      // div.className = 'whitespace-normal py-2'
      div.textContent = text.length > 100 ? text.substring(0, 100) + '...' : text
      div.title = text
      return div
    },
  },
  {
    headerName: 'Tanggal Pemakaian',
    field: 'tanggalPemakaian',
    sortable: true,
    filter: false,
    // flex: 1,
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
    headerName: 'Status',
    field: 'status',
    sortable: true,
    filter: false,
    width: 140,
    cellRenderer: (params: any) => {
      const raw = params.value || ''
      const key = String(raw).toLowerCase()

      const map: Record<string, { label: string; classes: string }> = {
        'approved': { label: 'Disetujui', classes: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' },
        'rejected': { label: 'Ditolak', classes: 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' },
        'pending': { label: 'Diajukan', classes: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' },
        'draft': { label: 'Draft', classes: 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400' },
      }

      const info = map[key] || (raw ? { label: String(raw), classes: 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400' } : { label: '', classes: '' })

      const span = document.createElement('span')
      span.className = `px-2 py-1 rounded-full text-xs font-medium ${info.classes}`
      span.textContent = info.label
      return span
    },
  },
  {
    headerName: 'Tanggal',
    field: 'tanggal',
    sortable: true,
    filter: false,
    // flex: 1,
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
    headerName: 'Persetujuan',
    field: 'persetujuan',
    sortable: true,
    filter: false,
    // flex: 1,
  },
  {
    headerName: 'Actions',
    field: 'actions',
    sortable: false,
    filter: false,
    width: 160,
    pinned: 'right',
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
      // Approve / Reject buttons for approvers
      if (canApprove.value) {
        const approveBtn = document.createElement('button')
        approveBtn.className = 'flex items-center justify-center w-8 h-8 rounded-lg text-green-600 hover:bg-green-50 dark:hover:bg-green-600/10 transition-colors'
        approveBtn.innerHTML = `
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20 6L9 17l-5-5"></path>
          </svg>
        `
        approveBtn.title = 'Approve'
        approveBtn.onclick = () => openApprovalModal(params.data.id, 'Approved')

        const rejectBtn = document.createElement('button')
        rejectBtn.className = 'flex items-center justify-center w-8 h-8 rounded-lg text-red-600 hover:bg-red-50 dark:hover:bg-red-600/10 transition-colors'
        rejectBtn.innerHTML = `
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 6L6 18M6 6l12 12"></path>
          </svg>
        `
        rejectBtn.title = 'Reject'
        rejectBtn.onclick = () => openApprovalModal(params.data.id, 'Rejected')

        // Only show approve/reject when current status is Pending or Draft (or not already final)
        const st = (params.data && params.data.status) ? String(params.data.status).toLowerCase() : ''
        if (!['approved', 'rejected'].includes(st)) {
          div.appendChild(approveBtn)
          div.appendChild(rejectBtn)
        }
      }
      
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

// Data will be loaded from API via datasource
const rowDataArray = ref<PengajuanDanaRow[]>([])

// Filter state
const filterNamaPengaju = ref('')
const filterStatus = ref('')
const filterTanggal = ref('')
const filterTipePenyaluran = ref('')
const filterJumlahMax = ref('')

// Filtered data based on filter
const filteredData = computed(() => {
  let filtered = [...rowDataArray.value]
  
  // Filter by Nama Pengaju
  if (filterNamaPengaju.value) {
    const searchTerm = filterNamaPengaju.value.toLowerCase()
    filtered = filtered.filter((item) =>
      item.namaPengaju.toLowerCase().includes(searchTerm)
    )
  }
  
  // Filter by Status
  if (filterStatus.value) {
    filtered = filtered.filter((item) => item.status === filterStatus.value)
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
  
  // Filter by Tipe Penyaluran
  if (filterTipePenyaluran.value) {
    filtered = filtered.filter((item) => item.submissionType === filterTipePenyaluran.value)
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

// Show empty state flag (not used - AG Grid overlay will be used)
const showEmptyState = computed(() => false)

// Handle add button
const handleAdd = () => {
  router.push('/keuangan/pengajuan-dana/new')
}

// Handle edit
const handleEdit = (id: string) => {
  router.push(`/keuangan/pengajuan-dana/${id}/edit`)
}

// Handle delete
const handleDelete = (id: string) => {
  deleteId.value = id
  showDeleteModal.value = true
}

const confirmDelete = () => {
  if (!deleteId.value) return

  ;(async () => {
    try {
      const csrf = await getCsrfTokenSafe()
      const res = await fetch(`/admin/api/pengajuan-dana/${deleteId.value}`, {
        method: 'DELETE',
        credentials: 'same-origin',
        headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' },
      })
      const json = await res.json()
      if (!json.success) throw new Error(json.message || 'Delete failed')

      toast.success('Pengajuan dana berhasil dihapus')
      showDeleteModal.value = false
      deleteId.value = null
      refreshGrid()
    } catch (err) {
      console.error('Delete error', err)
      toast.error('Gagal menghapus pengajuan dana')
    }
  })()
}

const cancelDelete = () => {
  showDeleteModal.value = false
  deleteId.value = null
}

// Handle export to Excel
const handleExportExcel = () => {
  // Build query like datasource but request many rows for export
  const qp: Record<string, any> = { per_page: 10000, page: 1 }
  if (filterNamaPengaju.value) qp.search = filterNamaPengaju.value
  if (filterStatus.value) qp.status = filterStatus.value
  if (filterTanggal.value) qp.tanggal = filterTanggal.value
  if (filterTipePenyaluran.value) qp.submission_type = filterTipePenyaluran.value
  if (filterJumlahMax.value) qp.amount_max = filterJumlahMax.value

  const queryString = Object.keys(qp).map(k => encodeURIComponent(k) + '=' + encodeURIComponent(qp[k])).join('&')

  fetch(`/admin/api/pengajuan-dana?${queryString}`, { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(res => res.json())
    .then((json) => {
      if (!json.success) {
        toast.error('Gagal mengambil data untuk export')
        return
      }

      const dataToExport = (json.data || []).map((item: any) => ({
        'Nama Pengaju': item.fundraiser ? item.fundraiser.name : '-',
        'Tanggal Pemakaian': item.used_at ? new Date(item.used_at).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' }) : '',
        'Tipe Pengajuan': item.submission_type ? String(item.submission_type).charAt(0).toUpperCase() + String(item.submission_type).slice(1) : '',
        'Tujuan Pengajuan': item.purpose || '',
        'Jumlah Dana': item.amount ? new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(item.amount) : '',
        'Status': item.status || '',
        'Tanggal': item.created_at ? new Date(item.created_at).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' }) : '',
        'Persetujuan': resolveApprovalLabel(item),
      }))

      const worksheet = XLSX.utils.json_to_sheet(dataToExport)
      const workbook = XLSX.utils.book_new()
      XLSX.utils.book_append_sheet(workbook, worksheet, 'Pengajuan Dana')
      const now = new Date()
      const filename = `Pengajuan_Dana_${now.toISOString().split('T')[0]}.xlsx`
      XLSX.writeFile(workbook, filename)
    })
    .catch((err) => {
      console.error('Export error', err)
      toast.error('Gagal mengekspor data')
    })
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
      if (colId === 'tanggal' || colId === 'tanggalPemakaian') {
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
      // Map AG Grid request to API pagination
      const start = params.startRow || 0
      const end = params.endRow || 0
      const pageSize = Math.max(10, (end - start))
      const page = Math.floor(start / pageSize) + 1

      // Build query params from filters
      const qp: Record<string, any> = {
        per_page: pageSize,
        page,
      }

      if (filterNamaPengaju.value) qp.search = filterNamaPengaju.value
      if (filterStatus.value) qp.status = filterStatus.value
      if (filterTanggal.value) qp.tanggal = filterTanggal.value
      if (filterTipePenyaluran.value) qp.submission_type = filterTipePenyaluran.value
      if (filterJumlahMax.value) qp.amount_max = filterJumlahMax.value

      // Sorting: map first sortModel entry
      if (params.sortModel && params.sortModel.length > 0) {
        const s = params.sortModel[0]
        const colMap: Record<string, string> = {
          namaPengaju: 'fundraiser',
          tanggalPemakaian: 'used_at',
          jumlahDana: 'amount',
          tanggal: 'created_at',
        }
        if (colMap[s.colId]) {
          qp.sort_by = colMap[s.colId]
          qp.sort_dir = s.sort === 'asc' ? 'asc' : 'desc'
        }
      }

      const queryString = Object.keys(qp).map(k => encodeURIComponent(k) + '=' + encodeURIComponent(qp[k])).join('&')

      fetch(`/admin/api/pengajuan-dana?${queryString}`, { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(res => res.json())
        .then((json) => {
          if (!json.success) {
            params.failCallback()
            return
          }

          const rows = (json.data || []).map((r: any) => ({
            id: r.id,
            namaPengaju: r.fundraiser ? r.fundraiser.name : '-',
            tanggalPemakaian: r.used_at || '',
            submissionType: r.submission_type || '',
            purpose: r.purpose || '',
            jumlahDana: r.amount || 0,
            status: r.status || 'Draft',
            tanggal: r.created_at ? r.created_at.split(' ')[0] : '',
            persetujuan: resolveApprovalLabel(r),
            latestApproval: r.latest_approval || null,
          }))

          let lastRow: number | undefined = undefined
          if (typeof json.pagination?.total === 'number') {
            lastRow = json.pagination.total
          }

          params.successCallback(rows, lastRow)
          // Hide loading overlay after first successful load
          isLoadingGrid.value = false
          // Show AG Grid built-in no-rows overlay when there are no rows
          const gridApi = (agGridRef.value as any)?.api
          try {
            if (gridApi) {
              if (!rows || rows.length === 0) gridApi.showNoRowsOverlay()
              else gridApi.hideOverlay()
            }
          } catch (e) {
            // ignore overlay errors
          }
        })
        .catch((err) => {
          console.error('Error fetching pengajuan-dana:', err)
          params.failCallback()
        })
    },
  }
}

// Infinite scroll datasource - create as ref for reactivity
const dataSource = ref<IDatasource>(createDataSource())

// AG Grid overlay template when no rows
const overlayNoRowsTemplate = `
  <div class="p-6 text-center text-gray-600 dark:text-gray-400">
    <svg class="mx-auto mb-3 w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
    </svg>
    <div class="text-lg font-medium">Tidak ada data</div>
    <div class="text-sm mt-1">Belum ada data yang tersedia</div>
  </div>
`

const refreshGrid = (scrollToTop = false) => {
  const newDataSource = createDataSource()
  dataSource.value.getRows = newDataSource.getRows

  nextTick(() => {
    const gridApi = (agGridRef.value as any)?.api
    if (gridApi) {
      try {
        gridApi.purgeInfiniteCache()
        gridApi.refreshInfiniteCache()

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

// Fetch submission types from API (same as PengajuanDanaForm)
const fetchSubmissionTypes = async () => {
  try {
    const res = await fetch('/admin/api/program-share-types/submission-types', {
      credentials: 'same-origin',
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    const json = await res.json()
    if (json.success && Array.isArray(json.data)) {
      // Map API data to filter options, always keep "Semua" at the top
      const options = json.data.map((item: any) => ({
        value: item.value,
        label: item.value,
      }))
      tipePenyaluranOptions.value = [
        { value: '', label: 'Semua' },
        ...options
      ]
    }
  } catch (err) {
    console.error('Error fetching submission types', err)
    // Keep default "Semua" option if API fails
  }
}

// Set datasource after component is mounted
onMounted(() => {
  fetchUser()
  fetchSubmissionTypes()
  refreshGrid()
})

// Approval modal state
const showApprovalModal = ref(false)
const approvalTargetId = ref<string | null>(null)
const approvalDecision = ref<'Approved'|'Rejected'|'Pending'|null>(null)

const openApprovalModal = (id: string, decision: 'Approved'|'Rejected') => {
  approvalTargetId.value = id
  approvalDecision.value = decision
  showApprovalModal.value = true
}

const handleApprovalConfirm = async (comment: string) => {
  if (!approvalTargetId.value || !approvalDecision.value) return
  try {
    const csrf = await getCsrfTokenSafe()
    const res = await fetch(`/admin/api/pengajuan-dana/${approvalTargetId.value}/approve`, {
      method: 'POST',
      credentials: 'same-origin',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' },
      body: JSON.stringify({ decision: approvalDecision.value, comment }),
    })
    const json = await res.json()
    if (!json.success) throw new Error(json.message || 'Approval failed')

    toast.success('Tindakan persetujuan berhasil')
    showApprovalModal.value = false
    approvalTargetId.value = null
    approvalDecision.value = null
    refreshGrid(true)
  } catch (err) {
    console.error('Approval error', err)
    toast.error('Gagal melakukan persetujuan')
  }
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
watch([filterNamaPengaju, filterStatus, filterTanggal, filterTipePenyaluran, filterJumlahMax], () => {
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
  filterNamaPengaju.value = ''
  filterStatus.value = ''
  filterTanggal.value = ''
  filterTipePenyaluran.value = ''
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
