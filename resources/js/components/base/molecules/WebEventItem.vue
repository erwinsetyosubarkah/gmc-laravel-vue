<template>
  <div class="col-lg-4 col-md-6 col-sm-6">
    <div class="service-block">
      <img :src="imageUrl" :alt="title" class="img-fluid">
      <div class="content">
        <h4 class="mt-4 mb-2 title-color">
          <RouterLink :to="detailUrl">{{ title }}</RouterLink>
        </h4>
        <small>
          <strong>
            <i class="icofont-book-mark mr-2"></i>Dilaksanakan: {{ eventDate }}
          </strong>
        </small>
        <p class="mt-2" v-html="description"></p>
        <small><RouterLink :to="detailUrl">Selengkapnya</RouterLink></small>
        <small class="float-right mb-4">
          <strong>
            <i class="icofont-calendar mr-2"></i> {{ createdAt }}
          </strong>
        </small>
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
  title: {
    type: String,
    default: ''
  },
  detailUrl: {
    type: String,
    default: '#'
  },
  eventDate: {
    type: String,
    default: ''
  },
  description: {
    type: String,
    default: ''
  },
  createdAt: {
    type: String,
    default: ''
  }
})
</script>
