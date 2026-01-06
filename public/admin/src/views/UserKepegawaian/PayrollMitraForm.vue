<template>
  <AdminLayout>
    <div class="min-h-screen rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12">
      <div class="mb-6 flex items-center justify-between">
        <h3 class="font-semibold text-gray-800 text-theme-xl dark:text-white/90 sm:text-2xl">{{ title }}</h3>
      </div>

      <form @submit.prevent="handleSave" class="flex flex-col">
        <div class="mb-6 grid grid-cols-1 gap-4 lg:grid-cols-2">
          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Pilih Mitra <span class="text-red-500">*</span></label>
            <SearchableSelect
              v-model="form.mitra_id"
              :options="mitraOptions"
              placeholder="Pilih Mitra"
              :search-input="mitraSearch"
              @update:search-input="mitraSearch = $event"
            />
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Pilih Program <span class="text-red-500">*</span></label>
            <SearchableSelect
              v-model="form.program_id"
              :options="programOptions"
              placeholder="Pilih Program"
              :search-input="programSearch"
              @update:search-input="programSearch = $event"
            />
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Tanggal Payroll <span class="text-red-500">*</span></label>
            <flat-pickr
              v-model="form.payroll_date"
              :config="{ dateFormat: 'Y-m-d' }"
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
            />
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Jumlah <span class="text-red-500">*</span></label>
            <div class="relative">
              <input
                type="number"
                v-model.number="form.jumlah"
                placeholder="Masukkan jumlah"
                required
                class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-24 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
              />
              <span class="absolute text-gray-500 -translate-y-1/2 pointer-events-none right-4 top-1/2 dark:text-gray-400 text-sm">
                Rp&nbsp;{{ form.jumlah ? Number(form.jumlah).toLocaleString('id-ID') : '0' }}
              </span>
            </div>
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ shareLabel }} <span class="text-red-500">*</span></label>
            <input
              type="text"
              v-model="persentaseDisplay"
              placeholder="Masukkan persentase"
              required
              :readonly="isShareNominal"
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
              :class="isShareNominal ? 'cursor-not-allowed bg-gray-100/70 dark:bg-gray-900/70' : ''"
            />
            <p v-if="shareNote" class="text-xs text-gray-500 mt-1 dark:text-gray-400">
              {{ shareNote }}
            </p>
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Total</label>
            <div class="relative">
              <input
                type="number"
                :value="computedTotal"
                readonly
                class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-24 text-sm text-gray-800 placeholder:text-gray-400"
              />
              <span class="absolute text-gray-500 -translate-y-1/2 pointer-events-none right-4 top-1/2 dark:text-gray-400 text-sm">
                Rp&nbsp;{{ computedTotal ? Number(computedTotal).toLocaleString('id-ID') : '0' }}
              </span>
            </div>
            <p class="text-xs text-gray-500 mt-1 dark:text-gray-400">
              <span v-if="isShareNominal">Total mengikuti nominal fee mitra dari konfigurasi program.</span>
              <span v-else>Total dihitung otomatis: jumlah × persentase / 100</span>
            </p>
          </div>
        </div>

        <div class="flex items-center gap-3 mt-6 lg:justify-end">
          <button @click="handleCancel" type="button" class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 sm:w-auto">Batal</button>
          <button type="submit" :disabled="isSaving" class="flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto">{{ isSaving ? 'Menyimpan...' : 'Simpan' }}</button>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import SearchableSelect from '@/components/forms/SearchableSelect.vue'
import { useToast } from 'vue-toastification'
import { useAuth } from '@/composables/useAuth'
import flatPickr from 'vue-flatpickr-component'
import 'flatpickr/dist/flatpickr.css'

const route = useRoute()
const router = useRouter()
const toast = useToast()
const { fetchUser, user, isAdmin } = useAuth()

