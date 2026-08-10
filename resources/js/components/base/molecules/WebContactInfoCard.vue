<template>
  <div class="col-lg-4 col-sm-6 col-md-6 d-flex mb-4">
    <div class="contact-block w-100 mb-lg-0">
      <i :class="icon"></i>
      <h5>{{ title }}</h5>
      <p class="mb-0">{{ value }}</p>
    </div>
  </div>
</template>

<script setup>
defineProps({
  icon: {
    type: String,
    default: 'icofont-live-support'
  },
  title: {
    type: String,
    default: 'Hubungi Kami'
  },
  value: {
    type: String,
    default: ''
  }
})
</script>
