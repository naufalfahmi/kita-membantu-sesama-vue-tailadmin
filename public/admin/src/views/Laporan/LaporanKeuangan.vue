<template>
  <AdminLayout>
    <div
      class="min-h-screen rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12"
    >
      <!-- Header Section -->
      <div class="mb-8 text-center">
        <h1 class="mb-2 text-3xl font-bold text-gray-800 dark:text-white/90 sm:text-4xl">
          Laporan Keuangan
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-400">
          Ringkasan dan detail keuangan organisasi
        </p>
      </div>

      <!-- Tab Navigation -->
      <div
        role="tablist"
        class="mb-8 flex flex-wrap items-center justify-center gap-4 border-b border-gray-200 dark:border-gray-700"
      >
        <button
          v-for="tab in tabs"
          :key="tab.id"
          role="tab"
          :aria-selected="activeTab === tab.id"
          :aria-controls="`tabpanel-${tab.id}`"
          :id="`tab-${tab.id}`"
          @click="activeTab = tab.id"
          class="flex items-center gap-2 rounded-t-lg px-6 py-3 text-sm font-medium transition-all duration-200"
          :class="
            activeTab === tab.id
              ? 'border-b-2 border-brand-500 bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400'
              : 'text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.03]'
          "
        >
          <component :is="tab.icon" class="h-5 w-5" />
          <span>{{ tab.label }}</span>
        </button>
      </div>

      <!-- Tab Content -->
      <div class="space-y-6">
        <!-- Tab: Balance -->
        <div
          v-if="activeTab === 'balance'"
          role="tabpanel"
          id="tabpanel-balance"
          aria-labelledby="tab-balance"
        >
          <!-- Filter Tanggal (Hidden Input) -->
          <div class="mb-6 hidden">
            <input
              type="date"
              v-model="filterTanggal"
              class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
          </div>

          <!-- Saldo Card -->
          <div class="mb-6 rounded-lg border border-gray-200 bg-gradient-to-br from-brand-500 to-brand-600 p-6 shadow-lg dark:border-gray-700 dark:from-brand-600 dark:to-brand-700">
            <div class="text-center">
              <p class="mb-2 text-sm font-medium text-white/80">Total Saldo</p>
              <h2 class="text-4xl font-bold text-white sm:text-5xl">
                {{ formatCurrency(balanceData.saldo) }}
              </h2>
            </div>
          </div>

          <!-- Stats Grid -->
          <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
            <!-- Total Saldo Masuk -->
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-white/[0.03]">
              <div class="flex items-center justify-between">
                <div>
                  <p class="mb-1 text-sm font-medium text-gray-600 dark:text-gray-400">
                    Total Saldo Masuk
                  </p>
                  <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                    {{ formatCurrency(balanceData.totalMasuk) }}
                  </p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-100 dark:bg-green-500/10">
                  <svg
                    class="h-6 w-6 text-green-600 dark:text-green-400"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M12 4v16m8-8H4"
                    />
                  </svg>
                </div>
              </div>
            </div>

            <!-- Total Saldo Keluar -->
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-white/[0.03]">
              <div class="flex items-center justify-between">
                <div>
                  <p class="mb-1 text-sm font-medium text-gray-600 dark:text-gray-400">
                    Total Saldo Keluar
                  </p>
                  <p class="text-2xl font-bold text-red-600 dark:text-red-400">
                    {{ formatCurrency(balanceData.totalKeluar) }}
                  </p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-red-100 dark:bg-red-500/10">
                  <svg
                    class="h-6 w-6 text-red-600 dark:text-red-400"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M20 12H4"
                    />
                  </svg>
                </div>
              </div>
            </div>
          </div>

          <!-- Progress Ring -->
          <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-white/[0.03]">
            <h3 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white/90">
              Persentase Pemasukan vs Pengeluaran
            </h3>
            <div class="flex flex-col items-center justify-center">
              <div class="relative w-full max-w-[300px]">
                <VueApexCharts
                  type="radialBar"
                  height="300"
                  :options="progressChartOptions"
                  :series="progressChartSeries"
                />
              </div>
              <div class="mt-4 flex gap-6 text-center">
                <div>
                  <p class="text-sm text-gray-600 dark:text-gray-400">Pemasukan</p>
                  <p class="text-lg font-semibold text-green-600 dark:text-green-400">
                    {{ balanceData.persentaseMasuk }}%
                  </p>
                </div>
                <div>
                  <p class="text-sm text-gray-600 dark:text-gray-400">Pengeluaran</p>
                  <p class="text-lg font-semibold text-red-600 dark:text-red-400">
                    {{ balanceData.persentaseKeluar }}%
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Tab: Management -->
        <div
          v-if="activeTab === 'management'"
          role="tabpanel"
          id="tabpanel-management"
          aria-labelledby="tab-management"
        >
          <!-- Filter Section -->
          <div class="mb-6 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="mb-4 flex items-center justify-between">
              <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                Filter & Export
              </h3>
              <button
                @click="handleExportExcelManagement"
                class="flex items-center gap-2 rounded-lg bg-green-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-green-600"
              >
                <svg
                  class="fill-current"
                  width="20"
                  height="20"
                  viewBox="0 0 20 20"
                  fill="none"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <path
                    d="M10.8333 3.33333V9.16667H16.6667L10.8333 3.33333ZM4.16667 2.5H11.6667L17.5 8.33333V15.8333C17.5 16.2754 17.3244 16.6993 17.0118 17.0118C16.6993 17.3244 16.2754 17.5 15.8333 17.5H4.16667C3.72464 17.5 3.30072 17.3244 2.98816 17.0118C2.67559 16.6993 2.5 16.2754 2.5 15.8333V4.16667C2.5 3.72464 2.67559 3.30072 2.98816 2.98816C3.30072 2.67559 3.72464 2.5 4.16667 2.5Z"
                    fill="currentColor"
                  />
                </svg>
                Export Excel
              </button>
            </div>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
              <div class="flex-1">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                  Tanggal
                </label>
                <div class="relative">
                  <flat-pickr
                    v-model="filterManagementTanggal"
                    :config="flatpickrDateConfig"
                    class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pl-4 pr-11 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                    placeholder="Pilih tanggal"
                  />
                  <span
                    class="absolute text-gray-500 -translate-y-1/2 pointer-events-none right-3 top-1/2 dark:text-gray-400"
                  >
                    <svg
                      class="fill-current"
                      width="20"
                      height="20"
                      viewBox="0 0 20 20"
                      fill="none"
                      xmlns="http://www.w3.org/2000/svg"
                    >
                      <path
                        d="M10 18.3333C14.6024 18.3333 18.3333 14.6024 18.3333 9.99999C18.3333 5.39762 14.6024 1.66666 10 1.66666C5.39763 1.66666 1.66667 5.39762 1.66667 9.99999C1.66667 14.6024 5.39763 18.3333 10 18.3333Z"
                        stroke="currentColor"
                        stroke-width="1.5"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                      />
                      <path
                        d="M10 5V10L13.3333 11.6667"
                        stroke="currentColor"
                        stroke-width="1.5"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                      />
                    </svg>
                  </span>
                </div>
              </div>
              <div class="flex-1">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                  Keterangan
                </label>
                <input
                  type="text"
                  v-model="filterManagementKeterangan"
                  placeholder="Cari keterangan..."
                  class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                />
              </div>
              <div class="flex-1">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                  Masuk Min
                </label>
                <input
                  type="number"
                  v-model="filterManagementMasukMin"
                  placeholder="Masuk minimum..."
                  class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                />
              </div>
              <div class="flex-1">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                  Keluar Min
                </label>
                <input
                  type="number"
                  v-model="filterManagementKeluarMin"
                  placeholder="Keluar minimum..."
                  class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                />
              </div>
            </div>
            <div class="mt-4 flex justify-end">
              <button
                @click="resetFilterManagement"
                class="h-11 rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]"
              >
                Reset Filter
              </button>
            </div>
          </div>

          <div class="ag-theme-alpine dark:ag-theme-alpine-dark" style="width: 100%;">
            <ag-grid-vue
              class="ag-theme-alpine"
              style="width: 100%;"
              :columnDefs="managementColumnDefs"
              :rowData="filteredManagementData"
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

        <!-- Tab: Mitra -->
        <div
          v-if="activeTab === 'mitra'"
          role="tabpanel"
          id="tabpanel-mitra"
          aria-labelledby="tab-mitra"
        >
          <!-- Search Section -->
          <div class="mb-6 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex gap-4">
              <div class="flex-1">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                  Cari Mitra
                </label>
                <input
                  type="text"
                  v-model="searchMitra"
                  placeholder="Cari nama mitra atau program..."
                  class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                />
              </div>
            </div>
          </div>

          <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div
              v-for="mitra in paginatedMitraData"
              :key="mitra.id"
              class="group cursor-pointer rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition-all duration-300 hover:border-brand-300 hover:shadow-lg dark:border-gray-700 dark:bg-white/[0.03] dark:hover:border-brand-500"
            >
              <div class="mb-4 flex items-center justify-between">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-brand-500 text-white transition-all duration-300 group-hover:scale-110 group-hover:bg-brand-500 group-hover:text-white dark:bg-brand-500/10 dark:text-white/90">
                  <svg
                    class="h-6 w-6"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                    />
                  </svg>
                </div>
                <button
                  @click="handleMitraDetail(mitra.id)"
                  class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 hover:text-brand-500 dark:hover:bg-white/[0.03] dark:hover:text-brand-400"
                >
                  <svg
                    class="h-5 w-5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M9 5l7 7-7 7"
                    />
                  </svg>
                </button>
              </div>
              <h3 class="mb-2 text-lg font-semibold text-gray-800 dark:text-white/90">
                {{ mitra.nama }}
              </h3>
              <p class="mb-3 text-sm text-gray-600 dark:text-gray-400">
                {{ mitra.program }}
              </p>
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-xs text-gray-500 dark:text-gray-500">Nominal</p>
                  <p class="text-base font-semibold text-gray-800 dark:text-white/90">
                    {{ formatCurrency(mitra.nominal) }}
                  </p>
                </div>
                <span
                  class="rounded-full px-3 py-1 text-xs font-medium"
                  :class="
                    mitra.status === 'Aktif'
                      ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400'
                      : 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400'
                  "
                >
                  {{ mitra.status }}
                </span>
              </div>
            </div>
          </div>

          <!-- Empty State -->
          <div
            v-if="filteredMitraData.length === 0"
            class="flex flex-col items-center justify-center py-12 text-center"
          >
            <svg
              class="mb-4 h-16 w-16 text-gray-400 dark:text-gray-600"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
              />
            </svg>
            <p class="text-lg font-medium text-gray-600 dark:text-gray-400">
              {{ searchMitra ? 'Tidak ada data mitra ditemukan' : 'Tidak ada data mitra' }}
            </p>
            <p v-if="searchMitra" class="mt-2 text-sm text-gray-500 dark:text-gray-500">
              Coba ubah kata kunci pencarian Anda
            </p>
          </div>

          <!-- Pagination -->
          <div
            v-if="filteredMitraData.length > 0"
            class="mt-6 flex items-center justify-between"
          >
            <div class="text-sm text-gray-600 dark:text-gray-400">
              Menampilkan {{ startIndex + 1 }} - {{ endIndex }} dari {{ filteredMitraData.length }} mitra
            </div>
            <div class="flex gap-2">
              <button
                @click="currentMitraPage = Math.max(1, currentMitraPage - 1)"
                :disabled="currentMitraPage === 1"
                class="flex h-10 items-center justify-center rounded-lg border border-gray-300 bg-white px-4 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]"
              >
                Sebelumnya
              </button>
              <div class="flex gap-1">
                <button
                  v-for="page in totalMitraPages"
                  :key="page"
                  @click="currentMitraPage = page"
                  class="flex h-10 w-10 items-center justify-center rounded-lg border text-sm font-medium transition-colors"
                  :class="
                    currentMitraPage === page
                      ? 'border-brand-500 bg-brand-500 text-white'
                      : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]'
                  "
                >
                  {{ page }}
                </button>
              </div>
              <button
                @click="currentMitraPage = Math.min(totalMitraPages, currentMitraPage + 1)"
                :disabled="currentMitraPage === totalMitraPages"
                class="flex h-10 items-center justify-center rounded-lg border border-gray-300 bg-white px-4 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]"
              >
                Selanjutnya
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { AgGridVue } from 'ag-grid-vue3'
import VueApexCharts from 'vue3-apexcharts'
import flatPickr from 'vue-flatpickr-component'
import * as XLSX from 'xlsx'
import 'flatpickr/dist/flatpickr.css'
import 'ag-grid-community/styles/ag-grid.css'
import 'ag-grid-community/styles/ag-theme-alpine.css'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'


