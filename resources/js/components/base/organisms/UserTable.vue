<template>
  <BaseTable :rows="rows" :columns="columns">
    <template #rows="{ rows }">
      <tr v-for="(item, index) in rows" :key="item.id">
        <td class="text-center">{{ index + 1 }}</td>
        <td class="text-center">
          <img
            :src="getUserImage(item.photo)"
            alt="User"
            class="img-fluid"
            width="100"
          />
        </td>
        <td class="text-center">{{ item.name }}</td>
        <td class="text-center">{{ item.username }}</td>
        <td class="text-center">{{ item.email }}</td>
        <td class="text-center">{{ item.level }}</td>
        <td class="text-center">
          <UserActionButtons :id="item.id" @edit="$emit('edit', $event)" @delete="$emit('delete', $event)" />
        </td>
      </tr>
    </template>
  </BaseTable>
</template>

<script setup>
import BaseTable from '../atoms/BaseTable.vue'
import UserActionButtons from '../molecules/UserActionButtons.vue'
const props = defineProps({ rows: { type: Array, default: () => [] } })
const emit = defineEmits(['edit', 'delete'])

const columns = [
  { key: 'index', label: 'No', className: 'text-center' },
  { key: 'photo', label: 'Foto', className: 'text-center' },
  { key: 'name', label: 'Nama', className: 'text-center' },
  { key: 'username', label: 'Username', className: 'text-center' },
  { key: 'email', label: 'Email', className: 'text-center' },
  { key: 'level', label: 'Level', className: 'text-center' },
  { key: 'actions', label: 'Aksi', className: 'text-center' }
]

const getUserImage = (image) => {
  if (!image) return '/img/no-image.svg'
  return image.startsWith('http') ? image : `/storage/${image.replace(/^\/+/, '')}`
}
</script>

<style>
/* rely on global Bootstrap styles */
</style>
