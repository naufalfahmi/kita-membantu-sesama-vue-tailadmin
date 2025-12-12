<template>
  <div class="hidden lg:block">
    <div class="relative">
      <form @submit.prevent="handleSearch">
        <div class="relative">
          <button type="button" class="absolute -translate-y-1/2 left-4 top-1/2">
            <svg
              class="fill-gray-500 dark:fill-gray-400"
              width="20"
              height="20"
              viewBox="0 0 20 20"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                fill-rule="evenodd"
                clip-rule="evenodd"
                d="M3.04175 9.37363C3.04175 5.87693 5.87711 3.04199 9.37508 3.04199C12.8731 3.04199 15.7084 5.87693 15.7084 9.37363C15.7084 12.8703 12.8731 15.7053 9.37508 15.7053C5.87711 15.7053 3.04175 12.8703 3.04175 9.37363ZM9.37508 1.54199C5.04902 1.54199 1.54175 5.04817 1.54175 9.37363C1.54175 13.6991 5.04902 17.2053 9.37508 17.2053C11.2674 17.2053 13.003 16.5344 14.357 15.4176L17.177 18.238C17.4699 18.5309 17.9448 18.5309 18.2377 18.238C18.5306 17.9451 18.5306 17.4703 18.2377 17.1774L15.418 14.3573C16.5365 13.0033 17.2084 11.2669 17.2084 9.37363C17.2084 5.04817 13.7011 1.54199 9.37508 1.54199Z"
                fill=""
              />
            </svg>
          </button>
          <input
            ref="searchInput"
            v-model="searchQuery"
            type="text"
            placeholder="Search or type command..."
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-200 bg-transparent py-2.5 pl-12 pr-14 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-800 dark:bg-gray-900 dark:bg-white/[0.03] dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800 xl:w-[430px]"
            @input="handleInput"
            @focus="handleFocus"
            @keydown.down.prevent="navigateDown"
            @keydown.up.prevent="navigateUp"
            @keydown.enter.prevent="handleEnter"
            @keydown.escape="closeDropdown"
          />

          <button
            type="button"
            class="absolute right-2.5 top-1/2 inline-flex -translate-y-1/2 items-center gap-0.5 rounded-lg border border-gray-200 bg-gray-50 px-[7px] py-[4.5px] text-xs -tracking-[0.2px] text-gray-500 dark:border-gray-800 dark:bg-white/[0.03] dark:text-gray-400"
          >
            <span> ⌘ </span>
            <span> K </span>
          </button>
        </div>
      </form>

      <!-- Autocomplete Dropdown -->
      <div
        v-if="showDropdown"
        class="absolute top-full left-0 right-0 mt-2 z-50 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-lg max-h-96 overflow-y-auto xl:w-[430px]"
      >
        <!-- Search History -->
        <div v-if="searchQuery.length === 0 && searchHistory.length > 0 && !loading && suggestions.length === 0" class="py-2">
          <div class="px-4 py-2 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
            History Pencarian
          </div>
          <div
            v-for="(historyItem, index) in searchHistory"
            :key="index"
            class="group px-4 py-2.5 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors flex items-center justify-between"
            @click="selectHistory(historyItem)"
          >
            <div class="flex items-center gap-3 flex-1 min-w-0">
              <svg
                v-if="historyItem.type === 'search'"
                class="w-4 h-4 text-gray-400 flex-shrink-0"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
              <svg
                v-else-if="historyItem.type === 'menu'"
                class="w-4 h-4 text-gray-400 flex-shrink-0"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                />
              </svg>
              <span class="text-sm text-gray-700 dark:text-gray-300 truncate">
                {{ historyItem.label }}
              </span>
              <span
                v-if="historyItem.type === 'menu'"
                class="text-xs text-gray-400 px-1.5 py-0.5 rounded bg-gray-100 dark:bg-gray-800"
              >
                Menu
              </span>
            </div>
            <button
              class="opacity-0 group-hover:opacity-100 p-1 hover:bg-gray-200 dark:hover:bg-gray-600 rounded transition-opacity"
              @click.stop="removeHistory(historyItem)"
            >
              <svg
                class="w-4 h-4 text-gray-400 hover:text-red-500"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M6 18L18 6M6 6l12 12"
                />
              </svg>
            </button>
          </div>
          <div class="px-4 py-2 border-t border-gray-200 dark:border-gray-700">
            <button
              class="text-xs text-gray-500 dark:text-gray-400 hover:text-red-500 dark:hover:text-red-400 transition-colors"
              @click="clearAllHistory"
            >
              Hapus Semua History
            </button>
          </div>
        </div>

        <div v-if="loading" class="p-4 text-center text-gray-500 dark:text-gray-400">
          Mencari...
        </div>
        <div v-else-if="suggestions.length > 0 && searchQuery.length >= 2" class="py-2">
          <div
            v-for="(suggestion, index) in suggestions"
            :key="index"
            :class="[
              'px-4 py-3 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors',
              selectedIndex === index ? 'bg-gray-100 dark:bg-gray-700' : ''
            ]"
            @click="navigateToSuggestion(suggestion)"
            @mouseenter="selectedIndex = index"
          >
            <div class="flex items-center justify-between">
                  <div class="flex-1">
                    <div class="flex items-center gap-2">
                      <div class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ suggestion.title }}
                      </div>
                      <span
                        v-if="suggestion.type && suggestion.type !== 'menu'"
                        class="rounded-full px-1.5 py-0.5 text-xs font-medium"
                        :class="getTypeBadgeClass(suggestion.type)"
                      >
                        {{ getTypeLabel(suggestion.type) }}
                      </span>
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                      {{ suggestion.menu_name || suggestion.category }}
                    </div>
                  </div>
              <svg
                class="w-4 h-4 text-gray-400"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 5l7 7-7 7"
                />
              </svg>
            </div>
          </div>
        </div>
        <div v-else-if="searchQuery.length >= 2" class="p-4 text-center text-gray-500 dark:text-gray-400">
          Tidak ada hasil ditemukan
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useSearchHistory, type SearchHistoryItem } from '@/composables/useSearchHistory'

