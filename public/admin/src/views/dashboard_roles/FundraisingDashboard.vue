<template>
  <div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="rounded-lg border border-gray-100 p-4 bg-white dark:bg-white/[0.03]">
        <div class="text-sm text-gray-600">Total Dana Terkumpul</div>
        <div class="mt-2 text-2xl font-semibold text-gray-800">{{ formatCurrency(stats.total_collected || 0) }}</div>
      </div>
      <div class="rounded-lg border border-gray-100 p-4 bg-white dark:bg-white/[0.03]">
        <div class="text-sm text-gray-600">Donatur</div>
        <div class="mt-2 text-2xl font-semibold text-gray-800">{{ stats.donor_count || 0 }}</div>
      </div>
      <div class="rounded-lg border border-gray-100 p-4 bg-white dark:bg-white/[0.03]">
        <div class="text-sm text-gray-600">Rata-rata Donasi</div>
        <div class="mt-2 text-2xl font-semibold text-gray-800">{{ formatCurrency(stats.avg_donation || 0) }}</div>
      </div>
      <div class="rounded-lg border border-gray-100 p-4 bg-white dark:bg-white/[0.03]">
        <div class="text-sm text-gray-600">Growth vs Periode Sebelumnya</div>
        <div class="mt-2 text-2xl font-semibold text-gray-800">{{ formatPercent(stats.growth_percent) }}</div>
      </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
      <div class="mb-4 flex items-center justify-between">
        <h3 class="font-semibold text-gray-800 text-lg dark:text-white/90">Donasi Periode</h3>
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

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="rounded-lg border p-4">
          <h4 class="text-sm text-gray-600">Donasi per Campaign</h4>
          <apex-chart v-if="campaignSeries.length" type="bar" height="220" :options="campaignOptions" :series="campaignSeries"></apex-chart>
        </div>

        <div class="rounded-lg border p-4">
          <h4 class="text-sm text-gray-600">Channel Performance</h4>
          <apex-chart v-if="channelSeries.length" type="pie" height="220" :options="channelOptions" :series="channelSeries"></apex-chart>
        </div>
      </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
      <h3 class="font-semibold text-gray-800 mb-4">Donasi Masuk (Terbaru)</h3>
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="text-left text-gray-500">
              <th class="p-2">Tanggal</th>
              <th class="p-2">Donatur</th>
              <th class="p-2">Campaign</th>
              <th class="p-2 text-right">Nominal</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="t in recent" :key="t.id" class="border-t">
              <td class="p-2 text-gray-700">{{ t.tanggal_transaksi }}</td>
              <td class="p-2 text-gray-700">{{ t.donatur_id || '-' }}</td>
              <td class="p-2 text-gray-700">{{ t.program_id || '-' }}</td>
              <td class="p-2 text-right font-semibold text-gray-800">{{ formatCurrency(t.nominal) }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import ApexChart from 'vue3-apexcharts'

const props = defineProps({ days: { type: Number, default: 30 } })

const days = ref(props.days || 30)
const stats = ref({})
const recent = ref([])
const series = ref([])
const options = ref({})

const campaignSeries = ref([])
const campaignOptions = ref({})
const channelSeries = ref([])
const channelOptions = ref({})

const formatCurrency = (n) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(n)
const formatPercent = (n) => (n === null || n === undefined) ? '-' : `${n.toFixed(1)}%`

const loadData = async () => {
  try {
    const res = await fetch(`/admin/api/dashboard/stats?days=${days.value}`, { credentials: 'same-origin' })
    if (!res.ok) return
    const json = await res.json()
    if (json.success && json.data && json.data.fundraising) {
      const f = json.data.fundraising
      stats.value = {
        total_collected: f.total_collected,
        donor_count: f.donor_count,
        avg_donation: f.avg_donation,
        growth_percent: f.growth_percent,
      }
      recent.value = f.recent_transactions || []

      // build timeseries chart
      const categories = (f.timeseries || []).map(i => i.date)
      const data = (f.timeseries || []).map(i => i.total)
      if (data.length) {
        series.value = [{ name: 'Donasi', data }]
        options.value = {
          chart: { toolbar: { show: false }, zoom: { enabled: false } },
          stroke: { curve: 'smooth', width: 2 },
          xaxis: { categories, labels: { style: { colors: '#6B7280' } } },
          yaxis: { labels: { formatter: v => v }, forceNiceScale: true },
          tooltip: { y: { formatter: v => formatCurrency(v) } },
          colors: ['#06B6D4'],
          markers: { size: 0 },
        }
      }

      // campaign
      if (Array.isArray(f.per_campaign) && f.per_campaign.length) {
        const labels = f.per_campaign.map(p => p.program_name)
        const dataC = f.per_campaign.map(p => p.total)
        campaignSeries.value = [{ name: 'Per Campaign', data: dataC }]
        campaignOptions.value = { xaxis: { categories: labels }, colors: ['#7C3AED'] }
      } else {
        campaignSeries.value = []
      }

      // channel
      if (Array.isArray(f.channel_performance) && f.channel_performance.length) {
        const labels = f.channel_performance.map(c => c.channel)
        const dataCh = f.channel_performance.map(c => c.total)
        channelSeries.value = dataCh
        channelOptions.value = { labels }
      } else {
        channelSeries.value = []
      }
    }
  } catch (err) {
    console.error('Error loading fundraising dashboard', err)
  }
}

onMounted(() => { loadData() })
watch(days, () => loadData())
</script>

<style scoped></style>
