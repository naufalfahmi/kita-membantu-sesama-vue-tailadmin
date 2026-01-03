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
              Kode
            </label>
            <input
              type="text"
              v-model="formData.kode"
              placeholder="Kode donatur"
              readonly
              class="h-11 w-full rounded-lg border border-gray-300 bg-gray-100 px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800 cursor-not-allowed"
            />
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Tipe Donor <span class="text-red-500">*</span>
            </label>
            <SearchableMultiSelect
              v-model="formData.jenis_donatur"
              :options="donorTypeOptions"
              placeholder="Pilih tipe donor"
            />
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Fundraiser
            </label>
            <SearchableSelect
              v-model="formData.pic"
              :options="picOptions"
              placeholder="Pilih Fundraiser"
              :search-input="picSearchInput"
              @update:search-input="picSearchInput = $event"
              :disabled="isFundrising"
            />
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Nama <span class="text-red-500">*</span>
            </label>
            <input
              type="text"
              v-model="formData.nama"
              placeholder="Nama donatur"
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
              Tanggal Lahir
            </label>
            <div class="relative">
              <FlatPickr
                v-model="formData.tanggal_lahir"
                :config="flatpickrConfigTanggalLahir"
                class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-11 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                placeholder="Tanggal lahir"
              />
              <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                <svg
                  class="fill-current"
                  width="20"
                  height="20"
                  viewBox="0 0 20 20"
                  fill="none"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <path
                    fill-rule="evenodd"
                    clip-rule="evenodd"
                    d="M6.66659 1.5415C7.0808 1.5415 7.41658 1.87729 7.41658 2.2915V2.99984H12.5833V2.2915C12.5833 1.87729 12.919 1.5415 13.3333 1.5415C13.7475 1.5415 14.0833 1.87729 14.0833 2.2915V2.99984L15.4166 2.99984C16.5212 2.99984 17.4166 3.89527 17.4166 4.99984V7.49984V15.8332C17.4166 16.9377 16.5212 17.8332 15.4166 17.8332H4.58325C3.47868 17.8332 2.58325 16.9377 2.58325 15.8332V7.49984V4.99984C2.58325 3.89527 3.47868 2.99984 4.58325 2.99984L5.91659 2.99984V2.2915C5.91659 1.87729 6.25237 1.5415 6.66659 1.5415ZM6.66659 4.49984H4.58325C4.30711 4.49984 4.08325 4.7237 4.08325 4.99984V6.74984H15.9166V4.99984C15.9166 4.7237 15.6927 4.49984 15.4166 4.49984H13.3333H6.66659ZM15.9166 8.24984H4.08325V15.8332C4.08325 16.1093 4.30711 16.3332 4.58325 16.3332H15.4166C15.6927 16.3332 15.9166 16.1093 15.9166 15.8332V8.24984Z"
                    fill="currentColor"
                  />
                </svg>
              </span>
            </div>
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

          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Status
            </label>
            <SearchableSelect
              v-model="formData.status"
              :options="statusOptions"
              placeholder="Status"
              :search-input="statusSearchInput"
              @update:search-input="statusSearchInput = $event"
            />
          </div>

          <div class="lg:col-span-2">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Alamat
            </label>
            <textarea
              v-model="formData.alamat"
              placeholder="Alamat lengkap"
              rows="3"
              class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            ></textarea>
          </div>
          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Provinsi</label>
            <input type="text" v-model="formData.provinsi" placeholder="Provinsi" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800" />
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Kota / Kabupaten</label>
            <input type="text" v-model="formData.kota_kab" placeholder="Kota / Kabupaten" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800" />
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Kecamatan</label>
            <input type="text" v-model="formData.kecamatan" placeholder="Kecamatan" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800" />
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Kelurahan</label>
            <input type="text" v-model="formData.kelurahan" placeholder="Kelurahan" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800" />
          </div>        </div>

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
import SearchableMultiSelect from '@/components/forms/SearchableMultiSelect.vue'

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
const currentPageTitle = computed(() => (isEditMode.value ? 'Edit Donatur' : 'Tambah Donatur'))

const flatpickrConfigTanggalLahir = {
  dateFormat: 'Y-m-d',
  altInput: true,
  altFormat: 'd F Y',
  locale: 'id' as any,
  wrap: false,
  clickOpens: true,
  allowInput: false,
}

