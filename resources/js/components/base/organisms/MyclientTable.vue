<template>
  <BaseTable :rows="rows" :columns="columns">
    <template #rows="{ rows }">
      <tr v-for="(item, index) in rows" :key="item.id">
        <td class="text-center">{{ index + 1 }}</td>
        <td class="text-center">
          <img
            :src="getClientImage(item.client_image)"
            alt="Klien"
            class="img-fluid"
            width="100"
          />
        </td>
        <td class="text-center">{{ item.client_name }}</td>
        <td class="text-center">{{ item.company_name }}</td>
        <td class="text-center">{{ truncateAddress(item.client_address) }}</td>
        <td class="text-center">
          <MyclientActionButtons :id="item.id" @edit="$emit('edit', $event)" @delete="$emit('delete', $event)" />
        </td>
      </tr>
    </template>
  </BaseTable>
</template>

<script setup>
import BaseTable from '../atoms/BaseTable.vue'
import MyclientActionButtons from '../molecules/MyclientActionButtons.vue'

const props = defineProps({ rows: { type: Array, default: () => [] } })
const emit = defineEmits(['edit', 'delete'])

const columns = [
  { key: 'index', label: 'No', className: 'text-center' },
  { key: 'client_image', label: 'Foto', className: 'text-center' },
  { key: 'client_name', label: 'Nama Klien', className: 'text-center' },
  { key: 'company_name', label: 'Perusahaan', className: 'text-center' },
  { key: 'client_address', label: 'Alamat', className: 'text-center' },
  { key: 'actions', label: 'Aksi', className: 'text-center' }
]

const getClientImage = (image) => {
  if (!image) return 'https://via.placeholder.com/100x100?text=No+Image'
  return image.startsWith('http') ? image : `/storage/${image.replace(/^\/+/, '')}`
}

const truncateAddress = (value) => {
  if (!value) return ''
  const plainText = value.replace(/<[^>]*>/g, ' ')
  return plainText.length > 100 ? `${plainText.slice(0, 100)}...` : plainText
}
</script>

<style>
/* rely on global styles */
</style>
