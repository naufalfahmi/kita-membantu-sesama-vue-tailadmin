
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

          <div class="lg:col-span-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Program</label>
            <div class="flex items-center gap-2">
              <div class="flex-1">
                <div v-if="loadingProgramList" class="h-11 flex items-center rounded-lg border border-gray-300 bg-gray-50 px-4 text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                  <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Memuat data program...
                </div>
                <SearchableSelect v-else v-model="formData.programId" :options="programList" placeholder="Pilih program" />
              </div>
              <button
                v-if="formData.programId"
                type="button"
                title="Hapus pilihan program"
                @click="formData.programId = ''"
                class="h-11 w-11 flex items-center justify-center rounded-lg border border-gray-300 bg-white text-sm text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700"
              >
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                  <path d="M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
              </button>
            </div>
          </div>

          <div class="lg:col-span-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Kantor <span class="text-red-500">*</span></label>
            <SearchableSelect v-model="formData.branchId" :options="kantorCabangList" placeholder="Pilih atau cari kantor cabang" :search-input="kantorCabangSearchInput" @update:search-input="kantorCabangSearchInput = $event" />
          </div>

          <div class="lg:col-span-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Status <span class="text-red-500">*</span></label>
            <div v-if="formData.status === 'Approved'" class="h-11 flex items-center rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-700 dark:border-gray-700 dark:text-gray-400">
              <span class="flex-1">Disetujui</span>
              <input type="hidden" v-model="formData.status" />
            </div>
            <div v-else-if="formData.status === 'Rejected'" class="space-y-2">
              <div class="rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700 dark:border-red-800 dark:bg-red-900/10 dark:text-red-300">
                <div class="font-medium">Pengajuan ditolak</div>
                <div class="text-xs">Anda dapat mengajukan ulang jika diperlukan.</div>
              </div>
              <div class="flex items-center gap-3">
                <button type="button" @click="() => { formData.status = 'Pending' }" class="rounded bg-brand-500 px-3 py-1 text-sm text-white">Ajukan lagi</button>
                <button type="button" @click="() => { formData.status = 'Draft' }" class="rounded bg-gray-100 px-3 py-1 text-sm">Simpan sebagai Draft</button>
              </div>
            </div>
            <div v-else>
              <SearchableSelect v-model="formData.status" :options="statusOptionsFiltered" placeholder="Pilih status" />
            </div>
          </div>

          <div class="lg:col-span-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Nominal <span class="text-red-500">*</span></label>
            <div class="relative">
              <input type="number" v-model.number="formData.amount" placeholder="Masukkan nominal" min="1" step="1" required class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-24 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" @input="formatAmountInput" />
              <span class="absolute text-gray-500 -translate-y-1/2 pointer-events-none right-4 top-1/2 dark:text-gray-400 text-sm">{{ formattedAmount }}</span>
            </div>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Minimal nominal: Rp 1</p>
            <p v-if="isAmountExceeding" class="mt-1 text-xs text-red-500">Nominal melebihi sisa alokasi: {{ formatCurrency(programBalance?.remaining || 0) }}</p>
            <p v-else-if="formErrors.amount" class="mt-1 text-xs text-red-500">{{ formErrors.amount }}</p>
          </div>

          <div class="lg:col-span-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Tanggal Digunakan <span class="text-red-500">*</span></label>
            <div class="relative">
              <flat-pickr v-model="formData.usedAt" :config="flatpickrConfig" class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pl-4 pr-11 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800" placeholder="Pilih tanggal digunakan" required />
            </div>
          </div>

          <div class="lg:col-span-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Rentang Tanggal Transaksi (opsional)</label>
            <div class="flex items-center gap-2">
              <flat-pickr v-model="allocationRange" :config="flatpickrRangeConfig" class="dark:bg-dark-900 h-10 flex-1 appearance-none rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" placeholder="Pilih rentang tanggal" />
              <button v-if="((Array.isArray(allocationRange) && allocationRange.length > 0) || (allocationRange && String(allocationRange).length > 0))" type="button" title="Hapus Leader" @click="clearAllocationRange" class="h-10 w-10 flex items-center justify-center rounded-lg border border-gray-300 bg-white text-sm text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path><path d="M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
              </button>
            </div>
          </div>


          

          <div v-if="programDetail || loadingBalance" class="lg:col-span-2">
            <!-- Loading indicator when submission type changes -->
            <div v-if="loadingBalance" class="mt-3 rounded-lg border border-gray-200 bg-gray-50 p-3 text-sm text-gray-700 dark:border-gray-700 dark:bg-gray-900/40">
              <div class="flex items-center justify-center py-8">
                <svg class="animate-spin h-8 w-8 text-brand-500 dark:text-brand-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="ml-3 text-gray-600 dark:text-gray-400">Memuat detail program...</span>
              </div>
            </div>

            <div v-else-if="programDetail" class="mt-3 rounded-lg border border-gray-200 bg-gray-50 p-3 text-sm text-gray-700 dark:border-gray-700 dark:bg-gray-900/40">
              <div class="flex items-center justify-between">
                <div>
                  <div class="text-xs text-gray-500">Program</div>
                  <div class="text-lg font-medium">{{ programDetail.nama_program || programDetail.nama || '' }}</div>
                </div>
                <div class="text-right text-xs text-gray-500">
                  <div v-if="programDetail && Array.isArray(programDetail.shares)">
                    <div class="flex flex-wrap justify-end gap-2">
                      <template v-for="s in programShares" :key="s.id || s.share_id || s.program_share_id">
                        <span class="inline-flex items-center rounded px-2 py-0.5 bg-gray-100 text-xs text-gray-700 dark:bg-gray-800/40 dark:text-gray-300">
                          <span v-if="(s.type === 'percentage' || (s.type_key && String(s.type_key).toLowerCase().includes('percent')))">
                            {{ Number(s.value || 0) }}%
                          </span>
                          <span v-else>
                            {{ formatCurrency(Number(s.value || 0)) }}
                          </span>
                        </span>
                      </template>
                    </div>
                  </div>
                  <div v-else class="text-xs">&nbsp;</div>
                </div>
              </div>

              <div v-if="programBalance" class="mt-3 border-t pt-3">
                <div class="flex items-center justify-between">
                  <div>
                    <div class="text-xs text-gray-500">
                      <span v-if="programBalance.start_date && programBalance.end_date">Sisa alokasi {{ formatDateLong(programBalance.start_date) }} - {{ formatDateLong(programBalance.end_date) }}</span>
                      <span v-else>Sisa alokasi bulan {{ formatMonthYear(programBalance.month) }}</span>
                    </div>
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

                <div class="mt-3 text-sm text-gray-700">
                  <div>Estimasi sisa setelah pengajuan: <span class="font-medium">{{ formatCurrency((programBalance.remaining || 0) - (formData.amount || 0)) }}</span></div>
                  <div v-if="(formData.amount || 0) > (programBalance.remaining || 0)" class="mt-1 text-xs text-red-500">Kekurangan: {{ formatCurrency((formData.amount || 0) - (programBalance.remaining || 0)) }}</div>
                </div>

                <!-- Program Breakdown for aggregate balance -->
                <div v-if="programBalance.program_breakdown && programBalance.program_breakdown.length > 0" class="mt-3 border-t pt-3">
                  <div class="text-sm font-medium text-gray-700 mb-2">Detail Program:</div>
                  <div class="space-y-1 max-h-48 overflow-y-auto">
                    <div v-for="prog in programBalance.program_breakdown" :key="prog.program_id" class="flex items-center justify-between text-xs bg-gray-50 dark:bg-gray-800/20 rounded px-2 py-1">
                      <div class="font-medium">{{ prog.program_name }}</div>
                      <div class="text-gray-600">{{ formatCurrency(prog.remaining) }}</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="lg:col-span-2">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Tujuan Pengajuan</label>
            <textarea v-model="formData.purpose" placeholder="Masukkan tujuan atau alasan pengajuan dana" maxlength="1000" rows="4" class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"></textarea>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ formData.purpose.length }}/1000 karakter</p>
          </div>
        </div>

        <div class="flex items-center gap-3 mt-6 lg:justify-end">
          <button @click="handleCancel" type="button" class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] sm:w-auto">Batal</button>
          <button :disabled="isSubmitting || isAmountExceeding" type="submit" :class="['flex w-full justify-center rounded-lg px-4 py-2.5 text-sm font-medium sm:w-auto', (isSubmitting || isAmountExceeding) ? 'bg-gray-300 text-gray-600 cursor-not-allowed' : 'bg-brand-500 text-white hover:bg-brand-600']">{{ isEditMode ? 'Simpan Perubahan' : 'Simpan' }}</button>
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

