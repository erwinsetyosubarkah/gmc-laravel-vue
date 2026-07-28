<template>
  <div class="register-box">
    <div class="card card-outline card-primary">
      <div class="card-header text-center">
        <div class="card-header text-center">
          <div class="d-flex justify-content-center">
            <img
              :src="'/storage/' + profile.club_logo"
              :alt="profile.club_name"
              class="brand-image img-circle elevation-3 d-block mb-2"
              style="opacity: .8"
              height="80"
              width="80"
            />
          </div>
          <a href="/" class="h3">{{ profile.club_name }}</a>
        </div>
      </div>
      <div class="card-body">
        <p class="login-box-msg">Halaman Pendaftaran</p>

        <form @submit.prevent="onSubmit" novalidate>
          <div class="input-group mb-3">
            <input
              id="name"
              type="text"
              v-model="name"
              :class="['form-control', { 'is-invalid': errors.name }]"
              placeholder="Nama Lengkap"
              autocomplete="name"
              required
            />
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-user"></span>
              </div>
            </div>
            <div class="invalid-feedback">{{ errors.name }}</div>
          </div>

          <div class="input-group mb-3">
            <input
              id="username"
              type="text"
              v-model="username"
              :class="['form-control', { 'is-invalid': errors.username }]"
              placeholder="Username"
              autocomplete="username"
              required
            />
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-user"></span>
              </div>
            </div>
            <div class="invalid-feedback">{{ errors.username }}</div>
          </div>

          <input type="hidden" name="level" value="author" />

          <div class="input-group mb-3">
            <input
              id="email"
              type="email"
              v-model="email"
              :class="['form-control', { 'is-invalid': errors.email }]"
              placeholder="Email"
              autocomplete="email"
              required
            />
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-envelope"></span>
              </div>
            </div>
            <div class="invalid-feedback">{{ errors.email }}</div>
          </div>

          <div class="input-group mb-3">
            <input
              id="password"
              type="password"
              v-model="password"
              :class="['form-control', { 'is-invalid': errors.password }]"
              placeholder="Password"
              autocomplete="new-password"
              required
            />
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-lock"></span>
              </div>
            </div>
            <div class="invalid-feedback">{{ errors.password }}</div>
          </div>

          <div class="input-group mb-3">
            <input
              id="password2"
              type="password"
              v-model="password2"
              :class="['form-control', { 'is-invalid': errors.password2 }]"
              placeholder="Konfirmasi Password"
              autocomplete="new-password"
              required
            />
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-lock"></span>
              </div>
            </div>
            <div class="invalid-feedback">{{ errors.password2 }}</div>
          </div>

          <div class="row">
            <div class="col-12">
              <button type="submit" class="btn btn-primary btn-block" :disabled="isSubmitting">
                <span
                  v-if="isSubmitting"
                  class="spinner-border spinner-border-sm me-2"
                  role="status"
                  aria-hidden="true"
                ></span>
                {{ isSubmitting ? 'Mohon tunggu...' : 'DAFTAR' }}
              </button>
            </div>
          </div>
        </form>

        <div class="text-center mt-2">
          <a href="/auth/login">Sudah memiliki akun?</a>
        </div>
      </div>
      <!-- /.form-box -->
    </div>
    <!-- /.card -->
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useStore } from 'vuex';
import { useForm, useField } from 'vee-validate';
import { toTypedSchema } from '@vee-validate/yup';
import * as yup from 'yup';
import Swal from 'sweetalert2/dist/sweetalert2';
import apiClient from '@/services/api';
import { sha256Hex } from '@/services/hash';

const store = useStore();
const profile = computed(() => store.state.profile);

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
      .oneOf([yup.ref('password')], 'Password dan konfirmasi harus sama'),
  })
);

const { handleSubmit, errors, isSubmitting } = useForm({
  validationSchema: schema,
});

const { value: name } = useField('name');
const { value: username } = useField('username');
const { value: email } = useField('email');
const { value: password } = useField('password');
const { value: password2 } = useField('password2');


const onSubmit = handleSubmit(async (values) => {
  try {
    const hashedPassword = await sha256Hex(values.password);

    const response = await apiClient.post('/auth/register', {
      name: values.name,
      username: values.username,
      email: values.email,
      level: 'author',
      password: hashedPassword,
      password2: hashedPassword,
    });

    if (response.data.status === 'success') {
      await Swal.fire({
        title: response.data.title || 'Berhasil!',
        text: response.data.message,
        icon: 'success',
      });
      window.location.href = '/auth/login';
    } else {
      await Swal.fire({
        title: response.data.title || 'Gagal!',
        text: response.data.message || 'Pendaftaran gagal.',
        icon: 'error',
      });
    }
  } catch (error) {
    let title = 'Gagal!';
    let message = 'Gagal mengirim data. Silakan coba lagi.';

    if (error.response?.data) {
      title = error.response.data.title || title;
      if (error.response.data.message) {
        message = error.response.data.message;
      } else if (error.response.data.errors) {
        message = Object.values(error.response.data.errors).flat().join('\n');
      }
    }

    await Swal.fire({
      title,
      text: message,
      icon: 'error',
    });
  }
});
</script>
