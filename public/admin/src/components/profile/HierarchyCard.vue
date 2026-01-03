<template>
  <div class="p-5 mb-6 border border-gray-200 rounded-2xl dark:border-gray-800 lg:p-6">
    <h4 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white/90">Struktur Organisasi KMS</h4>

    <div v-if="loading" class="text-sm text-gray-500">Memuat struktur...</div>

    <div v-else>
      <div v-if="!user" class="text-sm text-gray-500">Data user tidak tersedia</div>

      <div v-else class="space-y-4">
        <div v-if="treeRoot" class="space-y-3">
          <p class="text-xs text-gray-400">Struktur Organisasi</p>
          <div class="mt-2 space-y-3">
            <HierarchyNode :node="treeRoot" :currentUserId="user?.id" />
          </div>
        </div>

        <div v-else class="text-sm text-gray-500">Tidak ada struktur tersedia.</div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import HierarchyNode from '@/components/profile/HierarchyNode.vue'

const user = ref<any>(null)
const ancestors = ref<any[]>([])
const subordinates = ref<any[]>([])
const treeRoot = ref<any | null>(null)
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

const userInitial = computed(() => user.value ? getInitial(user.value.name) : '')

const fetchDescendants = async (node: any) => {
  if (!node) return
  const subs = await fetchSubordinates(String(node.id))
  node.children = Array.isArray(subs) ? subs : []
  for (const child of node.children) {
    if (String(child.id) === String(node.id)) continue
    await fetchDescendants(child)
  }
}

onMounted(async () => {
  loading.value = true
  user.value = await fetchUser()
  if (user.value) {
    // try to fetch full karyawan record (includes nested leader and role)
    const karyawanDetail = await fetchKaryawanById(String(user.value.id))
    if (karyawanDetail) {
      // merge full detail so we have role/leader/subordinates
      user.value = { ...user.value, ...karyawanDetail }
    }

    // fetch leader chain (top-most -> immediate leader)
    const anc: any[] = []
    // prefer nested leader.id from karyawan show response, fallback to leader_id
    let currentLeaderId = (user.value && user.value.leader && user.value.leader.id) || user.value.leader_id || null
    let safety = 0
    while (currentLeaderId && safety < 20) {
      const l = await fetchKaryawanById(String(currentLeaderId))
      if (!l) break
      anc.unshift(l) // build from top-most to immediate
      currentLeaderId = l.leader_id
      safety++
    }
    ancestors.value = anc

    // determine root (top-most ancestor or immediate leader or user)
    let root = null
    if (ancestors.value && ancestors.value.length) {
      root = ancestors.value[0]
    } else if (currentLeaderId) {
      // try fetching immediate leader as fallback (using computed currentLeaderId)
      const leader = await fetchKaryawanById(String(currentLeaderId))
      if (leader) {
        ancestors.value = [leader]
        root = leader
      } else {
        root = user.value
      }
    } else {
      root = user.value
    }
    root.children = root.children || []
    await fetchDescendants(root)
    treeRoot.value = root
  }
  loading.value = false
})
</script>

<style scoped></style>
