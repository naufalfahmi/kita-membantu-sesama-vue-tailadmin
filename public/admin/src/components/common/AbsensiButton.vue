<template>
  <button
    class="relative flex items-center justify-center text-gray-500 transition-colors bg-white border border-gray-200 rounded-full hover:text-dark-900 h-11 w-11 hover:bg-gray-100 hover:text-gray-700 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white"
    @click="openModal"
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
        fill-rule="evenodd"
        clip-rule="evenodd"
        d="M10 1.66667C5.39763 1.66667 1.66667 5.39763 1.66667 10C1.66667 14.6024 5.39763 18.3333 10 18.3333C14.6024 18.3333 18.3333 14.6024 18.3333 10C18.3333 5.39763 14.6024 1.66667 10 1.66667ZM10 3.33333C13.6819 3.33333 16.6667 6.3181 16.6667 10C16.6667 13.6819 13.6819 16.6667 10 16.6667C6.3181 16.6667 3.33333 13.6819 3.33333 10C3.33333 6.3181 6.3181 3.33333 10 3.33333ZM9.16667 5.83333C9.16667 5.3731 9.53976 5 10 5C10.4602 5 10.8333 5.3731 10.8333 5.83333V9.16667H14.1667C14.6269 9.16667 15 9.53976 15 10C15 10.4602 14.6269 10.8333 14.1667 10.8333H10.8333V14.1667C10.8333 14.6269 10.4602 15 10 15C9.53976 15 9.16667 14.6269 9.16667 14.1667V10.8333H5.83333C5.3731 10.8333 5 10.4602 5 10C5 9.53976 5.3731 9.16667 5.83333 9.16667H9.16667V5.83333Z"
        fill="currentColor"
      />
    </svg>
  </button>

  <!-- Absensi Modal -->
  <AbsensiModal v-if="showModal" @close="closeModal" />
</template>

<script setup lang="ts">
import { ref } from 'vue'
import AbsensiModal from './AbsensiModal.vue'

const showModal = ref(false)

const openModal = () => {
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
}
</script>




