<template>
  <div v-if="show" class="modal fade show d-block" tabindex="-1" role="dialog" aria-modal="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">{{ title }}</h5>
          <BaseButton type="button" variant="secondary" customClass="close" @click="$emit('close')" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </BaseButton>
        </div>
        <div class="modal-body">
          <slot />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import BaseButton from './BaseButton.vue'

const props = defineProps({
  show: { type: Boolean, default: false },
  title: { type: String, default: '' }
})
</script>

<style>
/* Rely on Bootstrap modal styling */
</style>
