<template>
  <AdminLayout>
    <div class="min-h-screen rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12">
      <div class="mb-6 flex items-center justify-between">
        <h3 class="font-semibold text-gray-800 text-theme-xl dark:text-white/90 sm:text-2xl">Detail Periode</h3>
      </div>

      <div class="ag-theme-alpine dark:ag-theme-alpine-dark" style="width:100%; min-height:400px;">
        <ag-grid-vue class="ag-theme-alpine" style="width:100%;" :columnDefs="columnDefs" :rowData="gridRowData" :defaultColDef="defaultColDef" :pagination="true" :paginationPageSize="20" :animateRows="true" :domLayout="'autoHeight'"/>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import { AgGridVue } from 'ag-grid-vue3'
import { useRoute, useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'
import { useAuth } from '@/composables/useAuth'

const { hasPermission, isAdmin } = useAuth()
const canUpdate = computed(() => isAdmin() || hasPermission('update remunerasi'))
const route = useRoute()
const router = useRouter()
const toast = useToast()

const gridRowData = ref([])

const columnDefs = [
  { headerName: 'Nama Karyawan', field: 'name', flex: 1 },
  { headerName: 'Total', field: 'total', width: 200, cellRenderer: (params: any) => {
    const span = document.createElement('span')
    span.className = 'text-sm font-medium text-gray-800'
    const v = params.value || 0
    const formatted = formatCurrency(v)
    span.textContent = formatted
    return span
  } },
  { headerName: 'Status', field: 'status', width: 150, cellRenderer: (params: any) => {
    const mapLabel: Record<string,string> = { pending: 'Pending', locked: 'Selesai', transferred: 'Ditransfer' }
    const mapClass: Record<string,string> = {
      pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
      locked: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
      transferred: 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400'
    }
    const span = document.createElement('span')
    const v = params.value || ''
    span.className = `inline-block rounded-full px-3 py-1 text-xs font-medium ${mapClass[v] || 'bg-gray-100 text-gray-800'}`
    span.textContent = mapLabel[v] || v
    return span
  }},
  { headerName: 'Actions', field: 'actions', width: 160, cellRenderer: (params: any) => {
    const div = document.createElement('div')
    div.className = 'flex items-center gap-2 justify-end'
    const periodId = route.params.id

    // Edit button (pencil SVG) - only show if user canUpdate
    if (canUpdate.value) {
      const editBtn = document.createElement('button')
      editBtn.type = 'button'
      editBtn.title = 'Edit'
      editBtn.className = 'flex items-center justify-center w-8 h-8 rounded-lg text-brand-500 hover:bg-brand-50'
      editBtn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/></svg>`
      editBtn.onclick = (e: any) => { e.stopPropagation(); router.push(`/operasional/payroll/${periodId}/records/${params.data.record_id}/edit`) }
      div.appendChild(editBtn)
    }

    // Print slip button (printer SVG)
    const printBtn = document.createElement('button')
    printBtn.type = 'button'
    printBtn.title = 'Cetak Slip Gaji'
    printBtn.className = 'flex items-center justify-center w-8 h-8 rounded-lg text-gray-700 hover:bg-gray-100'
    printBtn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="6" y="9" width="12" height="8" rx="2" ry="2"/><path d="M6 13H4a2 2 0 0 0-2 2v4h20v-4a2 2 0 0 0-2-2h-2"/><path d="M6 9V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v4"/></svg>`
    printBtn.onclick = async (e: any) => { e.stopPropagation(); try { const res = await fetch('/admin/api/debug-auth', { credentials: 'same-origin' }); const j = await res.json(); if (j.authenticated) { window.open(`/admin/operasional/payroll/periods/${periodId}/records/${params.data.record_id}/slip?format=pdf`, '_blank') } else { toast.error('Sesi kadaluarsa, silakan masuk kembali'); router.push('/signin') } } catch (err) { toast.error('Gagal memeriksa status autentikasi'); } }
    div.appendChild(printBtn)

    return div
  }}
]

const defaultColDef = { resizable: true, sortable: true }

const loadDetail = async () => {
  const id = route.params.id
  const res = await fetch(`/admin/api/operasional/payroll/periods/${id}`, { credentials: 'same-origin' })
  const json = await res.json()
  const period = json.data
  if (!period) return
  gridRowData.value = (period.records || []).map((r: any) => ({
    record_id: r.id,
    name: r.employee ? r.employee.name : '',
    total: r.total_amount || 0,
    status: r.status
  }))
}

const formatCurrency = (v: number) => {
  const value = Number(v || 0)
  const formatted = new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(value)
  return `Rp. ${formatted}`
}

onMounted(loadDetail)

const getCsrfToken = async (): Promise<string> => {
  const meta = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
  if (meta) return meta
  const res = await fetch('/admin/api/csrf-token', { credentials: 'same-origin' })
  const json = await res.json()
  return json.csrf_token || ''
}

const markTransferred = async () => {
  try {
    const id = route.params.id
    const csrf = await getCsrfToken()
    const res = await fetch(`/admin/api/operasional/payroll/periods/${id}/transfer`, { method: 'POST', credentials: 'same-origin', headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' } })
    const json = await res.json()
    if (!json.success) throw new Error('fail')
    toast.success('Periode ditandai transferred')
    loadDetail()
  } catch (err) { toast.error('Gagal mengubah status') }
}
</script>
