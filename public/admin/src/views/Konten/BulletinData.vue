<template>
  <AdminLayout>
    <div
      class="min-h-screen rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12"
    >
        <div class="mb-6 flex items-center justify-between">
        <h3 class="font-semibold text-gray-800 text-theme-xl dark:text-white/90 sm:text-2xl">
          {{ currentPageTitle }}
        </h3>
          <!-- Add button hidden for view-only listing -->
      </div>

      <!-- Filter Section -->
      <div class="mb-6 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="flex gap-4">
          <div class="flex-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Nama Bulletin
            </label>
            <input
              type="text"
              v-model="filterNamaBulletin"
              placeholder="Cari nama bulletin..."
              class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
          </div>
          <div class="flex items-end">
            <button
              @click="resetFilter"
              class="h-11 rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]"
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
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRoute } from 'vue-router'
import { AgGridVue } from 'ag-grid-vue3'
import 'ag-grid-community/styles/ag-grid.css'
import 'ag-grid-community/styles/ag-theme-alpine.css'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import { useAuth } from '@/composables/useAuth'


const route = useRoute()
const currentPageTitle = ref<string>(String(route.meta.title || 'Bulletin Data'))
const { fetchUser, hasPermission, isAdmin } = useAuth()
const canCreate = computed(() => isAdmin() || hasPermission('create landing bulletin'))
const canUpdate = computed(() => isAdmin() || hasPermission('update landing bulletin'))
const canDelete = computed(() => isAdmin() || hasPermission('delete landing bulletin'))
const canView = computed(() => isAdmin() || hasPermission('view landing bulletin'))
fetchUser()

// Column definitions
const columnDefs = [
  {
    headerName: 'Nama Bulletin',
    field: 'namaBulletin',
    sortable: true,
    flex: 1,
  },
  {
    headerName: 'Tanggal Bulletin',
    field: 'tanggalBulletin',
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

      const detailsBtn = document.createElement('button')
      detailsBtn.className = 'flex items-center justify-center w-8 h-8 rounded-lg text-brand-500 hover:bg-brand-50 dark:hover:bg-brand-500/10 transition-colors'
      detailsBtn.innerHTML = `
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M12 20h9"></path>
          <path d="M12 4h9M12 12h9"></path>
        </svg>
      `
      detailsBtn.onclick = () => handleDetails(params.data.id)

      div.appendChild(detailsBtn)
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

// Sample data
const rowDataArray = [
  {
    id: '1',
    namaBulletin: 'Bulletin Bulanan Januari 2024',
    tanggalBulletin: '2024-01-15',
    tanggal: '2024-01-10',
  },
  {
    id: '2',
    namaBulletin: 'Bulletin Bulanan Februari 2024',
    tanggalBulletin: '2024-02-20',
    tanggal: '2024-02-15',
  },
  {
    id: '3',
    namaBulletin: 'Bulletin Bulanan Maret 2024',
    tanggalBulletin: '2024-03-10',
    tanggal: '2024-03-05',
  },
  {
    id: '4',
    namaBulletin: 'Bulletin Bulanan April 2024',
    tanggalBulletin: '2024-04-05',
    tanggal: '2024-04-01',
  },
  {
    id: '5',
    namaBulletin: 'Bulletin Bulanan Mei 2024',
    tanggalBulletin: '2024-05-12',
    tanggal: '2024-05-08',
  },
  {
    id: '6',
    namaBulletin: 'Bulletin Bulanan Juni 2024',
    tanggalBulletin: '2024-06-18',
    tanggal: '2024-06-14',
  },
  {
    id: '7',
    namaBulletin: 'Bulletin Bulanan Juli 2024',
    tanggalBulletin: '2024-07-25',
    tanggal: '2024-07-20',
  },
  {
    id: '8',
    namaBulletin: 'Bulletin Bulanan Agustus 2024',
    tanggalBulletin: '2024-08-30',
    tanggal: '2024-08-25',
  },
]

// Create ref for rowData
const rowData = ref(rowDataArray)

// Handle add button
const handleAdd = () => {
  // Add disabled for view-only listing
}

// Handle edit
const handleEdit = (id: string) => {
  // Edit disabled for view-only listing
}

// Handle delete
const handleDelete = (id: string) => {
  // Delete disabled for view-only listing
}

// Handle details - open read-only view
const handleDetails = (id: string) => {
  window.location.href = `/company/landing-bulletin/${id}/edit?view=1`
}

// Filter state
const filterNamaBulletin = ref('')

// Filtered data for AG Grid
const gridRowData = computed(() => {
  let filtered = [...rowDataArray]
  
  // Filter by nama bulletin
  if (filterNamaBulletin.value) {
    filtered = filtered.filter((item) =>
      item.namaBulletin.toLowerCase().includes(filterNamaBulletin.value.toLowerCase())
    )
  }
  
  return filtered
})

// Reset filter
const resetFilter = () => {
  filterNamaBulletin.value = ''
}
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

