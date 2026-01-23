<template>
  <AdminLayout>
    <div class="min-h-screen rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12">
      <div class="mb-6 flex items-center justify-between">
        <h3 class="font-semibold text-gray-800 text-theme-xl dark:text-white/90 sm:text-2xl">{{ currentPageTitle }}</h3>
        <div class="flex items-center gap-3">
          <button v-if="canGenerate" @click="openGenerate" class="flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600">Generate Periode</button>
          <span v-if="canGenerate && missingCount > 0" class="inline-flex items-center rounded-full bg-yellow-100 px-3 py-1 text-sm font-medium text-yellow-800">+{{ missingCount }} belum</span>
        </div>
      </div>

      <div class="mb-6 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
          <div class="flex-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Filter Tahun</label>
            <input type="number" v-model="filterYear" placeholder="Tahun..." class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800" />
          </div>
          <div class="flex-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Filter Bulan</label>
            <SearchableSelect v-model="filterMonth" :options="monthOptions" placeholder="Semua Bulan" />
          </div>
        </div>
      </div>

      <div class="ag-theme-alpine dark:ag-theme-alpine-dark" style="width:100%; min-height:400px;">
        <ag-grid-vue class="ag-theme-alpine" style="width:100%;" :columnDefs="columnDefs" :rowData="gridRowData" :defaultColDef="defaultColDef" :pagination="true" :paginationPageSize="20" :animateRows="true" :domLayout="'autoHeight'"/>
      </div>

      <ConfirmModal v-if="canGenerate" :isOpen="showGenerateModal" title="Generate Periode" message="Generate payroll untuk bulan & tahun yang dipilih. Semua karyawan aktif akan dibuatkan record." confirmText="Generate" confirmButtonClass="bg-brand-500 hover:bg-brand-600" @confirm="confirmGenerate" @cancel="closeGenerate" />

    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import ConfirmModal from '@/components/common/ConfirmModal.vue'
import SearchableSelect from '@/components/forms/SearchableSelect.vue'
import { AgGridVue } from 'ag-grid-vue3'
import { useToast } from 'vue-toastification'
import { useRouter } from 'vue-router'
import { useAuth } from '@/composables/useAuth'

const { hasPermission, isAdmin } = useAuth()
const canGenerate = computed(() => isAdmin() || hasPermission('generate payroll') || hasPermission('create payroll'))
const router = useRouter()
const toast = useToast()
const currentPageTitle = 'Rekap Gaji (Payroll)'

const months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']

const filterYear = ref(new Date().getFullYear())
const filterMonth = ref('')
let filterTimeout: any = null

const monthOptions = [
  { label: 'Semua Bulan', value: '' },
  ...months.map((m, i) => ({ label: m, value: String(i+1) }))
]

const showGenerateModal = ref(false)
const genMonth = ref(new Date().getMonth()+1)
const genYear = ref(new Date().getFullYear())

const gridRowData = ref([])
const missingCount = ref(0)

const columnDefs = [
  { headerName: 'Periode', field: 'periode', flex: 1 },

  { headerName: 'Pending', field: 'pending_count', width: 120, cellStyle: { textAlign: 'center' }, cellRenderer: (params: any) => {
    const s = document.createElement('span')
    s.className = 'text-sm font-medium text-gray-700'
    s.textContent = String(params.value || 0)
    return s
  }},
  { headerName: 'Locked', field: 'locked_count', width: 120, cellStyle: { textAlign: 'center' }, cellRenderer: (params: any) => {
    const s = document.createElement('span')
    s.className = 'text-sm font-medium text-gray-700'
    s.textContent = String(params.value || 0)
    return s
  }},
  { headerName: 'Transferred', field: 'transferred_count', width: 140, cellStyle: { textAlign: 'center' }, cellRenderer: (params: any) => {
    const s = document.createElement('span')
    s.className = 'text-sm font-medium text-gray-700'
    s.textContent = String(params.value || 0)
    return s
  }},
  { headerName: 'Generated At', field: 'generated_at', width: 200 },
  { headerName: 'Actions', field: 'actions', width: 160, cellStyle: { display: 'flex', justifyContent: 'center', alignItems: 'center' }, cellRenderer: (params: any) => {
    const div = document.createElement('div')
    div.className = 'flex items-center justify-center gap-3'
    const viewBtn = document.createElement('button')
    viewBtn.className = 'flex items-center justify-center w-8 h-8 rounded-lg text-brand-500 hover:bg-brand-50'
    viewBtn.setAttribute('title', 'Lihat')
    viewBtn.setAttribute('aria-label', 'Lihat')
    viewBtn.innerHTML = `
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <path d="M1.5 12s4-7.5 10.5-7.5S22.5 12 22.5 12s-4 7.5-10.5 7.5S1.5 12 1.5 12z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    `
    viewBtn.onclick = () => router.push(`/operasional/payroll/${params.data.id}`)
    div.appendChild(viewBtn)
    return div
  }}
]

