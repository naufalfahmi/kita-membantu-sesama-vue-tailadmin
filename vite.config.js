import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import vueJsx from '@vitejs/plugin-vue-jsx';
import { resolve } from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/app.js',
                'resources/js/admin.js'
            ],
            refresh: true,
        }),
        tailwindcss(),
        vue(),
        vueJsx(),
    ],
    resolve: {
        alias: {
            '@': resolve(__dirname, 'public/admin/src'),
        },
    },
    publicDir: resolve(__dirname, 'public/admin/public'),
    build: {
        rollupOptions: {
            output: {
                assetFileNames: 'assets/[name][extname]',
                chunkFileNames: 'assets/[name]-[hash].js',
                entryFileNames: 'assets/[name]-[hash].js',
            },
        },
    },
});
