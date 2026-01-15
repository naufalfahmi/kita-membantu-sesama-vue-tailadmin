<template>
  <AdminLayout>
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
          <!-- 1. Nama Program -->
          <div class="lg:col-span-2">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Nama Program <span class="text-red-500">*</span>
            </label>
            <input
              type="text"
              v-model="formData.nama_program"
              placeholder="Masukkan nama program"
              required
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
          </div>

          <!-- Dynamic shares table -->
          <div class="lg:col-span-2">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Pembagian (DP, OPS 1, OPS 2, Program, Fee Mitra, Bonus, Championship)
            </label>
            <div class="overflow-x-auto mt-2">
              <table class="w-full table-auto border border-gray-200 dark:border-gray-800 rounded-md">
                <thead class="bg-gray-50 dark:bg-gray-900">
                  <tr>
                    <th class="text-left px-4 py-2 text-sm text-gray-600 dark:text-gray-300">Nama</th>
                    <th class="text-left px-4 py-2 text-sm text-gray-600 dark:text-gray-300">Alias (Tipe Pengajuan)</th>
                    <th class="text-left px-4 py-2 text-sm text-gray-600 dark:text-gray-300">Tipe</th>
                    <th class="text-left px-4 py-2 text-sm text-gray-600 dark:text-gray-300">Nilai</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="type in programShareTypes" :key="type.key" class="border-t border-gray-100 dark:border-gray-800">
                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-400">{{ type.name }}</td>
                    <td class="px-4 py-3">
                      <input 
                        type="text" 
                        v-model="formData.shares[type.key].alias" 
                        :placeholder="type.alias || 'Masukkan alias'"
                        class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300"
                      />
                    </td>
                    <td class="px-4 py-3">
                      <SearchableSelect
                        v-model="formData.shares[type.key].type"
                        :options="shareTypeOptions"
                        placeholder="Pilih Tipe"
                        :search-input="shareSearchInputs[type.key]"
                        @update:search-input="(val: string) => (shareSearchInputs[type.key] = val)"
                      />
                    </td>
                    <td class="px-4 py-3">
                      <div class="flex items-center gap-2">
                        <input type="number" v-model.number="formData.shares[type.key].value" placeholder="0" min="0" step="0.01" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm dark:bg-gray-900" />
                        <span class="text-sm text-gray-600 dark:text-gray-400" v-if="formData.shares[type.key].type === 'percentage'">%</span>
                        <span class="text-sm text-gray-600 dark:text-gray-400" v-else>Rp</span>
                      </div>
                    </td>
                  </tr>

                  <!-- custom rows -->
                  <tr v-for="(row, idx) in formData.customRows" :key="row.key" class="border-t border-gray-100 dark:border-gray-800">
                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-400">
                      <input v-model="row.name" placeholder="Nama" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm" />
                    </td>
                    <td class="px-4 py-3">
                      <SearchableSelect
                        v-model="row.type"
                        :options="shareTypeOptions"
                        placeholder="Pilih Tipe"
                        :search-input="shareSearchInputs[row.key]"
                        @update:search-input="(val: string) => (shareSearchInputs[row.key] = val)"
                      />
                    </td>
                    <td class="px-4 py-3">
                      <div class="flex items-center gap-2">
                        <input type="number" v-model.number="row.value" placeholder="0" min="0" step="0.01" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm dark:bg-gray-900" />
                        <span class="text-sm text-gray-600 dark:text-gray-400" v-if="row.type === 'percentage'">%</span>
                        <span class="text-sm text-gray-600 dark:text-gray-400" v-else>Rp</span>
                        <button type="button" @click.prevent="() => removeCustomRow(idx)" class="text-red-500 ml-2">Hapus</button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Jumlah Persentase (hanya menghitung yang tipe "Persentase"): {{ calculatedJumlahPersentase }}</p>
          </div>
        </div>

        <div class="flex items-center gap-3 mt-6 lg:justify-end">
          <div class="flex-1 lg:flex lg:items-center lg:justify-start">
            <button type="button" @click="addCustomRow" class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
              Tambah Baris
            </button>
          </div>
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

    <!-- pinned percentage button -->
    <div class="fixed right-6 bottom-6 z-50">
      <button :title="calculatedJumlahPersentase > 100 ? 'Jumlah persentase melebihi 100%' : 'Jumlah persentase'" class="flex items-center gap-3 px-4 py-3 rounded-lg text-white shadow-lg" :class="percentageButtonClass">
        <span class="text-sm">Jumlah Persentase</span>
        <span class="text-lg font-semibold">{{ formattedPercentage }}</span>
      </button>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import SearchableSelect from '@/components/forms/SearchableSelect.vue'

