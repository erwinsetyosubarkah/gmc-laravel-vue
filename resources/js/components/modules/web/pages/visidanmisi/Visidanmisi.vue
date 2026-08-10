<template>
    <ContentLoader v-if="loading"/>
    <WebVisiDanMisiPageShell v-else>
        <template #visi-content>
            <WebProfileSummary
                :imageUrl="'/storage/' + profileData.club_logo"
                :clubName="profileData.club_name"
                :leaderName="profileData.leader_name"
                :leaderEmail="profileData.leader_email"
            >
                <li class="list-inline-item"><a href="#"><i class="icofont-facebook"></i></a></li>
                <li class="list-inline-item"><a href="#"><i class="icofont-twitter"></i></a></li>
                <li class="list-inline-item"><a href="#"><i class="icofont-skype"></i></a></li>
                <li class="list-inline-item"><a href="#"><i class="icofont-linkedin"></i></a></li>
                <li class="list-inline-item"><a href="#"><i class="icofont-pinterest"></i></a></li>
            </WebProfileSummary>

            <div class="col-lg-8 col-md-6">
                <div class="doctor-details mt-4 mt-lg-0">
                    <BaseWebPageTitle :title="visidanmisiData.page_title" />
                    <p v-html="visidanmisiData.visidanmisi.content"></p>
                </div>
            </div>
        </template>
    </WebVisiDanMisiPageShell>
</template>

<script setup>
    import { onMounted, ref, computed } from 'vue';
    import { useRoute } from 'vue-router'
    import { ContentLoader } from 'vue-content-loader';
    import Swal from 'sweetalert2/dist/sweetalert2';
    import apiClient from '@/services/api';
    import { useStore } from 'vuex'
    import WebProfileSummary from '../../../../base/molecules/WebProfileSummary.vue'
    import WebVisiDanMisiPageShell from '../../../../base/organisms/WebVisiDanMisiPageShell.vue'
    import BaseWebPageTitle from '../../../../base/atoms/BaseWebPageTitle.vue'

    const store = useStore()
    const profileData = computed(() => store.state.profile)

    const visidanmisiData = ref(null)

    const loading = ref(true)
    const errorMessage = ref('')


    const fetchVisidanmisi = async () => {
        try {
            const response = await apiClient.get('/web/getvisidanmisi')
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
