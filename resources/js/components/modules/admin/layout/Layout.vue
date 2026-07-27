<template>
    <div class="wrapper">
        <Navbar></Navbar>
        <Sidebar></Sidebar>
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
            <Header></Header>
            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                <div class="card card-secondary">
                    <div class="card-header">
                    <h3>Halaman {{ title }}</h3>
                    </div>
                    <div class="card-body">
                        <RouterView></RouterView>
                    </div>
                    <!-- /.card-body -->
                </div>
                </div>
            </section>
            <!-- end main content -->
            </div>

            <Footer></Footer>

            <!-- Control Sidebar -->
            <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
            </aside>
            <!-- /.control-sidebar -->
    </div>
</template>

<script setup>
    import Navbar from './parts/Navbar.vue';
    import Footer from './parts/Footer.vue';
    import Sidebar from './parts/Sidebar.vue';
    import Header from './parts/Header.vue';

    import { RouterView, useRoute } from 'vue-router'
    import { computed } from 'vue'

    const route = useRoute()

    let title = computed(() => route.meta.title)
</script>
