<template>
  <AdminLayout>
    <PageBreadcrumb :pageTitle="currentPageTitle" />
    <div
      class="min-h-screen rounded-2xl border border-gray-200 bg-white px-5 py-7 dark:border-gray-800 dark:bg-white/[0.03] xl:px-10 xl:py-12"
    >
      <div class="mb-6 flex items-center justify-between">
        <h3 class="font-semibold text-gray-800 text-theme-xl dark:text-white/90 sm:text-2xl">
          {{ currentPageTitle }}
        </h3>
        <button
          @click="handleCancel"
          class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]"
        >
          Batal
        </button>
      </div>

      <form @submit.prevent="handleSave" class="flex flex-col">
        <div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2">
          <!-- Kode Kantor Cabang -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Kode Kantor Cabang
            </label>
            <input
              type="text"
              v-model="formData.kode"
              placeholder="Kode akan di-generate otomatis"
              :readonly="!isEditMode"
              :disabled="!isEditMode"
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
              :class="{ 'cursor-not-allowed opacity-60': !isEditMode }"
            />
            <p v-if="!isEditMode" class="mt-1 text-xs text-gray-500 dark:text-gray-400">
              Kode akan di-generate otomatis
            </p>
          </div>

          <!-- Nama Kantor Cabang -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Nama Kantor Cabang <span class="text-red-500">*</span>
            </label>
            <input
              type="text"
              v-model="formData.nama"
              placeholder="Masukkan nama kantor cabang"
              required
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
          </div>

          <!-- Kelurahan -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Kelurahan
            </label>
            <input
              type="text"
              v-model="formData.kelurahan"
              placeholder="Masukkan kelurahan"
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
          </div>

          <!-- Kecamatan -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Kecamatan
            </label>
            <input
              type="text"
              v-model="formData.kecamatan"
              placeholder="Masukkan kecamatan"
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
          </div>

          <!-- Kota -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Kota
            </label>
            <input
              type="text"
              v-model="formData.kota"
              placeholder="Masukkan kota"
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
          </div>

          <!-- Provinsi -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Provinsi
            </label>
            <input
              type="text"
              v-model="formData.provinsi"
              placeholder="Masukkan provinsi"
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
          </div>

          <!-- Kode Pos -->
          <div class="lg:col-span-1">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Kode Pos
            </label>
            <input
              type="text"
              v-model="formData.kode_pos"
              placeholder="Masukkan kode pos"
              class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
          </div>

          <!-- Radius (m) placed next to Kode Pos -->
          <div class="lg:col-span-1">
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Radius (meter)</label>
            <input
              type="number"
              v-model.number="formData.radius"
              placeholder="Radius (m)"
              min="0"
              class="h-11 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
            />
          </div>

          <!-- Alamat -->
          <div class="lg:col-span-2">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Alamat
            </label>
            <textarea
              v-model="formData.alamat"
              placeholder="Masukkan alamat lengkap"
              rows="3"
              class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            ></textarea>
          </div>

          <!-- Map Container untuk Latitude & Longitude -->
          <div class="lg:col-span-2">
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Pilih Lokasi di Peta
            </label>
            <div
              ref="mapContainer"
              class="w-full rounded-lg border border-gray-300 dark:border-gray-700"
              style="height: 400px;"
            ></div>
            <div class="mt-3 grid grid-cols-1 gap-4 lg:grid-cols-2">
              <div>
                <label
                  class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                >
                  Latitude
                </label>
                <input
                  type="text"
                  v-model="formData.latitude"
                  placeholder="Koordinat latitude"
                  readonly
                  class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 dark:placeholder:text-white/30"
                />
              </div>
              <div>
                <label
                  class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                >
                  Longitude
                </label>
                <input
                  type="text"
                  v-model="formData.longitude"
                  placeholder="Koordinat longitude"
                  readonly
                  class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 dark:placeholder:text-white/30"
                />
              </div>
            </div>

            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
              Klik pada peta untuk memilih lokasi atau drag marker untuk memperbarui koordinat. 
              Titik di peta akan otomatis diperbarui saat Anda mengisi Kelurahan, Kecamatan, Kota, atau Provinsi.
            </p>
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
            {{ isEditMode ? 'Simpan Perubahan' : 'Simpan' }}
          </button>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted, onUnmounted, nextTick, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import PageBreadcrumb from '@/components/common/PageBreadcrumb.vue'

