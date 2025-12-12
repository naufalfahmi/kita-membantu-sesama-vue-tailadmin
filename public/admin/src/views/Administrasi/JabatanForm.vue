<template>
  <AdminLayout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />
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
        <!-- Nama Jabatan Field -->
        <div class="mb-6">
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Nama Jabatan <span class="text-red-500">*</span>
          </label>
          <input
            type="text"
            v-model="formData.name"
            placeholder="Masukkan nama jabatan"
            required
            maxlength="100"
            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
          />
          <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
            Maksimal 100 karakter
          </p>
        </div>

        <!-- Permission Matrix Section -->
        <div class="mb-6">
          <div class="mb-4 flex items-center justify-between">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">
              Hak Akses (Permissions)
            </label>
            <div class="flex items-center gap-3">
              <!-- Search Filter -->
              <input
                type="text"
                v-model="permissionSearch"
                placeholder="Cari menu..."
                class="h-9 w-48 rounded-lg border border-gray-300 bg-transparent px-3 py-1.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-2 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
              />
              <!-- Clone Button (only in edit mode) -->
              <button
                v-if="isEditMode && currentEditId"
                @click.prevent="handleClone"
                type="button"
                class="flex items-center gap-2 rounded-lg border border-brand-500 bg-transparent px-3 py-1.5 text-sm font-medium text-brand-500 hover:bg-brand-50 dark:border-brand-500 dark:hover:bg-brand-500/10"
              >
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                  <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                </svg>
                Duplikasi
              </button>
            </div>
          </div>

          <!-- Permission Matrix Controls -->
          <div class="mb-4 flex items-center gap-4 rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-800/50">
            <button
              @click.prevent="selectAllPermissions"
              type="button"
              class="text-sm font-medium text-brand-600 hover:text-brand-700 dark:text-brand-400"
            >
              Centang Semua
            </button>
            <button
              @click.prevent="deselectAllPermissions"
              type="button"
              class="text-sm font-medium text-gray-600 hover:text-gray-700 dark:text-gray-400"
            >
              Hapus Semua
            </button>
            <div class="flex-1"></div>
            <span class="text-xs text-gray-500 dark:text-gray-400">
              {{ selectedPermissionsCount }} dari {{ totalPermissionsCount }} dipilih
            </span>
          </div>

          <!-- Permission Matrix by Menu Structure -->
          <div class="space-y-4">
            <div
              v-for="mainMenu in filteredMenuStructure"
              :key="mainMenu.name"
              class="rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden"
            >
              <!-- Main Menu Header -->
              <div class="bg-gray-100 dark:bg-gray-800/50 px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                  <h4 class="font-semibold text-gray-800 dark:text-white/90">{{ mainMenu.name }}</h4>
                  <button
                    @click.prevent="toggleMainMenu(mainMenu.name)"
                    type="button"
                    class="text-xs text-brand-600 hover:text-brand-700 dark:text-brand-400"
                  >
                    {{ isMainMenuAllSelected(mainMenu.name) ? 'Hapus Semua' : 'Centang Semua' }}
                  </button>
                </div>
              </div>

              <!-- Sub Menu Table -->
              <div class="overflow-x-auto">
                <table class="w-full">
                  <thead class="bg-gray-50 dark:bg-gray-800/30">
                    <tr>
                      <th class="sticky left-0 z-10 min-w-[200px] border-r border-gray-200 bg-gray-50 px-4 py-3 text-left text-sm font-semibold text-gray-700 dark:border-gray-700 dark:bg-gray-800/30 dark:text-gray-300">
                        Sub Menu
                      </th>
                      <th class="border-r border-gray-200 px-4 py-3 text-center text-xs font-semibold text-gray-700 dark:border-gray-700 dark:text-gray-300">
                        <div class="flex flex-col items-center gap-1">
                          <span>Lihat</span>
                          <button
                            @click.prevent="toggleSubMenuAction(mainMenu.name, 'view')"
                            type="button"
                            class="text-xs text-brand-600 hover:text-brand-700 dark:text-brand-400"
                          >
                            Toggle
                          </button>
                        </div>
                      </th>
                      <th class="border-r border-gray-200 px-4 py-3 text-center text-xs font-semibold text-gray-700 dark:border-gray-700 dark:text-gray-300">
                        <div class="flex flex-col items-center gap-1">
                          <span>Buat</span>
                          <button
                            @click.prevent="toggleSubMenuAction(mainMenu.name, 'create')"
                            type="button"
                            class="text-xs text-brand-600 hover:text-brand-700 dark:text-brand-400"
                          >
                            Toggle
                          </button>
                        </div>
                      </th>
                      <th class="border-r border-gray-200 px-4 py-3 text-center text-xs font-semibold text-gray-700 dark:border-gray-700 dark:text-gray-300">
                        <div class="flex flex-col items-center gap-1">
                          <span>Ubah</span>
                          <button
                            @click.prevent="toggleSubMenuAction(mainMenu.name, 'update')"
                            type="button"
                            class="text-xs text-brand-600 hover:text-brand-700 dark:text-brand-400"
                          >
                            Toggle
                          </button>
                        </div>
                      </th>
                      <th class="border-r border-gray-200 px-4 py-3 text-center text-xs font-semibold text-gray-700 dark:border-gray-700 dark:text-gray-300">
                        <div class="flex flex-col items-center gap-1">
                          <span>Detail</span>
                          <button
                            @click.prevent="toggleSubMenuAction(mainMenu.name, 'show')"
                            type="button"
                            class="text-xs text-brand-600 hover:text-brand-700 dark:text-brand-400"
                          >
                            Toggle
                          </button>
                        </div>
                      </th>
                      <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 dark:text-gray-300">
                        <div class="flex flex-col items-center gap-1">
                          <span>Hapus</span>
                          <button
                            @click.prevent="toggleSubMenuAction(mainMenu.name, 'delete')"
                            type="button"
                            class="text-xs text-brand-600 hover:text-brand-700 dark:text-brand-400"
                          >
                            Toggle
                          </button>
                        </div>
                      </th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    <tr
                      v-for="subMenu in mainMenu.subItems"
                      :key="subMenu.permission"
                      class="hover:bg-gray-50 dark:hover:bg-gray-800/20"
                    >
                      <td class="sticky left-0 z-10 border-r border-gray-200 bg-white px-4 py-3 text-sm font-medium text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        {{ subMenu.name }}
                      </td>
                      <td class="border-r border-gray-200 px-4 py-3 text-center dark:border-gray-700">
                        <input
                          type="checkbox"
                          :checked="isPermissionSelected(`view ${subMenu.permission}`)"
                          @change="togglePermission(`view ${subMenu.permission}`)"
                          class="h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:border-gray-600 dark:bg-gray-800"
                        />
                      </td>
                      <td class="border-r border-gray-200 px-4 py-3 text-center dark:border-gray-700">
                        <input
                          type="checkbox"
                          :checked="isPermissionSelected(`create ${subMenu.permission}`)"
                          @change="togglePermission(`create ${subMenu.permission}`)"
                          class="h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:border-gray-600 dark:bg-gray-800"
                        />
                      </td>
                      <td class="border-r border-gray-200 px-4 py-3 text-center dark:border-gray-700">
                        <input
                          type="checkbox"
                          :checked="isPermissionSelected(`update ${subMenu.permission}`)"
                          @change="togglePermission(`update ${subMenu.permission}`)"
                          class="h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:border-gray-600 dark:bg-gray-800"
                        />
                      </td>
                      <td class="border-r border-gray-200 px-4 py-3 text-center dark:border-gray-700">
                        <input
                          type="checkbox"
                          :checked="isPermissionSelected(`show ${subMenu.permission}`)"
                          @change="togglePermission(`show ${subMenu.permission}`)"
                          class="h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:border-gray-600 dark:bg-gray-800"
                        />
                      </td>
                      <td class="px-4 py-3 text-center">
                        <input
                          type="checkbox"
                          :checked="isPermissionSelected(`delete ${subMenu.permission}`)"
                          @change="togglePermission(`delete ${subMenu.permission}`)"
                          class="h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:border-gray-600 dark:bg-gray-800"
                        />
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- Form Actions -->
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
            :disabled="isSaving"
            class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 disabled:opacity-50 disabled:cursor-not-allowed sm:w-auto"
          >
            {{ isSaving ? 'Menyimpan...' : (isEditMode ? 'Simpan Perubahan' : 'Simpan') }}
          </button>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'

