import { createRouter, createWebHistory } from 'vue-router';
// Impor komponen halaman Anda di sini
import Home from '../components/modules/web/pages/Home.vue';

const routes = [
  // Definisikan rute Anda di sini
  { path: '/', component: Home }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

export default router;