const route = useRoute()
const router = useRouter()
const currentPageTitle = computed(() => (route.meta.title as string) || 'Laporan Keuangan')

// Icon components
const BalanceIcon = {
  template: `
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5z" />
    </svg>
  `
}

const ManagementIcon = {
  template: `
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
    </svg>
  `
}

const MitraIcon = {
  template: `
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
    </svg>
  `
}

// Tab definitions
const tabs = [
  {
    id: 'balance',
    label: 'Balance',
    icon: BalanceIcon,
  },
  {
    id: 'management',
    label: 'Management',
    icon: ManagementIcon,
  },
  {
    id: 'mitra',
    label: 'Mitra',
    icon: MitraIcon,
  },
]

// Active tab - check query param for tab
const validTabs = ['balance', 'management', 'mitra']
const initialTab = route.query.tab as string
const activeTab = ref(validTabs.includes(initialTab) ? initialTab : 'balance')

// Watch route query to update tab
watch(() => route.query.tab, (newTab) => {
  if (newTab && validTabs.includes(newTab as string)) {
    activeTab.value = newTab as string
  }
})

// Filter tanggal (hidden)
const filterTanggal = ref('')

// Flatpickr configuration for date
const flatpickrDateConfig = {
  dateFormat: 'Y-m-d',
  altInput: true,
  altFormat: 'd/m/Y',
  wrap: false,
}

