<template>
    <ContentLoader v-if="loading" />
    <template v-else>
        <CustBanner :backgroundImage="baseUrl+'/storage/banner.png'" :description="home?.profile?.short_description" />
        <CustCarousel v-if="carouselData" :dataImage="carouselData" />
        <section class="newest-articles mt-5">
            <div class="container">
                <h4 class="text-capitalize">Artikel Terbaru</h4>
                <div class="divider mb-4"></div>
                    <template v-if="home?.articles?.length">
                        <div class="row">
                            <template v-for="(article, index) in home.articles" :key="index">
                                <div class="col-lg-4 col-md-6 col-sm-6">
                                    <div class="service-block mb-5">
                                        <img :src="baseUrl + '/storage/' + article.post_image" :alt="article.title" class="img-fluid" @click="zoomImg">

                                        <div class="content">
                                            <h4 class="mt-4 mb-2 title-color"><a href="/artikel/{{ article.id }}">{{ article.title }}</a></h4>
                                            <small class=""> <strong> <i class="icofont-book-mark mr-2"></i>{{ article.category_name }}</strong></small>
                                            <small class="float-right"><strong> <i class="icofont-calendar mr-2"></i> {{ article.created_at_humans }}</strong></small>
                                            <p class="mb-4 mt-2">{{  article.excerpt }} <small><a href="/artikel/{{ article.id }}">Selengkapnya</a></small></p>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                    <div v-else class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 text-center p-3 mb-5">
                                Produk tidak ditemukan.
                        </div>
                    </div>
            </div>
        </section>
    </template>
</template>

<script setup>
    import { ref, onMounted } from 'vue'
    import { useStore } from 'vuex'
    import Swal from 'sweetalert2/dist/sweetalert2';
    import apiClient from '@/services/api'
    import { ContentLoader } from 'vue-content-loader';
    import CustBanner from '@/components/base/templates/CustBanner.vue'
    import CustCarousel from '@/components/base/templates/CustCarousel.vue'

    const baseUrl = window.location.origin
    const store = useStore()

    const home = ref(null)
    const carouselData = ref(null)
    const loading = ref(true)
    const errorMessage = ref('')

    const fetchHome = async () => {
        try {
            const response = await apiClient.get('/home')
            home.value = response.data
            const galeriesBaru = home.value.galeries.map(({ image_title, galery_image }) => ({
                title: image_title,
                url: baseUrl + '/storage/' + galery_image
            }));
            carouselData.value = galeriesBaru;
        } catch (error) {
            errorMessage.value = 'Gagal mengambil data dari server.'
            Swal.fire({
                title: 'Gagal!',
                text: errorMessage.value,
                icon: 'error',
                confirmButtonText: 'Keren'
            });
        } finally {
            loading.value = false
        }
    }


    onMounted(() => {
        fetchHome()
    })

</script>
