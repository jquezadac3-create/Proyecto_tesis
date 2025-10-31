import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import fg from 'fast-glob';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/custom-choices.css',
                ...fg.sync('resources/js/**/*.js'),
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/js'),
            '@productos': path.resolve(__dirname, 'resources/js/productos'),
            '@utils': path.resolve(__dirname, 'resources/js/utils'),
        }
    }
});
