<template>
  <div class="relative" ref="dropdownRef">
    <button
      class="flex items-center text-gray-700 dark:text-gray-400"
      @click.prevent="toggleDropdown"
    >
      <span class="mr-3 overflow-hidden rounded-full h-11 w-11">
        <img 
          :src="userAvatar" 
          :alt="user?.name || 'User'"
          class="h-full w-full object-cover"
          @error="handleImageError"
        />
      </span>

      <span class="block mr-1 font-medium text-theme-sm">{{ user?.name || 'Loading...' }}</span>

      <ChevronDownIcon :class="{ 'rotate-180': dropdownOpen }" />
    </button>

    <!-- Dropdown Start -->
    <div
      v-if="dropdownOpen"
      class="absolute right-0 mt-[17px] flex w-[260px] flex-col rounded-2xl border border-gray-200 bg-white p-3 shadow-theme-lg dark:border-gray-800 dark:bg-gray-dark"
    >
      <div>
        <span class="block font-medium text-gray-700 text-theme-sm dark:text-gray-400">
          {{ user?.name || 'Loading...' }}
        </span>
        <span class="mt-0.5 block text-theme-xs text-gray-500 dark:text-gray-400">
          {{ user?.email || 'Loading...' }}
        </span>
      </div>

      <ul class="flex flex-col gap-1 pt-4 pb-3 border-b border-gray-200 dark:border-gray-800">
        <li v-for="item in menuItems" :key="item.href">
          <router-link
            :to="item.href"
            class="flex items-center gap-3 px-3 py-2 font-medium text-gray-700 rounded-lg group text-theme-sm hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300"
          >
            <!-- SVG icon would go here -->
            <component
              :is="item.icon"
              class="text-gray-500 group-hover:text-gray-700 dark:group-hover:text-gray-300"
            />
            {{ item.text }}
          </router-link>
        </li>
      </ul>
      <button
        @click="signOut"
        class="flex items-center gap-3 px-3 py-2 mt-3 font-medium text-gray-700 rounded-lg group text-theme-sm hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300 w-full text-left"
      >
        <LogoutIcon
          class="text-gray-500 group-hover:text-gray-700 dark:group-hover:text-gray-300"
        />
        Sign out
      </button>
    </div>
    <!-- Dropdown End -->
  </div>
</template>

<script setup lang="ts">
import { ChevronDownIcon, InfoCircleIcon, LogoutIcon, SettingsIcon, UserCircleIcon } from '@/icons'
import { onMounted, onUnmounted, ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { resetAuthState } from '@/router'
import { getCsrfTokenSafe } from '@/utils/getCsrfToken'

const router = useRouter()
const dropdownOpen = ref(false)
const dropdownRef = ref<HTMLElement | null>(null)
const user = ref<{ id: number; name: string; email: string; avatar?: string } | null>(null)
const imageError = ref(false)

const menuItems = [
  { href: '/profile', icon: UserCircleIcon, text: 'Profile' },
  { href: '/account/settings', icon: SettingsIcon, text: 'Account settings' },
  { href: '/support', icon: InfoCircleIcon, text: 'Support' },
]

const userAvatar = computed(() => {
  if (imageError.value || !user.value?.avatar) {
    // Default avatar dengan initial user
    const initial = user.value?.name?.charAt(0).toUpperCase() || 'U'
    const svg = `<svg width="44" height="44" xmlns="http://www.w3.org/2000/svg"><rect width="44" height="44" fill="#4F46E5"/><text x="50%" y="50%" font-family="Arial, sans-serif" font-size="18" font-weight="bold" fill="white" text-anchor="middle" dominant-baseline="central">${initial}</text></svg>`
    return `data:image/svg+xml;charset=utf-8,${encodeURIComponent(svg)}`
  }
  return user.value.avatar
})

const fetchUser = async () => {
  try {
    const response = await fetch('/admin/api/user', {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
      },
      credentials: 'same-origin',
    })

    if (response.ok) {
      const data = await response.json()
      if (data.success && data.user) {
        user.value = data.user
      }
    }
  } catch (error) {
    console.error('Error fetching user:', error)
  }
}

const toggleDropdown = () => {
  dropdownOpen.value = !dropdownOpen.value
}

const closeDropdown = () => {
  dropdownOpen.value = false
}

const handleImageError = () => {
  imageError.value = true
}

const signOut = async (e: Event) => {
  e.preventDefault()
  e.stopPropagation()
  
  try {
    // Get CSRF token (safe)
    const csrfToken = await getCsrfTokenSafe()

    // Call logout API
    const response = await fetch('/admin/api/logout', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
      },
      credentials: 'same-origin',
    })

    if (response.ok) {
      // Reset auth state
      resetAuthState()
      
      // Close dropdown
      closeDropdown()
      
      // Redirect to signin
      window.location.href = '/admin/signin'
    } else {
      console.error('Logout failed')
      // Still redirect even if API call fails
      resetAuthState()
      window.location.href = '/admin/signin'
    }
  } catch (error) {
    console.error('Error during logout:', error)
    // Still redirect even if there's an error
    resetAuthState()
    window.location.href = '/admin/signin'
  }
}

const handleClickOutside = (event: Event) => {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target as Node)) {
    closeDropdown()
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
  fetchUser()

  // listen for global user updates (e.g., avatar changed elsewhere)
  const handler = (e: Event) => {
    const ev = e as CustomEvent
    if (ev?.detail) {
      const data = ev.detail
      // normalize avatar (absolute/relative)
      let url = data.avatar_url || data.avatar || null
      if (url && !/^https?:\/\//i.test(url)) {
        url = (window.location.origin || '') + (url.startsWith('/') ? url : '/' + url)
      }
      if (url) {
        if (!user.value) user.value = { id: 0, name: '', email: '', avatar: url }
        else user.value.avatar = url
        imageError.value = false
      }
    }
  }
  window.addEventListener('user-updated', handler)

  // store for cleanup
  ;(window as any).__userUpdatedHandler = handler
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
  const h = (window as any).__userUpdatedHandler
  if (h) window.removeEventListener('user-updated', h)
})
</script>
