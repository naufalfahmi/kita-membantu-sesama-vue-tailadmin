<template>
  <AdminLayout>
    <PageBreadcrumb :pageTitle="pageTitle" />
    <div
      class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6"
    >
      <!-- Search Header -->
      <div class="mb-6">
        <h1 class="mb-2 text-2xl font-semibold text-gray-800 dark:text-white/90">
          Hasil Pencarian
        </h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">
          Menampilkan hasil untuk: <span class="font-medium text-gray-700 dark:text-gray-300">"{{ searchQuery }}"</span>
        </p>
      </div>

      <!-- Search History Section -->
      <div v-if="searchHistory.length > 0 && !loading" class="mb-6 rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900/50">
        <div class="mb-3 flex items-center justify-between">
          <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">
            History Pencarian
          </h3>
          <button
            class="text-xs text-gray-500 hover:text-red-500 dark:text-gray-400 dark:hover:text-red-400 transition-colors"
            @click="clearAllHistory"
          >
            Hapus Semua
          </button>
        </div>
        <div class="flex flex-wrap gap-2">
          <button
            v-for="(historyItem, index) in searchHistory"
            :key="index"
            class="group flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-sm text-gray-700 transition-all hover:border-brand-300 hover:bg-brand-50 hover:text-brand-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:border-brand-600 dark:hover:bg-brand-900/20 dark:hover:text-brand-400"
            @click="searchHistoryItem(historyItem)"
          >
            <span>{{ typeof historyItem === 'string' ? historyItem : historyItem.label }}</span>
            <span
              v-if="typeof historyItem !== 'string' && historyItem.type === 'menu'"
              class="text-xs text-gray-400 px-1.5 py-0.5 rounded bg-gray-100 dark:bg-gray-800"
            >
              Menu
            </span>
            <button
              class="opacity-0 group-hover:opacity-100 text-gray-400 hover:text-red-500 transition-opacity"
              @click.stop="removeHistory(historyItem)"
            >
              <svg
                class="h-3 w-3"
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
          </button>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="flex items-center justify-center py-12">
        <div class="text-center">
          <div class="mb-4 inline-block h-8 w-8 animate-spin rounded-full border-4 border-solid border-brand-500 border-r-transparent"></div>
          <p class="text-sm text-gray-500 dark:text-gray-400">Mencari...</p>
        </div>
      </div>

      <!-- Results -->
      <div v-else-if="!loading && (menus.length > 0 || dataResults.length > 0 || relationships.length > 0)" class="space-y-6">
        <!-- Menu Results -->
        <div v-if="menus.length > 0">
          <h2 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white/90">Menu</h2>
          <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div
              v-for="(menu, index) in menus"
              :key="index"
              class="group cursor-pointer rounded-lg border border-gray-200 bg-white p-4 transition-all hover:border-brand-300 hover:shadow-md dark:border-gray-700 dark:bg-gray-800 dark:hover:border-brand-600"
              @click="navigateTo(menu.path)"
            >
              <div class="flex items-start justify-between">
                <div class="flex-1">
                  <h3 class="mb-1 font-medium text-gray-800 dark:text-white/90 group-hover:text-brand-600 dark:group-hover:text-brand-400">
                    {{ menu.name }}
                  </h3>
                  <p class="text-xs text-gray-500 dark:text-gray-400">
                    {{ menu.category }}
                  </p>
                </div>
                <svg
                  class="h-5 w-5 text-gray-400 transition-transform group-hover:translate-x-1"
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
        </div>

        <!-- Data Results -->
        <div v-if="dataResults.length > 0">
          <h2 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white/90">Data</h2>
          <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div
              v-for="(item, index) in dataResults"
              :key="index"
              class="group cursor-pointer rounded-lg border border-gray-200 bg-white p-4 transition-all hover:border-brand-300 hover:shadow-md dark:border-gray-700 dark:bg-gray-800 dark:hover:border-brand-600"
              @click="navigateTo(item.path)"
            >
              <div class="flex items-start justify-between">
                <div class="flex-1">
                  <div class="mb-1 flex items-center gap-2">
                    <span
                      class="rounded-full px-2 py-0.5 text-xs font-medium"
                      :class="getTypeBadgeClass(item.type)"
                    >
                      {{ getTypeLabel(item.type) }}
                    </span>
                    <span v-if="item.menu_name" class="text-xs text-gray-400">
                      · {{ item.menu_name }}
                    </span>
                  </div>
                  <h3 class="mb-1 font-medium text-gray-800 dark:text-white/90 group-hover:text-brand-600 dark:group-hover:text-brand-400">
                    {{ item.title }}
                  </h3>
                  <p v-if="item.description" class="mb-1 text-xs text-gray-500 dark:text-gray-400 line-clamp-2">
                    {{ item.description }}
                  </p>
                  <p class="text-xs text-gray-500 dark:text-gray-400">
                    {{ item.category }}
                  </p>
                </div>
                <svg
                  class="h-5 w-5 text-gray-400 transition-transform group-hover:translate-x-1"
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
        </div>

        <!-- Relationships / Keterkaitan -->
        <div v-if="relationships.length > 0">
          <h2 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white/90">Keterkaitan</h2>
          <div class="space-y-4">
            <div
              v-for="(rel, index) in relationships"
              :key="index"
              class="rounded-lg border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800"
            >
              <div class="mb-3 flex items-start gap-3">
                <div class="flex-1">
                  <div class="mb-1 flex items-center gap-2">
                    <span
                      class="rounded-full px-2 py-0.5 text-xs font-medium"
                      :class="getTypeBadgeClass(rel.source.type)"
                    >
                      {{ getTypeLabel(rel.source.type) }}
                    </span>
                  </div>
                  <h3 class="font-medium text-gray-800 dark:text-white/90">
                    {{ rel.source.title }}
                  </h3>
                  <p v-if="rel.source.menu_name" class="text-xs text-gray-500 dark:text-gray-400">
                    Menu: {{ rel.source.menu_name }}
                  </p>
                </div>
              </div>
              
              <div class="ml-2 border-l-2 border-gray-200 dark:border-gray-700 pl-4">
                <p class="mb-2 text-xs font-medium text-gray-600 dark:text-gray-400">
                  Terkait dengan:
                </p>
                <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                  <div
                    v-for="(related, relIndex) in rel.related"
                    :key="relIndex"
                    class="group cursor-pointer rounded-lg border border-gray-200 bg-gray-50 p-3 transition-all hover:border-brand-300 hover:bg-brand-50 dark:border-gray-700 dark:bg-gray-900/50 dark:hover:bg-gray-800"
                    @click="navigateTo(related.path)"
                  >
                    <div class="flex items-start justify-between">
                      <div class="flex-1">
                        <p class="mb-1 text-xs font-medium text-gray-600 dark:text-gray-400">
                          {{ related.relationship }}
                        </p>
                        <h4 class="text-sm font-medium text-gray-800 dark:text-white/90 group-hover:text-brand-600 dark:group-hover:text-brand-400">
                          {{ related.title }}
                        </h4>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                          {{ related.menu_name }}
                        </p>
                      </div>
                      <svg
                        class="h-4 w-4 text-gray-400 transition-transform group-hover:translate-x-1"
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
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- No Results -->
      <div v-else class="py-12 text-center">
        <svg
          class="mx-auto mb-4 h-12 w-12 text-gray-400 dark:text-gray-500"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
          />
        </svg>
        <h3 class="mb-2 text-lg font-semibold text-gray-800 dark:text-white/90">
          Tidak ada hasil ditemukan
        </h3>
        <p class="text-sm text-gray-500 dark:text-gray-400">
          Coba gunakan kata kunci yang berbeda atau periksa ejaan Anda.
        </p>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import { useSearchHistory, type SearchHistoryItem } from '@/composables/useSearchHistory'