// Extend Window interface for maplibregl
declare global {
  interface Window {
    maplibregl?: any
  }
}

const route = useRoute()
const router = useRouter()
const toast = useToast()
const mapContainer = ref<HTMLElement | null>(null)
let mapInstance: any = null
let marker: any = null
let isGeocodingInProgress = false

const isEditMode = computed(() => route.params.id !== undefined && route.params.id !== 'new')
const currentPageTitle = computed(() => {
  return isEditMode.value ? 'Edit Kantor Cabang' : 'Tambah Kantor Cabang'
})

// Form data
const formData = reactive({
  kode: '',
  nama: '',
  kelurahan: '',
  kecamatan: '',
  kota: '',
  provinsi: '',
  kode_pos: '',
  alamat: '',
  latitude: '',
  longitude: '',
  radius: null,
})

// Initialize MapTiler map
const initMap = async () => {
  if (!mapContainer.value) return

  try {
    // Load MapTiler GL JS from CDN if not already loaded
    if (!window.maplibregl) {
      await new Promise<void>((resolve, reject) => {
        // Check if already loading
        if (document.querySelector('script[src*="maplibre-gl"]')) {
          // Wait for it to load
          const checkInterval = setInterval(() => {
            if (window.maplibregl) {
              clearInterval(checkInterval)
              resolve()
            }
          }, 100)
          return
        }

        const script = document.createElement('script')
        script.src = 'https://unpkg.com/maplibre-gl@3.6.2/dist/maplibre-gl.js'
        script.onload = () => resolve()
        script.onerror = () => reject(new Error('Failed to load MapTiler'))
        document.head.appendChild(script)

        const link = document.createElement('link')
        link.rel = 'stylesheet'
        link.href = 'https://unpkg.com/maplibre-gl@3.6.2/dist/maplibre-gl.css'
        document.head.appendChild(link)
      })
    }
    
    setupMap()
  } catch (error) {
    console.error('Error loading MapTiler:', error)
  }
}

const setupMap = () => {
  if (!mapContainer.value || !window.maplibregl) return

  // Get initial coordinates from form or use default (Jakarta)
  const initialLng = formData.longitude ? parseFloat(formData.longitude) : 106.8451
  const initialLat = formData.latitude ? parseFloat(formData.latitude) : -6.2088

  // Initialize map with MapTiler
  mapInstance = new window.maplibregl.Map({
    container: mapContainer.value,
    style: `https://api.maptiler.com/maps/streets-v2/style.json?key=M3H6Ho6JJ93ZPH10DnYt`,
    center: [initialLng, initialLat],
    zoom: formData.latitude && formData.longitude ? 15 : 13,
  })

  mapInstance.on('load', () => {
    // Add marker
    marker = new window.maplibregl.Marker({
      draggable: true,
    })
      .setLngLat([initialLng, initialLat])
      .addTo(mapInstance)

    // Update coordinates when marker is dragged
    marker.on('dragend', () => {
      const lngLat = marker.getLngLat()
      formData.longitude = lngLat.lng.toFixed(6)
      formData.latitude = lngLat.lat.toFixed(6)
        // update radius circle on map
        drawRadiusCircle()
    })

    // Update coordinates when map is clicked
    mapInstance.on('click', (e: any) => {
      const { lng, lat } = e.lngLat
      marker.setLngLat([lng, lat])
      formData.longitude = lng.toFixed(6)
      formData.latitude = lat.toFixed(6)
      // update radius circle on map
      drawRadiusCircle()
    })
  })
}

