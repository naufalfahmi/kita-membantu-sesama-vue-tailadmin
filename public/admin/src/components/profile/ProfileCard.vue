<template>
  <div>
    <div class="p-5 mb-6 border border-gray-200 rounded-2xl dark:border-gray-800 lg:p-6">
      <div class="flex flex-col gap-5 xl:flex-row xl:items-center xl:justify-between">
        <div class="flex flex-col items-center w-full gap-6 xl:flex-row">
          <div class="relative">
            <div
              :class="['w-20 h-20 overflow-hidden border border-gray-200 rounded-full dark:border-gray-800', isFallback ? 'flex items-center justify-center' : '']"
              :style="isFallback ? { backgroundColor: '#4F46E5' } : {}"
            >
                <img v-if="!isFallback" :src="displayAvatar" alt="user" class="w-full h-full object-cover" @error="handleImageError" />
                <img v-else :src="displayAvatar" alt="user" class="w-3/4 h-3/4 object-contain" @error="handleImageError" />
            </div>
            <button @click="isPhotoModal = true" class="absolute bottom-0 right-0 flex items-center justify-center w-6 h-6 bg-brand-500 rounded-full text-white hover:bg-brand-600">
              <svg class="w-3 h-3 fill-current" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M15.0911 2.78206C14.2125 1.90338 12.7878 1.90338 11.9092 2.78206L4.57524 10.116C4.26682 10.4244 4.0547 10.8158 3.96468 11.2426L3.31231 14.3352C3.25997 14.5833 3.33653 14.841 3.51583 15.0203C3.69512 15.1996 3.95286 15.2761 4.20096 15.2238L7.29355 14.5714C7.72031 14.4814 8.11172 14.2693 8.42013 13.9609L15.7541 6.62695C16.6327 5.74827 16.6327 4.32365 15.7541 3.44497L15.0911 2.78206ZM12.9698 3.84272C13.2627 3.54982 13.7376 3.54982 14.0305 3.84272L14.6934 4.50563C14.9863 4.79852 14.9863 5.2734 14.6934 5.56629L14.044 6.21573L12.3204 4.49215L12.9698 3.84272ZM11.2597 5.55281L5.6359 11.1766C5.53309 11.2794 5.46238 11.4099 5.43238 11.5522L5.01758 13.5185L6.98394 13.1037C7.1262 13.0737 7.25666 13.003 7.35947 12.9002L12.9833 7.27639L11.2597 5.55281Z" fill=""/>
              </svg>
            </button>
          </div>
          <div class="order-3 xl:order-2">
            <h4
              class="mb-2 text-lg font-semibold text-center text-gray-800 dark:text-white/90 xl:text-left"
            >
              {{ userData.name || 'User' }}
            </h4>
            <div
              class="flex flex-col items-center gap-1 text-center xl:flex-row xl:gap-3 xl:text-left"
            >
              <p class="text-sm text-gray-500 dark:text-gray-400">{{ userData.role && userData.role.name || 'User' }}</p>
              <div class="hidden h-3.5 w-px bg-gray-300 dark:bg-gray-700 xl:block"></div>
              <p class="text-sm text-gray-500 dark:text-gray-400">{{ userData.kantor_cabang && userData.kantor_cabang.nama || 'Kantor Pusat' }}</p>
            </div>
          </div>
          <div class="flex items-center order-2 gap-2 grow xl:order-3 xl:justify-end">
            <a
              v-if="userData.facebook"
              :href="userData.facebook"
              target="_blank"
              rel="noopener"
              class="social-button"
            >
              <svg
                class="fill-current"
                width="20"
                height="20"
                viewBox="0 0 20 20"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  d="M11.6666 11.2503H13.7499L14.5833 7.91699H11.6666V6.25033C11.6666 5.39251 11.6666 4.58366 13.3333 4.58366H14.5833V1.78374C14.3118 1.7477 13.2858 1.66699 12.2023 1.66699C9.94025 1.66699 8.33325 3.04771 8.33325 5.58342V7.91699H5.83325V11.2503H8.33325V18.3337H11.6666V11.2503Z"
                  fill=""
                />
              </svg>
            </a>
            <a v-if="userData.twitter" :href="userData.twitter" target="_blank" rel="noopener" class="social-button">
              <svg
                class="fill-current"
                width="20"
                height="20"
                viewBox="0 0 20 20"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  d="M15.1708 1.875H17.9274L11.9049 8.75833L18.9899 18.125H13.4424L9.09742 12.4442L4.12578 18.125H1.36745L7.80912 10.7625L1.01245 1.875H6.70078L10.6283 7.0675L15.1708 1.875ZM14.2033 16.475H15.7308L5.87078 3.43833H4.23162L14.2033 16.475Z"
                  fill=""
                />
              </svg>
            </a>
            <a
              v-if="userData.linkedin"
              :href="userData.linkedin"
              target="_blank"
              rel="noopener"
              class="social-button"
            >
              <svg
                class="fill-current"
                width="20"
                height="20"
                viewBox="0 0 20 20"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  d="M5.78381 4.16645C5.78351 4.84504 5.37181 5.45569 4.74286 5.71045C4.11391 5.96521 3.39331 5.81321 2.92083 5.32613C2.44836 4.83904 2.31837 4.11413 2.59216 3.49323C2.86596 2.87233 3.48886 2.47942 4.16715 2.49978C5.06804 2.52682 5.78422 3.26515 5.78381 4.16645ZM5.83381 7.06645H2.50048V17.4998H5.83381V7.06645ZM11.1005 7.06645H7.78381V17.4998H11.0672V12.0248C11.0672 8.97475 15.0422 8.69142 15.0422 12.0248V17.4998H18.3338V10.8914C18.3338 5.74978 12.4505 5.94145 11.0672 8.46642L11.1005 7.06645Z"
                  fill=""
                />
              </svg>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'

const userData = ref<any>({})
const hasImageError = ref(false)
const isPhotoModal = ref(false)
const selectedFile = ref<any>(null)
const previewImage = ref('')

const displayAvatar = computed(() => {
  if (userData.value?.avatar_url && !hasImageError.value) return userData.value.avatar_url
  const name = userData.value?.name || 'User'
  const initial = name.charAt(0).toUpperCase() || 'U'
  const svg = `<svg xmlns='http://www.w3.org/2000/svg' width='80' height='80'><rect width='100%' height='100%' fill='#4F46E5'/><text x='50%' y='50%' font-family='Arial, sans-serif' font-size='36' font-weight='bold' fill='white' text-anchor='middle' dominant-baseline='central'>${initial}</text></svg>`
  return `data:image/svg+xml;charset=utf-8,${encodeURIComponent(svg)}`
})

const isFallback = computed(() => {
  return !userData.value?.avatar_url || hasImageError.value
})

const handleImageError = () => {
  hasImageError.value = true
}

const loadUserData = async () => {
  try {
    const response = await fetch('/admin/api/user', { credentials: 'same-origin' })
    if (response.ok) {
      const data = await response.json()
      if (data.success && data.user) userData.value = data.user
    }
  } catch (err) {
    console.error('Error loading user data:', err)
  }
}

onMounted(() => {
  loadUserData()
})
</script>
