<template>
  <AdminLayout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />
    <div
      class="min-h-screen rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12"
    >
      <div class="mb-6 flex items-center justify-between">
        <h3 class="font-semibold text-gray-800 text-theme-xl dark:text-white/90 sm:text-2xl">
          {{ currentPageTitle }}
        </h3>
        <button
          @click="handleCancel"
          class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]"
        >
          Batal
        </button>
      </div>

      <form @submit.prevent="handleSave" class="flex flex-col">
        <div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2">
          <!-- Nama Karyawan -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Nama Karyawan <span class="text-red-500">*</span>
            </label>
            <SearchableSelect
              v-model="formData.karyawan_id"
              :options="karyawanList"
              placeholder="Pilih atau cari nama karyawan"
              :search-input="karyawanSearchInput"
              @update:search-input="karyawanSearchInput = $event"
              @update:model-value="handleKaryawanChange"
            />
          </div>

          <!-- Bulan -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Bulan <span class="text-red-500">*</span>
            </label>
            <input
              type="number"
              v-model.number="formData.bulan"
              placeholder="Masukkan bulan (1-12)"
              min="1"
              max="12"
              required
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
          </div>

          <!-- Tahun -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Tahun <span class="text-red-500">*</span>
            </label>
            <input
              type="number"
              v-model.number="formData.tahun"
              placeholder="Masukkan tahun"
              min="2000"
              :max="new Date().getFullYear() + 1"
              required
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
          </div>

          <!-- Kantor Cabang -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Kantor Cabang
            </label>
            <SearchableSelect
              v-model="formData.kantorCabang"
              :options="kantorCabangList"
              placeholder="Pilih atau cari kantor cabang"
              :search-input="kantorCabangSearchInput"
              @update:search-input="kantorCabangSearchInput = $event"
            />
          </div>
        </div>

        <!-- Bagian Kalkulasi Gaji -->
        <div class="mt-8 rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
          <h4 class="mb-6 text-lg font-semibold text-gray-800 dark:text-white/90">
            Bagian Kalkulasi Gaji
          </h4>
          
          <div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2">
            <!-- Gaji Pokok - Nominal (Display Only) -->
            <div class="lg:col-span-1">
              <label
                class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
              >
                Gaji Pokok - Nominal
              </label>
              <div
                class="dark:bg-dark-900 h-11 w-full flex items-center rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
              >
                <span class="font-medium">{{ formatCurrency(gajiPokok) }}</span>
              </div>
              <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                Display only (readonly)
              </p>
            </div>
          </div>
        </div>

        <!-- Bagian Bawah - Take Home Pay -->
        <div class="mt-8 rounded-lg border border-gray-200 bg-gray-50 p-6 dark:border-gray-800 dark:bg-gray-900/50">
          <div class="flex items-center justify-between">
            <label
              class="text-lg font-semibold text-gray-800 dark:text-white/90"
            >
              Take Home Pay
            </label>
            <span class="text-xl font-bold text-brand-500 dark:text-brand-400">
              {{ formatCurrency(takeHomePay) }}
            </span>
          </div>
          <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
            Display only
          </p>
        </div>

        <div class="flex items-center gap-3 mt-6 lg:justify-end">
          <button
            @click="handleCancel"
            type="button"
            class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] sm:w-auto"
          >
            Batal
          </button>
          <button
            type="submit"
            class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto"
          >
            {{ isEditMode ? 'Simpan Perubahan' : 'Simpan' }}
          </button>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { reactive, computed, ref, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import SearchableSelect from '@/components/forms/SearchableSelect.vue'

const route = useRoute()
const router = useRouter()
const toast = useToast()

const isEditMode = computed(() => route.params.id !== undefined && route.params.id !== 'new')
const currentPageTitle = computed(() => {
  return isEditMode.value ? 'Edit Remunerasi' : 'Tambah Remunerasi'
})

// Options for select fields (loaded from API)
const karyawanList = ref([])
const kantorCabangList = ref([])

// Search input refs
const karyawanSearchInput = ref('')
const kantorCabangSearchInput = ref('')

const loadOptions = async () => {
  try {
    // load karyawan
    const r1 = await fetch('/admin/api/karyawan?per_page=1000', { credentials: 'same-origin' })
    if (r1.ok) {
      const j1 = await r1.json()
      karyawanList.value = (j1.data || []).map((k: any) => ({ value: String(k.id), label: k.name || k.nama || '-' }))
    }

    // load kantor cabang
    const r2 = await fetch('/admin/api/kantor-cabang?per_page=1000', { credentials: 'same-origin' })
    if (r2.ok) {
      const j2 = await r2.json()
      kantorCabangList.value = (j2.data || []).map((c: any) => ({ value: String(c.id), label: c.nama || c.name || '-' }))
    }
  } catch (e) {
    console.error('Error loading options:', e)
  }
}

// Form data
const formData = reactive({
  karyawan_id: '',
  bulan: null as number | null,
  tahun: null as number | null,
  kantorCabang: '',
  namaKaryawan: '',
})

// Gaji data (will be loaded from API based on selected karyawan)
const gajiPokok = ref(0)

// Handle karyawan change - load gaji data (if available)
const handleKaryawanChange = () => {
  loadGajiPokok()
}

// Load gaji pokok based on selected karyawan
const loadGajiPokok = async () => {
  // Try to load gaji from API if endpoint exists. Fallback to 0.
  gajiPokok.value = 0
  if (!formData.karyawan_id) return

  try {
    const res = await fetch(`/admin/api/gaji?karyawan_id=${formData.karyawan_id}&per_page=1`, { credentials: 'same-origin' })
    if (!res.ok) return
    const j = await res.json()
    const items = j.data || []
    if (items.length > 0) {
      // assume the first item has gaji nominal in field `nominal` or similar
      const g = items[0]
      gajiPokok.value = g.nominal || g.gaji_pokok || 0
    }
  } catch (e) {
    console.error('Error loading gaji pokok:', e)
  }
}

// Calculate Take Home Pay (for now, same as gaji pokok)
// In real implementation, this would calculate based on deductions, allowances, etc.
const takeHomePay = computed(() => {
  // TODO: Implement proper calculation with deductions, allowances, etc.
  return gajiPokok.value
})

// Format currency
const formatCurrency = (amount: number) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(amount)
}

