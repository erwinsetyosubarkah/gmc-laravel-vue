<template>
  <div>
    <ContentLoader v-if="loading" speed="0.5" />
    <template v-else>
      <CategoryPageShell @create="openModal">
        <CategoryTable :rows="categories" @edit="onEditRequested" @delete="onDeleteRequested" />
      </CategoryPageShell>

      <BaseModal :show="showModal" :title="formMode === 'create' ? 'Tambah Kategori' : 'Ubah Kategori'" @close="closeModal">
        <CategoryForm :initialValues="formValues" :isSubmitting="isSubmitting" @submit="onSubmit" @cancel="closeModal" />
      </BaseModal>
    </template>
  </div>
</template>

<script setup>
import { onMounted, ref, reactive } from 'vue'
import { ContentLoader } from 'vue-content-loader';
import Swal from 'sweetalert2/dist/sweetalert2'
import apiClient from '@/services/api'
import BaseModal from '../../../../base/atoms/BaseModal.vue'
import CategoryForm from '../../../../base/molecules/CategoryForm.vue'
import CategoryTable from '../../../../base/organisms/CategoryTable.vue'
import CategoryPageShell from '../../../../base/organisms/CategoryPageShell.vue'

const loading = ref(false)
const categories = ref([])
const showModal = ref(false)
const formMode = ref('create')
const editId = ref(null)
const formValues = reactive({ category_name: '', category_slug: '' })
const isSubmitting = ref(false)

const openModal = () => {
  formMode.value = 'create'
  editId.value = null
  formValues.category_name = ''
  formValues.category_slug = ''
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
}

const editCategory = (item) => {
  formMode.value = 'edit'
  editId.value = item.id
  formValues.category_name = item.category_name
  formValues.category_slug = item.category_slug
  showModal.value = true
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
  } catch (error) {
    console.error(error)
    Swal.fire({ icon: 'error', title: 'Gagal', text: 'Tidak dapat mengambil data kategori dari server.' })
  } finally {
    loading.value = false
  }
}



const onSubmit = async (values) => {
  isSubmitting.value = true
  try {
    if (formMode.value === 'create') {
      await apiClient.post('/admin/category', values)
    } else {
      await apiClient.post(`/admin/category-edit/${editId.value}`, values)
    }

    await fetchCategories()
    closeModal()
    Swal.fire({ icon: 'success', title: 'Berhasil', text: formMode.value === 'create' ? 'Kategori berhasil ditambah.' : 'Kategori berhasil diubah.' })
  } catch (error) {
    console.error(error)
    Swal.fire({ icon: 'error', title: 'Gagal', text: error?.response?.data?.message || 'Proses simpan kategori gagal.' })
  } finally {
    isSubmitting.value = false
  }
}

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

onMounted(async () => {
  await fetchCategories()
})

const onEditRequested = (id) => {
  const item = categories.value.find(c => c.id == id)
  if (item) editCategory(item)
}

const onDeleteRequested = (id) => {
  deleteCategory(id)
}
</script>
<style>
/* use global CSS as requested */
</style>
