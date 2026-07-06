<template>
    <ContentLoader v-if="loading" />
    <section v-else class="section doctor-single shadow mb-4" style="padding: 0 !important;">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="doctor-details mt-4 mt-lg-0">
                        <h2 class="text-md">{{ eventData.page_title }}</h2>
                        <div class="divider my-4"></div>
                        <section class="section blog-wrap" style="padding:0 !important;">
                            <div class="container">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="row">
                                            <div class="col-lg-12 mb-1">
                                                <div class="single-blog-item">
                                                    <div class="row d-flex justify-content-center">
                                                        <div class="col-lg-6">
                                                            <img :src="'/storage/'+ eventData.event.event_image" :alt="eventData.event.event_title" class="img-fluid shadow">
                                                        </div>
                                                    </div>

                                                    <div class="blog-item-content mt-5 pl-4 pr-4">
                                                        <div class="blog-item-meta mb-3">
                                                            <span class="text-color-2 text-capitalize mr-3"><i class="icofont-book-mark mr-2"></i> Dilaksanakan: {{ eventData.event.event_date}}</span>
                                                            <span class="text-black text-capitalize mr-3 float-right"><i class="icofont-calendar mr-2"></i>  {{ $diffForHumans(eventData.event.created_at) }}</span>
                                                        </div>
                                                        <h2 class="mb-4 text-md">{{ eventData.event.event_title }}</h2>

                                                        <p v-html="eventData.event.event_description" class="text-justify"></p>

                                                        <div class="mt-5">
                                                            <ul class="float-right list-inline">
                                                                <li class="list-inline-item"> Share: </li>
                                                                <li class="list-inline-item"><a href="#" target="_blank"><i class="icofont-facebook" aria-hidden="true"></i></a></li>
                                                                <li class="list-inline-item"><a href="#" target="_blank"><i class="icofont-twitter" aria-hidden="true"></i></a></li>
                                                                <li class="list-inline-item"><a href="#" target="_blank"><i class="icofont-pinterest" aria-hidden="true"></i></a></li>
                                                                <li class="list-inline-item"><a href="#" target="_blank"><i class="icofont-linkedin" aria-hidden="true"></i></a></li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
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
    import { useRoute } from 'vue-router'
    import { ContentLoader } from 'vue-content-loader';
    import Swal from 'sweetalert2/dist/sweetalert2';
    import apiClient from '@/services/api';

    const eventData = ref(null)
    const route = useRoute()

    const eventId = route.params.id
    const loading = ref(true)
    const errorMessage = ref('')


    const fetchSingleEvent = async () => {
        try {
            const response = await apiClient.get('/getevent/'+eventId)
            eventData.value = response.data

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
        fetchSingleEvent()
    })
</script>
