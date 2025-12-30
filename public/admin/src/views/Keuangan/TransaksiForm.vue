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
          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Kantor Cabang <span class="text-red-500">*</span>
            </label>
            <SearchableSelect
              v-model="formData.branchId"
              :options="kantorCabangList"
              placeholder="Kantor Cabang"
              :search-input="kantorCabangSearchInput"
              @update:search-input="kantorCabangSearchInput = $event"
              :disabled="isFundrising"
            />
          </div>

          <!-- branch users removed; fundraiser will be shown when donor selected -->

          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Nominal <span class="text-red-500">*</span>
            </label>
            <input
              type="number"
              v-model.number="formData.nominal"
              placeholder="Nominal"
              min="0"
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Donatur <span class="text-red-500">*</span>
            </label>
            <AsyncSearchableSelect
              v-model="formData.donorId"
              fetch-url="/admin/api/donatur"
              placeholder="Donatur"
            />
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Fundraiser
            </label>
            <input
              type="text"
              :value="fundraiserDisplay ? fundraiserDisplay.name : 'Tidak ada Fundraiser'"
              disabled
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300"
            />
            <input v-if="false" type="hidden" v-model="formData.fundraiserId" />
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Program <span class="text-red-500">*</span>
            </label>
            <SearchableSelect
              v-model="formData.programId"
              :options="programList"
              placeholder="Program"
              :search-input="programSearchInput"
              @update:search-input="programSearchInput = $event"
            />
          </div>

          <div v-if="programBreakdown.length > 0" class="lg:col-span-2">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Rincian Pembagian Program</label>
            <div class="overflow-x-auto rounded-lg border border-gray-300 bg-white dark:border-gray-700">
              <table class="min-w-full text-sm text-left text-gray-700 dark:text-gray-300">
                <thead class="bg-gray-50 dark:bg-gray-800">
                  <tr>
                    <th class="px-4 py-2 font-medium">Nama</th>
                    <th class="px-4 py-2 font-medium">Nilai</th>
                    <th class="px-4 py-2 font-medium text-right">Jumlah (Rp)</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, idx) in programBreakdown" :key="idx" class="border-t border-gray-100 dark:border-gray-800">
                    <td class="px-4 py-2 align-top">
                      <div class="font-medium">{{ item.name }}</div>
                    </td>
                    <td class="px-4 py-2 align-top text-gray-500 text-sm">{{ item.displayValue }}</td>
                    <td class="px-4 py-2 align-top text-right font-medium">Rp {{ item.amount.toLocaleString() }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Mitra
            </label>
            <SearchableSelect
              v-model="formData.mitraId"
              :options="mitraList"
              placeholder="Mitra (opsional)"
              :search-input="mitraSearchInput"
              @update:search-input="mitraSearchInput = $event"
            />
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Tanggal Transaksi <span class="text-red-500">*</span>
            </label>
            <div class="relative">
              <flat-pickr
                v-model="formData.transactionDate"
                :config="flatpickrDateConfig"
                class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-11 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                placeholder="Tanggal Transaksi"
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

          <div class="lg:col-span-2">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Keterangan
            </label>
            <textarea
              v-model="formData.notes"
              placeholder="Keterangan"
              maxlength="1000"
              rows="4"
              class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            ></textarea>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
              Maksimal 1000 karakter.
            </p>
          </div>
        </div>

        <div class="flex items-center gap-3 mt-6 lg:justify-end">
          <button
            @click="handleCancel"
            type="button"
            :disabled="saving"
            class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] sm:w-auto disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Batal
          </button>
          <button
            type="submit"
            :disabled="saving"
            class="flex w-full justify-center items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <svg v-if="saving" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ saving ? 'Menyimpan...' : (isEditMode ? 'Simpan Perubahan' : 'Simpan') }}
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
import flatPickr from 'vue-flatpickr-component'
import 'flatpickr/dist/flatpickr.css'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import SearchableSelect from '@/components/forms/SearchableSelect.vue'
import AsyncSearchableSelect from '@/components/forms/AsyncSearchableSelect.vue'
import { useAuth } from '@/composables/useAuth'

