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
          <!-- 1. Top Filter Section -->
          <div class="mb-6">
            <div class="grid grid-cols-1 gap-3 md:grid-cols-12">
              <!-- Range picker -->
              <div class="md:col-span-4">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Rentang Tanggal</label>
                <flat-pickr
                  v-model="balanceRange"
                  :config="rangePickrConfig"
                  class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                  placeholder="Pilih rentang tanggal"
                />
              </div>

              <!-- Program select -->
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

              <!-- Kantor Cabang select -->
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

              <!-- Reset button -->
              <div class="md:col-span-2 flex items-end justify-end">
                <button
                  @click="resetBalanceFilter"
                  class="h-11 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-white/[0.03]"
                >
                  Reset
                </button>
              </div>
            </div>
          </div>

          <!-- 2. Main Stats Row - Pemasukan & Pengeluaran -->
          <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
            <!-- Total Pemasukan (Green gradient) -->
            <div class="rounded-lg border border-gray-200 bg-gradient-to-br from-green-500 to-green-600 p-6 shadow-lg dark:border-gray-700 dark:from-green-600 dark:to-green-700">
              <div class="flex items-center justify-between">
                <div>
                  <p class="mb-1 text-sm font-medium text-white/80">Total Pemasukan</p>
                  <h3 class="text-3xl font-bold text-white">{{ formatCurrency(balanceTotals.totalMasuk || 0) }}</h3>
                </div>
                <div class="flex h-14 w-14 items-center justify-center rounded-lg bg-white/20">
                  <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                  </svg>
                </div>
              </div>
            </div>

            <!-- Total Pengeluaran (Red gradient) -->
            <div class="rounded-lg border border-gray-200 bg-gradient-to-br from-red-500 to-red-600 p-6 shadow-lg dark:border-gray-700 dark:from-red-600 dark:to-red-700">
              <div class="flex items-center justify-between">
                <div>
                  <p class="mb-1 text-sm font-medium text-white/80">Total Pengeluaran</p>
                  <h3 class="text-3xl font-bold text-white">{{ formatCurrency(balanceTotals.totalKeluar || 0) }}</h3>
                </div>
                <div class="flex h-14 w-14 items-center justify-center rounded-lg bg-white/20">
                  <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                  </svg>
                </div>
              </div>
            </div>
          </div>

          <!-- Allocation Summary Table -->
          <div class="mb-6 rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-white/[0.03]">
            <div class="mb-4 flex items-center justify-between">
              <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Ringkasan Alokasi</h3>
            </div>
            
            <div class="relative overflow-x-auto">
              <!-- Loading State -->
               <div
                v-if="isLoadingAllocation"
                class="absolute inset-0 z-10 flex items-center justify-center bg-white/80 dark:bg-gray-900/80"
              >
                <div class="flex flex-col items-center gap-3">
                  <div class="h-10 w-10 animate-spin rounded-full border-4 border-gray-200 border-t-brand-500"></div>
                  <p class="text-sm text-gray-600 dark:text-gray-400">Memuat data alokasi...</p>
                </div>
              </div>

              <table class="w-full table-auto">
                <thead>
                  <tr class="border-b border-gray-200 text-left text-sm font-semibold text-gray-600 dark:border-gray-700 dark:text-gray-400">
                    <th class="px-4 py-3">Kategori</th>
                    <th class="px-4 py-3 text-right">Dana Siap Salur</th>
                    <th class="px-4 py-3 text-right">Penyaluran</th>
                    <th class="px-4 py-3 text-right">Saldo</th>
                  </tr>
                </thead>
                <tbody>
                   <tr v-for="(box, idx) in filteredAllocationBoxes" :key="`alloc-${idx}`" class="border-b border-gray-100 hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-white/[0.02]">
                      <td class="px-4 py-3 text-sm font-medium text-gray-800 dark:text-white/90">{{ box.label }}</td>
                      <td class="px-4 py-3 text-right text-sm font-bold" :class="box.label.includes('Sisa') ? 'text-brand-600 dark:text-brand-400' : 'text-gray-800 dark:text-white/90'">
                        {{ formatCurrency(box.value) }}
                      </td>
                      <td class="px-4 py-3 text-right text-sm font-bold text-purple-600 dark:text-purple-400">
                        {{ formatCurrency(box.penyaluran || 0) }}
                      </td>
                      <td class="px-4 py-3 text-right text-sm font-bold" :class="(box.value - (box.penyaluran || 0)) >= 0 ? 'text-brand-600 dark:text-brand-400' : 'text-red-600 dark:text-red-400'">
                        {{ formatCurrency(box.value - (box.penyaluran || 0)) }}
                      </td>
                   </tr>
                   <tr v-if="filteredAllocationBoxes.length === 0 && !grandTotalBox && !isLoadingAllocation">
                      <td colspan="4" class="px-4 py-12 text-center text-gray-500">Tidak ada data alokasi</td>
                   </tr>
                </tbody>
                <tfoot v-if="grandTotalBox">
                  <tr class="bg-gray-50 dark:bg-gray-800/50">
                    <td class="px-4 py-4 text-sm font-bold text-gray-800 dark:text-white/90">{{ grandTotalBox.label }}</td>
                    <td class="px-4 py-4 text-right text-lg font-bold text-brand-600 dark:text-brand-400">
                      {{ formatCurrency(grandTotalBox.value) }}
                    </td>
                    <td class="px-4 py-4 text-right text-lg font-bold text-purple-600 dark:text-purple-400">
                      {{ formatCurrency(grandTotalBox.penyaluran || 0) }}
                    </td>
                    <td class="px-4 py-4 text-right text-lg font-bold" :class="(grandTotalBox.value - (grandTotalBox.penyaluran || 0)) >= 0 ? 'text-brand-600 dark:text-brand-400' : 'text-red-600 dark:text-red-400'">
                      {{ formatCurrency(grandTotalBox.value - (grandTotalBox.penyaluran || 0)) }}
                    </td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>

          <!-- 3. Breakdown Section - Pengajuan Dana & Penyaluran -->
          <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
            <!-- Pengajuan Dana (Orange) -->
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-white/[0.03]">
              <div class="flex items-center justify-between">
                <div>
                  <p class="mb-1 text-sm font-medium text-gray-600 dark:text-gray-400">Pengajuan Dana</p>
                  <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">
                    {{ formatCurrency(balanceBreakdown.pengajuan_dana || 0) }}
                  </p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-orange-100 dark:bg-orange-500/10">
                  <svg class="h-6 w-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                  </svg>
                </div>
              </div>
            </div>

            <!-- Penyaluran (Purple) -->
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-white/[0.03]">
              <div class="flex items-center justify-between">
                <div>
                  <p class="mb-1 text-sm font-medium text-gray-600 dark:text-gray-400">Penyaluran</p>
                  <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                    {{ formatCurrency(balanceBreakdown.penyaluran || 0) }}
                  </p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-purple-100 dark:bg-purple-500/10">
                  <svg class="h-6 w-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                  </svg>
                </div>
              </div>
            </div>
          </div>


          <!-- 4. Charts Row - Donut Chart & Saldo Card -->
          <div class="mb-6 grid grid-cols-1 gap-4 lg:grid-cols-2">
            <!-- Donut Chart for Breakdown -->
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-white/[0.03]">
              <h3 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white/90">Breakdown Pengeluaran</h3>
              <div class="flex items-center justify-center">
                <VueApexCharts
                  type="donut"
                  height="280"
                  :options="breakdownChartOptions"
                  :series="breakdownChartSeries"
                />
              </div>
            </div>

            <!-- Saldo Card - Breakdown Details -->
            <div class="rounded-lg border border-gray-200 bg-gradient-to-br from-brand-500 to-brand-600 p-6 shadow-lg dark:border-gray-700 dark:from-brand-600 dark:to-brand-700">
              <h3 class="mb-4 text-lg font-semibold text-white/90">Detail Breakdown</h3>
              <div class="space-y-4">
                <div>
                  <p class="text-sm text-white/80">Selisih Dana Belum Tersalurkan</p>
                  <p class="text-3xl font-bold text-white">{{ formatCurrency(Math.max(0, (balanceBreakdown.pengajuan_dana || 0) - (balanceBreakdown.penyaluran || 0))) }}</p>
                </div>
                <div class="space-y-3 border-t border-white/20 pt-4">
                  <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                      <div class="h-3 w-3 rounded-full bg-orange-300"></div>
                      <p class="text-xs text-white/70">Pengajuan Dana</p>
                    </div>
                    <p class="text-sm font-semibold text-white">{{ formatCurrency(balanceBreakdown.pengajuan_dana || 0) }}</p>
                  </div>
                  <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                      <div class="h-3 w-3 rounded-full bg-purple-300"></div>
                      <p class="text-xs text-white/70">Penyaluran</p>
                    </div>
                    <p class="text-sm font-semibold text-white">{{ formatCurrency(balanceBreakdown.penyaluran || 0) }}</p>
                  </div>
                  <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                      <div class="h-3 w-3 rounded-full bg-cyan-300"></div>
                      <p class="text-xs text-white/70">Selisih</p>
                    </div>
                    <p class="text-sm font-semibold text-white">{{ formatCurrency(Math.max(0, (balanceBreakdown.pengajuan_dana || 0) - (balanceBreakdown.penyaluran || 0))) }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          
            <!-- NEW: Bank Accounts Section (Actual Balance) -->
            <div class="mb-6 rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-white/[0.03]">
              <div class="mb-4 flex items-center justify-between">
                <div>
                  <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Rekening Bank (Saldo Aktual)</h3>
                  <p class="text-sm text-gray-500 dark:text-gray-400">Total Saldo: <span class="font-bold text-gray-800 dark:text-white">{{ formatCurrency(totalBankBalance) }}</span></p>
                </div>
                <button v-if="can('create laporan keuangan')" @click="openBankModal()" class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                  + Tambah Rekening
                </button>
              </div>

              <!-- Bank Accounts Grid -->
              <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div v-for="bank in bankAccounts" :key="bank.id" class="relative group rounded-lg border border-gray-200 bg-gray-50 p-4 transition-all hover:-translate-y-1 hover:shadow-md dark:border-gray-700 dark:bg-gray-800/50">
                  <!-- Actions (Edit/Delete) -->
                  <div class="absolute top-2 right-2 opacity-0 transition-opacity group-hover:opacity-100 flex gap-2">
                    <button v-if="can('update laporan keuangan')" @click="openBankModal(bank)" class="text-gray-500 hover:text-blue-500" title="Edit">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                      </svg>
                    </button>
                    <button v-if="can('delete laporan keuangan')" @click="confirmDeleteBank(bank)" class="text-gray-500 hover:text-red-500" title="Hapus">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </div>

                  <div class="flex items-center gap-3 mb-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-blue-600 dark:bg-blue-500/20 dark:text-blue-400">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                      </svg>
                    </div>
                    <div>
                      <h4 class="font-semibold text-gray-800 dark:text-white">{{ bank.bank_name }}</h4>
                      <p class="text-xs text-gray-500 dark:text-gray-400">{{ bank.account_number }}</p>
                    </div>
                  </div>
                  <div class="flex items-end justify-between">
                    <div>
                      <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">Saldo Aktual</p>
                      <p class="text-lg font-bold text-gray-800 dark:text-white">{{ formatCurrency(bank.balance) }}</p>
                    </div>
                    <div v-if="bank.account_name" class="text-right">
                      <p class="text-[10px] text-gray-400 uppercase tracking-wider">A.N</p>
                      <p class="text-xs font-medium text-gray-600 dark:text-gray-300 max-w-[100px] truncate" :title="bank.account_name">{{ bank.account_name }}</p>
                    </div>
                  </div>
                </div>

                <!-- Empty State -->
                <div v-if="bankAccounts.length === 0" class="col-span-1 sm:col-span-2 lg:col-span-3 flex flex-col items-center justify-center rounded-lg border border-dashed border-gray-300 p-8 text-center text-gray-500 dark:border-gray-700 dark:text-gray-400">
                  <p>Belum ada data rekening bank.</p>
                  <button v-if="can('create laporan keuangan')" @click="openBankModal()" class="mt-2 text-sm font-medium text-brand-500 hover:text-brand-600 hover:underline">Tambah Rekening Baru</button>
                </div>
              </div>
            </div>

          <!-- Breakdown per Tipe Pengeluaran -->
          <div class="mb-6 rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-white/[0.03]">
            <div class="mb-4 flex items-center justify-between">
              <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Breakdown per Tipe Penyaluran</h3>
              <div class="text-sm text-gray-600 dark:text-gray-400">
                Total: <span class="font-bold text-purple-600 dark:text-purple-400">{{ formatCurrency(penyaluranByAliasData.total || 0) }}</span>
              </div>
            </div>

            <!-- Top 3 Categories Cards -->
            <div v-if="penyaluranByAliasData.breakdown && penyaluranByAliasData.breakdown.length > 0" class="mb-6 grid grid-cols-1 gap-3 sm:grid-cols-3">
              <div
                v-for="(item, index) in penyaluranByAliasData.breakdown.slice(0, 3)"
                :key="index"
                class="rounded-lg border p-4"
                :class="{
                  'border-purple-200 bg-purple-50 dark:border-purple-500/20 dark:bg-purple-500/10': index === 0,
                  'border-blue-200 bg-blue-50 dark:border-blue-500/20 dark:bg-blue-500/10': index === 1,
                  'border-green-200 bg-green-50 dark:border-green-500/20 dark:bg-green-500/10': index === 2,
                }"
              >
                <div class="flex items-center justify-between">
                  <div class="flex-1">
                    <div class="mb-1 flex items-center gap-2">
                      <span class="flex h-6 w-6 items-center justify-center rounded-full text-xs font-bold"
                        :class="{
                          'bg-purple-200 text-purple-700 dark:bg-purple-500/20 dark:text-purple-400': index === 0,
                          'bg-blue-200 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400': index === 1,
                          'bg-green-200 text-green-700 dark:bg-green-500/20 dark:text-green-400': index === 2,
                        }"
                      >
                        {{ index + 1 }}
                      </span>
                      <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ item.alias }}</p>
                    </div>
                    <p class="text-lg font-bold"
                      :class="{
                        'text-purple-700 dark:text-purple-400': index === 0,
                        'text-blue-700 dark:text-blue-400': index === 1,
                        'text-green-700 dark:text-green-400': index === 2,
                      }"
                    >
                      {{ formatCurrency(item.amount) }}
                    </p>
                    <p class="text-xs text-gray-600 dark:text-gray-400">
                      {{ item.percentage }}% • {{ item.count }} transaksi
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Horizontal Bar Chart -->
            <div class="relative">
              <!-- Loading State -->
              <div
                v-if="isLoadingPenyaluranByAlias"
                class="absolute inset-0 z-10 flex items-center justify-center bg-white/80 dark:bg-gray-900/80"
              >
                <div class="flex flex-col items-center gap-3">
                  <div class="h-10 w-10 animate-spin rounded-full border-4 border-gray-200 border-t-brand-500"></div>
                  <p class="text-sm text-gray-600 dark:text-gray-400">Memuat data kategori...</p>
                </div>
              </div>

              <VueApexCharts
                v-if="penyaluranByAliasData.breakdown && penyaluranByAliasData.breakdown.length > 0"
                type="bar"
                height="300"
                :options="penyaluranByAliasChartOptions"
                :series="penyaluranByAliasChartSeries"
              />

              <!-- Empty State -->
              <div v-if="!penyaluranByAliasData.breakdown || penyaluranByAliasData.breakdown.length === 0 && !isLoadingPenyaluranByAlias" class="py-12 text-center">
                <div class="flex flex-col items-center justify-center gap-3">
                  <svg class="h-16 w-16 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                  </svg>
                  <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Tidak ada data penyaluran</p>
                  <p class="text-xs text-gray-500 dark:text-gray-500">Belum ada penyaluran untuk periode ini</p>
                </div>
              </div>
            </div>

            <!-- Detailed Breakdown Table -->
            <div class="mt-6">
              <h4 class="mb-4 text-base font-semibold text-gray-800 dark:text-white/90">
                Detail Breakdown per Tipe Penyaluran
              </h4>
              <div class="relative overflow-x-auto">
                <!-- Loading State -->
                <div
                  v-if="isLoadingExpenseTypeBreakdown"
                  class="absolute inset-0 z-10 flex items-center justify-center bg-white/80 dark:bg-gray-900/80"
                >
                  <div class="flex flex-col items-center gap-3">
                    <div class="h-10 w-10 animate-spin rounded-full border-4 border-gray-200 border-t-brand-500"></div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Memuat detail breakdown...</p>
                  </div>
                </div>

                <table v-if="expenseTypeBreakdownData && expenseTypeBreakdownData.length > 0" class="w-full table-auto">
                  <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                      <th class="pb-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Nama Penyaluran</th>
                      <th class="pb-3 text-right text-sm font-semibold text-gray-700 dark:text-gray-300">Pengajuan Dana</th>
                      <th class="pb-3 text-right text-sm font-semibold text-gray-700 dark:text-gray-300">Penyaluran</th>
                    </tr>
                  </thead>
                  <tbody>
                    <template v-for="(item, index) in expenseTypeBreakdownData" :key="index">
                      <tr
                        class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-white/[0.02]"
                        :class="{ 'bg-blue-50/50 dark:bg-blue-900/10': expandedExpenseType === item.submission_type }"
                      >
                        <td class="py-3 text-sm font-medium text-gray-800 dark:text-white/90">
                          <div class="flex items-center gap-2">
                            <button
                              @click="expandedExpenseType = expandedExpenseType === item.submission_type ? null : item.submission_type"
                              class="flex h-6 w-6 items-center justify-center rounded hover:bg-gray-200 dark:hover:bg-gray-700"
                            >
                              <svg
                                class="h-4 w-4 text-gray-600 dark:text-gray-400 transition-transform"
                                :class="{ 'rotate-90': expandedExpenseType === item.submission_type }"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                              >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                              </svg>
                            </button>
                            {{ item.alias }}
                          </div>
                        </td>
                        <td class="py-3 text-right text-sm text-orange-600 dark:text-orange-400">{{ formatCurrency(item.pengajuan_dana) }}</td>
                        <td class="py-3 text-right text-sm text-purple-600 dark:text-purple-400">{{ formatCurrency(item.penyaluran) }}</td>
                      </tr>
                      
                      <!-- Detail Row -->
                      <tr v-if="expandedExpenseType === item.submission_type" class="bg-gray-50 dark:bg-gray-800/50">
                        <td colspan="3" class="p-4">
                          <div class="overflow-hidden rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">
                            <h5 class="bg-gray-50 px-4 py-2 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:bg-gray-800 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                              Detail Penyaluran ({{ item.details?.length || 0 }})
                            </h5>
                            <div v-if="!item.details || item.details.length === 0" class="p-4 text-center text-sm text-gray-500">
                              Tidak ada detail penyaluran
                            </div>
                            <table v-else class="w-full text-left text-sm">
                              <thead class="bg-gray-50 text-xs uppercase text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                                <tr>
                                  <th class="px-4 py-2">Tanggal</th>
                                  <th class="px-4 py-2">Keterangan</th>
                                  <th class="px-4 py-2">Penerima</th>
                                  <th class="px-4 py-2 text-right">Nominal</th>
                                </tr>
                              </thead>
                              <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="detail in item.details" :key="detail.id">
                                  <td class="px-4 py-2 text-gray-600 dark:text-gray-300 whitespace-nowrap">
                                    {{ formatDate(detail.created_at) }}
                                  </td>
                                  <td class="px-4 py-2 text-gray-600 dark:text-gray-300">
                                    {{ detail.program_name || detail.report || '-' }}
                                  </td>
                                  <td class="px-4 py-2 text-gray-600 dark:text-gray-300">
                                    {{ detail.pic || '-' }}
                                  </td>
                                  <td class="px-4 py-2 text-right font-medium text-gray-800 dark:text-white">
                                    {{ formatCurrency(detail.amount) }}
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                        </td>
                      </tr>
                    </template>
                  </tbody>
                  <tfoot>
                    <tr class="border-t-2 border-gray-300 dark:border-gray-600">
                      <td class="pt-3 text-sm font-bold text-gray-800 dark:text-white/90">Total</td>
                      <td class="pt-3 text-right text-sm font-bold text-orange-600 dark:text-orange-400">
                        {{ formatCurrency(expenseTypeBreakdownData.reduce((sum, item) => sum + item.pengajuan_dana, 0)) }}
                      </td>
                      <td class="pt-3 text-right text-sm font-bold text-purple-600 dark:text-purple-400">
                        {{ formatCurrency(expenseTypeBreakdownData.reduce((sum, item) => sum + item.penyaluran, 0)) }}
                      </td>
                    </tr>
                  </tfoot>
                </table>

                <!-- Empty State -->
                <div v-if="!expenseTypeBreakdownData || expenseTypeBreakdownData.length === 0 && !isLoadingExpenseTypeBreakdown" class="py-12 text-center">
                  <div class="flex flex-col items-center justify-center gap-3">
                    <svg class="h-16 w-16 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Tidak ada data breakdown</p>
                    <p class="text-xs text-gray-500 dark:text-gray-500">Belum ada data untuk periode ini</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- 6. Program Breakdown Table -->
          <div class="mb-6 rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-white/[0.03]">
            <div class="mb-4 flex items-center justify-between">
              <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Breakdown Inflow - Penyaluran per Program</h3>
              <button
                @click="handleExportProgramBreakdown"
                :disabled="programBreakdownData.length === 0"
                class="flex items-center gap-2 rounded-lg bg-green-500 px-4 py-2 text-sm font-medium text-white hover:bg-green-600 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export Excel
              </button>
            </div>
            <div class="relative overflow-x-auto">
              <!-- Loading State for Program Breakdown -->
              <div
                v-if="isLoadingProgramBreakdown"
                class="absolute inset-0 z-10 flex items-center justify-center bg-white/80 dark:bg-gray-900/80"
              >
                <div class="flex flex-col items-center gap-3">
                  <div class="h-10 w-10 animate-spin rounded-full border-4 border-gray-200 border-t-brand-500"></div>
                  <p class="text-sm text-gray-600 dark:text-gray-400">Memuat data program...</p>
                </div>
              </div>

              <table class="w-full table-auto">
                <thead>
                  <tr class="border-b border-gray-200 text-left text-sm font-semibold text-gray-600 dark:border-gray-700 dark:text-gray-400">
                    <th class="px-4 py-3">Program</th>
                    <th class="px-4 py-3 text-right">Dana Siap Salur</th>
                    <th class="px-4 py-3 text-right">Pengajuan Dana</th>
                    <th class="px-4 py-3 text-right">Penyaluran</th>
                    <th class="px-4 py-3 text-right">Selisih</th>
                    <th class="px-4 py-3 text-right">Saldo</th>
                  </tr>
                </thead>
                <tbody>
                  <template v-for="program in programBreakdownData" :key="program.id || 'null-program'">
                    <!-- Main Row -->
                    <tr
                      class="border-b border-gray-100 hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-white/[0.02]"
                      :class="{ 'bg-blue-50/50 dark:bg-blue-900/10': expandedProgramId === (program.id || 'null') }"
                    >
                      <td class="px-4 py-3 text-sm font-medium text-gray-800 dark:text-white/90">
                        <div class="flex items-center gap-2">
                          <!-- Expand button for Semua Program with breakdown -->
                          <button
                            v-if="program.id === null && program.breakdown"
                            @click="expandedProgramId = expandedProgramId === 'null' ? null : 'null'"
                            class="flex h-6 w-6 items-center justify-center rounded hover:bg-gray-200 dark:hover:bg-gray-700"
                          >
                            <svg
                              class="h-4 w-4 text-gray-600 dark:text-gray-400 transition-transform"
                              :class="{ 'rotate-90': expandedProgramId === 'null' }"
                              fill="none"
                              stroke="currentColor"
                              viewBox="0 0 24 24"
                            >
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                          </button>
                          <span v-if="program.id === null" class="inline-flex items-center gap-2">
                            <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            {{ program.nama }}
                          </span>
                          <span v-else>{{ program.nama }}</span>
                        </div>
                      </td>
                      <td class="px-4 py-3 text-right text-sm font-semibold text-green-600 dark:text-green-400">{{ formatCurrency(program.pemasukan || 0) }}</td>
                      <td class="px-4 py-3 text-right text-sm text-orange-600 dark:text-orange-400">{{ formatCurrency(program.pengajuan_dana || 0) }}</td>
                      <td class="px-4 py-3 text-right text-sm text-purple-600 dark:text-purple-400">{{ formatCurrency(program.penyaluran || 0) }}</td>
                      <td class="px-4 py-3 text-right text-sm font-semibold" :class="(program.selisih || 0) >= 0 ? 'text-cyan-600 dark:text-cyan-400' : 'text-red-600 dark:text-red-400'">{{ formatCurrency(program.selisih || 0) }}</td>
                      <td class="px-4 py-3 text-right text-sm font-bold" :class="(program.saldo || 0) >= 0 ? 'text-brand-600 dark:text-brand-400' : 'text-red-600 dark:text-red-400'">{{ formatCurrency(program.saldo || 0) }}</td>
                    </tr>
                    
                    <!-- Expanded Detail Row for Semua Program -->
                    <tr v-if="program.id === null && program.breakdown && expandedProgramId === 'null'" class="border-b border-gray-100 bg-blue-50/30 dark:border-gray-800 dark:bg-blue-900/5">
                      <td colspan="6" class="px-4 py-4">
                        <div class="ml-8 space-y-4">
                          <div class="text-xs font-semibold uppercase text-gray-600 dark:text-gray-400">Detail Breakdown (FIFO)</div>
                          
                          <!-- Pemasukan Breakdown -->
                          <div v-if="program.breakdown.pemasukan && program.breakdown.pemasukan.length > 0">
                            <div class="mb-2 text-sm font-medium text-green-700 dark:text-green-400">Dana Siap Salur dari Program:</div>
                            <div class="space-y-1">
                              <div
                                v-for="(item, idx) in program.breakdown.pemasukan"
                                :key="'pemasukan-' + idx"
                                class="flex items-center justify-between rounded bg-white px-3 py-2 text-sm dark:bg-gray-800/50"
                              >
                                <span class="text-gray-700 dark:text-gray-300">{{ item.program_nama }}</span>
                                <span class="font-semibold text-green-600 dark:text-green-400">{{ formatCurrency(item.amount) }}</span>
                              </div>
                            </div>
                          </div>
                          
                          <!-- Pengajuan Dana Breakdown -->
                          <div v-if="program.breakdown.pengajuan_dana && program.breakdown.pengajuan_dana.length > 0">
                            <div class="mb-2 text-sm font-medium text-orange-700 dark:text-orange-400">Pengajuan Dana dari Program:</div>
                            <div class="space-y-1">
                              <div
                                v-for="(item, idx) in program.breakdown.pengajuan_dana"
                                :key="'pengajuan-' + idx"
                                class="flex items-center justify-between rounded bg-white px-3 py-2 text-sm dark:bg-gray-800/50"
                              >
                                <span class="text-gray-700 dark:text-gray-300">{{ item.program_nama }}</span>
                                <span class="font-semibold text-orange-600 dark:text-orange-400">{{ formatCurrency(item.amount) }}</span>
                              </div>
                            </div>
                          </div>
                          
                          <!-- Penyaluran Breakdown -->
                          <div v-if="program.breakdown.penyaluran && program.breakdown.penyaluran.length > 0">
                            <div class="mb-2 text-sm font-medium text-purple-700 dark:text-purple-400">Penyaluran dari Program:</div>
                            <div class="space-y-1">
                              <div
                                v-for="(item, idx) in program.breakdown.penyaluran"
                                :key="'penyaluran-' + idx"
                                class="flex items-center justify-between rounded bg-white px-3 py-2 text-sm dark:bg-gray-800/50"
                              >
                                <span class="text-gray-700 dark:text-gray-300">{{ item.program_nama }}</span>
                                <span class="font-semibold text-purple-600 dark:text-purple-400">{{ formatCurrency(item.amount) }}</span>
                              </div>
                            </div>
                          </div>

                          <div v-if="(!program.breakdown.pemasukan || program.breakdown.pemasukan.length === 0) && (!program.breakdown.pengajuan_dana || program.breakdown.pengajuan_dana.length === 0) && (!program.breakdown.penyaluran || program.breakdown.penyaluran.length === 0)" class="text-sm text-gray-500 dark:text-gray-500">
                            Tidak ada detail breakdown untuk periode ini
                          </div>
                        </div>
                      </td>
                    </tr>
                  </template>
                  <tr v-if="programBreakdownData.length === 0 && !isLoadingProgramBreakdown">
                    <td colspan="6" class="px-4 py-12 text-center">
                      <div class="flex flex-col items-center justify-center gap-3">
                        <svg class="h-16 w-16 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Tidak ada data program</p>
                        <p class="text-xs text-gray-500 dark:text-gray-500">Belum ada aktivitas keuangan untuk periode ini</p>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- 7. Transaction Detail Table with Filter Tabs -->
          <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-white/[0.03]">
            <div class="mb-4 flex items-center justify-between">
              <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Detail Transaksi</h3>
              <button
                @click="handleExportTransactions"
                class="flex items-center gap-2 rounded-lg bg-green-500 px-4 py-2 text-sm font-medium text-white hover:bg-green-600"
              >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export Excel
              </button>
            </div>

            <!-- Filter Tabs -->
            <div class="mb-4 flex gap-2 border-b border-gray-200 dark:border-gray-700">
              <button
                v-for="filter in transactionFilters"
                :key="filter.value"
                @click="activeTransactionFilter = filter.value"
                class="px-4 py-2 text-sm font-medium transition-colors"
                :class="activeTransactionFilter === filter.value 
                  ? 'border-b-2 border-brand-500 text-brand-600 dark:text-brand-400' 
                  : 'text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-300'"
              >
                {{ filter.label }}
              </button>
            </div>

            <!-- Transaction Table -->
            <div class="relative overflow-x-auto">
              <!-- Loading Overlay -->
              <div
                v-if="isLoadingTransactionFilter"
                class="absolute inset-0 z-10 flex items-center justify-center bg-white/80 dark:bg-gray-900/80"
              >
                <div class="flex flex-col items-center gap-3">
                  <div class="h-10 w-10 animate-spin rounded-full border-4 border-gray-200 border-t-brand-500"></div>
                  <p class="text-sm text-gray-600 dark:text-gray-400">Memuat data...</p>
                </div>
              </div>

              <table class="w-full table-auto">
                <thead>
                  <tr class="border-b border-gray-200 text-left text-sm font-semibold text-gray-600 dark:border-gray-700 dark:text-gray-400">
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">Keterangan</th>
                    <th class="px-4 py-3">Tipe</th>
                    <th class="px-4 py-3 text-right">Masuk</th>
                    <th class="px-4 py-3 text-right">Keluar</th>
                    <th class="px-4 py-3 text-right">Saldo</th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="tx in filteredTransactions"
                    :key="tx.id"
                    class="border-b border-gray-100 hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-white/[0.02]"
                  >
                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ tx.tanggal }}</td>
                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ tx.keterangan }}</td>
                    <td class="px-4 py-3">
                      <span
                        class="inline-flex rounded-full px-2 py-1 text-xs font-medium"
                        :class="{
                          'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400': tx.masuk > 0,
                          'bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-400': tx.keluar > 0
                        }"
                      >
                        {{ tx.masuk > 0 ? 'Pemasukan' : 'Pengeluaran' }}
                      </span>
                    </td>
                    <td class="px-4 py-3 text-right text-sm font-semibold text-green-600 dark:text-green-400">
                      {{ tx.masuk > 0 ? formatCurrency(tx.masuk) : '-' }}
                    </td>
                    <td class="px-4 py-3 text-right text-sm font-semibold text-red-600 dark:text-red-400">
                      {{ tx.keluar > 0 ? formatCurrency(tx.keluar) : '-' }}
                    </td>
                    <td class="px-4 py-3 text-right text-sm font-medium text-gray-800 dark:text-white/90">
                      {{ formatCurrency(tx.saldo) }}
                    </td>
                  </tr>
                  <tr v-if="filteredTransactions.length === 0 && !isLoadingTransactionFilter">
                    <td colspan="6" class="px-4 py-12 text-center">
                      <div class="flex flex-col items-center justify-center gap-3">
                        <svg class="h-16 w-16 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Tidak ada transaksi</p>
                        <p class="text-xs text-gray-500 dark:text-gray-500">Belum ada data transaksi untuk filter ini</p>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            <div v-if="filteredTransactions.length > 0" class="mt-4 flex items-center justify-between">
              <div class="text-sm text-gray-600 dark:text-gray-400">
                Menampilkan halaman {{ balancePagination.current_page }} dari {{ balancePagination.last_page }} — total {{ balancePagination.total }} transaksi
              </div>
              <div class="flex gap-2">
                <button
                  :disabled="balancePagination.current_page <= 1"
                  @click="() => { balancePagination.current_page = Math.max(1, balancePagination.current_page - 1); fetchBalanceData(balancePagination.current_page); }"
                  class="h-10 rounded-lg border border-gray-300 bg-white px-4 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-white/[0.03]"
                >
                  Sebelumnya
                </button>
                <button
                  :disabled="balancePagination.current_page >= balancePagination.last_page"
                  @click="() => { balancePagination.current_page = Math.min(balancePagination.last_page, balancePagination.current_page + 1); fetchBalanceData(balancePagination.current_page); }"
                  class="h-10 rounded-lg border border-gray-300 bg-white px-4 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-white/[0.03]"
                >
                  Selanjutnya
                </button>
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
                {{ mitra.transaksi_count || 0 }} transaksi • {{ formatCurrency(mitra.transaksi_total || 0) }}
              </p>
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-xs text-gray-500 dark:text-gray-500">Jumlah Transaksi</p>
                  <p class="text-base font-semibold text-gray-800 dark:text-white/90">
                    {{ mitra.transaksi_count || 0 }}
                  </p>
                </div>
                <div>
                  <p class="text-xs text-gray-500 dark:text-gray-500">Total Nilai</p>
                  <p class="text-base font-semibold text-gray-800 dark:text-white/90">
                    {{ formatCurrency(mitra.transaksi_total || 0) }}
                  </p>
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
            v-if="mitraList.length > 0"
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

  <Modal :isOpen="showBankModal" :title="editBankId ? 'Edit Rekening' : 'Tambah Rekening'" @close="closeBankModal">
    <template #content>
      <div class="space-y-4">
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Bank <span class="text-red-500">*</span></label>
          <input
            v-model="bankForm.bank_name"
            type="text"
            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-600 dark:bg-gray-800 dark:text-white"
            placeholder="Contoh: BCA, Mandiri, BRI"
          >
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Nomor Rekening</label>
            <input
              v-model="bankForm.account_number"
              type="text"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-600 dark:bg-gray-800 dark:text-white"
              placeholder="1234567890"
            >
          </div>
          <div>
            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Atas Nama</label>
            <input
              v-model="bankForm.account_name"
              type="text"
              class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-600 dark:bg-gray-800 dark:text-white"
              placeholder="Yayasan..."
            >
          </div>
        </div>
        <div>
          <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Saldo Aktual (Rp) <span class="text-red-500">*</span></label>
          <input
            v-model.number="bankForm.balance"
            type="number"
            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none dark:border-gray-600 dark:bg-gray-800 dark:text-white"
            placeholder="0"
          />
          <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ formatCurrency(bankForm.balance) }}</p>
        </div>
      </div>
    </template>
    <template #footer>
      <div class="flex justify-end gap-2">
        <button @click="closeBankModal" class="rounded-lg px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">Batal</button>
        <button @click="saveBank" :disabled="isSavingBank" class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 disabled:opacity-50">
          {{ isSavingBank ? 'Menyimpan...' : 'Simpan' }}
        </button>
      </div>
    </template>
  </Modal>

  <ConfirmModal
    :isOpen="showDeleteBankModal"
    title="Hapus Rekening"
    message="Apakah Anda yakin ingin menghapus rekening ini? Data yang dihapus tidak dapat dikembalikan."
    confirmText="Hapus"
    confirmButtonClass="bg-red-500 hover:bg-red-600"
    @confirm="deleteBank"
    @cancel="showDeleteBankModal = false"
  />

  </AdminLayout>
