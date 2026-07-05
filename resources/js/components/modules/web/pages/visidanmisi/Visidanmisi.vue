<template>
    <ContentLoader v-if="loading"/>
    <section v-else class="section doctor-single shadow" style="padding: 0 !important;">
        <div class="container mt-4 mb-4 pt-5 pb-5">
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="doctor-img-block">
                        <img :src="'/storage/'+ profileData.club_logo" :alt="profileData.club_name" class="img-fluid w-50">

                        <div class="info-block mt-4">
                            <h4 class="mb-0">{{ profileData.leader_name }}</h4>
                            <p>{{ profileData.leader_email }}</p>

                            <ul class="list-inline mt-4 doctor-social-links">
                                <li class="list-inline-item"><a href="#"><i class="icofont-facebook"></i></a></li>
                                <li class="list-inline-item"><a href="#"><i class="icofont-twitter"></i></a></li>
                                <li class="list-inline-item"><a href="#"><i class="icofont-skype"></i></a></li>
                                <li class="list-inline-item"><a href="#"><i class="icofont-linkedin"></i></a></li>
                                <li class="list-inline-item"><a href="#"><i class="icofont-pinterest"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8 col-md-6">
                    <div class="doctor-details mt-4 mt-lg-0">
                        <h2 class="text-md">{{ visidanmisiData.page_title }}</h2>
                        <div class="divider my-4"></div>
                        <p v-html="visidanmisiData.visidanmisi.content"></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
    import { onMounted, ref, computed } from 'vue';
    import { useRoute } from 'vue-router'
    import { ContentLoader } from 'vue-content-loader';
    import Swal from 'sweetalert2/dist/sweetalert2';
    import apiClient from '@/services/api';
    import { useStore } from 'vuex'

    const store = useStore()
    const profileData = computed(() => store.state.profile)

    const visidanmisiData = ref(null)

    const loading = ref(true)
    const errorMessage = ref('')


    const fetchVisidanmisi = async () => {
        try {
            const response = await apiClient.get('/getvisidanmisi')
            visidanmisiData.value = response.data

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
        fetchVisidanmisi()
    })
</script>
