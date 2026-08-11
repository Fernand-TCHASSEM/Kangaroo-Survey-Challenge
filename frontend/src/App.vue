<script setup>
import { onMounted, onUnmounted, ref } from 'vue'
import { useSurveys } from './composables/useSurveys'
import SurveyList from './components/SurveyList.vue'
import SurveyDetail from './components/SurveyDetail.vue'

const {
  surveys,
  surveysLoading,
  surveysError,
  fetchSurveys,
  results,
  resultsLoading,
  resultsError,
  fetchResults,
} = useSurveys()

function codeFromUrl() {
  return new URLSearchParams(window.location.search).get('code')
}

function searchFromUrl() {
  return new URLSearchParams(window.location.search).get('q') || ''
}

const selectedCode = ref(codeFromUrl())
const initialSearch = searchFromUrl()

// Selecting a survey pushes ?code=XX1 to the URL so it's bookmarkable/shareable
// and works with the browser's back/forward buttons (see handlePopState below).
function selectSurvey(code) {
  selectedCode.value = code
  fetchResults(code)

  const url = new URL(window.location.href)
  url.searchParams.set('code', code)
  window.history.pushState({ code }, '', url)
}

// Searching replaces ?q=term in the URL (not pushState — it fires on every
// debounced keystroke, and each one isn't a distinct navigation step) and
// clears the current selection, since it may not match the new results.
function search(term) {
  selectedCode.value = null
  results.value = []
  fetchSurveys(term)

  const url = new URL(window.location.href)
  if (term) {
    url.searchParams.set('q', term)
  } else {
    url.searchParams.delete('q')
  }
  url.searchParams.delete('code')
  window.history.replaceState(null, '', url)
}

function handlePopState() {
  const code = codeFromUrl()
  selectedCode.value = code
  if (code) {
    fetchResults(code)
  }
}

onMounted(async () => {
  await fetchSurveys(initialSearch)

  if (selectedCode.value) {
    fetchResults(selectedCode.value)
  }

  window.addEventListener('popstate', handlePopState)
})

onUnmounted(() => {
  window.removeEventListener('popstate', handlePopState)
})
</script>

<template>
  <main>
    <h1>Kangaroo Survey Results</h1>

    <SurveyList
      :surveys="surveys"
      :loading="surveysLoading"
      :error="surveysError"
      :selected-code="selectedCode"
      :initial-search="initialSearch"
      @select="selectSurvey"
      @search="search"
    />

    <SurveyDetail
      v-if="selectedCode"
      :code="selectedCode"
      :results="results"
      :loading="resultsLoading"
      :error="resultsError"
    />
  </main>
</template>