const donorTypeOptions = [
  { value: 'komunitas', label: 'Komunitas' },
  { value: 'kotak_infaq', label: 'Kotak Infaq' },
  { value: 'retail', label: 'Retail' },
]

const statusOptions = [
  { value: 'aktif', label: 'Aktif' },
  { value: 'tidak_aktif', label: 'Tidak Aktif' },
  { value: 'pending', label: 'Pending' },
]

interface KantorCabangOption {
  id: string | number
  nama?: string | null
  name?: string | null
  kode?: string | null
}

interface KaryawanOption {
  id: string | number
  nama?: string | null
  name?: string | null
}

const kantorCabangOptions = ref<KantorCabangOption[]>([])
const karyawanOptions = ref<KaryawanOption[]>([])
const kantorCabangSearchInput = ref('')
const picSearchInput = ref('')
const statusSearchInput = ref('')
const isSubmitting = ref(false)

const kantorCabangSelectOptions = computed(() =>
  kantorCabangOptions.value.map((item) => ({
    value: String(item.id),
    label: item.nama || item.name || '-',
  }))
)

const picOptions = computed(() =>
  karyawanOptions.value.map((item) => ({
    value: String(item.id),
    label: item.nama || item.name || '-',
  }))
)

const formData = reactive({
  kode: '',
  nama: '',
  jenis_donatur: [] as string[],
  pic: '',
  provinsi: '',
  kota_kab: '',
  kecamatan: '',
  kelurahan: '',
  alamat: '',
  no_handphone: '',
  email: '',
  tanggal_lahir: '',
  kantor_cabang_id: '',
  status: 'aktif',
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
    const requests: Promise<Response>[] = []

    // Request kantor cabang. If user is global admin or has a role named
    // "Admin Cabang", request the full list; otherwise request only
    // assigned kantor cabang.
    const isKantorAdmin = (() => {
      if (!currentUser.value) return false
      if (currentUser.value.is_admin) return true
      const roles = currentUser.value.roles || currentUser.value.role ? (currentUser.value.roles || [currentUser.value.role]) : []
      return Array.isArray(roles) && roles.some((r: any) => {
        const name = typeof r === 'string' ? r : r?.name
        return typeof name === 'string' && name.trim().toLowerCase() === 'admin cabang'
      })
    })()

    const kantorUrl = isKantorAdmin ? '/admin/api/kantor-cabang?per_page=1000' : '/admin/api/kantor-cabang?per_page=1000&only_assigned=1'
    requests.push(fetch(kantorUrl, { credentials: 'same-origin' }))

    // Request only users with the Fundraising role for the PIC select.
    // Note: role name in DB is sometimes stored as 'fundrising' (legacy),
    // so use that lowercase value to match existing data.
    const karyawanUrl = '/admin/api/karyawan?per_page=1000&role_name=fundrising'
    requests.push(fetch(karyawanUrl, { credentials: 'same-origin' }))

    // Fetch next kode only for new donatur
    if (!isEditMode.value) {
      requests.push(fetch('/admin/api/donatur-next-kode', { credentials: 'same-origin' }))
    }

    const responses = await Promise.all(requests)
    const [kantorRes, karyawanRes, nextKodeRes] = responses

    if (kantorRes.ok) {
      const json = await kantorRes.json()
      if (json.success) {
        const payload = Array.isArray(json.data) ? json.data : json.data?.data
        kantorCabangOptions.value = Array.isArray(payload) ? payload : []
      }
    }

    if (karyawanRes.ok) {
      const json = await karyawanRes.json()
      if (json.success) {
        const payload = Array.isArray(json.data) ? json.data : json.data?.data
        karyawanOptions.value = Array.isArray(payload) ? payload : []
      }
    }

    // If backend returned no karyawan (e.g. user has no subordinates), ensure
    // the current user is available as a PIC option so the select isn't empty.
    if (Array.isArray(karyawanOptions.value) && karyawanOptions.value.length === 0 && currentUser.value) {
      karyawanOptions.value = [currentUser.value]
    }

    if (nextKodeRes && nextKodeRes.ok) {
      const json = await nextKodeRes.json()
      if (json.success && json.data?.kode) {
        formData.kode = json.data.kode
      }
    }
  } catch (error) {
    toast.error('Gagal memuat data referensi')
  }
}