const route = useRoute()
const router = useRouter()
const { history: searchHistory, loadHistory, saveSearch, removeHistory, clearAllHistory } = useSearchHistory()

const searchQuery = ref('')
const menus = ref<any[]>([])
const dataResults = ref<any[]>([])
const relationships = ref<any[]>([])
const loading = ref(false)

// Search from history item
const searchHistoryItem = (item: SearchHistoryItem | string) => {
  // Handle backward compatibility with old string format
  if (typeof item === 'string') {
    router.push({
      path: '/search',
      query: { q: item },
    })
    return
  }
  
  // New object format
  if (item.type === 'menu' && item.path) {
    router.push(item.path)
  } else {
    router.push({
      path: '/search',
      query: { q: item.label },
    })
  }
}

const pageTitle = computed(() => {
  return searchQuery.value ? `Pencarian: "${searchQuery.value}"` : 'Hasil Pencarian'
})

const fetchSearchResults = async () => {
  const query = route.query.q as string
  
  if (!query || query.length < 2) {
    searchQuery.value = ''
    menus.value = []
    dataResults.value = []
    relationships.value = []
    return
  }

  searchQuery.value = query
  loading.value = true

  // Save to history
  if (query.trim().length >= 2) {
    saveSearch(query.trim())
  }

  try {
    const response = await fetch(
      `/admin/api/search?q=${encodeURIComponent(query)}&limit=20`,
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
      const result = await response.json()
      if (result.success && result.data) {
        menus.value = result.data.menus || []
        dataResults.value = result.data.data || []
        relationships.value = result.data.relationships || []
      }
    }
  } catch (error) {
    console.error('Error fetching search results:', error)
    menus.value = []
    dataResults.value = []
    relationships.value = []
  } finally {
    loading.value = false
  }
}

const navigateTo = (path: string) => {
  if (path) {
    router.push(path)
  }
}

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

onMounted(() => {
  loadHistory()
  fetchSearchResults()
})

watch(
  () => route.query.q,
  () => {
    fetchSearchResults()
    loadHistory() // Reload history when query changes
  }
)
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>

