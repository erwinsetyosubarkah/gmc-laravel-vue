import { createRouter, createWebHistory } from 'vue-router';
// Impor komponen halaman Anda di sini
import Home from '@/components/modules/web/pages/home/Home.vue';
import ArticleSingle from '@/components/modules/web/pages/article/ArticleSingle.vue';

const routes = [
  // Definisikan rute Anda di sini
  { path: '/', component: Home },
  { path: '/article/:id', component: ArticleSingle }
];

const router = createRouter({
  history: createWebHistory(),
  routes,
    // Tambahkan fungsi ini di bawah konfigurasi rute Anda
    scrollBehavior(to, from, savedPosition) {
        // Jika pengguna menekan tombol Back/Forward browser, kembalikan ke posisi terakhir mereka
        if (savedPosition) {
            return savedPosition;
        } else {
            // Untuk navigasi rute baru, otomatis scroll ke koordinat x:0, y:0 (paling atas)
            return { top: 0, left: 0 };
        }
    }
});

export default router;
