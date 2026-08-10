<template>
  <nav aria-label="Page navigation">
    <ul class="pagination">
      <li
        v-for="(link, index) in links"
        :key="index"
        class="page-item"
        :class="{ 'active': link.active, 'disabled': !link.url }"
      >
        <a
          class="page-link"
          href="#"
          @click.prevent="$emit('change-page', link.url)"
          v-html="link.label"
        ></a>
      </li>
    </ul>
  </nav>
</template>

<script setup>
const props = defineProps({
  links: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['change-page'])
</script>
