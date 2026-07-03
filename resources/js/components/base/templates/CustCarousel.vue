<template>
    <section class="features">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="feature-block d-lg-flex">
                        <div class="col-lg-12 testimonial-wrap-2">
                            <template v-for="(image, index) in dataImage" :key="index">
                                <div class="testimonial-block style-2  gray-bg feature-item">
                                    <div class="">
                                        <img :src="image.url" alt="" class="img-fluid">
                                    </div>
                                    <div class="position-absolute" style="top: 50px; background-color: rgb(253, 251, 251, 0.8); box-shadow: 3px 3px 5px rgba(0, 0, 0, 0.5);">
                                        <h5>{{ image.title }}</h5>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
    const props = defineProps({
        dataImage: {
            type: Array,
            required: true,
            default: () => []
        },
    });


</script>
