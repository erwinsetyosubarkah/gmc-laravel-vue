<template>
  <ContentLoader v-if="loading" />
  <ArticleSinglePageShell v-else :page-title="articleData?.page_title || 'Article'">
    <ArticleSingleContent :article="articleData?.artikel" />
  </ArticleSinglePageShell>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import { ContentLoader } from 'vue-content-loader'
import Swal from 'sweetalert2/dist/sweetalert2'
import apiClient from '@/services/api'

import ArticleSinglePageShell from '../../../../base/organisms/ArticleSinglePageShell.vue'
import ArticleSingleContent from '../../../../base/molecules/ArticleSingleContent.vue'

const articleData = ref(null)
const route = useRoute()

const articleId = route.params.id
const loading = ref(true)
const errorMessage = ref('')

const fetchSingleArticle = async () => {
  try {
    const response = await apiClient.get(`/web/getartikel/${articleId}`)
    articleData.value = response.data
  } catch (error) {
    errorMessage.value = 'Gagal mengambil data dari server.'
    Swal.fire({
      title: 'Gagal!',
      text: errorMessage.value,
      icon: 'error'
    })
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchSingleArticle()
})
</script>
