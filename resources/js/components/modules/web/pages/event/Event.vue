<template>
    <ContentLoader v-if="loading" />
    <section v-else class="section doctor-single shadow mb-4" style="padding: 0 !important;">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="doctor-details mt-4 mt-lg-0">
                        <h2 class="text-md">{{ eventsData.page_title }}</h2>
                        <div class="divider my-4"></div>
                        <div class="row justify-content-center mb-4">
                            <div class="col-md-6">
                                <form @submit.prevent="searchEvent">
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
                        <section class="section service-2" style="padding: 0 !important;">
                            <div class="container">
                                <template v-if="eventsData?.events?.data?.length > 0">
                                    <div class="row">
                                        <template v-for="(item, index) in eventsData.events.data" :key="index">
                                            <div class="col-lg-4 col-md-6 col-sm-6">
                                                <div class="service-block">
                                                    <img :src="'storage/'+ item.event_image" :alt="item.event_title" class="img-fluid">
                                                    <div class="content">
                                                        <h4 class="mt-4 mb-2 title-color"><RouterLink :to="'/event/'+ item.id">{{ item.event_title }}</RouterLink></h4>
                                                        <small class=""> <strong> <i class="icofont-book-mark mr-2"></i>Dilaksanakan: {{ item.event_date}}</strong></small>
                                                        <p class="mt-2" v-html="limitText(item.event_description)"></p>
                                                        <small><RouterLink :to="'/event/'+ item.id">Selengkapnya</RouterLink></small>
                                                        <small class="float-right mb-4"><strong> <i class="icofont-calendar mr-2"></i> {{ $diffForHumans(item.created_at) }}</strong></small>
                                                    </div>
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
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 col-sm-12 text-center p-3">
                                            Produk tidak ditemukan.
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </section>
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
    import { limitText } from '@/utils/substr'

    const eventsData = ref(null)
    const paginationLinks = ref([]);
    const searchKeyword = ref('');

    const loading = ref(true)
    const errorMessage = ref('')

    const fetchEvent = async (page = 1) => {
        try {
            const response = await apiClient.get('/getevent', {
                params: {
                    search: searchKeyword.value,
                    page: page
                }
            })
            eventsData.value = response.data
            paginationLinks.value = eventsData.value.events.links;

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

    const searchEvent = () => {
        loading.value = true;
        fetchEvent(1);
    }

    const changePage = (url) => {
        if (!url) return;

        const urlParams = new URLSearchParams(new URL(url).search);
        const pageNumber = urlParams.get('page');

        loading.value = true;
        fetchEvent(pageNumber);
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

        fetchEvent();
    });
</script>
