<template>
  <div>
    <select
      :id="id"
      :class="['form-control', inputClass]"
      :value="modelValue"
      @change="handleChange"
    >
      <option value="" disabled hidden>{{ placeholder }}</option>
      <option
        v-for="option in options"
        :key="optionKey(option)"
        :value="optionValue(option)"
      >
        {{ optionLabel(option) }}
      </option>
    </select>
    <div v-if="error" class="invalid-feedback d-block">{{ error }}</div>
  </div>
</template>

<script setup>
const props = defineProps({
  modelValue: { type: [String, Number], default: '' },
  options: { type: Array, default: () => [] },
  placeholder: { type: String, default: 'Pilih...' },
  id: { type: String, default: '' },
  inputClass: { type: String, default: '' },
  error: { type: String, default: '' },
  valueKey: { type: String, default: 'value' },
  labelKey: { type: String, default: 'label' }
})

const emit = defineEmits(['update:modelValue'])

const optionValue = (option) => (typeof option === 'object' ? option[props.valueKey] : option)
const optionLabel = (option) => (typeof option === 'object' ? option[props.labelKey] : option)
const optionKey = (option) => (typeof option === 'object' ? option[props.valueKey] : option)

const handleChange = (event) => {
  emit('update:modelValue', event.target.value)
}
</script>

<style>
/* Global Bootstrap styles are expected */
</style>
