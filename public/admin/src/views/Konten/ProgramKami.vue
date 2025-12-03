<template>
  <AdminLayout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />
    <div
      class="min-h-screen rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12"
    >
      <div class="mb-6 flex items-center justify-between">
        <h3 class="font-semibold text-gray-800 text-theme-xl dark:text-white/90 sm:text-2xl">
          {{ currentPageTitle }}
        </h3>
      </div>

      <!-- Grid Cards -->
      <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        <div
          v-for="program in programList"
          :key="program.id"
          @click="handleProgramClick(program.id)"
          class="group relative cursor-pointer overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm transition-all duration-300 hover:border-brand-300 hover:shadow-lg dark:border-gray-700 dark:bg-white/[0.03] dark:hover:border-brand-500"
        >
          <!-- Program Image -->
          <div class="relative h-48 w-full overflow-hidden bg-gray-100 dark:bg-gray-800">
            <img
              v-if="program.image && !imageErrors.has(program.id)"
              :src="program.image"
              :alt="program.namaProgram"
              class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-110"
              @error="() => handleImageError(program.id)"
              loading="lazy"
            />
            <!-- Placeholder jika gambar error atau tidak ada -->
            <div
              v-else
              class="flex h-full w-full items-center justify-center bg-gray-200 dark:bg-gray-700"
            >
              <svg
                class="h-12 w-12 text-gray-400"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                />
              </svg>
            </div>
            <!-- Status Badge (optional) -->
            <div v-if="program.status" class="absolute right-2 top-2 z-10">
              <Badge :color="getStatusColor(program.status)" variant="solid" size="sm">
                {{ program.status }}
              </Badge>
            </div>
          </div>

          <!-- Program Title -->
          <div class="p-4">
            <h3 class="text-lg font-semibold text-gray-800 transition-colors group-hover:text-brand-500 dark:text-white/90 dark:group-hover:text-brand-400">
              {{ program.namaProgram }}
            </h3>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div
        v-if="programList.length === 0"
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
            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
          />
        </svg>
        <p class="text-lg font-medium text-gray-600 dark:text-gray-400">
          Belum ada program yang tersedia
        </p>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRoute } from 'vue-router'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import Badge from '@/components/ui/Badge.vue'

const route = useRoute()
const currentPageTitle = computed(() => (route.meta.title as string) || 'Program Kami')

// Program data structure
interface Program {
  id: string
  namaProgram: string
  image: string
  status?: string // Optional status badge (e.g., "New", "Popular", etc.)
}

// Sample program data
// Note: Replace these with actual image paths from your server
const programList = ref<Program[]>([
  {
    id: '1',
    namaProgram: 'Program Pendidikan Anak',
    image: '/images/program/pendidikan.jpg',
    status: 'New',
  },
  {
    id: '2',
    namaProgram: 'Program Kesehatan Masyarakat',
    image: '/images/program/kesehatan.jpg',
    status: 'Popular',
  },
  {
    id: '3',
    namaProgram: 'Program Pemberdayaan Ekonomi',
    image: '/images/program/ekonomi.jpg',
  },
  {
    id: '4',
    namaProgram: 'Program Bantuan Bencana',
    image: '/images/program/bencana.jpg',
    status: 'New',
  },
  {
    id: '5',
    namaProgram: 'Program Pelatihan Keterampilan',
    image: '/images/program/pelatihan.jpg',
  },
  {
    id: '6',
    namaProgram: 'Program Konservasi Lingkungan',
    image: '/images/program/lingkungan.jpg',
    status: 'Popular',
  },
  {
    id: '7',
    namaProgram: 'Program Festival Budaya',
    image: '/images/program/budaya.jpg',
  },
  {
    id: '8',
    namaProgram: 'Program Infrastruktur Desa',
    image: '/images/program/infrastruktur.jpg',
    status: 'New',
  },
])

// Handle program card click
const handleProgramClick = (programId: string) => {
  // TODO: Navigate to program detail page or open modal
  // router.push(`/konten/program-kami/${programId}`)
}

// Track images that have errored by program ID to prevent infinite loops
const imageErrors = ref<Set<string>>(new Set())

// Handle image error - prevent infinite loop using reactive state
const handleImageError = (programId: string) => {
  // Add to error set to trigger reactive update
  if (!imageErrors.value.has(programId)) {
    imageErrors.value.add(programId)
    // Create new Set to trigger reactivity
    imageErrors.value = new Set(imageErrors.value)
  }
}

// Get status badge color
const getStatusColor = (status: string): 'primary' | 'success' | 'error' | 'warning' | 'info' => {
  const statusLower = status.toLowerCase()
  if (statusLower === 'new') return 'success'
  if (statusLower === 'popular') return 'primary'
  if (statusLower === 'hot') return 'error'
  if (statusLower === 'coming soon') return 'warning'
  return 'info'
}
</script>

<style scoped>
/* Smooth card hover effect */
.group:hover {
  transform: translateY(-4px);
}

/* Image transition */
img {
  transition: transform 0.3s ease-in-out;
}
</style>

