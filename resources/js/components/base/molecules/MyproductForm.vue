<template>
  <form @submit.prevent="onSubmit" enctype="multipart/form-data">
    <div class="row g-3">
      <div class="col-12 form-group">
        <label for="product_name" class="form-label">Nama Produk</label>
        <BaseInput
          id="product_name"
          v-model="product_name"
          :error="errors.product_name"
          placeholder="Masukan nama produk..."
        />
      </div>

      <div class="col-12 col-md-6 form-group">
        <label for="stock" class="form-label">Stok</label>
        <BaseInput
          id="stock"
          type="number"
          v-model.number="stock"
          :error="errors.stock"
          placeholder="Masukan stok..."
        />
      </div>

      <div class="col-12 col-md-6 form-group">
        <label for="price" class="form-label">Harga</label>
        <BaseInput
          id="price"
          type="number"
          v-model.number="price"
          :error="errors.price"
          placeholder="Masukan harga..."
        />
      </div>

      <div class="col-12 form-group">
        <label for="product_image" class="form-label">Foto Produk</label>
        <BaseFileInput id="product_image" accept="image/*" @change="onFileChange" />
        <div v-if="errors.product_image" class="invalid-feedback d-block">{{ errors.product_image }}</div>
        <img
          v-if="previewImage"
          :src="previewImage"
          class="mb-2 mb-md-4 shadow-1-strong rounded mt-2"
          style="cursor: zoom-in;"
          width="100"
        />
      </div>

      <div class="col-12 form-group">
        <label for="product_description" class="form-label">Deskripsi Produk</label>
        <BaseEditor
          id="product_description"
          v-model="product_description"
          :error="errors.product_description"
          placeholder="Masukan deskripsi produk..."
        />
      </div>
    </div>

    <div class="modal-footer">
    <BaseButton type="button" variant="secondary" @click="$emit('cancel')">Batal</BaseButton>
    <BaseButton type="submit" variant="primary" :disabled="isSubmitting">
        <template v-if="isSubmitting">
        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
        </template>
        {{ isSubmitting ? 'Menyimpan...' : 'Simpan' }}
    </BaseButton>
    </div>

  </form>
</template>

<script setup>
import { watch } from 'vue'
import { useForm, useField } from 'vee-validate'
import { toTypedSchema } from '@vee-validate/yup'
import * as yup from 'yup'
import BaseButton from '../atoms/BaseButton.vue'
import BaseInput from '../atoms/BaseInput.vue'
import BaseFileInput from '../atoms/BaseFileInput.vue'
import BaseEditor from '../atoms/BaseEditor.vue'

const props = defineProps({
  initialValues: { type: Object, default: () => ({}) },
  previewImage: { type: String, default: '' },
  isSubmitting: { type: Boolean, default: false },
})

const emit = defineEmits(['submit', 'change:file', 'cancel'])

const schema = toTypedSchema(
  yup.object({
    product_name: yup.string().required('Nama produk wajib diisi.').min(3, 'Nama produk minimal 3 karakter.'),
    stock: yup.number().typeError('Stok harus berupa angka.').required('Stok wajib diisi.').min(0, 'Stok minimal 0.'),
    price: yup.number().typeError('Harga harus berupa angka.').required('Harga wajib diisi.').min(0, 'Harga minimal 0.'),
    product_image: yup.mixed().nullable(),
    product_description: yup.string().nullable(),
  })
)

const { handleSubmit, errors, setValues } = useForm({
  validationSchema: schema,
  initialValues: {
    product_name: '',
    stock: '',
    price: '',
    product_image: null,
    product_description: '',
  },
})

const { value: product_name } = useField('product_name')
const { value: stock } = useField('stock')
const { value: price } = useField('price')
const { value: product_image } = useField('product_image')
const { value: product_description } = useField('product_description')

watch(
  () => props.initialValues,
  (newValues) => {
    setValues({
      product_name: newValues.product_name || '',
      stock: newValues.stock ?? '',
      price: newValues.price ?? '',
      product_image: null,
      product_description: newValues.product_description || '',
    })
  },
  { immediate: true, deep: true }
)

const onFileChange = (file) => {
  product_image.value = file
  emit('change:file', file)
}

const onSubmit = handleSubmit((values) => {
  emit('submit', values)
})
</script>

<style>
/* use global Bootstrap styles */
</style>
