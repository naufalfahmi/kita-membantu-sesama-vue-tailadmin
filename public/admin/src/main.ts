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
import { initOneSignal } from '@/utils/oneSignal'

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

router.isReady().then(async () => {
  app.mount('#app')

  // initialize OneSignal with external user id if available
  try {
    const res = await fetch('/admin/api/user', { credentials: 'same-origin' })
    if (res.ok) {
      const data = await res.json().catch(() => null)
      const externalId = data?.data?.id || data?.user?.id || data?.id || null
      await initOneSignal(externalId ? String(externalId) : undefined)
    } else {
      await initOneSignal(undefined)
    }
  } catch (e) {
    console.warn('[OneSignal] init skipped', e)
  }
})
