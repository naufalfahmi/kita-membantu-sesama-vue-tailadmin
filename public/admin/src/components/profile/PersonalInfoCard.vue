<template>
  <div>
    <div class="p-5 mb-6 border border-gray-200 rounded-2xl dark:border-gray-800 lg:p-6">
      <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
        <div class="w-full">
          <h4 class="text-lg font-semibold text-gray-800 dark:text-white/90 lg:mb-6">
            Personal Information
          </h4>

          <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
            <!-- Row 1: No Induk -->
            <div class="col-span-1 w-full">
              <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">No Induk</p>
              <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ userData.no_induk || '-' }}</p>
            </div>

            <!-- Row 2: Pangkat & Posisi (displayed inline but full-width row) -->
            <div class="col-span-1">
              <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">Pangkat &amp; Posisi</p>
              <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ userData.pangkat?.nama || userData.pangkat?.name || '-' }} {{ userData.posisi ? ' | ' + userData.posisi : '' }}</p>
            </div>

            <!-- Row 3: No Handphone -->
            <div class="col-span-1">
              <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">No Handphone</p>
              <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ userData.no_handphone || '-' }}</p>
            </div>

            <!-- Row 4: Bank & Rekening -->
            <div class="col-span-1">
              <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">Rekening Bank</p>
              <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ (userData.nama_bank || '-') + (userData.no_rekening ? (' — ' + userData.no_rekening) : '') }}</p>
            </div>

            <!-- Row 5: Tanggal Lahir & Pendidikan -->
            <div class="col-span-1">
              <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">Tanggal Lahir &amp; Pendidikan</p>
              <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ formatDate(userData.tanggal_lahir) }} {{ userData.pendidikan ? ' | ' + userData.pendidikan : '' }}</p>
            </div>

            <!-- Row 6: Tanggal Masuk & Tipe Absen -->
            <div class="col-span-1">
              <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">Tanggal Masuk &amp; Tipe Absen</p>
              <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ formatDate(userData.tanggal_masuk) }} {{ userData.tipe_absensi?.nama || userData.tipe_absensi?.name ? (' | ' + (userData.tipe_absensi?.nama || userData.tipe_absensi?.name)) : '' }}</p>
            </div>
          </div>
        </div>


      </div>
    </div>


  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'

const userData = ref<any>({})

const formatDate = (dateString: string) => {
  if (!dateString) return '-'
  const date = new Date(dateString)
  return date.toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  })
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
