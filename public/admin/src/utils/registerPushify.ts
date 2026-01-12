import { getCsrfTokenSafe } from '@/utils/getCsrfToken'

const getCookie = (name: string): string | null => {
  const match = document.cookie.match(new RegExp('(?:^|; )' + name.replace(/([.$?*|{}()\[\]\\/\+^])/g, '\\$1') + '=([^;]*)'))
  return match ? decodeURIComponent(match[1]) : null
}

let pixelLoader: Promise<boolean> | null = null

const waitForPushifySubscriberId = async (retries = 5, delayMs = 500): Promise<string | null> => {
  for (let i = 0; i < retries; i++) {
    const id =
      (window as any)?.Pushify?.subscriber?.id ||
      localStorage.getItem('pushify_subscriber_id') ||
      getCookie('pushify_subscriber_id') ||
      null
    if (id) return id
    await new Promise((res) => setTimeout(res, delayMs))
  }
  return null
}

const loadPushifyPixel = async (): Promise<boolean> => {
  if (typeof window === 'undefined') return false
  if ((window as any).Pushify) return true
  if (pixelLoader) return pixelLoader

  pixelLoader = new Promise((resolve) => {
    const script = document.createElement('script')
    // Embed URL provided by Pushify dashboard
    script.src = 'https://pushify.com/pixel/cTuIvt3PzZdzReFK'
    script.defer = true
    script.async = true
    script.onload = () => resolve(true)
    script.onerror = () => {
      console.warn('[Pushify] pixel script failed to load')
      resolve(false)
    }
    document.head.appendChild(script)
  })

  return pixelLoader
}

// Pushify VAPID public key (applicationServerKey) - provided by Pushify dashboard
const PUSHIFY_VAPID_PUBLIC_KEY = 'BLXdtqVfhfI_80eHud3U_APDHa-b4R-NfW9L2zJ8hYvF9V1SwQEbFRr0YeIAhcA5cjmD5DGx29EUZi6g4M3c5_g'

const urlBase64ToUint8Array = (base64String: string): Uint8Array => {
  const padding = '='.repeat((4 - (base64String.length % 4)) % 4)
  const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/')
  const rawData = atob(base64)
  const outputArray = new Uint8Array(rawData.length)
  for (let i = 0; i < rawData.length; ++i) {
    outputArray[i] = rawData.charCodeAt(i)
  }
  return outputArray
}

export const registerPushify = async (): Promise<boolean> => {
  if (typeof window === 'undefined' || !('serviceWorker' in navigator) || !('Notification' in window)) return false

  // Ensure Pushify pixel script is loaded to set subscriber id
  await loadPushifyPixel()

  try {
    await navigator.serviceWorker.register('/pushify.js')
  } catch (error) {
    console.error('[Pushify] service worker registration failed', error)
    return false
  }

  let permission: NotificationPermission = Notification.permission
  if (permission === 'default') {
    permission = await Notification.requestPermission()
  }

  if (permission !== 'granted') {
    console.warn('[Pushify] permission not granted')
    return false
  }

  try {
    const registration = await navigator.serviceWorker.ready

    let subscription = await registration.pushManager.getSubscription()
    if (!subscription) {
      try {
        subscription = await registration.pushManager.subscribe({
          userVisibleOnly: true,
          applicationServerKey: urlBase64ToUint8Array(PUSHIFY_VAPID_PUBLIC_KEY),
        })
      } catch (subscribeError) {
        console.error('[Pushify] subscribe failed', subscribeError)
      }
    }

    const pushifySubscriberId = await waitForPushifySubscriberId()

    if (subscription) {
      const payload = {
        endpoint: subscription.endpoint,
        keys: (subscription.toJSON() as any)?.keys || {},
        pushify_subscriber_id: pushifySubscriberId,
        device: navigator.userAgent.slice(0, 100),
      }

      const csrf = await getCsrfTokenSafe()

      const res = await fetch('/admin/api/push-subscriptions', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrf,
        },
        body: JSON.stringify(payload),
        credentials: 'same-origin',
      })

      const data = await res.json().catch(() => null)
      if (!res.ok || !data?.success) {
        console.warn('[Pushify] failed to store subscription', data)
        return false
      }

      if (!pushifySubscriberId) {
        console.warn('[Pushify] subscriber id missing even after pixel load')
      }

      return true
    }
  } catch (error) {
    console.error('[Pushify] failed to register subscription', error)
  }

  return false
}
