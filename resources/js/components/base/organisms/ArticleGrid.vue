<template>
  <div class="row">
    <ArticleCard v-for="(item, index) in articles" :key="index" :article="item" />
  </div>
</template>

<script setup>
import ArticleCard from '../molecules/ArticleCard.vue'

const props = defineProps({
  articles: {
    type: Array,
    default: () => []
  }
})
</script>
