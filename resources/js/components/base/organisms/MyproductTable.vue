<template>
  <BaseTable :rows="rows" :columns="columns">
    <template #rows="{ rows }">
      <tr v-for="(item, index) in rows" :key="item.id">
        <td class="text-center">{{ index + 1 }}</td>
        <td class="text-center">
          <img
            :src="getProductImage(item.product_image)"
            alt="Produk"
            class="img-fluid"
            width="100"
          />
        </td>
        <td class="text-center">{{ item.product_name }}</td>
        <td class="text-center">{{ item.stock }}</td>
        <td class="text-center">Rp. {{ formatPrice(item.price) }}</td>
        <td class="text-center">{{ truncateDescription(item.product_description) }}</td>
        <td class="text-center">
          <MyproductActionButtons :id="item.id" @edit="$emit('edit', $event)" @delete="$emit('delete', $event)" />
        </td>
      </tr>
    </template>
  </BaseTable>
</template>

<script setup>
import BaseTable from '../atoms/BaseTable.vue'
import MyproductActionButtons from '../molecules/MyproductActionButtons.vue'

const props = defineProps({ rows: { type: Array, default: () => [] } })
const emit = defineEmits(['edit', 'delete'])

const columns = [
  { key: 'index', label: 'No', className: 'text-center' },
  { key: 'product_image', label: 'Foto Produk', className: 'text-center' },
  { key: 'product_name', label: 'Nama Produk', className: 'text-center' },
  { key: 'stock', label: 'Stok', className: 'text-center' },
  { key: 'price', label: 'Harga', className: 'text-center' },
  { key: 'product_description', label: 'Deskripsi Produk', className: 'text-center' },
  { key: 'actions', label: 'Aksi', className: 'text-center' }
]

const getProductImage = (image) => {
  if (!image) return 'https://via.placeholder.com/100x100?text=No+Image'
  return image.startsWith('http') ? image : `/storage/${image.replace(/^\/+/, '')}`
}

const formatPrice = (value) => {
  if (value === null || value === undefined || value === '') return '0'
  return Number(value).toLocaleString('id-ID')
}

const truncateDescription = (value) => {
  if (!value) return ''
  const plainText = value.replace(/<[^>]*>/g, ' ')
  return plainText.length > 100 ? `${plainText.slice(0, 100)}...` : plainText
}
</script>

<style>
/* rely on global Bootstrap styles */
</style>
