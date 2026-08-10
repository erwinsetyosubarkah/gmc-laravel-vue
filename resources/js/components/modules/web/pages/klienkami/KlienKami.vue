<template>
    <ContentLoader v-if="loading" />
    <WebEventPageShell v-else>
        <template #header>
            <BaseWebPageTitle :title="klienkamiData.page_title" />
        </template>
        <template #search>
            <div class="row justify-content-center mb-4">
                <div class="col-md-6">
                    <WebSearchForm v-model="searchKeyword" @search="searchKlienkami" />
                </div>
            </div>
        </template>
        <div class="container">
            <div class="row align-items-center">
                <template v-if="klienkamiData?.klienkami?.data?.length > 0">
                    <div class="col-lg-12 testimonial-wrap-2">
                        <template v-for="(item, index) in klienkamiData.klienkami.data" :key="index">
                            <WebClientItem
                                :imageUrl="'/storage/' + item.client_image"
                                :name="item.client_name"
                                :company="item.company_name"
                                :address="item.client_address"
                            />
                        </template>
                    </div>
                    <div class="d-flex justify-content-center">
                        <WebPagination :links="paginationLinks" @change-page="changePage" />
                    </div>
                </template>
                <template v-else>
                    <BaseWebEmptyState message="Klien tidak ditemukan." />
                </template>
            </div>
        </div>
    </WebEventPageShell>
</template>

<script setup>
    import { onMounted, ref } from 'vue';
    import { ContentLoader } from 'vue-content-loader';
    import Swal from 'sweetalert2/dist/sweetalert2';
    import apiClient from '@/services/api';
    import WebEventPageShell from '../../../../base/organisms/WebEventPageShell.vue'
    import WebClientItem from '../../../../base/molecules/WebClientItem.vue'
    import WebSearchForm from '../../../../base/molecules/WebSearchForm.vue'
    import WebPagination from '../../../../base/molecules/WebPagination.vue'
    import BaseWebPageTitle from '../../../../base/atoms/BaseWebPageTitle.vue'
    import BaseWebEmptyState from '../../../../base/atoms/BaseWebEmptyState.vue'

    const klienkamiData = ref(null)
    const paginationLinks = ref([]);
    const searchKeyword = ref('');

    const loading = ref(true)
    const errorMessage = ref('')

    const fetchKlienkami = async (page = 1) => {
        try {
            const response = await apiClient.get('/web/getklienkami', {
                params: {
                    search: searchKeyword.value,
                    page: page
                }
            })
            klienkamiData.value = response.data
            paginationLinks.value = klienkamiData.value.klienkami.links;

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

    const searchKlienkami = () => {
        loading.value = true;
        fetchKlienkami(1);
    }

    const changePage = (url) => {
        if (!url) return;

        const urlParams = new URLSearchParams(new URL(url).search);
        const pageNumber = urlParams.get('page');

        loading.value = true;
        fetchKlienkami(pageNumber);
    };

    function zoomImg(src){
        previmg(src);
    }

    function previmg(src){
        var modal;

    function removeModal() {
            modal.remove();
            $('body').off('keyup.modal-close');
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
            removeModal();
        }).appendTo('body');
        //handling ESC
        $('body').on('keyup.modal-close', function(e) {
            if (e.key === 'Escape') {
            removeModal();
            }
        });
    }

    onMounted(() => {
        const urlParams = new URLSearchParams(window.location.search);
        searchKeyword.value = urlParams.get('search') || '';

        fetchKlienkami();
    });
</script>
