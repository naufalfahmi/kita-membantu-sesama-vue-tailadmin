<template>
  <AdminLayout>
    <div class="min-h-screen rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12">
      <div class="mb-6 flex items-center justify-between">
        <h3 class="font-semibold text-gray-800 text-theme-xl dark:text-white/90 sm:text-2xl">
          {{ currentPageTitle }}
        </h3>
        <button
          @click="handleCancel"
          type="button"
          class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]"
        >
          Batal
        </button>
      </div>

      <form @submit.prevent="handleSave" class="flex flex-col">
        <div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2">
          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Nama <span class="text-red-500">*</span>
            </label>
            <input
              type="text"
              v-model="formData.nama"
              placeholder="Nama"
              required
              class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              No. Handphone
            </label>
            <input
              type="text"
              inputmode="numeric"
              pattern="[0-9]*"
              v-model="formData.no_handphone"
              placeholder="No. Handphone"
              class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Email
            </label>
            <input
              type="email"
              v-model="formData.email"
              placeholder="Email"
              class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Nama Bank
            </label>
            <input
              type="text"
              v-model="formData.nama_bank"
              placeholder="Nama Bank"
              class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              No. Rekening
            </label>
            <input
              type="number"
              v-model="formData.no_rekening"
              placeholder="No. Rekening"
              class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Tanggal Lahir
            </label>
            <div class="relative">
              <FlatPickr
                v-model="formData.tanggal_lahir"
                :config="flatpickrConfig"
                class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pl-4 pr-11 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                placeholder="Tanggal Lahir"
                readonly
              />
              <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path
                    fill-rule="evenodd"
                    clip-rule="evenodd"
                    d="M6.66659 1.5415C7.0808 1.5415 7.41658 1.87729 7.41658 2.2915V2.99984H12.5833V2.2915C12.5833 1.87729 12.919 1.5415 13.3333 1.5415C13.7475 1.5415 14.0833 1.87729 14.0833 2.2915V2.99984L15.4166 2.99984C16.5212 2.99984 17.4166 3.89527 17.4166 4.99984V7.49984V15.8332C17.4166 16.9377 16.5212 17.8332 15.4166 17.8332H4.58325C3.47868 17.8332 2.58325 16.9377 2.58325 15.8332V7.49984V4.99984C2.58325 3.89527 3.47868 2.99984 4.58325 2.99984L5.91659 2.99984V2.2915C5.91659 1.87729 6.25237 1.5415 6.66659 1.5415ZM6.66659 4.49984H4.58325C4.30711 4.49984 4.08325 4.7237 4.08325 4.99984V6.74984H15.9166V4.99984C15.9166 4.7237 15.6927 4.49984 15.4166 4.49984H13.3333H6.66659ZM15.9166 8.24984H4.08325V15.8332C4.08325 16.1093 4.30711 16.3332 4.58325 16.3332H15.4166C15.6927 16.3332 15.9166 16.1093 15.9166 15.8332V8.24984Z"
                    fill=""
                  />
                </svg>
              </span>
            </div>
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Pendidikan
            </label>
            <input
              type="text"
              v-model="formData.pendidikan"
              placeholder="Pendidikan"
              class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Kantor Cabang
            </label>
            <SearchableSelect
              v-model="formData.kantor_cabang_id"
              :options="kantorCabangSelectOptions"
              placeholder="Kantor Cabang"
              :search-input="kantorCabangSearchInput"
              @update:search-input="kantorCabangSearchInput = $event"
              :disabled="isFundrising"
            />
          </div>
        </div>

        <div class="mt-6 flex items-center gap-3 lg:justify-end">
          <button
            @click="handleCancel"
            type="button"
            class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] sm:w-auto"
          >
            Batal
          </button>
          <button
            type="submit"
            :disabled="isSubmitting"
            class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 disabled:cursor-not-allowed disabled:bg-brand-300 sm:w-auto"
          >
            {{ isEditMode ? 'Simpan Perubahan' : 'Simpan' }}
          </button>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { reactive, computed, ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'
import FlatPickr from 'vue-flatpickr-component'
import 'flatpickr/dist/flatpickr.css'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import SearchableSelect from '@/components/forms/SearchableSelect.vue'

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
const currentPageTitle = computed(() => (isEditMode.value ? 'Edit Mitra' : 'Tambah Mitra'))

const flatpickrConfig = {
  dateFormat: 'Y-m-d',
  altInput: true,
  altFormat: 'd F Y',
  locale: 'id' as any,
  wrap: false,
  clickOpens: true,
  allowInput: false,
}

const kantorCabangOptions = ref<any[]>([])
const kantorCabangSearchInput = ref('')
const isSubmitting = ref(false)

const kantorCabangSelectOptions = computed(() =>
  kantorCabangOptions.value.map((item: any) => ({
    value: String(item.id),
    label: item.nama || item.name || '-',
  }))
)

const formData = reactive({
  nama: '',
  email: '',
  no_handphone: '',
  nama_bank: '',
  no_rekening: '',
  tanggal_lahir: '',
  pendidikan: '',
  kantor_cabang_id: '',
})

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

const fetchReferenceData = async () => {
  try {
    const res = await fetch('/admin/api/kantor-cabang?per_page=1000', { credentials: 'same-origin' })
    if (!res.ok) throw new Error('Failed to load kantor cabang')
    const json = await res.json()
    if (json.success) {
      kantorCabangOptions.value = json.data || []
    }
  } catch (error) {
    toast.error('Gagal memuat data kantor cabang')
  }
}

const loadData = async (id: string) => {
  try {
    const res = await fetch(`/admin/api/mitra/${id}`, { credentials: 'same-origin' })
    if (!res.ok) throw new Error('Failed to fetch mitra')
    const json = await res.json()

    if (json.success && json.data) {
      const data = json.data
      formData.nama = data.nama || ''
      formData.email = data.email || ''
      formData.no_handphone = data.no_handphone || ''
      formData.nama_bank = data.nama_bank || ''
      formData.no_rekening = data.no_rekening || ''
      formData.tanggal_lahir = data.tanggal_lahir || ''
      formData.pendidikan = data.pendidikan || ''
      formData.kantor_cabang_id = data.kantor_cabang_id ? String(data.kantor_cabang_id) : ''
    } else {
      toast.error(json.message || 'Mitra tidak ditemukan')
      router.push('/user-kepegawaian/mitra')
    }
  } catch (error) {
    toast.error('Gagal memuat data mitra')
    router.push('/user-kepegawaian/mitra')
  }
}

const handleCancel = () => {
  router.push('/user-kepegawaian/mitra')
}

const handleSave = async () => {
  if (!formData.nama.trim()) {
    toast.error('Nama wajib diisi')
    return
  }

  isSubmitting.value = true

  try {
    const tokenRes = await fetch('/admin/api/csrf-token', { credentials: 'same-origin' })
    if (!tokenRes.ok) throw new Error('Failed to fetch CSRF token')
    const tokenJson = await tokenRes.json()

    const toNullable = (value: string | number | null | undefined) => {
      if (value === null || value === undefined) {
        return null
      }

      const trimmed = String(value).trim()
      return trimmed !== '' ? trimmed : null
    }

    const payload: Record<string, any> = {
      nama: formData.nama.trim(),
      email: toNullable(formData.email),
      no_handphone: toNullable(formData.no_handphone),
      nama_bank: toNullable(formData.nama_bank),
      no_rekening: toNullable(formData.no_rekening),
      tanggal_lahir: formData.tanggal_lahir || null,
      pendidikan: toNullable(formData.pendidikan),
      kantor_cabang_id: toNullable(formData.kantor_cabang_id),
    }

    let url = '/admin/api/mitra'
    let method: 'POST' | 'PUT' = 'POST'

    if (isEditMode.value && route.params.id) {
      url = `/admin/api/mitra/${route.params.id}`
      method = 'PUT'
    }

    const res = await fetch(url, {
      method,
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': tokenJson.csrf_token,
      },
      credentials: 'same-origin',
      body: JSON.stringify(payload),
    })

    const json = await res.json().catch(() => ({}))

    if (!res.ok) {
      const firstError = json?.errors ? (Object.values(json.errors) as any[])[0]?.[0] : undefined
      const message = firstError || json.message || 'Request gagal'
      throw new Error(message)
    }

    if (json.success) {
      toast.success(json.message || 'Mitra berhasil disimpan')
      router.push('/user-kepegawaian/mitra')
    } else {
      toast.error(json.message || 'Gagal menyimpan mitra')
    }
  } catch (error: any) {
    toast.error(error?.message || 'Gagal menyimpan mitra')
  } finally {
    isSubmitting.value = false
  }
}

onMounted(async () => {
  await fetchCurrentUser()
  await fetchReferenceData()

  if (isEditMode.value && route.params.id) {
    await loadData(route.params.id as string)
  }

  // For Fundrising role, auto-set Kantor Cabang from current user
  if (isFundrising.value && !isEditMode.value) {
    if (currentUser.value?.kantor_cabang?.id) {
      formData.kantor_cabang_id = String(currentUser.value.kantor_cabang.id)
    }
  }
})
</script>

