<template>
  <form @submit.prevent="onSubmit">
    <div class="form-group mb-3">
      <label for="title" class="form-label">Judul</label>
      <BaseInput
        id="title"
        name="title"
        v-model="title"
        :error="errors.title"
        placeholder="Masukan Judul..."
      />
    </div>

    <div class="form-group mb-4">
      <BaseEditor
        id="content"
        label="Isi Visi dan Misi"
        v-model="content"
        :error="errors.content"
        placeholder="Masukan isi visi dan misi..."
      />
    </div>

    <div class="d-flex justify-content-start gap-2">
      <BaseButton type="submit" variant="primary" :disabled="isSubmitting">
        <template v-if="isSubmitting">
          <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
        </template>
        {{ isSubmitting ? 'Menyimpan...' : 'Simpan' }}
      </BaseButton>
    </div>
  </form>
</template>

<script setup>
import { watch } from 'vue'
import { useForm, useField } from 'vee-validate'
import { toTypedSchema } from '@vee-validate/yup'
import * as yup from 'yup'
import BaseInput from '../atoms/BaseInput.vue'
import BaseEditor from '../atoms/BaseEditor.vue'
import BaseButton from '../atoms/BaseButton.vue'

const props = defineProps({
  initialValues: { type: Object, default: () => ({ title: '', content: '' }) },
  isSubmitting: { type: Boolean, default: false },
})

const emit = defineEmits(['submit'])

const schema = toTypedSchema(
  yup.object({
    title: yup.string().required('Judul wajib diisi').min(5, 'Judul minimal 5 karakter'),
    content: yup.string().required('Isi visi dan misi wajib diisi'),
  })
)

const { handleSubmit, errors, setValues } = useForm({
  validationSchema: schema,
  initialValues: { title: '', content: '' },
})

const { value: title } = useField('title')
const { value: content } = useField('content')

watch(
  () => props.initialValues,
  (values) => {
    setValues({
      title: values.title || '',
      content: values.content || '',
    })
  },
  { deep: true, immediate: true }
)

const onSubmit = handleSubmit((values) => {
  emit('submit', values)
})
</script>

<style>
/* global Bootstrap styling */
</style>
