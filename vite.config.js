import { defineConfig } from 'vite';
// vite.config.js
import path from 'path';
import laravel from 'laravel-vite-plugin';
import ladminViteInputs from '@hexters/ladmin-vite-input';

export default defineConfig({
    plugins: [
        laravel({
            input: ladminViteInputs([
                'resources/scss/app.scss',
                'resources/css/app.css',
                'resources/js/app.js'
            ]),
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '~bootstrap': path.resolve(__dirname, 'node_modules/bootstrap'),
            '@upload': '/resources/js/uploadfile'
        },
    },
});
