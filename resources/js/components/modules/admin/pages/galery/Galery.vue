<template>
  <div>
    <ContentLoader v-if="loading" speed="0.5" />
    <button type="button" class="btn btn-success mb-3" @click="openModal" v-show="!loading">
      <i class="fas fa-plus"></i> Tambah
    </button>

    <p v-if="loading" class="text-muted">Memuat data galeri...</p>

    <table id="table-galery" class="table table-bordered table-striped" v-show="!loading">
      <thead>
        <tr>
          <th class="text-center">No</th>
          <th class="text-center">Foto</th>
          <th class="text-center">Judul</th>
          <th class="text-center">Aksi</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>

    <div v-if="showModal" class="modal fade show d-block" tabindex="-1" role="dialog" aria-modal="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ formMode === 'create' ? 'Tambah Galeri' : 'Ubah Galeri' }}</h5>
            <button type="button" class="close" @click="closeModal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>

          <form @submit.prevent="onSubmit" enctype="multipart/form-data">
            <div class="modal-body">
              <div class="form-group">
                <label for="image_title">Judul</label>
                <input
                  v-model="image_title"
                  type="text"
                  class="form-control"
                  :class="{ 'is-invalid': errors.image_title }"
                  id="image_title"
                  placeholder="Masukan judul foto..."
                />
                <div class="invalid-feedback">{{ errors.image_title }}</div>
              </div>

              <div class="form-group">
                <label for="galery_image">Foto</label>
                <input
                  type="file"
                  class="form-control"
                  :class="{ 'is-invalid': errors.galery_image }"
                  id="galery_image"
                  @change="handleImageChange"
                />
                <div class="invalid-feedback">{{ errors.galery_image }}</div>
                <img
                  v-if="previewImage"
                  :src="previewImage"
                  class="mb-2 mb-md-4 shadow-1-strong rounded mt-2"
                  style="cursor: zoom-in;"
                  width="100"
                  @click="zoomImg(previewImage)"
                />
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
  mygaleries: {
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
const galleries = ref([])
const tableInstance = ref(null)

const schema = toTypedSchema(
  yup.object({
    image_title: yup.string().required('Judul wajib diisi.').min(3, 'Judul minimal 3 karakter.'),
    galery_image: yup.mixed().nullable()
  })
)

const { handleSubmit, errors, isSubmitting, resetForm, setValues, setFieldError } = useForm({
  validationSchema: schema,
  initialValues: {
    image_title: '',
    galery_image: null
  }
})

const { value: image_title } = useField('image_title')
const { value: galery_image } = useField('galery_image')

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
  const fileInput = document.getElementById('galery_image')
  if (fileInput) fileInput.value = ''
}

const getGaleryImage = (image) => {
  if (!image) return defaultImage
  return image.startsWith('http') ? image : `/storage/${image.replace(/^\/+/, '')}`
}

const updateDataTable = (data = []) => {
  if (!tableInstance.value) {
    initDataTable(data)
    return
  }

  tableInstance.value.clear().rows.add(data).draw(false)
}

const fetchGalleries = async () => {
  loading.value = true
  try {
    const response = await apiClient.get('/admin/galery-all')
    const payload = response?.data || {}
    const galeryList = Array.isArray(payload?.data)
      ? payload.data
      : (Array.isArray(payload) ? payload : [])

    galleries.value = galeryList

    await nextTick()
    updateDataTable(galleries.value)
  } catch (error) {
    console.error(error)
    Swal.fire({ icon: 'error', title: 'Gagal', text: 'Tidak dapat mengambil data galeri dari server.' })
  } finally {
    loading.value = false
  }
}

const handleImageChange = (event) => {
  const file = event.target.files?.[0]
  if (!file) {
    galery_image.value = null
    previewImage.value = ''
    setFieldError('galery_image', '')
    return
  }

  galery_image.value = file
  setFieldError('galery_image', '')

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
  const fileInput = document.getElementById('galery_image')
  if (formMode.value === 'create' && !fileInput?.files?.[0]) {
    setFieldError('galery_image', 'Foto wajib diisi.')
    return
  }

  const formData = new FormData()
  formData.append('image_title', values.image_title)

  if (fileInput?.files?.[0]) {
    formData.append('galery_image', fileInput.files[0])
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
  }
})

const editGallery = (item) => {
  formMode.value = 'edit'
  editId.value = item.id
  setValues({
    image_title: item.image_title || '',
    galery_image: null
  })
  previewImage.value = getGaleryImage(item.galery_image)
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
    if (Array.isArray(value) && value.length) {
      galleries.value = value
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
      data: 'galery_image',
      orderable: false,
      searchable: false,
      className: 'text-center',
      render: (data) => `<img src="${getGaleryImage(data)}" alt="Galeri" class="img-fluid" width="100" />`
    },
    { data: 'image_title', className: 'text-center' },
    {
      data: null,
      orderable: false,
      searchable: false,
      className: 'text-center',
      render: (data, type, row) => `
        <button type="button" class="badge badge-warning mr-2 ml-2 btn-edit-galery border-0" data-id="${row.id}">
          <i class="fas fa-edit"></i> Ubah
        </button>
        <button type="button" class="badge badge-danger mr-2 ml-2 btn-delete-galery border-0" data-id="${row.id}">
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
  if (!$('#table-galery').length) return

  if (tableInstance.value) return updateDataTable(data)

  $(document).off('click', '#table-galery .btn-edit-galery')
  $(document).off('click', '#table-galery .btn-delete-galery')

  tableInstance.value = $('#table-galery').DataTable(getDataTablesOptions(data))

  $(document).on('click', '#table-galery .btn-edit-galery', function () {
    const id = $(this).data('id')
    const item = galleries.value.find((gallery) => gallery.id == id)
    if (item) editGallery(item)
  })

  $(document).on('click', '#table-galery .btn-delete-galery', function () {
    const id = $(this).data('id')
    deleteGallery(id)
  })
}

watch(galleries, async (newGalleries) => {
  if (tableInstance.value) {
    await nextTick()
    tableInstance.value.clear().rows.add(newGalleries).draw()
  }
}, { flush: 'post' })

onMounted(async () => {
  initDataTable([])
  await fetchGalleries()
})
</script>

<style lang="scss" scoped></style>
