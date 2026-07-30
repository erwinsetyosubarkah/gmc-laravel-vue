<template>
  <div>
    <ContentLoader v-if="loading" speed="0.5" />
    <template v-else>
      <button type="button" class="btn btn-success mb-3" @click="openModal">
        <i class="fas fa-plus"></i> Tambah
      </button>

      <table id="table-event" class="table table-bordered table-striped">
        <thead>
          <tr>
            <th class="text-center">No</th>
            <th class="text-center">Gambar Event</th>
            <th class="text-center">Nama Event</th>
            <th class="text-center">Tanggal</th>
            <th class="text-center">Deskripsi</th>
            <th class="text-center">Aksi</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>

      <div v-if="showModal" class="modal fade show d-block" tabindex="-1" role="dialog" aria-modal="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">{{ formMode === 'create' ? 'Tambah Event' : 'Ubah Event' }}</h5>
              <button type="button" class="close" @click="closeModal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>

            <form @submit.prevent="onSubmit" enctype="multipart/form-data">
              <div class="modal-body">
                <div class="form-group">
                  <label for="event_title">Nama Event</label>
                  <input
                    v-model="event_title"
                    type="text"
                    class="form-control"
                    :class="{ 'is-invalid': errors.event_title }"
                    id="event_title"
                    placeholder="Masukan nama event..."
                  />
                  <div class="invalid-feedback">{{ errors.event_title }}</div>
                </div>

                <div class="form-group">
                  <label for="event_date">Tanggal</label>
                  <input
                    v-model="event_date"
                    type="datetime-local"
                    class="form-control"
                    :class="{ 'is-invalid': errors.event_date }"
                    id="event_date"
                  />
                  <div class="invalid-feedback">{{ errors.event_date }}</div>
                </div>

                <div class="form-group">
                  <label for="event_image">Gambar Event</label>
                  <input
                    type="file"
                    class="form-control"
                    :class="{ 'is-invalid': errors.event_image }"
                    id="event_image"
                    @change="handleImageChange"
                  />
                  <div class="invalid-feedback">{{ errors.event_image }}</div>
                  <img
                    v-if="previewImage"
                    :src="previewImage"
                    class="mb-2 mb-md-4 shadow-1-strong rounded mt-2"
                    style="cursor: zoom-in;"
                    width="100"
                    @click="zoomImg(previewImage)"
                  />
                </div>

                <div class="form-group">
                  <label for="event_description">Deskripsi</label>
                  <textarea
                    v-model="event_description"
                    class="form-control ckeditor"
                    :class="{ 'is-invalid': errors.event_description }"
                    id="event_description"
                  ></textarea>
                  <div class="invalid-feedback">{{ errors.event_description }}</div>
                </div>
              </div>

              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" @click="closeModal">Close</button>
                <button type="submit" class="btn btn-primary" :disabled="isSubmitting">
                  <span v-if="isSubmitting" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                  {{ isSubmitting ? 'Menyimpan...' : 'Simpan' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { onMounted, nextTick, ref, watch } from 'vue'
import { ContentLoader } from 'vue-content-loader'
import { useForm, useField } from 'vee-validate'
import { toTypedSchema } from '@vee-validate/yup'
import * as yup from 'yup'
import Swal from 'sweetalert2/dist/sweetalert2'
import apiClient from '@/services/api'

const props = defineProps({
  myevents: {
    type: Array,
    default: () => []
  }
})

const defaultImage = 'https://via.placeholder.com/100x100?text=No+Image'
const showModal = ref(false)
const formMode = ref('create')
const editId = ref(null)
const previewImage = ref('')
const loading = ref(false)
const events = ref([])
const tableInstance = ref(null)

const schema = toTypedSchema(
  yup.object({
    event_title: yup.string().required('Nama event wajib diisi.').min(3, 'Nama event minimal 3 karakter.'),
    event_date: yup.string().required('Tanggal wajib diisi.'),
    event_image: yup.mixed().nullable(),
    event_description: yup.string().nullable(),
  })
)

const { handleSubmit, errors, isSubmitting, resetForm, setValues, setFieldError } = useForm({
  validationSchema: schema,
  initialValues: {
    event_title: '',
    event_date: '',
    event_image: null,
    event_description: '',
  }
})

const { value: event_title } = useField('event_title')
const { value: event_date } = useField('event_date')
const { value: event_image } = useField('event_image')
const { value: event_description } = useField('event_description')

const openModal = () => {
  formMode.value = 'create'
  editId.value = null
  resetForm()
  previewImage.value = ''
  clearFileInput()
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  resetForm()
  previewImage.value = ''
  clearFileInput()
}

const clearFileInput = () => {
  const fileInput = document.getElementById('event_image')
  if (fileInput) fileInput.value = ''
}

const getEventImage = (image) => {
  if (!image) return defaultImage
  return image.startsWith('http') ? image : `/storage/${image.replace(/^\/+/, '')}`
}

const formatDate = (value) => {
  if (!value) return '-'
  const date = new Date(value)
  if (Number.isNaN(date.getTime())) return value
  return date.toLocaleString('id-ID', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const formatDateForInput = (value) => {
  if (!value) return ''
  const date = new Date(value)
  if (Number.isNaN(date.getTime())) return value
  const pad = (num) => String(num).padStart(2, '0')
  return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}`
}

const truncateText = (value) => {
  if (!value) return ''
  const plainText = value.replace(/<[^>]*>/g, ' ')
  return plainText.length > 90 ? `${plainText.slice(0, 90)}...` : plainText
}

const updateDataTable = (data = []) => {
  if (!tableInstance.value) {
    initDataTable(data)
    return
  }

  tableInstance.value.clear().rows.add(data).draw(false)
}

const fetchEvents = async () => {
  loading.value = true
  try {
    const response = await apiClient.get('/admin/event-all')
    const payload = response?.data || {}
    const eventList = Array.isArray(payload?.data) ? payload.data : (Array.isArray(payload) ? payload : [])

    events.value = eventList

    await nextTick()
    updateDataTable(events.value)
  } catch (error) {
    console.error(error)
    Swal.fire({ icon: 'error', title: 'Gagal', text: 'Tidak dapat mengambil data event dari server.' })
  } finally {
    loading.value = false
  }
}

const handleImageChange = (event) => {
  const file = event.target.files?.[0]
  if (!file) {
    event_image.value = null
    previewImage.value = ''
    setFieldError('event_image', '')
    return
  }

  event_image.value = file
  setFieldError('event_image', '')

  const reader = new FileReader()
  reader.onload = () => {
    previewImage.value = reader.result
  }
  reader.readAsDataURL(file)
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

const onSubmit = handleSubmit(async (values) => {
  const fileInput = document.getElementById('event_image')
  if (formMode.value === 'create' && !fileInput?.files?.[0]) {
    setFieldError('event_image', 'Gambar wajib diisi.')
    return
  }

  const formData = new FormData()
  formData.append('event_title', values.event_title)
  formData.append('event_date', values.event_date)
  formData.append('event_description', values.event_description || '')

  if (fileInput?.files?.[0]) {
    formData.append('event_image', fileInput.files[0])
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
    const serverErrors = error?.response?.data?.errors || {}
    Object.entries(serverErrors).forEach(([field, message]) => {
      setFieldError(field, Array.isArray(message) ? message[0] : message)
    })

    Swal.fire({
      icon: 'error',
      title: 'Gagal',
      text: error?.response?.data?.message || 'Proses simpan event gagal.'
    })
  }
})

const editEvent = (item) => {
  formMode.value = 'edit'
  editId.value = item.id
  setValues({
    event_title: item.event_title || '',
    event_date: formatDateForInput(item.event_date),
    event_image: null,
    event_description: item.event_description || '',
  })
  previewImage.value = getEventImage(item.event_image)
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
    if (Array.isArray(value) && value.length) {
      events.value = value
    }
  },
  { immediate: true }
)

const getDataTablesOptions = (data) => ({
  data,
  columns: [
    {
      data: null,
      orderable: false,
      searchable: false,
      className: 'text-center',
      render: (data, type, row, meta) => meta.row + 1
    },
    {
      data: 'event_image',
      orderable: false,
      searchable: false,
      className: 'text-center',
      render: (data) => `<img src="${getEventImage(data)}" alt="Event" class="img-fluid" width="100" />`
    },
    { data: 'event_title', className: 'text-center' },
    {
      data: 'event_date',
      className: 'text-center',
      render: (data) => formatDate(data)
    },
    {
      data: 'event_description',
      className: 'text-center',
      render: (data) => truncateText(data)
    },
    {
      data: null,
      orderable: false,
      searchable: false,
      className: 'text-center',
      render: (data, type, row) => `
        <button type="button" class="badge badge-warning mr-2 ml-2 btn-edit-event border-0" data-id="${row.id}">
          <i class="fas fa-edit"></i> Ubah
        </button>
        <button type="button" class="badge badge-danger mr-2 ml-2 btn-delete-event border-0" data-id="${row.id}">
          <i class="fas fa-trash"></i> Hapus
        </button>`
    }
  ],
  paging: true,
  lengthChange: true,
  searching: true,
  ordering: true,
  info: true,
  autoWidth: true,
  responsive: true,
  language: {
    decimal: '',
    emptyTable: 'Tidak ada data ditemukan di tabel',
    info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
    infoEmpty: 'Menampilkan 0 dari 0 dari 0 data',
    infoFiltered: '(Difilter dari _MAX_ total data)',
    infoPostFix: '',
    thousands: ',',
    lengthMenu: 'Menampilkan _MENU_ data',
    loadingRecords: 'Loading...',
    processing: '',
    search: 'Cari:',
    zeroRecords: 'Tidak ada data ditemukan',
    paginate: {
      first: 'Pertama',
      last: 'Terakhir',
      next: 'Selanjutnya',
      previous: 'Sebelumnya'
    }
  }
})

const initDataTable = (data = []) => {
  if (!$('#table-event').length) return

  if (tableInstance.value) {
    updateDataTable(data)
    return
  }

  $(document).off('click', '#table-event .btn-edit-event')
  $(document).off('click', '#table-event .btn-delete-event')

  tableInstance.value = $('#table-event').DataTable(getDataTablesOptions(data))

  $(document).on('click', '#table-event .btn-edit-event', function () {
    const id = $(this).data('id')
    const item = events.value.find((event) => event.id == id)
    if (item) editEvent(item)
  })

  $(document).on('click', '#table-event .btn-delete-event', function () {
    const id = $(this).data('id')
    deleteEvent(id)
  })
}

watch(events, async (newEvents) => {
  if (tableInstance.value) {
    await nextTick()
    tableInstance.value.clear().rows.add(newEvents).draw()
  }
}, { flush: 'post' })

onMounted(async () => {
  initDataTable([])
  await fetchEvents()
})
</script>
