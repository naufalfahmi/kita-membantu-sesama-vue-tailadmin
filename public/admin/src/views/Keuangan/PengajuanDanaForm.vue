
<template>
  <AdminLayout>
    <div class="min-h-screen rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12">
      <div class="mb-6 flex items-center justify-between">
        <h3 class="font-semibold text-gray-800 text-theme-xl dark:text-white/90 sm:text-2xl">{{ currentPageTitle }}</h3>
        <button @click="handleCancel" class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">Batal</button>
      </div>

      <form @submit.prevent="handleSave" class="flex flex-col">
        <div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2">
          <div class="lg:col-span-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Pengaju <span class="text-red-500">*</span></label>
            <SearchableSelect v-model="formData.applicant" :options="applicantList" placeholder="Pilih atau cari pengaju" :search-input="applicantSearchInput" @update:search-input="applicantSearchInput = $event" />
          </div>

          <div class="lg:col-span-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Tipe Pengajuan <span class="text-red-500">*</span></label>
            <SearchableSelect v-model="formData.submissionType" :options="submissionTypeList" placeholder="Pilih tipe pengajuan" :search-input="submissionTypeSearchInput" @update:search-input="submissionTypeSearchInput = $event" />
          </div>

          <div v-if="formData.submissionType === 'program'" class="lg:col-span-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Program</label>
            <SearchableSelect v-model="formData.programId" :options="programList" placeholder="Pilih program" />
            <div v-if="loadingBalance" class="mt-2 text-sm text-gray-500">Memuat saldo...</div>
            <div v-else-if="programBalance" class="mt-3 rounded-lg border border-gray-200 bg-gray-50 p-3 text-sm text-gray-700 dark:border-gray-700 dark:bg-gray-900/40">
              <div class="flex items-center justify-between">
                <div>
                  <div class="text-xs text-gray-500">Sisa alokasi bulan {{ formatMonthYear(programBalance.month) }}</div>
                  <div class="text-lg font-medium">{{ formatCurrency(programBalance.remaining) }}</div>
                </div>
                <div class="text-right text-xs text-gray-500">
                  <div>Inflow: {{ formatCurrency(programBalance.inflow) }}</div>
                  <div>Dialokasikan: {{ formatCurrency(programBalance.allocated) }}</div>
                  <div>Terpakai: {{ formatCurrency(programBalance.outflow) }}</div>
                </div>
              </div>

              <div class="mt-3 flex items-center justify-between">
                <div class="text-sm text-gray-600">Opsi:</div>
                <div class="flex items-center gap-3">
                  <button type="button" @click="() => { formData.amount = programBalance.remaining }" class="rounded bg-gray-100 px-3 py-1 text-sm">Gunakan Seluruh Alokasi</button>
                </div>
              </div>

              <div class="mt-3">
                <div class="mb-2 text-sm font-medium">Daftar Transaksi (bulan)</div>
                <div class="space-y-2 max-h-48 overflow-auto">
                  <div v-for="t in transaksiList" :key="t.id" class="flex items-center justify-between rounded-md border border-gray-200 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
                    <div>
                      <div class="font-medium">{{ t.kode || t.id }}</div>
                      <div class="text-xs text-gray-500">{{ t.tanggal_transaksi }}</div>
                    </div>
                    <div class="text-right">
                      <div class="text-sm">Nominal: {{ formatCurrency(t.nominal) }}</div>
                      <div class="text-xs text-gray-500">Terpakai: {{ formatCurrency(t.used) }} — Tersedia: {{ formatCurrency(t.available) }}</div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="mt-3 text-sm text-gray-700">
                <div>Estimasi sisa setelah pengajuan: <span class="font-medium">{{ formatCurrency((programBalance.remaining || 0) - (formData.amount || 0)) }}</span></div>
                <div v-if="(formData.amount || 0) > (programBalance.remaining || 0)" class="mt-1 text-xs text-red-500">Kekurangan: {{ formatCurrency((formData.amount || 0) - (programBalance.remaining || 0)) }}</div>
              </div>
            </div>
          </div>

          <div class="lg:col-span-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Status <span class="text-red-500">*</span></label>
            <SearchableSelect v-model="formData.status" :options="statusOptionsFiltered" placeholder="Pilih status" />
          </div>

          <div class="lg:col-span-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Nominal <span class="text-red-500">*</span></label>
            <div class="relative">
              <input type="number" v-model.number="formData.amount" placeholder="Masukkan nominal" min="1" step="1" required class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-24 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" @input="formatAmountInput" />
              <span class="absolute text-gray-500 -translate-y-1/2 pointer-events-none right-4 top-1/2 dark:text-gray-400 text-sm">{{ formattedAmount }}</span>
            </div>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Minimal nominal: Rp 1</p>
          </div>

          <div class="lg:col-span-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Tanggal Digunakan <span class="text-red-500">*</span></label>
            <div class="relative">
              <flat-pickr v-model="formData.usedAt" :config="flatpickrConfig" class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pl-4 pr-11 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" placeholder="Pilih tanggal digunakan" required />
            </div>
          </div>

          <div class="lg:col-span-2">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Tujuan Pengajuan</label>
            <textarea v-model="formData.purpose" placeholder="Masukkan tujuan atau alasan pengajuan dana" maxlength="1000" rows="4" class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"></textarea>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ formData.purpose.length }}/1000 karakter</p>
          </div>

          <div class="lg:col-span-2">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Kantor Cabang <span class="text-red-500">*</span></label>
            <SearchableSelect v-model="formData.branchId" :options="kantorCabangList" placeholder="Pilih atau cari kantor cabang" :search-input="kantorCabangSearchInput" @update:search-input="kantorCabangSearchInput = $event" />
          </div>
        </div>

        <div class="flex items-center gap-3 mt-6 lg:justify-end">
          <button @click="handleCancel" type="button" class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] sm:w-auto">Batal</button>
          <button type="submit" class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto">{{ isEditMode ? 'Simpan Perubahan' : 'Simpan' }}</button>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { reactive, computed, ref, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import flatPickr from 'vue-flatpickr-component'
