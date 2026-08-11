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

const selectedCode = ref(codeFromUrl())

// Selecting a survey pushes ?code=XX1 to the URL so it's bookmarkable/shareable
// and works with the browser's back/forward buttons (see handlePopState below).
function selectSurvey(code) {
  selectedCode.value = code
  fetchResults(code)

  const url = new URL(window.location.href)
  url.searchParams.set('code', code)
  window.history.pushState({ code }, '', url)
}

function handlePopState() {
  const code = codeFromUrl()
  selectedCode.value = code
  if (code) {
    fetchResults(code)
  }
}

onMounted(() => {
  fetchSurveys()
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
      @select="selectSurvey"
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
