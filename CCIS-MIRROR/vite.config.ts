import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
  plugins: [
    laravel({
      input: ['resources/css/app.css', 'resources/js/app.ts'],
      refresh: true,
    }),
    vue(),
  ],
  server: {
    host: '0.0.0.0',      // Listen on all network interfaces
    port: 5173,           // Default Vite port
    hmr: {
      host: '192.168.1.15', // Your LAN IP
      port: 5173,
    },
  },
})