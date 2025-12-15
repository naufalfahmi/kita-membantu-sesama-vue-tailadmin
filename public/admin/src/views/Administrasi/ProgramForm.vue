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

          <!-- 2. Persentase Hak Program -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Persentase Hak Program
            </label>
            <input
              type="number"
              v-model.number="formData.persentase_hak_program"
              placeholder="0"
              min="0"
              max="100"
              step="0.01"
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
          </div>

          <!-- 3. Persentase Hak Program Operasional -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Persentase Hak Program Operasional
            </label>
            <input
              type="number"
              v-model.number="formData.persentase_hak_program_operasional"
              placeholder="0"
              min="0"
              max="100"
              step="0.01"
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
          </div>

          <!-- 4. Persentase Hak Championship -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Persentase Hak Championship
            </label>
            <input
              type="number"
              v-model.number="formData.persentase_hak_championship"
              placeholder="0"
              min="0"
              max="100"
              step="0.01"
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
          </div>

          <!-- Row dengan 2 kolom: Tipe Pembagian Marketing dan Persentase Hak Marketing -->
          <div class="lg:col-span-1 grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2">
            <!-- 5. Persentase Hak Marketing (Tipe Pembagian) -->
            <div class="lg:col-span-1">
              <label
                class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
              >
                Persentase Hak Marketing
              </label>
              <SearchableSelect
                v-model="formData.tipe_pembagian_marketing"
                :options="tipePembagianOptions"
                placeholder="Pilih Tipe Pembagian"
                :search-input="tipePembagianSearchInput"
                @update:search-input="tipePembagianSearchInput = $event"
              />
            </div>

            <!-- 6. Persentase Hak Marketing -->
            <div class="lg:col-span-1">
              <label
                class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
              >
                Persentase Hak Marketing
              </label>
              <input
                type="number"
                v-model.number="formData.persentase_hak_marketing"
                placeholder="0"
                min="0"
                max="100"
                step="0.01"
                class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
              />
            </div>
          </div>

          <!-- 7. Persentase Hak Operasional 1 -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Persentase Hak Operasional 1
            </label>
            <input
              type="number"
              v-model.number="formData.persentase_hak_operasional_1"
              placeholder="0"
              min="0"
              max="100"
              step="0.01"
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
          </div>

          <!-- 8. Persentase Hak Iklan -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Persentase Hak Iklan
            </label>
            <input
              type="number"
              v-model.number="formData.persentase_hak_iklan"
              placeholder="0"
              min="0"
              max="100"
              step="0.01"
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
          </div>

          <!-- 9. Persentase Hak Operasional 2 -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Persentase Hak Operasional 2
            </label>
            <input
              type="number"
              v-model.number="formData.persentase_hak_operasional_2"
              placeholder="0"
              min="0"
              max="100"
              step="0.01"
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
          </div>

          <!-- 10. Persentase Hak Operasional 3 -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Persentase Hak Operasional 3
            </label>
            <input
              type="number"
              v-model.number="formData.persentase_hak_operasional_3"
              placeholder="0"
              min="0"
              max="100"
              step="0.01"
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
          </div>

          <!-- 11. Jumlah Persentase -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Jumlah Persentase
            </label>
            <input
              type="number"
              :value="calculatedJumlahPersentase"
              placeholder="0"
              min="0"
              max="100"
              step="0.01"
              readonly
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800 cursor-not-allowed opacity-60"
            />
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
              Total dari semua persentase
            </p>
          </div>
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
  persentase_hak_program: null as number | null,
  persentase_hak_program_operasional: null as number | null,
  persentase_hak_championship: null as number | null,
  tipe_pembagian_marketing: '',
  persentase_hak_marketing: null as number | null,
  persentase_hak_operasional_1: null as number | null,
  persentase_hak_iklan: null as number | null,
  persentase_hak_operasional_2: null as number | null,
  persentase_hak_operasional_3: null as number | null,
  jumlah_persentase: null as number | null,
})

