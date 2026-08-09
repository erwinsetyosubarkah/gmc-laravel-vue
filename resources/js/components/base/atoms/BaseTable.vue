<template>
  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th v-for="column in columns" :key="column.key" :class="column.className">{{ column.label }}</th>
      </tr>
    </thead>
    <tbody>
      <template v-if="rows.length">
        <slot name="rows" :rows="rows" />
      </template>
      <tr v-else>
        <td :colspan="columns.length" class="text-center">tidak ada data ditemukan di tabel</td>
      </tr>
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