const route = useRoute()
const router = useRouter()
const toast = useToast()

// Menu structure based on MenuService
const menuStructure = [
  {
    name: 'Company',
    subItems: [
      { name: 'Landing Profile', permission: 'landing profile' },
      { name: 'Landing Kegiatan', permission: 'landing kegiatan' },
      { name: 'Landing Program', permission: 'landing program' },
      { name: 'Landing Proposal', permission: 'landing proposal' },
      { name: 'Landing Bulletin', permission: 'landing bulletin' },
    ],
  },
  {
    name: 'Administrasi',
    subItems: [
      { name: 'Kantor Cabang', permission: 'kantor cabang' },
      { name: 'Program', permission: 'program' },
      { name: 'Jabatan', permission: 'jabatan' },
      { name: 'Pangkat', permission: 'pangkat' },
      { name: 'Tipe Absensi', permission: 'tipe absensi' },
      { name: 'Gaji', permission: 'gaji' },
      { name: 'Tipe Donatur', permission: 'tipe donatur' },
      { name: 'Form Pesan', permission: 'form pesan' },
      { name: 'SOP', permission: 'sop' },
      { name: 'Aturan Kepegawaian', permission: 'aturan kepegawaian' },
    ],
  },
  {
    name: 'Konten & Publikasi',
    subItems: [
      { name: 'Program Kami', permission: 'program kami' },
      { name: 'Profile Kami', permission: 'profile kami' },
      { name: 'Proposal Data', permission: 'proposal data' },
      { name: 'Bulletin Data', permission: 'bulletin data' },
    ],
  },
  {
    name: 'User & Kepegawaian',
    subItems: [
      { name: 'Karyawan', permission: 'karyawan' },
      { name: 'Mitra', permission: 'mitra' },
      { name: 'Donatur', permission: 'donatur' },
    ],
  },
  {
    name: 'Operasional',
    subItems: [
      { name: 'Absensi', permission: 'absensi' },
      { name: 'Remunerasi', permission: 'remunerasi' },
    ],
  },
  {
    name: 'Keuangan',
    subItems: [
      { name: 'Keuangan', permission: 'keuangan' },
      { name: 'Penyaluran', permission: 'penyaluran' },
      { name: 'Pengajuan Dana', permission: 'pengajuan dana' },
    ],
  },
  {
    name: 'Transaksi',
    subItems: [
      { name: 'Transaksi', permission: 'transaksi' },
    ],
  },
  {
    name: 'Laporan',
    subItems: [
      { name: 'Laporan Transaksi', permission: 'laporan transaksi' },
      { name: 'Laporan Keuangan', permission: 'laporan keuangan' },
    ],
  },
]

