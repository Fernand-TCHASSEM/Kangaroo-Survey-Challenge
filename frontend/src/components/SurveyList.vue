<script setup>
import { computed, ref } from 'vue'

const props = defineProps({
  surveys: { type: Array, required: true },
  loading: { type: Boolean, default: false },
  error: { type: Object, default: null },
  selectedCode: { type: String, default: null },
})

defineEmits(['select'])

const search = ref('')

const filteredSurveys = computed(() => {
  const term = search.value.trim().toLowerCase()

  if (!term) {
    return props.surveys
  }

  return props.surveys.filter(
    (survey) =>
      survey.name.toLowerCase().includes(term) || survey.code.toLowerCase().includes(term),
  )
})
</script>

<template>
  <section>
    <input
      v-model="search"
      type="search"
      placeholder="Search by name or code…"
      aria-label="Search surveys"
    />

    <p v-if="loading">Loading surveys…</p>
    <p v-else-if="error">Could not load surveys: {{ error.message }}</p>
    <p v-else-if="filteredSurveys.length === 0">No survey matches “{{ search }}”.</p>

    <ul v-else class="survey-list">
      <li v-for="survey in filteredSurveys" :key="survey.code">
        <button
          type="button"
          :class="{ active: survey.code === selectedCode }"
          @click="$emit('select', survey.code)"
        >
          {{ survey.name }} <span class="code">{{ survey.code }}</span>
        </button>
      </li>
    </ul>
  </section>
</template>
