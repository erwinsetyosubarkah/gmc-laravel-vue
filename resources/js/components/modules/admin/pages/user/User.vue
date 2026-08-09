<template>
  <div>
    <ContentLoader v-if="loading" speed="0.5" />
    <template v-else>
      <UserPageShell @create="openModal">
        <UserTable :rows="users" @edit="editUser" @delete="deleteUser" />
      </UserPageShell>

      <BaseModal :show="showModal" :title="formMode === 'create' ? 'Tambah User' : 'Ubah User'" @close="closeModal">
        <UserForm
          :initialValues="formValues"
          :previewImage="previewImage"
          :isCreateMode="formMode === 'create'"
          :isSubmitting="isSubmitting"
          @submit="submitForm"
          @change:file="handleFileChange"
          @cancel="closeModal"
          @zoom="zoomImg(previewImage)"
        />
      </BaseModal>
    </template>
  </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue'
import { ContentLoader } from 'vue-content-loader'
import Swal from 'sweetalert2/dist/sweetalert2'
import apiClient from '@/services/api'
import BaseModal from '../../../../base/atoms/BaseModal.vue'
import UserPageShell from '../../../../base/organisms/UserPageShell.vue'
import UserTable from '../../../../base/organisms/UserTable.vue'
import UserForm from '../../../../base/molecules/UserForm.vue'

const showModal = ref(false)
const formMode = ref('create')
const editId = ref(null)
const loading = ref(false)
const isSubmitting = ref(false)
const users = ref([])
const previewImage = ref('')
const selectedFile = ref(null)

const formValues = reactive({
  name: '',
  username: '',
  email: '',
  password: '',
  password2: '',
  level: 'author'
})

const defaultImage = '/img/no-image.svg'

const openModal = () => {
  formMode.value = 'create'
  editId.value = null
  selectedFile.value = null
  previewImage.value = ''
  Object.assign(formValues, {
    name: '',
    username: '',
    email: '',
    password: '',
    password2: '',
    level: 'author'
  })
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  selectedFile.value = null
  previewImage.value = ''
}

const getUserImage = (image) => {
  if (!image) return defaultImage
  return image.startsWith('http') ? image : `/storage/${image.replace(/^\/+/, '')}`
}

const fetchUsers = async () => {
  loading.value = true
  try {
    const response = await apiClient.get('/admin/user-all')
    const payload = response?.data

    if (payload?.status === 'success' && Array.isArray(payload.data)) {
      users.value = payload.data
    } else if (Array.isArray(payload)) {
      users.value = payload
    } else {
      users.value = []
    }
  } catch (error) {
    console.error(error)
    Swal.fire({ icon: 'error', title: 'Gagal', text: 'Tidak dapat mengambil data user.' })
  } finally {
    loading.value = false
  }
}

const handleFileChange = (file) => {
  selectedFile.value = file
  previewImage.value = file ? URL.createObjectURL(file) : ''
}

const submitForm = async (values, meta = {}) => {
  isSubmitting.value = true

  const formData = new FormData()
  formData.append('name', values.name)
  formData.append('username', values.username)
  formData.append('email', values.email)
  formData.append('level', values.level || '')

  if (values.password) {
    formData.append('password', values.password)
  }

  if (values.password2) {
    formData.append('password2', values.password2)
  }

  if (selectedFile.value) {
    formData.append('photo', selectedFile.value)
  }

  if (formMode.value === 'edit' && editId.value) {
    formData.append('old_photo', users.value.find((user) => user.id === editId.value)?.photo || '')
  }

  try {
    if (formMode.value === 'create') {
      await apiClient.post('/admin/user', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
    } else {
      await apiClient.post(`/admin/user-edit/${editId.value}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
    }

    await fetchUsers()
    closeModal()
    Swal.fire({
      icon: 'success',
      title: 'Berhasil',
      text: formMode.value === 'create' ? 'User berhasil ditambah.' : 'User berhasil diubah.'
    })
    meta?.resolve?.()
  } catch (error) {
    console.error(error)
    const message = error?.response?.data?.message || 'Proses simpan user gagal.'
    Swal.fire({ icon: 'error', title: 'Gagal', text: message })
    meta?.reject?.(error)
  } finally {
    isSubmitting.value = false
  }
}

const editUser = (id) => {
  const item = users.value.find((user) => user.id == id)
  if (!item) return

  formMode.value = 'edit'
  editId.value = item.id
  selectedFile.value = null
  Object.assign(formValues, {
    name: item.name || '',
    username: item.username || '',
    email: item.email || '',
    password: '',
    password2: '',
    level: item.level || 'author'
  })
  previewImage.value = getUserImage(item.photo)
  showModal.value = true
}

const deleteUser = async (id) => {
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
    const response = await apiClient.delete(`/admin/user/${id}`)
    const payload = response?.data

    if (payload?.status === 'success') {
      await fetchUsers()
      Swal.fire({ icon: 'success', title: 'Berhasil', text: payload.message || 'User berhasil dihapus.' })
    } else {
      Swal.fire({ icon: 'error', title: 'Gagal', text: payload?.message || 'Gagal menghapus user.' })
    }
  } catch (error) {
    console.error(error)
    Swal.fire({ icon: 'error', title: 'Gagal', text: error?.response?.data?.message || 'Gagal menghapus user.' })
  }
}

const zoomImg = (src) => {
  if (!src) return

  const modal = document.createElement('div')
  modal.style.position = 'fixed'
  modal.style.top = '0'
  modal.style.left = '0'
  modal.style.width = '100%'
  modal.style.height = '100%'
  modal.style.background = 'rgba(0, 0, 0, 0.8)'
  modal.style.display = 'flex'
  modal.style.alignItems = 'center'
  modal.style.justifyContent = 'center'
  modal.style.zIndex = '99999'
  modal.style.cursor = 'zoom-out'
  modal.onclick = () => modal.remove()

  const image = document.createElement('img')
  image.src = src
  image.style.maxWidth = '90vw'
  image.style.maxHeight = '90vh'
  image.style.objectFit = 'contain'
  image.style.borderRadius = '8px'

  modal.appendChild(image)
  document.body.appendChild(modal)
}

onMounted(async () => {
  await fetchUsers()
})
</script>
