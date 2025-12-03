<template>
  <div class="relative z-20 bg-transparent" ref="selectRef">
    <div
      @click="toggleDropdown"
      class="dark:bg-dark-900 h-11 w-full flex items-center rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 shadow-theme-xs cursor-pointer hover:border-brand-300 focus-within:border-brand-300 focus-within:outline-hidden focus-within:ring-3 focus-within:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus-within:border-brand-800"
      :class="{ 'border-brand-300': isOpen }"
    >
      <span :class="{ 'text-gray-800 dark:text-white/90': selectedLabel, 'text-gray-400': !selectedLabel }">
        {{ selectedLabel || placeholder }}
      </span>
    </div>
    <span
      class="absolute z-30 text-gray-500 -translate-y-1/2 pointer-events-none right-4 top-1/2 dark:text-gray-400"
    >
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
    
    <!-- Dropdown -->
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
        class="absolute z-50 w-full mt-1 bg-white rounded-lg shadow-lg border border-gray-200 dark:bg-gray-900 dark:border-gray-700 max-h-60 overflow-hidden"
      >
        <!-- Search input di dalam dropdown -->
        <div class="p-2 border-b border-gray-200 dark:border-gray-700">
          <input
            type="text"
            :value="searchInput"
            @input="handleSearchInput"
            placeholder="Cari..."
            class="w-full h-9 rounded-lg border border-gray-300 bg-transparent px-3 py-1.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90"
            @click.stop
            ref="searchInputRef"
          />
        </div>
        
        <!-- List options -->
        <div class="overflow-y-auto max-h-48 custom-scrollbar">
          <button
            v-for="option in filteredOptions"
            :key="option.value"
            type="button"
            @click="selectOption(option)"
            class="w-full px-4 py-2 text-left text-sm text-gray-800 hover:bg-gray-100 dark:text-white/90 dark:hover:bg-gray-800 transition-colors"
            :class="{ 'bg-gray-100 dark:bg-gray-800': modelValue === option.value }"
          >
            {{ option.label }}
          </button>
          <div
            v-if="filteredOptions.length === 0"
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
import { ref, computed, nextTick, onMounted, onBeforeUnmount } from 'vue'

interface Option {
  value: string
  label: string
}

interface Props {
  modelValue: string
  options: Option[]
  placeholder?: string
  searchInput?: string
}

const props = withDefaults(defineProps<Props>(), {
  placeholder: 'Pilih...',
  searchInput: '',
})

const emit = defineEmits<{
  'update:modelValue': [value: string]
  'update:search-input': [value: string]
}>()

const isOpen = ref(false)
const searchInputRef = ref<HTMLInputElement | null>(null)
const selectRef = ref<HTMLElement | null>(null)
const localSearchInput = ref(props.searchInput || '')

// Get selected label
const selectedLabel = computed(() => {
  if (!props.modelValue) return ''
  const selected = props.options.find((opt) => opt.value === props.modelValue)
  return selected ? selected.label : ''
})

// Filtered options
const filteredOptions = computed(() => {
  const searchTerm = localSearchInput.value.toLowerCase()
  if (!searchTerm) {
    return props.options
  }
  return props.options.filter((option) =>
    option.label.toLowerCase().includes(searchTerm)
  )
})

// Toggle dropdown
const toggleDropdown = () => {
  isOpen.value = !isOpen.value
  if (isOpen.value) {
    nextTick(() => {
      searchInputRef.value?.focus()
    })
  } else {
    localSearchInput.value = ''
    emit('update:search-input', '')
  }
}

// Handle search input
const handleSearchInput = (event: Event) => {
  const target = event.target as HTMLInputElement
  localSearchInput.value = target.value
  emit('update:search-input', target.value)
}

// Select option
const selectOption = (option: Option) => {
  emit('update:modelValue', option.value)
  localSearchInput.value = ''
  emit('update:search-input', '')
  isOpen.value = false
}

// Handle click outside
const handleClickOutside = (event: MouseEvent) => {
  if (selectRef.value && !selectRef.value.contains(event.target as Node)) {
    isOpen.value = false
    localSearchInput.value = ''
    emit('update:search-input', '')
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
  if (props.searchInput) {
    localSearchInput.value = props.searchInput
  }
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

