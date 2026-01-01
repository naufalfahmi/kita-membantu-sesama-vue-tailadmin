<template>
  <AdminLayout>
    <div class="min-h-screen rounded-2xl border border-gray-200 bg-white px-5 py-7 pb-24 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12">
      <div class="mb-6 flex items-center justify-between">
        <h3 class="font-semibold text-gray-800 text-theme-xl dark:text-white/90 sm:text-2xl">Edit Payroll Record</h3>
        <div class="flex items-center gap-3">
          <!-- Save button moved to sticky footer -->
        </div>
      </div>

      <div class="mb-4">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium">Karyawan</label>
            <div>{{ record.employee?.name }}</div>
          </div>
          <div>
            <label class="block text-sm font-medium">No. Induk</label>
            <div>{{ record.employee?.no_induk || '-' }}</div>
          </div>

          <div>
            <label class="block text-sm font-medium">Periode</label>
            <div>{{ formatPeriod(record.period) }}</div>
          </div>        </div>
      </div>

      <div>
        <!-- Penghasilan -->
        <div class="mb-6 rounded border border-gray-200 p-4 bg-white dark:bg-white/[0.03]">
          <div class="flex items-center justify-between mb-3">
            <h4 class="font-medium">Penghasilan</h4>
            <div class="flex items-center gap-2">
              <div class="text-sm text-gray-500">Total: <span class="font-medium">{{ formatCurrency(mainTotal) }}</span></div>
              <button @click="addItem('penghasilan')" class="rounded bg-gray-100 px-3 py-1 text-sm">Tambah Item</button>
            </div>
          </div>

          <table class="w-full table-auto border-collapse">
            <thead>
              <tr>
                <th class="text-left p-2">#</th>
                <th class="text-left p-2">Uraian</th>
                <th class="text-left p-2">Qty</th>
                <th class="sr-only">Tipe</th>
                <th class="text-left p-2 w-24">Satuan</th>
                <th class="text-left p-2">Nilai Satuan</th>
                <th class="text-left p-2">Jumlah</th>
                <th class="text-left p-2">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, idx) in items.filter(i => getGroup(i) === 'penghasilan')" :key="item.id || item._tempId">
                <td class="p-2">{{ idx + 1 }}</td>
                <td class="p-2"><input v-model="item.description" class="w-full rounded border p-2"/></td>
                <td class="p-2"><input v-model.number="item.qty" type="number" step="1" min="0" class="w-full rounded border p-2"/></td>
                <td class="p-2">
                  <select v-model="item.qty_type" class="hidden">
                    <option value="percent">Percent</option>
                    <option value="multiplier">Multiplier</option>
                  </select>
                </td>
                <td class="p-2"><input v-model="item.unit" @input="onUnitChange(item)" class="w-full rounded border p-2"/></td>
                <td class="p-2"><input v-model.number="item.unit_value" type="number" step="1" min="0" class="w-full rounded border p-2"/>
                  
                </td>
                <td class="p-2">
                  <div class="">{{ formatCurrency(computeItemAmount(item)) }}</div>
                  
                </td>
                <td class="p-2 relative">
                  <div class="flex items-center justify-end">
                    <button @click.stop="toggleSettings(item, $event)" class="p-1 rounded hover:bg-gray-100" title="Ubah tipe">
                      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09c.7 0 1.26-.41 1.51-1a1.65 1.65 0 0 0-.33-1.82l-.06-.06A2 2 0 0 1 7.4 2.34l.06.06a1.65 1.65 0 0 0 1.82.33h.09C10.3 2.6 10.8 2 12 2s1.7.6 1.83 1.73h.09c.7 0 1.26.41 1.51 1a1.65 1.65 0 0 0 1.82.33l.06-.06A2 2 0 0 1 20.6 6.6l-.06.06a1.65 1.65 0 0 0-.33 1.82c.13.63.63 1.23 1.46 1.23H21a2 2 0 0 1 0 4h-.09c-.83 0-1.33.6-1.46 1.23z"></path></svg>
                    </button>

                    <div v-if="settingsOpen === (item.id || item._tempId)" class="absolute right-0 mt-2 w-40 bg-white border rounded shadow p-2 z-50">
                      <div class="text-sm font-medium mb-1">Tipe</div>
                      <div class="flex flex-col gap-1">
                        <button @click="() => { setType(item,'percent'); settingsOpen = null }" :class="['text-sm text-left p-1 hover:bg-gray-50', item.qty_type==='percent' ? 'bg-gray-100 font-semibold' : '']">Percent</button>
                        <button @click="() => { setType(item,'multiplier'); settingsOpen = null }" :class="['text-sm text-left p-1 hover:bg-gray-50', item.qty_type==='multiplier' ? 'bg-gray-100 font-semibold' : '']">Multiplier</button>
                      </div>
                      <div class="border-t mt-2 pt-2">
                        <button @click="(e) => { e.stopPropagation(); removeItem(item); settingsOpen = null }" class="text-sm text-left p-1 text-red-600 hover:bg-red-50">Hapus Item</button>
                      </div>
                    </div>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Fundraising -->
        <div class="mb-6 rounded border border-gray-200 p-4 bg-white dark:bg-white/[0.03]">
          <div class="flex items-center justify-between mb-3">
            <h4 class="font-medium">Fundraising</h4>
            <div class="flex items-center gap-2"><div class="text-sm text-gray-500">Total: <span class="font-medium">{{ formatCurrency(fundraisingTotal) }}</span></div><button @click="addItem('fundraising')" class="rounded bg-gray-100 px-3 py-1 text-sm">Tambah Item</button></div>
          </div>

          <table class="w-full table-auto border-collapse">
            <thead>
              <tr>
                <th class="text-left p-2">#</th>
                <th class="text-left p-2">Uraian</th>
                <th class="text-left p-2">Qty</th>
                <th class="sr-only">Tipe</th>
                <th class="text-left p-2 w-24">Satuan</th>
                <th class="text-left p-2">Nilai Satuan</th>
                <th class="text-left p-2">Jumlah</th>
                <th class="text-left p-2">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, idx) in items.filter(i => getGroup(i) === 'fundraising')" :key="item.id || item._tempId">
                <td class="p-2">{{ idx + 1 }}</td>
                <td class="p-2"><input v-model="item.description" class="w-full rounded border p-2"/></td>
                <td class="p-2"><input v-model.number="item.qty" type="number" step="1" min="0" class="w-full rounded border p-2"/></td>
                <td class="p-2">
                  <select v-model="item.qty_type" class="hidden">
                    <option value="percent">Percent</option>
                    <option value="multiplier">Multiplier</option>
                  </select>
                </td>
                <td class="p-2"><input v-model="item.unit" @input="onUnitChange(item)" class="w-full rounded border p-2"/></td>
                <td class="p-2"><input v-model.number="item.unit_value" type="number" class="w-full rounded border p-2"/>
                  
                </td>
                <td class="p-2">
                  <div class="">{{ formatCurrency(computeItemAmount(item)) }}</div>
                  
                </td>
                <td class="p-2 relative">
                  <div class="flex items-center justify-end">
                    <button @click.stop="toggleSettings(item, $event)" class="p-1 rounded hover:bg-gray-100" title="Ubah tipe">
                      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09c.7 0 1.26-.41 1.51-1a1.65 1.65 0 0 0-.33-1.82l-.06-.06A2 2 0 0 1 7.4 2.34l.06.06a1.65 1.65 0 0 0 1.82.33h.09C10.3 2.6 10.8 2 12 2s1.7.6 1.83 1.73h.09c.7 0 1.26.41 1.51 1a1.65 1.65 0 0 0 1.82.33l.06-.06A2 2 0 0 1 20.6 6.6l-.06.06a1.65 1.65 0 0 0-.33 1.82c.13.63.63 1.23 1.46 1.23H21a2 2 0 0 1 0 4h-.09c-.83 0-1.33.6-1.46 1.23z"></path></svg>
                    </button>

                    <div v-if="settingsOpen === (item.id || item._tempId)" class="absolute right-0 mt-2 w-40 bg-white border rounded shadow p-2 z-50">
                      <div class="text-sm font-medium mb-1">Tipe</div>
                      <div class="flex flex-col gap-1">
                        <button @click="() => { setType(item,'percent'); settingsOpen = null }" :class="['text-sm text-left p-1 hover:bg-gray-50', item.qty_type==='percent' ? 'bg-gray-100 font-semibold' : '']">Percent</button>
                        <button @click="() => { setType(item,'multiplier'); settingsOpen = null }" :class="['text-sm text-left p-1 hover:bg-gray-50', item.qty_type==='multiplier' ? 'bg-gray-100 font-semibold' : '']">Multiplier</button>
                      </div>
                      <div class="border-t mt-2 pt-2">
                        <button @click="(e) => { e.stopPropagation(); removeItem(item); settingsOpen = null }" class="text-sm text-left p-1 text-red-600 hover:bg-red-50">Hapus Item</button>
                      </div>
                    </div>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Lain-lain -->
        <div class="mb-6 rounded border border-gray-200 p-4 bg-white dark:bg-white/[0.03]">
          <div class="flex items-center justify-between mb-3">
            <h4 class="font-medium">Lain-lain</h4>
            <div class="flex items-center gap-2"><div class="text-sm text-gray-500">Total: <span class="font-medium">{{ formatCurrency(otherTotal) }}</span></div><button @click="addItem('other')" class="rounded bg-gray-100 px-3 py-1 text-sm">Tambah Item</button></div>
          </div>

          <table class="w-full table-auto border-collapse">
            <thead>
              <tr>
                <th class="text-left p-2">#</th>
                <th class="text-left p-2">Uraian</th>
                <th class="text-left p-2">Qty</th>
                <th class="sr-only">Tipe</th>
                <th class="text-left p-2 w-24">Satuan</th>
                <th class="text-left p-2">Nilai Satuan</th>
                <th class="text-left p-2">Jumlah</th>
                <th class="text-left p-2">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, idx) in items.filter(i => getGroup(i) === 'other')" :key="item.id || item._tempId">
                <td class="p-2">{{ idx + 1 }}</td>
                <td class="p-2"><input v-model="item.description" class="w-full rounded border p-2"/></td>
                <td class="p-2"><input v-model.number="item.qty" type="number" step="1" min="0" class="w-full rounded border p-2"/></td>
                <td class="p-2">
                  <select v-model="item.qty_type" class="hidden">
                    <option value="percent">Percent</option>
                    <option value="multiplier">Multiplier</option>
                  </select>
                </td>
                <td class="p-2"><input v-model="item.unit" @input="onUnitChange(item)" class="w-full rounded border p-2"/></td>
                <td class="p-2"><input v-model.number="item.unit_value" type="number" class="w-full rounded border p-2"/>
                  
                </td>
                <td class="p-2">
                  <div class="">{{ formatCurrency(computeItemAmount(item)) }}</div>
                  
                </td>
                <td class="p-2 relative">
                  <div class="flex items-center justify-end">
                    <button @click.stop="toggleSettings(item, $event)" class="p-1 rounded hover:bg-gray-100" title="Ubah tipe">
                      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09c.7 0 1.26-.41 1.51-1a1.65 1.65 0 0 0-.33-1.82l-.06-.06A2 2 0 0 1 7.4 2.34l.06.06a1.65 1.65 0 0 0 1.82.33h.09C10.3 2.6 10.8 2 12 2s1.7.6 1.83 1.73h.09c.7 0 1.26.41 1.51 1a1.65 1.65 0 0 0 1.82.33l.06-.06A2 2 0 0 1 20.6 6.6l-.06.06a1.65 1.65 0 0 0-.33 1.82c.13.63.63 1.23 1.46 1.23H21a2 2 0 0 1 0 4h-.09c-.83 0-1.33.6-1.46 1.23z"></path></svg>
                    </button>

                    <div v-if="settingsOpen === (item.id || item._tempId)" class="absolute right-0 mt-2 w-40 bg-white border rounded shadow p-2 z-50">
                      <div class="text-sm font-medium mb-1">Tipe</div>
                      <div class="flex flex-col gap-1">
                        <button @click="() => { setType(item,'percent'); settingsOpen = null }" :class="['text-sm text-left p-1 hover:bg-gray-50', item.qty_type==='percent' ? 'bg-gray-100 font-semibold' : '']">Percent</button>
                        <button @click="() => { setType(item,'multiplier'); settingsOpen = null }" :class="['text-sm text-left p-1 hover:bg-gray-50', item.qty_type==='multiplier' ? 'bg-gray-100 font-semibold' : '']">Multiplier</button>
                      </div>
                      <div class="border-t mt-2 pt-2">
                        <button @click="(e) => { e.stopPropagation(); removeItem(item); settingsOpen = null }" class="text-sm text-left p-1 text-red-600 hover:bg-red-50">Hapus Item</button>
                      </div>
                    </div>
                  </div>
                </td>              </tr>            </tbody>
          </table>
        </div>

        <!-- Potongan -->
        <div class="mb-6 rounded border border-gray-200 p-4 bg-white dark:bg-white/[0.03]">
          <div class="flex items-center justify-between mb-3">
            <h4 class="font-medium">Potongan</h4>
            <div class="flex items-center gap-2"><div class="text-sm text-gray-500">Total: <span class="font-medium">{{ formatCurrency(deductionsTotal) }}</span></div><button @click="addItem('deduction')" class="rounded bg-gray-100 px-3 py-1 text-sm">Tambah Item</button></div>
          </div>

          <table class="w-full table-auto border-collapse">
            <thead>
              <tr>
                <th class="text-left p-2">#</th>
                <th class="text-left p-2">Uraian</th>
                <th class="text-left p-2">Qty</th>
                <th class="sr-only">Tipe</th>
                <th class="text-left p-2 w-24">Satuan</th>
                <th class="text-left p-2">Nilai Satuan</th>
                <th class="text-left p-2">Jumlah</th>
                <th class="text-left p-2">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, idx) in items.filter(i => getGroup(i) === 'deduction')" :key="item.id || item._tempId">
                <td class="p-2">{{ idx + 1 }}</td>
                <td class="p-2"><input v-model="item.description" class="w-full rounded border p-2"/></td>
                <td class="p-2"><input v-model.number="item.qty" type="number" step="1" min="0" class="w-full rounded border p-2"/></td>
                <td class="p-2">
                  <select v-model="item.qty_type" class="hidden">
                    <option value="percent">Percent</option>
                    <option value="multiplier">Multiplier</option>
                  </select>
                </td>
                <td class="p-2"><input v-model="item.unit" @input="onUnitChange(item)" class="w-full rounded border p-2"/></td>
                <td class="p-2"><input v-model.number="item.unit_value" type="number" class="w-full rounded border p-2"/>
                  
                </td>
                <td class="p-2">
                  <div class="">{{ formatCurrency(computeItemAmount(item)) }}</div>
                  
                </td>
                <td class="p-2 relative">
                  <div class="flex items-center justify-end">
                    <button @click.stop="toggleSettings(item, $event)" class="p-1 rounded hover:bg-gray-100" title="Ubah tipe">
                      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09c.7 0 1.26-.41 1.51-1a1.65 1.65 0 0 0-.33-1.82l-.06-.06A2 2 0 0 1 7.4 2.34l.06.06a1.65 1.65 0 0 0 1.82.33h.09C10.3 2.6 10.8 2 12 2s1.7.6 1.83 1.73h.09c.7 0 1.26.41 1.51 1a1.65 1.65 0 0 0 1.82.33l.06-.06A2 2 0 0 1 20.6 6.6l-.06.06a1.65 1.65 0 0 0-.33 1.82c.13.63.63 1.23 1.46 1.23H21a2 2 0 0 1 0 4h-.09c-.83 0-1.33.6-1.46 1.23z"></path></svg>
                    </button>

                    <div v-if="settingsOpen === (item.id || item._tempId)" class="absolute right-0 mt-2 w-40 bg-white border rounded shadow p-2 z-50">
                      <div class="text-sm font-medium mb-1">Tipe</div>
                      <div class="flex flex-col gap-1">
                        <button @click="() => { setType(item,'percent'); settingsOpen = null }" :class="['text-sm text-left p-1 hover:bg-gray-50', item.qty_type==='percent' ? 'bg-gray-100 font-semibold' : '']">Percent</button>
                        <button @click="() => { setType(item,'multiplier'); settingsOpen = null }" :class="['text-sm text-left p-1 hover:bg-gray-50', item.qty_type==='multiplier' ? 'bg-gray-100 font-semibold' : '']">Multiplier</button>
                      </div>
                      <div class="border-t mt-2 pt-2">
                        <button @click="(e) => { e.stopPropagation(); removeItem(item); settingsOpen = null }" class="text-sm text-left p-1 text-red-600 hover:bg-red-50">Hapus Item</button>
                      </div>
                    </div>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="mt-4 rounded border border-gray-200 p-4 bg-white dark:bg-white/[0.03]">
          <div class="text-sm text-gray-700">
            <div class="flex justify-between"><div>Total Penghasilan</div><div class="font-semibold">{{ formatCurrency(mainTotal) }}</div></div>
            <div class="flex justify-between"><div>Total Fundraising</div><div class="font-semibold">{{ formatCurrency(fundraisingTotal) }}</div></div>
            <div class="flex justify-between"><div>Total Lain-lain</div><div class="font-semibold">{{ formatCurrency(otherTotal) }}</div></div>
            <div class="flex justify-between"><div>Total Potongan</div><div class="font-semibold">{{ formatCurrency(deductionsTotal) }}</div></div>
            <div class="border-t mt-2 mb-2"></div>
            <div class="flex justify-between"><div class="font-semibold text-lg">Take Home Pay</div><div class="font-semibold text-lg">{{ formatCurrency(takeHome) }}</div></div>
          </div>
        </div>

        <!-- Sticky footer with totals / status / actions -->
        <div class="sticky bottom-0 left-0 right-0 z-40 bg-white/90 border-t dark:bg-white/[0.03]">
          <div class="max-w-6xl mx-auto px-5 py-3 flex items-center justify-between gap-4">
            <div class="flex-1">
              <div class="text-sm text-gray-500">Take Home: <span class="font-semibold">{{ formatCurrency(takeHome) }}</span></div>
            </div>

            <div class="flex items-center gap-3">
                  <div class="flex items-center gap-3">
                

                <select v-model="record.status" class="h-11 rounded-lg border px-4 py-2 text-sm text-gray-800">
                  <option value="pending">pending</option>
                  <option value="locked">locked</option>
                  <option value="transferred">transferred</option>
                </select>

                <button @click="saveAll" class="rounded bg-brand-500 px-4 py-2 text-white">Simpan</button>
                <button @click="router.back()" class="rounded border px-4 py-2">Kembali</button>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import { useRoute, useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'

const route = useRoute()
const router = useRouter()
const toast = useToast()

const record = ref<any>({})
const items = ref<any[]>([])

const months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']

const formatPeriod = (p: any) => {
  if (!p) return '-'
  const m = p.month ? months[(p.month || 1) - 1] : ''
  const y = p.year || ''
  const s = p.status ? ` · ${p.status}` : ''
  return `${m} ${y}${s}`
}

const getCsrfToken = async (): Promise<string> => {
  const meta = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
  if (meta) return meta
  const res = await fetch('/admin/api/csrf-token', { credentials: 'same-origin' })
  const json = await res.json()
  return json.csrf_token || ''
}

const loadRecord = async () => {
  const periodId = route.params.periodId
  const recordId = route.params.recordId
  const res = await fetch(`/admin/api/operasional/payroll/periods/${periodId}/records/${recordId}`, { credentials: 'same-origin' })
  const json = await res.json()
  if (!json.success) { toast.error('Gagal memuat data'); return }
  record.value = json.data
  // ensure a default status exists
  if (!record.value.status) record.value.status = 'pending'
  items.value = (json.data.items || []).map((it: any) => ({
    ...it,
    qty: Number(it.qty),
    unit_value: Math.abs(Number(it.unit_value)),
    qty_type: it.qty_type === 'fixed' ? 'multiplier' : it.qty_type
  }))
}

onMounted(async () => { await loadRecord(); ensureDefaultItems() })

const ensureDefaultItems = () => {
  // ensure a consistent set of default items exist (case-insensitive match)
  const defaults = [
    // Penghasilan
    { description: 'Gaji Pokok', qty: 0, qty_type: 'multiplier', unit: 'bulan', unit_value: 0, group: 'penghasilan' },
    { description: 'Uang Makan', qty: 0, qty_type: 'multiplier', unit: 'bulan', unit_value: 0, group: 'penghasilan' },
    { description: 'Uang Transport', qty: 0, qty_type: 'multiplier', unit: 'bulan', unit_value: 0, group: 'penghasilan' },
    // Fundraising
    { description: 'Dana Kolekan', qty: 0, qty_type: 'multiplier', unit: '', unit_value: 0, group: 'fundraising' },
    { description: 'Kotak Home', qty: 0, qty_type: 'multiplier', unit: '', unit_value: 0, group: 'fundraising' },
    { description: 'Kotak Publik', qty: 0, qty_type: 'multiplier', unit: '', unit_value: 0, group: 'fundraising' },
    { description: 'Qurban', qty: 0, qty_type: 'multiplier', unit: '', unit_value: 0, group: 'fundraising' },
    // Lain-lain
    { description: 'Tunjangan Jabatan', qty: 0, qty_type: 'multiplier', unit: '', unit_value: 0, group: 'other' },
    { description: 'THR', qty: 0, qty_type: 'multiplier', unit: '', unit_value: 0, group: 'other' },
    { description: 'Perjalanan Dinas', qty: 0, qty_type: 'multiplier', unit: '', unit_value: 0, group: 'other' },
    { description: 'Lembur', qty: 0, qty_type: 'multiplier', unit: '', unit_value: 0, group: 'other' },
    // Potongan
    { description: 'Tidak Masuk', qty: 0, qty_type: 'multiplier', unit: '', unit_value: 0, group: 'deduction' },
    { description: 'Terlambat', qty: 0, qty_type: 'multiplier', unit: '', unit_value: 0, group: 'deduction' },
  ]

  for (const d of defaults) {
    const exists = items.value.some(i => (i.description || '').toLowerCase() === d.description.toLowerCase())
    if (!exists) {
      items.value.push({ id: null, _tempId: String(Date.now()) + Math.random().toString(36).substr(2,5), description: d.description, qty: d.qty, qty_type: d.qty_type, unit: d.unit, unit_value: d.unit_value, amount: 0, group: d.group })
    }
  }
}



const addItem = (group: string = 'penghasilan') => {
  items.value.push({ id: null, _tempId: String(Date.now()) + Math.random().toString(36).substr(2,5), description: '', qty: 0, qty_type: 'multiplier', unit: '', unit_value: 0, amount: 0, group })
} 

// Preset helpers removed — defaults will be added automatically on load

// baseOptions removed — percent now uses item's own unit_value (Nilai Satuan)

const removeItem = async (it: any) => {
  if (it.id) {
    // call delete API
    const periodId = route.params.periodId
    const recordId = route.params.recordId
    const csrf = await getCsrfToken()
    const res = await fetch(`/admin/api/operasional/payroll/periods/${periodId}/records/${recordId}/items/${it.id}`, { method: 'DELETE', credentials: 'same-origin', headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' } })
    const json = await res.json()
    if (!json.success) { toast.error('Gagal menghapus item'); return }
    toast.success('Item dihapus')
    loadRecord()
  } else {
    items.value = items.value.filter(x => x !== it)
  }
}

const fileRef = ref<File | null>(null)
const replaceFileInput = ref<HTMLInputElement | null>(null)

const onFileChange = (e: any) => {
  const f = e.target.files && e.target.files[0]
  fileRef.value = f || null
}

const onDeleteProof = async () => {
  if (!confirm('Hapus bukti transfer? Tindakan ini tidak dapat dibatalkan.')) return
  const periodId = route.params.periodId
  const recordId = route.params.recordId
  try {
    const csrf = await getCsrfToken()
    const res = await fetch(`/admin/api/operasional/payroll/periods/${periodId}/records/${recordId}/transfer-proof`, {
      method: 'DELETE',
      headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin'
    })
    const json = await res.json()
    if (!json.success) { toast.error(json.message || 'Gagal menghapus bukti'); return }
    // update UI
    record.value.transfer_proof = null
    toast.success('Bukti transfer dihapus')
  } catch (err) {
    toast.error('Gagal menghapus bukti')
  }
}

const onReplaceProof = () => {
  const input: any = replaceFileInput.value || null
  if (input) input.click()
}

const onReplaceFileChange = async (e: any) => {
  const f = e.target.files && e.target.files[0]
  if (!f) return
  const fd = new FormData()
  fd.append('transfer_proof', f)

  try {
    const periodId = route.params.periodId
    const recordId = route.params.recordId
    const csrf = await getCsrfToken()
    const resp = await fetch(`/admin/api/operasional/payroll/periods/${periodId}/records/${recordId}/transfer-proof`, {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin',
      body: fd
    })
    const json = await resp.json()
    if (!json.success) {
      if (json.errors) {
        const first = Object.keys(json.errors)[0]
        toast.error(json.errors[first][0])
      } else if (json.message) {
        toast.error(json.message)
      } else {
        toast.error('Gagal mengganti bukti')
      }
      return
    }
    // Refresh record
    await loadRecord()
    toast.success('Bukti transfer berhasil diperbarui')
  } catch (err) {
    toast.error('Gagal mengganti bukti')
  }
}

const saveAll = async () => {
  const periodId = route.params.periodId
  const recordId = route.params.recordId

  

  // Otherwise status remains pending — proceed to save existing or new items one by one for simplicity
  const tempMap: Record<string, string> = {}

  // First pass: only attempt to create/update items that have meaningful data
  const candidates = items.value.map((it: any, idx: number) => ({ it, origIndex: idx }))
    .filter(({ it }) => {
      // save if existing (has id) OR has description OR non-zero qty/unit_value
      if (it.id) return true
      if (it.description && (it.description || '').toString().trim() !== '') return true
      if (Number(it.qty) !== 0) return true
      if (Number(it.unit_value) !== 0) return true
      return false
    })

  for (let j = 0; j < candidates.length; j++) {
    const { it, origIndex } = candidates[j]

    const payload: any = {
      description: it.description,
      qty: Math.round(Number(it.qty) || 0),
      qty_type: it.qty_type === 'fixed' ? 'multiplier' : it.qty_type,
      unit: it.unit,
      unit_value: it.unit_value,
      group: it.group || getGroup(it),
      order_index: j
    }

    if (!it.id) {
      const csrf = await getCsrfToken()
      const res = await fetch(`/admin/api/operasional/payroll/periods/${periodId}/records/${recordId}/items`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin',
        body: JSON.stringify(payload)
      })
      const json = await res.json()
      if (!json.success) { toast.error('Gagal menambah item'); return }
      if (it._tempId) tempMap[it._tempId] = json.data.id
      const created = json.data
      items.value[origIndex] = {
        ...created,
        qty: Number(created.qty),
        unit_value: Math.abs(Number(created.unit_value)),
        qty_type: created.qty_type === 'fixed' ? 'multiplier' : created.qty_type
      }
    } else {
      const csrf = await getCsrfToken()
      const res = await fetch(`/admin/api/operasional/payroll/periods/${periodId}/records/${recordId}/items/${it.id}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin',
        body: JSON.stringify(payload)
      })
      const json = await res.json()
      if (!json.success) { toast.error('Gagal mengubah item'); return }
      const updated = json.data
      items.value[origIndex] = {
        ...updated,
        qty: Number(updated.qty),
        unit_value: Math.abs(Number(updated.unit_value)),
        qty_type: updated.qty_type === 'fixed' ? 'multiplier' : updated.qty_type
      }
    }
  }

  // After saving items, update the record (status/notes) if changed
  const csrf2 = await getCsrfToken()
  let res2
  if (fileRef.value) {
    const fd = new FormData()
    fd.append('status', record.value.status)
    fd.append('transfer_proof', fileRef.value)
    res2 = await fetch(`/admin/api/operasional/payroll/periods/${periodId}/records/${recordId}`, {
      method: 'PUT',
      headers: { 'X-CSRF-TOKEN': csrf2, 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin',
      body: fd
    })
  } else {
    res2 = await fetch(`/admin/api/operasional/payroll/periods/${periodId}/records/${recordId}`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf2, 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin',
      body: JSON.stringify({ status: record.value.status })
    })
  }
  const json2 = await res2.json()
  if (!json2.success) { toast.error('Gagal mengubah status'); return }

  // reload record to get updated totals and normalized items
  await loadRecord()
  toast.success('Perubahan tersimpan')
}

const computeItemAmount = (it: any): number => {
  if (it.qty_type === 'fixed' || it.qty_type === 'multiplier') return (Number(it.qty) || 0) * (Number(it.unit_value) || 0)
  if (it.qty_type === 'percent') {
    // percent is calculated from the item's own unit_value
    return ((Number(it.qty) || 0) / 100) * (Number(it.unit_value) || 0)
  }
  return 0
}

const formatCurrency = (v: number) => {
  const value = Number(v || 0)
  const formatted = new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(value)
  return `Rp. ${formatted}`
}

// Group helpers
const getGroup = (it: any): string => {
  if (it.group) return it.group
  const desc = (it.description || '').toLowerCase()
  const peng = ['gaji pokok','uang makan','uang transport']
  const fund = ['dana kolekan','kotak home','kotak publik','qurban']
  const other = ['tunjangan jabatan','thr','perjalanan dinas','lembur']
  const ded = ['tidak masuk','terlambat']

  if (peng.includes(desc)) return 'penghasilan'
  if (fund.includes(desc)) return 'fundraising'
  if (other.includes(desc)) return 'other'
  if (ded.includes(desc)) return 'deduction'
  return 'other'
}

const sumGroup = (groupKey: string) => {
  return items.value.reduce((acc, it) => {
    if (getGroup(it) === groupKey) {
      // Always compute amount from current fields so totals reflect local edits immediately
      const amt = computeItemAmount(it)
      return acc + amt
    }
    return acc
  }, 0)
}

const mainTotal = computed(() => sumGroup('penghasilan'))
const fundraisingTotal = computed(() => sumGroup('fundraising'))
const otherTotal = computed(() => sumGroup('other'))
const deductionsTotal = computed(() => sumGroup('deduction'))
const allTotal = computed(() => Number(mainTotal.value) + Number(fundraisingTotal.value) + Number(otherTotal.value))
const takeHome = computed(() => Number(allTotal.value) - Number(deductionsTotal.value))

// Settings menu state
const settingsOpen = ref<string | null>(null)
const toggleSettings = (item: any, ev?: Event) => {
  if (ev) ev.stopPropagation()
  const id = item.id || item._tempId
  settingsOpen.value = settingsOpen.value === id ? null : id
}
const setType = (item: any, type: string) => {
  // map legacy 'fixed' -> 'multiplier'
  if (type === 'fixed') type = 'multiplier'
  item.qty_type = type
  if (type === 'percent') {
    // ensure unit indicates percentage
    if (!item.unit || item.unit === '') item.unit = '%'
    if (item.unit_value === null || item.unit_value === undefined) item.unit_value = 0
  } else {
    // if switching away from percent, clear percent-specific unit marker
    if (item.unit === '%') item.unit = ''
  }
  settingsOpen.value = null
}

const onUnitChange = (item: any) => {
  const unitStr = (item.unit || '').toString()
  if (unitStr.includes('%')) {
    item.qty_type = 'percent'
    if (!unitStr.trim()) item.unit = '%'
    if (item.unit_value === null || item.unit_value === undefined) item.unit_value = 0
  } else {
    if (item.qty_type === 'percent') {
      item.qty_type = 'multiplier'
      if (item.unit === '%') item.unit = ''
    }
  }
}

const handleDocClick = () => { settingsOpen.value = null }

onMounted(() => { document.addEventListener('click', handleDocClick) })
onUnmounted(() => { document.removeEventListener('click', handleDocClick) })
</script>
