<template>
  <form @submit.prevent="onSubmit" enctype="multipart/form-data">
    <div class="row g-3">
      <div class="col-12 col-md-6 form-group">
        <label for="club_name" class="form-label">Nama Club</label>
        <BaseInput
          id="club_name"
          name="club_name"
          v-model="club_name"
          :error="errors.club_name"
          placeholder="Masukan nama club..."
        />
      </div>

      <div class="col-12 col-md-6 form-group">
        <label for="club_name_abbreviation" class="form-label">Singkatan Nama Club</label>
        <BaseInput
          id="club_name_abbreviation"
          name="club_name_abbreviation"
          v-model="club_name_abbreviation"
          :error="errors.club_name_abbreviation"
          placeholder="Masukan singkatan nama club..."
        />
      </div>

      <div class="col-12 col-md-6 form-group">
        <label for="email" class="form-label">Email</label>
        <BaseInput
          id="email"
          name="email"
          type="email"
          v-model="email"
          :error="errors.email"
          placeholder="Masukan email contoh : example@dmail.com"
        />
      </div>

      <div class="col-12 col-md-6 form-group">
        <label for="leader_name" class="form-label">Nama Ketua</label>
        <BaseInput
          id="leader_name"
          name="leader_name"
          v-model="leader_name"
          :error="errors.leader_name"
          placeholder="Masukan Nama Ketua..."
        />
      </div>

      <div class="col-12 col-md-6 form-group">
        <label for="leader_email" class="form-label">Email Ketua</label>
        <BaseInput
          id="leader_email"
          name="leader_email"
          type="email"
          v-model="leader_email"
          :error="errors.leader_email"
          placeholder="Masukan email contoh : example@dmail.com"
        />
      </div>

      <div class="col-12 col-md-6">
        <label for="phone" class="form-label">Telephone / HP</label>
        <BaseInput
          id="phone"
          name="phone"
          type="tel"
          v-model="phone"
          :error="errors.phone"
          placeholder="Masukan nomor telpon atau HP ..."
        />
      </div>

      <div class="col-12 form-group">
        <label for="club_logo" class="form-label">Logo Club</label>
        <BaseFileInput id="club_logo" @change="onFileChange" />
        <input type="hidden" name="old_club_logo" :value="old_club_logo" />

        <img
          v-if="previewImage"
          :src="previewImage"
          class="mb-2 mb-md-4 shadow-1-strong rounded"
          style="cursor: zoom-in;"
          width="100"
        />
      </div>

      <div class="col-12 form-group">
        <label for="address" class="form-label">Alamat</label>
        <textarea
          id="address"
          ref="addressRef"
          class="form-control"
          rows="2"
          placeholder="Masukan alamat..."
          v-model="address"
        ></textarea>
      </div>

      <div class="col-12 form-group">
        <label for="short_description" class="form-label">Deskripsi Singkat</label>
        <textarea
          id="short_description"
          ref="shortDescriptionRef"
          class="form-control"
          maxlength="100"
          placeholder="Masukan deskripsi singkat..."
          v-model="short_description"
        ></textarea>
        <div class="invalid-feedback d-block">{{ errors.short_description }}</div>
      </div>

      <div class="col-12">
        <label for="description" class="form-label">Deskripsi Lengkap</label>
        <textarea
          id="description"
          ref="descriptionRef"
          class="form-control"
          placeholder="Masukan deskripsi lengkap..."
          v-model="description"
        ></textarea>
      </div>

      <div class="col-12 d-flex justify-content-start gap-2 mt-4">
        <BaseButton type="submit" variant="primary" :disabled="isSubmitting">
          <template v-if="isSubmitting">
            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
          </template>
          {{ isSubmitting ? 'Menyimpan...' : 'Simpan' }}
        </BaseButton>
      </div>
    </div>
  </form>
</template>

<script setup>
import { ref, watch, onMounted, onBeforeUnmount } from 'vue'
import { useForm, useField } from 'vee-validate'
import { toTypedSchema } from '@vee-validate/yup'
import * as yup from 'yup'
import BaseButton from '../atoms/BaseButton.vue'
import BaseInput from '../atoms/BaseInput.vue'
import BaseFileInput from '../atoms/BaseFileInput.vue'

const props = defineProps({
  initialValues: { type: Object, default: () => ({}) },
  isSubmitting: { type: Boolean, default: false },
  previewImage: { type: String, default: '' },
})

