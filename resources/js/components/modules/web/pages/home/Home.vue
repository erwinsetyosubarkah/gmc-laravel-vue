<template>
    <ContentLoader v-if="loading" />
    <template v-else>
        <CustBanner :backgroundImage="baseUrl+'/storage/banner.png'" :description="home?.profile?.short_description" />
        <CustCarousel v-if="carouselData" :dataImage="carouselData" />
        <section class="newest-articles mt-5">
            <div class="container">
                <h4 class="text-capitalize">Artikel Terbaru</h4>
                <div class="divider mb-4"></div>
                <CustListCard :listDataCard="listDataCard"/>
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
    import CustListCard from '@/components/base/templates/CustListCard.vue';

    const baseUrl = window.location.origin
    const store = useStore()

    const home = ref(null)
    const carouselData = ref(null)
    const listDataCard = ref(null)
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

            const articlesBaru = home.value.articles.map(({ image_title, post_image, id, category, ...sisaKey }) => ({
                title: image_title,
                img_url: baseUrl + '/storage/' + post_image,
                action_url: baseUrl + '/article/' + id,
                category_name: category.category_name,
                ...sisaKey
            }));
            console.log(articlesBaru);
            listDataCard.value = articlesBaru;

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
