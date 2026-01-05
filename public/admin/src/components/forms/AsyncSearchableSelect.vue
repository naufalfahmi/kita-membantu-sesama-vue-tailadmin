<template>
  <div ref="selectRef" :class="['relative bg-transparent', isOpen ? 'z-50' : 'z-10']">
    <div
      @click="!disabled && toggleDropdown()"
      class="dark:bg-dark-900 h-11 w-full flex items-center rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 shadow-theme-xs focus-within:border-brand-300 focus-within:outline-hidden focus-within:ring-3 focus-within:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus-within:border-brand-800"
      :class="{ 
        'border-brand-300': isOpen,
        'cursor-pointer hover:border-brand-300': !disabled,
        'cursor-not-allowed bg-gray-100 dark:bg-gray-800 opacity-75': disabled
      }"
    >
      <span :class="{ 'text-gray-800 dark:text-white/90': selectedLabel, 'text-gray-400': !selectedLabel }">
        {{ selectedLabel || placeholder }}
      </span>
      <span v-if="loading && !isOpen" class="ml-2 text-sm text-gray-500">
        (memuat...)
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
        <!-- Search input -->
        <div class="p-2 border-b border-gray-200 dark:border-gray-700">
          <input
            type="text"
            v-model="localSearchInput"
            @input="onInput"
            placeholder="Cari..."
            class="w-full h-9 rounded-lg border border-gray-300 bg-transparent px-3 py-1.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90"
            @click.stop
            ref="searchInputRef"
          />
        </div>

        <!-- All option -->
        <div v-if="props.includeAll" class="px-2 border-b bg-gray-50 dark:bg-gray-800">
          <button type="button" @click="selectAllOption" class="w-full text-left text-sm px-4 py-2 text-gray-700 hover:bg-gray-100 dark:text-white/90 dark:hover:bg-gray-800">
            {{ props.allLabel }}
          </button>
        </div>

        <!-- Options list with infinite scroll -->
        <div ref="listContainer" class="overflow-y-auto max-h-48 custom-scrollbar" @scroll.passive="onScroll">
          <button
            v-for="option in options"
            :key="option.value"
            type="button"
            @click="selectOption(option)"
            class="w-full px-4 py-2 text-left text-sm text-gray-800 hover:bg-gray-100 dark:text-white/90 dark:hover:bg-gray-800 transition-colors"
            :class="{ 'bg-gray-100 dark:bg-gray-800': modelValue === option.value }"
          >
            {{ option.label }}
          </button>

          <div v-if="options.length === 0 && !loading" class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">
            Tidak ada data ditemukan
          </div>

          <div v-if="loading" class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">Memuat...</div>
          <div v-if="!hasMore && options.length > 0" class="px-4 py-2 text-xs text-gray-400">Tidak ada lagi</div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup lang="ts">
// AsyncSearchableSelect
// - Usage: <AsyncSearchableSelect v-model="id" fetch-url="/admin/api/donatur" placeholder="Donatur" />
// - Features: debounced search (300ms), server-side pagination (per_page/page), infinite scroll, and fetching label by id when modelValue is set.
import { ref, computed, nextTick, onMounted, onBeforeUnmount, watch } from 'vue'
import { useAuth } from '@/composables/useAuth'

// Lightweight debounce helper to avoid extra dev-dependencies for types
function debounceFn<T extends (...args: any[]) => any>(fn: T, wait = 300) {
  let timeout: ReturnType<typeof setTimeout> | null = null
  const debounced = (...args: Parameters<T>) => {
    if (timeout) clearTimeout(timeout)
    timeout = setTimeout(() => {
      fn(...args)
    }, wait)
  }
  ;(debounced as any).cancel = () => {
    if (timeout) {
      clearTimeout(timeout)
      timeout = null
    }
  }
  return debounced as T & { cancel?: () => void }
}

interface Option {
  value: string
  label: string
}

interface Props {
  modelValue: string
  fetchUrl?: string
  perPage?: number
  placeholder?: string
  disabled?: boolean
  includeAll?: boolean
  allLabel?: string
  allValue?: string
}

const props = withDefaults(defineProps<Props>(), {
  perPage: 20,
  placeholder: 'Pilih...',
  disabled: false,
  includeAll: false,
  allLabel: 'Semua',
  allValue: '',
})

const emit = defineEmits<{
  'update:modelValue': [value: string]
  'update:search-input': [value: string]
  'fetched': [item: any]
}>()

// determine admin status to optionally limit donatur results to assigned branches
const { isAdmin, fetchUser, user } = useAuth()

// detect fundraiser-like role names to force pic filter
const isFundraiser = computed(() => {
  const r = (user.value && (user.value as any).role) || null
  const name = r ? ((r.name && String(r.name)) || String(r)) : ''
  const n = String(name || '').trim().toLowerCase()
  return n === 'fundrising' || n === 'fundraising' || n === 'fundraiser'
})

// detect director of fundraising role (e.g., "Direktur Fundrising")
const isDirectorFundraising = computed(() => {
  const r = (user.value && (user.value as any).role) || null
  const name = r ? ((r.name && String(r.name)) || String(r)) : ''
  const n = String(name || '').trim().toLowerCase()
  return n.includes('direktur') && (n.includes('fund') || n.includes('fundr'))
})

const isOpen = ref(false)
const loading = ref(false)
const options = ref<Option[]>([])
const page = ref(1)
const lastPage = ref<number | null>(null)
const localSearchInput = ref('')
const searchInputRef = ref<HTMLInputElement | null>(null)
const selectRef = ref<HTMLElement | null>(null)
const listContainer = ref<HTMLElement | null>(null)

