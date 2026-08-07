<template>
    <ContentLoader v-if="loading" speed="0.5" />
  <form v-else @submit.prevent="onSubmit">
    <div class="form-group">
      <label for="title">Judul</label>
      <input
        id="title"
        v-model="title"
        type="text"
        class="form-control"
        :class="{ 'is-invalid': errors.title }"
        placeholder="Masukan Judul..."
      />
      <div class="invalid-feedback">{{ errors.title }}</div>
    </div>

    <div class="form-group">
      <label for="content">Isi Visi dan Misi</label>
      <textarea id="content" v-model="content" class="form-control" ref="editor"></textarea>
      <div class="invalid-feedback d-block">{{ errors.content }}</div>
    </div>

    <button type="submit" class="btn btn-primary" :disabled="isSubmitting">
      <span v-if="isSubmitting" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
      {{ isSubmitting ? 'Menyimpan...' : 'Simpan' }}
    </button>
  </form>
</template>

<script setup>
import { onMounted, ref, onBeforeUnmount } from 'vue';
import { ContentLoader } from 'vue-content-loader';
import { useForm, useField } from 'vee-validate';
import { toTypedSchema } from '@vee-validate/yup';
import * as yup from 'yup';
import apiClient from '@/services/api';
import Swal from 'sweetalert2/dist/sweetalert2';

const loading = ref(false);
const editor = ref(null);
let instance = null;

const schema = toTypedSchema(
  yup.object({
    title: yup.string().required('Judul wajib diisi').min(5, 'Judul minimal 5 karakter'),
    content: yup.string().required('Isi visi dan misi wajib diisi')
  })
);

const { handleSubmit, errors, isSubmitting, setValues } = useForm({
  validationSchema: schema,
  initialValues: {
    title: '',
    content: ''
  }
});

const { value: title } = useField('title');
const { value: content } = useField('content');

const syncEditorContent = async () => {
  if (instance) {
    content.value = await instance.getData();
  }
};

const fetchVisiMisi = async () => {
  loading.value = true;

  try {
    const response = await apiClient.get('/web/getvisidanmisi');
    const data = response?.data?.visidanmisi || response?.data || {};

    setValues({
      title: data.title || '',
      content: data.content || ''
    });

    if (instance) {
      await instance.setData(data.content || '');
    }
  } catch (error) {
    Swal.fire({
      title: 'Gagal!',
      text: 'Gagal mengambil data visi dan misi.',
      icon: 'error'
    });
  } finally {
    loading.value = false;
  }
};

const onSubmit = handleSubmit(async (values) => {
  await syncEditorContent();

  const payload = {
    ...values,
    content: content.value
  };

  try {
    const response = await apiClient.post('/admin/visidanmisi', payload);

    if (response?.data?.status === 'success') {
      await Swal.fire({
        title: 'Berhasil!',
        text: response.data.message || 'Data visi dan misi berhasil disimpan.',
        icon: 'success'
      });
    } else {
      await Swal.fire({
        title: 'Gagal!',
        text: response.data.message || 'Gagal menyimpan data.',
        icon: 'error'
      });
    }
  } catch (error) {
    Swal.fire({
      title: 'Gagal!',
      text: error?.response?.data?.message || 'Terjadi kesalahan saat menyimpan data.',
      icon: 'error'
    });
  }
});

onMounted(async () => {
  await fetchVisiMisi();
  instance = await ClassicEditor.create(editor.value);

  instance.model.document.on('change:data', async () => {
    await syncEditorContent();
  });
});

onBeforeUnmount(async () => {
  if (instance) {
    await instance.destroy();
  }
});
</script>
