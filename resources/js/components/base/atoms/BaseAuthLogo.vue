<template>
  <div class="d-flex justify-content-center">
    <img
      :src="resolvedSrc"
      alt=""
      class="brand-image img-circle elevation-3 d-block mb-2"
      style="opacity: 0.8"
      height="80"
      width="80"
    />
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  src: {
    type: String,
    default: ''
  }
})

const resolvedSrc = computed(() => {
  if (!props.src) {
    return ''
  }

  if (props.src.startsWith('http')) {
    return props.src
  }

  return `/storage/${props.src.replace(/^\/+/, '')}`
})
</script>
