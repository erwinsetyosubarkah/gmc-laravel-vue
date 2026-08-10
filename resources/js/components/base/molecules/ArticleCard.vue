<template>
  <div class="col-lg-4 col-md-6 col-sm-6">
    <div class="service-block mb-5">
      <img :src="articleImage" :alt="article.title" class="img-fluid" onclick="zoomImg()">
      <div class="content">
        <h4 class="mt-4 mb-2 title-color">
          <RouterLink :to="`/web/article/${article.id}`">{{ article.title }}</RouterLink>
        </h4>
        <small class="">
          <strong>
            <i class="icofont-book-mark mr-2"></i>
            {{ article.category?.category_name }}
          </strong>
        </small>
        <small class="float-right">
          <strong>
            <i class="icofont-calendar mr-2"></i>
            {{ $diffForHumans(article.created_at) }}
          </strong>
        </small>
        <div class="mb-4 mt-2">
          <p v-html="article.excerpt"></p>
          <small>
            <RouterLink :to="`/web/article/${article.id}`">Selengkapnya</RouterLink>
          </small>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  article: {
    type: Object,
    default: () => ({})
  }
})

const articleImage = computed(() => {
  if (!props.article?.post_image) return '/storage/no-image.png'
  return `/storage/${props.article.post_image}`
})
</script>
