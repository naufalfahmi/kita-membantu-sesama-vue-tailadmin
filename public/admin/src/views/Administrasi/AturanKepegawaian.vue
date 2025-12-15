<template>
  <AdminLayout>
    <div
      class="min-h-screen rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12"
    >
      <div class="mb-6">
        <h3 class="font-semibold text-gray-800 text-theme-xl dark:text-white/90 sm:text-2xl">
          {{ currentPageTitle }}
        </h3>
      </div>

      <!-- Accordion List -->
      <div class="space-y-4">
        <div
          v-for="item in aturanItems"
          :key="item.id"
          class="rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-white/[0.03]"
        >
          <!-- Accordion Header -->
          <button
            @click="toggleAccordion(item.id)"
            class="flex w-full items-center justify-between px-6 py-4 text-left transition-colors hover:bg-gray-50 dark:hover:bg-white/[0.03]"
            :aria-expanded="openItems.includes(item.id)"
            :aria-controls="`content-${item.id}`"
          >
            <span class="text-base font-medium text-gray-800 dark:text-white/90">
              {{ item.title }}
            </span>
            <svg
              class="h-5 w-5 text-gray-500 transition-transform duration-200 dark:text-gray-400"
              :class="{ 'rotate-180': openItems.includes(item.id) }"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M19 9l-7 7-7-7"
              />
            </svg>
          </button>

          <!-- Accordion Content -->
          <transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
          >
            <div
              v-show="openItems.includes(item.id)"
              :id="`content-${item.id}`"
              class="overflow-hidden"
            >
              <div
                class="px-6 pb-4"
                ref="contentRefs"
              >
                <!-- If item has sub-items, show them -->
                <div v-if="item.subItems && item.subItems.length > 0" class="space-y-2">
                  <div
                    v-for="subItem in item.subItems"
                    :key="subItem.id"
                    class="rounded-md border border-gray-200 bg-gray-50 px-4 py-3 dark:border-gray-700 dark:bg-gray-900/50"
                  >
                    <h4 class="text-sm font-medium text-gray-800 dark:text-white/90">
                      {{ subItem.title }}
                    </h4>
                    <p
                      v-if="subItem.description"
                      class="mt-1 text-sm text-gray-600 dark:text-gray-400"
                    >
                      {{ subItem.description }}
                    </p>
                  </div>
                </div>
                <!-- If no sub-items, show description or placeholder -->
                <div v-else class="rounded-md border border-gray-200 bg-gray-50 px-4 py-3 dark:border-gray-700 dark:bg-gray-900/50">
                  <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ item.description || 'Detail aturan akan ditampilkan di sini.' }}
                  </p>
                </div>
              </div>
            </div>
          </transition>
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

const route = useRoute()
const currentPageTitle = computed(() => (route.meta.title as string) || 'Aturan Kepegawaian')

// Accordion state
const openItems = ref<string[]>([])
const contentRefs = ref<HTMLElement[]>([])

// Toggle accordion
const toggleAccordion = (id: string) => {
  const index = openItems.value.indexOf(id)
  if (index > -1) {
    openItems.value.splice(index, 1)
  } else {
    openItems.value.push(id)
  }
}

// Aturan Kepegawaian Items
const aturanItems = [
  {
    id: 'absensi',
    title: 'Absensi',
    description: '',
    subItems: [
      {
        id: 'keterlambatan',
        title: 'Keterlambatan',
        description: 'Aturan dan prosedur terkait keterlambatan masuk kerja',
      },
      {
        id: 'izin-meninggalkan-tugas',
        title: 'Izin meninggalkan tugas di jam kantor',
        description: 'Prosedur izin meninggalkan tugas selama jam kerja',
      },
    ],
  },
  {
    id: 'remunerasi-back-office',
    title: 'Remunerasi back office',
    description: 'Aturan dan prosedur remunerasi untuk staff back office',
  },
  {
    id: 'remunerasi-marketing',
    title: 'Remunerasi marketing',
    description: 'Aturan dan prosedur remunerasi untuk tim marketing',
  },
  {
    id: 'jabatan',
    title: 'Jabatan',
    description: 'Aturan dan prosedur terkait jabatan dalam organisasi',
  },
  {
    id: 'golongan-pangkat',
    title: 'Golongan / Pangkat',
    description: 'Aturan dan prosedur terkait golongan dan pangkat pegawai',
  },
  {
    id: 'pakaian-kerja',
    title: 'Pakaian Kerja',
    description: 'Aturan dan prosedur terkait pakaian kerja dan dress code',
  },
  {
    id: 'status-pegawai',
    title: 'Status Pegawai',
    description: 'Aturan dan prosedur terkait status kepegawaian',
  },
  {
    id: 'cuti',
    title: 'Cuti',
    description: 'Aturan dan prosedur terkait cuti pegawai',
  },
  {
    id: 'ketidakhadiran-karyawan',
    title: 'Ketidak hadiran karyawan',
    description: 'Aturan dan prosedur terkait ketidakhadiran karyawan',
  },
  {
    id: 'lembur',
    title: 'Lembur',
    description: 'Aturan dan prosedur terkait lembur dan overtime',
  },
]
</script>

<style scoped>
/* Smooth height transition for accordion */
[class*="content-"] {
  transition: max-height 0.3s ease-out;
}
</style>