// Options for Tipe Pembagian Marketing dropdown
const tipePembagianOptions = [
  { value: 'percentage', label: 'Persentase' },
  { value: 'nominal', label: 'Nominal' },
]
const tipePembagianSearchInput = ref('')

const route = useRoute()
const router = useRouter()
const toast = useToast()

const isEditMode = computed(() => route.params.id !== undefined && route.params.id !== 'new')
const currentPageTitle = computed(() => {
  return isEditMode.value ? 'Edit Program' : 'Tambah Program'
})

// Form data
const formData = reactive({
  nama_program: '',
  tipe_pembagian_marketing: '',
  jumlah_persentase: null as number | null,
  // shares will be an object keyed by program_share_type key
  shares: {} as Record<string, { type: string | null; value: number | null; alias: string | null }>,
  // custom rows added per-program
  customRows: [] as Array<{ key: string; name: string; type: string; value: number | null }> ,
})

// Available program share types loaded from API
const programShareTypes = ref<Array<{ id: string; name: string; key: string; alias: string | null; default_type: string; orders?: number }>>([])

// per-row search inputs for SearchableSelect
const shareSearchInputs = reactive<Record<string, string>>({})

// options for per-share type select
const shareTypeOptions = [
  { value: 'percentage', label: 'Persentase' },
  { value: 'nominal', label: 'Nominal' },
]

// Computed untuk menghitung total jumlah persentase dari semua shares yang bertipe 'percentage'
const calculatedJumlahPersentase = computed(() => {
  let total = 0
  for (const key of Object.keys(formData.shares)) {
    const s = formData.shares[key]
    if (s && s.type === 'percentage' && s.value !== null && !Number.isNaN(s.value)) {
      total += Number(s.value)
    }
  }
  return total > 0 ? parseFloat(total.toFixed(2)) : 0
})

// Watch untuk update jumlah_persentase otomatis saat ada perubahan
// Update jumlah_persentase whenever shares change
watch(() => calculatedJumlahPersentase.value, (val) => {
  formData.jumlah_persentase = val
}, { immediate: true })

// formatted percentage display (e.g., "63.16%" or "100%" for whole numbers)
const formattedPercentage = computed(() => {
  const v = Number(calculatedJumlahPersentase.value || 0)
  // show without decimals for whole numbers (e.g., 100 -> "100%")
  if (Number.isInteger(v)) return `${v}%`
  return v.toFixed(2) + '%'
})

// dynamic button class: red if over 100%, otherwise brand color
const percentageButtonClass = computed(() => {
  const v = Number(calculatedJumlahPersentase.value || 0)
  return v > 100 ? 'bg-red-500 hover:bg-red-600' : 'bg-brand-500 hover:bg-brand-600'
})

// Fetch available program share types
const fetchProgramShareTypes = async () => {
  try {
    const response = await fetch('/admin/api/program-share-types', { credentials: 'same-origin' })
    if (!response.ok) return
    const res = await response.json()
    if (res.success && Array.isArray(res.data)) {
      // sort by orders ascending (lowest first)
      programShareTypes.value = res.data.slice().sort((a: any, b: any) => {
        const ao = a.orders ?? 0
        const bo = b.orders ?? 0
        return ao - bo
      })
      // initialize shares structure with defaults and search inputs
      for (const t of programShareTypes.value) {
        if (!formData.shares[t.key]) {
          formData.shares[t.key] = { type: t.default_type || 'percentage', value: null, alias: t.alias || null }
        }
        if (shareSearchInputs[t.key] === undefined) {
          shareSearchInputs[t.key] = ''
        }
      }
      // ensure customRows exists
      if (!Array.isArray(formData.customRows)) {
        formData.customRows = []
      }
    }
  } catch (e) {
    console.error('Failed to load program share types:', e)
  }
}

// Get CSRF token
const getCsrfToken = async (): Promise<string> => {
  try {
    // Try to get from meta tag first
    const metaToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    if (metaToken) {
      return metaToken
    }
    
    // If not available, fetch from API
    const response = await fetch('/admin/api/csrf-token', {
      method: 'GET',
      credentials: 'same-origin',
    })
    
    if (!response.ok) {
      throw new Error(`Failed to get CSRF token: ${response.status}`)
    }
    
    const data = await response.json()
    return data.csrf_token || ''
  } catch (e) {
    console.error('Failed to get CSRF token:', e)
    return ''
  }
}

