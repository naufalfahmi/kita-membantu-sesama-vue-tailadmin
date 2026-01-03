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
            @click="handleExportCsv"
            class="flex items-center gap-1 sm:gap-2 rounded-lg border border-gray-300 bg-white px-2 py-1.5 text-xs sm:px-3 sm:py-2.5 sm:text-sm font-medium text-gray-700 hover:bg-gray-50"
            title="Export CSV (delimiter: ;)"
          >
            <svg class="fill-current" width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M4 4H16V6H4V4ZM4 8H16V10H4V8ZM4 12H16V14H4V12Z" fill="currentColor"/>
            </svg>
            Export CSV
          </button>
          <button
            @click="handleExportProgram"
            class="flex items-center gap-1 sm:gap-2 rounded-lg border border-gray-300 bg-white px-2 py-1.5 text-xs sm:px-3 sm:py-2.5 sm:text-sm font-medium text-gray-700 hover:bg-gray-50"
            title="Export Program CSV (detailed program shares)"
          >
            <svg class="fill-current" width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M3 4H17V6H3V4ZM3 8H11V10H3V8ZM3 12H11V14H3V12Z" fill="currentColor"/>
            </svg>
            Export Program
          </button>
          <button
            v-if="canCreate"
            @click="handleAdd"
            class="flex items-center gap-1 sm:gap-2 rounded-lg bg-brand-500 px-3 py-1.5 sm:px-4 sm:py-2.5 text-xs sm:text-sm font-medium text-white hover:bg-brand-600"
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
              Rentang Tanggal Transaksi
            </label>
            <div class="relative">
              <FlatPickr
                v-model="filterTanggal"
                :config="flatpickrDateConfig"
                @on-change="debouncedFetch"
                class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-11 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                placeholder="Pilih rentang tanggal"
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
            <AsyncSearchableSelect
              v-model="filterDonatur"
              fetch-url="/admin/api/donatur"
              placeholder="Semua Donatur"
              :include-all="true"
              all-label="Semua Donatur"
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
              Mitra
            </label>
            <SearchableSelect
              v-model="filterMitra"
              :options="mitraOptions"
              placeholder="Semua Mitra"
              :search-input="mitraSearchInput"
              @update:search-input="mitraSearchInput = $event"
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

      <div v-else class="ag-theme-alpine dark:ag-theme-alpine-dark" style="width: 100%; height: 600px;">
        <ag-grid-vue
          ref="agGridRef"
          class="ag-theme-alpine"
          style="width: 100%; height: 100%;"
          :columnDefs="columnDefs"
          :defaultColDef="defaultColDef"
          :rowModelType="'infinite'"
          :cacheBlockSize="pageSize"
          :maxBlocksInCache="5"
          theme="legacy"
          :animateRows="true"
          :suppressHorizontalScroll="true"
          @grid-ready="onGridReady"
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
import { useRoute, useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'
import { AgGridVue } from 'ag-grid-vue3'
import FlatPickr from 'vue-flatpickr-component'
import * as XLSX from 'xlsx'
import JSZip from 'jszip'
import 'flatpickr/dist/flatpickr.css'
import 'ag-grid-community/styles/ag-grid.css'
import 'ag-grid-community/styles/ag-theme-alpine.css'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import ConfirmModal from '@/components/common/ConfirmModal.vue'
import SearchableSelect from '@/components/forms/SearchableSelect.vue'
import AsyncSearchableSelect from '@/components/forms/AsyncSearchableSelect.vue'
import { useAuth } from '@/composables/useAuth'

interface TransaksiRow {
  id: string
  kode: string | null
  donatur: string | null
  mitra: string | null
  fundraiser: string | null
  program: string | null
  kantor_cabang: string | null
  nominal: number
  nominal_formatted: string
  tanggal_transaksi: string | null
  tanggal_dibuat: string | null
  keterangan: string | null
}

const route = useRoute()
const router = useRouter()
const toast = useToast()

const currentPageTitle = ref<string>(String(route.meta.title || 'Transaksi'))

// Permissions
const { fetchUser, hasPermission, isAdmin, user } = useAuth()
const canCreate = computed(() => isAdmin() || hasPermission('create transaksi'))
const canUpdate = computed(() => isAdmin() || hasPermission('update transaksi'))
const canDelete = computed(() => isAdmin() || hasPermission('delete transaksi'))
const canView = computed(() => isAdmin() || hasPermission('view transaksi'))

const agGridRef = ref<InstanceType<typeof AgGridVue> | null>(null)

// AG Grid server-side/infinite settings
const gridApi = ref<any | null>(null)
const gridColumnApi = ref<any | null>(null)
const pageSize = 20

const loading = ref(false)
const rowData = ref<TransaksiRow[]>([])
const filterSearch = ref('')
const filterTanggal = ref<any>([])
const filterDonatur = ref('')
const filterProgram = ref('')
const filterFundraiser = ref('')
const filterKantorCabang = ref('')
const filterMitra = ref('')

// Helpers used by exports: sanitize control characters and CSV escaping (semicolon delimiter)
const sanitizeString = (s: any) => String(s || '').replace(/[\x00-\x08\x0B\x0C\x0E-\x1F]/g, '')
const csvEscape = (v: any) => {
  const s = sanitizeString(v === null || v === undefined ? '' : String(v))
  const needQuote = s.includes(';') || s.includes('"') || s.includes('\n') || s.includes('\r')
  const escaped = s.replace(/"/g, '""')
  return needQuote ? `"${escaped}"` : escaped
}

// Shared number formatter for nominal columns
const formatCurrency = (n: number) => {
  try {
    return new Intl.NumberFormat('id-ID').format(Number(n || 0))
  } catch (e) {
    return String(n || 0)
  }
}


// Sanitize sheet name to be a valid Excel sheet/file name, and ensure uniqueness where used
const sanitizeSheetName = (s: string) => {
  let name = String(s || '')
    .replace(/[\/*?:\[\]]+/g, '-')
    .slice(0, 31)
    .trim()
  if (!name) name = 'Sheet'
  return name
}
const showDeleteModal = ref(false)
const deleteId = ref<string | null>(null)
let debounceTimer: ReturnType<typeof setTimeout> | undefined

// Dropdown options
const donaturList = ref<any[]>([])
const donaturMap = computed(() => {
  const map = new Map<string, any>()
  for (const item of donaturList.value) {
    if (item?.id) {
      map.set(String(item.id), item)
    }
  }
  return map
})

const resolveDonaturInfo = (transaksi: any) => {
  if (!transaksi) return {}
  const directDonatur = transaksi.donatur || {}
  const id = directDonatur?.id || transaksi.donatur_id
  const lookup = id ? donaturMap.value.get(String(id)) : undefined
  const baseInfo = lookup || directDonatur || {}
  const merged = { ...baseInfo }

  const kantorId =
    merged.kantor_cabang_id ||
    directDonatur?.kantor_cabang_id ||
    transaksi?.kantor_cabang_id ||
    transaksi?.kantor_cabang?.id

  const kantorSource =
    (kantorId && kantorCabangMap.value.get(String(kantorId))) ||
    transaksi?.kantor_cabang ||
    merged.kantor_cabang

  if (kantorSource) {
    // Keep kantor cabang reference but do NOT copy address fields from kantor cabang.
    // Address fields (provinsi, kota_kab, kecamatan, kelurahan) should come
    // exclusively from the donatur record itself to match export requirements.
    merged.kantor_cabang = merged.kantor_cabang || kantorSource
  }

  return merged
}
const programList = ref<any[]>([])
const fundraiserList = ref<any[]>([])
const kantorCabangList = ref<any[]>([])
const mitraList = ref<any[]>([])

const kantorCabangMap = computed(() => {
  const map = new Map<string, any>()
  for (const item of kantorCabangList.value) {
    if (item?.id) {
      map.set(String(item.id), item)
    }
  }
  return map
})

// Search inputs for SearchableSelect
const donaturSearchInput = ref('')
const programSearchInput = ref('')
const fundraiserSearchInput = ref('')
const kantorCabangSearchInput = ref('')
const mitraSearchInput = ref('')

// Computed options for SearchableSelect
const donaturOptions = computed(() => [
  { value: '', label: 'Semua Donatur' },
  ...donaturList.value.map((item) => ({
    value: item.id,
    label: item.nama || '-',
  })),
])

const programOptions = computed(() => [
  { value: '', label: 'Semua Program' },
  ...programList.value.map((item) => ({
    value: item.id,
    label: item.nama_program || '-',
  })),
])

const mitraOptions = computed(() => [
  { value: '', label: 'Semua Mitra' },
  ...mitraList.value.map((item: any) => ({ value: item.id, label: item.nama || item.name || '-' })),
])

const fundraiserOptions = computed(() => {
  const roleName = String(user?.value?.role?.name || '').toLowerCase()
  const isLeader = roleName.includes('leader') || roleName.includes('lead')
  const isAtasan = roleName.includes('atasan') || roleName.includes('supervisor') || roleName.includes('manager')

  const map = new Map<string, string>()

  const userIdStr = user?.value?.id ? String(user.value.id) : null
  const userEmail = user?.value?.email ? String(user.value.email).toLowerCase() : null

  const isManagedByUser = (entity: any) => {
    if (!entity) return false
    const checkIds = [
      'atasan_id',
      'leader_id',
      'parent_id',
      'supervisor_id',
      'manager_id',
      'manager_id',
    ]
    for (const k of checkIds) {
      if (entity[k] && userIdStr && String(entity[k]) === userIdStr) return true
    }
    // nested objects
    const nestedKeys = ['atasan', 'leader', 'parent', 'supervisor', 'manager']
    for (const nk of nestedKeys) {
      const obj = entity[nk]
      if (obj) {
        if (obj.id && userIdStr && String(obj.id) === userIdStr) return true
        if (obj.email && userEmail && String(obj.email).toLowerCase() === userEmail) return true
      }
    }
    // emails on root
    const emailKeys = ['atasan_email', 'leader_email', 'parent_email', 'supervisor_email', 'manager_email', 'email']
    for (const ek of emailKeys) {
      if (entity[ek] && userEmail && String(entity[ek]).toLowerCase() === userEmail) return true
    }
    return false
  }

  // Helper to add an entry
  const addEntry = (id: any, label: any) => {
    if (!id) return
    const key = String(id)
    if (!map.has(key)) map.set(key, String(label || '-'))
  }

  // If user is 'atasan' show only themselves
  if (isAtasan && user?.value?.id) {
    addEntry(user.value.id, user.value.name || user.value.nama || user.value.email || 'Saya')
    return Array.from(map.entries()).map(([value, label]) => ({ value, label }))
  }

  // For leaders, include only subordinates + themselves
  if (isLeader && user?.value?.id) {
    // add karyawan entries that look like subordinates (try common keys)
    for (const item of fundraiserList.value) {
      if (!item || !item.id) continue
      if (isManagedByUser(item) || (userIdStr && String(item.id) === userIdStr)) {
        addEntry(item.id, item.name || item.nama || item.label || item.email || item.username)
      }
    }

    // include PICs from donatur that belong to subordinates
    for (const donor of donaturList.value) {
      const pic = donor?.pic_user || (donor?.pic ? { id: donor.pic, nama: donor.pic_nama || donor.pic_name || '' } : null)
      if (!pic || !pic.id) continue
      // check pic object for manager/leader relation or id/email match
      if (isManagedByUser(pic) || (userIdStr && String(pic.id) === userIdStr)) {
        addEntry(pic.id, pic.nama || pic.name || pic.label || '-')
      }
    }

    // Ensure current user is present
    addEntry(user.value.id, user.value.name || user.value.nama || user.value.email || 'Saya')

    return Array.from(map.entries()).map(([value, label]) => ({ value, label }))
  }

  // Default behavior: include all fundraisers + unique PICs
  // include the global "Semua Fundraiser" option in default case
  map.set('', 'Semua Fundraiser')
  for (const item of fundraiserList.value) {
    if (item?.id) addEntry(item.id, item.name || item.nama || '-')
  }
  for (const donor of donaturList.value) {
    const pic = donor?.pic_user || (donor?.pic ? { id: donor.pic, nama: donor.pic_nama || donor.pic_name || '' } : null)
    if (pic && pic.id) addEntry(pic.id, pic.nama || pic.name || '-')
  }

  return Array.from(map.entries()).map(([value, label]) => ({ value, label }))
})

// Compute subordinate IDs for leader users (used to filter datasource client-side)
const subordinateFundraiserIds = computed(() => {
  const roleName = String(user?.value?.role?.name || '').toLowerCase()
  const isLeader = roleName.includes('leader') || roleName.includes('lead')
  const set = new Set<string>()
  if (!isLeader || !user?.value?.id) return set

  for (const item of fundraiserList.value) {
    if (!item || !item.id) continue
    if (isManagedByUser(item) || (userIdStr && String(item.id) === userIdStr)) set.add(String(item.id))
  }

  for (const donor of donaturList.value) {
    const pic = donor?.pic_user || (donor?.pic ? { id: donor.pic } : null)
    if (!pic || !pic.id) continue
    if (isManagedByUser(pic) || (userIdStr && String(pic.id) === userIdStr)) set.add(String(pic.id))
  }

  // Always include current user id
  set.add(String(user.value.id))
  return set
})

const kantorCabangOptions = computed(() => [
  { value: '', label: 'Semua Kantor Cabang' },
  ...kantorCabangList.value.map((item) => ({
    value: item.id,
    label: item.nama || '-',
  })),
])



const flatpickrDateConfig = ({
  mode: 'range',
  dateFormat: 'Y-m-d',
  altInput: true,
  altFormat: 'd/m/Y',
  wrap: false,
} as any)

// Format date to local YYYY-MM-DD (avoid timezone shifts from toISOString)
const formatYMDLocal = (d: any) => {
  if (!d) return ''
  if (typeof d === 'string') return d
  if (d instanceof Date && !isNaN(d.getTime())) {
    const yyyy = d.getFullYear()
    const mm = String(d.getMonth() + 1).padStart(2, '0')
    const dd = String(d.getDate()).padStart(2, '0')
    return `${yyyy}-${mm}-${dd}`
  }
  return String(d)
}

const columnDefs = computed(() => {
  const roleName = String(user?.value?.role?.name || '').toLowerCase()
  const isFundrisingRole = roleName === 'fundrising' || roleName === 'fundraising'
  const isRoleAdminCabang = (() => {
    const roles = user?.value?.roles || (user?.value?.role ? [user.value.role] : [])
    if (!Array.isArray(roles)) return false
    return roles.some((r: any) => {
      const name = typeof r === 'string' ? r : r?.name
      return typeof name === 'string' && name.trim().toLowerCase() === 'admin cabang'
    })
  })()

  // If the current user's jabatan/role is Fundrising, show only the requested columns
  if (isFundrisingRole) {
    return [
      {
        headerName: 'No',
        field: '__no',
        width: 80,
        sortable: false,
        valueGetter: (params: any) => {
          try {
            return (params.node && typeof params.node.rowIndex === 'number') ? params.node.rowIndex + 1 : '-'
          } catch (e) {
            return '-'
          }
        },
      },
      {
        headerName: 'Donatur',
        field: 'donatur',
        sortable: true,
        flex: 1,
        valueFormatter: (params: any) => params.value || '-',
      },
          {
            headerName: 'Fundrising',
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
        field: 'nominal',
        sortable: true,
        flex: 1,
        valueFormatter: (params: any) => params.data?.nominal_formatted || params.value || '-',
      },
      {
        headerName: 'Tanggal Transaksi',
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
    ]
  }

  const cols: any[] = [
    {
      headerName: 'No',
      field: '__no',
      width: 60,
      sortable: false,
      valueGetter: (params: any) => {
        try {
          return (params.node && typeof params.node.rowIndex === 'number') ? params.node.rowIndex + 1 : '-'
        } catch (e) {
          return '-'
        }
      },
    },
    {
      headerName: 'Donatur',
      field: 'donatur',
      sortable: true,
      // flex: 1,
      valueFormatter: (params: any) => params.value || '-',
    },
    {
      headerName: 'Kantor Cabang',
      field: 'kantor_cabang',
      sortable: true,
      // flex: 1,
      valueFormatter: (params: any) => params.value || '-',
    },
    // 'Dibuat oleh' column: hide for Admin Cabang users
    ...(!isRoleAdminCabang
      ? [
          {
            headerName: 'Dibuat oleh',
            field: 'fundraiser',
            sortable: true,
            valueFormatter: (params: any) => params.value || '-',
          },
        ]
      : []),
    {
      headerName: 'Fundraiser',
      field: 'fundraiser_pic',
      sortable: true,
      // flex: 1,
      valueFormatter: (params: any) => (params.data?.fundraiser_pic?.nama || params.data?.fundraiser_pic?.name) || params.value || '-',
    },
    {
      headerName: 'Program',
      field: 'program',
      sortable: true,
      // flex: 1,
      valueFormatter: (params: any) => params.value || '-',
    },
    {
      headerName: 'Nominal',
      field: 'nominal',
      sortable: true,
      // flex: 1,
      valueFormatter: (params: any) => params.data?.nominal_formatted || params.value || '-',
    },
    {
      headerName: 'Tanggal Transaksi',
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
      headerName: 'Tanggal Dibuat',
      field: 'tanggal_dibuat',
      sortable: true,
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
  ]

  if (canUpdate.value || canDelete.value) {
    cols.push({
      headerName: 'Actions',
      field: 'actions',
      sortable: false,
      filter: false,
      width: 100,
      pinned: 'right',
      cellRenderer: (params: any) => {
        // If this is a pinned row (bottom), show a simple '-' instead of action icons
        try {
          if (params?.node?.rowPinned === 'bottom') {
            const span = document.createElement('span')
            span.textContent = '-'
            return span
          }
        } catch (e) {
          // ignore and render normally
        }

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

        return div
      },
    })
  }

  return cols
})

const defaultColDef = {
  resizable: true,
  sortable: true,
  filter: false,
}

// AG Grid infinite datasource (server side paging)
const buildQueryFromParams = (startRow: number, endRow: number) => {
  const perPage = endRow - startRow || pageSize
  const page = Math.floor(startRow / perPage) + 1
  const params = new URLSearchParams()
  params.append('per_page', String(perPage))
  params.append('page', String(page))

  if (filterSearch.value) params.append('search', filterSearch.value)

  if (Array.isArray(filterTanggal.value) && filterTanggal.value.length === 2) {
    const [from, to] = filterTanggal.value
    params.append('tanggal_from', formatYMDLocal(from))
    params.append('tanggal_to', formatYMDLocal(to))
  } else if (filterTanggal.value) {
    params.append('tanggal', formatYMDLocal(filterTanggal.value))
  }

  if (filterDonatur.value) params.append('donatur_id', filterDonatur.value)
  if (filterProgram.value) params.append('program_id', filterProgram.value)
  if (filterMitra.value) params.append('mitra_id', filterMitra.value)
  if (filterKantorCabang.value) params.append('kantor_cabang_id', filterKantorCabang.value)
  if (filterFundraiser.value) params.append('fundraiser_id', filterFundraiser.value)

  return params
}

// Simple in-memory page cache keyed by filter params (excluding page)
const CACHE_TTL_MS = 5 * 60 * 1000 // 5 minutes
interface CacheEntry {
  ts: number
  total: number
  pages: Map<number, any[]>
}
const transaksiCache = new Map<string, CacheEntry>()

const evictOldestCacheEntry = () => {
  if (transaksiCache.size <= 50) return
  let oldestKey: string | null = null
  let oldestTs = Infinity
  for (const [k, v] of transaksiCache.entries()) {
    if (v.ts < oldestTs) {
      oldestTs = v.ts
      oldestKey = k
    }
  }
  if (oldestKey) transaksiCache.delete(oldestKey)
}

const createDatasource = () => {
  return {
    async getRows(params: any) {
      try {
        const startRow = params.startRow || 0
        const endRow = params.endRow || (startRow + pageSize)
        const query = buildQueryFromParams(startRow, endRow)

        // Include AG Grid sort model (first sort only) into request; this affects cache key
        if (params.sortModel && params.sortModel.length > 0) {
          const s = params.sortModel[0]
          if (s.colId) {
            query.append('sort_by', s.colId)
            query.append('sort_dir', (s.sort || '').toLowerCase() === 'asc' ? 'asc' : 'desc')
          }
        }

        // Build baseKey (filter + sort params excluding page) to reuse cached pages when available
        const baseParams = new URLSearchParams(query.toString())
        baseParams.delete('page') // remove page so we can cache per-filter+sort
        const baseKey = baseParams.toString()
        const pageNum = parseInt(query.get('page') || '1', 10)

        const now = Date.now()
        const cached = transaksiCache.get(baseKey)
        if (cached && (now - cached.ts) < CACHE_TTL_MS && cached.pages.has(pageNum)) {
          const rowsThisPage = cached.pages.get(pageNum) || []
          const lastRow = cached.total >= 0 ? cached.total : undefined
          params.successCallback(rowsThisPage, lastRow)

          // restore pinned bottom row (total nominal) from cache if present
          try {
            const cachedTotalNominal = (cached as any).total_nominal ?? (cached as any).totalNominal ?? 0
            const cachedTotalNominalFormatted = (cached as any).total_nominal_formatted ?? formatCurrency(cachedTotalNominal)
            if (gridApi.value) {
              gridApi.value.setPinnedBottomRowData([
                { donatur: 'Total', kantor_cabang: '', fundraiser: '', program: '', nominal: cachedTotalNominal, nominal_formatted: cachedTotalNominalFormatted, actions: '' },
              ])
            }
          } catch (e) {
            // ignore
          }

          return
        }

        const res = await fetch(`/admin/api/transaksi?${query.toString()}`, { credentials: 'same-origin' })
        if (!res.ok) throw new Error('Failed to fetch transaksi')
        const json = await res.json()
        if (!json.success) throw new Error(json.message || 'Failed to fetch transaksi')

        const dataArray = Array.isArray(json.data) ? json.data : json.data?.data || []

        const rowsThisPage = dataArray.map((item: any) => ({
          id: item.id,
          kode: item.kode,
          donatur: item.donatur?.nama || null,
          mitra: item.mitra?.nama || null,
          kantor_cabang: item.kantor_cabang?.nama || null,
          // Prefer donatur PIC as fundraiser (donatur.pic / donatur_pic / donatur.pic_user), fallback to item.fundraiser
          fundraiser:
            item.donatur_pic?.nama ||
            item.donatur?.pic_user?.nama ||
            item.donatur?.pic_nama ||
            item.donatur?.pic_name ||
            (item.fundraiser?.nama ?? null),
          // include IDs to allow client-side filtering for leader role
          fundraiser_id: item.donatur_pic?.id || item.donatur?.pic_user?.id || item.donatur?.pic || item.fundraiser?.id || null,
          fundraiser_pic:
            item.donatur_pic?.nama ||
            item.donatur?.pic_user?.nama ||
            item.donatur?.pic_user?.name ||
            item.donatur?.pic_nama ||
            item.donatur?.pic_name ||
            null,
          fundraiser_pic_id: item.donatur_pic?.id || item.donatur?.pic_user?.id || (item.donatur?.pic ? item.donatur.pic : null) || null,
          program: item.program?.nama || null,
          nominal: item.nominal,
          nominal_formatted: item.nominal_formatted,
          tanggal_transaksi: item.tanggal_transaksi,
          tanggal_dibuat: item.created_at || null,
          keterangan: item.keterangan,
        }))

        const total = json.pagination?.total ?? (json.data?.total ?? -1)

        // Store in cache (include aggregate total nominal if available)
        let entry = transaksiCache.get(baseKey)
        if (!entry) {
          entry = { ts: now, total: total, pages: new Map<number, any[]>() }
          transaksiCache.set(baseKey, entry)
        }
        entry.ts = now
        entry.total = total
        // store server-provided aggregate if present
        try {
          ;(entry as any)["total_nominal"] = json.total_nominal ?? 0
          ;(entry as any)["total_nominal_formatted"] = json.total_nominal_formatted ?? formatCurrency((entry as any)["total_nominal"])
        } catch (e) {
          // ignore
        }
        entry.pages.set(pageNum, rowsThisPage)
        evictOldestCacheEntry()

        // If current user is a leader, filter rows client-side to only show subordinates
        try {
          const roleName = String(user?.value?.role?.name || '').toLowerCase()
          const isLeader = roleName.includes('leader') || roleName.includes('lead')
          if (isLeader) {
            const subSet = subordinateFundraiserIds.value
            const filtered = rowsThisPage.filter((r: any) => {
              const fId = r.fundraiser_id != null ? String(r.fundraiser_id) : null
              const fpId = r.fundraiser_pic_id != null ? String(r.fundraiser_pic_id) : null
              return (fId && subSet.has(fId)) || (fpId && subSet.has(fpId))
            })
            const lastRow = filtered.length >= 0 ? filtered.length : undefined
            params.successCallback(filtered, lastRow)
          } else {
            const lastRow = total >= 0 ? total : undefined
            params.successCallback(rowsThisPage, lastRow)
          }
        } catch (e) {
          const lastRow = total >= 0 ? total : undefined
          params.successCallback(rowsThisPage, lastRow)
        }

        // set pinned bottom row to show total nominal (server-provided or formatted)
        try {
          const totalNominal = json.total_nominal ?? 0
          const totalNominalFormatted = json.total_nominal_formatted ?? formatCurrency(totalNominal)
          if (gridApi.value) {
            gridApi.value.setPinnedBottomRowData([
              { donatur: 'Total', kantor_cabang: '', fundraiser: '', program: '', nominal: totalNominal, nominal_formatted: totalNominalFormatted, actions: '' },
            ])
          }
        } catch (e) {
          // ignore
        }
      } catch (err) {
        console.error('AG Grid datasource error', err)
        params.failCallback()
      }
    },
  }
}

const onGridReady = (event: any) => {
  gridApi.value = event.api
  gridColumnApi.value = event.columnApi
  const ds = createDatasource()
  gridApi.value.setDatasource(ds)
}

const refreshGrid = () => {
  if (gridApi.value) {
    const ds = createDatasource()
    gridApi.value.setDatasource(ds)
  }
}

  const fetchFilterOptions = async () => {
    try {
      const kantorUrl = isAdmin() ? '/admin/api/kantor-cabang?per_page=1000' : '/admin/api/kantor-cabang?per_page=1000&only_assigned=1'
      const karyawanUrl = isAdmin() ? '/admin/api/karyawan?per_page=1000' : '/admin/api/karyawan?per_page=1000&only_assigned=1'
      const [kantorRes, programRes, fundraiserRes] = await Promise.all([
        fetch(kantorUrl, { credentials: 'same-origin' }),
        fetch('/admin/api/program?per_page=1000', { credentials: 'same-origin' }),
        fetch(karyawanUrl, { credentials: 'same-origin' }),
      ])

    if (kantorRes.ok) {
      const json = await kantorRes.json()
      if (json.success) {
        kantorCabangList.value = Array.isArray(json.data) ? json.data : json.data?.data || []
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
    // fetch mitra list
    try {
      const mitraRes = await fetch('/admin/api/mitra?per_page=1000', { credentials: 'same-origin' })
      if (mitraRes.ok) {
        const json = await mitraRes.json()
        if (json.success) mitraList.value = Array.isArray(json.data) ? json.data : json.data?.data || []
      }
    } catch (e) {
      console.error('Error fetching mitra options:', e)
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

    const formatYMDLocal = (d: any) => {
      if (!d) return ''
      if (typeof d === 'string') return d
      if (d instanceof Date && !isNaN(d.getTime())) {
        const yyyy = d.getFullYear()
        const mm = String(d.getMonth() + 1).padStart(2, '0')
        const dd = String(d.getDate()).padStart(2, '0')
        return `${yyyy}-${mm}-${dd}`
      }
      return String(d)
    }

    if (Array.isArray(filterTanggal.value) && filterTanggal.value.length === 2) {
      const [from, to] = filterTanggal.value
      params.append('tanggal_from', formatYMDLocal(from))
      params.append('tanggal_to', formatYMDLocal(to))
    } else if (filterTanggal.value) {
      params.append('tanggal', formatYMDLocal(filterTanggal.value))
    }

    if (filterDonatur.value) {
      params.append('donatur_id', filterDonatur.value)
    }

    if (filterProgram.value) {
      params.append('program_id', filterProgram.value)
    }

    if (filterMitra.value) {
      params.append('mitra_id', filterMitra.value)
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
        mitra: item.mitra?.nama || null,
        kantor_cabang: item.kantor_cabang?.nama || null,
        // 'Dibuat oleh' (creator)
        // Use donatur PIC for fundraiser display when available
        fundraiser:
          item.donatur_pic?.nama ||
          item.donatur?.pic_user?.nama ||
          item.donatur?.pic_nama ||
          item.donatur?.pic_name ||
          (item.fundraiser?.nama ?? null),
        // donatur PIC detailed field
        fundraiser_pic:
          item.donatur_pic?.nama ||
          item.donatur?.pic_user?.nama ||
          item.donatur?.pic_user?.name ||
          item.donatur?.pic_nama ||
          item.donatur?.pic_name ||
          null,
        program: item.program?.nama || null,
        nominal: item.nominal,
        nominal_formatted: item.nominal_formatted,
        tanggal_transaksi: item.tanggal_transaksi,
        tanggal_dibuat: item.created_at || null,
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
    // Refresh the AG Grid datasource when filters change
    refreshGrid()
  }, 300)
}

const resetFilter = () => {
  filterSearch.value = ''
  filterTanggal.value = []
  filterDonatur.value = ''
  filterProgram.value = ''
  filterKantorCabang.value = ''
  filterMitra.value = ''
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
      // Clear cache to avoid stale data then refresh grid
      transaksiCache.clear()
      refreshGrid()
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

const handleExportExcel = async () => {
  try {
    // Build same params as fetchData but request many items
    const params = new URLSearchParams()
    params.append('per_page', '1000')

    if (filterSearch.value) params.append('search', filterSearch.value)

    if (Array.isArray(filterTanggal.value) && filterTanggal.value.length === 2) {
      const [from, to] = filterTanggal.value
      params.append('tanggal_from', formatYMDLocal(from))
      params.append('tanggal_to', formatYMDLocal(to))
    } else if (filterTanggal.value) {
      params.append('tanggal', formatYMDLocal(filterTanggal.value))
    }

    if (filterDonatur.value) params.append('donatur_id', filterDonatur.value)
    if (filterProgram.value) params.append('program_id', filterProgram.value)
    if (filterMitra.value) params.append('mitra_id', filterMitra.value)
    if (filterKantorCabang.value) params.append('kantor_cabang_id', filterKantorCabang.value)
    if (filterFundraiser.value) params.append('fundraiser_id', filterFundraiser.value)

    const res = await fetch(`/admin/api/transaksi?${params.toString()}`, {
      credentials: 'same-origin',
    })

    if (!res.ok) throw new Error('Failed to fetch transaksi for export')

    const json = await res.json()
    if (!json.success) throw new Error(json.message || 'Failed to fetch transaksi for export')

    const transaksiList = (json.data || [])

    // using shared sanitizeString helper

    // Try to load the Excel template and fill it; fallback to simple export if template missing
    const tplRes = await fetch('/template/template-transaksi.xlsx')
    if (!tplRes || !tplRes.ok) {
      // Fallback to previous simple export
      const data = transaksiList.map((item: any) => ({
        'Donatur': item.donatur?.nama || '-',
        'Kantor Cabang': item.kantor_cabang?.nama || '-',
        'Dibuat oleh': (item.fundraiser?.name || item.fundraiser?.nama || item.fundraiser?.label) || '-',
        'Fundraiser': item.donatur_pic?.nama || item.donatur?.pic_user?.nama || item.donatur?.pic_user?.name || '-',
        'Program': item.program?.nama_program || '-',
        'Nominal': item.nominal_formatted,
        'Tanggal': item.tanggal_transaksi
          ? new Date(item.tanggal_transaksi).toLocaleDateString('id-ID', {
              year: 'numeric',
              month: 'long',
              day: 'numeric',
            })
          : '-',
        'Keterangan': item.keterangan || '-',
      }))

      const worksheet = XLSX.utils.json_to_sheet(data)
      const workbook = XLSX.utils.book_new()
      XLSX.utils.book_append_sheet(workbook, worksheet, 'Transaksi')

      const now = new Date()
      const filename = `Transaksi_${now.toISOString().split('T')[0]}.xlsx`
      XLSX.writeFile(workbook, filename)
      toast.success('Export Excel berhasil (fallback)')
      return
    }

    const arrayBuffer = await tplRes.arrayBuffer()
    const templateWb = XLSX.read(arrayBuffer, { type: 'array' })
    const sheetName = templateWb.SheetNames[0]
    const ws = templateWb.Sheets[sheetName]
    // Remove merges and autofilter from template sheet to prevent inconsistent references
    if (ws['!merges']) delete ws['!merges']
    if (ws['!autofilter']) delete ws['!autofilter']

    // Read header rows (1..3) and build flattened column names so we can map fields
    const range = XLSX.utils.decode_range(ws['!ref'] || 'A1:A1')
    let cols = range.e.c + 1

    const rowVal = (r: number, c: number) => {
      const cell = ws[XLSX.utils.encode_cell({ r, c })]
      return cell && cell.v !== undefined && cell.v !== null ? String(cell.v).trim() : null
    }

    const colNames: (string | null)[] = []
    for (let c = 0; c < cols; c++) {
      const top = rowVal(0, c)
      const mid = rowVal(1, c)
      const low = rowVal(2, c)
      let name: string | null = null
      if (mid && low) name = `${mid} ${low}`
      else if (mid) name = mid
      else if (top) name = top
      else if (low) name = low
      else name = null
      colNames.push(name)
    }

    // Helper to find a column by candidates
    const findCol = (candidates: string[]) => {
      const lc = candidates.map((s) => s.toLowerCase())
      for (let i = 0; i < colNames.length; i++) {
        const v = (colNames[i] || '').toLowerCase()
        if (lc.some((c) => c && v.includes(c))) return i
      }
      return -1
    }

    const colIdx = {
      nama: findCol(['nama']),
      alamat: findCol(['alamat']),
      kelurahan: findCol(['kelurahan']),
      kecamatan: findCol(['kecamatan']),
      kota: findCol(['kota', 'kota/kab', 'kota/kab']),
      provinsi: findCol(['provinsi']),
      no_hp: findCol(['no hp', 'no hp', 'no.', 'hp']),
      penempatan: findCol(['penempatan']),
      pj: findCol(['pj']),
      status: findCol(['status']),
      total: findCol(['total']),
    }

    // Find repeating tgl/dana/keterangan groups left-to-right
    const groups: { tgl: number; dana: number; ket: number }[] = []
    for (let i = 0; i < colNames.length; i++) {
      const n = (colNames[i] || '').toLowerCase()
      if (n.includes('tgl') || n === 'tgl') {
        // find next columns for dana and keterangan
        const danaIdx = (() => {
          for (let j = i + 1; j < Math.min(i + 6, colNames.length); j++) {
            const nn = (colNames[j] || '').toLowerCase()
            if (nn.includes('dana') || nn.includes('amt') || nn.includes('dana ')) return j
          }
          return -1
        })()
        const ketIdx = (() => {
          for (let j = (danaIdx !== -1 ? danaIdx + 1 : i + 1); j < Math.min(i + 8, colNames.length); j++) {
            const nn = (colNames[j] || '').toLowerCase()
            if (nn.includes('keterangan') || nn.includes('ket') || nn.includes('keter')) return j
          }
          return -1
        })()
        if (danaIdx !== -1 && ketIdx !== -1) {
          groups.push({ tgl: i, dana: danaIdx, ket: ketIdx })
        }
      }
    }

    // Helper to get fundraiser display name (handle API keys 'name' or 'nama' or label)
    const getFundraiserName = (t: any) => {
      if (!t) return 'Unassigned'
      const f = t.fundraiser || (t.fundraiser_id ? { id: t.fundraiser_id } : null)
      return (f && (f.name || f.nama || f.label)) ? (f.name || f.nama || f.label) : 'Unassigned'
    }

    // Build fundraiser groups: if a fundraiser filter is selected, use single group; otherwise group by fundraiser name
    const fundMap = new Map<string, any[]>()
    if (filterFundraiser.value) {
      const fName = (filterFundraiser.value && typeof filterFundraiser.value === 'string') ? String(filterFundraiser.value) : ((filterFundraiser.value as any)?.label || 'Filtered')
      fundMap.set(String(fName || 'Filtered'), transaksiList)
    } else {
      for (const t of transaksiList) {
        const name = getFundraiserName(t)
        let arr = fundMap.get(name) as any[] | undefined
        if (!arr) { arr = []; fundMap.set(name, arr) }
        arr.push(t)
      }
    }

    // Prepare output workbook
    const outWb = XLSX.utils.book_new()

    // Helper to sanitize sheet name
    const sanitizeSheetName = (s: string) => {
      let name = String(s || '')
        .replace(/[\\/*?:\[\]]+/g, '-')
        .slice(0, 31)
        .trim()
      if (!name) name = 'Sheet'
      // Ensure uniqueness
      let base = name
      let idx = 1
      while (outWb.SheetNames.includes(name)) {
        name = base.slice(0, Math.max(1, 31 - (` ${idx}`).length)) + ` ${idx}`
        idx++
      }
      return name
    }

    // For each fundraiser group, clone the template sheet and write data for that fundraiser only
    for (const [fundName, fundTransaksi] of fundMap.entries()) {
      const wsCopy = JSON.parse(JSON.stringify(ws))
      // Remove merges/autofilter on clone
      if (wsCopy['!merges']) delete wsCopy['!merges']
      if (wsCopy['!autofilter']) delete wsCopy['!autofilter']

      // Read header rows (1..3) and build flattened column names so we can map fields
      const range2 = XLSX.utils.decode_range(wsCopy['!ref'] || 'A1:A1')
      let cols2 = range2.e.c + 1

      const rowVal2 = (r: number, c: number) => {
        const cell = wsCopy[XLSX.utils.encode_cell({ r, c })]
        return cell && cell.v !== undefined && cell.v !== null ? String(cell.v).trim() : null
      }

      const colNames2: (string | null)[] = []
      for (let c = 0; c < cols2; c++) {
        const top = rowVal2(0, c)
        const mid = rowVal2(1, c)
        const low = rowVal2(2, c)
        let name: string | null = null
        if (mid && low) name = `${mid} ${low}`
        else if (mid) name = mid
        else if (top) name = top
        else if (low) name = low
        else name = null
        colNames2.push(name)
      }

      // Helper findCol local
      const findCol2 = (candidates: string[]) => {
        const lc = candidates.map((s) => s.toLowerCase())
        for (let i = 0; i < colNames2.length; i++) {
          const v = (colNames2[i] || '').toLowerCase()
          if (lc.some((c) => c && v.includes(c))) return i
        }
        return -1
      }

      const colIdx2 = {
        nama: findCol2(['nama']),
        alamat: findCol2(['alamat']),
        kelurahan: findCol2(['kelurahan']),
        kecamatan: findCol2(['kecamatan']),
        kota: findCol2(['kota', 'kota/kab', 'kota/kab']),
        provinsi: findCol2(['provinsi']),
        no_hp: findCol2(['no hp', 'no hp', 'no.', 'hp']),
        penempatan: findCol2(['penempatan']),
        pj: findCol2(['pj']),
        status: findCol2(['status']),
        total: findCol2(['total']),
      }

      // Find repeating tgl/dana/keterangan groups left-to-right
      const groups2: { tgl: number; dana: number; ket: number }[] = []
      for (let i = 0; i < colNames2.length; i++) {
        const n = (colNames2[i] || '').toLowerCase()
        if (n.includes('tgl') || n === 'tgl') {
          const danaIdx = (() => {
            for (let j = i + 1; j < Math.min(i + 6, colNames2.length); j++) {
              const nn = (colNames2[j] || '').toLowerCase()
              if (nn.includes('dana') || nn.includes('amt') || nn.includes('dana ')) return j
            }
            return -1
          })()
          const ketIdx = (() => {
            for (let j = (danaIdx !== -1 ? danaIdx + 1 : i + 1); j < Math.min(i + 8, colNames2.length); j++) {
              const nn = (colNames2[j] || '').toLowerCase()
              if (nn.includes('keterangan') || nn.includes('ket') || nn.includes('keter')) return j
            }
            return -1
          })()
          if (danaIdx !== -1 && ketIdx !== -1) groups2.push({ tgl: i, dana: danaIdx, ket: ketIdx })
        }
      }

      // Determine months present in this funder's data
      const monthMap2 = new Map<string, { label: string; key: string }>()
      for (const t of fundTransaksi) {
        if (!t.tanggal_transaksi) continue
        const d = new Date(t.tanggal_transaksi)
        if (isNaN(d.getTime())) continue
        const key = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}`
        if (!monthMap2.has(key)) {
          const label = d.toLocaleString('en-GB', { month: 'long', year: 'numeric' })
          monthMap2.set(key, { label, key })
        }
      }
      const months2 = Array.from(monthMap2.values())

      // If no groups found for this sheet, fallback to simple listing sheet
      if (groups2.length === 0) {
        const data = fundTransaksi.map((item: any) => ({
          'Kode': item.kode || '-',
          'Donatur': item.donatur?.nama || '-',
          'Kantor Cabang': item.kantor_cabang?.nama || '-',
          'Program': item.program?.nama_program || '-',
          'Nominal': item.nominal_formatted,
          'Tanggal': item.tanggal_transaksi
            ? new Date(item.tanggal_transaksi).toLocaleDateString('en-GB', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
              })
            : '-',
          'Keterangan': item.keterangan || '-',
        }))

        const worksheet = XLSX.utils.json_to_sheet(data)
        XLSX.utils.book_append_sheet(outWb, worksheet, sanitizeSheetName(fundName))
        continue
      }

      // Write headers on this clone
      const headerTitleRow = 0
      const headerMainRow = 1
      const headerSubRow = 2

      const setCell2 = (r: number, c: number, value: any, type: 's' | 'n' = 's') => {
        if (c < 0) return
        const cellRef = XLSX.utils.encode_cell({ r, c })
        if (value === null || value === undefined || value === '') { delete wsCopy[cellRef]; return }
        if (type === 'n') {
          const num = Number(value)
          if (Number.isFinite(num)) wsCopy[cellRef] = { v: num, t: 'n' }
          else delete wsCopy[cellRef]
        } else {
          wsCopy[cellRef] = { v: sanitizeString(String(value)), t: 's' }
        }
      }

      setCell2(headerTitleRow, 0, 'Transaksi')

      const mainHeaders = ['No', 'Tgl', 'Nama', 'Alamat', 'Kelurahan', 'Kecamatan', 'Kota/Kab', 'Provinsi', 'No HP', 'Penempatan', 'PJ', 'Status']
      const mainIdx2: Record<string, number> = {}
      for (let i = 0; i < mainHeaders.length; i++) { setCell2(headerMainRow, i, mainHeaders[i]); setCell2(headerSubRow, i, ''); mainIdx2[mainHeaders[i]] = i }

      if (months2.length > groups2.length) toast.info(`Template hanya memiliki ${groups2.length} kelompok bulan; ${months2.length - groups2.length} bulan tambahan dipotong.`)
      for (let i = 0; i < groups2.length; i++) {
        const g = groups2[i]
        if (i < months2.length) {
          const m = months2[i]
          setCell2(headerMainRow, g.tgl, m.label)
          setCell2(headerSubRow, g.tgl, 'tgl')
          setCell2(headerSubRow, g.dana, 'dana')
          setCell2(headerSubRow, g.ket, 'keterangan')
        } else { setCell2(headerMainRow, g.tgl, ''); setCell2(headerSubRow, g.tgl, ''); setCell2(headerSubRow, g.dana, ''); setCell2(headerSubRow, g.ket, '') }
      }

      let totalCol2 = colIdx2.total
      if (totalCol2 < 0) { totalCol2 = cols2; setCell2(headerMainRow, totalCol2, 'Total'); setCell2(headerSubRow, totalCol2, ''); cols2 = totalCol2 + 1 } else { setCell2(headerMainRow, totalCol2, 'Total'); setCell2(headerSubRow, totalCol2, '') }

      // Clear data rows
      const startRow2 = 3
      const safeClearTo2 = Math.max(range2.e.r, startRow2 + fundTransaksi.length + 50)
      for (let r = startRow2; r <= safeClearTo2; r++) {
        for (const h of Object.values(mainIdx2)) delete wsCopy[XLSX.utils.encode_cell({ r, c: h })]
        for (const g of groups2) { delete wsCopy[XLSX.utils.encode_cell({ r, c: g.tgl })]; delete wsCopy[XLSX.utils.encode_cell({ r, c: g.dana })]; delete wsCopy[XLSX.utils.encode_cell({ r, c: g.ket })] }
        delete wsCopy[XLSX.utils.encode_cell({ r, c: totalCol2 })]
      }

      // Build per-donor aggregates for this fund
      const donorMap = new Map()
      for (const t of fundTransaksi) {
        const donorKey = t.donatur?.id || t.donatur_id || t.donatur?.nama || `unknown_${t.id}`
        if (!donorMap.has(donorKey)) donorMap.set(donorKey, { info: resolveDonaturInfo(t), months: new Map(), total: 0 })
        const rec = donorMap.get(donorKey)
        rec.info = resolveDonaturInfo(t)
        const d = t.tanggal_transaksi ? new Date(t.tanggal_transaksi) : null
        const tglISO = d && !isNaN(d.getTime()) ? `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}` : ''
        const tglDisplay = d && !isNaN(d.getTime()) ? d.toLocaleDateString('en-GB', { day: 'numeric', month: 'long', year: 'numeric' }) : ''
        const monthKey = d ? `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}` : 'unknown'
        const nominal = Number(t.nominal || 0)
        if (!rec.months.has(monthKey)) rec.months.set(monthKey, { tglISO, tglDisplay, dana: nominal, ket: t.keterangan || '' })
        else { const ex = rec.months.get(monthKey); if (ex.tglISO === '' || (tglISO !== '' && tglISO < ex.tglISO)) { ex.tglISO = tglISO; ex.tglDisplay = tglDisplay } ex.dana = (ex.dana || 0) + nominal; ex.ket = ex.ket ? `${ex.ket}; ${t.keterangan || ''}` : (t.keterangan || '') }
        rec.total += nominal
      }

      const donors = Array.from(donorMap.entries()).map(([k, v]) => ({ key: k, info: v.info, months: v.months, total: v.total }))
      const monthTotals = months2.map(() => 0)

      // Write donors into wsCopy
      let writeRow2 = startRow2
      let counter2 = 1
      for (const donor of donors) {
        setCell2(writeRow2, mainIdx2['No'], counter2, 'n')
        let earliestISO = ''
        let earliestDisplay = ''
        for (const [mk, mv] of donor.months.entries()) { if (mv.tglISO && (earliestISO === '' || mv.tglISO < earliestISO)) { earliestISO = mv.tglISO; earliestDisplay = mv.tglDisplay } }
        setCell2(writeRow2, mainIdx2['Tgl'], earliestDisplay, 's')
        setCell2(writeRow2, mainIdx2['Nama'], donor.info?.nama || '', 's')
        setCell2(writeRow2, mainIdx2['Alamat'], donor.info?.alamat || '', 's')
        setCell2(writeRow2, mainIdx2['Kelurahan'], donor.info?.kelurahan || '', 's')
        setCell2(writeRow2, mainIdx2['Kecamatan'], donor.info?.kecamatan || '', 's')
        setCell2(writeRow2, mainIdx2['Kota/Kab'], donor.info?.kota || donor.info?.kota_kab || '', 's')
        setCell2(writeRow2, mainIdx2['Provinsi'], donor.info?.provinsi || '', 's')
        const resolvedPhone = donor.info?.no_handphone || donor.info?.no_hp || ''
        const resolvedPic = donor.info?.pic_user?.nama || donor.info?.pic_user?.name || donor.info?.pic || donor.info?.pj || ''
        setCell2(writeRow2, mainIdx2['No HP'], resolvedPhone, 's')
        setCell2(writeRow2, mainIdx2['Penempatan'], donor.info?.penempatan || '', 's')
        setCell2(writeRow2, mainIdx2['PJ'], resolvedPic, 's')
        setCell2(writeRow2, mainIdx2['Status'], donor.info?.status || '', 's')
        for (let i = 0; i < groups2.length; i++) {
          const g = groups2[i]
          if (i < months2.length) {
            const monthKey = months2[i].key
            const agg = donor.months.get(monthKey)
            if (agg) {
              setCell2(writeRow2, g.tgl, agg.tglDisplay, 's')
              setCell2(writeRow2, g.dana, agg.dana, 'n')
              setCell2(writeRow2, g.ket, agg.ket, 's')
              monthTotals[i] += Number(agg.dana || 0)
            } else {
              setCell2(writeRow2, g.tgl, '', 's')
              setCell2(writeRow2, g.dana, '', 'n')
              setCell2(writeRow2, g.ket, '', 's')
            }
          } else {
            setCell2(writeRow2, g.tgl, '', 's')
            setCell2(writeRow2, g.dana, '', 'n')
            setCell2(writeRow2, g.ket, '', 's')
          }
        }
        setCell2(writeRow2, totalCol2, donor.total, 'n')
        writeRow2++
        counter2++
      }

      const totalAllMonths = monthTotals.reduce((sum, val) => sum + val, 0)

      // Spacer row before totals to visually separate donor data
      writeRow2++

      const monthTotalRow = writeRow2++
      setCell2(monthTotalRow, mainIdx2['Nama'], 'Total per Tanggal', 's')
      setCell2(monthTotalRow, totalCol2, totalAllMonths, 'n')
      for (let i = 0; i < groups2.length; i++) {
        const g = groups2[i]
        if (i < months2.length) {
          setCell2(monthTotalRow, g.tgl, 'Total', 's')
          setCell2(monthTotalRow, g.dana, monthTotals[i], 'n')
          setCell2(monthTotalRow, g.ket, '', 's')
        } else {
          setCell2(monthTotalRow, g.tgl, '', 's')
          setCell2(monthTotalRow, g.dana, '', 'n')
          setCell2(monthTotalRow, g.ket, '', 's')
        }
      }

      const grandTotalRow = writeRow2++
      setCell2(grandTotalRow, mainIdx2['Nama'], 'Grand Total', 's')
      setCell2(grandTotalRow, totalCol2, totalAllMonths, 'n')
      for (let i = 0; i < groups2.length; i++) {
        const g = groups2[i]
        setCell2(grandTotalRow, g.tgl, '', 's')
        setCell2(grandTotalRow, g.dana, '', 'n')
        setCell2(grandTotalRow, g.ket, '', 's')
      }

      // Update wsCopy range and append to outWb
      const newRef2 = `A1:${XLSX.utils.encode_col(cols2 - 1)}${Math.max(writeRow2 - 1, headerSubRow + 1)}`
      wsCopy['!ref'] = newRef2
      XLSX.utils.book_append_sheet(outWb, wsCopy, sanitizeSheetName(fundName))
    }

    // Build filename including date range if present
    let rangePart = ''
    if (Array.isArray(filterTanggal.value) && filterTanggal.value.length === 2) {
      const [from, to] = filterTanggal.value
      rangePart = `_${formatYMDLocal(from)}_to_${formatYMDLocal(to)}`
    } else if (filterTanggal.value) {
      const d = formatYMDLocal(filterTanggal.value)
      rangePart = `_${d}`
    }

    const now = new Date()
    const filename = `Transaksi${rangePart}_${now.toISOString().split('T')[0]}.xlsx`

    // Ensure we have sheets before writing
    if (!outWb || !Array.isArray(outWb.SheetNames) || outWb.SheetNames.length === 0) {
      throw new Error('Tidak ada sheet yang dibuat untuk diekspor')
    }

    // Validation: detect control chars or invalid numbers in cells
    const detectInvalidCells = (wb: any) => {
      const invalid: Array<any> = []
      const ctrlRe = /[\x00-\x08\x0B\x0C\x0E-\x1F]/
      for (const sheetName of wb.SheetNames) {
        const sh = wb.Sheets[sheetName]
        for (const ref of Object.keys(sh)) {
          if (ref.startsWith('!')) continue
          const cell = sh[ref]
          if (!cell) continue
          if (typeof cell.v === 'string' && ctrlRe.test(cell.v)) {
            invalid.push({ sheet: sheetName, ref, value: cell.v })
          }
          if (cell.t === 'n') {
            const num = Number(cell.v)
            if (!Number.isFinite(num)) invalid.push({ sheet: sheetName, ref, value: cell.v })
          }
        }
      }
      return invalid
    }

    const invalidCells = detectInvalidCells(outWb)
    if (invalidCells.length) {
      console.error('Export aborted: found invalid cells', invalidCells.slice(0, 50))
      toast.error(`Export gagal: ditemukan ${invalidCells.length} sel berisi karakter tidak valid. Lihat console untuk detail.`)
      return
    }

    try {
      // write to an in-memory array and read back to validate
      const buffer = XLSX.write(outWb, { bookType: 'xlsx', type: 'array' })
      try {
        XLSX.read(buffer, { type: 'array' })
      } catch (readErr) {
        console.error('Validation read failed', readErr)
        toast.error('Validasi file gagal: ' + ((readErr as any)?.message || 'lihat console'))
        return
      }

      // trigger download
      const blob = new Blob([buffer], { type: 'application/octet-stream' })
      const url = URL.createObjectURL(blob)
      const a = document.createElement('a')
      a.href = url
      a.download = filename
      document.body.appendChild(a)
      a.click()
      a.remove()
      URL.revokeObjectURL(url)

      toast.success('Export Excel berhasil (menggunakan template)')
    } catch (writeErr) {
      console.error('Write error', writeErr)
      toast.error('Gagal menulis file: ' + ((writeErr as any)?.message || 'lihat console'))
      throw writeErr
    }
  } catch (err) {
    console.error('Export failed', err)
    toast.error('Gagal mengekspor data: ' + ((err as any)?.message || 'lihat console'))
  }
}

const handleExportCsv = async () => {
  try {
    const params = new URLSearchParams()
    params.append('per_page', '1000')

    if (filterSearch.value) params.append('search', filterSearch.value)

    if (Array.isArray(filterTanggal.value) && filterTanggal.value.length === 2) {
      const [from, to] = filterTanggal.value
      params.append('tanggal_from', formatYMDLocal(from))
      params.append('tanggal_to', formatYMDLocal(to))
    } else if (filterTanggal.value) {
      params.append('tanggal', formatYMDLocal(filterTanggal.value))
    }

    if (filterDonatur.value) params.append('donatur_id', filterDonatur.value)
    if (filterProgram.value) params.append('program_id', filterProgram.value)
    if (filterMitra.value) params.append('mitra_id', filterMitra.value)
    if (filterKantorCabang.value) params.append('kantor_cabang_id', filterKantorCabang.value)
    if (filterFundraiser.value) params.append('fundraiser_id', filterFundraiser.value)

    const res = await fetch(`/admin/api/transaksi?${params.toString()}`, {
      credentials: 'same-origin',
    })

    if (!res.ok) throw new Error('Failed to fetch transaksi for CSV export')

    const json = await res.json()
    if (!json.success) throw new Error(json.message || 'Failed to fetch transaksi for CSV export')

    const transaksiList = (json.data || [])

    // Group by donatur PIC (PIC name resolved from donatur_pic or donatur.pic_user)
    const getPicName = (t: any) => {
      if (!t) return 'Unassigned'
      const pic = t.donatur_pic || (t.donatur?.pic_user ? t.donatur.pic_user : (t.donatur && t.donatur.pic ? { id: t.donatur.pic, nama: t.donatur.pic_nama || t.donatur.pic_name || '' } : null))
      return (pic && (pic.nama || pic.name)) ? (pic.nama || pic.name) : 'Unassigned'
    }

    const fundMap = new Map<string, any[]>()
    if (filterFundraiser.value) {
      // When a fundraiser filter is applied, try to resolve it to a PIC name (could be a karyawan id or a PIC id)
      let fName = String(filterFundraiser.value)
      const foundKaryawan = fundraiserList.value.find((u: any) => String(u.id) === String(filterFundraiser.value))
      if (foundKaryawan) {
        fName = foundKaryawan.name || foundKaryawan.nama || String(filterFundraiser.value)
      } else {
        // search donatur list for matching pic
        const foundPicDonor = donaturList.value.find((d: any) => (d.pic && String(d.pic) === String(filterFundraiser.value)) || (d.pic_user && String(d.pic_user.id) === String(filterFundraiser.value)))
        if (foundPicDonor) {
          const pic = foundPicDonor.pic_user || (foundPicDonor.pic ? { id: foundPicDonor.pic, nama: foundPicDonor.pic_nama || foundPicDonor.pic_name || '' } : null)
          if (pic) fName = pic.nama || pic.name || fName
        }
      }
      fundMap.set(String(fName || 'Filtered'), transaksiList)
    } else {
      for (const t of transaksiList) {
        const name = getPicName(t)
        let arr = fundMap.get(name) as any[] | undefined
        if (!arr) { arr = []; fundMap.set(name, arr) }
        arr.push(t)
      }
    }

    const zip = new JSZip()

    for (const [fundName, fundTransaksi] of fundMap.entries()) {
      // Determine month columns present
      const monthSet = new Set<string>()
      for (const t of fundTransaksi) {
        if (!t.tanggal_transaksi) continue
        const d = new Date(t.tanggal_transaksi)
        if (isNaN(d.getTime())) continue
        const key = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}`
        monthSet.add(key)
      }
      const months = Array.from(monthSet).sort()
      const monthMeta = months.map((m) => {
        const [y, mm] = m.split('-')
        const date = new Date(Number(y), Number(mm) - 1, 1)
        const label = date.toLocaleString('en-GB', { month: 'long', year: 'numeric' })
        return { key: m, label }
      })

      // Build donor aggregates (earliest date, total nominal, concatenated notes per month)
      const donorMap = new Map()
      for (const t of fundTransaksi) {
        const donorKey = t.donatur?.id || t.donatur_id || t.donatur?.nama || `unknown_${t.kode}`
        if (!donorMap.has(donorKey)) donorMap.set(donorKey, { info: resolveDonaturInfo(t), months: new Map() })
        const rec = donorMap.get(donorKey)
        rec.info = resolveDonaturInfo(t)
        const d = t.tanggal_transaksi ? new Date(t.tanggal_transaksi) : null
        if (!d || isNaN(d.getTime())) continue
        const monthKey = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}`
        const tglISO = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`
        const tglDisplay = d.toLocaleDateString('en-GB', { day: 'numeric', month: 'long', year: 'numeric' })
        const nominal = Number(t.nominal || 0)
        const note = t.keterangan || ''
        if (!rec.months.has(monthKey)) {
          rec.months.set(monthKey, { tglISO, tglDisplay, nominal, ket: note })
        } else {
          const agg = rec.months.get(monthKey)
          if (!agg.tglISO || (tglISO && tglISO < agg.tglISO)) {
            agg.tglISO = tglISO
            agg.tglDisplay = tglDisplay
          }
          agg.nominal = (agg.nominal || 0) + nominal
          agg.ket = agg.ket ? `${agg.ket}; ${note}` : note
        }
      }

      // Headers: layered row (month label spanning 3 columns) + sub headers
      const mainHeaders = ['No', 'Nama', 'Alamat', 'Kelurahan', 'Kecamatan', 'Kota/Kab', 'Provinsi', 'No HP', 'Penempatan', 'PJ', 'Status']
      const headerRow1 = [...mainHeaders]
      const headerRow2 = Array(mainHeaders.length).fill('')
      for (const meta of monthMeta) {
        headerRow1.push(meta.label, '', '')
        headerRow2.push('Tanggal', 'Nominal', 'Keterangan')
      }
      const rows: string[] = []
      rows.push(headerRow1.map(csvEscape).join(';'))
      rows.push(headerRow2.map(csvEscape).join(';'))

      const donors = Array.from(donorMap.entries()).map(([k, v]) => ({ key: k, info: v.info, months: v.months }))
      const monthTotals = monthMeta.map(() => 0)

      let idx = 1
      for (const donor of donors) {
        const rowArr: string[] = []
        rowArr.push(csvEscape(String(idx)))
        rowArr.push(csvEscape(donor.info?.nama || ''))
        rowArr.push(csvEscape(donor.info?.alamat || ''))
        rowArr.push(csvEscape(donor.info?.kelurahan || ''))
        rowArr.push(csvEscape(donor.info?.kecamatan || ''))
        rowArr.push(csvEscape(donor.info?.kota || donor.info?.kota_kab || ''))
        rowArr.push(csvEscape(donor.info?.provinsi || ''))
        const resolvedPhone = donor.info?.no_handphone || donor.info?.no_hp || ''
        const resolvedPic = donor.info?.pic_user?.nama || donor.info?.pic_user?.name || donor.info?.pic || donor.info?.pj || ''
        rowArr.push(csvEscape(resolvedPhone))
        rowArr.push(csvEscape(donor.info?.penempatan || ''))
        rowArr.push(csvEscape(resolvedPic))
        rowArr.push(csvEscape(donor.info?.status || ''))

        monthMeta.forEach((meta, monthIndex) => {
          const agg = donor.months.get(meta.key)
          if (!agg) {
            rowArr.push(csvEscape(''))
            rowArr.push(csvEscape(''))
            rowArr.push(csvEscape(''))
          } else {
            rowArr.push(csvEscape(agg.tglDisplay || ''))
            rowArr.push(csvEscape(String(agg.nominal || 0)))
            rowArr.push(csvEscape(agg.ket || ''))
            monthTotals[monthIndex] += Number(agg.nominal || 0)
          }
        })

        rows.push(rowArr.join(';'))
        idx++
      }

      const totalAllMonths = monthTotals.reduce((sum, val) => sum + val, 0)

      // Spacer row before summary rows for readability
      rows.push('')

      const monthTotalRow: string[] = []
      for (let i = 0; i < mainHeaders.length; i++) {
        if (i === 1) monthTotalRow.push(csvEscape('Total per Tanggal'))
        else monthTotalRow.push(csvEscape(''))
      }
      monthMeta.forEach((_, monthIndex) => {
        monthTotalRow.push(csvEscape('Total'))
        monthTotalRow.push(csvEscape(String(monthTotals[monthIndex] || 0)))
        monthTotalRow.push(csvEscape(''))
      })
      rows.push(monthTotalRow.join(';'))

      const grandTotalRow: string[] = []
      for (let i = 0; i < mainHeaders.length; i++) {
        if (i === 1) grandTotalRow.push(csvEscape('Grand Total'))
        else grandTotalRow.push(csvEscape(''))
      }
      monthMeta.forEach((_, monthIndex) => {
        if (monthIndex === 0) {
          grandTotalRow.push(csvEscape(''))
          grandTotalRow.push(csvEscape(String(totalAllMonths)))
          grandTotalRow.push(csvEscape(''))
        } else {
          grandTotalRow.push(csvEscape(''))
          grandTotalRow.push(csvEscape(''))
          grandTotalRow.push(csvEscape(''))
        }
      })
      rows.push(grandTotalRow.join(';'))

      const csvContent = '\uFEFF' + rows.join('\r\n')
      zip.file(`${sanitizeSheetName(fundName)}.csv`, csvContent)
    }

    if (zip.length === 0) throw new Error('Tidak ada data untuk diekspor')

    const zipBlob = await zip.generateAsync({ type: 'blob' })
    const now = new Date()
    const filename = `Transaksi_${now.toISOString().split('T')[0]}.zip`
    const url = URL.createObjectURL(zipBlob)
    const a = document.createElement('a')
    a.href = url
    a.download = filename
    document.body.appendChild(a)
    a.click()
    a.remove()
    URL.revokeObjectURL(url)

    toast.success('Export CSV per-PIC berhasil (ZIP)')
  } catch (err) {
    console.error('CSV export failed', err)
    toast.error('Gagal mengekspor CSV: ' + ((err as any)?.message || 'lihat console'))
  }
}

const handleExportProgram = async () => {
  try {
    const params = new URLSearchParams()
    if (filterSearch.value) params.append('search', filterSearch.value)

    if (Array.isArray(filterTanggal.value) && filterTanggal.value.length === 2) {
      const [from, to] = filterTanggal.value
      params.append('tanggal_from', formatYMDLocal(from))
      params.append('tanggal_to', formatYMDLocal(to))
    } else if (filterTanggal.value) {
      params.append('tanggal', formatYMDLocal(filterTanggal.value))
    }

    if (filterDonatur.value) params.append('donatur_id', filterDonatur.value)
    if (filterProgram.value) params.append('program_id', filterProgram.value)
    if (filterMitra.value) params.append('mitra_id', filterMitra.value)
    if (filterKantorCabang.value) params.append('kantor_cabang_id', filterKantorCabang.value)
    if (filterFundraiser.value) params.append('fundraiser_id', filterFundraiser.value)

    const url = `/admin/api/transaksi/export-program?${params.toString()}`
    const res = await fetch(url, { credentials: 'same-origin' })
    if (!res.ok) throw new Error('Failed to export program CSV')

    const blob = await res.blob()
    const filename = res.headers.get('Content-Disposition')?.split('filename=')?.pop()?.replace(/\"/g, '') || `transaksi_export_program_${new Date().toISOString().slice(0,19).replace(/[:T]/g,'_')}.csv`

    const a = document.createElement('a')
    const urlObject = URL.createObjectURL(blob)
    a.href = urlObject
    a.download = filename
    document.body.appendChild(a)
    a.click()
    a.remove()
    URL.revokeObjectURL(urlObject)

    toast.success('Export Program CSV berhasil')
  } catch (err) {
    console.error('Export Program failed', err)
    toast.error('Gagal mengekspor program: ' + ((err as any)?.message || 'lihat console'))
  }
}

onMounted(async () => {
  // Ensure user permissions are loaded before fetching data so cellRenderers can rely on them
  await fetchUser()
  await fetchFilterOptions()
  // If the user is an 'atasan', default the fundraiser filter to themselves
  try {
    const roleNameInit = String(user?.value?.role?.name || '').toLowerCase()
    const isAtasanInit = roleNameInit.includes('atasan') || roleNameInit.includes('supervisor') || roleNameInit.includes('manager')
    if (isAtasanInit && user?.value?.id) {
      filterFundraiser.value = String(user.value.id)
      // refresh datasource to apply the filter
      refreshGrid()
    }
  } catch (e) {
    // ignore
  }
  await fetchData()

  // Refresh AG Grid to re-run cell renderers with correct permission flags
  try {
    const gridApi = (agGridRef.value as any)?.api
    if (gridApi && typeof gridApi.refreshCells === 'function') {
      gridApi.refreshCells({ force: true })
    }
  } catch (e) {
    // Ignore
  }
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
