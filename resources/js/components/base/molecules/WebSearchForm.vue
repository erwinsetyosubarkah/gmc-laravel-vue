<template>
  <form @submit.prevent="$emit('search')">
    <BaseWebSearchInput
      :model-value="modelValue"
      :placeholder="placeholder"
      :name="name"
      :button-text="buttonText"
      @update:modelValue="$emit('update:modelValue', $event)"
      @search="$emit('search')"
    />
  </form>
</template>

<script setup>
import BaseWebSearchInput from '../atoms/BaseWebSearchInput.vue'

defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: 'Masukan kata kunci...'
  },
  name: {
    type: String,
    default: 'search'
  },
  buttonText: {
    type: String,
    default: 'Cari'
  }
})

defineEmits(['update:modelValue', 'search'])
</script>
