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
          <!-- Filter Tanggal (Rentang) - cleaner responsive layout -->
          <div class="mb-6">
            <div class="grid grid-cols-1 gap-3 md:grid-cols-12">
              <!-- Range picker spans larger area on md+ -->
              <div class="md:col-span-4">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Rentang Tanggal</label>
                <flat-pickr
                  v-model="balanceRange"
                  :config="rangePickrConfig"
                  class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300"
                  placeholder="Pilih rentang tanggal"
                />
              </div>

              <!-- Program select (wider) -->
              <div class="md:col-span-3">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Program</label>
                <SearchableSelect
                  v-model="selectedProgram"
                  :options="programOptions"
                  placeholder="Semua Program"
                  :search-input="programSearchInput"
                  @update:search-input="programSearchInput = $event"
                />
              </div>

              <!-- Kantor Cabang select (wider) -->
              <div class="md:col-span-3">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Kantor Cabang</label>
                <SearchableSelect
                  v-model="selectedKantor"
                  :options="kantorOptions"
                  placeholder="Semua Kantor Cabang"
                  :search-input="kantorSearchInput"
                  @update:search-input="kantorSearchInput = $event"
                />
              </div>

              <!-- Buttons aligned to right and bottom -->
              <div class="md:col-span-2 flex items-end justify-end">
                <div class="flex gap-2">
                  <button
                    @click="resetBalanceFilter"
                    class="h-11 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50"
                  >
                    Reset
                  </button>
                </div>
              </div>
            </div>


          </div>



          <!-- Saldo Card -->
          <div class="mb-6 rounded-lg border border-gray-200 bg-gradient-to-br from-brand-500 to-brand-600 p-6 shadow-lg dark:border-gray-700 dark:from-brand-600 dark:to-brand-700">
            <div class="text-center">
              <p class="mb-2 text-sm font-medium text-white/80">Total Saldo</p>
              <h2 class="text-4xl font-bold text-white sm:text-5xl">
                {{ formatCurrency(balanceTotals.saldo_akhir || 0) }}
              </h2>
              <p class="mt-2 text-sm text-white/80">Saldo awal: {{ formatCurrency(balanceTotals.saldo_awal || 0) }}</p>
            </div>
          </div>

          <!-- Accordion (moved to appear ABOVE the stats) -->
          <div v-if="accordionOpen" ref="accordionRef" class="mb-6 rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-white/[0.03]">
            <div class="mb-4 flex items-center justify-between">
              <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Transaksi ({{ accordionFilterLabel }})</h3>
              <div class="flex items-center gap-2">
                <button @click="handleExportDisplayed" class="h-10 rounded-lg bg-green-500 px-4 py-2 text-sm font-medium text-white hover:bg-green-600">Export Excel</button>
                <button @click="accordionOpen = false" class="h-10 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm">Tutup</button>
              </div>
            </div>

            <div class="overflow-x-auto">
              <table class="w-full table-auto">
                <thead>
                  <tr class="text-sm font-semibold text-left text-gray-600">
                    <th class="px-4 py-2">Tanggal</th>
                    <th class="px-4 py-2">Keterangan</th>
                    <th class="px-4 py-2 text-right">Masuk</th>
                    <th class="px-4 py-2 text-right">Keluar</th>
                    <th class="px-4 py-2 text-right">Saldo</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="tx in displayedTransactions" :key="tx.id" class="border-t">
                    <td class="px-4 py-3 text-sm text-gray-700">{{ tx.tanggal }}</td>
                    <td class="px-4 py-3 text-sm text-gray-700">{{ tx.keterangan }}</td>
                    <td class="px-4 py-3 text-sm text-right text-green-600">{{ tx.masuk > 0 ? formatCurrency(tx.masuk) : '-' }}</td>
                    <td class="px-4 py-3 text-sm text-right text-red-600">{{ tx.keluar > 0 ? formatCurrency(tx.keluar) : '-' }}</td>
                    <td class="px-4 py-3 text-sm text-right">{{ formatCurrency(tx.saldo) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div class="mt-4 flex items-center justify-between">
              <div class="text-sm text-gray-600">Menampilkan halaman {{ balancePagination.current_page }} dari {{ balancePagination.last_page }} — total {{ balancePagination.total }} transaksi</div>
              <div class="flex gap-2">
                <button :disabled="balancePagination.current_page <= 1" @click="( () => { balancePagination.current_page = Math.max(1, balancePagination.current_page - 1); fetchBalanceData(balancePagination.current_page); } )()" class="h-10 rounded-lg border px-3 bg-white">Sebelumnya</button>
                <button :disabled="balancePagination.current_page >= balancePagination.last_page" @click="( () => { balancePagination.current_page = Math.min(balancePagination.last_page, balancePagination.current_page + 1); fetchBalanceData(balancePagination.current_page); } )()" class="h-10 rounded-lg border px-3 bg-white">Selanjutnya</button>
              </div>
            </div>
          </div>

          <!-- Stats Grid -->
          <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
            <!-- Total Saldo Masuk (clickable to expand transactions) -->
            <button @click.prevent="toggleAccordion('masuk')" class="w-full text-left rounded-lg border border-gray-200 bg-white p-6 shadow-sm hover:border-brand-300 focus:outline-none dark:border-gray-700 dark:bg-white/[0.03]">
              <div class="flex items-center justify-between">
                <div>
                  <p class="mb-1 text-sm font-medium text-gray-600 dark:text-gray-400">Total Saldo Masuk</p>
                  <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ formatCurrency(balanceTotals.totalMasuk || 0) }}</p>
                </div>
                <div class="flex items-center gap-3">
                  <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-100 dark:bg-green-500/10">
                    <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                  </div>
                  <svg :class="['h-5 w-5 transition-transform', accordionOpen && accordionFilter === 'masuk' ? 'rotate-180' : 'rotate-0']" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                  </svg>
                </div>
              </div>
            </button>

            <!-- Total Saldo Keluar (clickable) -->
            <button @click.prevent="toggleAccordion('keluar')" class="w-full text-left rounded-lg border border-gray-200 bg-white p-6 shadow-sm hover:border-brand-300 focus:outline-none dark:border-gray-700 dark:bg-white/[0.03]">
              <div class="flex items-center justify-between">
                <div>
                  <p class="mb-1 text-sm font-medium text-gray-600 dark:text-gray-400">Total Saldo Keluar</p>
                  <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ formatCurrency(balanceTotals.totalKeluar || 0) }}</p>
                </div>
                <div class="flex items-center gap-3">
                  <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-red-100 dark:bg-red-500/10">
                    <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                    </svg>
                  </div>
                  <svg :class="['h-5 w-5 transition-transform', accordionOpen && accordionFilter === 'keluar' ? 'rotate-180' : 'rotate-0']" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                  </svg>
                </div>
              </div>
            </button>
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
                    {{ persentaseMasukFormatted }}%
                  </p>
                </div>
                <div>
                  <p class="text-sm text-gray-600 dark:text-gray-400">Pengeluaran</p>
                  <p class="text-lg font-semibold text-red-600 dark:text-red-400">
                    {{ persentaseKeluarFormatted }}%
                  </p>
                </div>
              </div>
            </div>
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
              v-for="mitra in mitraList"
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
                  @click="openMitraDetail(mitra.id)"
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

          <!-- Mitra Detail Modal -->
          <div v-if="showMitraModal" class="fixed inset-0 z-50 flex items-start justify-center p-6">
            <div class="absolute inset-0 bg-black/40" @click="closeMitraModal"></div>
            <div class="relative z-60 w-full max-w-4xl rounded-lg bg-white p-6 shadow-lg dark:bg-gray-900">
              <div class="flex items-center justify-between mb-4">
                <div>
                  <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Detail Mitra - {{ mitraDetail.nama }}</h3>
                  <p class="text-sm text-gray-500">Total transaksi: {{ mitraDetail.transaksi_count }} — Total nilai: {{ formatCurrency(mitraDetail.transaksi_total) }}</p>
                </div>
                <div class="flex items-center gap-2">
                  <button @click="handleExportMitraTransactions" class="h-10 rounded-lg bg-green-500 px-4 py-2 text-sm font-medium text-white hover:bg-green-600">Export Excel</button>
                  <button @click="closeMitraModal" class="h-10 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm">Tutup</button>
                </div>
              </div>

              <div class="overflow-x-auto">
                <table class="w-full table-auto">
                  <thead>
                    <tr class="text-sm font-semibold text-left text-gray-600">
                      <th class="px-4 py-2">Tanggal</th>
                      <th class="px-4 py-2">Keterangan</th>
                      <th class="px-4 py-2">Donatur</th>
                      <th class="px-4 py-2">Program</th>
                      <th class="px-4 py-2 text-right">Nominal</th>
                      <th class="px-4 py-2">Kantor</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-if="mitraTxLoading">
                      <td colspan="6" class="px-4 py-6 text-center">Loading...</td>
                    </tr>
                    <tr v-for="tx in mitraTransactions" :key="tx.id" class="border-t">
                      <td class="px-4 py-3 text-sm text-gray-700">{{ tx.tanggal }}</td>
                      <td class="px-4 py-3 text-sm text-gray-700">{{ tx.keterangan }}</td>
                      <td class="px-4 py-3 text-sm text-gray-700">{{ tx.donatur }}</td>
                      <td class="px-4 py-3 text-sm text-gray-700">{{ tx.program }}</td>
                      <td class="px-4 py-3 text-sm text-right">{{ formatCurrency(tx.nominal) }}</td>
                      <td class="px-4 py-3 text-sm text-gray-700">{{ tx.kantor }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <div class="mt-4 flex items-center justify-between">
                <div class="text-sm text-gray-600">Halaman {{ mitraTxPagination.current_page }} dari {{ mitraTxPagination.last_page }} — total {{ mitraTxPagination.total }} transaksi</div>
                <div class="flex gap-2">
                  <button :disabled="mitraTxPagination.current_page <= 1" @click="( () => { mitraTxPagination.current_page = Math.max(1, mitraTxPagination.current_page - 1); openMitraDetail(mitraDetail.id, mitraTxPagination.current_page); } )()" class="h-10 rounded-lg border px-3 bg-white">Sebelumnya</button>
                  <button :disabled="mitraTxPagination.current_page >= mitraTxPagination.last_page" @click="( () => { mitraTxPagination.current_page = Math.min(mitraTxPagination.last_page, mitraTxPagination.current_page + 1); openMitraDetail(mitraDetail.id, mitraTxPagination.current_page); } )()" class="h-10 rounded-lg border px-3 bg-white">Selanjutnya</button>
                </div>
              </div>
            </div>
          </div>

          <!-- Empty State -->
          <div
            v-if="mitraList.length === 0"
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
              Menampilkan {{ startIndex + 1 }} - {{ endIndex }} dari {{ mitraPagination.total }} mitra
            </div>
            <div class="flex gap-2">
              <button
                @click="( () => { mitraPagination.current_page = Math.max(1, mitraPagination.current_page - 1); fetchMitraList(mitraPagination.current_page); } )()"
                :disabled="mitraPagination.current_page === 1"
                class="flex h-10 items-center justify-center rounded-lg border border-gray-300 bg-white px-4 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]"
              >
                Sebelumnya
              </button>
              <div class="flex gap-1">
                <button
                  v-for="page in totalMitraPages"
                  :key="page"
                  @click="( () => { mitraPagination.current_page = page; fetchMitraList(page); } )()"
                  class="flex h-10 w-10 items-center justify-center rounded-lg border text-sm font-medium transition-colors"
                  :class="
                    mitraPagination.current_page === page
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
import { ref, computed, watch, onMounted, nextTick } from 'vue'
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
import SearchableSelect from '@/components/forms/SearchableSelect.vue'


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
    id: 'mitra',
    label: 'Mitra',
    icon: MitraIcon,
  },
]

