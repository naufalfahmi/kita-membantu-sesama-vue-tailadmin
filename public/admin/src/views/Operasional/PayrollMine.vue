<template>
  <AdminLayout>
    <div class="min-h-screen rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12">
      <div class="mb-6 flex items-center justify-between">
        <h3 class="font-semibold text-gray-800 text-theme-xl dark:text-white/90 sm:text-2xl">Slip Gaji Saya</h3>
      </div>

      <div class="ag-theme-alpine" style="width:100%; min-height:300px;">
        <ag-grid-vue class="ag-theme-alpine" style="width:100%; min-height:300px;" :columnDefs="columnDefs" :rowData="rowData" :defaultColDef="defaultColDef" :pagination="true" :paginationPageSize="20" :animateRows="true" :domLayout="'autoHeight'"/>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import { AgGridVue } from 'ag-grid-vue3'
import { useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'

const router = useRouter()
const toast = useToast()

const rowData = ref([])
const defaultColDef = { resizable: true, sortable: true }

const columnDefs = [
  { headerName: 'Periode', field: 'period_label', flex: 1 },
  { headerName: 'Status', field: 'status', width: 150, cellRenderer: (params: any) => {
    const mapLabel: Record<string,string> = { pending: 'Pending', locked: 'Selesai', transferred: 'Ditransfer' }
    const span = document.createElement('span')
    span.className = 'inline-block rounded-full px-3 py-1 text-xs font-medium bg-gray-100 text-gray-800'
    span.textContent = mapLabel[params.value] || params.value || ''
    return span
  }},
  { headerName: 'Total', field: 'total', width: 160, cellRenderer: (params: any) => {
    const span = document.createElement('span')
    span.className = 'text-sm font-medium text-gray-800'
    span.textContent = params.value ? `Rp ${params.value.toLocaleString()}` : 'Rp 0'
    return span
  }},
  { headerName: 'Actions', field: 'actions', width: 160, cellStyle: { display: 'flex', justifyContent: 'center', alignItems: 'center' }, cellRenderer: (params: any) => {
    const div = document.createElement('div')
    div.className = 'flex items-center justify-center gap-3'
    const printBtn = document.createElement('button')
    printBtn.className = 'flex items-center justify-center px-3 py-1 rounded-lg text-white bg-brand-500 hover:bg-brand-600 text-sm'
    printBtn.innerText = 'Cetak Slip'
    printBtn.onclick = async () => {
      try {
        const res = await fetch('/admin/api/debug-auth', { credentials: 'same-origin' })
        const j = await res.json()
        if (j.authenticated) {
          window.open(`/admin/operasional/payroll/periods/${params.data.period_id}/records/${params.data.id}/slip?format=pdf`, '_blank')
        } else {
          toast.error('Sesi kadaluarsa, silakan masuk kembali')
          router.push('/signin')
        }
      } catch (err) {
        toast.error('Gagal memeriksa status autentikasi')
      }
    }
    div.appendChild(printBtn)
    return div
  }}
]

onMounted(async () => {
  try {
    const res = await fetch('/admin/api/operasional/payroll/me/list', { credentials: 'same-origin' })
    const json = await res.json()
    if (res.ok && json.success) {
      rowData.value = json.data
    } else {
      toast.error(json.message || 'Gagal mengambil data slip')
    }
  } catch (err) {
    toast.error('Gagal mengambil slip gaji')
  }
})
</script>
