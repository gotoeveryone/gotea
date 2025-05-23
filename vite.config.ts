import { resolve } from "path";
import { defineConfig } from "vite";
import vue from "@vitejs/plugin-vue";
import biome from "vite-plugin-biome";
import stylelint from "vite-plugin-stylelint";

const stylesDir = resolve(__dirname, "resources", "assets", "styles");
const scriptsDir = resolve(__dirname, "resources", "assets", "scripts");

export default defineConfig({
  build: {
    sourcemap: false, // https://github.com/vitejs/vite-plugin-vue/issues/35
    minify: false,
    outDir: "./webroot/",
    emptyOutDir: false,
    rollupOptions: {
      input: {
        main: resolve(scriptsDir, "main.ts"),
        app: resolve(stylesDir, "app.scss"),
        view: resolve(stylesDir, "view.scss"),
      },
      output: {
        assetFileNames: (assetInfo) => {
          const directory = /\.css$/i.test(assetInfo.name as string)
            ? "css"
            : "js";
          return `${directory}/[name][extname]`;
        },
        chunkFileNames: "js/[name].js",
        entryFileNames: "js/[name].js",
      },
    },
  },
  css: {
    preprocessorOptions: {
      scss: {
        additionalData: `
          @import "~/base/variables";
          @import "~/base/mixin";
        `,
      },
    },
  },
  plugins: [
    vue(),
    biome({
      mode: "lint",
      files: "./resources/assets/scripts",
    }),
    stylelint({
      include: [
        "./resources/assets/styles/**/*.scss",
        "./resources/assets/scripts/**/*.vue",
      ],
    }),
  ],
  resolve: {
    alias: {
      "@": scriptsDir,
      "~": stylesDir,
      vue: "vue/dist/vue.esm-bundler.js",
    },
  },
  test: {
    globals: true,
    watch: false,
    environment: "jsdom",
    include: ["./resources/**/*.test.ts"],
    exclude: ["**/node_modules/**"],
  },
});