</template>

<script setup lang="ts">
import { ref, computed, watch, onMounted, nextTick } from 'vue'
import { useRoute, useRouter } from 'vue-router'
// import { AgGridVue } from 'ag-grid-vue3'
import VueApexCharts from 'vue3-apexcharts'
import flatPickr from 'vue-flatpickr-component'
import * as XLSX from 'xlsx'
import 'flatpickr/dist/flatpickr.css'
import 'ag-grid-community/styles/ag-grid.css'
import 'ag-grid-community/styles/ag-theme-alpine.css'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import SearchableSelect from '@/components/forms/SearchableSelect.vue'
import Modal from '@/components/ui/Modal.vue'
import ConfirmModal from '@/components/common/ConfirmModal.vue'
import { useToast } from 'vue-toastification'
import { getCsrfTokenSafe } from '@/utils/getCsrfToken'
import { useAuth } from '@/composables/useAuth'


const route = useRoute()
const router = useRouter()
const toast = useToast()
const { can } = useAuth()
const currentPageTitle = computed(() => (route.meta.title as string) || 'Laporan Keuangan')


// Helper for date formatting
const formatDate = (dateString: string) => {
  if (!dateString) return '-'
  return new Date(dateString).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

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
const balanceStart = ref('2020-01-01')
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
    fetchProgramBreakdown()
    fetchPenyaluranByAlias()
    fetchExpenseTypeBreakdown()
    fetchAllocationSummary()
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

// NEW: Additional balance breakdown data
const balanceBreakdown = ref({
  pengajuan_dana: 0,
  pengajuan_dana_percentage: 0,
  penyaluran: 0,
  penyaluran_percentage: 0,
})

const programBreakdownData = ref([])
const isLoadingTransactionFilter = ref(false)
const isLoadingProgramBreakdown = ref(false)

// NEW: Expanded row state for program breakdown details
const expandedProgramId = ref(null)

// NEW: Penyaluran by Alias data
const penyaluranByAliasData = ref({ total: 0, breakdown: [] })
const isLoadingPenyaluranByAlias = ref(false)

// NEW: Expense Type Breakdown data
const expenseTypeBreakdownData = ref([])
const expandedExpenseType = ref(null)
const isLoadingExpenseTypeBreakdown = ref(false)

// NEW: Allocation Summary Boxes data
const allocationBoxes = ref([])
const isLoadingAllocation = ref(false)

const filteredAllocationBoxes = computed(() => {
  return allocationBoxes.value.filter((box: any) => !box.label.toLowerCase().includes('keseluruhan transaksi'))
})

const grandTotalBox = computed(() => {
  return allocationBoxes.value.find((box: any) => box.label.toLowerCase().includes('keseluruhan transaksi'))
})

const transactionFilters = [
  { value: 'all', label: 'Semua' },
  { value: 'masuk', label: 'Pemasukan' },
  { value: 'pengajuan_dana', label: 'Pengajuan Dana' },
  { value: 'penyaluran', label: 'Penyaluran' },
]

const activeTransactionFilter = ref('all')

// Watch for transaction filter changes and add loading animation
watch(activeTransactionFilter, () => {
  isLoadingTransactionFilter.value = true
  // Reset to first page when changing filter
  balancePagination.value.current_page = 1
  setTimeout(() => {
    isLoadingTransactionFilter.value = false
  }, 300)
})

// NEW: Filtered transactions based on active filter
const filteredTransactions = computed(() => {
  if (!balanceTransactions.value) return []
  
  switch (activeTransactionFilter.value) {
    case 'masuk':
      return balanceTransactions.value.filter(t => t.masuk > 0)
    case 'pengajuan_dana':
      return balanceTransactions.value.filter(t => t.keluar > 0 && t.keterangan.toLowerCase().includes('pengajuan'))
    case 'penyaluran':
      return balanceTransactions.value.filter(t => t.keluar > 0 && t.keterangan.toLowerCase().includes('penyalur'))
    default:
      return balanceTransactions.value
  }
})

// NEW: Breakdown Donut Chart
const breakdownChartOptions = computed(() => ({
  chart: {
    fontFamily: 'Outfit, sans-serif',
    type: 'donut',
  },
  colors: ['#f97316', '#a855f7', '#06b6d4'],
  labels: ['Pengajuan Dana', 'Penyaluran', 'Selisih'],
  legend: {
    position: 'bottom',
    labels: {
      colors: '#6B7280',
    },
  },
  dataLabels: {
    enabled: true,
    formatter: function (val: number) {
      return val.toFixed(1) + '%'
    },
  },
  plotOptions: {
    pie: {
      donut: {
        size: '70%',
        labels: {
          show: true,
          name: {
            show: true,
            fontSize: '14px',
            fontWeight: 600,
            color: '#374151',
          },
          value: {
            show: true,
            fontSize: '24px',
            fontWeight: 700,
            color: '#1F2937',
            formatter: function (val: any) {
              return formatCurrency(parseFloat(val))
            },
          },
          total: {
            show: true,
            label: 'Pengajuan Dana',
            fontSize: '14px',
            fontWeight: 600,
            color: '#6B7280',
            formatter: function (w: any) {
              // Show only Pengajuan Dana (first segment) as the source fund
              const pengajuanDana = w.globals.seriesTotals[0] || 0
              return formatCurrency(pengajuanDana)
            },
          },
        },
      },
    },
  },
}))

const breakdownChartSeries = computed(() => {
  const pengajuan = balanceBreakdown.value.pengajuan_dana || 0
  const penyaluran = balanceBreakdown.value.penyaluran || 0
  const selisih = Math.max(0, pengajuan - penyaluran)
  return [pengajuan, penyaluran, selisih]
})


// NEW: Penyaluran by Alias Horizontal Bar Chart
// Helper function untuk generate array warna
const generateColors = (count: number) => {
  const baseColors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#ec4899', '#6366f1', '#14b8a6', '#f97316', '#8b5cf6', '#06b6d4']
  const colors = []
  for (let i = 0; i < count; i++) {
    colors.push(baseColors[i % baseColors.length])
  }
  return colors
}

// Kemudian di chartOptions
const penyaluranByAliasChartOptions = computed(() => {
  const dataCount = penyaluranByAliasData.value.breakdown.length
  const dynamicColors = generateColors(dataCount)
  
  return {
    chart: {
      fontFamily: 'Outfit, sans-serif',
      type: 'bar',
      toolbar: {
        show: false,
      },
    },
    colors: dynamicColors,
    plotOptions: {
      bar: {
        horizontal: true,
        barHeight: '70%',
        distributed: true, // PENTING: Ini membuat setiap bar punya warna berbeda
      },
    },
    dataLabels: {
      enabled: true,
      formatter: function (val: number, opts: any) {
        const color = dynamicColors[opts.dataPointIndex]
        return formatCurrency(val);
      },
      offsetX: 10,
      style: {
        fontSize: '12px',
      },
    },
    xaxis: {
      categories: penyaluranByAliasData.value.breakdown.map((item: any) => item.alias),
      labels: {
        style: {
          colors: '#6B7280',
        },
        formatter: function (val: number) {
          return formatCurrency(val)
        },
      },
    },
    yaxis: {
      labels: {
        style: {
          colors: '#6B7280',
        },
      },
    },
    legend: {
      show: false, // Karena pakai distributed, legend jadi tidak perlu
    },
    tooltip: {
      y: {
        formatter: function (val: number, opts: any) {
          const item = penyaluranByAliasData.value.breakdown[opts.dataPointIndex]
          return `${formatCurrency(val)} (${item.percentage}% • ${item.count} transaksi)`
        },
      },
    },
    grid: {
      borderColor: '#e5e7eb',
    },
  }
})

const penyaluranByAliasChartSeries = computed(() => [
  {
    name: 'Jumlah Penyaluran',
    data: penyaluranByAliasData.value.breakdown.map((item: any) => item.amount),
  },
])


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
    
    // NEW: Set breakdown data from API response
    if (data.breakdown) {
      balanceBreakdown.value = {
        pengajuan_dana: data.breakdown.pengajuan_dana || 0,
        pengajuan_dana_percentage: data.breakdown.pengajuan_percentage || 0,
        penyaluran: data.breakdown.penyaluran || 0,
        penyaluran_percentage: data.breakdown.penyaluran_percentage || 0,
      }
      console.log('Balance breakdown loaded:', {
        pengajuan_dana: balanceBreakdown.value.pengajuan_dana,
        pengajuan_percentage: balanceBreakdown.value.pengajuan_dana_percentage,
        penyaluran: balanceBreakdown.value.penyaluran,
        penyaluran_percentage: balanceBreakdown.value.penyaluran_percentage,
      })
    }
    
    // Fetch program breakdown
    await fetchProgramBreakdown()
  } catch (err) {
    console.error('Exception fetching laporan keuangan', err)
  }
}