interface SelectOption {
  value: string
  label: string
}

const route = useRoute()
const router = useRouter()
const toast = useToast()

// User data for role-based field restrictions
const currentUser = ref<any>(null)
const isFundrising = computed(() => {
  const roleName = currentUser.value?.role?.name?.toLowerCase()
  return roleName === 'fundrising' || roleName === 'fundraising'
})

const isEditMode = computed(() => route.params.id !== undefined && route.params.id !== 'new')
const currentPageTitle = computed(() => {
  return isEditMode.value ? 'Edit Transaksi' : 'Tambah Transaksi'
})

// Loading states
const loading = ref(false)
const saving = ref(false)

// Flatpickr configuration for single date selection
const flatpickrDateConfig = {
  dateFormat: 'Y-m-d',
  altInput: true,
  altFormat: 'd/m/Y',
  allowInput: false,
}

// Options from API
const kantorCabangList = ref<SelectOption[]>([])
const kantorCabangDataMap = ref<Map<string, any>>(new Map())
const programList = ref<SelectOption[]>([])
const mitraList = ref<SelectOption[]>([])
const fundraiserDisplay = ref<any>(null)
const programDetail = ref<any | null>(null)
const programBreakdown = ref<Array<{ name: string; type: string; value: number | null; amount: number }>>([])

// Search input refs
const kantorCabangSearchInput = ref('')
const programSearchInput = ref('')
const mitraSearchInput = ref('')

// Form data
const formData = reactive({
  branchId: '',
  nominal: null as number | null,
  donorId: '',
  programId: '',
  mitraId: '',
  fundraiserId: '',
  transactionDate: '',
  notes: '',
})

// Fetch current user data
const { fetchUser } = useAuth()

const fetchCurrentUser = async () => {
  try {
    const res = await fetch('/admin/api/user', { credentials: 'same-origin' })
    if (res.ok) {
      const json = await res.json()
      if (json.success && json.user) {
        currentUser.value = json.user
      }
    }
  } catch (error) {
    console.error('Error fetching current user:', error)
  }
}

// Fetch dropdown options from APIs
const fetchOptions = async () => {
  try {
    // Decide kantor-cabang URL: show all branches only to global admins
    const isRoleAdminCabang = (() => {
      if (!currentUser.value) return false
      const roles = currentUser.value.roles || (currentUser.value.role ? [currentUser.value.role] : [])
      return Array.isArray(roles) && roles.some((r: any) => {
        const name = typeof r === 'string' ? r : r?.name
        return typeof name === 'string' && name.trim().toLowerCase() === 'admin cabang'
      })
    })()

    const isGlobalAdmin = Boolean(currentUser.value && currentUser.value.is_admin)
    const kantorUrl = (isGlobalAdmin && !isRoleAdminCabang)
      ? '/admin/api/kantor-cabang?per_page=1000'
      : '/admin/api/kantor-cabang?per_page=1000&only_assigned=1'

    const [kantorRes, programRes] = await Promise.all([
      fetch(kantorUrl, { credentials: 'same-origin' }),
      fetch('/admin/api/program?per_page=100', { credentials: 'same-origin' }),
    ])

    if (kantorRes.ok) {
      const json = await kantorRes.json()
      const dataArray = json.success && json.data ? (Array.isArray(json.data) ? json.data : json.data.data || []) : []
      kantorCabangList.value = dataArray.map((item: any) => ({
        value: item.id,
        label: item.nama,
      }))
      // Store full data for later access
      kantorCabangDataMap.value = new Map(dataArray.map((item: any) => [item.id, item]))
    }

    if (programRes.ok) {
      const json = await programRes.json()
      const dataArray = json.success && json.data ? (Array.isArray(json.data) ? json.data : json.data.data || []) : []
      programList.value = dataArray.map((item: any) => ({
        value: item.id,
        label: item.nama_program,
      }))
    }

    // Fetch mitra list (optional)
    try {
      const mitraRes = await fetch('/admin/api/mitra?per_page=100', { credentials: 'same-origin' })
      if (mitraRes.ok) {
        const json = await mitraRes.json()
        const dataArray = json.success && json.data ? (Array.isArray(json.data) ? json.data : json.data.data || []) : []
        mitraList.value = dataArray.map((item: any) => ({ value: item.id, label: item.nama || item.name || '' }))
      }
    } catch (e) {
      // ignore mitra fetch error; mitra is optional
    }
  } catch (error) {
    console.error('Error fetching options:', error)
    toast.error('Gagal memuat data opsi')
  }
}

