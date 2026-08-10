<template>
  <form @submit.prevent="$emit('submit')" novalidate>
    <div class="input-group mb-3">
      <input
        id="name"
        type="text"
        :value="modelValue.name"
        :class="['form-control', { 'is-invalid': errors.name } ]"
        placeholder="Nama Lengkap"
        autocomplete="name"
        required
        @input="$emit('update:name', $event.target.value)"
      />
      <div class="input-group-append">
        <div class="input-group-text">
          <span class="fas fa-user"></span>
        </div>
      </div>
      <div class="invalid-feedback">{{ errors.name }}</div>
    </div>

    <div class="input-group mb-3">
      <input
        id="username"
        type="text"
        :value="modelValue.username"
        :class="['form-control', { 'is-invalid': errors.username } ]"
        placeholder="Username"
        autocomplete="username"
        required
        @input="$emit('update:username', $event.target.value)"
      />
      <div class="input-group-append">
        <div class="input-group-text">
          <span class="fas fa-user"></span>
        </div>
      </div>
      <div class="invalid-feedback">{{ errors.username }}</div>
    </div>

    <input type="hidden" name="level" value="author" />

    <div class="input-group mb-3">
      <input
        id="email"
        type="email"
        :value="modelValue.email"
        :class="['form-control', { 'is-invalid': errors.email } ]"
        placeholder="Email"
        autocomplete="email"
        required
        @input="$emit('update:email', $event.target.value)"
      />
      <div class="input-group-append">
        <div class="input-group-text">
          <span class="fas fa-envelope"></span>
        </div>
      </div>
      <div class="invalid-feedback">{{ errors.email }}</div>
    </div>

    <div class="input-group mb-3">
      <input
        id="password"
        type="password"
        :value="modelValue.password"
        :class="['form-control', { 'is-invalid': errors.password } ]"
        placeholder="Password"
        autocomplete="new-password"
        required
        @input="$emit('update:password', $event.target.value)"
      />
      <div class="input-group-append">
        <div class="input-group-text">
          <span class="fas fa-lock"></span>
        </div>
      </div>
      <div class="invalid-feedback">{{ errors.password }}</div>
    </div>

    <div class="input-group mb-3">
      <input
        id="password2"
        type="password"
        :value="modelValue.password2"
        :class="['form-control', { 'is-invalid': errors.password2 } ]"
        placeholder="Konfirmasi Password"
        autocomplete="new-password"
        required
        @input="$emit('update:password2', $event.target.value)"
      />
      <div class="input-group-append">
        <div class="input-group-text">
          <span class="fas fa-lock"></span>
        </div>
      </div>
      <div class="invalid-feedback">{{ errors.password2 }}</div>
    </div>

    <div class="row">
      <div class="col-12">
        <button type="submit" class="btn btn-primary btn-block" :disabled="isSubmitting">
          <span
            v-if="isSubmitting"
            class="spinner-border spinner-border-sm me-2"
            role="status"
            aria-hidden="true"
          ></span>
          {{ isSubmitting ? 'Mohon tunggu...' : 'DAFTAR' }}
        </button>
      </div>
    </div>
  </form>
</template>

<script setup>
const props = defineProps({
  modelValue: {
    type: Object,
    default: () => ({
      name: '',
      username: '',
      email: '',
      password: '',
      password2: ''
    })
  },
  errors: {
    type: Object,
    default: () => ({})
  },
  isSubmitting: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits([
  'submit',
  'update:name',
  'update:username',
  'update:email',
  'update:password',
  'update:password2'
])
</script>
