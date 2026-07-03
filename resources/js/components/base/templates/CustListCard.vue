<template>
    <template v-if="listDataCard.length">
        <div class="row">
            <template v-for="(item, index) in listDataCard" :key="index">
                <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class="service-block mb-5">
                        <img :src="item.img_url" :alt="item.title" class="img-fluid" style="cursor: zoom-in;" @click="zoomImg(item.img_url)">

                        <div class="content">
                            <h4 class="mt-4 mb-2 title-color"><a href="{{ item.action_url }}">{{ item.title }}</a></h4>
                            <small class=""> <strong> <i class="icofont-book-mark mr-2"></i>{{ item.category_name }}</strong></small>
                            <small class="float-right"><strong> <i class="icofont-calendar mr-2"></i> {{ item.created_at_humans }}</strong></small>
                            <p class="mb-4 mt-2">{{  item.excerpt }} <small><a href="{{ item.action_url }}">Selengkapnya</a></small></p>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </template>
    <div v-else class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 text-center p-3 mb-5">
            {{ noData }}
        </div>
    </div>
</template>


<script setup>
    const props = defineProps({
        listDataCard: {
            type: Array,
            required: true,
            default: () => []
        },
        noData: {
            type: String,
            required: true,
            default: 'Data tidak ditemukan.'
        }
    });

    function zoomImg(src){
        previmg(src);
    }

    function previmg(src){
        var modal;

        function removeModal() {
            modal.remove();
            $('body').off('keyup.modal-close');
        }
        modal = $('<div>').css({
            background: 'RGBA(0,0,0,.5) url(' + src + ') no-repeat center',
            backgroundSize: 'contain',
            width: '100%',
            height: '100%',
            position: 'fixed',
            zIndex: '10000',
            top: '0',
            left: '0',
            cursor: 'zoom-out'
        }).click(function() {
            removeModal();
        }).appendTo('body');
        //handling ESC
        $('body').on('keyup.modal-close', function(e) {
            if (e.key === 'Escape') {
            removeModal();
            }
        });
    }
</script>
