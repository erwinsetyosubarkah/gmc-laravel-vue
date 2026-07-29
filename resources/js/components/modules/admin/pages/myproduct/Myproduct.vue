<template>
  <div>
    <button type="button" class="btn btn-success mb-3" @click="openModal">
      <i class="fas fa-plus"></i> Tambah
    </button>

    <p v-if="loading" class="text-muted">Memuat data produk...</p>

    <table v-else id="table-myproduct" class="table table-bordered table-striped">
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

      <tbody>
        <tr v-for="(item, index) in products" :key="item.id ?? index">
          <td class="text-center">{{ index + 1 }}</td>
          <td class="text-center">
            <img
              :src="getProductImage(item.product_image)"
              :alt="item.product_name"
              class="img-fluid"
              width="100"
            />
          </td>
          <td class="text-center">{{ item.product_name }}</td>
          <td class="text-center">{{ item.stock }}</td>
          <td class="text-center">Rp. {{ formatPrice(item.price) }}</td>
          <td class="text-center">{{ truncateDescription(item.product_description) }}</td>
          <td class="text-center">
            <button type="button" class="badge badge-warning mr-2 ml-2 btn-edit border-0" @click="editProduct(item)">
              <i class="fas fa-edit"></i> Ubah
            </button>
            <button type="button" class="badge badge-danger mr-2 ml-2 btn-hapus border-0" @click="deleteProduct(item.id)">
              <i class="fas fa-trash"></i> Hapus
            </button>
          </td>
        </tr>
      </tbody>
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

          <form @submit.prevent="submitForm" enctype="multipart/form-data">
            <div class="modal-body">
              <div class="form-group">
                <label for="product_name">Nama Produk</label>
                <input v-model="form.product_name" type="text" class="form-control" id="product_name" placeholder="Masukan nama produk..." required />
              </div>

              <div class="form-group">
                <label for="stock">Stok</label>
                <input v-model.number="form.stock" type="number" class="form-control" id="stock" required />
              </div>

              <div class="form-group">
                <label for="price">Harga</label>
                <input v-model.number="form.price" type="number" class="form-control" id="price" required />
              </div>

              <div class="form-group">
                <label for="product_image">Foto Produk</label>
                <input type="file" class="form-control" id="product_image" @change="handleImageChange" />
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
                <textarea v-model="form.product_description" class="form-control ckeditor" id="product_description"></textarea>
              </div>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" @click="closeModal">Close</button>
              <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref, watch } from 'vue'
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
const previewImage = ref('')
const loading = ref(false)
const products = ref([])
const tableInstance = ref(null)
const form = ref({
  id: null,
  product_name: '',
  stock: '',
  price: '',
  product_image: '',
  product_description: ''
})

const resetForm = () => {
  form.value = {
    id: null,
    product_name: '',
    stock: '',
    price: '',
    product_image: '',
    product_description: ''
  }
  previewImage.value = ''
}

const openModal = () => {
  formMode.value = 'create'
  resetForm()
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  resetForm()
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

const fetchProducts = async () => {
  loading.value = true
  try {
    const response = await apiClient.get('/api/admin/myproducts')
    const payload = response?.data

    if (payload?.status === 'success' && Array.isArray(payload.data)) {
      products.value = payload.data
    } else if (Array.isArray(payload)) {
      products.value = payload
    } else {
      products.value = []
    }
  } catch (error) {
    console.error(error)
    Swal.fire({
      icon: 'error',
      title: 'Gagal',
      text: 'Tidak dapat mengambil data produk dari server.'
    })
  } finally {
    loading.value = false
  }
}

const dataURLtoFile = (dataUrl, filename) => {
  const arr = dataUrl.split(',')
  const mime = arr[0].match(/:(.*?);/)[1]
  const bstr = atob(arr[1])
  let n = bstr.length
  const u8arr = new Uint8Array(n)

  while (n--) {
    u8arr[n] = bstr.charCodeAt(n)
  }

  return new File([u8arr], filename, { type: mime })
}

const handleImageChange = (event) => {
  const file = event.target.files?.[0]
  if (!file) return

  const reader = new FileReader()
  reader.onload = () => {
    previewImage.value = reader.result
    form.value.product_image = reader.result
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

const submitForm = async () => {
  const formData = new FormData()
  formData.append('product_name', form.value.product_name)
  formData.append('stock', form.value.stock)
  formData.append('price', form.value.price)
  formData.append('product_description', form.value.product_description || '')

  if (form.value.product_image && typeof form.value.product_image === 'string' && form.value.product_image.startsWith('data:image')) {
    formData.append('product_image', dataURLtoFile(form.value.product_image, 'product-image.png'))
  }

  const fileInput = document.getElementById('product_image')
  if (fileInput?.files?.[0]) {
    formData.append('product_image', fileInput.files[0])
  }

  try {
    if (formMode.value === 'create') {
      await apiClient.post('/api/admin/myproducts', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
    } else {
      await apiClient.post(`/api/admin/myproducts/${form.value.id}`, formData, {
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
}

const editProduct = (item) => {
  formMode.value = 'edit'
  form.value = { ...item }
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
    const response = await apiClient.delete(`/api/admin/myproducts/${id}`)
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

onMounted(() => {
  fetchProducts()
  initDataTable()
})

const initDataTable = () => {
  if (tableInstance.value) {
    tableInstance.value.destroy()
  }

  setTimeout(() => {
    if ($('#table-myproduct').length) {
      tableInstance.value = $('#table-myproduct').DataTable({
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
    }
  }, 0)
}

watch(products, () => {
  setTimeout(() => {
    if (tableInstance.value) {
      tableInstance.value.destroy()
    }

    if ($('#table-myproduct').length) {
      tableInstance.value = $('#table-myproduct').DataTable({
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
    }
  }, 0)
})
</script>