// Permission actions
const actions = ['view', 'create', 'update', 'show', 'delete']

// State
const isEditMode = computed(() => route.params.id !== undefined && route.params.id !== 'new')
const currentPageTitle = computed(() => {
  return isEditMode.value ? 'Edit Jabatan' : 'Tambah Jabatan'
})
const currentEditId = ref<string | null>(isEditMode.value ? (route.params.id as string) : null)
const isSaving = ref(false)
const permissionSearch = ref('')
const formData = ref({
  name: '',
})
const selectedPermissions = ref<Set<string>>(new Set())

// Filtered menu structure based on search
const filteredMenuStructure = computed(() => {
  if (!permissionSearch.value) {
    return menuStructure
  }
  const search = permissionSearch.value.toLowerCase()
  return menuStructure
    .map((mainMenu) => {
      const filteredSubItems = mainMenu.subItems.filter(
        (subItem) =>
          subItem.name.toLowerCase().includes(search) ||
          subItem.permission.toLowerCase().includes(search) ||
          mainMenu.name.toLowerCase().includes(search)
      )
      if (filteredSubItems.length > 0 || mainMenu.name.toLowerCase().includes(search)) {
        return {
          ...mainMenu,
          subItems: filteredSubItems.length > 0 ? filteredSubItems : mainMenu.subItems,
        }
      }
      return null
    })
    .filter((menu) => menu !== null) as typeof menuStructure
})

// Calculate total permissions count
const totalPermissionsCount = computed(() => {
  return menuStructure.reduce((total, mainMenu) => {
    return total + mainMenu.subItems.length * actions.length
  }, 0)
})

// Selected permissions count
const selectedPermissionsCount = computed(() => selectedPermissions.value.size)

// Permission helpers
const isPermissionSelected = (permission: string) => {
  return selectedPermissions.value.has(permission)
}

const togglePermission = (permission: string) => {
  if (selectedPermissions.value.has(permission)) {
    selectedPermissions.value.delete(permission)
  } else {
    selectedPermissions.value.add(permission)
  }
}

const selectAllPermissions = () => {
  menuStructure.forEach((mainMenu) => {
    mainMenu.subItems.forEach((subMenu) => {
      actions.forEach((action) => {
        selectedPermissions.value.add(`${action} ${subMenu.permission}`)
      })
    })
  })
}

const deselectAllPermissions = () => {
  selectedPermissions.value.clear()
}

