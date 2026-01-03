<template>
  <div>
    <div class="flex items-start gap-3">
      <button
        v-if="hasChildren"
        @click="expanded = !expanded"
        class="flex items-center justify-center w-6 h-6 text-sm text-gray-500 rounded hover:bg-gray-100"
        aria-label="toggle-children"
      >
        <span v-if="expanded">-</span>
        <span v-else>+</span>
      </button>

      <div class="flex items-center gap-3 w-full">
        <div :class="['w-10 h-10 rounded-full flex items-center justify-center text-sm', isCurrent ? 'bg-brand-100 text-brand-700' : 'bg-gray-100 text-gray-700']">{{ initial }}</div>
        <div class="flex-1">
          <div class="flex items-center gap-2">
            <div :class="['text-sm font-medium', isCurrent ? 'text-brand-600' : 'text-gray-800']">{{ node.name }}</div>
            <div v-if="isCurrent" class="text-xs text-white bg-brand-500 px-2 py-0.5 rounded">Anda</div>
          </div>
          <div class="text-xs text-gray-500">{{ node.role?.name || '-' }}</div>
        </div>
      </div>
    </div>

    <div v-if="hasChildren && expanded" class="mt-4 ml-8 pl-4 border-l-2 border-gray-100 space-y-4">
      <HierarchyNode v-for="child in node.children" :key="child.id" :node="child" :currentUserId="currentUserId" />
    </div>
  </div>
</template>

<script setup lang="ts">
defineOptions({ name: 'HierarchyNode' })
import { ref, computed } from 'vue'
import type { PropType } from 'vue'

const props = defineProps({
  node: { type: Object as PropType<any>, required: true },
  currentUserId: { type: [String, Number] as PropType<string | number | null>, default: null }
})

const expanded = ref(true)

const hasChildren = computed(() => Array.isArray(props.node.children) && props.node.children.length > 0)
const isCurrent = computed(() => String(props.node.id) === String(props.currentUserId))
const initial = computed(() => {
  if (!props.node || !props.node.name) return 'U'
  return String(props.node.name).charAt(0).toUpperCase()
})
</script>

<style scoped></style>
