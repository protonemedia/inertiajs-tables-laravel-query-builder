const path = require("path")
const { defineConfig } = require("vite")
import vue from "@vitejs/plugin-vue"
import cssInjectedByJsPlugin from "vite-plugin-css-injected-by-js";

// https://vitejs.dev/config/
export default defineConfig({
    plugins: [vue(), cssInjectedByJsPlugin()],
    build: {
        lib: {
            entry: path.resolve(__dirname, "js/main.js"),
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
                    vue: "Vue",
                    Vue: "Vue"
                }
            }
        }
    }
})
