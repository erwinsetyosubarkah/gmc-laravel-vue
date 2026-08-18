import { defineConfig } from 'vitest/config'
import viteConfig from './vite.config.js'

export default defineConfig({
    ...viteConfig,
    test: {
        environment: 'jsdom',
        globals: true,
        include: [
            'resources/js/test/unit_test/**/*.spec.js',
            'resources/js/test/component_test/**/*.spec.js',
            'resources/js/test/integration_test/**/*.spec.js',
        ],
    },
})