const fetchAllocationSummary = async () => {
  isLoadingAllocation.value = true
  try {
    const params = new URLSearchParams()
    params.append('start', balanceStart.value)
    params.append('end', balanceEnd.value)
    if (selectedProgram.value) params.append('program_id', selectedProgram.value)
    if (selectedKantor.value) params.append('kantor_cabang_id', selectedKantor.value)

    const res = await fetch(`/admin/api/laporan/keuangan/allocation-summary?${params.toString()}`, {
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      },
      credentials: 'same-origin',
    })

    if (!res.ok) return
    const json = await res.json()
    if (json.success) {
      allocationBoxes.value = json.data || []
    }
  } catch (err) {
    console.error('Error fetching allocation summary', err)
  } finally {
    isLoadingAllocation.value = false
  }
}


// NEW: Bank Accounts Logic
const bankAccounts = ref([])
const totalBankBalance = ref(0)
const showBankModal = ref(false)
const showDeleteBankModal = ref(false)
const editBankId = ref(null)
const deleteBankId = ref(null)
const isSavingBank = ref(false)

const bankForm = ref({
  bank_name: '',
  account_number: '',
  account_name: '',
  balance: 0,
})

const fetchBankAccounts = async () => {
  try {
    const res = await fetch('/admin/api/bank-accounts', {
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin'
    })
    if (!res.ok) return
    const json = await res.json()
    if (json.success) {
      const accounts = json.data || []
      // Sort by created_at ascending to keep position stable
      accounts.sort((a: any, b: any) => (a.created_at || '').localeCompare(b.created_at || ''))
      bankAccounts.value = accounts
      totalBankBalance.value = json.total_balance || 0
    }
  } catch (err) {
    console.error('Error fetching bank accounts', err)
  }
}

