<template>
  <div>
    <div class="mb-3">
      <BaseButton type="button" variant="success" @click="$emit('create')">
        <i class="fas fa-plus"></i> Tambah User
      </BaseButton>
    </div>
    <slot />
  </div>
</template>

<script setup>
import BaseButton from '../atoms/BaseButton.vue'
const emit = defineEmits(['create'])
</script>

<style>
/* layout wrapper */
</style>
