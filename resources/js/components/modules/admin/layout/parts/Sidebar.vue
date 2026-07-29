<template>
     <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="/admin/dashboard" class="brand-link">
        <img :src="'/storage/'+profile.club_logo" :alt="profile.club_name" class="brand-image img-circle elevation-3" style="opacity: .8" height="160" width="160">
        <span class="brand-text font-weight-light">{{ profile.club_name }}</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img v-if="auth?.user?.photo == ''" src="../../../../../../../public/vendor/adminlte/dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
                <img v-else :src="'/storage/'+auth?.user?.photo" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
            <a href="#" class="d-block">{{ auth?.user?.name }}</a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
                with font-awesome or any other icon font library -->
            <li class="nav-header">HOME</li>
            <li class="nav-item">
                <RouterLink :to="'/admin/dashboard'" class="nav-link">
                    <i class="nav-icon fas fa-home"></i>
                    <p>
                        Home
                    </p>
                </RouterLink>
            </li>
            <template v-if="auth?.user?.level == 'admin'">
                <li class="nav-header">PENGATURAN</li>
                <li class="nav-item">
                    <RouterLink :to="'/admin/profile'" class="nav-link">
                        <i class="nav-icon far fa-user-circle"></i>
                        <p>
                            Profile
                        </p>
                    </RouterLink>
                </li>
                <li class="nav-item">
                    <RouterLink :to="'/admin/visidanmisi'" class="nav-link">
                        <i class="nav-icon far fa-eye"></i>
                        <p>
                            Visi dan Misi
                        </p>
                    </RouterLink>
                </li>
                <li class="nav-item">
                    <RouterLink :to="'/admin/myproduct'" class="nav-link">
                        <i class="nav-icon fas fa-cube"></i>
                        <p>
                            Produk Kami
                        </p>
                    </RouterLink>
                </li>
                <li class="nav-item">
                    <RouterLink :to="'/admin/category'" class="nav-link">
                        <i class="nav-icon fas fa-layer-group"></i>
                        <p>
                            Kategori
                        </p>
                    </RouterLink>
                </li>
            </template>


            <li class="nav-header">KONTEN WEBSITE</li>
            <li class="nav-item">
                <RouterLink :to="'/admin/post'" class="nav-link">
                    <i class="nav-icon fas fa-newspaper"></i>
                    <p>Artikel</p>
                </RouterLink>
            </li>

            <template v-if="auth?.user?.level == 'admin'">
                <li class="nav-item">
                    <RouterLink :to="'/admin/event'" class="nav-link">
                    <i class="nav-icon fas fa-calendar"></i>
                    <p>Event</p>
                    </RouterLink>
                </li>
            </template>

            <li class="nav-item">
                <RouterLink :to="'/admin/galery'" class="nav-link">
                <i class="nav-icon fas fa-images"></i>
                <p>Galery Foto</p>
                </RouterLink>
            </li>

            <template v-if="auth?.user?.level == 'admin'">
                <li class="nav-item">
                    <RouterLink :to="'/admin/myclient'" class="nav-link">
                        <i class="nav-icon fas fa-user-tie"></i>
                        <p>Klien Kami</p>
                    </RouterLink>
                </li>

                <li class="nav-header">MANAGEMEN USER</li>
                <li class="nav-item">
                    <RouterLink :to="'/admin/user'" class="nav-link">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Users</p>
                    </RouterLink>
                </li>
            </template>

            </ul>
        </nav>
        <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </aside>
</template>

<script setup>
    import { computed, onMounted } from 'vue'
    import { RouterLink } from 'vue-router'
    import { useStore } from 'vuex'
    import apiClient from '@/services/api'

    const store = useStore()

    const profile = computed(() => store.state.profile)
    const auth = computed(() => store.state.auth)


    onMounted(async () => {
        try {
            const { data } = await apiClient.get('/auth/check')

            await store.dispatch('updateAuth', data)
        } catch (error) {
            console.log(error)
        }
    })


</script>
