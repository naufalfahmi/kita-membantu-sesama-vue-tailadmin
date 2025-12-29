<template>
  <div class="p-5 mb-6 border border-gray-200 rounded-2xl dark:border-gray-800 lg:p-6">
    <h4 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white/90">Struktur Atasan / Bawahan</h4>

    <div v-if="loading" class="text-sm text-gray-500">Memuat struktur...</div>

    <div v-else>
      <div v-if="!user" class="text-sm text-gray-500">Data user tidak tersedia</div>

      <div v-else class="space-y-4">
        <div v-if="ancestors && ancestors.length" class="space-y-3">
          <p class="text-xs text-gray-400">Atasan</p>
          <div class="space-y-2">
            <div v-for="(a, idx) in ancestors" :key="a.id" class="flex items-center gap-3">
              <div class="w-3/4">
                <div class="mt-1 flex items-center gap-3">
                  <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center text-sm text-gray-700">{{ getInitial(a.name) }}</div>
                  <div>
                    <div class="text-sm font-medium text-gray-800">{{ a.name }}</div>
                    <div class="text-xs text-gray-500">{{ a.posisi || a.role?.name || '-' }}</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="flex items-center gap-3">
          <div class="w-3/4">
            <p class="text-xs text-gray-400">Anda</p>
            <div class="mt-1 flex items-center gap-3">
              <div class="w-10 h-10 bg-brand-100 rounded-full flex items-center justify-center text-sm text-brand-700">{{ userInitial }}</div>
              <div>
                <div class="text-sm font-medium text-gray-800">{{ user.name }}</div>
                <div class="text-xs text-gray-500">{{ user.posisi || user.role?.name || '-' }}</div>
              </div>
            </div>
          </div>
        </div>

        <div v-if="subordinates && subordinates.length" class="pt-2">
          <p class="text-xs text-gray-400">Bawahan ({{ subordinates.length }})</p>
          <div class="mt-2 grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-3">
            <div v-for="sub in subordinates" :key="sub.id" class="p-3 border border-gray-100 rounded-lg bg-white">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center text-xs text-gray-700">{{ getInitial(sub.name) }}</div>
                <div>
                  <div class="text-sm font-medium text-gray-800">{{ sub.name }}</div>
                  <div class="text-xs text-gray-500">{{ sub.posisi || sub.role?.name || '-' }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div v-else class="text-sm text-gray-500">Tidak ada bawahan.</div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'

const user = ref<any>(null)
const ancestors = ref<any[]>([])
const subordinates = ref<any[]>([])
const loading = ref(true)

const fetchUser = async () => {
  try {
    const res = await fetch('/admin/api/user', { credentials: 'same-origin' })
    if (!res.ok) return null
    const json = await res.json()
    if (json.success && json.user) return json.user
    return null
  } catch (e) {
    return null
  }
}

const fetchKaryawanById = async (id: string) => {
  try {
    const res = await fetch(`/admin/api/karyawan/${id}`, { credentials: 'same-origin' })
    if (!res.ok) return null
    const json = await res.json()
    if (json.success && json.data) return json.data
    return null
  } catch (e) {
    return null
  }
}

const fetchSubordinates = async (userId: string) => {
  try {
    const params = new URLSearchParams()
    params.append('per_page', '1000')
    params.append('leader_id', String(userId))
    const res = await fetch(`/admin/api/karyawan?${params.toString()}`, { credentials: 'same-origin' })
    if (!res.ok) return []
    const json = await res.json()
    if (json.success) {
      const data = Array.isArray(json.data) ? json.data : (json.data && json.data.data) || []
      return data
    }
    return []
  } catch (e) {
    return []
  }
}

const getInitial = (name: string) => {
  if (!name) return 'U'
  return String(name).charAt(0).toUpperCase()
}

const leaderInitial = computed(() => leader.value ? getInitial(leader.value.name) : '')
const userInitial = computed(() => user.value ? getInitial(user.value.name) : '')

onMounted(async () => {
  loading.value = true
  user.value = await fetchUser()
  if (user.value) {
    // fetch leader if exists
      // fetch ancestor chain (top-most -> immediate leader)
      const anc: any[] = []
      let currentLeaderId = user.value.leader_id
      let safety = 0
      while (currentLeaderId && safety < 10) {
        const l = await fetchKaryawanById(String(currentLeaderId))
        if (!l) break
        anc.unshift(l) // build from top-most to immediate
        currentLeaderId = l.leader_id
        safety++
      }
      ancestors.value = anc

      // fetch direct subordinates
      const subs = await fetchSubordinates(String(user.value.id))
      subordinates.value = Array.isArray(subs) ? subs : []
  }
  loading.value = false
})
</script>

<style scoped></style>
