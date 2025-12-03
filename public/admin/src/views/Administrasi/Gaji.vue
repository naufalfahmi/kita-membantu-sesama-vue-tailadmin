<template>
  <AdminLayout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />
    <div class="min-h-screen rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12">
      <div class="mb-6 flex items-center justify-between">
        <h3 class="font-semibold text-gray-800 text-theme-xl dark:text-white/90 sm:text-2xl">{{ currentPageTitle }}</h3>
        <router-link class="flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600" :to="{ name: 'Tambah Gaji' }">Tambah Gaji</router-link>
      </div>

      <!-- Filter Section -->
      <div class="mb-6 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="flex gap-4">
          <div class="flex-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Nama Gaji
            </label>
            <input
              type="text"
              v-model="filterNamaGaji"
              placeholder="Cari nama gaji..."
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

      <div class="ag-theme-alpine dark:ag-theme-alpine-dark" style="width: 100%; min-height: 400px;">
        <ag-grid-vue
          class="ag-theme-alpine"
          style="width: 100%;"
          :columnDefs="columnDefs"
          :rowData="filteredRowData"
          :defaultColDef="defaultColDef"
          :pagination="true"
          :paginationPageSize="20"
          :animateRows="true"
          theme="legacy"
          :suppressHorizontalScroll="true"
          :domLayout="'autoHeight'"
          @grid-ready="onGridReady"
        />
      </div>

      <ConfirmModal
        :isOpen="showDeleteModal"
        title="Hapus Gaji"
        message="Apakah Anda yakin ingin menghapus gaji ini? Tindakan ini tidak dapat dibatalkan."
        confirmText="Hapus"
        confirmButtonClass="bg-red-500 hover:bg-red-600"
        @confirm="confirmDelete"
        @cancel="cancelDelete"
      />
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useToast } from 'vue-toastification';
import ConfirmModal from '@/components/common/ConfirmModal.vue';
import AdminLayout from '@/components/layout/AdminLayout.vue';
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue';
import { AgGridVue } from 'ag-grid-vue3';
import 'ag-grid-community/styles/ag-grid.css';
import 'ag-grid-community/styles/ag-theme-alpine.css';

const route = useRoute();
const router = useRouter();
const toast = useToast();

const currentPageTitle = computed(() => (route.meta.title as string) || 'Gaji');

const columnDefs = [
  { headerName: 'Jabatan', valueGetter: (params: any) => params.data?.jabatan?.nama || '-', sortable: true, filter: true, flex: 1 },
  { headerName: 'Pangkat', valueGetter: (params: any) => params.data?.pangkat?.nama || '-', sortable: true, filter: true, flex: 1 },
  { headerName: 'Nama', field: 'nama', sortable: true, filter: true, flex: 1 },
  { headerName: 'Nominal', field: 'nominal', valueGetter: (params: any) => formatCurrency(params.data?.nominal), sortable: true, flex: 1 },
  { headerName: 'Tanggal Dibuat', field: 'created_at', valueGetter: (params: any) => formatDate(params.data?.created_at), flex: 1 },
  { headerName: 'Actions', field: 'actions', cellRenderer: (params: any) => actionCellRenderer(params), width: 160 },
  // Updated actions column with icon buttons
];
const rowData = ref<any[]>([]);
const gridApi = ref(null);
const defaultColDef = { resizable: true };

const showDeleteModal = ref(false);
const deleteId = ref<string | null>(null);

// Filter state
const filterNamaGaji = ref('');

// Filtered data for AG Grid
const filteredRowData = computed(() => {
  let filtered = [...rowData.value];
  
  if (filterNamaGaji.value) {
    filtered = filtered.filter((item) =>
      item.nama?.toLowerCase().includes(filterNamaGaji.value.toLowerCase())
    );
  }
  
  return filtered;
});

function formatCurrency(value: any) {
  if (value == null || isNaN(Number(value))) return '-';
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(Number(value));
}

function formatDate(value: string) {
  if (!value) return '-';
  try {
    return new Date(value).toLocaleDateString('id-ID');
  } catch (e) {
    return value;
  }
}

function actionCellRenderer(params: any) {
  if (!params.data || !params.data.id) return '';
  const id = params.data.id;
  const container = document.createElement('div');
  container.className = 'flex items-center gap-3';

  const editBtn = document.createElement('button');
  editBtn.className = 'flex items-center justify-center w-8 h-8 rounded-lg text-brand-500 hover:bg-brand-50 dark:hover:bg-brand-500/10 transition-colors';
  editBtn.innerHTML = `
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
    </svg>
  `;
  editBtn.addEventListener('click', () => {
    router.push(`/administrasi/gaji/${id}/edit`);
  });
  container.appendChild(editBtn);

  const delBtn = document.createElement('button');
  delBtn.className = 'flex items-center justify-center w-8 h-8 rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors';
  delBtn.innerHTML = `
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <path d="M3 6h18"></path>
      <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
      <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
      <line x1="10" y1="11" x2="10" y2="17"></line>
      <line x1="14" y1="11" x2="14" y2="17"></line>
    </svg>
  `;
  delBtn.addEventListener('click', () => {
    deleteId.value = id;
    showDeleteModal.value = true;
  });
  container.appendChild(delBtn);

  return container;
}

async function fetchData() {
  try {
    const res = await fetch('/admin/api/gaji?per_page=1000', { credentials: 'same-origin' });
    const json = await res.json();
    if (json.success) {
      rowData.value = json.data;
    } else {
      toast.error(json.message || 'Gagal memuat data');
    }
  } catch (e) {
    toast.error('Gagal memuat data');
  }
}

function onGridReady(params: any) {
  gridApi.value = params.api;
}

async function confirmDelete() {
  if (!deleteId.value) return;
  try {
    const tokRes = await fetch('/admin/api/csrf-token', { credentials: 'same-origin' });
    const tokJson = await tokRes.json();

    const res = await fetch(`/admin/api/gaji/${deleteId.value}`, {
      method: 'DELETE',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': tokJson.csrf_token,
      },
      credentials: 'same-origin',
    });
    const json = await res.json();
    if (json.success) {
      toast.success(json.message || 'Gaji berhasil dihapus');
      fetchData();
    } else {
      toast.error(json.message || 'Gagal menghapus gaji');
    }
  } catch (e) {
    toast.error('Gagal menghapus gaji');
  } finally {
    showDeleteModal.value = false;
    deleteId.value = null;
  }
}

function cancelDelete() {
  showDeleteModal.value = false;
  deleteId.value = null;
}

function resetFilter() {
  filterNamaGaji.value = '';
}

function handleAdd() {
  router.push({ name: 'admin.administrasi.gaji.create' });
}

onMounted(() => {
  fetchData();
});
</script>

<style scoped>
.toolbar { margin-bottom: 1rem; }

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

