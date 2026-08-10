<template>
  <section class="section doctor-single shadow" style="padding: 0 !important;">
    <div class="container mt-4 mb-4 pt-5 pb-5">
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
