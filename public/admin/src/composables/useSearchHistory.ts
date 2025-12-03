import { ref } from 'vue'

export interface SearchHistoryItem {
  type: 'search' | 'menu'
  label: string
  path?: string
  timestamp?: number
}

const MAX_HISTORY = 10
const STORAGE_KEY = 'kms_search_history'

export function useSearchHistory() {
  const history = ref<SearchHistoryItem[]>([])

  // Load history from localStorage
  const loadHistory = () => {
    try {
      const stored = localStorage.getItem(STORAGE_KEY)
      if (stored) {
        const parsed = JSON.parse(stored)
        // Handle backward compatibility - convert old string format to new object format
        if (Array.isArray(parsed)) {
          history.value = parsed.map((item: any) => {
            if (typeof item === 'string') {
              // Old format: string - convert to new format
              return {
                type: 'search' as const,
                label: item,
                timestamp: Date.now(),
              }
            }
            // New format: object - ensure it has required fields
            if (item && typeof item === 'object' && item.label) {
              return {
                type: item.type || 'search',
                label: item.label,
                path: item.path || undefined,
                timestamp: item.timestamp || Date.now(),
              }
            }
            // Invalid format - skip
            return null
          }).filter((item: any) => item !== null) as SearchHistoryItem[]
        } else {
          history.value = []
        }
      }
    } catch (error) {
      console.error('Error loading search history:', error)
      history.value = []
    }
  }

  // Save search query to history
  const saveSearch = (query: string) => {
    if (!query || query.trim().length < 2) return
    
    const trimmedQuery = query.trim()
    const item: SearchHistoryItem = {
      type: 'search',
      label: trimmedQuery,
      timestamp: Date.now(),
    }
    
    saveToHistory(item)
  }

  // Save menu navigation to history
  const saveMenu = (menuName: string, menuPath: string) => {
    if (!menuName || !menuPath) return
    
    const item: SearchHistoryItem = {
      type: 'menu',
      label: menuName,
      path: menuPath,
      timestamp: Date.now(),
    }
    
    saveToHistory(item)
  }

  // Save item to history
  const saveToHistory = (item: SearchHistoryItem) => {
    // Remove if already exists (same type and label)
    history.value = history.value.filter(
      (h) => !(h.type === item.type && h.label.toLowerCase() === item.label.toLowerCase())
    )
    
    // Add to beginning
    history.value.unshift(item)
    
    // Limit to MAX_HISTORY items
    if (history.value.length > MAX_HISTORY) {
      history.value = history.value.slice(0, MAX_HISTORY)
    }
    
    // Save to localStorage
    try {
      localStorage.setItem(STORAGE_KEY, JSON.stringify(history.value))
    } catch (error) {
      console.error('Error saving search history:', error)
    }
  }

  // Remove single history item
  const removeHistory = (item: SearchHistoryItem | string) => {
    // Handle backward compatibility with old string format
    if (typeof item === 'string') {
      history.value = history.value.filter((h) => h.label !== item)
    } else {
      history.value = history.value.filter(
        (h) => !(h.type === item.type && h.label === item.label && (!item.path || h.path === item.path))
      )
    }
    
    try {
      localStorage.setItem(STORAGE_KEY, JSON.stringify(history.value))
    } catch (error) {
      console.error('Error removing search history:', error)
    }
  }

  // Clear all history
  const clearAllHistory = () => {
    history.value = []
    try {
      localStorage.removeItem(STORAGE_KEY)
    } catch (error) {
      console.error('Error clearing search history:', error)
    }
  }

  // Get history as simple string array (for backward compatibility)
  const getHistoryAsStrings = (): string[] => {
    return history.value.map(item => item.label)
  }

  return {
    history,
    loadHistory,
    saveSearch,
    saveMenu,
    removeHistory,
    clearAllHistory,
    getHistoryAsStrings,
  }
}

