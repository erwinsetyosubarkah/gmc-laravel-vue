<template>
  <div class="col-lg-4 col-md-6">
    <div class="doctor-img-block">
      <img :src="imageUrl" :alt="clubName" class="img-fluid w-50">
      <div class="info-block mt-4">
        <h4 class="mb-0">{{ leaderName }}</h4>
        <p>{{ leaderEmail }}</p>
        <ul class="list-inline mt-4 doctor-social-links">
          <slot />
        </ul>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  imageUrl: {
    type: String,
    default: ''
  },
  clubName: {
    type: String,
    default: ''
  },
  leaderName: {
    type: String,
    default: ''
  },
  leaderEmail: {
    type: String,
    default: ''
  }
})
</script>
