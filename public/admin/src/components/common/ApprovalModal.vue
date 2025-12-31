<template>
  <div v-if="isOpen" class="fixed inset-0 flex items-center justify-center overflow-y-auto z-99999">
    <div class="fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-[32px]" aria-hidden="true" @click="$emit('cancel')"></div>

    <div class="relative w-full max-w-lg mx-4 bg-white rounded-2xl shadow-theme-lg dark:bg-gray-900 z-50" @click.stop>
      <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-800">
        <h3 class="text-xl font-semibold text-gray-800 dark:text-white/90">{{ title }}</h3>
        <button @click="$emit('cancel')" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
          ✕
        </button>
      </div>

      <div class="p-6">
        <p class="text-gray-700 dark:text-gray-300 mb-3">{{ message }}</p>
        <label class="text-sm text-gray-600 dark:text-gray-400">Justifikasi / catatan (wajib):</label>
        <textarea v-model="comment" rows="5" class="mt-2 w-full rounded-lg border border-gray-300 p-3 text-sm dark:bg-gray-900 dark:border-gray-700" />
      </div>

      <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-200 dark:border-gray-800">
        <button @click="$emit('cancel')" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700 dark:hover:bg-gray-700 transition-colors">Batal</button>
        <button @click="confirm" :class="['px-4 py-2 text-sm font-medium text-white rounded-lg transition-colors', confirmButtonClass || 'bg-brand-500 hover:bg-brand-600']">{{ confirmText }}</button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
interface Props { isOpen: boolean; title?: string; message?: string; confirmText?: string; confirmButtonClass?: string }
const props = defineProps<Props>()
const emit = defineEmits(['confirm', 'cancel'])

const comment = ref('')

watch(() => props.isOpen, (v) => { if (!v) comment.value = '' })

const confirm = () => {
  if (!comment.value || String(comment.value).trim().length === 0) {
    // simple client side validation - emit a different event could be used, but keep it simple
    // we emit confirm with comment only when present
    return
  }
  emit('confirm', comment.value)
}
</script>

<style scoped></style>