const loadData = async (id: string) => {
  try {
    const res = await fetch(`/admin/api/donatur/${id}`, { credentials: 'same-origin' })
    if (!res.ok) throw new Error('Failed to fetch donatur')
    const json = await res.json()

    if (json.success && json.data) {
      const data = json.data
      formData.kode = data.kode || ''
      formData.nama = data.nama || ''
      formData.jenis_donatur = Array.isArray(data.jenis_donatur) ? data.jenis_donatur : []
      // PIC returned as pic (id) and pic_user (object) — prefer id from pic_user
      formData.pic = data.pic_user?.id ? String(data.pic_user.id) : (data.pic || '')
      formData.alamat = data.alamat || ''
      formData.provinsi = data.provinsi || ''
      formData.kota_kab = data.kota_kab || ''
      formData.kecamatan = data.kecamatan || ''
      formData.kelurahan = data.kelurahan || ''
      formData.no_handphone = data.no_handphone || ''
      formData.email = data.email || ''
      formData.tanggal_lahir = data.tanggal_lahir || ''
      formData.kantor_cabang_id = data.kantor_cabang_id ? String(data.kantor_cabang_id) : ''
      formData.status = data.status || 'aktif'
    } else {
      toast.error(json.message || 'Donatur tidak ditemukan')
      router.push('/user-kepegawaian/donatur')
    }
  } catch (error) {
    toast.error('Gagal memuat data donatur')
    router.push('/user-kepegawaian/donatur')
  }
}

const handleCancel = () => {
  router.push('/user-kepegawaian/donatur')
}

const handleSave = async () => {
  if (!formData.nama.trim()) {
    toast.error('Nama wajib diisi')
    return
  }

  if (!formData.jenis_donatur.length) {
    toast.error('Minimal satu tipe donor harus dipilih')
    return
  }

  isSubmitting.value = true

  try {
    const tokenRes = await fetch('/admin/api/csrf-token', { credentials: 'same-origin' })
    if (!tokenRes.ok) throw new Error('Failed to fetch CSRF token')
    const tokenJson = await tokenRes.json()

    const toNullable = (value: string | null | undefined) => {
      if (value === undefined || value === null) {
        return null
      }
      const trimmed = String(value).trim()
      return trimmed !== '' ? trimmed : null
    }

    const payload: Record<string, any> = {
      nama: formData.nama.trim(),
      jenis_donatur: formData.jenis_donatur,
      pic: toNullable(formData.pic),
      alamat: toNullable(formData.alamat),
      provinsi: toNullable(formData.provinsi),
      kota_kab: toNullable(formData.kota_kab),
      kecamatan: toNullable(formData.kecamatan),
      kelurahan: toNullable(formData.kelurahan),
      no_handphone: toNullable(formData.no_handphone),
      email: toNullable(formData.email),
      tanggal_lahir: formData.tanggal_lahir || null,
      kantor_cabang_id: toNullable(formData.kantor_cabang_id),
      status: toNullable(formData.status) || 'aktif',
    }

    if (payload.no_handphone) {
      payload.no_handphone = String(payload.no_handphone).replace(/\D+/g, '')
    }

    let url = '/admin/api/donatur'
    let method: 'POST' | 'PUT' = 'POST'

    if (isEditMode.value && route.params.id) {
      url = `/admin/api/donatur/${route.params.id}`
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
      toast.success(json.message || 'Donatur berhasil disimpan')
      router.push('/user-kepegawaian/donatur')
    } else {
      toast.error(json.message || 'Gagal menyimpan donatur')
    }
  } catch (error: any) {
    toast.error(error?.message || 'Gagal menyimpan donatur')
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

  // For Fundrising role, auto-set PIC and Kantor Cabang from current user
  if (isFundrising.value && !isEditMode.value) {
    // Set PIC to current user's name
    if (currentUser.value?.id) {
      formData.pic = String(currentUser.value.id)
    }
    // Set Kantor Cabang to current user's kantor_cabang
    if (currentUser.value?.kantor_cabang?.id) {
      formData.kantor_cabang_id = String(currentUser.value.kantor_cabang.id)
    }
  }
})
</script>

