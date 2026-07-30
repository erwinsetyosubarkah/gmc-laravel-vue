<template>
  <div>
    <ContentLoader v-if="loading" speed="0.5" />
    <template v-else>
      <button type="button" class="btn btn-success mb-3" @click="openModal">
        <i class="fas fa-plus"></i> Tambah
      </button>

      <table id="table-post" class="table table-bordered table-striped">
        <thead>
          <tr>
            <th class="text-center">No</th>
            <th class="text-center">Gambar Artikel</th>
            <th class="text-center">Judul</th>
            <th class="text-center">Slug</th>
            <th class="text-center">Artikel</th>
            <th class="text-center">Kategori</th>
            <th class="text-center">Author</th>
            <th class="text-center">Aksi</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>

      <div v-if="showModal" class="modal fade show d-block" tabindex="-1" role="dialog" aria-modal="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">{{ formMode === 'create' ? 'Tambah Artikel' : 'Ubah Artikel' }}</h5>
              <button type="button" class="close" @click="closeModal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>

            <form @submit.prevent="onSubmit" enctype="multipart/form-data">
              <div class="modal-body">
                <div class="form-group">
                  <label for="title">Judul</label>
                  <input
                    v-model="title"
                    type="text"
                    class="form-control"
                    :class="{ 'is-invalid': errors.title }"
                    id="title"
                    placeholder="Masukan judul artikel..."
                    @input="slug = generateSlug($event.target.value)"
                  />
                  <div class="invalid-feedback">{{ errors.title }}</div>
                </div>

                <div class="form-group">
                  <label for="slug">Slug</label>
                  <input
                    v-model="slug"
                    type="text"
                    class="form-control"
                    :class="{ 'is-invalid': errors.slug }"
                    id="slug"
                    placeholder="Masukan slug..."
                  />
                  <div class="invalid-feedback">{{ errors.slug }}</div>
                </div>

                <div class="form-group">
                  <label for="category_id">Kategori</label>
                  <select
                    v-model="category_id"
                    id="category_id"
                    class="form-control"
                    :class="{ 'is-invalid': errors.category_id }"
                  >
                    <option value="" disabled>Pilih kategori</option>
                    <option v-for="category in categories" :key="category.id" :value="category.id">
                      {{ category.category_name }}
                    </option>
                  </select>
                  <div class="invalid-feedback">{{ errors.category_id }}</div>
                </div>

                <div class="form-group">
                  <label for="post_image">Foto</label>
                  <input
                    type="file"
                    class="form-control"
                    :class="{ 'is-invalid': errors.post_image }"
                    id="post_image"
                    @change="handleImageChange"
                  />
                  <div class="invalid-feedback">{{ errors.post_image }}</div>
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
                  <label for="body">Artikel</label>
                  <textarea
                    v-model="body"
                    class="form-control ckeditor"
                    :class="{ 'is-invalid': errors.body }"
                    id="body"
                  ></textarea>
                  <div class="invalid-feedback">{{ errors.body }}</div>
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
  myposts: {
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
const posts = ref([])
const categories = ref([])
const tableInstance = ref(null)

const schema = toTypedSchema(
  yup.object({
    title: yup.string().required('Judul wajib diisi.').min(3, 'Judul minimal 3 karakter.'),
    slug: yup.string().required('Slug wajib diisi.').min(3, 'Slug minimal 3 karakter.'),
    category_id: yup.mixed().required('Kategori wajib dipilih.'),
    body: yup.string().required('Artikel wajib diisi.'),
    post_image: yup.mixed().nullable(),
  })
)

const { handleSubmit, errors, isSubmitting, resetForm, setValues, setFieldError } = useForm({
  validationSchema: schema,
  initialValues: {
    title: '',
    slug: '',
    category_id: '',
    post_image: null,
    body: '',
  }
})

const { value: title } = useField('title')
const { value: slug } = useField('slug')
const { value: category_id } = useField('category_id')
const { value: post_image } = useField('post_image')
const { value: body } = useField('body')

const generateSlug = (value = '') => value
  .toLowerCase()
  .trim()
  .replace(/[^a-z0-9\s-]/g, '')
  .replace(/\s+/g, '-')
  .replace(/-+/g, '-')

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
  const fileInput = document.getElementById('post_image')
  if (fileInput) fileInput.value = ''
}

const getPostImage = (image) => {
  if (!image) return defaultImage
  return image.startsWith('http') ? image : `/storage/${image.replace(/^\/+/, '')}`
}