// Load data if edit mode
const loadData = async () => {
  await loadOptions()

  if (isEditMode.value && route.params.id) {
    const id = route.params.id as string
    try {
      const res = await fetch(`/admin/api/operasional/remunerasi/${id}`, { credentials: 'same-origin' })
      if (!res.ok) throw new Error('Failed to load remunerasi')
      const json = await res.json()
      if (json && json.data) {
        const d = json.data
        formData.namaKaryawan = ''
        formData.karyawan_id = d.karyawan_id ? String(d.karyawan_id) : ''
        formData.bulan = d.bulan_remunerasi || null
        formData.tahun = d.tahun_remunerasi || null
        formData.kantorCabang = d.kantor_cabang_id ? String(d.kantor_cabang_id) : ''
        gajiPokok.value = d.gaji_pokok || 0
      }
    } catch (e) {
      console.error('Error loading data for edit:', e)
    }
  } else {
    // not edit - still load options
  }
}

// Handle cancel
const handleCancel = () => {
  router.push('/operasional/remunerasi')
}

// Handle save
const handleSave = async () => {
  if (!formData.karyawan_id) {
    toast.error('Nama Karyawan wajib diisi')
    return
  }

  if (!formData.bulan || formData.bulan < 1 || formData.bulan > 12) {
    toast.error('Bulan harus antara 1-12')
    return
  }

  if (!formData.tahun) {
    toast.error('Tahun wajib diisi')
    return
  }

  try {
    const t = await fetch('/admin/api/csrf-token', { credentials: 'same-origin' })
    const tk = await t.json()

    const payload: any = {
      karyawan_id: formData.karyawan_id ? Number(formData.karyawan_id) : null,
      bulan_remunerasi: formData.bulan,
      tahun_remunerasi: formData.tahun,
      gaji_pokok: gajiPokok.value || null,
      take_home_pay: takeHomePay.value || 0,
      tanggal: null,
      kantor_cabang_id: formData.kantorCabang || null,
    }

    if (formData.tahun) payload.tanggal = `${formData.tahun}-${String(formData.bulan).padStart(2, '0')}-15`

    let res
    if (isEditMode.value) {
      const id = route.params.id as string
      res = await fetch(`/admin/api/operasional/remunerasi/${id}`, {
        method: 'PUT',
        credentials: 'same-origin',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': tk.csrf_token,
        },
        body: JSON.stringify(payload),
      })
    } else {
      res = await fetch('/admin/api/operasional/remunerasi', {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': tk.csrf_token,
        },
        body: JSON.stringify(payload),
      })
    }

    const json = await res.json().catch(() => ({}))
    if (!res.ok) {
      const msg = (json && json.message) || 'Request gagal'
      throw new Error(msg)
    }

    // success
    toast.success(json.message || (isEditMode.value ? 'Remunerasi berhasil diupdate' : 'Remunerasi berhasil ditambahkan'))
    router.push('/operasional/remunerasi')
  } catch (error) {
    console.error('Error saving:', error)
    toast.error('Terjadi kesalahan saat menyimpan data')
  }
}

// Watch for karyawan_id changes to load gaji pokok
watch(() => formData.karyawan_id, () => {
  loadGajiPokok()
})

onMounted(() => {
  loadData()
})
</script>

