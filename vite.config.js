import { resolve } from "path";
import { defineConfig } from "vite";
import vue from "@vitejs/plugin-vue";

// https://vitejs.dev/config/
export default defineConfig({
    plugins: [vue()],

    build: {
        lib: {
            entry: resolve(__dirname, "js/main.js"),
            name: "Inertia.js Tables for Laravel Query Builder",
            fileName: (format) => `inertiajs-tables-laravel-query-builder.${format}.js`
        },
        rollupOptions: {
            external: [
                /^@inertiajs.*/,
                /^@popperjs.*/,
                /^lodash-es.*/,
                "qs",
                "vue"
            ],
            output: {
                globals: {
                    vue: "Vue"
                }
            }
        }
    }
})
