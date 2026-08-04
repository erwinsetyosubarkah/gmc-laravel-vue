<template>
  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th v-for="column in columns" :key="column.key" :class="column.className">{{ column.label }}</th>
      </tr>
    </thead>
    <tbody>
      <slot name="rows" :rows="rows" />
    </tbody>
  </table>
</template>

<script setup>
const props = defineProps({
  rows: { type: Array, default: () => [] },
  columns: { type: Array, required: true }
})
</script>

<style>
/* Base table inherits global Bootstrap table styles */
</style>
