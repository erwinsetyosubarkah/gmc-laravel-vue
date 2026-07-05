<template>
    <Navbar></Navbar>
    <div class="container pt-4">
        <RouterView />
    </div>
    <Footer></Footer>
</template>

<script setup>
    import Navbar from './parts/Navbar.vue';
    import Footer from './parts/Footer.vue';
</script>