const flatpickrRangeConfig = {
  dateFormat: 'Y-m-d',
  mode: 'range' as any,
  altInput: true,
  altFormat: 'd F Y',
  locale: 'id' as any,
  wrap: true,
  clickOpens: true,
  allowInput: false,
  // no minDate here; allow selection of past ranges as needed
}

const applicantList: Ref<Array<{ value: string; label: string }>> = ref([])
const submissionTypeList: Ref<Array<{ value: string; label: string; share_key?: string }>> = ref([])
const kantorCabangList: Ref<Array<{ value: string; label: string }>> = ref([])
const programList: Ref<Array<{ value: string; label: string }>> = ref([])

const applicantSearchInput = ref('')
const submissionTypeSearchInput = ref('')
const kantorCabangSearchInput = ref('')
const loadingProgramList = ref(false)
const allocationRange = ref([] as any)

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

const formErrors = reactive({
  amount: '' as string,
})

const isSubmitting = ref(false)

const isAmountExceeding = computed(() => {
  // Check balance validation for all submission types
  if (!programBalance || !programBalance.value) return false
  const rem = Number(programBalance.value.remaining || 0)
  const amt = Number(formData.amount || 0)
  return amt > rem
})

const formattedAmount = computed(() => {
  if (!formData.amount || formData.amount <= 0) return ''
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(formData.amount)
})

  const programBalance: Ref<any> = ref(null)
  const programDetail: Ref<any> = ref(null)
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