// Load data if edit mode
const loadData = async () => {
  if (isEditMode.value && route.params.id) {
    loading.value = true
    try {
      const id = route.params.id as string
      const res = await fetch(`/admin/api/transaksi/${id}`, { credentials: 'same-origin' })

      if (!res.ok) throw new Error('Failed to fetch transaksi')

      const json = await res.json()

      if (json.success && json.data) {
        const data = json.data
        formData.branchId = data.kantor_cabang_id || ''
        formData.nominal = data.nominal
        formData.donorId = data.donatur_id || ''
        formData.programId = data.program_id || ''
        formData.mitraId = data.mitra_id || ''
        formData.transactionDate = data.tanggal_transaksi || ''
        formData.notes = data.keterangan || ''
      }
    } catch (error) {
      console.error('Error loading transaksi:', error)
      toast.error('Gagal memuat data transaksi')
    } finally {
      loading.value = false
    }
  }
}

// Handle cancel
const handleCancel = () => {
  router.push('/keuangan/transaksi')
}

// Handle save
const handleSave = async () => {
  if (!formData.branchId) {
    toast.warning('Kantor Cabang wajib diisi')
    return
  }

  if (!formData.nominal || formData.nominal <= 0) {
    toast.warning('Nominal wajib diisi')
    return
  }

  if (!formData.donorId) {
    toast.warning('Donatur wajib diisi')
    return
  }

  if (!formData.programId) {
    toast.warning('Program wajib diisi')
    return
  }

  if (!formData.transactionDate) {
    toast.warning('Tanggal transaksi wajib diisi')
    return
  }

  saving.value = true

  try {
    // Fetch CSRF token
    const tokenRes = await fetch('/admin/api/csrf-token', { credentials: 'same-origin' })
    if (!tokenRes.ok) throw new Error('Failed to fetch CSRF token')
    const tokenJson = await tokenRes.json()

    // Prepare data for API
    const payload: Record<string, any> = {
      kantor_cabang_id: formData.branchId,
      nominal: formData.nominal,
      donatur_id: formData.donorId,
      program_id: formData.programId,
      mitra_id: formData.mitraId || null,
      tanggal_transaksi: formData.transactionDate,
      keterangan: formData.notes || null,
    }

    // For Fundrising role, set fundraiser_id to current user
    if (isFundrising.value && currentUser.value?.id) {
      payload.fundraiser_id = currentUser.value.id
    }

    let res: Response

    if (isEditMode.value) {
      res = await fetch(`/admin/api/transaksi/${route.params.id}`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': tokenJson.csrf_token,
        },
        credentials: 'same-origin',
        body: JSON.stringify(payload),
      })
    } else {
      res = await fetch('/admin/api/transaksi', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': tokenJson.csrf_token,
        },
        credentials: 'same-origin',
        body: JSON.stringify(payload),
      })
    }

    const json = await res.json()

    if (json.success) {
      toast.success(json.message || (isEditMode.value ? 'Transaksi berhasil diupdate' : 'Transaksi berhasil ditambahkan'))
      router.push('/keuangan/transaksi')
    } else {
      // Handle validation errors
      if (json.errors) {
        const errorMessages = Object.values(json.errors).flat().join(', ')
        toast.error(errorMessages)
      } else {
        toast.error(json.message || 'Gagal menyimpan transaksi')
      }
    }
  } catch (error) {
    console.error('Error saving:', error)
    toast.error('Terjadi kesalahan saat menyimpan data')
  } finally {
    saving.value = false
  }
}

