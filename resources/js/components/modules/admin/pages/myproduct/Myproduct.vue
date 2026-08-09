<template>
  <div>
    <ContentLoader v-if="loading" speed="0.5" />
    <template v-else>
      <MyproductPageShell @create="openModal">
        <MyproductTable :rows="products" @edit="editProduct" @delete="deleteProduct" />
      </MyproductPageShell>

      <BaseModal :show="showModal" :title="formMode === 'create' ? 'Tambah Produk' : 'Ubah Produk'" @close="closeModal">
        <MyproductForm
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
import MyproductPageShell from '../../../../base/organisms/MyproductPageShell.vue'
import MyproductTable from '../../../../base/organisms/MyproductTable.vue'
import MyproductForm from '../../../../base/molecules/MyproductForm.vue'

const props = defineProps({
  myproducts: {
    type: Array,
    default: () => []
  }
})

const showModal = ref(false)
const formMode = ref('create')
const editId = ref(null)
const loading = ref(false)
const products = ref([])
const previewImage = ref('')
const selectedFile = ref(null)

const formValues = reactive({
  product_name: '',
  stock: '',
  price: '',
  product_description: '',
})

const openModal = () => {
  formMode.value = 'create'
  editId.value = null
  selectedFile.value = null
  previewImage.value = ''
  Object.assign(formValues, {
    product_name: '',
    stock: '',
    price: '',
    product_description: '',
  })
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  selectedFile.value = null
  previewImage.value = ''
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
  } catch (error) {
    console.error(error)
    Swal.fire({ icon: 'error', title: 'Gagal', text: 'Tidak dapat mengambil data produk dari server.' })
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
  formData.append('product_name', values.product_name)
  formData.append('stock', values.stock)
  formData.append('price', values.price)
  formData.append('product_description', values.product_description || '')

  if (selectedFile.value) {
    formData.append('product_image', selectedFile.value)
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
}

const editProduct = (id) => {
  const item = products.value.find((product) => product.id == id)
  if (!item) return

  formMode.value = 'edit'
  editId.value = item.id
  selectedFile.value = null
  Object.assign(formValues, {
    product_name: item.product_name || '',
    stock: item.stock ?? '',
    price: item.price ?? '',
    product_description: item.product_description || '',
  })
  previewImage.value = item.product_image ? (item.product_image.startsWith('http') ? item.product_image : `/storage/${item.product_image.replace(/^\/+/, '')}`) : ''
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
    if (Array.isArray(value)) {
      products.value = value
    }
  },
  { immediate: true }
)

onMounted(async () => {
  await fetchProducts()
})
</script>
