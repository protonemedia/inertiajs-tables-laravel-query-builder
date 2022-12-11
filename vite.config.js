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
            fileName: "inertiajs-tables-laravel-query-builder",
        },
        rollupOptions: {
            external: [
                /^@inertiajs.*/,
                "vue"
            ],
            output: {
                globals: {
                    vue: "Vue"
                }
            }
        }
    }
});
