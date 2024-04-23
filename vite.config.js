import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from "@vitejs/plugin-vue";
import {globSync} from "glob";
import { nxViteTsPaths } from '@nx/vite/plugins/nx-tsconfig-paths.plugin';

let files = globSync(['resources/js/*.js', 'resources/js/*/*.js', 'resources/scss/*.scss']), input = [];
files.forEach(file => {
    input.push(file.replace(/[\\\/]+/g, '/'));
});

export default defineConfig({
    root: __dirname,
    cacheDir: './node_modules/.vite/.',
    build: {
        outDir: './public/build',
        reportCompressedSize: true,
        commonjsOptions: {
            transformMixedEsModules: true,
        },
        include: ['resources/js/**/*.js', 'resources/js/*.js', 'resources/components/**/*.vue', 'resources/components/*.vue'],
    },
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
        nxViteTsPaths()
    ],
});
