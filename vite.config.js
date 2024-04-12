import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from "@vitejs/plugin-vue";
import {globSync} from "glob";

let files = globSync(['resources/js/*.js', 'resources/js/*/*.js', 'resources/scss/*.scss']), input = [];
files.forEach(file => {
    input.push(file.replace(/[\\\/]+/g, '/'));
});

export default defineConfig({
    resolve: {
        alias: {
            moment: 'moment/moment',
            vue: 'vue/dist/vue.esm-bundler.js'
        }
    },
    plugins: [
        laravel({
            input,
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false
                },
            },
        }),
    ],
});