// add a new custom row
const addCustomRow = () => {
  const key = `custom_${Date.now()}`
  formData.customRows.push({ key, name: '', type: 'percentage', value: null })
  shareSearchInputs[key] = ''
}

const removeCustomRow = (idx: number) => {
  formData.customRows.splice(idx, 1)
}

// Load data if edit mode
const loadData = async () => {
  if (isEditMode.value && route.params.id) {
    const id = route.params.id as string
    try {
      const csrfToken = await getCsrfToken()
      const response = await fetch(`/admin/api/program/${id}`, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
        },
        credentials: 'same-origin',
      })
      
      const result = await response.json()
      if (result.success && result.data) {
        const data = result.data
        formData.nama_program = data.nama_program || ''
        formData.tipe_pembagian_marketing = data.tipe_pembagian_marketing || ''
        formData.jumlah_persentase = data.jumlah_persentase ? parseFloat(data.jumlah_persentase) : null

        // map shares from API if available
        if (Array.isArray(data.shares)) {
          formData.customRows = []
          for (const s of data.shares) {
            const key = s.program_share_type_key || (s.program_share_type_id ? programShareTypes.value.find((t) => t.id === s.program_share_type_id)?.key : null)
            if (key && formData.shares[key] !== undefined) {
              formData.shares[key] = { type: s.type || 'percentage', value: s.value !== null ? parseFloat(s.value) : null, alias: s.alias || null }
            } else {
              // treat as custom row (program-specific)
              const rkey = s.program_share_type_key || `custom_${Date.now()}_${Math.floor(Math.random()*1000)}`
              formData.customRows.push({ key: rkey, name: s.name || '', type: s.type || 'percentage', value: s.value !== null ? parseFloat(s.value) : null })
              shareSearchInputs[rkey] = ''
            }
          }
        }
      } else {
        toast.error('Gagal memuat data program')
        router.push('/administrasi/program')
      }
    } catch (error) {
      console.error('Error loading data:', error)
      toast.error('Terjadi kesalahan saat memuat data')
      router.push('/administrasi/program')
    }
  }
}

// Handle cancel
const handleCancel = () => {
  router.push('/administrasi/program')
}

// Handle save
const handleSave = async () => {
  if (!formData.nama_program) {
    toast.error('Nama Program wajib diisi')
    return
  }

  try {
    // Get CSRF token
    const csrfToken = await getCsrfToken()
    if (!csrfToken) {
      toast.error('Gagal mendapatkan CSRF token. Silakan refresh halaman.')
      return
    }

    // build payload including dynamic shares
    const payload: Record<string, any> = {
      nama_program: formData.nama_program,
      tipe_pembagian_marketing: formData.tipe_pembagian_marketing || null,
      jumlah_persentase: calculatedJumlahPersentase.value || null,
      shares: [],
    }

    for (const t of programShareTypes.value) {
        const s = formData.shares[t.key]
        payload.shares.push({
          program_share_type_key: t.key,
          program_share_type_id: t.id,
          name: t.name,
          type: s?.type || t.default_type || 'percentage',
          value: s?.value !== undefined ? (s?.value ?? null) : null,
          alias: s?.alias || null,
          orders: t.orders ?? null,
        })
    }

    // include custom rows
    for (const row of formData.customRows) {
      payload.shares.push({
        program_share_type_key: row.key,
        program_share_type_id: null,
        name: row.name || null,
        type: row.type || 'percentage',
        value: row.value !== undefined ? (row.value ?? null) : null,
        is_custom: true,
      })
    }

    const url = isEditMode.value 
      ? `/admin/api/program/${route.params.id}`
      : '/admin/api/program'
    
    const method = isEditMode.value ? 'PUT' : 'POST'

    const response = await fetch(url, {
      method: method,
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
      },
      credentials: 'same-origin',
      body: JSON.stringify(payload),
    })

    const result = await response.json()
    
    if (result.success) {
      toast.success(isEditMode.value ? 'Program berhasil diupdate' : 'Program berhasil ditambahkan')
      router.push('/administrasi/program')
    } else {
      if (result.errors) {
        const errorMessages = Object.values(result.errors).flat().join(', ')
        toast.error(`Validasi gagal: ${errorMessages}`)
      } else {
        toast.error(result.message || 'Gagal menyimpan data')
      }
    }
  } catch (error) {
    console.error('Error saving:', error)
    toast.error('Terjadi kesalahan saat menyimpan data')
  }
}

onMounted(async () => {
  await fetchProgramShareTypes()
  await loadData()
})
</script>


