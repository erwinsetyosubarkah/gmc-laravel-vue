<template>
  <form @submit.prevent="onSubmit" enctype="multipart/form-data">
    <div class="row g-3">
      <div class="col-12 form-group">
        <label for="name" class="form-label">Nama</label>
        <BaseInput
          id="name"
          v-model="name"
          :error="errors.name"
          placeholder="Masukan nama..."
        />
      </div>

      <div class="col-12 form-group">
        <label for="username" class="form-label">Username</label>
        <BaseInput
          id="username"
          v-model="username"
          :error="errors.username"
          placeholder="Masukan username..."
        />
      </div>

      <div class="col-12 form-group">
        <label for="email" class="form-label">Email</label>
        <BaseInput
          id="email"
          type="email"
          v-model="email"
          :error="errors.email"
          placeholder="Masukan email..."
        />
      </div>

      <div class="col-12 col-md-6 form-group">
        <label for="password" class="form-label">Password</label>
        <BaseInput
          id="password"
          type="password"
          v-model="password"
          :error="errors.password"
          placeholder="Masukan password..."
        />
      </div>

      <div class="col-12 col-md-6 form-group">
        <label for="password2" class="form-label">Konfirmasi Password</label>
        <BaseInput
          id="password2"
          type="password"
          v-model="password2"
          :error="errors.password2"
          placeholder="Konfirmasi password..."
        />
      </div>

      <div class="col-12 form-group">
        <label for="level" class="form-label">Level</label>
        <BaseSelect
          id="level"
          v-model="level"
          :options="levelOptions"
          :error="errors.level"
          placeholder="Pilih level"
        />
      </div>

      <div class="col-12 form-group">
        <label for="photo" class="form-label">Foto</label>
        <BaseFileInput id="photo" accept="image/*" @change="onFileChange" />
        <div v-if="errors.photo" class="invalid-feedback d-block">{{ errors.photo }}</div>
        <img
          v-if="previewImage"
          :src="previewImage"
          class="mb-2 mb-md-4 shadow-1-strong rounded mt-2"
          style="cursor: zoom-in;"
          width="100"
          @click="$emit('zoom')"
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
import BaseSelect from '../atoms/BaseSelect.vue'

const props = defineProps({
  initialValues: { type: Object, default: () => ({}) },
  previewImage: { type: String, default: '' },
  isSubmitting: { type: Boolean, default: false },
  isCreateMode: { type: Boolean, default: true }
})

const emit = defineEmits(['submit', 'change:file', 'cancel', 'zoom'])

const schema = toTypedSchema(
  yup.object({
    name: yup.string().required('Nama wajib diisi.').min(3, 'Nama minimal 3 karakter.').max(255, 'Nama maksimal 255 karakter.'),
    username: yup.string().required('Username wajib diisi.').min(3, 'Username minimal 3 karakter.').max(255, 'Username maksimal 255 karakter.'),
    email: yup.string().required('Email wajib diisi.').email('Format email tidak valid.'),
    level: yup.string().required('Level wajib dipilih.'),
    password: yup.string().when([], {
      is: () => props.isCreateMode,
      then: (schema) => schema.required('Password wajib diisi.').min(5, 'Password minimal 5 karakter.'),
      otherwise: (schema) => schema.notRequired()
    }),
    password2: yup.string().when([], {
      is: () => props.isCreateMode,
      then: (schema) => schema.required('Konfirmasi password wajib diisi.').min(5, 'Konfirmasi password minimal 5 karakter.').oneOf([yup.ref('password')], 'Konfirmasi password tidak sama.'),
      otherwise: (schema) => schema.notRequired()
    }),
    photo: yup.mixed().nullable()
  })
)

const { handleSubmit, errors, setValues } = useForm({
  validationSchema: schema,
  initialValues: {
    name: '',
    username: '',
    email: '',
    password: '',
    password2: '',
    level: 'author',
    photo: null
  }
})

const { value: name } = useField('name')
const { value: username } = useField('username')
const { value: email } = useField('email')
const { value: password } = useField('password')
const { value: password2 } = useField('password2')
const { value: level } = useField('level')
const { value: photo } = useField('photo')

const levelOptions = [
  { value: 'admin', label: 'Admin' },
  { value: 'author', label: 'Author' }
]

watch(
  () => props.initialValues,
  (newValues) => {
    setValues({
      name: newValues.name || '',
      username: newValues.username || '',
      email: newValues.email || '',
      password: '',
      password2: '',
      level: newValues.level || 'author',
      photo: null
    })
  },
  { immediate: true, deep: true }
)

const onFileChange = (file) => {
  photo.value = file
  emit('change:file', file)
}

const onSubmit = handleSubmit(async (values) => {
  await new Promise((resolve, reject) => {
    emit('submit', values, { resolve, reject })
  })
})
</script>

<style>
/* rely on global Bootstrap styles */
</style>
