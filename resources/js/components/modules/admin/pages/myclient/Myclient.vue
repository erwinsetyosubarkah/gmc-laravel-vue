<template>
  <div>
    <ContentLoader v-if="loading" speed="0.5" />
    <template v-else>
      <MyclientPageShell @create="openModal">
        <MyclientTable :rows="clients" @edit="editClient" @delete="deleteClient" />
      </MyclientPageShell>

      <BaseModal :show="showModal" :title="formMode === 'create' ? 'Tambah Klien' : 'Ubah Klien'" @close="closeModal">
        <MyclientForm
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
import { onMounted, ref, reactive, watch } from 'vue'
import { ContentLoader } from 'vue-content-loader'
import Swal from 'sweetalert2/dist/sweetalert2'
import apiClient from '@/services/api'
import BaseModal from '../../../../base/atoms/BaseModal.vue'
import MyclientPageShell from '../../../../base/organisms/MyclientPageShell.vue'
import MyclientTable from '../../../../base/organisms/MyclientTable.vue'
import MyclientForm from '../../../../base/molecules/MyclientForm.vue'

const props = defineProps({
  myclients: {
    type: Array,
    default: () => []
  }
})

const showModal = ref(false)
const formMode = ref('create')
const editId = ref(null)
const loading = ref(false)
const clients = ref([])
const previewImage = ref('')
const selectedFile = ref(null)

const formValues = reactive({
  client_name: '',
  company_name: '',
  client_address: ''
})

const getClientImage = (image) => {
  if (!image) return 'https://via.placeholder.com/100x100?text=No+Image'
  return image.startsWith('http') ? image : `/storage/${image.replace(/^\/+/, '')}`
}

const openModal = () => {
  formMode.value = 'create'
  editId.value = null
  selectedFile.value = null
  previewImage.value = ''
  formValues.client_name = ''
  formValues.company_name = ''
  formValues.client_address = ''
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  selectedFile.value = null
  previewImage.value = ''
}

const fetchClients = async () => {
  loading.value = true
  try {
    const response = await apiClient.get('/admin/myclient-all')
    const payload = response?.data

    if (payload?.status === 'success' && Array.isArray(payload.data)) {
      clients.value = payload.data
    } else if (Array.isArray(payload)) {
      clients.value = payload
    } else {
      clients.value = []
    }
  } catch (error) {
    console.error(error)
    Swal.fire({ icon: 'error', title: 'Gagal', text: 'Tidak dapat mengambil data klien dari server.' })
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
  formData.append('client_name', values.client_name)
  formData.append('company_name', values.company_name)
  formData.append('client_address', values.client_address || '')

  if (selectedFile.value) {
    formData.append('client_image', selectedFile.value)
  }

  try {
    if (formMode.value === 'create') {
      await apiClient.post('/admin/myclient', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
    } else {
      await apiClient.post(`/admin/myclient-edit/${editId.value}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
    }

    await fetchClients()
    closeModal()
    Swal.fire({
      icon: 'success',
      title: 'Berhasil',
      text: formMode.value === 'create' ? 'Klien berhasil ditambah.' : 'Klien berhasil diubah.'
    })
  } catch (error) {
    console.error(error)
    Swal.fire({
      icon: 'error',
      title: 'Gagal',
      text: error?.response?.data?.message || 'Proses simpan klien gagal.'
    })
  }
}

const editClient = (id) => {
  const item = clients.value.find((client) => client.id == id)
  if (!item) return

  formMode.value = 'edit'
  editId.value = item.id
  selectedFile.value = null
  previewImage.value = getClientImage(item.client_image)

  formValues.client_name = item.client_name || ''
  formValues.company_name = item.company_name || ''
  formValues.client_address = item.client_address || ''

  showModal.value = true
}

const deleteClient = async (id) => {
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
    const response = await apiClient.delete(`/admin/myclient/${id}`)
    const payload = response?.data

    if (payload?.status === 'success') {
      await fetchClients()
      Swal.fire({ icon: 'success', title: 'Berhasil', text: payload.message || 'Klien berhasil dihapus.' })
    } else {
      Swal.fire({ icon: 'error', title: 'Gagal', text: payload?.message || 'Gagal menghapus klien.' })
    }
  } catch (error) {
    console.error(error)
    Swal.fire({ icon: 'error', title: 'Gagal', text: error?.response?.data?.message || 'Gagal menghapus klien.' })
  }
}

watch(
  () => props.myclients,
  (value) => {
    if (Array.isArray(value)) {
      clients.value = value
    }
  },
  { immediate: true }
)

onMounted(async () => {
  await fetchClients()
})
</script>
