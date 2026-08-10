<template>
  <LoginPageShell :brand-name="profile.club_name" :logo="profile.club_logo">
    <LoginForm
      :model-value="{ username, password }"
      :errors="errors"
      :is-submitting="isSubmitting"
      @update:username="username = $event"
      @update:password="password = $event"
      @submit="onSubmit"
    />
  </LoginPageShell>
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
import LoginPageShell from "../../base/organisms/LoginPageShell.vue";
import LoginForm from "../../base/molecules/LoginForm.vue";

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
