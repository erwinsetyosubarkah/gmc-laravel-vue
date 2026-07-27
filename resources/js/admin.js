import { createApp, nextTick } from 'vue';

import '../css/admin.css';

import Layout from './components/modules/admin/layout/Layout.vue';

import router from './router';
import store from './store';

const app = createApp({});

const rootElement = document.getElementById('app')

if (rootElement) {

    const profileData = JSON.parse(rootElement.getAttribute('data-profile') || '{}')

    app.use(router);
    app.use(store);
    store.commit('SET_PROFILE', profileData)
    app.component('layout', Layout);
    app.mount('#app')
}

