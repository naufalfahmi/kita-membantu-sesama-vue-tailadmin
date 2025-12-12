<template>
  <AdminLayout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />
    <div class="min-h-screen rounded-2xl border border-gray-200 bg-white px-5 py-7 xl:px-10 xl:py-12">
      <div class="text-center">
        <h3 class="text-2xl font-semibold mb-3">Halaman Terbatas</h3>
        <p class="text-gray-600">Anda tidak memiliki izin untuk mengakses halaman ini.</p>
        <div class="mt-6">
          <router-link to="/" class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm text-white">Kembali ke Dashboard</router-link>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import { ref } from 'vue'
const currentPageTitle = ref<string>('Halaman Terbatas')
</script>

<style scoped></style>
