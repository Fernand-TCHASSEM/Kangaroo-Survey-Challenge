<script setup>
defineProps({
  code: { type: String, required: true },
  results: { type: Array, required: true },
  loading: { type: Boolean, default: false },
  error: { type: Object, default: null },
})

function maxCount(result) {
  return Math.max(...Object.values(result), 1)
}
</script>

<template>
  <section>
    <h2>Results for {{ code }}</h2>

    <p v-if="loading">Loading results…</p>
    <p v-else-if="error?.status === 404">No survey found for code “{{ code }}”.</p>
    <p v-else-if="error">Could not load results: {{ error.message }}</p>

    <div v-else v-for="question in results" :key="question.label" class="question">
      <h3>{{ question.label }}</h3>

      <ul v-if="question.type === 'qcm'" class="bars">
        <li v-for="(count, option) in question.result" :key="option">
          <span class="option">{{ option }}</span>
          <span class="bar-track">
            <span
              class="bar-fill"
              :style="{ width: (count / maxCount(question.result)) * 100 + '%' }"
            />
          </span>
          <span class="count">{{ count }}</span>
        </li>
      </ul>

      <p v-else-if="question.type === 'numeric'">
        Average: <strong>{{ question.result.toFixed(2) }}</strong>
      </p>

      <pre v-else>{{ question.result }}</pre>
    </div>
  </section>
</template>
