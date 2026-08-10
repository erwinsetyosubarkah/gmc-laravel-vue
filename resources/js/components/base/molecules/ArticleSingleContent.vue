<template>
  <div class="single-blog-item">
    <div class="row d-flex justify-content-center">
      <div class="col-lg-6">
        <BaseArticleImage :src="article?.post_image" :alt="article?.title || 'Article image'" class="img-fluid shadow" />
      </div>
    </div>

    <div class="blog-item-content mt-5 pl-4 pr-4">
      <ArticleSingleMeta
        :category-name="article?.category?.category_name || ''"
        :created-at-human="article?.created_at_humans || ''"
      />

      <h2 class="mb-4 text-md">
        <a href="">{{ article?.title || '' }}</a>
      </h2>

      <div v-html="article?.body || ''" class="text-justify"></div>

      <ArticleSingleShare />
    </div>
  </div>
</template>

<script setup>
import BaseArticleImage from '../atoms/BaseArticleImage.vue'
import ArticleSingleMeta from './ArticleSingleMeta.vue'
import ArticleSingleShare from './ArticleSingleShare.vue'

const props = defineProps({
  article: {
    type: Object,
    default: () => ({})
  }
})
</script>
