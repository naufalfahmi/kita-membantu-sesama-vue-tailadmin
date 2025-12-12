<template>
  <AdminLayout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />
    <div class="min-h-screen rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12">
      <div class="mb-6 flex items-center justify-between">
        <h3 class="font-semibold text-gray-800 text-theme-xl dark:text-white/90 sm:text-2xl">{{ currentPageTitle }}</h3>
        <button
          @click="handleCancel"
          class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]"
        >
          Kembali
        </button>
      </div>

      <form @submit.prevent="save" class="flex flex-col">
        <div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2">
          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Nama <span class="text-red-500">*</span>
            </label>
            <input
              type="text"
              v-model="form.nama"
              placeholder="Masukkan nama gaji"
              required
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Nominal (Rp) <span class="text-red-500">*</span>
            </label>
            <input
              type="text"
              v-model="nominalDisplay"
              placeholder="Masukkan nominal gaji"
              @input="onNominalInput"
              required
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Jabatan
            </label>
            <SearchableSelect
              v-model="form.jabatan_id"
              :options="jabatanOptions.map(j => ({ value: String(j.id), label: j.name }))"
              placeholder="Pilih jabatan"
              :search-input="jabatanSearchInput"
              @update:search-input="jabatanSearchInput = $event"
            />
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Pangkat
            </label>
            <SearchableSelect
              v-model="form.pangkat_id"
              :options="pangkatOptions.map(p => ({ value: String(p.id), label: p.nama }))"
              placeholder="Pilih pangkat"
              :search-input="pangkatSearchInput"
              @update:search-input="pangkatSearchInput = $event"
            />
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Tipe
            </label>
            <SearchableSelect
              v-model="form.tipe"
              :options="tipeOptions"
              placeholder="Pilih tipe gaji"
              :search-input="tipeSearchInput"
              @update:search-input="tipeSearchInput = $event"
            />
          </div>

          <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Tanggal Efektif
            </label>
            <input
              type="date"
              v-model="form.tanggal_efektif"
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
          </div>

          <div class="lg:col-span-2">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
              Keterangan
            </label>
            <textarea
              v-model="form.keterangan"
              placeholder="Masukkan keterangan gaji (opsional)"
              rows="3"
              class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            ></textarea>
          </div>
        </div>

        <div class="flex items-center gap-3 mt-6 lg:justify-end">
          <button
            @click="handleCancel"
            type="button"
            class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] sm:w-auto"
          >
            Batal
          </button>
          <button
            type="submit"
            class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto"
          >
            {{ isEdit ? 'Simpan Perubahan' : 'Simpan' }}
          </button>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useToast } from 'vue-toastification';
import AdminLayout from '@/components/layout/AdminLayout.vue';
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue';
import SearchableSelect from '@/components/forms/SearchableSelect.vue';

const toast = useToast();
const route = useRoute();
const router = useRouter();

const currentPageTitle = ref<string>(String(route.meta.title || 'Gaji'))

const isEdit = ref(false);
const isLoading = ref(false);
const form = ref({
  nama: '',
  nominal: 0,
  tipe: '',
  tanggal_efektif: '',
  keterangan: '',
  jabatan_id: '',
  pangkat_id: '',
});

const jabatanOptions = ref<any[]>([]);
const pangkatOptions = ref<any[]>([]);

// Tipe options
const tipeOptions = ref<{ value: string; label: string }[]>([
  { value: 'bulanan', label: 'Bulanan' },
  { value: 'harian', label: 'Harian' },
])

const tipeSearchInput = ref('')

// Search inputs for searchable selects
const jabatanSearchInput = ref('');
const pangkatSearchInput = ref('');

const nominalDisplay = ref('');

function onNominalInput() {
  // Remove non-numeric characters except comma and dot
  let value = nominalDisplay.value.replace(/[^\d.,]/g, '');
  // Remove dots (thousands separator)
  value = value.replace(/\./g, '');
  // Replace comma with dot for decimal
  value = value.replace(',', '.');
  // Parse as number
  const numValue = parseFloat(value) || 0;
  form.value.nominal = numValue;
  // Format display with Indonesian locale
  nominalDisplay.value = new Intl.NumberFormat('id-ID').format(numValue);
}

function handleCancel() {
  router.push({ name: 'Gaji' });
}

async function loadData(id: string) {
  try {
    const res = await fetch(`/admin/api/gaji/${id}`, { credentials: 'same-origin' });
    const json = await res.json();
    if (json.success) {
      form.value = {
        nama: json.data.nama || '',
        nominal: json.data.nominal || 0,
        tipe: json.data.tipe || '',
        tanggal_efektif: json.data.tanggal_efektif || '',
        keterangan: json.data.keterangan || '',
        jabatan_id: json.data.jabatan_id
          ? String(json.data.jabatan_id)
          : json.data.jabatan
            ? String(json.data.jabatan.id)
            : '',
        pangkat_id: json.data.pangkat_id
          ? String(json.data.pangkat_id)
          : json.data.pangkat
            ? String(json.data.pangkat.id)
            : '',
      };
      nominalDisplay.value = new Intl.NumberFormat('id-ID').format(form.value.nominal || 0);
    } else {
      toast.error(json.message || 'Gagal memuat data');
    }
  } catch (e) {
    toast.error('Gagal memuat data');
  }
}

async function save() {
  if (!form.value.nama) {
    toast.error('Nama gaji wajib diisi');
    return;
  }
  if (!form.value.nominal || form.value.nominal <= 0) {
    toast.error('Nominal gaji wajib diisi dan harus lebih dari 0');
    return;
  }

  isLoading.value = true;
  try {
    const tokRes = await fetch('/admin/api/csrf-token', { credentials: 'same-origin' });
    const tokJson = await tokRes.json();

    const payload = {
      nama: form.value.nama,
      nominal: form.value.nominal,
      tipe: form.value.tipe,
      tanggal_efektif: form.value.tanggal_efektif,
      keterangan: form.value.keterangan,
      jabatan_id: form.value.jabatan_id || null,
      pangkat_id: form.value.pangkat_id || null,
    };

    let url = '/admin/api/gaji';
    let method = 'POST';
    if (isEdit.value && route.params.id) {
      url = `/admin/api/gaji/${route.params.id}`;
      method = 'PUT';
    }

    const res = await fetch(url, {
      method,
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': tokJson.csrf_token,
      },
      credentials: 'same-origin',
      body: JSON.stringify(payload),
    });

    const json = await res.json();
    if (json.success) {
      toast.success(json.message || 'Berhasil disimpan');
      router.push({ name: 'Gaji' });
    } else {
      toast.error(json.message || 'Gagal menyimpan');
    }
  } catch (e) {
    toast.error('Gagal menyimpan');
  } finally {
    isLoading.value = false;
  }
}

onMounted(() => {
  if (route.params.id) {
    isEdit.value = true;
    loadData(route.params.id as string);
  }
});

onMounted(async () => {
  // fetch jabatan and pangkat options
  try {
    const [jabRes, pangRes] = await Promise.all([
      fetch('/admin/api/jabatan?per_page=1000', { credentials: 'same-origin' }),
      fetch('/admin/api/pangkat?per_page=1000', { credentials: 'same-origin' }),
    ]);
    const jabJson = await jabRes.json();
    const pangJson = await pangRes.json();

    if (jabJson && jabJson.success) jabatanOptions.value = jabJson.data || [];
    if (pangJson && pangJson.success) pangkatOptions.value = pangJson.data || [];
  } catch (e) {
    // non-blocking
  }
});
</script>

<style scoped>
</style>

