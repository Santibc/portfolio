import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/images/**/*.{jpg,jpeg,png,svg,gif,webp}',
            ],
            refresh: true,
        }),
    ],
});
