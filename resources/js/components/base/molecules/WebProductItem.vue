<template>
  <div class="col-lg-6 col-md-6 col-sm-6">
    <div class="service-block">
      <img :src="imageUrl" :alt="title" class="img-fluid" style="width: 300px;height: 200px;object-fit: cover;object-position: center;">
      <div class="content">
        <h4 class="mt-4 mb-2 title-color">
          <RouterLink :to="detailUrl">{{ title }}</RouterLink>
        </h4>
        <small><strong>Stock : {{ stock }}</strong></small>
        <small class="float-right"><strong>Harga : {{ price }}</strong></small>
        <p class="mb-4 mt-2" v-html="description"></p>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    default: ''
  },
  imageUrl: {
    type: String,
    default: ''
  },
  detailUrl: {
    type: String,
    default: '#'
  },
  stock: {
    type: [String, Number],
    default: ''
  },
  price: {
    type: String,
    default: ''
  },
  description: {
    type: String,
    default: ''
  }
})
</script>
