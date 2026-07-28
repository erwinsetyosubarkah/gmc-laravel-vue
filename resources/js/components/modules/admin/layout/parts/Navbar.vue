<template>
    <!-- Preloader -->
    <div class="preloader flex-column justify-content-center align-items-center">
        <img class="animation__shake rounded-circle" :src="'/storage/'+profile.club_logo" :alt="profile.club_name" height="60" width="60">
    </div>

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/web/"><i class="fas fa-desktop"></i> Web</a>
        </li>
        </ul>

        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">


        <!-- Messages Dropdown Menu -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
            <i class="fas fa-user"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                <button type="submit" class="btn btn-secondary ml-2" @click="onLogout"><i class="fas fa-sign-out-alt"></i> Logout</button>
            </div>
        </li>

        <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button">
            <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-widget="control-sidebar" data-controlsidebar-slide="true" href="#" role="button">
            <i class="fas fa-th-large"></i>
            </a>
        </li>
        </ul>
    </nav>
    <!-- /.navbar -->
</template>

<script setup>
    import { computed } from 'vue'
    import { useStore } from 'vuex'
    import apiClient from '@/services/api'
    import Swal, { swal } from 'sweetalert2/dist/sweetalert2'

    const store = useStore()

    const profile = computed(() => store.state.profile)

    const onLogout = async () => {
        try {
            const { data } = await apiClient.post('/auth/logout');
            await store.dispatch('updateAuth', null)
        } catch (error) {
            Swal.fire({
                title: 'Gagal!',
                html: 'Gagal Logout',
                icon: 'error'
            });
        }
    }

</script>
