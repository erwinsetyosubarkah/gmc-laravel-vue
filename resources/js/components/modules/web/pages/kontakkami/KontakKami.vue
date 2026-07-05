<template>
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

    <!-- contact form start -->
    <section class="section contact-info pb-0">
        <div class="container">
            <!-- PERBAIKAN 1: Menghapus style inline padding agar layout grid Bootstrap bekerja normal -->
            <div class="row" id="card-parent">
                <!-- PERBAIKAN 2: Menggunakan class 'd-flex' pada kolom agar layout di dalamnya bisa otomatis ditarik penuh -->
                <div class="col-lg-4 col-sm-6 col-md-6 d-flex mb-4">
                    <div class="contact-block w-100 mb-lg-0">
                        <i class="icofont-live-support"></i>
                        <h5>Hubungi Kami</h5>
                        <p class="mb-0">{{ profileData?.phone }}</p>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6 col-md-6 d-flex mb-4">
                    <div class="contact-block w-100 mb-lg-0">
                        <i class="icofont-support-faq"></i>
                        <h5>Email Kami</h5>
                        <p class="mb-0">{{ profileData?.email }}</p>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6 col-md-6 d-flex mb-4">
                    <div class="contact-block w-100 mb-lg-0">
                        <i class="icofont-location-pin"></i>
                        <h5>Alamat</h5>
                        <p class="mb-0">{{ profileData?.address }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

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

<script setup>
    import { computed } from 'vue';
    // Gunakan optional chaining (?.) di template untuk menghindari error sebelum state terisi
    import { useStore } from 'vuex'

    const store = useStore()
    const profileData = computed(() => store.state.profile)

    // PERBAIKAN 3: Blok onMounted & jQuery dihapus total karena digantikan oleh CSS scoped di bawah
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
