import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/admin.js',
                'resources/js/auth.js',
            ],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        })
    ],
    server: {
        // Mengamankan performa HMR di browser agar selalu instan
        hmr: {
            host: 'localhost',
        },
        watch: {
            // Batasi file yang diawasi agar Vite tidak mendeteksi perubahan dari folder sampah
            ignored: ['**/node_modules/**', '**/dist/**', '**/.git/**'],
            usePolling: true, // Membantu deteksi perubahan berkas jika Anda menggunakan WSL2 / Docker
        },
    },
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './resources/js'),
            'vue': 'vue/dist/vue.esm-bundler.js',
        },
    },

});