// Computed untuk menghitung total jumlah persentase dari semua field persentase
const calculatedJumlahPersentase = computed(() => {
  let total = 0
  
  // Menjumlahkan semua persentase yang ada
  if (formData.persentase_hak_program !== null) {
    total += formData.persentase_hak_program
  }
  if (formData.persentase_hak_program_operasional !== null) {
    total += formData.persentase_hak_program_operasional
  }
  if (formData.persentase_hak_championship !== null) {
    total += formData.persentase_hak_championship
  }
  if (formData.persentase_hak_marketing !== null) {
    total += formData.persentase_hak_marketing
  }
  if (formData.persentase_hak_operasional_1 !== null) {
    total += formData.persentase_hak_operasional_1
  }
  if (formData.persentase_hak_iklan !== null) {
    total += formData.persentase_hak_iklan
  }
  if (formData.persentase_hak_operasional_2 !== null) {
    total += formData.persentase_hak_operasional_2
  }
  if (formData.persentase_hak_operasional_3 !== null) {
    total += formData.persentase_hak_operasional_3
  }
  
  // Bulatkan ke 2 desimal
  return total > 0 ? parseFloat(total.toFixed(2)) : 0
})

// Watch untuk update jumlah_persentase otomatis saat ada perubahan
watch([
  () => formData.persentase_hak_program,
  () => formData.persentase_hak_program_operasional,
  () => formData.persentase_hak_championship,
  () => formData.tipe_pembagian_marketing,
  () => formData.persentase_hak_marketing,
  () => formData.persentase_hak_operasional_1,
  () => formData.persentase_hak_iklan,
  () => formData.persentase_hak_operasional_2,
  () => formData.persentase_hak_operasional_3,
], () => {
  formData.jumlah_persentase = calculatedJumlahPersentase.value
}, { immediate: true })

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
        formData.persentase_hak_program = data.persentase_hak_program ? parseFloat(data.persentase_hak_program) : null
        formData.persentase_hak_program_operasional = data.persentase_hak_program_operasional ? parseFloat(data.persentase_hak_program_operasional) : null
        formData.persentase_hak_championship = data.persentase_hak_championship ? parseFloat(data.persentase_hak_championship) : null
        formData.tipe_pembagian_marketing = data.tipe_pembagian_marketing || ''
        formData.persentase_hak_marketing = data.persentase_hak_marketing ? parseFloat(data.persentase_hak_marketing) : null
        formData.persentase_hak_operasional_1 = data.persentase_hak_operasional_1 ? parseFloat(data.persentase_hak_operasional_1) : null
        formData.persentase_hak_iklan = data.persentase_hak_iklan ? parseFloat(data.persentase_hak_iklan) : null
        formData.persentase_hak_operasional_2 = data.persentase_hak_operasional_2 ? parseFloat(data.persentase_hak_operasional_2) : null
        formData.persentase_hak_operasional_3 = data.persentase_hak_operasional_3 ? parseFloat(data.persentase_hak_operasional_3) : null
        formData.jumlah_persentase = data.jumlah_persentase ? parseFloat(data.jumlah_persentase) : null
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

    const payload: Record<string, string | number | null> = {
      nama_program: formData.nama_program,
      persentase_hak_program: formData.persentase_hak_program || null,
      persentase_hak_program_operasional: formData.persentase_hak_program_operasional || null,
      persentase_hak_championship: formData.persentase_hak_championship || null,
      tipe_pembagian_marketing: formData.tipe_pembagian_marketing || null,
      persentase_hak_marketing: formData.persentase_hak_marketing || null,
      persentase_hak_operasional_1: formData.persentase_hak_operasional_1 || null,
      persentase_hak_iklan: formData.persentase_hak_iklan || null,
      persentase_hak_operasional_2: formData.persentase_hak_operasional_2 || null,
      persentase_hak_operasional_3: formData.persentase_hak_operasional_3 || null,
      jumlah_persentase: calculatedJumlahPersentase.value || null,
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

onMounted(() => {
  loadData()
})
</script>


