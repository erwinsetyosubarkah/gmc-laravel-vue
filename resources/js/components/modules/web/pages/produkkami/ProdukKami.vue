<template>
    <ContentLoader v-if="loading" />
    <section v-else class="section doctor-single shadow" style="padding: 0 !important;">
      <div class="container mt-4 mb-4 pt-5 pb-5">
          <div class="row">
              <div class="col-lg-12">
                  <div class="doctor-details mt-4 mt-lg-0">
                        <h2 class="text-md">{{ produkkamiData.page_title }}</h2>
                      <div class="divider my-4"></div>
                        <div class="row justify-content-center mb-4">
                            <div class="col-md-6">
                                <form @submit.prevent="searchProducts">
                                    <div class="input-group mb-3">
                                        <input
                                            type="text"
                                            class="form-control"
                                            placeholder="Masukan kata kunci..."
                                            name="search"
                                            v-model="searchKeyword"
                                        >
                                        <div class="input-group-append">
                                        <button class="btn btn-primary" style="background-color: #223a66 !important;" type="submit" id="button-addon2">Cari</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                      <section class="section service-2" style="padding: 0 !important;">
                        <div class="container">
                            <template v-if="produkkamiData?.produkkami?.data?.length > 0">
                                <div class="row">
                                    <template v-for="(item, index) in produkkamiData.produkkami.data" :key="index">

                                        <div class="col-lg-6 col-md-6 col-sm-6">
                                            <div class="service-block">
                                                <img :src="'/storage/'+ item.product_image" :alt="item.product_name" class="img-fluid" style="width: 300px;height: 200px;object-fit: cover;object-position: center;">
                                                <div class="content">
                                                    <h4 class="mt-4 mb-2 title-color"><RouterLink :to="'/produkkami/'+ item.id">{{ item.product_name }}</RouterLink></h4>
                                                    <small class=""> <strong> Stock : {{ numberFormat(item.stock) }}</strong></small>
                                                    <small class="float-right"><strong> Harga : {{ formatRupiah(item.price)  }}</strong></small>
                                                    <p class="mb-4 mt-2" v-html="limitText(item.product_description)"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </template>

                                </div>
                                <div class="d-flex justify-content-center mt-4">
                                    <nav aria-label="Page navigation">
                                        <ul class="pagination">
                                            <li
                                                v-for="(link, index) in paginationLinks"
                                                :key="index"
                                                class="page-item"
                                                :class="{ 'active': link.active, 'disabled': !link.url }"
                                            >
                                                <a
                                                class="page-link"
                                                href="#"
                                                @click.prevent="changePage(link.url)"
                                                v-html="link.label"
                                                ></a>
                                            </li>
                                        </ul>
                                    </nav>
                                </div>
                            </template>
                            <template v-else>
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12 text-center p-3">
                                         Produk tidak ditemukan.
                                    </div>
                                </div>
                            </template>
                        </div>
                    </section>
                  </div>
              </div>
          </div>
      </div>
  </section>
</template>

<script setup>
    import { onMounted, ref } from 'vue';
    import { ContentLoader } from 'vue-content-loader';
    import Swal from 'sweetalert2/dist/sweetalert2';
    import apiClient from '@/services/api';
    import { formatRupiah, numberFormat } from '@/utils/format'
    import { limitText } from '@/utils/substr'

    const produkkamiData = ref(null)
    const paginationLinks = ref([]);
    const searchKeyword = ref('');

    const loading = ref(true)
    const errorMessage = ref('')

    const fetchProdukKami = async (page = 1) => {
        try {
            const response = await apiClient.get('/getprodukkami', {
                params: {
                    search: searchKeyword.value,
                    page: page
                }
            })
            produkkamiData.value = response.data
            paginationLinks.value = produkkamiData.value.produkkami.links;

        } catch (error) {
            if (error.response && error.response.status === 422) {
                errorMessage.value = error.response.data.message || 'Validasi Gagal.';
            } else {
                errorMessage.value = 'Gagal mengambil data dari server.'
            }

            Swal.fire({
                title: 'Gagal!',
                html: errorMessage.value,
                icon: 'error'
            });
        } finally {
            loading.value = false
        }
    }

    const searchProducts = () => {
        loading.value = true;
        fetchProdukKami(1);
    }

    const changePage = (url) => {
        if (!url) return;

        const urlParams = new URLSearchParams(new URL(url).search);
        const pageNumber = urlParams.get('page');

        loading.value = true;
        fetchProdukKami(pageNumber);
    };

    onMounted(() => {
        const urlParams = new URLSearchParams(window.location.search);
        searchKeyword.value = urlParams.get('search') || '';

        fetchProdukKami();
    });
</script>