// Utility: compute destination point given lat/lon, distance (m), bearing (deg)
const destinationPoint = (lat: number, lon: number, distance: number, bearing: number) => {
  const R = 6378137 // Earth radius in meters
  const δ = distance / R
  const θ = (bearing * Math.PI) / 180
  const φ1 = (lat * Math.PI) / 180
  const λ1 = (lon * Math.PI) / 180

  const sinφ1 = Math.sin(φ1)
  const cosφ1 = Math.cos(φ1)
  const sinδ = Math.sin(δ)
  const cosδ = Math.cos(δ)

  const sinφ2 = sinφ1 * cosδ + cosφ1 * sinδ * Math.cos(θ)
  const φ2 = Math.asin(sinφ2)
  const y = Math.sin(θ) * sinδ * cosφ1
  const x = cosδ - sinφ1 * sinφ2
  const λ2 = λ1 + Math.atan2(y, x)

  return { lat: (φ2 * 180) / Math.PI, lon: ((λ2 * 180) / Math.PI) }
}

// Create a GeoJSON polygon approximating a circle
const createCircleGeoJSON = (centerLat: number, centerLon: number, radiusMeters: number, steps = 64) => {
  const coords: Array<[number, number]> = []
  for (let i = 0; i <= steps; i++) {
    const bearing = (i * 360) / steps
    const p = destinationPoint(centerLat, centerLon, radiusMeters, bearing)
    coords.push([p.lon, p.lat])
  }
  return {
    type: 'Feature',
    geometry: {
      type: 'Polygon',
      coordinates: [coords],
    },
  }
}

// Draw or update radius circle on map
const drawRadiusCircle = () => {
  if (!mapInstance) return
  const lat = parseFloat(formData.latitude || '0')
  const lon = parseFloat(formData.longitude || '0')
  const r = formData.radius

  const srcId = 'kantor-radius-src'
  const fillLayerId = 'kantor-radius-fill'
  const outlineLayerId = 'kantor-radius-outline'

  // remove existing
  if (mapInstance.getLayer(outlineLayerId)) {
    try { mapInstance.removeLayer(outlineLayerId) } catch (e) {}
  }
  if (mapInstance.getLayer(fillLayerId)) {
    try { mapInstance.removeLayer(fillLayerId) } catch (e) {}
  }
  if (mapInstance.getSource(srcId)) {
    try { mapInstance.removeSource(srcId) } catch (e) {}
  }

  if (!r || !lat || !lon) return

  const feature = createCircleGeoJSON(lat, lon, Number(r), 128)

  mapInstance.addSource(srcId, {
    type: 'geojson',
    data: feature,
  })

  mapInstance.addLayer({
    id: fillLayerId,
    type: 'fill',
    source: srcId,
    paint: {
      'fill-color': '#3b82f6',
      'fill-opacity': 0.12,
    },
  })

  mapInstance.addLayer({
    id: outlineLayerId,
    type: 'line',
    source: srcId,
    paint: {
      'line-color': '#2563eb',
      'line-width': 2,
    },
  })
}

// Watch radius changes to update circle interactively
watch(() => formData.radius, () => {
  if (mapInstance) drawRadiusCircle()
})

// Geocoding function to get coordinates from address
const geocodeAddress = async (address: string): Promise<{ lat: number; lng: number } | null> => {
  if (!address || address.trim() === '') return null

  try {
    // Using Nominatim (OpenStreetMap) - free and no API key required
    const encodedAddress = encodeURIComponent(address + ', Indonesia')
    const response = await fetch(
      `https://nominatim.openstreetmap.org/search?q=${encodedAddress}&format=json&limit=1&addressdetails=1`,
      {
        headers: {
          'User-Agent': 'KMS-Application/1.0' // Required by Nominatim
        }
      }
    )

    if (!response.ok) {
      throw new Error('Geocoding request failed')
    }

    const data = await response.json()
    
    if (data && data.length > 0) {
      return {
        lat: parseFloat(data[0].lat),
        lng: parseFloat(data[0].lon)
      }
    }

    return null
  } catch (error) {
    console.error('Error geocoding address:', error)
    return null
  }
}

