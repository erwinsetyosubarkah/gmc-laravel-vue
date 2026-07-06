import { createApp, nextTick } from 'vue';


import '../css/app.css';

import router from './router';
import store from './store';

import Layout from './components/modules/web/layout/Layout.vue';



import 'bootstrap';
import '../../public/vendor/novena/plugins/counterup/jquery.easing.js';

import '../../public/vendor/novena/plugins/slick-carousel/slick/slick.min.js';

import '../../public/vendor/novena/plugins/counterup/jquery.waypoints.min.js';

import '../../public/vendor/novena/plugins/counterup/jquery.counterup.min.js';

import '../../public/vendor/novena/plugins/google-map/map.js';
import 'https://maps.googleapis.com/maps/api/js?key=AIzaSyAkeLMlsiwzp6b3Gnaxd86lvakimwGA6UA&callback=initMap';

import './script.js';
import './contact.js';

import VueSweetalert2 from 'vue-sweetalert2';
import dayjs from 'dayjs';
import relativeTime from 'dayjs/plugin/relativeTime';
import 'dayjs/locale/id';

dayjs.extend(relativeTime);
dayjs.locale('id');

const rootElement = document.getElementById('app')

if (rootElement) {

    const profileData = JSON.parse(rootElement.getAttribute('data-profile') || '{}')
    const categoriesData = JSON.parse(rootElement.getAttribute('data-categories') || '{}')
    const newEventsData = JSON.parse(rootElement.getAttribute('data-newevents') || '{}')

    const app = createApp({});
    app.use(router);

    store.commit('SET_PROFILE', profileData)
    store.commit('SET_CATEGORIES', categoriesData)
    store.commit('SET_NEWEVENTS', newEventsData)

    app.config.globalProperties.$diffForHumans = (dateString) => {
        if (!dateString) return '';
        return dayjs(dateString).fromNow();
    };

    app.use(store)

    // 2. Pasang Mixin Global agar otomatis mengeksekusi ulang setelah v-for render
    app.mixin({
        mounted() {
            this.jalankanSemuaScriptNovena();
        },
        updated() {
            this.jalankanSemuaScriptNovena();
        },
        methods: {
            jalankanSemuaScriptNovena() {
            nextTick(() => {
                // Picu script plugin visual (Slick, Shuffle, dll)
                if (typeof window.initNovenaPlugins === 'function') {
                window.initNovenaPlugins();
                }
                // Picu script form kontak
                if (typeof window.initNovenaContact === 'function') {
                window.initNovenaContact();
                }
            });
            }
        }
    });
    app.use(VueSweetalert2);

    app.component('layout', Layout);
    app.mount('#app')
}