const openBankModal = (bank = null) => {
  if (bank) {
    editBankId.value = bank.id
    bankForm.value = {
      bank_name: bank.bank_name,
      account_number: bank.account_number,
      account_name: bank.account_name,
      balance: Number(bank.balance),
    }
  } else {
    editBankId.value = null
    bankForm.value = {
      bank_name: '',
      account_number: '',
      account_name: '',
      balance: 0,
    }
  }
  showBankModal.value = true
}

const closeBankModal = () => {
  showBankModal.value = false
  editBankId.value = null
}

const saveBank = async () => {
  if (!bankForm.value.bank_name || bankForm.value.balance === '') {
    toast.error('Nama Bank dan Saldo wajib diisi')
    return
  }

  isSavingBank.value = true
  try {
    const url = editBankId.value 
      ? `/admin/api/bank-accounts/${editBankId.value}`
      : '/admin/api/bank-accounts'
    
    const method = editBankId.value ? 'PUT' : 'POST'
    
    const csrf = await getCsrfTokenSafe()
    const res = await fetch(url, {
      method,
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': csrf,
      },
      credentials: 'same-origin',
      body: JSON.stringify(bankForm.value),
    })

    const json = await res.json()
    if (json.success) {
      toast.success(json.message)
      closeBankModal()
      fetchBankAccounts()
    } else {
      toast.error(json.message || 'Gagal menyimpan rekening')
    }
  } catch (err) {
    console.error('Error saving bank', err)
    toast.error('Terjadi kesalahan sistem')
  } finally {
    isSavingBank.value = false
  }
}