// Update map marker position
const updateMapMarker = (lat: number, lng: number) => {
  if (!mapInstance || !marker) return

  // Update marker position
  marker.setLngLat([lng, lat])
  
  // Update form data
  formData.latitude = lat.toFixed(6)
  formData.longitude = lng.toFixed(6)
  
  // Center map on new location
  mapInstance.flyTo({
    center: [lng, lat],
    zoom: 15,
    duration: 1000
  })
}

// Watch for all address field changes (alamat, kelurahan, kecamatan, kota, provinsi) and update map
watch(
  () => [formData.alamat, formData.kelurahan, formData.kecamatan, formData.kota, formData.provinsi],
  async (newValues, oldValues) => {
    // Skip if geocoding is already in progress or map is not initialized
    if (isGeocodingInProgress || !mapInstance || !marker) return
    
    // Skip if this is initial load (all values are empty)
    if (!newValues[0] && !newValues[1] && !newValues[2] && !newValues[3] && !newValues[4]) return

    // Build address string from available fields
    const addressParts = []
    if (formData.alamat) addressParts.push(formData.alamat)
    if (formData.kelurahan) addressParts.push(formData.kelurahan)
    if (formData.kecamatan) addressParts.push(formData.kecamatan)
    if (formData.kota) addressParts.push(formData.kota)
    if (formData.provinsi) addressParts.push(formData.provinsi)
    
    const fullAddress = addressParts.join(', ')
    
    if (fullAddress.trim() === '') return

    // Add small delay to avoid too many requests while user is typing
    await new Promise(resolve => setTimeout(resolve, 500))
    
    // Check if values changed during delay
    const currentAddress = [
      formData.alamat,
      formData.kelurahan,
      formData.kecamatan,
      formData.kota,
      formData.provinsi
    ].filter(Boolean).join(', ')
    
    if (currentAddress !== fullAddress) return

    isGeocodingInProgress = true
    
    try {
      const coordinates = await geocodeAddress(fullAddress)
      
      if (coordinates) {
        updateMapMarker(coordinates.lat, coordinates.lng)
      }
    } catch (error) {
      console.error('Error updating map from address:', error)
    } finally {
      isGeocodingInProgress = false
    }
  },
  { deep: true }
)

// Get CSRF token
const getCsrfToken = async (): Promise<string> => {
  try {
    // Try to get from meta tag first
    const metaToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    if (metaToken) {
      return metaToken
    }
    
    // If not available, fetch from API
    const response = await fetch('/admin/api/csrf-token', {
      method: 'GET',
      credentials: 'same-origin',
    })
    
    if (!response.ok) {
      throw new Error(`Failed to get CSRF token: ${response.status}`)
    }
    
    const data = await response.json()
    return data.csrf_token || ''
  } catch (e) {
    console.error('Failed to get CSRF token:', e)
    return ''
  }
}

// Load next kode for new kantor cabang
const loadNextKode = async () => {
  if (!isEditMode.value) {
    try {
      const csrfToken = await getCsrfToken()
      const response = await fetch('/admin/api/kantor-cabang-next-kode', {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
        },
        credentials: 'same-origin',
      })
      
      const result = await response.json()
      if (result.success && result.data) {
        formData.kode = result.data.kode || ''
      }
    } catch (error) {
      console.error('Error loading next kode:', error)
    }
  }
}

