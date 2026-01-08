<template>
  <div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="rounded-lg border border-gray-100 p-4 bg-white dark:bg-white/[0.03]">
        <div class="text-sm text-gray-600">Total Transaksi</div>
        <div class="mt-2 text-2xl font-semibold text-gray-800">{{ formatCurrency(stats.total_transaksi || 0) }}</div>
      </div>
      <div class="rounded-lg border border-gray-100 p-4 bg-white dark:bg-white/[0.03]">
        <div class="text-sm text-gray-600">Total Fee</div>
        <div class="mt-2 text-2xl font-semibold text-gray-800">{{ formatCurrency(stats.total_fee || 0) }}</div>
      </div>
      <div class="rounded-lg border border-gray-100 p-4 bg-white dark:bg-white/[0.03]">
        <div class="text-sm text-gray-600">Total Donatur</div>
        <div class="mt-2 text-2xl font-semibold text-gray-800">{{ stats.donor_count || 0 }} donatur</div>
      </div>
      <div class="rounded-lg border border-gray-100 p-4 bg-white dark:bg-white/[0.03]">
        <div class="text-sm text-gray-600">Total Transaksi</div>
        <div class="mt-2 text-2xl font-semibold text-gray-800">{{ stats.transaksi_count || 0 }} transaksi</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const stats = ref({})

const formatCurrency = (n) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(n)

const loadData = async () => {
  try {
    const res = await fetch('/admin/api/dashboard/stats', { credentials: 'same-origin' })
    if (!res.ok) return
    const json = await res.json()
    if (json.success && json.data && json.data.mitra) {
      stats.value = json.data.mitra
    }
  } catch (err) {
    console.error('Error loading mitra dashboard', err)
  }
}

onMounted(() => loadData())
</script>

<style scoped></style>
