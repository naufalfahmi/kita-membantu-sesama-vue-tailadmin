<template>
  <AdminLayout>
    <div
      class="min-h-screen rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12"
    >
      <!-- Header Section -->
      <div class="mb-8 text-center">
        <h1 class="mb-2 text-3xl font-bold text-gray-800 dark:text-white/90 sm:text-4xl">
          Standard Operasional Prosedure
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-400">
          Kita Membantu Sesama
        </p>
      </div>

      <!-- Tab Navigation -->
      <div
        role="tablist"
        class="mb-8 flex flex-wrap items-center justify-center gap-4 border-b border-gray-200 dark:border-gray-700"
      >
        <button
          v-for="tab in tabs"
          :key="tab.id"
          role="tab"
          :aria-selected="activeTab === tab.id"
          :aria-controls="`tabpanel-${tab.id}`"
          :id="`tab-${tab.id}`"
          @click="activeTab = tab.id"
          class="flex items-center gap-2 rounded-t-lg px-6 py-3 text-sm font-medium transition-all duration-200"
          :class="
            activeTab === tab.id
              ? 'border-b-2 border-brand-500 bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400'
              : 'text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-white/[0.03]'
          "
        >
          <component :is="tab.icon" class="h-5 w-5" />
          <span>{{ tab.label }}</span>
        </button>
      </div>

      <!-- Content Area -->
      <div class="space-y-6">
        <!-- Section Title -->
        <h2 class="text-2xl font-semibold text-gray-800 dark:text-white/90">
          {{ activeTabLabel }}
        </h2>

        <!-- Grid Cards -->
        <div
          role="tabpanel"
          :id="`tabpanel-${activeTab}`"
          :aria-labelledby="`tab-${activeTab}`"
          class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
        >
          <div
            v-for="item in currentSOPItems"
            :key="item.id"
            class="group cursor-pointer rounded-lg border border-gray-200 bg-white p-6 shadow-sm transition-all duration-300 hover:border-brand-300 hover:shadow-lg dark:border-gray-700 dark:bg-white/[0.03] dark:hover:border-brand-500"
          >
            <div class="mb-4 flex items-center justify-center">
              <div
                class="flex h-16 w-16 items-center justify-center rounded-lg bg-brand-500 text-white transition-all duration-300 group-hover:scale-110 group-hover:bg-brand-500 group-hover:text-white dark:bg-brand-500/10 dark:text-white/90"
              >
                <component
                  :is="item.icon"
                  class="h-8 w-8"
                />
              </div>
            </div>
            <h3 class="mb-2 text-center text-lg font-semibold text-gray-800 dark:text-white/90">
              {{ item.title }}
            </h3>
            <p
              v-if="item.description"
              class="text-center text-sm text-gray-600 dark:text-gray-400"
            >
              {{ item.description }}
            </p>
          </div>
        </div>

        <!-- Empty State -->
        <div
          v-if="currentSOPItems.length === 0"
          class="flex flex-col items-center justify-center py-12 text-center"
        >
          <svg
            class="mb-4 h-16 w-16 text-gray-400 dark:text-gray-600"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
            />
          </svg>
          <p class="text-lg font-medium text-gray-600 dark:text-gray-400">
            Tidak ada SOP untuk kategori ini
          </p>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRoute } from 'vue-router'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import { UserGroupIcon, PieChartIcon, FolderIcon, DocsIcon, BarChartIcon } from '@/icons'

const route = useRoute()
const currentPageTitle = computed(() => (route.meta.title as string) || 'SOP')

// Simple icon components
const CurrencyIcon = {
  template: `
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.818.182A9.75 9.75 0 0019.5 12M9 9.182l-.818-.182A9.75 9.75 0 004.5 12m0 0V18M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>
  `
}

const BuildingIcon = {
  template: `
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-1.5-1.5h-9m0 0H3m0 0v18m0-18v18" />
    </svg>
  `
}

