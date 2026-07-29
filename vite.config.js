import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';
import inject from '@rollup/plugin-inject';

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
        }),
        inject({
            include: ['**/*.js', '**/*.vue'],
            // include: [
            //     'node_modules/jquery/**/*.js',
            //     'node_modules/shufflejs/**/*.js',
            //     'node_modules/popper.js/**/*.js',
            //     'node_modules/@popperjs/core/**/*.js', // Tambahan jika Anda memakai Popper v2 (Bootstrap 5)
            //     'resources/js/**/*.js',
            //     'resources/js/**/*.vue',
            // ],
            $: 'jquery',
            jQuery: 'jquery',
            'window.jQuery': 'jquery',
            'window.Shuffle': ['shufflejs', 'default'],
            Shuffle: ['shufflejs', 'default'],
            Popper: ['popper.js', 'default'],
        }),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './resources/js'),
            'vue': 'vue/dist/vue.esm-bundler.js',
        },
    },

    // Optimasi 2: Paksa pre-bundling dependensi besar agar dev server instan
    optimizeDeps: {
        include: ['jquery', 'shufflejs', 'popper.js', 'vue', 'axios'],
    },
    // Optimasi 3: Tingkatkan performa build production
    build: {
        chunkSizeWarningLimit: 1000,
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (id.includes('node_modules')) {
                        if (id.includes('jquery') || id.includes('shufflejs')) {
                            return 'vendor-libs';
                        }
                        return 'vendor';
                    }
                },
            },
        },
    },
});
