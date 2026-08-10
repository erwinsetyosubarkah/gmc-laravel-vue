<template>
  <div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 text-center p-3">
      {{ message }}
    </div>
  </div>
</template>

<script setup>
defineProps({
  message: {
    type: String,
    default: 'Data tidak ditemukan.'
  }
})
</script>
