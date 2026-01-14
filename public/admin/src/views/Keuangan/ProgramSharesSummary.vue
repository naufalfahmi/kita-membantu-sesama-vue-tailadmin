<template>
  <AdminLayout>
    <div class="min-h-screen rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12">
      <div class="mb-6 flex items-center justify-between">
        <h3 class="font-semibold text-gray-800 text-theme-xl dark:text-white/90 sm:text-2xl">Ringkasan Program - Shares</h3>
        <div class="flex items-center gap-3">
          <button @click="fetchSummary" class="flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
            Muat Ulang
          </button>
        </div>
      </div>

      <div class="mb-6 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3 lg:grid-cols-6">
          <div class="flex-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Rentang Transaksi (opsional)</label>
            <flat-pickr v-model="range" :config="flatpickrRangeConfig" class="h-11 w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm" placeholder="Pilih rentang" />
          </div>
        </div>
      </div>

      <div class="relative" style="width: 100%; height: 600px;">
        <div class="ag-theme-alpine dark:ag-theme-alpine-dark" style="width: 100%; height: 100%;">
          <ag-grid-vue
            ref="agGridRef"
            class="ag-theme-alpine"
            style="width: 100%; height: 100%;"
            :columnDefs="columnDefs"
            :defaultColDef="defaultColDef"
            :rowData="rows"
            :pinnedBottomRowData="pinnedBottomRowData"
            :animateRows="true"
          />
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import flatPickr from 'vue-flatpickr-component'
import 'flatpickr/dist/flatpickr.css'
import { AgGridVue } from 'ag-grid-vue3'
import 'ag-grid-community/styles/ag-grid.css'
import 'ag-grid-community/styles/ag-theme-alpine.css'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import { useToast } from 'vue-toastification'

const agGridRef = ref<InstanceType<typeof AgGridVue> | null>(null)
const toast = useToast()
const range = ref<any>(null)

const flatpickrRangeConfig: any = {
  mode: 'range',
  dateFormat: 'Y-m-d',
  altInput: true,
  altFormat: 'd F Y',
}

const columnDefs = ref<any[]>([
  { headerName: 'Program', field: 'program_name', flex: 2 },
  { headerName: 'Total Transaksi', field: 'total_transaksi', flex: 1, valueFormatter: currencyFormatter },
])

const defaultColDef = {
  resizable: true,
  sortable: true,
}

const rows = ref<any[]>([])
const pinnedBottomRowData = ref<any[]>([])

function currencyFormatter(params: any) {
  if (params.value !== undefined && params.value !== null) {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(params.value)
  }
  return ''
}

async function fetchSummary() {
  try {
    let url = '/admin/api/keuangan/program-shares-summary'
    if (range.value && Array.isArray(range.value) && range.value.length === 2) {
      const [s, e] = range.value
      url += `?start_date=${encodeURIComponent(s)}&end_date=${encodeURIComponent(e)}`
    }
    const res = await fetch(url, { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    const data = await res.json()
    if (!data.success) {
      toast.error('Gagal memuat ringkasan')
      return
    }

    // build column defs dynamically
    const cols = ['program_name', 'total_transaksi', ...(data.data.columns ?? [])]
    const defs: any[] = []
    defs.push({ headerName: 'Program', field: 'program_name', flex: 2 })
    defs.push({ headerName: 'Total Transaksi', field: 'total_transaksi', flex: 1, valueFormatter: currencyFormatter })
    const shareTypeLabels: Record<string, string> = (data.data && data.data.share_type_labels) ? data.data.share_type_labels : {}
    ;(data.data.columns ?? []).forEach((c: string) => {
      const cleaned = String(c || '').replace(/^custom_/, '')
      const header = shareTypeLabels[c] || shareTypeLabels[cleaned] || toHeader(cleaned)
      defs.push({ headerName: header, field: c, flex: 1, valueFormatter: currencyFormatter })
    })

    columnDefs.value = defs
    rows.value = data.data.rows || []

    // pinned totals rows: totals and disbursed_totals
    const totals = Object.assign({ program_name: 'TOTAL' }, data.data.totals || {})
    const disb = Object.assign({ program_name: 'DISBURSED TOTALS' }, data.data.disbursed_totals || {})
    pinnedBottomRowData.value = [totals, disb]
  } catch (e) {
    console.error(e)
    toast.error('Terjadi kesalahan saat memuat ringkasan')
  }
}

function toHeader(key: string) {
  // simple mapping for known keys
  const map: any = { dp: 'DP', ops_1: 'OPS 1', ops_2: 'OPS 2', program: 'Program', fee_mitra: 'Fee Mitra', bonus: 'Bonus', championship: 'Championship' }
  if (map[key]) return map[key]
  // Remove custom_ prefix and make a human-friendly title case label
  const cleaned = String(key || '').replace(/^custom_/, '').replace(/_/g, ' ').trim()
  return cleaned.replace(/\b\w/g, (c) => c.toUpperCase())
}

onMounted(() => {
  fetchSummary()
})
</script>
