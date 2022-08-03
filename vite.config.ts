import { resolve } from 'path';
import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';

const stylesDir = resolve(__dirname, 'resources', 'assets', 'styles');
const scriptsDir = resolve(__dirname, 'resources', 'assets', 'scripts');

export default defineConfig({
  build: {
    outDir: './webroot/',
    emptyOutDir: false,
    assetsDir: 'assets',
    manifest: false,
    rollupOptions: {
      input: {
        main: resolve(scriptsDir, 'main.ts'),
        'css/app': resolve(stylesDir, 'app.scss'),
        'css/view': resolve(stylesDir, 'view.scss'),
      },
      output: {
        entryFileNames: '[name].js',
        chunkFileNames: '[name].js',
        assetFileNames: '[name].[ext]',
      },
    },
  },
  plugins: [vue()],
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
