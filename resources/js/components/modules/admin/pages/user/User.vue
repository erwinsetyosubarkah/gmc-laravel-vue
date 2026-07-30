<template>
  <div>
    <ContentLoader v-if="loading" speed="0.5" />
    <template v-else>
      <button type="button" class="btn btn-success mb-3" @click="openModal">
        <i class="fas fa-plus"></i> Tambah
      </button>

      <table id="table-user" class="table table-bordered table-striped">
        <thead>
          <tr>
            <th class="text-center">No</th>
            <th class="text-center">Foto</th>
            <th class="text-center">Nama</th>
            <th class="text-center">Username</th>
            <th class="text-center">Email</th>
            <th class="text-center">Level</th>
            <th class="text-center">Aksi</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>

      <div v-if="showModal" class="modal fade show d-block" tabindex="-1" role="dialog" aria-modal="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">{{ formMode === 'create' ? 'Tambah User' : 'Ubah User' }}</h5>
              <button type="button" class="close" @click="closeModal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>

            <form @submit.prevent="onSubmit" enctype="multipart/form-data">
              <div class="modal-body">
                <div class="form-group">
                  <label for="name">Nama</label>
                  <input
                    v-model="name"
                    type="text"
                    class="form-control"
                    :class="{ 'is-invalid': errors.name }"
                    id="name"
                    placeholder="Masukan nama..."
                  />
                  <div class="invalid-feedback">{{ errors.name }}</div>
                </div>

                <div class="form-group">
                  <label for="username">Username</label>
                  <input
                    v-model="username"
                    type="text"
                    class="form-control"
                    :class="{ 'is-invalid': errors.username }"
                    id="username"
                  />
                  <div class="invalid-feedback">{{ errors.username }}</div>
                </div>

                <div class="form-group">
                  <label for="email">Email</label>
                  <input
                    v-model="email"
                    type="email"
                    class="form-control"
                    :class="{ 'is-invalid': errors.email }"
                    id="email"
                  />
                  <div class="invalid-feedback">{{ errors.email }}</div>
                </div>

                <div class="form-group">
                  <label for="password">Password</label>
                  <input
                    v-model="password"
                    type="password"
                    class="form-control"
                    :class="{ 'is-invalid': errors.password }"
                    id="password"
                  />
                  <div class="invalid-feedback">{{ errors.password }}</div>
                </div>

                <div class="form-group">
                  <label for="password2">Konfirmasi Password</label>
                  <input
                    v-model="password2"
                    type="password"
                    class="form-control"
                    :class="{ 'is-invalid': errors.password2 }"
                    id="password2"
                  />
                  <div class="invalid-feedback">{{ errors.password2 }}</div>
                </div>

                <div class="form-group">
                  <label for="level">Level</label>
                  <select
                    v-model="level"
                    id="level"
                    class="form-control"
                    :class="{ 'is-invalid': errors.level }"
                  >
                    <option value="admin">admin</option>
                    <option value="author">author</option>
                  </select>
                  <div class="invalid-feedback">{{ errors.level }}</div>
                </div>

                <div class="form-group">
                  <label for="photo">Foto</label>
                  <input
                    type="file"
                    class="form-control"
                    :class="{ 'is-invalid': errors.photo }"
                    id="photo"
                    @change="handleImageChange"
                  />
                  <div class="invalid-feedback">{{ errors.photo }}</div>
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
    </template>
  </div>
</template>

<script setup>
import { onMounted, nextTick, ref } from 'vue'
import { ContentLoader } from 'vue-content-loader'
import { useForm, useField } from 'vee-validate'
import { toTypedSchema } from '@vee-validate/yup'
import * as yup from 'yup'
import Swal from 'sweetalert2/dist/sweetalert2'
import apiClient from '@/services/api'

const defaultImage = 'https://via.placeholder.com/100x100?text=No+Image'
const showModal = ref(false)
const formMode = ref('create')
const editId = ref(null)
const previewImage = ref('')
const loading = ref(false)
const users = ref([])
const tableInstance = ref(null)

