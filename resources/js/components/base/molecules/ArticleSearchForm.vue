<template>
  <form @submit.prevent="$emit('search')">
    <div class="input-group mb-3">
      <input
        type="text"
        class="form-control"
        placeholder="Masukan kata kunci..."
        name="search"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
      >
      <div class="input-group-append">
        <button class="btn btn-primary" style="background-color: #223a66 !important;" type="submit" id="button-addon2">
          Cari
        </button>
      </div>
    </div>
  </form>
</template>

<script setup>
const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  }
})

const emit = defineEmits(['update:modelValue', 'search'])
</script>
