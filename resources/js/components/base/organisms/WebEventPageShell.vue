<template>
  <section class="section doctor-single shadow mb-4" style="padding: 0 !important;">
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <div class="doctor-details mt-4 mt-lg-0">
            <slot name="header" />
            <slot name="search" />
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
