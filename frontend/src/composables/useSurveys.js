import { ref } from 'vue'

const API_BASE = import.meta.env.VITE_API_BASE || 'http://localhost:8000'

async function fetchJson(url) {
  const response = await fetch(url)

  if (!response.ok) {
    const error = new Error(`Request to ${url} failed with status ${response.status}`)
    error.status = response.status
    throw error
  }

  return response.json()
}

export function useSurveys() {
  const surveys = ref([])
  const surveysLoading = ref(false)
  const surveysError = ref(null)

  async function fetchSurveys(search) {
    surveysLoading.value = true
    surveysError.value = null

    const url = new URL('/api/list.json', API_BASE)
    if (search) {
      url.searchParams.set('q', search)
    }

    try {
      surveys.value = await fetchJson(url)
    } catch (error) {
      surveysError.value = error
    } finally {
      surveysLoading.value = false
    }
  }

  const results = ref([])
  const resultsLoading = ref(false)
  const resultsError = ref(null)

  async function fetchResults(code) {
    resultsLoading.value = true
    resultsError.value = null
    results.value = []

    try {
      results.value = await fetchJson(`${API_BASE}/api/${code}.json`)
    } catch (error) {
      resultsError.value = error
    } finally {
      resultsLoading.value = false
    }
  }

  return {
    surveys,
    surveysLoading,
    surveysError,
    fetchSurveys,
    results,
    resultsLoading,
    resultsError,
    fetchResults,
  }
}
