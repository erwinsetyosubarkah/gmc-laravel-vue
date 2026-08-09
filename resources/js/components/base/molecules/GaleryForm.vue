<template>
  <form @submit.prevent="onSubmit" enctype="multipart/form-data">
    <div class="row g-3">
      <div class="col-12 form-group">
        <label for="image_title" class="form-label">Judul</label>
        <BaseInput
          id="image_title"
          v-model="image_title"
          :error="errors.image_title"
          placeholder="Masukan judul foto..."
        />
      </div>

      <div class="col-12 form-group">
        <label for="galery_image" class="form-label">Foto</label>
        <BaseFileInput id="galery_image" accept="image/*" @change="onFileChange" />
        <div v-if="errors.galery_image" class="invalid-feedback d-block">{{ errors.galery_image }}</div>
        <img
          v-if="previewImage"
          :src="previewImage"
          class="mb-2 mb-md-4 shadow-1-strong rounded mt-2"
          style="cursor: zoom-in;"
          width="100"
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

const props = defineProps({
  initialValues: { type: Object, default: () => ({}) },
  previewImage: { type: String, default: '' },
  isSubmitting: { type: Boolean, default: false }
})

const emit = defineEmits(['submit', 'change:file', 'cancel'])

const schema = toTypedSchema(
  yup.object({
    image_title: yup.string().required('Judul wajib diisi.').min(3, 'Judul minimal 3 karakter.'),
    galery_image: yup.mixed().nullable()
  })
)

const { handleSubmit, errors, setValues } = useForm({
  validationSchema: schema,
  initialValues: {
    image_title: '',
    galery_image: null
  }
})

const { value: image_title } = useField('image_title')
const { value: galery_image } = useField('galery_image')

watch(
  () => props.initialValues,
  (newValues) => {
    setValues({
      image_title: newValues.image_title || '',
      galery_image: null
    })
  },
  { immediate: true, deep: true }
)

const onFileChange = (file) => {
  galery_image.value = file
  emit('change:file', file)
}

const onSubmit = handleSubmit((values) => {
  emit('submit', values)
})
</script>

<style>
/* use global Bootstrap styles */
</style>
