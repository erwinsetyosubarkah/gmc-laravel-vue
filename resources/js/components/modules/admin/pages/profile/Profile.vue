<template>
  <ProfilePageShell>
    <ProfileForm
      :initialValues="profileValues"
      :isSubmitting="isSubmitting"
      :previewImage="previewImage"
      @submit="handleSubmit"
      @change:file="handleFileChange"
    />
  </ProfilePageShell>
</template>

<script setup>
import { computed, ref, watchEffect } from 'vue'
import { useStore } from 'vuex'
import Swal from 'sweetalert2/dist/sweetalert2'
import apiClient from '@/services/api'
import { useRouter } from 'vue-router'
import ProfilePageShell from '../../../../base/organisms/ProfilePageShell.vue'
import ProfileForm from '../../../../base/molecules/ProfileForm.vue'

const router = useRouter()
const store = useStore()
const profileData = computed(() => store.state.profile || {})

const selectedFile = ref(null)
const previewImage = ref('')
const isSubmitting = ref(false)

watchEffect(() => {
  const data = profileData.value
  if (data && Object.keys(data).length && !selectedFile.value) {
    previewImage.value = data.club_logo ? `/storage/${data.club_logo}` : ''
  }
})

const profileValues = computed(() => ({
  club_name: profileData.value.club_name || '',
  club_name_abbreviation: profileData.value.club_name_abbreviation || '',
  email: profileData.value.email || '',
  leader_name: profileData.value.leader_name || '',
  leader_email: profileData.value.leader_email || '',
  phone: profileData.value.phone || '',
  address: profileData.value.address || '',
  short_description: profileData.value.short_description || '',
  description: profileData.value.description || '',
  old_club_logo: profileData.value.club_logo || '',
}))

const handleFileChange = (file) => {
  selectedFile.value = file
  previewImage.value = file
    ? URL.createObjectURL(file)
    : profileValues.value.old_club_logo
      ? `/storage/${profileValues.value.old_club_logo}`
      : ''
}

const handleSubmit = async (values) => {
  isSubmitting.value = true


  const formData = new FormData()
  formData.append('id', profileData.value.id)

  Object.entries(values).forEach(([key, value]) => {
    if (value !== null && value !== undefined && value !== '') {
      formData.append(key, value)
    }
  })

  if (selectedFile.value) {
    formData.append('club_logo', selectedFile.value)
  }

  try {
    const response = await apiClient.post('/admin/profile', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })

    if (response.status >= 200 && response.status < 300 && response.data.status === 'success') {
      await Swal.fire({
        title: response.data?.title,
        text: response.data?.message,
        icon: response.data?.status,
      })
      router.go(0)

    } else {
      throw new Error(response.data?.message || 'Proses simpan profil gagal.')
    }
  } catch (error) {
    const message = error.response?.data?.message || error.message || 'Terjadi kesalahan saat menyimpan profil.'
    await Swal.fire({ title: 'Gagal!', text: message, icon: 'error' })
  } finally {
    isSubmitting.value = false
  }
}
</script>

<style>
/* use global CSS as requested */
</style>