// Management Tab Filters
const filterManagementTanggal = ref('')
const filterManagementKeterangan = ref('')
const filterManagementMasukMin = ref('')
const filterManagementKeluarMin = ref('')

// Mitra Tab Search & Pagination
const searchMitra = ref('')
const currentMitraPage = ref(1)
const mitraPerPage = 6

// Balance Data
const balanceData = ref({
  saldo: 250000000,
  totalMasuk: 500000000,
  totalKeluar: 250000000,
  persentaseMasuk: 66.67,
  persentaseKeluar: 33.33,
})

// Progress Chart Options
const progressChartOptions = computed(() => ({
  chart: {
    fontFamily: 'Outfit, sans-serif',
    type: 'radialBar',
  },
  colors: ['#10b981', '#ef4444'],
  plotOptions: {
    radialBar: {
      startAngle: -90,
      endAngle: 90,
      hollow: {
        size: '70%',
      },
      track: {
        background: '#E4E7EC',
        strokeWidth: '100%',
        margin: 5,
      },
      dataLabels: {
        name: {
          show: false,
        },
        value: {
          fontSize: '24px',
          fontWeight: '600',
          offsetY: 0,
          color: '#1D2939',
          formatter: function (val: number) {
            return val.toFixed(1) + '%'
          },
        },
      },
    },
  },
  fill: {
    type: 'solid',
  },
  stroke: {
    lineCap: 'round',
  },
  labels: ['Pemasukan'],
}))

