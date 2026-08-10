<template>
    <ContentLoader v-if="loading" />
    <WebEventSinglePageShell v-else>
        <template #title>
            <BaseWebPageTitle :title="eventData.page_title" />
        </template>
        <WebEventSingleContent
            :imageUrl="'/storage/' + eventData.event.event_image"
            :title="eventData.event.event_title"
            :date="eventData.event.event_date"
            :createdAt="$diffForHumans(eventData.event.created_at)"
            :description="eventData.event.event_description"
        />
    </WebEventSinglePageShell>
</template>

<script setup>
    import { onMounted, ref } from 'vue';
    import { useRoute } from 'vue-router'
    import { ContentLoader } from 'vue-content-loader';
    import Swal from 'sweetalert2/dist/sweetalert2';
    import apiClient from '@/services/api';
    import WebEventSinglePageShell from '../../../../base/organisms/WebEventSinglePageShell.vue'
    import WebEventSingleContent from '../../../../base/molecules/WebEventSingleContent.vue'
    import BaseWebPageTitle from '../../../../base/atoms/BaseWebPageTitle.vue'

    const eventData = ref(null)
    const route = useRoute()

    const eventId = route.params.id
    const loading = ref(true)
    const errorMessage = ref('')


    const fetchSingleEvent = async () => {
        try {
            const response = await apiClient.get('/web/getevent/'+eventId)
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
