<template>
  <section class="section doctor-single shadow mb-4" style="padding: 0 !important;">
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <div class="doctor-details mt-4 mt-lg-0">
            <slot name="title" />
            <div class="divider my-4"></div>
            <slot />
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
