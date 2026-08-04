<template>
  <div>
    <input
      :id="id"
      :type="type"
      :placeholder="placeholder"
      :class="['form-control', inputClass]"
      :value="modelValue"
      @input="handleInput"
    />
    <div v-if="error" class="invalid-feedback d-block">{{ error }}</div>
  </div>
</template>

<script setup>
const props = defineProps({
  modelValue: { type: [String, Number], default: '' },
  type: { type: String, default: 'text' },
  placeholder: { type: String, default: '' },
  id: { type: String, default: '' },
  inputClass: { type: String, default: '' },
  error: { type: String, default: '' }
})
const emit = defineEmits(['update:modelValue', 'input'])

const handleInput = (event) => {
  const value = event.target.value
  emit('update:modelValue', value)
  emit('input', event)
}
</script>

<style>
/* Global styles used */
</style>
