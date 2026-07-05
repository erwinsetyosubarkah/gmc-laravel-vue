<template>
    <section class="section doctor-single shadow" style="padding: 0 !important;">
      <div class="container mt-4 mb-4 pt-5 pb-5">
          <div class="row">
              <div class="col-lg-4 col-md-6">
                  <div class="doctor-img-block">
                      <img :src="'/storage/'+ profileData.club_logo" :alt="profileData.club_name" class="img-fluid w-50">

                      <div class="info-block mt-4">
                          <h4 class="mb-0">{{ profileData.leader_name }}</h4>
                          <p>{{ profileData.leader_email }}</p>

                          <ul class="list-inline mt-4 doctor-social-links">
                              <li class="list-inline-item"><a href="#"><i class="icofont-facebook"></i></a></li>
                              <li class="list-inline-item"><a href="#"><i class="icofont-twitter"></i></a></li>
                              <li class="list-inline-item"><a href="#"><i class="icofont-skype"></i></a></li>
                              <li class="list-inline-item"><a href="#"><i class="icofont-linkedin"></i></a></li>
                              <li class="list-inline-item"><a href="#"><i class="icofont-pinterest"></i></a></li>
                          </ul>
                      </div>
                  </div>
              </div>

              <div class="col-lg-8 col-md-6">
                  <div class="doctor-details mt-4 mt-lg-0">
                      <h2 class="text-md">Profil</h2>
                      <div class="divider my-4"></div>
                      <p v-html="profileData.description"></p>
                  </div>
              </div>
          </div>
      </div>
  </section>
</template>

<script setup>
    import { computed } from 'vue';
    import { ContentLoader } from 'vue-content-loader';
    import { useStore } from 'vuex'

    const store = useStore()
    const profileData = computed(() => store.state.profile)

</script>
