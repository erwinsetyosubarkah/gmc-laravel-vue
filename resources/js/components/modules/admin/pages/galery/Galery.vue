<template>
  <div>
    <ContentLoader v-if="loading" speed="0.5" />
    <template v-else>
      <GaleryPageShell @create="openModal">
        <GaleryTable :rows="galleries" @edit="editGallery" @delete="deleteGallery" />
      </GaleryPageShell>

      <BaseModal :show="showModal" :title="formMode === 'create' ? 'Tambah Galeri' : 'Ubah Galeri'" @close="closeModal">
        <GaleryForm
          :initialValues="formValues"
          :previewImage="previewImage"
          :isSubmitting="isSubmitting"
          @submit="submitForm"
          @change:file="handleFileChange"
          @cancel="closeModal"
        />
      </BaseModal>
    </template>
  </div>
</template>

<script setup>
import { onMounted, onBeforeUnmount, ref, reactive, watch } from 'vue'
import { ContentLoader } from 'vue-content-loader'
import Swal from 'sweetalert2/dist/sweetalert2'
import apiClient from '@/services/api'
import BaseModal from '../../../../base/atoms/BaseModal.vue'
import GaleryPageShell from '../../../../base/organisms/GaleryPageShell.vue'
import GaleryTable from '../../../../base/organisms/GaleryTable.vue'
import GaleryForm from '../../../../base/molecules/GaleryForm.vue'

const props = defineProps({
  mygaleries: {
    type: Array,
    default: () => []
  }
})

const showModal = ref(false)
const formMode = ref('create')
const editId = ref(null)
const loading = ref(false)
const galleries = ref([])
const selectedFile = ref(null)
const previewImage = ref('')
const previewObjectUrl = ref('')
const isSubmitting = ref(false)

const formValues = reactive({
  image_title: ''
})

const setPreviewImage = (value, isBlob = false) => {
  if (previewObjectUrl.value) {
    URL.revokeObjectURL(previewObjectUrl.value)
    previewObjectUrl.value = ''
  }

  previewImage.value = value || ''
  if (isBlob && value) {
    previewObjectUrl.value = value
  }
}

const getGaleryImage = (image) => {
  if (!image) return 'https://via.placeholder.com/100x100?text=No+Image'
  return image.startsWith('http') ? image : `/storage/${image.replace(/^\/+/, '')}`
}

const openModal = () => {
  formMode.value = 'create'
  editId.value = null
  selectedFile.value = null
  Object.assign(formValues, { image_title: '' })
  setPreviewImage('')
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  selectedFile.value = null
  setPreviewImage('')
}

const fetchGalleries = async () => {
  loading.value = true
  try {
    const response = await apiClient.get('/admin/galery-all')
    const payload = response?.data || {}

    galleries.value = Array.isArray(payload?.data)
      ? payload.data
      : Array.isArray(payload)
      ? payload
      : []
  } catch (error) {
    console.error(error)
    Swal.fire({ icon: 'error', title: 'Gagal', text: 'Tidak dapat mengambil data galeri dari server.' })
  } finally {
    loading.value = false
  }
}

const handleFileChange = (file) => {
  selectedFile.value = file
  if (file) {
    setPreviewImage(URL.createObjectURL(file), true)
  } else {
    setPreviewImage('')
  }
}

const submitForm = async (values) => {
  if (formMode.value === 'create' && !selectedFile.value) {
    Swal.fire({ icon: 'warning', title: 'Foto wajib diisi.', text: 'Silakan pilih foto galeri sebelum menyimpan.' })
    return
  }

  isSubmitting.value = true

  const formData = new FormData()
  formData.append('image_title', values.image_title)
  if (selectedFile.value) {
    formData.append('galery_image', selectedFile.value)
  }

  try {
    if (formMode.value === 'create') {
      await apiClient.post('/admin/galery', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
    } else {
      await apiClient.post(`/admin/galery-edit/${editId.value}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
    }

    await fetchGalleries()
    closeModal()
    Swal.fire({
      icon: 'success',
      title: 'Berhasil',
      text: formMode.value === 'create' ? 'Data galeri berhasil ditambah.' : 'Data galeri berhasil diubah.'
    })
  } catch (error) {
    console.error(error)
    Swal.fire({
      icon: 'error',
      title: 'Gagal',
      text: error?.response?.data?.message || 'Proses simpan galeri gagal.'
    })
  } finally {
    isSubmitting.value = false
  }
}

const editGallery = (id) => {
  const item = galleries.value.find((gallery) => gallery.id == id)
  if (!item) return

  formMode.value = 'edit'
  editId.value = item.id
  selectedFile.value = null
  Object.assign(formValues, { image_title: item.image_title || '' })
  setPreviewImage(getGaleryImage(item.galery_image))
  showModal.value = true
}

const deleteGallery = async (id) => {
  const result = await Swal.fire({
    title: 'Yakin ingin menghapus?',
    text: 'Tindakan ini tidak bisa dibatalkan.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Ya, hapus',
    cancelButtonText: 'Batal'
  })

  if (!result.isConfirmed) return

  try {
    const response = await apiClient.delete(`/admin/galery/${id}`)
    const payload = response?.data || {}

    if (payload?.status === 'success') {
      await fetchGalleries()
      Swal.fire({ icon: 'success', title: 'Berhasil', text: payload.message || 'Data galeri berhasil dihapus.' })
    } else {
      Swal.fire({ icon: 'error', title: 'Gagal', text: payload?.message || 'Gagal menghapus galeri.' })
    }
  } catch (error) {
    console.error(error)
    Swal.fire({ icon: 'error', title: 'Gagal', text: error?.response?.data?.message || 'Gagal menghapus galeri.' })
  }
}

watch(
  () => props.mygaleries,
  (value) => {
    if (Array.isArray(value)) {
      galleries.value = value
    }
  },
  { immediate: true }
)

onMounted(async () => {
  await fetchGalleries()
})

onBeforeUnmount(() => {
  if (previewObjectUrl.value) {
    URL.revokeObjectURL(previewObjectUrl.value)
  }
})
</script>

<style lang="scss" scoped></style>
