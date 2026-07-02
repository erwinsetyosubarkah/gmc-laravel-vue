<template>
    <Navbar></Navbar>
    <RouterView />
    <Footer></Footer>
</template>

<script setup>
    import Navbar from './parts/Navbar.vue';
    import Footer from './parts/Footer.vue';
</script>
