import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
const path = require("path")

export default defineConfig({
    resolve:{
        alias: {
            "vue": path.resolve("./node_modules/vue")
        }
    },
    plugins: [
        laravel([
            "resources/css/app.css",
            "resources/js/app.js",
        ]),
        vue({

        }),
    ]
});