import 'flatpickr/dist/flatpickr.css'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import SearchableSelect from '@/components/forms/SearchableSelect.vue'
import { getCsrfTokenSafe } from '@/utils/getCsrfToken'
import { useToast } from 'vue-toastification'
import { useAuth } from '@/composables/useAuth'
import type { Ref } from 'vue'

const route = useRoute()
const router = useRouter()
const toast = useToast()
const { fetchUser } = useAuth()

const isEditMode = computed(() => route.params.id !== undefined && route.params.id !== 'new')
const currentPageTitle = computed(() => (isEditMode.value ? 'Edit Pengajuan Dana' : 'Tambah Pengajuan Dana'))

const flatpickrConfig = {
  dateFormat: 'Y-m-d',
  altInput: true,
  altFormat: 'd F Y',
  locale: 'id' as any,
  wrap: true,
  clickOpens: true,
  allowInput: false,
  minDate: 'today',
}

const applicantList: Ref<Array<{ value: string; label: string }>> = ref([])
const submissionTypeList = [
  { value: 'operasional', label: 'Operasional' },
  { value: 'program', label: 'Program' },
  { value: 'kegiatan', label: 'Kegiatan' },
  { value: 'pengembangan', label: 'Pengembangan' },
  { value: 'darurat', label: 'Darurat' },
  { value: 'lainnya', label: 'Lainnya' },
]
const kantorCabangList: Ref<Array<{ value: string; label: string }>> = ref([])
const programList: Ref<Array<{ value: string; label: string }>> = ref([])

const applicantSearchInput = ref('')
const submissionTypeSearchInput = ref('')
const kantorCabangSearchInput = ref('')

const currentUserId = ref('')

const formData = reactive({
  applicant: '',
  submissionType: '',
  status: 'Draft',
  programId: '',
  amount: null as number | null,
  usedAt: '',
  purpose: '',
  branchId: '',
})