// Tab definitions
const tabs = [
  {
    id: 'fundraising',
    label: 'Fundraising',
    icon: CurrencyIcon,
  },
  {
    id: 'operasional',
    label: 'Operasional',
    icon: BuildingIcon,
  },
  {
    id: 'empowering',
    label: 'Empowering',
    icon: UserGroupIcon,
  },
]

// Active tab
const activeTab = ref('fundraising')

// Active tab label
const activeTabLabel = computed(() => {
  const tab = tabs.find((t) => t.id === activeTab.value)
  return tab ? tab.label.toUpperCase() : ''
})

// Simple icon components for SOP items
const createIcon = (path: string) => ({
  template: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">${path}</svg>`
})

// SOP Items Data
const sopItems = {
  fundraising: [
    {
      id: 'kotak-amal',
      title: 'Kotak Amal',
      description: 'Prosedur pengelolaan kotak amal',
      icon: createIcon('<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5z" />'),
    },
    {
      id: 'kotak-infaq',
      title: 'Kotak Infaq',
      description: 'Prosedur pengelolaan kotak infaq',
      icon: DocsIcon,
    },
    {
      id: 'donasi-online',
      title: 'Donasi Online',
      description: 'Prosedur penanganan donasi online',
      icon: BarChartIcon,
    },
    {
      id: 'donasi-offline',
      title: 'Donasi Offline',
      description: 'Prosedur penanganan donasi offline',
      icon: PieChartIcon,
    },
    {
      id: 'zakat',
      title: 'Zakat',
      description: 'Prosedur pengelolaan zakat',
      icon: createIcon('<path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />'),
    },
    {
      id: 'sedekah',
      title: 'Sedekah',
      description: 'Prosedur pengelolaan sedekah',
      icon: createIcon('<path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" />'),
    },
  ],
  operasional: [
    {
      id: 'absensi',
      title: 'Absensi',
      description: 'Prosedur absensi karyawan',
      icon: createIcon('<path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />'),
    },
    {
      id: 'remunerasi',
      title: 'Remunerasi',
      description: 'Prosedur penggajian',
      icon: CurrencyIcon,
    },
    {
      id: 'kepegawaian',
      title: 'Kepegawaian',
      description: 'Prosedur kepegawaian',
      icon: UserGroupIcon,
    },
    {
      id: 'kantor-cabang',
      title: 'Kantor Cabang',
      description: 'Prosedur pengelolaan kantor cabang',
      icon: BuildingIcon,
    },
    {
      id: 'pelaporan',
      title: 'Pelaporan',
      description: 'Prosedur pelaporan operasional',
      icon: DocsIcon,
    },
    {
      id: 'rapat',
      title: 'Rapat',
      description: 'Prosedur pelaksanaan rapat',
      icon: createIcon('<path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />'),
    },
  ],
  empowering: [
    {
      id: 'pelatihan',
      title: 'Pelatihan',
      description: 'Prosedur pelaksanaan pelatihan',
      icon: createIcon('<path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 4.797a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443a55.381 55.381 0 015.25 2.882v3.675m0 0a.75.75 0 10.75.75h-.008a.75.75 0 00-.75-.75zm-7.5 0a.75.75 0 100-1.5.75.75 0 000 1.5z" />'),
    },
    {
      id: 'pendidikan',
      title: 'Pendidikan',
      description: 'Prosedur program pendidikan',
      icon: DocsIcon,
    },
    {
      id: 'pemberdayaan',
      title: 'Pemberdayaan',
      description: 'Prosedur program pemberdayaan',
      icon: createIcon('<path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" />'),
    },
    {
      id: 'kemitraan',
      title: 'Kemitraan',
      description: 'Prosedur kemitraan',
      icon: FolderIcon,
    },
  ],
}

// Current SOP items based on active tab
const currentSOPItems = computed(() => {
  return sopItems[activeTab.value as keyof typeof sopItems] || []
})
</script>

<style scoped>
/* Smooth transitions for tab switching */
[role="tabpanel"] {
  animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Card hover effect */
.group:hover {
  transform: translateY(-4px);
}
</style>

