<template>
  <div class="login-box">
    <div class="card card-outline card-primary">
      <div class="card-header text-center">
        <BaseAuthLogo :src="logo" />
        <a href="/" class="h3">{{ brandName }}</a>
      </div>

      <div class="card-body">
        <p class="login-box-msg">{{ title }}</p>

        <slot />

        <div class="text-center mt-2">
          <a href="/auth/register">Belum memiliki akun?</a>
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
    default: 'Halaman Login'
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
