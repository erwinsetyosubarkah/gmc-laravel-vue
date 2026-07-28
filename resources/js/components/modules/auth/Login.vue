<template>
  <div class="login-box">
    <!-- /.login-logo -->
    <div class="card card-outline card-primary">
      <div class="card-header text-center">
        <div class="d-flex justify-content-center">
          <img
            :src="'/storage/' + profile.club_logo"
            alt=""
            class="brand-image img-circle elevation-3 d-block mb-2"
            style="opacity: 0.8"
            height="80"
            width="80"
          />
        </div>
        <a href="/" class="h3">{{ profile.club_name }}</a>
      </div>
      <div class="card-body">
        <p class="login-box-msg">Halaman Login</p>

        <form @submit.prevent="onSubmit">
          <div class="input-group mb-3">
            <input
              id="username"
              type="text"
              v-model="username"
              :class="['form-control', { 'is-invalid': errors.username }]"
              placeholder="Username"
              autofocus
              required
              value=""
            />
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-envelope"></span>
              </div>
            </div>
            <div class="invalid-feedback">
              {{ errors.username }}
            </div>
          </div>
          <div class="input-group mb-3">
            <input
              id="password"
              type="password"
              v-model="password"
              :class="['form-control', { 'is-invalid': errors.password }]"
              placeholder="Password"
              required
            />
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-lock"></span>
              </div>
            </div>
            <div class="invalid-feedback">
              {{ errors.password }}
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <!-- <button type="submit" class="btn btn-primary btn-block">LOGIN</button> -->
              <button
                type="submit"
                class="btn btn-primary w-100 fw-bold py-2"
                :disabled="isSubmitting"
              >
                <span
                  v-if="isSubmitting"
                  class="spinner-border spinner-border-sm me-2"
                  role="status"
                  aria-hidden="true"
                ></span>
                {{ isSubmitting ? "Mohon tunggu..." : "LOGIN" }}
              </button>
            </div>
            <!-- /.col -->
          </div>
        </form>

        <div class="text-center mt-2">
          <a href="/auth/register">Belum memiliki akun?</a>
        </div>
      </div>
      <!-- /.card-body -->
    </div>
    <!-- /.card -->
  </div>
  <!-- /.login-box -->
</template>

<script setup>
import { computed, ref } from "vue";
import { useStore } from "vuex";
import { useForm, useField } from "vee-validate";
import { toTypedSchema } from "@vee-validate/yup";
import * as yup from "yup";
import Swal from "sweetalert2/dist/sweetalert2";
import apiClient from "@/services/api";
import { sha256Hex } from "@/services/hash";

const store = useStore();

const profile = computed(() => store.state.profile);

// 1. Skema Validasi dengan Yup
const schema = toTypedSchema(
  yup.object({
    username: yup
      .string()
      .required("Username wajib diisi")
      .min(4, "Username minimal harus 4 karakter"),
    password: yup
      .string()
      .required("Password wajib diisi")
      .min(6, "Password minimal harus 6 karakter"),
  })
);

// 2. Inisialisasi Form
const { handleSubmit, errors, isSubmitting } = useForm({
  validationSchema: schema,
});

// 3. Bind Field ke variabel refs otomatis
const { value: username } = useField("username");
const { value: password } = useField("password");

const loading = ref(true);
const errorMessage = ref("");

// 4. Handle Submit fungsi langsung
const onSubmit = handleSubmit(async (values) => {
  try {
    const hashedPassword = await sha256Hex(values.password);
    const response = await apiClient.post("/auth/login", {
      username: values.username,
      password: hashedPassword,
    });
    if (response.data.status == "success") {
      const result = await Swal.fire({
        title: "Berhasil!",
        text: response.data.message,
        icon: "success",
      });

      if (result.isConfirmed) {
        window.location.href = '/admin/dashboard'
      }
    } else {
      Swal.fire({
        title: "Gagal!",
        html: response.data.message,
        icon: "error",
      });
    }
  } catch (error) {
    console.log(error);

    if (error.response && error.response.status === 422) {
      errorMessage.value = error.response.data.message || "Validasi Gagal.";
    } else {
      errorMessage.value = "Gagal mengambil data dari server.";
    }

    Swal.fire({
      title: "Gagal!",
      html: errorMessage.value,
      icon: "error",
    });
  } finally {
    loading.value = false;
  }
});
</script>