const router = useRouter()
const { history: searchHistory, loadHistory, saveSearch, saveMenu, removeHistory, clearAllHistory } = useSearchHistory()

const searchQuery = ref('')
const suggestions = ref<any[]>([])
const showDropdown = ref(false)
const selectedIndex = ref(-1)
const loading = ref(false)
const searchInput = ref<HTMLInputElement | null>(null)
let debounceTimer: ReturnType<typeof setTimeout> | null = null

// Handle focus event
const handleFocus = () => {
  loadHistory() // Reload history when focused
  // Clear suggestions when focusing (to show history instead)
  suggestions.value = []
  // Always show dropdown when focused (history will show if available and input is empty)
  showDropdown.value = true
}

// Select history item
const selectHistory = (item: SearchHistoryItem) => {
  if (item.type === 'menu' && item.path) {
    router.push(item.path)
    closeDropdown()
  } else {
    searchQuery.value = item.label
    handleSearch()
  }
}

// Load history on mount
loadHistory()

const fetchAutocomplete = async () => {
  if (searchQuery.value.length < 2) {
    suggestions.value = []
    return
  }

  loading.value = true
  try {
    const response = await fetch(
      `/admin/api/search/autocomplete?q=${encodeURIComponent(searchQuery.value)}&limit=5`,
      {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
        },
        credentials: 'same-origin',
      }
    )

    if (response.ok) {
      const data = await response.json()
      if (data.success && data.data) {
        suggestions.value = data.data
        selectedIndex.value = -1
      }
    }
  } catch (error) {
    console.error('Error fetching autocomplete:', error)
    suggestions.value = []
  } finally {
    loading.value = false
  }
}

