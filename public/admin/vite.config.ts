import { fileURLToPath, URL } from 'node:url'

import vue from '@vitejs/plugin-vue'
import vueJsx from '@vitejs/plugin-vue-jsx'
import { defineConfig } from 'vite'
import vueDevTools from 'vite-plugin-vue-devtools'

// https://vite.dev/config/
export default defineConfig({
  base: '/admin/',
  plugins: [vue(), vueJsx(), vueDevTools()],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url)),
    },
  },
  build: {
    rollupOptions: {
      input: 'index.dev.html',
      output: {
        manualChunks: undefined,
      },
    },
    // Build output goes to `dist` to avoid overriding source files
    outDir: 'dist',
    assetsDir: 'assets',
    emptyOutDir: true,
  },
})
