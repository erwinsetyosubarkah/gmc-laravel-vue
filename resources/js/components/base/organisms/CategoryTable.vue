<template>
  <BaseTable :rows="rows" :columns="columns">
    <template #rows="{ rows }">
      <tr v-for="(row, index) in rows" :key="row.id">
        <td class="text-center">{{ index + 1 }}</td>
        <td class="text-center">{{ row.category_name }}</td>
        <td class="text-center">{{ row.category_slug }}</td>
        <td class="text-center">
          <CategoryActionButtons :id="row.id" @edit="emit('edit', $event)" @delete="emit('delete', $event)" />
        </td>
      </tr>
    </template>
  </BaseTable>
</template>

<script setup>
import BaseTable from '../atoms/BaseTable.vue'
import CategoryActionButtons from '../molecules/CategoryActionButtons.vue'

const props = defineProps({ rows: { type: Array, default: () => [] } })
const emit = defineEmits(['edit', 'delete'])

const columns = [
  { key: 'index', label: 'No', className: 'text-center' },
  { key: 'category_name', label: 'Kategori', className: 'text-center' },
  { key: 'category_slug', label: 'Slug', className: 'text-center' },
  { key: 'actions', label: 'Aksi', className: 'text-center' }
]
</script>

<style>
/* rely on global styles */
</style>