const confirmDeleteBank = (bank) => {
  deleteBankId.value = bank.id
  showDeleteBankModal.value = true
}

const deleteBank = async () => {
  if (!deleteBankId.value) return
  
  try {
    const csrf = await getCsrfTokenSafe()
    const res = await fetch(`/admin/api/bank-accounts/${deleteBankId.value}`, {
      method: 'DELETE',
      headers: { 
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': csrf,
      },
      credentials: 'same-origin'
    })
    
    const json = await res.json()
    if (json.success) {
      toast.success(json.message)
      showDeleteBankModal.value = false
      deleteBankId.value = null
      fetchBankAccounts()
    } else {
      toast.error(json.message || 'Gagal menghapus rekening')
    }
  } catch (err) {
    console.error(err)
    toast.error('Gagal menghapus rekening')
  }
}



// NEW: Fetch program breakdown
const fetchProgramBreakdown = async () => {
  try {
    isLoadingProgramBreakdown.value = true
    const params = new URLSearchParams()
    params.append('start', balanceStart.value)
    params.append('end', balanceEnd.value)
    if (selectedProgram.value) params.append('program_id', selectedProgram.value)
    if (selectedKantor.value) params.append('kantor_cabang_id', selectedKantor.value)

    const res = await fetch(`/admin/api/laporan/keuangan/program-breakdown?${params.toString()}`, {
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      },
      credentials: 'same-origin',
    })

    if (!res.ok) {
      console.error('Program breakdown fetch failed:', res.status, res.statusText)
      return
    }
    const json = await res.json()
    console.log('Program breakdown response:', json)
    if (json.success) {
      programBreakdownData.value = json.data || []
      console.log('Program breakdown data loaded:', programBreakdownData.value.length, 'items')
    }
  } catch (err) {
    console.error('Error fetching program breakdown', err)
  } finally {
    isLoadingProgramBreakdown.value = false
  }
}




