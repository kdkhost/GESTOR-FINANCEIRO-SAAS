import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
        vue({ template: { transformAssetUrls: { base: null, includeAbsolute: false } } }),
        react(),
    ],
    resolve: {
        alias: {
            '@': '/resources/js',
            '~vue': '/resources/js/vue',
            '~react': '/resources/js/react',
        },
    },
});