// Active tab - check query param for tab
const validTabs = ['balance', 'mitra']
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

// Balance filters & state
const balanceStart = ref(new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().split('T')[0])
const balanceEnd = ref(new Date().toISOString().split('T')[0])
const showBalanceTransactions = ref(false)

const balanceTotals = ref({
  saldo_awal: 0,
  totalMasuk: 0,
  totalKeluar: 0,
  saldo_akhir: 0,
})

const balanceTransactions = ref([])
const balancePagination = ref({ current_page: 1, last_page: 1, per_page: 20, total: 0 })

// Range picker config and model
const rangePickrConfig = {
  mode: 'range',
  dateFormat: 'Y-m-d',
  altInput: true,
  altFormat: 'd/m/Y',
  wrap: false,
}

const balanceRange = ref([balanceStart.value, balanceEnd.value])

// Debounced auto-apply on filter changes
let balanceFilterTimeout: any = null
const scheduleFetch = (delay = 400) => {
  if (balanceFilterTimeout) clearTimeout(balanceFilterTimeout)
  balanceFilterTimeout = setTimeout(() => {
    balancePagination.value.current_page = 1
    fetchBalanceData(1)
  }, delay)
}

watch(balanceRange, (v) => {
  if (Array.isArray(v)) {
    balanceStart.value = v[0] || balanceStart.value
    balanceEnd.value = v[1] || balanceEnd.value
  } else if (typeof v === 'string') {
    const parts = v.split(' to ')
    balanceStart.value = parts[0] || balanceStart.value
    balanceEnd.value = parts[1] || balanceEnd.value
  }
  scheduleFetch()
})