const handleInput = () => {
  // Show dropdown when typing or when clearing input
  if (searchQuery.value.length === 0 && searchHistory.value.length > 0) {
    showDropdown.value = true
    suggestions.value = [] // Clear suggestions to show history
  }
  
  if (debounceTimer) {
    clearTimeout(debounceTimer)
  }

  if (searchQuery.value.length >= 2) {
    debounceTimer = setTimeout(() => {
      fetchAutocomplete()
    }, 300)
  } else {
    // Clear suggestions when input is less than 2 characters
    suggestions.value = []
  }
}

const handleSearch = () => {
  if (searchQuery.value.trim().length >= 2) {
    // Save to history
    saveSearch(searchQuery.value.trim())
    
    router.push({
      path: '/search',
      query: { q: searchQuery.value.trim() },
    })
    closeDropdown()
  }
}

const handleEnter = () => {
  if (selectedIndex.value >= 0 && suggestions.value[selectedIndex.value]) {
    navigateToSuggestion(suggestions.value[selectedIndex.value])
  } else {
    handleSearch()
  }
}

const navigateDown = () => {
  if (selectedIndex.value < suggestions.value.length - 1) {
    selectedIndex.value++
  }
}

const navigateUp = () => {
  if (selectedIndex.value > 0) {
    selectedIndex.value--
  }
}

const navigateToSuggestion = (suggestion: any) => {
  if (!suggestion) return
  
  // Save to history based on suggestion type
  if (suggestion.type === 'menu') {
    // Menu from autocomplete - save as menu history and navigate to menu
    saveMenu(suggestion.title, suggestion.path)
    router.push(suggestion.path)
  } else {
    // Data items or keywords - save current search query as keyword and go to search page
    const keyword = searchQuery.value.trim()
    
    if (keyword && keyword.length >= 2) {
      // Save keyword to search history
      saveSearch(keyword)
      
      // Navigate to search page with keyword
      router.push({
        path: '/search',
        query: { q: keyword },
      })
    } else if (suggestion.path) {
      // Fallback: navigate to suggestion path directly
      router.push(suggestion.path)
    }
  }
  
  closeDropdown()
}

const closeDropdown = () => {
  showDropdown.value = false
  selectedIndex.value = -1
}

// Keyboard shortcut handler (Cmd/Ctrl + K)
const handleKeyboardShortcut = (e: KeyboardEvent) => {
  if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
    e.preventDefault()
    loadHistory() // Load history when using shortcut
    searchInput.value?.focus()
    showDropdown.value = true
  }
  
  // Close dropdown when clicking outside
  if (e.key === 'Escape') {
    closeDropdown()
    searchInput.value?.blur()
  }
}

// Click outside to close dropdown
const handleClickOutside = (e: MouseEvent) => {
  const target = e.target as HTMLElement
  if (!target.closest('.relative')) {
    closeDropdown()
  }
}

onMounted(() => {
  document.addEventListener('keydown', handleKeyboardShortcut)
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('keydown', handleKeyboardShortcut)
  document.removeEventListener('click', handleClickOutside)
  if (debounceTimer) {
    clearTimeout(debounceTimer)
  }
})

const getTypeLabel = (type: string): string => {
  const labels: Record<string, string> = {
    jabatan: 'Jabatan',
    landing_kegiatan: 'Kegiatan',
    karyawan: 'Karyawan',
    program: 'Program',
    pangkat: 'Pangkat',
    kantor_cabang: 'Kantor Cabang',
    absensi: 'Absensi',
    remunerasi: 'Remunerasi',
    transaksi: 'Transaksi',
  }
  return labels[type] || type
}

const getTypeBadgeClass = (type: string): string => {
  const classes: Record<string, string> = {
    jabatan: 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
    landing_kegiatan: 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
    karyawan: 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400',
    program: 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400',
    pangkat: 'bg-pink-100 text-pink-700 dark:bg-pink-900/30 dark:text-pink-400',
    kantor_cabang: 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400',
    absensi: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
    remunerasi: 'bg-teal-100 text-teal-700 dark:bg-teal-900/30 dark:text-teal-400',
    transaksi: 'bg-cyan-100 text-cyan-700 dark:bg-cyan-900/30 dark:text-cyan-400',
  }
  return classes[type] || 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300'
}
</script>
