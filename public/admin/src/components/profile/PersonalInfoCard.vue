<template>
  <div>
    <div class="p-5 mb-6 border border-gray-200 rounded-2xl dark:border-gray-800 lg:p-6">
      <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
        <div class="w-full">
          <h4 class="text-lg font-semibold text-gray-800 dark:text-white/90 lg:mb-6">
            Personal Information
          </h4>

          <div v-if="!isMitra" class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
            <div class="col-span-1 w-full">
              <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">No Induk</p>
              <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ userData.no_induk || '-' }}</p>
            </div>

            <div class="col-span-1">
              <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">Pangkat &amp; Posisi</p>
              <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ userData.pangkat?.nama || userData.pangkat?.name || '-' }} {{ userData.posisi ? ' | ' + userData.posisi : '' }}</p>
            </div>

            <div class="col-span-1">
              <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">No Handphone</p>
              <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ userData.no_handphone || '-' }}</p>
            </div>

            <div class="col-span-1">
              <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">Rekening Bank</p>
              <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ formatBankInfo(userData.nama_bank, userData.no_rekening) }}</p>
            </div>

            <div class="col-span-1">
              <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">Tanggal Lahir &amp; Pendidikan</p>
              <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ formatDate(userData.tanggal_lahir) }} {{ userData.pendidikan ? ' | ' + userData.pendidikan : '' }}</p>
            </div>

            <div class="col-span-1">
              <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">Tanggal Masuk &amp; Tipe Absen</p>
              <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ formatDate(userData.tanggal_masuk) }} {{ userData.tipe_absensi?.nama || userData.tipe_absensi?.name ? (' | ' + (userData.tipe_absensi?.nama || userData.tipe_absensi?.name)) : '' }}</p>
            </div>
          </div>

          <div v-else class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
            <div class="col-span-1 w-full">
              <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">Nama Mitra</p>
              <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ mitraProfile?.nama || userData.name || '-' }}</p>
            </div>

            <div class="col-span-1">
              <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">Email</p>
              <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ mitraProfile?.email || userData.email || '-' }}</p>
            </div>

            <div class="col-span-1">
              <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">No Handphone</p>
              <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ mitraProfile?.no_handphone || userData.no_handphone || '-' }}</p>
            </div>

            <div class="col-span-1">
              <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">Rekening Bank</p>
              <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ formatBankInfo(mitraProfile?.nama_bank, mitraProfile?.no_rekening) }}</p>
            </div>

            <div class="col-span-1">
              <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">Kantor Cabang</p>
              <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ mitraProfile?.kantor_cabang?.nama || '-' }}</p>
            </div>

            <div class="col-span-1">
              <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">Jabatan</p>
              <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ mitraProfile?.jabatan?.name || '-' }}</p>
            </div>

            <div class="col-span-1">
              <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">Tanggal Lahir &amp; Pendidikan</p>
              <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ formatDate(mitraProfile?.tanggal_lahir) }} {{ mitraProfile?.pendidikan ? ' | ' + mitraProfile?.pendidikan : '' }}</p>
            </div>

            <div class="col-span-1">
              <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">Tanggal Bergabung</p>
              <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ formatDate(mitraProfile?.tanggal_dibuat) }}</p>
            </div>
          </div>
        </div>


      </div>
    </div>


  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'

const userData = ref<any>({})
const mitraProfile = computed(() => userData.value?.mitra_profile || null)
const isMitra = computed(() => {
  if (mitraProfile.value) {
    return true
  }
  const roleName = (userData.value?.role?.name || '').toString().toLowerCase()
  return roleName === 'mitra'
})

const formatDate = (dateString?: string | null) => {
  if (!dateString) return '-'
  const date = new Date(dateString)
  return date.toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  })
}

const formatBankInfo = (namaBank?: string | null, nomor?: string | null) => {
  const bank = namaBank ? String(namaBank).trim() : ''
  const rek = nomor ? String(nomor).trim() : ''
  if (!bank && !rek) return '-'
  if (bank && rek) return `${bank} — ${rek}`
  return bank || rek || '-'
}

const loadUserData = async () => {
  try {
    const response = await fetch('/admin/api/user', {
      credentials: 'same-origin'
    })
    if (response.ok) {
      const data = await response.json()
      if (data.success && data.user) {
        userData.value = data.user
      }
    }
  } catch (error) {
    console.error('Error loading user data:', error)
  }
}

onMounted(() => {
  loadUserData()
})
</script>