const schema = toTypedSchema(
  yup.object({
    name: yup.string().required('Nama wajib diisi.').min(3, 'Nama minimal 3 karakter.').max(255, 'Nama maksimal 255 karakter.'),
    username: yup.string().required('Username wajib diisi.').min(3, 'Username minimal 3 karakter.').max(255, 'Username maksimal 255 karakter.'),
    email: yup.string().required('Email wajib diisi.').email('Format email tidak valid.'),
    level: yup.string().required('Level wajib dipilih.'),
    password: yup.string().when([], {
      is: () => formMode.value === 'create',
      then: (schema) => schema.required('Password wajib diisi.').min(5, 'Password minimal 5 karakter.'),
      otherwise: (schema) => schema.notRequired()
    }),
    password2: yup.string().when([], {
      is: () => formMode.value === 'create',
      then: (schema) => schema.required('Konfirmasi password wajib diisi.').min(5, 'Konfirmasi password minimal 5 karakter.').oneOf([yup.ref('password')], 'Konfirmasi password tidak sama.'),
      otherwise: (schema) => schema.notRequired()
    }),
    photo: yup.mixed().nullable()
  })
)

const { handleSubmit, errors, isSubmitting, resetForm, setValues } = useForm({
  validationSchema: schema,
  initialValues: {
    name: '',
    username: '',
    email: '',
    password: '',
    password2: '',
    level: 'author',
    photo: null
  }
})

const { value: name } = useField('name')
const { value: username } = useField('username')
const { value: email } = useField('email')
const { value: password } = useField('password')
const { value: password2 } = useField('password2')
const { value: level } = useField('level')
const { value: photo } = useField('photo')

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
  const fileInput = document.getElementById('photo')
  if (fileInput) fileInput.value = ''
}

const getUserImage = (image) => {
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

    await nextTick()
    updateDataTable(users.value)
  } catch (error) {
    console.error(error)
    Swal.fire({ icon: 'error', title: 'Gagal', text: 'Tidak dapat mengambil data user.' })
  } finally {
    loading.value = false
  }
}

const handleImageChange = (event) => {
  const file = event.target.files?.[0]
  if (!file) return

  photo.value = file

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

  if (values.photo) {
    formData.append('photo', values.photo)
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
  } catch (error) {
    console.error(error)
    const message = error?.response?.data?.message || 'Proses simpan user gagal.'
    Swal.fire({ icon: 'error', title: 'Gagal', text: message })
  }
})

const editUser = (item) => {
  formMode.value = 'edit'
  editId.value = item.id
  setValues({
    name: item.name || '',
    username: item.username || '',
    email: item.email || '',
    password: '',
    password2: '',
    level: item.level || 'author',
    photo: null
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
      data: 'photo',
      orderable: false,
      searchable: false,
      className: 'text-center',
      render: (data) => {
        const src = getUserImage(data)
        return `<img src="${src}" alt="User" class="img-fluid" width="100" />`
      }
    },
    { data: 'name', className: 'text-center' },
    { data: 'username', className: 'text-center' },
    { data: 'email', className: 'text-center' },
    { data: 'level', className: 'text-center' },
    {
      data: null,
      orderable: false,
      searchable: false,
      className: 'text-center',
      render: (data, type, row) => `
        <button type="button" class="badge badge-warning mr-2 ml-2 btn-edit-user border-0" data-id="${row.id}">
          <i class="fas fa-edit"></i> Ubah
        </button>
        <button type="button" class="badge badge-danger mr-2 ml-2 btn-delete-user border-0" data-id="${row.id}">
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
  if (!$('#table-user').length) return

  if (tableInstance.value) return updateDataTable(data)

  $(document).off('click', '#table-user .btn-edit-user')
  $(document).off('click', '#table-user .btn-delete-user')

  tableInstance.value = $('#table-user').DataTable(getDataTablesOptions(data))

  $(document).on('click', '#table-user .btn-edit-user', function () {
    const id = $(this).data('id')
    const item = users.value.find((user) => user.id == id)
    if (item) editUser(item)
  })

  $(document).on('click', '#table-user .btn-delete-user', function () {
    const id = $(this).data('id')
    deleteUser(id)
  })
}

onMounted(async () => {
  initDataTable([])
  await fetchUsers()
})
</script>
