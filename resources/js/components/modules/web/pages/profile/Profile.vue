<template>
  <WebProfilePageShell>
    <template #profile-content>
      <WebProfileSummary
        :imageUrl="'/storage/' + profileData.club_logo"
        :clubName="profileData.club_name"
        :leaderName="profileData.leader_name"
        :leaderEmail="profileData.leader_email"
      >
        <li class="list-inline-item"><a href="#"><i class="icofont-facebook"></i></a></li>
        <li class="list-inline-item"><a href="#"><i class="icofont-twitter"></i></a></li>
        <li class="list-inline-item"><a href="#"><i class="icofont-skype"></i></a></li>
        <li class="list-inline-item"><a href="#"><i class="icofont-linkedin"></i></a></li>
        <li class="list-inline-item"><a href="#"><i class="icofont-pinterest"></i></a></li>
      </WebProfileSummary>

      <div class="col-lg-8 col-md-6">
        <div class="doctor-details mt-4 mt-lg-0">
          <BaseWebPageTitle title="Profil" />
          <p v-html="profileData.description"></p>
        </div>
      </div>
    </template>
  </WebProfilePageShell>
</template>

<script setup>
    import { computed } from 'vue';
    import { ContentLoader } from 'vue-content-loader';
    import { useStore } from 'vuex'
    import WebProfilePageShell from '../../../../base/organisms/WebProfilePageShell.vue'
    import WebProfileSummary from '../../../../base/molecules/WebProfileSummary.vue'
    import BaseWebPageTitle from '../../../../base/atoms/BaseWebPageTitle.vue'

    const store = useStore()
    const profileData = computed(() => store.state.profile)

</script>