const programs = ref([])
const kantorCabangs = ref([])
const selectedProgram = ref('')
const selectedKantor = ref('')

const programSearchInput = ref('')
const kantorSearchInput = ref('')

// Options with explicit "Semua" entries
const programOptions = computed(() => {
  const base = programs.value.map(p => ({ value: String(p.id), label: p.nama_program || p.name || p.nama }))
  return [{ value: '', label: 'Semua Program' }, ...base]
})

const kantorOptions = computed(() => {
  const base = kantorCabangs.value.map(k => ({ value: String(k.id), label: k.nama || k.name }))
  return [{ value: '', label: 'Semua Kantor Cabang' }, ...base]
})

// Apply fetch when program/kantor filters change
watch([selectedProgram, selectedKantor], () => {
  scheduleFetch()
})

// Accordion state
const accordionOpen = ref(false)
const accordionFilter = ref('all')
const accordionFilterLabel = computed(() => {
  if (accordionFilter.value === 'masuk') return 'Pemasukan'
  if (accordionFilter.value === 'keluar') return 'Pengeluaran'
  return 'Semua'
})

const displayedTransactions = computed(() => {
  if (!balanceTransactions.value) return []
  if (accordionFilter.value === 'masuk') return balanceTransactions.value.filter(t => t.masuk > 0)
  if (accordionFilter.value === 'keluar') return balanceTransactions.value.filter(t => t.keluar > 0)
  return balanceTransactions.value
})

