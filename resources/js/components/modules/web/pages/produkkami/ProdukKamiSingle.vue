<template>
    <ContentLoader v-if="loading"/>
    <WebEventSinglePageShell v-else>
        <template #title>
            <BaseWebPageTitle :title="produkkamiData.page_title" />
        </template>
        <WebProductSingleContent
            :imageUrl="'/storage/' + produkkamiData.produkkami.product_image"
            :title="produkkamiData.produkkami.product_name"
            :price="numberFormat(produkkamiData.produkkami.price)"
            :createdAt="$diffForHumans(produkkamiData.produkkami.created_at)"
            :description="produkkamiData.produkkami.product_description"
        />
    </WebEventSinglePageShell>
</template>

<script setup>
    import { onMounted, ref } from 'vue';
    import { useRoute } from 'vue-router'
    import { ContentLoader } from 'vue-content-loader';
    import Swal from 'sweetalert2/dist/sweetalert2';
    import apiClient from '@/services/api';
     import { formatRupiah, numberFormat } from '@/utils/format'
    import WebEventSinglePageShell from '../../../../base/organisms/WebEventSinglePageShell.vue'
    import WebProductSingleContent from '../../../../base/molecules/WebProductSingleContent.vue'
    import BaseWebPageTitle from '../../../../base/atoms/BaseWebPageTitle.vue'

    const produkkamiData = ref(null)
    const route = useRoute()

    const produkkamiId = route.params.id
    const loading = ref(true)
    const errorMessage = ref('')


    const fetchSingleProdukkami = async () => {
        try {
            const response = await apiClient.get('/web/getprodukkami/'+produkkamiId)
            produkkamiData.value = response.data

        } catch (error) {
            errorMessage.value = 'Gagal mengambil data dari server.'
            Swal.fire({
                title: 'Gagal!',
                text: errorMessage.value,
                icon: 'error'
            });
        } finally {
            loading.value = false
        }
    }


    onMounted(() => {
        fetchSingleProdukkami()
    })
</script>
