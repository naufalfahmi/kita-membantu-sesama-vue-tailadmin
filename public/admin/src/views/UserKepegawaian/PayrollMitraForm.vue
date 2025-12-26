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
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Jumlah <span class="text-red-500">*</span></label>
            <input
              type="number"
              v-model.number="form.jumlah"
              placeholder="Masukkan jumlah"
              required
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
            />
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Persentase (%) <span class="text-red-500">*</span></label>
            <input
              type="text"
              v-model="persentaseDisplay"
              placeholder="Masukkan persentase"
              required
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
            />
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Total</label>
            <input
              type="number"
              :value="computedTotal"
              readonly
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400"
            />
            <p class="text-xs text-gray-500 mt-1">Total dihitung otomatis: jumlah × persentase / 100</p>
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
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import SearchableSelect from '@/components/forms/SearchableSelect.vue'
import { useToast } from 'vue-toastification'
import { useAuth } from '@/composables/useAuth'

const route = useRoute()
const router = useRouter()
const toast = useToast()
const { fetchUser, hasPermission, isAdmin } = useAuth()

const isEdit = computed(() => route.params.id !== undefined && route.params.id !== 'new')
const title = computed(() => isEdit.value ? 'Edit Payroll Mitra' : 'Tambah Payroll Mitra')

const isSaving = ref(false)
const mitraRaw = ref<any[]>([])
const programRaw = ref<any[]>([])
const mitraSearch = ref('')
const programSearch = ref('')

const mitraOptions = computed(() => mitraRaw.value.map((m: any) => ({ value: m.id, label: m.nama })))
const programOptions = computed(() => programRaw.value.map((p: any) => ({ value: p.id, label: p.nama_program })))

const form = ref({ mitra_id: '', program_id: '', nama_mitra: '', jumlah: 0, persentase: 0, total: 0 })

const computedTotal = computed(() => {
  const j = Number(form.value.jumlah) || 0
  const p = Number(form.value.persentase) || 0
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

const loadOptions = async () => {
  try {
    const [mRes, pRes] = await Promise.all([
      fetch('/admin/api/mitra?per_page=1000', { credentials: 'same-origin' }),
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
      Object.assign(form.value, json.data)
      if (json.data.mitra && json.data.mitra.id) form.value.mitra_id = json.data.mitra.id
      if (json.data.program && json.data.program.id) form.value.program_id = json.data.program.id
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
