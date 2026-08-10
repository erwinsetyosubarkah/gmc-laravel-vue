<template>
  <section class="section blog-wrap" style="padding:0 !important;">
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <div class="row">
            <div class="col-lg-12 mb-1">
              <div class="single-blog-item">
                <div class="row d-flex justify-content-center">
                  <div class="col-lg-6">
                    <img :src="imageUrl" :alt="title" class="img-fluid shadow">
                  </div>
                </div>

                <div class="blog-item-content mt-5 pl-4 pr-4">
                  <div class="blog-item-meta mb-3">
                    <span class="text-color-2 text-capitalize mr-3">
                      <i class="icofont-book-mark mr-2"></i>Harga: {{ price }}
                    </span>
                    <span class="text-black text-capitalize mr-3 float-right">
                      <i class="icofont-calendar mr-2"></i> {{ createdAt }}
                    </span>
                  </div>
                  <h2 class="mb-4 text-md">{{ title }}</h2>
                  <p v-html="description"></p>
                  <div class="mt-5">
                    <ul class="float-right list-inline">
                      <li class="list-inline-item"> Share: </li>
                      <li class="list-inline-item"><a href="#" target="_blank"><i class="icofont-facebook" aria-hidden="true"></i></a></li>
                      <li class="list-inline-item"><a href="#" target="_blank"><i class="icofont-twitter" aria-hidden="true"></i></a></li>
                      <li class="list-inline-item"><a href="#" target="_blank"><i class="icofont-pinterest" aria-hidden="true"></i></a></li>
                      <li class="list-inline-item"><a href="#" target="_blank"><i class="icofont-linkedin" aria-hidden="true"></i></a></li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
defineProps({
  imageUrl: {
    type: String,
    default: ''
  },
  title: {
    type: String,
    default: ''
  },
  price: {
    type: String,
    default: ''
  },
  createdAt: {
    type: String,
    default: ''
  },
  description: {
    type: String,
    default: ''
  }
})
</script>
