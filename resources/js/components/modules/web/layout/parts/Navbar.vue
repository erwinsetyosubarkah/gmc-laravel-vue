<template>
   <header>
        <div class="header-top-bar">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <ul class="top-bar-info list-inline-item pl-0 mb-0">
                            <li class="list-inline-item"><a href="mailto:{{ profile.email }}"><i class="icofont-support-faq mr-2"></i> {{ profile.email }}</a></li>
                            <li class="list-inline-item"><i class="icofont-location-pin mr-2"></i> {{ profile.address }} </li>
                        </ul>
                    </div>
                    <div class="col-lg-6">
                        <div class="text-lg-right top-right-bar mt-2 mt-lg-0">
                            <a href="tel:{{ profile.phone }}">
                                <span>Call Now : </span>
                                <span class="h4">{{ profile.phone }}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <nav class="navbar navbar-expand-lg navigation shadow" id="navbar">
            <div class="container">
                <RouterLink class="navbar-brand" to="/">
                    <img :src="'/storage/'+profile.club_logo" alt="" class="" height="100"> <span class="font-weight-bold">{{ profile.club_name }}</span>
                </RouterLink>


                <button class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="#navbarmain" aria-controls="navbarmain" aria-expanded="false" aria-label="Toggle navigation">
                <span class="icofont-navigation-menu"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarmain">
                <ul class="navbar-nav ml-auto">
                <li class="nav-item active">
                    <RouterLink class="nav-link" to="/">Home</RouterLink>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="dropdown02" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Profile Club <i class="icofont-thin-down"></i></a>
                    <ul class="dropdown-menu" aria-labelledby="dropdown02">
                        <li><RouterLink class="dropdown-item" to="/profile">Profile</RouterLink></li>
                        <li><RouterLink class="dropdown-item" to="/visidanmisi">Visi dan Misi</RouterLink></li>
                        <li><RouterLink class="dropdown-item" to="/produkkami">Produk Kami</RouterLink></li>
                        <li><RouterLink class="dropdown-item" to="/kontakkami">Kontak Kami</RouterLink></li>
                    </ul>
                </li>
                <li class="nav-item"><RouterLink class="nav-link" to="/article">Artikel</RouterLink></li>
                    <li class="nav-item"><RouterLink class="nav-link" to="/event">Event</RouterLink></li>
                    <li class="nav-item"><RouterLink class="nav-link" to="/galery">Galery Foto</RouterLink></li>
                    <li class="nav-item"><RouterLink class="nav-link" to="/klienkami">Klien Kami</RouterLink></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle btn btn-primary btn-sm text-white" href="#" id="dropdown05" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-user"></i> Login <i class="icofont-thin-down"></i></a>
                        <ul class="dropdown-menu" aria-labelledby="dropdown05">
                            <li><a class="dropdown-item" href="/admin-login"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                            <li><a class="dropdown-item" href="/admin-register"><i class="fas fa-edit"></i> Daftar</a></li>
                        </ul>
                    </li>

                </ul>
            </div>
            </div>
        </nav>
    </header>
</template>

<script setup>
    import { computed } from 'vue'
    import { useStore } from 'vuex'

    const store = useStore()

    const profile = computed(() => store.state.profile)


</script>
