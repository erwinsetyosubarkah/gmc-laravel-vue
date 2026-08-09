<template>
  <div>
    <BaseButton
      type="button"
      customClass="badge badge-warning mr-2 ml-2 border-0"
      @click="$emit('edit', id)"
    >
      <i class="fas fa-edit"></i> Ubah
    </BaseButton>
    <BaseButton
      type="button"
      customClass="badge badge-danger mr-2 ml-2 border-0"
      @click="$emit('delete', id)"
    >
      <i class="fas fa-trash"></i> Hapus
    </BaseButton>
  </div>
</template>

<script setup>
import BaseButton from '../atoms/BaseButton.vue'
const props = defineProps({ id: { type: [String, Number], required: true } })
</script>

<style>
/* rely on Bootstrap styles */
</style>
