import { createRouter, createWebHistory } from 'vue-router';
// Impor komponen halaman Anda di sini
import Home from '@/components/modules/web/pages/home/Home.vue';
import Article from '@/components/modules/web/pages/article/Article.vue';
import ArticleSingle from '@/components/modules/web/pages/article/ArticleSingle.vue';
import Profile from '@/components/modules/web/pages/profile/Profile.vue';
import Visidanmisi from '@/components/modules/web/pages/visidanmisi/Visidanmisi.vue';
import ProdukKami from '@/components/modules/web/pages/produkkami/ProdukKami.vue';
import ProdukKamiSingle from '@/components/modules/web/pages/produkkami/ProdukKamiSingle.vue';
import KontakKami from '@/components/modules/web/pages/kontakkami/KontakKami.vue';
import Event from '@/components/modules/web/pages/event/Event.vue';
import EventSingle from '@/components/modules/web/pages/event/EventSingle.vue';
import NotFoundPage from '@/components/modules/errors/NotFoundPage.vue';

const routes = [
    // Definisikan rute Anda di sini
    {
        path: '/',
        name: 'home',
        component: Home
    },
    {
        path: '/article',
        name: 'article',
        component: Article
    },
    {
        path: '/article/:id',
        name: 'article-single',
        component: ArticleSingle
    },
    {
        path: '/profile',
        name: 'profile',
        component: Profile
    },
    {
        path: '/visidanmisi',
        name: 'visidanmisi',
        component: Visidanmisi
    },
    {
        path: '/produkkami',
        name: 'produkkami',
        component: ProdukKami
    },
    {
        path: '/produkkami/:id',
        name: 'produkkami-single',
        component: ProdukKamiSingle
    },
    {
        path: '/kontakkami',
        name: 'kontakkami',
        component: KontakKami
    },
    {
        path: '/event',
        name: 'event',
        component: Event
    },
    {
        path: '/event/:id',
        name: 'event-single',
        component: EventSingle
    },
    {
        path: '/:pathMatch(.*)*',
        name: 'not-found',
        component: NotFoundPage
    }

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
