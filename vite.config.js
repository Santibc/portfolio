import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                // Landing pública: bundle aparte, sin las librerías del panel
                'resources/css/landing.css',
                'resources/js/landing.js',
                'resources/images/logoico.png', // Archivo específico
            ],
            refresh: true,
        }),
    ],
});