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
          Kembali
        </button>
      </div>

      <form @submit.prevent="handleSave" class="flex flex-col">
        <div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2">
          <div class="lg:col-span-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Nominal <span class="text-red-500">*</span>
            </label>
            <div class="relative">
              <input
                type="number"
                v-model.number="formData.amount"
                placeholder="Masukkan nominal"
                min="1"
                step="1"
                required
                class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-24 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
              />
              <span class="absolute text-gray-500 -translate-y-1/2 pointer-events-none right-4 top-1/2 dark:text-gray-400 text-sm">{{ formattedAmountDisplay }}</span>
            </div>
          </div>

          <div class="lg:col-span-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Nama Penyaluran <span class="text-red-500">*</span>
            </label>
            <input
              type="text"
              v-model="formData.program"
              placeholder="Masukkan nama program atau tahapannya"
              required
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
          </div>

          <div class="lg:col-span-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Tipe Penyaluran <span class="text-red-500">*</span></label>
            <SearchableSelect
              v-model="formData.submissionType"
              :options="submissionTypeList"
              placeholder="Pilih tipe penyaluran"
              :search-input="submissionTypeSearchInput"
              @update:search-input="submissionTypeSearchInput = $event"
            />
            <p class="mt-2 text-sm text-gray-600">Sisa kredit untuk <span class="font-medium">{{ formData.submissionType }}</span>: <span class="font-medium">{{ formattedCreditTotal }}</span></p>
          </div>

          <div class="lg:col-span-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              PIC <span class="text-red-500">*</span>
            </label>
            <input
              type="text"
              v-model="formData.pic"
              placeholder="Masukkan penanggung jawab penyaluran"
              required
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
          </div>

          <div class="lg:col-span-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Kelurahan</label>
            <input type="text" v-model="formData.village" placeholder="Masukkan kelurahan" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
          </div>
          <div class="lg:col-span-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Kecamatan</label>
            <input type="text" v-model="formData.district" placeholder="Masukkan kecamatan" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
          </div>
          <div class="lg:col-span-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Kota</label>
            <input type="text" v-model="formData.city" placeholder="Masukkan kota" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
          </div>
          <div class="lg:col-span-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Provinsi</label>
            <input type="text" v-model="formData.province" placeholder="Masukkan provinsi" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
          </div>
          <div class="lg:col-span-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Kode Pos</label>
            <input type="text" v-model="formData.postalCode" placeholder="Masukkan kode pos" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
          </div>

          <div class="lg:col-span-2">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Alamat</label>
            <textarea v-model="formData.address" placeholder="Masukkan detail alamat lengkap" rows="3" class="dark:bg-dark-900 h-auto w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"></textarea>
          </div>

          <div class="lg:col-span-2">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Laporan</label>
            <textarea v-model="formData.report" placeholder="Masukkan catatan laporan hasil penyaluran" rows="4" class="dark:bg-dark-900 h-auto w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"></textarea>
          </div>

          <div class="lg:col-span-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Kantor Cabang <span class="text-red-500">*</span>
            </label>
            <template v-if="currentUser?.is_admin">
              <SearchableSelect
                v-model="formData.branchId"
                :options="kantorCabangList"
                placeholder="Pilih atau cari kantor cabang"
                :search-input="kantorCabangSearchInput"
                @update:search-input="kantorCabangSearchInput = $event"
              />
            </template>
            <template v-else>
              <input type="text" :value="currentUser?.kantor_cabang?.nama || ''" readonly class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900/50 dark:text-white/90" />
            </template>
          </div>

          <div class="lg:col-span-2">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Gambar Dokumentasi
            </label>
            <div class="space-y-4">
              <div class="relative">
                <input type="file" ref="fileInputRef" @change="handleFileSelect" multiple accept="image/*" class="hidden" id="image-upload" />
                <label for="image-upload" class="flex cursor-pointer items-center justify-center rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 px-6 py-8 text-center transition-colors hover:border-brand-300 hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-900/50">
                  <div class="text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400"><span class="font-medium text-brand-500">Klik untuk upload</span> atau drag and drop</p>
                    <p class="text-xs text-gray-500">PNG, JPG hingga 20MB</p>
                  </div>
                </label>
              </div>

              <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5">
                <div v-for="(img, index) in existingImages" :key="'old-' + img.id" class="relative group">
                  <div class="aspect-square overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
                    <img :src="img.url" class="h-full w-full object-cover" />
                  </div>
                  <button type="button" @click="removeExistingFile(index, img.id)" class="absolute -right-2 -top-2 flex h-6 w-6 items-center justify-center rounded-full bg-red-500 text-white shadow-sm hover:bg-red-600">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                  </button>
                  <div class="mt-1 text-[10px] text-gray-400 text-center">Existing</div>
                </div>

                <div v-for="(file, index) in selectedFiles" :key="'new-' + index" class="relative group">
                  <div class="aspect-square overflow-hidden rounded-lg border border-brand-200 bg-brand-50 dark:border-brand-900/30">
                    <img :src="getFilePreview(file)" class="h-full w-full object-cover" />
                  </div>
                  <button type="button" @click="removeFile(index)" class="absolute -right-2 -top-2 flex h-6 w-6 items-center justify-center rounded-full bg-gray-500 text-white shadow-sm hover:bg-gray-600">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                  </button>
                  <div class="mt-1 text-[10px] text-brand-500 text-center truncate px-1">Baru</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="flex items-center gap-3 mt-6 lg:justify-end">
          <button @click="handleCancel" type="button" class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 sm:w-auto">Kembali</button>
          <button type="submit" class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto">
            {{ isEditMode ? 'Update' : 'Buat' }}
          </button>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { reactive, computed, ref, onMounted, watch } from 'vue'