const isEdit = computed(() => route.params.id !== undefined && route.params.id !== 'new')
const title = computed(() => isEdit.value ? 'Edit Payroll Mitra' : 'Tambah Payroll Mitra')

const isSaving = ref(false)
const mitraRaw = ref<any[]>([])
const programRaw = ref<any[]>([])
const mitraSearch = ref('')
const programSearch = ref('')

const mitraOptions = computed(() => mitraRaw.value.map((m: any) => ({ value: m.id, label: m.nama })))
const programOptions = computed(() => programRaw.value.map((p: any) => ({ value: p.id, label: p.nama_program })))

const todayDate = new Date().toISOString().split('T')[0]
const form = ref({ mitra_id: '', program_id: '', nama_mitra: '', jumlah: 0, persentase: 0, total: 0, payroll_date: todayDate })

const FEE_SHARE_KEY = 'fee_mitra'
const shareInfo = ref<{ type: string; value: number | null; key: string | null }>({
  type: 'percentage',
  value: null,
  key: null,
})

const shareLabel = computed(() => (shareInfo.value.type === 'nominal' ? 'Nominal Fee Mitra' : 'Persentase (%)'))
const isShareNominal = computed(() => shareInfo.value.type === 'nominal')

const formatCurrency = (value: number | null | undefined) =>
  new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value ?? 0)

const formatPercent = (value: number | null | undefined) => {
  if (value == null || Number.isNaN(value)) return '0%'
  if (Number.isInteger(value)) return `${value}%`
  return `${Number(value).toFixed(2).replace(/\.00$/, '')}%`
}

const shareNote = computed(() => {
  const value = shareInfo.value.value
  if (value == null) return ''
  if (shareInfo.value.type === 'nominal') {
    return `Fee Mitra ditetapkan sebagai nominal: ${formatCurrency(value)}`
  }
  return `Persentase default dari program: ${formatPercent(value)}`
})

const computedTotal = computed(() => {
  const j = Number(form.value.jumlah) || 0
  const p = Number(form.value.persentase) || 0
  if (isShareNominal.value && shareInfo.value.value !== null) {
    return Number(shareInfo.value.value)
  }
  return Number((j * p) / 100)
})

const persentaseDisplay = computed({
  get() {
    const p = Number(form.value.persentase) || 0
    return p.toFixed(0)
  },
  set(val: string) {
    // remove percent sign and commas, accept decimals but store as number (percentage)
    const cleaned = String(val).replace(/%/g, '').replace(/,/g, '').trim()
    const parsed = parseFloat(cleaned)
    form.value.persentase = isNaN(parsed) ? 0 : parsed
  }
})

const suppressShareAutoFill = ref(false)

const loadProgramShare = async (programId: string | null, options: { applyToForm?: boolean } = {}) => {
  const { applyToForm = true } = options
  shareInfo.value = { type: 'percentage', value: null, key: null }
  if (!programId) {
    return
  }

  try {
    const response = await fetch(`/admin/api/program/${programId}`, { credentials: 'same-origin' })
    if (!response.ok) {
      return
    }
    const json = await response.json()
    if (!json.success || !json.data) {
      return
    }

    const shares = Array.isArray(json.data.shares) ? json.data.shares : []
    let share = shares.find((item) => String(item.program_share_type_key) === FEE_SHARE_KEY)
    if (!share && shares.length > 0) {
      share = shares[0]
    }

    const shareValueRaw = share?.value
    const shareValue = shareValueRaw !== undefined && shareValueRaw !== null && !Number.isNaN(Number(shareValueRaw))
      ? Number(shareValueRaw)
      : null

    const shareType = share?.type || 'percentage'
    shareInfo.value = {
      type: shareType,
      value: shareValue,
      key: share?.program_share_type_key ?? FEE_SHARE_KEY,
    }

    if (applyToForm && shareValue !== null) {
      form.value.persentase = shareValue
    }
  } catch (error) {
    console.error('loadProgramShare:', error)
  }
}

