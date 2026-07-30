<template>
  <div>
    <ContentLoader v-if="loading" speed="0.5" />
    <button type="button" class="btn btn-success mb-3" @click="openModal" v-show="!loading">
      <i class="fas fa-plus"></i> Tambah
    </button>

    <p v-if="loading" class="text-muted">Memuat data kategori...</p>

    <table id="table-category" class="table table-bordered table-striped" v-show="!loading">
      <thead>
        <tr>
          <th class="text-center">No</th>
          <th class="text-center">Kategori</th>
          <th class="text-center">Slug</th>
          <th class="text-center">Aksi</th>
        </tr>
      </thead>

      <tbody>

      </tbody>
    </table>

    <div v-if="showModal" class="modal fade show d-block" tabindex="-1" role="dialog" aria-modal="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ formMode === 'create' ? 'Tambah Kategori' : 'Ubah Kategori' }}</h5>
            <button type="button" class="close" @click="closeModal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>

          <form @submit.prevent="onSubmit">
            <div class="modal-body">
              <div class="form-group">
                <label for="category_name">Kategori</label>
                <input
                  v-model="category_name"
                  type="text"
                  class="form-control"
                  :class="{ 'is-invalid': errors.category_name }"
                  id="category_name"
                  placeholder="Masukan nama kategori..."
                  @input="autoSlug"
                />
                <div class="invalid-feedback">{{ errors.category_name }}</div>
              </div>

              <div class="form-group">
                <label for="category_slug">Slug</label>
                <input
                  v-model="category_slug"
                  type="text"
                  class="form-control"
                  :class="{ 'is-invalid': errors.category_slug }"
                  id="category_slug"
                  placeholder="Masukan slug..."
                />
                <div class="invalid-feedback">{{ errors.category_slug }}</div>
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
import { ContentLoader } from 'vue-content-loader';
import { useForm, useField } from 'vee-validate'
import { toTypedSchema } from '@vee-validate/yup'
import * as yup from 'yup'
import Swal from 'sweetalert2/dist/sweetalert2'
import apiClient from '@/services/api'

const loading = ref(false)
const categories = ref([])
const showModal = ref(false)
const formMode = ref('create')
const editId = ref(null)
const tableInstance = ref(null)

const schema = toTypedSchema(
  yup.object({
    category_name: yup.string().required('Nama kategori wajib diisi.').min(5, 'Nama kategori minimal 5 karakter.'),
    category_slug: yup.string().required('Slug wajib diisi.').min(5, 'Slug minimal 5 karakter.'),
  })
)

const { handleSubmit, errors, isSubmitting, resetForm, setValues } = useForm({
  validationSchema: schema,
  initialValues: {
    category_name: '',
    category_slug: '',
  }
})

const { value: category_name } = useField('category_name')
const { value: category_slug } = useField('category_slug')

const stringToSlug = (str) => {
  str = str.replace(/^\s+|\s+$/g, '').toLowerCase()
  const from = 'àáäâèéëêìíïîòóöôùúüûñç·/_,:;'
  const to   = 'aaaaeeeeiiiioooouuuunc------'
  for (let i = 0, l = from.length; i < l; i++) {
    str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i))
  }
  return str
    .replace(/[^a-z0-9 -]/g, '')
    .replace(/\s+/g, '-')
    .replace(/-+/g, '-')
}

const autoSlug = () => {
  category_slug.value = stringToSlug(category_name.value)
}

const openModal = () => {
  formMode.value = 'create'
  editId.value = null
  resetForm()
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  resetForm()
}

const editCategory = (item) => {
  formMode.value = 'edit'
  editId.value = item.id
  setValues({
    category_name: item.category_name,
    category_slug: item.category_slug,
  })
  showModal.value = true
}

const updateDataTable = (data = []) => {
  if (!tableInstance.value) {
    initDataTable(data)
    return
  }

  tableInstance.value.clear().rows.add(data).draw(false)
}

const fetchCategories = async () => {
    loading.value = true
    try {
        const response = await apiClient.get('/admin/category-all')
        const payload = response?.data

        if (payload?.status === 'success' && Array.isArray(payload.data)) {
            categories.value = payload.data
        } else if (Array.isArray(payload)) {
            categories.value = payload
        } else {
            categories.value = []
        }

        await nextTick()
        updateDataTable(categories.value)
    } catch (error) {
        console.error(error)
        Swal.fire({ icon: 'error', title: 'Gagal', text: 'Tidak dapat mengambil data kategori dari server.' })
    } finally {
        loading.value = false
    }
}



const onSubmit = handleSubmit(async (values) => {
  try {
    if (formMode.value === 'create') {
      await apiClient.post('/admin/category', values)
    } else {
      await apiClient.post(`/admin/category-edit/${editId.value}`, values)
    }

    await fetchCategories()
    closeModal()
    Swal.fire({
      icon: 'success',
      title: 'Berhasil',
      text: formMode.value === 'create' ? 'Kategori berhasil ditambah.' : 'Kategori berhasil diubah.'
    })
  } catch (error) {
    console.error(error)
    Swal.fire({
      icon: 'error',
      title: 'Gagal',
      text: error?.response?.data?.message || 'Proses simpan kategori gagal.'
    })
  }
})

const deleteCategory = async (id) => {
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
    const response = await apiClient.post(`/admin/category/${id}`, { _method: 'DELETE' })
    const payload = response?.data

    if (payload?.status === 'success') {
      await fetchCategories()
      Swal.fire({ icon: 'success', title: 'Berhasil', text: payload.message || 'Kategori berhasil dihapus.' })
    } else {
      Swal.fire({ icon: 'error', title: 'Gagal', text: payload?.message || 'Gagal menghapus kategori.' })
    }
  } catch (error) {
    console.error(error)
    Swal.fire({ icon: 'error', title: 'Gagal', text: error?.response?.data?.message || 'Gagal menghapus kategori.' })
  }
}

const getDataTablesOptions = (data) => ({
    data: data, // Masukkan data array langsung ke DataTables
    columns: [
        {
          data: null,
          orderable: false,
          searchable: false,
          className: 'text-center',
          render: function (data, type, row, meta) {
            return meta.row + 1
          }
        },
        { data: 'category_name', className: 'text-center' },
        { data: 'category_slug', className: 'text-center' },
        {
          data: null,
          orderable: false,
          searchable: false,
          className: 'text-center',
          // Render tombol aksi secara dinamis agar terikat dengan fungsi Vue Anda
          render: function (data, type, row) {
            return `
        <button type="button" class="badge badge-warning mr-2 ml-2 btn-edit-category border-0" data-id="${row.id}">
          <i class="fas fa-edit"></i> Ubah
        </button>
        <button type="button" class="badge badge-danger mr-2 ml-2 btn-delete-category border-0" data-id="${row.id}">
          <i class="fas fa-trash"></i> Hapus
        </button>`;
          }
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
    if (!$('#table-category').length) return

    if (tableInstance.value) {
        updateDataTable(data)
        return
    }

    tableInstance.value = $('#table-category').DataTable(getDataTablesOptions(data))
    $(document).off('click', '#table-category .btn-edit-category')
    $(document).off('click', '#table-category .btn-delete-category')

    $(document).on('click', '#table-category .btn-edit-category', function () {
        const id = $(this).data('id')
        const item = categories.value.find(c => c.id == id)
        if (item) editCategory(item)
    })

    $(document).on('click', '#table-category .btn-delete-category', function () {
        const id = $(this).data('id')
        deleteCategory(id)
    })
}

onMounted(async () => {
    initDataTable(categories.value)
    await fetchCategories()
})


</script>
