<template>
  <div class="space-y-6">
    <!-- Single Select Input -->
    <div>
      <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
        Select Input
      </label>
      <div class="relative z-20 bg-transparent">
        <SearchableSelect v-model="singleSelect" :options="optionss" placeholder="Select Option" />
      </div>
    </div>

    <!-- Multiple Select Input -->
    <div>
      <MultipleSelect v-model="selectedItems" :options="optionss" class="w-full" />
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import MultipleSelect from './MultipleSelect.vue'
import SearchableSelect from '../SearchableSelect.vue'

const optionss = [
  { value: 'apple', label: 'Apple' },
  { value: 'banana', label: 'Banana' },
  { value: 'cherry', label: 'Cherry' },
  { value: 'date', label: 'Date' },
  { value: 'elderberry', label: 'Elderberry' },
  { value: 'graphs', label: 'Graphs' },
]

const selectedItems = ref([])

const singleSelect = ref('')

const options = ref([
  { text: 'Option 1', selected: false },
  { text: 'Option 2', selected: false },
  { text: 'Option 3', selected: false },
  { text: 'Option 4', selected: false },
])

const selected = computed(() => options.value.filter((option) => option.selected))
</script>
