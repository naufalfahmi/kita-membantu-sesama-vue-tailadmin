import { ref } from 'vue'

const user = ref<Record<string, unknown> | null>(null)
const loading = ref(false)

export function useAuth() {
  // Internal promise to prevent multiple concurrent requests
  let fetchUserPromise: Promise<Record<string, unknown> | null> | null = null

  const fetchUser = async (force = false): Promise<Record<string, unknown> | null> => {
    // Return cached user unless forced
    if (user.value && !force) return user.value

    // If there is an inflight fetch, return it
    if (fetchUserPromise && !force) return fetchUserPromise

    loading.value = true

    fetchUserPromise = (async () => {
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

        if (!response.ok) {
          return null
        }

        const data = await response.json()
        if (data.success && data.user) {
          user.value = data.user
        } else {
          user.value = null
        }
      } catch (err) {
        console.error('Error fetching user:', err)
        user.value = null
      } finally {
        loading.value = false
        fetchUserPromise = null
      }

      return user.value
    })()

    return fetchUserPromise
  }

  const hasPermission = (permission: string): boolean => {
    if (!user.value) return false
    // Ensure permissions is normalized to array of strings
    const maybePerms = (user.value as Record<string, unknown>)['permissions']
    if (!Array.isArray(maybePerms)) return false
    const requested = String(permission || '').trim().toLowerCase()
    return (maybePerms as string[]).some(p => String(p || '').trim().toLowerCase() === requested)
  }

  const isAdmin = (): boolean => {
    if (!user.value) return false
    return Boolean((user.value as Record<string, unknown>)['is_admin'])
  }

  const can = (permission: string) => hasPermission(permission)

  return {
    user,
    loading,
    fetchUser,
    hasPermission,
    isAdmin,
    can,
  }
}
