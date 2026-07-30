<template>
  <div>
    <ContentLoader v-if="loading" speed="0.5" />
    <button type="button" class="btn btn-success mb-3" @click="openModal" v-show="!loading">
      <i class="fas fa-plus"></i> Tambah
    </button>

    <p v-if="loading" class="text-muted">Memuat data klien...</p>

    <table id="table-myclient" class="table table-bordered table-striped" v-show="!loading">
      <thead>
        <tr>
          <th class="text-center">No</th>
          <th class="text-center">Foto</th>
          <th class="text-center">Nama Klien</th>
          <th class="text-center">Perusahaan</th>
          <th class="text-center">Alamat</th>
          <th class="text-center">Aksi</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>

    <div v-if="showModal" class="modal fade show d-block" tabindex="-1" role="dialog" aria-modal="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ formMode === 'create' ? 'Tambah Klien' : 'Ubah Klien' }}</h5>
            <button type="button" class="close" @click="closeModal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>

          <form @submit.prevent="onSubmit" enctype="multipart/form-data">
            <div class="modal-body">
              <div class="form-group">
                <label for="client_name">Nama Klien</label>
                <input
                  v-model="client_name"
                  type="text"
                  class="form-control"
                  :class="{ 'is-invalid': errors.client_name }"
                  id="client_name"
                  placeholder="Masukan nama klien..."
                />
                <div class="invalid-feedback">{{ errors.client_name }}</div>
              </div>

              <div class="form-group">
                <label for="company_name">Perusahaan</label>
                <input
                  v-model="company_name"
                  type="text"
                  class="form-control"
                  :class="{ 'is-invalid': errors.company_name }"
                  id="company_name"
                  placeholder="Masukan nama perusahaan..."
                />
                <div class="invalid-feedback">{{ errors.company_name }}</div>
              </div>

              <div class="form-group">
                <label for="client_image">Foto</label>
                <input
                  type="file"
                  class="form-control"
                  :class="{ 'is-invalid': errors.client_image }"
                  id="client_image"
                  @change="handleImageChange"
                />
                <div class="invalid-feedback">{{ errors.client_image }}</div>
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
                <label for="client_address">Alamat</label>
                <textarea
                  v-model="client_address"
                  class="form-control ckeditor"
                  :class="{ 'is-invalid': errors.client_address }"
                  id="client_address"
                  placeholder="Masukan alamat..."
                ></textarea>
                <div class="invalid-feedback">{{ errors.client_address }}</div>
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
  myclients: {
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
const clients = ref([])
const tableInstance = ref(null)

const schema = toTypedSchema(
  yup.object({
    client_name: yup.string().required('Nama klien wajib diisi.').min(3, 'Nama klien minimal 3 karakter.'),
    company_name: yup.string().required('Nama perusahaan wajib diisi.').min(3, 'Nama perusahaan minimal 3 karakter.'),
    client_image: yup.mixed().nullable(),
    client_address: yup.string().required('Alamat wajib diisi.'),
  })
)

const { handleSubmit, errors, isSubmitting, resetForm, setValues } = useForm({
  validationSchema: schema,
  initialValues: {
    client_name: '',
    company_name: '',
    client_image: null,
    client_address: '',
  }
})

const { value: client_name } = useField('client_name')
const { value: company_name } = useField('company_name')
const { value: client_image } = useField('client_image')
const { value: client_address } = useField('client_address')

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
  const fileInput = document.getElementById('client_image')
  if (fileInput) fileInput.value = ''
}

const getClientImage = (image) => {
  if (!image) return defaultImage
  return image.startsWith('http') ? image : `/storage/${image.replace(/^\/+/, '')}`
}

const stripTags = (value) => {
  if (!value) return ''
  return value.replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim()
}

const truncateAddress = (value) => {
  const plainText = stripTags(value)
  return plainText.length > 80 ? `${plainText.slice(0, 80)}...` : plainText
}

const updateDataTable = (data = []) => {
  if (!tableInstance.value) {
    initDataTable(data)
    return
  }

  tableInstance.value.clear().rows.add(data).draw(false)
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

    await nextTick()
    updateDataTable(clients.value)
  } catch (error) {
    console.error(error)
    Swal.fire({ icon: 'error', title: 'Gagal', text: 'Tidak dapat mengambil data klien dari server.' })
  } finally {
    loading.value = false
  }
}

const handleImageChange = (event) => {
  const file = event.target.files?.[0]
  if (!file) return

  client_image.value = file

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
  const formData = new FormData()
  formData.append('client_name', values.client_name)
  formData.append('company_name', values.company_name)
  formData.append('client_address', values.client_address || '')

  const fileInput = document.getElementById('client_image')
  if (fileInput?.files?.[0]) {
    formData.append('client_image', fileInput.files[0])
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
})

const editClient = (item) => {
  formMode.value = 'edit'
  editId.value = item.id
  setValues({
    client_name: item.client_name || '',
    company_name: item.company_name || '',
    client_image: null,
    client_address: item.client_address || '',
  })
  previewImage.value = getClientImage(item.client_image)
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
    if (Array.isArray(value) && value.length) {
      clients.value = value
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
      data: 'client_image',
      orderable: false,
      searchable: false,
      className: 'text-center',
      render: (data) => {
        const src = getClientImage(data)
        return `<img src="${src}" alt="Klien" class="img-fluid" width="100" />`
      }
    },
    { data: 'client_name', className: 'text-center' },
    { data: 'company_name', className: 'text-center' },
    {
      data: 'client_address',
      className: 'text-center',
      render: (data) => truncateAddress(data)
    },
    {
      data: null,
      orderable: false,
      searchable: false,
      className: 'text-center',
      render: (data, type, row) => `
        <button type="button" class="badge badge-warning mr-2 ml-2 btn-edit-client border-0" data-id="${row.id}">
          <i class="fas fa-edit"></i> Ubah
        </button>
        <button type="button" class="badge badge-danger mr-2 ml-2 btn-delete-client border-0" data-id="${row.id}">
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
  if (!$('#table-myclient').length) return

  if (tableInstance.value) return updateDataTable(data)

  $(document).off('click', '#table-myclient .btn-edit-client')
  $(document).off('click', '#table-myclient .btn-delete-client')

  tableInstance.value = $('#table-myclient').DataTable(getDataTablesOptions(data))

  $(document).on('click', '#table-myclient .btn-edit-client', function () {
    const id = $(this).data('id')
    const item = clients.value.find((client) => client.id == id)
    if (item) editClient(item)
  })

  $(document).on('click', '#table-myclient .btn-delete-client', function () {
    const id = $(this).data('id')
    deleteClient(id)
  })
}

watch(clients, async (newClients) => {
  if (tableInstance.value) {
    await nextTick()
    tableInstance.value.clear().rows.add(newClients).draw()
  }
}, { flush: 'post' })

onMounted(async () => {
  initDataTable([])
  await fetchClients()
})
</script>