const formatDateLong = (dStr: string | null) => {
  if (!dStr) return ''
  try {
    // expect YYYY-MM-DD
    const parts = String(dStr).split('-')
    if (parts.length < 3) return dStr
    const y = Number(parts[0])
    const m = Number(parts[1]) - 1
    const day = Number(parts[2])
    const dt = new Date(Date.UTC(y, m, day))
    return new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }).format(dt)
  } catch (e) {
    return dStr
  }
}

const clearAllocationRange = () => {
  allocationRange.value = []
  // reload balance after clearing so API falls back to month-based usedAt
  loadProgramBalance()
}

const programShares = computed(() => {
  if (!programDetail.value || !Array.isArray(programDetail.value.shares)) return []
  // Get share_key from selected submission type
  const selectedType = submissionTypeList.value.find(t => t.value === formData.submissionType)
  const wanted = selectedType?.share_key || 'program'

  // prefer the wanted key, fallback to 'program', else return first matching
  const shares = programDetail.value.shares.map((s: any) => ({
    ...s,
    program_share_type_key: s.program_share_type_key ?? s.type_key ?? (s.type?.key ?? null),
  }))

  const matched = shares.filter((s: any) => String(s.program_share_type_key) === wanted)
  if (matched.length > 0) return matched

  const fallback = shares.filter((s: any) => String(s.program_share_type_key) === 'program')
  if (fallback.length > 0) return fallback

  return shares
})

