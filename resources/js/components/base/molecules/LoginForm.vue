<template>
  <form @submit.prevent="$emit('submit')">
    <div class="input-group mb-3">
      <input
        id="username"
        type="text"
        :value="modelValue.username"
        :class="['form-control', { 'is-invalid': errors.username } ]"
        placeholder="Username"
        autofocus
        required
        @input="$emit('update:username', $event.target.value)"
      />
      <div class="input-group-append">
        <div class="input-group-text">
          <span class="fas fa-envelope"></span>
        </div>
      </div>
      <div class="invalid-feedback">
        {{ errors.username }}
      </div>
    </div>

    <div class="input-group mb-3">
      <input
        id="password"
        type="password"
        :value="modelValue.password"
        :class="['form-control', { 'is-invalid': errors.password } ]"
        placeholder="Password"
        required
        @input="$emit('update:password', $event.target.value)"
      />
      <div class="input-group-append">
        <div class="input-group-text">
          <span class="fas fa-lock"></span>
        </div>
      </div>
      <div class="invalid-feedback">
        {{ errors.password }}
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <button
          type="submit"
          class="btn btn-primary w-100 fw-bold py-2"
          :disabled="isSubmitting"
        >
          <span
            v-if="isSubmitting"
            class="spinner-border spinner-border-sm me-2"
            role="status"
            aria-hidden="true"
          ></span>
          {{ isSubmitting ? 'Mohon tunggu...' : 'LOGIN' }}
        </button>
      </div>
    </div>
  </form>
</template>

<script setup>
const props = defineProps({
  modelValue: {
    type: Object,
    default: () => ({ username: '', password: '' })
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

const emit = defineEmits(['submit', 'update:username', 'update:password'])
</script>