const progressChartSeries = computed(() => [balanceData.value.persentaseMasuk])

// Format currency helper
const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
  }).format(value)
}

// Management Tab - Column Definitions
const managementColumnDefs = [
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
    headerName: 'Keterangan',
    field: 'keterangan',
    sortable: true,
    flex: 2,
  },
  {
    headerName: 'Masuk',
    field: 'masuk',
    sortable: true,
    flex: 1,
    valueFormatter: (params: any) => {
      if (params.value && params.value > 0) {
        return formatCurrency(params.value)
      }
      return '-'
    },
    cellStyle: { color: '#10b981', fontWeight: '500' },
  },
  {
    headerName: 'Keluar',
    field: 'keluar',
    sortable: true,
    flex: 1,
    valueFormatter: (params: any) => {
      if (params.value && params.value > 0) {
        return formatCurrency(params.value)
      }
      return '-'
    },
    cellStyle: { color: '#ef4444', fontWeight: '500' },
  },
  {
    headerName: 'Saldo',
    field: 'saldo',
    sortable: true,
    flex: 1,
    valueFormatter: (params: any) => {
      if (params.value) {
        return formatCurrency(params.value)
      }
      return '-'
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
      
      const detailBtn = document.createElement('button')
      detailBtn.className = 'flex items-center justify-center w-8 h-8 rounded-lg text-brand-500 hover:bg-brand-50 dark:hover:bg-brand-500/10 transition-colors'
      detailBtn.innerHTML = `
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
          <circle cx="12" cy="12" r="3"></circle>
        </svg>
      `
      detailBtn.onclick = () => handleDetail(params.data.id)
      
      const downloadBtn = document.createElement('button')
      downloadBtn.className = 'flex items-center justify-center w-8 h-8 rounded-lg text-gray-500 hover:bg-gray-50 dark:hover:bg-white/[0.03] dark:hover:text-gray-400 transition-colors'
      downloadBtn.innerHTML = `
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
          <polyline points="7 10 12 15 17 10"></polyline>
          <line x1="12" y1="15" x2="12" y2="3"></line>
        </svg>
      `
      downloadBtn.onclick = () => handleDownload(params.data.id)
      
      div.appendChild(detailBtn)
      div.appendChild(downloadBtn)
      
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

// Management Tab - Filtered Data
const filteredManagementData = computed(() => {
  let filtered = [...managementRowData]
  
  // Filter by Tanggal
  if (filterManagementTanggal.value) {
    filtered = filtered.filter((item) => {
      const itemDate = new Date(item.tanggal)
      const filterDate = new Date(filterManagementTanggal.value)
      
      return (
        itemDate.getFullYear() === filterDate.getFullYear() &&
        itemDate.getMonth() === filterDate.getMonth() &&
        itemDate.getDate() === filterDate.getDate()
      )
    })
  }
  
  // Filter by Keterangan
  if (filterManagementKeterangan.value) {
    const searchTerm = filterManagementKeterangan.value.toLowerCase()
    filtered = filtered.filter((item) =>
      item.keterangan.toLowerCase().includes(searchTerm)
    )
  }
  
  // Filter by Masuk Min
  if (filterManagementMasukMin.value) {
    const minAmount = parseFloat(filterManagementMasukMin.value)
    if (!isNaN(minAmount)) {
      filtered = filtered.filter((item) => item.masuk >= minAmount)
    }
  }
  
  // Filter by Keluar Min
  if (filterManagementKeluarMin.value) {
    const minAmount = parseFloat(filterManagementKeluarMin.value)
    if (!isNaN(minAmount)) {
      filtered = filtered.filter((item) => item.keluar >= minAmount)
    }
  }
  
  return filtered
})

// Management Tab - Sample Data
const managementRowData = [
  {
    id: '1',
    tanggal: '2024-01-15',
    keterangan: 'Donasi dari PT ABC',
    masuk: 50000000,
    keluar: 0,
    saldo: 50000000,
  },
  {
    id: '2',
    tanggal: '2024-01-16',
    keterangan: 'Pembayaran gaji karyawan',
    masuk: 0,
    keluar: 25000000,
    saldo: 25000000,
  },
  {
    id: '3',
    tanggal: '2024-01-17',
    keterangan: 'Donasi online',
    masuk: 10000000,
    keluar: 0,
    saldo: 35000000,
  },
  {
    id: '4',
    tanggal: '2024-01-18',
    keterangan: 'Biaya operasional',
    masuk: 0,
    keluar: 5000000,
    saldo: 30000000,
  },
  {
    id: '5',
    tanggal: '2024-01-19',
    keterangan: 'Zakat dari donatur',
    masuk: 75000000,
    keluar: 0,
    saldo: 105000000,
  },
  {
    id: '6',
    tanggal: '2024-01-20',
    keterangan: 'Penyaluran ke program pendidikan',
    masuk: 0,
    keluar: 30000000,
    saldo: 75000000,
  },
  {
    id: '7',
    tanggal: '2024-01-21',
    keterangan: 'Donasi dari Yayasan XYZ',
    masuk: 100000000,
    keluar: 0,
    saldo: 175000000,
  },
  {
    id: '8',
    tanggal: '2024-01-22',
    keterangan: 'Biaya administrasi',
    masuk: 0,
    keluar: 2000000,
    saldo: 173000000,
  },
]

// Mitra Data - Filtered
const filteredMitraData = computed(() => {
  if (!searchMitra.value) {
    return mitraData.value
  }
  const searchTerm = searchMitra.value.toLowerCase()
  return mitraData.value.filter((mitra) =>
    mitra.nama.toLowerCase().includes(searchTerm) ||
    mitra.program.toLowerCase().includes(searchTerm)
  )
})

// Mitra Pagination
const totalMitraPages = computed(() => {
  return Math.ceil(filteredMitraData.value.length / mitraPerPage)
})

const startIndex = computed(() => {
  return (currentMitraPage.value - 1) * mitraPerPage
})

const endIndex = computed(() => {
  return Math.min(startIndex.value + mitraPerPage, filteredMitraData.value.length)
})

const paginatedMitraData = computed(() => {
  return filteredMitraData.value.slice(startIndex.value, endIndex.value)
})

// Watch search to reset page
watch(searchMitra, () => {
  currentMitraPage.value = 1
})

// Mitra Data
const mitraData = ref([
  {
    id: '1',
    nama: 'PT ABC Foundation',
    program: 'Program Pendidikan Anak',
    nominal: 50000000,
    status: 'Aktif',
  },
  {
    id: '2',
    nama: 'Yayasan XYZ',
    program: 'Program Pemberdayaan Masyarakat',
    nominal: 75000000,
    status: 'Aktif',
  },
  {
    id: '3',
    nama: 'CV Sejahtera',
    program: 'Program Kesehatan',
    nominal: 30000000,
    status: 'Aktif',
  },
  {
    id: '4',
    nama: 'PT Harmoni',
    program: 'Program Bantuan Sosial',
    nominal: 100000000,
    status: 'Aktif',
  },
  {
    id: '5',
    nama: 'Yayasan Peduli',
    program: 'Program Lingkungan',
    nominal: 25000000,
    status: 'Aktif',
  },
  {
    id: '6',
    nama: 'PT Berkah',
    program: 'Program Kemanusiaan',
    nominal: 60000000,
    status: 'Aktif',
  },
  {
    id: '7',
    nama: 'PT Cinta Indonesia',
    program: 'Program Beasiswa',
    nominal: 80000000,
    status: 'Aktif',
  },
  {
    id: '8',
    nama: 'Yayasan Kasih Ibu',
    program: 'Program Kesehatan Ibu dan Anak',
    nominal: 45000000,
    status: 'Aktif',
  },
  {
    id: '9',
    nama: 'CV Maju Bersama',
    program: 'Program Pemberdayaan Ekonomi',
    nominal: 55000000,
    status: 'Aktif',
  },
  {
    id: '10',
    nama: 'PT Harapan Bangsa',
    program: 'Program Infrastruktur',
    nominal: 120000000,
    status: 'Aktif',
  },
  {
    id: '11',
    nama: 'Yayasan Bantu Sesama',
    program: 'Program Bantuan Pangan',
    nominal: 35000000,
    status: 'Aktif',
  },
  {
    id: '12',
    nama: 'PT Gotong Royong',
    program: 'Program Air Bersih',
    nominal: 70000000,
    status: 'Aktif',
  },
  {
    id: '13',
    nama: 'CV Jaya Abadi',
    program: 'Program Teknologi',
    nominal: 90000000,
    status: 'Aktif',
  },
  {
    id: '14',
    nama: 'Yayasan Damai Sentosa',
    program: 'Program Perdamaian',
    nominal: 40000000,
    status: 'Aktif',
  },
  {
    id: '15',
    nama: 'PT Harmoni Sejahtera',
    program: 'Program Sosial Budaya',
    nominal: 65000000,
    status: 'Aktif',
  },
])

// Handlers
const handleDetail = (id: string) => {
  console.log('Detail transaksi:', id)
  alert(`Detail transaksi dengan ID: ${id}`)
}

const handleDownload = (id: string) => {
  console.log('Download transaksi:', id)
  alert(`Download transaksi dengan ID: ${id}`)
}

const handleMitraDetail = (id: string) => {
  router.push(`/laporan/detail-mitra/${id}`)
}

// Management Tab - Reset Filter
const resetFilterManagement = () => {
  filterManagementTanggal.value = ''
  filterManagementKeterangan.value = ''
  filterManagementMasukMin.value = ''
  filterManagementKeluarMin.value = ''
}

// Management Tab - Export Excel
const handleExportExcelManagement = () => {
  const dataToExport = filteredManagementData.value.map((item) => {
    const tanggal = new Date(item.tanggal)
    
    return {
      'Tanggal': tanggal.toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
      }),
      'Keterangan': item.keterangan,
      'Masuk': item.masuk > 0 ? formatCurrency(item.masuk) : '-',
      'Keluar': item.keluar > 0 ? formatCurrency(item.keluar) : '-',
      'Saldo': formatCurrency(item.saldo),
    }
  })
  
  const worksheet = XLSX.utils.json_to_sheet(dataToExport)
  const workbook = XLSX.utils.book_new()
  XLSX.utils.book_append_sheet(workbook, worksheet, 'Laporan Keuangan Management')
  
  const now = new Date()
  const filename = `Laporan_Keuangan_Management_${now.toISOString().split('T')[0]}.xlsx`
  
  XLSX.writeFile(workbook, filename)
}
</script>

<style scoped>
/* Smooth transitions for tab switching */
[role="tabpanel"] {
  animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Card hover effect */
.group:hover {
  transform: translateY(-4px);
}
</style>

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

