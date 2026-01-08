<template>
  <div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="rounded-lg border border-gray-100 p-4 bg-white dark:bg-white/[0.03]">
        <div class="text-sm text-gray-600">Transaksi by Mitra</div>
        <div class="mt-2 text-2xl font-semibold text-gray-800">{{ formatCurrency(topMitraTotal) }}</div>
      </div>
      <div class="rounded-lg border border-gray-100 p-4 bg-white dark:bg-white/[0.03]">
        <div class="text-sm text-gray-600">Transaksi Cabang {{ stats.branch_name ? '(' + stats.branch_name + ')' : '' }}</div>
        <div class="mt-2 text-2xl font-semibold text-gray-800">{{ formatCurrency(stats.transaksi_total || 0) }}</div>
      </div>
      <div class="rounded-lg border border-gray-100 p-4 bg-white dark:bg-white/[0.03]">
        <div class="text-sm text-gray-600">Total Mitra</div>
        <div class="mt-2 text-2xl font-semibold text-gray-800">{{ stats.mitra_count || 0 }} mitra</div>
      </div>
      <div class="rounded-lg border border-gray-100 p-4 bg-white dark:bg-white/[0.03]">
        <div class="text-sm text-gray-600">Total Donatur</div>
        <div class="mt-2 text-2xl font-semibold text-gray-800">{{ stats.donatur_count || 0 }} donatur</div>
      </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
      <div class="mb-4 flex items-center justify-between">
        <h3 class="font-semibold text-gray-800 text-lg dark:text-white/90">Transaksi Periode</h3>
        <div class="flex items-center gap-3">
          <label class="text-sm text-gray-500">Rentang</label>
          <select v-model.number="days" @change="loadData" class="h-9 rounded-lg border border-gray-300 bg-white px-3 text-sm">
            <option :value="7">7 Hari</option>
            <option :value="30">30 Hari</option>
            <option :value="90">90 Hari</option>
          </select>
        </div>
      </div>

      <div class="mb-6">
        <apex-chart v-if="series.length" type="area" height="260" :options="options" :series="series"></apex-chart>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
        <h4 class="text-sm text-gray-600">Top Mitra by Donasi</h4>
        <div class="overflow-x-auto mt-3">
          <table class="w-full text-sm">
            <thead>
              <tr class="text-left text-gray-500">
                <th class="p-2">Mitra</th>
                <th class="p-2 text-right">Total</th>
                <th class="p-2 text-right">Count</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="m in perMitra" :key="m.mitra_id" class="border-t">
                <td class="p-2 text-gray-700">{{ m.mitra_name || m.mitra_id || 'Unknown' }}</td>
                <td class="p-2 text-right font-semibold text-gray-800">{{ formatCurrency(m.total || 0) }}</td>
                <td class="p-2 text-right text-gray-700">{{ m.count || 0 }}</td>
              </tr>
              <tr v-if="perMitra.length === 0">
                <td class="p-2 text-gray-500" colspan="3">No data</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
        <h4 class="text-sm text-gray-600">Recent Transactions</h4>
        <div class="overflow-x-auto mt-3">
          <table class="w-full text-sm">
            <thead>
              <tr class="text-left text-gray-500">
                <th class="p-2">Tanggal</th>
                <th class="p-2">Donatur</th>
                <th class="p-2">Mitra</th>
                <th class="p-2 text-right">Nominal</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="t in recent" :key="t.id" class="border-t">
                <td class="p-2 text-gray-700">{{ t.tanggal_transaksi }}</td>
                <td class="p-2 text-gray-700">{{ t.donatur_name || t.donatur_id || '-' }}</td>
                <td class="p-2 text-gray-700">{{ t.mitra_name || t.mitra_id || '-' }}</td>
                <td class="p-2 text-right font-semibold text-gray-800">{{ formatCurrency(t.nominal) }}</td>
              </tr>
              <tr v-if="recent.length === 0">
                <td class="p-2 text-gray-500" colspan="4">No recent transactions</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, computed } from 'vue'
import ApexChart from 'vue3-apexcharts'

const days = ref(30)
const stats = ref({})
const recent = ref([])
const series = ref([])
const options = ref({})

const perMitra = ref([])

const topMitraTotal = computed(() => {
  if (perMitra.value && perMitra.value.length) return perMitra.value[0].total || 0
  return stats.value.transaksi_total || 0
})

const formatCurrency = (n) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(n)

const loadData = async () => {
  try {
    const res = await fetch(`/admin/api/dashboard/stats?days=${days.value}`, { credentials: 'same-origin' })
    if (!res.ok) return
    const json = await res.json()
      if (json.success && json.data && json.data.admin_cabang) {
      const a = json.data.admin_cabang
      stats.value = {
        mitra_count: a.mitra_count,
        donatur_count: a.donatur_count,
        absensi_today: a.absensi_today,
        payroll_records: a.payroll_records,
        transaksi_total: a.transaksi_total,
        branch_name: a.branch_name || null,
      }
      recent.value = a.recent_transactions || []

      const categories = (a.timeseries || []).map(i => i.date)
      const data = (a.timeseries || []).map(i => i.total)
      if (data.length) {
        series.value = [{ name: 'Donasi', data }]
        options.value = {
          chart: { toolbar: { show: false }, zoom: { enabled: false } },
          stroke: { curve: 'smooth', width: 2 },
          xaxis: { categories, labels: { style: { colors: '#6B7280' } } },
          yaxis: { labels: { formatter: (v) => v }, forceNiceScale: true },
          tooltip: { y: { formatter: v => formatCurrency(v) } },
          colors: ['#06B6D4'],
          markers: { size: 0 },
        }
      }

      perMitra.value = Array.isArray(a.per_mitra) ? a.per_mitra : []
    }
  } catch (err) {
    console.error('Error loading admin cabang dashboard', err)
  }
}

onMounted(() => { loadData() })
watch(days, () => loadData())
</script>

<style scoped></style>
