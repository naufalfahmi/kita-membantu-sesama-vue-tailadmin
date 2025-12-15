<template>
  <AdminLayout>

    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6">
      <h3 class="mb-5 text-lg font-semibold text-gray-800 dark:text-white/90 lg:mb-7">Support / Contact</h3>

      <div class="max-w-md space-y-4">
        <div class="rounded-lg border border-gray-200 p-4">
          <h3 class="font-medium">Customer Service</h3>
          <p class="text-sm text-gray-600">Hubungi tim CS kami melalui:</p>

          <ul class="mt-3 space-y-2">
            <li>
              <a class="text-brand-600" :href="`mailto:${email}`">{{ email }}</a>
            </li>
            <li>
              <a class="text-brand-600" :href="`https://wa.me/${whatsappPlain}`" target="_blank">WhatsApp: {{ whatsappDisplay }}</a>
            </li>
          </ul>
        </div>

        <div class="text-sm text-gray-500">
          Jika ingin bantuan lainnya, kirim detail masalah Anda lewat email atau WhatsApp di atas.
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'

const currentPageTitle = 'Support'
const email = 'support@kitasemesta.org'
// WhatsApp in international format without plus or punctuation
const whatsappPlain = '6281234567890'
const whatsappDisplay = '+62 812-3456-7890'
</script>

<style scoped></style>
