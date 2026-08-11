<script setup>
import { onMounted, ref } from 'vue'
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

const selectedCode = ref(null)

function selectSurvey(code) {
  selectedCode.value = code
  fetchResults(code)
}

onMounted(fetchSurveys)
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
