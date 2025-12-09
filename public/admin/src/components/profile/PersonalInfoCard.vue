<template>
  <div>
    <div class="p-5 mb-6 border border-gray-200 rounded-2xl dark:border-gray-800 lg:p-6">
      <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
        <div>
          <h4 class="text-lg font-semibold text-gray-800 dark:text-white/90 lg:mb-6">
            Personal Information
          </h4>

          <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 lg:gap-7 2xl:gap-x-32">
            <div>
              <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">Nama</p>
              <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ userData.name || '-' }}</p>
            </div>

            <div>
              <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">
                Email Address
              </p>
              <p class="text-sm font-medium text-gray-800 dark:text-white/90">
                {{ userData.email || '-' }}
              </p>
            </div>

            <div>
              <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">No Handphone</p>
              <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ userData.no_handphone || '-' }}</p>
            </div>

            <div>
              <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">Tanggal Masuk</p>
              <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ formatDate(userData.tanggal_masuk) || '-' }}</p>
            </div>

            <div>
              <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">Pendidikan</p>
              <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ userData.pendidikan || '-' }}</p>
            </div>

            <div>
              <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">Nama Bank</p>
              <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ userData.nama_bank || '-' }}</p>
            </div>

            <div>
              <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">No. Rekening</p>
              <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ userData.no_rekening || '-' }}</p>
            </div>

            <div>
              <p class="mb-2 text-xs leading-normal text-gray-500 dark:text-gray-400">Tanggal Lahir</p>
              <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ formatDate(userData.tanggal_lahir) || '-' }}</p>
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
