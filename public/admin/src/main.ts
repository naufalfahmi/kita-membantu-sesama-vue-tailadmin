import './assets/main.css'
// Import Swiper styles
import 'swiper/css'
import 'swiper/css/navigation'
import 'swiper/css/pagination'
import 'jsvectormap/dist/jsvectormap.css'
import 'flatpickr/dist/flatpickr.css'
import Toast from 'vue-toastification'
import 'vue-toastification/dist/index.css'

import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import VueApexCharts from 'vue3-apexcharts'
import { AgGridVue } from 'ag-grid-vue3'
import 'ag-grid-community/styles/ag-grid.css'
import 'ag-grid-community/styles/ag-theme-alpine.css'

const getCsrfToken = (): string => {
  return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
}

const getCookie = (name: string): string | null => {
  const match = document.cookie.match(new RegExp('(?:^|; )' + name.replace(/([.$?*|{}()\[\]\\\/\+^])/g, '\\$1') + '=([^;]*)'))
  return match ? decodeURIComponent(match[1]) : null
}

const registerPushify = async () => {
  if (typeof window === 'undefined' || !('serviceWorker' in navigator) || !('Notification' in window)) return

  try {
    await navigator.serviceWorker.register('/pushify.js')
  } catch (error) {
    console.error('[Pushify] service worker registration failed', error)
    return
  }

  let permission: NotificationPermission = Notification.permission
  if (permission === 'default') {
    permission = await Notification.requestPermission()
  }

  if (permission !== 'granted') {
    console.warn('[Pushify] permission not granted')
    return
  }

  try {
    const registration = await navigator.serviceWorker.ready
    const subscription = await registration.pushManager.getSubscription()

    // Pushify stores subscriber id client-side; attempt to read it
    const pushifySubscriberId =
      (window as any)?.Pushify?.subscriber?.id ||
      localStorage.getItem('pushify_subscriber_id') ||
      getCookie('pushify_subscriber_id') ||
      null

    if (subscription) {
      const payload = {
        endpoint: subscription.endpoint,
        keys: (subscription.toJSON() as any)?.keys || {},
        pushify_subscriber_id: pushifySubscriberId,
        device: navigator.userAgent,
      }

      await fetch('/admin/api/push-subscriptions', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': getCsrfToken(),
        },
        body: JSON.stringify(payload),
        credentials: 'same-origin',
      })
    }
  } catch (error) {
    console.error('[Pushify] failed to register subscription', error)
  }
}

const app = createApp(App)

// Register AG Grid globally so lazy-loaded views can use <ag-grid-vue>
app.component('AgGridVue', AgGridVue)

app.use(router)
app.use(VueApexCharts)
app.use(Toast, {
  transition: 'Vue-Toastification__bounce',
  maxToasts: 20,
  newestOnTop: true,
  position: 'top-right',
  timeout: 3000,
  closeOnClick: true,
  pauseOnFocusLoss: true,
  pauseOnHover: true,
  draggable: true,
  draggablePercent: 0.6,
  showCloseButtonOnHover: false,
  hideProgressBar: false,
  closeButton: 'button',
  icon: true,
  rtl: false,
  containerClass: 'toast-container-custom',
})

router.isReady().then(() => {
  app.mount('#app')
  // fire-and-forget push registration
  registerPushify()
})
