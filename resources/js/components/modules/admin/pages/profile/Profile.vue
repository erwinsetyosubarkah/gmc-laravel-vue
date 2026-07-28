<template>
  <form @submit.prevent="onSubmit" enctype="multipart/form-data">
    <div class="form-group">
      <label for="club_name">Nama Club</label>
      <input
        id="club_name"
        type="text"
        class="form-control"
        :class="{ 'is-invalid': errors.club_name }"
        placeholder="Masukan nama club..."
        v-model="club_name"
      />
      <div class="invalid-feedback">{{ errors.club_name }}</div>
    </div>

    <div class="form-group">
      <label for="club_name_abbreviation">Singkatan Nama Club</label>
      <input
        id="club_name_abbreviation"
        type="text"
        class="form-control"
        :class="{ 'is-invalid': errors.club_name_abbreviation }"
        placeholder="Masukan singkatan nama club..."
        v-model="club_name_abbreviation"
      />
      <div class="invalid-feedback">{{ errors.club_name_abbreviation }}</div>
    </div>

    <div class="form-group">
      <label for="email">Email</label>
      <input
        id="email"
        type="email"
        class="form-control"
        :class="{ 'is-invalid': errors.email }"
        placeholder="Masukan email contoh : example@dmail.com"
        v-model="email"
      />
      <div class="invalid-feedback">{{ errors.email }}</div>
    </div>

    <div class="form-group">
      <label for="leader_name">Nama Ketua</label>
      <input
        id="leader_name"
        type="text"
        class="form-control"
        :class="{ 'is-invalid': errors.leader_name }"
        placeholder="Masukan Nama Ketua..."
        v-model="leader_name"
      />
      <div class="invalid-feedback">{{ errors.leader_name }}</div>
    </div>

    <div class="form-group">
      <label for="leader_email">Email Ketua</label>
      <input
        id="leader_email"
        type="email"
        class="form-control"
        :class="{ 'is-invalid': errors.leader_email }"
        placeholder="Masukan email contoh : example@dmail.com"
        v-model="leader_email"
      />
      <div class="invalid-feedback">{{ errors.leader_email }}</div>
    </div>

    <div class="form-group">
      <label for="phone">Telephone / HP</label>
      <input
        id="phone"
        type="tel"
        class="form-control"
        :class="{ 'is-invalid': errors.phone }"
        placeholder="Masukan nomor telpon atau HP ..."
        v-model="phone"
      />
      <div class="invalid-feedback">{{ errors.phone }}</div>
    </div>

    <div class="form-group">
      <label for="club_logo">Logo Club</label>
      <input id="club_logo" type="file" class="form-control" @change="handleFileChange" />
      <input type="hidden" name="old_club_logo" v-model="old_club_logo" />
      <img
        v-if="previewImage"
        :src="previewImage"
        class="mb-2 mb-md-4 shadow-1-strong rounded"
        style="cursor: zoom-in;"
        width="100"
      />
    </div>

    <div class="form-group">
      <label for="address">Alamat</label>
      <textarea id="address" class="form-control" cols="30" rows="2" v-model="address"></textarea>
    </div>

    <div class="form-group">
      <label for="short_description">Deskripsi Singkat</label>
      <textarea id="short_description" class="form-control" maxlength="100" v-model="short_description"></textarea>
      <div class="invalid-feedback d-block">{{ errors.short_description }}</div>
    </div>

    <div class="form-group">
      <label for="description">Deskripsi Lengkap</label>
      <textarea id="description" class="form-control ckeditor" v-model="description"></textarea>
    </div>

    <button type="submit" class="btn btn-primary" :disabled="isSubmitting">
      <span v-if="isSubmitting" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
      {{ isSubmitting ? 'Menyimpan...' : 'Simpan' }}
    </button>
  </form>
</template>

<script setup>
import { computed, ref, watchEffect } from 'vue';
import { useStore } from 'vuex';
import { useForm, useField } from 'vee-validate';
import { toTypedSchema } from '@vee-validate/yup';
import * as yup from 'yup';
import Swal from 'sweetalert2/dist/sweetalert2';
import apiClient from '@/services/api';
import { useRouter } from 'vue-router';

const router = useRouter();

const store = useStore();
const profileData = computed(() => store.state.profile || {});

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
  })
);

const { handleSubmit, errors, isSubmitting, setValues } = useForm({
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
});

const { value: club_name } = useField('club_name');
const { value: club_name_abbreviation } = useField('club_name_abbreviation');
const { value: email } = useField('email');
const { value: leader_name } = useField('leader_name');
const { value: leader_email } = useField('leader_email');
const { value: phone } = useField('phone');
const { value: address } = useField('address');
const { value: short_description } = useField('short_description');
const { value: description } = useField('description');
const { value: old_club_logo } = useField('old_club_logo');

const selectedFile = ref(null);
const previewImage = ref('');

watchEffect(() => {
  const data = profileData.value;

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
    });

    if (!selectedFile.value) {
      previewImage.value = data.club_logo ? `/storage/${data.club_logo}` : '';
    }
  }
});

const handleFileChange = (event) => {
  const file = event.target.files[0];
  if (!file) return;

  selectedFile.value = file;
  previewImage.value = URL.createObjectURL(file);
};

const onSubmit = handleSubmit(async (values) => {
  const formData = new FormData();
  formData.append('id',profileData.value.id)

  Object.entries(values).forEach(([key, value]) => {
    if (value !== null && value !== undefined && value !== '') {
      formData.append(key, value);
    }
  });

  if (selectedFile.value) {
    formData.append('club_logo', selectedFile.value);
  }

  try {
    const response = await apiClient.post('/admin-profile', formData, {
        headers: {
            'Content-Type': 'multipart/form-data',
        },
    });

    if (response.status >= 200 && response.status < 300 && response.data.status == 'success') {

        await Swal.fire({
            title: response.data?.title,
            text: response.data?.message,
            icon: response.data?.status,
        });
        router.go(0);
    } else {
      await Swal.fire({
        title: 'Gagal!',
        text: response.data?.message,
        icon: 'error',
      });
    }
  } catch (error) {
    await Swal.fire({
      title: 'Gagal!',
      html: error.response?.data?.message,
      icon: 'error',
    });
  }
});
</script>
