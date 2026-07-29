<template>
  <div>
    <ContentLoader v-if="loading" speed="0.5" />
    <button type="button" class="btn btn-success mb-3" @click="openModal" v-show="!loading">
      <i class="fas fa-plus"></i> Tambah
    </button>

    <p v-if="loading" class="text-muted">Memuat data produk...</p>

    <table id="table-myproduct" class="table table-bordered table-striped" v-show="!loading">
      <thead>
        <tr>
          <th class="text-center">No</th>
          <th class="text-center">Foto Produk</th>
          <th class="text-center">Nama Produk</th>
          <th class="text-center">Stok</th>
          <th class="text-center">Harga</th>
          <th class="text-center">Deskripsi Produk</th>
          <th class="text-center">Aksi</th>
        </tr>
      </thead>

        <tbody></tbody>
    </table>

    <div v-if="showModal" class="modal fade show d-block" tabindex="-1" role="dialog" aria-modal="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ formMode === 'create' ? 'Tambah Produk' : 'Ubah Produk' }}</h5>
            <button type="button" class="close" @click="closeModal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>

          <form @submit.prevent="onSubmit" enctype="multipart/form-data">
            <div class="modal-body">
              <div class="form-group">
                <label for="product_name">Nama Produk</label>
                <input
                  v-model="product_name"
                  type="text"
                  class="form-control"
                  :class="{ 'is-invalid': errors.product_name }"
                  id="product_name"
                  placeholder="Masukan nama produk..."
                />
                <div class="invalid-feedback">{{ errors.product_name }}</div>
              </div>

              <div class="form-group">
                <label for="stock">Stok</label>
                <input
                  v-model.number="stock"
                  type="number"
                  class="form-control"
                  :class="{ 'is-invalid': errors.stock }"
                  id="stock"
                />
                <div class="invalid-feedback">{{ errors.stock }}</div>
              </div>

              <div class="form-group">
                <label for="price">Harga</label>
                <input
                  v-model.number="price"
                  type="number"
                  class="form-control"
                  :class="{ 'is-invalid': errors.price }"
                  id="price"
                />
                <div class="invalid-feedback">{{ errors.price }}</div>
              </div>

              <div class="form-group">
                <label for="product_image">Foto Produk</label>
                <input
                  type="file"
                  class="form-control"
                  :class="{ 'is-invalid': errors.product_image }"
                  id="product_image"
                  @change="handleImageChange"
                />
                <div class="invalid-feedback">{{ errors.product_image }}</div>
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
                <label for="product_description">Deskripsi</label>
                <textarea
                  v-model="product_description"
                  class="form-control ckeditor"
                  :class="{ 'is-invalid': errors.product_description }"
                  id="product_description"
                ></textarea>
                <div class="invalid-feedback">{{ errors.product_description }}</div>
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
import { onMounted, onUnmounted, nextTick, ref, watch } from 'vue'
import { ContentLoader } from 'vue-content-loader';
import { useForm, useField } from 'vee-validate'
import { toTypedSchema } from '@vee-validate/yup'
import * as yup from 'yup'
import Swal from 'sweetalert2/dist/sweetalert2'
import apiClient from '@/services/api'

