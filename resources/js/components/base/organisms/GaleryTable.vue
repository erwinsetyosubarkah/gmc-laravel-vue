<template>
  <BaseTable :rows="rows" :columns="columns">
    <template #rows="{ rows }">
      <tr v-for="(item, index) in rows" :key="item.id">
        <td class="text-center">{{ index + 1 }}</td>
        <td class="text-center">
          <img
            :src="getGaleryImage(item.galery_image)"
            alt="Galeri"
            class="img-fluid"
            width="100"
          />
        </td>
        <td class="text-center">{{ item.image_title }}</td>
        <td class="text-center">
          <GaleryActionButtons :id="item.id" @edit="$emit('edit', $event)" @delete="$emit('delete', $event)" />
        </td>
      </tr>
    </template>
  </BaseTable>
</template>

<script setup>
import BaseTable from '../atoms/BaseTable.vue'
import GaleryActionButtons from '../molecules/GaleryActionButtons.vue'

const props = defineProps({ rows: { type: Array, default: () => [] } })
const emit = defineEmits(['edit', 'delete'])

const columns = [
  { key: 'index', label: 'No', className: 'text-center' },
  { key: 'galery_image', label: 'Foto', className: 'text-center' },
  { key: 'image_title', label: 'Judul', className: 'text-center' },
  { key: 'actions', label: 'Aksi', className: 'text-center' }
]

const getGaleryImage = (image) => {
  if (!image) return 'https://via.placeholder.com/100x100?text=No+Image'
  return image.startsWith('http') ? image : `/storage/${image.replace(/^\/+/, '')}`
}
</script>

<style>
/* rely on global Bootstrap styles */
</style>