const truncateText = (value) => {
  if (!value) return ''
  const plainText = value.replace(/<[^>]*>/g, ' ')
  return plainText.length > 80 ? `${plainText.slice(0, 80)}...` : plainText
}

const updateDataTable = (data = []) => {

  if (!tableInstance.value) {
    initDataTable(data)
    return
  }

  tableInstance.value.clear().rows.add(data).draw(false)
}

const fetchPosts = async () => {
  loading.value = true
  try {
    const response = await apiClient.get('/admin/post-all')
    const payload = response?.data || {}
    const postList = Array.isArray(payload?.posts) ? payload.posts : (Array.isArray(payload) ? payload : [])
    const categoryList = Array.isArray(payload?.categories) ? payload.categories : []

    // if (payload?.status === 'success' && Array.isArray(payload.data)) {
    //   products.value = payload.data
    // } else if (Array.isArray(payload)) {
    //   products.value = payload
    // } else {
    //   products.value = []
    // }

    posts.value = postList
    categories.value = categoryList

    await nextTick()
    updateDataTable(posts.value)
  } catch (error) {
    console.error(error)
    Swal.fire({ icon: 'error', title: 'Gagal', text: 'Tidak dapat mengambil data artikel dari server.' })
  } finally {
    loading.value = false
  }
}

const handleImageChange = (event) => {
  const file = event.target.files?.[0]
  if (!file) {
    post_image.value = null
    previewImage.value = ''
    setFieldError('post_image', '')
    return
  }

  post_image.value = file
  setFieldError('post_image', '')

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
  const fileInput = document.getElementById('post_image')
  if (formMode.value === 'create' && !fileInput?.files?.[0]) {
    setFieldError('post_image', 'Foto wajib diisi.')
    return
  }

  const formData = new FormData()
  formData.append('title', values.title)
  formData.append('slug', values.slug)
  formData.append('category_id', values.category_id)
  formData.append('body', values.body || '')

  if (fileInput?.files?.[0]) {
    formData.append('post_image', fileInput.files[0])
  }

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
  }
})

const editPost = (item) => {
  formMode.value = 'edit'
  editId.value = item.id
  setValues({
    title: item.title || '',
    slug: item.slug || '',
    category_id: item.category_id ?? '',
    post_image: null,
    body: item.body || '',
  })
  previewImage.value = getPostImage(item.post_image)
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
    if (Array.isArray(value) && value.length) {
      posts.value = value
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
      data: 'post_image',
      orderable: false,
      searchable: false,
      className: 'text-center',
      render: (data) => `<img src="${getPostImage(data)}" alt="Artikel" class="img-fluid" width="100" />`
    },
    { data: 'title', className: 'text-center' },
    { data: 'slug', className: 'text-center' },
    {
      data: 'body',
      className: 'text-center',
      render: (data) => truncateText(data)
    },
    {
      data: null,
      className: 'text-center',
      render: (data, type, row) => row?.category?.category_name || '-'
    },
    {
      data: null,
      className: 'text-center',
      render: (data, type, row) => row?.user?.name || '-'
    },
    {
      data: null,
      orderable: false,
      searchable: false,
      className: 'text-center',
      render: (data, type, row) => `
        <button type="button" class="badge badge-warning mr-2 ml-2 btn-edit-post border-0" data-id="${row.id}">
          <i class="fas fa-edit"></i> Ubah
        </button>
        <button type="button" class="badge badge-danger mr-2 ml-2 btn-delete-post border-0" data-id="${row.id}">
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
  if (!$('#table-post').length) return

  if (tableInstance.value) {
    updateDataTable(data)
    return
  }

  $(document).off('click', '#table-post .btn-edit-post')
  $(document).off('click', '#table-post .btn-delete-post')

  tableInstance.value = $('#table-post').DataTable(getDataTablesOptions(data))

  $(document).on('click', '#table-post .btn-edit-post', function () {
    const id = $(this).data('id')
    const item = posts.value.find((post) => post.id == id)
    if (item) editPost(item)
  })

  $(document).on('click', '#table-post .btn-delete-post', function () {
    const id = $(this).data('id')
    deletePost(id)
  })
}

watch(posts, async (newPosts) => {
  if (tableInstance.value) {
    await nextTick()
    tableInstance.value.clear().rows.add(newPosts).draw()
  }
}, { flush: 'post' })

onMounted(async () => {
  initDataTable([])
  await fetchPosts()
})

</script>
