<template>
  <RegisterPageShell :brand-name="profile.club_name" :logo="profile.club_logo">
    <RegisterForm
      :model-value="{ name, username, email, password, password2 }"
      :errors="errors"
      :is-submitting="isSubmitting"
      @update:name="name = $event"
      @update:username="username = $event"
      @update:email="email = $event"
      @update:password="password = $event"
      @update:password2="password2 = $event"
      @submit="onSubmit"
    />
  </RegisterPageShell>
</template>

<script setup>
import { computed } from 'vue'
import { useStore } from 'vuex'
import { useForm, useField } from 'vee-validate'
import { toTypedSchema } from '@vee-validate/yup'
import * as yup from 'yup'
import Swal from 'sweetalert2/dist/sweetalert2'
import apiClient from '@/services/api'
import { sha256Hex } from '@/services/hash'
import RegisterPageShell from '../../base/organisms/RegisterPageShell.vue'
import RegisterForm from '../../base/molecules/RegisterForm.vue'

const store = useStore()
const profile = computed(() => store.state.profile)

const schema = toTypedSchema(
  yup.object({
    name: yup.string().required('Nama wajib diisi').max(255, 'Nama maksimal 255 karakter'),
    username: yup
      .string()
      .required('Username wajib diisi')
      .min(3, 'Username minimal harus 3 karakter')
      .max(255, 'Username maksimal 255 karakter'),
    email: yup.string().required('Email wajib diisi').email('Email tidak valid'),
    password: yup
      .string()
      .required('Password wajib diisi')
      .min(6, 'Password minimal harus 6 karakter'),
    password2: yup
      .string()
      .required('Konfirmasi password wajib diisi')
      .oneOf([yup.ref('password')], 'Password dan konfirmasi harus sama')
  })
)

const { handleSubmit, errors, isSubmitting } = useForm({
  validationSchema: schema
})

const { value: name } = useField('name')
const { value: username } = useField('username')
const { value: email } = useField('email')
const { value: password } = useField('password')
const { value: password2 } = useField('password2')

const onSubmit = handleSubmit(async (values) => {
  try {
    const hashedPassword = await sha256Hex(values.password)

    const response = await apiClient.post('/auth/register', {
      name: values.name,
      username: values.username,
      email: values.email,
      level: 'author',
      password: hashedPassword,
      password2: hashedPassword
    })

    if (response.data.status === 'success') {
      await Swal.fire({
        title: response.data.title || 'Berhasil!',
        text: response.data.message,
        icon: 'success'
      })
      window.location.href = '/auth/login'
    } else {
      await Swal.fire({
        title: response.data.title || 'Gagal!',
        text: response.data.message || 'Pendaftaran gagal.',
        icon: 'error'
      })
    }
  } catch (error) {
    let title = 'Gagal!'
    let message = 'Gagal mengirim data. Silakan coba lagi.'

    if (error.response?.data) {
      title = error.response.data.title || title
      if (error.response.data.message) {
        message = error.response.data.message
      } else if (error.response.data.errors) {
        message = Object.values(error.response.data.errors).flat().join('\n')
      }
    }

    await Swal.fire({
      title,
      text: message,
      icon: 'error'
    })
  }
})
</script>
