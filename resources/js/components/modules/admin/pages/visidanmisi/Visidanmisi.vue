<template>
  <VisidanmisiPageShell>
    <ContentLoader v-if="loading" speed="0.5" />
    <VisidanmisiForm
      v-else
      :initialValues="formValues"
      :isSubmitting="isSubmitting"
      @submit="handleSubmit"
    />
  </VisidanmisiPageShell>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { ContentLoader } from 'vue-content-loader'
import apiClient from '@/services/api'
import Swal from 'sweetalert2/dist/sweetalert2'
import VisidanmisiPageShell from '../../../../base/organisms/VisidanmisiPageShell.vue'
import VisidanmisiForm from '../../../../base/molecules/VisidanmisiForm.vue'

const router = useRouter()
const loading = ref(false)
const isSubmitting = ref(false)
const formValues = ref({ title: '', content: '' })

const fetchVisiMisi = async () => {
  loading.value = true
  try {
    const response = await apiClient.get('/web/getvisidanmisi')
    const data = response?.data?.visidanmisi || response?.data || {}
    formValues.value = {
      title: data.title || '',
      content: data.content || '',
    }
  } catch (error) {
    await Swal.fire({
      title: 'Gagal!',
      text: 'Gagal mengambil data visi dan misi.',
      icon: 'error',
    })
  } finally {
    loading.value = false
  }
}

const handleSubmit = async (values) => {
  isSubmitting.value = true
  try {
    const response = await apiClient.post('/admin/visidanmisi', values)
    if (response?.data?.status === 'success') {
      await Swal.fire({
        title: 'Berhasil!',
        text: response.data.message || 'Data visi dan misi berhasil disimpan.',
        icon: 'success',
      })
      router.go(0)
    } else {
      throw new Error(response?.data?.message || 'Gagal menyimpan data.')
    }
  } catch (error) {
    await Swal.fire({
      title: 'Gagal!',
      text: error?.response?.data?.message || error.message || 'Terjadi kesalahan saat menyimpan data.',
      icon: 'error',
    })
  } finally {
    isSubmitting.value = false
  }
}

onMounted(fetchVisiMisi)
</script>

<style>
/* global Bootstrap styling */
</style>
