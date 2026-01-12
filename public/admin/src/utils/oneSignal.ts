const APP_ID = '79707caf-162b-482f-b04d-6d23b9fbb830'

let loader: Promise<any> | null = null

const loadSdk = async (): Promise<any> => {
  if (typeof window === 'undefined') return null
  if ((window as any).OneSignal) return (window as any).OneSignal
  if (loader) return loader

  loader = new Promise((resolve, reject) => {
    const script = document.createElement('script')
    script.src = 'https://cdn.onesignal.com/sdks/web/v16/OneSignalSDK.page.js'
    script.defer = true
    script.onload = () => resolve((window as any).OneSignal)
    script.onerror = (e) => reject(e)
    document.head.appendChild(script)
  })

  return loader
}

export const initOneSignal = async (externalUserId?: string): Promise<boolean> => {
  try {
    const OneSignal = await loadSdk()
    if (!OneSignal) return false

    ;(window as any).OneSignalDeferred = (window as any).OneSignalDeferred || []
    ;(window as any).OneSignalDeferred.push(async function (OneSignalInstance: any) {
      await OneSignalInstance.init({
        appId: APP_ID,
        serviceWorkerPath: '/OneSignalSDKWorker.js',
        serviceWorkerUpdaterPath: '/OneSignalSDKWorker.js',
        serviceWorkerParam: { scope: '/' },
      })

      try {
        // Skip if not supported (e.g., in-app browsers)
        const supported = await OneSignalInstance.Notifications.isPushSupported()
        if (!supported) {
          console.warn('[OneSignal] push not supported in this browser')
          return
        }
      } catch (err) {
        console.warn('[OneSignal] support check failed', err)
      }

      try {
        // Ensure permission; OneSignal may return a string or boolean
        const permission = await OneSignalInstance.Notifications.requestPermission()
        const granted = permission === 'granted' || permission === true
        if (!granted) {
          console.warn('[OneSignal] notifications not granted, current state:', permission)
        }
      } catch (err) {
        console.warn('[OneSignal] permission request failed', err)
      }

      try {
        // Ensure subscription is opted-in; this is required for repeat sends
        const isOptedIn = await OneSignalInstance.User.PushSubscription.optedIn
        if (!isOptedIn) {
          await OneSignalInstance.User.PushSubscription.optIn()
        }
      } catch (err) {
        console.warn('[OneSignal] opt-in failed', err)
      }

      // After opt-in, collect player id and register it with backend for debugging
      try {
        // get player id (OneSignal subscription id)
        const playerId = await (window as any).OneSignal?.getUserId()
        const extId = await (window as any).OneSignal?.getExternalUserId()
        if (playerId) {
          try {
            const csrf = await fetch('/admin/api/csrf-token').then(r=>r.text()).catch(()=>null)
            await fetch('/admin/api/onesignal/register', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf || ''
              },
              credentials: 'same-origin',
              body: JSON.stringify({ player_id: playerId, device: navigator.userAgent, external_user_id: extId || undefined })
            })
            console.info('[OneSignal] registered player id to backend', playerId)
          } catch (e) {
            console.warn('[OneSignal] backend register failed', e)
          }
        } else {
          console.info('[OneSignal] player id not available yet')
        }
      } catch (err) {
        console.warn('[OneSignal] collect player id failed', err)
      }

      if (externalUserId) {
        try {
          // Prefer the global OneSignal API if available
          const globalOneSignal = (window as any).OneSignal || OneSignal
          if (globalOneSignal && typeof globalOneSignal.setExternalUserId === 'function') {
            await globalOneSignal.setExternalUserId(String(externalUserId))
            console.info('[OneSignal] external user id set (global)', externalUserId)
          } else if (OneSignalInstance && typeof OneSignalInstance.setExternalUserId === 'function') {
            await OneSignalInstance.setExternalUserId(String(externalUserId))
            console.info('[OneSignal] external user id set (instance)', externalUserId)
          } else if (globalOneSignal && typeof globalOneSignal.login === 'function') {
            // fallback: older SDKs had login/identify methods
            await globalOneSignal.login(String(externalUserId))
            console.info('[OneSignal] external user id set via login fallback', externalUserId)
          } else {
            console.warn('[OneSignal] setExternalUserId not available on SDK instance')
            console.info('OneSignal available methods:', Object.keys(globalOneSignal || {}))
          }
        } catch (e) {
          console.warn('[OneSignal] setExternalUserId failed', e)
        }
      }
    })
    return true
  } catch (e) {
    console.error('[OneSignal] init failed', e)
    return false
  }
}
