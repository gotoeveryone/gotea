import path from 'path';
import legacy from '@vitejs/plugin-legacy';
import { defineConfig } from 'vite';
import { createVuePlugin } from 'vite-plugin-vue2';

const stylesDir = path.join(__dirname, 'resources', 'assets', 'styles');
const scriptsDir = path.join(__dirname, 'resources', 'assets', 'scripts');

export default defineConfig({
  build: {
    outDir: './webroot/',
    emptyOutDir: false,
    assetsDir: 'build',
    manifest: true,
    rollupOptions: {
      input: {
        main: path.join(scriptsDir, 'main.ts'),
        'css/app': path.join(stylesDir, 'app.scss'),
        'css/view': path.join(stylesDir, 'view.scss'),
      },
    },
  },
  plugins: [
    legacy({
      targets: ['defaults', 'not IE 11'],
    }),
    createVuePlugin(),
  ],
  resolve: {
    alias: {
      '@': scriptsDir,
    },
  },
  server: {
    hmr: {
      protocol: 'ws',
      host: 'localhost',
      port: 3000,
    },
  },
});