const toggleMainMenu = (mainMenuName: string) => {
  const mainMenu = menuStructure.find((m) => m.name === mainMenuName)
  if (!mainMenu) return

  const allSelected = mainMenu.subItems.every((subMenu) =>
    actions.every((action) => isPermissionSelected(`${action} ${subMenu.permission}`))
  )

  mainMenu.subItems.forEach((subMenu) => {
    actions.forEach((action) => {
      const permission = `${action} ${subMenu.permission}`
      if (allSelected) {
        selectedPermissions.value.delete(permission)
      } else {
        selectedPermissions.value.add(permission)
      }
    })
  })
}

const isMainMenuAllSelected = (mainMenuName: string) => {
  const mainMenu = menuStructure.find((m) => m.name === mainMenuName)
  if (!mainMenu) return false

  return mainMenu.subItems.every((subMenu) =>
    actions.every((action) => isPermissionSelected(`${action} ${subMenu.permission}`))
  )
}

const toggleSubMenuAction = (mainMenuName: string, action: string) => {
  const mainMenu = menuStructure.find((m) => m.name === mainMenuName)
  if (!mainMenu) return

  const allSelected = mainMenu.subItems.every((subMenu) =>
    isPermissionSelected(`${action} ${subMenu.permission}`)
  )

  mainMenu.subItems.forEach((subMenu) => {
    const permission = `${action} ${subMenu.permission}`
    if (allSelected) {
      selectedPermissions.value.delete(permission)
    } else {
      selectedPermissions.value.add(permission)
    }
  })
}

// API functions
const fetchJabatanDetail = async (id: string) => {
  try {
    const response = await fetch(`/admin/api/jabatan/${id}`, {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      },
      credentials: 'same-origin',
    })

    if (!response.ok) throw new Error('Failed to fetch')

    const data = await response.json()
    if (data.success) {
      formData.value.name = data.data.name
      selectedPermissions.value = new Set(data.data.permissions || [])
    }
  } catch (error) {
    console.error('Error fetching jabatan detail:', error)
    toast.error('Gagal memuat data jabatan')
  }
}

const saveJabatan = async () => {
  if (!formData.value.name.trim()) {
    toast.error('Nama jabatan wajib diisi')
    return
  }

  isSaving.value = true
  try {
    const url = isEditMode.value
      ? `/admin/api/jabatan/${currentEditId.value}`
      : '/admin/api/jabatan'
    
    const method = isEditMode.value ? 'PUT' : 'POST'

    const response = await fetch(url, {
      method,
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
      },
      credentials: 'same-origin',
      body: JSON.stringify({
        name: formData.value.name,
        permissions: Array.from(selectedPermissions.value),
      }),
    })

    const data = await response.json()

    if (!response.ok) {
      throw new Error(data.message || 'Failed to save')
    }

    if (data.success) {
      toast.success(isEditMode.value ? 'Jabatan berhasil diupdate' : 'Jabatan berhasil ditambahkan')
      router.push('/administrasi/jabatan')
    } else {
      throw new Error(data.message || 'Failed to save')
    }
  } catch (error: any) {
    console.error('Error saving jabatan:', error)
    toast.error((error as any).message || 'Gagal menyimpan jabatan')
  } finally {
    isSaving.value = false
  }
}

const cloneJabatan = async () => {
  const newName = prompt('Masukkan nama untuk jabatan baru:', `${formData.value.name} (Copy)`)
  if (!newName) return

  try {
    const response = await fetch(`/admin/api/jabatan/${currentEditId.value}/clone`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
      },
      credentials: 'same-origin',
      body: JSON.stringify({ name: newName }),
    })

    const data = await response.json()

    if (data.success) {
      toast.success('Jabatan berhasil diduplikasi')
      router.push('/administrasi/jabatan')
    } else {
      throw new Error(data.message || 'Failed to clone')
    }
  } catch (error: any) {
    console.error('Error cloning jabatan:', error)
    toast.error((error as any).message || 'Gagal menduplikasi jabatan')
  }
}

// Handlers
const handleSave = () => {
  saveJabatan()
}

const handleClone = () => {
  cloneJabatan()
}

const handleCancel = () => {
  router.push('/administrasi/jabatan')
}

// Lifecycle
onMounted(async () => {
  if (isEditMode.value && route.params.id) {
    currentEditId.value = route.params.id as string
    await fetchJabatanDetail(currentEditId.value)
  }
})
</script>

<style scoped>
/* Sticky table header and first column */
table thead th:first-child,
table tbody td:first-child {
  position: sticky;
  left: 0;
  z-index: 10;
}

table thead th:first-child {
  z-index: 20;
}
</style>
