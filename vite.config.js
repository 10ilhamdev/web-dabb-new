import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: [
                "resources/views/**",
                "routes/**",
                "app/Http/Controllers/**",
            ],
        }),
    ],
    server: {
        host: "0.0.0.0",
        hmr: {
            host: "192.168.1.46", //ipv4 address laptop
        },
        watch: {
            usePolling: true,
        },
    },
    resolve: {
        alias: {
            "page-flip": "page-flip/dist/js/page-flip.module.js",
        },
    },
});
