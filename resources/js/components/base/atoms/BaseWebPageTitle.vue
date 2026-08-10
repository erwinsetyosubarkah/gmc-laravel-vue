<template>
  <div>
    <h2 class="text-md">{{ title }}</h2>
    <div class="divider my-4"></div>
  </div>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    default: ''
  }
})
</script>