// NEW: Fetch penyaluran by alias
const fetchPenyaluranByAlias = async () => {
  try {
    isLoadingPenyaluranByAlias.value = true
    const params = new URLSearchParams()
    params.append('start', balanceStart.value)
    params.append('end', balanceEnd.value)
    if (selectedProgram.value) params.append('program_id', selectedProgram.value)
    if (selectedKantor.value) params.append('kantor_cabang_id', selectedKantor.value)

    const res = await fetch(`/admin/api/laporan/keuangan/penyaluran-by-alias?${params.toString()}`, {
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      },
      credentials: 'same-origin',
    })

    if (!res.ok) return
    const json = await res.json()
    if (json.success) {
      penyaluranByAliasData.value = json.data || { total: 0, breakdown: [] }
    }
  } catch (err) {
    console.error('Error fetching penyaluran by alias', err)
  } finally {
    isLoadingPenyaluranByAlias.value = false
  }
}

// NEW: Fetch expense type breakdown
const fetchExpenseTypeBreakdown = async () => {
  try {
    isLoadingExpenseTypeBreakdown.value = true
    const params = new URLSearchParams()
    params.append('start', balanceStart.value)
    params.append('end', balanceEnd.value)
    if (selectedProgram.value) params.append('program_id', selectedProgram.value)
    if (selectedKantor.value) params.append('kantor_cabang_id', selectedKantor.value)

    const res = await fetch(`/admin/api/laporan/keuangan/expense-type-breakdown?${params.toString()}`, {
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      },
      credentials: 'same-origin',
    })

    if (!res.ok) return
    const json = await res.json()
    if (json.success) {
      expenseTypeBreakdownData.value = json.data || []
    }
  } catch (err) {
    console.error('Error fetching expense type breakdown', err)
  } finally {
    isLoadingExpenseTypeBreakdown.value = false
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
    fetchProgramBreakdown()
    fetchPenyaluranByAlias()
    fetchExpenseTypeBreakdown()
    fetchAllocationSummary()
    fetchBankAccounts()
  }
})

