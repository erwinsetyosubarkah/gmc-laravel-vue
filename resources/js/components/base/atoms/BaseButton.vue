<template>
  <button
    :type="type"
    :class="['btn', variantClass, customClass]"
    :disabled="disabled"
    @click="$emit('click')"
  >
    <slot />
  </button>
</template>

<script setup>
import { computed } from 'vue'
const props = defineProps({
  type: { type: String, default: 'button' },
  variant: { type: String, default: 'primary' },
  disabled: { type: Boolean, default: false },
  customClass: { type: String, default: '' }
})

const variantClass = computed(() => (props.variant.startsWith('btn-') ? props.variant : `btn-${props.variant}`))
</script>

<style>
/* Use global CSS (Bootstrap) */
</style>
