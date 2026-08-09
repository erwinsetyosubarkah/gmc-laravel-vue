<template>
  <BaseTable :rows="rows" :columns="columns">
    <template #rows="{ rows }">
      <tr v-for="(item, index) in rows" :key="item.id">
        <td class="text-center">{{ index + 1 }}</td>
        <td class="text-center">
          <img
            :src="getPostImage(item.post_image)"
            alt="Artikel"
            class="img-fluid"
            width="100"
          />
        </td>
        <td class="text-center">{{ item.title }}</td>
        <td class="text-center">{{ item.slug }}</td>
        <td class="text-center">{{ truncateText(item.body) }}</td>
        <td class="text-center">{{ item.category?.category_name || '-' }}</td>
        <td class="text-center">{{ item.user?.name || '-' }}</td>
        <td class="text-center">
          <PostActionButtons :id="item.id" @edit="$emit('edit', $event)" @delete="$emit('delete', $event)" />
        </td>
      </tr>
    </template>
  </BaseTable>
</template>

<script setup>
import BaseTable from '../atoms/BaseTable.vue'
import PostActionButtons from '../molecules/PostActionButtons.vue'

const props = defineProps({ rows: { type: Array, default: () => [] } })
const emit = defineEmits(['edit', 'delete'])

const columns = [
  { key: 'index', label: 'No', className: 'text-center' },
  { key: 'post_image', label: 'Gambar Artikel', className: 'text-center' },
  { key: 'title', label: 'Judul', className: 'text-center' },
  { key: 'slug', label: 'Slug', className: 'text-center' },
  { key: 'body', label: 'Artikel', className: 'text-center' },
  { key: 'category', label: 'Kategori', className: 'text-center' },
  { key: 'user', label: 'Author', className: 'text-center' },
  { key: 'actions', label: 'Aksi', className: 'text-center' }
]

const getPostImage = (image) => {
  if (!image) return 'https://via.placeholder.com/100x100?text=No+Image'
  return image.startsWith('http') ? image : `/storage/${image.replace(/^\/+/, '')}`
}

const truncateText = (value) => {
  if (!value) return ''
  const plainText = value.replace(/<[^>]*>/g, ' ')
  return plainText.length > 80 ? `${plainText.slice(0, 80)}...` : plainText
}
</script>

<style>
/* rely on global Bootstrap styles */
</style>
