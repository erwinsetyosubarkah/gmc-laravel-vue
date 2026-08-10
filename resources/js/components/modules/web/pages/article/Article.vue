<template>
  <ContentLoader v-if="loading" />
  <template v-else>
    <ArticlePageShell :page-title="articlesData?.page_title || 'Article'">
      <template #search>
        <ArticleSearchForm v-model="searchKeyword" @search="searchArtikel" />
      </template>

      <template v-if="articlesData?.artikels?.data?.length > 0">
        <ArticleGrid :articles="articlesData.artikels.data" />

        <div class="d-flex justify-content-center">
          <ArticlePagination :links="paginationLinks" @change-page="changePage" />
        </div>
      </template>

      <template v-else>
        <ArticleEmptyState />
      </template>
    </ArticlePageShell>
  </template>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { ContentLoader } from 'vue-content-loader'
import Swal from 'sweetalert2/dist/sweetalert2'
import apiClient from '@/services/api'

import ArticlePageShell from '../../../../base/organisms/ArticlePageShell.vue'
import ArticleSearchForm from '../../../../base/molecules/ArticleSearchForm.vue'
import ArticleGrid from '../../../../base/organisms/ArticleGrid.vue'
import ArticleEmptyState from '../../../../base/molecules/ArticleEmptyState.vue'
import ArticlePagination from '../../../../base/organisms/ArticlePagination.vue'

const articlesData = ref(null)
const paginationLinks = ref([])
const searchKeyword = ref('')

const loading = ref(true)
const errorMessage = ref('')

const fetchArtikel = async (page = 1) => {
  try {
    const response = await apiClient.get('/web/getartikel', {
      params: {
        search: searchKeyword.value,
        page: page
      }
    })

    articlesData.value = response.data
    paginationLinks.value = articlesData.value?.artikels?.links || []
  } catch (error) {
    if (error.response && error.response.status === 422) {
      errorMessage.value = error.response.data.message || 'Validasi Gagal.'
    } else {
      errorMessage.value = 'Gagal mengambil data dari server.'
    }

    Swal.fire({
      title: 'Gagal!',
      html: errorMessage.value,
      icon: 'error'
    })
  } finally {
    loading.value = false
  }
}

const searchArtikel = () => {
  loading.value = true
  fetchArtikel(1)
}

const changePage = (url) => {
  if (!url) return

  const urlParams = new URLSearchParams(new URL(url).search)
  const pageNumber = urlParams.get('page')

  loading.value = true
  fetchArtikel(pageNumber)
}

function zoomImg(src) {
  previmg(src)
}

function previmg(src) {
  var modal

  function removeModal() {
    modal.remove()
    $('body').off('keyup.modal-close')
  }

  modal = $('<div>').css({
    background: 'RGBA(0,0,0,.5) url(' + src + ') no-repeat center',
    backgroundSize: 'contain',
    width: '100%',
    height: '100%',
    position: 'fixed',
    zIndex: '10000',
    top: '0',
    left: '0',
    cursor: 'zoom-out'
  }).click(function() {
    removeModal()
  }).appendTo('body')

  $('body').on('keyup.modal-close', function(e) {
    if (e.key === 'Escape') {
      removeModal()
    }
  })
}

onMounted(() => {
  const urlParams = new URLSearchParams(window.location.search)
  searchKeyword.value = urlParams.get('search') || ''

  fetchArtikel()
})
</script>