const props = defineProps({
  myproducts: {
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
const products = ref([])
const tableInstance = ref(null)

const schema = toTypedSchema(
  yup.object({
    product_name: yup.string().required('Nama produk wajib diisi.').min(3, 'Nama produk minimal 3 karakter.'),
    stock: yup.number().typeError('Stok harus berupa angka.').required('Stok wajib diisi.').min(0, 'Stok minimal 0.'),
    price: yup.number().typeError('Harga harus berupa angka.').required('Harga wajib diisi.').min(0, 'Harga minimal 0.'),
    product_image: yup.mixed().nullable(),
    product_description: yup.string().nullable(),
  })
)

const { handleSubmit, errors, isSubmitting, resetForm, setValues } = useForm({
  validationSchema: schema,
  initialValues: {
    product_name: '',
    stock: '',
    price: '',
    product_image: null,
    product_description: '',
  }
})

const { value: product_name } = useField('product_name')
const { value: stock } = useField('stock')
const { value: price } = useField('price')
const { value: product_image } = useField('product_image')
const { value: product_description } = useField('product_description')

const openModal = () => {
  formMode.value = 'create'
  editId.value = null
  resetForm()
  previewImage.value = ''
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  resetForm()
  previewImage.value = ''
}

const getProductImage = (image) => {
  if (!image) return defaultImage
  return image.startsWith('http') ? image : `/storage/${image.replace(/^\/+/, '')}`
}

const formatPrice = (value) => {
  if (value === null || value === undefined || value === '') return '0'
  return Number(value).toLocaleString('id-ID')
}

const truncateDescription = (value) => {
  if (!value) return ''
  const plainText = value.replace(/<[^>]*>/g, ' ')
  return plainText.length > 100 ? `${plainText.slice(0, 100)}...` : plainText
}

const updateDataTable = (data = []) => {
  if (!tableInstance.value) {
    initDataTable(data)
    return
  }

  tableInstance.value.clear().rows.add(data).draw(false)
}

const fetchProducts = async () => {
  loading.value = true
  try {
    const response = await apiClient.get('/admin/myproduct-all')
    const payload = response?.data

    if (payload?.status === 'success' && Array.isArray(payload.data)) {
      products.value = payload.data
    } else if (Array.isArray(payload)) {
      products.value = payload
    } else {
      products.value = []
    }

    await nextTick()
    updateDataTable(products.value)
  } catch (error) {
    console.error(error)
    Swal.fire({ icon: 'error', title: 'Gagal', text: 'Tidak dapat mengambil data produk dari server.' })
  } finally {
    loading.value = false
  }
}

const handleImageChange = (event) => {
  const file = event.target.files?.[0]
  if (!file) return

  product_image.value = file

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
  formData.append('product_name', values.product_name)
  formData.append('stock', values.stock)
  formData.append('price', values.price)
  formData.append('product_description', values.product_description || '')

  const fileInput = document.getElementById('product_image')
  if (fileInput?.files?.[0]) {
    formData.append('product_image', fileInput.files[0])
  }

  try {
    if (formMode.value === 'create') {
      await apiClient.post('/admin/myproduct', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
    } else {
      await apiClient.post(`/admin/myproduct-edit/${editId.value}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
    }

    await fetchProducts()
    closeModal()
    Swal.fire({
      icon: 'success',
      title: 'Berhasil',
      text: formMode.value === 'create' ? 'Produk berhasil ditambah.' : 'Produk berhasil diubah.'
    })
  } catch (error) {
    console.error(error)
    Swal.fire({
      icon: 'error',
      title: 'Gagal',
      text: error?.response?.data?.message || 'Proses simpan produk gagal.'
    })
  }
})

const editProduct = (item) => {
  formMode.value = 'edit'
  editId.value = item.id
  setValues({
    product_name: item.product_name || '',
    stock: item.stock ?? '',
    price: item.price ?? '',
    product_image: null,
    product_description: item.product_description || '',
  })
  previewImage.value = getProductImage(item.product_image)
  showModal.value = true
}

const deleteProduct = async (id) => {
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
    const response = await apiClient.delete(`/admin/myproduct/${id}`)
    const payload = response?.data

    if (payload?.status === 'success') {
      await fetchProducts()
      Swal.fire({ icon: 'success', title: 'Berhasil', text: payload.message || 'Produk berhasil dihapus.' })
    } else {
      Swal.fire({ icon: 'error', title: 'Gagal', text: payload?.message || 'Gagal menghapus produk.' })
    }
  } catch (error) {
    console.error(error)
    Swal.fire({ icon: 'error', title: 'Gagal', text: error?.response?.data?.message || 'Gagal menghapus produk.' })
  }
}

watch(
  () => props.myproducts,
  (value) => {
    if (Array.isArray(value) && value.length) {
      products.value = value
    }
  },
  { immediate: true }
)

const getDataTablesOptions = (data) => ({
  data: data,
  columns: [
    {
      data: null,
      orderable: false,
      searchable: false,
      className: 'text-center',
      render: (data, type, row, meta) => meta.row + 1
    },
    {
      data: 'product_image',
      orderable: false,
      searchable: false,
      className: 'text-center',
      render: (data) => {
        const src = getProductImage(data)
        return `<img src="${src}" alt="Produk" class="img-fluid" width="100" />`
      }
    },
    { data: 'product_name', className: 'text-center' },
    { data: 'stock', className: 'text-center' },
    {
      data: 'price',
      className: 'text-center',
      render: (data) => `Rp. ${formatPrice(data)}`
    },
    {
      data: 'product_description',
      className: 'text-center',
      render: (data) => truncateDescription(data)
    },
    {
      data: null,
      orderable: false,
      searchable: false,
      className: 'text-center',
      render: (data, type, row) => `
        <button type="button" class="badge badge-warning mr-2 ml-2 btn-edit-product border-0" data-id="${row.id}">
          <i class="fas fa-edit"></i> Ubah
        </button>
        <button type="button" class="badge badge-danger mr-2 ml-2 btn-delete-product border-0" data-id="${row.id}">
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
  if (!$('#table-myproduct').length) return

  if (tableInstance.value) return updateDataTable(data)

  $('#table-myproduct').off('click', '.btn-edit-product')
  $('#table-myproduct').off('click', '.btn-delete-product')

  tableInstance.value = $('#table-myproduct').DataTable(getDataTablesOptions(data))

  $('#table-myproduct').on('click', '.btn-edit-product', function () {
    const id = $(this).data('id')
    const item = products.value.find((product) => product.id == id)
    if (item) editProduct(item)
  })

  $('#table-myproduct').on('click', '.btn-delete-product', function () {
    const id = $(this).data('id')
    deleteProduct(id)
  })
}

watch(products, async (newProducts) => {
  if (tableInstance.value) {
    await nextTick()
    tableInstance.value.clear().rows.add(newProducts).draw()
  }
}, { flush: 'post' })

onMounted(async () => {
  initDataTable([])
  await fetchProducts()
})

</script>
