<template>
    <ContentLoader v-if="loading" />
    <section v-else class="section doctor-single shadow mb-4" style="padding: 0 !important;">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="doctor-details mt-4 mt-lg-0">
                            <h2 class="text-md">{{ klienkamiData.page_title }}</h2>
                        <div class="divider my-4"></div>
                            <div class="row justify-content-center mb-4">
                                <div class="col-md-6">
                                    <form @submit.prevent="searchKlienkami">
                                        <div class="input-group mb-3">
                                            <input
                                                type="text"
                                                class="form-control"
                                                placeholder="Masukan kata kunci..."
                                                name="search"
                                                v-model="searchKeyword"
                                            >
                                            <div class="input-group-append">
                                            <button class="btn btn-primary" style="background-color: #223a66 !important;" type="submit" id="button-addon2">Cari</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="container">
                                <div class="row align-items-center">
                                    <template v-if="klienkamiData?.klienkami?.data?.length > 0">
                                        <div class="col-lg-12 testimonial-wrap-2">
                                            <template v-for="(item, index) in klienkamiData.klienkami.data" :key="index">
                                                <div class="testimonial-block style-2  gray-bg shadow">
                                                    <i class="icofont-quote-right"></i>
                                                    <div class="testimonial-thumb">
                                                        <img :src="'/storage/'+ item.client_image" :alt="item.client_name"  class="img-fluid" id="prevImg" style="cursor: zoom-in;" onclick="zoomImg()">
                                                    </div>

                                                    <div class="client-info ">
                                                        <h4>{{ item.client_name }}</h4>
                                                        <span v-html="item.company_name"></span>
                                                        <p v-html="item.client_address"></p>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                        <div class="d-flex justify-content-center">
                                            <nav aria-label="Page navigation">
                                                <ul class="pagination">
                                                    <li
                                                        v-for="(link, index) in paginationLinks"
                                                        :key="index"
                                                        class="page-item"
                                                        :class="{ 'active': link.active, 'disabled': !link.url }"
                                                    >
                                                        <a
                                                        class="page-link"
                                                        href="#"
                                                        @click.prevent="changePage(link.url)"
                                                        v-html="link.label"
                                                        ></a>
                                                    </li>
                                                </ul>
                                            </nav>
                                        </div>
                                    </template>
                                    <template v-else>
                                        <span>Produk tidak ditemukan.</span>
                                    </template>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
    import { onMounted, ref } from 'vue';
    import { ContentLoader } from 'vue-content-loader';
    import Swal from 'sweetalert2/dist/sweetalert2';
    import apiClient from '@/services/api';

    const klienkamiData = ref(null)
    const paginationLinks = ref([]);
    const searchKeyword = ref('');

    const loading = ref(true)
    const errorMessage = ref('')

    const fetchKlienkami = async (page = 1) => {
        try {
            const response = await apiClient.get('/getklienkami', {
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
