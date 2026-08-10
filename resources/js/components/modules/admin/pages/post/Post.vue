<template>
  <div>
    <ContentLoader v-if="loading" speed="0.5" />
    <template v-else>
      <PostPageShell @create="openModal">
        <PostTable :rows="posts" @edit="editPost" @delete="deletePost" />
      </PostPageShell>

      <BaseModal
        :show="showModal"
        :title="formMode === 'create' ? 'Tambah Artikel' : 'Ubah Artikel'"
        @close="closeModal"
      >
        <PostForm
          :key="formKey"
          :initialValues="formValues"
          :categories="categories"
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
import { onMounted, reactive, ref, watch } from 'vue'
import { ContentLoader } from 'vue-content-loader'
import Swal from 'sweetalert2/dist/sweetalert2'
import apiClient from '@/services/api'
import BaseModal from '../../../../base/atoms/BaseModal.vue'
import PostPageShell from '../../../../base/organisms/PostPageShell.vue'
import PostTable from '../../../../base/organisms/PostTable.vue'
import PostForm from '../../../../base/molecules/PostForm.vue'

const props = defineProps({
  myposts: {
    type: Array,
    default: () => []
  }
})

const showModal = ref(false)
const formMode = ref('create')
const editId = ref(null)
const loading = ref(false)
const isSubmitting = ref(false)
const posts = ref([])
const categories = ref([])
const previewImage = ref('')
const selectedFile = ref(null)
const formKey = ref(0)

const formValues = reactive({
  title: '',
  slug: '',
  category_id: '',
  body: ''
})

const resetFormValues = () => {
  formValues.title = ''
  formValues.slug = ''
  formValues.category_id = ''
  formValues.body = ''
  selectedFile.value = null
  previewImage.value = ''
  formKey.value += 1
}

const openModal = () => {
  formMode.value = 'create'
  editId.value = null
  resetFormValues()
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  resetFormValues()
}

const getPostImage = (image) => {
  if (!image) return 'https://via.placeholder.com/100x100?text=No+Image'
  return image.startsWith('http') ? image : `/storage/${image.replace(/^\/+/, '')}`
}

const fetchPosts = async () => {
  loading.value = true

  try {
    const response = await apiClient.get('/admin/post-all')
    const payload = response?.data || {}

    posts.value = Array.isArray(payload?.posts) ? payload.posts : (Array.isArray(payload) ? payload : [])
    categories.value = Array.isArray(payload?.categories) ? payload.categories : []
  } catch (error) {
    console.error(error)
    Swal.fire({ icon: 'error', title: 'Gagal', text: 'Tidak dapat mengambil data artikel dari server.' })
  } finally {
    loading.value = false
  }
}

const handleFileChange = (file) => {
  selectedFile.value = file
  previewImage.value = file ? URL.createObjectURL(file) : ''
}

const submitForm = async (values) => {
  const formData = new FormData()
  formData.append('title', values.title)
  formData.append('slug', values.slug)
  formData.append('category_id', values.category_id)
  formData.append('body', values.body || '')

  if (selectedFile.value) {
    formData.append('post_image', selectedFile.value)
  }

  isSubmitting.value = true

  try {
    if (formMode.value === 'create') {
      await apiClient.post('/admin/post', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
    } else {
      await apiClient.post(`/admin/post-edit/${editId.value}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
    }

    await fetchPosts()
    closeModal()
    Swal.fire({
      icon: 'success',
      title: 'Berhasil',
      text: formMode.value === 'create' ? 'Artikel berhasil ditambah.' : 'Artikel berhasil diubah.'
    })
  } catch (error) {
    console.error(error)
    Swal.fire({
      icon: 'error',
      title: 'Gagal',
      text: error?.response?.data?.message || 'Proses simpan artikel gagal.'
    })
  } finally {
    isSubmitting.value = false
  }
}

const editPost = (id) => {
  const item = posts.value.find((post) => post.id == id)
  if (!item) return

  formMode.value = 'edit'
  editId.value = item.id
  formValues.title = item.title || ''
  formValues.slug = item.slug || ''
  formValues.category_id = item.category_id ?? ''
  formValues.body = item.body || ''
  selectedFile.value = null
  previewImage.value = getPostImage(item.post_image)
  formKey.value += 1
  showModal.value = true
}

const deletePost = async (id) => {
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
    const response = await apiClient.delete(`/admin/post/${id}`)
    if (response?.data?.status === 'success') {
      await fetchPosts()
      Swal.fire({ icon: 'success', title: 'Berhasil', text: response.data.message || 'Artikel berhasil dihapus.' })
    } else {
      Swal.fire({ icon: 'error', title: 'Gagal', text: response?.data?.message || 'Gagal menghapus artikel.' })
    }
  } catch (error) {
    console.error(error)
    Swal.fire({ icon: 'error', title: 'Gagal', text: error?.response?.data?.message || 'Gagal menghapus artikel.' })
  }
}

watch(
  () => props.myposts,
  (value) => {
    if (Array.isArray(value)) {
      posts.value = value
    }
  },
  { immediate: true }
)

onMounted(async () => {
  await fetchPosts()
})
</script>