const emit = defineEmits(['submit', 'change:file'])

const schema = toTypedSchema(
  yup.object({
    club_name: yup.string().required('Nama club wajib diisi').min(5, 'Nama club minimal 5 karakter'),
    club_name_abbreviation: yup.string().required('Singkatan nama club wajib diisi'),
    email: yup.string().email('Format email tidak valid').nullable(),
    leader_name: yup.string().required('Nama ketua wajib diisi'),
    leader_email: yup.string().email('Format email ketua tidak valid').nullable(),
    phone: yup.string().nullable(),
    address: yup.string().nullable(),
    short_description: yup.string().max(100, 'Deskripsi singkat maksimal 100 karakter').nullable(),
    description: yup.string().nullable(),
    old_club_logo: yup.string().nullable(),
  })
)

const { handleSubmit, errors, values, setValues } = useForm({
  validationSchema: schema,
  initialValues: {
    club_name: '',
    club_name_abbreviation: '',
    email: '',
    leader_name: '',
    leader_email: '',
    phone: '',
    address: '',
    short_description: '',
    description: '',
    old_club_logo: '',
  },
})

const { value: club_name } = useField('club_name')
const { value: club_name_abbreviation } = useField('club_name_abbreviation')
const { value: email } = useField('email')
const { value: leader_name } = useField('leader_name')
const { value: leader_email } = useField('leader_email')
const { value: phone } = useField('phone')
const { value: address } = useField('address')
const { value: short_description } = useField('short_description')
const { value: description } = useField('description')
const { value: old_club_logo } = useField('old_club_logo')

const selectedFile = ref(null)
const addressRef = ref(null)
const shortDescriptionRef = ref(null)
const descriptionRef = ref(null)

let addressEditor = null
let shortDescriptionEditor = null
let descriptionEditor = null

watch(
  () => props.initialValues,
  (newValues) => {
    setValues({
      club_name: newValues.club_name || '',
      club_name_abbreviation: newValues.club_name_abbreviation || '',
      email: newValues.email || '',
      leader_name: newValues.leader_name || '',
      leader_email: newValues.leader_email || '',
      phone: newValues.phone || '',
      address: newValues.address || '',
      short_description: newValues.short_description || '',
      description: newValues.description || '',
      old_club_logo: newValues.old_club_logo || '',
    })

    if (addressEditor) addressEditor.setData(newValues.address || '')
    if (shortDescriptionEditor) shortDescriptionEditor.setData(newValues.short_description || '')
    if (descriptionEditor) descriptionEditor.setData(newValues.description || '')
  },
  { deep: true, immediate: true }
)

const onFileChange = (file) => {
  selectedFile.value = file
  emit('change:file', file)
}

const syncEditorContent = async () => {
  if (addressEditor) address.value = await addressEditor.getData()
  if (shortDescriptionEditor) short_description.value = await shortDescriptionEditor.getData()
  if (descriptionEditor) description.value = await descriptionEditor.getData()
}

const onSubmit = handleSubmit((values) => {
  emit('submit', { ...values, file: selectedFile.value })
})

onMounted(async () => {
  if (window.ClassicEditor) {
    if (addressRef.value) {
      addressEditor = await window.ClassicEditor.create(addressRef.value)
      addressEditor.model.document.on('change:data', () => {
        address.value = addressEditor.getData()
      })
      addressEditor.setData(address.value)
    }
    if (shortDescriptionRef.value) {
      shortDescriptionEditor = await window.ClassicEditor.create(shortDescriptionRef.value)
      shortDescriptionEditor.model.document.on('change:data', () => {
        short_description.value = shortDescriptionEditor.getData()
      })
      shortDescriptionEditor.setData(short_description.value)
    }
    if (descriptionRef.value) {
      descriptionEditor = await window.ClassicEditor.create(descriptionRef.value)
      descriptionEditor.model.document.on('change:data', () => {
        description.value = descriptionEditor.getData()
      })
      descriptionEditor.setData(description.value)
    }
  }
})

onBeforeUnmount(async () => {
  if (addressEditor) await addressEditor.destroy()
  if (shortDescriptionEditor) await shortDescriptionEditor.destroy()
  if (descriptionEditor) await descriptionEditor.destroy()
})
</script>

<style>
/* global Bootstrap styling */
</style>
