import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import tailwindcss from "tailwindcss";
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
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
        tailwindcss(),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './resources/js'),
            '~': path.resolve(__dirname, './resources'),
            'ziggy': path.resolve('vendor/tightenco/ziggy/dist/vue.m.js'),
        },
    },
    optimizeDeps: {
        include: [
            '@inertiajs/inertia',
            '@inertiajs/inertia-vue3',
            'axios',
            'vue',
        ],
    },
    server: {
        hmr: {
            host: 'localhost',
        },
        watch: {
            usePolling: true,
        },
        headers: {
            'Access-Control-Allow-Origin': '*',
        },
    },
});