const defaultColDef = { resizable: true, sortable: true }

const loadPeriods = async () => {
  const qs = new URLSearchParams()
  if (filterYear.value) qs.append('year', String(filterYear.value))
  if (filterMonth.value) qs.append('month', String(filterMonth.value))
  const res = await fetch(`/admin/api/operasional/payroll/periods?${qs.toString()}`, { credentials: 'same-origin' })
  const json = await res.json()
  gridRowData.value = (json.data || []).map((p: any) => ({
    id: p.id,
    periode: `${months[(p.month || 1) - 1]} ${p.year}`,
    status: p.status,
    pending_count: p.pending_count || 0,
    locked_count: p.locked_count || 0,
    transferred_count: p.transferred_count || 0,
    generated_at: p.generated_at
  }))
}

const loadMissingCount = async () => {
  try {
    const qs = new URLSearchParams()
    qs.append('year', String(genYear.value))
    qs.append('month', String(genMonth.value))
    const res = await fetch(`/admin/api/operasional/payroll/periods/missing-count?${qs.toString()}`, { credentials: 'same-origin' })
    const json = await res.json()
    if (json.success) missingCount.value = json.missing || 0
  } catch (e) {
    missingCount.value = 0
  }
}

onMounted(async () => {
  // If user only has view permission (no admin-like create/manage/generate), redirect them to their own payroll list (all periods)
  const isViewerOnly = (hasPermission('view payroll') || hasPermission('view remunerasi')) && !isAdmin() && !canGenerate.value
  if (isViewerOnly) {
    router.push('/operasional/payroll/mine')
    return
  }

  await loadPeriods()
  await loadMissingCount()
})

// debounce filter changes
watch([filterYear, filterMonth], () => {
  if (filterTimeout) clearTimeout(filterTimeout)
  filterTimeout = setTimeout(() => {
    loadPeriods()
  }, 450)
})

watch([genYear, genMonth], () => {
  // update missing count when generate year/month changed (debounced)
  if (filterTimeout) clearTimeout(filterTimeout)
  filterTimeout = setTimeout(() => {
    loadMissingCount()
  }, 300)
})

const openGenerate = () => { showGenerateModal.value = true }
const closeGenerate = () => { showGenerateModal.value = false }

const getCsrfToken = async (): Promise<string> => {
  const meta = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
  if (meta) return meta
  const res = await fetch('/admin/api/csrf-token', { credentials: 'same-origin' })
  const json = await res.json()
  return json.csrf_token || ''
}

const confirmGenerate = async () => {
  try {
    const csrf = await getCsrfToken()
    const res = await fetch('/admin/api/operasional/payroll/periods/generate', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrf,
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({ year: genYear.value, month: genMonth.value }),
      credentials: 'same-origin'
    })
    const json = await res.json()
    if (!json.success) throw new Error('Failed')
    toast.success('Periode payroll berhasil digenerate')
    closeGenerate()
    loadPeriods()
  } catch (err) {
    toast.error('Gagal generate periode')
  }
}
</script>
