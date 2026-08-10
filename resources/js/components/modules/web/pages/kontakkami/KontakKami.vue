<template>
    <WebContactPageShell>
        <template #page-title>
            <section class="page-title bg-1">
                <div class="overlay"></div>
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="block text-center">
                                <span class="text-white">Kontak Kami</span>
                                <h1 class="text-capitalize mb-5 text-lg">Segera Bergabung</h1>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </template>

        <template #contact-cards>
            <section class="section contact-info pb-0">
                <div class="container">
                    <div class="row" id="card-parent">
                        <WebContactInfoCard icon="icofont-live-support" title="Hubungi Kami" :value="profileData?.phone" />
                        <WebContactInfoCard icon="icofont-support-faq" title="Email Kami" :value="profileData?.email" />
                        <WebContactInfoCard icon="icofont-location-pin" title="Alamat" :value="profileData?.address" />
                    </div>
                </div>
            </section>
        </template>

        <template #contact-callout>
            <section class="contact-form-wrap section">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-6">
                            <div class="section-title text-center">
                                <h2 class="text-md mb-2">Kontak Kami</h2>
                                <div class="divider mx-auto my-4"></div>
                                <p class="mb-5">Hubungi Kami untuk info lebih lengkap.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </template>
    </WebContactPageShell>
</template>

<script setup>
    import { computed } from 'vue';
    import { useStore } from 'vuex'
    import WebContactPageShell from '../../../../base/organisms/WebContactPageShell.vue'
    import WebContactInfoCard from '../../../../base/molecules/WebContactInfoCard.vue'

    const store = useStore()
    const profileData = computed(() => store.state.profile)
</script>

<style scoped>
/* PERBAIKAN 4: Trik CSS untuk menyamakan tinggi secara otomatis & menggantikan kalkulasi jQuery */
#card-parent {
    display: flex;
    flex-wrap: wrap;
}

/* Memastikan elemen kartu ditarik mengikuti tinggi kolom Bootstrap-nya */
.contact-block {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
    width: 100%;

    /* Pengganti manipulasi .css() dari fungsi jQuery */
    padding: 25px 15px !important; /* Memberikan ruang ekstra pengganti +30px jQuery */
    margin: 0 !important;          /* Margin luar diserahkan ke kolom Bootstrap agar rapi */
    box-sizing: border-box;
}

/* Menjaga teks alamat atau nomor telepon yang panjang terbungkus dengan rapi */
.contact-block p {
    word-break: break-word;
    font-size: 15px;
}
</style>
