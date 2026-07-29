<template>
  <div class="card p-3">
    <form @submit.prevent="submitForm" enctype="multipart/form-data">
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
          width="100"
          style="cursor: zoom-in;"
          @click="zoomImg(previewImage)"
        />
        <input v-if="form.old_product_image" type="hidden" name="old_product_image" :value="form.old_product_image" />
      </div>

      <div class="form-group">
        <label for="product_description">Deskripsi</label>
        <textarea v-model="form.product_description" class="form-control ckeditor" id="product_description"></textarea>
      </div>

      <div class="form-group">
        <button type="submit" class="btn btn-primary">Ubah</button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import Swal from 'sweetalert2/dist/sweetalert2'
import apiClient from '@/services/api'

const route = useRoute()
const router = useRouter()

const form = ref({
  id: null,
  product_name: '',
  stock: '',
  price: '',
  product_image: '',
  old_product_image: '',
  product_description: ''
})

const previewImage = ref('')

const loadProduct = async () => {
  try {
    const response = await apiClient.get(`/api/admin/myproducts/${route.params.id}`)
    const payload = response?.data

    if (payload?.status === 'success' && payload.data) {
      form.value = {
        id: payload.data.id,
        product_name: payload.data.product_name || '',
        stock: payload.data.stock || '',
        price: payload.data.price || '',
        product_image: payload.data.product_image || '',
        old_product_image: payload.data.product_image || '',
        product_description: payload.data.product_description || ''
      }

      previewImage.value = payload.data.product_image ? `/storage/${payload.data.product_image.replace(/^\/+/, '')}` : ''
    }
  } catch (error) {
    console.error(error)
    Swal.fire({ icon: 'error', title: 'Gagal', text: 'Tidak dapat memuat data produk.' })
  }
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
  formData.append('old_product_image', form.value.old_product_image || '')

  const fileInput = document.getElementById('product_image')
  if (fileInput?.files?.[0]) {
    formData.append('product_image', fileInput.files[0])
  }

  try {
    const response = await apiClient.post(`/api/admin/myproducts/${form.value.id}`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })

    if (response?.data?.status === 'success') {
      Swal.fire({ icon: 'success', title: 'Berhasil', text: response.data.message || 'Produk berhasil diubah.' })
      router.push('/admin/myproduct')
    } else {
      Swal.fire({ icon: 'error', title: 'Gagal', text: response?.data?.message || 'Gagal mengubah produk.' })
    }
  } catch (error) {
    console.error(error)
    Swal.fire({ icon: 'error', title: 'Gagal', text: error?.response?.data?.message || 'Gagal mengubah produk.' })
  }
}

onMounted(() => {
  loadProduct()
})
</script>
