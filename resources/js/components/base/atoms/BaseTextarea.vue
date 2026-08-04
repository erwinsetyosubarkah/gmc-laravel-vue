<template>
  <div>
    <textarea
      ref="textareaRef"
      :id="id"
      :class="['form-control', textareaClass]"
      :value="modelValue"
      @input="handleInput"
      :rows="rows"
      :maxlength="maxlength"
      :placeholder="placeholder"
    />
    <div v-if="error" class="invalid-feedback d-block">{{ error }}</div>
  </div>
</template>

<script setup>
import { ref, defineExpose } from 'vue'
const props = defineProps({
  modelValue: { type: [String, Number], default: '' },
  placeholder: { type: String, default: '' },
  id: { type: String, default: '' },
  textareaClass: { type: String, default: '' },
  rows: { type: [String, Number], default: 3 },
  maxlength: { type: [String, Number], default: null },
  error: { type: String, default: '' },
})
const emit = defineEmits(['update:modelValue'])
const textareaRef = ref(null)
defineExpose({ textareaRef })

const handleInput = (event) => {
  emit('update:modelValue', event.target.value)
}
</script>

<style>
/* global Bootstrap styles are expected */
</style>
