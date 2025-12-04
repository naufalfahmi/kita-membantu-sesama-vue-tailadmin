<template>
  <AdminLayout>
    <div
      class="min-h-screen rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12"
    >
      <div class="mx-auto w-full max-w-3xl text-center">
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

        <div class="mb-8 rounded-lg border border-gray-200 bg-gray-50 p-6 dark:border-gray-800 dark:bg-gray-900/50">
          <h2 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white/90">
            Mulai Menggunakan Sistem
          </h2>
          <p class="mb-6 text-sm text-gray-600 dark:text-gray-400">
            Dashboard ini menyediakan berbagai fitur untuk mengelola data dan aktivitas Anda. 
            Jelajahi menu di sidebar untuk mulai menggunakan fitur-fitur yang tersedia.
          </p>
          <div class="flex flex-wrap justify-center gap-4">
            <router-link
              to="/"
              class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-6 py-3 text-sm font-medium text-white transition-colors hover:bg-brand-600 dark:bg-brand-500 dark:hover:bg-brand-600"
            >
              Ke Dashboard
            </router-link>
            <router-link
              to="/profile"
              class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-6 py-3 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700"
            >
              Lihat Profil
            </router-link>
          </div>
        </div>

        <div class="text-sm text-gray-500 dark:text-gray-400">
          <p>Jika Anda memerlukan bantuan, silakan hubungi administrator sistem.</p>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/components/layout/AdminLayout.vue'
</script>

<style></style>




