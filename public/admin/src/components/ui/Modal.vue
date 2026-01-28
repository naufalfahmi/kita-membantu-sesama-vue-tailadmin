<template>
  <teleport to="body">
    <div v-if="isOpen" class="fixed inset-0 z-[99999] flex items-center justify-center overflow-y-auto px-4 py-6 sm:px-0">
      <!-- Backdrop -->
      <div
        class="fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-[32px]"
        aria-hidden="true"
        @click="$emit('close')"
      ></div>

      <!-- Modal Content -->
      <div
        class="relative w-full max-w-lg transform rounded-2xl bg-white shadow-theme-lg transition-all dark:bg-gray-900"
        @click.stop
      >
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4 dark:border-gray-800">
          <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
            {{ title }}
          </h3>
          <button
            @click="$emit('close')"
            class="ml-auto inline-flex items-center justify-center rounded-lg p-1 text-gray-500 transition-colors hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800"
          >
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Body -->
        <div class="px-6 py-6">
          <slot name="content"></slot>
        </div>

        <!-- Footer -->
        <div v-if="$slots.footer" class="border-t border-gray-200 px-6 py-4 dark:border-gray-800">
          <slot name="footer"></slot>
        </div>
      </div>
    </div>
  </teleport>
</template>

<script setup lang="ts">
interface Props {
  isOpen: boolean
  title?: string
}

defineProps<Props>()
defineEmits(['close'])
</script>
