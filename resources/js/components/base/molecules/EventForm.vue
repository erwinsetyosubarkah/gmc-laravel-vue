<template>
  <form @submit.prevent="onSubmit" enctype="multipart/form-data">
    <div class="row g-3">
      <div class="col-12 form-group">
        <label for="event_title" class="form-label">Nama Event</label>
        <BaseInput
          id="event_title"
          v-model="event_title"
          :error="errors.event_title"
          placeholder="Masukan nama event..."
        />
      </div>

      <div class="col-12 form-group">
        <label for="event_date" class="form-label">Tanggal</label>
        <BaseInput
          id="event_date"
          type="datetime-local"
          v-model="event_date"
          :error="errors.event_date"
        />
      </div>

      <div class="col-12 form-group">
        <label for="event_image" class="form-label">Gambar Event</label>
        <BaseFileInput id="event_image" accept="image/*" @change="onFileChange" />
        <div v-if="errors.event_image" class="invalid-feedback d-block">{{ errors.event_image }}</div>
        <img
          v-if="previewImage"
          :src="previewImage"
          class="mb-2 mb-md-4 shadow-1-strong rounded mt-2"
          style="cursor: zoom-in;"
          width="100"
        />
      </div>

      <div class="col-12 form-group">
        <label for="event_description" class="form-label">Deskripsi</label>
        <BaseEditor
          id="event_description"
          v-model="event_description"
          :error="errors.event_description"
          placeholder="Masukan deskripsi event..."
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
  isSubmitting: { type: Boolean, default: false }
})
const emit = defineEmits(['submit', 'change:file', 'cancel'])

const schema = toTypedSchema(
  yup.object({
    event_title: yup.string().required('Nama event wajib diisi.').min(3, 'Nama event minimal 3 karakter.'),
    event_date: yup.string().required('Tanggal wajib diisi.'),
    event_image: yup.mixed().nullable(),
    event_description: yup.string().nullable(),
  })
)

const { handleSubmit, errors, setValues } = useForm({
  validationSchema: schema,
  initialValues: {
    event_title: '',
    event_date: '',
    event_image: null,
    event_description: '',
  }
})

const { value: event_title } = useField('event_title')
const { value: event_date } = useField('event_date')
const { value: event_image } = useField('event_image')
const { value: event_description } = useField('event_description')

watch(
  () => props.initialValues,
  (newValues) => {
    setValues({
      event_title: newValues.event_title || '',
      event_date: newValues.event_date || '',
      event_image: null,
      event_description: newValues.event_description || '',
    })
  },
  { immediate: true, deep: true }
)

const onFileChange = (file) => {
  event_image.value = file
  emit('change:file', file)
}

const onSubmit = handleSubmit((values) => {
  emit('submit', values)
})
</script>

<style>
/* global Bootstrap styles */
</style>
