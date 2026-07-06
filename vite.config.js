import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';
import inject from '@rollup/plugin-inject';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
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
});
