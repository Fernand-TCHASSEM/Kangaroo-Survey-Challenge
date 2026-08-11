<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  surveys: { type: Array, required: true },
  loading: { type: Boolean, default: false },
  error: { type: Object, default: null },
  selectedCode: { type: String, default: null },
  initialSearch: { type: String, default: '' },
})

const emit = defineEmits(['select', 'search'])

const search = ref(props.initialSearch)
let debounceTimer = null

// Debounced so the backend isn't hit on every keystroke.
watch(search, (term) => {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(() => emit('search', term.trim()), 300)
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
    <p v-else-if="surveys.length === 0">No survey matches “{{ search }}”.</p>

    <ul v-else class="survey-list">
      <li v-for="survey in surveys" :key="survey.code">
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
