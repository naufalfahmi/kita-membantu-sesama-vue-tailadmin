<template>
  <teleport to="body">
    <div class="fixed inset-0 flex items-center justify-center overflow-y-auto modal-root" style="z-index:99999; pointer-events:auto;">
      <div
        v-if="fullScreenBackdrop"
        class="fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-[32px]"
        aria-hidden="true"
        @click="$emit('close')"
      ></div>
      <slot name="body"></slot>
    </div>
  </teleport>
</template>

<script setup lang="ts">
import { onMounted, onUnmounted } from 'vue'
interface ModalProps {
  fullScreenBackdrop?: boolean
}

defineProps<ModalProps>()
defineEmits(['close'])

onMounted(() => {
  console.log('Modal mounted')
  const el = document.querySelector('.modal-root') as HTMLElement | null
  if (el) console.log('Modal root exists with z-index:', window.getComputedStyle(el).zIndex)
})

onUnmounted(() => {
  console.log('Modal unmounted')
})
</script>
