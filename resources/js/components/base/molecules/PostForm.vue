<template>
  <form @submit.prevent="onSubmit" enctype="multipart/form-data">
    <div class="row g-3">
      <div class="col-12 form-group">
        <label for="title" class="form-label">Judul</label>
        <BaseInput
          id="title"
          v-model="title"
          :error="errors.title"
          placeholder="Masukan judul artikel..."
        />
      </div>

      <div class="col-12 form-group">
        <label for="slug" class="form-label">Slug</label>
        <BaseInput
          id="slug"
          v-model="slug"
          :error="errors.slug"
          placeholder="Masukan slug..."
        />
      </div>

      <div class="col-12 form-group">
        <label for="category_id" class="form-label">Kategori</label>
        <select
          id="category_id"
          v-model="category_id"
          class="form-control"
          :class="{ 'is-invalid': errors.category_id }"
        >
          <option value="" disabled>Pilih kategori</option>
          <option v-for="category in categories" :key="category.id" :value="category.id">
            {{ category.category_name }}
          </option>
        </select>
        <div v-if="errors.category_id" class="invalid-feedback d-block">{{ errors.category_id }}</div>
      </div>

      <div class="col-12 form-group">
        <label for="post_image" class="form-label">Foto</label>
        <BaseFileInput id="post_image" accept="image/*" @change="onFileChange" />
        <div v-if="errors.post_image" class="invalid-feedback d-block">{{ errors.post_image }}</div>
        <img
          v-if="previewImage"
          :src="previewImage"
          class="mb-2 mb-md-4 shadow-1-strong rounded mt-2"
          style="cursor: zoom-in;"
          width="100"
          @click="zoomImg(previewImage)"
        />
      </div>

      <div class="col-12 form-group">
        <label for="body" class="form-label">Artikel</label>
        <BaseEditor
          id="body"
          v-model="body"
          :error="errors.body"
          placeholder="Masukan artikel..."
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
  categories: { type: Array, default: () => [] },
  previewImage: { type: String, default: '' },
  isSubmitting: { type: Boolean, default: false }
})

const emit = defineEmits(['submit', 'change:file', 'cancel'])

const schema = toTypedSchema(
  yup.object({
    title: yup.string().required('Judul wajib diisi.').min(3, 'Judul minimal 3 karakter.'),
    slug: yup.string().required('Slug wajib diisi.').min(3, 'Slug minimal 3 karakter.'),
    category_id: yup.mixed().required('Kategori wajib dipilih.'),
    body: yup.string().required('Artikel wajib diisi.'),
    post_image: yup.mixed().nullable(),
  })
)

const { handleSubmit, errors, setValues } = useForm({
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

watch(title, (value) => {
  slug.value = generateSlug(value)
})

watch(
  () => props.initialValues,
  (newValues) => {
    setValues({
      title: newValues.title || '',
      slug: newValues.slug || '',
      category_id: newValues.category_id ?? '',
      post_image: null,
      body: newValues.body || '',
    })
  },
  { immediate: true, deep: true }
)

const onFileChange = (file) => {
  post_image.value = file
  emit('change:file', file)
}

const onSubmit = handleSubmit((values) => {
  emit('submit', values)
})

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
</script>

<style>
/* use global Bootstrap styles */
</style>
