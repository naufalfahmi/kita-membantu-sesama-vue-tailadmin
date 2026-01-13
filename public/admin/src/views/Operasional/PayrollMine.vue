<template>
  <AdminLayout>
    <div class="min-h-screen rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12">
      <div class="mb-6 flex items-center justify-between">
        <h3 class="font-semibold text-gray-800 text-theme-xl dark:text-white/90 sm:text-2xl">Slip Gaji Saya</h3>
        <div class="flex items-center gap-3">
          <label class="text-sm text-gray-500">Tahun</label>
          <select v-model="selectedYear" class="border rounded-md px-3 py-1 text-sm">
            <option :value="''">Semua</option>
            <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
          </select>
        </div>
      </div>

      <div class="ag-theme-alpine" style="width:100%; min-height:300px;">
        <ag-grid-vue class="ag-theme-alpine" style="width:100%; min-height:300px;" :columnDefs="columnDefs" :rowData="rowData" :defaultColDef="defaultColDef" :pagination="true" :paginationPageSize="20" :animateRows="true" :domLayout="'autoHeight'"/>
      </div>
    </div>

    <!-- pinned total button -->
    <div class="fixed right-6 bottom-6 z-50">
      <button class="flex items-center gap-3 px-4 py-3 rounded-lg text-white bg-brand-500 hover:bg-brand-600 shadow-lg">
        <span class="text-sm">Total Gaji</span>
        <span class="text-lg font-semibold">Rp {{ formattedTotal }}</span>
      </button>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref, onMounted, watch, computed } from 'vue'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import { AgGridVue } from 'ag-grid-vue3'
import { useRouter } from 'vue-router'
import { useToast } from 'vue-toastification' 

const router = useRouter()
const toast = useToast()

const rowData = ref([])
const defaultColDef = { resizable: true, sortable: true }

// year filter
const currentYear = new Date().getFullYear()
const years: number[] = []
for (let i = 0; i < 5; i++) years.push(currentYear - i)
// default to 'Semua' (empty string) so no year filter is applied by default
const selectedYear = ref<number|string>('')

const fetchData = async () => {
  try {
    const url = `/admin/api/operasional/payroll/me/list${selectedYear.value ? '?year=' + selectedYear.value : ''}`
    const res = await fetch(url, { credentials: 'same-origin' })
    const json = await res.json()
    if (res.ok && json.success) {
      rowData.value = json.data
    } else {
      toast.error(json.message || 'Gagal mengambil data slip')
    }
  } catch (err) {
    toast.error('Gagal mengambil slip gaji')
  }
}

onMounted(fetchData)
watch(selectedYear, () => { fetchData() })

const totalAll = computed(() => {
  return rowData.value.reduce((acc: number, r: any) => acc + (Number(r.total) || 0), 0)
})
const formattedTotal = computed(() => totalAll.value.toLocaleString())

const columnDefs = [
  { headerName: 'Periode', field: 'period_label', flex: 1 },
  { headerName: 'Status', field: 'status', width: 150, cellRenderer: (params: any) => {
    const mapLabel: Record<string,string> = { pending: 'Pending', locked: 'Selesai', transferred: 'Ditransfer' }
    const mapColor: Record<string,string> = { 
      pending: 'bg-yellow-100 text-yellow-800', 
      locked: 'bg-green-100 text-green-800', 
      transferred: 'bg-blue-100 text-blue-800' 
    }
    const span = document.createElement('span')
    span.className = `inline-block rounded-full px-3 py-1 text-xs font-medium ${mapColor[params.value] || 'bg-gray-100 text-gray-800'}`
    span.textContent = mapLabel[params.value] || params.value || ''
    return span
  }},
  { headerName: 'Total', field: 'total', width: 160, cellRenderer: (params: any) => {
    const span = document.createElement('span')
    span.className = 'text-sm font-medium text-gray-800'
    span.textContent = params.value ? `Rp ${params.value.toLocaleString()}` : 'Rp 0'
    return span
  }},
  { headerName: 'Bukti', field: 'transfer_proof', width: 140, cellRenderer: (params: any) => {
    const div = document.createElement('div')
    if (params.data && params.data.transfer_proof) {
      const link = document.createElement('a')
      link.href = `/storage/${params.data.transfer_proof}`
      link.target = '_blank'
      link.className = 'text-sm text-brand-500 hover:underline'
      link.textContent = 'Lihat Bukti'
      div.appendChild(link)
    } else {
      const span = document.createElement('span')
      span.className = 'text-sm text-gray-500'
      span.textContent = '—'
      div.appendChild(span)
    }
    return div
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

// initial load handled by fetchData() onMounted and year watcher
</script>
