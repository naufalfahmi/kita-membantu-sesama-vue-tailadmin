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

      <form @submit.prevent="handleSave" class="flex flex-col">

        <!-- Hero -->
        <div class="mb-8 rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
          <h4 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white/90">Hero</h4>

          <div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2">
            <div class="lg:col-span-2">
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Judul Hero</label>
              <input type="text" v-model="formData.hero_title" placeholder="Masukkan judul hero" maxlength="255" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300" :class="{ 'border-red-500': errors.hero_title }" />
              <p v-if="errors.hero_title" class="mt-1 text-xs text-red-500">{{ errors.hero_title }}</p>
            </div>

            <div class="lg:col-span-2">
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Deskripsi Hero</label>
              <textarea v-model="formData.hero_description" placeholder="Deskripsi hero" rows="4" class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300" :class="{ 'border-red-500': errors.hero_description }"></textarea>
              <p v-if="errors.hero_description" class="mt-1 text-xs text-red-500">{{ errors.hero_description }}</p>
            </div>

            <div class="lg:col-span-2">
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Gambar Hero</label>
              <div class="space-y-3">
                <input
                  ref="heroFileInput"
                  type="file"
                  accept="image/jpeg,image/jpg,image/png,image/webp"
                  @change="handleHeroImageSelect"
                  class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300"
                />

                <div class="flex items-center gap-3">
                  <input type="text" v-model="heroImageUrl" placeholder="Atau masukkan URL gambar (mis. /storage/...)" @input="handleHeroImageUrlChange" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs" />
                  <button v-if="previewHeroImage" type="button" @click="removeHeroImage" class="rounded-lg bg-red-50 px-3 py-2 text-sm text-red-600">Hapus</button>
                </div>

                <div v-if="previewHeroImage" class="mt-3">
                  <img :src="previewHeroImage" alt="Preview Hero Image" class="w-48 h-28 rounded-lg object-cover border border-gray-300" />
                </div>

                <p v-if="errors.hero_image" class="mt-1 text-xs text-red-500">{{ errors.hero_image }}</p>
              </div>
            </div>

            <div class="lg:col-span-1 flex items-center gap-3">
              <label class="flex items-center gap-2">
                <input type="checkbox" v-model="formData.hero_button_active" class="h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500" />
                <span class="text-sm text-gray-700 dark:text-gray-400">Aktifkan tombol Hero</span>
              </label>
            </div>

            <div class="lg:col-span-1">
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Link Tombol Hero</label>
              <input v-if="formData.hero_button_active" type="text" v-model="formData.hero_button_link" placeholder="https://example.com atau #program" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs" :class="{ 'border-red-500': errors.hero_button_link }" />
              <p v-if="errors.hero_button_link" class="mt-1 text-xs text-red-500">{{ errors.hero_button_link }}</p>
            </div>

            <div class="lg:col-span-1 flex items-center gap-3">
              <label class="flex items-center gap-2">
                <input type="checkbox" v-model="formData.hero_whatsapp_active" class="h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500" />
                <span class="text-sm text-gray-700 dark:text-gray-400">Aktifkan WhatsApp</span>
              </label>
            </div>

            <div class="lg:col-span-1">
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Nomor WhatsApp</label>
              <input v-if="formData.hero_whatsapp_active" type="text" v-model="formData.hero_whatsapp_number" placeholder="6281234567890" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs" :class="{ 'border-red-500': errors.hero_whatsapp_number }" />
              <p v-if="errors.hero_whatsapp_number" class="mt-1 text-xs text-red-500">{{ errors.hero_whatsapp_number }}</p>
            </div>
          </div>
        </div>

        <!-- Features (separate card) -->
        <div class="mb-8 rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
          <div class="mb-4 flex items-center justify-between">
            <h4 class="text-lg font-semibold text-gray-800 dark:text-white/90">Features</h4>
            <button
              v-if="existingProfile ? canUpdate : canCreate"
              type="button"
              @click="addFeature"
              class="flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600"
            >
              Tambah Feature
            </button>
          </div>

          <div v-if="formData.features.length === 0" class="py-6 text-center text-sm text-gray-500 dark:text-gray-400">
            Belum ada feature. Klik "Tambah Feature" untuk menambahkan.
          </div>

          <div v-for="(feature, index) in formData.features" :key="feature.id" class="mb-4 rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50">
            <div class="mb-3 flex items-center justify-between">
              <span class="text-sm font-medium text-gray-700 dark:text-gray-400">Feature #{{ index + 1 }}</span>
              <button
                v-if="existingProfile ? canUpdate : canCreate"
                type="button"
                @click="removeFeature(index)"
                class="flex items-center gap-1 rounded-lg border border-red-300 bg-white px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 dark:border-red-700 dark:bg-gray-800 dark:text-red-400 dark:hover:bg-red-900/20"
              >
                Hapus
              </button>
            </div>

            <div class="grid grid-cols-1 gap-x-4 gap-y-4 lg:grid-cols-2">
              <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Judul Feature <span class="text-red-500">*</span></label>
                <input type="text" v-model="feature.title" placeholder="Judul feature" required maxlength="255" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300" :class="{ 'border-red-500': errors[`features.${index}.title`] }" />
                <p v-if="errors[`features.${index}.title`]" class="mt-1 text-xs text-red-500">{{ errors[`features.${index}.title`] }}</p>
              </div>

              <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Deskripsi Feature</label>
                <textarea v-model="feature.description" placeholder="Deskripsi feature" rows="3" class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300" :class="{ 'border-red-500': errors[`features.${index}.description`] }"></textarea>
                <p v-if="errors[`features.${index}.description`]" class="mt-1 text-xs text-red-500">{{ errors[`features.${index}.description`] }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- 4. Visi & Misi -->
        <div class="mb-8 rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
          <h4 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white/90">Visi & Misi</h4>

          <div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2">
            <div class="lg:col-span-2">
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Judul Visi</label>
              <input
                type="text"
                v-model="formData.vision_title"
                placeholder="Masukkan judul visi"
                maxlength="255"
                class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300"
                :class="{ 'border-red-500': errors.vision_title }"
              />
              <p v-if="errors.vision_title" class="mt-1 text-xs text-red-500">{{ errors.vision_title }}</p>
            </div>

            <div class="lg:col-span-2">
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Deskripsi Visi</label>
              <textarea v-model="formData.vision_description" placeholder="Deskripsi visi" rows="4" class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300" :class="{ 'border-red-500': errors.vision_description }"></textarea>
              <p v-if="errors.vision_description" class="mt-1 text-xs text-red-500">{{ errors.vision_description }}</p>
            </div>

            <div class="lg:col-span-2">
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Deskripsi Misi</label>
              <textarea v-model="formData.mission_description" placeholder="Deskripsi misi" rows="4" class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300" :class="{ 'border-red-500': errors.mission_description }"></textarea>
              <p v-if="errors.mission_description" class="mt-1 text-xs text-red-500">{{ errors.mission_description }}</p>
            </div>

            <div class="lg:col-span-2">
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Gambar Visi & Misi</label>
              <div class="space-y-3">
                <input
                  ref="visionFileInput"
                  type="file"
                  accept="image/jpeg,image/jpg,image/png,image/webp"
                  @change="handleVisionImageSelect"
                  class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300"
                />

                <div class="flex items-center gap-3">
                  <input type="text" v-model="visionImageUrl" placeholder="Atau masukkan URL gambar (mis. /storage/...)" @input="handleVisionImageUrlChange" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs" />
                  <button v-if="previewVisionImage" type="button" @click="removeVisionImage" class="rounded-lg bg-red-50 px-3 py-2 text-sm text-red-600">Hapus</button>
                </div>

                <div v-if="previewVisionImage" class="mt-3">
                  <img :src="previewVisionImage" alt="Preview Vision Image" class="w-48 h-28 rounded-lg object-cover border border-gray-300" />
                </div>

                <p v-if="errors.vision_mission_image" class="mt-1 text-xs text-red-500">{{ errors.vision_mission_image }}</p>
              </div>
            </div>

          </div>
        </div>

        <!-- 1. Data Kontak -->
        <div class="mb-8 rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
          <h4 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white/90">
            Data Kontak
          </h4>
          <div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2">
            <!-- Email -->
            <div class="lg:col-span-1">
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                Email <span class="text-red-500">*</span>
              </label>
              <input
                type="email"
                v-model="formData.email"
                placeholder="contoh@email.com"
                required
                class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                :class="{ 'border-red-500': errors.email }"
              />
              <p v-if="errors.email" class="mt-1 text-xs text-red-500">
                {{ errors.email }}
              </p>
            </div>
            <div class="lg:col-span-1">
              <label
                class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
              >
                No. Handphone <span class="text-red-500">*</span>
              </label>
              <input
                type="tel"
                v-model="formData.phone_number"
                placeholder="081234567890"
                required
                maxlength="15"
                class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                :class="{ 'border-red-500': errors.phone_number }"
              />
              <p v-if="errors.phone_number" class="mt-1 text-xs text-red-500">
                {{ errors.phone_number }}
              </p>
            </div>
          </div>
        </div>

        <!-- 2. List Bank (Dynamic Repeater) -->
        <div class="mb-8 rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
          <div class="mb-4 flex items-center justify-between">
            <h4 class="text-lg font-semibold text-gray-800 dark:text-white/90">
              List Bank
            </h4>
            <button
              v-if="existingProfile ? canUpdate : canCreate"
              type="button"
              @click="addBank"
              class="flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600"
            >
              <svg
                class="fill-current"
                width="16"
                height="16"
                viewBox="0 0 20 20"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  fill-rule="evenodd"
                  clip-rule="evenodd"
                  d="M10 3.33333C10.4602 3.33333 10.8333 3.70643 10.8333 4.16667V9.16667H15.8333C16.2936 9.16667 16.6667 9.53976 16.6667 10C16.6667 10.4602 16.2936 10.8333 15.8333 10.8333H10.8333V15.8333C10.8333 16.2936 10.4602 16.6667 10 16.6667C9.53976 16.6667 9.16667 16.2936 9.16667 15.8333V10.8333H4.16667C3.70643 10.8333 3.33333 10.4602 3.33333 10C3.33333 9.53976 3.70643 9.16667 4.16667 9.16667H9.16667V4.16667C9.16667 3.70643 9.53976 3.33333 10 3.33333Z"
                  fill="currentColor"
                />
              </svg>
              Tambah Bank
            </button>
          </div>

          <div v-if="formData.banks.length === 0" class="py-8 text-center text-sm text-gray-500 dark:text-gray-400">
            Belum ada data bank. Klik tombol "Tambah Bank" untuk menambahkan.
          </div>

          <div
            v-for="(bank, index) in formData.banks"
            :key="bank.id"
            class="mb-4 rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50"
          >
            <div class="mb-3 flex items-center justify-between">
              <span class="text-sm font-medium text-gray-700 dark:text-gray-400">
                Bank #{{ index + 1 }}
              </span>
              <button
                v-if="existingProfile ? canUpdate : canCreate"
                type="button"
                @click="removeBank(index)"
                class="flex items-center gap-1 rounded-lg border border-red-300 bg-white px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 dark:border-red-700 dark:bg-gray-800 dark:text-red-400 dark:hover:bg-red-900/20"
              >
                <svg
                  width="14"
                  height="14"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path d="M3 6h18"></path>
                  <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                  <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                </svg>
                Hapus Bank
              </button>
            </div>
            <div class="grid grid-cols-1 gap-x-4 gap-y-4 lg:grid-cols-2">
              <!-- Nama Bank -->
              <div>
                <label
                  class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                >
                  Nama Bank <span class="text-red-500">*</span>
                </label>
                <input
                  type="text"
                  v-model="bank.bank_name"
                  placeholder="Contoh: Bank Mandiri"
                  required
                  class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                  :class="{ 'border-red-500': errors[`banks.${index}.bank_name`] }"
                />
                <p v-if="errors[`banks.${index}.bank_name`]" class="mt-1 text-xs text-red-500">
                  {{ errors[`banks.${index}.bank_name`] }}
                </p>
              </div>

              <!-- No. Rekening -->
              <div>
                <label
                  class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                >
                  No. Rekening <span class="text-red-500">*</span>
                </label>
                <input
                  type="text"
                  v-model="bank.account_number"
                  placeholder="Contoh: 1234567890"
                  required
                  class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                  :class="{ 'border-red-500': errors[`banks.${index}.account_number`] }"
                />
                <p v-if="errors[`banks.${index}.account_number`]" class="mt-1 text-xs text-red-500">
                  {{ errors[`banks.${index}.account_number`] }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- 3. List Alamat (Dynamic Repeater) -->
        <div class="mb-8 rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
          <div class="mb-4 flex items-center justify-between">
            <h4 class="text-lg font-semibold text-gray-800 dark:text-white/90">
              List Alamat
            </h4>
            <button
              v-if="existingProfile ? canUpdate : canCreate"
              type="button"
              @click="addAddress"
              class="flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600"
            >
              <svg
                class="fill-current"
                width="16"
                height="16"
                viewBox="0 0 20 20"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  fill-rule="evenodd"
                  clip-rule="evenodd"
                  d="M10 3.33333C10.4602 3.33333 10.8333 3.70643 10.8333 4.16667V9.16667H15.8333C16.2936 9.16667 16.6667 9.53976 16.6667 10C16.6667 10.4602 16.2936 10.8333 15.8333 10.8333H10.8333V15.8333C10.8333 16.2936 10.4602 16.6667 10 16.6667C9.53976 16.6667 9.16667 16.2936 9.16667 15.8333V10.8333H4.16667C3.70643 10.8333 3.33333 10.4602 3.33333 10C3.33333 9.53976 3.70643 9.16667 4.16667 9.16667H9.16667V4.16667C9.16667 3.70643 9.53976 3.33333 10 3.33333Z"
                  fill="currentColor"
                />
              </svg>
              Tambah Alamat
            </button>
          </div>

          <div v-if="formData.addresses.length === 0" class="py-8 text-center text-sm text-gray-500 dark:text-gray-400">
            Belum ada data alamat. Klik tombol "Tambah Alamat" untuk menambahkan.
          </div>

          <div
            v-for="(address, index) in formData.addresses"
            :key="address.id"
            class="mb-4 rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/50"
          >
            <div class="mb-3 flex items-center justify-between">
              <div class="flex items-center gap-3">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-400">
                  Alamat #{{ index + 1 }}
                </span>
                <label class="flex items-center gap-2">
                  <input
                    type="checkbox"
                    v-model="address.is_primary"
                    @change="setPrimaryAddress(index)"
                    class="h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500 dark:border-gray-600 dark:bg-gray-700"
                  />
                  <span class="text-xs text-gray-600 dark:text-gray-400">Alamat Utama</span>
                </label>
              </div>
              <button
                v-if="existingProfile ? canUpdate : canCreate"
                type="button"
                @click="removeAddress(index)"
                class="flex items-center gap-1 rounded-lg border border-red-300 bg-white px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 dark:border-red-700 dark:bg-gray-800 dark:text-red-400 dark:hover:bg-red-900/20"
              >
                <svg
                  width="14"
                  height="14"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path d="M3 6h18"></path>
                  <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                  <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                </svg>
                Hapus Alamat
              </button>
            </div>
            <div class="grid grid-cols-1 gap-x-4 gap-y-4">
              <!-- Nama -->
              <div>
                <label
                  class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                >
                  Nama <span class="text-red-500">*</span>
                </label>
                <input
                  type="text"
                  v-model="address.name"
                  placeholder="Contoh: Kantor Pusat"
                  required
                  class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                  :class="{ 'border-red-500': errors[`addresses.${index}.name`] }"
                />
                <p v-if="errors[`addresses.${index}.name`]" class="mt-1 text-xs text-red-500">
                  {{ errors[`addresses.${index}.name`] }}
                </p>
              </div>

              <!-- Alamat -->
              <div>
                <label
                  class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                >
                  Alamat <span class="text-red-500">*</span>
                </label>
                <textarea
                  v-model="address.address"
                  placeholder="Masukkan alamat lengkap"
                  rows="3"
                  required
                  class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                  :class="{ 'border-red-500': errors[`addresses.${index}.address`] }"
                ></textarea>
                <p v-if="errors[`addresses.${index}.address`]" class="mt-1 text-xs text-red-500">
                  {{ errors[`addresses.${index}.address`] }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Duplicate Visi & Misi removed (already present earlier) -->

        <!-- Features moved above (after Hero) -->
        <!-- CTA Card -->
        <div class="mb-8 rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
          <h4 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white/90">CTA</h4>

          <div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2">
            <div class="lg:col-span-2">
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Judul CTA</label>
              <input type="text" v-model="formData.cta_title" placeholder="Judul CTA" maxlength="255" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300" :class="{ 'border-red-500': errors.cta_title }" />
              <p v-if="errors.cta_title" class="mt-1 text-xs text-red-500">{{ errors.cta_title }}</p>
            </div>

            <div class="lg:col-span-2">
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Deskripsi CTA</label>
              <textarea v-model="formData.cta_description" placeholder="Deskripsi CTA" rows="3" class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300" :class="{ 'border-red-500': errors.cta_description }"></textarea>
              <p v-if="errors.cta_description" class="mt-1 text-xs text-red-500">{{ errors.cta_description }}</p>
            </div>

            <div class="lg:col-span-1 flex items-center gap-3">
              <label class="flex items-center gap-2">
                <input type="checkbox" v-model="formData.cta_button_active" class="h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500" />
                <span class="text-sm text-gray-700 dark:text-gray-400">Aktifkan tombol CTA</span>
              </label>
            </div>

            <div class="lg:col-span-1">
              <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Link CTA</label>
              <input v-if="formData.cta_button_active" type="text" v-model="formData.cta_button_link" placeholder="https://example.com" class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300" :class="{ 'border-red-500': errors.cta_button_link }" />
              <p v-if="errors.cta_button_link" class="mt-1 text-xs text-red-500">{{ errors.cta_button_link }}</p>
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center gap-3 lg:justify-end">
          <button
            type="button"
            @click="handleCancel"
            class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] sm:w-auto"
          >
            Batal
          </button>
          <button
            v-if="existingProfile ? canUpdate : canCreate"
            type="submit"
            class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto"
          >
            Simpan
          </button>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref, reactive, computed } from 'vue'
import { useToast } from 'vue-toastification'
import { useRoute, useRouter } from 'vue-router'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'
import { useAuth } from '@/composables/useAuth'

const route = useRoute()
const router = useRouter()
const currentPageTitle = ref<string>(String(route.meta.title || 'Landing Profile'))

// Form data
const formData = reactive({
  email: '',
  phone_number: '',
  banks: [] as Array<{ id: number; bank_name: string; account_number: string }>,
  addresses: [] as Array<{ id: number; name: string; address: string; is_primary: boolean }>,

  // Hero fields
  hero_title: '',
  hero_description: '',
  hero_button_active: false,
  hero_button_link: '',
  hero_whatsapp_active: false,
  hero_whatsapp_number: '',
  hero_image: '',

  // Vision & Mission fields
  vision_title: '',
  vision_description: '',
  mission_description: '',
  // This will hold either a URL or a base64 data URL
  vision_mission_image: '',

  // Features list
  features: [] as Array<{ id: number; title: string; description: string }>,

  // CTA fields
  cta_title: '',
  cta_description: '',
  cta_button_active: false,
  cta_button_link: '',
})

// Validation errors
const errors = ref<Record<string, string>>({})
const toast = useToast()

// Auth / permissions
const { fetchUser, hasPermission, isAdmin } = useAuth()
fetchUser()

const canCreate = computed(() => isAdmin() || hasPermission('create landing profile'))
const canUpdate = computed(() => isAdmin() || hasPermission('update landing profile'))

// Counter for unique IDs
let bankIdCounter = 0
let addressIdCounter = 0
let featureIdCounter = 0

// Add new bank
const addBank = () => {
  if (existingProfile.value ? !canUpdate.value : !canCreate.value) {
    toast.error('Anda tidak memiliki izin untuk menambah data')
    return
  }
  formData.banks.push({
    id: ++bankIdCounter,
    bank_name: '',
    account_number: '',
  })
}

// Remove bank
const removeBank = (index: number) => {
  if (existingProfile.value ? !canUpdate.value : !canCreate.value) {
    toast.error('Anda tidak memiliki izin untuk menghapus data')
    return
  }
  formData.banks.splice(index, 1)
  // Clear related errors
  delete errors.value[`banks.${index}.bank_name`]
  delete errors.value[`banks.${index}.account_number`]
}

// Add new address
const addAddress = () => {
  if (existingProfile.value ? !canUpdate.value : !canCreate.value) {
    toast.error('Anda tidak memiliki izin untuk menambah data')
    return
  }
  formData.addresses.push({
    id: ++addressIdCounter,
    name: '',
    address: '',
    is_primary: false,
  })
}

// Remove address
const removeAddress = (index: number) => {
  if (existingProfile.value ? !canUpdate.value : !canCreate.value) {
    toast.error('Anda tidak memiliki izin untuk menghapus data')
    return
  }
  formData.addresses.splice(index, 1)
  // Clear related errors
  delete errors.value[`addresses.${index}.name`]
  delete errors.value[`addresses.${index}.address`]
}

// Add new feature
const addFeature = () => {
  if (existingProfile.value ? !canUpdate.value : !canCreate.value) {
    toast.error('Anda tidak memiliki izin untuk menambah data')
    return
  }
  formData.features.push({
    id: ++featureIdCounter,
    title: '',
    description: '',
  })
}

// Remove feature
const removeFeature = (index: number) => {
  if (existingProfile.value ? !canUpdate.value : !canCreate.value) {
    toast.error('Anda tidak memiliki izin untuk menghapus data')
    return
  }
  formData.features.splice(index, 1)
  // Clear related errors
  delete errors.value[`features.${index}.title`]
  delete errors.value[`features.${index}.description`]
}

// Set primary address (only one can be primary)
const setPrimaryAddress = (index: number) => {
  if (formData.addresses[index].is_primary) {
    // Unset all other addresses as primary
    formData.addresses.forEach((addr, i) => {
      if (i !== index) {
        addr.is_primary = false
      }
    })
  }
}

// Validate email format
const validateEmail = (email: string): boolean => {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return emailRegex.test(email)
}

// Validate phone number (numeric, max 15 digits)
const validatePhoneNumber = (phone: string): boolean => {
  const phoneRegex = /^[0-9]{1,15}$/
  return phoneRegex.test(phone)
}

// Check for duplicate account numbers
const hasDuplicateAccountNumbers = (): boolean => {
  const accountNumbers = formData.banks
    .map((bank) => bank.account_number.trim())
    .filter((num) => num !== '')
    return new Set(accountNumbers).size !== accountNumbers.length
}

// Validate form
const validateForm = (): boolean => {
  errors.value = {}

  // Validate email
  if (!formData.email) {
    errors.value.email = 'Email wajib diisi'
  } else if (!validateEmail(formData.email)) {
    errors.value.email = 'Format email tidak valid'
  }

  // Validate phone number
  if (!formData.phone_number) {
    errors.value.phone_number = 'No. Handphone wajib diisi'
  } else if (!validatePhoneNumber(formData.phone_number)) {
    errors.value.phone_number = 'No. Handphone harus berupa angka (maksimal 15 digit)'
  }

  // Optional: small length checks for vision/mission
  if (formData.vision_title && formData.vision_title.length > 255) {
    errors.value.vision_title = 'Judul visi maksimal 255 karakter'
  }
  if (formData.vision_description && formData.vision_description.length > 5000) {
    errors.value.vision_description = 'Deskripsi visi terlalu panjang'
  }
  if (formData.mission_description && formData.mission_description.length > 5000) {
    errors.value.mission_description = 'Deskripsi misi terlalu panjang'
  }

  // Validate banks
  formData.banks.forEach((bank, index) => {
    if (!bank.bank_name.trim()) {
      errors.value[`banks.${index}.bank_name`] = 'Nama Bank wajib diisi'
    }
    if (!bank.account_number.trim()) {
      errors.value[`banks.${index}.account_number`] = 'No. Rekening wajib diisi'
    } else if (!/^[0-9]+$/.test(bank.account_number.trim())) {
      errors.value[`banks.${index}.account_number`] = 'No. Rekening harus berupa angka'
    }
  })

  // Check for duplicate account numbers
  if (hasDuplicateAccountNumbers()) {
    alert('Terdapat nomor rekening yang duplikat. Harap periksa kembali.')
    return false
  }

  // Validate addresses
  formData.addresses.forEach((address, index) => {
    if (!address.name.trim()) {
      errors.value[`addresses.${index}.name`] = 'Nama wajib diisi'
    }
    if (!address.address.trim()) {
      errors.value[`addresses.${index}.address`] = 'Alamat wajib diisi'
    }
  })

  // Validate features
  formData.features.forEach((feature, index) => {
    if (!feature.title || !feature.title.trim()) {
      errors.value[`features.${index}.title`] = 'Judul feature wajib diisi'
    } else if (feature.title && feature.title.length > 255) {
      errors.value[`features.${index}.title`] = 'Judul feature maksimal 255 karakter'
    }
    if (feature.description && feature.description.length > 2000) {
      errors.value[`features.${index}.description`] = 'Deskripsi feature terlalu panjang'
    }
  })

  // Validate CTA
  if (formData.cta_button_active) {
    if (!formData.cta_title || !formData.cta_title.trim()) {
      errors.value.cta_title = 'Judul CTA wajib diisi ketika tombol aktif'
    }
    // simple URL check for http(s)
    const urlRegex = /^https?:\/\/.+/i
    if (!formData.cta_button_link || !urlRegex.test(String(formData.cta_button_link || ''))) {
      errors.value.cta_button_link = 'Link CTA wajib diisi dan harus berupa URL (http/https)'
    }
  }

  // Validate Hero
  if (formData.hero_button_active) {
    if (!formData.hero_title || !formData.hero_title.trim()) {
      errors.value.hero_title = 'Judul Hero wajib diisi ketika tombol aktif'
    }
    // allow http(s) URLs or in-page anchors like #program
    const urlOrAnchorRegex = /(^https?:\/\/.+)|(^#.+)/i
    if (!formData.hero_button_link || !urlOrAnchorRegex.test(String(formData.hero_button_link || ''))) {
      errors.value.hero_button_link = 'Link Tombol Hero wajib diisi dan harus berupa URL (http/https) atau anchor (mis. #program)'
    }
  }

  if (formData.hero_whatsapp_active) {
    const phoneRegex = /^[0-9+]{5,20}$/
    if (!formData.hero_whatsapp_number || !phoneRegex.test(String(formData.hero_whatsapp_number || ''))) {
      errors.value.hero_whatsapp_number = 'Nomor WhatsApp tidak valid (hanya angka dan tanda +, 5-20 karakter)'
    }
  }

  return Object.keys(errors.value).length === 0
}

// Handle cancel
const handleCancel = () => {
  if (confirm('Apakah Anda yakin ingin membatalkan? Perubahan yang belum disimpan akan hilang.')) {
    router.back()
  }
}

// Handle save
const handleSave = async () => {
  if (existingProfile.value ? !canUpdate.value : !canCreate.value) {
    toast.error('Anda tidak memiliki izin untuk menyimpan data')
    return
  }

  if (!validateForm()) {
    return
  }
  try {
    // Prepare data for submission (convert to backend field names)
    const submitData: any = {
      email: formData.email,
      phone_number: formData.phone_number,
      bank_account_1: formData.banks.map((bank) => ({
        label: bank.bank_name.trim(),
        value: bank.account_number.trim(),
      })),
      address: formData.addresses.map((address) => ({
        label: address.name.trim(),
        value: address.address.trim(),
        is_primary: !!address.is_primary,
      })),

      // Vision & Mission
      vision_title: formData.vision_title || null,
      vision_description: formData.vision_description || null,
      mission_description: formData.mission_description || null,
      vision_mission_image: formData.vision_mission_image || null,

      // Features
      features: formData.features.map((f) => ({
        title: f.title ? f.title.trim() : null,
        description: f.description ? f.description.trim() : null,
      })),

      // CTA
      cta_title: formData.cta_title || null,
      cta_description: formData.cta_description || null,
      cta_button_active: formData.cta_button_active ? true : false,
      cta_button_link: formData.cta_button_active ? (formData.cta_button_link || null) : null,

      // Hero
      hero_title: formData.hero_title || null,
      hero_description: formData.hero_description || null,
      hero_button_active: formData.hero_button_active ? true : false,
      hero_button_link: formData.hero_button_active ? (formData.hero_button_link || null) : null,
      hero_whatsapp_active: formData.hero_whatsapp_active ? true : false,
      hero_whatsapp_number: formData.hero_whatsapp_active ? (formData.hero_whatsapp_number || null) : null,
      hero_image: formData.hero_image || null,
    }

    // Fetch CSRF token
    const tokenRes = await fetch('/admin/api/csrf-token', { credentials: 'same-origin' })
    if (!tokenRes.ok) throw new Error('Failed to fetch CSRF token')
    const tokenJson = await tokenRes.json()

    // Determine whether to create or update (exists if GET returned data)
    const method = existingProfile.value ? 'PUT' : 'POST'
    const url = '/admin/api/company/landing-profile'

    const res = await fetch(url, {
      method,
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': tokenJson.csrf_token,
      },
      credentials: 'same-origin',
      body: JSON.stringify(submitData),
    })

    const json = await res.json().catch(() => ({}))

    if (res.ok && json.success) {
      toast.success('Data berhasil disimpan')
      await loadData()
    } else {
      if (json.errors) {
        // Map validation errors to UI
        Object.keys(json.errors).forEach((key) => {
          errors.value[key] = json.errors[key][0]
        })
      }
      // In case backend returns the image path as part of data, refresh preview
      if (json.data && json.data.vision_mission_image) {
        previewVisionImage.value = resolveImagePreview(json.data.vision_mission_image)
      }
      throw new Error(json.message || 'Gagal menyimpan data')
    }
    } catch (error) {
    console.error('Error saving:', error)
    toast.error((error as any).message || 'Terjadi kesalahan saat menyimpan data')
  }
}

// Helper to resolve preview URL
  const resolveImagePreview = (val: string | null) => {
    if (!val) return ''
    const asStr = String(val || '')
    if (asStr.startsWith('data:')) return asStr
    if (asStr.startsWith('http')) return asStr
    // Assume stored path relative to storage
    return `/storage/${asStr}`
  }

  // Vision image preview
  const previewVisionImage = ref<string>('')
  const visionImageUrl = ref<string>('')
  const visionFileInput = ref<HTMLInputElement | null>(null)

  // Handle vision image selection (read file and convert to base64 data URL)
  const handleVisionImageSelect = (event: Event) => {
    const input = event.target as HTMLInputElement
    if (!input || !input.files || input.files.length === 0) return
    const file = input.files[0]

    // Preview using object URL
    previewVisionImage.value = URL.createObjectURL(file)

    // Also read as base64 to send as string to backend (simple solution until we add server upload)
    const reader = new FileReader()
    reader.onload = () => {
      formData.vision_mission_image = String(reader.result || '')
    }
    reader.readAsDataURL(file)

    // clear the text URL field
    visionImageUrl.value = ''

    // Also reset native file input ref (keeps consistency)
    if (visionFileInput.value) visionFileInput.value.files = input.files
  }

  const handleVisionImageUrlChange = () => {
    if (visionImageUrl.value && visionImageUrl.value.trim() !== '') {
      formData.vision_mission_image = visionImageUrl.value.trim()
      previewVisionImage.value = resolveImagePreview(formData.vision_mission_image)
    }
  }

  const removeVisionImage = () => {
    formData.vision_mission_image = ''
    previewVisionImage.value && URL.revokeObjectURL(previewVisionImage.value)
    previewVisionImage.value = ''
    visionImageUrl.value = ''
    // reset file input if present
    if (visionFileInput.value) {
      try { visionFileInput.value.value = '' } catch (e) { /* ignore */ }
      visionFileInput.value.files = null
    }
  }

  // Hero image preview
  const previewHeroImage = ref<string>('')
  const heroImageUrl = ref<string>('')
  const heroFileInput = ref<HTMLInputElement | null>(null)

  // Handle hero image selection (read file and convert to base64 data URL)
  const handleHeroImageSelect = (event: Event) => {
    const input = event.target as HTMLInputElement
    if (!input || !input.files || input.files.length === 0) return
    const file = input.files[0]

    // Preview using object URL
    previewHeroImage.value = URL.createObjectURL(file)

    // Also read as base64 to send as string to backend (simple solution until we add server upload)
    const reader = new FileReader()
    reader.onload = () => {
      formData.hero_image = String(reader.result || '')
    }
    reader.readAsDataURL(file)

    // clear the text URL field
    heroImageUrl.value = ''

    // Also reset native file input ref (keeps consistency)
    if (heroFileInput.value) heroFileInput.value.files = input.files
  }

  const handleHeroImageUrlChange = () => {
    if (heroImageUrl.value && heroImageUrl.value.trim() !== '') {
      formData.hero_image = heroImageUrl.value.trim()
      previewHeroImage.value = resolveImagePreview(formData.hero_image)
    }
  }

  const removeHeroImage = () => {
    formData.hero_image = ''
    previewHeroImage.value && URL.revokeObjectURL(previewHeroImage.value)
    previewHeroImage.value = ''
    heroImageUrl.value = ''
    // reset file input if present
    if (heroFileInput.value) {
      try { heroFileInput.value.value = '' } catch (e) { /* ignore */ }
      heroFileInput.value.files = null
    }
  }

  // Load existing data (if editing)
  const loadData = async () => {
    try {
      const res = await fetch('/admin/api/company/landing-profile', { credentials: 'same-origin' })
      if (!res.ok) throw new Error('Failed to load landing profile')
      const json = await res.json()
      if (json.success && json.data) {
        const data = json.data
        existingProfile.value = data
        formData.email = data.email || ''
        formData.phone_number = data.phone_number || ''
        formData.banks = Array.isArray(data.bank_account_1)
          ? data.bank_account_1.map((b: any, i: number) => ({ id: ++bankIdCounter, bank_name: b.label || b.bank_name || '', account_number: String(b.value || ''), }))
          : []
        formData.addresses = Array.isArray(data.address)
          ? data.address.map((a: any, i: number) => ({ id: ++addressIdCounter, name: a.label || a.name || '', address: a.value || '', is_primary: !!a.is_primary }))
          : []

        // Populate new fields
        formData.vision_title = data.vision_title || ''
        formData.vision_description = data.vision_description || ''
        formData.mission_description = data.mission_description || ''
        formData.vision_mission_image = data.vision_mission_image || ''
        previewVisionImage.value = resolveImagePreview(formData.vision_mission_image)

        // Features
        formData.features = Array.isArray(data.features)
          ? data.features.map((f: any) => ({ id: ++featureIdCounter, title: f.title || f.nama || f.label || '', description: f.description || f.desc || '' }))
          : []

        // CTA
        formData.cta_title = data.cta_title || ''
        formData.cta_description = data.cta_description || ''
        formData.cta_button_active = !!data.cta_button_active
        formData.cta_button_link = data.cta_button_link || ''

        // Hero
        formData.hero_title = data.hero_title || ''
        formData.hero_description = data.hero_description || ''
        formData.hero_button_active = !!data.hero_button_active
        formData.hero_button_link = data.hero_button_link || ''
        formData.hero_whatsapp_active = !!data.hero_whatsapp_active
        formData.hero_whatsapp_number = data.hero_whatsapp_number || ''
        formData.hero_image = data.hero_image || ''
        previewHeroImage.value = resolveImagePreview(formData.hero_image)
      }
  } catch (error) {
    console.error('Error loading data:', error)
  }
}

// Initialize: Load data on mount
// Track whether a profile already exists (used to pick POST vs PUT)
const existingProfile = ref(null as any)

loadData()
</script>

<style scoped>
/* Custom scrollbar for long lists */
.overflow-y-auto::-webkit-scrollbar {
  width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background: #888;
  border-radius: 10px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #555;
}

.dark .overflow-y-auto::-webkit-scrollbar-track {
  background: #1f2937;
}

.dark .overflow-y-auto::-webkit-scrollbar-thumb {
  background: #4b5563;
}

.dark .overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #6b7280;
}
</style>




