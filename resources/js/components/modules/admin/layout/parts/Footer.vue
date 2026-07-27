<template>
    <footer class="main-footer">
        <strong>{{ profile.club_name }} - Copyright &copy; 2014-2021.</strong>
        All rights reserved.
    </footer>
</template>

<script setup>
    import { computed } from 'vue'
    import { useStore } from 'vuex'

    const store = useStore()

    const profile = computed(() => store.state.profile)

</script>
