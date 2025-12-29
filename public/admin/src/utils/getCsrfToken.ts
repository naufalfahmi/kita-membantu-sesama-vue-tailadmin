export async function getCsrfTokenSafe(): Promise<string> {
  try {
    const meta = document.querySelector('meta[name="csrf-token"]')
    const m = meta ? meta.getAttribute('content') : null
    if (m && m.trim() !== '') return m

    const res = await fetch('/admin/api/csrf-token', { method: 'GET', credentials: 'same-origin' })
    // try to parse as JSON safely, fall back to text
    try {
      const j = await res.json()
      if (j && typeof j === 'object' && 'csrf_token' in j) return String(j.csrf_token || '')
    } catch (e) {
      try {
        const t = await res.text()
        // try parse text as JSON
        try {
          const parsed = JSON.parse(t)
          if (parsed && parsed.csrf_token) return String(parsed.csrf_token)
        } catch (e2) {
          // not JSON, return raw text trimmed
          return String(t || '').trim()
        }
      } catch (e3) {
        return ''
      }
    }
  } catch (err) {
    console.error('getCsrfTokenSafe error:', err)
  }
  return ''
}
