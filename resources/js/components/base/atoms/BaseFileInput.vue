<template>
  <div>
    <input
      :id="id"
      type="file"
      class="form-control"
      @change="handleChange"
      :accept="accept"
    />
    <div v-if="helpText" class="form-text">{{ helpText }}</div>
  </div>
</template>

<script setup>
const props = defineProps({
  id: { type: String, default: '' },
  accept: { type: String, default: '' },
  helpText: { type: String, default: '' },
})
const emit = defineEmits(['change'])

const handleChange = (event) => {
  const file = event.target.files[0] || null
  emit('change', file)
}
</script>

<style>
/* global Bootstrap styles are expected */
</style>
