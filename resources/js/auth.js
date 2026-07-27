import { createApp, nextTick } from 'vue';


import '../css/auth.css';

import router from './router';
import store from './store';

import Layout from './components/modules/auth/Layout.vue';



import 'bootstrap';
import '../../public/vendor/adminlte/plugins/jquery/jquery.min.js';
import '../../public/vendor/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js';
import '../../public/vendor/adminlte/dist/js/adminlte.min.js';

import VueSweetalert2 from 'vue-sweetalert2';

// import '../../public/vendor/ckeditor5/ckeditor.js';

// $(document).ready(function () {
//     ClassicEditor
//         .create( document.querySelector( '.ckeditor' ), {
//         // toolbar: [ 'heading', '|', 'bold', 'italic', 'link' ]
//         } )
//         .then( editor => {
//         window.editor = editor;
//         } )
//         .catch( err => {
//         console.error( err.stack );
//         } );
// });

const rootElement = document.getElementById('app')

if (rootElement) {

    const profileData = JSON.parse(rootElement.getAttribute('data-profile') || '{}')

    const app = createApp({});
    app.use(router);

    store.commit('SET_PROFILE', profileData)

    app.use(store)

    app.use(VueSweetalert2);

    app.component('layout', Layout);
    app.mount('#app')
}