// Load data if edit mode
const loadData = async () => {
  if (isEditMode.value && route.params.id) {
    const id = route.params.id as string
    try {
      const csrfToken = await getCsrfToken()
      const response = await fetch(`/admin/api/kantor-cabang/${id}`, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
        },
        credentials: 'same-origin',
      })
      
      const result = await response.json()
      if (result.success && result.data) {
        const data = result.data
        formData.kode = data.kode || ''
        formData.nama = data.nama || ''
        formData.kelurahan = data.kelurahan || ''
        formData.kecamatan = data.kecamatan || ''
        formData.kota = data.kota || ''
        formData.provinsi = data.provinsi || ''
        formData.kode_pos = data.kode_pos || ''
        formData.radius = data.radius ?? null
        formData.alamat = data.alamat || ''
        formData.latitude = data.latitude ? String(data.latitude) : ''
        formData.longitude = data.longitude ? String(data.longitude) : ''
        
        // Update map if coordinates exist
        if (data.latitude && data.longitude) {
          await nextTick()
          if (mapInstance && marker) {
            const lat = parseFloat(data.latitude)
            const lng = parseFloat(data.longitude)
            marker.setLngLat([lng, lat])
            mapInstance.flyTo({
              center: [lng, lat],
              zoom: 15,
              duration: 1000
            })
          }
        }
      } else {
        toast.error('Gagal memuat data kantor cabang')
        router.push('/administrasi/kantor-cabang')
      }
    } catch (error) {
      console.error('Error loading data:', error)
      toast.error('Terjadi kesalahan saat memuat data')
      router.push('/administrasi/kantor-cabang')
    }
  }
}

// Handle cancel
const handleCancel = () => {
  router.push('/administrasi/kantor-cabang')
}

// Handle save
const handleSave = async () => {
  if (!formData.nama) {
    toast.error('Nama Kantor Cabang wajib diisi')
    return
  }

  try {
    // Get CSRF token
    const csrfToken = await getCsrfToken()
    if (!csrfToken) {
      toast.error('Gagal mendapatkan CSRF token. Silakan refresh halaman.')
      return
    }

    const payload: any = {
      nama: formData.nama,
      kelurahan: formData.kelurahan || null,
      kecamatan: formData.kecamatan || null,
      kota: formData.kota || null,
      provinsi: formData.provinsi || null,
      kode_pos: formData.kode_pos || null,
      alamat: formData.alamat || null,
      latitude: formData.latitude ? parseFloat(formData.latitude) : null,
      longitude: formData.longitude ? parseFloat(formData.longitude) : null,
      radius: formData.radius !== null ? parseInt(String(formData.radius), 10) : null,
    }

    // Only include kode in edit mode, let backend generate in create mode
    if (isEditMode.value && formData.kode) {
      payload.kode = formData.kode
    }

    const url = isEditMode.value 
      ? `/admin/api/kantor-cabang/${route.params.id}`
      : '/admin/api/kantor-cabang'
    
    const method = isEditMode.value ? 'PUT' : 'POST'

    const response = await fetch(url, {
      method: method,
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
      },
      credentials: 'same-origin',
      body: JSON.stringify(payload),
    })

    const result = await response.json()
    
    if (result.success) {
      toast.success(isEditMode.value ? 'Kantor cabang berhasil diupdate' : 'Kantor cabang berhasil ditambahkan')
      router.push('/administrasi/kantor-cabang')
    } else {
      if (result.errors) {
        const errorMessages = Object.values(result.errors).flat().join(', ')
        toast.error(`Validasi gagal: ${errorMessages}`)
      } else {
        toast.error(result.message || 'Gagal menyimpan data')
      }
    }
  } catch (error) {
    console.error('Error saving:', error)
    toast.error('Terjadi kesalahan saat menyimpan data')
  }
}

onMounted(async () => {
  if (!isEditMode.value) {
    await loadNextKode()
  } else {
    await loadData()
  }
  await nextTick()
  initMap()
})

onUnmounted(() => {
  if (mapInstance) {
    mapInstance.remove()
    mapInstance = null
  }
  marker = null
})
</script>

