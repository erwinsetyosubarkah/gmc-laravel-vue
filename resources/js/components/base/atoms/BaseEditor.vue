<template>
  <div class="form-group">
    <label v-if="label" :for="id" class="form-label">{{ label }}</label>
    <textarea
      :id="id"
      ref="editorRef"
      class="form-control"
      :placeholder="placeholder"
    ></textarea>
    <div v-if="error" class="invalid-feedback d-block">{{ error }}</div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted, onBeforeUnmount } from 'vue'

const props = defineProps({
  modelValue: { type: String, default: '' },
  id: { type: String, default: '' },
  label: { type: String, default: '' },
  placeholder: { type: String, default: '' },
  error: { type: String, default: '' },
})

const emit = defineEmits(['update:modelValue'])

const editorRef = ref(null)
let editorInstance = null

const updateModelValue = () => {
  if (!editorInstance) return
  const data = editorInstance.getData()
  if (data !== props.modelValue) {
    emit('update:modelValue', data)
  }
}

watch(
  () => props.modelValue,
  (value) => {
    if (editorInstance && value !== editorInstance.getData()) {
      editorInstance.setData(value || '')
    }
  }
)

onMounted(async () => {
  if (window.ClassicEditor && editorRef.value) {
    editorInstance = await window.ClassicEditor.create(editorRef.value)
    editorInstance.model.document.on('change:data', updateModelValue)
    if (props.modelValue) {
      editorInstance.setData(props.modelValue)
    }
  }
})

onBeforeUnmount(async () => {
  if (editorInstance) {
    await editorInstance.destroy()
    editorInstance = null
  }
})
</script>

<style>
/* global Bootstrap styling */
</style>