const accordionRef = ref<HTMLElement | null>(null)

const toggleAccordion = async (filter = 'all') => {
  console.log('toggleAccordion called', { filter, accordionOpen: accordionOpen.value, accordionFilter: accordionFilter.value })
  if (accordionOpen.value && accordionFilter.value === filter) {
    accordionOpen.value = false
  } else {
    accordionFilter.value = filter
    accordionOpen.value = true
    // Ensure transactions are fresh when opening
    balancePagination.value.current_page = 1
    await fetchBalanceData(1)
    console.log('toggleAccordion: fetched transactions count', balanceTransactions.value.length)
    // Wait DOM update then scroll accordion into view so it's above the clicked stat
    await nextTick()
    try {
      accordionRef.value?.scrollIntoView({ behavior: 'smooth', block: 'start' })
    } catch (err) {
      // ignore
    }
  }
}

const handleExportDisplayed = () => {
  const r = displayedTransactions.value.map((b) => ({
    Tanggal: b.tanggal,
    Keterangan: b.keterangan,
    Masuk: b.masuk > 0 ? formatCurrency(b.masuk) : '-',
    Keluar: b.keluar > 0 ? formatCurrency(b.keluar) : '-',
    Saldo: formatCurrency(b.saldo),
  }))
  const ws = XLSX.utils.json_to_sheet(r)
  const wb = XLSX.utils.book_new()
  XLSX.utils.book_append_sheet(wb, ws, 'Laporan Keuangan')
  const filename = `Laporan_Keuangan_${balanceStart.value}_${balanceEnd.value}.xlsx`
  XLSX.writeFile(wb, filename)
}


