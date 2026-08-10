<template>
    <ContentLoader v-if="loading" />
    <WebProductPageShell v-else>
        <template #header>
            <BaseWebPageTitle :title="produkkamiData.page_title" />
        </template>
        <template #search>
            <div class="row justify-content-center mb-4">
                <div class="col-md-6">
                    <WebSearchForm v-model="searchKeyword" @search="searchProducts" />
                </div>
            </div>
        </template>
        <template v-if="produkkamiData?.produkkami?.data?.length > 0">
            <div class="row">
                <template v-for="(item, index) in produkkamiData.produkkami.data" :key="index">
                    <WebProductItem
                        :imageUrl="'/storage/' + item.product_image"
                        :title="item.product_name"
                        :detailUrl="'/web/produkkami/' + item.id"
                        :stock="numberFormat(item.stock)"
                        :price="formatRupiah(item.price)"
                        :description="limitText(item.product_description)"
                    />
                </template>
            </div>
            <div class="d-flex justify-content-center mt-4">
                <WebPagination :links="paginationLinks" @change-page="changePage" />
            </div>
        </template>
        <template v-else>
            <BaseWebEmptyState message="Produk tidak ditemukan." />
        </template>
    </WebProductPageShell>
</template>

<script setup>
    import { onMounted, ref } from 'vue';
    import { ContentLoader } from 'vue-content-loader';
    import Swal from 'sweetalert2/dist/sweetalert2';
    import apiClient from '@/services/api';
    import { formatRupiah, numberFormat } from '@/utils/format'
    import { limitText } from '@/utils/substr'
    import WebProductPageShell from '../../../../base/organisms/WebProductPageShell.vue'
    import WebProductItem from '../../../../base/molecules/WebProductItem.vue'
    import WebSearchForm from '../../../../base/molecules/WebSearchForm.vue'
    import WebPagination from '../../../../base/molecules/WebPagination.vue'
    import BaseWebPageTitle from '../../../../base/atoms/BaseWebPageTitle.vue'
    import BaseWebEmptyState from '../../../../base/atoms/BaseWebEmptyState.vue'

    const produkkamiData = ref(null)
    const paginationLinks = ref([]);
    const searchKeyword = ref('');

    const loading = ref(true)
    const errorMessage = ref('')

    const fetchProdukKami = async (page = 1) => {
        try {
            const response = await apiClient.get('/web/getprodukkami', {
                params: {
                    search: searchKeyword.value,
                    page: page
                }
            })
            produkkamiData.value = response.data
            paginationLinks.value = produkkamiData.value.produkkami.links;

        } catch (error) {
            if (error.response && error.response.status === 422) {
                errorMessage.value = error.response.data.message || 'Validasi Gagal.';
            } else {
                errorMessage.value = 'Gagal mengambil data dari server.'
            }

            Swal.fire({
                title: 'Gagal!',
                html: errorMessage.value,
                icon: 'error'
            });
        } finally {
            loading.value = false
        }
    }

    const searchProducts = () => {
        loading.value = true;
        fetchProdukKami(1);
    }

    const changePage = (url) => {
        if (!url) return;

        const urlParams = new URLSearchParams(new URL(url).search);
        const pageNumber = urlParams.get('page');

        loading.value = true;
        fetchProdukKami(pageNumber);
    };

    onMounted(() => {
        const urlParams = new URLSearchParams(window.location.search);
        searchKeyword.value = urlParams.get('search') || '';

        fetchProdukKami();
    });
</script>
