<template>
  <div class="register-box">
    <div class="card card-outline card-primary">
      <div class="card-header text-center">
        <div class="d-flex justify-content-center">
          <BaseAuthLogo :src="logo" />
        </div>
        <a href="/" class="h3">{{ brandName }}</a>
      </div>

      <div class="card-body">
        <p class="login-box-msg">{{ title }}</p>

        <slot />

        <div class="text-center mt-2">
          <a href="/auth/login">Sudah memiliki akun?</a>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import BaseAuthLogo from '../atoms/BaseAuthLogo.vue'

const props = defineProps({
  title: {
    type: String,
    default: 'Halaman Pendaftaran'
  },
  brandName: {
    type: String,
    default: ''
  },
  logo: {
    type: String,
    default: ''
  }
})
</script>