const formattedAmount = computed(() => {
  if (!formData.amount || formData.amount <= 0) return ''
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(formData.amount)
})

const programBalance: Ref<any> = ref(null)
const transaksiList: Ref<Array<any>> = ref([])
const loadingBalance = ref(false)
let balanceTimer: number | null = null

const formatCurrency = (v: number | null) => {
  if (!v || v <= 0) return 'Rp 0'
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(v)
}

const formatMonthYear = (ym: string | null) => {
  if (!ym) return ''
  // expected format 'YYYY-MM'
  try {
    const parts = String(ym).split('-')
    if (parts.length < 2) return ym
    const y = Number(parts[0])
    const m = Number(parts[1]) - 1
    const d = new Date(Date.UTC(y, m, 1))
    return new Intl.DateTimeFormat('id-ID', { month: 'long', year: 'numeric' }).format(d)
  } catch (e) {
    return ym
  }
}

const loadProgramBalance = async () => {
  if (!formData.programId || !formData.usedAt) {
    programBalance.value = null
    transaksiList.value = []
    return
  }
  loadingBalance.value = true
  try {
    // month param in YYYY-MM
    const d = new Date(formData.usedAt)
    if (isNaN(d.getTime())) {
      programBalance.value = null
      transaksiList.value = []
      loadingBalance.value = false
      return
    }
    const month = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}`
    const res = await fetch(`/admin/api/program/${formData.programId}/balance?month=${month}`, { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    const json = await res.json()
    if (!json.success) throw new Error(json.message || 'Failed to load balance')
    programBalance.value = json.data
    transaksiList.value = json.data.transaksis || []
    // if creating new and amount not set, default to full remaining allocation
    if (!isEditMode.value) {
      const rem = Number(json.data.remaining || 0)
      if (rem > 0 && (!formData.amount || formData.amount <= 0)) {
        formData.amount = rem
      }
    }
  } catch (err) {
    console.error('Error loading program balance', err)
    toast.error('Gagal memuat saldo program')
    programBalance.value = null
    transaksiList.value = []
  } finally {
    loadingBalance.value = false
  }
}

// watch programId and usedAt with debounce
watch([
  () => formData.programId,
  () => formData.usedAt,
], () => {
  if (balanceTimer) window.clearTimeout(balanceTimer)
  balanceTimer = window.setTimeout(() => {
    loadProgramBalance()
  }, 300)
})

const formatAmountInput = (event: Event) => {
  const target = event.target as HTMLInputElement
  const value = parseFloat(target.value)
  if (isNaN(value) || value < 0) formData.amount = null
  else formData.amount = Math.floor(value)
}

const loadData = async () => {
  if (!isEditMode.value) return
  const id = route.params.id as string
  try {
    const res = await fetch(`/admin/api/pengajuan-dana/${id}`, { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    const json = await res.json()
    if (!json.success) throw new Error(json.message || 'Failed to load')
    const data = json.data
    formData.applicant = data.fundraiser ? String(data.fundraiser.id) : ''

      // populate status from server so edit shows current status
      formData.status = data.status || 'Draft'

      // ensure the applicant option exists in the select options (in case options endpoint
      // does not include the historic fundraiser). This allows the existing value to be displayed.
      if (data.fundraiser && !applicantList.value.find(a => a.value === String(data.fundraiser.id))) {
        applicantList.value.unshift({ value: String(data.fundraiser.id), label: data.fundraiser.name || (data.fundraiser.nama || '') })
      }
    formData.submissionType = data.submission_type
    formData.amount = data.amount
    formData.usedAt = data.used_at
    formData.purpose = data.purpose
    formData.branchId = data.kantor_cabang ? data.kantor_cabang.id : ''
      formData.programId = data.program ? String(data.program.id) : ''

      // ensure branch option exists
      if (data.kantor_cabang && !kantorCabangList.value.find(b => b.value === String(data.kantor_cabang.id))) {
        kantorCabangList.value.unshift({ value: String(data.kantor_cabang.id), label: data.kantor_cabang.nama || '' })
      }

      // ensure program option exists
      if (data.program && !programList.value.find(p => p.value === String(data.program.id))) {
        // program may have `nama_program` or `nama` depending on API
        const label = data.program.nama_program || data.program.nama || ''
        programList.value.unshift({ value: String(data.program.id), label })
      }
  } catch (err) {
    console.error('Error loading data:', err)
    toast.error('Gagal memuat data pengajuan dana')
  }
}

const loadOptions = async () => {
  try {
    const userObj = await fetchUser()
    currentUserId.value = userObj?.id ? String(userObj.id) : ''
    const res = await fetch('/admin/api/pengajuan-dana/options', { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    const json = await res.json()
    if (!json.success) throw new Error(json.message || 'Failed to load options')
    const users = json.data.users || []
    applicantList.value = users.map((u: any) => ({ value: String(u.id), label: u.name }))
    const branches = json.data.kantor_cabangs || []
    kantorCabangList.value = branches.map((b: any) => ({ value: String(b.id), label: b.nama }))
    const programs = json.data.programs || []
    programList.value = programs.map((p: any) => ({ value: String(p.id), label: p.nama }))
    if (currentUserId.value) {
      const exists = applicantList.value.find(a => a.value === String(currentUserId.value))
      if (exists && !formData.applicant) formData.applicant = String(currentUserId.value)
    }
    // if editing, backfill programId if provided by API
    if (isEditMode.value && json.data && json.data.current && json.data.current.program) {
      formData.programId = String(json.data.current.program.id)
    }
  } catch (err) {
    console.error('Error loading options', err)
    toast.error('Gagal memuat opsi form')
  }
}

// Status options: when applicant is the current user limit to Draft or Diajukan (DB value 'pending')
const statusOptions = [
  { value: 'Draft', label: 'Draft' },
  { value: 'pending', label: 'Diajukan' },
]

const isApplicantSelf = computed(() => {
  return String(formData.applicant) === String(currentUserId.value)
})

const statusOptionsFiltered = computed(() => {
  // currently restricted set for self; admins/editors could be expanded later
  if (isApplicantSelf.value) return statusOptions
  return statusOptions
})

const handleCancel = () => router.push('/keuangan/pengajuan-dana')

const handleSave = async () => {
  if (!formData.applicant) { toast.error('Pengaju wajib diisi'); return }
  if (!formData.submissionType) { toast.error('Tipe Pengajuan wajib diisi'); return }
  if (!formData.amount || formData.amount <= 0) { toast.error('Nominal wajib diisi dan harus lebih dari 0'); return }
  if (!formData.usedAt) { toast.error('Tanggal Digunakan wajib diisi'); return }
  if (!formData.branchId) { toast.error('Kantor Cabang wajib diisi'); return }

  try {
    const payload = {
      fundraiser_id: formData.applicant,
      submission_type: formData.submissionType,
      program_id: formData.programId || null,
      status: formData.status,
      amount: formData.amount,
      used_at: formData.usedAt,
      purpose: formData.purpose,
      kantor_cabang_id: formData.branchId,
    }

    const csrf = await getCsrfTokenSafe()
    const url = isEditMode.value ? `/admin/api/pengajuan-dana/${route.params.id}` : '/admin/api/pengajuan-dana'
    const method = isEditMode.value ? 'PUT' : 'POST'
    const res = await fetch(url, { method, credentials: 'same-origin', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' }, body: JSON.stringify(payload) })
    const result = await res.json()
    if (!result.success) {
      if (result.errors) { const first = Object.values(result.errors)[0]; toast.error(first ? String((first as any)[0]) : 'Validation failed'); return }
      throw new Error(result.message || 'Failed to save')
    }

    toast.success(isEditMode.value ? 'Pengajuan dana berhasil diupdate' : 'Pengajuan dana berhasil ditambahkan')
    router.push('/keuangan/pengajuan-dana')
  } catch (err) {
    console.error('Error saving:', err)
    toast.error('Terjadi kesalahan saat menyimpan data')
  }
}

onMounted(async () => {
  await loadOptions()
  await loadData()
})
</script>
          }

