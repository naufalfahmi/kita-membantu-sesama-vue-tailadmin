<template>
  <div ref="selectRef" :class="['relative bg-transparent', isOpen ? 'z-50' : 'z-10']">
    <div
      @click="toggleDropdown"
      class="dark:bg-dark-900 min-h-[44px] w-full flex flex-wrap items-center gap-2 rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-11 text-sm text-gray-800 shadow-theme-xs cursor-pointer hover:border-brand-300 focus-within:border-brand-300 focus-within:outline-hidden focus-within:ring-3 focus-within:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus-within:border-brand-800"
      :class="{ 'border-brand-300': isOpen }"
    >
      <template v-if="selectedOptions.length">
        <span
          v-for="option in selectedOptions"
          :key="option.value"
          class="group flex items-center gap-1 rounded-full bg-gray-100 px-2 py-1 text-xs text-gray-800 dark:bg-gray-800 dark:text-white/90"
        >
          {{ option.label }}
          <button
            type="button"
            class="text-gray-500 transition-colors hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
            @click.stop="removeOption(option.value)"
            aria-label="Hapus pilihan"
          >
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="18" y1="6" x2="6" y2="18"></line>
              <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
          </button>
        </span>
      </template>
      <span v-else class="text-gray-400">{{ placeholder }}</span>
    </div>
    <span class="absolute text-gray-500 -translate-y-1/2 pointer-events-none right-4 top-1/2 dark:text-gray-400">
      <svg
        class="stroke-current"
        width="20"
        height="20"
        viewBox="0 0 20 20"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
      >
        <path
          d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396"
          stroke=""
          stroke-width="1.5"
          stroke-linecap="round"
          stroke-linejoin="round"
        />
      </svg>
    </span>

    <transition
      enter-active-class="transition duration-100 ease-out"
      enter-from-class="transform scale-95 opacity-0"
      enter-to-class="transform scale-100 opacity-100"
      leave-active-class="transition duration-75 ease-in"
      leave-from-class="transform scale-100 opacity-100"
      leave-to-class="transform scale-95 opacity-0"
    >
      <div
        v-if="isOpen"
        class="absolute z-50 w-full mt-1 rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-900"
      >
        <div class="p-2 border-b border-gray-200 dark:border-gray-700">
          <input
            type="text"
            v-model="localSearchInput"
            placeholder="Cari..."
            class="w-full h-9 rounded-lg border border-gray-300 bg-transparent px-3 py-1.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90"
            @click.stop
            ref="searchInputRef"
          />
        </div>
        <div class="custom-scrollbar max-h-48 overflow-y-auto">
          <button
            v-for="option in filteredOptions"
            :key="option.value"
            type="button"
            class="flex w-full items-center justify-between px-4 py-2 text-left text-sm text-gray-800 hover:bg-gray-100 dark:text-white/90 dark:hover:bg-gray-800"
            :class="{ 'bg-gray-100 dark:bg-gray-800': isSelected(option.value) }"
            @click="toggleOption(option.value)"
          >
            <span>{{ option.label }}</span>
            <svg
              v-if="isSelected(option.value)"
              class="h-4 w-4 text-brand-500"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              viewBox="0 0 24 24"
            >
              <path d="M5 13l4 4L19 7" />
            </svg>
          </button>
          <div
            v-if="!filteredOptions.length"
            class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400"
          >
            Tidak ada data ditemukan
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup lang="ts">
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue'

type Option = {
  value: string
  label: string
}

type Props = {
  modelValue: string[]
  options: Option[]
  placeholder?: string
  searchInput?: string
}

const props = withDefaults(defineProps<Props>(), {
  modelValue: () => [],
  options: () => [],
  placeholder: 'Pilih...',
  searchInput: '',
})

const emit = defineEmits<{
  'update:modelValue': [value: string[]]
  'update:search-input': [value: string]
}>()

const isOpen = ref(false)
const searchInputRef = ref<HTMLInputElement | null>(null)
const selectRef = ref<HTMLElement | null>(null)
const localSearchInput = ref(props.searchInput || '')

const selectedOptions = computed(() =>
  props.options.filter((option) => props.modelValue.includes(option.value))
)

const filteredOptions = computed(() => {
  const term = localSearchInput.value.trim().toLowerCase()
  if (!term) {
    return props.options
  }

  return props.options.filter((option) => option.label.toLowerCase().includes(term))
})

const toggleDropdown = () => {
  isOpen.value = !isOpen.value
  if (isOpen.value) {
    nextTick(() => searchInputRef.value?.focus())
  } else {
    resetSearch()
  }
}

const toggleOption = (value: string) => {
  const next = props.modelValue.includes(value)
    ? props.modelValue.filter((item) => item !== value)
    : [...props.modelValue, value]

  emit('update:modelValue', next)
}

const removeOption = (value: string) => {
  if (!props.modelValue.includes(value)) return
  emit('update:modelValue', props.modelValue.filter((item) => item !== value))
}

const isSelected = (value: string) => props.modelValue.includes(value)

const resetSearch = () => {
  localSearchInput.value = ''
  emit('update:search-input', '')
}

const handleClickOutside = (event: MouseEvent) => {
  if (selectRef.value && !selectRef.value.contains(event.target as Node)) {
    isOpen.value = false
    resetSearch()
  }
}

watch(
  () => props.searchInput,
  (value) => {
    if (value !== undefined) {
      localSearchInput.value = value
    }
  }
)

watch(localSearchInput, (value) => {
  emit('update:search-input', value)
})

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 3px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #888;
  border-radius: 3px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #555;
}

.dark .custom-scrollbar::-webkit-scrollbar-track {
  background: #1f2937;
}

.dark .custom-scrollbar::-webkit-scrollbar-thumb {
  background: #4b5563;
}

.dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #6b7280;
}
</style>
