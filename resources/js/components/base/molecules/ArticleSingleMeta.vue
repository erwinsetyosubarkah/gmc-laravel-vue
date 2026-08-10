<template>
  <div class="blog-item-meta mb-3">
    <span class="text-color-2 text-capitalize mr-3">
      <i class="icofont-book-mark mr-2"></i>
      {{ categoryName }}
    </span>
    <span class="text-black text-capitalize mr-3 float-right">
      <i class="icofont-calendar mr-2"></i>
      {{ createdAtHuman }}
    </span>
  </div>
</template>

<script setup>
const props = defineProps({
  categoryName: {
    type: String,
    default: ''
  },
  createdAtHuman: {
    type: String,
    default: ''
  }
})
</script>
