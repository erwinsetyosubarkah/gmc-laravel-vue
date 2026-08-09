<template>
  <BaseTable :rows="rows" :columns="columns">
    <template #rows="{ rows }">
      <tr v-for="(item, index) in rows" :key="item.id">
        <td class="text-center">{{ index + 1 }}</td>
        <td class="text-center">
          <img
            :src="getEventImage(item.event_image)"
            alt="Event"
            class="img-fluid"
            width="100"
          />
        </td>
        <td class="text-center">{{ item.event_title }}</td>
        <td class="text-center">{{ formatDate(item.event_date) }}</td>
        <td class="text-center">{{ truncateText(item.event_description) }}</td>
        <td class="text-center">
          <EventActionButtons
            :id="item.id"
            @edit="$emit('edit', $event)"
            @delete="$emit('delete', $event)"
          />
        </td>
      </tr>
    </template>
  </BaseTable>
</template>

<script setup>
import BaseTable from '../atoms/BaseTable.vue'
import EventActionButtons from '../molecules/EventActionButtons.vue'

const props = defineProps({ rows: { type: Array, default: () => [] } })
const emit = defineEmits(['edit', 'delete'])

const columns = [
  { key: 'index', label: 'No', className: 'text-center' },
  { key: 'event_image', label: 'Gambar Event', className: 'text-center' },
  { key: 'event_title', label: 'Nama Event', className: 'text-center' },
  { key: 'event_date', label: 'Tanggal', className: 'text-center' },
  { key: 'event_description', label: 'Deskripsi', className: 'text-center' },
  { key: 'actions', label: 'Aksi', className: 'text-center' }
]

const getEventImage = (image) => {
  if (!image) return 'https://via.placeholder.com/100x100?text=No+Image'
  return image.startsWith('http') ? image : `/storage/${image.replace(/^\/+/, '')}`
}

const formatDate = (value) => {
  if (!value) return '-'
  const date = new Date(value)
  if (Number.isNaN(date.getTime())) return value
  return date.toLocaleString('id-ID', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const truncateText = (value) => {
  if (!value) return ''
  const plainText = value.replace(/<[^>]*>/g, ' ')
  return plainText.length > 90 ? `${plainText.slice(0, 90)}...` : plainText
}
</script>

<style>
/* table styling relies on Bootstrap */
</style>