import { useToast } from 'vue-toastification'
import { useRoute, useRouter } from 'vue-router'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import SearchableSelect from '@/components/forms/SearchableSelect.vue'

const route = useRoute()
const router = useRouter()
const toast = useToast()

const isEditMode = computed(() => route.params.id !== undefined && route.params.id !== 'new')
const currentPageTitle = computed(() => isEditMode.value ? 'Edit Penyaluran' : 'Buat Penyaluran')

// State
const currentUser = ref<any>(null)
const kantorCabangList = ref<Array<{ value: string; label: string }>>([])
const submissionTypeList = ref<Array<{ value: string; label: string }>>([])
const creditTotal = ref<number>(0)

// Search inputs
const kantorCabangSearchInput = ref('')
const submissionTypeSearchInput = ref('')

// Form & Files
const formData = reactive({
  amount: null as number | null,
  program: '',
  submissionType: '',
  pic: '',
  village: '',
  district: '',
  city: '',
  province: '',
  postalCode: '',
  address: '',
  report: '',
  branchId: '',
  pengajuanId: '',
})

const fileInputRef = ref<HTMLInputElement | null>(null)
const selectedFiles = ref<File[]>([])
const existingImages = ref<Array<{ id: number; url: string }>>([])
const deletedImageIds = ref<number[]>([])

// Formatters
const formattedCreditTotal = computed(() => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(creditTotal.value || 0))
const formattedAmountDisplay = computed(() => {
  const v = Number(formData.amount || 0)
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(v)
})

