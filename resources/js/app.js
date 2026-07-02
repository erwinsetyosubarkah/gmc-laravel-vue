import { createApp } from 'vue';


import 'bootstrap'
import 'bootstrap/dist/css/bootstrap.min.css'

import '../css/app.css';
import router from './router';
import store from './store';

import Layout from './components/modules/web/layout/Layout.vue';


const rootElement = document.getElementById('app')

if (rootElement) {

    const profileData = JSON.parse(rootElement.getAttribute('data-profile') || '{}')

    const app = createApp({});
    app.use(router);

    store.commit('SET_PROFILE', profileData)

    app.use(store)

    app.component('layout', Layout);
    app.mount('#app')
}
