<template>
  <div class="input-group mb-3">
    <input
      type="text"
      class="form-control"
      :placeholder="placeholder"
      :name="name"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
    >
    <div class="input-group-append">
      <button class="btn btn-primary" type="button" @click="$emit('search')" style="background-color: #223a66 !important;">
        {{ buttonText }}
      </button>
    </div>
  </div>
</template>

<script setup>
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