onMounted(async () => {
  // ensure global auth state is populated for components that rely on it
  await fetchUser()
  await fetchCurrentUser()
  await fetchOptions()
  await loadData()

  // For Fundrising role, auto-set Kantor Cabang from current user
  if (isFundrising.value && !isEditMode.value) {
    if (currentUser.value?.kantor_cabang?.id) {
      formData.branchId = String(currentUser.value.kantor_cabang.id)
    }
  }
})

// Watch donor selection and load donor details (to show fundraiser)
watch(() => formData.donorId, async (newDonorId) => {
  fundraiserDisplay.value = null
  formData.fundraiserId = ''
  if (!newDonorId) return
  try {
    const res = await fetch(`/admin/api/donatur/${newDonorId}`, { credentials: 'same-origin' })
    if (!res.ok) return
    const json = await res.json()
    if (json.success && json.data) {
      const donor = json.data
      // donor.pic_user contains PIC info; use as fundraiser if present
      if (donor.pic_user) {
        fundraiserDisplay.value = { id: donor.pic_user.id, name: donor.pic_user.nama }
        formData.fundraiserId = donor.pic_user.id
      } else if (donor.pic) {
        fundraiserDisplay.value = { id: donor.pic, name: '' }
        formData.fundraiserId = donor.pic
      }
    }
  } catch (e) {
    console.error('Failed to load donor details:', e)
  }
})

// Watch program selection and nominal to compute breakdown
const computeProgramBreakdown = () => {
  programBreakdown.value = []
  const nominal = Number(formData.nominal) || 0
  if (!programDetail.value || !Array.isArray(programDetail.value.shares)) return
  const humanizeKey = (k: string | null | undefined) => {
    if (!k) return ''
    // replace underscores/dashes, remove random suffixes, and title-case
    const cleaned = String(k).replace(/[-_]+/g, ' ').replace(/\b[a-f0-9]{4,}\b/g, '').trim()
    return cleaned.split(' ').filter(Boolean).map((w: string) => w.charAt(0).toUpperCase() + w.slice(1)).join(' ')
  }

  const formatValueNumber = (v: any) => {
    if (v === null || v === undefined) return null
    const n = Number(v)
    if (Number.isNaN(n)) return null
    return n
  }

  programBreakdown.value = programDetail.value.shares.map((s: any) => {
    const isPercentage = (s.type || 'percentage') === 'percentage'
    const rawValue = formatValueNumber(s.value)
    const amount = isPercentage && rawValue !== null ? Math.round(Number(nominal * (rawValue / 100))) : (rawValue !== null ? Math.round(rawValue) : 0)
    const displayName = s.name || (s.program_share_type_key ? humanizeKey(s.program_share_type_key) : '-')
    let displayValue = '-'
    if (rawValue !== null) {
      if (isPercentage) {
        // show integer without decimals unless there are decimals
        displayValue = Number.isInteger(rawValue) ? `${rawValue}%` : `${parseFloat(String(rawValue)).toFixed(2).replace(/\.00$/, '')}%`
      } else {
        displayValue = Number(rawValue).toLocaleString()
      }
    }
    return { name: displayName, type: s.type || 'percentage', value: rawValue, amount, displayValue }
  })
}

watch(() => formData.programId, async (newProgramId) => {
  programDetail.value = null
  programBreakdown.value = []
  if (!newProgramId) return
  try {
    const res = await fetch(`/admin/api/program/${newProgramId}`, { credentials: 'same-origin' })
    if (!res.ok) return
    const json = await res.json()
    if (json.success && json.data) {
      programDetail.value = json.data
      computeProgramBreakdown()
    }
  } catch (e) {
    console.error('Failed to load program details:', e)
  }
}, { immediate: false })

watch(() => formData.nominal, () => {
  computeProgramBreakdown()
})
</script>




