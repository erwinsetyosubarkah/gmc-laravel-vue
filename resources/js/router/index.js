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
import Galery from '@/components/modules/web/pages/galery/Galery.vue';
import KlienKami from '@/components/modules/web/pages/klienkami/KlienKami.vue';
import Dashboard from '@/components/modules/admin/pages/dashboard/Dashboard.vue';
import AdminProfile from '@/components/modules/admin/pages/profile/Profile.vue';
import AdminVisidanmisi from '@/components/modules/admin/pages/visidanmisi/Visidanmisi.vue';
import AdminMyproduct from '@/components/modules/admin/pages/myproduct/Myproduct.vue';
import AdminCategory from '@/components/modules/admin/pages/category/Category.vue';
import AdminPost from '@/components/modules/admin/pages/post/Post.vue';
import AdminEvent from '@/components/modules/admin/pages/event/Event.vue';
import AdminGalery from '@/components/modules/admin/pages/galery/Galery.vue';
import AdminMyclient from '@/components/modules/admin/pages/myclient/Myclient.vue';
import AdminUser from '@/components/modules/admin/pages/user/User.vue';
import NotFoundPage from '@/components/modules/errors/NotFoundPage.vue';
import Login from '@/components/modules/auth/Login.vue';
import Register from '@/components/modules/auth/Register.vue';

const routes = [
    // Definisikan rute Anda di sini
    {
        path: '/web/',
        name: 'web-home',
        component: Home
    },
    {
        path: '/web/article',
        name: 'article',
        component: Article
    },
    {
        path: '/web/article/:id',
        name: 'article-single',
        component: ArticleSingle
    },
    {
        path: '/web/profile',
        name: 'profile',
        component: Profile
    },
    {
        path: '/web/visidanmisi',
        name: 'visidanmisi',
        component: Visidanmisi
    },
    {
        path: '/web/produkkami',
        name: 'produkkami',
        component: ProdukKami
    },
    {
        path: '/web/produkkami/:id',
        name: 'produkkami-single',
        component: ProdukKamiSingle
    },
    {
        path: '/web/kontakkami',
        name: 'kontakkami',
        component: KontakKami
    },
    {
        path: '/web/event',
        name: 'event',
        component: Event
    },
    {
        path: '/web/event/:id',
        name: 'event-single',
        component: EventSingle
    },
    {
        path: '/web/galery',
        name: 'galery',
        component: Galery
    },
    {
        path: '/web/klienkami',
        name: 'klienkami',
        component: KlienKami
    },
    {
        path: '/admin/dashboard',
        name: 'admin-dashboard',
        component: Dashboard,
         meta: {
            title: 'Dashboard'
        }
    },
    {
        path: '/admin/profile',
        name: 'admin-profile',
        component: AdminProfile,
         meta: {
            title: 'Profile'
        }
    },
    {
        path: '/admin/visidanmisi',
        name: 'admin-visidanmisi',
        component: AdminVisidanmisi,
         meta: {
            title: 'Visi dan Misi'
        }
    },
    {
        path: '/admin/myproduct',
        name: 'admin-myproduct',
        component: AdminMyproduct,
        meta: {
            title: 'Produk Kami'
        }
    },
    {
        path: '/admin/category',
        name: 'admin-category',
        component: AdminCategory,
        meta: {
            title: 'Kategori'
        }
    },
    {
        path: '/admin/post',
        name: 'admin-post',
        component: AdminPost,
        meta: {
            title: 'Artikel'
        }
    },
    {
        path: '/admin/event',
        name: 'admin-event',
        component: AdminEvent,
        meta: {
            title: 'Event'
        }
    },
    {
        path: '/admin/galery',
        name: 'admin-galery',
        component: AdminGalery,
        meta: {
            title: 'Galeri'
        }
    },
    {
        path: '/admin/myclient',
        name: 'admin-myclient',
        component: AdminMyclient,
        meta: {
            title: 'Klien Kami'
        }
    },
    {
        path: '/admin/user',
        name: 'admin-user',
        component: AdminUser,
        meta: {
            title: 'User'
        }
    },
    {
        path: '/auth/login',
        name: 'auth-login',
        component: Login
    },
    {
        path: '/auth/register',
        name: 'auth-register',
        component: Register
    },
    {
        path: '/',
        name: 'home',
        beforeEnter(){
            window.location.href = `/web/`;
        }
    },
    {
        path: '/:pathMatch(.*)*',
        name: 'not-found',
        beforeEnter(to, from) {
            // 1. Ambil URL halaman asal (sebelum tersasar ke 404)
            const previousUri = from.fullPath;

            // 2. Encode URI agar aman dibaca sebagai query parameter
            const encodedUri = encodeURIComponent(previousUri);

            // 3. Redirect ke /not-found sambil membawa parameter
            // Hasilnya akan menjadi seperti: /not-found?from=%2Fhalaman-lama
            window.location.href = `/not-found?from=${encodedUri}`;
        }

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
    },
    linkActiveClass: 'active'
});

export default router;
