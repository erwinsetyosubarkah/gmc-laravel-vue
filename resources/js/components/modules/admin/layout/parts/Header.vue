<template>
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-12">
            <ol class="breadcrumb ">
              <li class="breadcrumb-item"><RouterLink :to="'/admin-dashboard'">Dashboard</RouterLink></li>
              <li class="breadcrumb-item active">{{ title }}</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
</template>

<script setup>
    import { RouterLink, useRoute } from 'vue-router'
    import { computed } from 'vue'

    const route = useRoute()

    const title = computed(() => route.meta.title)


</script>
