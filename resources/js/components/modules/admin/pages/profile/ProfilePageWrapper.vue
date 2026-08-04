<template>
  <ProfilePageShell>
    <ProfileForm
      :values="values"
      :errors="errors"
      :isSubmitting="isSubmitting"
      :previewImage="previewImage"
      @submit="handleSubmit"
      @change:file="handleFileChange"
    />
  </ProfilePageShell>
</template>

<script setup>
import { computed, ref, watchEffect, onBeforeUnmount, onMounted } from 'vue'
import { useStore } from 'vuex'
import { useForm, useField } from 'vee-validate'
import { toTypedSchema } from '@vee-validate/yup'
import * as yup from 'yup'
import Swal from 'sweetalert2/dist/sweetalert2'
import apiClient from '@/services/api'
import { useRouter } from 'vue-router'
import ProfilePageShell from '../../../base/organisms/ProfilePageShell.vue'
import ProfileForm from '../../../base/molecules/ProfileForm.vue'

const router = useRouter()
const store = useStore()
const profileData = computed(() => store.state.profile || {})

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

const { handleSubmit: validateSubmit, errors, isSubmitting, setValues } = useForm({
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
const previewImage = ref('')
const editor_al = ref(null)
const editor_ds = ref(null)
const editor_dl = ref(null)
let instance_al = null
let instance_ds = null
let instance_dl = null

watchEffect(() => {
  const data = profileData.value
  if (data && Object.keys(data).length) {
    setValues({
      club_name: data.club_name || '',
      club_name_abbreviation: data.club_name_abbreviation || '',
      email: data.email || '',
      leader_name: data.leader_name || '',
      leader_email: data.leader_email || '',
      phone: data.phone || '',
      address: data.address || '',
      short_description: data.short_description || '',
      description: data.description || '',
      old_club_logo: data.club_logo || '',
    })
    if (!selectedFile.value) {
      previewImage.value = data.club_logo ? `/storage/${data.club_logo}` : ''
    }
  }
})

const handleFileChange = (file) => {
  selectedFile.value = file
  previewImage.value = file ? URL.createObjectURL(file) : profileData.value.club_logo ? `/storage/${profileData.value.club_logo}` : ''
}

const syncEditorContent = async () => {
  if (instance_al) setValues({ address: await instance_al.getData() })
  if (instance_ds) setValues({ short_description: await instance_ds.getData() })
  if (instance_dl) setValues({ description: await instance_dl.getData() })
}

const submitProfile = validateSubmit(async (values) => {
  await syncEditorContent()
  const formData = new FormData()
  formData.append('id', profileData.value.id)
  Object.entries(values).forEach(([key, value]) => {
    if (value !== null && value !== undefined && value !== '') {
      formData.append(key, value)
    }
  })
  if (selectedFile.value) {
    formData.append('club_logo', selectedFile.value)
  }

  try {
    const response = await apiClient.post('/admin/profile', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    if (response.status >= 200 && response.status < 300 && response.data.status === 'success') {
      await Swal.fire({
        title: response.data?.title,
        text: response.data?.message,
        icon: response.data?.status,
      })
      router.go(0)
    } else {
      await Swal.fire({ title: 'Gagal!', text: response.data?.message, icon: 'error' })
    }
  } catch (error) {
    await Swal.fire({ title: 'Gagal!', html: error.response?.data?.message, icon: 'error' })
  }
})

onMounted(async () => {
  instance_al = await ClassicEditor.create(editor_al.value)
  instance_ds = await ClassicEditor.create(editor_ds.value)
  instance_dl = await ClassicEditor.create(editor_dl.value)

  instance_al.model.document.on('change:data', () => setValues({ address: instance_al.getData() }))
  instance_ds.model.document.on('change:data', () => setValues({ short_description: instance_ds.getData() }))
  instance_dl.model.document.on('change:data', () => setValues({ description: instance_dl.getData() }))
})

onBeforeUnmount(async () => {
  if (instance_al) await instance_al.destroy()
  if (instance_ds) await instance_ds.destroy()
  if (instance_dl) await instance_dl.destroy()
})

const values = {
  club_name,
  club_name_abbreviation,
  email,
  leader_name,
  leader_email,
  phone,
  address,
  short_description,
  description,
  old_club_logo,
}

const errorsObject = errors

const handleSubmit = () => submitProfile()
</script>

<style>
/* global Bootstrap styling */
</style>