onMounted(() => {
  if (activeTab.value === 'balance') {
    balanceRange.value = [balanceStart.value, balanceEnd.value]
    fetchBalanceData(1)
    fetchPrograms()
    fetchKantorCabangs()
    fetchProgramBreakdown()
    fetchPenyaluranByAlias()
    fetchExpenseTypeBreakdown()
    fetchAllocationSummary()
    fetchBankAccounts()
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

// NEW: Export program breakdown
const handleExportProgramBreakdown = () => {
  const data = programBreakdownData.value.map((p) => ({
    'Program': p.nama,
    'Dana Siap Salur': formatCurrency(p.pemasukan || 0),
    'Pengajuan Dana': formatCurrency(p.pengajuan_dana || 0),
    'Penyaluran': formatCurrency(p.penyaluran || 0),
    'Selisih': formatCurrency(p.selisih || 0),
    'Saldo': formatCurrency(p.saldo || 0),
  }))
  
  const ws = XLSX.utils.json_to_sheet(data)
  const wb = XLSX.utils.book_new()
  XLSX.utils.book_append_sheet(wb, ws, 'Breakdown Program')
  const filename = `Breakdown_Program_${balanceStart.value}_${balanceEnd.value}.xlsx`
  XLSX.writeFile(wb, filename)
}

// NEW: Export filtered transactions
const handleExportTransactions = () => {
  const data = filteredTransactions.value.map((tx) => ({
    'Tanggal': tx.tanggal,
    'Keterangan': tx.keterangan,
    'Tipe': tx.masuk > 0 ? 'Pemasukan' : 'Pengeluaran',
    'Masuk': tx.masuk > 0 ? formatCurrency(tx.masuk) : '-',
    'Keluar': tx.keluar > 0 ? formatCurrency(tx.keluar) : '-',
    'Saldo': formatCurrency(tx.saldo),
  }))
  
  const ws = XLSX.utils.json_to_sheet(data)
  const wb = XLSX.utils.book_new()
  XLSX.utils.book_append_sheet(wb, ws, 'Detail Transaksi')
  const filterLabel = transactionFilters.find(f => f.value === activeTransactionFilter.value)?.label || 'Semua'
  const filename = `Transaksi_${filterLabel}_${balanceStart.value}_${balanceEnd.value}.xlsx`
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

