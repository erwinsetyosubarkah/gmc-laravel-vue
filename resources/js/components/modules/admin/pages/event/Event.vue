<template>
  <div>
    <ContentLoader v-if="loading" speed="0.5" />
    <template v-else>
      <EventPageShell @create="openModal">
        <EventTable :rows="events" @edit="editEvent" @delete="deleteEvent" />
      </EventPageShell>

      <BaseModal :show="showModal" :title="formMode === 'create' ? 'Tambah Event' : 'Ubah Event'" @close="closeModal">
        <EventForm
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
import { onMounted, reactive, ref, watch } from 'vue'
import { ContentLoader } from 'vue-content-loader'
import Swal from 'sweetalert2/dist/sweetalert2'
import apiClient from '@/services/api'
import BaseModal from '../../../../base/atoms/BaseModal.vue'
import EventPageShell from '../../../../base/organisms/EventPageShell.vue'
import EventTable from '../../../../base/organisms/EventTable.vue'
import EventForm from '../../../../base/molecules/EventForm.vue'

const props = defineProps({
  myevents: {
    type: Array,
    default: () => []
  }
})

const showModal = ref(false)
const formMode = ref('create')
const editId = ref(null)
const loading = ref(false)
const events = ref([])
const previewImage = ref('')
const selectedFile = ref(null)
const isSubmitting = ref(false)

const formValues = reactive({
  event_title: '',
  event_date: '',
  event_description: ''
})

const openModal = () => {
  formMode.value = 'create'
  editId.value = null
  selectedFile.value = null
  previewImage.value = ''
  Object.assign(formValues, {
    event_title: '',
    event_date: '',
    event_description: ''
  })
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  selectedFile.value = null
  previewImage.value = ''
}

const fetchEvents = async () => {
  loading.value = true
  try {
    const response = await apiClient.get('/admin/event-all')
    const payload = response?.data || {}
    const eventList = Array.isArray(payload?.data) ? payload.data : (Array.isArray(payload) ? payload : [])
    events.value = eventList
  } catch (error) {
    console.error(error)
    Swal.fire({ icon: 'error', title: 'Gagal', text: 'Tidak dapat mengambil data event dari server.' })
  } finally {
    loading.value = false
  }
}

const handleFileChange = (file) => {
  selectedFile.value = file
  previewImage.value = file ? URL.createObjectURL(file) : ''
}

const submitForm = async (values) => {
  isSubmitting.value = true
  const formData = new FormData()
  formData.append('event_title', values.event_title)
  formData.append('event_date', values.event_date)
  formData.append('event_description', values.event_description || '')

  if (selectedFile.value) {
    formData.append('event_image', selectedFile.value)
  }

  try {
    if (formMode.value === 'create') {
      await apiClient.post('/admin/event', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
    } else {
      await apiClient.post(`/admin/event-edit/${editId.value}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
    }

    await fetchEvents()
    closeModal()
    Swal.fire({
      icon: 'success',
      title: 'Berhasil',
      text: formMode.value === 'create' ? 'Event berhasil ditambah.' : 'Event berhasil diubah.'
    })
  } catch (error) {
    console.error(error)
    Swal.fire({
      icon: 'error',
      title: 'Gagal',
      text: error?.response?.data?.message || 'Proses simpan event gagal.'
    })
  } finally {
    isSubmitting.value = false
  }
}

const formatDateForInput = (value) => {
  if (!value) return ''
  const date = new Date(value)
  if (Number.isNaN(date.getTime())) return value
  const pad = (num) => String(num).padStart(2, '0')
  return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}`
}

const getEventImage = (image) => {
  if (!image) return ''
  return image.startsWith('http') ? image : `/storage/${image.replace(/^\/+/, '')}`
}

const editEvent = (id) => {
  const item = events.value.find((event) => event.id == id)
  if (!item) {
    Swal.fire({ icon: 'error', title: 'Gagal', text: 'Data event tidak ditemukan.' })
    return
  }

  formMode.value = 'edit'
  editId.value = item.id
  selectedFile.value = null
  previewImage.value = getEventImage(item.event_image)
  Object.assign(formValues, {
    event_title: item.event_title || '',
    event_date: formatDateForInput(item.event_date),
    event_description: item.event_description || ''
  })
  showModal.value = true
}

const deleteEvent = async (id) => {
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
    const response = await apiClient.delete(`/admin/event/${id}`)
    if (response?.data?.status === 'success') {
      await fetchEvents()
      Swal.fire({ icon: 'success', title: 'Berhasil', text: response.data.message || 'Event berhasil dihapus.' })
    } else {
      Swal.fire({ icon: 'error', title: 'Gagal', text: response?.data?.message || 'Gagal menghapus event.' })
    }
  } catch (error) {
    Swal.fire({ icon: 'error', title: 'Gagal', text: error?.response?.data?.message || 'Gagal menghapus event.' })
  }
}

watch(
  () => props.myevents,
  (value) => {
    if (Array.isArray(value)) {
      events.value = value
    }
  },
  { immediate: true }
)

onMounted(async () => {
  await fetchEvents()
})
</script>