watch(
  () => form.value.program_id,
  async (programId) => {
    if (suppressShareAutoFill.value) {
      suppressShareAutoFill.value = false
      return
    }
    form.value.jumlah = ''
    await loadProgramShare(programId)
  }
)

const loadOptions = async () => {
  try {
    // Decide mitra URL: show all mitra only to global admins (not branch admins)
    const isRoleAdminCabang = (() => {
      if (!user.value) return false
      const roles = (user.value as any).roles || ((user.value as any).role ? [(user.value as any).role] : [])
      return Array.isArray(roles) && roles.some((r: any) => {
        const name = typeof r === 'string' ? r : r?.name
        return typeof name === 'string' && name.trim().toLowerCase() === 'admin cabang'
      })
    })()

    const isGlobalAdmin = Boolean(user.value && (user.value as any).is_admin)
    const mitraUrl = (isGlobalAdmin && !isRoleAdminCabang)
      ? '/admin/api/mitra?per_page=1000'
      : '/admin/api/mitra?per_page=1000&only_assigned=1'

    const [mRes, pRes] = await Promise.all([
      fetch(mitraUrl, { credentials: 'same-origin' }),
      fetch('/admin/api/program?per_page=1000', { credentials: 'same-origin' }),
    ])
    const mJson = await mRes.json()
    const pJson = await pRes.json()
    mitraRaw.value = mJson.success ? mJson.data : []
    programRaw.value = pJson.success ? pJson.data : []
  } catch (e) {
    // ignore
  }
}

const fetchDetail = async (id:string) => {
  try {
    const res = await fetch(`/admin/api/mitra-payroll/${id}`, { credentials: 'same-origin' })
    const json = await res.json()
    if (json.success) {
      // ensure mitra_id/program_id are set to ids
      suppressShareAutoFill.value = true
      Object.assign(form.value, json.data)
      suppressShareAutoFill.value = false
      if (json.data.mitra && json.data.mitra.id) form.value.mitra_id = json.data.mitra.id
        if (json.data.program && json.data.program.id) {
          suppressShareAutoFill.value = true
          form.value.program_id = json.data.program.id
        }
      await loadProgramShare(json.data.program_id ?? null, { applyToForm: false })
    } else toast.error('Gagal memuat data')
  } catch (e) { toast.error('Gagal memuat data') }
}

const handleSave = async () => {
  if (!form.value.jumlah || !form.value.persentase) { toast.error('Jumlah dan persentase wajib diisi'); return }
  isSaving.value = true
  try {
    // ensure nama_mitra is filled from selected mitra
    if (form.value.mitra_id) {
      const m = mitraRaw.value.find((x:any) => x.id === form.value.mitra_id)
      form.value.nama_mitra = m ? m.nama : ''
    }
    // set computed total
    form.value.total = computedTotal.value

    const url = isEdit.value ? `/admin/api/mitra-payroll/${route.params.id}` : '/admin/api/mitra-payroll'
    const method = isEdit.value ? 'PUT' : 'POST'
    const tokenRes = await fetch('/admin/api/csrf-token', { credentials: 'same-origin' })
    const token = (await tokenRes.json()).csrf_token
    const res = await fetch(url, { method, headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token }, credentials: 'same-origin', body: JSON.stringify(form.value) })
    const json = await res.json()
    if (json.success) { toast.success('Berhasil'); router.push('/user-kepegawaian/payroll-mitra') }
    else toast.error(json.message || 'Gagal menyimpan')
  } catch (e) { toast.error('Gagal menyimpan') }
  finally { isSaving.value = false }
}

const handleCancel = () => router.push('/user-kepegawaian/payroll-mitra')

onMounted(async () => {
  await fetchUser()
  await loadOptions()
  if (isEdit.value && route.params.id) await fetchDetail(route.params.id as string)
})
</script>

<style scoped></style>
