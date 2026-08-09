<template>
  <form @submit.prevent="onSubmit" enctype="multipart/form-data">
    <div class="row g-3">
      <div class="col-12 form-group">
        <label for="client_name" class="form-label">Nama Klien</label>
        <BaseInput
          id="client_name"
          v-model="client_name"
          :error="errors.client_name"
          placeholder="Masukan nama klien..."
        />
      </div>

      <div class="col-12 form-group">
        <label for="company_name" class="form-label">Perusahaan</label>
        <BaseInput
          id="company_name"
          v-model="company_name"
          :error="errors.company_name"
          placeholder="Masukan nama perusahaan..."
        />
      </div>

      <div class="col-12 form-group">
        <label for="client_image" class="form-label">Foto</label>
        <BaseFileInput id="client_image" accept="image/*" @change="onFileChange" />
        <div v-if="errors.client_image" class="invalid-feedback d-block">{{ errors.client_image }}</div>
        <img
          v-if="previewImage"
          :src="previewImage"
          class="mb-2 mb-md-4 shadow-1-strong rounded mt-2"
          style="cursor: zoom-in;"
          width="100"
        />
      </div>

      <div class="col-12 form-group">
        <label for="client_address" class="form-label">Alamat</label>
        <BaseEditor
          id="client_address"
          v-model="client_address"
          :error="errors.client_address"
          placeholder="Masukan alamat..."
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
    client_name: yup.string().required('Nama klien wajib diisi.').min(3, 'Nama klien minimal 3 karakter.'),
    company_name: yup.string().required('Nama perusahaan wajib diisi.').min(3, 'Nama perusahaan minimal 3 karakter.'),
    client_image: yup.mixed().nullable(),
    client_address: yup.string().required('Alamat wajib diisi.'),
  })
)

const { handleSubmit, errors, setValues } = useForm({
  validationSchema: schema,
  initialValues: {
    client_name: '',
    company_name: '',
    client_image: null,
    client_address: '',
  }
})

const { value: client_name } = useField('client_name')
const { value: company_name } = useField('company_name')
const { value: client_image } = useField('client_image')
const { value: client_address } = useField('client_address')

watch(
  () => props.initialValues,
  (newValues) => {
    setValues({
      client_name: newValues.client_name || '',
      company_name: newValues.company_name || '',
      client_image: null,
      client_address: newValues.client_address || '',
    })
  },
  { immediate: true, deep: true }
)

const onFileChange = (file) => {
  client_image.value = file
  emit('change:file', file)
}

const onSubmit = handleSubmit((values) => {
  emit('submit', values)
})
</script>

<style>
/* use global Bootstrap styles */
</style>
