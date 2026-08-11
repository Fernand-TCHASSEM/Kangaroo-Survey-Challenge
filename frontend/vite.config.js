import { defineConfig, loadEnv } from 'vite'
import vue from '@vitejs/plugin-vue'

export default defineConfig(({ mode }) => {
  const env = loadEnv(mode, process.cwd(), '')

  return {
    plugins: [vue()],
    server: {
      host: '0.0.0.0',
      port: 5173,
      strictPort: true,
      allowedHosts: env.VITE_ALLOWED_HOSTS
        ? env.VITE_ALLOWED_HOSTS.split(',')
        : true,   // true = accepts all hosts if nothing is specified
      hmr: {
        host: env.VITE_HMR_HOST || 'localhost',
        protocol: 'ws',
        clientPort: env.VITE_HMR_CLIENT_PORT || 5173,
      },
      watch: {
        usePolling: true,
      },
    },
  }
})