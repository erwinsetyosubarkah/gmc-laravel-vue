<template>
    <section class="banner" :style="'background: url(\'' + props.backgroundImage + '\') no-repeat;'">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-12 col-xl-7">
                    <div class="block">
                        <div class="divider mb-3"></div>
                        <h3 class="text-uppercase letter-spacing text-white">Solid community with sharing </h3>

                        <p class="mb-4 pr-5 text-white">{{ props.description }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
    const props = defineProps({
        backgroundImage: {
            type: String,
            required: true,
            default: ''
        },
        description: {
            type: String,
            required: true,
            default: ''
        }
    });
</script>