const loadProgramBalance = async () => {
  loadingBalance.value = true
  try {
    // Get share_key from submissionTypeList based on selected alias
    const selectedType = submissionTypeList.value.find(t => t.value === formData.submissionType)
    const shareKey = selectedType?.share_key || 'program'
    
    // Send alias to backend for mapping
    const alias = formData.submissionType

    // if allocationRange selected (two dates), use start_date & end_date
    let url = ''
    let selectedStart: string | null = null
    let selectedEnd: string | null = null
    const parseRangeToIso = (val: any) => {
      // Return [startIso, endIso] or [null,null]
      if (!val) return [null, null]
      // If array with two entries
      if (Array.isArray(val) && val.length >= 2) {
        const toIso = (v: any) => {
          if (!v) return null
          if (typeof v === 'string' && /^\d{4}-\d{2}-\d{2}$/.test(v)) return v
          const dt = new Date(v)
          if (isNaN(dt.getTime())) return null
          return dt.toISOString().split('T')[0]
        }
        return [toIso(val[0]), toIso(val[1])]
      }
      // If single string, try to extract two yyyy-mm-dd occurrences
      if (typeof val === 'string') {
        const m = Array.from(val.matchAll(/(\d{4}-\d{2}-\d{2})/g)).map(x => x[1])
        if (m.length >= 2) return [m[0], m[1]]
        // fallback: split on non-digit sequences and try
        const parts = val.split(/[^0-9-]+/).filter(Boolean)
        if (parts.length >= 2 && /^\d{4}-\d{2}-\d{2}$/.test(parts[0]) && /^\d{4}-\d{2}-\d{2}$/.test(parts[1])) return [parts[0], parts[1]]
      }
      // If object/date-like
      try {
        const dt = new Date(val)
        if (!isNaN(dt.getTime())) {
          const iso = dt.toISOString().split('T')[0]
          return [iso, iso]
        }
      } catch (e) {}
      return [null, null]
    }
    if (allocationRange) {
      const [s, e] = parseRangeToIso(allocationRange.value)
      selectedStart = s
      selectedEnd = e
    }

    // Determine if single program or aggregate (all programs)
    if (formData.programId) {
      // Single program balance
      if (selectedStart && selectedEnd) {
        url = `/admin/api/program/${formData.programId}/balance?start_date=${encodeURIComponent(selectedStart)}&end_date=${encodeURIComponent(selectedEnd)}&alias=${encodeURIComponent(alias)}`
      } else {
        const d = new Date(formData.usedAt)
        if (isNaN(d.getTime())) {
          programBalance.value = null
          loadingBalance.value = false
          return
        }
        const month = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}`
        url = `/admin/api/program/${formData.programId}/balance?month=${month}&alias=${encodeURIComponent(alias)}&lookback=1`
      }
    } else {
      // Aggregate balance across all programs
      if (selectedStart && selectedEnd) {
        url = `/admin/api/programs/aggregate-balance?start_date=${encodeURIComponent(selectedStart)}&end_date=${encodeURIComponent(selectedEnd)}&alias=${encodeURIComponent(alias)}`
      } else {
        const d = new Date(formData.usedAt)
        if (isNaN(d.getTime())) {
          programBalance.value = null
          loadingBalance.value = false
          return
        }
        const month = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}`
        url = `/admin/api/programs/aggregate-balance?month=${month}&alias=${encodeURIComponent(alias)}`
      }
    }


    const res = await fetch(url, { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    const json = await res.json()
    if (!json.success) throw new Error(json.message || 'Failed to load balance')
    // If the API didn't return start/end date but we requested a range, inject them so UI reflects selection
    if (selectedStart && selectedEnd) {
      if (!json.data) json.data = {}
      if (!json.data.start_date) json.data.start_date = selectedStart
      if (!json.data.end_date) json.data.end_date = selectedEnd
    }
    programBalance.value = json.data
    // Do NOT auto-fill `amount` for any submission type; user must enter nominal manually
  } catch (err) {
    console.error('Error loading program balance', err)
    toast.error('Gagal memuat saldo program')
    programBalance.value = null
  } finally {
    loadingBalance.value = false
  }
}

const loadProgramDetail = async () => {
  if (!formData.programId) {
    // No specific program selected = "All Programs"
    programDetail.value = {
      nama_program: 'Semua Program',
      shares: [] // Will be filled from aggregate response if needed
    }
    return
  }
  try {
    const res = await fetch(`/admin/api/program/${formData.programId}`, { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    const json = await res.json()
    if (!json.success) throw new Error(json.message || 'Failed to load program')
    programDetail.value = json.data
  } catch (err) {
    console.error('Error loading program detail', err)
    programDetail.value = null
  }
}

// Fetch submission types from API (dynamic based on program_share_types.alias)
const fetchSubmissionTypes = async () => {
  try {
    const res = await fetch('/admin/api/program-share-types/submission-types', {
      credentials: 'same-origin',
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    const json = await res.json()
    if (json.success && Array.isArray(json.data)) {
      // Use alias only for label
      submissionTypeList.value = json.data.map((item: any) => ({
        value: item.value,
        label: item.value,
        share_key: item.share_key,
        name: item.name
      }))
    } else {
      // Fallback to hardcoded values if API fails
      submissionTypeList.value = [
        { value: 'Program', label: 'Program', share_key: 'program' },
        { value: 'Operasional', label: 'Operasional', share_key: 'ops_2' },
        { value: 'Gaji Karyawan', label: 'Gaji Karyawan', share_key: 'ops_1' },
      ]
    }
  } catch (err) {
    console.error('Error fetching submission types', err)
    // Fallback to hardcoded values
    submissionTypeList.value = [
      { value: 'Program', label: 'Program', share_key: 'program' },
      { value: 'Operasional', label: 'Operasional', share_key: 'ops_2' },
      { value: 'Gaji Karyawan', label: 'Gaji Karyawan', share_key: 'ops_1' },
    ]
  }
}

// watch programId and usedAt with debounce
watch([
  () => formData.programId,
  () => formData.usedAt,
  () => allocationRange.value,
], () => {
  if (balanceTimer) window.clearTimeout(balanceTimer)
  balanceTimer = window.setTimeout(() => {
    // load program detail always when program changes
    loadProgramDetail()
    loadProgramBalance()
  }, 300)
})

// When user selects 'program' as submission type, clear nominal so they enter it manually
watch(() => formData.submissionType, (val) => {
  const v = String(val)
  // Clear amount when changing submission type (user enters manually)
  if (!isEditMode.value) {
    formData.amount = null
  }
  // when submission type changes, refresh program details/balance
  if (submissionTypeList.value.some(t => t.value === v)) {
    // Always load detail/balance (handles both single program and aggregate)
    loadProgramDetail()
    loadProgramBalance()
  } else {
    programDetail.value = null
    programBalance.value = null
  }
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
    formData.purpose = data.purpose || ''
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
  loadingProgramList.value = true
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
  } finally {
    loadingProgramList.value = false
  }
}

// Status options allowed in this form (approval statuses handled elsewhere)
const statusOptions = [
  { value: 'Draft', label: 'Draft' },
  { value: 'Pending', label: 'Diajukan' },
]

const isApplicantSelf = computed(() => String(formData.applicant) === String(currentUserId.value))

const statusOptionsFiltered = computed(() => {
  // This form never exposes 'Approved' or 'Rejected' options; approvals are handled
  // in the approval UI. Always show only Draft/Pending here.
  return statusOptions
})

const handleCancel = () => router.push('/keuangan/pengajuan-dana')

const handleSave = async () => {
  if (!formData.applicant) { toast.error('Pengaju wajib diisi'); return }
  if (!formData.submissionType) { toast.error('Tipe Pengajuan wajib diisi'); return }
  if (!formData.amount || formData.amount <= 0) { toast.error('Nominal wajib diisi dan harus lebih dari 0'); return }
  if (!formData.usedAt) { toast.error('Tanggal Digunakan wajib diisi'); return }
  if (!formData.branchId) { toast.error('Kantor Cabang wajib diisi'); return }

  if (isSubmitting.value) return
  isSubmitting.value = true

  try {
    // Client-side validation: if program-type, ensure amount not exceed remaining
    if (isAmountExceeding.value) {
      const rem = formatCurrency(Number(programBalance.value?.remaining || 0))
      toast.error(`nominal melebihi batas tidak dapat menyimpan — sisa alokasi: ${rem}`)
      isSubmitting.value = false
      return
    }

    const payload: any = {
      fundraiser_id: formData.applicant,
      submission_type: formData.submissionType,
      program_id: formData.programId || null,
      status: formData.status,
      amount: formData.amount,
      used_at: formData.usedAt,
      purpose: formData.purpose,
      kantor_cabang_id: formData.branchId,
    }

    // If user selected an explicit allocation range, include it so backend validates against same window
    if (allocationRange && Array.isArray(allocationRange.value) && allocationRange.value.length >= 2) {
      const toIso = (v: any) => {
        if (!v) return null
        if (typeof v === 'string' && /^\d{4}-\d{2}-\d{2}$/.test(v)) return v
        const dt = new Date(v)
        if (isNaN(dt.getTime())) return null
        return dt.toISOString().split('T')[0]
      }
      const [s, e] = allocationRange.value
      const startIso = toIso(s)
      const endIso = toIso(e)
      if (startIso && endIso) {
        payload.start_date = startIso
        payload.end_date = endIso
      }
    }

    const csrf = await getCsrfTokenSafe()
    const url = isEditMode.value ? `/admin/api/pengajuan-dana/${route.params.id}` : '/admin/api/pengajuan-dana'
    const method = isEditMode.value ? 'PUT' : 'POST'
    const res = await fetch(url, { method, credentials: 'same-origin', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' }, body: JSON.stringify(payload) })
    const result = await res.json()
    if (!result.success) {
      // backend may include remaining value for clearer messaging
      if (result.remaining !== undefined) {
        toast.error(`nominal melebihi batas tidak dapat menyimpan — sisa alokasi: ${formatCurrency(Number(result.remaining))}`)
        isSubmitting.value = false
        return
      }
      if (result.errors) { const first = Object.values(result.errors)[0]; toast.error(first ? String((first as any)[0]) : 'Validation failed'); isSubmitting.value = false; return }
      throw new Error(result.message || 'Failed to save')
    }

    toast.success(isEditMode.value ? 'Pengajuan dana berhasil diupdate' : 'Pengajuan dana berhasil ditambahkan')
    router.push('/keuangan/pengajuan-dana')
  } catch (err) {
    console.error('Error saving:', err)
    toast.error('Terjadi kesalahan saat menyimpan data')
  } finally {
    isSubmitting.value = false
  }
}

onMounted(async () => {
  await fetchSubmissionTypes()
  await loadOptions()
  await loadData()
})

// Default usedAt to today when creating new pengajuan
onMounted(() => {
  const today = new Date().toISOString().split('T')[0]
  if (!isEditMode.value && (!formData.usedAt || String(formData.usedAt).trim() === '')) {
    formData.usedAt = today
  }
})
</script>
          }