// Methods: API Fetching
const fetchSubmissionTypes = async () => {
  try {
    const res = await fetch('/admin/api/program-share-types/submission-types', { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    const json = await res.json()
    if (json.success && Array.isArray(json.data)) {
      submissionTypeList.value = json.data.map((item: any) => ({ value: item.value, label: item.value }))
      if (!formData.submissionType && submissionTypeList.value.length > 0) formData.submissionType = submissionTypeList.value[0].value
    }
  } catch (err) { console.error(err) }
}

const fetchKantorCabangOptions = async () => {
  try {
    const res = await fetch('/admin/api/kantor-cabang?per_page=1000', { credentials: 'same-origin' })
    const json = await res.json()
    const dataArray = json.success && json.data ? (Array.isArray(json.data) ? json.data : json.data.data || []) : []
    kantorCabangList.value = dataArray.map((item: any) => ({ value: item.id, label: item.nama }))
  } catch (e) { console.error(e) }
}

const fetchCurrentUser = async () => {
  try {
    const res = await fetch('/admin/api/user', { credentials: 'same-origin' })
    if (res.ok) {
      const json = await res.json()
      if (json.success && json.user) {
        currentUser.value = json.user
        if (currentUser.value?.kantor_cabang?.id) formData.branchId = String(currentUser.value.kantor_cabang.id)
        if (currentUser.value?.is_admin) await fetchKantorCabangOptions()
      }
    }
  } catch (e) { console.error(e) }
}

const loadCreditForType = async (type: string) => {
  if (!type) { creditTotal.value = 0; return }
  try {
    const res = await fetch(`/admin/api/penyaluran/my-credit?type=${encodeURIComponent(type)}`, { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    const json = await res.json()
    creditTotal.value = json?.data?.remaining || 0
  } catch (e) { creditTotal.value = 0 }
}

// Methods: Data Loading
const loadData = async () => {
  if (isEditMode.value && route.params.id) {
    try {
      const res = await fetch(`/admin/api/penyaluran/${route.params.id}`, { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      const json = await res.json()
      if (json.success && json.data) {
        const p = json.data
        formData.amount = Number(p.amount)
        formData.program = p.program_name
        formData.submissionType = p.submission_type
        formData.pic = p.pic
        formData.village = p.village
        formData.district = p.district
        formData.city = p.city
        formData.province = p.province
        formData.postalCode = p.postal_code
        formData.address = p.address
        formData.report = p.report
        formData.branchId = String(p.kantor_cabang_id)
        
        // Load Existing Images
        if (p.images) {
          existingImages.value = p.images.map((img: any) => ({
            id: img.id,
            url: img.url
          }))
        }
      }
    } catch (err) { console.error(err) }
  }
}

// Methods: File Handling
const handleFileSelect = (event: Event) => {
  const target = event.target as HTMLInputElement
  if (target.files) {
    const files = Array.from(target.files)
    const maxSize = 20 * 1024 * 1024
    const validFiles = files.filter(f => f.size <= maxSize)
    if (validFiles.length < files.length) toast.warning('Beberapa file melebihi 20MB')
    selectedFiles.value.push(...validFiles)
    if (fileInputRef.value) fileInputRef.value.value = ''
  }
}

const getFilePreview = (file: File) => URL.createObjectURL(file)

const removeFile = (index: number) => {
  selectedFiles.value.splice(index, 1)
}

const removeExistingFile = (index: number, id: number) => {
  existingImages.value.splice(index, 1)
  deletedImageIds.value.push(id)
}

// Methods: Form Submission
const handleSave = async () => {
  if (!formData.amount || !formData.program || !formData.pic || !formData.branchId) {
    toast.error('Mohon lengkapi field wajib (*)')
    return
  }

  try {
    const formDataToSend = new FormData()
    formDataToSend.append('amount', String(formData.amount))
    formDataToSend.append('submission_type', formData.submissionType)
    formDataToSend.append('program_name', formData.program)
    formDataToSend.append('pic', formData.pic)
    formDataToSend.append('village', formData.village || '')
    formDataToSend.append('district', formData.district || '')
    formDataToSend.append('city', formData.city || '')
    formDataToSend.append('province', formData.province || '')
    formDataToSend.append('postal_code', formData.postalCode || '')
    formDataToSend.append('address', formData.address || '')
    formDataToSend.append('report', formData.report || '')
    formDataToSend.append('kantor_cabang_id', formData.branchId)
    
    if (formData.pengajuanId) formDataToSend.append('pengajuan_dana_id', formData.pengajuanId)

    // Add New Files
    selectedFiles.value.forEach(file => formDataToSend.append('images[]', file))
    
    // Add Deleted Image IDs
    deletedImageIds.value.forEach(id => formDataToSend.append('deleted_image_ids[]', String(id)))

    if (isEditMode.value) formDataToSend.append('_method', 'PUT')

    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
    const url = isEditMode.value ? `/admin/api/penyaluran/${route.params.id}` : '/admin/api/penyaluran'
    
    const res = await fetch(url, {
      method: 'POST',
      body: formDataToSend,
      credentials: 'same-origin',
      headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' }
    })

    const json = await res.json()
    if (json.success) {
      toast.success('Berhasil disimpan')
      router.push('/keuangan/penyaluran').then(() => {
        window.dispatchEvent(new CustomEvent('penyaluran:changed'))
      })
    } else {
      toast.error(json.message || 'Gagal menyimpan')
    }
  } catch (error) {
    toast.error('Terjadi kesalahan sistem')
  }
}

const handleCancel = () => router.push('/keuangan/penyaluran')

// Lifecycle & Watchers
onMounted(async () => {
  await fetchSubmissionTypes()
  await fetchCurrentUser()
  
  // Handle prefill from Pengajuan Dana
  const pengajuanId = route.query.pengajuan_id
  if (pengajuanId) {
    try {
      const res = await fetch(`/admin/api/pengajuan-dana/${pengajuanId}`, { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      const json = await res.json()
      if (json.success && json.data) {
        const d = json.data
        formData.pengajuanId = String(pengajuanId)
        formData.submissionType = d.submission_type || formData.submissionType
        formData.program = d.program?.nama || d.program?.nama_program || formData.program
        formData.pic = d.fundraiser?.name || formData.pic
        formData.amount = d.amount ? Number(d.amount) : null
        formData.report = d.purpose || formData.report
      }
    } catch (e) { console.error(e) }
  }
  
  await loadData()
  loadCreditForType(formData.submissionType)
})

watch(() => formData.submissionType, (val) => loadCreditForType(String(val || '')))
</script>