const hasMore = computed(() => {
  return lastPage.value === null ? true : page.value < (lastPage.value || 0)
})

// Get selected label by current options or fetch when modelValue set
const selectedLabel = computed(() => {
  if (props.includeAll && String(props.modelValue) === String(props.allValue)) {
    return props.allLabel || ''
  }
  const found = options.value.find((o) => o.value === props.modelValue)
  return found ? found.label : ''
})

// Fetch a page
const fetchPage = async (q = '', p = 1) => {
  if (!props.fetchUrl) return
  loading.value = true
  try {
    const params = new URLSearchParams()
    if (q) params.append('search', q)
    params.append('per_page', String(props.perPage))
    params.append('page', String(p))

    // If fetching donatur and user is not admin, request only assigned kantor cabang
    // but DO NOT add only_assigned for director of fundraising so backend
    // visibility rules can return donors of their subordinates.
    if (
      props.fetchUrl &&
      props.fetchUrl.includes('/admin/api/donatur') &&
      !isAdmin() &&
      !isDirectorFundraising.value
    ) {
      params.append('only_assigned', '1')
      // If the current user is a fundraiser, further restrict to their PIC only
      if (isFundraiser.value && user.value && (user.value as any).id) {
        params.append('pic', String((user.value as any).id))
      }
    }
    const res = await fetch(`${props.fetchUrl}?${params.toString()}`, { credentials: 'same-origin' })
    if (!res.ok) throw new Error('Failed to fetch')
    const json = await res.json()
    if (json.success) {
      const dataArray = Array.isArray(json.data) ? json.data : (json.data && json.data.data) || []
      const mapped = dataArray.map((item: any) => ({ value: item.id, label: item.nama || item.name || item.kode || item.label || '' }))

      if (p === 1) {
        options.value = mapped
      } else {
        // avoid duplicates
        const existingIds = new Set(options.value.map((o) => o.value))
        mapped.forEach((m: Option) => {
          if (!existingIds.has(m.value)) options.value.push(m)
        })
      }

      // pagination info if provided
      if (json.pagination) {
        lastPage.value = json.pagination.last_page
      } else if (json.data && json.data.last_page) {
        lastPage.value = json.data.last_page
      } else {
        // unknown, infer from results
        lastPage.value = mapped.length < props.perPage ? p : null
      }

      page.value = p
    }
  } catch (e) {
    console.error('AsyncSelect fetch error:', e)
  } finally {
    loading.value = false
  }
}

// Fetch by id (for initial selection label)
const fetchById = async (id: string) => {
  if (!props.fetchUrl || !id) return
  try {
    const res = await fetch(`${props.fetchUrl}/${id}`, { credentials: 'same-origin' })
    if (!res.ok) throw new Error('Failed to fetch by id')
    const json = await res.json()
    if (json.success && json.data) {
      const item = json.data
      const opt = { value: item.id, label: item.nama || item.name || item.kode || '' }
      // include into options if not exists
      if (!options.value.find((o) => o.value === opt.value)) {
        options.value.unshift(opt)
      }
      // emit full fetched item so parent can use the full payload without
      // needing to fetch it separately
      try {
        emit('fetched', item)
      } catch (e) {
        // ignore
      }
    }
  } catch (e) {
    // ignore
  }
}

// Debounced search
const debouncedSearch = debounceFn(async (q: string) => {
  page.value = 1
  lastPage.value = null
  await fetchPage(q, 1)
}, 300)

const onInput = (e: Event) => {
  const t = e.target as HTMLInputElement
  localSearchInput.value = t.value
  emit('update:search-input', t.value)
  debouncedSearch(t.value)
}

const toggleDropdown = () => {
  isOpen.value = !isOpen.value
  if (isOpen.value) {
    nextTick(() => {
      searchInputRef.value?.focus()
      // fetch first page with current search input
      fetchPage(localSearchInput.value || '', 1)
    })
  } else {
    localSearchInput.value = ''
    emit('update:search-input', '')
  }
}

const selectOption = (option: Option) => {
  emit('update:modelValue', option.value)
  localSearchInput.value = ''
  emit('update:search-input', '')
  isOpen.value = false
}

const selectAllOption = () => {
  emit('update:modelValue', props.allValue || '')
  localSearchInput.value = ''
  emit('update:search-input', '')
  isOpen.value = false
}

const onScroll = () => {
  if (!listContainer.value || loading.value || !hasMore.value) return
  const sc = listContainer.value
  const nearBottom = sc.scrollTop + sc.clientHeight >= sc.scrollHeight - 30
  if (nearBottom) {
    fetchPage(localSearchInput.value || '', page.value + 1)
  }
}

const handleClickOutside = (event: MouseEvent) => {
  if (selectRef.value && !selectRef.value.contains(event.target as Node)) {
    isOpen.value = false
    localSearchInput.value = ''
    emit('update:search-input', '')
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
  if (props.modelValue) {
    fetchById(props.modelValue)
  }
  // Ensure current user is loaded so isAdmin() and isFundraiser work reliably
  ;(async () => {
    try {
      await fetchUser()
    } catch (e) {
      // ignore
    }
  })()
})

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside)
  if ((debouncedSearch as any).cancel) (debouncedSearch as any).cancel()
})

watch(() => props.modelValue, (newVal) => {
  if (newVal) fetchById(newVal)
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