// Progress Chart Options (same as before)
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

const persentaseMasuk = computed(() => {
  const masuk = balanceTotals.value.totalMasuk || 0
  const keluar = balanceTotals.value.totalKeluar || 0
  const denom = masuk + keluar
  return denom === 0 ? 0 : (masuk / denom) * 100
})

const persentaseMasukFormatted = computed(() => persentaseMasuk.value.toFixed(2))
const persentaseKeluarFormatted = computed(() => (100 - persentaseMasuk.value).toFixed(2))

const progressChartSeries = computed(() => [persentaseMasuk.value])

const fetchBalanceData = async (page = 1) => {
  try {
    console.log('fetchBalanceData start', { start: balanceStart.value, end: balanceEnd.value, program: selectedProgram.value, kantor: selectedKantor.value, page })
    balancePagination.value.current_page = page
    const params = new URLSearchParams()
    params.append('start', balanceStart.value)
    params.append('end', balanceEnd.value)
    params.append('page', String(page))
    params.append('per_page', String(balancePagination.value.per_page || 20))
    if (selectedProgram.value) params.append('program_id', selectedProgram.value)
    if (selectedKantor.value) params.append('kantor_cabang_id', selectedKantor.value)

    const res = await fetch(`/admin/api/laporan/keuangan?${params.toString()}`, {
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      },
      credentials: 'same-origin',
    })

    if (!res.ok) {
      console.error('Error fetching laporan keuangan', res.status)
      return
    }

    const json = await res.json()
    console.log('fetchBalanceData json', json)
    if (!json.success) {
      console.error('API returned error', json.message)
      return
    }

    const data = json.data
    balanceTotals.value = data.totals || { saldo_awal: 0, totalMasuk: 0, totalKeluar: 0, saldo_akhir: 0 }
    balanceTransactions.value = data.transactions || []
    balancePagination.value = { ...(data.pagination || {}), per_page: (data.pagination && data.pagination.per_page) || 20 }
  } catch (err) {
    console.error('Exception fetching laporan keuangan', err)
  }
}



const resetBalanceFilter = () => {
  const start = new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().split('T')[0]
  const end = new Date().toISOString().split('T')[0]
  balanceStart.value = start
  balanceEnd.value = end
  balanceRange.value = [start, end]
  selectedProgram.value = ''
  selectedKantor.value = ''
  // immediately fetch since filters changed
  balancePagination.value.current_page = 1
  fetchBalanceData(1)
}

