import { svelte } from "@sveltejs/vite-plugin-svelte";
import { vitePreprocess } from "@sveltejs/vite-plugin-svelte";
import autoprefixer from "autoprefixer";
import laravel from "laravel-vite-plugin";
import tailwindcss from "tailwindcss";
import { defineConfig } from "vite";

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
        svelte({
            // prebundleSvelteLibraries: true,
        }),
    ],
    build: {
        rollupOptions: {
            input: {
                app: "resources/js/app.js",
            },
        },
        outDir: "public/js",
    },
    css: {
        postcss: {
            plugins: [tailwindcss, autoprefixer],
        },
    },
    preprocess: vitePreprocess(),
});
