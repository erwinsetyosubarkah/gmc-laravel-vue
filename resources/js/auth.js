import { createApp, nextTick } from 'vue';


import '../css/auth.css';

import router from './router';
import store from './store';

import Layout from './components/modules/auth/Layout.vue';


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