const fetchPrograms = async () => {
  try {
    const res = await fetch('/admin/api/program?per_page=200', { headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' })
    if (!res.ok) return
    const json = await res.json()
    if (json.success) programs.value = json.data || json.data?.data || json.data?.items || []
  } catch (err) {
    console.error('Error fetching programs', err)
  }
}

const fetchKantorCabangs = async () => {
  try {
    const res = await fetch('/admin/api/kantor-cabang?per_page=200', { headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' })
    if (!res.ok) return
    const json = await res.json()
    if (json.success) kantorCabangs.value = json.data || json.data?.data || json.data?.items || []
  } catch (err) {
    console.error('Error fetching kantor cabang', err)
  }
}

watch(activeTab, (v) => {
  if (v === 'balance') {
    balanceRange.value = [balanceStart.value, balanceEnd.value]
    fetchBalanceData(1)
    fetchPrograms()
    fetchKantorCabangs()
  }
})

onMounted(() => {
  if (activeTab.value === 'balance') {
    balanceRange.value = [balanceStart.value, balanceEnd.value]
    fetchBalanceData(1)
    fetchPrograms()
    fetchKantorCabangs()
  }
})

const handleExportBalance = () => {
  const r = balanceTransactions.value.map((b) => ({
    Tanggal: b.tanggal,
    Keterangan: b.keterangan,
    Masuk: b.masuk > 0 ? formatCurrency(b.masuk) : '-',
    Keluar: b.keluar > 0 ? formatCurrency(b.keluar) : '-',
    Saldo: formatCurrency(b.saldo),
  }))
  const ws = XLSX.utils.json_to_sheet(r)
  const wb = XLSX.utils.book_new()
  XLSX.utils.book_append_sheet(wb, ws, 'Laporan Keuangan')
  const filename = `Laporan_Keuangan_${balanceStart.value}_${balanceEnd.value}.xlsx`
  XLSX.writeFile(wb, filename)
}

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
// Server-backed Mitra data & pagination
const mitraList = ref([])
const mitraPagination = ref({ current_page: 1, last_page: 1, per_page: 12, total: 0 })
const mitraLoading = ref(false)

// Detail modal state
const showMitraModal = ref(false)
const mitraDetail = ref({ id: '', nama: '', transaksi_count: 0, transaksi_total: 0 })
const mitraTransactions = ref([])
const mitraTxPagination = ref({ current_page: 1, last_page: 1, per_page: 20, total: 0 })
const mitraTxLoading = ref(false)

// Fetch mitra list from API
const fetchMitraList = async (page = 1) => {
  try {
    mitraLoading.value = true
    const params = new URLSearchParams()
    params.append('page', String(page))
    params.append('per_page', String(mitraPagination.value.per_page || 12))
    if (searchMitra.value) params.append('search', searchMitra.value)

    const res = await fetch(`/admin/api/laporan/mitra?${params.toString()}`, {
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin',
    })
    if (!res.ok) return
    const json = await res.json()
    if (!json.success) return

    mitraList.value = json.data || []
    mitraPagination.value = { ...(json.pagination || {}), per_page: mitraPagination.value.per_page }
  } catch (err) {
    console.error('Error fetching mitra list', err)
  } finally {
    mitraLoading.value = false
  }
}

// Debounce search
let mitraSearchTimeout: any = null
watch(searchMitra, () => {
  if (mitraSearchTimeout) clearTimeout(mitraSearchTimeout)
  mitraSearchTimeout = setTimeout(() => fetchMitraList(1), 400)
})

// Open mitra detail modal and fetch transactions
const openMitraDetail = async (id: string, page = 1) => {
  try {
    mitraTxLoading.value = true
    mitraDetail.value = { id, nama: '', transaksi_count: 0, transaksi_total: 0 }
    // try get name from current list
    const found = mitraList.value.find((m: any) => String(m.id) === String(id))
    if (found) mitraDetail.value.nama = found.nama

    mitraTxPagination.value.current_page = page
    const res = await fetch(`/admin/api/laporan/mitra/${id}/transaksi?page=${page}&per_page=${mitraTxPagination.value.per_page}`, {
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin',
    })
    if (!res.ok) return
    const json = await res.json()
    if (!json.success) return

    mitraTransactions.value = json.data || []
    mitraTxPagination.value = { ...(json.pagination || {}), per_page: mitraTxPagination.value.per_page }
    if (json.totals) {
      mitraDetail.value.transaksi_count = json.totals.count || 0
      mitraDetail.value.transaksi_total = json.totals.nominal || 0
    }

    showMitraModal.value = true
  } catch (err) {
    console.error('Error fetching mitra transactions', err)
  } finally {
    mitraTxLoading.value = false
  }
}

const closeMitraModal = () => {
  showMitraModal.value = false
  mitraTransactions.value = []
}

// Replace client-side pagination with server values
const totalMitraPages = computed(() => Math.ceil(mitraPagination.value.total / mitraPagination.value.per_page))
const startIndex = computed(() => (mitraPagination.value.current_page - 1) * mitraPagination.value.per_page)
const endIndex = computed(() => Math.min(startIndex.value + mitraPagination.value.per_page, mitraPagination.value.total))

// Fetch mitra list when tab active
watch(activeTab, (v) => {
  if (v === 'mitra') fetchMitraList(1)
})

onMounted(() => {
  if (activeTab.value === 'mitra') fetchMitraList(1)
})

// Handlers
const handleDetail = (id: string) => {
  alert(`Detail transaksi dengan ID: ${id}`)
}

const handleDownload = (id: string) => {
  alert(`Download transaksi dengan ID: ${id}`)
}

const handleMitraDetail = (id: string) => {
  // backward-compatible handler - open modal
  openMitraDetail(id)
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

