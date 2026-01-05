<template>
  <AdminLayout>
    <div v-if="!showProfile"
      class="min-h-screen rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12"
    >
      <div class="mx-auto w-full max-w-7xl text-center">
        <div class="mb-8">
          <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-brand-100 dark:bg-brand-500/10">
            <svg
              class="h-10 w-10 text-brand-500 dark:text-brand-400"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
          </div>
          <h1
            class="mb-4 font-semibold text-gray-800 text-3xl dark:text-white/90 sm:text-4xl"
          >
            Selamat Datang!
          </h1>
          <p class="mb-8 text-base text-gray-500 dark:text-gray-400 sm:text-lg">
            Terima kasih telah bergabung dengan sistem kami. Anda telah berhasil masuk ke dashboard.
          </p>
        </div>

        <!-- Kegiatan Kami: fetch from public API with pagination (6 per page) -->
        <div class="mb-8 rounded-lg border border-gray-200 bg-gray-50 p-6 dark:border-gray-800 dark:bg-gray-900/50">
          <h2 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white/90">Kegiatan Kami</h2>

          <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <div
              v-for="k in kegiatanList"
              :key="k.id"
              class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm transition-all duration-200 hover:shadow-lg dark:border-gray-700 dark:bg-white/[0.03]"
            >
              <div class="relative h-44 w-full overflow-hidden bg-gray-100 dark:bg-gray-800">
                <img
                  v-if="getImage(k)"
                  :src="getImage(k)"
                  :alt="k.title"
                  class="h-full w-full object-cover"
                  loading="lazy"
                />
                <div v-else class="flex h-full w-full items-center justify-center bg-gray-200 dark:bg-gray-700">
                  <svg class="h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                  </svg>
                </div>
              </div>

              <div class="p-4">
                <h3 class="text-md font-semibold text-gray-800 dark:text-white/90">{{ k.title }}</h3>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ truncateText(k.description, 140) }}</p>
                <div class="mt-3 text-xs text-gray-500 dark:text-gray-400">{{ formatDate(k.activity_date) }}</div>
              </div>
            </div>
          </div>

          <!-- Pagination -->
          <div v-if="total > perPage" class="mt-6 flex items-center justify-center gap-3">
            <button
              :disabled="page <= 1 || loading"
              @click="fetchKegiatan(page - 1)"
              class="px-3 py-2 rounded-md border bg-white text-sm text-gray-700 hover:bg-gray-50 disabled:opacity-50"
            >Prev</button>

            <button
              v-for="p in totalPages"
              :key="p"
              @click="fetchKegiatan(p)"
              :class="['px-3 py-2 rounded-md text-sm', page===p ? 'bg-brand-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-50']"
            >{{ p }}</button>

            <button
              :disabled="!hasMore || loading"
              @click="fetchKegiatan(page + 1)"
              class="px-3 py-2 rounded-md border bg-white text-sm text-gray-700 hover:bg-gray-50 disabled:opacity-50"
            >Next</button>
          </div>
        </div>

        <div class="text-sm text-gray-500 dark:text-gray-400">
          <p>Jika Anda memerlukan bantuan, silakan hubungi administrator sistem.</p>
        </div>
      </div>
    </div>
    <div v-else class="min-h-screen rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12">
      <div class="">
        <UserProfile />
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/components/layout/AdminLayout.vue'
import { useRoute } from 'vue-router'
import { ref, watch, onMounted } from 'vue'
import UserProfile from '@/views/Others/UserProfile.vue'

const route = useRoute()
const showProfile = ref(false)

// Kegiatan (public) for Welcome page
const kegiatanList = ref([])
const page = ref(1)
const perPage = ref(6)
const total = ref(0)
const hasMore = ref(false)
const loading = ref(false)

const totalPages = () => Math.max(1, Math.ceil(total.value / perPage.value))

const fetchKegiatan = async (p = 1) => {
  loading.value = true
  page.value = Math.max(1, p)
  try {
    const res = await fetch(`/api/landing-kegiatan?per_page=${perPage.value}&page=${page.value}`, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        Accept: 'application/json',
      },
      credentials: 'same-origin',
    })
    const j = await res.json()
    if (j.success) {
      kegiatanList.value = j.data || []
      total.value = j.total || 0
      hasMore.value = !!j.has_more
    } else {
      kegiatanList.value = []
      total.value = 0
      hasMore.value = false
    }
  } catch (e) {
    console.error('Error fetching kegiatan:', e)
    kegiatanList.value = []
    total.value = 0
    hasMore.value = false
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchKegiatan(1)
})

const getImage = (k) => {
  try {
    let imgs = k.images
    if (!imgs) return null
    if (typeof imgs === 'string') imgs = JSON.parse(imgs || '[]')
    if (Array.isArray(imgs) && imgs.length > 0) {
      return `/storage/${imgs[0]}`
    }
    return null
  } catch (e) {
    return null
  }
}

const truncateText = (html = '', len = 140) => {
  if (!html) return ''
  const txt = html.replace(/<[^>]*>/g, '')
  if (txt.length <= len) return txt
  return txt.slice(0, len).trim() + '...'
}

const formatDate = (d) => {
  if (!d) return ''
  try {
    const dt = new Date(d)
    return dt.toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' })
  } catch (e) {
    return ''
  }
}

watch(
  () => route.hash,
  (h) => {
    showProfile.value = h === '#profile'
  },
  { immediate: true }
)
</script>

<style></style>




