<template>
  <img :src="resolvedSrc" :alt="alt" class="img-fluid" />
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  src: {
    type: String,
    default: ''
  },
  alt: {
    type: String,
    default: 'Article image'
  }
})

const resolvedSrc = computed(() => {
  if (!props.src) return '/storage/no-image.png'
  return props.src.startsWith('http') ? props.src : `/storage/${props.src.replace(/^\/+/, '')}`
})
</script>
