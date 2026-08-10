<template>
  <section class="section doctor-single shadow mb-4" style="padding: 0 !important;">
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <div class="doctor-details mt-4 mt-lg-0">
            <h2 class="text-md">{{ pageTitle }}</h2>
            <div class="divider my-4"></div>

            <div class="row justify-content-center mb-4">
              <div class="col-md-6">
                <slot name="search" />
              </div>
            </div>

            <section class="section service-2" style="padding: 0 !important;">
              <div class="container">
                <slot />
              </div>
            </section>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
const props = defineProps({
  pageTitle: {
    type: String,
    default: ''
  }
})
</script>
