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
      })

      if (externalUserId) {
        try {
          await OneSignalInstance.login(String(externalUserId))
        } catch (e) {
          console.warn('[OneSignal] login failed', e)
        }
      }
    })
    return true
  } catch (e) {
    console.error('[OneSignal] init failed', e)
    return false
  }
}
