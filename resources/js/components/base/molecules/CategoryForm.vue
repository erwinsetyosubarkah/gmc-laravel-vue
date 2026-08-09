<template>
  <form @submit.prevent="onSubmit">
    <div class="form-group">
      <label for="category_name">Kategori</label>
      <BaseInput id="category_name" v-model="values.category_name" :error="errors.category_name" placeholder="Masukan nama kategori..." @input="autoSlug" />
    </div>

    <div class="form-group">
      <label for="category_slug">Slug</label>
      <BaseInput id="category_slug" v-model="values.category_slug" :error="errors.category_slug" placeholder="Masukan slug..." />
    </div>

    <div class="modal-footer">
      <BaseButton type="button" variant="secondary" @click="$emit('cancel')">Batal</BaseButton>
      <BaseButton type="submit" :disabled="isSubmitting">{{ isSubmitting ? 'Menyimpan...' : 'Simpan' }}</BaseButton>
    </div>
  </form>
</template>

<script setup>
import { reactive, watch } from 'vue'
import BaseInput from '../atoms/BaseInput.vue'
import BaseButton from '../atoms/BaseButton.vue'

const props = defineProps({
  initialValues: { type: Object, default: () => ({ category_name: '', category_slug: '' }) },
  isSubmitting: { type: Boolean, default: false }
})
const emit = defineEmits(['submit', 'cancel'])

const values = reactive({ category_name: props.initialValues.category_name || '', category_slug: props.initialValues.category_slug || '' })
const errors = reactive({ category_name: '', category_slug: '' })

watch(() => props.initialValues, (nv) => {
  values.category_name = nv.category_name || ''
  values.category_slug = nv.category_slug || ''
})

const stringToSlug = (str) => {
  str = str.replace(/^\s+|\s+$/g, '').toLowerCase()
  const from = 'àáäâèéëêìíïîòóöôùúüûñç·/_,:;'
  const to   = 'aaaaeeeeiiiioooouuuunc------'
  for (let i = 0, l = from.length; i < l; i++) {
    str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i))
  }
  return str
    .replace(/[^a-z0-9 -]/g, '')
    .replace(/\s+/g, '-')
    .replace(/-+/g, '-')
}

const autoSlug = () => {
  values.category_slug = stringToSlug(values.category_name)
}

const onSubmit = () => {
  // simple client-side validation
  errors.category_name = values.category_name.length < 5 ? 'Nama kategori minimal 5 karakter.' : ''
  errors.category_slug = values.category_slug.length < 5 ? 'Slug minimal 5 karakter.' : ''

  if (errors.category_name || errors.category_slug) return
  emit('submit', { category_name: values.category_name, category_slug: values.category_slug })
}
</script>

<style>
/* Global styling */
</style>
